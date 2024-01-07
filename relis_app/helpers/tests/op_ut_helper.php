<?php

// TEST ELEMENT CONTROLLER
class OpUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "op";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->screenPerspective();
        $this->qaPerspective();
        $this->classPerspective();
    }

    /*
     * Test 1
     * Action : set_perspective
     * Description : set the working perspective for screening in the session userdata
     * Expected userdata(working_perspective) : screen
     */
    private function screenPerspective()
    {
        $action = "set_perspective";
        $test_name = "Set the working perspective for screening in the session userdata";
        $test_aspect = "User session data";
        $expected_value = "screen";

        $response = $this->http_client->response($this->controller, $action . '/screen');

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('working_perspective');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 2
     * Action : set_perspective
     * Description : set the working perspective for qa in the session userdata
     * Expected userdata(working_perspective) : qa
     */
    private function qaPerspective()
    {
        $action = "set_perspective";
        $test_name = "Set the working perspective for qa in the session userdata";
        $test_aspect = "User session data";
        $expected_value = "qa";

        $response = $this->http_client->response($this->controller, $action . '/qa');

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('working_perspective');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 3
     * Action : set_perspective
     * Description : set the working perspective for classification in the session userdata
     * Expected userdata(working_perspective) : class
     */
    private function classPerspective()
    {
        $action = "set_perspective";
        $test_name = "Set the working perspective for classification in the session userdata";
        $test_aspect = "User session data";
        $expected_value = "class";

        $response = $this->http_client->response($this->controller, $action . '/class');

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('working_perspective');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }   
}



