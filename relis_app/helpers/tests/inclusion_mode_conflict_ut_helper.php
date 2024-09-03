<?php
class inclusion_mode_conflictUnitTest {
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "screening";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests() {
        $this->TestInitialize();
        $this->changeModeWhenNoInclusionCriteria();
        $this->affected_phases();
        $this->defaultCriteria_one();
        $this->keepOneFromAny();
        $this->keepOneFromAll();
        $this->resetScreening();
    }

    private function TestInitialize() {
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
        //add users to test Project
        addUserToProject(getAdminUserId(), "Reviewer");
        addUserToProject(getTestUserId(), "Reviewer");
        addScreeningPhase("Abstract");
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
    }

    private function changeModeWhenNoInclusionCriteria() {
        $action = "edit_screening_config";
        $test_name = "Testing if changing inclusion mode from Default is prevented if there are no inclusion criteria";
        $test_aspect = "Is mode change allowed when there are no inclusion criteria ?";
        $expected_value = "None";
        addCriteria("aze", "exclusioncrieria");
        $data = array(
            'screening_result_on' => true,
            'assign_papers_on' => true,
            'screening_reviewer_number' => 2,
            'screening_inclusion_mode' => "One",
            'screening_conflict_type' => "IncludeExclude",
            'screening_screening_conflict_resolution' => "Unanimity",
            'use_kappa' => true,
            'screening_validation_on' => true,
            'screening_validator_assignment_type' => "Normal",
            'assign_to_non_screened_validator_on' => false,
            'validation_default_percentage' => 20
        );

        $response = $this->http_client->response($this->controller, $action, $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT screening_inclusion_mode FROM relis_dev_correct_" . getProjectShortName() . ".config")->row_array()["screening_inclusion_mode"];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    private function affected_phases() {
        $action = "get_affected_phases (Model method)";
        $test_name = "Testing is changes are made to the right affected phases";
        $test_aspect = "Affected phases";
        $expected_value = json_encode(array(3));
        $actual_value = "";

        addScreeningPhase("Link");
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".screen_phase_config SET config_type = 'Custom' WHERE screen_phase_config_id = 3");

        $model = new Screening_dataAccess();
        $actual_value = json_encode($model->get_affected_phases(getScreeningPhaseId("Link")));

        run_test($this->controller, $action, $test_name, $test_aspect, $actual_value, $actual_value);
    }

    private function defaultCriteria_one() {
        $action = "solve_mode_conflict";
        $test_name = "Testing if default criteria is added when going from None to One inclusion mode";
        $test_aspect = "Is a default criteria properly added?";
        $expected_value = "Yes";
        $actual_value = "No";

        assignPapers_and_performScreening([getAdminUserId()], "Abstract", $done = 5, $include = 3);
        addCriteria("Criteria 1", "inclusioncriteria");
        $data = array(
            'default_criterion' => '',
            'config_array' => serialize(array(
                'operation_type' => 'edit',
                'table_config' => 'config',
                'current_operation' => 'edit_config_screening',
                'redirect_after_save' => 'element/display_element/configurations/1',
                'operation_source' => 'own',
                'child_field' => '',
                'table_config_parent' => '',
                'parent_id' => '',
                'parent_field' => '',
                'parent_table' => '',
                'config_id' => '1',
                'screening_on' => '1',
                'screening_result_on' => '0',
                'assign_papers_on' => '0',
                'screening_reviewer_number' => '2',
                'screening_inclusion_mode' => 'Any',
                'screening_conflict_type' => 'ExclusionCriteria',
                'screening_screening_conflict_resolution' => 'Unanimity',
                'use_kappa' => '1',
                'screening_validation_on' => '0',
                'screening_validator_assignment_type' => 'Normal',
                'assign_to_non_screened_validator_on' => '0',
                'validation_default_percentage' => '10',
            )),
            'affected_phases' => serialize(array(
                0 => getScreeningPhaseId("Abstract"),
            )),
        );
        
        $response = $this->http_client->response($this->controller, $action, $data, "POST");
        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $query = $this->ci->db->query("
            SELECT COUNT(*) as count
            FROM relis_dev_correct_" . getProjectShortName() . 
            ".screen_inclusion_mapping WHERE criteria_id = (
                SELECT criteria_id
                FROM relis_dev_correct_" . getProjectShortName() . ".ref_inclusioncriteria
                WHERE ref_value = 'Default'
            )
        ");
        $actual_value = $query->row_array()['count'] == 3 ? "Yes" : "No";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    } 

    private function keepOneFromAny() {
        $action = "solve_mode_conflict";
        $test_name = "Testing if removal of extra criteria works properly when switching from Any to One inclusion mode";
        $test_aspect = "How many criteria were removed?";
        $expected_value = "Yes";
        $actual_value = "No";

        addCriteria("inclusion 2", "inclusioncriteria");
        addCriteria("inclusion 3", "inclusioncriteria");
    
        $query = $this->ci->db->query("
            INSERT INTO relis_dev_correct_" . getProjectShortName() . 
            ".screen_inclusion_mapping(screening_id, criteria_id) values (1,1),
            (1,2), (1,3), (3,2), (3,3), (2,2)
        ");

        $data = array(
            'keep_one' => '',
            'config_array' => serialize(array(
                'operation_type' => 'edit',
                'table_config' => 'config',
                'current_operation' => 'edit_config_screening',
                'redirect_after_save' => 'element/display_element/configurations/1',
                'operation_source' => 'own',
                'child_field' => '',
                'table_config_parent' => '',
                'parent_id' => '',
                'parent_field' => '',
                'parent_table' => '',
                'config_id' => '1',
                'screening_on' => '1',
                'screening_result_on' => '0',
                'assign_papers_on' => '0',
                'screening_reviewer_number' => '2',
                'screening_inclusion_mode' => 'One',
                'screening_conflict_type' => 'ExclusionCriteria',
                'screening_screening_conflict_resolution' => 'Unanimity',
                'use_kappa' => '1',
                'screening_validation_on' => '0',
                'screening_validator_assignment_type' => 'Normal',
                'assign_to_non_screened_validator_on' => '0',
                'validation_default_percentage' => '10',
            )),
            'affected_phases' => serialize(array(
                0 => getScreeningPhaseId("Abstract"),
            )),
        );

        $response = $this->http_client->response($this->controller, $action, $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $query = $this->ci->db->query("
            SELECT COUNT(*) as count
            FROM relis_dev_correct_" . getProjectShortName() . 
            ".screen_inclusion_mapping"
        );
        $actual_value = $query->row_array()['count'] == 3 ? "Yes" : "No";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    private function    keepOneFromAll() {
        $action = "solve_mode_conflict";
        $test_name = "Testing if one random criteria is added properly when switching from All to One inclusion mode";
        $test_aspect = "How many criteria were added?";
        $expected_value = "Yes";
        $actual_value = "No";

        $data = array(
            'screening_result_on' => true,
            'assign_papers_on' => true,
            'screening_reviewer_number' => 2,
            'screening_inclusion_mode' => "All",
            'screening_conflict_type' => "IncludeExclude",
            'screening_screening_conflict_resolution' => "Unanimity",
            'use_kappa' => true,
            'screening_validation_on' => true,
            'screening_validator_assignment_type' => "Normal",
            'assign_to_non_screened_validator_on' => false,
            'validation_default_percentage' => 20
        );

        $response = $this->http_client->response($this->controller, "edit_screening_config", $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = array(
                'keep_one_from_all' => '',
                'config_array' => serialize(array(
                    'operation_type' => 'edit',
                    'table_config' => 'config',
                    'current_operation' => 'edit_config_screening',
                    'redirect_after_save' => 'element/display_element/configurations/1',
                    'operation_source' => 'own',
                    'child_field' => '',
                    'table_config_parent' => '',
                    'parent_id' => '',
                    'parent_field' => '',
                    'parent_table' => '',
                    'config_id' => '1',
                    'screening_on' => '1',
                    'screening_result_on' => '0',
                    'assign_papers_on' => '0',
                    'screening_reviewer_number' => '2',
                    'screening_inclusion_mode' => 'One',
                    'screening_conflict_type' => 'ExclusionCriteria',
                    'screening_screening_conflict_resolution' => 'Unanimity',
                    'use_kappa' => '1',
                    'screening_validation_on' => '0',
                    'screening_validator_assignment_type' => 'Normal',
                    'assign_to_non_screened_validator_on' => '0',
                    'validation_default_percentage' => '10',
                )),
                'affected_phases' => serialize(array(
                    0 => getScreeningPhaseId("Abstract"),
                )),
            );

            $response = $this->http_client->response($this->controller, $action, $data, "POST");

            if ($response['status_code'] >= 400) {
                $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
            } else {
                    $query = $this->ci->db->query("
                    SELECT COUNT(*) as count
                    FROM relis_dev_correct_" . getProjectShortName() . 
                    ".screen_inclusion_mapping"
                );
                $actual_value = $query->row_array()['count'] == 3 ? 'Yes' : 'No';
                }  
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    

    private function resetScreening() {
        $action = "solve_mode_conflict";
        $test_name = "Testing if reset screening works properly";
        $test_aspect = "Were screenings properly reset?";
        $expected_value = "Yes";
        $actual_value = "No";

        $data = array(
            'screening_result_on' => true,
            'assign_papers_on' => true,
            'screening_reviewer_number' => 2,
            'screening_inclusion_mode' => "All",
            'screening_conflict_type' => "IncludeExclude",
            'screening_screening_conflict_resolution' => "Unanimity",
            'use_kappa' => true,
            'screening_validation_on' => true,
            'screening_validator_assignment_type' => "Normal",
            'assign_to_non_screened_validator_on' => false,
            'validation_default_percentage' => 20
        );

        $response = $this->http_client->response($this->controller, "edit_screening_config", $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = array(
                'reset' => '',
                'config_array' => serialize(array(
                    'operation_type' => 'edit',
                    'table_config' => 'config',
                    'current_operation' => 'edit_config_screening',
                    'redirect_after_save' => 'element/display_element/configurations/1',
                    'operation_source' => 'own',
                    'child_field' => '',
                    'table_config_parent' => '',
                    'parent_id' => '',
                    'parent_field' => '',
                    'parent_table' => '',
                    'config_id' => '1',
                    'screening_on' => '1',
                    'screening_result_on' => '0',
                    'assign_papers_on' => '0',
                    'screening_reviewer_number' => '2',
                    'screening_inclusion_mode' => 'One',
                    'screening_conflict_type' => 'ExclusionCriteria',
                    'screening_screening_conflict_resolution' => 'Unanimity',
                    'use_kappa' => '1',
                    'screening_validation_on' => '0',
                    'screening_validator_assignment_type' => 'Normal',
                    'assign_to_non_screened_validator_on' => '0',
                    'validation_default_percentage' => '10',
                )),
                'affected_phases' => serialize(array(
                    0 => getScreeningPhaseId("Abstract"),
                )),
            );

            $response = $this->http_client->response($this->controller, $action, $data, "POST");

            if ($response['status_code'] >= 400) {
                $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
            } else {
                    $query = $this->ci->db->query("
                    SELECT COUNT(*) as count
                    FROM relis_dev_correct_" . getProjectShortName() . 
                    ".screening_paper Where screening_status = 'Reseted'"
                );
                $actual_value = $query->row_array()['count'] == 5 ? 'Yes' : 'No';
                }  
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

}
