<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* ReLiS - Issue #103 - Assignment Engine
 *
 * Cette library centralise la logique d'assignation des papers aux reviewers,
 * en respectant les contraintes configurées (assignment_constraint).
 *
 * Workflow:
 *   1) init()       → prépare l'état interne
 *   2) validate()   → vérifie la faisabilité avant assignation (renvoie erreurs)
 *   3) assign()     → calcule le mapping paper → users
 *
 * Le controller appelant fait ensuite les inserts en DB selon son schéma.
 */

class Assignment_engine_lib
{
    private $CI;

    // Contexte
    private $scope;              // 'screening', 'qa', ...
    private $phase_id;           // null si pas applicable
    private $papers;             // array de papers (avec au moins 'id')
    private $users;              // array de user_ids sélectionnés
    private $reviews_per_paper;  // entier

    // État interne calculé
    private $constraints;        // contraintes actives chargées
    private $user_tags;          // user_id => array of tags
    private $workload;           // user_id => nombre de papers assignés
    private $previous_cache;     // paper_id|scope => array user_ids

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Screening_dataAccess');
    }

    // ═══════════════════════════════════════════════════════════════════
    // PUBLIC API
    // ═══════════════════════════════════════════════════════════════════

    public function init($scope, $phase_id, $papers, $users, $reviews_per_paper)
    {
        $this->scope             = $scope;
        $this->phase_id          = $phase_id;
        $this->papers            = $papers;
        $this->users             = array_map('intval', $users);
        $this->reviews_per_paper = max(1, intval($reviews_per_paper));

        $this->workload = array();
        foreach ($this->users as $u) $this->workload[$u] = 0;

        $this->previous_cache = array();
        $this->constraints = $this->CI->Screening_dataAccess->get_active_constraints(
            $scope, $phase_id
        );

        // Pré-charger les tags de chaque user
        $this->user_tags = array();
        foreach ($this->users as $u) {
            $this->user_tags[$u] = $this->CI->Screening_dataAccess->get_user_tags($u);
        }
    }

    /**
     * Vérifie la faisabilité. Renvoie un array d'erreurs (vide = OK).
     */
    public function validate()
    {
        $errors = array();

        // Bypass mandatory pour QA et Classification avec 1 seul user
        if (in_array($this->scope, array('qa','qa_validation','classification','classification_validation'))
            && count($this->users) === 1) {
            return $errors; // pas de validation, on assigne quoi qu'il arrive
        }

        if (count($this->users) < $this->reviews_per_paper) {
            $errors[] = "Reviews per paper ($this->reviews_per_paper) exceeds the number of selected users (" . count($this->users) . ").";
            return $errors;
        }

        foreach ($this->constraints as $c) {
            $params = json_decode($c['constraint_params'], true);
            if (!is_array($params)) {
                $errors[] = "Constraint #{$c['constraint_id']} has invalid JSON params.";
                continue;
            }

            switch ($c['constraint_type']) {
                case 'min_tag_per_paper':
                    $count = $this->count_selected_users_with_tag($params['tag_id']);
                    if ($count < intval($params['min_count'])) {
                        $errors[] = "Constraint min_tag_per_paper: only $count selected user(s) have the required tag (need {$params['min_count']}).";
                    }
                    break;

                case 'max_tag_per_paper':
                    $without = count($this->users) - $this->count_selected_users_with_tag($params['tag_id']);
                    if ($without < ($this->reviews_per_paper - intval($params['max_count']))) {
                        $errors[] = "Constraint max_tag_per_paper: not enough users without the tag to respect the max.";
                    }
                    break;

                case 'tag_combination':
                    $any_feasible = false;
                    foreach ($params['options'] as $opt) {
                        if ($this->count_selected_users_with_tag($opt['tag_id']) >= intval($opt['count'])) {
                            $any_feasible = true;
                            break;
                        }
                    }
                    if (!$any_feasible) {
                        $errors[] = "Constraint tag_combination: no option is feasible with the selected users.";
                    }
                    break;

                case 'same_user_from_previous_phase':
                    if (!empty($params['mode']) && $params['mode'] === 'strict') {
                        foreach ($this->papers as $paper) {
                            $previous = $this->get_previous_cached($paper['id'], $params);
                            foreach ($previous as $u) {
                                if (!in_array($u, $this->users)) {
                                    $errors[] = "Constraint same_user (strict): user #$u must be selected for paper #{$paper['id']}.";
                                }
                            }
                        }
                    }
                    break;

                case 'force_different_user_from_previous_phase':
                    foreach ($this->papers as $paper) {
                        $previous = $this->get_previous_cached($paper['id'], $params);
                        $eligible = array_diff($this->users, $previous);
                        if (count($eligible) < $this->reviews_per_paper) {
                            $errors[] = "Constraint force_different: paper #{$paper['id']} has only " . count($eligible) . " eligible reviewers (need $this->reviews_per_paper).";
                        }
                    }
                    break;
            }
        }
        return $errors;
    }

    /**
     * Calcule le mapping paper_id => array of user_ids.
     */
    public function assign()
    {
        $mapping = array();

        // ─── PASS 1 : contraintes dures ──────────────────────────────────
        foreach ($this->papers as $paper) {
            $paper_id = $paper['id'];
            $mapping[$paper_id] = array();
            $blacklist = array();

            foreach ($this->constraints as $c) {
                $params = json_decode($c['constraint_params'], true);
                if (!is_array($params)) continue;

                switch ($c['constraint_type']) {
                    case 'same_user_from_previous_phase':
                        $previous = $this->get_previous_cached($paper_id, $params);
                        foreach ($previous as $u) {
                            if (in_array($u, $this->users)
                                && !in_array($u, $mapping[$paper_id])
                                && count($mapping[$paper_id]) < $this->reviews_per_paper) {
                                $mapping[$paper_id][] = $u;
                                $this->workload[$u]++;
                            }
                        }
                        break;

                    case 'force_different_user_from_previous_phase':
                        $previous = $this->get_previous_cached($paper_id, $params);
                        $blacklist = array_merge($blacklist, $previous);
                        break;

                    case 'min_tag_per_paper':
                        $tag_id   = intval($params['tag_id']);
                        $already  = $this->count_assigned_with_tag($mapping[$paper_id], $tag_id);
                        $needed   = intval($params['min_count']) - $already;
                        $this->fill_with_tag($mapping, $paper_id, $tag_id, $needed, $blacklist);
                        break;

                    case 'tag_combination':
                        $option = $this->pick_best_option($params['options'], $mapping[$paper_id]);
                        if ($option !== null) {
                            foreach ($option as $opt) {
                                $tag_id = intval($opt['tag_id']);
                                $needed = intval($opt['count'])
                                    - $this->count_assigned_with_tag($mapping[$paper_id], $tag_id);
                                $this->fill_with_tag($mapping, $paper_id, $tag_id, $needed, $blacklist);
                            }
                        }
                        break;
                }
            }

            // ─── PASS 2 : remplissage round-robin équilibré ──────────────
            while (count($mapping[$paper_id]) < $this->reviews_per_paper) {
                $candidates = $this->eligible_candidates($mapping[$paper_id], $blacklist, $paper_id);
                if (empty($candidates)) break;
                usort($candidates, function ($a, $b) {
                    return $this->workload[$a] - $this->workload[$b];
                });
                $chosen = $candidates[0];
                $mapping[$paper_id][] = $chosen;
                $this->workload[$chosen]++;
            }
        }

        return $mapping;
    }

    // ═══════════════════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ═══════════════════════════════════════════════════════════════════

    private function count_selected_users_with_tag($tag_id)
    {
        $count = 0;
        foreach ($this->users as $u) {
            if ($this->CI->Screening_dataAccess->user_has_tag($u, $tag_id)) $count++;
        }
        return $count;
    }

    private function count_assigned_with_tag($assigned_users, $tag_id)
    {
        $count = 0;
        foreach ($assigned_users as $u) {
            if ($this->CI->Screening_dataAccess->user_has_tag($u, $tag_id)) $count++;
        }
        return $count;
    }

    private function fill_with_tag(&$mapping, $paper_id, $tag_id, $needed, $blacklist)
    {
        if ($needed <= 0) return;

        $candidates = array();
        foreach ($this->users as $u) {
            if (in_array($u, $mapping[$paper_id])) continue;
            if (in_array($u, $blacklist)) continue;
            if ($this->CI->Screening_dataAccess->user_has_tag($u, $tag_id)) {
                $candidates[] = $u;
            }
        }

        usort($candidates, function ($a, $b) {
            return $this->workload[$a] - $this->workload[$b];
        });

        for ($i = 0; $i < $needed && $i < count($candidates); $i++) {
            if (count($mapping[$paper_id]) >= $this->reviews_per_paper) break;
            $mapping[$paper_id][] = $candidates[$i];
            $this->workload[$candidates[$i]]++;
        }
    }

    private function eligible_candidates($already_assigned, $blacklist, $paper_id)
    {
        $candidates = array();
        foreach ($this->users as $u) {
            if (in_array($u, $already_assigned)) continue;
            if (in_array($u, $blacklist)) continue;
            // Vérif max_tag contraintes
            if (!$this->respects_max_tags($already_assigned, $u)) continue;
            $candidates[] = $u;
        }
        return $candidates;
    }

    private function respects_max_tags($current_assigned, $candidate)
    {
        foreach ($this->constraints as $c) {
            if ($c['constraint_type'] !== 'max_tag_per_paper') continue;
            $params  = json_decode($c['constraint_params'], true);
            if (!is_array($params)) continue;
            $tag_id  = intval($params['tag_id']);
            $maxc    = intval($params['max_count']);
            $current = $this->count_assigned_with_tag($current_assigned, $tag_id);
            if ($this->CI->Screening_dataAccess->user_has_tag($candidate, $tag_id)
                && $current + 1 > $maxc) {
                return false;
            }
        }
        return true;
    }

    private function pick_best_option($options, $already_assigned)
    {
        // Choisit l'option qui ajoute la charge minimale (équilibrage)
        $best = null; $best_cost = PHP_INT_MAX;
        foreach ($options as $opt_idx => $opt) {
            // Option = soit un seul élément (1 senior), soit une combinaison
            $opts_normalized = is_array($opt) && isset($opt[0]) ? $opt : array($opt);
            $cost = 0;
            $feasible = true;
            foreach ($opts_normalized as $o) {
                $candidates_for_tag = $this->count_selected_users_with_tag($o['tag_id']);
                if ($candidates_for_tag < intval($o['count'])) {
                    $feasible = false;
                    break;
                }
                $cost += $o['count'];
            }
            if ($feasible && $cost < $best_cost) {
                $best = $opts_normalized;
                $best_cost = $cost;
            }
        }
        return $best;
    }

    private function get_previous_cached($paper_id, $params)
    {
        $previous_scope    = $params['previous_scope'];
        $previous_phase_id = isset($params['previous_phase_id']) ? $params['previous_phase_id'] : null;
        $cache_key = $paper_id . '|' . $previous_scope . '|' . ($previous_phase_id ?? 'null');
        if (!isset($this->previous_cache[$cache_key])) {
            $this->previous_cache[$cache_key] =
                $this->CI->Screening_dataAccess->get_previous_reviewers(
                    $paper_id, $previous_scope, $previous_phase_id
                );
        }
        return $this->previous_cache[$cache_key];
    }
}
