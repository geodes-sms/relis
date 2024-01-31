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

	$config['config_id'] = 'str_mng';
	$config['table_name'] = 'str_management';
	$config['table_id'] = 'str_id';
	$config['table_active_field'] = 'str_active'; //to detect deleted records
	$config['reference_title'] = 'String management';
	$config['reference_title_min'] = 'String management';

	/*
		- order_by: The sorting order for the string management in the list view.
		- search_by: The fields to be used for searching string management
		- The configuration includes a fields array, which defines the fields of the string-management table.
	*/
	$config['order_by'] = 'str_text ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'str_text'; // separer les champs par virgule

	//  	$config['links']['add_child']="users/user_usergroup;Add user";


	/*$config['links']['edit']=array(
				  'label'=>'Edit',
				  'title'=>'Edit string',
				  'on_list'=>True,
				  'on_view'=>True
		  );
		  
		  $config['links']['view']=array(
				  'label'=>'View',
				  'title'=>'View',
				  'on_list'=>True,
				  'on_view'=>True
		  );
		  
		  $config['links']['delete']=array(
				  'label'=>'Delete',
				  'title'=>'Delete',
				  'on_list'=>True,
				  'on_view'=>True
		  );*/
	$fields['str_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'
	);


	$fields['str_label'] = array(
		'field_title' => 'Label',
		'field_type' => 'text',
		'field_size' => 400,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	$fields['str_text'] = array(
		'field_title' => 'Text',
		'field_type' => 'text',
		'field_size' => 800,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	$fields['str_lang'] = array(
		'field_title' => 'Language',
		'field_value' => 'en',
		'default_value' => 'en',
		'field_type' => 'text',
		'field_size' => 3,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);

	$fields['str_category'] = array(
		'field_title' => 'Category',
		'field_value' => 'default',
		'default_value' => 'default',
		'field_type' => 'text',
		'field_size' => 18,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);


	$fields['str_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);
	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on string managements. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['add_str_mng'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add string',
		'operation_description' => 'Add string',
		'page_title' => 'Add string',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_str_mng',
		'db_save_model' => 'add_str_mng',

		'generate_stored_procedure' => True,

		'fields' => array(
			'str_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'str_label' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'str_text' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'str_lang' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'str_category' => array('mandatory' => '', 'field_state' => 'hidden')

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

	$operations['edit_str_mng'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit string',
		'operation_description' => 'Edit string',
		'page_title' => 'Edit string',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/entity_list/list_str_mng',
		'data_source' => 'get_detail_str_mng',
		'db_save_model' => 'update_str_mng',

		'generate_stored_procedure' => True,


		'fields' => array(
			'str_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'str_label' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'str_text' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'str_lang' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			//'str_category'=>array('mandatory'=>'','field_state'=>'hidden')

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

	$operations['detail_str_mng'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Detail of a string',
		'operation_description' => 'Detail of a string',
		'page_title' => 'Log ',


		'data_source' => 'get_detail_str_mng',
		'generate_stored_procedure' => True,

		'fields' => array(
			'str_label' => array(),
			'str_text' => array(),
			'str_lang' => array(),
			//'str_category'=>array()

		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit element',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_str_mng/~current_element~',
			),
			'delete' => array(
				'label' => '',
				'title' => 'Delete the user',
				'url' => 'element/delete_element/remove_str_mng/~current_element~'
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),



		),
	);


	//configure edition mode button

	if (edition_mode_active()) {
		$edit_mode = array(
			'label' => 'Close edition mode',
			'title' => 'Close edition mode',
			'icon' => 'fa-ban',
			'url' => 'config/update_edition_mode/no',
		);
	} else {
		$edit_mode = array(
			'label' => 'Open edition mode',
			'title' => 'Open edition mode',
			'icon' => 'fa-check',
			'url' => 'config/update_edition_mode/yes',
		);
	}

	$operations['list_str_mng'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List ',
		'operation_description' => 'List ',
		'page_title' => 'Label Mangement ',

		'table_display_style' => 'normal',

		'data_source' => 'get_list_str_mng',
		'generate_stored_procedure' => True,

		'fields' => array(
			'str_id' => array(),
			'str_label' => array(
				'link' => array(
					'url' => 'element/display_element/detail_str_mng/',
					'id_field' => 'str_id',
					'trim' => '0'
				)
			),
			'str_text' => array(),
			'str_lang' => array(),

		),
		'order_by' => 'str_label DESC ',
		'search_by' => 'str_label,str_text',

		'list_links' => array(
			/*'view'=>array(
										  'label'=>'View',
										  'title'=>'Disaly element',
										  'icon'=>'folder',
										  'url'=>'element/display_element/detail_str_mng/',
									  ),
							  'edit'=>array(
										  'label'=>'Edit',
										  'title'=>'Edit element',
										  'icon'=>'edit',
										  'url'=>'element/edit_element/edit_str_mng/',
									  ),
							  'delete'=>array(
										  'label'=>'Delete',
										  'title'=>'Delete the user',
										  'url'=>'element/delete_element/remove_str_mng/'
									  )
							  */
		),
		'conditions' => array(
			'active_lang' => array(
				'field' => 'str_lang',
				'value' => active_language(),
				'evaluation' => '',
				'add_on_generation' => False,
				'parameter_type' => 'VARCHAR(3)'
			)

		),



		'top_links' => array(
			'add_sc' => $edit_mode,
			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);

	$operations['remove_str_mng'] = array(
		'operation_type' => 'Remove',
		'operation_title' => 'Remove element',
		'operation_description' => 'Delete a string',

		'redirect_after_delete' => 'element/entity_list/list_str_mng',
		'db_delete_model' => 'remove_str_mng',
		'generate_stored_procedure' => True,


	);

	$config['operations'] = $operations;

	return $config;

}