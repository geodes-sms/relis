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
 */


/**
 * adding a new database configuration to the "database.php" file in the "config" folder of the application, 
 * allowing the application to connect to the newly created project database using the specified settings
 */
//responsible for displaying the installation result after installing or updating a project
function project_install_result($array_error = array(), $array_success = array(), $type = "new_project")
{
	$ci = get_instance();

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
	$ci->load->view('shared/body', $data);
}

/**
 * creates a reference table in the specified database or the current project database. 
 * It handles the creation of the table, insertion of initial values, and updating the ref_tables table to include the new reference table
 */
function create_reference_table($ref_conf, $ref_value, $target_db = 'current')
{
	$ci = get_instance();

	$target_db = ($target_db == 'current') ? project_db() : $target_db;
	$table_name = $ref_conf;
	$desc = $ref_value['ref_name'];
	//
	$del_line = "DROP TABLE IF EXISTS " . $table_name . ";";
	$res_sql = $ci->manage_mdl->run_query($del_line, False, $target_db);
	//print_test($res_sql);
	$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
	`ref_id` int(11) NOT NULL AUTO_INCREMENT,
	  `ref_value` varchar(50) NOT NULL,
	  `ref_desc` varchar(250) DEFAULT NULL,
	  `ref_active` int(1) NOT NULL DEFAULT '1',
	  PRIMARY KEY (`ref_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	$res_sql = $ci->manage_mdl->run_query($sql, False, $target_db);
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
		$res_sql = $ci->manage_mdl->run_query($sql1, False, $target_db);
	}
	//Add in the list of reference tables
	//print_test($res_sql);
	$sql2 = " INSERT INTO ref_tables (reftab_label, reftab_table, reftab_desc, reftab_active) VALUES
('" . $ref_conf . "', '" . $ref_conf . "', '" . $desc . "', 1);";
	$res_sql = $ci->manage_mdl->run_query($sql2, False, $target_db);
	//print_test($res_sql);
	return "$del_line $sql $sql1 $sql2";
}

//populate common tables for all projects in the specified database or the current project database by retrieving the table configurations
function populate_common_tables($target_db = 'current', $config = 'init')
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
			'author',
			'paper_author',
			'venue',
			'screen_phase',
			'screening',
			'screen_decison',
			'operations',
			'qa_questions',
			'qa_responses',
			'qa_result',
			'qa_assignment',
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
 * iterate over the fields in a table configuration and identifies fields that have foreign key constraints defined. 
 * It generates SQL statements to drop those foreign key constraints from the table. 
 * The function returns the complete SQL query for dropping the foreign key constraints, or an empty string if there are no constraints to drop
 */
function get_froreign_keys_constraint($config, $table_config)
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

/**
 * update stored procedures in the specified database or the current project database based on the old and new configuration names. 
 * It handles various operations such as getting, counting, removing, adding, updating, and getting details of elements using stored procedures
 */
function update_stored_procedure($config, $verbose = FALSE, $target_db = 'current', $add_constraint = False)
{
	$ci = get_instance();

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
		$ci->manage_stored_procedure_lib->create_stored_procedure_get($config, TRUE, $verbose, $target_db);
		/*
		 * Stored procedure to count number of elements (used for navigation link)
		 */
		if ($config == 'papers')
			$ci->manage_stored_procedure_lib->create_stored_procedure_count($config, TRUE, $verbose, $target_db);
		/*
		 * Stored procedure to remove element
		 */
		if ($config != 'papers')
			$ci->manage_stored_procedure_lib->create_stored_procedure_remove($config, TRUE, $verbose, $target_db);
		/*
		 * Stored procedure to add element
		 */
		if ($config != 'papers')
			$ci->manage_stored_procedure_lib->create_stored_procedure_add($config, TRUE, $verbose, $target_db);
		/*
		 * Stored procedure to update element
		 */
		if ($config != 'papers')
			$ci->manage_stored_procedure_lib->create_stored_procedure_update($config, TRUE, $verbose, $target_db);
		/*
		 * Stored procedure to get detail element (select row)
		 */
		if ($config != 'papers')
			$ci->manage_stored_procedure_lib->create_stored_procedure_detail($config, TRUE, $verbose, $target_db);
		if ($add_constraint) {
			//$this->manage_stored_procedure_lib->add_froreign_keys_constraint($config,TRUE,$verbose,$target_db);
		}
	}
	foreach ($new_configs as $k => $config) {
		if ($config == 'papers')
			$ci->manage_stored_procedure_lib->create_stored_procedure_count($config, TRUE, $verbose, $target_db);
		create_stored_procedures($config, $target_db, False);
	}
}

/**
 * updates the screening values in the database by updating the general screening configuration, adding new screening phases, 
 * and updating the values in the reference tables for exclusion criteria, source papers, and search strategy.
 */
function update_screening_values($screening, $target_db = 'current')
{
	$ci = get_instance();

	$target_db = ($target_db == 'current') ? project_db() : $target_db;
	$ci->db3 = $ci->load->database($target_db, TRUE);
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
	$result = $ci->db3->update('config', $config, "config_id=1");
	//add new phases
	if (!empty($screening['phases'])) {
		//clean existing
		$result = $ci->db3->update('screen_phase', array('screen_phase_active' => 0));
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
		$result = $ci->db3->insert_batch('screen_phase', $all_phases);
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
			$result = $ci->db3->update($s_config_value['table'], array('ref_active' => 0));
			$all_elements = array();
			foreach ($screening[$s_config_id] as $key => $value) {
				$conf_element['ref_value'] = $value;
				$conf_element['ref_desc'] = $value;
				array_push($all_elements, $conf_element);
			}
			//	print_test($all_elements);
			$result = $ci->db3->insert_batch($s_config_value['table'], $all_elements);
			////print_test($result);
		}
	}
}

/**
 * updates the QA values in the database by either overriding existing QA data or updating the general QA configuration, questions, and responses
 */
function update_qa_values($qa, $target_db = 'current')
{
	$ci = get_instance();

	$res_install_config = $ci->entity_configuration_lib->get_install_config($target_db);
	$qa_action = $res_install_config['qa_action'];
	$target_db = ($target_db == 'current') ? project_db() : $target_db;
	$ci->db3 = $ci->load->database($target_db, TRUE);
	if ($qa_action == 'override') {
		$ci->Project_dataAccess->delete_update_qa();
	}
	$config['qa_cutt_off_score'] = !empty($qa['cutt_off_score']) ? $qa['cutt_off_score'] : "2";
	$config['qa_on'] = 1;
	$result = $ci->db3->update('config', $config, "config_id=1");
	//questions
	if (!empty($qa['questions'])) {
		$all_phases = array();
		$i = 1;
		$result = $ci->db3->update('qa_questions', array('question_active' => 0));
		foreach ($qa['questions'] as $key => $value) {
			$conf_phase['question'] = $value['title'];
			array_push($all_phases, $conf_phase);
			$i++;
		}
		$result = $ci->db3->insert_batch('qa_questions', $all_phases);
	}
	// Add resposes
	if (!empty($qa['responses'])) {
		$all_responses = array();
		$i = 1;
		$result = $ci->db3->update('qa_responses', array('response_active' => 0));
		$conf_phase = array();
		foreach ($qa['responses'] as $key => $value) {
			$conf_phase['response'] = $value['title'];
			$conf_phase['score'] = $value['score'];
			array_push($all_responses, $conf_phase);
			$i++;
		}
		$result = $ci->db3->insert_batch('qa_responses', $all_responses);
	}
}

//populates views for common tables in a specified database
function populate_common_tables_views($target_db = 'current')
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