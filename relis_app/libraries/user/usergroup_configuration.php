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
	The function creates a configuration array with various settings for managing user_groups. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with user_groups.
		- table_id: The primary key field for the usergroup table.
		- table_active_field: The field used to determine whether a record is active or deleted.
*/
function get_config_usergroup()
{

	$config['config_id'] = 'usergroup';
	$config['table_name'] = table_name('usergroup');
	$config['table_id'] = 'usergroup_id';
	$config['table_active_field'] = 'usergroup_active'; //to detect deleted records
	$config['main_field'] = 'usergroup_name';


	$config['entity_label'] = 'Usergroup';
	$config['entity_label_plural'] = 'Usergroups';

	$config['reference_title'] = 'Usergroups';
	$config['reference_title_min'] = 'Usergroup';



	/*
		The configuration also includes settings for the list view:
			- order_by: The sorting order for the user_groups in the list view.
			- search_by: The field to be used for searching user_groups
			- The configuration includes a fields array, which defines the fields of the usergroup table.
	*/
	$config['order_by'] = 'usergroup_name ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'usergroup_name'; // separer les champs par virgule



	$fields['usergroup_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'field_value' => 'auto_increment',


		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show'
	);


	$fields['usergroup_name'] = array(
		'field_title' => 'Name',
		'field_type' => 'text',
		'field_size' => 50,

		'input_type' => 'text',
		'mandatory' => ' mandatory ',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',

	);


	$fields['usergroup_description'] = array(
		'field_title' => 'Description',
		'field_type' => 'text',
		'field_size' => 50,
		'input_type' => 'text',
		'mandatory' => '  ',


		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
	);



	$fields['usergroup_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'default_value' => '1',
		'field_value' => '1',
		'default_value' => '1',



		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on user_groups. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['list_usergroups'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of usergroups',
		'operation_description' => 'List usergroups',
		'page_title' => 'User Groups',

		//'page_template'=>'list',

		'data_source' => 'get_list_usergroup',
		'generate_stored_procedure' => True,

		'fields' => array(
			'usergroup_id' => array(),
			'usergroup_name' => array(),
			'usergroup_description' => array()


		),
		'order_by' => 'usergroup_name ASC ',
		'search_by' => 'usergroup_name',


		'list_links' => array(


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
	$config['operations'] = $operations;
	return $config;

}