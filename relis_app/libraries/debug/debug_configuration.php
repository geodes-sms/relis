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
	This function returns a configuration array for managing config_debug in a system. 
	The function creates a configuration array with various settings for managing config_debug in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with debugging.
		- table_id: The primary key field for the debug table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the config_debug in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
		- etc.
*/
function get_config_debug() {
		$config['config_id']='debug';
		$config['table_name']=table_name('debug');
	   	$config['table_id']='debug_id';
	   	$config['table_active_field']='debug_active';//to detect deleted records
	   	$config['main_field']='title';
	   	
	   	$config['entity_label']='Debug';	   
	   	$config['entity_label_plural']='Debug';
	   	
	
	
	   	$config['order_by']='debug_id DESC '; //mettre la valeur à mettre dans la requette
	  	
	   
	   	
	   	$fields['debug_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'int',
				'field_size'=>11,
	   			'field_value'=>'auto_increment',
				'default_value'=>'auto_increment'
				
	   	);
	   	
	   	
	   	$fields['title']=array(
	   			'field_title'=>'Title',
	   			'field_type'=>'text', 			
				'field_size'=>1000,  
	   			
				'input_type'=>'text', 
				'mandatory'=>' mandatory ' 
						
	   	);
		$fields['comment']=array(
	   			'field_title'=>'Comment',
	   			'field_type'=>'text',
	   			'field_size'=>1000,
	   			'input_type'=>'textarea',
	   	
	   	);
		$fields['page_code']=array(
	   			'field_title'=>'Page code',
	   			'field_type'=>'text', 			
				'field_size'=>200,  
	   			
				'input_type'=>'text'
						
	   	);
	   	
		$fields['page_url']=array(
	   			'field_title'=>'Page Url',
	   			'field_type'=>'text', 			
				'field_size'=>200,  
	   			
				'input_type'=>'text'
						
	   	);
		
		$fields['debug_picture']=array(
				'field_title'=>'Attach image',
				'field_type'=>'image',			
				'input_type'=>'image',
				
		);
		
		
	   	$fields['created_by']=array(
	   			'field_title'=>'Created by',
	   			'field_type'=>'number',
				'field_size'=>11,
				'default_value'=>1,
				
	   			'field_value'=>active_user_id(),
	   			
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'users;user_name',
				'mandatory'=>' mandatory ',
				
	   	);
		
		$fields['creation_time']=array(
	   			'field_title'=>'Creation time',
	   			'field_type'=>'time',
				'default_value'=>'CURRENT_TIMESTAMP',
	   			'field_value'=>bm_current_time('Y-m-d H:i:s'),
	   			
	   			'field_size'=>20,
	   			'mandatory'=>' mandatory ',
				
	   	);
		
		$fields['status']=array(
	   			'field_title'=>'Status',
	   			'field_type'=>'text',
				'field_value'=>'New',
	   			'field_size'=>15,
	   			'input_type'=>'select',
				'input_select_source'=>'array',
				'input_select_values'=>array(
								'New' => 'New',
								'Progress' => 'Progress',
								'Info' => 'Info',
								'Done' => 'Done',			
							),	
				'number_of_values'=>'1'			
		);
		
		
	   	$fields['debug_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'1'
				
				
	   	);
	   	
		$config['fields']=$fields;
	   	
		/*
			The $operations array defines different operations or actions that can be performed on debugging. 
			Each operation has a type, title, description, page title, save function, page template, and other settings.
		*/
		$operations['add_debug']=array(
			'operation_type'=>'Add',
			'operation_title'=>'Add a comment',	
			'operation_description'=>'Add a comment',	
			'page_title'=>'Add new comment',			
			'save_function'=>'element/save_element',
			'page_template'=>'general/frm_entity',
			'redirect_after_save'=>'element/entity_list/list_debug',
			'db_save_model'=>'add_debug',
				
			'generate_stored_procedure'=>True,
					
			'fields'=>array(
					'debug_id'=>array('mandatory'=>'','field_state'=>'hidden'),
					'status'=>array('mandatory'=>'','field_state'=>'hidden'),
					'created_by'=>array('mandatory'=>'','field_state'=>'hidden'),
					'page_code'=>array('mandatory'=>'','field_state'=>'hidden','field_value'=>get_debug_info('debug_paper_code')),
					'page_url'=>array('mandatory'=>'','field_state'=>'hidden','field_value'=>get_debug_info('debug_paper_url')),
					
					
					'title'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
					'comment'=>array('mandatory'=>'','field_state'=>'enabled'),
					'debug_picture'=>array('mandatory'=>'','field_state'=>'hidden'),
								
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
		
		$operations['edit_debug']=array(
				'operation_type'=>'Edit',
				'operation_title'=>'Edit a comment',
				'operation_description'=>'Edit a comment',
				'page_title'=>'Edit a comment ',
				'save_function'=>'element/save_element',
				'page_template'=>'general/frm_entity',
				
				'redirect_after_save'=>'element/entity_list/list_debug',
				'data_source'=>'get_debug_detail',
				'db_save_model'=>'update_debug',
				
		
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
						'debug_id'=>array('mandatory'=>'','field_state'=>'hidden'),
						'title'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
						'comment'=>array('mandatory'=>'','field_state'=>'enabled'),
						'debug_picture'=>array('mandatory'=>'','field_state'=>'enabled'),
						'status'=>array('mandatory'=>'','field_state'=>'enabled'),
							
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
		
		$operations['list_debug']=array(
				'operation_type'=>'List',
				'operation_title'=>'List coments',
				'operation_description'=>'List comments',
				'page_title'=>'List comments',
				
				//'page_template'=>'list',
				
				'data_source'=>'get_list_debug',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
						'title'=>array('link'=>array(
								'url'=>'element/display_element/detail_debug/',
								'id_field'=>'debug_id',
								'trim'=>'0'
							)),
						//'title'=>array(),
						'page_url'=>array('link'=>array(
								'url'=>'',
								'id_field'=>'page_url',
								'trim'=>'0')),
						'created_by'=>array(),
						'creation_time'=>array(),
						'status'=>array()
							
				),
				'order_by'=>'debug_id DESC ', 
				'search_by'=>'title',
			
				'list_links'=>array(
						'delete'=>array(
									'label'=>'Delete',
									'title'=>'Delete the user',
									'url'=>'element/delete_element/remove_debug/')
												
				),
				
				'top_links'=>array(
							'add'=>array(
										'label'=>'',
										'title'=>'Add a new user',
										'icon'=>'add',
										'url'=>'element/add_element/add_debug',
									),
							'close'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									)
				
				),
		);
		
		$operations['detail_debug']=array(
				'operation_type'=>'Detail',
				'operation_title'=>'Comment',
				'operation_description'=>'Comment',
				'page_title'=>'Comment ',
				
				
				'data_source'=>'get_debug_detail',
				'generate_stored_procedure'=>True,
					
				'fields'=>array(
					//	'debug_id'=>array(),
						'title'=>array(),
						'comment'=>array(),
						'page_code'=>array(),
						'page_url'=>array(),
						
						'creation_time'=>array(),
						'created_by'=>array(),
						'debug_picture'=>array(),
						
							
				),
				
				
				'top_links'=>array(
						'edit'=>array(
									'label'=>'',
									'title'=>'Edit',
									'icon'=>'edit',
									'url'=>'element/edit_element/edit_debug/~current_element~',
								),	
						'back'=>array(
										'label'=>'',
										'title'=>'Close',
										'icon'=>'add',
										'url'=>'home',
									),
								
						
				
				),
		);
		
		
		$operations['remove_debug']=array(
				'operation_type'=>'Remove',
				'operation_title'=>'Remove a user',
				'operation_description'=>'Delete a debug comment',

				'redirect_after_delete'=>'element/entity_list/list_debug',
				'db_delete_model'=>'remove_debug',
				'generate_stored_procedure'=>True,
					
				
		);
		
		
	 	$config['operations']=$operations;
	return $config;
	
}