<?php

// TEST HOME CONTROLLER
class HomeUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "home";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->homeIndex_projectDb_default();
        $this->homeIndex_projectDb_notDefault();
        $this->sqlQuery();
        $this->sqlMultiQuery();
        $this->sqlQueryResponse();
        $this->sqlSelectQueryResponse();
        $this->sqlMultiQueryResponse();
        $this->sqlMultiQuery_wrongDelimiter();
        $this->export();
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
        createDemoProject();
    }

    /*
     * Test 1
     * Action : index
     * Description : Navigate to index action when project_db userdata = default.
     * Expected result : the user is redirected to project/projects_list
     */
    private function homeIndex_projectDb_default()
    {
        $action = "index";
        $test_name = "Navigate to index action when project_db userdata = default";
        $test_redirectUrl = "Redirected URL";
        $expected_redirectUrl = "project/projects_list";

        //add userdata project_db = default
        $this->http_client->addUserData('project_db', 'default', "relis_session");

        //Navigate to index action
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_redirectUrl = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_redirectUrl = $this->http_client->getShortUrl($response['url']);
        }

        run_test($this->controller, $action, $test_name, $test_redirectUrl, $expected_redirectUrl, $actual_redirectUrl);
    }

    /*
     * Test 2
     * Action : index
     * Description : Navigate to index action when project_db userdata != default.
     * Expected result : the user is redirected to index
     */
    private function homeIndex_projectDb_notDefault()
    {
        $action = "index";
        $test_name = "Navigate to index action when project_db userdata != default";
        $test_redirectUrl = "Redirected URL";
        $expected_redirectUrl = "home/index";

        //add userdata project_db = abc
        $this->http_client->addUserData('project_db', 'abc', "relis_session");

        //Navigate to index action
        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_redirectUrl = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_redirectUrl = $this->http_client->getShortUrl($response['url']);
        }

        run_test($this->controller, $action, $test_name, $test_redirectUrl, $expected_redirectUrl, $actual_redirectUrl);
    }

    /*
     * Test 3
     * Action : sql_query
     * Description : Display text field to enter single sql query to query the database.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function sqlQuery()
    {
        $action = "sql_query";
        $test_name = "Display text field to enter single sql query to query the database";
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
     * Test 4
     * Action : sql_query
     * Description : Display text field to enter multi sql query to query the database.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function sqlMultiQuery()
    {
        $action = "sql_query";
        $test_name = "Display text field to enter multi sql query to query the database";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/multi");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 5
     * Action : sql_query_response
     * Description : Enter and execute a single SQL query.
     * Expected result : check the DB update from the executed query
     */
    private function sqlQueryResponse()
    {
        $action = "sql_query_response";
        $test_name = "Enter and execute a single SQL query";
        $test_updatedDB = 'Is "TestTable" created?';
        $expected_updatedDB = 'Yes';

        $postData = ["query_type" => "single", "return_table" => "on", "sql_field" => "CREATE TABLE relis_dev_correct_" . getProjectShortName() . ".TestTable(id INT)"];
        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual__updatedDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual__updatedDB = $this->ci->db->query("SELECT table_name FROM information_schema.tables WHERE table_name = 'TestTable' AND table_schema = 'relis_dev_correct_" . getProjectShortName() . "'")->row_array()['table_name'];
            $actual__updatedDB = (strtolower($actual__updatedDB) == strtolower("TestTable")) ? 'Yes' : 'No';
        }

        run_test($this->controller, $action, $test_name, $test_updatedDB, $expected_updatedDB, $actual__updatedDB);
    }

    /*
     * Test 6
     * Action : sql_query_response
     * Description : Enter and execute a SQL SELECT query.
     * Expected result : check if the query outputs the correct data
     */
    private function sqlSelectQueryResponse()
    {
        $action = "sql_query_response";
        $test_name = "Enter and execute a SQL SELECT query";
        $test_data = 'is correct data displayed?';
        $expected_data = 'Yes';
        $actual_data = "No";

        $postData = [
            'query_type' => 'single',
            "return_table" => "on",
            'sql_field' => 'SELECT * FROM users'
        ];

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual_data = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $userData = $this->ci->db->query("SELECT * FROM users")->result_array();

            foreach ($userData as $user) {
                if (strstr($response['content'], $user['user_name']) == false || strstr($response['content'], $user['user_username']) == false || strstr($response['content'], $user['user_mail']) == false) {
                    $actual_data = "No";
                    break;
                }
                $actual_data = "Yes";
            }
        }

        run_test($this->controller, $action, $test_name, $test_data, $expected_data, $actual_data);
    }

    /*
     * Test 7
     * Action : sql_query_response
     * Description : Enter and execute a Multi SQL query.
     * Expected result : check the DB update from the executed queries
     */
    private function sqlMultiQueryResponse()
    {
        $action = "sql_query_response";
        $test_name = "Enter and execute a Multi SQL query";
        $test_updatedDB = 'Are queries executed?';
        $expected_updatedDB = 'Yes';

        $postData = [
            'query_type' => 'multi',
            'delimiter' => ';',
            'sql_field' => 'CREATE TABLE relis_dev_correct_demoTestProject.TestTable2(id INT); CREATE TABLE relis_dev_correct_demoTestProject.TestTable3(id INT)',
        ];

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual__updatedDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $query1 = $this->ci->db->query("SELECT table_name FROM information_schema.tables WHERE table_name = 'TestTable2' AND table_schema = 'relis_dev_correct_" . getProjectShortName() . "'")->row_array()['table_name'];
            $query2 = $this->ci->db->query("SELECT table_name FROM information_schema.tables WHERE table_name = 'TestTable3' AND table_schema = 'relis_dev_correct_" . getProjectShortName() . "'")->row_array()['table_name'];
            $actual__updatedDB = (strtolower($query1) == "testtable2" && strtolower($query2) == "testtable3") ? 'Yes' : 'No';
        }

        run_test($this->controller, $action, $test_name, $test_updatedDB, $expected_updatedDB, $actual__updatedDB);
    }

    /*
     * Test 8
     * Action : sql_query_response
     * Description : Enter and execute a Multi SQL query with wrong delimiter.
     * Expected result : check the DB update from the executed queries
     */
    private function sqlMultiQuery_wrongDelimiter()
    {
        $action = "sql_query_response";
        $test_name = "Enter and execute a Multi SQL query with wrong delimiter";
        $test_updatedDB = 'Are queries executed?';
        $expected_updatedDB = 'No';

        $postData = [
            'query_type' => 'multi',
            'delimiter' => ':',
            'sql_field' => 'CREATE TABLE relis_dev_correct_demoTestProject.TestTable4(id INT); CREATE TABLE relis_dev_correct_demoTestProject.TestTable5(id INT)',
        ];

        $response = $this->http_client->response($this->controller, $action, $postData, "POST");

        if ($response['status_code'] >= 400) {
            $actual__updatedDB = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $query1 = $this->ci->db->query("SELECT table_name FROM information_schema.tables WHERE table_name = 'TestTable4' AND table_schema = 'relis_dev_correct_" . getProjectShortName() . "'")->row_array();
            $query2 = $this->ci->db->query("SELECT table_name FROM information_schema.tables WHERE table_name = 'TestTable5' AND table_schema = 'relis_dev_correct_" . getProjectShortName() . "'")->row_array();
            $actual__updatedDB = (empty($query1) && empty($query2)) ? 'No' : 'Yes';
        }

        run_test($this->controller, $action, $test_name, $test_updatedDB, $expected_updatedDB, $actual__updatedDB);
    }

    /*
     * Test 9
     * Action : export
     * Description : displays the export page, allowing users to export data in different formats.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function export()
    {
        $action = "export";
        $test_name = "displays the export page, allowing users to export data in different formats";
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
}