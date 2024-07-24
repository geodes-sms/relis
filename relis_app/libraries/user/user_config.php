<?php
//Some informations fave been hard codded for form validation
/*
	The function creates a configuration array with various settings for managing users. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with users.
		- table_id: The primary key field for the users table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing users.
		- reference_title_min: A shorter version of the reference title.
*/
function get_user()
{

	$config['table_name'] = 'users';
	$config['table_id'] = 'user_id';
	$config['table_active_field'] = 'user_active'; //to detect deleted records


	$config['entity_label'] = 'user';
	$config['entity_label_plural'] = 'users';

	$config['entity_title']['add'] = 'Add new user';
	$config['entity_title']['edit'] = 'Edit user';
	$config['entity_title']['view'] = 'User detail';
	$config['entity_title']['list'] = 'List of users';

	//La fontion qui vont etre appeler pour enregistrer un ajout et la modification
	// 	$config['save_new_function']='manager/save_element';
	// 	$config['save_edit_function']='manager/save_element';

	$config['reference_title'] = 'Users';
	$config['reference_title_min'] = 'User';

	/*
	   	The configuration also includes settings for the list view:
		   - order_by: The sorting order for the users in the list view.
		   - search_by: The field to be used for searching users
		   - links: An array defining links for adding, editing, viewing, deleting users.
		   - The configuration includes a fields array, which defines the fields of the users table.
   	*/
	$config['order_by'] = 'user_name ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'user_name'; // separer les champs par virgule


	//links
	$config['links']['add'] = array(
		'label' => '',
		'title' => 'Add new user',
		'icon' => 'plus',
		'on_list' => True,
		'on_view' => False
	);

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit user informations',
		'icon' => 'edit',
		'url' => 'manager/edit_element/users/',
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
		'input_type' => 'email',
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
		'input_select_values' => 'usergroup;usergroup_name', //the reference table and the field to be displayed


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
		'input_select_values' => 'user_project;project_id', //the reference table and the field to be displayed
		'input_select_key_field' => 'user_id',
		'number_of_values' => '*',
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_view' => 'show',
		'on_list' => 'show', //for  number of values this must be hidden on list unless ther is an error while getting list from database
		'multi-select' => 'Yes'

	);
	$fields['created_by'] = array(
		'field_title' => 'Created by',
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

	$fields['creation_time'] = array(
		'field_title' => 'Creation time',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'show',
		'field_size' => 20,
		'mandatory' => ' mandatory ',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
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