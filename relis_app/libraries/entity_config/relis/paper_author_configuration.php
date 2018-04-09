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
function get_paper_author() {
	$config['config_id']='paper_author';
		$config['table_name']='paperauthor';
	   	$config['table_id']='paperauthor_id';
	   	$config['table_active_field']='paperauthor_active';//to detect deleted records
	   
	   	
	   	$config['entity_label']='Paper author';
	   	$config['entity_label_plural']='Paper authors';
	   	
	   	
	   	//Concerne l'affichage
	   		  
	   	$config['order_by']='paperauthor_id ASC '; //mettre la valeur à mettre dans la requette
	   
	   	
	   	
	   	$fields['paperauthor_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'int',
				'field_size'=>11,
	   			'field_value'=>'auto_increment',
				'default_value'=>'auto_increment',
	   	);
	   
	   	$fields['paperId']=array(
	   			'field_title'=>'Paper',
	   			'field_type'=>'int',	   			
	   			'field_size'=>11,
	   			'mandatory'=>' mandatory ',
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
				'input_select_source_type'=>'drill_down',
				'drill_down_type'=>'not_linked',
	   			'input_select_values'=>'papers;title',
	   			
	   	);
	   	
		$fields['authorId']=array(
	   			'field_title'=>'Author',
	   			'field_type'=>'int',	   			
	   			'field_size'=>11,
	   			'mandatory'=>' mandatory ',
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
				'input_select_source_type'=>'drill_down',
				'drill_down_type'=>'not_linked',
	   			'input_select_values'=>'author;author_name',
				
		);
	
		
		$fields['author_rank']=array(
	   			'field_title'=>'Rank',
	   			'field_type'=>'number', 				
				'field_size'=>2,  	   			
				'input_type'=>'text',
				'field_value'=>'1',
				'default_value'=>'1',
				'mandatory'=>' mandatory ' 
						
	   	);
	   	
		$fields['paperauthor_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'int',
	   			'field_size'=>'1',
	   			'field_value'=>'1',
				'default_value'=>'1',
	   	);
		
		$operations=array();
		$config['operations']=$operations;
	   	$config['fields']=$fields;
	   	
	
	return $config;
	
}