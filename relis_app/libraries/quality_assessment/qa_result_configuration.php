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
	The function creates a configuration array with various settings for managing qa_result in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with qa_result.
		- table_id: The primary key field for the qa_result table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the qa_results in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
		- etc.
*/
function get_qa_result() {
		$config['config_id']='qa_result';
		$config['table_name']=table_name('qa_result');
	   	$config['table_id']='qa_id';
	   	$config['table_active_field']='qa_active';//to detect deleted records
	   	$config['main_field']='paper_id';
	   	
	   	$config['entity_label']='Quality assessment';	   
	   	$config['entity_label_plural']='Quality assessment';
	   	
	

	   	//list view
	   	$config['order_by']='qa_id DESC '; //mettre la valeur à mettre dans la requette
	   	
	   
	   	
	   	$fields['qa_id']=array(
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
	   	$fields['question']=array(
	   			'field_title'=>'Question',
	   			'field_type'=>'int',
	   			'field_size'=>11,
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'qa_questions;question',	   		  
	   			'mandatory'=>' mandatory ',   		  
	   	
	   	);
	   	
	   	$fields['response']=array(
	   			'field_title'=>'Response',
	   			'field_type'=>'int',
	   			'field_size'=>11,
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'qa_responses;response',
	   			'mandatory'=>' mandatory ',
	   		  
	   	);
	   	
	   	$fields['done_by']=array(
	   			'field_title'=>'Done by',
	   			'field_type'=>'int',
	   			'field_size'=>11,
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'users;user_name',
	   			'mandatory'=>' mandatory ',
	   	);
	   	
	   	$fields['qa_time']=array(
	   			'field_title'=>'Time',
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
	   			'input_select_values'=>array('Pending'=>'Pending',
	   					'Done'=>'Done'
	   			),
	   			'field_value'=>'Pending',
	   			'default_value'=>'Pending',
	   			'mandatory'=>'mandatory',
	   	);
	 
	   	$fields['qa_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'1'
				
				
	   	);
	   	
		$config['fields']=$fields;
	   	
		/*
			The $operations array defines different operations or actions that can be performed on the qa_results. 
			Each operation has a type, title, description, page title, save function, page template, and other settings.
		*/
		$operations['add_qa_result']=array(
			'operation_type'=>'Add',
			'operation_title'=>'Add quality assessment',	
			'operation_description'=>'Add quality assessment',	
			'page_title'=>'Add quality assessment',			
			'save_function'=>'element/save_element',
			'page_template'=>'general/frm_entity',
			'redirect_after_save'=>'element/entity_list/list_qa_result',
			'db_save_model'=>'add_qa_result',
				
			'generate_stored_procedure'=>True,
					
			'fields'=>array(
					'qa_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'paper_id'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
					'question'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
					'response'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
					'done_by'=>array('mandatory'=>'mandatory','field_state'=>'hidden','field_value'=>active_user_id()),
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
		
		$operations['edit_qa_result']=array(
				'operation_type'=>'Edit',
				'operation_title'=>'Edit quality assessment',
				'operation_description'=>'Edit quality assessment',
				'page_title'=>'Edit quality assessment ',
				'save_function'=>'element/save_element',
				'page_template'=>'general/frm_entity',
				
				'redirect_after_save'=>'element/entity_list/list_qa_result',
				'data_source'=>'get_detail_qa_result',
				'db_save_model'=>'update_qa_result',
				
		
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					'qa_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'paper_id'=>array('mandatory'=>'mandatory','field_state'=>'disabled'),
					'question'=>array('mandatory'=>'mandatory','field_state'=>'disabled'),
					'response'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
							
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
		
		$operations['list_qa_result']=array(
				'operation_type'=>'List',
				'operation_title'=>'List quality assessment',
				'operation_description'=>'List quality assessment',
				'page_title'=>'List quality assessment',
				
				
				'data_source'=>'get_list_qa_result',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					'qa_id'=>array(),
					'paper_id'=>array(),
					'question'=>array(),
					'response'=>array(),
							
				),
				'order_by'=>'paper_id ASC ', 
		
				'list_links'=>array(
						'view'=>array(
									'label'=>'View',
									'title'=>'Disaly element',
									'icon'=>'folder',
									'url'=>'element/display_element/detail_qa_result/',
								),
						'edit'=>array(
									'label'=>'Edit',
									'title'=>'Edit',
									'icon'=>'edit',
									'url'=>'element/edit_element/edit_qa_result/',
								),
						'delete'=>array(
									'label'=>'Delete',
									'title'=>'Delete ',
									'url'=>'element/delete_element/remove_qa_result/'
								)
												
				),
				
				'top_links'=>array(
							'add'=>array(
										'label'=>'',
										'title'=>'Add new',
										'icon'=>'add',
										'url'=>'element/add_element/add_qa_result',
									),
							'close'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									)
				
				),
		);
		
		$operations['detail_qa_result']=array(
				'operation_type'=>'Detail',
				'operation_title'=>'Detail quality assessment',
				'operation_description'=>'Detail quality assessment',
				'page_title'=>'Quality assessment ',
				
				
				
				'data_source'=>'get_detail_qa_result',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
										
					'paper_id'=>array(),
					'question'=>array(),
					'response'=>array(),
						
							
				),
				
				
				'top_links'=>array(
						'edit'=>array(
									'label'=>'',
									'title'=>'Edit',
									'icon'=>'edit',
									'url'=>'element/edit_element/edit_qa_result/~current_element~',
								),	
						'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									),
								
						
				
				),
		);
		
	
		$operations['remove_qa_result']=array(
				'operation_type'=>'Remove',
				'operation_title'=>'Remove quality assessment',
				'operation_description'=>'Delete quality assessment',
				'redirect_after_delete'=>'element/entity_list/list_qa_result',
				'db_delete_model'=>'remove_qa_result',
				'generate_stored_procedure'=>True,
					
				
		);
		
		
	 	$config['operations']=$operations;
	return $config;
	
}