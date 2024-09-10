<?php

// TEST DATA_EXTRACTION CONTROLLER
class Data_extractionUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "data_extraction";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->activateClassification();
        $this->deActivateClassification();
        $this->displayAssignementForm();
        $this->saveAssignmentclass_emptyUsers();
        $this->saveAssignment_3of5papers_2reviewers();
        $this->saveAssignment_5papers_1reviewer();
        $this->saveAssignment_6papers_3reviewers();
        $this->saveAssignment_5papers_3reviewers();
        $this->saveAssignment_5papers_2reviewers();
        $this->displayPaper();
        $this->class_completion_info();
        $this->listAllClassifications();
        $this->listAllClassifications_page7();
        $this->listAllClassifications_noDynamicTable();
        $this->listAllClassifications_dt();
        $this->listAllClassifications_page7_dt();
        $this->searchClassification_3matches();
        $this->searchClassification_1match();
        $this->searchClassification_0Match();
        $this->editClassification_normal();
        $this->editClassification_modal();
        $this->newClassification_normal();
        $this->newClassification_modal();
        $this->displayValidationAssignementForm();
        $this->saveAssignmentValidation_noPercentage();
        $this->saveAssignmentValidation_wrongPercentage();
        $this->saveAssignmentValidation_emptyUsers();
        $this->saveAssignmentClassValidation_30percent();
        $this->saveAssignmentClassValidation_100percent();
        $this->displayPaperValidation();
        $this->classValidate_setCcorrect();
        $this->classValidate_setNotCcorrect();
        $this->classValidation_completion_info();
        $this->removeClassification();
        $this->removeClassification_redirectToPaper();
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
        //add users to test Project
        addUserToProject(getAdminUserId(), "Reviewer");
        addUserToProject(getTestUserId(), "Reviewer");
    }

    /* 
     * Test 1
     * Action : activate_classification
     * Description : turn the classification feature ON
     * Expected database update: The value of "classification_on" field in the "config" table in the project Db shoud become "1"
     */
    private function activateClassification()
    {
        $action = "activate_classification";
        $test_name = "Turn the classification feature ON";
        $test_dbUpdate = "Is classification ON (1 = yes, 0 = no)";
        $expected_DbUpdate = '1';

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_DbUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_DbUpdate = $this->ci->db->query("SELECT classification_on FROM relis_dev_correct_" . getProjectShortName() . ".config WHERE config_id = 1")->row_array()['classification_on'];
        }

        run_test($this->controller, $action, $test_name, $test_dbUpdate, $expected_DbUpdate, $actual_DbUpdate);
    }

    /*
     * Test 2
     * Action : activate_classification
     * Description : turn the classification feature OFF
     * Expected database update: The value of "classification_on" field in the "config" table in the project Db shoud become "0"
     */
    private function deActivateClassification()
    {
        $action = "activate_classification";
        $test_name = "Turn the classification feature OFF";
        $test_dbUpdate = "Is classification ON (1 = yes, 0 = no)";
        $expected_DbUpdate = '0';

        $response = $this->http_client->response($this->controller, $action . "/0");

        if ($response['status_code'] >= 400) {
            $actual_DbUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_DbUpdate = $this->ci->db->query("SELECT classification_on FROM relis_dev_correct_" . getProjectShortName() . ".config WHERE config_id = 1")->row_array()['classification_on'];
        }

        run_test($this->controller, $action, $test_name, $test_dbUpdate, $expected_DbUpdate, $actual_DbUpdate);
    }

    /*
     * Test 3
     * Action : class_assignment_set
     * Description : load the view to display the classification assignment form
     * Expected Number of users available for classification
     */
    private function displayAssignementForm()
    {
        $action = "class_assignment_set";
        $test_name = "Load the view to display the classification assignment form";
        $test_nbrOfUser = "Number of users added to the project available for classification";
        $expected_nbrOfUser = 2;

        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_nbrOfUser = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //available users in the application
            $users = $this->ci->db->query("SELECT * FROM users")->result_array();
            //users added to the project with correct rights available for assignment
            $projectUserids = $this->ci->db->query("SELECT user_id FROM userproject WHERE project_id = " . getProjectId() . " AND user_role !='Guest'")->result_array();
            //users listed in the assignement form
            $usersListedInTheAssignementForm = [];

            for ($i = 0; $i < count($users); $i++) {
                //check if user is listed in the form
                if (strstr($response['content'], "\">" . $users[$i]['user_name'] . "<") != false) {
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

            $actual_nbrOfUser = count($usersListedInTheAssignementForm);
        }

        run_test($this->controller, $action, $test_name, $test_nbrOfUser, $expected_nbrOfUser, $actual_nbrOfUser);
    }

    /*
     * Test 4
     * Action : class_assignment_save
     * Description : Handle the saving of paper assignments for classification with empty users in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in assigned table
     *          - assign_class operation not inserted in operations table in the project DB
     */
    private function saveAssignmentclass_emptyUsers()
    {
        $action = "class_assignment_save";
        $test_name = "Handle the saving of paper assignments for classification with empty users in POST request";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";
        $actual_assignement = "";

        $postData = ["number_of_users" => 0, "percentage" => 100];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assignment_type='Classification'")->row_array()['row_count'];

            //Check if the assign_class operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class' AND operation_desc = 'Assign  papers for classification' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 5
     * Action : class_assignment_save
     * Description : Test the number of assignment per reviewer with 3 out of 5 papers to assign to 2 reviewers.
     * Expected Paper assignements :
     *          - The three assigned papers are added to the project database in the "assigned" table, one reviewer is assigned 1 papers and the other reviewer is assigned 2 papers.
     *          - assign_class operation inserted in operations table in the project DB
     */
    private function saveAssignment_3of5papers_2reviewers()
    {
        $action = "class_assignment_save";
        $test_name = "Handle the saving of paper assignments for classification with 3 out of 5 papers to assign to 2 reviewers";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');

        $postData = ["number_of_users" => 2, "percentage" => 100, "user_1" => getAdminUserId(), "user_2" => getTestUserId(), "assign_all_paper_checkbox" => "off", "number_of_papers_to_assign" => 3];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check the number of papers assigned to the first user
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getAdminUserId())->row_array()['row_count'];

            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getTestUserId())->row_array()['row_count'];

            //Check if the assign_class operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class' AND operation_desc = 'Assign  papers for classification' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if (abs($nbrOfAssignment1 - $nbrOfAssignment2) == 1 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        // Cleanup: Remove the papers assignment and operation records
        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id IN (" . getAdminUserId() . ", " . getTestUserId() . ")");
        $this->ci->db->query("DELETE FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class' AND operation_desc = 'Assign papers for classification' AND operation_state = 'Active' AND operation_active = 1");

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 6
     * Action : class_assignment_save
     * Description : Test the number of assignment per reviewer with 5 papers to assign to 1 reviewer.
     * Expected Paper assignements : 
     *          - The five assigned papers are added to the project database in the "assigned" table with the reviewer's user ID assigned to the "assigned_user_id" field for all five papers.
     *          - assign_class operation inserted in operations table in the project DB
     */
    private function saveAssignment_5papers_1reviewer()
    {
        $action = "class_assignment_save";
        $test_name = "Handle the saving of paper assignments for classification with 5 papers to assign to 1 reviewer";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        $userId = getAdminUserId(); //reviewer user ID
        $postData = ["number_of_users" => 1, "percentage" => 100, "user_1" => $userId];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned to the only reviewer
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . $userId)->row_array()['row_count'];

            //Check if the assign_class operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class' AND operation_desc = 'Assign  papers for classification' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 5 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 7
     * Action : class_assignment_save
     * Description : Test the number of assignment per reviewer with 6 papers to assign to 3 reviewers.
     * Expected Paper assignements : 
     *          - The 6 assigned papers are added to the project database in the "assigned" table, the 3 reviewers are assigned 2 papers each.
     *          - assign_class operation inserted in operations table in the project DB
     */
    private function saveAssignment_6papers_3reviewers()
    {
        $action = "class_assignment_save";
        $test_name = "Handle the saving of paper assignments for classification with 6 papers to assign to 3 reviewers";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 6 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/6_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');

        $postData = ["number_of_users" => 3, "percentage" => 100, "user_1" => getAdminUserId(), "user_2" => getTestUserId(), "user_3" => getDemoUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check the number of papers assigned to the first user
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getAdminUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getTestUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the third user
            $nbrOfAssignment3 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getDemoUserId())->row_array()['row_count'];

            //Check if the assign_class operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class' AND operation_desc = 'Assign  papers for classification' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment1 == 2 && $nbrOfAssignment2 == 2 && $nbrOfAssignment3 == 2 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 8
     * Action : class_assignment_save
     * Description : Test the number of assignment per reviewer with 5 papers to assign to 3 reviewers.
     * Expected Paper assignements : 
     *          - The five assigned papers are added to the project database in the "assigned" table, two reviewers are assigned 2 papers and the other reviewer is assigned 1 paper.
     *          - assign_class operation inserted in operations table in the project DB
     */
    private function saveAssignment_5papers_3reviewers()
    {
        $action = "class_assignment_save";
        $test_name = "Handle the saving of paper assignments for classification with 5 papers to assign to 3 reviewers";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');

        $postData = ["number_of_users" => 3, "percentage" => 100, "user_1" => getAdminUserId(), "user_2" => getTestUserId(), "user_3" => getDemoUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check the number of papers assigned to the first user
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getAdminUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getTestUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the third user
            $nbrOfAssignment3 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getDemoUserId())->row_array()['row_count'];

            //Check if the assign_class operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class' AND operation_desc = 'Assign  papers for classification' AND operation_state = 'Active' AND operation_active = 1")->row_array();

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
     * Test 9
     * Action : class_assignment_save
     * Description : Test the number of assignment per reviewer with 5 papers to assign to 2 reviewers.
     * Expected Paper assignements : 
     *          - The five assigned papers are added to the project database in the "assigned" table, one reviewer is assigned 2 papers and the other reviewer is assigned 3 papers.
     *          - assign_class operation inserted in operations table in the project DB
     */
    private function saveAssignment_5papers_2reviewers()
    {
        $action = "class_assignment_save";
        $test_name = "Handle the saving of paper assignments for classification with 5 papers to assign to 2 reviewers";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');

        $postData = ["number_of_users" => 2, "percentage" => 100, "user_1" => getAdminUserId(), "user_2" => getTestUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check the number of papers assigned to the first user
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getAdminUserId())->row_array()['row_count'];

            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getTestUserId())->row_array()['row_count'];

            //Check if the assign_class operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class' AND operation_desc = 'Assign  papers for classification' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if (abs($nbrOfAssignment1 - $nbrOfAssignment2) == 1 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 10
     * Action : display_paper
     * Description : Display paper details to perform classification
     * Expected displayed paper
     */
    private function displayPaper()
    {
        $action = "display_paper";
        $test_name = "Display paper details to perform classification";
        $test_displayedPaper = "Displayed paper";
        $actual_displayedPaper = "";

        //get random paper that is available for classification
        $paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE screening_status = 'Included' ORDER BY RAND() LIMIT 1")->row_array();
        $expected_displayedPaper = $paper['title'];

        $response = $this->http_client->response($this->controller, $action . "/" . $paper['id']);

        if ($response['status_code'] >= 400) {
            $actual_displayedPaper = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //papers available in the project
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();

            for ($i = 0; $i < count($papers); $i++) {
                //check wich paper is displayed
                if (strstr($response['content'], $papers[$i]['title']) != false) {
                    $actual_displayedPaper = $paper['title'];
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_displayedPaper, $expected_displayedPaper, $actual_displayedPaper);
    }

    /*
     * Test 11
     * Action : class_completion
     * Description : retrieves completion information for classification
     * Expected completion calculation
     */
    private function class_completion_info()
    {
        $action = "class_completion";
        $test_name = "Retrieves completion information for classification";
        $test_completion = "Completion calculation";

        //perform classification
        performClassification();

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_calculation = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //Get papers assigned to admin user
            $papersAssignedToAdminUser = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getAdminUserId() . " AND assignment_type='classification'")->result_array();
            //Get papers assigned to test user
            $papersAssignedTotestUser = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getTestUserId() . " AND assignment_type='classification'")->result_array();
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
     * Test 12
     * Action : list_classification
     * Description : display the list of all classifications done.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function listAllClassifications()
    {
        $action = "list_classification";
        $test_name = "Display the list of all classifications done";
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
     * Test 13
     * Action : list_classification
     * Description : display the list of all classifications done in page 7.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function listAllClassifications_page7()
    {
        $action = "list_classification";
        $test_name = "Display the list of all classifications done page 7";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/normal/_/7");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 14
     * Action : list_classification
     * Description : display the list of all classifications done with no dynamic table.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function listAllClassifications_noDynamicTable()
    {
        $action = "list_classification";
        $test_name = "Display the list of all classifications done with no dynamic table";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/normal/_/0/0");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 15
     * Action : list_classification_dt
     * Description : display the list of all classifications done using Java script datatable.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function listAllClassifications_dt()
    {
        $action = "list_classification_dt";
        $test_name = "Display the list of all classifications done using Java script datatable";
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
     * Test 16
     * Action : list_classification_dt
     * Description : display the list of all classifications done in page 7 using Java script datatable.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function listAllClassifications_page7_dt()
    {
        $action = "list_classification_dt";
        $test_name = "Display the list of all classifications done page 7 using Java script datatable";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/normal/_/7");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 17
     * Action : search_classification
     * Description : Search and display classification info with a string that matches 5 papers.
     * Expected displayed papers and number of displayed papers: 5
     */
    private function searchClassification_3matches()
    {
        $action = "search_classification";
        $test_name = "Search and display classification info with a string that match 5 papers";
        $test_NbrOfPapers = "Nbr of displayed papers";
        $expected_NbrOfPapers = 5;
        $actual_NbrOfPapers = 0;

        //search papers with year = 2017
        $response = $this->http_client->response($this->controller, $action . "/year/2017");

        if ($response['status_code'] >= 400) {
            $actual_NbrOfPapers = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $getAllpapersWhereYearIs2017 = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".classification WHERE year = 2017")->result_array();

            //check if paper is listed in the search result
            foreach ($getAllpapersWhereYearIs2017 as $paper) {
                $title = $this->ci->db->query("SELECT title FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id = " . $paper['class_paper_id'])->row_array()['title'];

                if (strstr($response['content'], $title) != false) {
                    $actual_NbrOfPapers++;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_NbrOfPapers, $expected_NbrOfPapers, $actual_NbrOfPapers);
    }

    /*
     * Test 18
     * Action : search_classification
     * Description : Search and display classification info with a string that matches 1 paper.
     * Expected displayed paper(s) and number of displayed papers: 1
     */
    private function searchClassification_1match()
    {
        $action = "search_classification";
        $test_name = "Search and display classification info with a string that matches 1 paper";
        $test_NbrOfPapers = "Nbr of displayed papers";
        $expected_NbrOfPapers = 1;
        $actual_NbrOfPapers = 0;

        //search classification paper with class_paper_id = 3
        $response = $this->http_client->response($this->controller, $action . "/class_paper_id/3");

        if ($response['status_code'] >= 400) {
            $actual_NbrOfPapers = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $paperTitles = $this->ci->db->query("SELECT title FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();

            //get the number of papers listed in the search result
            foreach ($paperTitles as $title) {
                if (strstr($response['content'], $title['title']) != false) {
                    $actual_NbrOfPapers++;
                }
            }

            //check if the paper with class_paper_id = 3 is the only paper listed in the search result
            $title = $this->ci->db->query("SELECT title FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id = 3")->row_array()['title'];
            if (strstr($response['content'], $title) != false && $actual_NbrOfPapers == 1) {
                $actual_NbrOfPapers = 1;
            }
        }

        run_test($this->controller, $action, $test_name, $test_NbrOfPapers, $expected_NbrOfPapers, $actual_NbrOfPapers);
    }

    /*
     * Test 19
     * Action : search_classification
     * Description : Search and display classification info with a string that matches 0 paper.
     * Expected displayed paper(s) and number of displayed papers: 0
     */
    private function searchClassification_0Match()
    {
        $action = "search_classification";
        $test_name = "Search and display classification info with a string that matches 0 paper";
        $test_NbrOfPapers = "Nbr of displayed papers";
        $expected_NbrOfPapers = 0;
        $actual_NbrOfPapers = "";


        //search classification paper with brand = 40
        $response = $this->http_client->response($this->controller, $action . "/brand/30");

        if ($response['status_code'] >= 400) {
            $actual_DbUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $paperTitles = $this->ci->db->query("SELECT title FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();

            //get the number of papers listed in the search result
            foreach ($paperTitles as $title) {
                if (strstr($response['content'], $title['title']) != false) {
                    $actual_NbrOfPapers = "Not zero";
                    break;
                }
                $actual_NbrOfPapers = 0;
            }
        }

        run_test($this->controller, $action, $test_name, $test_NbrOfPapers, $expected_NbrOfPapers, $actual_NbrOfPapers);
    }

    /*
     * Test 20
     * Action : edit_classification
     * Description : Display the form to edit a classification.
     * Expected displayed correct form fields
     */
    private function editClassification_normal()
    {
        $action = "edit_classification";
        $test_name = "Display the form to edit a classification";
        $test_formFields = "Are form fields properly displayed?";
        $expected_formFields = "Yes";
        $actual_formFields = "No";

        $response = $this->http_client->response($this->controller, $action . "/1/normal");

        if ($response['status_code'] >= 400) {
            $actual_formFields = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //Get the test project classification fields from the test project installation file
            $testProjectFields = get_classification_demoTestProject()['config']['classification']['fields'];
            unset($testProjectFields['user_id'], $testProjectFields['classification_time'], $testProjectFields['class_active'], $testProjectFields['class_id'], $testProjectFields['class_paper_id'], $testProjectFields['variety']);

            $missingFields = [];
            //check if the fields are properly displayed
            foreach ($testProjectFields as $key => $value) {
                if (strstr($response['content'], 'for="' . $key . '"') == false) {
                    array_push($missingFields, "Field missing: " . $value['field_title']);
                }
                if ($value['input_type'] == 'select' && strstr($response['content'], 'select name="' . $key . '"') == false) {
                    array_push($missingFields, "Field must be a select field: " . $value['field_title']);
                }

                if ($value['input_type'] == 'text' && strstr($response['content'], 'input type="text" name="' . $key . '"') == false) {
                    array_push($missingFields, "Field must be a text field: " . $value['field_title']);
                }
            }

            if (empty($missingFields)) {
                $actual_formFields = "Yes";
            } else {
                $actual_formFields = json_encode($missingFields);
            }
        }

        run_test($this->controller, $action, $test_name, $test_formFields, $expected_formFields, $actual_formFields);
    }

    /*
     * Test 21
     * Action : edit_classification
     * Description : Display the form to edit a classification with modal view.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function editClassification_modal()
    {
        $action = "edit_classification";
        $test_name = "Display the form to edit a classification with modal view";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/1/modal");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 22
     * Action : new_classification
     * Description : Display the form for a new classification.
     * Expected displayed correct form fields
     */
    private function newClassification_normal()
    {
        $action = "new_classification";
        $test_name = "Display the form for a new classification";
        $test_formFields = "Are form fields properly displayed?";
        $expected_formFields = "Yes";
        $actual_formFields = "No";

        $response = $this->http_client->response($this->controller, $action . "/1");

        if ($response['status_code'] >= 400) {
            $actual_formFields = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //Get the test project classification fields from the test project installation file
            $testProjectFields = get_classification_demoTestProject()['config']['classification']['fields'];
            unset($testProjectFields['user_id'], $testProjectFields['classification_time'], $testProjectFields['class_active'], $testProjectFields['class_id'], $testProjectFields['class_paper_id'], $testProjectFields['variety']);


            $missingFields = [];
            //check if the fields are properly displayed
            foreach ($testProjectFields as $key => $value) {
                if (strstr($response['content'], 'for="' . $key . '"') == false) {
                    array_push($missingFields, "Field missing: " . $value['field_title']);
                }
                if ($value['input_type'] == 'select' && strstr($response['content'], 'select name="' . $key . '"') == false) {
                    array_push($missingFields, "Field must be a select field: " . $value['field_title']);
                }

                if ($value['input_type'] == 'text' && strstr($response['content'], 'input type="text" name="' . $key . '"') == false) {
                    array_push($missingFields, "Field must be a text field: " . $value['field_title']);
                }
            }

            if (empty($missingFields)) {
                $actual_formFields = "Yes";
            } else {
                $actual_formFields = json_encode($missingFields);
            }
        }

        run_test($this->controller, $action, $test_name, $test_formFields, $expected_formFields, $actual_formFields);
    }

    /*
     * Test 23
     * Action : new_classification_modal
     * Description : Display the form for a new classification in the modal view.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function newClassification_modal()
    {
        $action = "new_classification_modal";
        $test_name = "Display the form for a new classification in the modal view.";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/2");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 24
     * Action : class_assignment_validation_set
     * Description : load the view to display the classification validation assignment form
     * Expected Number of users available for classification validation
     */
    private function displayValidationAssignementForm()
    {
        $action = "class_assignment_validation_set";
        $test_name = "Load the view to display the classification validation assignment form";
        $test_nbrOfUser = "Number of users added to the project available for classification validation";
        $expected_nbrOfUser = 2;

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_nbrOfUser = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //available users in the application
            $users = $this->ci->db->query("SELECT * FROM users")->result_array();
            //users added to the project with correct rights available for classification validation
            $projectUserids = $this->ci->db->query("SELECT user_id FROM userproject WHERE project_id = " . getProjectId() . " AND user_role !='Guest'")->result_array();
            //users listed in the assignement form
            $usersListedInTheAssignementForm = [];

            for ($i = 0; $i < count($users); $i++) {
                //check if user is listed in the form
                if (strstr($response['content'], "\">" . $users[$i]['user_name'] . "<") != false) {
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

            $actual_nbrOfUser = count($usersListedInTheAssignementForm);
        }

        run_test($this->controller, $action, $test_name, $test_nbrOfUser, $expected_nbrOfUser, $actual_nbrOfUser);
    }

    /*
     * Test 25
     * Action : class_validation_assignment_save
     * Description : Handle the saving of paper assignments for classification validation with empty percentage in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in assigned table
     *          - assign_class_validat operation not inserted in operations table in the project DB
     */
    private function saveAssignmentValidation_noPercentage()
    {
        $action = "class_validation_assignment_save";
        $test_name = "Handle the saving of paper assignments for classification validation with empty percentage in POST request";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";
        $actual_assignement = "";

        $postData = ["number_of_users" => 2, "percentage" => "", "user_1" => getAdminUserId(), "user_2" => getTestUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assignment_type='Validation'")->row_array()['row_count'];

            //Check if the assign_class_validat operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class_validat' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 26
     * Action : class_validation_assignment_save
     * Description : Handle the saving of paper assignments for classification validation with Wrong percentage (150%) in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in assigned table
     *          - assign_class_validat operation not inserted in operations table in the project DB
     */
    private function saveAssignmentValidation_wrongPercentage()
    {
        $action = "class_validation_assignment_save";
        $test_name = "Handle the saving of paper assignments for classification validation with Wrong percentage (150%) in POST request";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";
        $actual_assignement = "";

        $postData = ["number_of_users" => 2, "percentage" => 150, "user_1" => getAdminUserId(), "user_2" => getTestUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assignment_type='Validation'")->row_array()['row_count'];

            //Check if the assign_class_validat operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class_validat' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 27
     * Action : class_validation_assignment_save
     * Description : Handle the saving of paper assignments for classification validation with empty users in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in assigned table
     *          - assign_class_validat operation not inserted in operations table in the project DB
     */
    private function saveAssignmentValidation_emptyUsers()
    {
        $action = "class_validation_assignment_save";
        $test_name = "Handle the saving of paper assignments for classification validation with empty users in POST request";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";
        $actual_assignement = "";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');

        $postData = ["number_of_users" => 0, "percentage" => 100];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_DbUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assignment_type='Validation'")->row_array()['row_count'];

            //Check if the assign_class_validat operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class_validat' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 28
     * Action : class_validation_assignment_save
     * Description : save the assignments of papers for classification validation with 30% of paper assignement.
     * Expected Paper assignements : 
     *          - 30% of the papers are added in the project DB in assigned table
     *          - assign_class_validat operation inserted in operations table in the project DB
     */
    private function saveAssignmentClassValidation_30percent()
    {
        //Assign papers for classification to users
        $response = $this->http_client->response($this->controller, "class_assignment_save", ["number_of_users" => 2, "percentage" => 100, "user_1" => getAdminUserId(), "user_2" => getTestUserId()], "POST");
        performClassification();

        $action = "class_validation_assignment_save";
        $test_name = "Save the assignments of papers for classification validation with 30% of paper assignement";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        $postData = ["number_of_users" => 2, "percentage" => 30, "user_1" => getAdminUserId(), "user_2" => getTestUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if 30% of the papers have been assigned (we have 5 papers to be assigned, so 30% of 5 = 1.5 ~ 2)
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assignment_type='Validation'")->row_array()['row_count'];

            //Check if the assign_qa_validation operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class_validat' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 2 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 29
     * Action : class_validation_assignment_save
     * Description : save the assignments of papers for classification validation with 100% of paper assignement.
     * Expected Paper assignements : 
     *          - 100% of the papers are added in the project DB in assigned table
     *          - assign_class_validat operation inserted in operations table in the project DB
     */
    private function saveAssignmentClassValidation_100percent()
    {
        //Assign papers for classification to users
        $response = $this->http_client->response($this->controller, "class_assignment_save", ["number_of_users" => 2, "percentage" => 100, "user_1" => getAdminUserId(), "user_2" => getTestUserId()], "POST");
        performClassification();

        $action = "class_validation_assignment_save";
        $test_name = "Save the assignments of papers for classification validation with 100% of paper assignement";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening with 4 paper inclusions
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        //perform classification
        assignPapersForClassification([getAdminUserId(), getTestUserId()]);
        performClassification();

        $postData = ["number_of_users" => 2, "percentage" => 100, "user_1" => getAdminUserId(), "user_2" => getTestUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned
            $paperIds = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            $paperIdList = array();
            foreach ($paperIds as $paperId) {
                $paperIdList[] = $paperId['id'];
            }
            $paperIdList = implode(',', $paperIdList);
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_paper_id IN (" . $paperIdList . ") AND assignment_type='Validation'")->row_array()['row_count'];

            //Check if the assign_qa_validation operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_class_validat' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if (count($paperIds) == $nbrOfAssignment && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 30
     * Action : display_paper_validation
     * Description : Display paper details to perform classification validation
     * Expected displayed paper
     */
    private function displayPaperValidation()
    {
        $action = "display_paper_validation";
        $test_name = "Display paper details to perform classification validation";
        $test_displayedPaper = "Displayed paper";

        //get random paper that is available for classification
        $paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE screening_status = 'Included' ORDER BY RAND() LIMIT 1")->row_array();
        $expected_displayedPaper = $paper['title'];
        $actual_displayedPaper = "";

        $response = $this->http_client->response($this->controller, $action . "/" . $paper['id']);

        if ($response['status_code'] >= 400) {
            $actual_displayedPaper = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //papers available in the project
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();

            for ($i = 0; $i < count($papers); $i++) {
                //check wich paper is displayed
                if (strstr($response['content'], $papers[$i]['title']) != false) {
                    $actual_displayedPaper = $paper['title'];
                }
            }
        }


        run_test($this->controller, $action, $test_name, $test_displayedPaper, $expected_displayedPaper, $actual_displayedPaper);
    }

    /*
     * Test 31
     * Action : class_validate
     * Description : perform the validation of a classification for a specific paper as Correct QA.
     * Expected update in DB : In the assigned table, the validation field become "Correct"
     */
    private function classValidate_setCcorrect()
    {
        $action = "class_validate";
        $test_name = "Perform the validation of a classification for a specific paper as Correct QA.";
        $test_paperUpdate = "Validation field value in assigned table";
        $expected_paperUpdate = "Correct";

        $getApaperId = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper ORDER BY id DESC LIMIT 1")->row_array()['id'];
        $response = $this->http_client->response($this->controller, $action . "/" . $getApaperId);

        if ($response['status_code'] >= 400) {
            $actual_paperUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_paperUpdate = $this->ci->db->query("SELECT validation FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_paper_id = " . $getApaperId)->row_array()['validation'];
        }

        run_test($this->controller, $action, $test_name, $test_paperUpdate, $expected_paperUpdate, $actual_paperUpdate);
    }

    /*
     * Test 32
     * Action : class_validate
     * Description : perform the validation of a classification for a specific paper as not correct QA.
     * Expected update in DB : In the assigned table, the validation field become "Not Correct"
     */
    private function classValidate_setNotCcorrect()
    {
        $action = "class_validate";
        $test_name = "Perform the validation of a classification for a specific paper as not correct QA.";
        $test_paperUpdate = "Paper validation";
        $expected_paperUpdate = "Not Correct";

        $getApaperId = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper ORDER BY id DESC LIMIT 1")->row_array()['id'];
        $response = $this->http_client->response($this->controller, $action . "/" . $getApaperId . "/0");

        if ($response['status_code'] >= 400) {
            $actual_paperUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_paperUpdate = $this->ci->db->query("SELECT validation FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_paper_id = " . $getApaperId)->row_array()['validation'];
        }

        run_test($this->controller, $action, $test_name, $test_paperUpdate, $expected_paperUpdate, $actual_paperUpdate);
    }

    /*
     * Test 33
     * Action : class_completion
     * Description : retrieves completion information for classification validation
     * Expected HTTP Response Code : 200
     */
    private function classValidation_completion_info()
    {
        $action = "class_completion";
        $test_name = "Retrieves completion information for classification validation";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/validate");

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response["status_code"]];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 34
     * Action : remove_classification
     * Description : remove a classification entry for a project and redirect to data_extraction/display_paper
     * Expected removed classification: the class_active field in the classification table become 0
     */
    private function removeClassification()
    {
        $action = "remove_classification";
        $test_name = "Remove a classification entry for a project and redirect to data_extraction/display_paper";
        $test_class_removed = "Classification active? (1 = yes, 0 = no)";
        $expected_class_removed = "0";

        $classification_entry = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".classification LIMIT 1")->row_array();
        $response = $this->http_client->response($this->controller, $action . "/" . $classification_entry['class_id'] . "/" . $classification_entry['class_paper_id'] . "/data_extraction/display_paper");

        if ($response['status_code'] >= 400) {
            $actual_class_removed = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_class_removed = $this->ci->db->query("SELECT class_active FROM relis_dev_correct_" . getProjectShortName() . ".classification WHERE class_id = " . $classification_entry['class_id'])->row_array()['class_active'];
        }

        run_test($this->controller, $action, $test_name, $test_class_removed, $expected_class_removed, $actual_class_removed);
    }

    /* 
     * Test 35
     * Action : remove_classification
     * Description : remove a classification entry for a project and redirect to paper/view_paper
     * Expected removed classification: the class_active field in the classification table become 0
     */
    private function removeClassification_redirectToPaper()
    {
        $action = "remove_classification";
        $test_name = "Remove a classification entry for a project and redirect to paper/view_paper";
        $test_class_removed = "Classification active? (1 = yes, 0 = no)";
        $expected_class_removed = "0";

        $classification_entry = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".classification ORDER BY class_id DESC LIMIT 1")->row_array();
        $response = $this->http_client->response($this->controller, $action . "/" . $classification_entry['class_id'] . "/" . $classification_entry['class_paper_id'] . "/paper/view_paper");

        if ($response['status_code'] >= 400) {
            $actual_class_removed = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_class_removed = $this->ci->db->query("SELECT class_active FROM relis_dev_correct_" . getProjectShortName() . ".classification WHERE class_id = " . $classification_entry['class_id'])->row_array()['class_active'];
        }

        run_test($this->controller, $action, $test_name, $test_class_removed, $expected_class_removed, $actual_class_removed);
    }
}