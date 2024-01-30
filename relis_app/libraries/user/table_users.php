<?php
//Some informations fave been hard codded for form validation
function get_users()
{

	$config['table_name'] = 'users';
	$config['table_id'] = 'user_id';
	$config['table_active_field'] = 'user_active'; //to detect deleted records
	$config['reference_title'] = 'Users';
	$config['reference_title_min'] = 'User';

	//list view
	$config['order_by'] = 'user_name ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'user_name'; // separer les champs par virgule

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit user informations',
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

	$fields['user_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_view' => 'hidden',
		'on_list' => 'show'
	);


	$fields['user_name'] = array(
		'field_title' => 'Name',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 50,
		'mandatory' => ' mandatory '
	);

	$fields['user_username'] = array(
		'field_title' => 'Username',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show',
		'field_size' => 20,
		'mandatory' => ' mandatory '
	);


	$fields['user_mail'] = array(
		'field_title' => 'Email',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 50,
		'mandatory' => ' '
	);


	$fields['user_usergroup'] = array(
		'field_title' => 'Usergroup',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 20,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'usergroup;usergroup_name' //the reference table and the field to be displayed
	);

	$fields['user_password'] = array(
		'field_title' => 'Password',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'input_type' => 'password',
		'on_list' => 'hidden',
		'on_view' => 'hidden',
		'field_size' => 35
	);
	$fields['user_picture'] = array(
		'field_title' => 'Picture',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
		'on_list' => 'hidden',
		'input_type' => 'image'
	);
	$fields['user_projects'] = array(
		'field_title' => 'Projects',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down', //drill_down
		'input_select_values' => 'user_project;project_id', //the reference table and the field to be displayed
		'input_select_key_field' => 'user_id',
		'number_of_values' => '*',
		'compute_result' => 'no',
		'on_add' => 'not_set',
		//not_set for drill_down
		'on_edit' => 'enabled',
		//not_set for drill_down
		'on_view' => 'hidden',
		'on_list' => 'hidden', //for  number of values this must be hidden on list unless ther is an error while getting list from database
		'multi-select' => 'Yes'

	);
	$fields['user_default_lang'] = array(
		'field_title' => 'Default language',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array('en' => 'English', 'fr' => 'Français'),
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);
	$fields['user_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_view' => 'hidden',
		'on_list' => 'hidden'
	);
	$config['fields'] = $fields;


	return $config;

}