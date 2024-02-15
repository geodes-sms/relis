<?php

/*
	The function creates a configuration array with various settings for managing string-management. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with string-management.
		- table_id: The primary key field for the string-management table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing string-management.
		- reference_title_min: A shorter version of the reference title.
*/
function get_str_mng()
{

	$config['table_name'] = 'str_management';
	$config['table_id'] = 'str_id';
	$config['table_active_field'] = 'str_active'; //to detect deleted records
	$config['reference_title'] = 'String management';
	$config['reference_title_min'] = 'String management';

	/*
		The configuration also includes settings for the list view:
			- order_by: The sorting order for the string-management in the list view.
			- links: An array defining links for editing, viewing, deleting string-managements.
			- The configuration includes a fields array, which defines the fields of the string-management table.
	*/
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