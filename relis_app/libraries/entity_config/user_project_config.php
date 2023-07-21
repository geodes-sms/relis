<?php

/*
	The function creates a configuration array with various settings for managing user_projects. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with user_projects.
		- table_id: The primary key field for the userproject table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing user_projects.
		- reference_title_min: A shorter version of the reference title.
*/
function get_user_project()
{
	$config['table_name'] = 'userproject';
	$config['table_id'] = 'userproject_id';
	$config['table_active_field'] = 'userproject_active'; //to detect deleted records
	$config['reference_title'] = 'User project';
	$config['reference_title_min'] = 'User project';


	/*
	   	The configuration also includes settings for the list view:
		   - order_by: The sorting order for the user_projects in the list view.
		   - links: An array defining links for editing, viewing user_projects.
		   - The configuration includes a fields array, which defines the fields of the user_projects table.
   	*/
	$config['order_by'] = 'userproject_id ASC '; //mettre la valeur à mettre dans la requette
	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit',
		'on_list' => True,
		'on_view' => True
	);
	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);



	$fields['userproject_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',

		//pour l'affichage
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$fields['user_id'] = array(
		'field_title' => 'User',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_id', //the reference table and the field to be displayed
		'compute_result' => 'no',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);

	$fields['project_id'] = array(
		'field_title' => 'Project',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down',
		'drill_down_type' => 'not_linked',
		'input_select_values' => 'project;project_title', //the reference table and the field to be displayed
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$fields['user_role'] = array(
		'field_title' => 'User role',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 50,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Reviewer' => 'Reviewer',
			'Project admin' => 'Project admin',
			'Guest' => 'Guest'
		),
		'initial_value' => 'Reviewer',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$fields['added_by'] = array(
		'field_title' => 'Added by',
		'field_type' => 'number',
		'field_value' => 'active_user',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name', //the reference table and the field to be displayed
		'mandatory' => ' mandatory ',
		'on_add' => 'hidden',
		'on_edit' => 'not_set',
		'on_list' => 'show'
	);

	$fields['add_time'] = array(
		'field_title' => 'Added time',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'show',
		'field_size' => 20,
		'mandatory' => ' mandatory ',
		'on_add' => 'hidden',
		'on_edit' => 'not_set',
	);


	$fields['userproject_active'] = array(
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