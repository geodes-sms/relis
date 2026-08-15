<?php

// TEST PAPER CONTROLLER
class ScreenPhaseUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "screen_phases";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();

        $this->listScreenPhases_display();
        $this->savePhase_withoutTitle();
        $this->savePhase_withoutDisplayedField();
        $this->savePhase_FirstPhase();
        $this->savePhase_ParentPhase();
        $this->savePhase_SiblingPhase();
        $this->savePhase_ChildPhase();
        $this->deletePhase();
        $this->deletePhase_withTransfer();
        $this->deletePhase_withoutTransfer_withSibling();
        $this->modifyPhase_withoutTitle();
        $this->modifyPhase_withoutDisplayedFields();
        $this->modifyPhase_withoutSiblings();
        $this->modifyPhase_withSiblings();
        $this->replacePhase();

        /* tree integrity tests */

        $this->add_second_firstPhase();
        $this->add_sibling_to_firstPhase();
        $this->delete_a_firstPhase();
        $this->replace_an_unused_phase();
        $this->add_child_to_firstPhase_with_pending();
        $this->add_child_to_used_firstPhase();
        $this->modify_a_used_phase();
        $this->add_sibling_to_finalPhase();
        $this->add_parent_to_finalPhase();
        $this->add_child_to_finalPhase_with_pending();
        $this->add_child_to_initialPhase_with_nextPhase_pending();
        $this->add_child_to_used_finalPhase();
        $this->add_child_to_initialPhase_with_used_nextPhase();
        $this->add_parent_to_initialPhase_with_pending();
        $this->add_parent_to_used_initialPhase();
        $this->add_sibling_to_intermediatePhase_with_1_parent();
        $this->add_parent_to_intermediatePhase();
        $this->add_child_to_intermediatePhase_with_pending_next_phase();
        $this->add_child_to_intermediatePhase_with_used_next_phase();
        $this->add_parent_to_initialPhase_with_sibling();
        $this->add_child_to_initialPhase_with_siblings_intermediates();
        $this->add_child_to_intermediatePhase_with_siblings_childs();

        $this->saveBibtexPaper();
        $this->importPapersSave_1CSV();
        $this->importPapersSaveBibtext_1paper();
    }

    private function TestInitialize()
    {
        //delete generated userdata session files
        deleteSessionFiles();
        //delete created test user
        deleteCreatedTestUser();
        //delete created test Project
        deleteCreatedTestProject();
        //create test user
        addTestUser();
        //Login as admin
        $this->http_client->response("user", "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        //create test Project
        createDemoProject();
        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase_config(screen_phase_id) values(1)");
        // clean the phases table
        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        //add users to test Project
        addUserToProject(getAdminUserId(), "Reviewer");
        addUserToProject(getTestUserId(), "Reviewer");
    }

    /*
     * Test 1
     * Action : display_phases
     * Description : display the phases tree with 1 active phase and 1 inactive phase.
     * Expected result : check if 1 active phase and 1 inactive phase are displayed.
     */
    private function listScreenPhases_display() {

        $action = "display_phases";
        $test_name = "display the phases tree with 1 active phase and 1 inactive phase";
        $test_aspect = "are 1 active phase and 1 inactive phase are displayed displayed ?";
        $expected_value = "Yes";
        $actual_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (101, 'inactive', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 102, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 0),
        (102, 'active', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[101]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (
                strstr($response['content'], "inactive") != false &&
                strstr($response['content'], "active") != false
            ) {
                $actual_value = "Yes";
            }
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);

    }

    /*
     * Test 2
     * Action : save_screen_phase
     * Description : Save a screen phase without title.
     * Expected result : The screen phase is not added in the DB.
     */
    private function savePhase_withoutTitle() {
        $action = "save_screen_phase";
        $test_name = "Save a screen phase without title";
        $test_aspect = "is the phase added in DB ?";
        $expected_value = 0;
        $actual_value = 1;


        $postData = array(
            'title' => '',
            'displayed_fields' => array('Title', 'Abstract'),
            'add_type' => 'first',
            'id' => -1,
            'child' => -1,
            'parent' => -1,
        );

        $response =  $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = count($this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->result_array());
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);

    }

    /*
     * Test 3
     * Action : save_screen_phase
     * Description : Save a screen phase without displayed fields.
     * Expected result : The screen phase is not added in the DB.
     */
    private function savePhase_withoutDisplayedField() {

        $action = "save_screen_phase";
        $test_name = "Save a screen phase without displayed field";
        $test_aspect = "is the phase added in DB ?";
        $expected_value = 0;
        $actual_value = 1;


        $postData = array(
            'title' => 'first phase',
            'displayed_fields' => array(),
            'add_type' => 'first',
            'id' => -1,
            'child' => -1,
            'parent' => -1,
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = count($this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->result_array());
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 4
     * Action : save_screen_phase
     * Description : Save a 'first' screen phase.
     * Expected result : The screen phase is correctly added in the DB.
     */
    private function savePhase_FirstPhase() {

        $action = "save_screen_phase";
        $test_name = "Save a 'first' screen phase";
        $test_aspect = "is the phase correctly added in DB ?";
        $expected_value = "Yes";
        $actual_value = "No";

        $postData = array(
            'title' => 'first phase',
            'displayed_fields' => array('Title'),
            'add_type' => 'first',
            'id' => -1,
            'child' => -1,
            'parent' => -1,
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {

            $DB_table = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->result_array();

            if (count($DB_table) == 1) {

                $phase = $DB_table[array_key_first($DB_table)];

                if ($phase['depth_level'] == 0 &&
                    $phase['next_phase'] == '' &&
                    $phase['parent'] == '[]' &&
                    $phase['displayed_fields'] == 'Title') {
                    $actual_value = "Yes";
                }

            }
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 5
     * Action : save_screen_phase
     * Description : Save a 'parent' screen phase.
     * Expected result : The screen phase is correctly added in the DB.
     */
    private function savePhase_ParentPhase() {

        $action = "save_screen_phase";
        $test_name = "Save a 'parent' screen phase";
        $test_aspect = "is the phase correctly added in DB ?";
        $expected_value = "Yes";
        $actual_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (104, 'first phase', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'title' => 'parent phase',
            'displayed_fields' => array('Title'),
            'add_type' => 'parent',
            'id' => -1,
            'child' => 104,
            'parent' => -1,
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $DB_table = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->result_array();

            if (count($DB_table) == 2) {
                $first_phase = $DB_table[array_search(104, array_column($DB_table, 'screen_phase_id'))];

                if ($first_phase['parent'] != '[]') {
                    $parentId = json_decode($first_phase['parent'])[0];
                    $parent_phase = $DB_table[array_search($parentId, array_column($DB_table, 'screen_phase_id'))];

                    $actual_value = $parent_phase['depth_level'] == 0 &&
                    $parent_phase['next_phase'] == 104 &&
                    $parent_phase['parent'] == '[]' &&
                    $first_phase['depth_level'] == 1 ? "Yes" : "No";
                }
            }
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);

    }

    /*
     * Test 6
     * Action : save_screen_phase
     * Description : Save a 'sibling' screen phase.
     * Expected result : The screen phase is correctly added in the DB.
     */
    private function savePhase_SiblingPhase() {
        $action = "save_screen_phase";
        $test_name = "Save a 'sibling' screen phase";
        $test_aspect = "is the phase correctly added in DB ?";
        $expected_value = "Yes";
        $actual_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (106, 'initial', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 108, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (107, 'initial 2', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 108, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (108, 'intermediate', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 109, '[106,107]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (109, 'finale', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[108]', 2, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'title' => 'sibling phase',
            'displayed_fields' => array('Abstract'),
            'add_type' => 'sibling',
            'id' => 108,
            'child' => 109,
            'parent' => 106,
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $DB_table = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->result_array();

            if (count($DB_table) == 5) {
                $new_phase = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE 
                screen_phase_id != 106 AND 
                screen_phase_id != 107 AND 
                screen_phase_id != 108 AND 
                screen_phase_id != 109")->row_array();

                $phases = array();
                foreach ($DB_table as $raw_phase) {
                    $phases[$raw_phase['screen_phase_id']] = $raw_phase;
                }

                $actual_value = $new_phase['depth_level'] == 1 &&
                $new_phase['next_phase'] == 109 &&
                $new_phase['parent'] == '[106]' &&
                $phases[106]['next_phase'] == $new_phase['screen_phase_id'] &&
                $phases[108]['parent'] == '[107]' &&
                $phases[109]['parent'] == '[108,' . $new_phase['screen_phase_id'] . ']' ? "Yes" : "No";
            }
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);

    }

    /*
     * Test 7
     * Action : save_screen_phase
     * Description : Save a 'child' screen phase.
     * Expected result : The screen phase is correctly added in the DB.
     */
    private function savePhase_ChildPhase() {

        $action = "save_screen_phase";
        $test_name = "Save a 'child' screen phase";
        $test_aspect = "is the phase correctly added in DB ?";
        $expected_value = "Yes";
        $actual_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (111, 'first phase', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'title' => 'parent phase',
            'displayed_fields' => array('Title'),
            'add_type' => 'child',
            'id' => -1,
            'child' => -1,
            'parent' => 111,
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $DB_table = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->result_array();

            if (count($DB_table) == 2) {
                $first_phase = $DB_table[array_search(111, array_column($DB_table, 'screen_phase_id'))];

                if ($first_phase['next_phase'] != null) {
                    $child_phase = $DB_table[array_search($first_phase['next_phase'], array_column($DB_table, 'screen_phase_id'))];

                    $actual_value = $child_phase['depth_level'] == 1 &&
                    $child_phase['next_phase'] == null &&
                    $child_phase['parent'] == '[111]' ? "Yes" : "No";
                }
            }
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 8
     * Action : save_phase_deletion
     * Description : Delete a screen phase.
     * Expected result : The phase is correctly set to inactive in the DB.
     */
    private function deletePhase() {
        $action = "save_phase_deletion";
        $test_name = "Delete a screen phase";
        $test_aspect = "is the phase correctly set to inactive in the DB ?";
        $expected_value = "Yes";
        $actual_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (112, 'first phase', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 113, '[114]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (113, 'second phase', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[112]', 2, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (114, 'initial phase', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 112, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'phase_id' => 112
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $DB_table = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_active = 1")->result_array();

            if (count($DB_table) == 2) {
                $phases = array();
                foreach ($DB_table as $raw_phase) {
                    $phases[$raw_phase['screen_phase_id']] = $raw_phase;
                }

                $actual_value = $phases[113]['depth_level'] == 1 &&
                $phases[113]['parent'] == '[114]' &&
                $phases[114]['next_phase'] == 113 ? "Yes" : "No";
            }
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 9
     * Action : save_phase_deletion
     * Description : Delete a screen phase with a transfer.
     * Expected result : The phase is correctly set to inactive in the DB.
     */
    private function deletePhase_withTransfer() {
        $action = "save_phase_deletion";
        $test_name = "Delete a screen phase with a transfer";
        $test_aspect = "is the phase correctly set to inactive in the DB ?";
        $expected_value = "Yes";
        $actual_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (114, 'initial', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 116, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (115, 'initial 2', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 117, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (116, 'intermediate', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 118, '[114]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (117, 'intermediate 2', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 118, '[115]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (118, 'finale', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[116,117]', 2, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'phase_id' => 117,
            'transfer_phase' => 116
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $DB_table = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_active = 1")->result_array();

            $phases = array();
            foreach ($DB_table as $raw_phase) {
                $phases[$raw_phase['screen_phase_id']] = $raw_phase;
            }

            if (count($phases) == 4) {
                $actual_value = $phases[114]['next_phase'] == 116 &&
                $phases[115]['next_phase'] == 116 &&
                $phases[116]['parent'] == '[114,115]' &&
                $phases[118]['parent'] == '[116]' ? "Yes" : "No";
            }
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);

    }

    /*
     * Test 10
     * Action : save_phase_deletion
     * Description : Delete a screen phase without transfer but with siblings.
     * Expected result : The phase stays active.
     */
    private function deletePhase_withoutTransfer_withSibling() {
        $action = "save_phase_deletion";
        $test_name = "Delete a screen phase without transfer but with siblings";
        $test_aspect = "do the phase stays active ?";
        $expected_value = "Yes";
        $actual_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (119, 'initial', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 121, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (120, 'initial 2', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 122, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (121, 'intermediate', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 123, '[119]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (122, 'intermediate 2', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 123, '[120]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (123, 'finale', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[121,122]', 2, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'phase_id' => 122
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $DB_table = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_active = 1")->result_array();

            if (count($DB_table) == 5) {
                $actual_value = "Yes";
            }
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 11
     * Action : save_modification
     * Description : Modify a phase with an empty title.
     * Expected result : The phase is not modified.
     */
    private function modifyPhase_withoutTitle() {
        $action = "save_modification";
        $test_name = "Modify a phase with an empty title";
        $test_aspect = "is the phase modified ?";
        $expected_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (124, 'initial', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 125, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (125, 'final', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[124]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'phase_id' => 124,
            'displayed_fields' => array('Title'),
            'title' => ''
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $phase = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_id = 124")->row_array();

            $actual_value = $phase['displayed_fields'] == 'Abstract' && $phase['phase_title'] == 'initial' ? "No" : "Yes";
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 12
     * Action : save_modification
     * Description : Modify a phase with no displayed fields.
     * Expected result : The phase is not modified.
     */
    private function modifyPhase_withoutDisplayedFields() {
        $action = "save_modification";
        $test_name = "Modify a phase with no displayed fields";
        $test_aspect = "is the phase modified ?";
        $expected_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (126, 'initial', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 127, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (127, 'final', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[126]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'phase_id' => 126,
            'displayed_fields' => array(),
            'title' => 'different title'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $phase = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_id = 126")->row_array();

            $actual_value = $phase['displayed_fields'] == 'Abstract' && $phase['phase_title'] == 'initial' ? "No" : "Yes";
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 13
     * Action : save_modification
     * Description : Modify a phase without siblings.
     * Expected result : The phase is correctly modified.
     */
    private function modifyPhase_withoutSiblings() {
        $action = "save_modification";
        $test_name = "Modify a phase without siblings";
        $test_aspect = "is the phase modified ?";
        $expected_value = "Yes";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (128, 'initial', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 129, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (129, 'final', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[128]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'phase_id' => 128,
            'displayed_fields' => array('Title'),
            'title' => 'different title'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $phase = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_id = 128")->row_array();

            $actual_value = $phase['displayed_fields'] == 'Title' && $phase['phase_title'] == 'different title' ? "Yes" : "No";
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 14
     * Action : save_modification
     * Description : Modify a phase with a siblings.
     * Expected result : The phase and the sibling phase are correctly modified.
     */
    private function modifyPhase_withSiblings() {
        $action = "save_modification";
        $test_name = "Modify a phase with a sibling";
        $test_aspect = "are the phases modified ?";
        $expected_value = "Yes";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (130, 'initial', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 132, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (131, 'initial 2', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 132, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (132, 'final', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[130,131]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'phase_id' => 130,
            'displayed_fields' => array('Title'),
            'title' => 'different title'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $phase_1 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_id = 130")->row_array();
            $phase_2 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_id = 131")->row_array();

            $actual_value = $phase_1['displayed_fields'] == 'Title' && $phase_1['phase_title'] == 'different title' && $phase_2['displayed_fields'] == 'Title' ? "Yes" : "No";
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 15
     * Action : save_replacement
     * Description : replace a phase.
     * Expected result : The phase is correctly replaced.
     */
    private function replacePhase() {
        $action = "save_replacement";
        $test_name = "Replace a phase";
        $test_aspect = "is the phase correctly replaced ?";
        $expected_value = "Yes";
        $actual_value = "No";

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (133, 'initial', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 134, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (134, 'intermediate', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 135, '[133]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (135, 'final', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[134]', 2, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $postData = array(
            'phase_id' => 134,
            'new_title' => 'new title'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $DB_table = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->result_array();

            if (count($DB_table) == 4) {
                $phases = array();

                $new_phase_id = 0;

                foreach ($DB_table as $row) {
                    $phases[$row['screen_phase_id']] = $row;
                    if ($row['screen_phase_id'] != 133 && $row['screen_phase_id'] != 134 && $row['screen_phase_id'] != 135) {
                        $new_phase_id = $row['screen_phase_id'];
                    }
                }

                $actual_value = $phases[133]['next_phase'] == $new_phase_id &&
                $phases[134]['screen_phase_active'] == 0 &&
                $phases[135]['parent'] == '[' . $new_phase_id . ']' &&
                $phases[$new_phase_id]['phase_title'] == 'new title' &&
                $phases[$new_phase_id]['next_phase'] == 135 &&
                $phases[$new_phase_id]['parent'] == '[133]' ? "Yes" : "No";
            }
        }

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* tree integrity tests */

    /* One phase */

    /*
     * Test 16
     * Action : add_phase
     * Description : add a second 'first' phase.
     * Expected result : http code 307.
     */
    private function add_second_firstPhase() {
        $action= "add_phase";
        $test_name = "add a second 'first' phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (136, 'first', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $response = $this->http_client->response($this->controller, $action . "/first/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 17
     * Action : add_phase
     * Description : add a sibling to a 'first' phase.
     * Expected result : http code 307.
     */
    private function add_sibling_to_firstPhase() {
        $action= "add_phase";
        $test_name = "add a sibling to a 'first' phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $response = $this->http_client->response($this->controller, $action . "/sibling/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 18
     * Action : delete_phase
     * Description : delete a 'first' phase.
     * Expected result : http code 307.
     */
    private function delete_a_firstPhase() {
        $action= "delete_phase";
        $test_name = "delete a 'first' phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $response = $this->http_client->response($this->controller, $action . "/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 19
     * Action : replace_phase
     * Description : replace an unused phase.
     * Expected result : http code 307.
     */
    private function replace_an_unused_phase() {
        $action= "replace_phase";
        $test_name = "replace an unused phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $response = $this->http_client->response($this->controller, $action . "/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* One phase has_pending = 1 */

    /*
     * Test 20
     * Action : add_phase
     * Description : add a child to a 'first' phase with pending.
     * Expected result : http code 307.
     */
    private function add_child_to_firstPhase_with_pending() {
        $action= "add_phase";
        $test_name = "add a child to a 'first' phase with pending";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET has_pending = 1 WHERE screen_phase_id = 136");

        $response = $this->http_client->response($this->controller, $action . "/child/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET has_pending = 0 WHERE screen_phase_id = 136");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* One phase used = 1 */

    /*
     * Test 21
     * Action : add_phase
     * Description : add a child to a used 'first' phase.
     * Expected result : http code 307.
     */
    private function add_child_to_used_firstPhase() {
        $action= "add_phase";
        $test_name = "add a child to a used 'first' phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET used = 1 WHERE screen_phase_id = 136");

        $response = $this->http_client->response($this->controller, $action . "/child/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 22
     * Action : modify_phase
     * Description : modify a used phase.
     * Expected result : http code 307.
     */
    private function modify_a_used_phase() {
        $action= "modify_phase";
        $test_name = "modify a used phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $response = $this->http_client->response($this->controller, $action . "/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET used = 0 WHERE screen_phase_id = 136");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Two phases :
     *   * 136
     *   |
     *   * 137
     */

    /*
     * Test 23
     * Action : add_phase
     * Description : add a sibling to a 'final' phase.
     * Expected result : http code 307.
     */
    private function add_sibling_to_finalPhase() {
        $action= "add_phase";
        $test_name = "add a sibling to a 'final' phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (137, 'final', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', null, '[136]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET next_phase = 137 WHERE screen_phase_id = 136");

        $response = $this->http_client->response($this->controller, $action . "/sibling/137");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 24
     * Action : add_phase
     * Description : add a parnet to a 'final' phase.
     * Expected result : http code 307.
     */
    private function add_parent_to_finalPhase() {
        $action= "add_phase";
        $test_name = "add a parnet to a 'final' phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $response = $this->http_client->response($this->controller, $action . "/parent/137");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Two phases, has_pending final phase*/

    /*
     * Test 25
     * Action : add_phase
     * Description : add a child to a 'final' phase with pending.
     * Expected result : http code 307.
     */
    private function add_child_to_finalPhase_with_pending() {
        $action= "add_phase";
        $test_name = "add a child to a 'final' phase with pending";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET has_pending = 1 WHERE screen_phase_id = 137");

        $response = $this->http_client->response($this->controller, $action . "/child/137");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 26
     * Action : add_phase
     * Description : add a child to a 'initial' phase with next phase pending.
     * Expected result : http code 307.
     */
    private function add_child_to_initialPhase_with_nextPhase_pending() {
        $action= "add_phase";
        $test_name = "add a child to a 'initial' phase with next phase pending";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $response = $this->http_client->response($this->controller, $action . "/child/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET has_pending = 0 WHERE screen_phase_id = 137");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Two phases, used final phase*/

    /*
     * Test 27
     * Action : add_phase
     * Description : add a child to a used 'final' phase.
     * Expected result : http code 307.
     */
    private function add_child_to_used_finalPhase() {
        $action= "add_phase";
        $test_name = "add a child to a used 'final' phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET used = 1 WHERE screen_phase_id = 137");

        $response = $this->http_client->response($this->controller, $action . "/child/137");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 28
     * Action : add_phase
     * Description : add a child to a 'initial' phase with used next phase.
     * Expected result : http code 307.
     */
    private function add_child_to_initialPhase_with_used_nextPhase() {
        $action= "add_phase";
        $test_name = "add a child to a 'initial' phase with used next phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $response = $this->http_client->response($this->controller, $action . "/child/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET used = 0 WHERE screen_phase_id = 137");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Two phases, has_pending initial phase*/

    /*
     * Test 29
     * Action : add_phase
     * Description : add a parent to a 'initial' phase with pending.
     * Expected result : http code 307.
     */
    private function add_parent_to_initialPhase_with_pending() {
        $action= "add_phase";
        $test_name = "add a parent to a 'initial' phase with pending";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET has_pending = 1 WHERE screen_phase_id = 136");

        $response = $this->http_client->response($this->controller, $action . "/parent/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET has_pending = 0 WHERE screen_phase_id = 136");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Two phases, used initial phase*/

    /*
     * Test 30
     * Action : add_phase
     * Description : add a parent to a used 'initial' phase.
     * Expected result : http code 307.
     */
    private function add_parent_to_used_initialPhase() {
        $action= "add_phase";
        $test_name = "add a parent to a used 'initial' phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET used = 1 WHERE screen_phase_id = 136");

        $response = $this->http_client->response($this->controller, $action . "/parent/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET used = 0 WHERE screen_phase_id = 136");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Three Phases
     *    * 138
     *    |
     *    * 136
     *    |
     *    * 137
     */

    /*
     * Test 31
     * Action : add_phase
     * Description : add a sibling to a 'intermediate' phase with 1 parent.
     * Expected result : http code 307.
     */
    private function add_sibling_to_intermediatePhase_with_1_parent() {
        $action= "add_phase";
        $test_name = "add a sibling to a 'intermediate' phase with 1 parent";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (138, 'initial', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 136, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET parent = '[138]', depth_level = 1 WHERE screen_phase_id = 136");
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET depth_level = 2 WHERE screen_phase_id = 137");

        $response = $this->http_client->response($this->controller, $action . "/sibling/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 32
     * Action : add_phase
     * Description : add a parent to a 'intermediate' phase.
     * Expected result : http code 307.
     */
    private function add_parent_to_intermediatePhase() {
        $action= "add_phase";
        $test_name = "add a parent to a 'intermediate' phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $response = $this->http_client->response($this->controller, $action . "/parent/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Three phases, has_pending final phase */

    /*
     * Test 33
     * Action : add_phase
     * Description : add a child to a 'intermediate' phase with pending next phase.
     * Expected result : http code 307.
     */
    private function add_child_to_intermediatePhase_with_pending_next_phase() {
        $action= "add_phase";
        $test_name = "add a child to a 'intermediate' phase with pending next phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET has_pending = 1 WHERE screen_phase_id = 137");

        $response = $this->http_client->response($this->controller, $action . "/child/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET has_pending = 0 WHERE screen_phase_id = 137");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Three phases, used final phase */

    /*
     * Test 34
     * Action : add_phase
     * Description : add a child to a 'intermediate' phase with used next phase.
     * Expected result : http code 307.
     */
    private function add_child_to_intermediatePhase_with_used_next_phase() {
        $action= "add_phase";
        $test_name = "add a child to a 'intermediate' phase with used next phase";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET used = 1 WHERE screen_phase_id = 137");

        $response = $this->http_client->response($this->controller, $action . "/child/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET used = 0 WHERE screen_phase_id = 137");

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Four Phases :
     *   *   * 138, 139
     *    \ /
     *     * 136
     *     |
     *     * 137
     */

    /*
     * Test 35
     * Action : add_phase
     * Description : add a parent to a 'initial' phase with sibling.
     * Expected result : http code 307.
     */
    private function add_parent_to_initialPhase_with_sibling() {
        $action= "add_phase";
        $test_name = "add a parent to a 'initial' phase with sibling";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (139, 'initial 2', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 136, '[]', 0, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET parent = '[138, 139]' WHERE screen_phase_id = 136");

        $response = $this->http_client->response($this->controller, $action . "/parent/138");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Five Phases :
     *  *   * 138, 139
     *  |   |
     *  *   * 136, 140
     *   \ /
     *    * 137
     */

    /*
     * Test 36
     * Action : add_phase
     * Description : add a child to a 'initial' phase with siblings intermediates.
     * Expected result : http code 307.
     */
    private function add_child_to_initialPhase_with_siblings_intermediates() {
        $action= "add_phase";
        $test_name = "add a child to a 'initial' phase with siblings intermediates";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (140, 'intermediate 2', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 137, '[139]', 1, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET parent = '[138]' WHERE screen_phase_id = 136");
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET next_phase = 140 WHERE screen_phase_id = 139");

        $response = $this->http_client->response($this->controller, $action . "/child/138");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* Seven Phases :
     *  *   * 138, 139
     *  |   |
     *  *   * 136, 140
     *  |   |
     *  *   * 141, 142
     *   \ /
     *    * 137
     */

    /*
     * Test 37
     * Action : add_phase
     * Description : add a child to a 'intermediate' phase with siblings childs.
     * Expected result : http code 307.
     */
    private function add_child_to_intermediatePhase_with_siblings_childs() {
        $action= "add_phase";
        $test_name = "add a child to a 'intermediate' phase with siblings childs";
        $test_aspect = "http code";
        $expected_value = http_code()[307];

        $this->ci->db->query("INSERT INTO relis_dev_correct_" . getProjectShortName() . ".screen_phase (screen_phase_id, phase_title, description, displayed_fields, phase_state, source_paper, source_paper_status, screen_phase_order, screen_phase_final, phase_type, next_phase, parent, depth_level, used, has_pending, added_by, add_time, screen_phase_active) 
        VALUES (141, 'intermediate 3', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 137, '[136]', 2, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1),
        (142, 'intermediate 4', 'desc', 'Abstract', 'Closed', 'Previous phase', 'Included', 10, 0, 'Screening', 137, '[140]', 2, 0, 0, 1, '". bm_current_time('Y-m-d H:i:s') . "', 1)");

        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET parent = '[141,142]' WHERE screen_phase_id = 137");
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET next_phase = 141 WHERE screen_phase_id = 136");
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase SET next_phase = 142 WHERE screen_phase_id = 140");

        $response = $this->http_client->response($this->controller, $action . "/child/136");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /* thoses are the sames tests as in paper_ut_helper, but with import_phase specified */

    /*
     * Test 38
     * Action : save_bibtex_paper
     * Description : handles the saving of a paper from a BibTeX entry with import_phase specified
     * Expected paper inserted in DB
     */
    private function saveBibtexPaper()
    {
        $action = "save_bibtex_paper";
        $test_name = "Handles the saving of a paper from a BibTeX entry";
        $test_aspect_papersInDB = "Paper inserted in DB";

        $bibtextKey = "Barbierato2016";
        $autour = "Barbierato, Enrico and Gribaudo, Marco and Iacono, Mauro";
        $journal = "Electronic Notes in Theoretical Computer Science";
        $title = "Modeling Hybrid Systems in SIMTHESys";
        $year = "2016";
        $pages = "5--25";
        $volume = "327";
        $abstract = "Hybrid systems (HS) have been proven a valid formalism to study and analyze specific issues in a variety of fields. However, most of the analysis techniques for HS are based on low-level description, where single states of the systems have to be defined and enumerated by the modeler. Some high level modeling formalisms, such as Fluid Stochastic Petri Nets, have been introduced to overcome such difficulties, but simple procedures allowing the definitions of domain specific languages for HS could simplify the analysis of such systems. This paper presents a stochastic HS language consisting of a subset of piecewise deterministic Markov processes, and shows how SIMTHESys \endash a compositional, metamodeling based framework describing and extending formalisms \endash can be used to convert into this paradigm a wide number of high-level HS description languages. A simple example applying the technique to solve a model of the energy consumption of a data-center specified using Queuing Network and Hybrid Petri Nets is presented to show the effectiveness of the proposal.";
        $doi = "10.1016/j.entcs.2016.09.021";
        $paper = "https://dx.doi.org/10.1016/j.entcs.2016.09.021";

        $bibtextData = "@ARTICLE{" . $bibtextKey . ",
            author = {" . $autour . "},
            journal = {" . $journal . "},
            title = {" . $title . "},
            year = {" . $year . "},
            pages = {" . $pages . "},
            volume = {" . $volume . "},
            abstract = {" . $abstract . "},
            doi = {" . $doi . "},
            paper = {" . $paper . "}
          }";

        $paper = ["bibtexKey" => $bibtextKey, "title" => $title];
        $expected_PaperInDB = json_encode($paper);

        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase");

        $response = $this->http_client->response("paper", $action, ["bibtext" => $bibtextData, 'import_phase' => 138], "POST");

        if ($response['status_code'] >= 400) {
            $actual_PaperInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $paper_data = $this->ci->db->query("SELECT bibtexKey, title FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE bibtexKey = '" . $bibtextKey . "'")->row_array();
            $actual_PaperInDB = json_encode($paper_data);
        }

        run_test("paper", $action, $test_name, $test_aspect_papersInDB, $expected_PaperInDB, $actual_PaperInDB);
    }

    /*
     * Test 39
     * Action : import_papers_save_csv
     * Description : inserting 1 loaded csv paper into the database with import_phase specified
     * Expected papers inserted in DB : 1
     */
    private function importPapersSave_1CSV()
    {
        $action = "import_papers_save_csv";
        $test_name = "Inserting 1 loaded csv papers into the database";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = (string) ($this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'] + 1);

        $paperData = getCSVdata('relis_app/helpers/tests/testFiles/paper/1_csvPaper.xls');
        $response = $this->http_client->response("paper", $action, [
            "paper_title" => "4",
            "bibtexKey" => "1",
            "paper_link" => "3",
            "year" => "",
            "paper_abstract" => "",
            "bibtex" => "",
            "paper_key" => "",
            "paper_author" => "",
            "data_array" => $paperData,
            "paper_start_from" => "2",
            "papers_sources" => "",
            "import_phase" => 139
        ], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test("paper", $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }

    /*
     * Test 40
     * Action : import_papers_save_bibtext
     * Description : Inserting the loaded bibtext papers with 1 paper into the database, with the import_phase specified
     * Expected papers inserted in DB : 1
     */
    private function importPapersSaveBibtext_1paper()
    {
        $action = "import_papers_save_bibtext";
        $test_name = "Inserting the loaded bibtext papers with 1 paper into the database";
        $test_aspect_papersInDB = "Nbr of papers in project DB";
        $expected_nbrOfPapersInDB = (string) ($this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'] + 1);

        $paperData = getBibtextData('relis_app/helpers/tests/testFiles/paper/1_bibPaper.bib');
        $response = $this->http_client->response("paper", $action, ["data_array" => $paperData, "papers_sources" => "", "import_phase" => 138], "POST");

        if ($response['status_code'] >= 400) {
            $actual_nbrOfPapersInDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfPapersInDB = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper")->row_array()['row_count'];
        }

        run_test("paper", $action, $test_name, $test_aspect_papersInDB, $expected_nbrOfPapersInDB, $actual_nbrOfPapersInDB);
    }
}