<?php

// TEST PROJECT CONTROLLER
class ProjectUnitTest
{
    private $controller;
    private $http_client;
    private $ci;

    function __construct()
    {
        $this->controller = "project";
        $this->http_client = new Http_client();
        $this->ci = get_instance();
    }

    function run_tests()
    {
        $this->accessProjectControllerWithoutLogin();
        $this->TestInitialize();
        $this->newProjectForm();
        $this->newProjectForm_fromRelisEditor();
        $this->saveNewProject_notPhpFile();
        $this->saveNewProject_validInstallationFile();
        $this->saveNewProject_existingLabel();
        $this->projectList_2projectsInstalled();
        $this->projectList_3projectsInstalled();
        $this->remove_project();
        $this->saveNewProjectFromRelisEditor_notPhpFile();
        $this->saveNewProjectFromRelisEditor_validInstallationFile();
        $this->saveNewProjectFromRelisEditor_existingLabel();
        $this->set_project();
        $this->set_project2();
        $this->publishProject();
        $this->reopenProject();
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
    }

    /*
     * Test 1
     * Action : index
     * Description : Access project controller without login.
     * Scenario : 
     *      -  The project controller can't be accessed without login, 
     *      -  if the user is not logged in they must be redirected to user/index page with response code 307 Temporary Redirect.
     */
    private function accessProjectControllerWithoutLogin()
    {
        $this->http_client->unsetCookie('relis_session');
        $action = "index";
        $test_name = "Access project controller without login";
        $test_aspect = "http response code";
        $expected_value = http_code()[307];

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
     * Action : new_project
     * Description : Test the rendering of the form for adding a new project.
     */
    private function newProjectForm()
    {
        $action = "new_project";
        $test_name = "Display new project form";
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
     * Test 3
     * Action : new_project_editor
     * Description : Test the rendering of the form for adding a new project from ReLiS Editor.
     */
    private function newProjectForm_fromRelisEditor()
    {
        $action = "new_project_editor";
        $test_name = "Display new project form from ReLiS editor";
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
     * Action : save_new_project
     * Description : Add a new project with an installation file which is not a .php file.
     * Scenario : When the user add a new project, the project should not be created in the database
     * Expected projects table last ID: the projects table last project ID should be the same before and after the test
     */
    private function saveNewProject_notPhpFile()
    {
        $action = "save_new_project";
        $test_name = "Add a new project with an installation file which is not a .php file";
        $test_aspect = "Table last ID";
        $expected_value = $this->ci->db->query("SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1")->row_array()['project_id'];
        $filePath = 'relis_app/helpers/tests/testFiles/project/classification_install_' . getProjectShortName() . '.pdf';

        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'install_config', 'filePath' => $filePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1")->row_array()['project_id'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 5
     * Action : save_new_project
     * Description : Add a new project with a valid project installation configuration file.
     * Scenario : When the user add a new project, a new database should be created representing the new project
     * Expected result : "Project created"
     */
    private function saveNewProject_validInstallationFile()
    {
        $action = "save_new_project";
        $test_name = "Add a new project with a valid project installation configuration file";
        $test_aspect = "Created database for the new project";
        $filePath = getProjectPath();
        $expected_value = "Created";
        $actual_value = "Not created";

        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'install_config', 'filePath' => $filePath], "POST");

        $url = $this->http_client->getShortUrl($response['url']);
        if ($response["status_code"] == http_code()[303] && $url == "project/save_new_project_part2/" . getProjectShortName()) {
            $response = $this->http_client->response($url, "");
        }

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //check if the new project is inserted in the projects table in the relis_db database
            $projectResult = $this->ci->db->query("SELECT project_id from projects where project_label LIKE '" . getProjectShortName() . "'")->row_array();
            //check if a new database is created for the project
            $dbResult = $this->ci->db->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'relis_dev_correct_" . getProjectShortName() . "'")->result_array();

            if (!empty($projectResult) && !empty($dbResult)) {
                $actual_value = "Created";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 6
     * Action : save_new_project
     * Description : Add a new project with an already used project label (project_short_name).
     * Scenario : When the user add a new project with an existing project label, the project should not be created in the database
     * Expected projects table last ID: the projects table last project ID should be the same before and after the test
     */
    private function saveNewProject_existingLabel()
    {
        $action = "save_new_project";
        $test_name = "Add a new project with an already used project label";
        $test_aspect = "Table last ID";
        $expected_value = $this->ci->db->query("SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1")->row_array()['project_id'];

        $filePath = getProjectPath();
        $response = $this->http_client->response($this->controller, $action, ['fileFieldName' => 'install_config', 'filePath' => $filePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1")->row_array()['project_id'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 7
     * Action : projects_list
     * Description : Display the list of installed projects when 2 projects are installed.
     * Expected result: check if the projects list returned is correct
     */
    private function projectList_2projectsInstalled()
    {
        $action = "projects_list";
        $test_name = "Display the list of installed projects when 2 projects are installed";
        $test_aspect = "Installed project(s)";
        $expected_value = '{"nombre":2,"list":[{"project_id":"' . getProjectDetails('demo_relis')['project_id'] . '","project_label":"demo_relis","project_title":"Demo ReLiS","project_description":"Demo ReLiS","project_creator":"1","project_icon":null,"creation_time":"' . getProjectDetails('demo_relis')['creation_time'] . '","project_public":"0","project_active":"1"},{"project_id":"' . getProjectDetails()['project_id'] . '","project_label":"demoTestProject","project_title":"Demo Test Project","project_description":"Demo Test Project","project_creator":"1","project_icon":null,"creation_time":"' . getProjectDetails()['creation_time'] . '","project_public":"0","project_active":"1"}]}';

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //get the list of installed projects
            $ci = get_instance();
            $ref_table_config = get_table_configuration("project");
            $ref_table_config['current_operation'] = 'list_projects';
            $projectsList = $ci->DBConnection_mdl->get_list_mdl($ref_table_config);

            $actual_value = json_encode($projectsList);
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 8
     * Action : projects_list
     * Description : Display the list of installed projects when more than 1 project is installed.
     * Expected result: check if the projects list returned is correct
     */
    private function projectList_3projectsInstalled()
    {
        $action = "projects_list";
        $test_name = "Display the list of installed projects when more than 1 project is installed";
        $test_aspect = "Installed project(s)";

        //install second project
        createDemoProject("demoTestProject2");
        $expected_value = '{"nombre":3,"list":[{"project_id":"' . getProjectDetails('demo_relis')['project_id'] . '","project_label":"demo_relis","project_title":"Demo ReLiS","project_description":"Demo ReLiS","project_creator":"1","project_icon":null,"creation_time":"' . getProjectDetails('demo_relis')['creation_time'] . '","project_public":"0","project_active":"1"},{"project_id":"' . getProjectDetails()['project_id'] . '","project_label":"demoTestProject","project_title":"Demo Test Project","project_description":"Demo Test Project","project_creator":"1","project_icon":null,"creation_time":"' . getProjectDetails()['creation_time'] . '","project_public":"0","project_active":"1"},{"project_id":"' . getProjectDetails('demoTestProject2')['project_id'] . '","project_label":"demoTestProject2","project_title":"Demo Test Project","project_description":"Demo Test Project","project_creator":"1","project_icon":null,"creation_time":"' . getProjectDetails('demoTestProject2')['creation_time'] . '","project_public":"0","project_active":"1"}]}';

        $response = $this->http_client->response($this->controller, $action);

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //get the list of installed projects
            $ci = get_instance();
            $ref_table_config = get_table_configuration("project");
            $ref_table_config['current_operation'] = 'list_projects';
            $projectsList = $ci->DBConnection_mdl->get_list_mdl($ref_table_config);

            $actual_value = json_encode($projectsList);
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);

        //delete created second Project
        deleteCreatedTestProject("demoTestProject2");
    }

    /*
     * Test 9
     * Action : remove_project
     * Description : Remove installed project.
     * Expected result : "Project removed"
     */
    private function remove_project()
    {
        $action = "remove_project";
        $test_name = "Remove installed project";
        $test_aspect = "Removed project in the database";
        $expected_value = 'Removed';
        $actual_value = 'Not removed';

        $response = $this->http_client->response($this->controller, $action . "/" . getProjectId());

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //check if the project is uninstalled in the projects table in the relis_db database
            $project_active = $this->ci->db->query("SELECT project_active from projects where project_id LIKE '" . getProjectId() . "'")->row_array()['project_active'];
            //check if the project database is removed
            $dbResult = $this->ci->db->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'relis_dev_correct_" . getProjectShortName() . "'")->result_array();

            if ($project_active == 0 && empty($dbResult)) {
                $actual_value = "Removed";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 10
     * Action : save_new_project_editor
     * Description : Add a new project from relis editor with an installation file which is not a .php file.
     * Scenario : When the user add a new project, the project should not be created in the database
     * Expected projects table last ID: the projects table last project ID should be the same before and after the test
     */
    private function saveNewProjectFromRelisEditor_notPhpFile()
    {
        $this->TestInitialize();

        $action = "save_new_project_editor";
        $test_name = "Add a new project from relis editor with an installation file which is not a .php file";
        $test_aspect = "Table last ID";
        $expected_value = $this->ci->db->query("SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1")->row_array()['project_id'];
        $filePath = 'tests/classification_install_' . getProjectShortName() . '.pdf';

        $response = $this->http_client->response($this->controller, $action, ['selected_config' => $filePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1")->row_array()['project_id'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 11
     * Action : save_new_project_editor
     * Description : Add a new project from relis editor with a valid project installation configuration file.
     * Scenario : When the user add a new project, a new database should be created representing the new project
     * Expected result : "Project created"
     */
    private function saveNewProjectFromRelisEditor_validInstallationFile()
    {
        $action = "save_new_project_editor";
        $test_name = "Add a new project from relis editor with a valid project installation configuration file";
        $test_aspect = "Created database for the new project";
        $expected_value = "Created";
        $actual_value = "Not created";
        $filePath = 'tests/classification_install_' . getProjectShortName() . '.php';

        $response = $this->http_client->response($this->controller, $action, ['selected_config' => $filePath], "POST");

        $url = $this->http_client->getShortUrl($response['url']);
        if ($response["status_code"] == http_code()[303] && $url == "project/save_new_project_part2/" . getProjectShortName()) {
            $response = $this->http_client->response($url, "");
        }

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            //check if the new project is inserted in the projects table in the relis_db database
            $projectResult = $this->ci->db->query("SELECT project_id from projects where project_label LIKE '" . getProjectShortName() . "'")->row_array();
            //check if a new database is created for the project
            $dbResult = $this->ci->db->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'relis_dev_correct_" . getProjectShortName() . "'")->result_array();

            if (!empty($projectResult) && !empty($dbResult)) {
                $actual_value = "Created";
            }
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 12
     * Action : save_new_project_editor
     * Description : Add a new project from relis editor with an already used project label (project_short_name).
     * Scenario : When the user add a new project with an existing project label, the project should not be created in the database
     * Expected projects table last ID: the projects table last project ID should be the same before and after the test
     */
    private function saveNewProjectFromRelisEditor_existingLabel()
    {
        $action = "save_new_project_editor";
        $test_name = "Add a new project from relis editor with an already used project label";
        $test_aspect = "Table last ID";
        $expected_value = $this->ci->db->query("SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1")->row_array()['project_id'];
        $filePath = 'tests/classification_install_' . getProjectShortName() . '.php';

        $response = $this->http_client->response($this->controller, $action, ['selected_config' => $filePath], "POST");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1")->row_array()['project_id'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 13
     * Action : set_project
     * Description : Set the project as active project in the user session.
     * Expected userdata(project_db) session: demoTestProject
     */
    private function set_project()
    {
        $action = "set_project";
        $test_name = "Set the project as active project in the user session";
        $test_aspect = "Project label session data";
        $expected_value = 'demoTestProject';

        $response = $this->http_client->response($this->controller, $action . "/" . getProjectId());

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('project_db');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 14
     * Action : set_project2
     * Description : switch and update the active project in the session
     * Expected userdata(project_db) session: demoTestProject
     */
    private function set_project2()
    {
        $action = "set_project2";
        $test_name = "Switch and update the active project in the session";
        $test_aspect = "Project label session data";
        $expected_value = 'demoTestProject';

        $response = $this->http_client->response($this->controller, $action . "/" . getProjectShortName() . "/" . getProjectId());

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->http_client->readUserdata('project_db');
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 15
     * Action : publish_project
     * Description : Publish Project.
     * Expected "project_public" field in the projects table : 1
     */
    private function publishProject()
    {
        $action = "publish_project";
        $test_name = "Publish project";
        $test_aspect = "project_public field in the projects table";
        $expected_value = '1';

        $response = $this->http_client->response($this->controller, $action . "/" . getProjectId() . "/1");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT project_public from projects where project_label LIKE '" . getProjectShortName() . "'")->row_array()['project_public'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }

    /*
     * Test 16
     * Action : publish_project
     * Description : Reopen Project.
     * Expected "project_public" field in the projects table : 0
     */
    private function reopenProject()
    {
        $action = "publish_project";
        $test_name = "Reopen project";
        $test_aspect = "project_public field in the projects table";
        $expected_value = '0';

        $response = $this->http_client->response($this->controller, $action . "/" . getProjectId() . "/0");

        if ($response['status_code'] >= 400) {
            $actual_value = "<span style='color:red'>" . $response['content'] . "</span>";
        } else {
            $actual_value = $this->ci->db->query("SELECT project_public from projects where project_label LIKE '" . getProjectShortName() . "'")->row_array()['project_public'];
        }

        run_test($this->controller, $action, $test_name, $test_aspect, $expected_value, $actual_value);
    }
}
