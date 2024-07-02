<?php
function get_project()
{

	$config['table_name'] = 'projects';
	$config['table_id'] = 'project_id';
	$config['table_active_field'] = 'project_active'; //to detect deleted records
	$config['reference_title'] = 'Projects';
	$config['reference_title_min'] = 'Project';

	//list view
	$config['order_by'] = 'project_id ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'project_title,project_description'; // separer les champs par virgule

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