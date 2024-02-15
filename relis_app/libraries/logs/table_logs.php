<?php
//Some informations fave been hard codded for form validation
function get_logs()
{

	$config['table_name'] = 'log';
	$config['table_id'] = 'log_id';
	$config['table_active_field'] = 'log_active'; //to detect deleted records
	$config['reference_title'] = 'Logs';
	$config['reference_title_min'] = 'Log';

	//list view
	$config['order_by'] = 'log_id DESC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'log_user_id'; // separer les champs par virgule

	$config['links']['add'] = False;

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit user informations',
		'on_list' => False,
		'on_view' => False
	);

	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => False,
		'on_view' => False
	);

	$fields['log_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_view' => 'hidden',
		'on_list' => 'show'
	);


	$fields['log_type'] = array(
		'field_title' => 'Type log',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'hidden',
		'field_size' => 50,
		'mandatory' => ' mandatory '
	);
	$fields['log_user_id'] = array(
		'field_title' => 'Utilisateur',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 20,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name'
	);

	$fields['log_event'] = array(
		'field_title' => 'Evenement',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show',
		'field_size' => 200,
		'mandatory' => ' mandatory '
	);
	$fields['log_time'] = array(
		'field_title' => 'Time',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 200,
		'mandatory' => ' mandatory '
	);
	$fields['log_ip_address'] = array(
		'field_title' => 'IP',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'hidden',
		'field_size' => 200,
		'mandatory' => ' mandatory '
	);



	$fields['log_active'] = array(
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