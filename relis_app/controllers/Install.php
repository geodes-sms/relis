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
 * This controller contain function to install and update SLR projects
 */
defined('BASEPATH') or exit('No direct script access allowed');
class Install extends CI_Controller
{
	/*
	 * 
	 * En cours de réalisation utilisé pour l'installation
	 */
	function __construct()
	{
		parent::__construct();
	
		
	}

	public function index()
	{
		redirect('home');
	}

	//function is used to display the ReLiS editor page.
	public function relis_editor($type = "client")
	{
		$data['page_title'] = lng('ReLiS editor');
		$data['page'] = 'install/relis_editor';
		$data['editor_url'] = $this->config->item('editor_url');
		$data['top_buttons'] = "";
		if ($type == 'admin') {
			$data['left_menu_admin'] = True;
			$data['editor_url'] = get_adminconfig_element('editor_url');
			if (has_usergroup(1)) {
				$data['top_buttons'] = get_top_button('all', lng_min('Manage editor server'), 'home/manage_editor', lng_min('Manage editor server'), ' fa-gear', '', ' btn-info ');
			}
		} else {
			$data['editor_url'] = get_appconfig_element('editor_url');
		}
		$this->check_editor($data['editor_url']);
		//exit;
		$data['top_buttons'] .= get_top_button('back', 'Back', 'manage');
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	/**
	 * ensure that the ReLiS editor server is running before loading the editor page. If the server is not running, it attempts to start it
	 */
	private function check_editor($url)
	{
		$status = exec("netstat -lnp | grep 8080");
		if (!empty($status)) { // server already runnning
		} else {
			$message = exec("/u/relis/tomcat/bin/startup.sh");
			sleep(2);
		}
	}

	/**
	 * display the installation form, allowing the user to update the project. 
	 * The form may include fields for entering project information or selecting options related to the installation/update process.
	 */
	public function install_form()
	{
		$data['page_title'] = lng('Update project');
		$data['top_buttons'] = get_top_button('all', lng_min('Load from editor'), 'install/install_form_editor', lng_min('Load from editor'), ' fa-exchange', '', ' btn-info ');
		$data['top_buttons'] .= get_top_button('back', 'Back', 'home');
		$data['page'] = 'install/frm_install';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	/**
	 * display the installation form with editor-related options. 
	 * It retrieves the available projects and their associated syntax and generated files
	 */
	public function install_form_editor()
	{
		$project_published = project_published();
		$data['project_published'] = $project_published;
		//$dir=$this->config->item('editor_generated_path');
		//$editor_url=$this->config->item('editor_url');
		$dir = get_appconfig_element('editor_generated_path');
		$editor_url = get_appconfig_element('editor_url');
		$path_separator = path_separator(); // used to diferenciate windows and linux server
		$Tprojects = array();
		if (is_dir($dir)) {
			$files = array_diff(scandir($dir), array('.', '..', ".metadata"));
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
		$data['page_title'] = lng('Update project');
		$editor_url = $this->config->item('editor_url');
		$data['top_buttons'] = "";
		if (!$project_published)
			$data['top_buttons'] .= get_top_button('all', 'Upload configuration file', 'install/install_form', 'Upload configuration file', ' fa-upload', '', ' btn-info ');
		$data['top_buttons'] .= "<li>" . anchor('install/relis_editor', '<button class="btn btn-primary">  Open editor </button></li>', 'title="Open editor"') . "</li>";
		$data['top_buttons'] .= get_top_button('back', 'Back', 'manage');
		$data['page'] = 'install/frm_install_editor';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}
	
	/**
	 * save the installation form data, clean the previous installation, create database tables and stored procedures, update the project information, and handle screening and quality assessment configuration updates.
	 */
	public function save_install_form_part2($verbose = FALSE)
	{
		$error_array = array();
		$success_array = array();
		if ($verbose)
			echo "<h2>Import done</h2>";
		array_push($success_array, 'Setup file imported');
		//Read installation configuration
		$res_install_config = $this->entity_configuration_lib->get_install_config();
		//	print_test($res_install_config);
		$project_short_name = $res_install_config['project_short_name'];
		//cleaning old installation
		if (!empty($res_install_config['class_action']) and $res_install_config['class_action'] != 'override') {
			array_push($success_array, 'Classification no override');
		} else {
			$this->clean_previous_installation();
			if ($verbose)
				echo "<h2>Previous installation cleaned</h2>";
			array_push($success_array, 'Previous installation cleaned');
			//create database sql script
			$ref_tables = array();
			$generated_tables = array();
			$foreign_key_constraints = array();
			//echo "<h3>creating project spécific tables</h3>";
			//reference tables
			$sql_ref = "";
			if (!empty($res_install_config['reference_tables'])) {
				foreach ($res_install_config['reference_tables'] as $key => $value) {
					array_push($ref_tables, $key);
					$sql_ref .= $this->create_reference_table($key, $value);
					$sql_ref .= "<br/><br/>";
				}
			}
			//echo $sql_ref."<br/>";
			//tables
			$sql_table = "";
			if (!empty($res_install_config['config'])) {
				foreach ($res_install_config['config'] as $key_config => $config_values) {
					array_push($generated_tables, $key_config);
					//$sql_table.=$this->create_table_config($config_values);
					//$sql_table.="<br/><br/>";
					$this->populate_common_tables('current', $key_config);
					$foreign_key = $this->get_froreign_keys_constraint($key_config, $config_values);
					if (!empty($foreign_key)) {
						array_push($foreign_key_constraints, $foreign_key);
					}
				}
			}
			//echo $sql_table."<br/>";
			if ($verbose)
				echo "<h2>New project specific tables created</h2>";
			array_push($success_array, 'New project specific tables created');
			$sql_install_info = "UPDATE installation_info SET  install_active=0 where install_active = 1 ; ";
			$res_sql = $this->manage_mdl->run_query($sql_install_info);
			$sql_install_info = "INSERT INTO installation_info (reference_tables,generated_tables,foreign_key_constraint) VALUES ('" . json_encode($ref_tables) . "','" . json_encode($generated_tables) . "','" . json_encode($foreign_key_constraints) . "')   ; ";
			//echo $sql_install_info;
			$res_sql = $this->manage_mdl->run_query($sql_install_info);
			//echo "<h3>creating project stored procedures</h3>";
			// stored procedures
			if (!empty($res_install_config['config'])) {
				foreach ($res_install_config['config'] as $key_config => $config_values) {
					//$this->update_stored_procedure($key_config);
					$this->update_stored_procedure($key_config, FALSE, 'current', TRUE);
				}
			}
			if (!empty($res_install_config['reference_tables'])) {
				foreach ($res_install_config['reference_tables'] as $key => $value) {
					$this->update_stored_procedure($key);
				}
			}
			if ($verbose)
				echo "<h2>New project specific stored procedures created</h2>";
			array_push($success_array, 'New project specific stored procedures created');
			$project_title = "Review";
			if (!empty($res_install_config['project_title'])) {
				$project_title = $res_install_config['project_title'];
			}
			$sql_update_config = "UPDATE config SET project_title ='" . $project_title . "',project_description='" . $project_title . "',run_setup=0 WHERE config_id =1 ";
			//$this->db2->query("UPDATE config SET project_title ='".$project_title."',project_description='Project description goes here',run_setup=0 WHERE config_id =1 ");
			//$res_sql = $this->manage_mdl->run_query($sql_update_config);
			$sql_update_project = "UPDATE  projects  SET project_title='" . $project_title . "',project_description='" . $project_title . "' WHERE project_label LIKE '" . $project_short_name . "'";
			$res_sql = $this->manage_mdl->run_query($sql_update_project, false, 'default');
			//echo $sql_update_project;
			if ($verbose)
				echo "<h2>Project updated</h2>";
			array_push($success_array, 'Project updated');
			//update screening_values
		}
		//add screening_values if available
		if (!empty($res_install_config['screening']) and !(!empty($res_install_config['screen_action']) and $res_install_config['screen_action'] != 'override')) {
			$this->update_screening_values($res_install_config['screening'], $project_short_name);
			array_push($success_array, 'Screening configuration set');
		} else {
			if (empty($res_install_config['screening'])) {
				set_appconfig_element('screening_on', 0);
			}
		}
		//adding Qality assessment values
		//	print_test($res_install_config);
		if (!empty($res_install_config['qa']) and !empty($res_install_config['qa_action'] and $res_install_config['qa_action'] == 'override')) {
			$this->update_qa_values($res_install_config['qa'], $project_short_name);
			array_push($success_array, 'Quality assessment configuration set');
		} else {
			if (empty($res_install_config['qa'])) {
				set_appconfig_element('qa_on', 0);
			}
			array_push($success_array, 'Retained Quality assessment configuration set');
		}
		$this->project_install_result($error_array, $success_array, 'update_project');
		//echo "<h2>Installation done</h3>";
		//echo anchor('home','<h2> Start the Application </h3>');
	}
	
	/**
	 *  handle the upload and validation of the installation configuration file. 
	 * If the file is valid, it is copied to the appropriate location and the function redirects to the save_install_form_part2() method to continue the installation process.
	 */
	public function save_install_form()
	{
		$error_array = array();
		$success_array = array();
		if ($_FILES["install_config"]["error"] > 0) {
			//echo "Error: " . $_FILES["file"]["error"] . "<br />";
			array_push($error_array, "Error: " . file_upload_error($_FILES["install_config"]["error"]));
		} elseif ($_FILES["install_config"]["type"] !== "application/octet-stream") {
			//echo "File must be a .php";
			array_push($error_array, "File must be a .php");
		} else {
			//$monfichier="";
			//exit;
			$fp = fopen($_FILES['install_config']['tmp_name'], 'rb');
			$line = fgets($fp);
			$Tline = explode("//", $line);
			if (!empty($Tline[1]) and trim($Tline[1]) == project_db()) {
				$project_specific_config_folder = get_ci_config('project_specific_config_folder');
				$f_new = fopen($project_specific_config_folder . "temp/install_config_" . project_db() . ".php", 'w+');
				rewind($fp);
				while (($line = fgets($fp)) !== false) {
					// fputs($f_new, $line. "\n"); 
					fputs($f_new, $line);
					//echo "$line<br>";
				}
				fclose($f_new);
				$temp_table_config = $this->entity_config_lib->get_new_install_config(project_db());
				if (!valid_install_configuration_file($temp_table_config)) {
					array_push($error_array, "Not a valid configuration file");
				} else {
					copy($project_specific_config_folder . "temp/install_config_" . project_db() . ".php", $project_specific_config_folder . "install_config_" . project_db() . ".php");
					redirect('install/save_install_form_part2');
				}
				// redirect('install');
				// $this->save_install_form_part2();
			} else {
				array_push($error_array, 'The file you are trying to upload does not contain a correct updated version of this project');
				//echo "<h2>The file you are trying to upload does not contain an correct updated version of this project</h2>";
				//echo "<h2>".anchor('home',lng('Back'))."</h2>";
			}
		}
		if (!empty($error_array)) {
			//print_r($error_array);
			$this->project_install_result($error_array, $success_array, 'update_project');
		}
	}

	/**
	 * handle the upload and validation of the installation configuration file specifically for the installation form in the editor. 
	 * If the file is valid, it is copied to the appropriate location and the function redirects to the save_install_form_part2() method to continue the installation process.
	 */
	public function save_install_form_editor()
	{
		$post_arr = $this->input->post();
		$error_array = array();
		$success_array = array();
		if (empty($post_arr['selected_config'])) {
			//echo "Error: " . $_FILES["file"]["error"] . "<br />";
			array_push($error_array, "Error: Choose a file ");
		} elseif (!is_file($post_arr['selected_config'])) {
			//echo "File must be a .php";
			array_push($error_array, "File must be a .php");
		} else {
			//$monfichier="";
			//exit;
			$fp = fopen($post_arr['selected_config'], 'rb');
			$line = fgets($fp);
			$Tline = explode("//", $line);
			if (!empty($Tline[1]) and trim($Tline[1]) == project_db()) {
				$project_specific_config_folder = get_ci_config('project_specific_config_folder');
				$f_new = fopen($project_specific_config_folder . "temp/install_config_" . project_db() . ".php", 'w+');
				rewind($fp);
				while (($line = fgets($fp)) !== false) {
					// fputs($f_new, $line. "\n"); 
					fputs($f_new, $line);
					//echo "$line<br>";
				}
				fclose($f_new);
				$temp_table_config = $this->entity_config_lib->get_new_install_config(project_db());
				if (!valid_install_configuration_file($temp_table_config)) {
					array_push($error_array, "Not a valid configuration file");
				} else {
					copy($project_specific_config_folder . "temp/install_config_" . project_db() . ".php", $project_specific_config_folder . "install_config_" . project_db() . ".php");
					redirect('install/save_install_form_part2');
				}
				// redirect('install');
				//$this->save_install_form_part2();
			} else {
				array_push($error_array, 'The file you are trying to upload does not contain a correct updated version of this project');
				//echo "<h2>The file you are trying to upload does not contain an correct updated version of this project</h2>";
				//echo "<h2>".anchor('home',lng('Back'))."</h2>";
			}
		}
		if (!empty($error_array)) {
			//print_r($error_array);
			$this->project_install_result($error_array, $success_array, 'update_project_editor');
		}
	}

	// responsible for cleaning up the previous installation by dropping tables and deleting records from the database.
	private function clean_previous_installation()
	{
		//echo "<h3>Cleaning old installation</h3>";
		$sql = "select * from installation_info where install_active=1 ";
		$this->db2 = $this->load->database(project_db(), TRUE);
		$res = $this->db2->query($sql)->row_array();
		if (!empty($res)) {
			$reference_tables = json_decode($res['reference_tables']);
			$generated_tables = json_decode($res['generated_tables']);
			$foreign_key_constraint = json_decode($res['foreign_key_constraint']);
			//print_test($res);
			//print_test($generated_tables);
			//EXIT;
			if (!empty($foreign_key_constraint)) {
				foreach ($foreign_key_constraint as $k => $v_constraint) {
					$v_constraint = trim($v_constraint);
					if (!empty($v_constraint))
						$res_sql = $this->manage_mdl->run_query($v_constraint);
				}
			}
			$i = 1;
			if (!empty($reference_tables)) {
				foreach ($reference_tables as $key => $value) {
					if ($i == 1)
						$sql = "DROP TABLE  IF EXISTS `" . $value . "` ";
					else
						$sql .= " , `" . $value . "` ";
					$i++;
				}
			}
			if (!empty($generated_tables)) {
				foreach ($generated_tables as $key => $value) {
					if ($i == 1)
						$sql = "DROP TABLE IF EXISTS `" . $value . "` ";
					else
						$sql .= " , `" . $value . "` ";
					$i++;
				}
			}
			$sql .= " ; ";
			$res_sql = $this->manage_mdl->run_query($sql);
			//print_test($res_sql);
			//echo $sql;
			$sql = "DELETE from ref_tables where reftab_id !=0";
			$res_sql = $this->manage_mdl->run_query($sql);
			//print_test($res_sql);
			//echo $sql;
		}
	}

	//create a table in the specified database based on the configuration provided
	private function create_table_config($config, $target_db = 'current')
	{
		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		//	print_test($config);
		$table_id = $config['table_id'];
		$del_line = "DROP TABLE IF EXISTS " . $config['table_name'] . ";";
		$res_sql = $this->manage_mdl->run_query($del_line, False, $target_db);
		$sql = "CREATE TABLE IF NOT EXISTS " . $config['table_name'] . " (
		$table_id int(11) NOT NULL AUTO_INCREMENT,";
		$field_default = "   ";
		$field_type = "  ";
		foreach ($config['fields'] as $key => $value) {
			if ($key != $table_id and $key != $config['table_active_field']) {
				//start with select
				if ($value['input_type'] == 'select') {
					if ($value['input_select_source'] == 'array') { //static
						$i = 1;
						$field_type = " enum(";
						foreach ($value['input_select_values'] as $k => $v) {
							if ($i == 1)
								$field_type .= "'" . $k . "'";
							else
								$field_type .= ",'" . $k . "'";
							$i++;
						}
						$field_type .= ") ";
						$field_default = "   DEFAULT NULL ";
						if (!empty($value['initial_value'])) {
							$field_default = "   NOT NULL DEFAULT '" . $value['initial_value'] . "' ";
						}
					} elseif ($value['input_select_source'] == 'yes_no') {
						$field_type = " int(2) ";
						$field_default = "  DEFAULT NULL ";
						if (!empty($value['initial_value'])) {
							$field_default = "   NOT NULL DEFAULT '" . $value['initial_value'] . "' ";
						}
					} else { //dynamic
						$field_type = " int(11) ";
						//$field_default="  DEFAULT '0' ";
						$field_default = "  DEFAULT NULL ";
					}
				} elseif ($value['input_type'] == 'date') {
					$field_type = " timestamp ";
					$field_default = "  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ";
					$field_default = "  NOT NULL DEFAULT CURRENT_TIMESTAMP  ";
				} else { //Free category
					if (!empty($value['field_value']) and $value['field_value'] == '0_1') { //Yes_no
					} elseif ($value['field_type'] == 'number') {
						$field_type = " int(" . $value['field_size'] . ") ";
						$field_default = "   DEFAULT '0' ";
						if (!empty($value['initial_value'])) {
							$field_default = "   NOT NULL DEFAULT '" . $value['initial_value'] . "' ";
						}
					} else {
						$field_type = " varchar(" . $value['field_size'] . ") ";
						$field_default = "   DEFAULT NULL ";
						if (!empty($value['initial_value'])) {
							$field_default = "   NOT NULL DEFAULT '" . $value['initial_value'] . "' ";
						}
					}
				}
				if (!(isset($value['category_type']) and ($value['category_type'] == 'WithSubCategories' or $value['category_type'] == 'WithMultiValues'))) {
					$sql .= " " . $key . " $field_type $field_default,";
				}
			}
		}
		$sql .= " " . $config['table_active_field'] . " int(1) NOT NULL DEFAULT '1',";
		$sql .= " PRIMARY KEY ($table_id)";
		$sql .= ") ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		$res_sql = $this->manage_mdl->run_query($sql, False, $target_db);
		return "$del_line $sql";
	}
}