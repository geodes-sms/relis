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
 * This controller contain all the pages user can access before connection to the application
 * - homepage
 * - authentification page
 * - help page
 */
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Home extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//$this->load->library('paper');
	}

	/**
	 *  home page
	 */
	public function index()
	{
		if (!($this->session->userdata('project_db')) or $this->session->userdata('project_db') == 'default') {
			redirect('project/projects_list');
		}
		if ($this->session->userdata('working_perspective') == 'screen') {
			redirect('screening/screening');
		}
		if ($this->session->userdata('working_perspective') == 'qa') {
			redirect('quality_assessment/qa');
		}

		$left_menu = $this->manager_lib->get_left_menu();
		$project_published = project_published();
		$my_class_completion = $this->Paper_dataAccess->count_papers('all');
		$data['processed_papers'] = $this->Paper_dataAccess->count_papers('processed');
		$data['pending_papers'] = $this->Paper_dataAccess->count_papers('pending');
		$data['assigned_me_papers'] = $this->Paper_dataAccess->count_papers('assigned_me');
		$data['excluded_papers'] = $this->Paper_dataAccess->count_papers('excluded');
		$gen_class_completion = $this->manager_lib->get_classification_completion('class', 'all');
		$my_class_completion = $this->manager_lib->get_classification_completion('class', '');
		if (get_appconfig_element('class_validation_on')) {
			$gen_validation_completion = $this->manager_lib->get_classification_completion('validation', 'all');
			$my_validation_completion = $this->manager_lib->get_classification_completion('validation', '');
		}
		if (!empty($my_class_completion['all_papers'])) {
			$data['classification_completion']['title'] = "My classification completion";
			$data['classification_completion']['all_papers'] = array(
				'value' => $my_class_completion['all_papers'],
				'title' => 'All',
				'url' => 'element/entity_list/list_class_assignment_mine'
			);
			$data['classification_completion']['pending_papers'] = array(
				'value' => $my_class_completion['pending_papers'],
				'title' => 'Pending',
				'url' => 'element/entity_list/list_class_assignment_pending_mine'
			);
			$data['classification_completion']['done_papers'] = array(
				'value' => $my_class_completion['processed_papers'],
				'title' => 'Processed',
				'url' => 'element/entity_list/list_class_assignment_done_mine'
			);
			$data['classification_completion']['gauge_all'] = $my_class_completion['all_papers'];
			$data['classification_completion']['gauge_done'] = $my_class_completion['processed_papers'];
		}
		if (!empty($gen_class_completion['all_papers'])) {
			$data['gen_classification_completion']['title'] = "Overall classification completion";
			$data['gen_classification_completion']['all_papers'] = array(
				'value' => $gen_class_completion['all_papers'],
				'title' => 'All',
				'url' => 'element/entity_list/list_class_assignment'
			);
			$data['gen_classification_completion']['pending_papers'] = array(
				'value' => $gen_class_completion['pending_papers'],
				'title' => 'Pending',
				'url' => 'element/entity_list/list_class_assignment_pending'
			);
			$data['gen_classification_completion']['done_papers'] = array(
				'value' => $gen_class_completion['processed_papers'],
				'title' => 'Processed',
				'url' => 'element/entity_list/list_class_assignment_done'
			);
			$data['gen_classification_completion']['gauge_all'] = $gen_class_completion['all_papers'];
			$data['gen_classification_completion']['gauge_done'] = $gen_class_completion['processed_papers'];
		}
		if (!empty($my_validation_completion['all_papers'])) {
			$data['my_validation_completion']['title'] = "My validation completion";
			$data['my_validation_completion']['all_papers'] = array(
				'value' => $my_validation_completion['all_papers'],
				'title' => 'All',
				'url' => 'element/entity_list/list_class_validation_mine'
			);
			$data['my_validation_completion']['pending_papers'] = array(
				'value' => $my_validation_completion['pending_papers'],
				'title' => 'Pending',
				'url' => ''
			);
			$data['my_validation_completion']['done_papers'] = array(
				'value' => $my_validation_completion['processed_papers'],
				'title' => 'Processed',
				'url' => ''
			);
			$data['my_validation_completion']['gauge_all'] = $my_validation_completion['all_papers'];
			$data['my_validation_completion']['gauge_done'] = $my_validation_completion['processed_papers'];
		}
		if (!empty($gen_validation_completion['all_papers'])) {
			$data['gen_validation_completion']['title'] = "Overall validation completion";
			$data['gen_validation_completion']['all_papers'] = array(
				'value' => $gen_validation_completion['all_papers'],
				'title' => 'All',
				'url' => 'element/entity_list/list_class_validation'
			);
			$data['gen_validation_completion']['pending_papers'] = array(
				'value' => $gen_validation_completion['pending_papers'],
				'title' => 'Pending',
				'url' => ''
			);
			$data['gen_validation_completion']['done_papers'] = array(
				'value' => $gen_validation_completion['processed_papers'],
				'title' => 'Processed',
				'url' => ''
			);
			$data['gen_validation_completion']['gauge_all'] = $gen_validation_completion['all_papers'];
			$data['gen_validation_completion']['gauge_done'] = $gen_validation_completion['processed_papers'];
		}
		$action_but = array();
		if (can_manage_project() and !$project_published)
			$action_but['assign_screen'] = get_top_button('all', 'Assign papers for classification', 'data_extraction/class_assignment_set', 'Assign papers', 'fa-mail-forward', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
		if (can_review_project() and !$project_published)
			$action_but['screen'] = get_top_button('all', 'Classify', 'element/entity_list/list_class_assignment_pending_mine', 'Classify', 'fa-search', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
		if (can_manage_project()) {
			$action_but['screen_completion'] = get_top_button('all', 'Result', 'element/entity_list/list_classification', 'Result', 'fa-th', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
		}
		$data['action_but_screen'] = $action_but;
		$action_but = array();
		if (get_appconfig_element('class_validation_on')) {
			if (can_validate_project() and !$project_published) {
				$action_but['assign_screen'] = get_top_button('all', 'Assign papers for validation', 'data_extraction/class_assignment_validation_set', 'Assign papers', 'fa-mail-forward', '', ' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ', False);
				$action_but['screen'] = get_top_button('all', 'Validate', 'element/entity_list/list_class_validation_mine', 'Validate', 'fa-check-square-o', '', ' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ', False);
			}
			$action_but['screen_completion'] = get_top_button('all', 'Result', 'element/entity_list/list_class_validation', 'Result', 'fa-th', '', ' btn-primary action_butt  col-md-3 col-sm-3 col-xs-12', False);
			$data['action_but_validate'] = $action_but;
		}
		$data['configuration'] = get_project_config($this->session->userdata('project_db'));
		$data['users'] = $this->User_dataAccess->get_users_all();
		foreach ($data['users'] as $key => $value) {
			if (!(user_project($this->session->userdata('project_id'), $value['user_id'])) or $value['user_usergroup'] == 1) {
				unset($data['users'][$key]);
			}
		}
		$data['page'] = 'general/home';
		$this->load->view('shared/body', $data);
	}

	/*
	 * change the active language in the session between French and English
	 */
	public function change_lang()
	{
		if ($this->session->userdata('active_language') and $this->session->userdata('active_language') == 'fr') {
			$this->session->set_userdata('active_language', 'en');
		} else {
			$this->session->set_userdata('active_language', 'fr');
		}
	}

	/**
	 * automate the process of updating stored procedures for database operations, providing a convenient way to keep the stored procedures in sync with the application's configuration
	 */
	public function update_stored_procedure($config = "all")
	{
		if ($config == 'all') {
			$configs = array('author', 'venue', 'users', 'usergroup', 'papers', 'classification', 'exclusion', 'assignation', 'paper_author', 'logs', 'str_mng', 'config', 'user_project');
			$reftables = $this->DBConnection_mdl->get_reference_tables_list();
			foreach ($reftables as $key => $value) {
				array_push($configs, $value['reftab_label']);
			}
		} else {
			$configs = array($config);
		}
		print_test($configs);
		foreach ($configs as $k => $config) {
			/*
			 * Stored procedure to get list of element
			 */
			$this->manage_stored_procedure_lib->create_stored_procedure_get($config);
			/*
			 * Stored procedure to count number of elements (used for navigation link)
			 */
			if ($config == 'papers')
				$this->manage_stored_procedure_lib->create_stored_procedure_count($config);
			/*
			 * Stored procedure to remove element
			 */
			$this->manage_stored_procedure_lib->create_stored_procedure_remove($config);
			/*
			 * Stored procedure to add element
			 */
			$this->manage_stored_procedure_lib->create_stored_procedure_add($config);
			/*
			 * Stored procedure to update element
			 */
			$this->manage_stored_procedure_lib->create_stored_procedure_update($config);
			/*
			 * Stored procedure to get detail element (select row)
			 */
			$this->manage_stored_procedure_lib->create_stored_procedure_detail($config);
		}
		///do not forget to add the stored procedure for papers : assigned, processed, and pending and update the 
	}

	//creation of tables based on predefined configurations
	public function create_table_config($config, $target_db = 'current')
	{
		$res = $this->manage_stored_procedure_lib->create_table_config(get_table_config($config), $target_db);
		echo $res;
	}

	/*
	 * test the database operations and ensure that data can be successfully updated and inserted into the relevant tables
	 */
	public function test_values()
	{
		$i = 1;
		for ($i = 1; $i <= 1; $i++) {
			/*
										* Préparation des valeurs qui sont générés de façon aléatoire
										
									   $fields=array(
											   
											   'number_citation'=>rand(2 ,206)
											   
								   
									   );
								   
									   print_test($fields);
								   */
			/*
			 * update des données
			 */
			//$headersaved = $this->db_current->update ( 'classification', $fields,array('class_paper_id'=>$i) );
			//print_test($headersaved);
		}
		$i = 1;
		for ($i = 16; $i <= 20; $i++) {
			/*
			 * Préparation des valeurs qui sont générés de façon aléatoire
			 */
			$fields = array(
				'class_paper_id' => $i,
				'transformation_name' => "Test transformation $i",
				'domain' => rand(1, 5),
				//'trans_language'=>rand(1,4 ),	
				'source_language' => rand(1, 4),
				'target_language' => rand(1, 4),
				//'scope'=>rand(1 , 3),	
				'industrial' => rand(0, 1),
				'bidirectional' => rand(0, 1),
				'year' => rand(2011, 2016),
				'number_citation' => rand(2, 2016),
				'user_id' => 1
			);
			//print_test($fields);
			/*
			 * Insertion des données
			 */
			$headersaved = $this->db_current->insert('classification', $fields);
			print_test($headersaved);
		}
		$i = 1;
		for ($i = 16; $i <= 20; $i++) {
			/*
			 * Préparation des valeurs qui sont générés de façon aléatoire
			 */
			$intent_numbers = rand(1, 3);
			$j = 1;
			for ($j = 1; $j <= $intent_numbers; $j++) {
				$fields = array(
					'parent_field_id' => $i,
					'name_used' => "Intent $i $j",
					'intent' => rand(1, 4),
					'line_code' => rand(2000, 50000),
					'op_result' => rand(1, 3),
				);
				//print_test($fields);
				/*
				 * Insertion des données
				 */
				$headersaved = $this->db_current->insert('intent', $fields);
				print_test($headersaved);
			}
		}
		$i = 1;
		for ($i = 16; $i <= 20; $i++) {
			/*
			 * Préparation des valeurs qui sont générés de façon aléatoire
			 */
			$intent_numbers = rand(1, 4);
			$j = 1;
			for ($j = 1; $j <= $intent_numbers; $j++) {
				$fields = array(
					'parent_field_id' => $i,
					'trans_language' => rand(2, 4),
					'trans_language' => $j
				);
				//print_test($fields);
				/*
				 * Insertion des données
				 */
				$headersaved = $this->db_current->insert('trans_language', $fields);
				print_test($headersaved);
			}
		}
	}

	//perform a database query and generate random values for the 'classification' table based on the results of the query
	//test the generation and insertion of random values into the 'classification' table based on the query results
	public function test_icse()
	{
		$sql = "SELECT * FROM  `paper` WHERE  `classification_status` =  'To classify' AND  `paper_active` =1  ";
		$i = 1;
		$pred = array(
			0 => 'Predefined',
			1 => 'Output-based',
			2 => 'Rule-based',
		);
		$res = $this->db_current->query($sql)->result_array();
		foreach ($res as $key => $value) {
			$paper_id = $value['id'];
			//print_test($value);
			$temp = rand(0, 2);
			$fields = array(
				'class_paper_id' => $paper_id,
				'year' => rand(2011, 2017),
				'Tool' => rand(1, 5),
				'template_style' => $pred[$temp],
				'user_id' => rand(16, 17)
			);
			print_test($fields);
			/*
			 * Insertion des données
			 */
			$headersaved = $this->db_current->insert('classification', $fields);
			print_test($headersaved);
		}
		/*
						   for($i=287;$i<=367;$i++){
								
							   $temp=rand(0,2);
							   $fields=array(
									   'class_paper_id'=>$i,
									   'year'=>rand(2011,2017 ),
									   'Tool'=>rand(1,5 ),
									   'template_style'=>$pred[$temp],
									   'user_id'=>rand(16,17)
					   
							   );
					   
							   print_test($fields);
							   
							   //$headersaved = $this->db_current->insert ( 'classification', $fields );
							   //print_test($headersaved);
						   }
					   
						   */
	}

	/*
	 * retrieve and display various statistics and visualizations based on the data in the database. 
	 * It generates results for specific fields in the classification table and presents them on the result page along with other relevant data
	 */
	public function result()
	{
		old_version();
		//save_metrics("bricetest metrics");
		/*
		 * Recupération du nombre de papiers par catégories
		 */
		$data['all_papers'] = $this->Paper_dataAccess->count_papers('all');
		$data['processed_papers'] = $this->Paper_dataAccess->count_papers('processed');
		$data['pending_papers'] = $this->Paper_dataAccess->count_papers('pending');
		$data['assigned_me_papers'] = $this->Paper_dataAccess->count_papers('assigned_me');
		$data['excluded_papers'] = $this->Paper_dataAccess->count_papers('excluded');
		/*
		 * Stucture de la table des classification
		 */
		$table_config = $this->table_ref_lib->ref_table_config('classification');
		//print_test($table_config);
		$result_fin = array();
		foreach ($table_config['fields'] as $key_conf => $value_conf) {
			//if(!empty($value_conf['compute_result']) AND $value_conf['compute_result']=='yes' AND ($value_conf['input_type'] =='select') AND ($value_conf['input_select_source'] =='table') ){
			if (isset($value_conf['number_of_values']) and ($value_conf['number_of_values'] == '1' or $value_conf['number_of_values'] == '0') and ($value_conf['input_type'] == 'select') and ($value_conf['input_select_source'] == 'table' or $value_conf['input_select_source'] == 'array' or $value_conf['input_select_source'] == 'yes_no')) {
				//print_test($value_conf);
				$ref_field = $key_conf;
				if ($value_conf['input_select_source'] == 'array') {
					$result = $this->Data_extraction_dataAccess->get_result_classification($key_conf);
					foreach ($result as $key => $value) {
						$result[$key]['field_desc'] = $value['field'];
					}
				} elseif ($value_conf['input_select_source'] == 'yes_no') {
					$result = $this->Data_extraction_dataAccess->get_result_classification($key_conf);
					$yes_no = array("False", 'True');
					foreach ($result as $key => $value) {
						$result[$key]['field_desc'] = $yes_no[$value['field']];
					}
				} else {
					$conf = explode(";", $value_conf['input_select_values']);
					$ref_config = $conf[0];
					$ref_table = $this->DBConnection_mdl->get_reference_corresponding_table($ref_config);
					$ref_table_name = $ref_table['reftab_table'];
					$ref_table_desc = $ref_table['reftab_desc'];
					$result = $this->Data_extraction_dataAccess->get_result_classification($ref_field);
					foreach ($result as $key => $value) {
						$result[$key]['field_desc'] = $this->manage_mdl->get_reference_value($ref_table_name, $result[$key]['field']);
					}
				}
				$result_fin[$ref_config . $key_conf]['name'] = $value_conf['field_title'];
				$result_fin[$ref_config . $key_conf]['field_name'] = $ref_field;
				$result_fin[$ref_config . $key_conf]['rows'] = $result;
				//print_test($result);
			}
		}
		//print_test($result_fin);
		/*
		 * La page contient des graphique cette valeur permettra le chargement de la librarie highcharts  
		 */
		$data['has_graph'] = 'yes';
		$data['result_table'] = $result_fin;
		$data['page'] = 'result';
		$this->load->view('shared/body', $data);
		//$this->load->view('welcome_message');
	}

	/*
	 * Page permettant de saisir une requette sql et avoir le résultat
	 */
	public function sql_query($query_type = "single")
	{
		$data['return_table'] = 1;
		$data['query_type'] = $query_type;
		/*
		 * La vue qui va s'afficher
		 */
		if ($query_type != 'multi') {
			$data['top_buttons'] = get_top_button('all', 'Switch to multi query!', 'home/sql_query/multi', 'Switch to multi query!', ' fa-exchange', '', ' btn-info ');
			$data['title'] = 'Query database - single SQL query';
		} else {
			$data['top_buttons'] = get_top_button('all', 'Switch to single query!', 'home/sql_query/', 'Switch to single query!', ' fa-exchange', '', ' btn-info ');
			$data['title'] = lng_min('Query database - multiple SQL queries');
		}
		$data['page'] = 'sql';
		$this->load->view('shared/body', $data);
	}

	/*
	 * Page de traitement de requete sql saisie et affichade du résultat
	 */
	public function sql_query_response()
	{
		/*
		 * Récupération de la réquette saisier
		 */
		$post_arr = $this->input->post();
		//print_test($post_arr); 
		$sql = "";
		$sql = $post_arr['sql_field'];
		$query_type = $post_arr['query_type'];
		/*
		 * Verification si il faut afficher le résultat ou pas
		 */
		if (isset($post_arr['return_table'])) {
			$return_table = 1;
		} else {
			$return_table = 0;
		}
		$data['query_type'] = $query_type;
		if (!empty($sql)) {
			$data['sql_field'] = $sql;
			$data['return_table'] = $return_table;
			/*
			 * Appel du model manage_mdl->run_query  pour executer la requette et recuperer le resultat
			 */
			$pre_select_sql = " select* from ( ";
			$post_select_sql = " ) as T ";
			if ($query_type != 'multi') {
				if (!has_usergroup(1)) {
					//if used is not super admin he can just execute select queries
					$sql = $pre_select_sql . $sql . $post_select_sql;
				}
				$res = $this->manage_mdl->run_query($sql, $return_table);
			} else {
				$delimiter = $post_arr['delimiter'];
				$T_queries = explode(!empty($delimiter) ? $delimiter : ';', $sql);
				//print_test($T_queries);
				$error = 0;
				$all = 0;
				$t_error_message = " ";
				foreach ($T_queries as $key => $v_sql) {
					$v_sql = trim($v_sql);
					if (!empty($v_sql)) {
						if (!has_usergroup(1)) {
							//if used is not super admin he can just execute select queries
							$v_sql = $pre_select_sql . $v_sql . $post_select_sql;
						}
						$T_res = $this->manage_mdl->run_query($v_sql);
						if ($T_res['code'] != 0) {
							$error++;
							$t_error_message .= " <br/> - " . $T_res['message'];
						}
						$all++;
					}
				}
				if ($error == 0) {
					$res['code'] = 0;
					$res['message'] = $all . ' query executed!';
				} else {
					$res['code'] = 1;
					$res['message'] = ($all - $error) . " Succeded - $error Errors<br/>" . $t_error_message;
				}
			}
		} else {
			$res['code'] = 1;
			$res['message'] = lng_min('Query was empty');
		}
		//	print_test($res);
		if ($res['code'] == 0) { //L'execution de la requette a réussit
			/*	
			 * Péparation du résultat à afficher
			 */
			$data['message_success'] = "Success";
			$data['message_error'] = "";
			$array_header = array();
			if ($return_table) {
				$data['display_list'] = "OK";
				if (!empty($res['message']) and is_array($res['message']) and count($res['message']) > 0) {
					foreach ($res['message'][0] as $key => $value) {
						array_push($array_header, $key);
					}
					array_unshift($res['message'], $array_header);
					$data['list'] = $res['message'];
				}
			}
		} else { //L'execution de la requette a echoué
			/*
			 * Préparation du message d'erreur à afficher
			 */
			$data['message_error'] = "Error: " . $res['message'];
			$data['message_success'] = "";
		}
		if ($query_type != 'multi') {
			$data['top_buttons'] = get_top_button('all', 'Switch to multi query!', 'home/sql_query/multi', 'Switch to multi query!', ' fa-exchange', '', ' btn-info ');
			$data['title'] = lng_min('Run SQL query');
		} else {
			$data['top_buttons'] = get_top_button('all', 'Switch to single query!', 'home/sql_query/', 'Switch to single query!', ' fa-exchange', '', ' btn-info ');
			$data['title'] = lng_min('Run multiple SQL queries');
		}
		$data['page'] = 'sql';
		$this->load->view('shared/body', $data);
	}

	//displays the export page, allowing users to export data in different formats
	public function export($type = 1)
	{
		$data['t_type'] = $type;
		$data['page_title'] = lng('Exports');
		$data['top_buttons'] = get_top_button('back', 'Back', 'home');
		$data['left_menu_perspective'] = 'z_left_menu_screening';
		$data['project_perspective'] = 'screening';
		$data['page'] = 'export';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	/**
	 * list the files in a specified directory and display their content. 
	 * It iterates through the files and subdirectories, excluding specific files, 
	 * and calls the metrics_file_content() function to display the content of each file
	 */
	public function metrics_view()
	{
		echo "<h1>list of files</h1>";
		$dir = "C:/xampp/htdocs/relis/relis_multi_gen_01/cside/metrics_new";
		$dir = "C:/xampp/htdocs/relis/relis_multi_gen_01/cside/metrics_new";
		if (is_dir($dir)) {
			$files = array_diff(scandir($dir), array('.', '..', ".metadata"));
			//$files = scandir($dir);
			//print_test($files);
			foreach ($files as $key => $value) {
				//directories per day
				$dir_f = $dir . "/" . $value;
				echo "<h2>$value</h2>";
				if (is_dir($dir_f)) {
					$files_f = array_diff(scandir($dir_f), array('.', '..', ".metadata"));
					foreach ($files_f as $key_f => $value_f) {
						if (strrpos($value_f, "dmin_") != '1' and strrpos($value_f, "ser_unknown") != '1') {
							$file = $dir . "/" . $value . "/" . $value_f;
							echo "<h2>" . $file . "</h2>";
							$this->metrics_file_content($file);
						}
					}
					//print_test($files_f);
				} else {
					echo "<p>nop inside</p>";
				}
			}
		} else {
			echo "nop";
		}
	}

	//read the content of a file, extract relevant metrics, and store them in a database table for further analysis or reporting
	public function metrics_file_content($file = "C:/xampp/htdocs/relis/relis_multi_gen_01/cside/metrics_new/2016_Dec_10/pierre_13.txt")
	{
		//$file="C:/xampp/htdocs/relis/relis_multi_gen_01/cside/metrics_new/2016_Dec_11/younous_18.txt";
		ini_set('auto_detect_line_endings', TRUE);
		$fp = fopen($file, 'rb');
		$i = 1;
		$last_count = 0;
		$choosen_metrics = array();
		while ((($line = (fgets($fp)))) !== false) {
			$Tline = explode("__--~~", $line);
			$metrics = json_decode($Tline['2'], true);
			if (isset($metrics['server_info']['HTTP_USER_AGENT'])) {
				//print_test($this->getBrowser($metrics['server_info']['HTTP_USER_AGENT']));
			}
			//print_test($metrics);
			$choosen_metrics['time'] = isset($metrics['server_info']['REQUEST_TIME']) ? $metrics['server_info']['REQUEST_TIME'] : "";
			$client = isset($metrics['server_info']['HTTP_USER_AGENT']) ? $this->getBrowser($metrics['server_info']['HTTP_USER_AGENT']) : "";
			$choosen_metrics['browser'] = isset($client['name']) ? $client['name'] : "";
			$choosen_metrics['system'] = isset($client['platform']) ? $client['platform'] : "";
			$choosen_metrics['page_url_source'] = isset($metrics['server_info']['HTTP_REFERER']) ? $metrics['server_info']['HTTP_REFERER'] : "";
			$choosen_metrics['page_url'] = isset($metrics['server_info']['REDIRECT_URL']) ? $metrics['server_info']['REDIRECT_URL'] : "";
			$choosen_metrics['status'] = isset($metrics['server_info']['REDIRECT_STATUS']) ? $metrics['server_info']['REDIRECT_STATUS'] : "";
			$choosen_metrics['method'] = isset($metrics['server_info']['REQUEST_METHOD']) ? $metrics['server_info']['REQUEST_METHOD'] : "";
			$choosen_metrics['user'] = isset($metrics['session']['user_id']) ? $metrics['session']['user_id'] : "";
			$choosen_metrics['project'] = isset($metrics['session']['project_db']) ? $metrics['session']['project_db'] : "admin";
			$choosen_metrics['screen_height'] = isset($metrics['session']['screen_height']) ? $metrics['session']['screen_height'] : "";
			$choosen_metrics['screen_width'] = isset($metrics['session']['screen_width']) ? $metrics['session']['screen_width'] : "";
			//	$choosen_metrics['profiler']=$metrics['profiler'];
			$choosen_metrics['metric_id'] = "";
			/*
										  $pos_start=strrpos($metrics['profiler'],$start);
										  $pos_end=strrpos($metrics['profiler'],$end);
										  
										  $got =substr($metrics['profiler'],$pos_start + strlen($start), $pos_end - $pos_start - strlen($start));
										  echo "<h1>sss $got </h1>";
										  */
			if (!strstr($choosen_metrics['page_url'], 'add_screen_size')) {
				$start = "COMPILE_CONTROLLER<div>";
				$end = "</div></fieldset></div>";
				$pos_start = strrpos($metrics['profiler'], $start);
				$pos_end = strrpos($metrics['profiler'], $end);
				$choosen_metrics['page'] = substr($metrics['profiler'], $pos_start + strlen($start), $pos_end - $pos_start - strlen($start));
				$start = "MEMORY_USAGE ";
				$end = " bytes</fieldset>";
				$pos_start = strrpos($metrics['profiler'], $start);
				$pos_end = strrpos($metrics['profiler'], $end);
				$choosen_metrics['memory_usage'] = str_replace(",", "", substr($metrics['profiler'], $pos_start + strlen($start), $pos_end - $pos_start - strlen($start)));
				$start = "Total Execution Time</td><td>";
				$end = "</td></tr></table>";
				$pos_start = strrpos($metrics['profiler'], $start);
				$pos_end = strrpos($metrics['profiler'], $end);
				$choosen_metrics['execution_time'] = substr($metrics['profiler'], $pos_start + strlen($start), $pos_end - $pos_start - strlen($start));
				print_test($choosen_metrics);
				$this->db4 = $this->load->database("spl", TRUE);
				$this->db4->insert('metrics', $choosen_metrics);
			}
		}
	}

	//retrieve statistical information from the 'metrics' table and display it in a table format.
	public function getStat()
	{
		$this->db4 = $this->load->database("spl", TRUE);
		$sql = "SELECT DISTINCT page , count(*) as nombre from metrics GROUP BY page ORDER BY nombre DESC";
		//$sql="SELECT DISTINCT user , count(*) as nombre from metrics GROUP BY user ORDER BY nombre DESC";
		//$sql="SELECT DISTINCT user,page , count(*) as nombre from metrics GROUP BY user,page ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT hist , count(*) as nombre from metrics where hist_num=3  GROUP BY hist ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT hist , count(*) as nombre  ,AVG(date_diff_1) as date_diff_1_v,AVG(date_diff_2) as date_diff_2_v from metrics where hist_num=3 and page LIKE'manage/add_classification'  GROUP BY hist ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT hist , date_diff_1,date_diff_2 from metrics where hist_num=3 and page LIKE'manage/add_classification' AND hist like 'paper/list_paper -> paper/view_paper -> manage/add_classification' ";
		$sql = "SELECT  hist , date_diff_1,date_diff_2 from metrics where hist_num=3 and page LIKE'paper/view_paper' AND hist like '%manage/add_classification -> paper/view_paper' ";
		$sql = "SELECT  DISTINCT hist , count(*) as nombre  ,AVG(date_diff_1) as date_diff_1_v,AVG(date_diff_2) as date_diff_2_v from metrics where hist_num=3 and page LIKE'paper/view_paper'  GROUP BY hist ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT page , count(*) as nombre from metrics GROUP BY page ORDER BY nombre DESC";
		//-numbre total
		//$sql="SELECT  count(*) as nombre from metrics ";
		//-utilisateurs
		//$sql="SELECT DISTINCT  user from metrics";
		//- utilisateur par op�ration
		$sql = "SELECT DISTINCT user , count(*) as nombre from metrics GROUP BY user ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT user,page , count(*) as nombre from metrics GROUP BY user,page ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT project , count(*) as nombre from metrics GROUP BY project ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT page , count(*) as nombre from metrics GROUP BY page ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT page , count(*) as nombre from metrics  GROUP BY page ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT project , count(*) as nombre from metrics GROUP BY project ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT page , count(*) as nombre from metrics  GROUP BY page ORDER BY nombre DESC";
		$sql = "SELECT DISTINCT hist , count(*) as nombre   from metrics where hist_num=3 and page LIKE'manage/add_classification'  GROUP BY hist ORDER BY nombre DESC";
		$res = $this->db4->query($sql)->result_array();
		$tmpl = array(
			'table_open' => '<table class="table table-striped table-hover">',
			'table_close' => '</table>'
		);
		$this->table->set_template($tmpl);
		echo $this->table->generate($res);
		//print_test($res);
	}

	/**
	 * Calculate and update the historical information in the 'metrics' table based on the time and page values. 
	 * The historical information includes the date, time differences between consecutive actions, and the history of pages visited. 
	 * The updated records provide a more comprehensive view of user activity over time
	 */
	public function getLienHist()
	{
		$this->db4 = $this->load->database("spl", TRUE);
		$sql = "SELECT metric_id,user,time, page ,page_url_source,page_url from metrics  ORDER BY  user, time ASC";
		$res = $this->db4->query($sql)->result_array();
		$prev_time_1 = 0;
		$prev_time_2 = 0;
		$prev_page_1 = '';
		$prev_page_2 = '';
		$hist = "";
		foreach ($res as $key => $value) {
			$hist = $value['page'];
			$hist_num = 1;
			$value['date'] = date('Y-m-d : H:i:s', $value['time']);
			if (!empty($prev_time_1)) {
				$value['date_diff_1'] = ($value['time'] - $prev_time_1);
				$value['hist_page_1'] = $prev_page_1;
				if ($value['date_diff_1'] < 3600) {
					$hist = $prev_page_1 . " -> " . $hist;
					$hist_num++;
				}
			} else {
				$value['date_diff_1'] = "";
				$value['hist_page_1'] = "";
			}
			if (!empty($prev_time_2)) {
				$value['date_diff_2'] = ($prev_time_1 - $prev_time_2);
				$value['hist_page_2'] = $prev_page_2;
				if (!empty($prev_time_1) and !empty($value['date_diff_1']) and $value['date_diff_1'] < 3600) {
					if ($value['date_diff_2'] < 3600) {
						$hist = $prev_page_2 . " -> " . $hist;
						$hist_num++;
					}
				}
			} else {
				$value['date_diff_2'] = "";
				$value['hist_page_2'] = "";
			}
			$value['hist'] = $hist;
			$value['hist_num'] = $hist_num;
			$prev_time_2 = $prev_time_1;
			$prev_page_2 = $prev_page_1;
			$prev_time_1 = $value['time'];
			$prev_page_1 = $value['page'];
			print_test($value);
			$res = $this->db4->update('metrics', $value, array('metric_id' => $value['metric_id']));
		}
		//	print_test($res);
	}

	//extract browser information from the user agent string
	//This information can be useful for browser compatibility checks, analytics, and other purposes
	function getBrowser($u_agent)
	{
		//$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$ub = 'Unknown';
		$platform = 'Unknown';
		$version = "";
		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		} elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}
		// Next get the name of the useragent yes seperately and for good reason
		if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		} elseif (preg_match('/Firefox/i', $u_agent)) {
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		} elseif (preg_match('/Chrome/i', $u_agent)) {
			$bname = 'Google Chrome';
			$ub = "Chrome";
		} elseif (preg_match('/Safari/i', $u_agent)) {
			$bname = 'Apple Safari';
			$ub = "Safari";
		} elseif (preg_match('/Opera/i', $u_agent)) {
			$bname = 'Opera';
			$ub = "Opera";
		} elseif (preg_match('/Netscape/i', $u_agent)) {
			$bname = 'Netscape';
			$ub = "Netscape";
		}
		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
			')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
				$version = $matches['version'][0];
			} else {
				if (isset($matches['version'][1]))
					$version = $matches['version'][1];
				else
					$version = "Unknown";
			}
		} else {
			$version = $matches['version'][0];
		}
		// check if we have a number
		if ($version == null || $version == "") {
			$version = "?";
		}
		return array(
			'userAgent' => $u_agent,
			'name' => $bname,
			'version' => $version,
			'platform' => $platform,
			'pattern' => $pattern
		);
	}

	//simulate and test the assignment of users to papers based on certain criteria
	public function test_assignment()
	{
		$number_of_papers = 46;
		$number_of_user = 4;
		$User_per_papers = 3;
		$papers = array();
		$i = 1;
		while ($i <= $number_of_papers) {
			$papers[$i]['paper'] = "paper_" . $i;
			$papers[$i]['users'] = array();
			$j = 1;
			while ($j <= $User_per_papers) {
				$temp_user = $i % $number_of_user + $j;
				if ($temp_user > $number_of_user)
					$temp_user = $temp_user - $number_of_user;
				array_push($papers[$i]['users'], $temp_user);
				$j++;
			}
			$i++;
		}
		//print_test($papers);
		$nuser = array();
		foreach ($papers as $key => $value) {
			foreach ($value['users'] as $key_u => $value_u) {
				if (isset($nuser[$value_u])) {
					$nuser[$value_u]++;
				} else {
					$nuser[$value_u] = 1;
				}
			}
		}
		print_test($nuser);
	}

	//calculate the kappa statistic, which measures the agreement between two raters or evaluators
	public function calculate_kappa()
	{
		$matrice = array(
			0 => array(2, 0),
			1 => array(1, 1),
			2 => array(0, 2)
		);
		$matrice = array(
			0 => array(0, 0, 0, 0, 14),
			1 => array(0, 2, 6, 4, 2),
			2 => array(0, 0, 3, 5, 6),
			3 => array(0, 3, 9, 2, 0),
			4 => array(2, 2, 8, 1, 1),
			5 => array(7, 7, 0, 0, 0),
			6 => array(3, 2, 6, 3, 0),
			7 => array(2, 5, 3, 2, 2),
			8 => array(6, 5, 2, 1, 0),
			9 => array(6, 5, 2, 1, 0)
		);
		$matrice = $this->get_screen_for_kappa();
		print_test($matrice);
		$N = count($matrice);
		$k = count($matrice[0]);
		$n = 0;
		foreach ($matrice[0] as $key => $value) {
			$n += $value;
		}
		print_test($N);
		print_test($n);
		print_test($k);
		$p = array();
		for ($j = 0; $j < $k; $j++) {
			$p[$j] = 0.0;
			for ($i = 0; $i < $N; $i++) {
				$p[$j] = $p[$j] + $matrice[$i][$j];
			}
			$p[$j] = $p[$j] / ($N * $n);
		}
		print_test($p);
		$P = array();
		for ($j = 0; $j < $N; $j++) {
			$P[$j] = 0.0;
			for ($i = 0; $i < $k; $i++) {
				$P[$j] = $P[$j] + ($matrice[$j][$i] * $matrice[$j][$i]);
			}
			$P[$j] = ($P[$j] - $n) / ($n * ($n - 1));
		}
		print_test($P);
		$Pbar = array_sum($P) / $N;
		print_test($Pbar);
		$PbarE = 0.0;
		foreach ($p as $key => $value) {
			$PbarE += $value * $value;
		}
		print_test($PbarE);
		$kappa = ($Pbar - $PbarE) / (1 - $PbarE);
		print_test($kappa);
	}

	/**
     * This function retrieves screening information for calculating the kappa statistic, 
     * which measures inter-rater agreement between screeners
     */
    public function get_screen_for_kappa()
    {
        $screening_phase_info = active_screening_phase_info();
        $current_phase = active_screening_phase();
        //	print_test($screening_phase_info);

        $result = $this->Screening_dataAccess->select_from_screening_paper($current_phase);

        //	print_test($result);
        $result_kappa = array();
        foreach ($result as $key => $value) {
            if (!isset($result_kappa[$value['paper_id']])) {
                $result_kappa[$value['paper_id']] = array(
                    'Included' => 0,
                    'Excluded' => 0,
                );
            }
            if (!empty($value['screening_decision']) and ($value['screening_decision'] == 'Included' or $value['screening_decision'] == 'Excluded')) {
                $result_kappa[$value['paper_id']][$value['screening_decision']] += 1;
            }
        }
        //print_test($result_kappa);
        $result_kappa_clean = array();
        foreach ($result_kappa as $k => $v) {
            array_push($result_kappa_clean, array($v['Included'], $v['Excluded']));
        }
        //print_test($result_kappa_clean);
        return $result_kappa_clean;
    }

	//send a test email using CodeIgniter's Email library.
	public function test_mail_old()
	{
		$ci = get_instance();
		$ci->load->library('email');
		$config['protocol'] = "smtp";
		$config['smtp_host'] = "ssl://smtp.gmail.com";
		$config['smtp_port'] = "465";
		$config['smtp_user'] = "relisgeodes@gmail.com";
		$config['smtp_pass'] = "R3l1sApp";
		$config['charset'] = "utf-8";
		$config['mailtype'] = "html";
		$config['newline'] = "\r\n";
		$ci->email->initialize($config);
		$ci->email->from('relisgeodes@gmail.com', 'ReLiS');
		$list = array('bbigendako@gmail.com');
		$ci->email->to($list);
		$this->email->reply_to('relisgeodes@gmail.com', 'Explendid Videos');
		$ci->email->subject('This is an email test');
		$ci->email->message('It is working. Great!');
		if ($ci->email->send()) {
			echo "Email sent successfully.";
		} else {
			//	echo "Error in sending Email.";
			echo $ci->email->print_debugger();
		}
		//$res=$ci->email->send();
		//print_test($ci->email);
	}

	// send a test email using the send_mail() function from the user_lib library.
	public function test_mail()
	{
		$message = "
					<h2>Relis Validation message</h2>
					<p>
					Wecome to ReLiS:<br/>
					Your validation code is : <b>53653536363</b>
					</p>
					
					test message";
		$subject = "Validation code";
		$destination = array('bbigendako@gmail.com', 'relisgeodes@gmail.com');
		$res = $this->user_lib->send_mail($subject, $message, $destination);
		print_test($res);
	}

	//generate a random string using the random_str() function from the bm_lib library
	public function test_randomstr()
	{
		$res = $this->bm_lib->random_str(10);
		print_test($res);
	}

	//retrieve and print the configuration of a reference table.
	public function test_new_config($ref_table = 'new_users')
	{
		$ref_table_config = get_table_configuration($ref_table);
		print_test($ref_table_config);
	}

	//import data from a CSV file and insert it into the database tables paper and classification.
	public function import_edouard()
	{
		$transfo_kind = array(
			'Structurelle' => 1,
			'Comportementale' => 2,
			'Mixte' => 3,
		);
		$mm_kind = array(
			'input specific / output general' => 1,
			'input specific / output specific' => 2,
			'input general / output general' => 3,
			'input general / output specific' => 4,
		);
		$model_kind = array(
			'Jouets' => 1,
			'Open source' => 3,
			'Industriels' => 2,
		);
		$intent = array(
			'Abstraction' => 2,
			'Analysis' => 6,
			'Editing' => 7,
			'Language Translation' => 4,
			'Model Composition' => 9,
			'Model Visualization' => 8,
			'Refinement' => 1,
			'Semantic Definition' => 3,
		);
		$transfo_langauge = array(
			'Langage dédié (QVT…)' => 1,
			'Langage classique (Java…)' => 2,
			'Langage ad hoc' => 3,
		);
		$validation = array(
			'No validation' => 2,
			'Validation empirique' => 1,
			'Validation théorique (formel)' => 3,
		);
		$scope = array(
			'Exo/Out-place' => 3,
			'Endo/In-place' => 1,
			'Endo/Out-place' => 2,
		);
		$orientation = array(
			'Académie' => 1,
			'Industrie' => 2
		);
		$all_file = "cside/test/classification_edouard.csv";
		ini_set('auto_detect_line_endings', TRUE);
		$fp = fopen($all_file, 'rb');
		$i = 1;
		$last_count = 0;
		$paper = array();
		$classification = array();
		$i = 0;
		while ((($Tline = (fgetcsv($fp, 0, ";", '"')))) !== false) {
			//	print_test($Tline);
			if ($i > 0) {
				print_test("element:" . $i);
				//$Tline = array_map( "utf8_encode", $Tline );
				$preview = "";
				$preview = !empty($Tline[1]) ? "<b>Authors:</b><br/>" . $this->mres_escape($Tline[1]) . " <br/>" : "";
				$preview .= !empty($Tline[7]) ? "<b>Key words:</b><br/>" . $this->mres_escape($Tline[7]) . " <br/>" : "";
				$paper = array(
					'id' => $i,
					'bibtexKey' => 'paper_' . $Tline[0],
					'title' => $this->mres_escape($Tline[2]),
					'preview' => $preview,
					'abstract' => $this->mres_escape($Tline[6]),
					'doi' => $Tline[4],
					'year' => $Tline[3],
					'added_by' => 1,
					'addition_mode' => 'Automatic',
					'classification_status' => 'To classify',
					'operation_code' => '1_' . time()
				);
				print_test($paper);
				//	$res=$this->db_current->insert('paper',$paper);
				//	print_test($res);
				$classification = array(
					'class_paper_id' => $i,
					'transfo_kind' => $transfo_kind[$Tline[10]],
					'mm_kind' => $mm_kind[$Tline[12]],
					'model_kind' => $model_kind[$Tline[18]],
					'intent' => $intent[$Tline[14]],
					'transfo_langauge' => $transfo_langauge[$Tline[16]],
					'validation' => $validation[$Tline[20]],
					'scope' => $scope[$Tline[22]],
					'orientation' => $orientation[$Tline[24]],
					'comment_transfo_kind' => $this->mres_escape($Tline[11]),
					'comment_mm_kind' => $this->mres_escape($Tline[13]),
					'comment_model_kind' => $this->mres_escape($Tline[19]),
					'comment_intent' => $this->mres_escape($Tline[15]),
					'comment_transfo_langauge' => $this->mres_escape($Tline[17]),
					'comment_validation' => $this->mres_escape($Tline[21]),
					'comment_scope' => $this->mres_escape($Tline[23]),
					'comment_orientation' => $this->mres_escape($Tline[25]),
					'year' => $Tline[3],
				);
				print_test($classification);
				//	$res=$this->db_current->insert('classification',$classification);
				//	print_test($res);
			}
			$i++;
		}
	}

	// import data from a CSV file and insert it into the database tables paper and classification.
	public function import_lechanceux()
	{
		$template_style = array(
			'Predefined' => 1,
			'Output-based' => 2,
			'Rule-based' => 3,
		);
		$design_time = array(
			'General purpose' => 1,
			'Domain specific' => 2,
			'Schema' => 3,
			'Programming Language' => 4
		);
		$run_time = array(
			'General purpose' => 1,
			'Domain specific' => 2,
			'Structured data' => 3,
			'Source code' => 4
		);
		$output_type = array(
			'Source code' => 1,
			'Structured data' => 2,
			'Natural language' => 3
		);
		$tool = array(
			'Acceleo' => 1,
			'Xpand' => 2,
			'EGL' => 3,
			'JET' => 4,
			'MOFScript' => 5,
			'Other' => 6,
			'Programmed' => 7,
			'Simulink TLC' => 8,
			'StringTemplate' => 9,
			'T4' => 10,
			'Unspecified' => 11,
			'Velocity' => 12,
			'Rational' => 13,
			'XSLT' => 14,
			'Fujaba' => 15,
			'FreeMarker' => 16,
			'Rhapsody' => 17,
			'Xtend' => 18
		);
		$mde = array(
			'Yes' => 1,
			'No' => 0
		);
		$context = array(
			'Standalone' => 1,
			'Intermediate' => 2,
			'Last' => 3
		);
		$validation = array(
			'Benchmark' => 1,
			'Case study' => 2,
			'User study' => 3,
			'No validation' => 4,
			'Formal' => 3
		);
		$scale = array(
			'Small scale' => 1,
			'Large scale' => 2,
			'No application' => 3
		);
		$domain = array(
			'Software engineering' => 1,
			'Embedded systems' => 2,
			'Web technology' => 3,
			'Networking' => 4,
			'Aspect-oriented systems' => 5,
			'Mobile systems' => 6,
			'Programming languages' => 7,
			'Testing' => 8,
			'Other' => 9,
			'Compilers' => 10,
			'Bio-medical' => 11,
			'Distributed systems' => 12,
			'Simulation ' => 13,
			'Databases' => 14,
			'Security' => 15,
			'Artificial intelligence' => 16,
			'Refactoring' => 17,
			'Robotics' => 18,
			'Graphics' => 19
		);
		$orientation = array(
			'Academic' => 1,
			'Industry' => 2
		);
		$publication_type = array(
			'C' => 1,
			'J' => 2,
			'O' => 3
		);
		$venue_type = array(
			'MDE' => 1,
			'Other' => 2,
			'SE' => 3
		);
		$all_file = "cside/test/classification_lechanceux.csv";
		ini_set('auto_detect_line_endings', TRUE);
		$fp = fopen($all_file, 'rb');
		$i = 1;
		$last_count = 0;
		$paper = array();
		$classification = array();
		$i = 0;
		while ((($Tline = (fgetcsv($fp, 0, ";", '"')))) !== false) {
			print_test($Tline);
			if ($i > 0) {
				print_test("element:" . $i);
				//$Tline = array_map( "utf8_encode", $Tline );
				$preview = "";
				//$preview=!empty($Tline[1])?"<b>Authors:</b><br/>".$this->mres_escape($Tline[1])." <br/>":"";
				//$preview.=!empty($Tline[7])?"<b>Key words:</b><br/>".$this->mres_escape($Tline[7])." <br/>":"";
				$paper = array(
					'id' => $i,
					'bibtexKey' => 'paper_' . $i,
					'title' => $this->mres_escape($Tline[1]),
					//'preview'=>$preview,
					//'abstract'=>$this->mres_escape($Tline[6]),
					//'doi'=>$Tline[4],
					'year' => $Tline[2],
					'added_by' => 1,
					'addition_mode' => 'Automatic',
					'classification_status' => 'To classify',
					'operation_code' => '1_' . time()
				);
				print_test($paper);
				//	$res=$this->db_current->insert('paper',$paper);
				//	print_test($res);
				$classification = array(
					'class_paper_id' => $i,
					'template_style' => $template_style[$Tline[15]],
					'design_time' => $design_time[$Tline[6]],
					'run_time' => $run_time[$Tline[13]],
					'output_type' => $output_type[$Tline[10]],
					'tool' => $tool[$Tline[3]],
					'mde' => $mde[$Tline[12]],
					'context' => $context[$Tline[5]],
					'validation' => $validation[$Tline[18]],
					'scale' => $scale[$Tline[8]],
					'domain' => $domain[$Tline[17]],
					'orientation' => $orientation[$Tline[9]],
					'publication_type' => $publication_type[$Tline[19]],
					'venue_type' => $venue_type[$Tline[21]],
					'comment_template_style' => $this->mres_escape($Tline[16]),
					'comment_design_time' => $this->mres_escape($Tline[7]),
					'comment_run_time' => $this->mres_escape($Tline[14]),
					'comment_output_type' => $this->mres_escape($Tline[11]),
					'comment_tool' => $this->mres_escape($Tline[4]),
					'publication_name' => $this->mres_escape($Tline[20]),
					'year' => $Tline[2],
				);
				print_test($classification);
				//	$res=$this->db_current->insert('classification',$classification);
				//	print_test($res);
			}
			$i++;
		}
	}

	/**
	 * ensure that special characters are properly escaped before inserting the string into a MySQL query, 
	 * preventing any potential SQL injection vulnerabilities
	 */
	private function mres_escape($value)
	{
		$search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
		$replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");
		return str_replace($search, $replace, $value);
	}

	//handle starting, stopping, checking the status, and tailing the log of a Tomcat server
	public function start_editor($value = 1)
	{
		$commands = array(
			'start' => "/u/relis/tomcat/bin/startup.sh",
			'stop' => "/u/relis/tomcat/bin/shutdown.sh",
			'status' => "netstat -lnp | grep 8080",
			'tail' => " tail /u/relis/tomcat/logs/catalina.out",
		);
		if ($value == 1)
			$cmd = "/u/relis/tomcat/bin/startup.sh";
		elseif ($value == 2)
			$cmd = "/u/relis/tomcat/bin/shutdown.sh";
		//$message = exec($cmd);
		//print_test($message);
	}

	/*
	 * Page permettant de lancer une commande
	 */
	public function manage_editor($request = "zz", $run = 1)
	{
		if (!has_usergroup(1)) {
			set_top_msg(" You have no access to this feature ", 'error');
			redirect('home');
		}
		$commands = array(
			'Start' => "/u/relis/tomcat/bin/startup.sh",
			'Stop' => "/u/relis/tomcat/bin/shutdown.sh",
			'Status' => "netstat -lnp | grep 8080",
			'Log' => " tail /u/relis/tomcat/logs/catalina.out",
			'g_status' => "  cd /u/relis/public_html/relis_app  &&   git status",
			'g_pull' => "  cd /u/relis/public_html/relis_app  &&   git pull",
		);
		$script = "";
		$normal = FALSE;
		//print_test($request);
		if ($this->input->post()) {
			$post_arr = $this->input->post();
			if (!empty($post_arr['script'])) {
				$script = $post_arr['script'];
			}
		} elseif (!empty($request) and !empty($commands[$request])) {
			$normal = True;
			$script = $commands[$request];
		}
		if (!empty($script)) {
			if ($normal) {
				if ($request == 'Start') {
					//chech status
					$status = exec(trim($commands['Status']));
					if (!empty($status)) { // server already runnning
						$message = "Server already running.<br/> If you cannot access the editor please wait until the end of the startup process!";
					} else {
						$message = exec(trim($script));
					}
				} elseif ($request == 'Status') {
					$status = exec(trim($commands['Status']));
					if (!empty($status)) { // server already runnning
						$message = "Server  running";
					} else {
						$message = "Server not running";
					}
					//$message=$status;
				} else {
					$message = exec(trim($script));
				}
			} else {
				$message = exec(trim($script));
			}
			$data['command_response'] = $message;
		} else {
			$data['command_response'] = 'No command run';
		}
		$data['commands'] = $commands;
		if (active_user_id() == 1) {
			$data['allow_manual_sript'] = true;
		}
		$data['title'] = lng_min('Editor server');
		$data['page'] = 'editor_command';
		$data['top_buttons'] = get_top_button('all', lng_min('Back to editor'), 'install/relis_editor/admin', lng_min('Back to editor'), ' fa-exchange', '', ' btn-info ');
		$data['top_buttons'] .= get_top_button('back', 'Back', 'manage');
		$this->load->view('shared/body', $data);
	}

	///Backup database
	/**
	 * Host where the database is located
	 */
	var $host;
	/**
	 * Username used to connect to database
	 */
	var $username;
	/**
	 * Password used to connect to database
	 */
	var $passwd;
	/**
	 * Database to backup
	 */
	var $dbName;
	/**
	 * Database charset
	 */
	var $charset;
	/**
	 * Database connection
	 */
	var $conn;
	/**
	 * Backup directory where backup files are stored
	 */
	var $backupDir;
	/**
	 * Output backup file
	 */
	var $backupFile;
	/**
	 * Use gzip compression on backup file
	 */
	var $gzipBackupFile;
	/**
	 * Content of standard output
	 */
	var $output;
	/**
	 * Disable foreign key checks
	 */
	var $disableForeignKeyChecks;
	/**
	 * Batch size, number of rows to process per iteration
	 */
	var $batchSize;

	//perform a database backup
	public function backup_db()
	{
		//echo "backup ";
		//echo $this->db_current->database;
		//echo $this->db_current->password;
		//echo $this->db_current->username;
		$this->initialize_backup();
		$this->conn = $this->initializeDatabase();
		set_time_limit(900); // 15 minutes
		if (php_sapi_name() != "cli") {
			echo '<div style="font-family: monospace;">';
		}
		$result = $this->backupTables() ? 'OK' : 'KO';
		//echo "<h1>$result</h1>";
		//$backupDatabase->obfPrint('Backup result: ' . $result, 1);
		// Use $output variable for further processing, for example to send it by email
		//$output = $backupDatabase->getOutput();
		if (php_sapi_name() != "cli") {
			echo '</div>';
		}
	}

	//initialize the backup process by setting up various configuration parameters
	private function initialize_backup()
	{
		$this->host = $this->db_current->hostname;
		$this->username = $this->db_current->username;
		$this->passwd = $this->db_current->password;
		$this->dbName = $this->db_current->database;
		$this->charset = 'utf8';
		//$this->conn                    = $this->initializeDatabase();
		$this->backupDir = "C:/xampp/htdocs/relis/relis_dev/cside/metrics";
		$this->backupFile = 'myphp-backup-' . $this->dbName . '-' . date("Ymd_His", time()) . '.sql';
		$this->gzipBackupFile = false;
		$this->disableForeignKeyChecks = true;
		$this->batchSize = 1000; // default 1000 rows
		//$this->output                  = '';
	}

	/**
	 * establishe a database connection using mysqli, sets the character set, and handles any errors that may occur during the connection process
	 */
	private function initializeDatabase()
	{
		try {
			$conn = mysqli_connect($this->host, $this->username, $this->passwd, $this->dbName);
			if (mysqli_connect_errno()) {
				throw new Exception('ERROR connecting database: ' . mysqli_connect_error());
				die();
			}
			if (!mysqli_set_charset($conn, $this->charset)) {
				mysqli_query($conn, 'SET NAMES ' . $this->charset);
			}
		} catch (Exception $e) {
			print_r($e->getMessage());
			die();
		}
		return $conn;
	}

	/**
	 * Backup the whole database or just some tables
	 * Use '*' for whole database or 'table1 table2 table3...'
	 * @param string $tables
	 */
	public function backupTables($tables = '*')
	{
		try {
			/**
			 * Tables to export
			 */
			if ($tables == '*') {
				$tables = array();
				$result = mysqli_query($this->conn, 'SHOW TABLES');
				while ($row = mysqli_fetch_row($result)) {
					$tables[] = $row[0];
				}
			} else {
				$tables = is_array($tables) ? $tables : explode(',', str_replace(' ', '', $tables));
			}
			$sql = 'CREATE DATABASE IF NOT EXISTS `' . $this->dbName . "`;\n\n";
			$sql .= 'USE `' . $this->dbName . "`;\n\n";
			/**
			 * Disable foreign key checks
			 */
			if ($this->disableForeignKeyChecks === true) {
				$sql .= "SET foreign_key_checks = 0;\n\n";
			}
			/**
			 * Iterate tables
			 */
			foreach ($tables as $table) {
				$this->obfPrint("Backing up `" . $table . "` table..." . str_repeat('.', 50 - strlen($table)), 0, 0);
				/**
				 * CREATE TABLE
				 */
				$sql .= 'DROP TABLE IF EXISTS `' . $table . '`;';
				$row = mysqli_fetch_row(mysqli_query($this->conn, 'SHOW CREATE TABLE `' . $table . '`'));
				$sql .= "\n\n" . $row[1] . ";\n\n";
				/**
				 * INSERT INTO
				 */
				$row = mysqli_fetch_row(mysqli_query($this->conn, 'SELECT COUNT(*) FROM `' . $table . '`'));
				$numRows = $row[0];
				// Split table in batches in order to not exhaust system memory
				$numBatches = intval($numRows / $this->batchSize) + 1; // Number of while-loop calls to perform
				for ($b = 1; $b <= $numBatches; $b++) {
					$query = 'SELECT * FROM `' . $table . '` LIMIT ' . ($b * $this->batchSize - $this->batchSize) . ',' . $this->batchSize;
					$result = mysqli_query($this->conn, $query);
					$realBatchSize = mysqli_num_rows($result); // Last batch size can be different from $this->batchSize
					$numFields = mysqli_num_fields($result);
					if ($realBatchSize !== 0) {
						$sql .= 'INSERT INTO `' . $table . '` VALUES ';
						for ($i = 0; $i < $numFields; $i++) {
							$rowCount = 1;
							while ($row = mysqli_fetch_row($result)) {
								$sql .= '(';
								for ($j = 0; $j < $numFields; $j++) {
									if (isset($row[$j])) {
										$row[$j] = addslashes($row[$j]);
										$row[$j] = str_replace("\n", "\\n", $row[$j]);
										$row[$j] = str_replace("\r", "\\r", $row[$j]);
										$row[$j] = str_replace("\f", "\\f", $row[$j]);
										$row[$j] = str_replace("\t", "\\t", $row[$j]);
										$row[$j] = str_replace("\v", "\\v", $row[$j]);
										$row[$j] = str_replace("\a", "\\a", $row[$j]);
										$row[$j] = str_replace("\b", "\\b", $row[$j]);
										$sql .= '"' . $row[$j] . '"';
									} else {
										$sql .= 'NULL';
									}
									if ($j < ($numFields - 1)) {
										$sql .= ',';
									}
								}
								if ($rowCount == $realBatchSize) {
									$rowCount = 0;
									$sql .= ");\n"; //close the insert statement
								} else {
									$sql .= "),\n"; //close the row
								}
								$rowCount++;
							}
						}
						$this->saveFile($sql);
						$sql = '';
					}
				}
				/**
				 * CREATE TRIGGER
				 */
				// Check if there are some TRIGGERS associated to the table
				/*$query = "SHOW TRIGGERS LIKE '" . $table . "%'";
														$result = mysqli_query ($this->conn, $query);
														if ($result) {
														$triggers = array();
														while ($trigger = mysqli_fetch_row ($result)) {
														$triggers[] = $trigger[0];
														}
										   
														// Iterate through triggers of the table
														foreach ( $triggers as $trigger ) {
														$query= 'SHOW CREATE TRIGGER `' . $trigger . '`';
														$result = mysqli_fetch_array (mysqli_query ($this->conn, $query));
														$sql.= "\nDROP TRIGGER IF EXISTS `" . $trigger . "`;\n";
														$sql.= "DELIMITER $$\n" . $result[2] . "$$\n\nDELIMITER ;\n";
														}
										   
														$sql.= "\n";
										   
														$this->saveFile($sql);
														$sql = '';
														}*/
				$sql .= "\n\n";
				///$this->obfPrint('OK');
			}
			/**
			 * Re-enable foreign key checks
			 */
			if ($this->disableForeignKeyChecks === true) {
				$sql .= "SET foreign_key_checks = 1;\n";
			}
			$this->saveFile($sql);
			if ($this->gzipBackupFile) {
				$this->gzipBackupFile();
			} else {
				print_test('Backup file succesfully saved to ' . $this->backupDir . '/' . $this->backupFile, 1, 1);
				set_log('backup', 'Database succesfully saved to ' . $this->backupDir . '/' . $this->backupFile);
			}
		} catch (Exception $e) {
			print_test($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * Save SQL to file
	 * @param string $sql
	 */
	protected function saveFile(&$sql)
	{
		if (!$sql)
			return false;
		try {
			if (!file_exists($this->backupDir)) {
				mkdir($this->backupDir, 0777, true);
			}
			file_put_contents($this->backupDir . '/' . $this->backupFile, $sql, FILE_APPEND | LOCK_EX);
		} catch (Exception $e) {
			print_r($e->getMessage());
			return false;
		}
		return true;
	}

	/*
	 * Gzip backup file
	 *
	 * @param integer $level GZIP compression level (default: 9)
	 * @return string New filename (with .gz appended) if success, or false if operation fails
	 */
	protected function gzipBackupFile($level = 9)
	{
		if (!$this->gzipBackupFile) {
			return true;
		}
		$source = $this->backupDir . '/' . $this->backupFile;
		$dest = $source . '.gz';
		$this->obfPrint('Gzipping backup file to ' . $dest . '... ', 1, 0);
		$mode = 'wb' . $level;
		if ($fpOut = gzopen($dest, $mode)) {
			if ($fpIn = fopen($source, 'rb')) {
				while (!feof($fpIn)) {
					gzwrite($fpOut, fread($fpIn, 1024 * 256));
				}
				fclose($fpIn);
			} else {
				return false;
			}
			gzclose($fpOut);
			if (!unlink($source)) {
				return false;
			}
		} else {
			return false;
		}
		$this->obfPrint('OK');
		return $dest;
	}

	/**
	 * Prints message forcing output buffer flush
	 *
	 */
	public function obfPrint($msg = '', $lineBreaksBefore = 0, $lineBreaksAfter = 1)
	{
		print_test($msg);
	}

	/**
	 * Returns full execution output
	 *
	 */
	public function getOutput()
	{
		return $this->output;
	}
}