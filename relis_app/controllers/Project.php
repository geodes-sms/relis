<?php
/* ReLiS - A Tool for conducting systematic literature reviews and mapping studies.
 * Copyright (C) 2018  Eugene Syriani
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * --------------------------------------------------------------------------
 *
 *  :Author: Brice Michel Bigendako
 * --------------------------------------------------------------------------
 * 
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Project extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /*
     * Liste des projets installés	 *
     */
    public function projects_list()
    {
        //phpinfo();
        $this->session->set_userdata('working_perspective', 'class');
        //	$this->session->set_userdata('project_db',$projet_label);
        $this->session->set_userdata('project_id', FALSE);
        $this->session->set_userdata('project_title', '');
        $this->session->set_userdata('project_public', '');
        $config = "project";
        $this->session->set_userdata('project_db', 'default');
        $ref_table_config = get_table_configuration($config);
        $ref_table_config['current_operation'] = 'list_projects';
        $data['projects'] = $this->DBConnection_mdl->get_list_mdl($ref_table_config);
        //print_test($data);
        foreach ($data['projects']['list'] as $key => $value) {
            $detail_project = $this->DBConnection_mdl->get_row_details($ref_table_config['operations']['detail_project']['data_source'], $value['project_id'], true);
            if (!empty($detail_project['project_icon'])) {
                //$data['projects']['list'][$key]['icon']=$this->config->item('image_upload_path').$detail_project['project_icon']."_med.jpg";}
                $data['projects']['list'][$key]['icon'] = display_picture_from_db($detail_project['project_icon']);
            } else {
                $data['projects']['list'][$key]['icon'] = base_url() . $this->config->item('image_upload_path') . "init/model_project1.png";
            }
            if (!(user_project($value['project_id'])) and !has_usergroup(1)) {
                unset($data['projects']['list'][$key]);
            }
        }
        //print_test($data['projects']['list']);
        if (has_usergroup(1) or has_usergroup(2))
            $data['add_project_button'] = get_top_button('all', 'Add new project', 'project/new_project_editor', 'Add new project', 'fa-plus', '', ' btn-primary ', false);
        $data['page'] = 'general/projects_list';
        $data['page_title'] = (isset($ref_table_config['entity_title']['list'])) ? lng($ref_table_config['entity_title']['list']) : lng('Installed projects');
        $data['left_menu_admin'] = True;
        $this->load->view('shared/body', $data);
    }

    /*
     * Liste des projets installés	 *
     */
    public function projects_list2()
    {
        $this->session->set_userdata('working_perspective', 'class');
        $config = "project";
        $this->session->set_userdata('project_db', FALSE);
        $ref_table_config = get_table_config($config);
        $data['projects'] = $this->DBConnection_mdl->get_list($ref_table_config);
        foreach ($data['projects']['list'] as $key => $value) {
            $detail_project = $this->DBConnection_mdl->get_row_details($config, $value['project_id']);
            if (!empty($detail_project['project_icon'])) {
                //$data['projects']['list'][$key]['icon']=$this->config->item('image_upload_path').$detail_project['project_icon']."_med.jpg";}
                $data['projects']['list'][$key]['icon'] = display_picture_from_db($detail_project['project_icon']);
            } else {
                $data['projects']['list'][$key]['icon'] = base_url() . $this->config->item('image_upload_path') . "init/model_project1.png";
            }
            if (!(user_project($value['project_id']))) {
                unset($data['projects']['list'][$key]);
            }
        }
        //$data ['add_project_button'] = get_top_button ( 'all', 'Add new project', 'project/new_project','Add new project','fa-plus','',' btn-success ',false );
        $data['page'] = 'general/projects_list';
        $data['page_title'] = (isset($ref_table_config['entity_title']['list'])) ? lng($ref_table_config['entity_title']['list']) : lng('Installed projects');
        $data['left_menu_admin'] = True;
        $this->load->view('shared/body', $data);
    }

    /**
     * used to control the publication status of a project, allowing it to be visible to other users or reopening a project that was previously published
     */
    public function publish_project($project_id = 0, $operation = 1)
    {
        if (empty($project_id)) {
            $project_id = active_project_id();
        }
        if (!empty($project_id)) {
            $projet_label = project_db();
            $op = ($operation == 1) ? 1 : 0;

            $res_sql = $this->Project_dataAccess->update_project_public_field($op, $project_id);

            //echo  $sql;
            //print_test($res_sql);
            if ($operation == 1) {
                set_top_msg("Project $projet_label published");
                set_log('publish', "Project $projet_label published");
            } else {
                set_top_msg("Project $projet_label reopened");
                set_log('publish', "Project $projet_label reopened");
            }
        } else {
        }
        redirect('project/projects_list');
    }

    /**
     * responsible for rendering the form for adding a new project and providing navigation buttons for the user to interact with
     */
    public function new_project()
    {
        $data['page_title'] = lng('Add new project');
        $data['top_buttons'] = get_top_button('all', 'Load from editor', 'project/new_project_editor', 'Load from editor', ' fa-exchange', '', ' btn-info ');
        $data['top_buttons'] .= get_top_button('back', 'Back', 'project/choose_project/');
        $data['page'] = 'project/frm_new_project';
        $data['left_menu_admin'] = True;
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /**
     * responsible for rendering the form for adding a new project from the ReLiS Editor and displaying information about existing projects
     */
    public function new_project_editor()
    {
        // Create new project from ReLiS Editor
        /**
         * @var string $dir : the location of the folder where the installation files are located
         */
        $dir = get_ci_config('editor_generated_path');
        /**
         * @var string  $editor_url  the url adress of ReLiS Editor: 
         */
        $path_separator = path_separator(); // used to diferenciate windows and linux server
        $editor_url = $this->config->item('editor_url');
        $dir = get_adminconfig_element('editor_generated_path');
        $editor_url = get_adminconfig_element('editor_url');
        $Tprojects = array();
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..', ".metadata"));
            //print_test($files);
            foreach ($files as $key => $file) {
                if (is_dir($dir . $path_separator . $file)) {
                    $project_dir = $dir . $path_separator . $file;
                    $Tprojects[$file] = array();
                    $Tprojects[$file]['dir'] = $project_dir;
                    $Tprojects[$file]['syntax'] = array();
                    $Tprojects[$file]['generated'] = array();
                    //syntax
                    $project_content = array_diff(scandir($project_dir), array('.', '..', ".metadata"));
                    foreach ($project_content as $key => $value_c) {
                        if (!is_dir($project_dir . $path_separator . $value_c)) {
                            array_push($Tprojects[$file]['syntax'], $value_c);
                        } elseif ($value_c == 'src-gen') {
                            $project_content_gen = array_diff(scandir($project_dir . $path_separator . 'src-gen'), array('.', '..', ".metadata"));
                            foreach ($project_content_gen as $key_g => $value_g) {
                                if (!is_dir($project_dir . $path_separator . 'src-gen' . $path_separator . $value_g)) {
                                    array_push($Tprojects[$file]['generated'], $value_g);
                                }
                            }
                        }
                    }
                }
            }
        }
        $data['project_result'] = $Tprojects;
        $data['page_title'] = lng('Add new project');
        $data['top_buttons'] = get_top_button('all', lng_min('Upload configuration file'), 'project/new_project', lng_min('Upload configuration file'), ' fa-upload', '', ' btn-info ');
        $data['top_buttons'] .= "<li>" . anchor('install/relis_editor/admin', '<button class="btn btn-primary">  ' . lng_min('Open editor') . ' </button></li>', 'title="' . lng_min('Open editor') . '" ') . "</li>";
        $data['top_buttons'] .= get_top_button('back', 'Back', 'project/choose_project/');
        $data['page'] = 'project/frm_new_project_editor';
        $data['left_menu_admin'] = True;
        
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /**
     * handle the process of saving a new project, including file uploading, validation, and database checks. It also handles error reporting if any issues occur during the process
     */
    public function save_new_project()
    {
        $error_array = array();
        $success_array = array();
        if ($_FILES["install_config"]["error"] > 0) {
            array_push($error_array, "Error: " . file_upload_error($_FILES["install_config"]["error"]));
        } elseif ($_FILES["install_config"]["type"] !== "application/octet-stream" or $_FILES["install_config"]["type"] !== "application/octet-stream") {
            //echo "File must be a .php";
            array_push($error_array, "File must be a .php");
        } else {
            $fp = fopen($_FILES['install_config']['tmp_name'], 'rb');
            $line = fgets($fp);
            $Tline = explode("//", $line);
            if (empty($Tline[1])) {
                //echo "Check the file used";
                array_push($error_array, "Check the file used");
            } else {
                $project_short_name = trim($Tline[1]);
                $resul = $this->Project_dataAccess->select_project_id_by_label($project_short_name);

                //print_test($resul);
                if (!empty($resul)) {
                    //echo "<h2>Project already installed</h2>";
                    //echo "<h2>".anchor('home',lng('Back'))."</h2>";
                    array_push($error_array, "Project already installed");
                } else {
                    //Save the file in a temporal location
                    $project_specific_config_folder = get_ci_config('project_specific_config_folder');
                    $f_new_temp = fopen($project_specific_config_folder . "temp/install_config_" . $project_short_name . ".php", 'w+');
                    rewind($fp);
                    while (($line = fgets($fp)) !== false) {
                        //fputs($f_new_temp, $line. "\n");
                        fputs($f_new_temp, $line);
                        //echo "$line<br>";
                    }
                    fclose($f_new_temp);
                    //Retrieve the content to verify the validity of the file
                    $temp_table_config = $this->entity_configuration_lib->get_new_install_config($project_short_name);
                    if (!valid_install_configuration_file($temp_table_config)) {
                        array_push($error_array, "Not a valid configuration file");
                    } else {
                        copy($project_specific_config_folder . "temp/install_config_" . $project_short_name . ".php", $project_specific_config_folder . "install_config_" . $project_short_name . ".php");
                        redirect('project/save_new_project_part2/' . $project_short_name);
                    }
                }
            }
        }
        if (!empty($error_array)) {
            //print_r($error_array);
            $this->project_install_result($error_array);
        }
    }

    //save a new project. 
    public function save_new_project_editor()
    {
        $post_arr = $this->input->post();
        //print_test($post_arr); exit;
        $error_array = array();
        $success_array = array();
        if (empty($post_arr['selected_config'])) {
            array_push($error_array, lng("Error: Choose a file "));
        } elseif (!is_file($post_arr['selected_config'])) {
            //echo "File must be a .php";
            array_push($error_array, lng("File must be a .php"));
        } else {
            $fp = fopen($post_arr['selected_config'], 'rb');
            $line = fgets($fp);
            $Tline = explode("//", $line);
            if (empty($Tline[1])) {
                //echo "Check the file used";
                array_push($error_array, lng("Check the file used"));
            } else {
                $project_short_name = trim($Tline[1]);

                //Verifie if the project is already installed
                $resul = $this->Project_dataAccess->select_project_id_by_label($project_short_name);

                //print_test($resul);
                if (!empty($resul)) {
                    //echo "<h2>Project already installed</h2>";
                    //echo "<h2>".anchor('home',lng('Back'))."</h2>";
                    array_push($error_array, lng("Project already installed"));
                } else {
                    //Save the file in a temporal location
                    $project_specific_config_folder = get_ci_config('project_specific_config_folder');
                    $f_new_temp = fopen($project_specific_config_folder . "temp/install_config_" . $project_short_name . ".php", 'w+');
                    rewind($fp);
                    while (($line = fgets($fp)) !== false) {
                        //fputs($f_new_temp, $line. "\n");
                        fputs($f_new_temp, $line);
                        //echo "$line<br>";
                    }
                    fclose($f_new_temp);
                    //Retrieve the content to verify the validity of the file
                    $temp_table_config = $this->entity_configuration_lib->get_new_install_config($project_short_name);
                    if (!valid_install_configuration_file($temp_table_config)) {
                        array_push($error_array, "Not a valid configuration file");
                    } else {
                        copy($project_specific_config_folder . "temp/install_config_" . $project_short_name . ".php", $project_specific_config_folder . "install_config_" . $project_short_name . ".php");
                        redirect('project/save_new_project_part2/' . $project_short_name);
                    }
                }
            }
        }
        if (!empty($error_array)) {
            //print_r($error_array);
            $this->project_install_result($error_array, array(), 'new_project_editor');
        }
    }

    /**
     * perform various tasks related to the second part of saving a new project, including database creation, 
     * configuration updates, table population, stored procedure updates, and adding project-related data to the database.
     */
    public function save_new_project_part2($project_short_name, $verbose = false, $reloadTimes = -1)
    {
        $error_array = array();
        $success_array = array();
        if ($verbose)
            echo "<h2>Import done</h2>";
        array_push($success_array, lng('Setup file imported'));
        $database_name = get_ci_config('project_db_prefix') . $project_short_name;

        $res_sql = $this->Project_dataAccess->create_project_db($database_name);

        if ($verbose)
            echo "<h2>New database created</h2>";
        array_push($success_array, lng('New database created'));
        //setting CI database configuration
        if ($reloadTimes = -1) {
            $this->add_database_config($project_short_name);
        }
        //sleep to wait for config to be update (to correct for something really sure)
        sleep(2);
        if (!$this->dbConnectionConfigExist($project_short_name) && $reloadTimes != 0) {
            if ($reloadTimes == -1) {
                $reloadTimes = 5;
            }
            $reloadTimes = $reloadTimes - 1;
            $error_array = array();
            $success_array = array();
            redirect('project/save_new_project_part2/' . $project_short_name . '/0/' . $reloadTimes);
        }
        //echo "<h2>initialise database</h2>";
        // Populate created database
        $this->populate_created_database($project_short_name);
        //create initial tables
        $this->populate_common_tables($project_short_name);
        //echo "<h2>initialise stored procedures</h2>";
        $this->update_stored_procedure('init', FALSE, $project_short_name, TRUE);
        if ($verbose)
            echo "Database initialised";
        array_push($success_array, 'Database initialised');
        $res_install_config = $this->entity_configuration_lib->get_install_config($project_short_name);
        //print_test($res_install_config); exit;
        $project_title = "Project default name";
        if (!empty($res_install_config['project_title']))
            $project_title = $res_install_config['project_title'];
        /////$this->session->set_userdata('project_db',$project_short_name);
        $ref_tables = array();
        $generated_tables = array();
        $foreign_key_constraints = array();
        //echo "<h3>creating project spécific tables</h3>";
        //reference tables
        $sql_ref = "";
        ////print_test($res_install_config['reference_tables']);
        if (!empty($res_install_config['reference_tables'])) {
            foreach ($res_install_config['reference_tables'] as $key => $value) {
                array_push($ref_tables, $key);
                $sql_ref .= $this->create_reference_table($key, $value, $project_short_name);
                $sql_ref .= "<br/><br/>";
            }
        }
        //ecaho $sql_ref."<br/>";
        //tablesaa
        //print_test($res_install_config['config']);
        $sql_table = "";
        if (!empty($res_install_config['config'])) {
            foreach ($res_install_config['config'] as $key_config => $config_values) {
                array_push($generated_tables, $key_config);
                //$sql_table.=$this->create_table_config($config_values,$project_short_name);
                //$sql_table.=$this->manage_stored_procedure_lib->create_table_config($config_values,$project_short_name);
                //$sql_table.="<br/><br/>";
                $this->populate_common_tables($project_short_name, $key_config);
                $foreign_key = $this->get_froreign_keys_constraint($key_config, $config_values);
                if (!empty($foreign_key)) {
                    array_push($foreign_key_constraints, $foreign_key);
                }
            }
            /// ADD CONTRAINTS
        }
        //		echo $sql_table."<br/>";
        $this->populate_common_tables_views($project_short_name);
        if ($verbose)
            echo "<h3>Project specific tables created</h3>";
        array_push($success_array, 'Project specific tables created');

        $res_sql = $this->Project_dataAccess->update_installation_info($project_short_name);

        $res_sql = $this->Project_dataAccess->insert_installation_info($ref_tables, $generated_tables, $foreign_key_constraints, $project_short_name);

        if ($verbose)
            echo "<h3>Project specific stored procedure created</h3>";
        array_push($success_array, 'Project specific stored procedure created');
        // stored procedures
        if (!empty($res_install_config['config'])) {
            foreach ($res_install_config['config'] as $key_config => $config_values) {
                $this->update_stored_procedure($key_config, FALSE, $project_short_name, TRUE);
            }
        }
        if (!empty($res_install_config['reference_tables'])) {
            foreach ($res_install_config['reference_tables'] as $key => $value) {
                $this->update_stored_procedure($key, FALSE, $project_short_name);
            }
        }
        //add screening_values if available
        if (!empty($res_install_config['screening'])) {
            $this->update_screening_values($res_install_config['screening'], $project_short_name);
            array_push($success_array, 'Screening configuration added');
        }
        //adding Qality assessment values
        if (!empty($res_install_config['qa'])) {
            $this->update_qa_values($res_install_config['qa'], $project_short_name);
            array_push($success_array, 'Quality assessment configuration added');
        }

        //$sql_update_config="UPDATE config SET project_title ='".$project_title."',project_description='Project description goes here',run_setup=0 WHERE config_id =1 ";
        $creator = 1;
        $creator = $this->session->userdata('user_id');
        //	$sql_add_config="INSERT INTO userproject  (	user_id,project_id,	user_role,added_by	 ) VALUES ('".$project_title."','Project description goes here',".$creator.")";
        //echo $sql_add_config;
        ///	$res_sql = $this->manage_mdl->run_query($sql_add_config,false,$project_short_name);
        //print_test($res_sql);

        $res_sql = $this->Project_dataAccess->insert_into_project($project_short_name, $project_title, $creator);

        //Add the user as project admin
        if (!has_usergroup(1)) {
            $project_id = $this->get_last_added_project();

            $res_sql = $this->Project_dataAccess->insert_into_userproject($creator, $project_id);
        }
        // Update config editor according to general values
        $editor_url = get_adminconfig_element('editor_url');
        $editor_generated_path = get_adminconfig_element('editor_generated_path');
        if (!empty($editor_url) and !empty($editor_generated_path)) {

            $res_sql = $this->Project_dataAccess->update_config($editor_url, $editor_generated_path, $project_short_name);
        }
        if ($verbose)
            echo "<h3>New project added</h3>";
        array_push($success_array, 'New project added');
        //echo "<h2>Installation done</h3>";
        //echo anchor('home','<h2> Start the Application </h3>');
        $this->project_install_result($error_array, $success_array);
    }

    //remove the project from the database
    function remove_project($project_id)
    {
        $data['page'] = 'install/frm_install_result';
        $data['left_menu_admin'] = True;
        $data['array_success'] = array();
        //$detail_project=$this->DBConnection_mdl->get_row_details ( 'project',$project_id);
        $detail_project = $this->DBConnection_mdl->get_row_details('get_detail_project', $project_id, true);
        //print_test($detail_project); exit;
        //$res=$this->DBConnection_mdl->remove_element($project_id,'project');
        $res = $this->DBConnection_mdl->remove_element($project_id, 'remove_project', True);
        /*
         * Message de confirmation ou erreur
         */
        if ($res) {
            set_top_msg("Project " . $detail_project['project_title'] . " uninstalled !");
        } else {
            set_top_msg(" Operation failed ", 'error');
        }
        array_push($data['array_success'], 'Project removed');

        $res_sql = $this->Project_dataAccess->update_userproject($project_id);

        array_push($data['array_success'], 'Project unassigned to users');
        $database_name = $this->config->item('project_db_prefix') . $detail_project['project_label'];

        $res_sql = $this->Project_dataAccess->drop_project_db($database_name);

        array_push($data['array_success'], 'Database dropped');
        array_push($data['array_success'], 'Uninstall done');
        $data['next_operation_button'] = "";
        $data['page_title'] = lng('Uninstall the project : ') . $detail_project['project_title'];
        $data['next_operation_button'] = get_top_button('all', 'Back to the list of projects ', 'project/projects_list', 'Back to the list of projects', '', '', ' btn-success ', FALSE);
        $this->load->view('shared/body', $data);
    }

    //handle the validation process before removing a project
    function remove_project_validation($project_id)
    {
        $data['page'] = 'install/frm_install_result';
        $data['left_menu_admin'] = True;
        $detail_project = $this->DBConnection_mdl->get_row_details('project', $project_id);
        $data['array_warning'] = array('You want to unistall the project : ' . $detail_project['project_title'] . '  .The opération cannot be undone !');
        $data['array_success'] = array();
        $data['next_operation_button'] = "";
        $data['page_title'] = lng('Uninstall the project : ') . $detail_project['project_title'];
        $data['next_operation_button'] = get_top_button('all', 'Continue uninstall', 'project/remove_project/' . $project_id, 'Continue to uninstall', '', '', ' btn-success ', FALSE);
        $data['next_operation_button'] .= " &nbsp &nbsp &nbsp" . get_top_button('all', 'Cancel', 'admin/projects_list', 'Cancel', '', '', ' btn-danger ', FALSE);
        $this->load->view('shared/body', $data);
    }

    //responsible for displaying the installation result after installing or updating a project
    private function project_install_result($array_error = array(), $array_success = array(), $type = "new_project")
    {
        //$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'project/choose_project/' );
        $data['page'] = 'install/frm_install_result';
        $data['left_menu_admin'] = True;
        $data['array_error'] = $array_error;
        $data['array_success'] = $array_success;
        $data['next_operation_button'] = "";
        if ($type == "update_project") {
            $back_link = "install/install_form";
            $success_link = "home";
            $success_title = "Go back to the project";
            $page_title = "Update project";
        } elseif ($type == "new_project_editor") {
            $back_link = "project/new_project_editor";
            $success_link = "project/projects_list";
            $success_title = "Go back to project list";
            $page_title = "New project";
        } elseif ($type == "update_project_editor") {
            $back_link = "install/install_form_editor";
            $success_link = "manager/projects_lis";
            $success_title = "Go back to project list";
            $page_title = "New project";
        } else {
            $back_link = "project/new_project";
            $success_link = "project/projects_list";
            $success_title = "Go back to the project";
            $page_title = "Update project";
        }
        $data['page_title'] = lng($page_title);
        if (!empty($array_error)) {
            $data['next_operation_button'] = get_top_button('all', 'Back', $back_link, 'Back', '', '', ' btn-danger ', FALSE);
        } else {
            $data['next_operation_button'] = get_top_button('all', $success_title, $success_link, $success_title, '', '', ' btn-success ', FALSE);
        }
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /**
     * adding a new database configuration to the "database.php" file in the "config" folder of the application, 
     * allowing the application to connect to the newly created project database using the specified settings
     */
    private function add_database_config($project_short_name)
    {
        $database_config = '$db' . "['" . $project_short_name . "'] = array(
		'dsn'	=> '',
		'hostname' => '" . $this->config->item('project_db_host') . "',
		'username' => '" . $this->config->item('project_db_user') . "',
		'password' => '" . $this->config->item('project_db_pass') . "',
		'database' => '" . $this->config->item('project_db_prefix') . $project_short_name . "',
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => TRUE
);";
        $f_config = fopen("relis_app/config/database.php", 'a+');
        fputs($f_config, "\n" . $database_config . "\n");
        fclose($f_config);
    }

    /**
     * checks the existence of a database connection configuration by loading the configuration file, 
     * retrieving the specified configuration, and attempting to establish a connection using the mysqli driver
     */
    private function dbConnectionConfigExist($connectionName)
    {
        $db = [];
        //  Load the database config file.
        if (file_exists($file_path = APPPATH . 'config/database.php')) {
            include($file_path);
        }
        if (empty($db[$connectionName])) {
            return false;
        }
        $config = $db[$connectionName];
        //  Check database connection if using mysqli driver
        if ($config['dbdriver'] === 'mysqli') {
            $mysqli = new mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);
            if (!$mysqli->connect_error) {
                $mysqli->close();
                return true;
            }
            $mysqli->close();
        }
        return false;
    }

    /**
     * loads a database connection for the specified project, reads the initial SQL queries from a file "project_initial_query.sql", 
     * and executes each query on the database. This populates the created database with the necessary initial data or schema defined in the SQL file
     */
    private function populate_created_database($project_short_name)
    {
        $this->db2 = $this->load->database($project_short_name, TRUE);
        $db_sql = file_get_contents("relis_app/libraries/table_config/project/init_sql/project_initial_query.sql");
        $T_db_sql = explode(';;;;', $db_sql);
        foreach ($T_db_sql as $key => $v_sql) {
            $sql = trim($v_sql);
            //print_test($sql);
            if (!empty($sql)) {
                $res = $this->db2->query($sql);
                //print_test($res);
            }
        }
    }

    //populate common tables for all projects in the specified database or the current project database by retrieving the table configurations
    private function populate_common_tables($target_db = 'current', $config = 'init')
    {
        $target_db = ($target_db == 'current') ? project_db() : $target_db;
        //	$configs=array('assignment_screen','screening','assignment_screen_validate','screening_validate','operations');
        if ($config == 'init') {
            $configs = array(
                'config',
                'exclusioncrieria',
                'inclusioncriteria',
                'research_question',
                'affiliation',
                'papers_sources',
                'search_strategy',
                'papers',
                'author'
                ,
                'paper_author',
                'venue',
                'screen_phase',
                'screening',
                'screen_decison'
                ,
                'operations',
                'qa_questions',
                'qa_responses',
                'qa_result',
                'qa_assignment'
                ,
                'qa_validation_assignment',
                'assignation',
                'debug'
            );
        } else {
            $configs = array($config);
        }
        foreach ($configs as $key => $value) {
            //$tab_config=get_table_config($value);
            //$res=$this->create_table_config($tab_config,$target_db);
            //$res=$this->manage_stored_procedure_lib->create_table_config($tab_config,$target_db);
            //create tables
            //print_test($values);
            $table_configuration = get_table_configuration($value, $target_db);
            $res = create_table_configuration($table_configuration, $target_db);
        }
    }

    /**
     * update stored procedures in the specified database or the current project database based on the old and new configuration names. 
     * It handles various operations such as getting, counting, removing, adding, updating, and getting details of elements using stored procedures
     */
    private function update_stored_procedure($config, $verbose = FALSE, $target_db = 'current', $add_constraint = False)
    {
        $target_db = ($target_db == 'current') ? project_db() : $target_db;
        $old_configs = array();
        $new_configs = array();
        if ($config == 'init') {
            $old_configs = array('exclusion', 'papers');
            $new_configs = array(
                'exclusioncrieria',
                'inclusioncriteria',
                'research_question',
                'affiliation',
                'papers_sources',
                'search_strategy',
                'papers',
                'author'
                ,
                'paper_author',
                'venue',
                'screen_phase',
                'screening',
                'screen_decison',
                'str_mng'
                ,
                'config',
                'operations',
                'qa_questions',
                'qa_responses',
                'qa_result',
                'qa_assignment',
                'qa_validation_assignment',
                'assignation',
                'debug'
            );
            //$configs=array('assignation','author','class_scheme','config','exclusion','papers','paper_author','ref_exclusioncrieria','str_mng','venue');
            //$configs=get_relis_common_configs();
        } else {
            //$old_configs=array($config);
            $new_configs = array($config);
        }
        foreach ($old_configs as $k => $config) {
            /*
             * Stored procedure to get list of element
             */
            $this->manage_stored_procedure_lib->create_stored_procedure_get($config, TRUE, $verbose, $target_db);
            /*
             * Stored procedure to count number of elements (used for navigation link)
             */
            if ($config == 'papers')
                $this->manage_stored_procedure_lib->create_stored_procedure_count($config, TRUE, $verbose, $target_db);
            /*
             * Stored procedure to remove element
             */
            if ($config != 'papers')
                $this->manage_stored_procedure_lib->create_stored_procedure_remove($config, TRUE, $verbose, $target_db);
            /*
             * Stored procedure to add element
             */
            if ($config != 'papers')
                $this->manage_stored_procedure_lib->create_stored_procedure_add($config, TRUE, $verbose, $target_db);
            /*
             * Stored procedure to update element
             */
            if ($config != 'papers')
                $this->manage_stored_procedure_lib->create_stored_procedure_update($config, TRUE, $verbose, $target_db);
            /*
             * Stored procedure to get detail element (select row)
             */
            if ($config != 'papers')
                $this->manage_stored_procedure_lib->create_stored_procedure_detail($config, TRUE, $verbose, $target_db);
            if ($add_constraint) {
                //$this->manage_stored_procedure_lib->add_froreign_keys_constraint($config,TRUE,$verbose,$target_db);
            }
        }
        foreach ($new_configs as $k => $config) {
            if ($config == 'papers')
                $this->manage_stored_procedure_lib->create_stored_procedure_count($config, TRUE, $verbose, $target_db);
            create_stored_procedures($config, $target_db, False);
        }
    }

    /**
     * creates a reference table in the specified database or the current project database. 
     * It handles the creation of the table, insertion of initial values, and updating the ref_tables table to include the new reference table
     */
    private function create_reference_table($ref_conf, $ref_value, $target_db = 'current')
    {
        $target_db = ($target_db == 'current') ? project_db() : $target_db;
        $table_name = $ref_conf;
        $desc = $ref_value['ref_name'];
        //
        $del_line = "DROP TABLE IF EXISTS " . $table_name . ";";
        $res_sql = $this->manage_mdl->run_query($del_line, False, $target_db);
        //print_test($res_sql);
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
		`ref_id` int(11) NOT NULL AUTO_INCREMENT,
		  `ref_value` varchar(50) NOT NULL,
		  `ref_desc` varchar(250) DEFAULT NULL,
		  `ref_active` int(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`ref_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        $res_sql = $this->manage_mdl->run_query($sql, False, $target_db);
        //print_test($res_sql);
        //Add initial values
        $sql1 = "";
        if (!empty($ref_value['values'])) {
            $sql1 = "INSERT INTO " . $table_name . " ( ref_value, ref_desc) VALUES ";
            $ri = 1;
            foreach ($ref_value['values'] as $r_key => $r_value) {
                if ($ri == 1) {
                    $sql1 .= "('" . $r_value . "','" . $r_value . "')";
                } else {
                    $sql1 .= ",('" . $r_value . "','" . $r_value . "')";
                }
                $ri++;
            }
            $sql1 .= ";";
            $res_sql = $this->manage_mdl->run_query($sql1, False, $target_db);
        }
        //Add in the list of reference tables
        //print_test($res_sql);
        $sql2 = " INSERT INTO ref_tables (reftab_label, reftab_table, reftab_desc, reftab_active) VALUES
('" . $ref_conf . "', '" . $ref_conf . "', '" . $desc . "', 1);";
        $res_sql = $this->manage_mdl->run_query($sql2, False, $target_db);
        //print_test($res_sql);
        return "$del_line $sql $sql1 $sql2";
    }

    /**
     * iterate over the fields in a table configuration and identifies fields that have foreign key constraints defined. 
     * It generates SQL statements to drop those foreign key constraints from the table. 
     * The function returns the complete SQL query for dropping the foreign key constraints, or an empty string if there are no constraints to drop
     */
    private function get_froreign_keys_constraint($config, $table_config)
    {
        $sql_constraint_header = "ALTER TABLE " . $table_config['table_name'];
        $sql_constraint_drop = "";
        $i = 1;
        foreach ($table_config['fields'] as $key => $value) {
            if (!empty($value['input_type']) and !empty($value['input_select_source']) and !empty($value['input_select_values']) and ($value['input_type'] == 'select') and ($value['input_select_source'] == 'table')) {
                if (!(isset($value['category_type']) and ($value['category_type'] == 'WithSubCategories' or $value['category_type'] == 'WithMultiValues'))) { //Le schamps qui sonts dans les tables association
                    $conf = explode(";", $value['input_select_values']);
                    $ref_table = $conf[0];
                    if ($ref_table != "users") {
                        $constraint = $key;
                        if ($i != 1) {
                            $sql_constraint_drop .= ',';
                        }
                        $sql_constraint_drop .= " DROP FOREIGN KEY  " . $config . "_" . $constraint . "   ";
                        $i++;
                    }
                }
            }
        }
        $sql_query = "";
        if (!empty($sql_constraint_drop)) {
            $sql_query = $sql_constraint_header . $sql_constraint_drop . ";";
        }
        return $sql_query;
    }

    //populates views for common tables in a specified database
    private function populate_common_tables_views($target_db = 'current')
    {
        $target_db = ($target_db == 'current') ? project_db() : $target_db;
        $configs = array('papers', 'assignation', 'qa_assignment', 'author');
        foreach ($configs as $key => $value) {
            $table_configuration = get_table_configuration($value);
            if (!empty($table_configuration['table_views'])) {
                foreach ($table_configuration['table_views'] as $key => $view_value) {
                    create_view($view_value, $target_db);
                }
            }
        }
    }

    /**
     * updates the screening values in the database by updating the general screening configuration, adding new screening phases, 
     * and updating the values in the reference tables for exclusion criteria, source papers, and search strategy.
     */
    private function update_screening_values($screening, $target_db = 'current')
    {
        $target_db = ($target_db == 'current') ? project_db() : $target_db;
        $this->db3 = $this->load->database($target_db, TRUE);
        /*
              $screening=array(
                      'review_per_paper'=>2,
                      'conflict_type'=>'IncludeExclude',
                      'conflict_resolution'=>'Unanimity',
                      'validation_percentage'=>20,
                      'validation_assigment_mode'=>'Normal',
                      'phases'=>array(
                              '1'=>array(
                                      'title'=>'Phase 1',
                                      'description'=>'Screen per title',
                                      'fields'=>'Title',
                              ),
                              '2'=>array(
                                      'title'=>'Phase 2',
                                      'description'=>'Screen per title  and abstract',
                                      'fields'=>'Title|Abstract',
                              ),
                      ),
                      'exclusion_criteria'=>array('Criteria 1','Criteria 2','Criteria3' ),
                      'source_papers'=>array('Scopus','IEEE','Science direct' ),
                      'search_startegy'=>array('Snowballing','Database search' ),
              );
              */
        //Addd general values in configuration
        $config['screening_reviewer_number'] = !empty($screening['review_per_paper']) ? $screening['review_per_paper'] : "2";
        $config['screening_screening_conflict_resolution'] = !empty($screening['conflict_resolution']) ? $screening['conflict_resolution'] : "Unanimity";
        $config['screening_conflict_type'] = !empty($screening['conflict_type']) ? $screening['conflict_type'] : "IncludeExclude";
        $config['validation_default_percentage'] = !empty($screening['validation_percentage']) ? $screening['validation_percentage'] : "20";
        $config['screening_validator_assignment_type'] = !empty($screening['validation_assigment_mode']) ? $screening['validation_assigment_mode'] : "Normal";
        $config['screening_on'] = 1;
        $result = $this->db3->update('config', $config, "config_id=1");
        //add new phases
        if (!empty($screening['phases'])) {
            //clean existing
            $result = $this->db3->update('screen_phase', array('screen_phase_active' => 0));
            //get total number
            $nbr_phase = count($screening['phases']);
            $i = 1;
            $all_phases = array();
            foreach ($screening['phases'] as $key => $value) {
                $conf_phase['phase_title'] = !empty($value['title']) ? $value['title'] : "Phase " . $i;
                $conf_phase['description'] = !empty($value['description']) ? $value['description'] : "";
                $conf_phase['displayed_fields'] = !empty($value['fields']) ? $value['fields'] : "Title";
                $conf_phase['screen_phase_final'] = ($nbr_phase == $i) ? '1' : "0";
                $conf_phase['screen_phase_order '] = $i * 10;
                $conf_phase['added_by'] = active_user_id();
                array_push($all_phases, $conf_phase);
                $i++;
            }
            //print_test($all_phases);
            $result = $this->db3->insert_batch('screen_phase', $all_phases);
            //print_test($result);
        }
        // Add exclusion criteria , sourcepapers,and searc_strategy
        $screen_configs_to_save = array(
            'exclusion_criteria' => array('table' => 'ref_exclusioncrieria'),
            'source_papers' => array('table' => 'ref_papers_sources'),
            'search_startegy' => array('table' => 'ref_search_strategy'),
        );
        foreach ($screen_configs_to_save as $s_config_id => $s_config_value) {
            if (!empty($screening[$s_config_id])) {
                //clean existing
                $result = $this->db3->update($s_config_value['table'], array('ref_active' => 0));
                $all_elements = array();
                foreach ($screening[$s_config_id] as $key => $value) {
                    $conf_element['ref_value'] = $value;
                    $conf_element['ref_desc'] = $value;
                    array_push($all_elements, $conf_element);
                }
                //	print_test($all_elements);
                $result = $this->db3->insert_batch($s_config_value['table'], $all_elements);
                ////print_test($result);
            }
        }
    }

    /**
     * updates the QA values in the database by either overriding existing QA data or updating the general QA configuration, questions, and responses
     */
    private function update_qa_values($qa, $target_db = 'current')
    {
        $res_install_config = $this->entity_configuration_lib->get_install_config($target_db);
        $qa_action = $res_install_config['qa_action'];
        $target_db = ($target_db == 'current') ? project_db() : $target_db;
        $this->db3 = $this->load->database($target_db, TRUE);
        if ($qa_action == 'override') {
            $this->Project_dataAccess->delete_update_qa();
        }
        $config['qa_cutt_off_score'] = !empty($qa['cutt_off_score']) ? $qa['cutt_off_score'] : "2";
        $config['qa_on'] = 1;
        $result = $this->db3->update('config', $config, "config_id=1");
        //questions
        if (!empty($qa['questions'])) {
            $all_phases = array();
            $i = 1;
            $result = $this->db3->update('qa_questions', array('question_active' => 0));
            foreach ($qa['questions'] as $key => $value) {
                $conf_phase['question'] = $value['title'];
                array_push($all_phases, $conf_phase);
                $i++;
            }
            $result = $this->db3->insert_batch('qa_questions', $all_phases);
        }
        // Add resposes
        if (!empty($qa['responses'])) {
            $all_responses = array();
            $i = 1;
            $result = $this->db3->update('qa_responses', array('response_active' => 0));
            $conf_phase = array();
            foreach ($qa['responses'] as $key => $value) {
                $conf_phase['response'] = $value['title'];
                $conf_phase['score'] = $value['score'];
                array_push($all_responses, $conf_phase);
                $i++;
            }
            $result = $this->db3->insert_batch('qa_responses', $all_responses);
        }
    }

    //etrieves the ID of the last added project in the database
    private function get_last_added_project()
    {
        $res = $this->Project_dataAccess->get_last_added_project();
        if (!empty($res['project_id']))
            return $res['project_id'];
        else
            return 0;
    }

    //set the active project in the sessions by retrieving the project details based on the provided project ID
    public function set_project($project_id = 0)
    {
        if (!empty($project_id)) {
            $item_data = $this->DBConnection_mdl->get_row_details('project', $project_id);
            if (!empty($item_data)) {
                $this->session->set_userdata('project_db', $item_data['project_label']);
                $this->session->set_userdata('project_id', $project_id);
                $this->session->set_userdata('project_title', $item_data['project_title']);
                $this->session->set_userdata('project_public', $item_data['project_public']);
                $this->session->set_userdata('working_perspective', 'screen');
                $this->session->set_userdata('current_screen_phase', 0);
            }
        }
        redirect('screening/screening');
    }

    /**
     * switch between projects. When called with the appropriate parameters, 
     * it updates the active project in the session and redirects the user to the screening page of the selected project
     */
    public function set_project2($projet_label, $project_id = 0, $project_title = "")
    {
        if (!empty($projet_label)) {
            $this->session->set_userdata('project_db', $projet_label);
            $this->session->set_userdata('project_id', $project_id);
            $this->session->set_userdata('project_title', urldecode(urldecode($project_title)));
        }
        redirect('screening/screening');
    }

    //set the active project in the system
    public function set_project3($projet_label, $project_id = 0, $project_title = "")
    {
        old_version();
        if (!empty($projet_label)) {
            $this->session->set_userdata('project_db', $projet_label);
            $this->session->set_userdata('project_id', $project_id);
            $this->session->set_userdata('project_title', urldecode(urldecode($project_title)));
        }
        //redirect('home');
    }

    /*
        select a project and set it as the active project in the session. 
        The session data can then be used throughout the application to perform actions or retrieve 
        information specific to the selected project
    */
    public function set_project4($project_id = 0)
    {
        if (!empty($project_id)) {
            $item_data = $this->DBConnection_mdl->get_row_details('project', $project_id);
            if (!empty($item_data)) {
                $this->session->set_userdata('project_db', $item_data['project_label']);
                $this->session->set_userdata('project_id', $project_id);
                $this->session->set_userdata('project_title', $item_data['project_title']);
            }
        }
        redirect('screening/screening');
    }

    //redirection to projects_list page
    public function choose_project()
    {
        redirect('project/projects_list');
    }
}