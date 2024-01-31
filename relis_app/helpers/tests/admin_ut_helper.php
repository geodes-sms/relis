<?php

// TEST ADMIN CONTROLLER
class AdminUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "admin";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        //Login as admin
        $this->http_client->response("user", "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        $this->listConfigurations();
        $this->createTablesConfig();
        $this->describeConfig();
        $this->createViews();
        $this->createStoredProcedures();
    }

    /*
     * Test 1
     * Action : list_configurations
     * Description : display a list of configurations and provide options for managing them, allowing administrators to perform actions such as creating tables, generating stored procedures, and creating views for different configurations.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function listConfigurations()
    {
        $action = "list_configurations";
        $test_name = "display a list of configurations and provide options for managing them, allowing administrators to perform actions such as creating tables, generating stored procedures, and creating views for different configurations";
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
     * Action : create_tables_config
     * Description : automate the process of creating database tables for a specific configuration entity, based on its table configuration.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function createTablesConfig()
    {
        $action = "create_tables_config";
        $test_name = "automate the process of creating database tables for a specific configuration entity, based on its table configuration";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/users");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 3
     * Action : describe_config
     * Description : display the description of a specific configuration entity.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function describeConfig()
    {
        $action = "describe_config";
        $test_name = "display the description of a specific configuration entity";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/users");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 4
     * Action : create_views
     * Description : provide a convenient way for administrators to create views associated with an entity configuration.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function createViews()
    {
        $action = "create_views";
        $test_name = "provide a convenient way for administrators to create views associated with an entity configuration";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/users");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 5
     * Action : create_stored_procedures
     * Description : automate the process of generating stored procedures for CRUD (Create, Read, Update, Delete) operations associated with an entity configuration.
     * Expected HTTP Response Code : 200 OK (indicating a successful response from the server).
     */
    private function createStoredProcedures()
    {
        $action = "create_stored_procedures";
        $test_name = "automate the process of generating stored procedures for CRUD (Create, Read, Update, Delete) operations associated with an entity configuration";
        $test_aspect = "Http response code";
        $expected_value = http_code()[200];
        $response = $this->http_client->response($this->controller, $action . "/users");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }
}