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
	The function creates a configuration array with various settings for managing qa_responses in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with qa_responses.
		- table_id: The primary key field for the qa_responses table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the qa_responses in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
		- etc.
*/
function get_qa_responses() {
		$config['config_id']='qa_responses';
		$config['table_name']=table_name('qa_responses');
	   	$config['table_id']='response_id';
	   	$config['table_active_field']='response_active';//to detect deleted records
	   	$config['main_field']='response';
	   	
	   	$config['entity_label']='Response';	   
	   	$config['entity_label_plural']='Responses';
	   	
	

	   	//list view
	   	$config['order_by']='score ASC '; //mettre la valeur à mettre dans la requette
	   	
	   
	   	
	   	$fields['response_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'int',
				'field_size'=>11,
	   			'field_value'=>'auto_increment',
				'default_value'=>'auto_increment'
				
	   	);
	   	
	   	
	   	$fields['response']=array(
	   			'field_title'=>'Answers',
	   			'field_type'=>'text', 			
				'field_size'=>200, 	   			
				'input_type'=>'text', 
				'mandatory'=>' mandatory ' 
						
	   	);
		 	$fields['score']=array(
	   			'field_title'=>'Score',
	   			'field_type'=>'real', 			
				'field_size'=>10, 	   			
				'input_type'=>'text', 
				'mandatory'=>' mandatory ', 
				'display_null'=>True
						
	   	);
	   	$fields['response_category']=array(
	   			'field_title'=>'Response quategory',
	   			'field_type'=>'int', 			
				'field_size'=>2, 	   			
				'input_type'=>'text', 
						
	   	);
	   
		
		
	   	$fields['response_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'1'
				
				
	   	);
	   	
		$config['fields']=$fields;
	   	
		/*
			The $operations array defines different operations or actions that can be performed on the qa_responses. 
			Each operation has a type, title, description, page title, save function, page template, and other settings.
		*/
		$operations['add_qa_responses']=array(
			'operation_type'=>'Add',
			
			'page_title'=>'Add a new answers for quality assessment',			
			'save_function'=>'element/save_element',
			'page_template'=>'general/frm_entity',
			'redirect_after_save'=>'element/entity_list/list_qa_responses',
			'db_save_model'=>'add_qa_responses',
				
			'generate_stored_procedure'=>True,
					
			'fields'=>array(
					'response_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'response'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
					'score'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
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
		
		$operations['edit_qa_responses']=array(
				'operation_type'=>'Edit',
				'operation_title'=>'Edit response',
				'operation_description'=>'Edit response',
				'page_title'=>'Edit answers for quality assessment ',
				'save_function'=>'element/save_element',
				'page_template'=>'general/frm_entity',
				
				'redirect_after_save'=>'element/entity_list/list_qa_responses',
				'data_source'=>'get_detail_qa_responses',
				'db_save_model'=>'update_qa_responses',
				
		
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
						'response_id'=>array('mandatory'=>'','field_state'=>'hidden'),
						'response'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
						'score'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
							
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
		
		$operations['list_qa_responses']=array(
				'operation_type'=>'List',
				'operation_title'=>'List responses',
				'operation_description'=>'List responses',
				'page_title'=>'Answers for quality assessment',
				
				
				'data_source'=>'get_list_qa_responses',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
						//'response_id'=>array(),
						'response'=>array(
							'link'=>array(
									'url'=>'element/edit_element/edit_qa_responses/',
									'id_field'=>'response_id',
									'trim'=>''
								)
						),
						'score'=>array(),
							
				),
				'order_by'=>'response ASC ', 
				'search_by'=>'response',
		
				'list_links'=>array(
						
						'delete'=>array(
									'label'=>'Delete',
									'title'=>'Delete ',
									'url'=>'element/delete_element/remove_qa_responses/'
								)
												
				),
				
				'top_links'=>array(
							'add'=>array(
										'label'=>'',
										'title'=>'Add a new response',
										'icon'=>'add',
										'url'=>'element/add_element/add_qa_responses',
									),
							'close'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									)
				
				),
		);
		
		$operations['detail_qa_responses']=array(
				'operation_type'=>'Detail',
				'page_title'=>'Answers ',
				
				
				
				'data_source'=>'get_detail_qa_responses',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					
						'response'=>array(),
						'score'=>array(),
						
							
				),
				
				
				'top_links'=>array(
						'edit'=>array(
									'label'=>'',
									'title'=>'Edit',
									'icon'=>'edit',
									'url'=>'element/edit_element/edit_qa_responses/~current_element~',
								),	
						'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									),
								
						
				
				),
		);
		
	
		$operations['remove_qa_responses']=array(
				'operation_type'=>'Remove',
				'operation_title'=>'Remove a response',
				'operation_description'=>'Delete a response',
				'redirect_after_delete'=>'element/entity_list/list_qa_responses',
				'db_delete_model'=>'remove_qa_responses',
				'generate_stored_procedure'=>True,
					
				
		);
		
		
	 	$config['operations']=$operations;
	return $config;
	
}