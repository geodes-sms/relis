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
	The function returns the configuration array ($config) for managing paper assignments for screening purposes.
*/
function get_assignment_screening($table = 'assignment_screen', $title = 'Paper assignment for screening')
{
	/*
	   The configuration array includes the following information:
	   - table_name: The name of the table associated with paper assignments.
	   - table_id: The primary key field for the table.
	   - table_active_field: The field used to determine whether a record is active or deleted.
	   - reference_title: The title used for referencing paper assignments.
	   - reference_title_min: A shorter version of the reference title.
	   - entity_title: An array defining different titles for adding, editing, viewing, and listing assignments.
	*/

	$config['table_name'] = $table;
	$config['table_id'] = 'assignment_id';
	$config['table_active_field'] = 'assignment_active'; //to detect deleted records
	$config['reference_title'] = $title;
	$config['reference_title_min'] = $title;

	$config['entity_title']['add'] = 'new ' . $title;
	$config['entity_title']['edit'] = 'Edit ' . $title;
	$config['entity_title']['view'] = $title;
	$config['entity_title']['list'] = $title;

	//Concerne l'affichage
	/*
		The configuration also includes settings for displaying and managing the paper assignments:
			- order_by: The sorting order for the assignments.
			- links: An array defining links for editing and viewing assignments.
			- fields: An array defining the fields of the assignment table.
	*/
	$config['order_by'] = 'assignment_id DESC '; //mettre la valeur à mettre dans la requette
	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit assignment',
		'on_list' => True,
		'on_view' => True
	);
	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);



	$fields['assignment_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',

		//pour l'affichage
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$fields['paper_id'] = array(
		'field_title' => 'Paper',
		'field_type' => 'number',
		'field_value' => 'normal',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'papers;CONCAT_WS(" - ",bibtexKey,title)',
		//the reference table and the field to be displayed
		'field_size' => 11,
		'mandatory' => ' mandatory ',


		//pour l'affichage
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show'

	);

	$fields['user_id'] = array(
		// assigned to
		'field_title' => 'Assigned to',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		//the reference table and the field to be displayed
		'mandatory' => ' mandatory ',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['note'] = array(
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 200,
		'input_type' => 'textarea',


		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);

	$fields['assignment_type'] = array(
		'field_title' => 'Assignment type',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 20,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Normal' => 'Normal',
			'Veto' => 'Veto',
			'Info' => 'Info'
		),
		'initial_value' => 'Normal',
		'on_add' => 'hidden',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden',
	);

	$fields['assignment_mode'] = array(
		'field_title' => 'Assignment mode',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 30,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'auto' => 'Automatic',
			'manualy_bulk' => 'Manually Bulk',
			'manualy_single' => 'Manually'
		),
		'initial_value' => 'auto',
		'on_add' => 'hidden',
		'on_edit' => 'not_set',
		'on_list' => 'show'
	);


	$fields['assigned_by'] = array(
		'field_title' => 'Assigned by',
		'field_type' => 'number',
		'field_value' => 'active_user',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		//the reference table and the field to be displayed
		'on_add' => 'hidden',
		'on_edit' => 'not_set',
		'on_list' => 'show'
	);


	$fields['screening_done'] = array(
		'field_title' => 'Screened',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 2,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'initial_value' => 0,
		'input_select_values' => '',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show'
	);
	$fields['assignment_time'] = array(
		'field_title' => 'Time',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 50,
		'input_type' => 'date',


		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden'
	);
	$fields['operation_code'] = array(
		//used  for bulk assignment  in order to reverse the operation
		'field_title' => 'Operation code',
		'field_type' => 'text',
		'field_value' => 'normal',
		'mandatory' => 'mandatory',
		'field_size' => 20,
		'initial_value' => '01',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_view' => 'hidden',
		'on_list' => 'hidden'
	);
	$fields['assignment_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',

		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);

	$config['fields'] = $fields;

	return $config;

}