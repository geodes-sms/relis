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

/*
	This function returns a configuration array for managing logs. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with logs.
		- table_id: The primary key field for the log table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the logs in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
*/
function get_logs()
{
	$config['config_id'] = 'logs';
	$config['table_name'] = 'log';
	$config['table_id'] = 'log_id';
	$config['table_active_field'] = 'log_active'; //to detect deleted records
	$config['reference_title'] = 'Logs';
	$config['reference_title_min'] = 'Log';

	//list view
	$config['order_by'] = 'log_id DESC '; //mettre la valeur à mettre dans la requette



	$fields['log_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'

	);


	$fields['log_type'] = array(
		'field_title' => 'Name',
		'field_type' => 'text',
		'field_size' => 50,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	$fields['log_user_id'] = array(
		'field_title' => 'User',
		'field_type' => 'number',
		'field_size' => 11,
		'default_value' => 1,

		'field_value' => active_user_id(),

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		'mandatory' => ' mandatory ',
	);

	$fields['log_event'] = array(
		'field_title' => 'Action',
		'field_type' => 'text',
		'field_size' => 200,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	$fields['log_time'] = array(
		'field_title' => 'Timestamp',
		'field_type' => 'time',
		//'input_type'=>'text',
		'default_value' => 'CURRENT_TIMESTAMP',
		'field_value' => bm_current_time('Y-m-d H:i:s'),
		'field_size' => 20,
		'mandatory' => ' mandatory ',
	);
	$fields['log_publish'] = array(
		'field_title' => 'Publish',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '1',

	);
	$fields['log_ip_address'] = array(
		'field_title' => 'IP',
		'field_type' => 'text',
		'field_size' => 200,
		'input_type' => 'text',
	);



	$fields['log_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);
	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on logs. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['add_logs'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add log',
		'operation_description' => 'Add log',
		'page_title' => 'Add log',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'home',
		'db_save_model' => 'add_logs',

		'generate_stored_procedure' => True,

		'fields' => array(
			'log_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'log_type' => array('mandatory' => '', 'field_state' => 'hidden'),
			'log_user_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'log_event' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'log_ip_address' => array('mandatory' => '', 'field_state' => 'enabled'),
			'log_publish' => array('mandatory' => '', 'field_state' => 'hidden')
		),

		'top_links' => array(

			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);

	$operations['detail_logs'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Detail of a log',
		'operation_description' => 'Detail of a log',
		'page_title' => 'Log ',


		'data_source' => 'get_detail_logs',
		'generate_stored_procedure' => True,

		'fields' => array(
			'log_type' => array(),
			'log_user_id' => array(),
			'log_event' => array(),
			'log_time' => array(),
			'log_ip_address' => array()

		),


		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),



		),
	);
	if (has_usergroup(1)) {
		$clear_logs = array(
			'label' => 'Clear logs',
			'title' => 'Clear logs',
			'icon' => 'fa-eraser',
			'url' => 'manager/clear_logs_validation',
		);
	} else {
		$clear_logs = array();
	}
	$operations['list_logs'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List logs',
		'operation_description' => 'List logs',
		'page_title' => 'Logs',

		'table_display_style' => 'normal',

		'data_source' => 'get_list_logs',
		'generate_stored_procedure' => True,

		'fields' => array(
			'log_id' => array(),
			'log_user_id' => array(),
			'log_type' => array(),
			'log_event' => array(),
			'log_time' => array(),

		),
		'order_by' => 'log_id DESC ',
		'search_by' => 'log_type,log_event',

		'list_links' => array(
			'view' => array(
				'label' => 'Display',
				'title' => 'Disaly element',
				'icon' => 'folder',
				'url' => 'element/display_element/detail_logs/',
			)

		),
		'conditions' => array(
			'log_publish' => array(
				'field' => 'log_publish',
				'value' => '1',
				'evaluation' => '',
				'add_on_generation' => TRUE,
				'parameter_type' => 'VARCHAR(3)'
			),
		),

		'top_links' => array(
			'clear_logs' => $clear_logs,
			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),


		),
	);

	$config['operations'] = $operations;

	return $config;

}