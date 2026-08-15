<?php
/* ReLiS - A Tool for conducting systematic literature reviews and mapping studies.
 * Copyright (C) 2018  Eugene Syriani
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * --------------------------------------------------------------------------
 *
 *  :Author: Brice Michel Bigendako
 * --------------------------------------------------------------------------
 * ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Screen_phases extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function display_phases()
    {

        $this->db2 = $this->load->database(project_db(), TRUE);
        $this->db2->trans_start();

        $raw_phases_list = $this->db2->select("*")->get('screen_phase')->result_array();

        $phases_list = array();
        foreach ($raw_phases_list as $raw_phase) {
            $phases_list[$raw_phase['screen_phase_id']] = $raw_phase;
        }

        $data = array(
            'phases_list' => json_encode($phases_list),
            'top_buttons' => '<li><a href="'.base_url().'/home.html" title="Close"><button class="btn btn-danger"><i class="fa fa-close"></i> </button></a></li>',
            'page_title' => "Phases Tree - Edition",
            'page' => 'screening/display_screen_phases'
        );

        $this->load->view('shared/body', $data);
    }

    public function add_phase($add_type, $id, $null_screen_type_error = false)
    {
        $ref_table = "screen_phase";
        $table_config = get_table_configuration($ref_table);
        $this->db2 = $this->load->database(project_db(), TRUE);
        $this->db2->trans_start();

        $actual_phases = $this->get_phases();

        if ($id == -1 && $add_type != 'first' || ($id != -1 && !array_key_exists($id, $actual_phases))) {
            redirect('screen_phases/display_phases');
        }

        $phases = $this->get_phases();
        $phases[-1] = null;

        $parent = $phases;
        $child = $phases;
        $screen_type = $table_config['fields']['displayed_fields_vals']['input_select_values'];
        $lock_screen_types = false;

        switch ($add_type) {
            case 'sibling' :
                $phase_type = $this->get_phase_type($id);

                if ($phase_type == 'first' ||
                    $phase_type == 'final' ||
                    ($phase_type == 'intermediate' && $this->number_of_parents($id, $actual_phases) <= 1)) {

                    redirect('screen_phases/display_phases');
                }

                $child = array($phases[$id]['next_phase'] => $phases[$phases[$id]['next_phase']]['phase_title']);

                if ($phases[$id]['depth_level'] == 0) {
                    $parent = array(-1 => "null");
                } else {
                    $parent = array_filter($phases, fn($phase) =>
                        is_array($phase) && $phase['next_phase'] == $id && $phase['screen_phase_active'] == 1
                    );

                    $parent = array_map(fn($phase) => $phase['phase_title'], $parent);
                }

                $lock_screen_types = true;
                $screen_type = explode("|", $phases[$id]['displayed_fields']);

                break;

            case 'parent' :
                $phase_type = $this->get_phase_type($id);
                $phase = $phases[$id];

                if ($phase_type == 'final' ||
                    $phase_type == 'intermediate' ||
                    ($phase_type == 'initial' && ($phase['used'] != 0 || $phase['has_pending'] != 0 || count($this->get_siblings($id, $actual_phases)) != 0))) {
                    redirect('screen_phases/display_phases');
                }

                $parent = array(-1 => "null");
                $child = array($id => $phases[$id]['phase_title']);
                break;

            case 'child' :
                $phase_type = $this->get_phase_type($id);
                $phase = $phases[$id];

                switch ($phase_type) {
                    case 'final':
                    case 'first' :
                        if ($phase['used'] != 0 || $phase['has_pending'] != 0) {
                            redirect('screen_phases/display_phases');
                        }
                        break;

                    case 'intermediate':
                        $next_phase = $phases[$phase['next_phase']];

                        if ($next_phase['used'] != 0 || $next_phase['has_pending'] != 0 || count($this->get_siblings($next_phase['screen_phase_id'], $actual_phases)) != 0) {
                            redirect('screen_phases/display_phases');
                        }
                        break;

                    case 'initial':
                        $next_phase = $phases[$phase['next_phase']];

                        if ($next_phase['used'] != 0 ||
                            $next_phase['has_pending'] != 0 ||
                            count($this->get_siblings($next_phase['screen_phase_id'], $actual_phases)) != 0) {
                            redirect('screen_phases/display_phases');
                        }

                }
                $parent = array($id => $phases[$id]['phase_title']);
                $next_phase_id = $phases[$id]['next_phase'];

                if ($next_phase_id == null) {
                    $child = array(-1 => "null");
                } else {
                    $child = array($phases[$id]['next_phase'] => $phases[$phases[$id]['next_phase']]['phase_title']);
                }

                break;

            case 'first' :
                if (count($actual_phases) > 0) {
                    redirect('screen_phases/display_phases');
                }
                break;

            default :
                redirect('screen_phases/display_phases');
        }

        $data = array(
            'page' => 'screening/add_screen_phase',
            'page_title' => "Add screeninng phase",
            'screen_type' => $screen_type,
            'parent' => $parent,
            'child' => $child,
            'lock_screen_types' => $lock_screen_types,
            'add_type' => $add_type,
            'null_screen_type_error' => $null_screen_type_error,
            'id' => $id,
        );

        $this->load->view('shared/body', $data);
    }

    public function save_screen_phase()
    {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $this->db2->trans_start();
        $post_arr = $this->input->post();

        if (!isset($post_arr['displayed_fields']) || $post_arr['title'] == '') {
            redirect('screen_phases/add_phase/' . $post_arr['add_type'] . '/' . $post_arr['id'] . '/1');
        }

        $new_phase = array(
            'phase_title' => $post_arr['title'],
            'displayed_fields' => implode('|', $post_arr['displayed_fields']),
            'next_phase' => $post_arr['child'] != -1 ? $post_arr['child'] : null,
            'parent' => $post_arr['parent'] != -1 ? json_encode([intval($post_arr['parent'])]) : json_encode([]),
            'screen_phase_order' => 10,
            'added_by' => active_user_id()
        );

        $phases = $this->get_phases();

        switch ($post_arr['add_type']) {
            case 'parent' :
                $new_phase['depth_level'] = 0;

                foreach ($phases as $id => $phase) {
                    $phases[$id]['depth_level']++;
                }

                $this->db2->insert('screen_phase', $new_phase);
                $insert_id = $this->db2->insert_id();

                $actual_childs_parent = json_decode($phases[$post_arr['child']]['parent'], true) ?? [];

                $actual_childs_parent[] = $insert_id;

                $phases[$post_arr['child']]['parent'] = json_encode($actual_childs_parent);

                $this->db2->update_batch('screen_phase', array_values($phases), 'screen_phase_id');

                break;

            case 'child' :
                $new_phase['depth_level'] = $phases[$post_arr['parent']]['depth_level'] + 1;

                foreach ($phases as $id => $phase) {
                    if ($phase['depth_level'] >= $new_phase['depth_level']) {
                        $phases[$id]['depth_level']++;
                    }
                }

                $this->db2->insert('screen_phase', $new_phase);
                $insert_id = $this->db2->insert_id();

                $parents = [];

                foreach ($phases as $id => $phase) {
                    if ($phase['next_phase'] == $new_phase['next_phase']) {
                        $phases[$id]['next_phase'] = $insert_id;
                        $parents[] = $id;
                    }
                }

                $phases[$post_arr['parent']]['next_phase'] = $insert_id;
                if ($new_phase['next_phase'] != null) {
                    $phases[$new_phase['next_phase']]['parent'] = json_encode(array($insert_id));
                }

                $this->db2->update_batch('screen_phase', array_values($phases), 'screen_phase_id');
                $this->db2->update('screen_phase', array('parent' => json_encode($parents)), array('screen_phase_id' => $insert_id));
                break;

            case 'sibling' :

                $new_phase['depth_level'] = $post_arr['parent'] != -1 ? $phases[$post_arr['parent']]['depth_level'] + 1 : 0;
                $this->db2->insert('screen_phase', $new_phase);
                $insert_id = $this->db2->insert_id();

                if ($post_arr['parent'] != -1) {
                    $sibling_id = $phases[$post_arr['parent']]['next_phase'];
                    $phases[$post_arr['parent']]['next_phase'] = $insert_id;
                    $siblings_parents = json_decode($phases[$sibling_id]['parent'], true) ?? [];
                    $siblings_parents = array_values(array_diff($siblings_parents, [$post_arr['parent']]));
                    $phases[$sibling_id]['parent'] = json_encode($siblings_parents);
                }

                $actual_childs_parent = json_decode($phases[$post_arr['child']]['parent'], true) ?? [];
                $actual_childs_parent[] = $insert_id;
                $phases[$post_arr['child']]['parent'] = json_encode($actual_childs_parent);

                $this->db2->update_batch('screen_phase', array_values($phases), 'screen_phase_id');

                break;

            case 'first' :
                $new_phase['depth_level'] = 0;

                $this->db2->insert('screen_phase', $new_phase);

                $phase_config_save = array(
                    'screen_phase_id' => $this->db2->insert_id()
                );

                $this->db2->insert('screen_phase_config', $phase_config_save);

                break;
        }

        $this->db2->trans_complete();
        redirect('screen_phases/display_phases');
    }

    public function delete_phase($phase_id, $null_transfer_error = false) {
        $phase_type = $this->get_phase_type($phase_id);
        $phases = $this->get_phases();
        $actual_phase = $phases[$phase_id];

        if (!array_key_exists($phase_id, $phases) ||
            $phase_type == 'final' ||
            $phase_type == 'first' ||
            ($phase_type == 'intermediate' && count($this->get_siblings($phase_id, $phases)) == 0 && $actual_phase['used'] != 0)) {
            redirect('screen_phases/display_phases');
        }


        $siblings = $this->get_siblings($phase_id, $phases);

        $data = array(
            'page' => 'screening/delete_screen_phase',
            'page_title' => "Delete screening phase",
            'top_buttons' => '<li><a href="'. base_url().'/screen_phases/display_phases" title="Close"><button class="btn btn-danger"><i class="fa fa-close"></i> </button></a></li>',
            'transfer' => false,
            'phase_id' => $phase_id,
            'null_transfer_error' => $null_transfer_error,
        );

        if (count($siblings) != 0) {
            $data['transfer'] = true;
            $data['transfer_phases'] = array_map(fn($phase) => $phase['phase_title'], $this->get_siblings($phase_id, $phases));
        }

        $this->load->view('shared/body', $data);
    }

    public function save_phase_deletion($arr = null) {
        $post_arr = $arr ?? $this->input->post();
        $phases = $this->get_phases();
        $this->db2 = $this->load->database(project_db(), TRUE);
        $this->db2->trans_start();

        if (isset($post_arr['transfer_phase'])) {
            $transfer_phase = $post_arr['transfer_phase'];
        } else if (count($this->get_siblings($post_arr['phase_id'], $phases)) != 0) {
            redirect('screen_phases/delete_phase/' . $post_arr['phase_id'] . '/1');
        } else {
            $transfer_phase = $phases[$post_arr['phase_id']]['next_phase'];
        }

        $phases[$post_arr['phase_id']]['screen_phase_active'] = 0;

        $parents_transfer_phase = [];

        if ($phases[$post_arr['phase_id']]['depth_level'] != 0) {
            foreach ($phases as $id => $phase) {
                if ($phase['next_phase'] == $post_arr['phase_id']) {
                    $phases[$id]['next_phase'] = $transfer_phase;
                    $parents_transfer_phase[] = $id;
                }
            }
        }


        if (isset($post_arr['transfer_phase'])) {
            $existing_parents = json_decode($phases[$transfer_phase]['parent'] ?? '[]', true) ?: [];
            $all_parents = array_values(array_unique(array_merge($existing_parents, $parents_transfer_phase)));
            $phases[$transfer_phase]['parent'] = json_encode($all_parents);

            $next_of_transfer = $phases[$transfer_phase]['next_phase'] ?? null;
            if (!empty($next_of_transfer) && isset($phases[$next_of_transfer])) {
                $next_parents = json_decode($phases[$next_of_transfer]['parent'] ?? '[]', true) ?: [];
                $updated_next_parents = array_diff($next_parents, [$post_arr['phase_id']]);
                $phases[$next_of_transfer]['parent'] = json_encode(array_values($updated_next_parents));
            }
        } else {
            $phases[$transfer_phase]['parent'] = json_encode($parents_transfer_phase);
        }

        if ($phases[$post_arr['phase_id']]['depth_level'] == 0) {
            $this->db2->where('imported_in_phase', $post_arr['phase_id'])
                ->set('imported_in_phase', $transfer_phase)
                ->update('paper');
        }

        if (!isset($post_arr['transfer_phase'])) {
            foreach ($phases as $id => $phase) {
                if ($phase['depth_level'] >= $phases[$post_arr['phase_id']]['depth_level']) {
                    $phases[$id]['depth_level']--;
                }
            }
        }

        if ($phases[$post_arr['phase_id']]['has_pending'] == 1) {
            $this->db2->where('next_phase', $post_arr['phase_id'])
                ->where('decison_id IN (SELECT MAX(decison_id) FROM screen_decison GROUP BY paper_id)', null, false)
                ->set('next_phase', $transfer_phase)
                ->update('screen_decison');
        }

        $this->db2->where([
            'screening_phase'  => $post_arr['phase_id'],
            'screening_status' => 'Pending'
        ])
            ->set('screening_active', 0)
            ->update('screening_paper');

        if ($phases[$post_arr['phase_id']]['has_pending'] == 1 || $this->db2->affected_rows() > 0) {
            $phases[$transfer_phase]['has_pending'] = 1;
        }

        $this->db2->update_batch('screen_phase', $phases, 'screen_phase_id');

        $this->db2->trans_complete();

        redirect('screen_phases/display_phases');
    }

    public function modify_phase($phase_id, $error = false) {
        $phases = $this->get_phases();

        if (!array_key_exists($phase_id, $phases) || $phases[$phase_id]['used'] == 1) {
            redirect('screen_phases/display_phases');
        }

        $table_config = get_table_configuration("screen_phase");
        $screen_type = $table_config['fields']['displayed_fields_vals']['input_select_values'];
        $actual_phase = $phases[$phase_id];
        $siblings = $this->get_siblings($phase_id, $phases);
        $editable_displayed_fields = true;
        $siblings_titles = array();

        foreach ($siblings as $id => $sibling) {
            $editable_displayed_fields = $sibling['used'] == 0;
            $siblings_titles[] = $sibling['phase_title'];

            if (!$editable_displayed_fields) {
                break;
            }
        }

        $model = new Screening_dataAccess();
        $config_type = $model->get_phase_config_type($phase_id);

        $data = array(
            'page' => 'screening/modify_screen_phase',
            'page_title' => "Modify screening phase",
            'top_buttons' => '<li><a href="'. base_url().'/screen_phases/display_phases" title="Close"><button class="btn btn-danger"><i class="fa fa-close"></i> </button></a></li>',
            'phase' => $actual_phase,
            'phase_id' => $phase_id,
            'editable_displayed_fields' => $editable_displayed_fields,
            'siblings_titles' => implode(", ", $siblings_titles),
            'displayed_fields' => $screen_type,
            'config_type' => $config_type,
            'error' => $error,
        );

        $this->load->view('shared/body', $data);
    }

    public function save_modification() {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $this->db2->trans_start();
        $post_arr = $this->input->post();

        if (empty($post_arr['displayed_fields']) || $post_arr['title'] == '') {
            redirect('screen_phases/modify_phase/' . $post_arr['phase_id'] . '/1');
        }

        $siblings = $this->get_siblings($post_arr['phase_id'], $this->get_phases());

        if (count($siblings) > 0) {
            foreach ($siblings as $id => $sibling) {
                $siblings[$id]['displayed_fields'] = implode('|', $post_arr['displayed_fields']);
            }

            $this->db2->update_batch('screen_phase', $siblings, 'screen_phase_id');
        }

        $this->db2->update('screen_phase',
            array('phase_title' => $post_arr['title'], 'displayed_fields' => implode('|', $post_arr['displayed_fields'])),
            array('screen_phase_id' => $post_arr['phase_id'])
        );

        $this->db2->trans_complete();
        redirect('screen_phases/display_phases');
    }

    public function replace_phase($phase_id, $null_title_error = false) {
        $phases = $this->get_phases();

        if (!array_key_exists($phase_id, $phases) || $phases[$phase_id]['used'] == 0) {
            redirect('screen_phases/display_phases');
        }

        $actual_phase = $phases[$phase_id];
        $data = array(
            'page' => 'screening/replace_screen_phase',
            'page_title' => "Replace screening phase",
            'phase_id' => $phase_id,
            'phase_title' => $actual_phase['phase_title'],
            'null_title_error' => $null_title_error,
        );

        $this->load->view('shared/body', $data);
    }

    public function save_replacement() {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $this->db2->trans_start();
        $post_arr = $this->input->post();

        if (empty($post_arr['new_title'])) {
            redirect('screen_phases/replace_phase/' . $post_arr['phase_id'] . '/1');
        }

        $phases = $this->get_phases();
        $actual_phase = $phases[$post_arr['phase_id']];

        $actual_phase['phase_title'] = $post_arr['new_title'];
        $actual_phase['used'] = 0;
        unset($actual_phase['screen_phase_id']);

        $this->db2->insert('screen_phase', $actual_phase);
        $insert_id = $this->db2->insert_id();

        $next_phase_id = $phases[$post_arr['phase_id']]['next_phase'];
        $next_phase_parents = json_decode($phases[$next_phase_id]['parent']);
        $index = array_search($post_arr['phase_id'], $next_phase_parents);
        $next_phase_parents[$index] = $insert_id;
        $phases[$next_phase_id]['parent'] = json_encode($next_phase_parents);

        $this->db2->update("screen_phase", $phases[$next_phase_id], array('screen_phase_id' => $next_phase_id));
        $this->db2->update('screen_phase_config', array('screen_phase_id' => $insert_id), array('screen_phase_id' => $post_arr['phase_id']));

        $this->db2->trans_complete();

        $data = array(
            'phase_id' => $post_arr['phase_id'],
            'transfer_phase' => $insert_id,
        );

        $this->save_phase_deletion($data);
    }

    //switch phase config type between 'Default'and 'Custom'
    public function toggle_phase_config($phase_id) {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $model = new Screening_dataAccess();
        $config_type = $model->get_phase_config_type($phase_id);
        $config = get_appconfig();

        if ($config_type == 'Default') {
            $config_save = array(
                'config_type' => 'Custom',
                'screening_result_on' => $config['screening_result_on'],
                'assign_papers_on' => $config['assign_papers_on'],
                'screening_reviewer_number' => $config['screening_reviewer_number'],
                'screening_inclusion_mode' => $config['screening_inclusion_mode'],
                'screening_conflict_type' => $config['screening_conflict_type'],
                'screening_screening_conflict_resolution' => $config['screening_screening_conflict_resolution'],
                'use_kappa' => $config['use_kappa'],
                'screening_validation_on' => $config['screening_validation_on'],
                'screening_validator_assignment_type' => $config['screening_validator_assignment_type'],
                'assign_to_non_screened_validator_on' => $config['assign_to_non_screened_validator_on'],
                'validation_default_percentage' => $config['validation_default_percentage']
            );
            $this->db2->where('screen_phase_id', $phase_id);
            $this->db2->update('screen_phase_config', $config_save);
        }

        if ($config_type == 'Custom') {
            $this->db2->where('screen_phase_id', $phase_id);
            $this->db2->update('screen_phase_config', array('config_type' => 'Default'));

            $this->db2->where('screen_phase_id', $phase_id);
            $query = $this->db2->get('screen_phase_config');
            $current_custom_config = ($query->num_rows() > 0) ? $query->row_array() : array(); // Return the first row as an associative array
            $this->edit_screening_config($current_custom_config);
        }


        redirect('screen_phases/modify_phase/' . $phase_id);
    }

    public function edit_screening_config($current_custom_config = null) {
        $model = new Screening_dataAccess();
        $post_arr = $current_custom_config ? $current_custom_config : $this->input->post();
        $phase_id = !array_key_exists('screen_phase_id', $post_arr) ? null : $post_arr['screen_phase_id'];
        $config_type = $model->get_phase_config_type($phase_id);
        $current_inclusion_mode = $model->get_phase_config_value($phase_id, 'screening_inclusion_mode');
        $new_inclusion_mode = $post_arr['screening_inclusion_mode'];
        $redirect_url = $phase_id ? 'screen_phases/modify_phase/' . $phase_id : 'element/display_element/configurations/1';
        //Criterias must exist for modes other than None
        if ($new_inclusion_mode != 'None' && $model->count_inclusion_criteria() == 0) {
            set_top_msg('You need to add inclusion criteria before making this change', "error");
            redirect($redirect_url);
        }

        $affected_phases = $model->get_affected_phases($phase_id);

        //count already screened papers
        $this->db_current->from('screening_paper');
        $this->db_current->where('screening_status', 'Done');
        if (!empty($affected_phases)) {
            $this->db_current->where_in('screening_phase', $affected_phases);
        } else {
            $already_screened_papers = 0;
        }
        $query = $this->db_current->get();
        if ($query->num_rows() > 0) {
            $already_screened_papers = $query->num_rows();
        } else {
            $already_screened_papers = 0;
        }

        //Save if no papers already screened or if there is no conflict
        if ($already_screened_papers == 0 || !$this->mode_conflict_exists($current_inclusion_mode, $new_inclusion_mode)) {
            if ($already_screened_papers != 0 && $current_inclusion_mode == 'All' && $new_inclusion_mode == 'Any') {
                //insert all criteria in mapping table when going from 'All' to 'Any' and there are screened papers.
                $model->set_all_criteria($affected_phases);
            }
            $model->edit_screening_config($post_arr, $phase_id, $affected_phases);
            redirect($redirect_url);
        }  else {
            $this->db2->where_in('screen_phase_id', $affected_phases);
            $query = $this->db2->select('phase_title')->get('screen_phase');
            $affected_phases_titles = $query->result_array();
            //If screening already started and there is conflict, ask user how to solve it
            $data['post_arr'] = $post_arr;
            $data['phases_id'] = $affected_phases;
            $data['phases_title'] = $affected_phases_titles;
            $data['current_inclusion_mode'] = $current_inclusion_mode;
            $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
            $data['left_menu_perspective']='left_menu_screening';
            $data['project_perspective']='screening';
            $data['page'] = 'screening/inclusion_mode_conflict';
            $this->load->view('shared/body', $data);
        }

    }

    private function mode_conflict_exists($current_mode, $new_mode) {
        $mode_string = $current_mode . $new_mode;
        if ($mode_string == 'NoneOne' || $mode_string == 'NoneAny' || $mode_string == 'AnyOne' || $mode_string == 'AllOne') return true;
        return false;
    }

    private function are_siblings_equivalent($phase)
    {

        $siblings_query = $this->db2->select('screen_phase_id, displayed_fields')
            ->where('screen_phase_active', 1)
            ->where('depth_level', $phase['depth_level'])
            ->get('screen_phase');

        $siblings = $siblings_query->result_array();

        foreach ($siblings as $sibling) {
            if ($phase['displayed_fields'] != $sibling['displayed_fields']) {
                return false;
            }
        }

        return true;
    }

    private function are_descendants_unassigned($phase)
    {
        if (empty($phase)) {
            return true;
        }

        if ($phase['used'] != 0 || $phase['has_pending'] != 0) {
            return false;
        }

        if ($phase['next_phase'] == null) {
            return true;
        }

        $child_query = $this->db2->select('screen_phase_id, next_phase, used, has_pending')
            ->where('screen_phase_active', 1)
            ->where('screen_phase_id', $phase['next_phase'])
            ->get('screen_phase');

        return $this->are_descendants_unassigned($child_query->row_array());
    }

    private function number_of_parents($phase_id, $phases_list) {
        $nb_parents = count(array_filter($phases_list, fn($phase) =>
            is_array($phase) && $phase['next_phase'] == $phase_id && $phase['screen_phase_active'] == 1
        ));

        return $nb_parents;
    }

    private function get_siblings($phase_id, $phases) {
        $actual_phase = $phases[$phase_id];

        return array_filter($phases, fn($phase) =>
            $phase['screen_phase_id'] != $phase_id &&
            $phase['depth_level'] == $actual_phase['depth_level'] &&
            $phase['screen_phase_active'] == 1
        );
    }

    private function get_phases() {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $this->db2->trans_start();

        $phases_query = $this->db2->select('*')
            ->where('screen_phase_active', 1)
            ->get('screen_phase');

        $raw_phases = $phases_query->result_array();

        $phases = array();
        foreach ($raw_phases as $raw_phase) {
            $phases[$raw_phase['screen_phase_id']] = $raw_phase;
        }

        return $phases;
    }

    private function get_phase_type($phase_id) {
        $phases = $this->get_phases();
        $phase = $phases[$phase_id];

        if (count($phases) == 1) {
            return 'first';
        } else if ($phase['next_phase'] == null) {
            return 'final';
        } else if ($phase['depth_level'] == 0) {
            return 'initial';
        } else {
            return 'intermediate';
        }
    }
}
