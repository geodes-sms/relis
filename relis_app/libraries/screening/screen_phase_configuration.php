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
	The function creates a configuration array with various settings for managing screen_phases in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with screen_phases.
		- table_id: The primary key field for the screen_phase table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the screen_phase in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
		- etc.
*/
function get_config_screen_phase()
{
	$config['config_id'] = 'screen_phase';
	$config['table_name'] = table_name('screen_phase');
	$config['table_id'] = 'screen_phase_id';
	$config['table_active_field'] = 'screen_phase_active'; //to detect deleted records
	$config['main_field'] = 'phase_title';

	$config['entity_label'] = 'Screening phase';
	$config['entity_label_plural'] = 'Screening phases';

	//list view
	$config['order_by'] = 'screen_phase_id ASC '; //mettre la valeur à mettre dans la requette

	$fields['screen_phase_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'

	);

	$fields['phase_title'] = array(
		'field_title' => 'Title',
		'field_type' => 'text',
		'field_size' => 100,
		'input_type' => 'text',
		'mandatory' => ' mandatory '

	);
	$fields['description'] = array(
		'field_title' => 'Description',
		'field_type' => 'text',
		'field_size' => 1000,
		'input_type' => 'textarea',

	);

	$fields['displayed_fields'] = array(
		'field_title' => 'Displayed fields',
		'field_type' => 'text',
		'field_value' => 'paper_title,paper_abstract',
		'field_size' => 200,
		'input_type' => 'text',
	);

	$fields['displayed_fields_vals'] = array(
		'field_title' => 'Displayed fields',
		'field_type' => 'text',
		'field_value' => 'paper_title,paper_abstract',
		'field_size' => 200,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Title' => 'Title',
			'Abstract' => 'Abstract',
			'Preview' => 'Preview',
			'Bibtex' => 'Bibtex',
			'Link' => 'Link',
		),
		'number_of_values' => '*',
		'category_type' => 'WithMultiValues',
		'multi-select' => 'Yes',
		'not_in_db' => True,
	);

	$fields['phase_state'] = array(
		'field_title' => 'State',
		'field_type' => 'text',
		'field_size' => 20,
		'field_value' => 'Closed',
		'default_value' => 'Closed',
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Pending' => 'Pending',
			'Open' => 'Open',
			'Closed' => 'Closed',
			'Cancelled' => 'Cancelled',
		),

	);

	$fields['source_paper_id'] = array(
		'field_title' => 'Source papers choosen',
		'field_type' => 'text',
		'field_size' => 11,
		'input_type' => 'text'

	);
	$fields['source_paper'] = array(
		'field_title' => 'Source paper',
		'field_type' => 'text',
		'field_size' => 15,
		'field_value' => 'Previous phase',
		'default_value' => 'Previous phase',
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'All papers' => 'All papers',
			'Previous phase' => 'Previous phase'
		),

	);
	$fields['source_paper_status'] = array(
		'field_title' => 'Source paper status',
		'field_type' => 'text',
		'field_size' => 15,
		'field_value' => 'Included',
		'default_value' => 'Included',
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'All' => 'All',
			'Included' => 'Included',
			'Excluded' => 'Excluded'
		),

	);

	$fields['screen_phase_order'] = array(
		'field_title' => 'Order',
		'field_type' => 'number',
		'field_size' => 2,
		'input_type' => 'text',
		'mandatory' => ' mandatory '

	);

	$fields['screen_phase_final'] = array(
		'field_title' => 'Final phase',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '0',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',

	);
	$fields['phase_type'] = array(
		'field_title' => 'Phase category',
		'field_type' => 'text',
		'field_size' => 15,
		'field_value' => 'Screening',
		'default_value' => 'Screening',
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Screening' => 'Screening',
			'Validation' => 'Validation',
		),

	);
	$fields['phase_history'] = array(
		'field_title' => 'Phase history',
		//in json
		'field_type' => 'longtext',
		'field_size' => 2000,
		'input_type' => 'textarea'
	);

	$fields['added_by'] = array(
		'field_title' => 'Created by',
		'field_type' => 'number',
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
		'default_value' => 'CURRENT_TIMESTAMP',
		'field_value' => bm_current_time('Y-m-d H:i:s'),

		'field_size' => 20,
		'mandatory' => ' mandatory ',
	);
	$fields['screen_phase_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'


	);

	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on the screen_phase. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['add_screen_phase'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add a new screening phase',
		'operation_description' => 'Add a new screening phase',
		'page_title' => 'Add a new screening phase',
		'save_function' => 'element/save_element',
		'save_function' => 'screening/save_phase_screen',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_screen_phases',
		'db_save_model' => 'add_screen_phase',

		'generate_stored_procedure' => False,

		'fields' => array(
			'screen_phase_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'added_by' => array('mandatory' => '', 'field_state' => 'hidden'),
			'displayed_fields' => array('mandatory' => '', 'field_state' => 'hidden'),
			'screen_phase_order' => array('mandatory' => '', 'field_state' => 'hidden'),
			'phase_type' => array('mandatory' => '', 'field_state' => 'hidden', 'field_value' => 'Screening'),
			'source_paper' => array('mandatory' => 'mandatory', 'field_state' => 'hidden', 'field_value' => 'Previous phase'),
			'source_paper_status' => array('mandatory' => 'mandatory', 'field_state' => 'hidden', 'field_value' => 'Included'),

			'phase_title' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'description' => array('mandatory' => '', 'field_state' => 'enabled'),
			'displayed_fields_vals' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screen_phase_final' => array('mandatory' => '', 'field_state' => 'enabled')

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


	$operations['add_validation_phase'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add a new validation phase',
		'operation_description' => 'Add a new validation phase',
		'page_title' => 'Add a new validation phase',
		'save_function' => 'element/save_element',
		'save_function' => 'screening/save_phase_screen',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_screen_phases',
		'db_save_model' => 'add_screen_phase',

		'generate_stored_procedure' => False,

		'fields' => array(
			'screen_phase_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'added_by' => array('mandatory' => '', 'field_state' => 'hidden'),
			'displayed_fields' => array('mandatory' => '', 'field_state' => 'hidden'),
			'screen_phase_final' => array('mandatory' => '', 'field_state' => 'hidden'),
			'phase_type' => array('mandatory' => '', 'field_state' => 'hidden', 'field_value' => 'Validation'),

			'source_paper_status' => array('mandatory' => 'mandatory', 'field_state' => 'hidden', 'field_value' => 'Excluded'),

			'phase_title' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'description' => array('mandatory' => '', 'field_state' => 'enabled'),
			'source_paper' => array('mandatory' => 'mandatory', 'field_state' => 'enabled', 'field_value' => 'Previous phase'),
			'displayed_fields_vals' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screen_phase_order' => array('mandatory' => '', 'field_state' => 'enabled'),
			//'screen_phase_final'=>array('mandatory'=>'','field_state'=>'enabled')

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



	$operations['edit_screen_phase'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit a screening phase',
		'operation_description' => 'Edit a screening phase',
		'page_title' => 'Edit  screening phase ',
		'save_function' => 'element/save_element',
		'save_function' => 'screening/save_phase_screen',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/entity_list/list_screen_phases',
		'data_source' => 'get_screen_phase_detail',
		'db_save_model' => 'update_screen_phase',

		'generate_stored_procedure' => False,

		'fields' => array(
			'screen_phase_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'added_by' => array('mandatory' => '', 'field_state' => 'hidden'),
			'displayed_fields' => array('mandatory' => '', 'field_state' => 'hidden'),
			'phase_type' => array('mandatory' => '', 'field_state' => 'hidden'),
			'source_paper' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'source_paper_status' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),

			'phase_title' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'description' => array('mandatory' => '', 'field_state' => 'enabled'),
			'displayed_fields_vals' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screen_phase_order' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'screen_phase_final' => array('mandatory' => '', 'field_state' => 'enabled')

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

	$operations['list_screen_phases'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List phases',
		'operation_description' => 'List phases',
		'page_title' => 'List phases',

		//'page_template'=>'list',

		'data_source' => 'get_list_screen_phases',
		'generate_stored_procedure' => True,

		'fields' => array(
			'screen_phase_id' => array(),
			'phase_title' => array(
				'link' => array(
					'url' => 'element/display_element/detail_screen_phase/',
					'id_field' => 'screen_phase_id',
					'trim' => '0'
				)
			),

			'displayed_fields' => array(),
			//'source_paper'=>array(),
			//'source_paper_status'=>array(),
			'screen_phase_state' => array(),
			'screen_phase_order' => array(),
			//'phase_type'=>array(),
			'screen_phase_final' => array()

		),
		'order_by' => 'screen_phase_order ASC ',
		'search_by' => 'phase_title',

		'list_links' => array(
			/*'view'=>array(
										  'label'=>'View',
										  'title'=>'Disaly element',
										  'icon'=>'folder',
										  'url'=>'element/display_element/detail_screen_phase/',
									  ),
							  'edit'=>array(
										  'label'=>'Edit',
										  'title'=>'Edit',
										  'icon'=>'edit',
										  'url'=>'element/edit_element/edit_screen_phase/',
									  ),*/
			'delete' => array(
				'label' => 'Cancel',
				'title' => 'Cancel the phase',
				'url' => 'element/delete_element/remove_screen_phase/'
			)

		),

		'top_links' => array(
			'add_sc' => array(
				'label' => 'Add screeninng phase',
				'title' => 'Add a new phase',
				'icon' => 'fa-plus',
				'url' => 'element/add_element/add_screen_phase',
			),
			/*	'add_val'=>array(
											  'label'=>'Add validation phase',
											  'title'=>'Add a new phase',
											  'icon'=>'plus',
											  'url'=>'element/add_element/add_validation_phase',
										  ),*/
			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);

	$operations['detail_screen_phase'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Characteristics of a screening phase',
		'operation_description' => 'Characteristics of a screening phase',
		'page_title' => 'Phase ',

		//'page_template'=>'element/display_element',

		'data_source' => 'get_screen_phase_detail',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'screen_phase_id'=>array(),
			'phase_title' => array(),
			'description' => array(),
			'displayed_fields' => array(),
			//'phase_state'=>array(),
			//'source_paper'=>array(),
			//'source_paper_status'=>array(),
			//'screen_phase_order'=>array(),
			'screen_phase_final' => array(),
			'added_by' => array(),
			'add_time' => array(),

		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_screen_phase/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),
		),
	);


	$operations['remove_screen_phase'] = array(
		'operation_type' => 'Remove',
		'operation_title' => 'Remove a Phase',
		'operation_description' => 'Delete a phase',
		'redirect_after_delete' => 'element/entity_list/list_screen_phases',
		'db_delete_model' => 'remove_screen_phase',
		'generate_stored_procedure' => True,


	);

	$config['operations'] = $operations;
	return $config;
}