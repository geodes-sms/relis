<?php
function get_bibler_result($bibtex, $operation = "single")
{
    //clean the bibtex content
    $bibtex = strstr($bibtex, '@');
    $error = 1;
    $error_msg = "";
    $paper_array = array();
    $paper_preview_sucess = array(); //for import only
    $paper_preview_exist = array(); //for import only
    $paper_preview_error = array(); //for import only
    $init_time = microtime();
    $i = 1;
    $res = "init";
    while ($i < 10) { //up to ten attempt to connect to server if the connection does not work
        if ($operation == 'endnote') {
            $res = importendnotestringforrelis($bibtex);
        } else {
            $res = importbibtexstringforrelis($bibtex);
        }
        $correct = False;
        //if there is an error messag in the result retry
        if (strpos($res, 'Internal Server Error') !== false or empty($res)) {
            $i++;
        } else {
            //if there no error messag in the result retry
            $correct = True;
            $i = 20;
        }
    }
    $end_time = microtime();
    ini_set('auto_detect_line_endings', TRUE);
    if ($correct) {
        $Tres = json_decode($res, True);
        if (json_last_error() === JSON_ERROR_NONE) {
            if ($operation == 'single') {
                //for single add just consider the first element
                if (!empty($Tres['papers'][0])) {
                    $Tres = $Tres['papers'][0];
                }
                $result['bibtext'] = $bibtex;
                $paper_array = array();
                if (
                    !empty($Tres['result_code'])
                    and !empty($Tres['entry']['entrykey'])
                ) {
                    $error = 0;
                    $year = !empty($Tres['entry']['year']) ? $Tres['entry']['year'] : "";
                    $paper_array['bibtexKey'] = str_replace('\\', '', $Tres['entry']['entrykey']);
                    $title = !empty($value['entry']['title']) ? $value['entry']['title'] : "";
                    $title = str_replace('{', '', $title);
                    $title = str_replace('\\', '', $title);
                    $paper_array['title'] = str_replace('}', '', $title);
                    $paper_array['preview'] = !empty($Tres['preview']) ? $Tres['preview'] : "";
                    $paper_array['bibtex'] = !empty($Tres['bibtex']) ? $Tres['bibtex'] : "";
                    $paper_array['abstract'] = !empty($Tres['entry']['abstract']) ? $Tres['entry']['abstract'] : "";
                    $paper_array['doi'] = !empty($Tres['entry']['paper']) ? $Tres['entry']['paper'] : "";
                    $paper['venue'] = !empty($value['venue_full']) ? $value['venue_full'] : "";
                    $paper_array['year'] = $year;
                    $paper_array['authors'] = !empty($Tres['authors']) ? $Tres['authors'] : "";
                } else {
                    $msg = (!empty($Tres['result_msg']) ? $Tres['result_msg'] : "");
                    $error_msg .= "Error: check your Bibtext .<br/>" . $msg;
                }
            } else {
                if (empty($Tres['error']) and !empty($Tres['papers'])) {
                    $paper = array();
                    $i_ok = 1;
                    $i_ok_pupli = 1;
                    $i_Nok = 1;
                    foreach ($Tres['papers'] as $key => $value) {
                        if (!empty($value['entry']['entrykey'])) {
                            if (!empty($value['result_code'])) {
                                $error = 0;
                                $year = !empty($value['entry']['year']) ? $value['entry']['year'] : "";
                                $paper['bibtexKey'] = str_replace('\\', '', $value['entry']['entrykey']);
                                $title = !empty($value['entry']['title']) ? $value['entry']['title'] : "";
                                $title = str_replace('{', '', $title);
                                $title = str_replace('\\', '', $title);
                                $paper['title'] = str_replace('}', '', $title);
                                $paper['preview'] = !empty($value['preview']) ? $value['preview'] : "";
                                $paper['bibtex'] = !empty($value['bibtex']) ? $value['bibtex'] : "";
                                $paper['abstract'] = !empty($value['entry']['abstract']) ? $value['entry']['abstract'] : "";
                                $paper['doi'] = !empty($value['entry']['paper']) ? $value['entry']['paper'] : "";
                                $paper['venue'] = !empty($value['venue_full']) ? $value['venue_full'] : "";
                                $paper['year'] = $year;
                                $paper['authors'] = !empty($value['authors']) ? $value['authors'] : "";
                                array_push($paper_array, $paper);
                                if (paper_exist($paper)) {
                                    array_push($paper_preview_exist, array('i' => $i_ok_pupli, 'key' => $paper['bibtexKey'], 'preview' => $paper['preview']));
                                    $i_ok_pupli++;
                                } else {
                                    array_push($paper_preview_sucess, array('i' => $i_ok, 'key' => $paper['bibtexKey'], 'preview' => $paper['preview']));
                                    $i_ok++;
                                }
                            } else {
                                $preview = !empty($value['preview']) ? $value['preview'] : "";
                                $bibtexKey = !empty($value['bibtexKey']) ? str_replace('\\', '', $value['entry']['entrykey']) : "";
                                array_push(
                                    $paper_preview_error,
                                    array(
                                        'i' => $i_Nok,
                                        'key' => $bibtexKey,
                                        'preview' => $preview,
                                        'msg' => $value['result_msg']
                                    )
                                );
                                $i_Nok++;
                            }
                        }
                    }
                } else {
                    $error_msg .= "Error: No papers found.<br/>";
                    $error = 0;
                }
            }
        } else {
            $json_error = "";
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $json_error = 'No errors';
                    break;
                case JSON_ERROR_DEPTH:
                    $json_error = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $json_error = 'Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $json_error = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $json_error = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $json_error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $json_error = 'Unknown error';
                    break;
            }
            $error_msg .= "JSON Error : " . $json_error . ".<br/>";
        }
    }
    $result['error'] = $error;
    $result['error_msg'] = $error_msg;
    $result['paper_array'] = $paper_array;
    $result['paper_preview_sucess'] = $paper_preview_sucess;
    $result['paper_preview_exist'] = $paper_preview_exist;
    $result['paper_preview_error'] = $paper_preview_error;
    return $result;
}

//Imports a BibTeX string for the "reliS" system.
function importbibtexstringforrelis($data)
{
    $url = 'http://bibler:8000/';
    return httpPost($url . "importbibtexstringforrelis/", $data);
}

function httpPost($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    $payload = json_encode(array("bibtex" => utf8_encode($data)));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    // var_dump(json_last_error());
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    return $response;
}

function paper_exist($paper_array)
{
    $ci = get_instance();
    $bibtexKey = $paper_array['bibtexKey'];
    $exist = False;
    $stopsearch = False;
    $i = 1;
    while (!$stopsearch) {
        $res = $ci->db->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE BINARY bibtexKey = BINARY '" . $bibtexKey . "' and  paper_active=1")->row_array();

        if (empty($res)) {
            $stopsearch = True;
            $exist = False;
        } else {
            if ($res['title'] == $paper_array['title']) {
                $stopsearch = True;
                $exist = True;
            } else {
                $bibtexKey = $paper_array['bibtexKey'] . '_' . $i;
            }
        }
        $i++;
    }
    return $exist;
}

/*
 * handle the process of saving a new project, including file uploading, validation, and database checks.
 */
function save_new_project($projectInstallFile)
{
    $ci = get_instance();
    $ci->session->set_userdata('user_id', getAdminUserId());

    $fp = fopen($projectInstallFile, 'rb');
    $line = fgets($fp);
    $Tline = explode("//", $line);
    if (empty($Tline[1])) {
        //echo "Check the file used";
    } else {
        $project_short_name = trim($Tline[1]);
        $resul = $ci->Project_dataAccess->select_project_id_by_label($project_short_name);

        if (!empty($resul)) {
            //Project already installed
        } else {
            //Save the file in a temporal location
            $project_specific_config_folder = get_ci_config('project_specific_config_folder');
            $f_new_temp = fopen($project_specific_config_folder . "temp/install_config_" . $project_short_name . ".php", 'w+');
            rewind($fp);
            while (($line = fgets($fp)) !== false) {
                fputs($f_new_temp, $line);
            }
            fclose($f_new_temp);
            //Retrieve the content to verify the validity of the file
            $temp_table_config = $ci->entity_configuration_lib->get_new_install_config($project_short_name);
            if (!valid_install_configuration_file($temp_table_config)) {
                //Not a valid configuration file
            } else {
                copy($project_specific_config_folder . "temp/install_config_" . $project_short_name . ".php", $project_specific_config_folder . "install_config_" . $project_short_name . ".php");
                save_new_project_part2($project_short_name);
            }
        }
    }
}

/*
 * perform various tasks related to the second part of saving a new project, including database creation, 
 * configuration updates, table population, stored procedure updates, and adding project-related data to the database.
 */
function save_new_project_part2($project_short_name, $verbose = false, $reloadTimes = -1)
{
    $ci = get_instance();
    $ci->session->set_userdata('user_id', getAdminUserId());

    $database_name = get_ci_config('project_db_prefix') . $project_short_name;
    $res_sql = $ci->Project_dataAccess->create_project_db($database_name);

    //setting CI database configuration
    if ($reloadTimes = -1) {
        add_database_config($project_short_name);
    }
    //sleep to wait for config to be update (to correct for something really sure)
    sleep(2);
    if (!dbConnectionConfigExist($project_short_name) && $reloadTimes != 0) {
        if ($reloadTimes == -1) {
            $reloadTimes = 5;
        }
        $reloadTimes = $reloadTimes - 1;
        $error_array = array();
        $success_array = array();
        save_new_project_part2($project_short_name, 0, $reloadTimes);
    }

    // Populate created database
    populate_created_database($project_short_name);
    //create initial tables
    populate_common_tables($project_short_name);
    update_stored_procedure('init', FALSE, $project_short_name, TRUE);
    $res_install_config = $ci->entity_configuration_lib->get_install_config($project_short_name);
    $project_title = "Project default name";
    if (!empty($res_install_config['project_title']))
        $project_title = $res_install_config['project_title'];
    $ref_tables = array();
    $generated_tables = array();
    $foreign_key_constraints = array();
    //reference tables
    $sql_ref = "";
    if (!empty($res_install_config['reference_tables'])) {
        foreach ($res_install_config['reference_tables'] as $key => $value) {
            array_push($ref_tables, $key);
            $sql_ref .= create_reference_table($key, $value, $project_short_name);
            $sql_ref .= "<br/><br/>";
        }
    }
    $sql_table = "";
    if (!empty($res_install_config['config'])) {
        foreach ($res_install_config['config'] as $key_config => $config_values) {
            array_push($generated_tables, $key_config);
            populate_common_tables($project_short_name, $key_config);
            $foreign_key = get_froreign_keys_constraint($key_config, $config_values);
            if (!empty($foreign_key)) {
                array_push($foreign_key_constraints, $foreign_key);
            }
        }
    }

    populate_common_tables_views($project_short_name);
    $res_sql = $ci->Project_dataAccess->update_installation_info($project_short_name);
    $res_sql = $ci->Project_dataAccess->insert_installation_info($ref_tables, $generated_tables, $foreign_key_constraints, $project_short_name);

    // stored procedures
    if (!empty($res_install_config['config'])) {
        foreach ($res_install_config['config'] as $key_config => $config_values) {
            update_stored_procedure($key_config, FALSE, $project_short_name, TRUE);
        }
    }
    if (!empty($res_install_config['reference_tables'])) {
        foreach ($res_install_config['reference_tables'] as $key => $value) {
            update_stored_procedure($key, FALSE, $project_short_name);
        }
    }
    //add screening_values if available
    if (!empty($res_install_config['screening'])) {
        update_screening_values($res_install_config['screening'], $project_short_name);
    }
    //adding Qality assessment values
    if (!empty($res_install_config['qa'])) {
        update_qa_values($res_install_config['qa'], $project_short_name);
    }

    $creator = 1;
    $creator = $ci->session->userdata('user_id');
    $res_sql = $ci->Project_dataAccess->insert_into_project($project_short_name, $project_title, $creator);

    //Add the user as project admin
    if (!has_usergroup(1)) {
        $project_id = get_last_added_project();
        $res_sql = $ci->Project_dataAccess->insert_into_userproject($creator, $project_id);
    }
    // Update config editor according to general values
    $editor_url = get_adminconfig_element('editor_url');
    $editor_generated_path = get_adminconfig_element('editor_generated_path');

    if (!empty($editor_url) and !empty($editor_generated_path)) {
        $res_sql = $ci->Project_dataAccess->update_config($editor_url, $editor_generated_path, $project_short_name);
    }
}

/*
 * handle the process of importing papers from a BibTeX or EndNote file, 
 * inserting them into the database, and displaying appropriate messages to the user
 */
function import_papers_save_bibtext($data)
{
    $ci = get_instance();
    $ci->session->set_userdata('user_id', getAdminUserId());

    $post_arr = $data;
    //use save bibtext to get the right answer
    $data_array = json_decode($post_arr['data_array'], True);
    $papers_sources = (!empty($post_arr['papers_sources']) ? $post_arr['papers_sources'] : NULL);
    $search_strategy = (!empty($post_arr['search_strategy']) ? $post_arr['search_strategy'] : NULL);
    //	$paper_start_from = ((!empty($post_arr['paper_start_from']) AND is_numeric($post_arr['paper_start_from']))?$post_arr['paper_start_from']:2);
    $active_user = active_user_id();
    $added_active_phase = get_active_phase();
    $operation_code = active_user_id() . "_" . time();
    $default_key_prefix = get_appconfig_element('key_paper_prefix');
    $default_key_prefix = ($default_key_prefix == '0') ? '' : $default_key_prefix;
    $default_key_serial = get_appconfig_element('key_paper_serial');
    $serial_key = $default_key_serial;
    //set classification status
    if (get_appconfig_element('screening_on')) {
        $classification_status = 'Waiting';
        $screening_status = 'Pending';
    } else {
        $classification_status = 'To classify';
        $screening_status = 'Included';
    }
    //exit;
    $i = 1;
    $imported = 0;
    $exist = 0;
    foreach ($data_array as $key => $paper) {
        $paper['papers_sources'] = $papers_sources;
        $paper['search_strategy'] = $search_strategy;
        $paper['operation_code'] = $operation_code;
        $res = insert_paper_bibtext($paper);
        if ($res == '1') {
            $imported++;
        } else {
            $exist++;
        }
    }
    // update the operation tab
    $operation_arr = array(
        'operation_code' => $operation_code,
        'operation_type' => 'import_paper',
        'user_id' => active_user_id(),
        'operation_desc' => 'Paper import before screening'
    );
    $res2 = $ci->manage_mdl->add_operation($operation_arr);
    if (!empty($imported)) {
        set_top_msg(" $imported papers imported successfully");
    }
    if (!empty($exist)) {
        set_top_msg(" $exist papers already exist", 'error');
    }
}

/*
 * checks if a paper already exists based on the BibTeX key and title, 
 * and then inserts the paper into the database with the appropriate values and relationships with authors
 */
function insert_paper_bibtext($paper_array)
{
    $ci = get_instance();

    //check papers_exist
    //print_test($paper_array);
    $authors = $paper_array['authors'];
    unset($paper_array['authors']);
    $bibtexKey = $paper_array['bibtexKey'];
    $exist = False;
    $stopsearch = False;
    $i = 1;
    while (!$stopsearch) {
        $res = $ci->db_current->query("SELECT * FROM relis_dev_correct_" . getProjectShortName() . ".paper WHERE BINARY bibtexKey = BINARY '" . $bibtexKey . "' and  paper_active=1")->row_array();

        if (empty($res)) {
            $stopsearch = True;
            $exist = False;
        } else {
            if ($res['title'] == $paper_array['title']) {
                $stopsearch = True;
                $exist = True;
            } else {
                $bibtexKey = $paper_array['bibtexKey'] . '_' . $i;
            }
        }
        $i++;
    }
    if (!$exist) {
        //add venue
        if (!empty($paper_array['venue'])) {
            $venue_id = add_venue($paper_array['venue'], $paper_array['year']);
            $paper_array['venueId'] = $venue_id;
        }
        unset($paper_array['venue']);
        $paper_array['added_by'] = active_user_id();
        $paper_array['bibtexKey'] = $bibtexKey;
        //set classification status
        if (get_appconfig_element('screening_on')) {
            $paper_array['classification_status'] = 'Waiting';
            $paper_array['screening_status'] = 'Pending';
        } else {
            $paper_array['classification_status'] = 'To classify';
            $paper_array['screening_status'] = 'Included';
        }
        $ci->db_current->insert("relis_dev_correct_" . getProjectShortName() . ".paper", $paper_array);
        $paper_id = $ci->db_current->insert_id();
        if (!empty($authors)) {
            add_author($paper_id, $authors);
        }
        return 1;
    } else {
        return 'Paper already exit';
    }
    //print_test($res);
}

function add_author($paper_id, $author_array)
{
    $ci = get_instance();

    //check author exist
    foreach ($author_array as $key => $author) {
        $author_name = $author['first_name'] . ' ' . $author['last_name'];
        $res = $ci->db_current->query('SELECT * FROM relis_dev_correct_' . getProjectShortName() . '.author WHERE BINARY author_name = BINARY "' . $author_name . '" and  author_active=1')->row_array();

        //print_test($res);
        if (empty($res['author_id'])) {
            $ci->db_current->insert("relis_dev_correct_" . getProjectShortName() . ".author", array('author_name' => $author_name));
            $author_id = $ci->db_current->insert_id();
        } else {
            $author_id = $res['author_id'];
        }
        if (!empty($author_id)) {
            $ci->db_current->insert(
                "relis_dev_correct_" . getProjectShortName() . ".paperauthor",
                array(
                    'paperId' => $paper_id,
                    'authorId' => $author_id,
                    'author_rank' => $key + 1,
                )
            );
        }
    }
}

//add a venue to the database if it does not already exist.
function add_venue($venue, $year = 0)
{
    $ci = get_instance();

    $res = $ci->db_current->get_where(
        "relis_dev_correct_" . getProjectShortName() . ".venue",
        array('venue_fullName' => $venue, 'venue_active' => 1)
    )
        ->row_array();
    $array_venue = array('venue_fullName' => $venue);
    if (!empty($year)) {
        $array_venue['venue_year'] = $year;
    }
    if (empty($res['venue_id'])) {
        $ci->db_current->insert("relis_dev_correct_" . getProjectShortName() . ".venue", $array_venue);
        return $venue_id = $ci->db_current->insert_id();
    } else {
        return $res['venue_id'];
    }
}

function save_screening($data)
{
    $ci = get_instance();

    $post_arr = $data;
    $decision_source = 'new_screen';
    if ($post_arr['screen_type'] == 'edit_screen') {
        $decision_source = 'edit_screen';
    } elseif ($post_arr['screen_type'] == 'resolve_conflict') {
        $decision_source = 'conflict_resolution';
    }

    if (empty($post_arr['criteria_ex']) and $post_arr['decision'] == 'excluded') {
        set_top_msg('Please choose the exclusion criteria', "error");
        if ($post_arr['screen_type'] == 'simple_screen') {
            exit;
        } else {
            exit;
        }
    } else {
        if (!empty($post_arr['screen_type']) and $post_arr['screen_type'] == 'screen_validation') {
            $screening_table = 'screening_validate';
            $assignment_table = 'assignment_screen_validate';
        } else {
            $screening_table = 'screening';
            $assignment_table = 'assignment_screen';
        }
        $ci->db2 = $ci->load->database(project_db(), TRUE);
        $screening_phase = !empty($post_arr['screening_phase']) ? $post_arr['screening_phase'] : 1;
        $exclusion_criteria = ($post_arr['decision'] == 'excluded') ? $post_arr['criteria_ex'] : NULL;
        $inclusion_criteria = ($post_arr['decision'] == 'accepted') ? $post_arr['criteria_in'] : NULL;
        $screening_decision = ($post_arr['decision'] == 'excluded') ? 'Excluded' : 'Included';
        $screening_save = array(
            'screening_note' => $post_arr['note'],
            'screening_decision' => $screening_decision,
            'exclusion_criteria' => $exclusion_criteria,
            'inclusion_criteria' => $inclusion_criteria,
            'screening_time' => bm_current_time('Y-m-d H:i:s'),
            'screening_status' => 'Done',
        );
        //print_test($screening_save); exit;
        $res = $ci->db2->update('relis_dev_correct_' . getProjectShortName() . '.screening_paper', $screening_save, array('screening_id' => $post_arr['screening_id']));
        $screen_phase_detail = $ci->DBConnection_mdl->get_row_details('get_screen_phase_detail', $screening_phase, TRUE);
        $screening_phase_last_status = $screen_phase_detail['screen_phase_final'];
        $paper_status = get_paper_screen_status_new($post_arr['paper_id'], $screening_phase);
        $query_screen_decision = $ci->db2->get_where('screen_decison', array('paper_id' => $post_arr['paper_id'], 'screening_phase' => $screening_phase, 'decision_active' => 1), 1)->row_array();
        //screen history append
        $Tscreen_history = array(
            'decision_source' => $decision_source,
            'user' => active_user_id(),
            'decision' => $screening_decision,
            'criteria' => $exclusion_criteria,
            'criteria2' => $inclusion_criteria,
            'note' => $post_arr['note'],
            'paper_status' => $paper_status,
            'screening_time' => bm_current_time('Y-m-d H:i:s'),
        );
        $Json_screen_history = json_encode($Tscreen_history);
        if (empty($query_screen_decision)) {
            $ci->db2->insert('screen_decison', array('paper_id' => $post_arr['paper_id'], 'screening_phase' => $screening_phase, 'screening_decision' => $paper_status, 'decision_source' => $decision_source, 'decision_history' => $Json_screen_history));
        } else {
            if (!empty($query_screen_decision['decision_history']))
                $Json_screen_history = $query_screen_decision['decision_history'] . "~~__" . $Json_screen_history;
            if ($query_screen_decision['screening_decision'] != $paper_status) {
                $ci->db2->update('screen_decison', array('screening_decision' => $paper_status, 'decision_source' => $decision_source, 'decision_history' => $Json_screen_history), array('paper_id' => $post_arr['paper_id'], 'screening_phase' => $screening_phase, 'decision_active' => 1));
            } else {
                $ci->db2->update('screen_decison', array('decision_history' => $Json_screen_history), array('paper_id' => $post_arr['paper_id'], 'screening_phase' => $screening_phase, 'decision_active' => 1));
            }
        }
        if ($screening_phase_last_status or $paper_status == 'Excluded') {
            if ($paper_status == 'Included') {
                $ci->db2->update('paper', array('screening_status' => $paper_status, 'classification_status' => 'To classify'), array('id' => $post_arr['paper_id']));
            } else {
                $paper_status = (($paper_status != 'Included' and $paper_status != 'Excluded') ? 'Pending' : $paper_status);
                $ci->db2->update('paper', array('screening_status' => $paper_status, 'classification_status' => 'Waiting'), array('id' => $post_arr['paper_id']));
            }
        }
    }
    $after_save_redirect = $ci->session->userdata('after_save_redirect');
    if (!empty($after_save_redirect)) {
        $ci->session->set_userdata('after_save_redirect', '');
    } elseif (!(empty($post_arr['operation_type'])) and $post_arr['operation_type'] == 'edit') {
        set_top_msg('Element updated');
        if ($post_arr['operation_source'] == 'display_paper_screen') {
        } else {
        }
    } else {
        set_top_msg('Element saved');
        if (!empty($post_arr['screen_type']) and $post_arr['screen_type'] == 'screen_validation') {
        } else {
        }
    }
}

//The purpose of this function is to handle the saving of paper assignments for screening
function save_assignment_screen($data)
{
    $ci = get_instance();

    $post_arr = $data;
    $users = array();
    $i = 1;
    if (empty($post_arr['reviews_per_paper'])) {

    } else {
        // Get selected users
        while ($i <= $post_arr['number_of_users']) {
            if (!empty($post_arr['user_' . $i])) {
                array_push($users, $post_arr['user_' . $i]);
            }
            $i++;
        }
        //Verify if selected users is > of required reviews per paper
        if (count($users) < $post_arr['reviews_per_paper']) {

        } else {
            $currect_screening_phase = $post_arr['screening_phase'];
            $papers_sources = $post_arr['papers_sources'];
            $paper_source_status = $post_arr['paper_source_status'];
            $reviews_per_paper = $post_arr['reviews_per_paper'];
            //Get all papers
            $papers = get_papers_to_screen($papers_sources, $paper_source_status);
            $assign_papers = array();
            $ci->db2 = $ci->load->database(project_db(), TRUE);
            $operation_code = active_user_id() . "_" . time();
            foreach ($papers['to_assign'] as $key => $value) {
                $assign_papers[$key]['paper'] = $value['id'];
                $assign_papers[$key]['users'] = array();
                $assignment_save = array(
                    'paper_id' => $value['id'],
                    'user_id' => '',
                    'assignment_note' => '',
                    'assignment_type' => 'Normal',
                    'operation_code' => $operation_code,
                    'assignment_mode' => 'auto',
                    'screening_phase' => $currect_screening_phase,
                    'assigned_by' => getAdminUserId()
                );
                $j = 1;
                //the table to save assignments
                $table_name = get_table_configuration('screening', 'current', 'table_name');
                //print_test($table_name);
                while ($j <= $reviews_per_paper) {
                    $temp_user = ($key % count($users)) + $j;
                    if ($temp_user >= count($users))
                        $temp_user = $temp_user - count($users);
                    array_push($assign_papers[$key]['users'], $users[$temp_user]);
                    $assignment_save['user_id'] = $users[$temp_user];
                    //print_test($assignment_save);
                    $ci->db2->insert("relis_dev_correct_" . getProjectShortName() . "." . $table_name, $assignment_save);
                    $j++;
                }
            }
            $operation_arr = array(
                'operation_code' => $operation_code,
                'operation_type' => 'assign_papers',
                'user_id' => active_user_id(),
                'operation_desc' => 'Assign papers for screening'
            );
            $res2 = $ci->manage_mdl->add_operation($operation_arr);
        }
    }
}

/**
 * The purpose of this function is to retrieve and organize papers for screening based on the provided source, 
 * source status, current phase, and assignment role
 */
function get_papers_to_screen($source = 'all', $source_status = 'all', $current_phase = "", $assignment_role = "")
{
    $ci = get_instance();

    //$source_status="Included";
    //$source='1';
    if (empty($current_phase)) {
        $current_phase = active_screening_phase();
    }

    $all_papers = select_screening_all_papers($source, $source_status);

    $result['all_papers'] = $all_papers;
    // get papers already assigned in current phase
    $condition = "";
    if (!empty($assignment_role)) {
        $condition = " AND assignment_role = '$assignment_role'";
    }

    $paper_assigned = select_screening_paper($current_phase, $condition);

    //	$result['paper_assigned']=$paper_assigned;
    $det_paper_to_assign = array();
    $det_paper_assigned = array();
    if (empty($paper_assigned)) //no paper already assigned'
    {
        $det_paper_to_assign = $all_papers;
    } else {
        foreach ($all_papers as $key_all => $paper_all) {
            $found = False;
            foreach ($paper_assigned as $key_assigned => $value_assigned) {
                if ($paper_all['id'] == $value_assigned['paper_id']) {
                    $found = True;
                    array_push($det_paper_assigned, $paper_all);
                    break;
                }
            }
            if (!$found) {
                array_push($det_paper_to_assign, $paper_all);
            }
        }
    }
    $result['assigned'] = $det_paper_assigned;
    $result['to_assign'] = $det_paper_to_assign;
    return $result;
}

function select_screening_all_papers($source, $source_status)
{
    $ci = get_instance();

    if ($source == 'all') {
        //rechercher dans papers
        $condition = "";
        if ($source_status != 'all') {
            $condition = " AND screening_status = '$source_status'";
        }
        $sql = "SELECT P.*,screening_status as paper_status from relis_dev_correct_" . getProjectShortName() . ".paper P where paper_active = 1 $condition ";
    } else {
        $condition = "";
        if ($source_status != 'all') {
            $condition = " AND S.screening_decision = '$source_status'";
        }
        $sql = "SELECT decison_id,screening_decision as paper_status,P.* from relis_dev_correct_" . getProjectShortName() . ".screen_decison S
        LEFT JOIN paper P ON(S.paper_id=P.id  )
        WHERE screening_phase='$source'	AND  decision_active=1 AND P.paper_active=1 $condition
        ";
        //rechercher dans screen et la decision dans screen decision
    }
    $all_papers = $ci->db_current->query($sql)->result_array();

    return $all_papers;
}

function select_screening_paper($current_phase, $condition)
{
    $ci = get_instance();

    $sql = "Select DISTINCT (paper_id) from relis_dev_correct_" . getProjectShortName() . ".screening_paper WHERE screening_active =1 AND screening_phase = " . getScreeningPhaseId('Title') . "  $condition GROUP BY paper_id";
    $paper_assigned = $ci->db_current->query($sql)->result_array();
    return $paper_assigned;
}

//save the assignments of papers for quality assessment.
function qa_assignment_save($data)
{
    $ci = get_instance();

    $post_arr = $data;
    $users = array();
    $i = 1;
    $percentage = intval($post_arr['percentage']);
    if (empty($percentage)) {
        $percentage = 100;
    }
    // Get selected users
    while ($i <= $post_arr['number_of_users']) {
        if (!empty($post_arr['user_' . $i])) {
            array_push($users, $post_arr['user_' . $i]);
        }
        $i++;
    }
    //Verify if selected users is > of required reviews per paper
    if (count($users) < 1) {
        $data['err_msg'] = lng('Please select at least one user  ');
    } else {
        $reviews_per_paper = 1;
        $papers_all = get_papers_for_qa();
        $papers = $papers_all['papers_to_assign'];
        $papers_to_validate_nbr = round(count($papers) * $percentage / 100);
        $operation_description = "Assign  papers for QA";
        shuffle($papers); // randomize the list
        $assign_papers = array();
        $ci->db2 = $ci->load->database(project_db(), TRUE);
        $operation_code = active_user_id() . "_" . time();
        foreach ($papers as $key => $value) {
            if ($key < $papers_to_validate_nbr) {
                $assignment_save = array(
                    'paper_id' => $value,
                    'assigned_to' => '',
                    'assigned_by' => active_user_id(),
                    'operation_code' => $operation_code,
                    'assignment_mode' => 'auto',
                );
                $j = 1;
                //the table to save assignments
                $table_name = get_table_configuration('qa_assignment', 'current', 'table_name');
                while ($j <= $reviews_per_paper) {
                    $temp_user = ($key % count($users)) + $j;
                    if ($temp_user >= count($users))
                        $temp_user = $temp_user - count($users);
                    $assignment_save['assigned_to'] = $users[$temp_user];
                    //	print_test($assignment_save);
                    $ci->db2->insert($table_name, $assignment_save);
                    $j++;
                }
            }
        }
        $operation_arr = array(
            'operation_code' => $operation_code,
            'operation_type' => 'assign_qa',
            'user_id' => active_user_id(),
            'operation_desc' => $operation_description
        );
        //print_test($operation_arr);
        $res2 = $ci->manage_mdl->add_operation($operation_arr);
    }
}

function get_papers_for_qa()
{
    $ci = get_instance();

    //papers already assigned
    $papers_assigned = $ci->db_current->order_by('qa_assignment_id', 'ASC')
        ->get_where("relis_dev_correct_" . getProjectShortName() . ".qa_assignment", array('qa_assignment_active' => 1, 'assignment_type' => 'QA'))
        ->result_array();
    $papers_assigned_array = array();
    foreach ($papers_assigned as $key => $value) {
        $papers_assigned_array[$value['paper_id']] = $value['assigned_to'];
    }
    //all papers
    $all_papers = $ci->db_current->order_by('id', 'ASC')
        ->get_where("relis_dev_correct_" . getProjectShortName() . ".paper", array('paper_active' => 1, 'screening_status' => 'Included'))
        ->result_array();
    $paper_to_assign = array();
    $paper_to_assign_display[0] = array('Key', 'Title');
    foreach ($all_papers as $key => $value) {
        if (empty($papers_assigned_array[$value['id']])) { //exclude papers already assigned
            $paper_to_assign_display[$key + 1] = array($value['bibtexKey'], $value['title']);
            $paper_to_assign[$key] = $value['id'];
        }
    }
    $result['count_all_papers'] = count($all_papers);
    $result['count_papers_assigned'] = count($papers_assigned_array);
    $result['count_papers_to_assign'] = count($paper_to_assign); // we remove the header
    $result['papers_to_assign_display'] = $paper_to_assign_display;
    $result['papers_to_assign'] = $paper_to_assign;
    return $result;
}

//responsible for saving the QA result for a specific paper, question, and response.
function qa_conduct_save($update, $paper_id, $question, $response)
{
    $ci = get_instance();

    $qa_result = array(
        'paper_id' => $paper_id,
        'question' => $question,
        'response' => $response,
        'done_by' => active_user_id()
    );
    if (!$update) {
        $ci->db_current->insert("relis_dev_correct_" . getProjectShortName() . ".qa_result", $qa_result);
    } else {
        $ci->db_current->update("relis_dev_correct_" . getProjectShortName() . ".qa_result", $qa_result, array('paper_id' => $paper_id, 'question' => $question));
    }
    $after_after_save_redirect = $ci->session->userdata('after_save_redirect');
    if (!empty($after_after_save_redirect)) {
        $ci->session->set_userdata('after_save_redirect', '');
    } else {
        $after_after_save_redirect = "quality_assessment/qa_conduct_list";
    }
    //update assignment
    if (qa_done_for_paper($paper_id)) {
        $ci->db_current->update("relis_dev_correct_" . getProjectShortName() . ".qa_assignment", array('qa_status' => 'Done'), array('paper_id' => $paper_id));
    } else {
    }
}

//Verify if all questions have been answered for the paper
function qa_done_for_paper($paper_id)
{
    $ci = get_instance();

    $result = count_qa($paper_id);
    if (empty($result['nbr'])) {
        return TRUE; //all questions have been responded
    } else {
        return FALSE;
    }
}

function count_qa($paper_id)
{
    $ci = get_instance();
    $sql = "SELECT COUNT(*) AS nbr FROM
		relis_dev_correct_" . getProjectShortName() . ".qa_questions Q LEFT JOIN relis_dev_correct_" . getProjectShortName() . ".qa_result R ON(Q.question_id=R.question AND R.qa_active=1 AND R.paper_id=$paper_id)
		WHERE Q.question_active=1 AND paper_id IS NULL  ";
    $result = $ci->db_current->query($sql)->row_array();
    return $result;
}

//save the assignments of papers for quality assessment validation.
function qa_validation_assignment_save($data)
{
    $ci = get_instance();

    $post_arr = $data;
    $users = array();
    $i = 1;
    $percentage = intval($post_arr['percentage']);
    if (empty($percentage)) {
    } elseif ($percentage > 100 or $percentage <= 0) {
    } else {
        // Get selected users
        while ($i <= $post_arr['number_of_users']) {
            if (!empty($post_arr['user_' . $i])) {
                array_push($users, $post_arr['user_' . $i]);
            }
            $i++;
        }
        //Verify if selected users is > of required reviews per paper
        if (count($users) < 1) {
        } else {
            $reviews_per_paper = 1;
            $papers_all = get_papers_for_qa_validation();
            $papers = $papers_all['papers_to_assign'];
            $papers_to_validate_nbr = round(count($papers) * $percentage / 100);
            $operation_description = "Assign  papers for QA validation";
            shuffle($papers); // randomize the list
            $assign_papers = array();
            $ci->db2 = $ci->load->database(project_db(), TRUE);
            $operation_code = active_user_id() . "_" . time();
            foreach ($papers as $key => $value) {
                if ($key < $papers_to_validate_nbr) {
                    $assignment_save = array(
                        'paper_id' => $value,
                        'assigned_to' => '',
                        'assigned_by' => active_user_id(),
                        'operation_code' => $operation_code,
                        'assignment_mode' => 'auto',
                    );
                    $j = 1;
                    //the table to save assignments
                    $table_name = get_table_configuration('qa_validation_assignment', 'current', 'table_name');
                    while ($j <= $reviews_per_paper) {
                        $temp_user = ($key % count($users)) + $j;
                        if ($temp_user >= count($users))
                            $temp_user = $temp_user - count($users);
                        $assignment_save['assigned_to'] = $users[$temp_user];
                        //	print_test($assignment_save);
                        $ci->db2->insert($table_name, $assignment_save);
                        $j++;
                    }
                }
            }

            $operation_arr = array(
                'operation_code' => $operation_code,
                'operation_type' => 'assign_qa_validation',
                'user_id' => active_user_id(),
                'operation_desc' => $operation_description
            );
            //print_test($operation_arr);
            $res2 = $ci->manage_mdl->add_operation($operation_arr);
            set_top_msg('Operation completed');
        }
    }
}

//retrieve the papers that are eligible for validation in the quality assessment process
function get_papers_for_qa_validation()
{
    $ci = get_instance();

    //papers already assigned
    $papers_assigned = $ci->db_current->order_by('qa_validation_assignment_id', 'ASC')
        ->get_where("relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment", array('qa_validation_active' => 1))
        ->result_array();
    $papers_assigned_array = array();
    foreach ($papers_assigned as $key => $value) {
        $papers_assigned_array[$value['paper_id']] = $value['assigned_to'];
    }

    //all papers
    $all_papers = select_qa_papers();

    $paper_to_assign = array();
    $paper_to_assign_display[0] = array('Key', 'Title');
    foreach ($all_papers as $key => $value) {
        if (empty($papers_assigned_array[$value['id']])) { //exclude papers already assigned
            $paper_to_assign_display[$key + 1] = array($value['bibtexKey'], $value['title']);
            $paper_to_assign[$key] = $value['id'];
        }
    }
    $result['count_all_papers'] = count($all_papers);
    $result['count_papers_assigned'] = count($papers_assigned_array);
    $result['count_papers_to_assign'] = count($paper_to_assign); // we remove the header
    $result['papers_to_assign_display'] = $paper_to_assign_display;
    $result['papers_to_assign'] = $paper_to_assign;
    return $result;
}

function select_qa_papers()
{
    $ci = get_instance();
    $sql = "SELECT P.* FROM relis_dev_correct_" . getProjectShortName() . ".paper P, relis_dev_correct_" . getProjectShortName() . ".qa_assignment Q WHERE P.id=Q.paper_id AND Q.qa_status='Done' AND P.paper_active=1 AND Q.qa_assignment_active=1 ";
    $all_papers = $ci->db_current->query($sql)->result_array();
    return $all_papers;
}

//perform the validation of a quality assessment for a specific paper.
function qa_validate($paper_id, $op = 1)
{
    $ci = get_instance();

    if ($op == 1) {
        $ci->db_current->update("relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment", array('validation' => 'Correct', 'validation_note' => '', 'validation_time' => bm_current_time()), array('paper_id' => $paper_id));
    } else {
        $assignment = $ci->db_current->get_where(
            'qa_validation_assignment',
            array('qa_validation_active' => 1, 'paper_id' => $paper_id)
        )
            ->row_array();
        if (!empty($assignment['qa_validation_assignment_id'])) {
            $ci->db_current->update("relis_dev_correct_" . getProjectShortName() . ".qa_validation_assignment", array('validation' => 'Not Correct', 'validation_note' => '', 'validation_time' => bm_current_time()), array('paper_id' => $paper_id));
        }
    }
    if (!empty($after_after_save_redirect)) {
        $ci->session->set_userdata('after_save_redirect', '');
    } else {
        $after_after_save_redirect = "quality_assessment/qa_conduct_list_val";
    }
}

