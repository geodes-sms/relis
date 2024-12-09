<?php

// TEST ELEMENT CONTROLLER
class ManagerUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "manager";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->displayStrEntry();
        $this->edit_info();

        $this->projectInitialize();
        $this->listProjectConfig();
        $this->displayUserprojectDetail();
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
        $this->add_screen_phase();
        $this->add_user();
        $this->add_user_current_project();
        $this->add_venue();
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
        $this->update_screening_config();
        $this->update_qa_config();
        $this->update_class_config();
        $this->cancel_operation();
        $this->undo_cancel_operation();
        $this->clear_logs();
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
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
     * Test 2
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 3
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = "Yes";
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 4
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
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
     * Test 5
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 6
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 7
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 8
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 9
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 10
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 11
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 12
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 13
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 14
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 15
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 16
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 17
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 18
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 19
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 20
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 21
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 22
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 23
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 24
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 25
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 26
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 27
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 28
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 29
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 30
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 31
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 32
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 33
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 34
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 35
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[200];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 36
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
            'assign_to_non_screened_validator_on' => array(0, 1),
            'validation_default_percentage' => 50
        );

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = '1';
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 37
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = '1';
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 38
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

        //follow redirect
        while (in_array($response['status_code'], [301, 302, 303,307])) {
            $response = $this->http_client->response($this->http_client->getShortUrl($response['url']), "");
        }

        if ($response['status_code'] != 200) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = '1';
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 39
     * Action : cancel_operation
     * Description : cancel operation and update the operation state in the database
     * Expected operation state in DB: Cancelled
     */
    private function cancel_operation()
    {
        $action = "cancel_operation";
        $test_name = "Cancel operation and update the operation state in the database";
        $test_aspect_operationState = "Operation state in DB";
        $expected_operationState = "Cancelled";

        $response = $this->http_client->response($this->controller, $action . "/3");

        if ($response['status_code'] >= 400) {
            $actual_operationState = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_operationState = $this->ci->db->query("SELECT operation_state FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_id =3")->row_array()['operation_state'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_operationState, $expected_operationState, $actual_operationState);
    }

    /*
     * Test 40
     * Action : undo_cancel_operation
     * Description : undo cancel operation and update the operation state in the database
     * Expected operation state in DB: Active
     */
    private function undo_cancel_operation()
    {
        $action = "undo_cancel_operation";
        $test_name = "Undo cancel operation and update the operation state in the database";
        $test_aspect_operationState = "Operation state in DB";
        $expected_operationState = "Active";

        $response = $this->http_client->response($this->controller, $action . "/3");

        if ($response['status_code'] >= 400) {
            $actual_operationState = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_operationState = $this->ci->db->query("SELECT operation_state FROM relis_dev_correct_" . getProjectShortName() . ".operations WHERE operation_id =3")->row_array()['operation_state'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect_operationState, $expected_operationState, $actual_operationState);
    }

    /*
     * Test 41
     * Action : clear_logs
     * Description : clear log entries
     * Expected log states in DB: 0
     */
    private function clear_logs()
    {
        $action = "clear_logs";
        $test_name = "clear log entries";
        $test_aspect_logStates = "Log states in DB";
        $expected_logStates = "Cleared";
        $actual_logStates = "Not cleared";

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_logStates = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $states = $this->ci->db->query("SELECT log_active FROM log WHERE log_active =1")->result_array();

            if (empty($states)) {
                $actual_logStates = "Cleared";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect_logStates, $expected_logStates, $actual_logStates);
    }
}



