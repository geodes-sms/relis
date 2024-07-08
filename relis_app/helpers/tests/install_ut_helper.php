<?php

// TEST INSTALL CONTROLLER
class InstallUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "install";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->TestInitialize();
        $this->reLisEditorPage_normalUser();
        $this->reLisEditorPageAsAdmin();
        $this->installForm();
        $this->installFormEditor();
        $this->updateProject_changeTitle();
        $this->updateProject_changeShortNameAndTitle();
        $this->updateProjectFromEditor_changeTitle();
        $this->updateProjectFromEditor_changeShortNameAndTitle();
    }

    private function TestInitialize()
    {
        //delete generated userdata session files
        deleteSessionFiles();
        //delete created test user
        deleteCreatedTestUser();
        //create test user
        addTestUser();
        //delete created test Project
        deleteCreatedTestProject();
        //Login as admin user
        $this->http_client->response("user", "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");
        createDemoProject();
    }

    /*
     * Test 1
     * Action : relis_editor
     * Description : display the ReLiS editor page as normal user.
     * Expected http code : 200
     */
    private function reLisEditorPage_normalUser()
    {
        $action = "relis_editor";
        $test_name = "Display the ReLiS editor page as normal user";
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
     * Test 2
     * Action : relis_editor
     * Description : display the ReLiS editor page as admin user.
     * Expected http code : 200
     */
    private function reLisEditorPageAsAdmin()
    {
        $action = "relis_editor";
        $test_name = "Display the ReLiS editor page as admin";
        $test_httpCode = "Http response code";
        $expected_httpCode = http_code()[200];

        $response = $this->http_client->response($this->controller, $action . "/admin");

        if ($response['status_code'] >= 400) {
            $actual_httpCode = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_httpCode = http_code()[$response['status_code']];
        }

        run_test($this->controller, $action, $test_name, $test_httpCode, $expected_httpCode, $actual_httpCode);
    }

    /*
     * Test 3
     * Action : install_form
     * Description : display the installation form, allowing the user to upload the configuration file to update the project.
     * Expected http code : 200
     */
    private function installForm()
    {
        $action = "install_form";
        $test_name = "Display the installation form, allowing the user to upload the configuration file to update the project";
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
     * Test 4
     * Action : install_form_editor
     * Description : display the installation form with editor-related options allowing the user to update the project.
     * Expected http code : 200
     */
    private function installFormEditor()
    {
        $action = "install_form_editor";
        $test_name = "Display the installation form with editor-related options allowing the user to update the project.";
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
     * Test 5
     * Action : save_install_form
     * Description : Update a project from an updated project installation configuration file (update project title).
     * Expected result : Project title updated in database
     */
    private function updateProject_changeTitle()
    {
        $action = "save_install_form";
        $test_name = "Update a project from an updated project installation configuration file (update project title)";
        $test_updatedTitle = "updated project title in DB";
        $expected_updatedTitle = "Demo Test Project update";

        $updatedfilePath = 'relis_app/helpers/tests/testFiles/project/classification_install_' . getProjectShortName() . '_UpdateTitle.php';
        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'install_config', 'filePath' => $updatedfilePath], "POST");

        $url = $this->http_client->getShortUrl($response['url']);

        if ($url == "install/save_install_form_part2") {
            $response = $this->http_client->response($url, "");
        }

        if ($response['status_code'] >= 400) {
            $actual_updatedTitle = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //check if the project title is updated in the projects table in the relis_db database
            $projectTitle = $this->ci->db->query("SELECT project_title from projects where project_label = '" . getProjectShortName() . "'")->row_array()['project_title'];
            $actual_updatedTitle = $projectTitle;
        }

        run_test($this->controller, $action, $test_name, $test_updatedTitle, $expected_updatedTitle, $actual_updatedTitle);
    }

    /*
     * Test 6
     * Action : save_install_form
     * Description : Update a project from an updated project installation configuration file (update project_short_name and title).
     * Expected result : DB not updated because project_short_name can't be changed
     */
    private function updateProject_changeShortNameAndTitle()
    {
        $action = "save_install_form";
        $test_name = "Update a project from an updated project installation configuration file (update project_short_name and title)";
        $test_shortNameAndTitle = "Project shortname and title after update (same before update, DB not updated because project_short_name can't be changed)";
        $expected_shortNameAndTitle = json_encode($this->ci->db->query("SELECT project_label, project_title from projects where project_label = '" . getProjectShortName() . "'")->row_array());

        $updatedfilePath = 'relis_app/helpers/tests/testFiles/project/classification_install_' . getProjectShortName() . '_UpdateTitleAndShortName.php';
        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'install_config', 'filePath' => $updatedfilePath], "POST");

        $url = $this->http_client->getShortUrl($response['url']);

        if ($url == "install/save_install_form_part2") {
            $response = $this->http_client->response($url, "");
        }

        $actual_shortNameAndTitle = json_encode($this->ci->db->query("SELECT project_label, project_title from projects where project_label = '" . getProjectShortName() . "'")->row_array());

        run_test($this->controller, $action, $test_name, $test_shortNameAndTitle, $expected_shortNameAndTitle, $actual_shortNameAndTitle);
    }

    /*
     * Test 7
     * Action : save_install_form_editor
     * Description : Update a project from editor from an updated project installation configuration file (update project title).
     * Expected result : Project title updated in database
     */
    private function updateProjectFromEditor_changeTitle()
    {
        $action = "save_install_form_editor";
        $test_name = "Update a project from editor from an updated project installation configuration file (update project title)";
        $test_updatedTitle = "updated project title in DB";
        $expected_updatedTitle = "Demo Test Project update from editor";

        $updatedfilePath = 'relis_app/helpers/tests/testFiles/project/classification_install_' . getProjectShortName() . '_UpdateTitleFromEditor.php';
        $response = $this->http_client->response($this->controller, $action, ['selected_config' => $updatedfilePath], "POST");

        $url = $this->http_client->getShortUrl($response['url']);

        if ($url == "install/save_install_form_part2") {
            $response = $this->http_client->response($url, "");
        }

        if ($response['status_code'] >= 400) {
            $actual_updatedTitle = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //check if the project title is updated in the projects table in the relis_db database
            $projectTitle = $this->ci->db->query("SELECT project_title from projects where project_label = '" . getProjectShortName() . "'")->row_array()['project_title'];
            $actual_updatedTitle = $projectTitle;
        }

        run_test($this->controller, $action, $test_name, $test_updatedTitle, $expected_updatedTitle, $actual_updatedTitle);
    }

    /*
     * Test 8
     * Action : save_install_form_editor
     * Description : Update a project from editor from an updated project installation configuration file (update project_short_name and title).
     * Expected result : DB not updated because project_short_name can't be changed
     */
    private function updateProjectFromEditor_changeShortNameAndTitle()
    {
        $action = "save_install_form_editor";
        $test_name = "Update a project from editor from an updated project installation configuration file (update project_short_name and title)";
        $test_shortNameAndTitle = "Project shortname and title after update (same before update, DB not updated because project_short_name can't be changed)";
        $expected_shortNameAndTitle = json_encode($this->ci->db->query("SELECT project_label, project_title from projects where project_label = '" . getProjectShortName() . "'")->row_array());

        $updatedfilePath = 'relis_app/helpers/tests/testFiles/project/classification_install_' . getProjectShortName() . '_UpdateTitleAndShortNameFromEditor.php';
        $response = $this->http_client->response($this->controller, $action, ['selected_config' => $updatedfilePath], "POST");

        $url = $this->http_client->getShortUrl($response['url']);

        if ($url == "install/save_install_form_part2") {
            $response = $this->http_client->response($url, "");
        }

        $actual_shortNameAndTitle = json_encode($this->ci->db->query("SELECT project_label, project_title from projects where project_label = '" . getProjectShortName() . "'")->row_array());

        run_test($this->controller, $action, $test_name, $test_shortNameAndTitle, $expected_shortNameAndTitle, $actual_shortNameAndTitle);
    }
}