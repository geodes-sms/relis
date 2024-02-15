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



	$fields['editor_url'] = array(
		'field_title' => 'Editor location(url)',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 100,
		'mandatory' => ' mandatory '
	);
	$fields['editor_generated_path'] = array(
		'field_title' => 'Editor workspace',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 100,
		'mandatory' => ' mandatory '
	);

	$fields['csv_field_separator'] = array(
		'field_title' => 'CSV  separator for import',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 2,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(';' => ';', ',' => ','),
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$fields['csv_field_separator_export'] = array(
		'field_title' => 'CSV separator for export',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 2,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(';' => ';', ',' => ','),
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
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