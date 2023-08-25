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
	The function creates a configuration array with various settings for managing user_projects. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with user_projects.
		- table_id: The primary key field for the userproject table.
		- table_active_field: The field used to determine whether a record is active or deleted.
*/
function get_user_project()
{

	$config['config_id'] = 'user_project';
	$config['table_name'] = 'userproject';
	$config['table_id'] = 'userproject_id';
	$config['table_active_field'] = 'userproject_active';
	$config['main_field'] = 'project_id';
	$config['entity_label'] = 'User project';
	$config['entity_label_plural'] = 'User project';

	/*
		- order_by: The sorting order for the user_projects in the list view.
		- The configuration includes a fields array, which defines the fields of the user_projects table.
	*/
	$config['order_by'] = 'userproject_id ASC ';

	$fields['userproject_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment',
	);

	$fields['user_id'] = array(
		'field_title' => 'User',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down',
		'drill_down_type' => 'not_linked',
		'input_select_values' => 'users;user_name', //the reference table and the field to be displayed
	);

	$fields['project_id'] = array(
		'field_title' => 'Project',
		'field_type' => 'int',
		//	'field_value'=>'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down',
		'drill_down_type' => 'not_linked',
		'input_select_values' => 'project;project_title', //the reference table and the field to be displayed

	);
	$fields['user_role'] = array(
		'field_title' => 'Role',
		'field_type' => 'text',
		'field_size' => 20,
		//'field_value'=>'normal'
		'default_value' => 'Reviewer',
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Reviewer' => 'Reviewer',
			'Validator' => 'Validator',
			'Project admin' => 'Project manager',
			'Guest' => 'Guest'
		),



	);
	$fields['added_by'] = array(
		'field_title' => 'Added by',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => active_user_id(),

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		'mandatory' => ' mandatory ',

	);

	$fields['add_time'] = array(
		'field_title' => 'Add time',
		'field_type' => 'time',
		// This type cannot be added in the list of displayed  
		'default_value' => 'CURRENT_TIMESTAMP',
		'field_value' => bm_current_time('Y-m-d H:i:s'),

		'field_size' => 20,
		'mandatory' => ' mandatory ',
	);


	$fields['userproject_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1',
	);


	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on user_projects. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['add_userproject'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add a project to a user',
		'operation_description' => 'Add a project to a user',
		'page_title' => 'Add a project to the user',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_user_projects',
		'db_save_model' => 'add_users_project',

		'generate_stored_procedure' => True,

		'fields' => array(
			'userproject_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_id' => array('mandatory' => '', 'field_state' => 'enabled'),
			'project_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_role' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'added_by' => array('mandatory' => '', 'field_state' => 'hidden')

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

	$operations['project_to_user'] = array(
		'operation_type' => 'AddChild',
		'operation_title' => 'Add a project to a user',
		'operation_description' => 'Add a project to a user',
		'page_title' => 'Add a project to the user : ~current_parent_name~',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/display_element/detail_user/~current_element~',
		'db_save_model' => 'add_users_project',

		'master_field' => 'user_id',
		'parent_config' => 'users',
		'parent_detail_source' => 'get_user_detail', //To get the name of the user to be displayed in the title
		'parent_detail_source_field' => 'user_name',

		'generate_stored_procedure' => False,

		'check_exist' => array(
			'fields' => array('project_id', 'user_id'),
			'message' => 'The user have already been added to the project',
		),

		'fields' => array(
			'userproject_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'project_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_role' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'added_by' => array('mandatory' => '', 'field_state' => 'hidden')

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

	$operations['edit_project_to_user'] = array(
		'operation_type' => 'EditChild',
		'operation_title' => 'Edit project for user',
		'operation_description' => 'Edit project for user',
		'page_title' => 'Edit project for user',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/display_element/detail_user/~current_element~',
		'db_save_model' => 'update_user_project_2',
		'data_source' => 'get_userproject_detail',
		'master_field' => 'user_id',
		'parent_config' => 'users',

		'generate_stored_procedure' => True,

		'fields' => array(
			'userproject_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_id' => array('mandatory' => '', 'field_state' => 'disabled'),
			'project_id' => array('mandatory' => 'mandatory', 'field_state' => 'disabled'),
			'user_role' => array('mandatory' => 'mandatory', 'field_state' => 'enabled')


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


	$operations['edit_userproject'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit project for a user',
		'operation_description' => 'Edit project for a user',
		'page_title' => 'Edit project for a user ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/entity_list/list_user_projects',
		'data_source' => 'get_userproject_detail',
		'db_save_model' => 'update_user_project',

		'generate_stored_procedure' => True,

		'fields' => array(
			'userproject_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_id' => array('mandatory' => '', 'field_state' => 'disabled'),
			'project_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_role' => array('mandatory' => 'mandatory', 'field_state' => 'enabled')

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



	$operations['list_userprojects'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of user projects',
		'operation_description' => 'List user projects',
		'page_title' => 'List of projects and users working on',

		//'page_template'=>'list',

		'data_source' => 'get_list_user_project',
		'generate_stored_procedure' => True,

		'fields' => array(
			'userproject_id' => array(),
			'user_id' => array(),
			'project_id' => array(),
			'user_role' => array(),
			'added_by' => array(),
			'add_time' => array(),


		),
		'order_by' => 'user_id ASC ',


		'list_links' => array(
			'view' => array(
				'label' => 'View',
				'title' => 'Disaly element',
				'icon' => 'folder',
				'url' => 'element/display_element/detail_userproject/',
			),
			'edit' => array(
				'label' => 'Edit',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_userproject/',
			),
			'delete' => array(
				'label' => 'Delete',
				'title' => 'Delete the user',
				'url' => 'element/delete_element/remove_userproject/'
			)

		),

		'top_links' => array(
			'add' => array(
				'label' => '',
				'title' => 'Add a new user project',
				'icon' => 'add',
				'url' => 'element/add_element/add_userproject',
			),
			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);
	$operations['add_user_current_project'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add a project to a user',
		'operation_description' => 'Add a project to a user',
		'page_title' => 'Add a user to current project',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_users_current_projects',
		'db_save_model' => 'add_users_project',

		'generate_stored_procedure' => False,

		'fields' => array(
			'userproject_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'project_id' => array('mandatory' => 'mandatory', 'field_state' => 'hidden', 'field_value' => active_project_id()),
			'user_role' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'added_by' => array('mandatory' => '', 'field_state' => 'hidden')

		),

		'check_exist' => array(

			'fields' => array('project_id', 'user_id'),
			'message' => 'The user have already been added to the project',
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

	$operations['edit_user_current_project'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit project for a user',
		'operation_description' => 'Edit project for a user',
		'page_title' => 'Edit project for a user ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/entity_list/list_users_current_projects',
		'data_source' => 'get_userproject_detail',
		'db_save_model' => 'update_user_project',

		'generate_stored_procedure' => False,

		'fields' => array(
			'userproject_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'user_id' => array('mandatory' => '', 'field_state' => 'disabled'),
			'project_id' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'user_role' => array('mandatory' => 'mandatory', 'field_state' => 'enabled')

		),
		/*'check_exist'=>array(
							
							'fields'=>array('project_id','user_id'),
							'message'=>'The user have already been added to the project',
					),*/
		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);
	$operations['list_users_current_projects'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of user projects',
		'operation_description' => 'List user projects',
		'page_title' => 'Users in this project',

		//'page_template'=>'list',

		'data_source' => 'get_list_user_current_project',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'userproject_id'=>array(),
			'user_id' => array(),
			//'project_id'=>array(),
			'user_role' => array(),
			'added_by' => array(),
			'add_time' => array(),


		),
		'order_by' => 'user_id ASC ',
		'conditions' => array(
			'user_project' => array(
				'field' => 'project_id',
				'value' => active_project_id(),
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(4)'
			)
		),

		'list_links' => array(

			'edit' => array(
				'label' => 'Edit',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_user_current_project/',
			),
			'delete' => array(
				'label' => 'Remove',
				'title' => 'Remove the user from the project',
				'url' => 'element/delete_element/remove_user_current_project/'
			)

		),

		'top_links' => array(
			'add' => array(
				'label' => '',
				'title' => 'Add a new user to the project',
				'icon' => 'add',
				'url' => 'element/add_element/add_user_current_project',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);

	$operations['detail_userproject'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Characteristics of a userproject',
		'operation_description' => 'Characteristics of a userproject',
		'page_title' => 'Users project',

		//'page_template'=>'element/display_element',

		'data_source' => 'get_userproject_detail',
		'generate_stored_procedure' => True,

		'fields' => array(
			//		'userproject_id'=>array(),
			'user_id' => array(),
			'project_id' => array(),
			'user_role' => array(),
			'added_by' => array(),
			'add_time' => array(),

		),
		'top_links' => array(
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)
		),

	);
	$operations['remove_userproject'] = array(
		'operation_type' => 'Remove',
		'operation_title' => 'Remove a user project',
		'operation_description' => 'Delete a user from the displayed list',
		//'page_title'=>'Remove user '.active_user_name(),

		//'page_template'=>'detail',
		'redirect_after_delete' => 'element/entity_list/list_userprojects',
		'db_delete_model' => 'remove_user_project',
		'generate_stored_procedure' => True,


	);
	$operations['remove_userproject_c'] = $operations['remove_userproject'];
	$operations['remove_userproject_c']['redirect_after_delete'] = 'element/display_element/detail_user/~current_element~';
	$operations['remove_userproject_c']['generate_stored_procedure'] = False;



	$operations['remove_userproject_p'] = $operations['remove_userproject_c'];
	$operations['remove_userproject_p']['redirect_after_delete'] = 'element/display_element/detail_project/~current_element~';

	$operations['remove_user_current_project'] = $operations['remove_userproject_c'];
	$operations['remove_user_current_project']['redirect_after_delete'] = 'element/entity_list/list_users_current_projects';

	$config['operations'] = $operations;

	return $config;
}