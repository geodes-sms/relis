<?php

/*
	The function creates a configuration array with various settings for managing user_groups. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with user_groups.
		- table_id: The primary key field for the usergroup table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing user_groups.
		- reference_title_min: A shorter version of the reference title.
*/
function get_usergroup()
{

	$config['table_name'] = 'usergroup';
	$config['table_id'] = 'usergroup_id';
	$config['table_active_field'] = 'usergroup_active'; //to detect deleted records
	$config['reference_title'] = 'Usergroups';
	$config['reference_title_min'] = 'Usergroup';


	$config['entity_title']['add'] = 'Add new usergroup';
	$config['entity_title']['edit'] = 'Edit usergroup';
	$config['entity_title']['view'] = 'Usergroup';
	$config['entity_title']['list'] = 'List of usergroups';
	$config['entity_title']['add_child'] = 'Add a user to the usergroup';

	/*
	   	The configuration also includes settings for the list view:
		   - order_by: The sorting order for the user_groups in the list view.
		   - search_by: The field to be used for searching user_groups
		   - links: An array defining links for adding, editing, viewing, deleting user_groups.
		   - The configuration includes a fields array, which defines the fields of the usergroup table.
   	*/
	$config['order_by'] = 'usergroup_name ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'usergroup_name'; // separer les champs par virgule

	//  	$config['links']['add_child']="users/user_usergroup;Add user";

	$config['links']['add_child'] = array(
		'url' => 'users/user_usergroup', //part of the url mandatory for this case
		'label' => 'Add user',
		'title' => 'Add a user to the usergroup',
		'on_list' => True,
		'on_view' => True
	);

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit usergroup',
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
	$fields['usergroup_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show'
	);


	$fields['usergroup_name'] = array(
		'field_title' => 'Name',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 20,
		'mandatory' => ' mandatory '
	);


	$fields['usergroup_description'] = array(
		'field_title' => 'Description',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 50,
		'mandatory' => ' '
	);



	$fields['usergroup_active'] = array(
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