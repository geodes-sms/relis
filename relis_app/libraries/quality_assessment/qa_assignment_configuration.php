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
	The function creates a configuration array with various settings for managing qa_assignments in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with qa_assignments.
		- table_id: The primary key field for the qa_assignment table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the qa_assignment in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
		- etc.
*/
function get_qa_assignment() {
		$config['config_id']='qa_assignment';
		$config['table_name']=table_name('qa_assignment');
	   	$config['table_id']='qa_assignment_id';
	   	$config['table_active_field']='qa_assignment_active';//to detect deleted records
	   	$config['main_field']='paper_id';
	   	
	   	$config['entity_label']='Quality assessment assingnment';	   
	   	$config['entity_label_plural']='Quality assessment assingnment';
	   	
	

	   	//list view
	   	$config['order_by']='qa_assignment_id DESC '; 
	   	
	   
	   	
	   	$fields['qa_assignment_id']=array(
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
	   			'field_title'=>'Time',
	   			'field_type'=>'time',
	   			'default_value'=>'CURRENT_TIMESTAMP',
	   			'field_value'=>bm_current_time('Y-m-d H:i:s'),
	   			 
	   			'field_size'=>20,
	   			'mandatory'=>' mandatory ',
	   	);
	   
	   	$fields['qa_status']=array(
	   			'field_title'=>'QA Status',
	   			'field_type'=>'text',
	   			'field_size'=>15,
	   			'input_type'=>'select',
	   			'input_select_source'=>'array',
	   			'input_select_values'=>array('Pending'=>'Pending',
	   					'Done'=>'Done'
	   			),
	   			'field_value'=>'Pending',
	   			'default_value'=>'Pending',
	   			'mandatory'=>'mandatory',
	   	);
	   	
	   	$fields['assignment_type']=array(
	   			'field_title'=>'Assignment type',
	   			'field_type'=>'text',
	   			'field_size'=>15,
	   			'input_type'=>'select',
	   			'input_select_source'=>'array',
	   			'input_select_values'=>array('QA'=>'QA',
	   					'Validation'=>'Validation'
	   			),
	   			'field_value'=>'QA',
	   			'default_value'=>'QA',
	   			'mandatory'=>'mandatory',
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
	   	
	   	$fields['qa_assignment_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'1'
				
				
	   	);
	   	
		$config['fields']=$fields;

		/*
			The $table_views array defines different views or queries that can be used to retrieve specific sets of data related to qa_assignment. 
			Each view has a name, description, and SQL script.
		*/
	   	$table_views=array();
	
		$table_views['view_papers_in_qa']=array(
					'name'=>'view_papers_in_qa',
					'desc'=>'',
					
					'script'=>'SELECT Q.*,Q.qa_assignment_id as assignment_id,P.title,P.screening_status as status FROM qa_assignment Q,paper P where Q.paper_id=P.id AND qa_assignment_active=1 AND paper_active=1  ',
					
			);
		$config['table_views']=$table_views;
	
		/*
			The $operations array defines different operations or actions that can be performed on the qa_assignments. 
			Each operation has a type, title, description, page title, save function, page template, and other settings.
		*/
		$operations['add_qa_assignment']=array(
			'operation_type'=>'Add',
			'operation_title'=>'Add quality assessment assignment',	
			'operation_description'=>'Add quality assessment assignment',	
			'page_title'=>'Assign paper for quality assessment',			
			'save_function'=>'element/save_element',
			'page_template'=>'general/frm_entity',
			'redirect_after_save'=>'element/entity_list/list_qa_assignment',
			'db_save_model'=>'add_qa_assignment',
				
			'generate_stored_procedure'=>True,
					
			'fields'=>array(
					'qa_assignment_id'=>array('mandatory'=>'','field_state'=>'hidden'),
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
		
		$operations['edit_qa_assignment']=array(
				'operation_type'=>'Edit',
				'operation_title'=>'Edit quality assessment assignment',
				'operation_description'=>'Edit quality assessment assignment',
				'page_title'=>'Edit assignment paper for quality assessment ',
				'save_function'=>'element/save_element',
				'page_template'=>'general/frm_entity',
				
				'redirect_after_save'=>'element/entity_list/list_qa_assignment',
				'data_source'=>'get_detail_qa_assignment',
				'db_save_model'=>'update_qa_assignment',
				
		
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					'qa_assignment_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'paper_id'=>array('mandatory'=>'mandatory','field_state'=>'disabled'),
					'assigned_to'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
							
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
		
		$operations['list_qa_assignment']=array(
				'operation_type'=>'List',
				'operation_title'=>'List assignment for  quality assessment',
				'operation_description'=>'List assignment for quality assessment',
				'page_title'=>'List assignments for quality assessment',
				'table_display_style'=>'dynamic_table',
				
				'data_source'=>'get_list_qa_assignment',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					'qa_assignment_id'=>array(),
					'paper_id'=>array(
					'link'=>array(
								'url'=>'element/display_element/detail_qa_assignment/',
								'id_field'=>'qa_assignment_id',
								'trim'=>'50'
							)),
					'assigned_to'=>array(),
					'assigned_by'=>array(),
					'assignment_time'=>array(),
							
				),
				'order_by'=>'qa_assignment_id DESC ', 
		
				'list_links'=>array(
					/*	'view'=>array(
									'label'=>'View',
									'title'=>'Disaly element',
									'icon'=>'folder',
									'url'=>'element/display_element/detail_qa_assignment/',
								),
						'edit'=>array(
									'label'=>'Edit',
									'title'=>'Edit',
									'icon'=>'edit',
									'url'=>'element/edit_element/edit_qa_assignment/',
								),*/
						'delete'=>array(
									'label'=>'Delete',
									'title'=>'Delete ',
									'url'=>'element/delete_element/remove_qa_assignment/'
								)
												
				),
				
				'top_links'=>array(
							'add'=>array(
										'label'=>'',
										'title'=>'Add new',
										'icon'=>'add',
										'url'=>'element/add_element/add_qa_assignment',
									),
							'close'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									)
				
				),
		);
		

		
		$operations['list_qa_papers']=array(
				'operation_type'=>'List',
				'page_title'=>'All papers Papers in QA',
				'table_display_style'=>'dynamic_table',
				'table_name'=>'view_papers_in_qa',
				'data_source'=>'get_list_qa_papers',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					//'qa_assignment_id'=>array(),
					'paper_id'=>array('field_title'=>'Title',
					'link'=>array(
								'url'=>'paper/display_paper_min/',
								'id_field'=>'paper_id',
								'trim'=>trim_nbr_car()
							)),
					
							
				),
				'order_by'=>'qa_assignment_id DESC ', 
		
				'list_links'=>array(
					/*	'view'=>array(
									'label'=>'View',
									'title'=>'Disaly element',
									'icon'=>'folder',
									'url'=>'element/display_element/detail_qa_assignment/',
								),
						'edit'=>array(
									'label'=>'Edit',
									'title'=>'Edit',
									'icon'=>'edit',
									'url'=>'element/edit_element/edit_qa_assignment/',
								),
						'delete'=>array(
									'label'=>'Delete',
									'title'=>'Delete ',
									'url'=>'element/delete_element/remove_qa_assignment/'
								)*/
												
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
		
		$operations['list_qa_papers_done']=$operations['list_qa_papers'];
		$operations['list_qa_papers_done']['page_title']='Assessed papers in QA';
		$operations['list_qa_papers_done']['data_source']='get_list_qa_papers_per_status';
		$operations['list_qa_papers_done']['conditions']['screening_status']=array(
																'field'=>'qa_status',
																'value'=>'Done',
																'evaluation'=>'equal',
																'add_on_generation'=>False,
																'parameter_type'=>'VARCHAR(20)'
															);
		$operations['list_qa_papers_pending']=$operations['list_qa_papers_done'];
		$operations['list_qa_papers_pending']['page_title']='Pending papers in QA';
		$operations['list_qa_papers_pending']['generate_stored_procedure']=FALSE;
		$operations['list_qa_papers_pending']['conditions']['screening_status']['value']='Pending';
		
		$operations['detail_qa_assignment']=array(
				'operation_type'=>'Detail',
				'operation_title'=>'Detail assignment  for quality assessment',
				'operation_description'=>'Detail assignment  for quality assessment',
				'page_title'=>'Assignment  for quality assessment ',
				
				
				
				'data_source'=>'get_detail_qa_assignment',
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
									'url'=>'element/edit_element/edit_qa_assignment/~current_element~',
								),	
						'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									),
								
						
				
				),
		);
		
	
		$operations['remove_qa_assignment']=array(
				'operation_type'=>'Remove',
				'operation_title'=>'Remove  assignement for quality assessment',
				'operation_description'=>'Delete assignement for quality assessment',
				'redirect_after_delete'=>'element/entity_list/list_qa_assignment',
				'db_delete_model'=>'remove_qa_assignment',
				'generate_stored_procedure'=>True,
					
				
		);
		
		
	 	$config['operations']=$operations;
	return $config;
	
}