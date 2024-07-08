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
	The function creates a configuration array with various settings for managing screening in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with screening.
		- table_id: The primary key field for the screening table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the screenings in the list view.
		- The configuration includes a fields array, which defines the fields of the screening table.
		- etc.
*/
function get_screening()
{
	$config['config_id'] = 'screening';
	$config['table_name'] = 'screening_paper';
	$config['table_id'] = 'screening_id';
	$config['table_active_field'] = 'screening_active'; //to detect deleted records
	$config['main_field'] = 'paper_id';

	$config['entity_label'] = 'Screening';
	$config['entity_label_plural'] = 'Screening';



	$fields['screening_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'
	);

	$fields['paper_id'] = array(
		'field_title' => 'Paper',
		'field_type' => 'int',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'papers;CONCAT_WS(" - ",bibtexKey,title)', //the reference table and the field to be displayed

		'mandatory' => ' mandatory ',


	);

	$fields['screening_phase'] = array( // assigned to
		'field_title' => 'Screening phase',
		'field_type' => 'int',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'screen_phase;phase_title',
		'mandatory' => ' mandatory ',
	);

	$fields['user_id'] = array( // assigned to
		'field_title' => 'Assigned to',
		'field_type' => 'int',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		'mandatory' => ' mandatory ',
	);


	$fields['assignment_note'] = array(
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_size' => 200,
		'input_type' => 'textarea',

	);

	$fields['assignment_type'] = array(
		'field_title' => 'Assignment type',
		'field_type' => 'text',
		'field_value' => 'Normal',
		'default_value' => 'Normal',
		'field_size' => 20,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Normal' => 'Normal',
			'Veto' => 'Veto',
			'Info' => 'Info'
		),
		'mandatory' => ' mandatory ',

	);

	$fields['assignment_mode'] = array(
		'field_title' => 'Assignment mode',
		'field_type' => 'text',
		'field_value' => 'manualy_single',
		'default_value' => 'auto',
		'field_size' => 30,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'auto' => 'Automatic',
			'manualy_bulk' => 'Manually Bulk',
			'manualy_single' => 'Manually'
		),
		'mandatory' => ' mandatory ',
	);


	$fields['assigned_by'] = array(
		'field_title' => 'Assigned by',
		'field_type' => 'number',
		'field_size' => 11,
		'field_value' => active_user_id(),
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		'mandatory' => ' mandatory ',
	);

	$fields['assignment_time'] = array(
		'field_title' => 'Assignment time',
		'field_type' => 'time',
		'default_value' => 'CURRENT_TIMESTAMP',
		'field_value' => bm_current_time('Y-m-d H:i:s'),

		'field_size' => 20,
		'mandatory' => ' mandatory ',
	);

	$fields['operation_code'] = array(
		//used  for bulk assignment  in order to reverse the operation
		'field_title' => 'Operation code',
		'field_type' => 'text',
		'field_value' => '01',
		'default_value' => '01',
		'mandatory' => 'mandatory',
		'field_size' => 15,

	);

	$fields['screening_decision'] = array(
		'field_title' => 'Decision',
		'field_type' => 'text',
		'field_size' => 15,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Included' => 'Included',
			'Excluded' => 'Excluded',
		),
	);

	$fields['exclusion_criteria'] = array(
		'field_title' => 'Exclusion criteria',
		'field_type' => 'int',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'exclusioncrieria;ref_value', //the reference table and the field to be displayed

	);

	$fields['inclusion_criteria'] = array(
		'field_title' => 'Inclusion criteria',
		'field_type' => 'int',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'inclusioncriteria;ref_value', //the reference table and the field to be displayed

	);


	$fields['screening_note'] = array(
		'field_title' => 'Screening note',
		'field_type' => 'text',
		'field_size' => 200,
		'input_type' => 'textarea',

	);



	$fields['screening_time'] = array(
		'field_title' => 'Screening time',
		'field_type' => 'time',
		'field_size' => 20,

	);

	$fields['screening_status'] = array(
		'field_title' => 'Screening status',
		'field_type' => 'text',
		'field_size' => 15,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Pending' => 'Pending',
			'Done' => 'Done',
			'Reseted' => 'Reseted',
		),
		'field_value' => 'Pending',
		'default_value' => 'Pending',
		'mandatory' => 'mandatory',
	);

	$fields['assignment_role'] = array(
		'field_title' => 'Assignment role',
		'field_type' => 'text',
		'field_size' => 15,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Screening' => 'Screening',
			'Validation' => 'Validation',
		),
		'field_value' => 'Screening',
		'default_value' => 'Screening',
		'mandatory' => 'mandatory',
	);

	$fields['screening_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);

	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on screening. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['list_assignments'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of assignments',
		'operation_description' => 'List assignments',
		'page_title' => 'List of assignments',

		'page_template' => 'general/list',
		'table_display_style' => 'dynamic_table',
		'data_source' => 'get_list_assignments',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'screening_id'=>array(),
			'paper_id' => array(
				'link' => array(
					'url' => 'element/display_element/display_assignment/',
					'id_field' => 'screening_id',
					'trim' => trim_nbr_car()
				)
			),
			'user_id' => array(),
			//'assignment_note'=>array(),
			//'assignment_type'=>array(),
			//'assignment_role'=>array(),	   	
			//'screening_phase'=>array(),
			'assigned_by' => array(),
			'assignment_time' => array(),
			'assignment_mode' => array(),


		),

		'order_by' => 'screening_id ASC ',
		//'search_by'=>'project_title',
		'conditions' => array(
			'screening_phase' => array(
				'field' => 'screening_phase',
				'value' => active_screening_phase(),
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			),
			'assignment_role' => array(
				'field' => 'assignment_role',
				'value' => 'Screening',
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			),
		),

		'list_links' => array(
			/*'view'=>array(
									  'label'=>'View',
									  'title'=>'Display element',
									  'icon'=>'folder',
									  'url'=>'element/display_element/display_assignment/',
							  ),
							  'edit'=>array(
									  'label'=>'Edit',
									  'title'=>'Edit',
									  'icon'=>'edit',
									  'url'=>'element/edit_element/edit_assignment/',
							  ),*/
			'delete' => array(
				'label' => 'Cancel',
				'title' => 'Cancel assignment',
				'url' => 'element/delete_element/remove_assignment/'
			)

		),

		'top_links' => array(

			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);

	if (!can_manage_project()) {
		unset($operations['list_assignments']['list_links']['delete']);

	}

	$operations['list_assignments_validation'] = $operations['list_assignments'];
	$operations['list_assignments_validation']['page_title'] = 'Assignments for validation';
	$operations['list_assignments_validation']['conditions']['assignment_role']['value'] = 'Validation';
	$operations['list_assignments_validation']['list_links']['delete']['url'] = 'element/delete_element/remove_assignment_val/';

	if (!can_manage_project()) {
		unset($operations['list_assignments_validation']['list_links']['delete']);

	}

	$operations['list_my_assignments'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of assignments',
		'operation_description' => 'List assignments',
		'page_title' => 'My screening assignments',

		'page_template' => 'general/list',
		'table_display_style' => 'dynamic_table',
		'data_source' => 'get_list_my_assignments',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'screening_id'=>array(),
			'paper_id' => array(
				'link' => array(
					'url' => 'element/display_element/display_assignment/',
					'id_field' => 'screening_id',
					'trim' => trim_nbr_car()
				)
			),
			'user_id' => array(),
			//'assignment_note'=>array(),
			//'assignment_type'=>array(),
			//'assignment_role'=>array(),	   	
			//'screening_phase'=>array(),
			'assigned_by' => array(),
			'assignment_time' => array(),
			'assignment_mode' => array(),


		),

		'order_by' => 'screening_id ASC ',
		//'search_by'=>'project_title',
		'conditions' => array(
			'screening_phase' => array(
				'field' => 'screening_phase',
				'value' => active_screening_phase(),
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			),
			'assignment_role' => array(
				'field' => 'assignment_role',
				'value' => 'Screening',
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			),
			'user' => array(
				'field' => 'user_id',
				'value' => active_user_id(),
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			)
		),

		'list_links' => array(
			/*	'view'=>array(
									   'label'=>'View',
									   'title'=>'Display element',
									   'icon'=>'folder',
									   'url'=>'element/display_element/display_assignment/',
							   ),
							   'edit'=>array(
									   'label'=>'Edit',
									   'title'=>'Edit',
									   'icon'=>'edit',
									   'url'=>'element/edit_element/edit_assignment/',
							   ),
							   'delete'=>array(
									   'label'=>'Delete',
									   'title'=>'Delete assignment',
									   'url'=>'element/delete_element/remove_assignment/'
							   )*/

		),

		'top_links' => array(

			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);

	$operations['list_my_validations_assignment'] = $operations['list_my_assignments'];
	$operations['list_my_validations_assignment']['page_title'] = 'Validations asssigned to me';
	$operations['list_my_validations_assignment']['conditions']['assignment_role']['value'] = 'Validation';
	$operations['list_my_validations_assignment']['generate_stored_procedure'] = FALSE;


	$operations['list_screenings'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of screenings',
		'operation_description' => 'List screenings',
		'page_title' => 'List of screenings',

		'page_template' => 'general/list',
		'table_display_style' => 'dynamic_table',
		'data_source' => 'get_list_screenings',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'screening_id'=>array(),
			'paper_id' => array(
				'link' => array(
					'url' => 'element/display_element/display_screening/',
					'id_field' => 'screening_id',
					'trim' => trim_nbr_car()
				)
			),
			'user_id' => array(),
			'screening_decision' => array(),
			'exclusion_criteria' => array(),
			'inclusion_criteria' => array(),
			'screening_time' => array(),


		),

		'order_by' => 'screening_id ASC ',
		'order_by' => 'screening_time DESC ',
		//'search_by'=>'project_title',
		'conditions' => array(
			'screening_status' => array(
				'field' => 'screening_status',
				'value' => 'Done',
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			),
			'assignment_role' => array(
				'field' => 'assignment_role',
				'value' => 'Screening',
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			),
			'screening_phase' => array(
				'field' => 'screening_phase',
				'value' => active_screening_phase(),
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			)
		),

		'list_links' => array(
			/*'view'=>array(
									  'label'=>'View',
									  'title'=>'Disaly element',
									  'icon'=>'folder',
									  'url'=>'element/display_element/display_screening/',
							  ),*/
			'edit' => array(
				'label' => 'Edit',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'screening/edit_screen/',
			)

		),

		'top_links' => array(
			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'screening/screening',
			)

		),
	);

	if (!can_manage_project()) {
		unset($operations['list_screenings']['list_links']['edit']);

	}
	//List all validations
	$operations['list_screenings_validation'] = $operations['list_screenings'];
	$operations['list_screenings_validation']['page_title'] = 'Screenings  validation';
	$operations['list_screenings_validation']['conditions']['assignment_role']['value'] = 'Validation';
	$operations['list_screenings_validation']['generate_stored_procedure'] = FALSE;

	//List all pending validation
	$operations['list_pending_screenings_validation'] = $operations['list_screenings_validation'];
	$operations['list_pending_screenings_validation']['page_title'] = 'Pending screenings for validation';
	$operations['list_pending_screenings_validation']['conditions']['screening_status']['value'] = 'Pending';

	//List all pending screenings
	$operations['list_all_pending_screenings'] = $operations['list_pending_screenings_validation'];
	$operations['list_all_pending_screenings']['page_title'] = 'Pending screenings';
	$operations['list_all_pending_screenings']['conditions']['assignment_role']['value'] = 'Screening';


	$operations['list_my_screenings'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of screenings',
		'operation_description' => 'List screenings',
		'page_title' => 'My screenings',

		'page_template' => 'general/list',
		'table_display_style' => 'dynamic_table',
		'data_source' => 'get_list_my_screenings',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'screening_id'=>array(),
			'paper_id' => array(
				'link' => array(
					'url' => 'element/display_element/display_screening/',
					'id_field' => 'screening_id',
					'trim' => trim_nbr_car()
				)
			),
			'user_id' => array(),
			'screening_decision' => array(),
			'exclusion_criteria' => array(),
			'inclusion_criteria' => array(),
			'screening_time' => array(),


		),

		'order_by' => 'screening_id ASC ',
		'order_by' => 'screening_time DESC ',
		//'search_by'=>'project_title',
		'conditions' => array(
			'screening_status' => array(
				'field' => 'screening_status',
				'value' => 'Done',
				'evaluation' => 'equal',
				'parameter_type' => 'VARCHAR(20)',
				'add_on_generation' => False
			),
			'assignment_role' => array(
				'field' => 'assignment_role',
				'value' => 'Screening',
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			),
			'screening_phase' => array(
				'field' => 'screening_phase',
				'value' => active_screening_phase(),
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			),
			'user' => array(
				'field' => 'user_id',
				'value' => active_user_id(),
				'evaluation' => 'equal',
				'add_on_generation' => FALSE,
				'parameter_type' => 'VARCHAR(20)'
			)
		),

		'list_links' => array(
			/*'view'=>array(
									  'label'=>'View',
									  'title'=>'Disaly element',
									  'icon'=>'folder',
									  'url'=>'element/display_element/display_screening/',
							  ),*/
			'edit' => array(
				'label' => 'Edit',
				'title' => 'Edit',
				'icon' => 'edit',
				//	'btn_type'=>'btn-danger',
				'url' => 'screening/edit_screen/',
			)

		),

		'top_links' => array(
			'close' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'screening/screening',
			)

		),
	);
	// list of my screenings pending
	$operations['list_my_pending_screenings'] = $operations['list_my_screenings'];
	$operations['list_my_pending_screenings']['page_title'] = 'My pending screening';
	$operations['list_my_pending_screenings']['conditions']['screening_status']['value'] = 'Pending';
	$operations['list_my_pending_screenings']['generate_stored_procedure'] = FALSE;


	//list of my pending validations
	$operations['list_my_pending_validation'] = $operations['list_my_pending_screenings'];
	$operations['list_my_pending_validation']['page_title'] = 'My pending validation';
	$operations['list_my_pending_validation']['conditions']['assignment_role']['value'] = 'Validation';


	//list of my  validations done
	$operations['list_my_done_validation'] = $operations['list_my_pending_validation'];
	$operations['list_my_done_validation']['page_title'] = 'My validations';
	$operations['list_my_done_validation']['conditions']['screening_status']['value'] = 'Done';


	$operations['new_assignment'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'New assignment',
		'operation_description' => 'New assignment',
		'page_title' => 'Assign a paper',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_assignments',
		'db_save_model' => 'new_assignment',

		'generate_stored_procedure' => True,

		'fields' => array(
			'screening_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'assigned_by' => array('mandatory' => '', 'field_state' => 'hidden'),
			'assignment_mode' => array('mandatory' => '', 'field_state' => 'hidden'),
			'paper_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'user_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),

			'assignment_type' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'assignment_role' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_phase' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'assignment_note' => array('mandatory' => '', 'field_state' => 'enabled'),
		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		)

	);



	$operations['add_reviewer'] = array(
		'operation_type' => 'AddChild',
		'operation_title' => 'Add a reviewer to a paper',
		'operation_description' => 'AddAdd a reviewer to a paper',
		'page_title' => 'Add a reviewer to the paper : ~current_parent_name~',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'screening/display_paper_screen/~current_element~',
		'db_save_model' => 'new_assignment',

		'master_field' => 'paper_id',
		'parent_config' => 'papers',
		'parent_detail_source' => 'get_detail_papers', //To get the name of the user to be displayed in the title
		'parent_detail_source_field' => 'title',

		'generate_stored_procedure' => False,

		'fields' => array(
			'screening_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'assigned_by' => array('mandatory' => '', 'field_state' => 'hidden'),
			'assignment_mode' => array('mandatory' => '', 'field_state' => 'hidden'),
			'paper_id' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'user_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),

			'assignment_type' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'assignment_role' => array('mandatory' => 'mandatory', 'field_state' => 'hidden', 'field_value' => 'Screening'),
			'screening_phase' => array('mandatory' => 'mandatory', 'field_state' => 'hidden', 'field_value' => active_screening_phase()),
			'assignment_note' => array('mandatory' => '', 'field_state' => 'enabled'),

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


	$operations['edit_assignment'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit assignement',
		'operation_description' => 'Edit assignement',
		'page_title' => 'Edit assignement ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_assigments',
		'data_source' => 'get_detail_screen',
		'db_save_model' => 'update_assignment',

		'generate_stored_procedure' => True,

		'fields' => array(
			'screening_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'paper_id' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'user_id' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'assignment_note' => array('mandatory' => '', 'field_state' => 'enabled'),
			'assignment_type' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'assignment_role' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'screening_phase' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),

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

	$operations['screen_paper'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Screen paper',
		'operation_description' => 'Screen paper',
		'page_title' => 'Screen  ',
		'save_function' => 'screening/save_screening',
		'page_template' => 'screening/screen_paper',
		'redirect_after_save' => 'screening/screen_paper',
		'data_source' => 'get_detail_screen',
		'db_save_model' => 'save_screening', //to prepare

		'generate_stored_procedure' => False,

		'fields' => array(
			'screening_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'paper_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'screening_phase' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'screening_decision' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'exclusion_criteria' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'inclusion_criteria' => array('mandatory' => '', 'field_state' => 'enabled'),
			'screening_time' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'screening_status' => array('mandatory' => 'mandatory', 'field_state' => 'hidden', 'field_value' => 'Done'),
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
	$operations['validate_screen'] = $operations['screen_paper'];
	$operations['validate_screen']['page_title'] = "Screening validation";
	$operations['validate_screen']['redirect_after_save'] = "screening/screen_paper_validation";

	$operations['edit_screen'] = $operations['screen_paper'];

	$operations['edit_screen']['redirect_after_save'] = 'element/entity_list/list_screenings';
	$operations['edit_screen']['page_title'] = 'Edit screening';

	$operations['resolve_conflict'] = $operations['screen_paper'];
	$operations['resolve_conflict']['redirect_after_save'] = 'screening/display_paper_screen/~current_paper~';
	$operations['resolve_conflict']['page_title'] = 'Resolve screening conflict';

	$operations['display_assignment'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Info of an assignment',
		'operation_description' => 'Info of an assignment',
		'page_title' => 'Assignment ',


		'data_source' => 'get_detail_screen',
		'generate_stored_procedure' => True,

		'fields' => array(
			//	'screening_id'=>array(),
			'paper_id' => array(),
			'user_id' => array(),
			'assignment_note' => array(),
			'assignment_type' => array(),
			'assignment_role' => array(),
			'screening_phase' => array(),
			'assigned_by' => array(),
			'assignment_time' => array(),
			'assignment_mode' => array(),

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


	$operations['display_screening'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Info of an assignment',
		'operation_description' => 'Info of an assignment',
		'page_title' => 'Screening ',


		'data_source' => 'get_detail_screen',
		'generate_stored_procedure' => False,

		'fields' => array(
			//	'screening_id'=>array(),
			'paper_id' => array(),
			'user_id' => array(),
			'screening_decision' => array(),
			'exclusion_criteria' => array(),
			'inclusion_criteria' => array(),
			'screening_note' => array(),
			'screening_time' => array(),

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
	$operations['remove_assignment'] = array(
		'operation_type' => 'Remove',
		'operation_title' => 'Remove assignment',
		'operation_description' => 'Remove a project',
		'redirect_after_delete' => 'element/entity_list/list_assignments',
		'db_delete_model' => 'remove_screen',
		'generate_stored_procedure' => True,


	);

	$operations['remove_assignment_val'] = array(
		'operation_type' => 'Remove',
		'operation_title' => 'Remove assignment',
		'operation_description' => 'Remove a assignment',
		'redirect_after_delete' => 'element/entity_list/list_assignments_validation',
		'db_delete_model' => 'remove_screen',
		'generate_stored_procedure' => True,


	);
	$config['operations'] = $operations;

	return $config;
	//SELECT  id,`bibtexKey`, `title`,IFNULL(D.screening_decision,'Pending') as decision FROM `paper` P LEFT JOIN screen_decison D ON (P.id=D.paper_id AND D.screening_phase=1 AND D.decision_active =1)  WHERE `paper_active`=1 
}