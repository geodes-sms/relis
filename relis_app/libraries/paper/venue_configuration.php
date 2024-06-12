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
	The function creates a configuration array with various settings for managing venues in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with venues.
		- table_id: The primary key field for the venue table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the venues in the list view.
		- search_by: The fields to be used for searching venues, separated by commas
		- The configuration includes a fields array, which defines the fields of the venue table.
		- etc.
*/
function get_venue()
{
	$config['config_id'] = 'venue';
	$config['table_name'] = 'venue';
	$config['table_id'] = 'venue_id';
	$config['table_active_field'] = 'venue_active'; //to detect deleted records
	$config['main_field'] = 'venue_abbreviation';

	$config['entity_label_plural'] = 'Venues';
	$config['entity_label'] = 'Venue';

	//list view
	$config['order_by'] = 'venue_abbreviation ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'venue_abbreviation,venue_fullName'; // separer les champs par virgule


	$fields['venue_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'
	);




	$fields['venue_fullName'] = array(
		'field_title' => 'Full name',
		'field_type' => 'text',
		'field_size' => 200,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);

	$fields['venue_year'] = array(
		'field_title' => 'Year',
		'field_type' => 'int',
		'field_size' => 4,
		'input_type' => 'text'
	);
	$fields['venue_abbreviation'] = array(
		'field_title' => 'Abreviation',
		'field_type' => 'text',
		'field_size' => 20,
		'input_type' => 'text',
		//'mandatory'=>' mandatory '
	);
	$fields['venue_volume'] = array(
		'field_title' => 'Volume',
		'field_type' => 'int',
		'field_size' => 11,
		'input_type' => 'text'
	);

	$fields['venue_totalNumPapers'] = array(
		'field_title' => 'Papers number',
		'field_type' => 'int',
		'field_size' => 11,
		'input_type' => 'text'
	);


	$fields['venue_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);
	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on venues. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['add_venue'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add a new venue',
		'operation_description' => 'Add a new venue',
		'page_title' => 'Add a new venue',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_venues',
		'db_save_model' => 'add_venue',

		'generate_stored_procedure' => True,

		'fields' => array(
			'venue_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			//'venue_abbreviation'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
			'venue_fullName' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'venue_year' => array('mandatory' => '', 'field_state' => 'enabled'),
			//	'venue_volume'=>array('mandatory'=>'','field_state'=>'enabled'),
			//	'venue_totalNumPapers'=>array('mandatory'=>'','field_state'=>'enabled'),

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



	$operations['edit_venue'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit venue',
		'operation_description' => 'Edit venue',
		'page_title' => 'Edit venue ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/entity_list/list_venues',
		'data_source' => 'get_detail_venue',
		'db_save_model' => 'update_venue',

		'generate_stored_procedure' => True,

		'fields' => array(
			'venue_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			//'venue_abbreviation'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
			'venue_fullName' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'venue_year' => array('mandatory' => '', 'field_state' => 'enabled'),
			//'venue_volume'=>array('mandatory'=>'','field_state'=>'enabled'),
			//'venue_totalNumPapers'=>array('mandatory'=>'','field_state'=>'enabled'),

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

	$operations['list_venues'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List venues',
		'operation_description' => 'List venues',
		'page_title' => 'Venues',

		//'page_template'=>'list',

		'data_source' => 'get_list_venues',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'venue_id'=>array(),
			//'venue_abbreviation'=>array(),
			'venue_fullName' => array(),
			'venue_year' => array(),
			//'venue_volume'=>array(),
			//'venue_totalNumPapers'=>array()

		),
		'order_by' => 'venue_abbreviation ASC ',
		'search_by' => 'venue_abbreviation,venue_fullName',

		'list_links' => array(
			'view' => array(
				'label' => 'View',
				'title' => 'Disaly element',
				'icon' => 'folder',
				'url' => 'element/display_element/detail_venue/',
			),
			/*	'edit'=>array(
									   'label'=>'Edit',
									   'title'=>'Edit',
									   'icon'=>'edit',
									   'url'=>'element/edit_element/edit_venue/',
							   ),*/
			'delete' => array(
				'label' => 'Delete',
				'title' => 'Delete the user',
				'url' => 'element/delete_element/remove_venue/'
			)

		),

		'top_links' => array(
			'add' => array(
				'label' => '',
				'title' => 'Add a new venue',
				'icon' => 'add',
				'url' => 'element/add_element/add_venue',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);

	$operations['detail_venue'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Characteristics of a venue',
		'operation_description' => 'Characteristics of an venue',
		'page_title' => 'Venue ',

		'data_source' => 'get_detail_venue',
		'generate_stored_procedure' => True,

		'fields' => array(

			//'venue_abbreviation'=>array(),
			'venue_fullName' => array(),
			'venue_year' => array(),
			//	'venue_volume'=>array(),
			//	'venue_totalNumPapers'=>array()

		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_venue/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),



		),
	);


	$operations['remove_venue'] = array(
		'operation_type' => 'Remove',
		'operation_title' => 'Remove an venue',
		'operation_description' => 'Remove an venue from the displayed list',
		'redirect_after_delete' => 'element/entity_list/list_venues',
		'db_delete_model' => 'remove_venue',
		'generate_stored_procedure' => True,


	);

	$config['operations'] = $operations;
	return $config;

}