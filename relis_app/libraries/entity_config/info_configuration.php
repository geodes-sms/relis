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
	This function returns a configuration array for managing infos in a system. 
	The function creates a configuration array with various settings for managing infos in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with infos.
		- table_id: The primary key field for the info table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the infos in the list view.
		- search_by: The fields to be used for searching infos
		- The configuration includes a fields array, which defines the fields of the table.
*/
function get_info()
{

	$table = "info";
	$title = "Home page information";
	$config_id = "info";
	$value_label = "Value";
	$description_label = "Description";


	$config['config_id'] = $config_id;
	$config['table_name'] = $table;
	$config['table_id'] = 'info_id';
	$config['table_active_field'] = 'info_active';
	$config['main_field'] = 'info_title';

	$config['entity_label'] = $title;
	$config['entity_label_plural'] = $title;

	//list view
	$config['order_by'] = ' info_title ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'info_title'; // separer les champs par virgule



	$fields['info_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'
	);


	$fields['info_title'] = array(
		'field_title' => "Title",
		'field_type' => 'text',
		'field_size' => 500,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	 
	$fields['info_desc']=array(
			'field_title'=>"Content",
			'field_type'=>'text', 
			'field_size'=>2000,  
			'input_type'=>'textarea',
	);
	$fields['info_link']=array(
			'field_title'=>"Links",
			'field_type'=>'text', 
			'field_size'=>500,  
			'input_type'=>'text',
			'mandatory'=>''
	);

	$fields['info_type'] = array(
		'field_title' => 'Type',
		'field_type' => 'text',
		'field_size' => 20,
		'default_value' => 'Help',
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Home' => 'Home',
			'Home' => 'Home',
			'Features' => 'Features',
			'Help' => 'Help',
			'Reference' => 'Reference'
		)
	);

	$fields['info_order'] = array(
		'field_title' => "Order",
		'field_type' => 'int',
		'field_size' => 2,
		'default_value' => '1',
		'input_type' => 'text',
		'mandatory' => 'mandatory'
	);

	$fields['info_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);
	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on infos. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['list_info'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of ' . $title,
		'operation_description' => 'List of ' . $title,
		'page_title' => 'List  of ' . $title,

		//'page_template'=>'list',

		'data_source' => 'get_list_info',
		'generate_stored_procedure' => True,

		'fields' => array(

			'info_title' => array(
				'link' => array(
					'url' => 'element/display_element/detail_info/',
					'id_field' => 'info_id',
					'trim' => '0'
				)
			),
			//'info_desc'=>array(),		   	
			'info_type' => array(),
			'info_order' => array(),

		),
		'order_by' => 'info_type ASC ',
		'search_by' => 'info_title',


		'list_links' => array(

			'edit' => array(
				'label' => 'Edit',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_info/',
			),
			'delete' => array(
				'label' => 'Delete',
				'title' => 'Delete ',
				'icon' => 'trash',
				'url' => 'element/delete_element/remove_info/'
			)
		),

		'top_links' => array(
			'add' => array(
				'label' => '',
				'title' => 'Add',
				'icon' => 'add',
				'url' => 'element/add_element/add_info',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => '',
				'url' => 'home',
			)

		),


	);


	$operations['add_info'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add ' . $title,
		'operation_description' => 'Add ' . $title,
		'page_title' => 'Add',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_info',
		'db_save_model' => 'add_info',

		'generate_stored_procedure' => True,

		'fields' => array(
			'info_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'info_title' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'info_desc' => array('mandatory' => '', 'field_state' => 'enabled'),
			'info_link' => array('mandatory' => '', 'field_state' => 'enabled'),
			'info_type' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'info_order' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),


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

	$operations['edit_info'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit ' . $title,
		'operation_description' => 'Edit ' . $title,
		'page_title' => 'Edit ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/entity_list/list_info',
		'data_source' => 'get_detail_info',
		'db_save_model' => 'update_info',

		'generate_stored_procedure' => True,

		'fields' => array(
			'info_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'info_title' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'info_desc' => array('mandatory' => '', 'field_state' => 'enabled'),
			'info_link' => array('mandatory' => '', 'field_state' => 'enabled'),
			'info_type' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'info_order' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),

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

	$operations['detail_info'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Detail of a ' . $title,
		'operation_description' => 'Detail ',
		'page_title' => $title,

		//'page_template'=>'element/display_element',

		'data_source' => 'get_detail_info',
		'generate_stored_procedure' => True,

		'fields' => array(
			//	'user_id'=>array(),
			'info_title' => array(),
			'info_desc' => array(),
			'info_link' => array(),
			'info_type' => array(),
			'info_order' => array(),

		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_info/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),



		),
	);

	$operations['remove_info'] = array(
		'operation_type' => 'Remove',
		'operation_title' => 'Remove ' . $title,
		'operation_description' => 'Remove ' . $title,

		'redirect_after_delete' => 'element/entity_list/list_info',
		'db_delete_model' => 'remove_info',
		'generate_stored_procedure' => True,


	);
	$config['operations'] = $operations;

	return $config;

}