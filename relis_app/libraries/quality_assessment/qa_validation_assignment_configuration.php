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
	The function creates a configuration array with various settings for managing qa_validation_assignment in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with qa_validation_assignment.
		- table_id: The primary key field for the qa_validation_assignment table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the qa_validation_assignments in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
		- etc.
*/
function get_qa_validation_assignment() {
		$config['config_id']='qa_validation_assignment';
		$config['table_name']=table_name('qa_validation_assignment');
	   	$config['table_id']='qa_validation_assignment_id';
	   	$config['table_active_field']='qa_validation_active';//to detect deleted records
	   	$config['main_field']='paper_id';
	   	
	   	$config['entity_label']='Validation of quality assessment assingnment';	   
	   	$config['entity_label_plural']='Validation of quality assessment assingnment';
	   	
	

	   	//list view
	   	$config['order_by']='qa_validation_assignment_id DESC '; 
	   	
	   
	   	
	   	$fields['qa_validation_assignment_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'int',
				'field_size'=>11,
	   			'field_value'=>'auto_increment',
				'default_value'=>'auto_increment'
				
	   	);
	   	
	   	$fields['paper_id']=array(
	   			'field_title'=>'Paper',
	   			'field_type'=>'int',
	   			'field_size'=>11,
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'papers;CONCAT_WS(" - ",bibtexKey,title)',
	   	
	   			'mandatory'=>' mandatory ',
	   	
	   			 
	   	);
	  
	   	$fields['assigned_to']=array(
	   			'field_title'=>'Assigned to',
	   			'field_type'=>'int',
	   			'field_size'=>11,
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'users;user_name',
	   			'mandatory'=>' mandatory ',
	   	);
	   	
	   	$fields['assigned_by']=array(
	   			'field_title'=>'Assigned by',
	   			'field_type'=>'int',
	   			'field_size'=>11,
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'users;user_name',
	   			'mandatory'=>' mandatory ',
	   	);
	   	
	   	$fields['assignment_time']=array(
	   			'field_title'=>'Assignment time',
	   			'field_type'=>'time',
	   			'default_value'=>'CURRENT_TIMESTAMP',
	   			'field_value'=>bm_current_time('Y-m-d H:i:s'),
	   			 
	   			'field_size'=>20,
	   			'mandatory'=>' mandatory ',
	   	);
	   
	   	$fields['validation']=array(
	   			'field_title'=>'Validation',
	   			'field_type'=>'text',
	   			'field_size'=>15,
	   			'input_type'=>'select',
	   			'input_select_source'=>'array',
	   			'input_select_values'=>array('Correct'=>'Correct',
	   					'Not Correct'=>'Not Correct'
	   			)
	   	);
	   	$fields['validation_time']=array(
	   			'field_title'=>'Validation time',
	   			'field_type'=>'text',
				'field_size'=>20,
				'input_type'=>'text',
	   			
	   	);
	   	
		$fields['validation_note']=array(
	   			'field_title'=>'Validation note',
	   			'field_type'=>'text',
	   			'field_size'=>1000,
	   			'input_type'=>'textarea',
	   			
	   	);
	   	
		$fields['assignment_mode']=array(
				'field_title'=>'Assignment mode',
				'field_type'=>'text',
				'field_value'=>'manualy_single',
				'default_value'=>'auto',
				'field_size'=>30,
				'input_type'=>'select',
				'input_select_source'=>'array',
				'input_select_values'=>array('auto'=>'Automatic',
						'manualy_bulk' => 'Manually Bulk',
						'manualy_single' => 'Manually'
				),
				'mandatory'=>' mandatory ',
		);
	   	$fields['operation_code']=array( //used  for bulk assignment  in order to reverse the operation
	   			'field_title'=>'Operation code',
	   			'field_type'=>'text',
	   			'field_value'=>'01',
	   			'default_value'=> '01',
	   			'mandatory'=>'mandatory',
	   			'field_size'=>15,
	   	
	   	);
	   	
	   	$fields['qa_validation_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'1'
				
				
	   	);
	   	
		$config['fields']=$fields;
	   	
		/*
			The $operations array defines different operations or actions that can be performed on the qa_validation_assignments. 
			Each operation has a type, title, description, page title, save function, page template, and other settings.
		*/
		$operations['add_qa_validation_assignment']=array(
			'operation_type'=>'Add',
			'operation_title'=>'Add QA validation assignment',	
			'operation_description'=>'Add QA validation assignment',	
			'page_title'=>'Assign paper for quality assessment validation',			
			'save_function'=>'element/save_element',
			'page_template'=>'general/frm_entity',
			'redirect_after_save'=>'element/entity_list/list_qa_validation_assignment',
			'db_save_model'=>'add_qa_validation_assignment',
				
			'generate_stored_procedure'=>True,
					
			'fields'=>array(
					'qa_validation_assignment_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'paper_id'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
					'assigned_to'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
					'assigned_by'=>array('mandatory'=>'mandatory','field_state'=>'hidden','field_value'=>active_user_id()),
						),
				
				'top_links'=>array(
							
							'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'close',
										'url'=>'home',
									)
				
				),
			
		);
		$operations['edit_qa_validation_assignment']=array(
				'operation_type'=>'Edit',
				'operation_title'=>'Edit quality assessment validation assignment',
				'operation_description'=>'Edit quality assessment validation assignment',
				'page_title'=>'Edit assignment paper for quality assessment validation ',
				'save_function'=>'element/save_element',
				'page_template'=>'general/frm_entity',
				
				'redirect_after_save'=>'element/entity_list/list_qa_validation_assignment',
				'data_source'=>'get_detail_qa_validation_assignment',
				'db_save_model'=>'update_qa_validation_assignment',
				
		
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					'qa_validation_assignment_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'paper_id'=>array('mandatory'=>'','field_state'=>'disabled'),
					'assigned_to'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
					'validation'=>array('mandatory'=>'','field_state'=>'hidden'),
							
				),
		
				'top_links'=>array(
							
							'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'close',
										'url'=>'home',
									)
				
				),
					
		);
		
		$operations['qa_not_valid']=array(
				'operation_type'=>'Edit',
				'operation_title'=>'QA not valid',
				'operation_description'=>'QA not valid',
				'page_title'=>'Quality assessment not valid ',
				'save_function'=>'element/save_element',
				'page_template'=>'general/frm_entity',
				
				'redirect_after_save'=>'quality_assessment/qa_conduct_list_val',
				'data_source'=>'get_detail_qa_validation_assignment',
				'db_save_model'=>'update_qa_validation',
				
		
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					
					'qa_validation_assignment_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'paper_id'=>array('mandatory'=>'mandatory','field_state'=>'disabled'),
					'validation_note'=>array('mandatory'=>'mandatory','field_state'=>'enabled','field_title'=>'What is not correct with the classification'),
					'validation_time'=>array('mandatory'=>'mandatory','field_state'=>'hidden','field_value'=>bm_current_time()),
					'validation'=>array('mandatory'=>'mandatory','field_state'=>'hidden','field_value'=>'Not Correct'),
							
				),
		
				'top_links'=>array(
							
							'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'close',
										'url'=>'home',
									)
				
				),
					
		);
		
		$operations['edit_qa_validation']=array(
				'operation_type'=>'Edit',
				'operation_title'=>'Edit quality assessment validation assignment',
				'operation_description'=>'Edit quality assessment validation assignment',
				'page_title'=>'Edit assignment paper for quality assessment validation ',
				'save_function'=>'element/save_element',
				'page_template'=>'general/frm_entity',
				
				'redirect_after_save'=>'element/entity_list/list_qa_validation',
				'data_source'=>'get_detail_qa_validation_assignment',
				'db_save_model'=>'update_qa_validation_assignment',
				
		
				'generate_stored_procedure'=>FALSE, //It uses the one for 'edit_qa_validation_assignment'
					
				'fields'=>array(
					'qa_validation_assignment_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'paper_id'=>array('mandatory'=>'','field_state'=>'disabled'),
					'validation'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
					'assigned_to'=>array('mandatory'=>'','field_state'=>'hidden'),
							
				),
		
				'top_links'=>array(
							
							'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'close',
										'url'=>'home',
									)
				
				),
					
		);
		
		$operations['list_qa_validation_assignment']=array(
				'operation_type'=>'List',
				'operation_title'=>'List assignment for  quality assessment',
				'operation_description'=>'List assignment for quality assessment',
				'page_title'=>'Assignments for quality assessment validation',
				'table_display_style'=>'dynamic_table',
				
				'data_source'=>'get_list_qa_validation_assignment',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					'qa_validation_assignment_id'=>array(),
					'paper_id'=>array(
					'link'=>array(
								'url'=>'element/display_element/detail_qa_validation_assignment/',
								'id_field'=>'qa_validation_assignment_id',
								'trim'=>'50'
							)),
					'assigned_to'=>array(),
					'assigned_by'=>array(),
					'assignment_time'=>array(),
							
				),
				'order_by'=>'qa_validation_assignment_id DESC ', 
		
				'list_links'=>array(
					/*	'view'=>array(
									'label'=>'View',
									'title'=>'Disaly element',
									'icon'=>'folder',
									'url'=>'element/display_element/detail_qa_validation_assignment/',
								),
						'edit'=>array(
									'label'=>'Edit',
									'title'=>'Edit',
									'icon'=>'edit',
									'url'=>'element/edit_element/edit_qa_validation_assignment/',
								),*/
						'delete'=>array(
									'label'=>'Delete',
									'title'=>'Delete ',
									'url'=>'element/delete_element/remove_qa_validation_assignment/'
								)
												
				),
				
				'top_links'=>array(
						/*	'add'=>array(
										'label'=>'',
										'title'=>'Add new',
										'icon'=>'add',
										'url'=>'element/add_element/add_qa_validation_assignment',
									),*/
							'close'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									)
				
				),
		);
		
		$operations['list_qa_validation']=array(
				'operation_type'=>'List',
				'operation_title'=>'List assignment for  quality assessment',
				'operation_description'=>'List assignment for quality assessment',
				'page_title'=>'List validation for quality assessment',
				'table_display_style'=>'dynamic_table',
				
				'data_source'=>'get_list_qa_validation_assignment',
				'generate_stored_procedure'=>FALSE,
					
				'fields'=>array(
					'qa_validation_assignment_id'=>array(),
					'paper_id'=>array(
					'link'=>array(
								'url'=>'quality_assessment/qa_conduct_list_val/id/',
							
								'id_field'=>'paper_id',
								'trim'=>'50'
							)),
					'assigned_to'=>array(),
					'validation'=>array(),
					'validation_note'=>array(),
					'Validation_time'=>array(),
							
				),
				'order_by'=>'qa_validation_assignment_id DESC ', 
		
				'list_links'=>array(
					
						
												
				),
				
				'top_links'=>array(
						
							'close'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									)
				
				),
		);
		
		$operations['detail_qa_validation_assignment']=array(
				'operation_type'=>'Detail',
				'operation_title'=>'Detail assignment  for quality assessment',
				'operation_description'=>'Detail assignment  for quality assessment',
				'page_title'=>'Assignment  for quality assessment Validation ',
				
				
				
				'data_source'=>'get_detail_qa_validation_assignment',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
										
					'paper_id'=>array(),
					'assigned_to'=>array(),
					'assigned_by'=>array(),
					'assignment_time'=>array(),
						
							
				),
				
				
				'top_links'=>array(
						'edit'=>array(
									'label'=>'',
									'title'=>'Edit',
									'icon'=>'edit',
									'url'=>'element/edit_element/edit_qa_validation_assignment/~current_element~',
								),	
						'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									),
								
						
				
				),
		);
		
	
		$operations['remove_qa_validation_assignment']=array(
				'operation_type'=>'Remove',
				'operation_title'=>'Remove  assignement for quality assessment',
				'operation_description'=>'Delete assignement for quality assessment',
				'redirect_after_delete'=>'element/entity_list/list_qa_validation_assignment',
				'db_delete_model'=>'remove_qa_validation_assignment',
				'generate_stored_procedure'=>True,
					
				
		);
		
		
	 	$config['operations']=$operations;
	return $config;
	
}