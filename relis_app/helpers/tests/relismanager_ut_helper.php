<?php

// TEST ELEMENT CONTROLLER
class RelisManagerUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "relis/manager";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();


        $this->projectInitialize();
        $this->editExclusion();
        $this->newExclusion();
        $this->removeExclusion();
        $this->removeAssignation();
        $this->newAssignation();
        $this->edit_assignment_mine();
        $this->edit_assignment_all();
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
     * Action : edit_exclusion
     * Description : display the page with a form for editing paper exclusion
     */
    private function editExclusion()
    {
        $action = "edit_exclusion";
        $test_name = "Display the page with a form for editing paper exclusion";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 2
     * Action : new_exclusion
     * Description : display the page with a form for paper exclusion
     */
    private function newExclusion()
    {
        $action = "new_exclusion";
        $test_name = "Display the page with a form for paper exclusion";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 3
     * Action : remove_exclusion
     * Description : remove an exclusion and including the associated paper
     * Expected result : paper_excluded field in paper table must be 0
     */
    private function removeExclusion()
    {
        $action = "remove_exclusion";
        $test_name = "remove an exclusion and including the associated paper";
        $test_aspect = "Paper_excluded field in paper table";
        $expected_value = "0";
        $actual_value = "1";

        //add exclusion
        $this->ci->Paper_dataAccess->exclude_paper(1);
        $response = $this->http_client->response($this->controller, $action . "/1/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT paper_excluded FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE id = 1")->row_array()['paper_excluded'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 4
     * Action : remove_assignation
     * Description : responsible for removing an assignment from a paper
     * Expected result : assigned_active field in assigned table must be 0 
     */
    private function removeAssignation()
    {
        $action = "remove_assignation";
        $test_name = "responsible for removing an assignment from a paper";
        $test_aspect = "assigned_active field in assigned table";
        $expected_value = "0";
        $actual_value = "1";

        //get assigned paper_id
        $paper_id = $this->ci->db->query("SELECT assigned_paper_id FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_id = 1")->row_array()['assigned_paper_id'];

        $response = $this->http_client->response($this->controller, $action . "/1/" . $paper_id);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT assigned_active FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_id = 1")->row_array()['assigned_active'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 5
     * Action : new_assignation
     * Description : display the page with a form for paper assignation
     */
    private function newAssignation()
    {
        $action = "new_assignation";
        $test_name = "Display the page with a form for paper assignation";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 6
     * Action : edit_assignment_mine
     * Description : display form for editing paper assignment
     */
    private function edit_assignment_mine()
    {
        $action = "edit_assignment_mine";
        $test_name = "display form for editing paper assignment";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/1");

        //follow redirect
        while (in_array($response['status_code'], [http_code()[301], http_code()[302], http_code()[303], http_code()[307]])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != http_code()[200]) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 7
     * Action : edit_assignment_all
     * Description : display form for editing paper assignment for all users
     */
    private function edit_assignment_all()
    {
        $action = "edit_assignment_all";
        $test_name = "display form for editing paper assignment for all users";
        $test_aspect = "Response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/1");

        //follow redirect
        while (in_array($response['status_code'], [http_code()[301], http_code()[302], http_code()[303], http_code()[307]])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != http_code()[200]) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }
}



