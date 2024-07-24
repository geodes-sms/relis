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
	The function creates a configuration array with various settings for managing user_creation. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with user_creation.
		- table_id: The primary key field for the user_creation table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing user_creation.
		- reference_title_min: A shorter version of the reference title.
*/
function get_user_creation()
{
	$config['config_id'] = 'user_creation';
	$config['table_name'] = 'user_creation';
	$config['table_id'] = 'user_creation_id';
	$config['table_active_field'] = 'user_creation_active';
	$config['reference_title'] = 'Creation';
	$config['reference_title_min'] = 'Creation';

	/*
		- order_by: The sorting order for the user-creations in the list view.
		- The configuration includes a fields array, which defines the fields of the user-creation table.
	*/
	$config['order_by'] = 'user_creation_id ASC ';

	$fields['user_creation_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'

	);

	$fields['creation_user_id'] = array(
		'field_title' => 'User',
		'field_type' => 'number',
		'field_size' => 11,
		'default_value' => 1,

		//'field_value'=>active_user_id(),

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		'mandatory' => ' mandatory ',
	);
	$fields['confirmation_code'] = array(
		'field_title' => 'Confirmationcode',
		'field_type' => 'text',
		'field_size' => 50,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);

	$fields['confirmation_expiration'] = array(
		'field_title' => 'Confirmation code',
		'field_type' => 'int',
		'field_size' => 10,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	$fields['confirmation_try'] = array(
		'field_title' => 'Number of validation attempt',
		'field_type' => 'int',
		'field_size' => 10,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);

	$fields['user_creation_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);
	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on user-creations. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['add_user_creation'] = array(
		'operation_type' => 'Add',
		'page_title' => 'Add log',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'home',
		'db_save_model' => 'add_user_creation',

		'generate_stored_procedure' => True,

		'fields' => array(
			'user_creation_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'creation_user_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'confirmation_code' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'confirmation_try' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'confirmation_expiration' => array('mandatory' => 'mandatory', 'field_state' => 'enabled')
		),

		'top_links' => array(

			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);

	$operations['detail_creation'] = array(
		'operation_type' => 'Detail',
		'page_title' => 'User creation ',


		'data_source' => 'get_detail_user_creation',
		'generate_stored_procedure' => True,

		'fields' => array(
			'creation_user_id' => array(),
			'confirmation_code' => array(),
			'confirmation_expiration' => array(),
			'confirmation_try' => array()

		),


		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),



		),
	);

	$config['operations'] = $operations;

	return $config;

}