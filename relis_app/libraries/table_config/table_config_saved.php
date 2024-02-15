<?php
function get_configuration()
{

	$config['table_name'] = 'config';
	$config['table_id'] = 'config_id';
	$config['table_active_field'] = 'config_active'; //to detect deleted records
	$config['reference_title'] = 'Configuration';
	$config['reference_title_min'] = 'Configuration';

	//list view
	$config['order_by'] = 'config_id ASC '; //mettre la valeur à mettre dans la requette
	//	$config['search_by']='config_id';// separer les champs par virgule

	//  	$config['links']['add_child']="users/user_usergroup;Add user";


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
		'on_list' => False,
		'on_view' => False
	);
	$fields['config_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden'
	);


	$fields['project_title'] = array(
		'field_title' => 'Project title',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show',
		'field_size' => 400,
		'mandatory' => ' mandatory '
	);
	$fields['project_description'] = array(
		'field_title' => 'Project description',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 1000,
		'on_list' => 'show',
		'input_type' => 'textarea',
		'mandatory' => ' mandatory '
	);
	$fields['default_lang'] = array(
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
		'on_list' => 'show'
	);

	$fields['creator'] = array(
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
	$fields['run_setup'] = array(
		'field_title' => 'Run setup',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['rec_per_page'] = array(
		'field_title' => 'Records per page',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden',
		'field_size' => '10',
		'mandatory' => ' mandatory '
	);


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


	return $config;

}