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
 * 
 */
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

//library for retrieving configuration settings for various database tables/entities.
class Entity_configuration_lib
{
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	//Returns an array containing the configuration settings for the specified table.
	public function get_table_configuration($_table, $target_db = 'current')
	{
		$table_configurations = array();
		switch ($_table) {
			case 'users':
				require_once("user/user_configuration.php");
				$table_configurations['users'] = get_config_user();
				break;
			case 'new_users':
				require_once("user/new_user_configuration.php");
				$table_configurations['new_users'] = get_config_new_user();
				break;
			case 'user_creation':
				require_once("user/user_creation_configuration.php");
				$table_configurations['user_creation'] = get_user_creation();
				break;
			case 'usergroup':
				require_once("user/usergroup_configuration.php");
				$table_configurations['usergroup'] = get_config_usergroup();
				break;
			case 'project':
				require_once("project/project_configuration.php");
				$table_configurations['project'] = get_project();
				break;
			case 'user_project':
				require_once("entity_config/user_project_configuration.php");
				$table_configurations['user_project'] = get_user_project();
				break;
			case 'config_admin':
				require_once("entity_config/config_admin_configuration.php");
				$table_configurations['config_admin'] = get_admin_configuration();
				break;
			case 'config':
				require_once("entity_config/config_configuration.php");
				$table_configurations['config'] = get_configuration();
				break;

			case 'exclusioncrieria':
				require_once("entity_config/references_configuration.php");
				$table_configurations['exclusioncrieria'] = get_reference('ref_exclusioncrieria', 'Exclusion criteria', 'exclusioncrieria', 'Criteria');
				break;

			case 'inclusioncriteria':
				require_once("entity_config/references_configuration.php");
				$table_configurations['inclusioncriteria'] = get_reference('ref_inclusioncriteria', 'Inclusion criteria', 'inclusioncriteria', 'Criteria');
				break;
			case 'research_question':
				require_once("entity_config/references_configuration.php");
				$table_configurations['research_question'] = get_reference('research_question', 'Research question', 'research_question', 'Question');
				break;
			case 'affiliation':
				require_once("entity_config/references_configuration.php");
				$table_configurations['affiliation'] = get_reference('ref_affiliation', 'Authors affiliation', 'affiliation', 'Institute');
				break;
			case 'papers_sources':
				require_once("entity_config/references_configuration.php");
				$table_configurations['papers_sources'] = get_reference('ref_papers_sources', 'Papers sources', 'papers_sources');
				break;
			case 'search_strategy':
				require_once("entity_config/references_configuration.php");
				$table_configurations['search_strategy'] = get_reference('ref_search_strategy', 'Search strategy', 'search_strategy');
				break;

			case 'papers':
				require_once("paper/paper_configuration.php");
				$table_configurations['papers'] = get_papers();
				break;
			case 'author':
				require_once("paper/author_configuration.php");
				$table_configurations['author'] = get_author();
				break;
			case 'paper_author':
				require_once("paper/paper_author_configuration.php");
				$table_configurations['paper_author'] = get_paper_author();
				break;
			case 'venue':
				require_once("paper/venue_configuration.php");
				$table_configurations['venue'] = get_venue();
				break;

			case 'screen_phase':
				require_once("screening/screen_phase_configuration.php");
				$table_configurations['screen_phase'] = get_config_screen_phase();
				break;

			case 'screening':
				require_once("screening/screening_configuration.php");
				$table_configurations['screening'] = get_screening();
				break;

			case 'screen_decison':
				require_once("screening/screening_decision_configuration.php");
				$table_configurations['screen_decison'] = get_screening_decision();
				break;
			case 'logs':
				require_once("logs/logs_configuration.php");
				$table_configurations['logs'] = get_logs();
				break;
			case 'info':
				require_once("entity_config/info_configuration.php");
				$table_configurations['info'] = get_info();
				break;

			case 'str_mng':
				require_once("entity_config/str_mng_configuration.php");
				$table_configurations['str_mng'] = get_str_mng();
				break;

			case 'operations':
				require_once("entity_config/relis/operations_configuration.php");
				$table_configurations['operations'] = get_operation();
				break;
			case 'qa_questions':
				require_once("quality_assessment/qa_questions_configuration.php");
				$table_configurations['qa_questions'] = get_qa_questions();
				break;
			case 'qa_responses':
				require_once("quality_assessment/qa_responses_configuration.php");
				$table_configurations['qa_responses'] = get_qa_responses();
				break;
			case 'qa_result':
				require_once("quality_assessment/qa_result_configuration.php");
				$table_configurations['qa_result'] = get_qa_result();
				break;
			case 'qa_assignment':
				require_once("quality_assessment/qa_assignment_configuration.php");
				$table_configurations['qa_assignment'] = get_qa_assignment();
				break;
			case 'qa_validation_assignment':
				require_once("quality_assessment/qa_validation_assignment_configuration.php");
				$table_configurations['qa_validation_assignment'] = get_qa_validation_assignment();
				break;
			case 'assignation':
				require_once("data_extraction/class_assignment_configuration.php");
				$table_configurations['assignation'] = get_class_assignment();
				break;
			// relis project
			case 'debug':
				require_once("debug/debug_configuration.php");
				$table_configurations['debug'] = get_config_debug();
				break;

			case 'exclusion':
				require_once("entity_config/relis/exclusion_config.php");
				$table_configurations['exclusion'] = get_exclusion();
				break;

			case 'inclusion':
				require_once("entity_config/relis/inclusion_config.php");
				$table_configurations['inclusion'] = get_inclusion();
				break;



			/*case 'assignment_screen':
							require_once("entity_config/relis/assignment_screen_config.php");
							$table_configurations['assignment_screen']=get_assignment_screening();
							break;	
							
						case 'assignment_screen_validate':
							require_once("entity_config/relis/assignment_screen_config.php");
							$table_configurations['assignment_screen_validate']=get_assignment_screening('assignment_screen_validate','Paper assignment for screening validation');
							break;	
							
						
							
						case 'screening_validate':
							require_once("entity_config/relis/screening_config.php");
							$table_configurations['screening_validate']=get_screening_set('screening_validate','Screening validation');
							break;	
						*/


			//--------------------------------	












			default:

				$continue = TRUE;
				$target_db = ($target_db == 'current') ? project_db() : $target_db;



				if ($target_db != 'default') {
					//reference tables
					$reftables = $this->CI->DBConnection_mdl->get_reference_tables_list($target_db);


					foreach ($reftables as $key => $value) {
						if ($_table == $value['reftab_label']) {

							//require_once("entity_config/references_configuration.php");
							//$table_configurations['search_strategy']=get_reference('ref_search_strategy','Search strategy','search_strategy');
							//break;

							//	require_once("entity_config/refferences_config.php");
							//$table_configurations[$value['reftab_label']]=get_refference($value['reftab_table'],$value['reftab_desc']);
							require_once("entity_config/references_configuration.php");
							$table_configurations[$value['reftab_label']] = get_reference($value['reftab_table'], $value['reftab_desc'], $value['reftab_label']);
							$continue = FALSE;
						}
					}
				}



				//get generated configuration
				if ($continue) {
					$generated_config = $this->get_install_config($target_db);
					if (!empty($generated_config)) {
						foreach ($generated_config['config'] as $k_conf => $v_conf) {
							$table_configurations[$k_conf] = $v_conf;
						}
					}
				}



				break;
		}


		//	print_test($table_configurations);
		//	exit;


		$table_configurations[$_table]['config_label'] = $_table;
		//get fields to be selected

		if (!empty($table_configurations[$_table])) {
			$config = $table_configurations[$_table];


		} else {

			$config = array();
		}


		if (empty($config['fields'])) {
			set_top_msg('Error : Page "' . $_table . '" not found!', 'error');
			//	print_test($table_configurations); exit;
			redirect('home');

		} else {
			return $config;
		}
	}

	//Method is responsible for retrieving the installation configuration for a specified target database
	public function get_install_config($target_db = 'current')
	{
		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		if ($target_db == 'default') {

			//require_once("table_config/project/install_config_".$target_db.".php");
			//$res=get_classification();

			//print_test($result);
			//$result=$this->clean_install_config($res);

			$result = array();

		} else {
			$project_specific_config_folder = get_ci_config('project_specific_config_folder');
			require_once($project_specific_config_folder . "install_config_" . $target_db . ".php");
			require_once("table_config/project/install_config_" . $target_db . ".php");
			$res = call_user_func('get_classification_' . $target_db);

			$result = $this->clean_install_config($res);
		}
		//print_test($result); exit;
		return $result;

	}

	/*
		method is similar to get_install_config(), but it includes a different installation configuration file specific to the target database.
	*/
	public function get_new_install_config($target_db = 'current')
	{
		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		if ($target_db == 'default') {


			$result = array();

		} else {
			$project_specific_config_folder = get_ci_config('project_specific_config_folder');

			require_once($project_specific_config_folder . "temp/install_config_" . $target_db . ".php");
			$res = call_user_func('get_classification_' . $target_db);

			$result = $this->clean_install_config($res);
		}

		return $result;

	}


	/*
		used to clean up the installation configuration. 
		It performs various operations on the installation configuration data, 
		such as reorganizing reference tables, etc.
	*/
	private function clean_install_config($install_config)
	{
		//cleaning reference tables
		if (!empty($install_config['report'])) {

			$install_config['config']['classification']['report'] = $install_config['report'];
		}
		$reference_tab = array();
		if (!empty($install_config['reference_tables'])) {
			foreach ($install_config['reference_tables'] as $key_ref => $ref_values) {
				$ref = 'ref_' . Slug($key_ref);
				$reference_tab[$ref] = $ref_values;
			}
		}
		$install_config['reference_tables'] = $reference_tab;

		//if(!empty($install_config['reference_tables'])){
		foreach ($install_config['config'] as $key_config => $config_values) {
			$install_config['config'][$key_config]['config_id'] = $install_config['config'][$key_config]['table_name'];

			$operation_fields = array();


			foreach ($config_values['fields'] as $key_field => $value_field) {

				if (!empty($value_field['on_add']) and ($value_field['on_add'] != 'not_set')) {
					$operation_fields['add'][$key_field] = array(
						'mandatory' => !empty($value_field['mandatory']) ? $value_field['mandatory'] : '',
						'field_state' => $value_field['on_add'],
					);
				}
				if (!empty($value_field['on_edit']) and ($value_field['on_edit'] != 'not_set')) {
					$operation_fields['edit'][$key_field] = array(
						'mandatory' => !empty($value_field['mandatory']) ? $value_field['mandatory'] : '',
						'field_state' => $value_field['on_edit'],
					);
				}

				if (!empty($value_field['on_list']) and ($value_field['on_list'] == 'show')) {
					$operation_fields['list'][$key_field] = array();
				}
				if (empty($value_field['on_view']) or (!empty($value_field['on_view']) and ($value_field['on_view'] == 'show'))) {

					$field = array();
					//verification si on doit ajouter des link pour ajout drilldown
					if (!empty($value_field['input_select_source_type']) and $value_field['input_select_source_type'] == 'drill_down') {

						$field = array(
							'drilldown_add_link' => 'element/add_element_child/add_' . $value_field['input_select_values'] . '/',
							'drilldown_edit_link' => 'element/edit_drilldown/edit_' . $value_field['input_select_values'] . '/',
							'drilldown_remove_link' => 'element/delete_element/remove_' . $value_field['input_select_values'] . '/',
							'drilldown_display_link' => 'element/display_element/detail_' . $value_field['input_select_values'] . '/',

						);
					}
					$operation_fields['detail'][$key_field] = $field;
				}

				if (isset($value_field['category_type']) and $value_field['category_type'] == 'IndependantDynamicCategory') {
					//Get reference table
					$ref_table = 'ref_' . Slug($value_field['input_select_values']) . ';ref_value';
					$install_config['config'][$key_config]['fields'][$key_field]['input_select_values'] = $ref_table;

				} elseif (isset($value_field['category_type']) and ($value_field['category_type'] == 'WithMultiValues' or $value_field['category_type'] == 'WithSubCategories' or $value_field['category_type'] == 'ParentExternalKey' or $value_field['category_type'] == 'DependentDynamicCategory')) {

					$input_select_values = trim($value_field['input_select_values']);
					if (!empty($install_config['config'][$input_select_values]['main_field'])) {
						$main_field = $install_config['config'][$input_select_values]['main_field'];
						$install_config['config'][$key_config]['fields'][$key_field]['input_select_values'] = $input_select_values . ";" . $main_field;
					}
				}



			}

			//add operations to differents configs
			$install_config['config'][$key_config]['operations'] = $this->add_operations_to_config($install_config['config'][$key_config], $operation_fields);

		}
		//	}

		if (!empty($install_config['config']['classification']['operations']['add_classification'])) {

			$install_config['config']['classification']['operations']['new_classification'] = $install_config['config']['classification']['operations']['add_classification'];
			$install_config['config']['classification']['operations']['new_classification']['operation_type'] = 'AddChild';
			$install_config['config']['classification']['operations']['new_classification']['parent_config'] = 'papers';
			$install_config['config']['classification']['operations']['new_classification']['master_field'] = 'class_paper_id';
			$install_config['config']['classification']['operations']['new_classification']['parent_detail_source'] = 'get_detail_papers';
			$install_config['config']['classification']['operations']['new_classification']['parent_detail_source_field'] = 'title';
			$install_config['config']['classification']['operations']['new_classification']['fields']['class_paper_id']['field_state'] = 'hidden';
			$install_config['config']['classification']['operations']['new_classification']['generate_stored_procedure'] = FALSE;
			$install_config['config']['classification']['operations']['new_classification']['redirect_after_save'] = 'data_extraction/display_paper/~current_element~';
			$install_config['config']['classification']['operations']['new_classification']['page_title'] = 'Add a classification to the paper : ~current_parent_name~';


			$install_config['config']['classification']['operations']['update_classification'] = $install_config['config']['classification']['operations']['new_classification'];
			$install_config['config']['classification']['operations']['update_classification']['operation_type'] = 'EditChild';
			$install_config['config']['classification']['operations']['update_classification']['fields']['class_paper_id']['field_state'] = 'disabled';
			$install_config['config']['classification']['operations']['update_classification']['page_title'] = "Edit classification";
			$install_config['config']['classification']['operations']['update_classification']['db_save_model'] = "update_classification";
			$install_config['config']['classification']['operations']['update_classification']['generate_stored_procedure'] = True;
			$install_config['config']['classification']['operations']['update_classification']['data_source'] = 'get_detail_classification';
			$install_config['config']['classification']['operations']['update_classification']['support_drilldown'] = True;
			$install_config['config']['classification']['operations']['update_classification']['drilldown_source'] = 'detail_classification';


		}
		//print_test($install_config);
		//add cliquable link on paper title
		if (isset($install_config['config']['classification']['operations']['list_classification']['fields']['class_paper_id'])) {

			$install_config['config']['classification']['operations']['list_classification']['table_name'] = 'view_classification_paper';

			$install_config['config']['classification']['operations']['list_classification']['fields']['class_paper_id'] = array(
				'link' => array(
					'url' => 'element/display_element/detail_classification/',
					'id_field' => 'class_id',
					'trim' => trim_nbr_car(),
				)
			);
			$install_config['config']['classification']['operations']['list_classification']['list_links'] = array();
		}


		return $install_config;
	}

	private function add_operations_to_config($config, $fields)
	{
		$operations = array();
		if ($config != 'classification') {
			if (!empty($fields['add'])) {
				$operations['add_' . $config['config_id']] = array(
					'operation_type' => 'AddChild',
					'operation_title' => 'Add a new ' . $config['entity_label'],
					'operation_description' => 'Add a new ' . $config['entity_label'],
					'page_title' => 'Add a new ' . $config['entity_label'] . ' to : ~current_parent_name~',
					'page_title' => 'Add a new ' . $config['entity_label'],
					'save_function' => 'element/save_element',
					'page_template' => 'general/frm_entity',

					'parent_config' => 'classification',
					'master_field' => 'parent_field_id',
					'parent_detail_source' => 'get_detail_classification',
					'parent_detail_source_field' => 'class_paper_id',
					'redirect_after_save' => 'element/display_element/detail_classification/~current_element~',
					'db_save_model' => 'add_' . $config['config_id'],

					'generate_stored_procedure' => True,

					'fields' => $fields['add'],

					'top_links' => array(

						'back' => array(
							'label' => '',
							'title' => 'Close',
							'icon' => 'close',
							'url' => 'home',
						)

					),

				);


			}

			if (!empty($fields['edit'])) {
				$operations['edit_' . $config['config_id']] = array(
					'operation_type' => 'EditChild',
					'operation_title' => 'Edit a  ' . $config['entity_label'],
					'operation_description' => 'Edit a ' . $config['entity_label'],
					'page_title' => 'Edit a  ' . $config['entity_label'],
					'save_function' => 'element/save_element',
					'data_source' => 'get_detail_' . $config['config_id'],
					'page_template' => 'general/frm_entity',

					'parent_config' => 'classification',
					'master_field' => 'parent_field_id',
					'parent_detail_source' => 'get_detail_classification',
					'parent_detail_source_field' => 'class_paper_id',

					'redirect_after_save' => 'element/display_element/detail_classification/~current_element~',
					'db_save_model' => 'update_' . $config['config_id'],

					'generate_stored_procedure' => True,

					'fields' => $fields['edit'],



					'top_links' => array(

						'back' => array(
							'label' => '',
							'title' => 'Close',
							'icon' => 'close',
							'url' => 'home',
						)

					),

				);


			}

		} else {
			if (!empty($fields['add'])) {
				$operations['add_' . $config['config_id']] = array(
					'operation_type' => 'Add',
					'operation_title' => 'Add a new ' . $config['entity_label'],
					'operation_description' => 'Add a new ' . $config['entity_label'],
					'page_title' => 'Add a new ' . $config['entity_label'],
					'save_function' => 'element/save_element',
					'page_template' => 'general/frm_entity',
					'redirect_after_save' => 'element/entity_list/list_' . $config['config_id'],
					'db_save_model' => 'add_' . $config['config_id'],

					'generate_stored_procedure' => True,

					'fields' => $fields['add'],

					'top_links' => array(

						'back' => array(
							'label' => '',
							'title' => 'Close',
							'icon' => 'close',
							'url' => 'home',
						)

					),

				);


			}

			if (!empty($fields['edit'])) {
				$operations['edit_' . $config['config_id']] = array(
					'operation_type' => 'Edit',
					'operation_title' => 'Edit a  ' . $config['entity_label'],
					'operation_description' => 'Edit a ' . $config['entity_label'],
					'page_title' => 'Edit a  ' . $config['entity_label'],
					'save_function' => 'element/save_element',
					'data_source' => 'get_detail_' . $config['config_id'],
					'page_template' => 'general/frm_entity',
					'redirect_after_save' => 'element/entity_list/list_' . $config['config_id'],
					'db_save_model' => 'update_' . $config['config_id'],

					'generate_stored_procedure' => True,

					'fields' => $fields['edit'],



					'top_links' => array(

						'back' => array(
							'label' => '',
							'title' => 'Close',
							'icon' => 'close',
							'url' => 'home',
						)

					),

				);


			}
		}
		if (!empty($fields['list'])) {
			$operations['list_' . $config['config_id']] = array(
				'operation_type' => 'List',
				'operation_title' => 'List  ' . $config['entity_label_plural'],
				'operation_description' => 'List ' . $config['entity_label_plural'],
				'page_title' => 'List ' . $config['entity_label_plural'],
				//'table_display_style'=>'dynamic_table',
				'data_source' => 'get_list_' . $config['config_id'],
				'generate_stored_procedure' => True,

				'fields' => $fields['list'],
				'list_links' => array(
					'view' => array(
						'label' => 'View',
						'title' => 'Disaly element',
						'icon' => 'folder',
						'url' => 'element/display_element/detail_' . $config['config_id'] . '/',
					),

				),
				'top_links' => array(

					'back' => array(
						'label' => '',
						'title' => 'Close',
						'icon' => 'close',
						'url' => 'home',
					)

				),

			);


		}


		if (!empty($fields['detail'])) {
			$operations['detail_' . $config['config_id']] = array(
				'operation_type' => 'Detail',
				'operation_title' => 'Detail  ' . $config['entity_label'],
				'operation_description' => 'Detail ' . $config['entity_label'],
				'page_title' => 'Detail ' . $config['entity_label'],

				'data_source' => 'get_detail_' . $config['config_id'],
				'generate_stored_procedure' => True,

				'fields' => $fields['detail'],

				'top_links' => array(

					'back' => array(
						'label' => '',
						'title' => 'Close',
						'icon' => 'close',
						'url' => 'home',
					)

				),
			);
		}

		$operations['remove_' . $config['config_id']] = array(
			'operation_type' => 'Remove',
			'operation_title' => 'Remove a ' . $config['entity_label'],
			'operation_description' => 'Delete a ' . $config['entity_label'],

			//'redirect_after_delete'=>'element/entity_list/list_'.$config['config_id'],
			'redirect_after_delete' => 'element/display_element/detail_classification/~current_element~',
			'db_delete_model' => 'remove_' . $config['config_id'],
			'generate_stored_procedure' => True,
		);

		return $operations;
		// exit;
		//add_operation operation
	}
}