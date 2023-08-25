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
	The function creates a configuration array with various settings for managing qa_questions in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with qa_questions.
		- table_id: The primary key field for the qa_questions table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the qa_questions in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
		- etc.
*/
function get_qa_questions() {
		$config['config_id']='qa_questions';
		$config['table_name']=table_name('qa_questions');
	   	$config['table_id']='question_id';
	   	$config['table_active_field']='question_active';//to detect deleted records
	   	$config['main_field']='question';
	   	
	   	$config['entity_label']='Question';	   
	   	$config['entity_label_plural']='Questions';
	   	
	

	   	//list view
	   	$config['order_by']='question ASC '; //mettre la valeur à mettre dans la requette
	   	
	   
	   	
	   	$fields['question_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'int',
				'field_size'=>11,
	   			'field_value'=>'auto_increment',
				'default_value'=>'auto_increment'
				
	   	);
	   	
	   	
	   	$fields['question']=array(
	   			'field_title'=>'Question',
	   			'field_type'=>'text', 			
				'field_size'=>200, 	   			
				'input_type'=>'text', 
				'mandatory'=>' mandatory ' 
						
	   	);
	   	$fields['response_category']=array(
	   			'field_title'=>'Response quategory',
	   			'field_type'=>'int', 			
				'field_size'=>2, 	   			
				'input_type'=>'text', 
						
	   	);
	   
		
		
	   	$fields['question_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'1'
				
				
	   	);
	   	
		$config['fields']=$fields;
	   	
		/*
			The $operations array defines different operations or actions that can be performed on the qa_questions. 
			Each operation has a type, title, description, page title, save function, page template, and other settings.
		*/
		$operations['add_qa_questions']=array(
			'operation_type'=>'Add',
			'operation_title'=>'Add a new question',	
			'operation_description'=>'Add a new question',	
			'page_title'=>'Add a new question for quality assessment',			
			'save_function'=>'element/save_element',
			'page_template'=>'general/frm_entity',
			'redirect_after_save'=>'element/entity_list/list_qa_questions',
			'db_save_model'=>'add_qa_questions',
				
			'generate_stored_procedure'=>True,
					
			'fields'=>array(
					'question_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'question'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
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
		
		$operations['edit_qa_questions']=array(
				'operation_type'=>'Edit',
				'operation_title'=>'Edit question',
				'operation_description'=>'Edit question',
				'page_title'=>'Edit question for quality assessment ',
				'save_function'=>'element/save_element',
				'page_template'=>'general/frm_entity',
				
				'redirect_after_save'=>'element/entity_list/list_qa_questions',
				'data_source'=>'get_detail_qa_questions',
				'db_save_model'=>'update_qa_questions',
				
				
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
						'question_id'=>array('mandatory'=>'','field_state'=>'hidden'),
						'question'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
							
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
		
		$operations['list_qa_questions']=array(
				'operation_type'=>'List',
				'operation_title'=>'List questions',
				'operation_description'=>'List questions',
				'page_title'=>'Questions for quality assessment',
				
				
				'data_source'=>'get_list_qa_questions',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
						
						'question'=>array(
						'link'=>array(
								'url'=>'element/edit_element/edit_qa_questions/',
								'id_field'=>'question_id',
								'trim'=>''
							)
						),
							
				),
				'order_by'=>'question ASC ', 
				'search_by'=>'question',
		
				'list_links'=>array(
						
						'delete'=>array(
									'label'=>'Delete',
									'title'=>'Delete ',
									'url'=>'element/delete_element/remove_qa_questions/'
								)
												
				),
				
				'top_links'=>array(
							'add'=>array(
										'label'=>'',
										'title'=>'Add a new question',
										'icon'=>'add',
										'url'=>'element/add_element/add_qa_questions',
									),
							'close'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									)
				
				),
		);
		
		$operations['detail_qa_questions']=array(
				'operation_type'=>'Detail',
				'operation_title'=>'Detail of a question',
				'operation_description'=>'Detail of a question',
				'page_title'=>'Question ',
				
				
				
				'data_source'=>'get_detail_qa_questions',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					
						'question'=>array(),
						
							
				),
				
				
				'top_links'=>array(
						'edit'=>array(
									'label'=>'',
									'title'=>'Edit',
									'icon'=>'edit',
									'url'=>'element/edit_element/edit_qa_questions/~current_element~',
								),	
						'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									),
								
						
				
				),
		);
		
	
		$operations['remove_qa_questions']=array(
				'operation_type'=>'Remove',
				'operation_title'=>'Remove a question',
				'operation_description'=>'Delete a question',
				'redirect_after_delete'=>'element/entity_list/list_qa_questions',
				'db_delete_model'=>'remove_qa_questions',
				'generate_stored_procedure'=>True,
					
				
		);
		
		
	 	$config['operations']=$operations;
	return $config;
	
}