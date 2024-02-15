<?php

function run_test($controller, $action, $test_name, $test_aspect, $expected_values, $actual_values, $http_response_code = '-')
{
    $ci = get_instance();
    $ci->unit->run($actual_values, $expected_values, $test_name, $test_aspect, $controller, $action, $http_response_code);
}

###########  USER ##########
function getUser_username_byId($userId)
{
    $ci = get_instance();
    return $ci->db->query("SELECT user_username FROM users WHERE user_id = " . $userId)->row_array()["user_username"];
}

function getUser_username()
{
    return "testUserRelis";
}

function getAdminUserdetails()
{
    $ci = get_instance();
    return $ci->db->query("SELECT * FROM users WHERE user_username = 'admin'")->row_array();
}

function getAdminUserId()
{
    return getAdminUserdetails()["user_id"];
}

function getTestUserdetails()
{
    $ci = get_instance();
    return $ci->db->query("SELECT * FROM users WHERE user_username = '" . getUser_username() . "'")->row_array();
}

function getTestUserConfirmationDetails()
{
    $ci = get_instance();
    return $ci->db->query("SELECT * FROM user_creation WHERE creation_user_id = " . getTestUserId() . "")->row_array();
}

function getTestUserConfirmationCode()
{
    $ci = get_instance();
    return $ci->db->query("SELECT confirmation_code FROM user_creation WHERE creation_user_id = " . getTestUserId() . "")->row_array()["confirmation_code"];
}

function getTestUserId()
{
    return getTestUserdetails()["user_id"];
}

function getDemoUserId()
{
    $ci = get_instance();
    return $ci->db->query("SELECT * FROM users WHERE user_username = 'demo'")->row_array()["user_id"];
}

function addTestUser()
{
    $ci = get_instance();
    $validationCodeValidityLimit = 86400; // (24* 60 * 60) ;

    // Create new user
    $user_name = "christian";
    $user_mail = "123@gmai.com";
    $user_username = getUser_username();

    if (!$ci->user_lib->login_available($user_username)) {
        return;
    }
    $user_array = array(
        'user_name' => $user_name,
        'user_mail' => $user_mail,
        'user_username' => $user_username,
        'user_usergroup' => 2,
        'user_state' => 0,
        'user_password' => md5('123')
    );

    //add user in the db
    $ci->db->insert('users', $user_array);
    $user_id = $ci->db->insert_id();
    $confimation_code = $res = $ci->bm_lib->random_str(12);
    ;
    $ci->db->insert(
        'user_creation',
        array(
            'creation_user_id' => $user_id,
            'confirmation_code' => $confimation_code,
            'confirmation_expiration' => time() + $validationCodeValidityLimit,
            'confirmation_try' => 0,
        )
    );

    //validate account
    $res = $ci->db->get_where(
        'user_creation',
        array('creation_user_id' => getTestUserId(), 'user_creation_active' => 1)
    )
        ->row_array();
    if (!empty($res)) {
        if ($res['confirmation_code'] == getTestUserConfirmationCode()) {
            //Validation success
            //activate user
            $ci->db->update(
                'users',
                array('user_state' => 1),
                array('user_id' => getTestUserId())
            );
            //remove activation user informations
            $ci->db->update(
                'user_creation',
                array('user_creation_active' => 0),
                array('user_creation_id' => $res['user_creation_id'])
            );
        } else {
            $ci->db->update(
                'user_creation',
                array('confirmation_try' => $res['confirmation_try'] + 1),
                array('user_creation_id' => $res['user_creation_id'])
            );
        }
    }
}

function deleteCreatedTestUser()
{
    $ci = get_instance();
    $user_id = getTestUserId();
    //delete inserted test user if exist in the users table 
    $ci->db->query("DELETE FROM users WHERE user_id ='" . $user_id . "'");
    //delete all other inserted test users if exist in the users table 
    $ci->db->query("DELETE FROM users WHERE user_id >= 3");
    //delete inserted test user confirmation 
    $ci->db->query("DELETE FROM user_creation WHERE creation_user_id ='" . $user_id . "'");
}



###########  PROJECT ##########
function getProjectShortName()
{
    return "demoTestProject";
}

function getProjectId($projectName = "demoTestProject")
{
    $ci = get_instance();
    return $ci->db->query("SELECT project_id from projects where project_label LIKE '" . $projectName . "'")->row_array()['project_id'];
}

function getProjectDetails($projectName = "demoTestProject")
{
    $ci = get_instance();
    return $ci->db->query("SELECT * from projects where project_label LIKE '" . $projectName . "'")->row_array();
}

function getProjectPath($projectName = "demoTestProject")
{
    return 'relis_app/helpers/tests/testFiles/project/classification_install_' . $projectName . '.php';
}

function deleteCreatedTestProject($projectName = "demoTestProject")
{
    $ci = get_instance();

    //delete inserted userProject in the userproject table 
    $ci->db->query("DELETE FROM userproject WHERE project_id ='" . getProjectId($projectName) . "'");

    //delete inserted test project in the projects table in the relis_db database
    $ci->db->query("DELETE FROM projects WHERE project_label ='" . $projectName . "'");

    //Delete created test project database
    $ci->db->query("DROP DATABASE IF EXISTS relis_dev_correct_" . $projectName);
}

function createDemoProject($projectName = "demoTestProject")
{
    $ci = get_instance();
    $http_client = new Http_client();
    removeConfigArray($projectName);

    $response = $http_client->response("project", "save_new_project", ['fileFieldName' => 'install_config', 'filePath' => getProjectPath($projectName)], "POST");
    preg_match('/8083\/(.*?)(\.html)?$/', $response['url'], $matches);
    $url = $matches[1];

    if ($response["status_code"] == http_code()[303] && $url == "project/save_new_project_part2/" . $projectName) {
        $http_client->response($url, "");
    }

    //set demo project as active project
    $project_id = $ci->db->query("SELECT project_id from projects where project_label LIKE '" . $projectName . "'")->row_array()['project_id'];
    $http_client->response("project", "set_project/" . $project_id);
}

//Delete demoproject configuration array in relis_app\config\database.php
function removeConfigArray($projectName = "demoTestProject")
{
    $file_path = 'relis_app/config/database.php';
    $config_pattern = '/\$db\[\'' . $projectName . '\'\].*?\);/s';

    // Read the content of the file
    $file_content = file_get_contents($file_path);

    // Remove the specified configuration block
    $file_content = preg_replace($config_pattern, '', $file_content);

    // Remove consecutive blank lines of more than 2 lines
    $lines = explode("\n", $file_content);
    $filtered_lines = array_filter($lines, function ($line) {
        static $emptyLineCount = 0;

        if (trim($line) === '') {
            $emptyLineCount++;
            return $emptyLineCount <= 2;
        }

        $emptyLineCount = 0;
        return true;
    });
    $file_content = implode("\n", $filtered_lines);

    // Write the modified content back to the file
    file_put_contents($file_path, $file_content);
}

function addUserToProject($userId, $role)
{
    $http_client = new Http_client();

    //Add user to test project
    $userData = [
        "operation_type" => "new",
        "table_config" => "user_project",
        "current_operation" => "add_user_current_project",
        "redirect_after_save" => "element/entity_list/list_users_current_projects",
        "operation_source" => "own",
        "child_field" => "",
        "table_config_parent" => "",
        "parent_id" => "",
        "parent_field" => "",
        "parent_table" => "",
        "userproject_id" => "",
        "user_id" => $userId,
        "project_id" => getProjectId(),
        "user_role" => $role,
        "added_by" => getAdminUserdetails()['user_id'],
    ];
    $http_client->response("element", "save_element", $userData, "POST");
}


###########  PAPER ##########
function addBibtextPapersToProject($path)
{
    $ci = get_instance();

    $ci->session->set_userdata('project_db', getProjectShortName());
    $bibtextString = file_get_contents($path);
    $Tpapers = get_bibler_result($bibtextString, "multi_bibtex");
    $paperData = json_encode($Tpapers['paper_array']);
    import_papers_save_bibtext(["data_array" => $paperData, "papers_sources" => ""]);
}

function getCSVdata($filePath)
{
    $array_tab_values = array();
    $fp = fopen($filePath, 'rb');
    $i = 1;
    $last_count = 0;
    while ((($Tline = (fgetcsv($fp, 0, ",", get_ci_config("csv_string_dellimitter")))) !== false) and $i < 11) {
        $Tline = array_map("utf8_encode", $Tline);
        if ($last_count < count($Tline)) {
            $last_count = count($Tline);
        }
        $i++;
    }
    $i = 1;
    rewind($fp);
    while ((($Tline = (fgetcsv($fp, 0, ",", get_ci_config("csv_string_dellimitter")))) !== false)) {
        $Tline = array_map("utf8_encode", $Tline);
        array_push($array_tab_values, $Tline);
        $i++;
    }
    return json_encode($array_tab_values);
}

function getBibtextData($filePath)
{
    $bibtextString = file_get_contents($filePath);
    $Tpapers = get_bibler_result($bibtextString, "multi_bibtex");
    return json_encode($Tpapers['paper_array']);
}



###########  SCREENING ##########
function getScreeningPhaseId($field)
{
    $ci = get_instance();
    return $ci->db->query("SELECT screen_phase_id FROM relis_dev_correct_" . getProjectShortName() . ".screen_phase WHERE phase_title ='" . $field . "' AND screen_phase_active=1")->row_array()['screen_phase_id'];
}

function addScreeningPhase($field)
{
    $http_client = new Http_client();

    $data = [
        'operation_type' => 'new',
        'table_config' => 'screen_phase',
        'current_operation' => 'add_screen_phase',
        'redirect_after_save' => 'element/entity_list/list_screen_phases',
        'operation_source' => 'own',
        'child_field' => '',
        'table_config_parent' => '',
        'parent_id' => '',
        'parent_field' => '',
        'parent_table' => '',
        'screen_phase_id' => '',
        'added_by' => getAdminUserId(),
        'displayed_fields' => 'paper_title,paper_abstract',
        'screen_phase_order' => '',
        'phase_type' => 'Screening',
        'source_paper' => 'Previous phase',
        'source_paper_status' => 'Included',
        'phase_title' => 'Abstract',
        'description' => '',
        'displayed_fields_vals[]' => $field,
        'screen_phase_final' => 0,
    ];

    $http_client->response("screening", "save_phase_screen", $data, "POST");
}

//$done = Nbr of papers to screen; $include = Nbr of papers to include
function screenPapers($screenPhaseField, $done = -1, $reviewPerpaper = 1, $include = -1)
{
    $ci = get_instance();
    $http_client = new Http_client();
    $ci->session->set_userdata('project_db', getProjectShortName());

    $screenPhaseID = getScreeningPhaseId($screenPhaseField);
    //select screening phase as active phase
    $http_client->response("screening", "select_screen_phase" . "/" . $screenPhaseID);

    // 2. Perform screenings
    $nbrOfScreens = $ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening'")->row_array()['row_count'];

    $done = $done * $reviewPerpaper;
    if ($done < 0 || $done > $nbrOfScreens) {
        $done = $nbrOfScreens;
    }

    if ($include < 0 || $include > $done) {
        $include = $done;
    } else {
        $include = $include * $reviewPerpaper;
    }

    for ($i = 1; $i <= $done; $i++) {
        $screening_paper = $ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_id = " . $i . " AND assignment_role = 'Screening'")->row_array();

        if ($i <= $include) {
            //include papers
            $data = ["criteria_ex" => "", "criteria_in" => "", "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "accepted", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
        } else {
            //exclude papers
            $data = ["criteria_ex" => 1, "criteria_in" => "", "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
        }

        save_screening($data);
    }
}

//$done = Nbr of papers to screen; $include = Nbr of papers to include
function assignPapers_and_performScreening($userIds = array(), $screenPhaseField, $done = -1, $include = -1)
{
    $ci = get_instance();
    $http_client = new Http_client();
    $ci->session->set_userdata('project_db', getProjectShortName());

    // 1. Assign papers
    $screenPhaseID = getScreeningPhaseId($screenPhaseField);
    //select screening phase as active phase
    $http_client->response("screening", "select_screen_phase" . "/" . $screenPhaseID);

    $data = [
        "number_of_users" => count($userIds),
        "screening_phase" => $screenPhaseID,
        "papers_sources" => "all",
        "paper_source_status" => "all",
        "reviews_per_paper" => count($userIds)
    ];

    for ($i = 0; $i < count($userIds); $i++) {
        $data["user_" . ($i + 1)] = $userIds[$i];
    }

    save_assignment_screen($data);

    // 2. Perform screenings
    $nbrOfScreens = $ci->db->query("SELECT COUNT(*) AS row_count FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE assignment_role = 'Screening'")->row_array()['row_count'];

    $done = $done * $data["reviews_per_paper"];
    if ($done < 0 || $done > $nbrOfScreens) {
        $done = $nbrOfScreens;
    }

    if ($include < 0 || $include > $done) {
        $include = $done;
    } else {
        $include = $include * $data["reviews_per_paper"];
    }

    for ($i = 1; $i <= $done; $i++) {
        $screening_paper = $ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_id = " . $i . " AND assignment_role = 'Screening'")->row_array();

        if ($i <= $include) {
            //include papers
            $data = ["criteria_ex" => "", "criteria_in" => "", "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "accepted", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
        } else {
            //exclude papers
            $data = ["criteria_ex" => 1, "criteria_in" => "", "note" => "", "screening_id" => $screening_paper['screening_id'], "decision" => "excluded", "operation_type" => "new", "screening_phase" => $screening_paper['screening_phase'], "operation_source" => "list_screen/mine_screen", "paper_id" => $screening_paper['paper_id'], "assignment_id" => $screening_paper['screening_id'], "screen_type" => "simple_screen"];
        }

        save_screening($data);
    }
}



########### QA ##########

function assignPapersForQA($userIds = array())
{
    $postData = ["number_of_users" => count($userIds), "percentage" => 100];
    for ($i = 0; $i < count($userIds); $i++) {
        $postData["user_" . ($i + 1)] = $userIds[$i];
    }
    qa_assignment_save($postData);
}

//$done : number of papers to assess per user; $lowQuality : Nbr of papers to assess with low Quality
function performQA($userIds = array(), $done = -1, $lowQuality = 0)
{
    $qa_results = []; //stores each paperId as key and QA result (high quality, low quality, pending) as value
    $http_client = new Http_client();
    $ci = get_instance();

    for ($i = 0; $i < count($userIds); $i++) {
        //Get papers assigned to the user
        $papersAssignedToUser = $ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . $userIds[$i])->result_array();

        for ($j = 0; $j < count($papersAssignedToUser); $j++) {
            $qa_results[$papersAssignedToUser[$j]['paper_id']] = 'Pending';
        }

        //Log the user to perform QA
        $http_client->response("user", "check_form", ['user_username' => getUser_username_byId($userIds[$i]), 'user_password' => '123'], "POST");

        if ($done < 0 || $done > count($papersAssignedToUser)) {
            $done = count($papersAssignedToUser);
        }

        //perform QA for the user
        for ($k = 0; $k < $done; $k++) {
            $paperId = $papersAssignedToUser[$k]['paper_id'];

            if ($k < $lowQuality) {
                //low quality paper
                qa_conduct_save(0, $paperId, 1, 1);
                qa_conduct_save(0, $paperId, 2, 2);
                qa_conduct_save(0, $paperId, 3, 3);

                $qa_results[$paperId] = "Low quality";
            } else {
                //high quality paper
                qa_conduct_save(0, $paperId, 1, 1);
                qa_conduct_save(0, $paperId, 2, 1);
                qa_conduct_save(0, $paperId, 3, 2);
                $qa_results[$paperId] = "High quality";
            }
        }
    }

    return $qa_results;
}

//$done : number of papers to assess per user; $lowQuality : Nbr of papers to assess with low quality per user
function assignPapers_and_performQA($userIds = array(), $done = -1, $lowQuality = 0)
{
    $ci = get_instance();
    $ci->session->set_userdata('project_db', getProjectShortName());

    $qa_results = []; //stores each paperId as key and QA result (high quality, low quality, pending) as value
    $http_client = new Http_client();
    $ci = get_instance();

    // 1. Assign papers
    $postData = ["number_of_users" => count($userIds), "percentage" => 100];
    for ($i = 0; $i < count($userIds); $i++) {
        $postData["user_" . ($i + 1)] = $userIds[$i];
    }
    qa_assignment_save($postData);

    // 2. perform QA
    for ($i = 0; $i < count($userIds); $i++) {
        //Get papers assigned to the user
        $papersAssignedToUser = $ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_assignment WHERE assigned_to = " . $userIds[$i])->result_array();

        for ($j = 0; $j < count($papersAssignedToUser); $j++) {
            $qa_results[$papersAssignedToUser[$j]['paper_id']] = 'Pending';
        }

        //Log the user to perform QA
        $http_client->response("user", "check_form", ['user_username' => getUser_username_byId($userIds[$i]), 'user_password' => '123'], "POST");

        if ($done < 0 || $done > count($papersAssignedToUser)) {
            $done = count($papersAssignedToUser);
        }

        //perform QA for the user
        for ($k = 0; $k < $done; $k++) {
            $paperId = $papersAssignedToUser[$k]['paper_id'];

            if ($k < $lowQuality) {
                //low Quality paper
                qa_conduct_save(0, $paperId, 1, 1);
                qa_conduct_save(0, $paperId, 2, 2);
                qa_conduct_save(0, $paperId, 3, 3);
                $qa_results[$paperId] = "Low quality";
            } else {
                //High quality paper
                qa_conduct_save(0, $paperId, 1, 1);
                qa_conduct_save(0, $paperId, 2, 1);
                qa_conduct_save(0, $paperId, 3, 2);
                $qa_results[$paperId] = "High quality";
            }
        }
    }

    return $qa_results;
}

//$done : number of papers to validate; $correct : Nbr of papers to set correct
function performQA_Validation($userIds = array(), $done = -1, $correct = -1)
{
    $qa_results = []; //stores each paperId as key and QA validation result (correct, not correct, pending) as value
    $ci = get_instance();
    $ci->session->set_userdata('project_db', getProjectShortName());

    $papersAssigned = $ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment")->result_array();

    for ($j = 0; $j < count($papersAssigned); $j++) {
        $qa_results[$papersAssigned[$j]['paper_id']] = 'Pending';
    }

    if ($done < 0 || $done > count($papersAssigned)) {
        $done = count($papersAssigned);
    }

    if ($correct < 0 || $correct > count($papersAssigned)) {
        $correct = count($papersAssigned);
    }

    for ($k = 0; $k < $done; $k++) {
        $paperId = $papersAssigned[$k]['paper_id'];

        if ($k < $correct) {
            //set correct
            qa_validate($paperId);
            $qa_results[$paperId] = "Correct";
        } else {
            //set not correct
            qa_validate($paperId);
            $qa_results[$paperId] = "Not correct";
        }
    }

    return $qa_results;
}

//$done : number of papers to validate; $correct : Nbr of papers to set correct
function assignPapers_and_performQA_Validation($userIds = array(), $done = -1, $correct = -1)
{
    $qa_results = []; //stores each paperId as key and QA validation result (correct, not correct, pending) as value
    $ci = get_instance();
    $ci->session->set_userdata('project_db', getProjectShortName());

    // 1. Assign papers
    $postData = ["number_of_users" => count($userIds), "percentage" => 100];

    for ($i = 0; $i < count($userIds); $i++) {
        $postData["user_" . ($i + 1)] = $userIds[$i];
    }
    qa_validation_assignment_save($postData);

    // 2. perform QA validation
    $papersAssigned = $ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment")->result_array();

    for ($j = 0; $j < count($papersAssigned); $j++) {
        $qa_results[$papersAssigned[$j]['paper_id']] = 'Pending';
    }

    if ($done < 0 || $done > count($papersAssigned)) {
        $done = count($papersAssigned);
    }

    if ($correct < 0 || $correct > $done) {
        $correct = $done;
    }

    for ($k = 0; $k < $done; $k++) {
        $paperId = $papersAssigned[$k]['paper_id'];

        if ($k < $correct) {
            //set correct
            qa_validate($paperId);
            $qa_results[$paperId] = "Correct";
        } else {
            //set not correct
            qa_validate($paperId);
            $qa_results[$paperId] = "Not correct";
        }
    }

    return $qa_results;
}

function qaExcludeLowQuality()
{
    $http_client = new Http_client();
    $http_client->response("quality_assessment", "qa_exclude_low_quality");
}



########### Data extraction ##########
function assignPapersForClassification($userIds = array())
{
    $http_client = new Http_client();

    $postData = ["number_of_users" => count($userIds), "percentage" => 100];
    for ($i = 0; $i < count($userIds); $i++) {
        $postData["user_" . ($i + 1)] = $userIds[$i];
    }
    $http_client->response("data_extraction", "class_assignment_save", $postData, "POST");
}

function performClassification()
{
    $ci = get_instance();
    $http_client = new Http_client();

    //Get papers assigned to admin user
    $papersAssignedToAdminUser = $ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getAdminUserId() . " AND assignment_type='classification'")->result_array();

    //Get papers assigned to test user
    $papersAssignedTotestUser = $ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".assigned WHERE assigned_user_id = " . getTestUserId() . " AND assignment_type='classification'")->result_array();

    //Login as test user to perform QA for the test user
    $http_client->response("user", "check_form", ['user_username' => getUser_username(), 'user_password' => '123'], "POST");

    //perform QA for the test user
    for ($i = 0; $i < count($papersAssignedTotestUser); $i++) {
        $paperId = $papersAssignedTotestUser[$i]['assigned_paper_id'];

        $data = [
            'operation_type' => 'new',
            'table_config' => 'classification',
            'current_operation' => 'new_classification',
            'redirect_after_save' => 'data_extraction/display_paper/~current_element~',
            'operation_source' => 'parent',
            'child_field' => 'class_paper_id',
            'table_config_parent' => 'papers',
            'parent_id' => $paperId,
            'parent_field' => '',
            'parent_table' => '',
            'class_id' => '',
            'class_paper_id' => $paperId,
            'has_choco' => 1,
            'temperature' => 0,
            'start' => '2023-11-04',
            'code' => 'AB9',
            'brand' => 3,
            'cocoa_origin' => 'Cote dIvoire',
            'cocoa_level' => '45%',
            'types' => 'Milk',
            'venue' => 'udem',
            'year' => 2017,
            'citation' => 7,
            'note' => 'good',
            'user_id' => getTestUserId()
        ];

        $http_client->response("element", "save_element", $data, "POST");
    }

    //Login as Admin to perform QA for the Admin user
    $http_client->response("user", "check_form", ['user_username' => 'admin', 'user_password' => '123'], "POST");

    //perform QA for the Admin user
    for ($i = 0; $i < count($papersAssignedToAdminUser); $i++) {
        $paperId = $papersAssignedToAdminUser[$i]['assigned_paper_id'];

        $data = [
            'operation_type' => 'new',
            'table_config' => 'classification',
            'current_operation' => 'new_classification',
            'redirect_after_save' => 'data_extraction/display_paper/~current_element~',
            'operation_source' => 'parent',
            'child_field' => 'class_paper_id',
            'table_config_parent' => 'papers',
            'parent_id' => $paperId,
            'parent_field' => '',
            'parent_table' => '',
            'class_id' => '',
            'class_paper_id' => $paperId,
            'has_choco' => 1,
            'temperature' => 0,
            'start' => '2023-11-04',
            'code' => 'AB9',
            'brand' => 3,
            'cocoa_origin' => 'Cote dIvoire',
            'cocoa_level' => '45%',
            'types' => 'Milk',
            'venue' => 'udem',
            'year' => 2017,
            'citation' => 7,
            'note' => 'good',
            'user_id' => getAdminUserId()
        ];

        $http_client->response("element", "save_element", $data, "POST");
    }
}



########### Reporting ##########
//delete generated reporting files
function deleteReportingFiles()
{
    $directory = "cside/export_r";
    $reportingFiles = glob($directory . '/relis_*');

    if ($reportingFiles !== false) {
        foreach ($reportingFiles as $file) {
            if (is_file($file)) {
                unlink($file); // Delete the file
            }
        }
    }
}