<?php

// TEST MANAGE CONTROLLER
class ManageUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "manage";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->listeRefPapers();
        $this->listeRefClassification();
        $this->listeRefConfig();
        $this->listeRefSearch();
        $this->listeRefProjects();
        $this->addRefChild();
        $this->addRefDrilldown();
        $this->addClassification();
        $this->newAssignation();
        $this->newExclusion();
        $this->addRef();
        $this->editRef();
        $this->editExclusion(); 
        $this->viewRef();
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
        //add 5 papers to test Project
        addBibtextPapersToProject("relis_app/helpers/tests/testFiles/paper/5_bibPapers.bib");
    }

    /*
     * Test 1
     * Action : liste_ref
     * Description : Afficher la liste des articles.
     */
    private function listeRefPapers()
    {
        $action = "liste_ref";
        $test_name = "Afficher la liste des articles";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/papers");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 2
     * Action : liste_ref
     * Description : Afficher la liste des classifications.
     */
    private function listeRefClassification()
    {
        $action = "liste_ref";
        $test_name = "Afficher la liste des classifications";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/classification");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 3
     * Action : liste_ref
     * Description : Afficher la liste des configurations.
     */
    private function listeRefConfig()
    {
        $action = "liste_ref";
        $test_name = "Afficher la liste des configurations";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/config");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 4
     * Action : liste_ref
     * Description : recherche d'un article.
     */
    private function listeRefSearch()
    {
        $action = "liste_ref";
        $test_name = "recherche d'un article";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/papers/included");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 5
     * Action : liste_ref
     * Description : Afficher la liste des projects.
     */
    private function listeRefProjects()
    {
        $action = "liste_ref";
        $test_name = "Afficher la liste des projects";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/project");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 6
     * Action : add_ref_child
     * Description : function to display the page with a form for adding an element with an external key from the parent element (for example, adding a user from a user group) 
     */
    private function addRefChild()
    {
        $this->TestInitialize();
        $action = "add_ref_child";
        $test_name = "function to display the page with a form for adding an element with an external key from the parent element (for example, adding a user from a user group)";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/classification/class_paper_id/class_paper_id/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 7
     * Action : add_ref_drilldown
     * Description : Function to display the page with a form for adding an element with an external key coming from the child element
     */
    private function addRefDrilldown()
    {
        $action = "add_ref_drilldown";
        $test_name = "Function to display the page with a form for adding an element with an external key coming from the child element";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/classification/class_paper_id/class_paper_id/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 8
     * Action : add_classification
     * Description : Function to display the page with a form for adding a classification
     */
    private function addClassification()
    {
        assignPapers_and_performScreening([getAdminUserId()], 'Title');
        assignPapersForClassification([getAdminUserId()]);

        $action = "add_classification";
        $test_name = "Function to display the page with a form for adding a classification";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 9
     * Action : new_assignation
     * Description : Function to display the page with a form for assigning a paper to a user for classification
     */
    private function newAssignation()
    {
        $action = "new_assignation";
        $test_name = "Function to display the page with a form for assigning a paper to a user for classification";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 10
     * Action : new_exclusion
     * Description : Function to display the page with a form for paper exclusion
     */
    private function newExclusion()
    {
        $action = "new_exclusion";
        $test_name = "Function to display the page with a form for paper exclusion";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 11
     * Action : add_ref
     * Description : function to display the page with a form for adding an element 
     */
    private function addRef()
    {
        $action = "add_ref";
        $test_name = "function to display the page with a form for adding an element";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/classification");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 12
     * Action : edit_ref
     * Description : function to display the page with a form for editing an element 
     */
    private function editRef()
    {
        $action = "edit_ref";
        $test_name = "function to display the page with a form for editing an element";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/user/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 13
     * Action : edit_exclusion
     * Description : function to display the page with a form for editing an paper exclusion 
     */
    private function editExclusion()
    {
        $action = "edit_exclusion";
        $test_name = "function to display the page with a form for editing an paper exclusion";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 14
     * Action : view_ref
     * Description : function to display an element 
     */
    private function viewRef()
    {
        $action = "view_ref";
        $test_name = "function to display an element";
        $test_aspect = "Http response code";
        $expected_value = http_code()[307];
        $response = $this->http_client->response($this->controller, $action . "/papers/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }
}