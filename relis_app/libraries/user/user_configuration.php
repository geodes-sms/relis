<?php
/* ReLiS - A Tool for conducting systematic literature reviews and mapping studies.
 * Copyright (C) 2018  Eugene Syriani
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * --------------------------------------------------------------------------
 *
 *  :Author: Brice Michel Bigendako
 */

/*
	The function creates a configuration array with various settings for managing users. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with users.
		- table_id: The primary key field for the users table.
		- table_active_field: The field used to determine whether a record is active or deleted.
*/
function get_config_user()
{
	$config['config_id'] = 'users';
	$config['table_name'] = table_name('users');
	$config['table_id'] = 'user_id';
	$config['table_active_field'] = 'user_active'; //to detect deleted records
	$config['main_field'] = 'user_name';

	$config['entity_label'] = 'User';
	$config['entity_label_plural'] = 'Users';



	// 	$config['reference_title']='Users';  
	// 	$config['reference_title_min']='User'; 

	/*
		   - order_by: The sorting order for the users in the list view.
		   - The configuration includes a fields array, which defines the fields of the users table.
	   */
	$config['order_by'] = 'user_name ASC '; //mettre la valeur à mettre dans la requette
	//  	$config['search_by']='user_name';// separer les champs par virgule




	$fields['user_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'

	);


	$fields['user_name'] = array(
		'field_title' => 'Name',
		'field_type' => 'text',
		// type that will be used by the DB(change according to the DB)
		//'field_value'=>'normal',				
		'field_size' => 50,
		// type that will be used by the DB

		'input_type' => 'text',
		//the kind of field displayed in form
		'mandatory' => ' mandatory '

	);

	$fields['user_username'] = array(
		'field_title' => 'Username',
		'field_type' => 'text',
		//'field_value'=>'normal',

		'field_size' => 20,
		'input_type' => 'text',
		//the kind of field displayed in form				
		'mandatory' => ' mandatory '



	);


	$fields['user_mail'] = array(
		'field_title' => 'Email',
		'field_type' => 'text',

		//'field_value'=>'normal',
		'field_size' => 100,

		//	'input_type'=>'email',					   			
		'input_type' => 'text',

	);


	$fields['user_usergroup'] = array(
		'field_title' => 'Usergroup',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',


		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'usergroup;usergroup_name',
		//the reference table and the field to be displayed

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
	);

	$fields['user_password'] = array(
		'field_title' => 'Password',
		'field_type' => 'text',
		'field_size' => 35,
		'input_type' => 'password',

	);
	$fields['user_picture'] = array(
		'field_title' => 'Picture',
		'field_type' => 'image',
		//'field_size'=>200,
		//'field_value'=>'normal',			
		'input_type' => 'image',

	);



	$fields['user_projects'] = array(
		'field_title' => 'Projects',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'normal',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'user_project;project_id',
		//the reference table and the field to be displayed
		//'input_select_values'=>'user_project;CONCAT_WS(" - ",project_id,user_role)',
		'input_select_key_field' => 'user_id',
		'number_of_values' => '*',
		'input_select_source_type' => 'drill_down',
		'input_select_key_field' => 'user_id',
		'not_in_db' => True,

		//'multi-select' => 'Yes'



	);


	$fields['created_by'] = array(
		'field_title' => 'Created by',
		'field_type' => 'number',
		'field_size' => 11,
		'default_value' => 1,

		'field_value' => active_user_id(),
		// the default values (may be put it in operation)

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		//the reference table and the field to be displayed
		'mandatory' => ' mandatory ',

	);

	$fields['creation_time'] = array(
		'field_title' => 'Creation time',
		'field_type' => 'time',
		'default_value' => 'CURRENT_TIMESTAMP',
		'field_value' => bm_current_time('Y-m-d H:i:s'),

		'field_size' => 20,
		'mandatory' => ' mandatory ',

	);

	$fields['user_state'] = array(
		'field_title' => 'User active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '1',

	);


	$fields['user_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'


	);

	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on users. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['add_user'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add a new user',
		'operation_description' => 'Used when an admin want to add a new user',
		'page_title' => 'Add new user',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_all_users',
		'db_save_model' => 'add_users',

		'generate_stored_procedure' => True,

		'fields' => array(
			'user_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_state' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_name' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_username' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_mail' => array('mandatory' => '', 'field_state' => 'enabled', 'pattern' => 'valid_email'),
			'user_usergroup' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_password' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_picture' => array('mandatory' => '', 'field_state' => 'enabled'),
			'created_by' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_projects' => array('mandatory' => '', 'field_state' => 'drill_down')
		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);

	$operations['edit_user'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit a new user',
		'operation_description' => 'Used when an admin want to edit users caracteristics',
		'page_title' => 'Edit user ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/entity_list/list_all_users',
		'success_message' => 'Success: changes will be affected at next login ',
		'data_source' => 'get_user_detail',
		'db_save_model' => 'update_users',

		'support_drilldown' => TRUE,
		'drilldown_source' => 'detail_user',

		'generate_stored_procedure' => True,

		'fields' => array(
			'user_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_name' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_username' => array('mandatory' => 'mandatory', 'field_state' => 'disabled'),
			'user_mail' => array('mandatory' => '', 'field_state' => 'enabled', 'pattern' => 'valid_email'),
			'user_usergroup' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_password' => array('mandatory' => '', 'field_state' => 'enabled'),
			'user_picture' => array('mandatory' => '', 'field_state' => 'enabled'),
			'user_projects' => array('mandatory' => '', 'field_state' => 'drill_down')

		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);

	$operations['edit_user_min'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit a new user',
		'operation_description' => 'Used when a user want to edit his profile',
		'page_title' => 'Edit my account',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'home',
		'success_message' => 'Success: changes will be affected at next login ',
		'data_source' => 'get_user_detail',
		'db_save_model' => 'update_users_mine',

		'support_drilldown' => TRUE,
		'drilldown_source' => 'detail_user',

		'generate_stored_procedure' => True,

		'fields' => array(
			'user_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_name' => array('mandatory' => 'mandatory', 'field_state' => 'disabled'),
			'user_username' => array('mandatory' => 'mandatory', 'field_state' => 'disabled'),
			'user_mail' => array('mandatory' => '', 'field_state' => 'enabled', 'pattern' => 'valid_email'),
			//'user_usergroup'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
			'user_password' => array('mandatory' => '', 'field_state' => 'enabled'),
			'user_picture' => array('mandatory' => '', 'field_state' => 'enabled'),
			//'user_projects'=>array('mandatory'=>'','field_state'=>'drill_down')

		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);

	$operations['list_users'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List all user',
		'operation_description' => 'List all users',
		'page_title' => 'Users',

		//'page_template'=>'list',

		'data_source' => 'get_list_users',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'user_id'=>array(),
			'user_name' => array(
				'link' => array(
					'url' => 'element/display_element/detail_user/',
					'id_field' => 'user_id',
					'trim' => '0'
				)
			),
			'user_username' => array(),
			//'user_mail'=>array(),
			'user_usergroup' => array(),
			'user_projects' => array(),
			//'created_by'=>array()

		),
		'order_by' => 'user_name ASC ',
		'search_by' => 'user_name',
		/*	'conditions'=>array('activated_user'=>array(
														   'field'=>'user_state',
														   'value'=>'1',
														   'evaluation'=>'',
														   'add_on_generation'=>True
														   ),
										   'active_user'=>array(
														   'field'=>'user_id',
														   'value'=>active_user_id(),
														   'evaluation'=>'equal',
														   'add_on_generation'=>False,// The id will be called while 
														   'parameter_type'=>'INT'
														   )
														   
						   ),
						   */
		'list_links' => array(
			/*	'view'=>array(
													'label'=>'View',
													'title'=>'Disaly element',
													'icon'=>'folder',
													'url'=>'element/display_element/detail_user/',
												),
										'edit'=>array(
													'label'=>'Edit',
													'title'=>'Edit',
													'icon'=>'edit',
													'url'=>'element/edit_element/edit_user/',
												),
										
												*/
			'delete' => array(
				'label' => 'Delete',
				'title' => 'Delete the user',
				'url' => 'element/delete_element/remove_user/'
			)

		),

		'top_links' => array(
			'add' => array(
				'label' => '',
				'title' => 'Add a new user',
				'icon' => 'add',
				'url' => 'element/add_element/add_user',
			),
			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);

	$operations['detail_user'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Characteristics of a user',
		'operation_description' => 'Characteristics of a user',
		'page_title' => 'User ',

		//'page_template'=>'element/display_element',

		'data_source' => 'get_user_detail',
		'generate_stored_procedure' => True,

		'fields' => array(
			//	'user_id'=>array(),
			'user_name' => array(),
			'user_username' => array(),
			'user_mail' => array(),
			'user_usergroup' => array(),
			'user_projects' => array(
				'drilldown_add_link' => 'element/add_element_child/project_to_user/',
				'drilldown_edit_link' => 'element/edit_drilldown/edit_project_to_user/',
				'drilldown_remove_link' => 'element/delete_element/remove_userproject_c/',
				'drilldown_display_link' => 'element/display_element/detail_userproject/',

			),

			'user_picture' => array(),
			'creation_time' => array(),
			'created_by' => array()

		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_user/~current_element~',
			),

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),



		),
	);

	$operations['detail_user_min'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Characteristics of a user',
		'operation_description' => 'Characteristics of a user',
		'page_title' => 'User ',

		//'page_template'=>'element/display_element',

		'data_source' => 'get_user_detail',
		'generate_stored_procedure' => FALSE,

		'fields' => array(
			//	'user_id'=>array(),
			'user_name' => array(),
			'user_username' => array(),
			'user_mail' => array(),
			'user_usergroup' => array(),
			'user_projects' => array(
				//'drilldown_add_link'=>'element/add_element_child/project_to_user/',
				//'drilldown_edit_link'=>'element/edit_drilldown/edit_project_to_user/',
				//'drilldown_remove_link'=>'element/delete_element/remove_userproject_c/',
				//'drilldown_display_link'=>'element/display_element/detail_userproject/',

			),
			'user_picture' => array(),
			'creation_time' => array(),
			'created_by' => array()

		),


		'top_links' => array(
			'edit' => array(),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),



		),
	);

	$operations['detail_user_min_ed'] = $operations['detail_user_min']; // etail user with edit enabled
	$operations['detail_user_min_ed']['top_links']['edit'] = array(
		'label' => '',
		'title' => 'Edit',
		'icon' => 'edit',
		'url' => 'element/edit_element/edit_user_min/~current_element~',
	);

	$operations['remove_user'] = array(
		'operation_type' => 'Remove',
		'operation_title' => 'Remove a user',
		'operation_description' => 'Delete a user from the displayed list',
		//'page_title'=>'Remove user '.active_user_name(),

		//'page_template'=>'detail',
		'redirect_after_delete' => 'element/entity_list/list_all_users',
		'db_delete_model' => 'remove_users',
		'generate_stored_procedure' => True,


	);


	$config['operations'] = $operations;
	return $config;

}