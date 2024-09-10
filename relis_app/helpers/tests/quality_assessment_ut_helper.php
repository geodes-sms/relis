<?php

// TEST QUALITY_ASSESSMENT CONTROLLER
class Quality_assessmentUnitTest
{
    private $controller;
    private $http_client;
    private $ci;
    private $qa_results;
    private $qaVal_results;

    function __construct()
    {
        $this->controller = "quality_assessment";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->activateQA();
        $this->deActivateQA();
        $this->loadAssignmentView();
        $this->saveAssignmentQA_emptyUsers();
        $this->saveAssignmentQA_2of5papers_2reviewers();
        $this->saveAssignmentQA_4papers_1reviewer();
        $this->saveAssignmentQA_6papers_3reviewers();
        $this->saveAssignmentQA_5papers_3reviewers();
        $this->saveAssignmentQA_5papers_2reviewers();
        $this->qaConductList_all();
        $this->qaConductList_pending();
        $this->qaConductList_done();
        $this->qaConductList_excluded();
        $this->qaConductList_byId();
        $this->editQaForPaper();
        $this->qa_completion_info();
        $this->qaConductDetail();
        $this->qaExlusion();
        $this->qaCancelExlusion();
        $this->SaveQA_All_Questions();
        $this->SaveQA_1_Question();
        $this->displayQaResult_all();
        $this->displayQaResult_excluded();
        $this->loadAssignmentViewForValidation();
        $this->saveAssignmentQAValidation_emptyPercentage();
        $this->saveAssignmentQAValidation_invalidPercentage();
        $this->saveAssignmentQAValidation_emptyUsers();
        $this->saveAssignmentQAValidation_30percent();
        $this->saveAssignmentQAValidation_100percent();
        $this->qaValidationConductList_all();
        $this->qaValidationConductList_pending();
        $this->qaValidationConductList_done();
        $this->qaValidationConductList_excluded();
        $this->qaValidationConductList_byId();
        $this->qaValidation_completion_info();
        $this->qaExcludeLowQuality();
        $this->qaValidate_setCcorrect();
        $this->qaValidate_setNotCcorrect();
        $this->qa();
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
     * Action : activate_qa
     * Description : turn the quality assessment feature ON
     * Expected database update: The value of "qa_open" field in the "config" table in the project Db shoud become "1"
     */
    private function activateQA()
    {
        $action = "activate_qa";
        $test_name = "Turn the quality assessment feature ON";
        $test_dbUpdate = "Is QA on (1 = yes, 0 = no)";
        $expected_DbUpdate = '1';

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_DbUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_DbUpdate = $this->ci->db->query("SELECT qa_open FROM relis_dev_correct_" . getProjectShortName() . ".config WHERE config_id = 1")->row_array()['qa_open'];
        }

        run_test($this->controller, $action, $test_name, $test_dbUpdate, $expected_DbUpdate, $actual_DbUpdate);
    }

    /*
     * Test 2
     * Action : activate_qa
     * Description : turn the quality assessment feature OFF
     * Expected database update: The value of "qa_open" field in the "config" table in the project Db shoud become "0"
     */
    private function deActivateQA()
    {
        $action = "activate_qa";
        $test_name = "Turn the quality assessment feature OFF";
        $test_dbUpdate = "Is QA on (1 = yes, 0 = no)";
        $expected_DbUpdate = '0';

        $response = $this->http_client->response($this->controller, $action . "/0");

        if ($response['status_code'] >= 400) {
            $actual_DbUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_DbUpdate = $this->ci->db->query("SELECT qa_open FROM relis_dev_correct_" . getProjectShortName() . ".config WHERE config_id = 1")->row_array()['qa_open'];
        }

        run_test($this->controller, $action, $test_name, $test_dbUpdate, $expected_DbUpdate, $actual_DbUpdate);
    }

    /*
     * Test 3
     * Action : qa_assignment_set
     * Description : loads the form for assigning papers for quality assessment
     * Expected nbr of users (Number of users added to the project available for assignation) : 2
     * Expected nbr of papers (Number of included papers in the screening phase available for QA assignment) : 4
     */
    private function loadAssignmentView()
    {
        $action = "qa_assignment_set";
        $test_name = "Loads the form for assigning papers for quality assessment";

        $test_nbrOfUser = "Number of users added to the project available for assignation";
        $test_nbrOfPapers = "Number of included papers in the screening phase available for QA assignment";

        $expected_nbrOfUser = 2;
        $expected_nbrOfPapers = 4;

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening with 4 paper inclusions
        assignPapers_and_performScreening([getAdminUserId()], 'Title', -1, 4);

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

            //papers available in the project
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            //papers listed in the assignement form
            $papersListedInTheAssignementForm = [];

            for ($i = 0; $i < count($papers); $i++) {
                //check if paper is listed in the form and paper has been included in the screening phase
                if (strstr($response['content'], $papers[$i]['title']) != false && $papers[$i]['screening_status'] == "Included") {
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
     * Test 4
     * Action : qa_assignment_save
     * Description : Handle the saving of paper assignments for QA with empty users in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in qa_assignment table
     *          - assign_qa operation not inserted in operations table in the project DB
     */
    private function saveAssignmentQA_emptyUsers()
    {
        $action = "qa_assignment_save";
        $test_name = "Handle the saving of paper assignments for QA with empty users in POST request";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";
        $actual_assignement = "";

        $postData = ["number_of_users" => 0, "percentage" => 100];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned and are inserted in the qa_assignment table
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment")->row_array()['row_count'];

            //Check if the assign_qa operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa' AND operation_desc = 'Assign  papers for QA' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }


    /*
     * Test 5
     * Action : qa_assignment_save
     * Description : Test the number of assignment per reviewer with 2 out of 5 papers to assign to 2 reviewers.
     * Expected Paper assignements :
     *          - The three assigned papers are added to the project database in the "qa_assignment" table, one reviewer is assigned 1 papers each.
     *          - assign_qa operation inserted in operations table in the project DB
     */
    private function saveAssignmentQA_2of5papers_2reviewers()
    {
        $action = "qa_assignment_save";
        $test_name = "Test the number of assignment per reviewer with 2 out of 5 papers to assign to 2 reviewers";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');

        $postData = ["number_of_users" => 2, "percentage" => 100, "user_1" => getAdminUserId(), "user_2" => getTestUserId(), "assign_all_paper_checkbox" => "off", "number_of_papers_to_assign" => 2];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check the number of papers assigned to the first user
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getAdminUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getTestUserId())->row_array()['row_count'];

            //Check if the assign_qa operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa' AND operation_desc = 'Assign  papers for QA' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment1 == 1 && $nbrOfAssignment2 == 1 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }

            run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
        }
    }

    /*
     * Test 6
     * Action : qa_assignment_save
     * Description : Test the number of assignment per reviewer with 4 papers to assign to 1 reviewer.
     * Expected Paper assignements : 
     *          - The four assigned papers are added to the project database in the "qa_assignment" table, with the reviewer's user ID assigned to the "assigned_to" field for all four papers.
     *          - assign_qa operation inserted in operations table in the project DB
     */
    private function saveAssignmentQA_4papers_1reviewer()
    {
        $action = "qa_assignment_save";
        $test_name = "Test the number of assignment per reviewer with 4 papers to assign to 1 reviewer";
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
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . $userId)->row_array()['row_count'];

            //Check if the assign_qa operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa' AND operation_desc = 'Assign  papers for QA' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 4 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 7
     * Action : qa_assignment_save
     * Description : Test the number of assignment per reviewer with 6 papers to assign to 3 reviewers.
     * Expected Paper assignements : 
     *          - The 6 assigned papers are added to the project database in the "qa_assignment" table, the 3 reviewers are assigned 2 papers each.
     *          - assign_qa operation inserted in operations table in the project DB
     */
    private function saveAssignmentQA_6papers_3reviewers()
    {
        $action = "qa_assignment_save";
        $test_name = "Test the number of assignment per reviewer with 6 papers to assign to 3 reviewers";
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
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getAdminUserId())->row_array()['row_count'];

            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getTestUserId())->row_array()['row_count'];

            // Check the number of papers assigned to the third user
            $nbrOfAssignment3 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getDemoUserId())->row_array()['row_count'];

            //Check if the assign_qa operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa' AND operation_desc = 'Assign  papers for QA' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment1 == 2 && $nbrOfAssignment2 == 2 && $nbrOfAssignment3 == 2 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 8
     * Action : qa_assignment_save
     * Description : Test the number of assignment per reviewer with 5 papers to assign to 3 reviewers.
     * Expected Paper assignements : 
     *          - The five assigned papers are added to the project database in the "qa_assignment" table, two reviewers are assigned 2 papers and the other reviewer is assigned 1 paper.
     *          - assign_qa operation inserted in operations table in the project DB
     */
    private function saveAssignmentQA_5papers_3reviewers()
    {
        $action = "qa_assignment_save";
        $test_name = "Test the number of assignment per reviewer with 5 papers to assign to 3 reviewers";
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
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getAdminUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getTestUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the third user
            $nbrOfAssignment3 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getDemoUserId())->row_array()['row_count'];

            //Check if the assign_qa operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa' AND operation_desc = 'Assign  papers for QA' AND operation_state = 'Active' AND operation_active = 1")->row_array();

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
     * Action : qa_assignment_save
     * Description : Test the number of assignment per reviewer with 5 papers to assign to 2 reviewers.
     * Expected Paper assignements : 
     *          - The five assigned papers are added to the project database in the "qa_assignment" table, one reviewer is assigned 2 papers and the other reviewer is assigned 3 papers.
     *          - assign_qa operation inserted in operations table in the project DB
     */
    private function saveAssignmentQA_5papers_2reviewers()
    {
        $action = "qa_assignment_save";
        $test_name = "Test the number of assignment per reviewer with 5 papers to assign to 2 reviewers";
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
            $nbrOfAssignment1 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getAdminUserId())->row_array()['row_count'];
            // Check the number of papers assigned to the second user
            $nbrOfAssignment2 = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getTestUserId())->row_array()['row_count'];

            //Check if the assign_qa operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa' AND operation_desc = 'Assign  papers for QA' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if (abs($nbrOfAssignment1 - $nbrOfAssignment2) == 1 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }

            run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
        }
    }

    /*
     * Test 10
     * Action : qa_conduct_list
     * Description : Display the list of all QA conduct results 
     * Expected number of papers listed
     */
    private function qaConductList_all()
    {
        $action = "qa_conduct_list";
        $test_name = "Display the list of all QA conduct results";
        $test_numberOfElementsInList = "Nbr of Elements in List";
        $expected_numberOfElementsInList = 5;
        $actual_numberOfElementInList = 0;

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        //perform QA (2 high quality QAs, 2 low quality QAs, 1 pending)
        $this->qa_results = assignPapers_and_performQA([getAdminUserId()], 4, 2);

        $response = $this->http_client->response($this->controller, $action . "/mine/0/all");

        if ($response['status_code'] >= 400) {
            $actual_numberOfElementInList = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            for ($i = 0; $i < count($papers); $i++) {
                // Check if paper title is listed
                if (strstr($response['content'], $papers[$i]['title']) != false && array_key_exists($papers[$i]['id'], $this->qa_results)) {
                    $actual_numberOfElementInList++;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_numberOfElementsInList, $expected_numberOfElementsInList, $actual_numberOfElementInList);
    }

    /*
     * Test 11
     * Action : qa_conduct_list
     * Description : display the list of pending QA conduct results
     * Expected number of papers listed
     */
    private function qaConductList_pending()
    {
        $action = "qa_conduct_list";
        $test_name = "Display the list of pending QA conduct results";
        $test_numberOfElementsInList = "Nbr of Elements in List";
        $expected_numberOfElementsInList = 1;
        $actual_numberOfElementInList = 0;

        $response = $this->http_client->response($this->controller, $action . "/mine/0/pending");

        if ($response['status_code'] >= 400) {
            $actual_numberOfElementInList = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            for ($i = 0; $i < count($papers); $i++) {
                // Check if paper title is listed
                if (strstr($response['content'], $papers[$i]['title']) != false && $this->qa_results[$papers[$i]['id']] == "Pending") {
                    $actual_numberOfElementInList++;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_numberOfElementsInList, $expected_numberOfElementsInList, $actual_numberOfElementInList);
    }

    /*
     * Test 12
     * Action : qa_conduct_list
     * Description : display the list of done QA conduct results
     * Expected number of papers listed
     */
    private function qaConductList_done()
    {
        $action = "qa_conduct_list";
        $test_name = "Display the list of done QA conduct results";
        $test_numberOfElementsInList = "Nbr of Elements in List";
        $expected_numberOfElementsInList = 4;
        $actual_numberOfElementInList = 0;

        $response = $this->http_client->response($this->controller, $action . "/mine/0/done");

        if ($response['status_code'] >= 400) {
            $actual_numberOfElementInList = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            for ($i = 0; $i < count($papers); $i++) {
                // Check if paper title is listed
                if (strstr($response['content'], $papers[$i]['title']) != false && ($this->qa_results[$papers[$i]['id']] == "Low quality" || $this->qa_results[$papers[$i]['id']] == "High quality")) {
                    $actual_numberOfElementInList++;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_numberOfElementsInList, $expected_numberOfElementsInList, $actual_numberOfElementInList);
    }

    /*
     * Test 13
     * Action : qa_conduct_list
     * Description : display the list of excluded QA conduct results
     * Expected number of papers listed
     */
    private function qaConductList_excluded()
    {
        $action = "qa_conduct_list";
        $test_name = "Display the list of excluded QA conduct results";
        $test_numberOfElementsInList = "Nbr of Elements in List";
        $expected_numberOfElementsInList = 2;
        $actual_numberOfElementInList = 0;

        //Exclude low quality QA
        qaExcludeLowQuality();
        $response = $this->http_client->response($this->controller, $action . "/excluded/0/all");

        if ($response['status_code'] >= 400) {
            $actual_numberOfElementInList = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            for ($i = 0; $i < count($papers); $i++) {
                // Check if paper title is listed
                if (strstr($response['content'], $papers[$i]['title']) != false && $this->qa_results[$papers[$i]['id']] == "Low quality") {
                    $actual_numberOfElementInList++;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_numberOfElementsInList, $expected_numberOfElementsInList, $actual_numberOfElementInList);
    }

    /*
     * Test 14
     * Action : qa_conduct_list
     * Description : display the list of a specific QA conduct results
     * Expected paper listed
     */
    private function qaConductList_byId()
    {
        $action = "qa_conduct_list";
        $test_name = "Display the list of a specific QA conduct results";
        $test_paperListed = "Paper in List";
        $paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE screening_status != 'Excluded_QA' ORDER BY RAND() LIMIT 1")->row_array();
        $expected_paperListed = $paper['title'];
        $actual_paperListed = "";

        $response = $this->http_client->response($this->controller, $action . "/id/" . $paper['id'] . "/all");

        if ($response['status_code'] >= 400) {
            $actual_paperListed = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (strstr($response['content'], $paper['title']) != false) {
                $actual_paperListed = $paper['title'];
            }
        }

        run_test($this->controller, $action, $test_name, $test_paperListed, $expected_paperListed, $actual_paperListed);
    }

    /*
     * 
     * Test 15
     * Action : qa_conduct_save
     * Description : Edit the QA done for a paper
     * Expected database update: 
     *          - QA entries for the paper must be updated in qa_result table in the project Db
     *          - qa_status field of the updated paper in qa_assignment table remain "Done"
     */
    private function editQaForPaper()
    {
        $action = "qa_conduct_save";
        $test_name = "Edit the QA done for a paper";
        $testQa_result = "Quality assessment result";
        $testQa_status = "Paper QA status";
        $expected_qa_result = [];
        $expected_qa_status = "Done";
        $actual_qa_status = "";

        //Get one paper assigned to admin user
        $papersAssignedToAdminUser = $this->ci->db->query("SELECT paper_id FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getAdminUserId() . " LIMIT 1")->row_array()['paper_id'];

        //edit QA for the paper by answering 2 to the first and second questions, and 3 to the third question for all papers
        $this->http_client->response($this->controller, $action . "/1/" . $papersAssignedToAdminUser . "/1/2");
        $this->http_client->response($this->controller, $action . "/1/" . $papersAssignedToAdminUser . "/2/2");
        $response = $this->http_client->response($this->controller, $action . "/1/" . $papersAssignedToAdminUser . "/3/3");

        if ($response['status_code'] >= 400) {
            $actual_qa_result = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_qa_status = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //$j represent the number of qa_questions and qa_responses
            for ($j = 1; $j <= 3; $j++) {
                array_push($expected_qa_result, ["paper_id" => $papersAssignedToAdminUser, "question" => strval($j), "response" => ($j == 3) ? "3" : "2", "done_by" => getAdminUserId(), "validation" => "Pending", "qa_active" => strval(1)]);
            }

            $expected_qa_result = json_encode($expected_qa_result);
            $actual_qa_result = json_encode($this->ci->db->query("SELECT paper_id, question, response, done_by, validation, qa_active FROM relis_dev_correct_" . getProjectShortName() . ".qa_result WHERE paper_id = " . $papersAssignedToAdminUser)->result_array());

            //check qa_status for the edited paper
            $actual_qa_status = $this->ci->db->query("SELECT qa_status FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE paper_id = " . $papersAssignedToAdminUser)->row_array()['qa_status'];
        }

        run_test($this->controller, $action, $test_name, $testQa_result, $expected_qa_result, $actual_qa_result);
        run_test($this->controller, $action, $test_name, $testQa_status, $expected_qa_status, $actual_qa_status);
    }

    /*
     * Test 16
     * Action : qa_completion
     * Description : retrieves and display completion information for QA
     * Expected HTTP Response Code : 200
     */
    private function qa_completion_info()
    {
        $action = "qa_completion";
        $test_name = "Retrieves and display completion information for QA";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 17
     * Action : qa_conduct_detail
     * Description : display the detailed information and results of a specific quality assessment (QA) conduct
     * Expected HTTP Response Code : 200
     */
    private function qaConductDetail()
    {
        $action = "qa_conduct_detail";
        $test_name = "Display the detailed information and results of a specific quality assessment (QA) conduct";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/1");

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 18
     * Action : qa_exlusion
     * Description : exclude a paper from quality assessment
     * Expected update in DB : In the paper table, the screening_status field become "Excluded_QA" and classification_status field become "Waiting"
     */
    private function qaExlusion()
    {
        $action = "qa_exlusion";
        $test_name = "Exclude a paper from quality assessment";
        $test_paperUpdate = "Paper update";
        $getApaperId = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper LIMIT 1")->row_array()['id'];
        $expected_paperUpdate = json_encode(["id" => strval($getApaperId), "screening_status" => "Excluded_QA", "classification_status" => "Waiting"]);

        $response = $this->http_client->response($this->controller, $action . "/" . $getApaperId);

        if ($response['status_code'] >= 400) {
            $actual_paperUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_paperUpdate = json_encode($this->ci->db->query("SELECT id, screening_status, classification_status FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id = " . $getApaperId)->row_array());
        }

        run_test($this->controller, $action, $test_name, $test_paperUpdate, $expected_paperUpdate, $actual_paperUpdate);
    }

    /*
     * Test 19
     * Action : qa_exlusion
     * Description : cancel the exclusion of a paper from quality assessment
     * Expected update in DB : In the paper table, the screening_status field become "Included" and classification_status field become "To classify"
     */
    private function qaCancelExlusion()
    {
        $action = "qa_exlusion";
        $test_name = "Cancel the exclusion of a paper from quality assessment";
        $test_paperUpdate = "Paper update";
        $getApaperId = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper LIMIT 1")->row_array()['id'];
        $expected_paperUpdate = json_encode(["id" => strval($getApaperId), "screening_status" => "Included", "classification_status" => "To classify"]);

        $response = $this->http_client->response($this->controller, $action . "/" . $getApaperId . "/0");

        if ($response['status_code'] >= 400) {
            $actual_paperUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_paperUpdate = json_encode($this->ci->db->query("SELECT id, screening_status, classification_status FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id = " . $getApaperId)->row_array());
        }

        run_test($this->controller, $action, $test_name, $test_paperUpdate, $expected_paperUpdate, $actual_paperUpdate);
    }

    /*
     * Test 20
     * Action : qa_conduct_save
     * Description : save the QA for a paper by answering all QA questions.
     * Expected QA score : 4.5
     * Expected database update: 
     *          - QA entry for the assessed paper must be inserted in the qa_result table in the project Db
     *          - qa_status field of the assessed paper in qa_assignment table becomes "Done"
     */
    private function SaveQA_All_Questions()
    {
        $action = "qa_conduct_save";
        $test_name = "Save the QA for a paper by answering all QA questions";

        $test_qa_score = "QA score";
        $test_qa_result = "Quality assessment result";
        $test_qa_status = "Paper QA status";

        $expected_qa_score = 4.5;
        $expected_qa_status = "Done";

        //initialise the Database
        $this->TestInitialize();
        //add papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        //Assign papers to users for QA
        $response = $this->http_client->response($this->controller, "qa_assignment_save", ["number_of_users" => 1, "percentage" => 100, "user_1" => getAdminUserId()], "POST");

        //Get one paper assigned to admin user
        $papersAssignedToAdminUser = $this->ci->db->query("SELECT paper_id FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getAdminUserId() . " LIMIT 1")->row_array()['paper_id'];

        $expected_qa_result = json_encode(
            [
                ["paper_id" => $papersAssignedToAdminUser, "question" => strval(1), "response" => strval(1), "done_by" => getAdminUserId(), "validation" => "Pending", "qa_active" => strval(1)],
                ["paper_id" => $papersAssignedToAdminUser, "question" => strval(2), "response" => strval(3), "done_by" => getAdminUserId(), "validation" => "Pending", "qa_active" => strval(1)],
                ["paper_id" => $papersAssignedToAdminUser, "question" => strval(3), "response" => strval(2), "done_by" => getAdminUserId(), "validation" => "Pending", "qa_active" => strval(1)]
            ]
        );

        //perform QA for the paper by answering all questions
        $response = $this->http_client->response($this->controller, $action . "/0/" . $papersAssignedToAdminUser . "/1/1");
        $this->http_client->response($this->controller, $action . "/0/" . $papersAssignedToAdminUser . "/2/3");
        $this->http_client->response($this->controller, $action . "/0/" . $papersAssignedToAdminUser . "/3/2");

        if ($response['status_code'] >= 400) {
            $actual_qa_score = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_qa_result = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_qa_status = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //List of responses given by the user for the conducted QA
            $responseList = $this->ci->db->query("SELECT response FROM relis_dev_correct_" . getProjectShortName() . ".qa_result WHERE paper_id = " . $papersAssignedToAdminUser)->result_array();
            //List of scores according to a response given for a QA question
            $scoreList = $this->ci->db->query("SELECT response_id, score FROM relis_dev_correct_" . getProjectShortName() . ".qa_responses")->result_array();
            $qa_score = 0;
            foreach ($responseList as $k_response => $v_response) {
                foreach ($scoreList as $k_score => $v_score) {
                    if ($v_score['response_id'] == $v_response['response']) {
                        $qa_score += $v_score['score'];
                    }
                }
            }

            $actual_qa_score = $qa_score;
            $actual_qa_status = $this->ci->db->query("SELECT qa_status FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE paper_id = " . $papersAssignedToAdminUser)->row_array()['qa_status'];
            $actual_qa_result = json_encode($this->ci->db->query("SELECT paper_id, question, response, done_by, validation, qa_active FROM relis_dev_correct_" . getProjectShortName() . ".qa_result WHERE paper_id = " . $papersAssignedToAdminUser)->result_array());
        }

        run_test($this->controller, $action, $test_name, $test_qa_score, $expected_qa_score, $actual_qa_score);
        run_test($this->controller, $action, $test_name, $test_qa_result, $expected_qa_result, $actual_qa_result);
        run_test($this->controller, $action, $test_name, $test_qa_status, $expected_qa_status, $actual_qa_status);
    }

    /*
     * Test 21
     * Action : qa_conduct_save
     * Description : save the QA for a paper by answering only one QA question.
     * Expected database update: 
     *          - QA entry for the assessed paper must be inserted in the qa_result table in the project Db
     *          - qa_status field of the assessed paper in qa_assignment table remains "Pending"
     */
    private function SaveQA_1_Question()
    {
        $action = "qa_conduct_save";
        $test_name = "Save the QA for a paper by answering only one QA question";

        $test_qa_score = "QA score";
        $test_qa_result = "Quality assessment result";
        $test_qa_status = "Paper QA status";

        $expected_qa_score = 1.5;
        $expected_qa_status = "Pending";

        //initialise the Database
        $this->TestInitialize();
        //add papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        //Assign papers to users for QA
        $response = $this->http_client->response($this->controller, "qa_assignment_save", ["number_of_users" => 1, "percentage" => 100, "user_1" => getAdminUserId()], "POST");
        //Get one paper assigned to admin user
        $papersAssignedToAdminUser = $this->ci->db->query("SELECT paper_id FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . getAdminUserId() . " LIMIT 1")->row_array()['paper_id'];

        $expected_qa_result = json_encode(["paper_id" => $papersAssignedToAdminUser, "question" => strval(1), "response" => strval(2), "done_by" => getAdminUserId(), "validation" => "Pending", "qa_active" => strval(1)]);

        //perform QA for the paper by answering only one question
        $response = $this->http_client->response($this->controller, $action . "/0/" . $papersAssignedToAdminUser . "/1/2");

        if ($response['status_code'] >= 400) {
            $actual_qa_score = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_qa_result = "<span style='color:red'>" . $response['content'] . "</span>";
            $actual_qa_status = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //List of responses given by the user for the conducted QA
            $responseList = $this->ci->db->query("SELECT response FROM relis_dev_correct_" . getProjectShortName() . ".qa_result WHERE paper_id = " . $papersAssignedToAdminUser)->result_array();
            //List of scores according to a response given for a QA question
            $scoreList = $this->ci->db->query("SELECT response_id, score FROM relis_dev_correct_" . getProjectShortName() . ".qa_responses")->result_array();
            $qa_score = 0;
            foreach ($responseList as $k_response => $v_response) {
                foreach ($scoreList as $k_score => $v_score) {
                    if ($v_score['response_id'] == $v_response['response']) {
                        $qa_score += $v_score['score'];
                    }
                }
            }
            $actual_qa_score = $qa_score;
            $actual_qa_status = $this->ci->db->query("SELECT qa_status FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE paper_id = " . $papersAssignedToAdminUser)->row_array()['qa_status'];
            $actual_qa_result = json_encode($this->ci->db->query("SELECT paper_id, question, response, done_by, validation, qa_active FROM relis_dev_correct_" . getProjectShortName() . ".qa_result WHERE paper_id = " . $papersAssignedToAdminUser)->row_array());
        }

        run_test($this->controller, $action, $test_name, $test_qa_score, $expected_qa_score, $actual_qa_score);
        run_test($this->controller, $action, $test_name, $test_qa_result, $expected_qa_result, $actual_qa_result);
        run_test($this->controller, $action, $test_name, $test_qa_status, $expected_qa_status, $actual_qa_status);
    }

    /*
     * Test 22
     * Action : qa_conduct_result
     * Description : display the overall result of all quality assessment (QA) conducts
     * Expected correct score for each QA assessment
     */
    private function displayQaResult_all()
    {
        $action = "qa_conduct_result";
        $test_name = "Display the overall result of all quality assessment (QA) conducts";
        $test_scores = "Are score calculations correct?";
        $expected_scores = "Yes";

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        //perform QA by answering 1 to the first and second questions, and 2 to the third question for all 5 papers (making the score of 7.5 for each paper)
        assignPapers_and_performQA([getAdminUserId()], 5, 2);

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_scores = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (substr_count($response['content'], 7.5) == 3 && substr_count($response['content'], 4.5) == 2) {
                $actual_scores = "Yes";
            }
        }

        run_test($this->controller, $action, $test_scores, $test_scores, $expected_scores, $actual_scores);
    }

    /*
     * Test 23
     * Action : qa_conduct_result
     * Description : display the overall result of excluded quality assessment (QA) conducts
     * Expected HTTP Response Code : 200
     */
    private function displayQaResult_excluded()
    {
        $action = "qa_conduct_result";
        $test_name = "Display the overall result of excluded quality assessment (QA) conducts";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        //Exclude low quality QA
        qaExcludeLowQuality();
        $response = $this->http_client->response($this->controller, $action . "/excluded");

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 24
     * Action : qa_assignment_validation_set
     * Description : loads the form for assigning papers for quality assessment validation
     * Expected HTTP Response Code : 200
     */
    private function loadAssignmentViewForValidation()
    {
        $action = "qa_assignment_validation_set";
        $test_name = "Loads the form for assigning papers for quality assessment validation";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        //initialise the Database
        $this->TestInitialize();
        //add papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        //perform QA
        assignPapers_and_performQA([getAdminUserId()]);

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 25
     * Action : qa_validation_assignment_save
     * Description : Handle the saving of paper assignments for QA validation with empty percentage in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in qa_validation_assignment table
     *          - assign_qa_validation operation not inserted in operations table in the project DB
     */
    private function saveAssignmentQAValidation_emptyPercentage()
    {
        $action = "qa_validation_assignment_save";
        $test_name = "Handle the saving of paper assignments for QA validation with empty percentage in POST request";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";
        $actual_assignement = "";

        $postData = ["number_of_users" => 2, "percentage" => "", "user_1" => getAdminUserId(), "user_2" => getTestUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment")->row_array()['row_count'];

            //Check if the assign_qa operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa_validation' AND operation_desc = 'Assign  papers for QA validation' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 26
     * Action : qa_validation_assignment_save
     * Description : Handle the saving of paper assignments for QA validation with invalid percentage (>100) in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in qa_validation_assignment table
     *          - assign_qa_validation operation not inserted in operations table in the project DB
     */
    private function saveAssignmentQAValidation_invalidPercentage()
    {
        $action = "qa_validation_assignment_save";
        $test_name = "Handle the saving of paper assignments for QA validation with percentage > 100 in POST request";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";
        $actual_assignement = "";

        $postData = ["number_of_users" => 2, "percentage" => 150, "user_1" => getAdminUserId(), "user_2" => getTestUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment")->row_array()['row_count'];

            //Check if the assign_qa operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa_validation' AND operation_desc = 'Assign  papers for QA validation' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 27
     * Action : qa_validation_assignment_save
     * Description : Handle the saving of paper assignments for QA validation with empty users in POST request.
     * Expected Paper assignements : 
     *          - No papers are added in the project DB in qa_validation_assignment table
     *          - assign_qa_validation operation not inserted in operations table in the project DB
     */
    private function saveAssignmentQAValidation_emptyUsers()
    {
        $action = "qa_validation_assignment_save";
        $test_name = "Handle the saving of paper assignments for QA validation with empty users in POST request";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Not assigned";
        $actual_assignement = "";

        $postData = ["number_of_users" => 0, "percentage" => 100];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if all the papers have been assigned
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment")->row_array()['row_count'];

            //Check if the assign_qa operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa_validation' AND operation_desc = 'Assign  papers for QA validation' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 0 && empty($operation)) {
                $actual_assignement = "Not assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 28
     * Action : qa_validation_assignment_save
     * Description : save the assignments of papers for quality assessment validation with 30% of paper assignement.
     * Expected Paper assignements : 
     *          - 30% of the papers are added in the project DB in qa_validation_assignment table
     *          - assign_qa_validation operation inserted in operations table in the project DB
     */
    private function saveAssignmentQAValidation_30percent()
    {
        $action = "qa_validation_assignment_save";
        $test_name = "Save the assignments of papers for quality assessment validation with 30% of paper assignement";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        $postData = ["number_of_users" => 2, "percentage" => 30, "user_1" => getAdminUserId(), "user_2" => getTestUserId()];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_assignement = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            // Check if 30% of the papers have been assigned (we have 5 papers to be assigned, so 30% of 5 = 1.5 ~ 2)
            $paperIds = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            $paperIdList = array();

            foreach ($paperIds as $paperId) {
                $paperIdList[] = $paperId['id'];
            }

            $paperIdList = implode(',', $paperIdList);
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment WHERE paper_id IN (" . $paperIdList . ")")->row_array()['row_count'];

            //Check if the assign_qa_validation operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa_validation' AND operation_desc = 'Assign  papers for QA validation' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if ($nbrOfAssignment == 2 && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 29
     * Action : qa_validation_assignment_save
     * Description : save the assignments of papers for quality assessment validation with 100% of paper assignement.
     * Expected Paper assignements : 
     *          - 100% of the papers are added in the project DB in qa_validation_assignment table
     *          - assign_qa_validation operation inserted in operations table in the project DB
     */
    private function saveAssignmentQAValidation_100percent()
    {
        $action = "qa_validation_assignment_save";
        $test_name = "Save the assignments of papers for quality assessment validation with 100% of paper assignement";
        $test_assignement = "Paper assignements";
        $expected_assignement = "Assigned";
        $actual_assignement = "Not assigned";

        //initialise the Database
        $this->TestInitialize();
        //add papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        //perform QA
        assignPapers_and_performQA([getAdminUserId()]);

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
            $nbrOfAssignment = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment WHERE paper_id IN (" . $paperIdList . ")")->row_array()['row_count'];

            //Check if the assign_qa_validation operation is inserted in operations table in the project DB
            $operation = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_type = 'assign_qa_validation' AND operation_desc = 'Assign  papers for QA validation' AND operation_state = 'Active' AND operation_active = 1")->row_array();

            if (count($paperIds) == $nbrOfAssignment && !empty($operation)) {
                $actual_assignement = "Assigned";
            }
        }

        run_test($this->controller, $action, $test_name, $test_assignement, $expected_assignement, $actual_assignement);
    }

    /*
     * Test 30
     * Action : qa_conduct_list_val
     * Description : display the list of all QA validation conduct results
     * Expected number of papers listed
     */
    private function qaValidationConductList_all()
    {
        $action = "qa_conduct_list_val";
        $test_name = "Display the list of all QA validation conduct results";
        $test_numberOfElementsInList = "Nbr of Elements in List";
        $expected_numberOfElementsInList = 5;
        $actual_numberOfElementInList = 0;

        //initialise the Database
        $this->TestInitialize();
        //add papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        //perform QA for all the 5 papers with 2 low quality assessments
        $this->qa_results = assignPapers_and_performQA([getAdminUserId()], 5, 2);

        //perform QA (2 corrects, 2 not corrects, 1 pending)
        $this->qaVal_results = assignPapers_and_performQA_Validation([getAdminUserId()], 4, 2);

        $response = $this->http_client->response($this->controller, $action . "/mine/0/all");

        if ($response['status_code'] >= 400) {
            $actual_numberOfElementInList = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            for ($i = 0; $i < count($papers); $i++) {
                // Check if paper title is listed
                if (strstr($response['content'], $papers[$i]['title']) != false && array_key_exists($papers[$i]['id'], $this->qaVal_results)) {
                    $actual_numberOfElementInList++;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_numberOfElementsInList, $expected_numberOfElementsInList, $actual_numberOfElementInList);
    }

    /*
     * Test 31
     * Action : qa_conduct_list_val
     * Description : display the list of pending QA validation conduct results
     * Expected number of papers listed
     */
    private function qaValidationConductList_pending()
    {
        $action = "qa_conduct_list_val";
        $test_name = "Display the list of pending QA validation conduct results";
        $test_numberOfElementsInList = "Nbr of Elements in List";
        $expected_numberOfElementsInList = 1;
        $actual_numberOfElementInList = 0;

        $response = $this->http_client->response($this->controller, $action . "/mine/0/pending");

        if ($response['status_code'] >= 400) {
            $actual_numberOfElementInList = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            for ($i = 0; $i < count($papers); $i++) {
                // Check if paper title is listed
                if (strstr($response['content'], $papers[$i]['title']) != false && $this->qaVal_results[$papers[$i]['id']] == "Pending") {
                    $actual_numberOfElementInList++;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_numberOfElementsInList, $expected_numberOfElementsInList, $actual_numberOfElementInList);
    }

    /*
     * Test 32
     * Action : qa_conduct_list_val
     * Description : display the list of done QA validation conduct results
     * Expected number of papers listed
     */
    private function qaValidationConductList_done()
    {
        $action = "qa_conduct_list_val";
        $test_name = "Display the list of done QA validation conduct results";
        $test_numberOfElementsInList = "Nbr of Elements in List";
        $expected_numberOfElementsInList = 4;
        $actual_numberOfElementInList = 0;

        $response = $this->http_client->response($this->controller, $action . "/mine/0/done");

        if ($response['status_code'] >= 400) {
            $actual_numberOfElementInList = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            for ($i = 0; $i < count($papers); $i++) {
                // Check if paper title is listed
                if (strstr($response['content'], $papers[$i]['title']) != false && ($this->qaVal_results[$papers[$i]['id']] == "Correct" || $this->qaVal_results[$papers[$i]['id']] == "Not correct")) {
                    $actual_numberOfElementInList++;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_numberOfElementsInList, $expected_numberOfElementsInList, $actual_numberOfElementInList);
    }

    /*
     * Test 33
     * Action : qa_conduct_list_val
     * Description : display the list of excluded QA validation conduct results
     * Expected number of papers listed
     */
    private function qaValidationConductList_excluded()
    {
        $action = "qa_conduct_list_val";
        $test_name = "Display the list of excluded QA validation conduct results";
        $test_numberOfElementsInList = "Nbr of Elements in List";
        $expected_numberOfElementsInList = 2;
        $actual_numberOfElementInList = 0;

        qaExcludeLowQuality();
        $response = $this->http_client->response($this->controller, $action . "/excluded/0/all");

        if ($response['status_code'] >= 400) {
            $actual_numberOfElementInList = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $papers = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper")->result_array();
            for ($i = 0; $i < count($papers); $i++) {
                // Check if paper title is listed
                if (strstr($response['content'], $papers[$i]['title']) != false && $this->qa_results[$papers[$i]['id']] == "Low quality") {
                    $actual_numberOfElementInList++;
                }
            }
        }

        run_test($this->controller, $action, $test_name, $test_numberOfElementsInList, $expected_numberOfElementsInList, $actual_numberOfElementInList);
    }

    /*
     * Test 34
     * Action : qa_conduct_list_val
     * Description : display the list of a specific QA validation conduct results
     * Expected number of papers listed
     */
    private function qaValidationConductList_byId()
    {
        $action = "qa_conduct_list_val";
        $test_name = "Display the list of a specific QA validation conduct results";
        $test_paperListed = "Paper in List";

        $paper = $this->ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE screening_status != 'Excluded_QA' ORDER BY RAND() LIMIT 1")->row_array();
        $expected_paperListed = $paper['title'];
        $actual_paperListed = "";

        $response = $this->http_client->response($this->controller, $action . "/id/" . $paper['id'] . "/all");

        if ($response['status_code'] >= 400) {
            $actual_paperListed = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            if (strstr($response['content'], $paper['title']) != false) {
                $actual_paperListed = $paper['title'];
            }
        }

        run_test($this->controller, $action, $test_name, $test_paperListed, $expected_paperListed, $actual_paperListed);
    }

    /*
     * Test 35
     * Action : qa_completion
     * Description : retrieves and display completion information for QA validation
     * Expected HTTP Response Code : 200
     */
    private function qaValidation_completion_info()
    {
        $action = "qa_completion";
        $test_name = "Retrieves and display completion information for QA validation";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/validate");

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 36
     * Action : qa_exclude_low_quality
     * Description : exclude all papers with low quality
     * Expected update in DB : In the paper table, the screening_status field become "Excluded_QA" and classification_status field become "Waiting"
     */
    private function qaExcludeLowQuality()
    {
        $action = "qa_exclude_low_quality";
        $test_name = "Exclude all papers with low quality";
        $test_nbrOfExclusions = "Nbr of Paper Excluded";
        $expected_nbrOfExclusions = '2';

        //initialise the Database
        $this->TestInitialize();
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        //perform QA with 2 paper with low quality
        assignPapers_and_performQA([getAdminUserId()], 5, 2);

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_nbrOfExclusions = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_nbrOfExclusions = $this->ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE screening_status = 'Excluded_QA' AND classification_status = 'Waiting'")->row_array()['row_count'];
        }

        run_test($this->controller, $action, $test_name, $test_nbrOfExclusions, $expected_nbrOfExclusions, $actual_nbrOfExclusions);
    }

    /*
     * Test 37
     * Action : qa_validate
     * Description : perform the validation of a quality assessment for a specific paper as Correct QA.
     * Expected update in DB : In the qa_validation_assignment table, the validation field become "Correct"
     */
    private function qaValidate_setCcorrect()
    {
        $action = "qa_validate";
        $test_name = "Perform the validation of a quality assessment for a specific paper as Correct QA.";
        $test_paperUpdate = "Paper validation";
        $expected_paperUpdate = "Correct";

        //assign papers for QA Validation
        $postData = ["number_of_users" => 1, "percentage" => 100, "user_1" => getAdminUserId()];
        $response = $this->http_client->response($this->controller, "qa_validation_assignment_save", $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_paperUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $getApaperId = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper ORDER BY id DESC LIMIT 1")->row_array()['id'];
            $response = $this->http_client->response($this->controller, $action . "/" . $getApaperId);

            $actual_paperUpdate = $this->ci->db->query("SELECT validation FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment WHERE paper_id = " . $getApaperId)->row_array()['validation'];
        }

        run_test($this->controller, $action, $test_name, $test_paperUpdate, $expected_paperUpdate, $actual_paperUpdate);
    }

    /*
     * Test 38
     * Action : qa_validate
     * Description : perform the validation of a quality assessment for a specific paper as not correct QA.
     * Expected update in DB : In the qa_validation_assignment table, the validation field become "Not Correct"
     */
    private function qaValidate_setNotCcorrect()
    {
        $action = "qa_validate";
        $test_name = "Perform the validation of a quality assessment for a specific paper as not correct QA.";
        $test_paperUpdate = "Paper validation";
        $expected_paperUpdate = "Not Correct";

        $getApaperId = $this->ci->db->query("SELECT id FROM relis_dev_correct_" . getProjectShortName() . ".paper GROUP BY id DESC LIMIT 1")->row_array()['id'];
        $response = $this->http_client->response($this->controller, $action . "/" . $getApaperId . "/0");

        if ($response['status_code'] >= 400) {
            $actual_paperUpdate = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_paperUpdate = $this->ci->db->query("SELECT validation FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment WHERE paper_id = " . $getApaperId)->row_array()['validation'];
        }

        run_test($this->controller, $action, $test_name, $test_paperUpdate, $expected_paperUpdate, $actual_paperUpdate);
    }

    /*
     * Test 39
     * Action : qa
     * Description : handles various aspects related to the QA perspective.
     * Expected HTTP Response Code : 200
     */
    private function qa()
    {
        $action = "qa";
        $test_name = "handles various aspects related to the QA perspective";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }
}