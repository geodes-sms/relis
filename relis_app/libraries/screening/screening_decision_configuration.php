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
	The function creates a configuration array with various settings for managing screening_decisions in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with screening_decision.
		- table_id: The primary key field for the screening_decision table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- The configuration includes a fields array, which defines the fields of the screen_decision table.
		- etc.
*/
function get_screening_decision()
{
	$config['config_id'] = 'screen_decison';
	$config['table_name'] = 'screen_decison';
	$config['table_id'] = 'decison_id';
	$config['table_active_field'] = 'decision_active'; //to detect deleted records
	$config['main_field'] = 'paper_id';

	$config['entity_label'] = 'Screening';
	$config['entity_label_plural'] = 'Screening';



	$fields['decison_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'
	);

	$fields['paper_id'] = array(
		'field_title' => 'Paper',
		'field_type' => 'int',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'papers;CONCAT_WS(" - ",bibtexKey,title)', //the reference table and the field to be displayed

		'mandatory' => ' mandatory ',


	);

	$fields['screening_phase'] = array( // assigned to
		'field_title' => 'Screening phase',
		'field_type' => 'int',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'screen_phase;screen_phase_title',
		'mandatory' => ' mandatory ',
	);



	$fields['screening_decision'] = array(
		'field_title' => 'Decision',
		'field_type' => 'text',
		'field_size' => 15,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Pending' => 'Pending',
			'In review' => 'In review',
			'In conflict' => 'In conflict',
			'Included' => 'Included',
			'Excluded' => 'Excluded',
		),
	);

	$fields['decision_source'] = array(
		'field_title' => 'Decision source',
		'field_type' => 'text',
		'field_size' => 25,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'new_screen' => 'new_screen',
			'edit_screen' => 'edit_screen',
			'conflict_resolution' => 'conflict_resolution'
		),
	);
	$fields['decision_history'] = array(
		'field_title' => 'Decision history',
		//in json
		'field_type' => 'longtext',
		'field_size' => 2000,
		'input_type' => 'textarea'
	);

	$fields['decision_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);

	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on screening_decision. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['list_decisions'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of screening decidions',
		'operation_description' => 'List of screening decidions',
		'page_title' => 'List of screening decidions',

		'page_template' => 'general/list',
		'table_display_style' => 'dynamic_table',
		'data_source' => 'get_list_decisions',
		'generate_stored_procedure' => True,

		'fields' => array(
			'decison_id' => array(),
			'paper_id' => array(),
			'screening_phase' => array(),
			'screening_decision' => array()


		),

		'order_by' => 'screening_id ASC ',
		//'search_by'=>'project_title',


		'list_links' => array(
			'view' => array(
				'label' => 'View',
				'title' => 'Disaly element',
				'icon' => 'folder',
				'url' => 'element/display_element/display_decision/',
			),
			'edit' => array(
				'label' => 'Edit',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_decision/',
			),
			'delete' => array(
				'label' => 'Delete',
				'title' => 'Delete assignment',
				'url' => 'element/delete_element/remove_decision/'
			)

		),

		'top_links' => array(
			'add' => array(
				'label' => '',
				'title' => 'Add a new project',
				'icon' => 'add',
				'url' => 'element/add_element/new_decision',
			),
			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);

	$operations['new_decision'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'New decision',
		'operation_description' => 'New decision',
		'page_title' => 'Assign a paper',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_decisions',
		'db_save_model' => 'new_decision',

		'generate_stored_procedure' => True,

		'fields' => array(
			'decison_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'paper_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_decision' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_phase' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),

		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		)

	);

	$config['operations'] = $operations;

	return $config;

}