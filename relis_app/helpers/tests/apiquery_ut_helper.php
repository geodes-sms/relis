<?php

// TEST ELEMENT CONTROLLER
class ApiQueryUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "apiquery";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->sqlSelectQuery();
    }

    private function TestInitialize()
    {
        //delete generated userdata session files
        deleteSessionFiles();
        //delete created test user
        deleteCreatedTestUser();
        //delete created demoProject
        deleteCreatedTestProject();
        //create test user
        addTestUser();
        //Login as admin
        $this->http_client->response("user", "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
    }

    /*
     * Test 1
     * Action : run
     * Description : Execute a SQL select query.
     * Expected result : check if the query outputs the correct data
     */
    private function sqlSelectQuery()
    {
        $action = "run";
        $test_name = "Execute a SQL select query";
        $test_data = 'is correct data displayed?';

        $sql = 'SELECT * FROM config';
        $expected_data = json_encode($this->ci->db->query($sql)->result_array());

        $response = $this->http_client->response($this->controller, $action . '?sql=' . urlencode($sql));

        if ($response['status_code'] >= 400) {
            $actual_data = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_data = $response['content'];
        }

        run_test($this->controller, $action, $test_name, $test_data, $expected_data, $actual_data);
    }
}



