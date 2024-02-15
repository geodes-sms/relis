<?php
/*
	The function returns the configuration array ($config) for managing paper assignments.
	The configuration array includes the following information:
		- table_name: The name of the table associated with paper assignments.
		- table_id: The primary key field for the table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing paper assignments.
		- reference_title_min: A shorter version of the reference title.
*/

function get_assignation()
{
	$config['table_name'] = 'assigned';
	$config['table_id'] = 'assigned_id';
	$config['table_active_field'] = 'assigned_active'; //to detect deleted records
	$config['reference_title'] = 'Paper assignment';
	$config['reference_title_min'] = 'Paper assignment';

	//Concerne l'affichage
	/*
	   The configuration also includes settings for displaying and managing the paper assignments:
		   - order_by: The sorting order for the assignments.
		   - links: An array defining links for editing and viewing assignments.
		   - fields: An array defining the fields of the assignment table.
	*/

	$config['order_by'] = 'assigned_id DESC '; //mettre la valeur à mettre dans la requette
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



	$fields['assigned_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',

		//pour l'affichage
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$fields['assigned_paper_id'] = array(
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

	$fields['assigned_user_id'] = array(
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
		'on_add' => 'enable',
		'on_edit' => 'enable',
		'on_list' => 'show'
	);

	$fields['assigned_note'] = array(
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 200,
		'input_type' => 'textarea',


		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
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

	$fields['assigned_time'] = array(
		'field_title' => 'Time',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 200,
		'input_type' => 'date',


		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden'
	);

	$fields['assigned_active'] = array(
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