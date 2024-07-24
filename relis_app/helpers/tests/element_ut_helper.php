<?php

// TEST ELEMENT CONTROLLER
class ElementUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "element";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->displayInfo();
        $this->listAdmiConfig();
        $this->displayLogEntry();
        $this->displayStrEntry();
        $this->displayUserDetail();
        $this->displayUserMinDetail();
        $this->listAllUsers();
        $this->edit_info();
        $this->save_new_user();
        $this->remove_info();
        $this->remove_user();

        $this->projectInitialize();
        $this->listProjectConfig();
        $this->displayAuthor();
        $this->displayClassification();
        $this->displayProjectDetails();
        $this->displayScreeningPhaseDetails();
        $this->displayUserprojectDetail();
        $this->displayVenueDetail();
        $this->displayScreeningAssignment();
        $this->displayScreening();
        $this->listAffiliations();
        $this->listPapers();
        $this->listPendingScreening();
        $this->listAllAssignments();
        $this->listAllAuthors();
        $this->listAllAuthorClass();
        $this->listAllClassificationAssignments();
        $this->listAllClassificationAssignmentsDone();
        $this->listAllClassificationAssignmentsPending();
        $this->listAllclassifications();
        $this->listAllExcludedPapers();
        $this->listAllExclusionCriteria();
        $this->listAllIncludedPapers();
        $this->listAllInclusionCriteria();
        $this->listAllLogs();
        $this->listAllOperations();
        $this->listAllPaperScreened();
        $this->list_papers_screen_conflict();
        $this->list_papers_screen_excluded();
        $this->list_papers_screen_included();
        $this->list_qa_papers();
        $this->list_qa_papers_done();
        $this->list_qa_papers_pending();
        $this->list_qa_questions();
        $this->list_qa_responses();
        $this->list_ref_brand();
        $this->list_ref_variety();
        $this->list_research_question();
        $this->list_screen_phases();
        $this->list_venues();
        $this->list_screenings();
        $this->list_classification_graphs();
        $this->add_affiliation();
        $this->add_author();
        $this->add_exclusioncriteria();
        $this->add_inclusioncriteria();
        $this->add_info();
        $this->add_paper();
        $this->add_qa_questions();
        $this->add_qa_responses();
        $this->add_ref_brand();
        $this->add_ref_variety();
        $this->add_research_question();
        $this->add_user();
        $this->add_user_current_project();
        $this->add_venue();
        $this->add_reviewer();
        $this->new_assignment_class();
        $this->edit_author();
        $this->edit_exclusioncrieria();
        $this->edit_paper();
        $this->edit_qa_questions();
        $this->edit_qa_responses();
        $this->edit_ref_brand();
        $this->edit_ref_variety();
        $this->edit_screen_phase();
        $this->edit_user();
        $this->edit_venue();
        $this->edit_assignment_class();
        $this->edit_config_screening();
        $this->edit_config_qa();
        $this->edit_config_class();
        $this->edit_conf_papers();
        $this->edit_project();
        $this->update_classification();
        $this->save_new_paper();
        $this->save_new_affiliation();
        $this->save_new_exclusion_criteria();
        $this->save_new_qa_question();
        $this->save_new_qa_response();
        $this->save_new_brand();
        $this->save_new_variety();
        $this->save_new_research_question();
        $this->save_new_search_strategy();
        $this->save_new_user_project();
        $this->update_paper_config();
        $this->update_screening_config();
        $this->update_qa_config();
        $this->update_class_config();
        $this->update_exclusion_criteria();
        $this->update_paper();
        $this->update_qa_question();
        $this->update_qa_response();
        $this->update_brand();
        $this->update_variety();
        $this->remove_paper();
        $this->remove_assignment();
        $this->remove_author();
        $this->remove_class_assignment();
        $this->remove_exclusioncriteria();
        $this->remove_qa_questions();
        $this->remove_qa_responses();
        $this->remove_ref_brand();
        $this->remove_ref_variety();
        $this->remove_screen_phase();
        $this->remove_venue();
    }

    private function TestInitialize()
    {
        //delete generated userdata session files
        deleteSessionFiles();
        //delete created test user
        deleteCreatedTestUser();
        //delete created test Project
        deleteCreatedTestProject();
        //Login as admin
        $this->http_client->response("user", "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        //create test user
        addTestUser();
    }

    private function projectInitialize()
    {
        //create demo project
        createDemoProject();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //add users to test Project
        addUserToProject(getAdminUserId(), "Reviewer");
        addUserToProject(getTestUserId(), "Reviewer");
        //perform screening with 4 paper inclusions
        assignPapers_and_performScreening([getAdminUserId()], 'Title', -1, 4);
        //perform QA (2 high quality QAs, 2 low quality QAs)
        $this->qa_results = assignPapers_and_performQA([getAdminUserId()], 4, 2);
        //Exclude low quality papers
        qaExcludeLowQuality();
        //perform classification
        assignPapersForClassification([getAdminUserId(), getTestUserId()]);
        performClassification();
    }

    /*
     * Test 1
     * Action : display_element
     * Description : display info detail
     * Expected value: check if the correct element is displayed
     */
    private function displayInfo()
    {
        $action = "display_element";
        $test_name = "display info detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/detail_info/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $data = $this->ci->db->query("SELECT * FROM info WHERE info_id = 1")->row_array();

            //check if entry is listed
            if (strstr($response['content'], $data['info_title']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 2
     * Action : display_element
     * Description : display admin setting
     * Expected value: check if the correct element is displayed
     */
    private function listAdmiConfig()
    {
        $action = "display_element";
        $test_name = "display admin setting";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/admin_config/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $data = $this->ci->db->query("SELECT * FROM config_admin WHERE config_id = 1")->row_array();

            //check if entry is listed
            if (strstr($response['content'], $data['editor_url']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 3
     * Action : display_element
     * Description : display log entry
     * Expected value: check if the correct element is displayed
     */
    private function displayLogEntry()
    {
        $action = "display_element";
        $test_name = "display log entry";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/detail_logs/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $data = $this->ci->db->query("SELECT * FROM log WHERE log_id = 1")->row_array();

            //check if entry is listed
            if (strstr($response['content'], $data['log_event']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 4
     * Action : display_element
     * Description : display string management entry
     * Expected value: check if the correct element is displayed
     */
    private function displayStrEntry()
    {
        $action = "display_element";
        $test_name = "display string management entry";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/detail_str_mng/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $data = $this->ci->db->query("SELECT * FROM str_management WHERE str_id = 1")->row_array();

            //check if entry is listed
            if (strstr($response['content'], $data['str_label']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 5
     * Action : display_element
     * Description : display user details
     * Expected value: check if the correct element is displayed
     */
    private function displayUserDetail()
    {
        $action = "display_element";
        $test_name = "display user detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $user = $this->ci->db->query("SELECT * FROM users WHERE user_username = '" . getUser_username() . "'")->row_array();
        $response = $this->http_client->response($this->controller, $action . "/detail_user/" . $user['user_id']);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //check if entry is listed
            if (strstr($response['content'], $user['user_name']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 6
     * Action : display_element
     * Description : display user minimal details
     * Expected value: check if the correct element is displayed
     */
    private function displayUserMinDetail()
    {
        $action = "display_element";
        $test_name = "display user minimal details";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $user = $this->ci->db->query("SELECT * FROM users WHERE user_username = '" . getUser_username() . "'")->row_array();
        $response = $this->http_client->response($this->controller, $action . "/detail_user_min_ed/" . $user['user_id']);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //check if entry is listed
            if (strstr($response['content'], $user['user_name']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 7
     * Action : entity_list
     * Description : display list of all users
     * Expected value: check if the correct elements are displayed
     */
    private function listAllUsers()
    {
        $action = "entity_list";
        $test_name = "display list of all users";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_all_users");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM users")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['user_name']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 8
     * Action : edit_element
     * Description : display form for editing info
     */
    private function edit_info()
    {
        $action = "edit_element";
        $test_name = "display form for editing info";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_info/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 9
     * Action : save_element
     * Description : save new user
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_user()
    {
        $action = "save_element";
        $test_name = "save new user";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = [
            'operation_type' => 'new',
            'table_config' => 'users',
            'current_operation' => 'add_user',
            'redirect_after_save' => '',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'user_id' => '',
            'user_state' => 1,
            'user_name' => 'john',
            'user_username' => 'testField01',
            'user_mail' => '1234@gmail.com',
            'user_usergroup' => 3,
            'user_password' => '123',
            'user_password_val' => '123',
            'user_picture' => '(binary)',
            'created_by' => 1
        ];

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM users WHERE user_username = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 10
     * Action : delete_element
     * Description : delete info entry from the database
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_info()
    {
        $action = "delete_element";
        $test_name = "delete info entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_info/2");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM info WHERE info_id = 2")->row_array()['info_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 11
     * Action : delete_element
     * Description : delete user entry from the database 
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_user()
    {
        $action = "delete_element";
        $test_name = "delete user entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_user/" . getTestUserId());

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM users WHERE user_id =" . getTestUserId())->row_array()['user_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 12
     * Action : display_element
     * Description : display project configuration
     * Expected value: check if the correct element is displayed
     */
    private function listProjectConfig()
    {
        $action = "display_element";
        $test_name = "display project configuration";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/configurations/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 13
     * Action : display_element
     * Description : display author detail
     * Expected value: check if the correct element is displayed
     */
    private function displayAuthor()
    {
        $action = "display_element";
        $test_name = "display author detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/detail_author/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".author WHERE author_id = 1")->row_array();

            //check if entry is listed
            if (strstr($response['content'], $data['author_name']) != false) {
                $actual_value = "Yes";

            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 14
     * Action : display_element
     * Description : display classification detail
     * Expected value: check if the correct element is displayed
     */
    private function displayClassification()
    {
        $action = "display_element";
        $test_name = "display classification detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/detail_classification/1");

        // follow redirect
        while (in_array($response['status_code'], [http_code()[301], http_code()[302], http_code()[303], http_code()[307]])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".classification WHERE class_id = 1")->row_array();
            $paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id = " . $data['class_paper_id'])->row_array();

            //check if entry is listed
            if (strstr($response['content'], $paper['title']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 15
     * Action : display_element
     * Description : display project detail
     * Expected value: check if the correct element is displayed
     */
    private function displayProjectDetails()
    {
        $action = "display_element";
        $test_name = "display project detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $project = $this->ci->db->query("SELECT * FROM projects LIMIT 1")->row_array();
        $response = $this->http_client->response($this->controller, $action . "/detail_project/" . $project['project_id']);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //check if entry is listed
            if (strstr($response['content'], $project['project_label']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 16
     * Action : display_element
     * Description : display screening phase detail
     * Expected value: check if the correct element is displayed
     */
    private function displayScreeningPhaseDetails()
    {
        $action = "display_element";
        $test_name = "display screening phase detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/detail_screen_phase/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_id = 1")->row_array();

            //check if entry is listed
            if (strstr($response['content'], $data['description']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 17
     * Action : display_element
     * Description : display user-project detail
     * Expected value: check if the correct element is displayed
     */
    private function displayUserprojectDetail()
    {
        $action = "display_element";
        $test_name = "display user-project detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $userProject = $this->ci->db->query("SELECT * FROM userproject WHERE project_id = " . getProjectId() . " LIMIT 1")->row_array();
        $response = $this->http_client->response($this->controller, $action . "/detail_userproject/" . $userProject['userproject_id']);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $project = $this->ci->db->query("SELECT * FROM projects WHERE project_id = " . $userProject['project_id'])->row_array();

            //check if entry is listed
            if (strstr($response['content'], $project['project_title']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 18
     * Action : display_element
     * Description : display venue detail
     * Expected value: check if the correct element is displayed
     */
    private function displayVenueDetail()
    {
        $action = "display_element";
        $test_name = "display venue detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/detail_venue/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $venue = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".venue WHERE venue_id = 1")->row_array();

            //check if entry is listed
            if (strstr($response['content'], $venue['venue_fullName']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 19
     * Action : display_element
     * Description : display screening assignement detail
     * Expected value: check if the correct element is displayed
     */
    private function displayScreeningAssignment()
    {
        $action = "display_element";
        $test_name = "display screening assignement detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/display_assignment/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $assignment = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_id = 1")->row_array();
            $paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id = " . $assignment['paper_id'])->row_array();

            //check if entry is listed
            if (strstr($response['content'], $paper['title']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 20
     * Action : display_element
     * Description : display screening detail
     * Expected value: check if the correct element is displayed
     */
    private function displayScreening()
    {
        $action = "display_element";
        $test_name = "display screening detail";
        $test_aspect = "Correct element displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/display_screening/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entry in the db
            $screening = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_decison WHERE decison_id = 1")->row_array();
            $paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id = " . $screening['paper_id'])->row_array();

            //check if entry is listed
            if (strstr($response['content'], $paper['title']) != false) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 21
     * Action : entity_list
     * Description : display list of author affiliations
     * Expected value: check if the correct elements are displayed
     */
    private function listAffiliations()
    {
        $action = "entity_list";
        $test_name = "display list of author affiliations";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_affiliation");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_affiliation")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['ref_desc']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 22
     * Action : entity_list
     * Description : display list of papers
     * Expected value: check if the correct elements are displayed
     */
    private function listPapers()
    {
        $action = "entity_list";
        $test_name = "display list of papers";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_all_papers");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['title']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 23
     * Action : entity_list
     * Description : display list of pending screenings
     * Expected value: check if the correct elements are displayed
     */
    private function listPendingScreening()
    {
        $action = "entity_list";
        $test_name = "display list of pending screenings";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_all_pending_screenings");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 24
     * Action : entity_list
     * Description : display list of all screening assignments
     * Expected value: check if the correct elements are displayed
     */
    private function listAllAssignments()
    {
        $action = "entity_list";
        $test_name = "display list of all screening assignments";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_assignments");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 25
     * Action : entity_list
     * Description : display list of all authors 
     * Expected value: check if the correct elements are displayed
     */
    private function listAllAuthors()
    {
        $action = "entity_list";
        $test_name = "display list of all authors";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_authors");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".author")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['author_name']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 26
     * Action : entity_list
     * Description : display list of all authors in classification
     * Expected value: check if the correct elements are displayed
     */
    private function listAllAuthorClass()
    {
        $action = "entity_list";
        $test_name = "display list of all authors in classification";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_authors_class");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 27
     * Action : entity_list
     * Description : display list of all classification assignments
     * Expected value: check if the correct elements are displayed
     */
    private function listAllClassificationAssignments()
    {
        $action = "entity_list";
        $test_name = "display list of all classification assignments";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_class_assignment");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 28
     * Action : entity_list
     * Description : display list of all classification assignments done
     * Expected value: check if the correct elements are displayed
     */
    private function listAllClassificationAssignmentsDone()
    {
        $action = "entity_list";
        $test_name = "display list of all classification assignments done";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_class_assignment_done");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 29
     * Action : entity_list
     * Description : display list of all classification assignments pending
     * Expected value: check if the correct elements are displayed
     */
    private function listAllClassificationAssignmentsPending()
    {
        $action = "entity_list";
        $test_name = "display list of all classification assignments pending";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_class_assignment_pending");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 30
     * Action : entity_list
     * Description : display list of all classifications 
     * Expected value: check if the correct elements are displayed
     */
    private function listAllclassifications()
    {
        $action = "entity_list";
        $test_name = "display list of all classifications";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_classification");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".classification")->result_array();
            $paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id IN (SELECT class_paper_id FROM relis_dev_correct_" . getProjectShortName() . ".classification)")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($paper as $dt) {
                if (strstr($response['content'], $dt['title']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 31
     * Action : entity_list
     * Description : display list of all excluded papers
     * Expected value: check if the correct elements are displayed
     */
    private function listAllExcludedPapers()
    {
        $action = "entity_list";
        $test_name = "display list of all excluded papers";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_excluded_papers");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 32
     * Action : entity_list
     * Description : display list of all exclusion criteria 
     * Expected value: check if the correct elements are displayed
     */
    private function listAllExclusionCriteria()
    {
        $action = "entity_list";
        $test_name = "display list of all exclusion criteria";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_exclusioncrieria");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_exclusioncrieria")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['ref_desc']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 33
     * Action : entity_list
     * Description : display list of all included papers
     * Expected value: check if the correct elements are displayed
     */
    private function listAllIncludedPapers()
    {
        $action = "entity_list";
        $test_name = "display list of all included papers";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_included_papers");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 34
     * Action : entity_list
     * Description : display list of all inclusion criteria 
     * Expected value: check if the correct elements are displayed
     */
    private function listAllInclusionCriteria()
    {
        $action = "entity_list";
        $test_name = "display list of all inclusion criteria";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_inclusioncriteria");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_inclusioncriteria")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['ref_desc']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 35
     * Action : entity_list
     * Description : display list of all logs
     * Expected value: check if the correct elements are displayed
     */
    private function listAllLogs()
    {
        $action = "entity_list";
        $test_name = "display list of all logs";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_logs");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 36
     * Action : entity_list
     * Description : display list of all operations
     * Expected value: check if the correct elements are displayed
     */
    private function listAllOperations()
    {
        $action = "entity_list";
        $test_name = "display list of all operations";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_operations");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 37
     * Action : entity_list
     * Description : display list of all paper screened
     * Expected value: check if the correct elements are displayed
     */
    private function listAllPaperScreened()
    {
        $action = "entity_list";
        $test_name = "display list of all paper screened";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_papers_screen");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 38
     * Action : entity_list
     * Description : display list of all paper screen in conflict 
     * Expected value: check if the correct elements are displayed
     */
    private function list_papers_screen_conflict()
    {
        $action = "entity_list";
        $test_name = "display list of all paper screened in conflict";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_papers_screen_conflict");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 39
     * Action : entity_list
     * Description : display list of all paper screened excluded 
     * Expected value: check if the correct elements are displayed
     */
    private function list_papers_screen_excluded()
    {
        $action = "entity_list";
        $test_name = "display list of all excluded paper screen";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_papers_screen_excluded");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 40
     * Action : entity_list
     * Description : display list of all paper screened included 
     * Expected value: check if the correct elements are displayed
     */
    private function list_papers_screen_included()
    {
        $action = "entity_list";
        $test_name = "display list of all included paper screen";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_papers_screen_included");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 41
     * Action : entity_list
     * Description : display list of all QA papers
     * Expected value: check if the correct elements are displayed
     */
    private function list_qa_papers()
    {
        $action = "entity_list";
        $test_name = "display list of all QA papers";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_qa_papers");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 42
     * Action : entity_list
     * Description : display list of all QA papers done
     * Expected value: check if the correct elements are displayed
     */
    private function list_qa_papers_done()
    {
        $action = "entity_list";
        $test_name = "display list of all QA papers done";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_qa_papers_done");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 43
     * Action : entity_list
     * Description : display list of all QA papers pending
     * Expected value: check if the correct elements are displayed
     */
    private function list_qa_papers_pending()
    {
        $action = "entity_list";
        $test_name = "display list of all QA papers pending";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_qa_papers_pending");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 44
     * Action : entity_list
     * Description : display list of all qa questions
     * Expected value: check if the correct elements are displayed
     */
    private function list_qa_questions()
    {
        $action = "entity_list";
        $test_name = "display list of all qa questions";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_qa_questions");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_questions")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['question']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 45
     * Action : entity_list
     * Description : display list of all qa responses
     * Expected value: check if the correct elements are displayed
     */
    private function list_qa_responses()
    {
        $action = "entity_list";
        $test_name = "display list of all qa responses";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_qa_responses");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_responses")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['response']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 46
     * Action : entity_list
     * Description : display list of all brands
     * Expected value: check if the correct elements are displayed
     */
    private function list_ref_brand()
    {
        $action = "entity_list";
        $test_name = "display list of all brands";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_ref_brand");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_brand")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['ref_value']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 47
     * Action : entity_list
     * Description : display list of all varieties
     * Expected value: check if the correct elements are displayed
     */
    private function list_ref_variety()
    {
        $action = "entity_list";
        $test_name = "display list of all varieties";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_ref_variety");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_variety")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['ref_value']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 48
     * Action : entity_list
     * Description : display list of all research questions
     * Expected value: check if the correct elements are displayed
     */
    private function list_research_question()
    {
        $action = "entity_list";
        $test_name = "display list of all research questions";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_research_question");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 49
     * Action : entity_list
     * Description : display list of all screen phases
     * Expected value: check if the correct elements are displayed
     */
    private function list_screen_phases()
    {
        $action = "entity_list";
        $test_name = "display list of all screen phases";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_screen_phases");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['phase_title']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 50
     * Action : entity_list
     * Description : display list of all venues
     * Expected value: check if the correct elements are displayed
     */
    private function list_venues()
    {
        $action = "entity_list";
        $test_name = "display list of all venues";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";

        $response = $this->http_client->response($this->controller, $action . "/list_venues");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "No";

            //get entries in the db
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".venue")->result_array();

            //check if all entries are listed
            $entriesListed = [];
            foreach ($data as $dt) {
                if (strstr($response['content'], $dt['venue_fullName']) != false) {
                    array_push($entriesListed, $dt);
                }
            }
            if (count($entriesListed) == count($data)) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 51
     * Action : entity_list
     * Description : display list of all screeningy
     * Expected value: check if the correct elements are displayed
     */
    private function list_screenings()
    {
        $action = "entity_list";
        $test_name = "display list of all screenings";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_screenings");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 52
     * Action : entity_list_graph
     * Description : display list of all classification graphs
     * Expected value: check if the correct elements are displayed
     */
    private function list_classification_graphs()
    {
        $action = "entity_list_graph";
        $test_name = "display list of all classification graphs";
        $test_aspect = "Correct elements displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action . "/list_classification");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 53
     * Action : add_element
     * Description : display form for adding author affiliation
     */
    private function add_affiliation()
    {
        $action = "add_element";
        $test_name = "display form for adding author affiliation";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_affiliation");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 54
     * Action : add_element
     * Description : display form for adding author
     */
    private function add_author()
    {
        $action = "add_element";
        $test_name = "display form for adding author";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_author");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 55
     * Action : add_element
     * Description : display form for adding exclusion criteria
     */
    private function add_exclusioncriteria()
    {
        $action = "add_element";
        $test_name = "display form for adding exclusion criteria";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_exclusioncrieria");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 56
     * Action : add_element
     * Description : display form for adding inclusion criteria
     */
    private function add_inclusioncriteria()
    {
        $action = "add_element";
        $test_name = "display form for adding inclusion criteria";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_inclusioncriteria");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 57
     * Action : add_element
     * Description : display form for adding info
     */
    private function add_info()
    {
        $action = "add_element";
        $test_name = "display form for adding info";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_info");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 58
     * Action : add_element
     * Description : display form for adding paper
     */
    private function add_paper()
    {
        $action = "add_element";
        $test_name = "display form for adding paper";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_paper");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 59
     * Action : add_element
     * Description : display form for adding qa question
     */
    private function add_qa_questions()
    {
        $action = "add_element";
        $test_name = "display form for adding qa question";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_qa_questions");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 60
     * Action : add_element
     * Description : display form for adding qa response
     */
    private function add_qa_responses()
    {
        $action = "add_element";
        $test_name = "display form for adding qa response";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_qa_responses");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 61
     * Action : add_element
     * Description : display form for adding ref brand
     */
    private function add_ref_brand()
    {
        $action = "add_element";
        $test_name = "display form for adding ref brand";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_ref_brand");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 62
     * Action : add_element
     * Description : display form for adding ref variety
     */
    private function add_ref_variety()
    {
        $action = "add_element";
        $test_name = "display form for adding ref variety";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_ref_variety");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 63
     * Action : add_element
     * Description : display form for adding research question
     */
    private function add_research_question()
    {
        $action = "add_element";
        $test_name = "display form for adding research question";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_research_question");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 64
     * Action : add_element
     * Description : display form for adding screen phase
     */
    private function add_screen_phase()
    {
        $action = "add_element";
        $test_name = "display form for adding screen phase";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_screen_phase");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 65
     * Action : add_element
     * Description : display form for adding user
     */
    private function add_user()
    {
        $action = "add_element";
        $test_name = "display form for adding user";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_user");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 66
     * Action : add_element
     * Description : display form for adding user to project
     */
    private function add_user_current_project()
    {
        $action = "add_element";
        $test_name = "display form for adding user to project";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_user_current_project");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 67
     * Action : add_element
     * Description : display form for adding venue
     */
    private function add_venue()
    {
        $action = "add_element";
        $test_name = "display form for adding venue";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_venue");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 68
     * Action : add_element_child
     * Description : display form for adding a reviewer to a paper
     */
    private function add_reviewer()
    {
        $action = "add_element_child";
        $test_name = "display form for adding a reviewer to a paper";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/add_reviewer/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 69
     * Action : add_element_child
     * Description : display form for adding classification assignation to a paper
     */
    private function new_assignment_class()
    {
        $action = "add_element_child";
        $test_name = "display form for adding classification assignation to a paper";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/new_assignment_class/4");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 70
     * Action : edit_element
     * Description : display form for editing author
     */
    private function edit_author()
    {
        $action = "edit_element";
        $test_name = "display form for editing author";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_author/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 71
     * Action : edit_element
     * Description : display form for editing exclusion criteria
     */
    private function edit_exclusioncrieria()
    {
        $action = "edit_element";
        $test_name = "display form for editing exclusion criteria";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_exclusioncrieria/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 72
     * Action : edit_element
     * Description : display form for editing paper
     */
    private function edit_paper()
    {
        $action = "edit_element";
        $test_name = "display form for editing paper";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_paper/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 73
     * Action : edit_element
     * Description : display form for editing qa question
     */
    private function edit_qa_questions()
    {
        $action = "edit_element";
        $test_name = "display form for editing qa question";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_qa_questions/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 74
     * Action : edit_element
     * Description : display form for editing qa response
     */
    private function edit_qa_responses()
    {
        $action = "edit_element";
        $test_name = "display form for editing qa response";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_qa_responses/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 75
     * Action : edit_element
     * Description : display form for editing ref brand
     */
    private function edit_ref_brand()
    {
        $action = "edit_element";
        $test_name = "display form for editing ref brand";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_ref_brand/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 76
     * Action : edit_element
     * Description : display form for editing ref variety
     */
    private function edit_ref_variety()
    {
        $action = "edit_element";
        $test_name = "display form for editing ref variety";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_ref_variety/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 77
     * Action : edit_element
     * Description : display form for editing screen phase
     */
    private function edit_screen_phase()
    {
        $action = "edit_element";
        $test_name = "display form for editing screen phase";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_screen_phase/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 78
     * Action : edit_element
     * Description : display form for editing user
     */
    private function edit_user()
    {
        $action = "edit_element";
        $test_name = "display form for editing user";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_user/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 79
     * Action : edit_element
     * Description : display form for editing venue
     */
    private function edit_venue()
    {
        $action = "edit_element";
        $test_name = "display form for editing venue";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_venue/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 80
     * Action : edit_element
     * Description : display form for editing classification assignation to a paper
     */
    private function edit_assignment_class()
    {
        $action = "edit_element";
        $test_name = "display form for editing classification assignation to a paper";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_assignment_class/2");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 81
     * Action : edit_element
     * Description : display form for editing screening config
     */
    private function edit_config_screening()
    {
        $action = "edit_element";
        $test_name = "display form for editing screening config";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_config_screening/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 82
     * Action : edit_element
     * Description : display form for editing QA config
     */
    private function edit_config_qa()
    {
        $action = "edit_element";
        $test_name = "display form for editing QA config";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_config_qa/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 83
     * Action : edit_element
     * Description : display form for editing classification config
     */
    private function edit_config_class()
    {
        $action = "edit_element";
        $test_name = "display form for editing classification config";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_config_class/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 84
     * Action : edit_element
     * Description : display form for editing papers config
     */
    private function edit_conf_papers()
    {
        $action = "edit_element";
        $test_name = "display form for editing papers config";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_conf_papers/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 85
     * Action : edit_element
     * Description : display form for editing project
     */
    private function edit_project()
    {
        $action = "edit_element";
        $test_name = "display form for editing project";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/edit_project/" . getProjectId());

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 86
     * Action : edit_drilldown
     * Description : display form for updating classification
     */
    private function update_classification()
    {
        $action = "edit_drilldown";
        $test_name = "display form for updating classification";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/update_classification/2/4");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 87
     * Action : save_element
     * Description : save new paper 
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_paper()
    {
        $action = "save_element";
        $test_name = "save new paper";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = [
            'operation_type' => 'new',
            'table_config' => 'papers',
            'current_operation' => 'add_paper',
            'redirect_after_save' => '',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'id' => '',
            'added_by' => 1,
            'bibtexKey' => 'key01',
            'title' => 'testField01',
            'doi' => 'link',
            'year' => 2017,
            'venueId' => 2,
            'authors' => array(12),
            'preview' => '',
            'bibtex' => '',
            'abstract' => '',
            'papers_sources' => '',
            'search_strategy' => ''
        ];

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE title = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 88
     * Action : save_element
     * Description : save new affiliation 
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_affiliation()
    {
        $action = "save_element";
        $test_name = "save new affiliation";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'new',
            'table_config' => 'affiliation',
            'current_operation' => 'add_affiliation',
            'redirect_after_save' => 'element/entity_list/list_affiliation',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'ref_id' => '',
            'ref_value' => 'testField01',
            'ref_desc' => 'Description'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_affiliation WHERE ref_value = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 89
     * Action : save_element
     * Description : save new exclusion criteria 
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_exclusion_criteria()
    {
        $action = "save_element";
        $test_name = "save new exclusion criteria";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'new',
            'table_config' => 'exclusioncrieria',
            'current_operation' => 'add_exclusioncrieria',
            'redirect_after_save' => 'element/entity_list/list_exclusioncrieria',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'ref_id' => '',
            'ref_value' => 'testField01',
            'ref_desc' => 'Description'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_exclusioncrieria WHERE ref_value = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 90
     * Action : save_element
     * Description : save new qa question 
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_qa_question()
    {
        $action = "save_element";
        $test_name = "save new qa question";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'new',
            'table_config' => 'qa_questions',
            'current_operation' => 'add_qa_questions',
            'redirect_after_save' => 'element/entity_list/list_qa_questions',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'question_id' => '',
            'question' => 'testField01'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_questions WHERE question = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 91
     * Action : save_element
     * Description : save new qa response 
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_qa_response()
    {
        $action = "save_element";
        $test_name = "save new qa response";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'new',
            'table_config' => 'qa_responses',
            'current_operation' => 'add_qa_responses',
            'redirect_after_save' => 'element/entity_list/list_qa_responses',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'response_id' => '',
            'response' => 'testField01',
            'score' => 4
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_responses WHERE response = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 92
     * Action : save_element
     * Description : save new brand 
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_brand()
    {
        $action = "save_element";
        $test_name = "save new brand";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'new',
            'table_config' => 'ref_brand',
            'current_operation' => 'add_ref_brand',
            'redirect_after_save' => 'element/entity_list/list_ref_brand',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'ref_id' => '',
            'ref_value' => 'testField01',
            'ref_desc' => 'Description'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_brand WHERE ref_value = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 93
     * Action : save_element
     * Description : save new variety 
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_variety()
    {
        $action = "save_element";
        $test_name = "save new variety";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'new',
            'table_config' => 'ref_variety',
            'current_operation' => 'add_ref_variety',
            'redirect_after_save' => 'element/entity_list/list_ref_variety',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'ref_id' => '',
            'ref_value' => 'testField01',
            'ref_desc' => 'Description'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_variety WHERE ref_value = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 94
     * Action : save_element
     * Description : save new research question 
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_research_question()
    {
        $action = "save_element";
        $test_name = "save new research question";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'new',
            'table_config' => 'research_question',
            'current_operation' => 'add_research_question',
            'redirect_after_save' => 'element/entity_list/list_research_question',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'ref_id' => '',
            'ref_value' => 'testField01',
            'ref_desc' => 'Description'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".research_question WHERE ref_value = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 95
     * Action : save_element
     * Description : save new search strategy 
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_search_strategy()
    {
        $action = "save_element";
        $test_name = "save new search strategy";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'new',
            'table_config' => 'search_strategy',
            'current_operation' => 'add_search_strategy',
            'redirect_after_save' => 'element/entity_list/list_search_strategy',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'ref_id' => '',
            'ref_value' => 'testField01',
            'ref_desc' => 'Description'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_search_strategy WHERE ref_value = 'testField01'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 96
     * Action : save_element
     * Description : save new user to project
     * Expected result: check if the element is saved in the DB
     */
    private function save_new_user_project()
    {
        $action = "save_element";
        $test_name = "save new user to project";
        $test_aspect = "is element saved? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'new',
            'table_config' => 'user_project',
            'current_operation' => 'add_user_current_project',
            'redirect_after_save' => 'element/entity_list/list_users_current_projects',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'userproject_id' => '',
            'user_id' => 2,
            'project_id' => getProjectId(),
            'user_role' => 'Reviewer',
            'added_by' => 1
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM userproject WHERE user_id = 2")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 97
     * Action : save_element
     * Description : update paper config 
     * Expected result: check if the element is updated in the DB
     */
    private function update_paper_config()
    {
        $action = "save_element";
        $test_name = "update paper config";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'edit',
            'table_config' => 'config',
            'current_operation' => 'edit_conf_papers',
            'redirect_after_save' => 'element/display_element/configurations/1',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'config_id' => 1,
            'import_papers_on' => array(0, 1),
            'csv_field_separator' => ',',
            'csv_field_separator_export' => ',',
            'key_paper_prefix' => '',
            'key_paper_serial' => 1,
            'list_trim_nbr' => 80,
            'source_papers_on' => array(0, 1),
            'search_strategy_on' => array(0, 1)
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = '1';
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 98
     * Action : save_element
     * Description : update screening config 
     * Expected result: check if the element is updated in the DB
     */
    private function update_screening_config()
    {
        $action = "save_element";
        $test_name = "update screening config";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
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
            'config_id' => 1,
            'screening_on' => array(0, 1),
            'screening_result_on' => array(0, 1),
            'assign_papers_on' => array(0, 1),
            'screening_reviewer_number' => 2,
            'screening_conflict_type' => 'ExclusionCriteria',
            'screening_screening_conflict_resolution' => 'Unanimity',
            'use_kappa' => array(0, 1),
            'screening_validation_on' => array(0, 1),
            'screening_validator_assignment_type' => 'Normal',
            'screening_status_to_validate' => 'Excluded',
            'validation_default_percentage' => 50
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = '1';
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 99
     * Action : save_element
     * Description : update qa config 
     * Expected result: check if the element is updated in the DB
     */
    private function update_qa_config()
    {
        $action = "save_element";
        $test_name = "update qa config";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'edit',
            'table_config' => 'config',
            'current_operation' => 'edit_config_qa',
            'redirect_after_save' => 'element/display_element/configurations/1',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'config_id' => 1,
            'qa_on' => array(0, 1),
            'qa_cut_off_score' => 5,
            'qa_validation_on' => array(0, 1),
            'qa_validation_default_percentage' => 100
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = '1';
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 100
     * Action : save_element
     * Description : update classification config 
     * Expected result: check if the element is updated in the DB
     */
    private function update_class_config()
    {
        $action = "save_element";
        $test_name = "update classification config";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'edit',
            'table_config' => 'config',
            'current_operation' => 'edit_config_class',
            'redirect_after_save' => 'element/display_element/configurations/1',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'config_id' => 1,
            'class_validation_on' => array(0, 1),
            'class_validation_default_percentage' => 80
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = '1';
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 101
     * Action : save_element
     * Description : update exclusion criteria 
     * Expected result: check if the element is updated in the DB
     */
    private function update_exclusion_criteria()
    {
        $action = "save_element";
        $test_name = "update exclusion criteria";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'edit',
            'table_config' => 'exclusioncrieria',
            'current_operation' => 'edit_exclusioncrieria',
            'redirect_after_save' => 'element/entity_list/list_exclusioncrieria',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'ref_id' => 1,
            'ref_value' => 'testField02',
            'ref_desc' => 'EC1: Too short'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_exclusioncrieria WHERE ref_value = 'testField02'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 102
     * Action : save_element
     * Description : update paper 
     * Expected result: check if the element is updated in the DB
     */
    private function update_paper()
    {
        $action = "save_element";
        $test_name = "update paper";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'edit',
            'table_config' => 'papers',
            'current_operation' => 'edit_paper',
            'redirect_after_save' => 'element/entity_list/list_all_papers',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'id' => 1,
            'bibtexKey' => 'testField02',
            'title' => 'A Metamodel Composition Driven Approach to Design New Domain Specific Modeling Languages',
            'doi' => 'https://dx.doi.org/10.1109/eecs.2017.30',
            'year' => 2017,
            'venueId' => '',
            'authors' => array(1, 2, 3),
            'preview' => '<p><font face="verdana"><b><i>BOOK</i>(Abouzahra2017)</b></font></p>
                             <p>A. Abouzahra, A. Sabraoui, and K. Afdel. <i><span style="font-weight: 600; color: #3F3F3F;">A Metamodel Composition Driven Approach to Design New Domain Specific Modeling Languages</span></i>. 2017 European Conference on Electrical Engineering and Computer Science:. Ieee, New York (2017).</p>
                             <p><center></center></p>',
            'bibtex' => '@BOOK{Abouzahra2017,
                              author = {Abouzahra, A. and Sabraoui, A. and Afdel, K.},
                              publisher = {Ieee},
                              title = {A Metamodel Composition Driven Approach to Design New Domain Specific Modeling Languages},
                              year = {2017},
                              address = {New York},
                              series = {2017 European Conference on Electrical Engineering and Computer Science},
                              abstract = {Designing a new domain specific modeling language is a complex and consuming task job effort. One solution is to compose existing DSMLs to form a new more complete DSML. Based on a metamodels composition we can improve the process of rapid prototyping of new DSMLs. In this paper we investigate the consequences of composing metamodels of DSMLs on their graphical syntaxes. Our motivation is to upgrade the quick development of graphical comments for a new DSML formed by the composition of multiple DSMLs metamodels. We explain how the DSMLs can be reused to rapidly form new ones with low cost. Thus, this work contributes by defining a set of rules to compose DSMLs and provide a layer for DSMLs graphical syntaxes composition. We propose three rules to compose DSMLs metamodels: reference, specialization and fusion. We have used a small use case to illustrate the approach. For each defined rule we apply it to the use case to exemplify it. We expose, throughout these examples, how graphical syntaxes can be reused. We also show on how graphical comments can be composed.},
                              doi = {10.1109/eecs.2017.30},
                              paper = {https://dx.doi.org/10.1109/eecs.2017.30}
                             }',
            'abstract' => 'Designing a new domain specific modeling language is a complex and consuming task job effort. One solution is to compose existing DSMLs to form a new more complete DSML. Based on a metamodels composition we can improve the process of rapid prototyping of new DSMLs. In this paper we investigate the consequences of composing metamodels of DSMLs on their graphical syntaxes. Our motivation is to upgrade the quick development of graphical comments for a new DSML formed by the composition of multiple DSMLs metamodels. We explain how the DSMLs can be reused to rapidly form new ones with low cost. Thus, this work contributes by defining a set of rules to compose DSMLs and provide a layer for DSMLs graphical syntaxes composition. We propose three rules to compose DSMLs metamodels: reference, specialization and fusion. We have used a small use case to illustrate the approach. For each defined rule we apply it to the use case to exemplify it. We expose, throughout these examples, how graphical syntaxes can be reused. We also show on how graphical comments can be composed.',
            'papers_sources' => '',
            'search_strategy' => ''
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE bibtexKey = 'testField02'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 103
     * Action : save_element
     * Description : update qa question 
     * Expected result: check if the element is updated in the DB
     */
    private function update_qa_question()
    {
        $action = "save_element";
        $test_name = "update qa question";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'edit',
            'table_config' => 'qa_questions',
            'current_operation' => 'edit_qa_questions',
            'redirect_after_save' => 'element/entity_list/list_qa_questions',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'question_id' => 1,
            'question' => 'testField02'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_questions WHERE question = 'testField02'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 104
     * Action : save_element
     * Description : update qa response 
     * Expected result: check if the element is updated in the DB
     */
    private function update_qa_response()
    {
        $action = "save_element";
        $test_name = "update qa response";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'edit',
            'table_config' => 'qa_responses',
            'current_operation' => 'edit_qa_responses',
            'redirect_after_save' => 'element/entity_list/list_qa_responses',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'response_id' => 1,
            'response' => 'testField02',
            'score' => 3
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_responses WHERE response = 'testField02'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 105
     * Action : save_element
     * Description : update brand 
     * Expected result: check if the element is updated in the DB
     */
    private function update_brand()
    {
        $action = "save_element";
        $test_name = "update brand";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'edit',
            'table_config' => 'ref_brand',
            'current_operation' => 'edit_ref_brand',
            'redirect_after_save' => 'element/entity_list/list_ref_brand',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'ref_id' => 1,
            'ref_value' => 'testField02',
            'ref_desc' => 'Ferrero'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_brand WHERE ref_value = 'testField02'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 106
     * Action : save_element
     * Description : update variety 
     * Expected result: check if the element is updated in the DB
     */
    private function update_variety()
    {
        $action = "save_element";
        $test_name = "update variety";
        $test_aspect = "is element updated? (1 = Yes, 0 = No)";
        $expected_value = '1';
        $actual_value = '0';

        $postData = array(
            'operation_type' => 'edit',
            'table_config' => 'ref_variety',
            'current_operation' => 'edit_ref_variety',
            'redirect_after_save' => 'element/entity_list/list_ref_variety',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'ref_id' => 1,
            'ref_value' => 'testField02',
            'ref_desc' => 'Bitter'
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $data = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_variety WHERE ref_value = 'testField02'")->row_array();
            if (!empty($data)) {
                $actual_value = '1';
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 107
     * Action : delete_element
     * Description : delete paper entry from the database
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_paper()
    {
        $action = "delete_element";
        $test_name = "delete paper entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_paper/2");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id = 2")->row_array()['paper_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 108
     * Action : delete_element
     * Description : delete screening assignement entry from the database
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_assignment()
    {
        $action = "delete_element";
        $test_name = "delete screening assignement entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_assignment/3");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_id = 3")->row_array()['screening_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 109
     * Action : delete_element
     * Description : delete author entry from the database 
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_author()
    {
        $action = "delete_element";
        $test_name = "delete author entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_author/2");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".author WHERE author_id = 2")->row_array()['author_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 110
     * Action : delete_element
     * Description : delete classification assignment entry from the database 
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_class_assignment()
    {
        $action = "delete_element";
        $test_name = "delete classification assignment entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_class_assignment/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_id = 1")->row_array()['assigned_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 111
     * Action : delete_element
     * Description : delete exclusion criteria entry from the database 
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_exclusioncriteria()
    {
        $action = "delete_element";
        $test_name = "delete exclusion criteria entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_exclusioncrieria/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_exclusioncrieria WHERE ref_id = 1")->row_array()['ref_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 112
     * Action : delete_element
     * Description : delete qa question entry from the database
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_qa_questions()
    {
        $action = "delete_element";
        $test_name = "delete qa question entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_qa_questions/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_questions WHERE question_id = 1")->row_array()['question_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 113
     * Action : delete_element
     * Description : delete qa response entry from the database
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_qa_responses()
    {
        $action = "delete_element";
        $test_name = "delete qa response entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_qa_responses/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_responses WHERE response_id = 1")->row_array()['response_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 114
     * Action : delete_element
     * Description : delete brand entry from the database 
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_ref_brand()
    {
        $action = "delete_element";
        $test_name = "delete brand entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_ref_brand/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_brand WHERE ref_id = 1")->row_array()['ref_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 115
     * Action : delete_element
     * Description : delete variety entry from the database 
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_ref_variety()
    {
        $action = "delete_element";
        $test_name = "delete variety entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_ref_variety/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".ref_variety WHERE ref_id = 1")->row_array()['ref_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 116
     * Action : delete_element
     * Description : delete screen phase entry from the database 
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_screen_phase()
    {
        $action = "delete_element";
        $test_name = "delete screen phase entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_screen_phase/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_id = 1")->row_array()['screen_phase_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 117
     * Action : delete_element
     * Description : delete venue entry from the database 
     * Expected result: check if the element is deleted or active field become 0 in the DB
     */
    private function remove_venue()
    {
        $action = "delete_element";
        $test_name = "delete venue entry from the database";
        $test_aspect = "is element deleted? (1 = No, 0 = Yes)";
        $expected_value = '0';
        $actual_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/remove_venue/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".venue WHERE venue_id = 1")->row_array()['venue_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }
}



