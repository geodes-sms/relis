<?php

/*
	The function creates a configuration array with various settings for managing projects in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with projects.
		- table_id: The primary key field for the project table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing projects.
		- reference_title_min: A shorter version of the reference title.
*/
function get_project()
{
	$config['table_name'] = 'projects';
	$config['table_id'] = 'project_id';
	$config['table_active_field'] = 'project_active'; //to detect deleted records
	$config['reference_title'] = 'Projects';
	$config['reference_title_min'] = 'Project';

	/*
		The configuration also includes settings for the list view:
			- order_by: The sorting order for the projects in the list view.
			- links: An array defining links for editing, viewing, deleting projects.
			- The configuration includes a fields array, which defines the fields of the projects table.
	*/
	$config['order_by'] = 'project_id ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'project_title,project_description'; // separer les champs par virgule

	$config['entity_title']['add'] = 'Add new project';
	$config['entity_title']['edit'] = 'Edit project info';
	$config['entity_title']['view'] = 'Project detail';
	$config['entity_title']['list'] = 'Installed projects';

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
	$config['links']['delete'] = array(
		'label' => 'Delete',
		'title' => 'Delete',
		'on_list' => True,
		'on_view' => True
	);

	$fields['project_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_view' => 'hidden',
		'on_list' => 'show'
	);


	$fields['project_label'] = array(
		'field_title' => 'Short name',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 100,
		'mandatory' => ' mandatory '
	);

	$fields['project_title'] = array(
		'field_title' => 'Title',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 250,
		'mandatory' => ' mandatory ',
		'input_type' => 'text'
	);
	$fields['project_description'] = array(
		'field_title' => 'Description',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 1000,
		'mandatory' => ' mandatory ',
		'input_type' => 'textarea'
	);
	$fields['project_creator'] = array(
		'field_title' => 'Creator',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 20,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name' //the reference table and the field to be displayed
	);
	$fields['project_icon'] = array(
		'field_title' => 'Icon',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
		'on_list' => 'hidden',
		'input_type' => 'image'
	);

	$fields['project_active'] = array(
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