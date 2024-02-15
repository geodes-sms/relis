<?php
function get_str_mng()
{

	$config['table_name'] = 'str_management';
	$config['table_id'] = 'str_id';
	$config['table_active_field'] = 'str_active'; //to detect deleted records
	$config['reference_title'] = 'String management';
	$config['reference_title_min'] = 'String management';

	//list view
	$config['order_by'] = 'str_text ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'str_text'; // separer les champs par virgule

	//  	$config['links']['add_child']="users/user_usergroup;Add user";


	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit string',
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
	$fields['str_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show'
	);


	$fields['str_label'] = array(
		'field_title' => 'Label',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show',
		'field_size' => 400,
		'mandatory' => ' mandatory '
	);
	$fields['str_text'] = array(
		'field_title' => 'Text',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 800,
		'mandatory' => ' mandatory '
	);
	$fields['str_lang'] = array(
		'field_title' => 'Language',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show',
		'field_size' => 3,
		'mandatory' => ' mandatory '
	);

	$fields['str_category'] = array(
		'field_title' => 'Category',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'hidden',
		'on_view' => 'hidden',
		'field_size' => 18,
		'mandatory' => ' mandatory '
	);


	$fields['str_active'] = array(
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