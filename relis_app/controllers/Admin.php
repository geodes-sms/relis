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
 * This controller contain functions accessed by supper admin during development to create
 *  Tables, Views and stored procedures
 */

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Admin extends CI_Controller
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
		redirect('project/projects_list');
	}

	/**
	 * display a list of configurations and provide options for managing them, allowing administrators to perform actions such as creating tables, 
	 * generating stored procedures, and creating views for different configurations.
	 */
	public function list_configurations()
	{
		if (!has_usergroup(1)) //just for admins
			exit;
		$list_config = array();
		array_push($list_config, array('#', 'Config', 'Display structure ', 'Create table', 'Generate stored procedures', 'Create views', ));
		if (project_db() == 'admin' || project_db() == 'default') {
			$configurations = array('users', 'usergroup', 'project', 'user_project', 'logs', 'str_mng', 'config_admin', 'debug', 'user_creation', 'info');
			$data['left_menu_admin'] = True;
		} else {
			$configurations = array(
				'config',
				'exclusioncrieria',
				'inclusioncriteria',
				'research_question',
				'papers_sources',
				'search_strategy',
				'author',
				'venue',
				'papers',
				'paper_author',
				'screen_phase',
				'screening',
				'screen_decison',
				'str_mng',
				'operations'
				,
				'qa_questions',
				'qa_responses',
				'qa_result',
				'qa_assignment',
				'qa_validation_assignment',
				'assignation',
				'debug',
				'affiliation',
				'classification'
			);
		}
		foreach ($configurations as $key => $value_config) {
			//print_test(get_table_configuration($value));ding to structure
			array_push(
				$list_config,
				array(
					$key + 1,
					$value_config,
					anchor('admin/describe_config/' . $value_config, 'Display structure ' . $value_config),
					anchor('admin/create_tables_config/' . $value_config, 'Create table for ' . $value_config),
					anchor('admin/create_stored_procedures/' . $value_config, 'Generate stored procedures for ' . $value_config),
					anchor('admin/create_views/' . $value_config, 'Create views for ' . $value_config),
				)
			);
		}
		$data['page_title'] = "Setup - Configurations management";
		$data['nombre'] = 1;
		$data['list'] = $list_config;
		$data['page'] = 'general/list';
		// print_test($data);
		$this->load->view('shared/body', $data);
	}

	//automate the process of creating database tables for a specific configuration entity, based on its table configuration.
	public function create_tables_config($entity_config)
	{
		$table_configuration = get_table_configuration($entity_config);
		//	print_test($table_configuration);
		create_table_configuration($table_configuration);
		echo anchor('admin/list_configurations', "<h1>Back</h1>");
	}

	//display the description of a specific configuration entity
	public function describe_config($entity_config)
	{
		$table_configuration = get_table_configuration($entity_config);
		$table_to_display[0] = array('id', 'Title', 'DB type', 'Field type', 'Mandatory');
		//$table_to_display[0]=array('id','Title');
		$i = 1;
		foreach ($table_configuration['fields'] as $key => $value) {
			$type_db = !empty($value['field_type']) ? $value['field_type'] : "";
			$type_db .= !empty($value['field_size']) ? " (" . $value['field_size'] . ") " : "";
			$field_type = !empty($value['input_type']) ? $value['input_type'] : "";
			$field_type .= !empty($value['input_select_source']) ? " - " . $value['input_select_source'] . " " : "";
			$field_type .= !empty($value['input_select_values']) ? " - " . json_encode($value['input_select_values']) . " " : "";
			$madatory = !empty($value['mandatory']) ? "Yes" : "No";
			$table_to_display[$i] = array($key, $value['field_title'], $type_db, $field_type, $madatory);
			$i++;
		}
		$data['nombre'] = 1;
		$data['list'] = $table_to_display;
		$data['page'] = 'general/list';
		$data['page_title'] = "DESC " . $entity_config;
		$data['top_buttons'] = get_top_button('close', 'Close', 'admin/list_configurations');
		$this->load->view('shared/body', $data);
		$tmpl = array(
			'table_open' => '<table style="width:80%; margin:auto; ">',
			'table_close' => '</table>'
		);
		//$this->table->set_template($tmpl);
		//echo $this->table->generate($table_to_display);
		//echo anchor('admin/list_configurations',"<h1>Back</h1>");
	}

	// provide a convenient way for administrators to create views associated with an entity configuration
	public function create_views($entity_config, $operation = "")
	{
		$table_configuration = get_table_configuration($entity_config);
		if (!empty($table_configuration['table_views'])) {
			foreach ($table_configuration['table_views'] as $key => $view_value) {
				create_view($view_value);
			}
		} else {
			echo "No view available for this element";
		}
		echo anchor('admin/list_configurations', "<h1>Back</h1>");
	}

	/**
	 * automate the process of generating stored procedures for CRUD (Create, Read, Update, Delete) operations associated with an entity configuration.
	 */
	public function create_stored_procedures($entity_config, $operation = "")
	{
		$table_configuration = get_table_configuration($entity_config);
		//	print_test($table_configuration);
		foreach ($table_configuration['operations'] as $operation_key => $operation_value) {
			//	print_test($operation_value);
			if (!empty($operation_value['generate_stored_procedure'])) {
				if ($operation_value['operation_type'] == 'List') {
					//print_test($operation_value);
					$list_config = array(
						'stored_procedure_name' => $operation_value['data_source'],
						'fields' => '*',
						'table_name' => !empty($operation_value['table_name']) ? $operation_value['table_name'] : $table_configuration['table_name'],
						'table_active_field' => $table_configuration['table_active_field'],
						'order_by' => !empty($operation_value['order_by']) ? $operation_value['order_by'] : '',
						'search_by' => !empty($operation_value['search_by']) ? $operation_value['search_by'] : '',
						'conditions' => !empty($operation_value['conditions']) ? $operation_value['conditions'] : array(),
					);
					print_test($list_config);
					generate_stored_procedure_list($list_config);
				} elseif (in_array($operation_value['operation_type'], array('Add', 'Edit', 'EditChild', 'AddChild', 'AddDrill'))) {
					$fields = array();
					foreach ($operation_value['fields'] as $key_f => $value_f) {
						if (!empty($table_configuration['fields'][$key_f]) and $value_f['field_state'] != 'disabled' and $value_f['field_state'] != 'drill_down' and !(isset($table_configuration['fields'][$key_f]['multi-select']) and isset($table_configuration['fields'][$key_f]['multi-select']) == 'Yes')) {
							//print_test($key_f);
							//print_test($table_configuration['fields'][$key_f]);
							$field_det = $table_configuration['fields'][$key_f];
							$size = "250";
							$type = "VARCHAR";
							if ($field_det['field_type'] == 'number' || $field_det['field_type'] == 'int') {
								$type = "INT";
							} elseif ($field_det['field_type'] == 'real') {
								$type = "DOUBLE";
							} elseif ($field_det['field_type'] == 'longtext') {
								$type = "LONGTEXT";
							} elseif ($field_det['field_type'] == 'text') {
								if (!empty($field_det['field_size'])) {
									$size = $field_det['field_size'] + 5;
								}
								$type = " VARCHAR($size)";
							} elseif ($field_det['input_type'] == 'image') {
								$type = " LONGBLOB ";
							}
							$fields[$key_f] = $type;
						}
					}
					$add_config = array(
						'stored_procedure_name' => $operation_value['db_save_model'],
						'fields' => $fields,
						'table_name' => !empty($operation_value['table_name']) ? $operation_value['table_name'] : $table_configuration['table_name'],
						'table_active_field' => $table_configuration['table_active_field'],
						'table_id' => $table_configuration['table_id']
					);
					print_test($add_config);
					if (in_array($operation_value['operation_type'], array('Add', 'AddChild', 'AddDrill'))) {
						generate_stored_procedure_add($add_config);
					} else {
						generate_stored_procedure_update($add_config);
					}
				} elseif ($operation_value['operation_type'] == 'Detail') {
					//print_test($operation_value);
					$size = "11";
					$type = "INT";
					$field_table_id = $table_configuration['fields'][$table_configuration['table_id']];
					if ($field_table_id['field_type'] == 'number' || $field_table_id['field_type'] == 'int') {
						$type = "INT";
					} elseif ($field_table_id['field_type'] == 'real') {
						$type = "DOUBLE";
					} elseif ($field_table_id['field_type'] == 'text') {
						if (!empty($field_det['field_size'])) {
							$size = $field_det['field_size'] + 5;
						}
						$type = " VARCHAR($size)";
					}
					$detail_config = array(
						'stored_procedure_name' => $operation_value['data_source'],
						'fields' => '*',
						'table_name' => !empty($operation_value['table_name']) ? $operation_value['table_name'] : $table_configuration['table_name'],
						'table_active_field' => $table_configuration['table_active_field'],
						'table_id' => $table_configuration['table_id'],
						'table_id_type' => $type
					);
					print_test($detail_config);
					generate_stored_procedure_detail($detail_config);
				} elseif ($operation_value['operation_type'] == 'Remove') {
					//print_test($operation_value);
					$remove_config = array(
						'stored_procedure_name' => $operation_value['db_delete_model'],
						'table_name' => !empty($operation_value['table_name']) ? $operation_value['table_name'] : $table_configuration['table_name'],
						'table_active_field' => $table_configuration['table_active_field'],
						'table_id' => $table_configuration['table_id']
					);
					print_test($remove_config);
					generate_stored_procedure_remove($remove_config);
				}
			}
		}
		//create_table_configuration($table_configuration);
		echo anchor('admin/list_configurations', "<h1>Back</h1>");
	}
}