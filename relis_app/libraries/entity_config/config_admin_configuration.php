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
function get_admin_configuration() {
	
		$config['config_id']='config_admin';
		$config['table_name']=table_name('config_admin');
	   	$config['table_id']='config_id';
	   	$config['table_active_field']='config_active';//to detect deleted records
	   	$config['main_field']='config_type';
	   	
	  	$config['entity_label']='Configuration';
	   	$config['entity_label_plural']='Configurations';
	   	
	   	//list view
	   	$config['order_by']='config_id ASC '; //mettre la valeur à mettre dans la requette
	   

	   
	   	
	   	$fields['config_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'int',
				'field_size'=>11,
	   			'field_value'=>'auto_increment',
				'default_value'=>'auto_increment'
	   	);
	   	

	   	$fields['config_type']=array(
	   			'field_title'=>'Configuration type',
	   			'field_type'=>'text',	   			
	   			'field_value'=>'default',
	   			'field_size'=>100,
	   			'input_type'=>'text',
	   			'mandatory'=>' mandatory '
	   	);
	   	
	   	$fields['editor_url']=array(
	   			'field_title'=>'Editor location(url)',
	   			'field_type'=>'text',	   			
	   			'field_value'=>'default',
	   			'field_size'=>100,
	   			'input_type'=>'text',
	   			'mandatory'=>' mandatory '
	   	);
	   	$fields['editor_generated_path']=array(
	   			'field_title'=>'Editor workspace',
	   			'field_type'=>'text',	   			
	   			'field_value'=>'default',
	   			'field_size'=>100,
	   			'input_type'=>'text',
	   			'mandatory'=>' mandatory '
	   	);
	   	
		
		
		$fields['track_comment_on']=array(
				'field_title'=>'Debug comment active',
				'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'0',
				'default_value'=>'0',
				'input_type'=>'select',
				'input_select_source'=>'yes_no',
				'input_select_values'=>'1',
		);
		
		
		
		$fields['list_trim_nbr']=array(
	   			'field_title'=>'Paper name nb caracters displayed ',
	   			'field_type'=>'int',	   			
	   			'field_value'=>'80',
	   			'default_value'=>'80',
	   			'field_size'=>3,
	   			'input_type'=>'text',
	   			'mandatory'=>' '
	   	);
		
		
		$fields['first_connect']=array(
				'field_title'=>'First connect',
				'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'0',
				'input_type'=>'select',
				'input_select_source'=>'yes_no',
				'input_select_values'=>'1',
		);
		
	   	$fields['config_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'1'
	   	);
	   	$config['fields']=$fields;
		
	   	$config['init_query'][0]="INSERT INTO `config_admin`
		(`config_id`, `editor_url`, `editor_generated_path`) VALUES
(1, 'http://127.0.0.1:8080/relis/texteditor', 'C:\\dslforge_workspace')";

	   	$operations['admin_config']=array(
	   			'operation_type'=>'Detail',
	   			'operation_title'=>'Configurations values',
	   			'operation_description'=>'Configurations values',
	   			'page_title'=>'Admin settings',
	   	
	   			//'page_template'=>'general/display_element',
	   	
	   			'data_source'=>'get_detail_config_admin',
	   			'generate_stored_procedure'=>True,
	   				
	   			'fields'=>array(
	   					
	   					
	   					'editor_url'=>array(),
	   					'editor_generated_path'=>array(),
	   					'track_comment_on'=>array(),
	   					
	   					
	   						
	   			),
	   	
	   	
	   			'top_links'=>array(
	   					'edit'=>array(
	   							'label'=>'',
	   							'title'=>'Edit',
	   							'icon'=>'edit',
	   							'url'=>'op/edit_element/edit_admin_config/~current_element~',
	   					),
	   					'back'=>array(
	   							'label'=>'',
	   							'title'=>'Close',
	   							'icon'=>'',
	   							'url'=>'home',
	   					),
	   	
	   	
	   	
	   			),
	   	);
	   	
		
			$operations['edit_admin_config']=array(
	   			'operation_type'=>'Edit',
	   			'operation_title'=>'Edit configuration for papers',
	   			'operation_description'=>'Edit configuration for papers',
	   			'page_title'=>'Edit settings ',
	   			'save_function'=>'op/save_element',
	   			'page_template'=>'general/frm_entity',
	   	
	   			'redirect_after_save'=>'op/display_element/admin_config/1',
	   			'data_source'=>'get_detail_config_admin',
	   			'db_save_model'=>'update_config_admin',
	   	
	   			'generate_stored_procedure'=>True,
	   				
	   			'fields'=>array(
	   					'config_id'=>array('mandatory'=>'','field_state'=>'hidden'),
	   					'editor_url'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
	   					'editor_generated_path'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
	   					'track_comment_on'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),	   					
	   					
	   				
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
		
	   	$config['operations']=$operations;
	return $config;
	
}