<?php

// TEST CONFIG CONTROLLER
class ConfigUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "config";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        // $this->generateConfig();
        $this->updateEditionMode();
    }

    private function TestInitialize()
    {
        //delete generated userdata session files
        deleteSessionFiles();
        //delete generated reporting files
        deleteReportingFiles();
        //delete created test user
        deleteCreatedTestUser();
        //delete created demoProject
        deleteCreatedTestProject();
        //create test user
        addTestUser();
        //Login as admin
        $this->http_client->response("user", "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        //create demoProject
        createDemoProject();
        //add users to test Project
        addUserToProject(getAdminUserId(), "Reviewer");
        addUserToProject(getTestUserId(), "Reviewer");
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
        //perform screening with 4 included papers
        assignPapers_and_performScreening([getAdminUserId(), getTestUserId()], 'Title', -1, 4);
        // //perform QA with 2 low quality papers (1 for each user)
        assignPapers_and_performQA([getAdminUserId(), getTestUserId()], -1, 1);
        // //Exclude low quality papers
        qaExcludeLowQuality();
        //perform classification
        assignPapersForClassification([getAdminUserId(), getTestUserId()]);
        performClassification();
    }

    /*
     * Test 1
     * Action : generate_config
     * Description : function is used to generate the configuration and reference tables for the classification structure.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function generateConfig()
    {
        // error : Table 'relis_db.classification_scheme' doesn't exist select * from classification_scheme WHERE scheme_parent LIKE 'main' AND scheme_active=1 ORDER BY scheme_order ASC Filename: controllers/Config.php Line Number: 94
        $action = "generate_config";
        $test_name = "function is used to generate the configuration and reference tables for the classification structure";
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
     * Test 2
     * Action : update_edition_mode
     * Description : updates the edition mode in the session
     * Expected session data 'language_edit_mode': yes
     */
    private function updateEditionMode()
    {
        $action = "update_edition_mode";
        $test_name = "updates the edition mode in the session";
        $test_userdata = "edition mode session";
        $expected_userdata = "no";

        $response = $this->http_client->response($this->controller, $action . "/no");

        if ($response['status_code'] >= 400) {
            $actual_userdata = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_userdata = $this->http_client->readUserdata('language_edit_mode');
        }

        run_test($this->controller, $action, $test_name, $test_userdata, $expected_userdata, $actual_userdata);
    }
}