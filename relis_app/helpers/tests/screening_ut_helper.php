<?php

// TEST PAPER CONTROLLER
class ScreeningUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "screening";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->screeningSelect();
        $this->screeningSelect_2ScreeningPhases_classification();
        $this->screeningSelect_classification();
        $this->selectScreenPhase();
        $this->openScreenPhase();
        $this->closeScreenPhase();
        $this->assignmentScreen();
        $this->saveAssignmentScreen_withoutNumberOfUsers();
        $this->saveAssignmentScreen_withoutReviewsPerPaper();
        $this->saveAssignmentScreen_ReviewPerPaper_moreThan_NbrOfUsers();
        $this->saveAssignmentScreen_emptyUsers();
        $this->saveAssignment_5papers_1reviewer();
        $this->saveAssignment_6papers_3reviewers();
        $this->saveAssignment_5papers_3reviewers();
        $this->saveAssignment_5papers_2reviewers();
        $this->saveScreening_InclusionField();
        $this->saveAssignment_5papers_2reviewers_2reviewsPerPaper();
        $this->displayPapersForScreen();
        $this->displayPaperNotScreenedYet();
        $this->saveScreening_noExcusionField();
        $this->screenResult_noPaperScrenned();
        $this->screenResult_3PapersScrenned();
        $this->saveScreeningAllPapers();
        $this->displayPaperForScreenEdit();
        $this->screenCompletion();
        $this->screenResult_1Included_2excluded_1conflict();
        $this->savePhaseScreen_withoutTitleField();
        $this->savePhaseScreen_withoutDisplayed_fields_valsField();
        $this->savePhaseScreen_finalPhaseAlreadyExist();
        $this->savePhaseScreen();
        $this->displayPaperScreen();
        $this->listScreen();
        $this->validateScreenSet();
        $this->saveAssignmentValidation_emptyPercentage();
        $this->saveAssignmentValidation_invalidPercentage();
        $this->saveAssignmentValidation_emptyUsers();
        $this->saveAssignmentValidation_50percent();
        $this->saveAssignmentValidation_assignToNotScreenedUser();
        $this->saveAssignmentValidation_100percent();
        $this->saveAssignmentValidation_by1criteria();
        $this->screenPaperValidation();
        $this->screenValidationCompletion();
        $this->screenValidationResult();
        $this->removeScreening();
        $this->removeScreeningValidation();
        $this->screening_4papersScreened();
        $this->screening_0paperScreened();
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

        //add users to test Project
        addUserToProject(getAdminUserId(), "Reviewer");
        addUserToProject(getTestUserId(), "Reviewer");
    }

    /*
     * Test 1
     * Action : screening_select
     * Description : display a list of screening phases, quality assessment, classification and their completion statues in the Home page. (with 1 screening phase (Title), quality assessment, classification)
     * Expected result : check if 1 screening phase (Title) is displayed, 1 QA, 1 classification.
     */
    private function screeningSelect()
    {
        $action = "screening_select";
        $test_name = "Display a list of screening phases, quality assessment, classification and their completion statues in the Home page. (with 1 screening phase (Title), quality assessment, classification)";
        $test_aspect = "are 1 screening phase (Title), quality assessment, classification listed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (
                strstr($response['content'], "Screening : Title") != false &&
                strstr($response['content'], "Quality assessment") != false &&
                strstr($response['content'], "Classification") != false
            ) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 2
     * Action : screening_select
     * Description : display a list of screening phases, quality assessment, classification and their completion statues in the Home page. (with 2 screening phases (Title, abstract), quality assessment, classification)
     * Expected result : check if 2 screening phases (Title, Abstract), 1 classification are displayed.
     */
    private function screeningSelect_2ScreeningPhases_classification()
    {
        $action = "screening_select";
        $test_name = "Display a list of screening phases, quality assessment, classification and their completion statues in the Home page. (with 2 screening phases (Title, abstract), classification)";
        $test_aspect = "are 2 screening phase (Title, Abstract), classification listed?";
        $expected_value = "Yes";
        $actual_value = "No";

        //add abstract screening phase
        addScreeningPhase("Abstract");
        //deactivate QA phase
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET qa_on = 0");
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (
                strstr($response['content'], "Screening : Title") != false &&
                strstr($response['content'], "Screening : Abstract") != false &&
                strstr($response['content'], "Quality assessment") == false &&
                strstr($response['content'], "Classification") != false
            ) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 3
     * Action : screening_select
     * Description : display a list of screening phases, quality assessment, classification and their completion statues in the Home page. (with only classification phase)
     * Expected result : check if the classification phase is displayed.
     */
    private function screeningSelect_classification()
    {
        $action = "screening_select";
        $test_name = "Display a list of screening phases, quality assessment, classification and their completion statues in the Home page. (with only classification phase)";
        $test_aspect = "Is only classification phase listed?";
        $expected_value = "Yes";
        $actual_value = "No";

        //deactivate screening phase
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET screening_on = 0");
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (
                strstr($response['content'], "Screening : Title") == false &&
                strstr($response['content'], "Screening : Abstract") == false &&
                strstr($response['content'], "Quality assessment") == false &&
                strstr($response['content'], "Classification") != false
            ) {
                $actual_value = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 4
     * Action : select_screen_phase
     * Description : Handle the selection of a screen phase and update the session variable accordingly.
     * Expected session data 'current_screen_phase': screen phase ID
     */
    private function selectScreenPhase()
    {
        $this->TestInitialize();

        $action = "select_screen_phase";
        $test_name = "Handle the selection of a screen phase and update the session variable accordingly";

        $test_aspect_screenPhaseId = "Current screen phase ID";
        $expected_screenPhaseId = getScreeningPhaseId("Title");

        $response = $this->http_client->response($this->controller, $action . "/" . getScreeningPhaseId("Title"));

        if ($response['status_code'] >= 400) {
            $actual_screenPhaseId = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_screenPhaseId = $this->http_client->readUserdata('current_screen_phase');
        }

        run_test($this->controller, $action, $test_name, $test_aspect_screenPhaseId, $expected_screenPhaseId, $actual_screenPhaseId);
    }

    /*
     * Test 5
     * Action : screening_phase_manage
     * Description : Open a specific screening phase.
     * Expected phase state in DB: Open
     */
    private function openScreenPhase()
    {
        $action = "screening_phase_manage";
        $test_name = "Open a specific screening phase";
        $test_aspect_phaseState = "Phase state in DB";
        $expected_phaseState = "Open";

        $response = $this->http_client->response($this->controller, $action . "/" . getScreeningPhaseId("Title"));

        if ($response['status_code'] >= 400) {
            $actual_phaseState = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_phaseState = $this->ci->db->query("SELECT phase_state FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_id =" . getScreeningPhaseId("Title"))->row_array()['phase_state'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_phaseState, $expected_phaseState, $actual_phaseState);
    }

    /*
     * Test 6
     * Action : screening_phase_manage
     * Description : close a specific screening phase.
     * Expected phase state in DB: Closed
     */
    private function closeScreenPhase()
    {
        $action = "screening_phase_manage";
        $test_name = "Close a specific screening phase";
        $test_aspect_phaseState = "Phase state in DB";
        $expected_phaseState = "Closed";

        $response = $this->http_client->response($this->controller, $action . "/" . getScreeningPhaseId("Title") . "/0");

        if ($response['status_code'] >= 400) {
            $actual_phaseState = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_phaseState = $this->ci->db->query("SELECT phase_state FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE screen_phase_id =" . getScreeningPhaseId("Title"))->row_array()['phase_state'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_phaseState, $expected_phaseState, $actual_phaseState);
    }

    /*
     * Test 7
     * Action : assignment_screen
     * Description : loads the form for assigning papers for screening
     * Expected nbr of users (Number of users added to the project available for assignation) : 2
     * Expected nbr of papers available for screening : 5
     */
    private function assignmentScreen()
    {
        $action = "assignment_screen";
        $test_name = "Loads the form for assigning papers for screening";

        $test_nbrOfUser = "Number of users added to the project available for assignation";
        $test_nbrOfPapers = "Number of papers available for screening assignation";

        $expected_nbrOfUser = 2;
        $expected_nbrOfPapers = 5;

        //activate assign_papers_on
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET assign_papers_on = 1");

        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_nbrOfUser = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_nbrOfPapers = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //available users in the application
            $users = $this->ci->db->query("SELECT * FROM users")->result_array();
            //users added to the project with correct rights available for assignment
            $projectUserids = $this->ci->db->query("SELECT user_id FROM userproject WHERE project_id = " . getProjectId() . " AND user_role !='Guest'")->result_array();
            //users listed in the assignement form
            $usersListedInTheAssignementForm = [];

            for ($i = 0; $i < count($users); $i++) {
                //check if user is listed in the form
                if (strstr(str_replace(' ', '', $response['content']), "\">" . $users[$i]['user_name'] . "<") != false) {
                    for ($j = 0; $j < count($projectUserids); $j++) {
                        //check if user is added as reviewer in the project
                        if ($users[$i]['user_id'] == $projectUserids[$j]['user_id']) {
                            $userId = $users[$i]['user_id'];
                            $userName = $users[$i]['user_name'];
                            array_push($usersListedInTheAssignementForm, [$userId => $userName]);
                            break;
                        }
                    }
                }
            }

            //papers available in the project
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            //papers listed in the assignement form
            $papersListedInTheAssignementForm = [];

            for ($i = 0; $i < count($papers); $i++) {
                //check if paper is listed in the form
                if (strstr($response['content'], $papers[$i]['title']) != false) {
                    $paperId = $papers[$i]['id'];
                    $title = $papers[$i]['title'];
                    array_push($papersListedInTheAssignementForm, [$paperId => $title]);
                }
            }

            $actual_nbrOfUser = count($usersListedInTheAssignementForm);
            $actual_nbrOfPapers = count($papersListedInTheAssignementForm);
        }

        run_test($this->controller, $action, $test_name, $test_nbrOfUser, $expected_nbrOfUser, $actual_nbrOfUser);
        run_test($this->controller, $action, $test_name, $test_nbrOfPapers, $expected_nbrOfPapers, $actual_nbrOfPapers);
    }

    /*
     * Test 8
     * Action : save_assignment_screen
     * Description : Handle the saving of paper assignments for screening without number_of_users field.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in screening_paper table
     *          - assign_papers operation not inserted in operations table in the project DB
     */
    private function saveAssignmentScreen_withoutNumberOfUsers()
    {
        $action = "save_assignment_screen";
        $test_name = "Handle the saving of paper assignments for screening without number_of_users field";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";

        $user_1 = getAdminUserId();
        $user_2 = getTestUserId();
        $postData = [
            "number_of_users" => "",
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => $user_1,
            "user_2" => $user_2,
            "reviews_per_paper" => 1
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "";

            // Check if all the papers have been assigned and are inserted in the screening_paper table
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening'")->row_array()['row_count'];

            //Check if the assign_papers operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers' AND operation_desc = 'Assign papers for screening' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 9
     * Action : save_assignment_screen
     * Description : Handle the saving of paper assignments for screening without reviews_per_paper field.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in screening_paper table
     *          - assign_papers operation not inserted in operations table in the project DB
     */
    private function saveAssignmentScreen_withoutReviewsPerPaper()
    {
        $action = "save_assignment_screen";
        $test_name = "Handle the saving of paper assignments for screening without reviews_per_papers field";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";

        $user_1 = getAdminUserId();
        $user_2 = getTestUserId();
        $postData = [
            "number_of_users" => 2,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => $user_1,
            "user_2" => $user_2,
            "reviews_per_paper" => ""
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "";

            // Check if all the papers have been assigned and are inserted in the screening_paper table
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening'")->row_array()['row_count'];

            //Check if the assign_papers operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers' AND operation_desc = 'Assign papers for screening' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 10
     * Action : save_assignment_screen
     * Description : Handle the saving of paper assignments for screening when number_of_users < reviews_per_paper.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in screening_paper table
     *          - assign_papers operation not inserted in operations table in the project DB
     */
    private function saveAssignmentScreen_ReviewPerPaper_moreThan_NbrOfUsers()
    {
        $action = "save_assignment_screen";
        $test_name = "Handle the saving of paper assignments for screening when number_of_users < reviews_per_paper";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";

        $user_1 = getAdminUserId();
        $user_2 = getTestUserId();
        $postData = [
            "number_of_users" => 2,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => $user_1,
            "user_2" => $user_2,
            "reviews_per_paper" => 3
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "";

            // Check if all the papers have been assigned and are inserted in the screening_paper table
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening'")->row_array()['row_count'];

            //Check if the assign_papers operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers' AND operation_desc = 'Assign papers for screening' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 11
     * Action : save_assignment_screen
     * Description : Handle the saving of paper assignments for screening with empty users.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in screening_paper table
     *          - assign_papers operation not inserted in operations table in the project DB
     */
    private function saveAssignmentScreen_emptyUsers()
    {
        $action = "save_assignment_screen";
        $test_name = "Handle the saving of paper assignments for screening with empty users";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";

        $postData = [
            "number_of_users" => 1,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "reviews_per_paper" => 1
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "";

            // Check if all the papers have been assigned and are inserted in the screening_paper table
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening'")->row_array()['row_count'];

            //Check if the assign_papers operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers' AND operation_desc = 'Assign papers for screening' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 12
     * Action : save_assignment_screen
     * Description : Test the number of assignment per reviewer with 5 papers to assign to 1 reviewer.
     * Expected Paper assignements : 
     *          - The five assigned papers are added to the project database in the "screening_paper" table, with the reviewer's user ID assigned to the "user_id" field for all five papers.
     *          - assign_papers operation inserted in operations table in the project DB
     */
    private function saveAssignment_5papers_1reviewer()
    {
        $action = "save_assignment_screen";
        $test_name = "Test the number of assignment per reviewer with 5 papers to assign to 1 reviewer";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";

        $userId = getAdminUserId(); //reviewer user ID
        $postData = [
            "number_of_users" => 1,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => $userId,
            "reviews_per_paper" => 1
        ];

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "Not assigned";

            // Check if all the papers have been assigned to the only reviewer
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id = " . $userId)->row_array()['row_count'];

            //Check if the assign_papers operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers' AND operation_desc = 'Assign papers for screening' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 5 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 13
     * Action : save_assignment_screen
     * Description : Test the number of assignment per reviewer with 6 papers to assign to 3 reviewers.
     * Expected Paper assignements : 
     *          - The 6 assigned papers are added to the project database in the "screening_paper" table, the 3 reviewers are assigned 2 papers each.
     *          - assign_papers operation inserted in operations table in the project DB
     */
    private function saveAssignment_6papers_3reviewers()
    {
        $action = "save_assignment_screen";
        $test_name = "Test the number of assignment per reviewer with 6 papers to assign to 3 reviewers";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 6 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/6_bibPapers.bib");

        $postData = [
            "number_of_users" => 3,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => getAdminUserId(),
            "user_2" => getTestUserId(),
            "user_3" => getDemoUserId(),
            "reviews_per_paper" => 1
        ];
        //Select screening phase
        $this->http_client->response($this->controller, "select_screen_phase" . "/" . getScreeningPhaseId("Title"));

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "Not assigned";

            // Check the number of papers assigned to the first user
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id = " . getAdminUserId())->row_array()['row_count'];

            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id = " . getTestUserId())->row_array()['row_count'];

            // Check the number of papers assigned to the third user
            $nbrOfAssignment3 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id = " . getDemoUserId())->row_array()['row_count'];

            //Check if the assign_papers operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers' AND operation_desc = 'Assign papers for screening' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment1 == 2 && $nbrOfAssignment2 == 2 && $nbrOfAssignment3 == 2 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 14
     * Action : save_assignment_screen
     * Description : Test the number of assignment per reviewer with 5 papers to assign to 3 reviewers.
     * Expected Paper assignements : 
     *          - The five assigned papers are added to the project database in the "screening_paper" table, two reviewers are assigned 2 papers and the other reviewer is assigned 1 paper.
     *          - assign_papers operation inserted in operations table in the project DB
     */
    private function saveAssignment_5papers_3reviewers()
    {
        $action = "save_assignment_screen";
        $test_name = "Test the number of assignment per reviewer with 5 papers to assign to 3 reviewers";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");

        $postData = [
            "number_of_users" => 3,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => getAdminUserId(),
            "user_2" => getTestUserId(),
            "user_3" => getDemoUserId(),
            "reviews_per_paper" => 1
        ];
        //Select screening phase
        $this->http_client->response($this->controller, "select_screen_phase" . "/" . getScreeningPhaseId("Title"));

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "Not assigned";

            // Check the number of papers assigned to the first user
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND  user_id = " . getAdminUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND  user_id = " . getTestUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the third user
            $nbrOfAssignment3 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND  user_id = " . getDemoUserId())->row_array()['row_count'];

            //Check if the assign_papers operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers' AND operation_desc = 'Assign papers for screening' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            // Check the difference between each pair of assignments
            $diff1 = abs($nbrOfAssignment1 - $nbrOfAssignment2);
            $diff2 = abs($nbrOfAssignment2 - $nbrOfAssignment3);
            $diff3 = abs($nbrOfAssignment1 - $nbrOfAssignment3);

            if ($diff1 <= 1 && $diff2 <= 1 && $diff3 <= 1 && ($nbrOfAssignment1 + $nbrOfAssignment2 + $nbrOfAssignment3) == 5 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 15
     * Action : save_assignment_screen
     * Description : Test the number of assignment per reviewer with 5 papers to assign to 2 reviewers.
     * Expected Paper assignements : 
     *          - The five assigned papers are added to the project database in the "screening_paper" table, one reviewer is assigned 2 papers and the other reviewer is assigned 3 papers.
     *          - assign_papers operation inserted in operations table in the project DB
     */
    private function saveAssignment_5papers_2reviewers()
    {
        $action = "save_assignment_screen";
        $test_name = "Test the number of assignment per reviewer with 5 papers to assign to 2 reviewers";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");

        $postData = [
            "number_of_users" => 2,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => getAdminUserId(),
            "user_2" => getTestUserId(),
            "reviews_per_paper" => 1
        ];
        //Select screening phase
        $this->http_client->response($this->controller, "select_screen_phase" . "/" . getScreeningPhaseId("Title"));

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "Not assigned";

            // Check the number of papers assigned to the first user
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id = " . getAdminUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id = " . getTestUserId())->row_array()['row_count'];

            //Check if the assign_papers operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers' AND operation_desc = 'Assign papers for screening' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if (abs($nbrOfAssignment1 - $nbrOfAssignment2) == 1 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 16
     * Action : save_screening
     * Description : Save the screening inclusion decision made for a paper with inclusion criteria field.
     * Expected screening details: check the screening decision of the screened paper
     */
    private function saveScreening_InclusionField()
    {
        $action = "save_screening";
        $test_name = "Save the screening inclusion decision made for a paper with inclusion criteria field.";
        $test_aspect_screening = "Screening details";
        $expected_screening = '[{"paper_id":"1","screening_phase":"1","screening_decision":"Included","decision_source":"new_screen","decision_active":"1"}]';

        $screening_paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 1")->row_array();
        $data = ["criteria_ex" => "", "criteria_in" => 1, "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "accepted", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
        $response = $this->http_client->response($this->controller, $action, $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_screening = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_screening = json_encode($this->ci->db->query("SELECT paper_id, screening_phase, screening_decision, decision_source, decision_active FROM relis_dev_correct_" . getProjectShortName() . ".screen_decison")->result_array());
        }

        run_test($this->controller, $action, $test_name, $test_aspect_screening, $expected_screening, $actual_screening);
    }

    /*
     * Test 17
     * Action : save_assignment_screen
     * Description : Test the number of assignment per reviewer with 5 papers to assign to 2 reviewers with 2 reviews per paper.
     * Expected Paper assignements : 
     *          - The five papers are assigned to each user making the number of assignment to 10 in the project database in the "screening_paper" table
     *          - assign_papers operation inserted in operations table in the project DB
     */
    private function saveAssignment_5papers_2reviewers_2reviewsPerPaper()
    {
        $action = "save_assignment_screen";
        $test_name = "Test the number of assignment per reviewer with 5 papers to assign to 2 reviewers with 2 reviews per paper.";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");

        $postData = [
            "number_of_users" => 2,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => getAdminUserId(),
            "user_2" => getTestUserId(),
            "reviews_per_paper" => 2
        ];
        //Select screening phase
        $this->http_client->response($this->controller, "select_screen_phase" . "/" . getScreeningPhaseId("Title"));

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "Not assigned";

            // Check the distinct papers assigned to the first user
            $distinctAssignment1 = $this->ci->db->query("SELECT DISTINCT paper_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id = " . getAdminUserId())->result_array();

            // Check the distinct papers assigned to the second user
            $distinctAssignment2 = $this->ci->db->query("SELECT DISTINCT paper_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id = " . getTestUserId())->result_array();

            //Check if the assign_papers operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers' AND operation_desc = 'Assign papers for screening' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if (count($distinctAssignment1) + count($distinctAssignment2) == 10 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 18
     * Action : screen_paper
     * Description : Handle the display of papers for screening.
     * Expected result: check if correct info is displayed.
     */
    private function displayPapersForScreen()
    {
        $action = "screen_paper";
        $test_name = "Handle the display of papers for screening";
        $test_aspect = "Is correct info displayed?";
        $expected_value = "Yes";
        $actual_value = "No";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $displayedPaper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();

            foreach ($displayedPaper as $paper) {
                if (
                    strstr($response['content'], $paper["bibtexKey"]) != false &&
                    strstr($response['content'], $paper["title"]) != false
                ) {
                    $actual_value = "Yes";
                    break;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 19
     * Action : edit_screen
     * Description : Handle the display of a paper for editing when screening hasn’t been done yet.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function displayPaperNotScreenedYet()
    {
        $action = "edit_screen";
        $test_name = "Handle the display of a paper for editing when screening hasn’t been done yet";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];

        $user_id = getAdminUserId();
        $screening_id = $this->ci->db->query("SELECT screening_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id ='" . $user_id . "' AND screening_status = 'Pending' LIMIT 1")->row_array()['screening_id'];
        $response = $this->http_client->response($this->controller, $action . "/" . $screening_id);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 20
     * Action : save_screening
     * Description : Save the screening exclusion decision made for a paper with no exclusion criteria field.
     * Expected screening details: No screening done
     */
    private function saveScreening_noExcusionField()
    {
        $action = "save_screening";
        $test_name = "Save the screening exclusion decision made for a paper with no exclusion criteria field";

        $test_aspect_screening = "Screening details";

        $screening_paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 1")->row_array();
        $data = ["criteria_ex" => "", "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
        $response = $this->http_client->response($this->controller, $action, $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_screening = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $expected_screening = json_encode(array());
            $actual_screening = json_encode($this->ci->db->query("SELECT paper_id, screening_phase, screening_decision, decision_source, decision_active FROM relis_dev_correct_" . getProjectShortName() . ".screen_decison")->result_array());
        }

        run_test($this->controller, $action, $test_name, $test_aspect_screening, $expected_screening, $actual_screening);
    }

    /*
     * Test 21
     * Action : screen_result
     * Description : Display screening statistics and results while no paper is screened yet.
     * Expected result: check if the results values are corrects
     */
    private function screenResult_noPaperScrenned()
    {
        $action = "screen_result";
        $test_name = "Display screening statistics and results while no paper is screened yet";
        $test_aspect = "result values";
        $expected_value = ['included' => "0", 'excluded' => "0", 'conflict' => "0"];
        $actual_value = "";

        //activate screening_result_on
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET screening_result_on = 1");

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //convert the received http response html into array and retreive all the html tables to analyse the result displayed because the result is shown in an html table
            $contentInArray = html_to_array($response['content'], 'table')['children'][0]['children'];

            foreach ($contentInArray as $table) {
                if (
                    $table['tag'] == 'table' &&
                    $table['children'][0]['children'][0]['children'][0]['content'] == 'Decision' &&
                    $table['children'][0]['children'][0]['children'][1]['content'] == 'Papers' &&
                    $table['children'][0]['children'][0]['children'][2]['content'] == '%'
                ) {
                    foreach ($table['children'][1]['children'] as $tbody) {
                        if (isset($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'])) {
                            if ($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'] == 'Included') {
                                $nbrOfIncluded = $tbody['children'][1]['content'];
                            } elseif ($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'] == 'Excluded') {
                                $nbrOfExcluded = $tbody['children'][1]['content'];
                            } elseif ($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'] == 'In conflict') {
                                $nbrOfConflict = $tbody['children'][1]['content'];
                            }
                        }
                    }
                }
            }
            $actual_value = ['included' => $nbrOfIncluded, 'excluded' => $nbrOfExcluded, 'conflict' => $nbrOfConflict];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, json_encode($expected_value), json_encode($actual_value));
    }

    /*
     * Test 22
     * Action : screen_result
     * Description : Display screening statistics and results while 3 out of 5 papers are screened.
     * Expected result: check if the results values are corrects
     */
    private function screenResult_3PapersScrenned()
    {
        $action = "screen_result";
        $test_name = "Display screening statistics and results while 3 out of 5 papers are screened";
        $test_aspect = "result values";
        $expected_value = ['included' => "3", 'excluded' => "0", 'conflict' => "0"];
        $actual_value = "";

        //perform screening of 3 papers
        screenPapers("title", 3, 2);
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //convert the received http response html into array and retreive all the html tables to analyse the result displayed because the result is shown in an html table
            $contentInArray = html_to_array($response['content'], 'table')['children'][0]['children'];

            foreach ($contentInArray as $table) {
                if (
                    $table['tag'] == 'table' &&
                    $table['children'][0]['children'][0]['children'][0]['content'] == 'Decision' &&
                    $table['children'][0]['children'][0]['children'][1]['content'] == 'Papers' &&
                    $table['children'][0]['children'][0]['children'][2]['content'] == '%'
                ) {
                    foreach ($table['children'][1]['children'] as $tbody) {
                        if (isset($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'])) {
                            if ($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'] == 'Included') {
                                $nbrOfIncluded = $tbody['children'][1]['content'];
                            } elseif ($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'] == 'Excluded') {
                                $nbrOfExcluded = $tbody['children'][1]['content'];
                            } elseif ($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'] == 'In conflict') {
                                $nbrOfConflict = $tbody['children'][1]['content'];
                            }
                        }
                    }
                }
            }
            $actual_value = ['included' => $nbrOfIncluded, 'excluded' => $nbrOfExcluded, 'conflict' => $nbrOfConflict];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, json_encode($expected_value), json_encode($actual_value));
    }

    /*
     * Test 23
     * Action : save_screening
     * Description : Save the screening decision made for all papers.
     * Expected screening details
     */
    private function saveScreeningAllPapers()
    {
        $action = "save_screening";
        $test_name = "Save the screening decision made for all papers";

        $test_aspect_papers = "Screening papers";
        $test_aspect_decision = "Screening decisions";

        $screening_paper1 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 1")->row_array();
        $screening_paper2 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 2")->row_array();
        $screening_paper3 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 3")->row_array();
        $screening_paper4 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 4")->row_array();
        $screening_paper5 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 5")->row_array();
        $screening_paper6 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 6")->row_array();
        $screening_paper7 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 7")->row_array();
        $screening_paper8 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 8")->row_array();
        $screening_paper9 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 9")->row_array();
        $screening_paper10 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND screening_id = 10")->row_array();

        $data1 = ["criteria_ex" => 1, "note" => "", "screening_id" => $screening_paper1['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper1['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper1['paper_id'], "assignment_id" => $screening_paper1['screening_id'], "screen_type" => "simple_screen"];
        $data2 = ["criteria_ex" => 1, "note" => "", "screening_id" => $screening_paper2['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper2['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper2['paper_id'], "assignment_id" => $screening_paper2['screening_id'], "screen_type" => "simple_screen"];
        $data3 = ["criteria_ex" => 2, "note" => "", "screening_id" => $screening_paper3['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper3['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper3['paper_id'], "assignment_id" => $screening_paper3['screening_id'], "screen_type" => "simple_screen"];
        $data4 = ["criteria_ex" => 2, "note" => "", "screening_id" => $screening_paper4['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper4['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper4['paper_id'], "assignment_id" => $screening_paper4['screening_id'], "screen_type" => "simple_screen"];
        $data5 = ["criteria_ex" => "", "note" => "", "screening_id" => $screening_paper5['screening_id'], "decision" => "accepted", "operation_type" => "new", "screening_phase" => $screening_paper5['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper5['paper_id'], "assignment_id" => $screening_paper5['screening_id'], "screen_type" => "simple_screen"];
        $data6 = ["criteria_ex" => "", "note" => "", "screening_id" => $screening_paper6['screening_id'], "decision" => "accepted", "operation_type" => "new", "screening_phase" => $screening_paper6['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper6['paper_id'], "assignment_id" => $screening_paper6['screening_id'], "screen_type" => "simple_screen"];
        $data7 = ["criteria_ex" => 1, "note" => "", "screening_id" => $screening_paper7['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper7['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper7['paper_id'], "assignment_id" => $screening_paper7['screening_id'], "screen_type" => "simple_screen"];
        $data8 = ["criteria_ex" => 2, "note" => "", "screening_id" => $screening_paper8['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper8['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper8['paper_id'], "assignment_id" => $screening_paper8['screening_id'], "screen_type" => "simple_screen"];
        $data9 = ["criteria_ex" => 2, "note" => "", "screening_id" => $screening_paper9['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper9['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper9['paper_id'], "assignment_id" => $screening_paper9['screening_id'], "screen_type" => "simple_screen"];
        $data10 = ["criteria_ex" => "", "note" => "", "screening_id" => $screening_paper10['screening_id'], "decision" => "accepted", "operation_type" => "new", "screening_phase" => $screening_paper10['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper10['paper_id'], "assignment_id" => $screening_paper10['screening_id'], "screen_type" => "simple_screen"];


        $response1 = $this->http_client->response($this->controller, $action, $data1, "POST");
        $response2 = $this->http_client->response($this->controller, $action, $data2, "POST");
        $response3 = $this->http_client->response($this->controller, $action, $data3, "POST");
        $response4 = $this->http_client->response($this->controller, $action, $data4, "POST");
        $response5 = $this->http_client->response($this->controller, $action, $data5, "POST");
        $response6 = $this->http_client->response($this->controller, $action, $data6, "POST");
        $response7 = $this->http_client->response($this->controller, $action, $data7, "POST");
        $response8 = $this->http_client->response($this->controller, $action, $data8, "POST");
        $response9 = $this->http_client->response($this->controller, $action, $data9, "POST");
        $response10 = $this->http_client->response($this->controller, $action, $data10, "POST");

        if ($response1['status_code'] >= 400) {
            $actual_papers = "<span style='color:red'>" . $response1['content'] . "</span>";
            $actual_decision = "<span style='color:red'>" . $response1['content'] . "</span>";
        } else {
            $expected_papers = json_encode([
                ["paper_id" => $screening_paper1['paper_id'], "screening_phase" => $screening_paper1['screening_phase'], "user_id" => $screening_paper1['user_id'], "screening_decision" => "Excluded", "exclusion_criteria" => "1", "screening_status" => "Done", "screening_active" => "1"],
                ["paper_id" => $screening_paper2['paper_id'], "screening_phase" => $screening_paper2['screening_phase'], "user_id" => $screening_paper2['user_id'], "screening_decision" => "Excluded", "exclusion_criteria" => "1", "screening_status" => "Done", "screening_active" => "1"],
                ["paper_id" => $screening_paper3['paper_id'], "screening_phase" => $screening_paper3['screening_phase'], "user_id" => $screening_paper3['user_id'], "screening_decision" => "Excluded", "exclusion_criteria" => "2", "screening_status" => "Done", "screening_active" => "1"],
                ["paper_id" => $screening_paper4['paper_id'], "screening_phase" => $screening_paper4['screening_phase'], "user_id" => $screening_paper4['user_id'], "screening_decision" => "Excluded", "exclusion_criteria" => "2", "screening_status" => "Done", "screening_active" => "1"],
                ["paper_id" => $screening_paper5['paper_id'], "screening_phase" => $screening_paper5['screening_phase'], "user_id" => $screening_paper5['user_id'], "screening_decision" => "Included", "exclusion_criteria" => null, "screening_status" => "Done", "screening_active" => "1"],
                ["paper_id" => $screening_paper6['paper_id'], "screening_phase" => $screening_paper6['screening_phase'], "user_id" => $screening_paper6['user_id'], "screening_decision" => "Included", "exclusion_criteria" => null, "screening_status" => "Done", "screening_active" => "1"],
                ["paper_id" => $screening_paper7['paper_id'], "screening_phase" => $screening_paper7['screening_phase'], "user_id" => $screening_paper7['user_id'], "screening_decision" => "Excluded", "exclusion_criteria" => "1", "screening_status" => "Done", "screening_active" => "1"],
                ["paper_id" => $screening_paper8['paper_id'], "screening_phase" => $screening_paper8['screening_phase'], "user_id" => $screening_paper8['user_id'], "screening_decision" => "Excluded", "exclusion_criteria" => "2", "screening_status" => "Done", "screening_active" => "1"],
                ["paper_id" => $screening_paper9['paper_id'], "screening_phase" => $screening_paper9['screening_phase'], "user_id" => $screening_paper9['user_id'], "screening_decision" => "Excluded", "exclusion_criteria" => "2", "screening_status" => "Done", "screening_active" => "1"],
                ["paper_id" => $screening_paper10['paper_id'], "screening_phase" => $screening_paper10['screening_phase'], "user_id" => $screening_paper10['user_id'], "screening_decision" => "Included", "exclusion_criteria" => null, "screening_status" => "Done", "screening_active" => "1"],
            ]);
            $expected_decision = json_encode([
                ["paper_id" => $screening_paper1['paper_id'], "screening_phase" => $screening_paper1['screening_phase'], "screening_decision" => "Excluded", "decision_source" => "new_screen", "decision_active" => "1"],
                ["paper_id" => $screening_paper3['paper_id'], "screening_phase" => $screening_paper3['screening_phase'], "screening_decision" => "Excluded", "decision_source" => "new_screen", "decision_active" => "1"],
                ["paper_id" => $screening_paper5['paper_id'], "screening_phase" => $screening_paper5['screening_phase'], "screening_decision" => "Included", "decision_source" => "new_screen", "decision_active" => "1"],
                ["paper_id" => $screening_paper7['paper_id'], "screening_phase" => $screening_paper7['screening_phase'], "screening_decision" => "In conflict", "decision_source" => "new_screen", "decision_active" => "1"],
                ["paper_id" => $screening_paper9['paper_id'], "screening_phase" => $screening_paper9['screening_phase'], "screening_decision" => "In conflict", "decision_source" => "new_screen", "decision_active" => "1"]
            ]);

            $actual_papers = json_encode($this->ci->db->query("SELECT paper_id, screening_phase, user_id, screening_decision, exclusion_criteria, screening_status, screening_active FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper")->result_array());
            $actual_decision = json_encode($this->ci->db->query("SELECT paper_id, screening_phase, screening_decision, decision_source, decision_active FROM relis_dev_correct_" . getProjectShortName() . ".screen_decison")->result_array());
        }

        run_test($this->controller, $action, $test_name, $test_aspect_papers, $expected_papers, $actual_papers);
        run_test($this->controller, $action, $test_name, $test_aspect_decision, $expected_decision, $actual_decision);
    }

    /*
     * Test 24
     * Action : edit_screen
     * Description : Handle the display of a paper for editing a screening done.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function displayPaperForScreenEdit()
    {
        $action = "edit_screen";
        $test_name = "Handle the display of a paper for editing a screening done";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];

        $user_id = getAdminUserId();
        $screening_id = $this->ci->db->query("SELECT screening_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening' AND user_id ='" . $user_id . "' AND screening_status = 'Done' LIMIT 1")->row_array()['screening_id'];
        $response = $this->http_client->response($this->controller, $action . "/" . $screening_id);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 25
     * Action : screen_completion
     * Description : calculate and display the completion progress of screening for users.
     * Expected correct completion progress calculation
     */
    private function screenCompletion()
    {
        $action = "screen_completion";
        $test_name = "Calculate and display the completion progress of screening for users";
        $test_completion = "Completion calculation";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_calculation = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //Get papers assigned to admin user
            $papersAssignedToAdminUser = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE user_id = " . getAdminUserId() . " AND assignment_role='Screening'")->result_array();
            //Get papers assigned to test user
            $papersAssignedTotestUser = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE user_id = " . getTestUserId() . " AND assignment_role='Screening'")->result_array();
            $expected_calculation = "admin: " . count($papersAssignedToAdminUser) . "/" . count($papersAssignedToAdminUser) . " - christian: " . count($papersAssignedTotestUser) . "/" . count($papersAssignedTotestUser);
            $actual_calculation = "";

            $content = strtolower(str_replace(' ', '', $response['content']));
            if (strstr($content, "admin:" . count($papersAssignedToAdminUser) . "/" . count($papersAssignedToAdminUser)) != false && strstr($content, "christian:" . count($papersAssignedTotestUser) . "/" . count($papersAssignedTotestUser)) != false) {
                $actual_calculation = "admin: " . count($papersAssignedToAdminUser) . "/" . count($papersAssignedToAdminUser) . " - christian: " . count($papersAssignedTotestUser) . "/" . count($papersAssignedTotestUser);
            }
        }

        run_test($this->controller, $action, $test_name, $test_completion, $expected_calculation, $actual_calculation);
    }

    /*
     * Test 26
     * Action : screen_result
     * Description : Display screening statistics and results with 1 paper Included, 2 excluded and 1 conflict.
     * Expected result: check if the results values are corrects
     */
    private function screenResult_1Included_2excluded_1conflict()
    {
        $action = "screen_result";
        $test_name = "Display screening statistics and results with 1 paper Included, 2 excluded and 1 conflict";
        $test_aspect = "result values";
        $expected_value = ['included' => "1", 'excluded' => "2", 'conflict' => "2"];
        $actual_value = "";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //convert the received http response html into array and retreive all the html tables to analyse the result displayed because the result is shown in an html table
            $contentInArray = html_to_array($response['content'], 'table')['children'][0]['children'];

            foreach ($contentInArray as $table) {
                if (
                    $table['tag'] == 'table' &&
                    $table['children'][0]['children'][0]['children'][0]['content'] == 'Decision' &&
                    $table['children'][0]['children'][0]['children'][1]['content'] == 'Papers' &&
                    $table['children'][0]['children'][0]['children'][2]['content'] == '%'
                ) {
                    foreach ($table['children'][1]['children'] as $tbody) {
                        if (isset($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'])) {
                            if ($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'] == 'Included') {
                                $nbrOfIncluded = $tbody['children'][1]['content'];
                            } elseif ($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'] == 'Excluded') {
                                $nbrOfExcluded = $tbody['children'][1]['content'];
                            } elseif ($tbody['children'][0]['children'][0]['children'][0]['children'][0]['content'] == 'In conflict') {
                                $nbrOfConflict = $tbody['children'][1]['content'];
                            }
                        }
                    }
                }
            }
            $actual_value = ['included' => $nbrOfIncluded, 'excluded' => $nbrOfExcluded, 'conflict' => $nbrOfConflict];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, json_encode($expected_value), json_encode($actual_value));
    }

    /*
     * Test 27
     * Action : save_phase_screen
     * Description : Handle the process of saving a screening phase without Title field.
     * Expected screening phase saved in DB: No phase should be inserted (Number of phases should be same before and after the request)
     */
    private function savePhaseScreen_withoutTitleField()
    {
        $action = "save_phase_screen";
        $test_name = "Handle the process of saving a screening phase without Title field.";

        $test_aspect_screeningPhase = "Screening phase last ID";

        $data = [
            'operation_type' => 'new',
            'table_config' => 'screen_phase',
            'current_operation' => 'add_screen_phase',
            'redirect_after_save' => 'element/entity_list/list_screen_phases',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'screen_phase_id' => '',
            'added_by' => getAdminUserId(),
            'displayed_fields' => 'paper_title,paper_abstract',
            'screen_phase_order' => '',
            'phase_type' => 'Screening',
            'source_paper' => 'Previous phase',
            'source_paper_status' => 'Included',
            'phase_title' => '',
            'description' => '',
            'displayed_fields_vals[]' => 'Abstract',
            'screen_phase_final' => 0,
        ];

        $expected_screeningPhase = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->row_array()['row_count'];
        $response = $this->http_client->response($this->controller, $action, $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_screeningPhase = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_screeningPhase = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_screeningPhase, $expected_screeningPhase, $actual_screeningPhase);
    }

    /*
     * Test 28
     * Action : save_phase_screen
     * Description : Handle the process of saving a screening phase without displayed_fields_vals field.
     * Expected screening phase saved in DB: No phase should be inserted (Number of phases should be same before and after the request)
     */
    private function savePhaseScreen_withoutDisplayed_fields_valsField()
    {
        $action = "save_phase_screen";
        $test_name = "Handle the process of saving a screening phase without Displayed_fields_vals field.";
        $test_aspect_screeningPhase = "Screening phase Last ID";

        $data = [
            'operation_type' => 'new',
            'table_config' => 'screen_phase',
            'current_operation' => 'add_screen_phase',
            'redirect_after_save' => 'element/entity_list/list_screen_phases',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'screen_phase_id' => '',
            'added_by' => getAdminUserId(),
            'displayed_fields' => 'paper_title,paper_abstract',
            'screen_phase_order' => '',
            'phase_type' => 'Screening',
            'source_paper' => 'Previous phase',
            'source_paper_status' => 'Included',
            'phase_title' => 'Abstract',
            'description' => '',
            'displayed_fields_vals[]' => null,
            'screen_phase_final' => 0,
        ];

        $expected_screeningPhase = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->row_array()['row_count'];
        $response = $this->http_client->response($this->controller, $action, $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_screeningPhase = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_screeningPhase = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_screeningPhase, $expected_screeningPhase, $actual_screeningPhase);
    }

    /*
     * Test 29
     * Action : save_phase_screen
     * Description : Handle the process of saving a final screening phase while an final phase already exist.
     * Expected screening phase saved in DB: No phase should be inserted (Number of phases should be same before and after the request)
     */
    private function savePhaseScreen_finalPhaseAlreadyExist()
    {
        $action = "save_phase_screen";
        $test_name = "Handle the process of saving a final screening phase while an final phase already exist";
        $test_aspect_screeningPhase = "Screening phase Last ID";

        $data = [
            'operation_type' => 'new',
            'table_config' => 'screen_phase',
            'current_operation' => 'add_screen_phase',
            'redirect_after_save' => 'element/entity_list/list_screen_phases',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'screen_phase_id' => '',
            'added_by' => getAdminUserId(),
            'displayed_fields' => 'paper_title,paper_abstract',
            'screen_phase_order' => '',
            'phase_type' => 'Screening',
            'source_paper' => 'Previous phase',
            'source_paper_status' => 'Included',
            'phase_title' => 'Abstract',
            'description' => '',
            'displayed_fields_vals[]' => 'Abstract',
            'screen_phase_final' => 1,
        ];

        $expected_screeningPhase = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->row_array()['row_count'];
        $response = $this->http_client->response($this->controller, $action, $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_screeningPhase = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_screeningPhase = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_screeningPhase, $expected_screeningPhase, $actual_screeningPhase);
    }

    /*
     * Test 30
     * Action : save_phase_screen
     * Description : Handle the process of saving a screening phase.
     * Expected screening phase saved in DB.
     */
    private function savePhaseScreen()
    {
        $action = "save_phase_screen";
        $test_name = "Handle the process of saving a screening phase";
        $test_aspect_screeningPhase = "Screening phase saved";

        $data = [
            'operation_type' => 'new',
            'table_config' => 'screen_phase',
            'current_operation' => 'add_screen_phase',
            'redirect_after_save' => 'element/entity_list/list_screen_phases',
            'operation_source' => 'own',
            'child_field' => '',
            'table_config_parent' => '',
            'parent_id' => '',
            'parent_field' => '',
            'parent_table' => '',
            'screen_phase_id' => '',
            'added_by' => getAdminUserId(),
            'displayed_fields' => 'paper_title,paper_abstract',
            'screen_phase_order' => '',
            'phase_type' => 'Screening',
            'source_paper' => 'Previous phase',
            'source_paper_status' => 'Included',
            'phase_title' => 'Abstract',
            'description' => '',
            'displayed_fields_vals[]' => 'Abstract',
            'screen_phase_final' => 0,
        ];

        $response = $this->http_client->response($this->controller, $action, $data, "POST");

        if ($response['status_code'] >= 400) {
            $actual_screeningPhase = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $expected_screeningPhase = json_encode(
                [
                    "phase_title" => "Abstract",
                    "displayed_fields" => "Abstract",
                    "phase_state" => "Closed",
                    "screen_phase_final" => "0",
                    "phase_type" => "Screening",
                    "added_by" => getAdminUserId(),
                    "screen_phase_active" => "1"
                ]
            );

            $actual_screeningPhase = json_encode($this->ci->db->query("SELECT phase_title, displayed_fields, phase_state, screen_phase_final, phase_type, added_by, screen_phase_active FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase ORDER BY screen_phase_id DESC LIMIT 1")->row_array());
        }

        run_test($this->controller, $action, $test_name, $test_aspect_screeningPhase, $expected_screeningPhase, $actual_screeningPhase);
    }

    /*
     * Test 31
     * Action : display_paper_screen
     * Description : Display the details of a paper in the screening process.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function displayPaperScreen()
    {
        $action = "display_paper_screen";
        $test_name = "Display the details of a paper in the screening process";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $paper_id = 1;
        $response = $this->http_client->response($this->controller, $action . "/" . $paper_id);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 32
     * Action : list_screen
     * Description : display a list of screening done.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function listScreen()
    {
        $action = "list_screen";
        $test_name = "Generate a list of screening";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 33
     * Action : validate_screen_set
     * Description : Display page for assigning papers for validation in the screening process.
     * Expected nbr of users (Number of users added to the project available for assignation) : 2
     * Expected nbr of excluded papers during screening phase available for screening validation: 2
     */
    private function validateScreenSet()
    {
        $action = "validate_screen_set";
        $test_name = "Display page for assigning papers for validation in the screening process";

        $test_nbrOfUser = "Number of validator users added to the project available for assignation";
        $test_nbrOfPapers = "Number of excluded papers during screening phase available for screening validation";

        $expected_nbrOfUser = 2;
        $expected_nbrOfPapers = 2;

        //activate screening_validation_on
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET screening_validation_on = 1");

        //Add validator
        addUserToProject(getDemoUserId(), "Validator");

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_nbrOfUser = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_nbrOfPapers = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //available users in the application
            $users = $this->ci->db->query("SELECT * FROM users")->result_array();
            //users added to the project with validator role
            $projectUserids = $this->ci->db->query("SELECT user_id FROM userproject WHERE project_id = " . getProjectId() . " AND (user_role ='Validator' OR user_id = 1)")->result_array();
            //users listed in the assignement form
            $usersListedInTheAssignementForm = [];

            for ($i = 0; $i < count($users); $i++) {
                //check if user is listed in the form
                if (strstr(str_replace(' ', '', $response['content']), "\">" . $users[$i]['user_name'] . "<") != false) {
                    for ($j = 0; $j < count($projectUserids); $j++) {
                        //check if user is added as validator in the project
                        if ($users[$i]['user_id'] == $projectUserids[$j]['user_id']) {
                            $userId = $users[$i]['user_id'];
                            $userName = $users[$i]['user_name'];
                            array_push($usersListedInTheAssignementForm, [$userId => $userName]);
                            break;
                        }
                    }
                }
            }

            //papers available in the project
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            //papers listed in the assignement form
            $papersListedInTheAssignementForm = [];

            for ($i = 0; $i < count($papers); $i++) {
                //check if paper is listed in the form
                if (strstr($response['content'], $papers[$i]['title']) != false) {
                    $paperId = $papers[$i]['id'];
                    $title = $papers[$i]['title'];
                    array_push($papersListedInTheAssignementForm, [$paperId => $title]);
                }
            }

            $actual_nbrOfUser = count($usersListedInTheAssignementForm);
            $actual_nbrOfPapers = count($papersListedInTheAssignementForm);
        }

        run_test($this->controller, $action, $test_name, $test_nbrOfUser, $expected_nbrOfUser, $actual_nbrOfUser);
        run_test($this->controller, $action, $test_name, $test_nbrOfPapers, $expected_nbrOfPapers, $actual_nbrOfPapers);
    }

    /*
     * Test 34
     * Action : save_assign_screen_validation
     * Description : Handle the saving of paper assignments for screening validation with empty percentage in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in screening_paper table
     *          - assign_papers_valida operation not inserted in operations table in the project DB 
     */
    private function saveAssignmentValidation_emptyPercentage()
    {
        $action = "save_assign_screen_validation";
        $test_name = "Handle the saving of paper assignments for screening validation with empty percentage in POST request";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";

        $postData = [
            "number_of_users" => 1,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => 1,
            "paper_source_status" => "Excluded",
            "user_1" => getAdminUserId(),
            "percentage" => "",
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "";

            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'validation'")->row_array()['row_count'];

            //Check if the assign_papers_valida operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers_valida' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 35
     * Action : save_assign_screen_validation
     * Description : Handle the saving of paper assignments for screening validation with invalid percentage (>100) in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in screening_paper table
     *          - assign_papers_valida operation not inserted in operations table in the project DB 
     */
    private function saveAssignmentValidation_invalidPercentage()
    {
        $action = "save_assign_screen_validation";
        $test_name = "Handle the saving of paper assignments for screening validation with percentage > 100 in POST request";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";

        $postData = [
            "number_of_users" => 1,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => 1,
            "paper_source_status" => "Excluded",
            "user_1" => getAdminUserId(),
            "percentage" => 130,
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "";

            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'validation'")->row_array()['row_count'];

            //Check if the assign_papers_valida operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers_valida' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 36
     * Action : save_assign_screen_validation
     * Description : Handle the saving of paper assignments for screening validation with empty users in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in screening_paper table
     *          - assign_papers_valida operation not inserted in operations table in the project DB 
     */
    private function saveAssignmentValidation_emptyUsers()
    {
        $action = "save_assign_screen_validation";
        $test_name = "Handle the saving of paper assignments for QA validation with empty users in POST request";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";

        $postData = [
            "number_of_users" => 0,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => 1,
            "paper_source_status" => "Excluded",
            "percentage" => 100,
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "";

            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'validation'")->row_array()['row_count'];

            //Check if the assign_papers_valida operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers_valida' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 37
     * Action : save_assign_screen_validation
     * Description : save the assignments of papers for screening validation with 50% of paper assignement.
     * Expected Paper assignements : 
     *          - 50% of the papers are added in the project DB in screening_paper table
     *          - assign_papers_valida operation inserted in operations table in the project DB
     */
    private function saveAssignmentValidation_50percent()
    {
        $action = "save_assign_screen_validation";
        $test_name = "Save the assignments of papers for screening validation with 50% of paper assignement";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";

        $postData = [
            "number_of_users" => 1,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => 1,
            "paper_source_status" => "Excluded",
            "user_1" => getAdminUserId(),
            "percentage" => 50,
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "Not assigned";

            // Check if 50% of the papers have been assigned (we have 2 papers excluded in the screening phase to be assigned, so 50% of 2 = 1)
            $excludedPaperIds = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE screening_status='Excluded'")->result_array();
            $excludedPaperIdList = array();
            foreach ($excludedPaperIds as $paperId) {
                $excludedPaperIdList[] = $paperId['id'];
            }
            $excludedPaperIdList = implode(',', $excludedPaperIdList);
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE paper_id IN (" . $excludedPaperIdList . ") AND assignment_role = 'validation'")->row_array()['row_count'];

            //Check if the assign_papers_valida operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers_valida' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 1 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 38
     * Action : save_assign_screen_validation
     * Description : save the assignments of papers for screening validation to whom have not screened them.
     * Expected Paper assignements :
     *          - The numbers of papers each user are assigned all between 1 and 2. (5 papers for 3 user)
     *          - assign_papers_valida operation inserted in operations table in the project DB
     */
    private function saveAssignmentValidation_assignToNotScreenedUser()
    {
        $action = "save_assign_screen_validation";
        $test_name = "Save the assignments of papers for screening validation to whom have not screened them";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";

        //activate assign_to_non_screened_validator_on
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET assign_to_non_screened_validator_on = 1");

        //initialise the Database
        $this->TestInitialize();
        //select screening phase as active phase
        $screenPhaseID = getScreeningPhaseId("Title");
        $this->http_client->response("screening", "select_screen_phase" . "/" . $screenPhaseID);

        $AdminUserId = getAdminUserId();
        $TestUserId = getTestUserId();
        $DemoUserId = getDemoUserId();

        //add 1 paper & screening paper for admin and test user
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/1_bibPaper.bib");
        $data = [
            "number_of_users" => 2,
            "screening_phase" => $screenPhaseID,
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => $AdminUserId,
            "user_2" => $TestUserId,
            "reviews_per_paper" => 2
        ];
        save_assignment_screen($data);
        for ($i = 1; $i <= 2; $i++) {
            $screening_paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_decision IS NULL AND assignment_role = 'Screening'")->row_array();
            $data = ["criteria_ex" => 1, "criteria_in" => "", "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
            save_screening($data);
        }

        //add 2 papers & screening papers for admin user
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/2_bibPapers.bib");
        $data = [
            "number_of_users" => 1,
            "screening_phase" => $screenPhaseID,
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => $AdminUserId,
            "reviews_per_paper" => 1
        ];
        save_assignment_screen($data);
        for ($i = 1; $i <= 2; $i++) {
            $screening_paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_decision IS NULL AND assignment_role = 'Screening'")->row_array();
            $data = ["criteria_ex" => 1, "criteria_in" => "", "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
            save_screening($data);
        }

        //add 1 paper & screening paper for test user
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/1_bibPaper_b.bib");
        $data = [
            "number_of_users" => 1,
            "screening_phase" => $screenPhaseID,
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => $TestUserId,
            "reviews_per_paper" => 1
        ];
        save_assignment_screen($data);
        $screening_paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_decision IS NULL AND assignment_role = 'Screening'")->row_array();
        $data = ["criteria_ex" => 1, "criteria_in" => "", "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
        save_screening($data);

        //add 1 paper & screening paper for demo user
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/1_bibPaper_c.bib");
        $data = [
            "number_of_users" => 1,
            "screening_phase" => $screenPhaseID,
            "papers_sources" => "all",
            "paper_source_status" => "all",
            "user_1" => $DemoUserId,
            "reviews_per_paper" => 1
        ];
        save_assignment_screen($data);
        $screening_paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_decision IS NULL AND assignment_role = 'Screening'")->row_array();
        $data = ["criteria_ex" => 1, "criteria_in" => "", "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
        save_screening($data);

        $postData = [
            "number_of_users" => 3,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => 1,
            "paper_source_status" => "Excluded",
            "user_1" => getAdminUserId(),
            "user_2" => getTestUserId(),
            "user_3" => getDemoUserId(),
            "percentage" => 100,
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "Not assigned";

            // Check if all the papers have been assigned as expected
            $excludedPaperIds = $this->ci->db->query("SELECT paper_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_decision='Excluded'")->result_array();
            $excludedPaperIdList = array();
            foreach ($excludedPaperIds as $paperId) {
                $excludedPaperIdList[] = $paperId['paper_id'];
            }
            $excludedPaperIdList = array_unique($excludedPaperIdList);
            $excludedPaperIdList = implode(',', $excludedPaperIdList);

            $users = [getAdminUserId(), getTestUserId(), getDemoUserId()];

            $flag = 0;//Check if the number is as expected
            foreach ($users as $user) {
                $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE user_id = $user AND paper_id IN (" . $excludedPaperIdList . ") AND assignment_role = 'validation'")->row_array()['row_count'];
                if ($nbrOfAssignment == 1 or $nbrOfAssignment == 2){
                    $flag = 1;
                }
                else{
                    $flag = 0;
                    break;
                }
            }

            //Check if the assign_papers_valida operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers_valida' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($flag == 1 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }

            //deactivate assign_to_non_screened_validator_on
            $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET assign_to_non_screened_validator_on = 0");

            // Remove screenings
            $screening_ids = $this->ci->db->query("SELECT screening_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE user_id IN ('" . $TestUserId . "', '" . $DemoUserId . "', '" . $AdminUserId . "') AND assignment_role='Screening'")->result_array();
            foreach ($screening_ids as $id) {
                remove_screening($id['screening_id']);
            }

            // Remove screening validations
            $validation_ids = $this->ci->db->query("SELECT screening_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE user_id IN ('" . $TestUserId . "', '" . $DemoUserId . "', '" . $AdminUserId . "') AND assignment_role='Validation'")->result_array();
            foreach ($validation_ids as $id) {
                remove_screening_validation($id['screening_id']);
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 39
     * Action : save_assign_screen_validation
     * Description : save the assignments of papers for screening validation with 100% of paper assignement.
     * Expected Paper assignements : 
     *          - 100% of the papers are added in the project DB in screening_paper table
     *          - assign_papers_valida operation inserted in operations table in the project DB
     */
    private function saveAssignmentValidation_100percent()
    {
        $action = "save_assign_screen_validation";
        $test_name = "Save the assignments of papers for screening validation with 100% of paper assignement";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //select screening phase as active phase
        $this->http_client->response("screening", "select_screen_phase" . "/" . getScreeningPhaseId("Title"));
        //screening papers by including 3 papers and excluding 2 papers
        assignPapers_and_performScreening([getAdminUserId()], "Title", $done = 5, $include = 3);

        $postData = [
            "number_of_users" => 1,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => 1,
            "paper_source_status" => "Excluded",
            "user_1" => getAdminUserId(),
            "percentage" => 100,
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "Not assigned";

            // Check if all the papers have been assigned
            $excludedPaperIds = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE screening_status='Excluded'")->result_array();
            $excludedPaperIdList = array();
            foreach ($excludedPaperIds as $paperId) {
                $excludedPaperIdList[] = $paperId['id'];
            }
            $excludedPaperIdList = implode(',', $excludedPaperIdList);
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE paper_id IN (" . $excludedPaperIdList . ") AND assignment_role = 'validation'")->row_array()['row_count'];

            //Check if the assign_papers_valida operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers_valida' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if (count($excludedPaperIds) == $nbrOfAssignment && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 40
     * Action : save_assign_screen_validation
     * Description : save the assignments of papers for screening validation by exclusion criteria EC1 with 100%.
     * Expected Paper assignements :
     *          - All papers excluded by criteria EC1 are added in the project DB in screening_paper table
     *          - assign_papers_valida operation inserted in operations table in the project DB
     */
    private function saveAssignmentValidation_by1criteria()
    {
        $action = "save_assign_screen_validation";
        $test_name = "Save the assignments of papers for screening validation by exclusion criteria EC1 with 100%";

        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //select screening phase as active phase
        $this->http_client->response("screening", "select_screen_phase" . "/" . getScreeningPhaseId("Title"));
        //screening papers by excluding 5 papers with first 3 excluded by EC1
        assignPapers_and_performScreening([getAdminUserId()], "Title", $done = 5, $include = 0, $criteria = 1);

        $postData = [
            "number_of_users" => 1,
            "screening_phase" => getScreeningPhaseId("Title"),
            "papers_sources" => 1,
            "paper_source_status" => "Excluded",
            "user_1" => getAdminUserId(),
            "validation_by_exclusion_criteria_toggle" => "on",
            "choose_exclusion_criteria" => array('0' => 'EC1: Too short'),
            "percentage" => 100,
        ];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_assignement = "Not assigned";

            // Check if all the papers have been assigned
            $excludedPaperIds = $this->ci->db->query("SELECT paper_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_decision='Excluded' AND exclusion_criteria = '1'")->result_array();
            $excludedPaperIdList = array();
            foreach ($excludedPaperIds as $paperId) {
                $excludedPaperIdList[] = $paperId['paper_id'];
            }
            $excludedPaperIdList = implode(',', $excludedPaperIdList);
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE paper_id IN (" . $excludedPaperIdList . ") AND assignment_role = 'validation'")->row_array()['row_count'];

            //Check if the assign_papers_valida operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_papers_valida' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if (count($excludedPaperIds) == $nbrOfAssignment && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 41
     * Action : screen_paper_validation
     * Description : handle the display of a paper for screening validation
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function screenPaperValidation()
    {
        $action = "screen_paper_validation";
        $test_name = "handle the display of a paper for screening";
        $test_aspect = "Http response code";

        //activate screening_validation_on
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET screening_validation_on = 1");

        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 42
     * Action : screen_completion
     * Description : calculate and display the completion progress of screening validation for users.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function screenValidationCompletion()
    {
        $action = "screen_completion";
        $test_name = "calculate and display the completion progress of screening validation for users";
        $test_aspect = "Http response code";

        //activate screening_result_on
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET screening_result_on = 1");

        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/validate");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 43
     * Action : screen_validation_result
     * Description : Display screening validation statistics and results.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function screenValidationResult()
    {
        $action = "screen_validation_result";
        $test_name = "Display screening validation statistics and results";
        $test_aspect = "Http response code";

        //activate screening_validation_on
        $this->ci->db->query("UPDATE relis_dev_correct_" . getProjectShortName() . ".config SET screening_validation_on = 1");

        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 44
     * Action : remove_screening
     * Description : Remove a screening entry from the database.
     * Expected update in DB
     */
    private function removeScreening()
    {
        $action = "remove_screening";
        $test_name = "Remove a screening entry from the database";
        $test_aspect_screening_update = "Is screening removed?";
        $expected_screening_update = "Yes";
        $actual_screening_update = "No";

        $user_id = getAdminUserId();
        $screening_id = $this->ci->db->query("SELECT screening_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE user_id ='" . $user_id . "' AND assignment_role='Screening' LIMIT 1")->row_array()['screening_id'];
        $response = $this->http_client->response($this->controller, $action . "/" . $screening_id);

        if ($response['status_code'] >= 400) {
            $actual_screening_update = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $screening = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_id =" . $screening_id)->row_array();
            if ($screening['screening_status'] == 'Pending' && $screening['screening_active'] == 0) {
                $actual_screening_update = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect_screening_update, $expected_screening_update, $actual_screening_update);
    }

    /*
     * Test 45
     * Action : remove_screening_validation
     * Description : Handle the removal of screening validation entries from the database.
     * Expected update in DB
     */
    private function removeScreeningValidation()
    {
        $action = "remove_screening_validation";
        $test_name = "Handle the removal of screening validation entry from the database";
        $test_aspect_screening_update = "Is screening removed?";
        $expected_screening_update = "Yes";
        $actual_screening_update = "No";

        $user_id = getAdminUserId();
        $screening_id = $this->ci->db->query("SELECT screening_id FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE user_id ='" . $user_id . "' AND assignment_role='Validation' LIMIT 1")->row_array()['screening_id'];
        $response = $this->http_client->response($this->controller, $action . "/" . $screening_id);

        if ($response['status_code'] >= 400) {
            $actual_screening_update = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $screening = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_id =" . $screening_id)->row_array();
            if ($screening['screening_status'] == 'Pending' && $screening['screening_active'] == 0) {
                $actual_screening_update = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect_screening_update, $expected_screening_update, $actual_screening_update);
    }

    /*
     * Test 46
     * Action : screening
     * Description : Display screening home page with 4 papers screened.
     * Expected result: check if displayed data is correct
     */
    private function screening_4papersScreened()
    {
        $action = "screening";
        $test_name = "Display screening home page with 4 papers screened";
        $test_aspect = "is data correct";
        $expected_value = "Correct";
        $actual_value = "Not Correct";
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (
                strstr(str_replace(' ', '', $response['content']), '<divclass="count"style="color:black;">4</div>') != false &&
                strstr(str_replace(' ', '', $response['content']), '<divclass="countgreen">4</div>') != false &&
                strstr(str_replace(' ', '', $response['content']), '<divclass="count">0</div>') != false &&
                strstr(str_replace(' ', '', $response['content']), '<divclass="countred">0</div>') != false
            ) {
                $actual_value = "Correct";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 47
     * Action : screening
     * Description : Display screening home page with 0 paper screened.
     * Expected result: check if displayed data is correct
     */
    private function screening_0paperScreened()
    {
        $action = "screening";
        $test_name = "Display screening home page with 0 paper screened";
        $test_aspect = "is data correct";
        $expected_value = "Correct";
        $actual_value = "Not Correct";
        
        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //select screening phase as active phase
        $this->http_client->response("screening", "select_screen_phase" . "/" . getScreeningPhaseId("Title"));
        //screening papers by including 3 papers and excluding 2 papers
        assignPapers_and_performScreening([getAdminUserId()], "Title", $done = 0);
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (
                strstr(str_replace(' ', '', $response['content']), '<divclass="count"style="color:black;">5</div>') != false &&
                strstr(str_replace(' ', '', $response['content']), '<divclass="countgreen">0</div>') != false &&
                strstr(str_replace(' ', '', $response['content']), '<divclass="count">5</div>') != false &&
                strstr(str_replace(' ', '', $response['content']), '<divclass="countred">0</div>') != false
            ) {
                $actual_value = "Correct";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }
}