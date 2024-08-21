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
 *  :Author: Abdelhamid Rouatbi
 */

/*
	The function creates a configuration array with various settings for managing screening_decisions in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with screening_decision.
		- table_id: The primary key field for the screening_decision table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- The configuration includes a fields array, which defines the fields of the screen_decision table.
		- etc.
*/
function get_screening_phase_config()
{
	$config['config_id'] = 'screen_phase_config';
	$config['table_name'] = 'screen_phase_config';
	$config['table_id'] = 'screen_phase_config_id';
	$config['table_active_field'] = 'config_active'; //to detect deleted records
	
	$config['entity_label'] = 'Screening';
	$config['entity_label_plural'] = 'Screening';
	
	require_once APPPATH. "libraries/entity_config/config_configuration.php";
	$general_config = get_configuration();

	$fields['screen_phase_config_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'
	);

	$fields['screen_phase_id'] = array(
		'field_title' => 'Screen phase',
		'field_type' => 'int',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
        'number_of_values' => 1,
	);

	$fields['config_type'] = array(
		'field_title' => 'Config type',
		'field_type' => 'text',
		'field_size' => 15,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Default' => 'Default',
			'Custom' => 'Custom',
		),
		'field_value' => 'Default',
		'default_value' => 'Default',
		'mandatory' => 'mandatory',
	);

	$fields['screening_inclusion_mode'] = $general_config['fields']['screening_inclusion_mode'];
	$fields['screening_screening_conflict_resolution'] = $general_config['fields']['screening_screening_conflict_resolution'];
	$fields['screening_scscreening_conflict_typereening_conflict_resolution'] = $general_config['fields']['screening_conflict_type'];
	$fields['assign_papers_on'] = $general_config['fields']['assign_papers_on'];
	$fields['screening_result_on'] = $general_config['fields']['screening_result_on'];
	$fields['screening_validation_on'] = $general_config['fields']['screening_validation_on'];
	$fields['screening_reviewer_number'] = $general_config['fields']['screening_reviewer_number'];
	$fields['screening_status_to_validate'] = $general_config['fields']['screening_status_to_validate'];
	$fields['screening_validator_assignment_type'] = $general_config['fields']['screening_validator_assignment_type'];
	$fields['use_kappa'] = $general_config['fields']['use_kappa'];
	$fields['validation_default_percentage'] = $general_config['fields']['validation_default_percentage'];

	$fields['config_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',

		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
    
	$config['fields'] = $fields;
	/*
		The $operations array defines different operations or actions that can be performed on screening_decision. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['detail_screen_phase_config'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Characteristics of a screening phase configuration',
		'operation_description' => 'Characteristics of a screening phase configuration',
		'page_title' => 'Phase configuration',

		//'page_template'=>'element/display_element',

		'data_source' => 'get_detail_screen_phase_config',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'screen_phase_id'=>array(),
			'config_type' => array(),
			'screening_result_on' => array(),
			'assign_papers_on' => array(),
			'screening_reviewer_number' => array(),
			'screening_inclusion_mode' => array(),
			'screening_conflict_type' => array(),
			'screening_screening_conflict_resolution' => array(),
			'use_kappa' => array(),
			'screening_validation_on' => array(),
			'screening_validator_assignment_type' => array(),
			'validation_default_percentage' => array(),

			),

		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_screen_phase_config/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),
		),
	);

	$operations['edit_screen_phase_config'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit a screening phase configuration',
		'operation_description' => 'Edit a screening phase configuration',
		'page_title' => 'Edit screening phase configuration',
		'save_function' => 'screening/edit_screening_config',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/entity_list/list_screen_phases',
		'data_source' => 'get_detail_screen_phase_config',
		'db_save_model' => 'update_screen_phase',

		'generate_stored_procedure' => False,

		'fields' => array(
			'screen_phase_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'screening_result_on' => array('mandatory' => '', 'field_state' => 'enabled'),
			'assign_papers_on' => array('mandatory' => '', 'field_state' => 'enabled'),
			'screening_reviewer_number' => array('mandatory' => '', 'field_state' => 'enabled'),
			'screening_inclusion_mode' => array('mandatory' => '', 'field_state' => 'enabled'),
			'screening_conflict_type' => array('mandatory' => '', 'field_state' => 'enabled'),
			'screening_screening_conflict_resolution' => array('mandatory' => '', 'field_state' => 'enabled'),
			'use_kappa' => array('mandatory' => '', 'field_state' => 'enabled'),
			'screening_validation_on' => array('mandatory' => '', 'field_state' => 'enabled'),
			'screening_validator_assignment_type' => array('mandatory' => '', 'field_state' => 'enabled'),
			'validation_default_percentage' => array('mandatory' => '', 'field_state' => 'enabled'),

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

	$config['operations'] = $operations;	

	return $config;

}