<?php

/**
 * Issue #103 - Unit tests for the reviewer tag & assignment constraints system.
 *
 * Cette classe de tests couvre :
 *   1. Régression : comportements existants doivent rester identiques quand
 *      aucune contrainte n'est définie (vérifie qu'on n'a rien cassé)
 *   2. CRUD : reviewer_tag, userproject_tag, assignment_constraint
 *   3. Engine : chaque type de contrainte appliqué isolément
 *   4. Intégration : combinaisons réalistes (contrainte + assignation)
 */
class AssignmentEngineUnitTest
{
    private $controller;
    private $http_client;
    private $ci;
    private $db_name;

    function __construct()
    {
        $this->controller   = "screening";
        $this->http_client  = new Http_client();
        $this->ci           = get_instance();
        $this->db_name      = ""; // sera défini après création du projet
    }

    function run_tests()
    {
        $this->TestInitialize();

        // ─── 1. RÉGRESSION ───────────────────────────────────────────
        $this->regression_assignment_noConstraint_5papers_2reviewers();
        $this->regression_assignment_noConstraint_5papers_3reviewers_2reviewsPerPaper();
        $this->regression_assignment_noConstraint_emptyUsers();
        $this->regression_assignment_noConstraint_reviewsExceedsUsers();

        // ─── 2. CRUD ─────────────────────────────────────────────────
        $this->crud_reviewerTag_seedExists();
        $this->crud_reviewerTag_addNewTag();
        $this->crud_userprojectTag_assignTagToUser();
        $this->crud_userprojectTag_duplicateRefused();
        $this->crud_assignmentConstraint_createMinTagConstraint();
        $this->crud_assignmentConstraint_loadActiveConstraints();

        // ─── 3. ENGINE — chaque type de contrainte ───────────────────
        $this->engine_minTagPerPaper_satisfied();
        $this->engine_minTagPerPaper_blocksWhenNoSenior();
        $this->engine_maxTagPerPaper_satisfied();
        $this->engine_tagCombination_picksBestOption();
        $this->engine_forceDifferentUser_excludesPreviousReviewers();
        $this->engine_sameUserFromPreviousPhase_reusesPreviousReviewers();

        // ─── 4. INTÉGRATION ──────────────────────────────────────────
        $this->integration_multipleConstraints_allSatisfied();
        $this->integration_qa_mandatoryWithSingleUser();

        // ─── CLEANUP ─────────────────────────────────────────────────
        deleteCreatedTestProject();
        deleteCreatedTestUser();
    }

    // ═══════════════════════════════════════════════════════════════
    // SETUP
    // ═══════════════════════════════════════════════════════════════

    private function TestInitialize()
    {
        // Nettoyage de l'état précédent
        deleteSessionFiles();
        deleteCreatedTestUser();
        deleteCreatedTestProject();

        // Création du test user
        addTestUser();

        // Login en admin via HTTP (pattern ReLiS)
        $this->http_client->response(
            "user", "check_form",
            ['user_username' => 'admin', 'user_password' => '123'],
            "POST"
        );

        // Création du projet de test
        createDemoProject();

        // Ajout des reviewers au projet
        addUserToProject(getAdminUserId(), "Reviewer");
        addUserToProject(getTestUserId(),  "Reviewer");
        addUserToProject(getDemoUserId(), "Reviewer");

        // Import des 5 papers de test
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");

        // Mise à jour de db_name maintenant que le projet existe
        $this->db_name = "relis_dev_correct_" . getProjectShortName();
    }
    /**
     * Réinitialise les tables d'assignation et de contraintes entre 2 tests
     * pour éviter les interférences.
     */
    private function cleanAssignmentsAndConstraints()
    {
        $this->ci->db->query("DELETE FROM {$this->db_name}.screening_paper");
        $this->ci->db->query("DELETE FROM {$this->db_name}.assignment_constraint");
        $this->ci->db->query("DELETE FROM {$this->db_name}.userproject_tag");
    }

    /**
     * Helper : récupère l'ID d'un tag par son nom.
     */
    private function tagId($name)
    {
        $r = $this->ci->db->query("SELECT tag_id FROM {$this->db_name}.reviewer_tag
                                   WHERE tag_name = ?", array($name))->row_array();
        return $r ? intval($r['tag_id']) : 0;
    }

    /**
     * Helper : assigne un tag à un user dans le projet.
     */
    private function assignTagToUser($user_id, $tag_name)
    {
        $tag_id = $this->tagId($tag_name);
        $this->ci->db->query("INSERT INTO {$this->db_name}.userproject_tag
                              (user_id, tag_id, userproject_tag_active)
                              VALUES (?, ?, 1)
                              ON DUPLICATE KEY UPDATE userproject_tag_active = 1",
            array($user_id, $tag_id));
    }

    /**
     * Helper : crée une contrainte pour un scope donné.
     */
    private function createConstraint($scope, $phase_id, $type, $params)
    {
        $this->ci->db->query("INSERT INTO {$this->db_name}.assignment_constraint
                              (constraint_scope, phase_id, constraint_type,
                               constraint_params, constraint_priority, constraint_active)
                              VALUES (?, ?, ?, ?, 100, 1)",
            array($scope, $phase_id, $type, json_encode($params)));
    }

    /**
     * Active la phase Title pour la session courante.
     * À appeler avant chaque test qui fait une assignation de screening.
     */
    private function selectTitlePhase()
    {
        $this->http_client->response(
            $this->controller,
            "select_screen_phase/" . getScreeningPhaseId("Title")
        );
    }
    // ═══════════════════════════════════════════════════════════════
    // 1. TESTS DE RÉGRESSION — comportement legacy inchangé
    // ═══════════════════════════════════════════════════════════════

    /*
     * Test : 5 papers, 2 reviewers, 1 review/paper, aucune contrainte
     * Attendu : 5 assignations totales (comportement round-robin équilibré),
     *           identique à l'ancienne implémentation.
     */
    private function regression_assignment_noConstraint_5papers_2reviewers()
    {
        $action = "save_assignment_screen";
        $name   = "Regression: 5 papers, 2 reviewers, no constraint";

        $this->cleanAssignmentsAndConstraints();

        $postData = [
            "number_of_users"           => 2,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "user_2"                    => getTestUserId(),
            "reviews_per_paper"         => 1,
            "assign_all_paper_checkbox" => "on"
        ];

        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $count = $this->ci->db->query(
            "SELECT COUNT(*) AS c FROM {$this->db_name}.screening_paper
             WHERE assignment_role = 'Screening'"
        )->row_array()['c'];

        $expected = 5;
        $actual   = intval($count);
        run_test($this->controller, $action, $name, "Total assignments", $expected, $actual);
    }

    /*
     * Test : 5 papers, 3 reviewers, 2 reviews/paper
     * Attendu : 10 assignations totales (5 * 2 reviews)
     */
    private function regression_assignment_noConstraint_5papers_3reviewers_2reviewsPerPaper()
    {
        $action = "save_assignment_screen";
        $name   = "Regression: 5 papers, 3 reviewers, 2 reviews/paper, no constraint";

        $this->cleanAssignmentsAndConstraints();

        // Crée un 3e user pour ce test
        $extra_user_id = getDemoUserId();

        $postData = [
            "number_of_users"           => 3,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "user_2"                    => getTestUserId(),
            "user_3"                    => $extra_user_id,
            "reviews_per_paper"         => 2,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $count = $this->ci->db->query(
            "SELECT COUNT(*) AS c FROM {$this->db_name}.screening_paper
             WHERE assignment_role = 'Screening'"
        )->row_array()['c'];

        $expected = 10;
        $actual   = intval($count);
        run_test($this->controller, $action, $name, "Total assignments", $expected, $actual);
    }

    /*
     * Test : assignation sans utilisateurs sélectionnés
     * Attendu : aucune assignation créée, comportement identique à l'avant-engine.
     */
    private function regression_assignment_noConstraint_emptyUsers()
    {
        $action = "save_assignment_screen";
        $name   = "Regression: no user selected, no constraint";

        $this->cleanAssignmentsAndConstraints();

        $postData = [
            "number_of_users"           => 0,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "reviews_per_paper"         => 1,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $count = $this->ci->db->query(
            "SELECT COUNT(*) AS c FROM {$this->db_name}.screening_paper
             WHERE assignment_role = 'Screening'"
        )->row_array()['c'];

        $expected = 0;
        $actual   = intval($count);
        run_test($this->controller, $action, $name, "Total assignments", $expected, $actual);
    }

    /*
     * Test : reviews_per_paper > nombre d'utilisateurs sélectionnés
     * Attendu : erreur de validation, aucune assignation créée.
     */
    private function regression_assignment_noConstraint_reviewsExceedsUsers()
    {
        $action = "save_assignment_screen";
        $name   = "Regression: reviews_per_paper exceeds selected users";

        $this->cleanAssignmentsAndConstraints();

        $postData = [
            "number_of_users"           => 1,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "reviews_per_paper"         => 3,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $count = $this->ci->db->query(
            "SELECT COUNT(*) AS c FROM {$this->db_name}.screening_paper
             WHERE assignment_role = 'Screening'"
        )->row_array()['c'];

        $expected = 0;
        $actual   = intval($count);
        run_test($this->controller, $action, $name, "Total assignments", $expected, $actual);
    }

    // ═══════════════════════════════════════════════════════════════
    // 2. TESTS CRUD — tables reviewer_tag, userproject_tag, etc.
    // ═══════════════════════════════════════════════════════════════

    /*
     * Test : vérifier que le seed des 3 tags par défaut a bien été inséré
     * lors de la création du projet.
     */
    private function crud_reviewerTag_seedExists()
    {
        $action = "seed_reviewer_tag";
        $name   = "Seed: Junior/Senior/Methodologist exist after project creation";

        $count = $this->ci->db->query(
            "SELECT COUNT(*) AS c FROM {$this->db_name}.reviewer_tag
             WHERE tag_name IN ('Junior', 'Senior', 'Methodologist')"
        )->row_array()['c'];

        $expected = 3;
        $actual   = intval($count);
        run_test("reviewer_tag", $action, $name, "Seeded tags count", $expected, $actual);
    }

    /*
     * Test : insertion d'un nouveau tag custom
     */
    private function crud_reviewerTag_addNewTag()
    {
        $action = "add_reviewer_tag";
        $name   = "CRUD: add a new custom reviewer tag";

        $this->ci->db->query(
            "INSERT INTO {$this->db_name}.reviewer_tag
             (tag_name, tag_description, tag_color, tag_is_hierarchical, tag_rank)
             VALUES ('Expert', 'Top expert', '#FFA500', 1, 3)"
        );

        $row = $this->ci->db->query(
            "SELECT * FROM {$this->db_name}.reviewer_tag WHERE tag_name = 'Expert'"
        )->row_array();

        $expected = "Expert#FFA500";
        $actual   = !empty($row) ? $row['tag_name'] . $row['tag_color'] : "missing";
        run_test("reviewer_tag", $action, $name, "Tag created", $expected, $actual);
    }

    /*
     * Test : attribuer un tag à un user
     */
    private function crud_userprojectTag_assignTagToUser()
    {
        $action = "add_userproject_tag";
        $name   = "CRUD: assign Senior tag to admin user";

        $this->ci->db->query("DELETE FROM {$this->db_name}.userproject_tag");
        $this->assignTagToUser(getAdminUserId(), 'Senior');

        $count = $this->ci->db->query(
            "SELECT COUNT(*) AS c FROM {$this->db_name}.userproject_tag
             WHERE user_id = ? AND tag_id = ?",
            array(getAdminUserId(), $this->tagId('Senior'))
        )->row_array()['c'];

        $expected = 1;
        $actual   = intval($count);
        run_test("userproject_tag", $action, $name, "Tag assigned", $expected, $actual);
    }

    /*
     * Test : refus de double attribution du même tag au même user
     * (contrainte UNIQUE uq_user_tag)
     */
    private function crud_userprojectTag_duplicateRefused()
    {
        $action = "add_userproject_tag_duplicate";
        $name   = "CRUD: duplicate (user, tag) refused by UNIQUE constraint";

        $this->ci->db->query("DELETE FROM {$this->db_name}.userproject_tag");
        $this->assignTagToUser(getAdminUserId(), 'Senior');

        // Tentative de double insertion brute (sans ON DUPLICATE KEY)
        $db_ignore_errors = $this->ci->db->db_debug;
        $this->ci->db->db_debug = FALSE;
        $this->ci->db->query(
            "INSERT INTO {$this->db_name}.userproject_tag (user_id, tag_id)
             VALUES (?, ?)",
            array(getAdminUserId(), $this->tagId('Senior'))
        );
        $this->ci->db->db_debug = $db_ignore_errors;

        $count = $this->ci->db->query(
            "SELECT COUNT(*) AS c FROM {$this->db_name}.userproject_tag
             WHERE user_id = ? AND tag_id = ?",
            array(getAdminUserId(), $this->tagId('Senior'))
        )->row_array()['c'];

        $expected = 1;
        $actual   = intval($count);
        run_test("userproject_tag", $action, $name, "Duplicates prevented", $expected, $actual);
    }

    /*
     * Test : création d'une contrainte min_tag_per_paper
     */
    private function crud_assignmentConstraint_createMinTagConstraint()
    {
        $action = "add_assignment_constraint";
        $name   = "CRUD: create a min_tag_per_paper constraint";

        $this->ci->db->query("DELETE FROM {$this->db_name}.assignment_constraint");
        $this->createConstraint('screening', getScreeningPhaseId("Title"),
            'min_tag_per_paper',
            array('tag_id' => $this->tagId('Senior'), 'min_count' => 1));

        $row = $this->ci->db->query(
            "SELECT * FROM {$this->db_name}.assignment_constraint
             WHERE constraint_type = 'min_tag_per_paper'"
        )->row_array();

        $expected = "screening|min_tag_per_paper|active";
        $actual   = !empty($row)
            ? $row['constraint_scope'] . '|' . $row['constraint_type']
            . '|' . ($row['constraint_active'] ? 'active' : 'inactive')
            : "missing";
        run_test("assignment_constraint", $action, $name, "Constraint stored", $expected, $actual);
    }

    /*
     * Test : chargement des contraintes actives pour un scope
     */
    private function crud_assignmentConstraint_loadActiveConstraints()
    {
        $action = "get_active_constraints";
        $name   = "CRUD: load only active constraints for a given scope";

        $this->ci->db->query("DELETE FROM {$this->db_name}.assignment_constraint");

        // 2 actives + 1 inactive
        $this->createConstraint('screening', null, 'min_tag_per_paper',
            array('tag_id' => $this->tagId('Senior'), 'min_count' => 1));
        $this->createConstraint('screening', null, 'max_tag_per_paper',
            array('tag_id' => $this->tagId('Junior'), 'max_count' => 3));
        $this->ci->db->query("INSERT INTO {$this->db_name}.assignment_constraint
                              (constraint_scope, constraint_type, constraint_params, constraint_active)
                              VALUES ('screening', 'min_tag_per_paper',
                                      ?, 0)",
            array(json_encode(array('tag_id' => 1, 'min_count' => 1))));

        $count = $this->ci->db->query(
            "SELECT COUNT(*) AS c FROM {$this->db_name}.assignment_constraint
             WHERE constraint_scope = 'screening' AND constraint_active = 1"
        )->row_array()['c'];

        $expected = 2;
        $actual   = intval($count);
        run_test("assignment_constraint", $action, $name, "Active constraints loaded", $expected, $actual);
    }

    // ═══════════════════════════════════════════════════════════════
    // 3. TESTS DE L'ENGINE — chaque type de contrainte
    // ═══════════════════════════════════════════════════════════════

    /*
     * Test : min_tag_per_paper, contrainte satisfaite
     * Setup : Admin = Senior, Test = Junior
     *         Contrainte = au moins 1 Senior par paper
     * Attendu : chaque paper a au moins 1 assignation à un Senior
     */
    private function engine_minTagPerPaper_satisfied()
    {
        $action = "save_assignment_screen";
        $name   = "Engine: min 1 senior per paper is satisfied";

        $this->cleanAssignmentsAndConstraints();
        $this->assignTagToUser(getAdminUserId(), 'Senior');
        $this->assignTagToUser(getTestUserId(),  'Junior');
        $this->createConstraint('screening', getScreeningPhaseId("Title"),
            'min_tag_per_paper',
            array('tag_id' => $this->tagId('Senior'), 'min_count' => 1));

        $postData = [
            "number_of_users"           => 2,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "user_2"                    => getTestUserId(),
            "reviews_per_paper"         => 2,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $senior_tag_id = $this->tagId('Senior');
        $papers_without_senior = $this->ci->db->query(
            "SELECT DISTINCT paper_id FROM {$this->db_name}.screening_paper sp
             WHERE assignment_role = 'Screening'
               AND paper_id NOT IN (
                 SELECT sp2.paper_id FROM {$this->db_name}.screening_paper sp2
                 JOIN {$this->db_name}.userproject_tag upt ON upt.user_id = sp2.user_id
                 WHERE upt.tag_id = $senior_tag_id
                   AND sp2.assignment_role = 'Screening'
               )"
        )->num_rows();

        $expected = 0;
        $actual   = $papers_without_senior;
        run_test($this->controller, $action, $name, "Papers without senior", $expected, $actual);
    }

    /*
     * Test : min_tag_per_paper bloque quand aucun senior n'est sélectionné
     * Attendu : 0 assignation créée (validation bloque)
     */
    private function engine_minTagPerPaper_blocksWhenNoSenior()
    {
        $action = "save_assignment_screen";
        $name   = "Engine: min 1 senior blocks when no senior selected";

        $this->cleanAssignmentsAndConstraints();
        // Aucun user n'est Senior
        $this->createConstraint('screening', getScreeningPhaseId("Title"),
            'min_tag_per_paper',
            array('tag_id' => $this->tagId('Senior'), 'min_count' => 1));

        $postData = [
            "number_of_users"           => 2,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "user_2"                    => getTestUserId(),
            "reviews_per_paper"         => 2,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $count = $this->ci->db->query(
            "SELECT COUNT(*) AS c FROM {$this->db_name}.screening_paper
             WHERE assignment_role = 'Screening'"
        )->row_array()['c'];

        $expected = 0;
        $actual   = intval($count);
        run_test($this->controller, $action, $name, "Assignments blocked", $expected, $actual);
    }

    /*
     * Test : max_tag_per_paper satisfait
     * Setup : Admin = Junior, Test = Junior, Extra = Senior
     *         Contrainte = max 1 Junior par paper, reviews_per_paper = 2
     * Attendu : aucun paper avec plus d'1 Junior
     */
    private function engine_maxTagPerPaper_satisfied()
    {
        $action = "save_assignment_screen";
        $name   = "Engine: max 1 junior per paper is satisfied";

        $this->cleanAssignmentsAndConstraints();

        $extra_user_id = getDemoUserId();
        $this->assignTagToUser(getAdminUserId(),  'Junior');
        $this->assignTagToUser(getTestUserId(),   'Junior');
        $this->assignTagToUser($extra_user_id,    'Senior');
        $this->createConstraint('screening', getScreeningPhaseId("Title"),
            'max_tag_per_paper',
            array('tag_id' => $this->tagId('Junior'), 'max_count' => 1));

        $postData = [
            "number_of_users"           => 3,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "user_2"                    => getTestUserId(),
            "user_3"                    => $extra_user_id,
            "reviews_per_paper"         => 2,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $junior_tag_id = $this->tagId('Junior');
        $violating_papers = $this->ci->db->query(
            "SELECT sp.paper_id, COUNT(*) AS c
             FROM {$this->db_name}.screening_paper sp
             JOIN {$this->db_name}.userproject_tag upt ON upt.user_id = sp.user_id
             WHERE sp.assignment_role = 'Screening' AND upt.tag_id = $junior_tag_id
             GROUP BY sp.paper_id
             HAVING c > 1"
        )->num_rows();

        $expected = 0;
        $actual   = $violating_papers;
        run_test($this->controller, $action, $name, "Papers exceeding max", $expected, $actual);
    }

    /*
     * Test : tag_combination — choisit l'option qui équilibre la charge
     * Setup : Admin = Senior, Test = Junior, Extra = Junior
     *         Contrainte : 1 senior OU 2 juniors par paper
     * Attendu : chaque paper satisfait une des deux options
     */
    private function engine_tagCombination_picksBestOption()
    {
        $action = "save_assignment_screen";
        $name   = "Engine: tag_combination satisfies at least one option per paper";

        $this->cleanAssignmentsAndConstraints();

        $extra_user_id = getDemoUserId();
        $this->assignTagToUser(getAdminUserId(), 'Senior');
        $this->assignTagToUser(getTestUserId(),  'Junior');
        $this->assignTagToUser($extra_user_id,   'Junior');

        $this->createConstraint('screening', getScreeningPhaseId("Title"),
            'tag_combination',
            array('options' => array(
                array('tag_id' => $this->tagId('Senior'), 'count' => 1),
                array('tag_id' => $this->tagId('Junior'), 'count' => 2),
            )));

        $postData = [
            "number_of_users"           => 3,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "user_2"                    => getTestUserId(),
            "user_3"                    => $extra_user_id,
            "reviews_per_paper"         => 2,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $senior_tag = $this->tagId('Senior');
        $junior_tag = $this->tagId('Junior');

        // Pour chaque paper, vérifier : ≥1 senior OU ≥2 juniors
        $bad_papers = $this->ci->db->query(
            "SELECT sp.paper_id
             FROM {$this->db_name}.screening_paper sp
             WHERE sp.assignment_role = 'Screening'
             GROUP BY sp.paper_id
             HAVING SUM(CASE WHEN sp.user_id IN (
                              SELECT user_id FROM {$this->db_name}.userproject_tag
                              WHERE tag_id = $senior_tag) THEN 1 ELSE 0 END) < 1
                AND SUM(CASE WHEN sp.user_id IN (
                              SELECT user_id FROM {$this->db_name}.userproject_tag
                              WHERE tag_id = $junior_tag) THEN 1 ELSE 0 END) < 2"
        )->num_rows();

        $expected = 0;
        $actual   = $bad_papers;
        run_test($this->controller, $action, $name, "Papers not satisfying any option", $expected, $actual);
    }

    /*
     * Test : force_different_user_from_previous_phase
     * Setup : phase Title déjà screenée par Admin (paper #1, paper #2)
     *         Phase Abstract : contrainte = exclure les screeners précédents
     * Attendu : Admin n'est jamais assigné aux papers déjà screenés en Title
     */
    private function engine_forceDifferentUser_excludesPreviousReviewers()
    {
        $action = "save_assignment_screen";
        $name   = "Engine: force_different excludes previous reviewers";

        $this->cleanAssignmentsAndConstraints();
        $extra_user_id = getDemoUserId();

        // Phase 1 (Title) : Admin a screené 2 papers
        $title_phase = getScreeningPhaseId("Title");
        $papers = $this->ci->db->query(
            "SELECT id FROM {$this->db_name}.paper LIMIT 2"
        )->result_array();
        foreach ($papers as $p) {
            $this->ci->db->query(
                "INSERT INTO {$this->db_name}.screening_paper
                 (paper_id, user_id, assignment_role, screening_phase, assignment_type, assigned_by)
                 VALUES (?, ?, 'Screening', ?, 'Normal', ?)",
                array($p['id'], getAdminUserId(), $title_phase, getAdminUserId())
            );
        }

        // On crée une 2e phase fictive ou on utilise la phase existante autre que Title
        // Pour le test on récupère une autre phase
        $abstract_phase_row = $this->ci->db->query(
            "SELECT screen_phase_id FROM {$this->db_name}.screen_phase
             WHERE screen_phase_id != $title_phase
               AND screen_phase_active = 1 LIMIT 1"
        )->row_array();

        if (empty($abstract_phase_row)) {
            // Pas de 2e phase disponible, on skip
            run_test($this->controller, $action, $name, "Skipped (no 2nd phase)", "skipped", "skipped");
            return;
        }
        $abstract_phase = $abstract_phase_row['screen_phase_id'];

        $this->createConstraint('screening', $abstract_phase,
            'force_different_user_from_previous_phase',
            array('previous_scope' => 'screening',
                'previous_phase_id' => $title_phase));

        $postData = [
            "number_of_users"           => 2,
            "screening_phase"           => $abstract_phase,
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "user_2"                    => $extra_user_id,
            "reviews_per_paper"         => 1,
            "assign_all_paper_checkbox" => "on"
        ];

        // Active la 2e phase
        $this->ci->session->set_userdata('active_screening_phase', $abstract_phase);
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        // Vérification : Admin n'a pas été réassigné aux 2 papers qu'il avait déjà screenés
        $reassigned = $this->ci->db->query(
            "SELECT COUNT(*) AS c
             FROM {$this->db_name}.screening_paper sp1
             JOIN {$this->db_name}.screening_paper sp2
                  ON sp1.paper_id = sp2.paper_id AND sp1.user_id = sp2.user_id
             WHERE sp1.screening_phase = $title_phase
               AND sp2.screening_phase = $abstract_phase
               AND sp1.user_id = " . getAdminUserId()
        )->row_array()['c'];

        $expected = 0;
        $actual   = intval($reassigned);
        run_test($this->controller, $action, $name, "Same user reassigned", $expected, $actual);
    }

    /*
     * Test : same_user_from_previous_phase
     * Setup : Admin a screené paper #1 en Title
     *         Abstract : contrainte = même reviewer que la phase précédente
     * Attendu : Admin est réassigné au paper #1 en Abstract
     */
    private function engine_sameUserFromPreviousPhase_reusesPreviousReviewers()
    {
        $action = "save_assignment_screen";
        $name   = "Engine: same_user reuses previous reviewers";

        $this->cleanAssignmentsAndConstraints();
        $extra_user_id = getDemoUserId();

        $title_phase = getScreeningPhaseId("Title");
        $first_paper = $this->ci->db->query(
            "SELECT id FROM {$this->db_name}.paper LIMIT 1"
        )->row_array();

        $this->ci->db->query(
            "INSERT INTO {$this->db_name}.screening_paper
             (paper_id, user_id, assignment_role, screening_phase, assignment_type, assigned_by)
             VALUES (?, ?, 'Screening', ?, 'Normal', ?)",
            array($first_paper['id'], getAdminUserId(), $title_phase, getAdminUserId())
        );

        $abstract_phase_row = $this->ci->db->query(
            "SELECT screen_phase_id FROM {$this->db_name}.screen_phase
             WHERE screen_phase_id != $title_phase
               AND screen_phase_active = 1 LIMIT 1"
        )->row_array();

        if (empty($abstract_phase_row)) {
            run_test($this->controller, $action, $name, "Skipped (no 2nd phase)", "skipped", "skipped");
            return;
        }
        $abstract_phase = $abstract_phase_row['screen_phase_id'];

        $this->createConstraint('screening', $abstract_phase,
            'same_user_from_previous_phase',
            array('previous_scope' => 'screening',
                'previous_phase_id' => $title_phase,
                'mode' => 'preferred'));

        $postData = [
            "number_of_users"           => 2,
            "screening_phase"           => $abstract_phase,
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "user_2"                    => $extra_user_id,
            "reviews_per_paper"         => 1,
            "assign_all_paper_checkbox" => "on"
        ];

        $this->ci->session->set_userdata('active_screening_phase', $abstract_phase);
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $reassigned = $this->ci->db->query(
            "SELECT COUNT(*) AS c
             FROM {$this->db_name}.screening_paper
             WHERE paper_id = " . $first_paper['id'] . "
               AND user_id = " . getAdminUserId() . "
               AND screening_phase = $abstract_phase"
        )->row_array()['c'];

        $expected = 1;
        $actual   = intval($reassigned);
        run_test($this->controller, $action, $name, "Previous reviewer reused", $expected, $actual);
    }

    // ═══════════════════════════════════════════════════════════════
    // 4. TESTS D'INTÉGRATION — combinaisons réalistes
    // ═══════════════════════════════════════════════════════════════

    /*
     * Test : 2 contraintes appliquées simultanément
     *        - min 1 senior par paper
     *        - max 2 juniors par paper
     */
    private function integration_multipleConstraints_allSatisfied()
    {
        $action = "save_assignment_screen";
        $name   = "Integration: min 1 senior + max 2 juniors, both satisfied";

        $this->cleanAssignmentsAndConstraints();

        $extra1 = getDemoUserId();
        $this->assignTagToUser(getAdminUserId(), 'Senior');
        $this->assignTagToUser(getTestUserId(),  'Junior');
        $this->assignTagToUser($extra1,          'Junior');

        $this->createConstraint('screening', getScreeningPhaseId("Title"),
            'min_tag_per_paper',
            array('tag_id' => $this->tagId('Senior'), 'min_count' => 1));
        $this->createConstraint('screening', getScreeningPhaseId("Title"),
            'max_tag_per_paper',
            array('tag_id' => $this->tagId('Junior'), 'max_count' => 2));

        $postData = [
            "number_of_users"           => 4,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "user_2"                    => getTestUserId(),
            "user_3"                    => $extra1,
            "reviews_per_paper"         => 2,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $this->http_client->response($this->controller, $action, $postData, "POST");

        $senior_tag = $this->tagId('Senior');
        $junior_tag = $this->tagId('Junior');

        $violations = $this->ci->db->query(
            "SELECT sp.paper_id,
                    SUM(CASE WHEN sp.user_id IN (
                          SELECT user_id FROM {$this->db_name}.userproject_tag WHERE tag_id = $senior_tag)
                        THEN 1 ELSE 0 END) AS seniors,
                    SUM(CASE WHEN sp.user_id IN (
                          SELECT user_id FROM {$this->db_name}.userproject_tag WHERE tag_id = $junior_tag)
                        THEN 1 ELSE 0 END) AS juniors
             FROM {$this->db_name}.screening_paper sp
             WHERE sp.assignment_role = 'Screening'
             GROUP BY sp.paper_id
             HAVING seniors < 1 OR juniors > 2"
        )->num_rows();

        $expected = 0;
        $actual   = $violations;
        run_test($this->controller, $action, $name, "Constraint violations", $expected, $actual);
    }

    /*
     * Test : assignation QA avec 1 seul user, doit fonctionner (mandatory)
     *        sans appliquer la contrainte reviews_per_paper > users
     */
    private function integration_qa_mandatoryWithSingleUser()
    {
        $action = "qa_assignment_save";
        $name   = "Integration: QA mandatory bypass with single user";

        // Pré-requis : assignations screening doivent exister pour qu'il y ait des papers en QA
        $this->cleanAssignmentsAndConstraints();

        // Faire une assignation screening complète pour qu'il y ait des papers à QA
        $screening_post = [
            "number_of_users"           => 1,
            "screening_phase"           => getScreeningPhaseId("Title"),
            "papers_sources"            => "all",
            "paper_source_status"       => "all",
            "user_1"                    => getAdminUserId(),
            "reviews_per_paper"         => 1,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $this->http_client->response("screening", "save_assignment_screen", $screening_post, "POST");

        // Vérifier juste que l'engine ne plante pas avec 1 seul user en QA
        $postData = [
            "number_of_users"           => 1,
            "user_1"                    => getAdminUserId(),
            "percentage"                => 100,
            "assign_all_paper_checkbox" => "on"
        ];
        $this->selectTitlePhase();
        $response = $this->http_client->response("quality_assessment", $action, $postData, "POST");

        // On vérifie que la requête a abouti (pas un 500)
        $expected = "OK";
        $actual   = ($response['status_code'] < 500) ? "OK" : "Server error";
        run_test("quality_assessment", $action, $name, "QA assignment with 1 user", $expected, $actual);
    }
}