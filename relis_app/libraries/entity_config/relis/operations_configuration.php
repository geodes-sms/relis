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
	This function returns a configuration array for managing operations in a system. 
	The function creates a configuration array with various settings for managing operations in a system. Here are the key components of the configuration:
		- table_name: The name of the table associated with operations.
		- table_id: The primary key field for the operations table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the operations in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
		- etc.
*/
function get_operation() {
	
		$config['config_id']='operations';
		$config['table_name']='operations';
	   	$config['table_id']='operation_id';
	   	$config['table_active_field']='operation_active';//to detect deleted records
		$config['main_field']='operation_code';
		
	   	$config['entity_label_plural']='Operations';
		$config['entity_label']='Operation';
	   	
	   	
	   	//Concerne l'affichage
	   		  
	   	$config['order_by']='operation_id DESC '; //mettre la valeur à mettre dans la requette
	   
	   	
	   	$fields['operation_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'int',
				'field_size'=>11,
				'field_value'=>'auto_increment',
				'default_value'=>'auto_increment'
	   	);
	   	
	   	$fields['operation_code']=array( 
	   			'field_title'=>'Code',			
				'field_type'=>'text',
				'default_value'=> '01',
				'field_value'=>'01',				
				'field_size'=>50,	   			
				'input_type'=>'text', 
				'mandatory'=>' mandatory '
	   	);
		
		$fields['operation_type']=array(
	   			'field_title'=>'Operation type',
	   			'field_type'=>'text',
				'field_size'=>50,
				'mandatory'=>' mandatory ',
				'input_type'=>'select',
				'input_select_source'=>'array',
				'input_select_values'=>array('import_paper'=>'Import papers',
						'assign_papers' => 'Assign papers for screening',
						'assign_papers_validation' => 'Assign papers for screening validation'
				),
	   	);
		
		$fields['operation_type']=array(
	   			'field_title'=>'Operation type',
	   			'field_type'=>'text',
				'field_size'=>20,
				'mandatory'=>' mandatory ',
				'input_type'=>'text',
				
	   	);
		$fields['operation_desc']=array(
				'field_title'=>'Description',
				'field_type'=>'text',
				'default_value'=> '01',
				'field_value'=>'01',				
				'field_size'=>200,	   			
				'input_type'=>'text',
		);
		
		$fields['operation_state']=array(
	   			'field_title'=>'Operation state',
	   			'field_type'=>'text',
				'default_value'=>'Active',
				'field_size'=>20,
				'mandatory'=>' mandatory ',
				'input_type'=>'select',
				'input_select_source'=>'array',
				'input_select_values'=>array('Active'=>'Active',
						'Cancelled' => 'Cancelled',
				),
	   	);
		
		$fields['user_id']=array(
	   			'field_title'=>'Done by',
	   			'field_type'=>'int',
				'field_size'=>11,
	   			'field_value'=>active_user_id(),
	   			
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'users;user_name',
				'mandatory'=>' mandatory ',
	   	);
		
		
		
		$fields['operation_time']=array(
	   			'field_title'=>'Time',
	   			'field_type'=>'time', // This type cannot be added in the list of displayed  
				'default_value'=>'CURRENT_TIMESTAMP',
	   			'field_value'=>bm_current_time('Y-m-d H:i:s'),
	   			
	   			'field_size'=>20,
	   			'mandatory'=>' mandatory ',
	   	);
		
		$fields['operation_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'1',
	   	);
		
	   	$config['fields']=$fields;
	   	
		/*
			The $operations array defines different operations or actions that can be performed on the class assignment. 
			Each operation has a type, title, description, page title, save function, page template, and other settings.
		*/
		$operations['list_operations']=array(
			'operation_type'=>'List',
			'operation_title'=>'List operations',
			'operation_description'=>'List operations',
			'page_title'=>'List of bulk operations',
	
			//'page_template'=>'list',
	
			'data_source'=>'get_list_operations',
			'generate_stored_procedure'=>True,
				
			'fields'=>array(
					'operation_id'=>array(),
					'operation_type'=>array(),
					'operation_desc'=>array(),
					'user_id'=>array(),
					'operation_time'=>array(),
					'operation_state'=>array()
						
			),
			'order_by'=>'operation_id DESC',
		
		
			'list_links'=>array(
					
					'edit'=>array(
							'label'=>'Undo',
							'title'=>'Undo operation',
							'icon'=>'rotate-left',
							'url'=>'manager/cancel_operation/'
					)
	
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
	
	$config['operations']=$operations;
	
	return $config;
	
}