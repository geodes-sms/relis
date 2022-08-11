<?php 
function get_classification_intent_relation() {
	
		$config['table_name']='classification_intent_relation';
	   	$config['table_id']='class_intent_rel_id';
	   	$config['table_active_field']='class_intent_rel_active';//to detect deleted records
	   	$config['reference_title']='Intents relations';
	   	$config['reference_title_min']='Intents relation';
	   	
	   	
	   	//Concerne l'affichage
	   		  
	   	$config['order_by']='class_intent_rel_id ASC '; //mettre la valeur à mettre dans la requette
	  
	   	$config['links']['edit']=array(
	   			'label'=>'Edit',
	   			'title'=>'Edit scope',
	   			'on_list'=>True,
	   			'on_view'=>True
	   	);	   	
	   	$config['links']['view']=array(
	   			'label'=>'View',
	   			'title'=>'View',
	   			'on_list'=>True,
	   			'on_view'=>True
	   	);
	   	
	   	
	   	
	   	$fields['class_intent_rel_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'number',
	   			'field_value'=>'auto_increment',
	   			
	   			//pour l'affichage
	   			'on_add'=>'hidden',
	   			'on_edit'=>'hidden',
	   			'on_list'=>'show',
	   			'on_view'=>'hidden',
	   	);
	   
	   	$fields['class_intent_rel_classification_id']=array(
	   			'field_title'=>'Classification',
	   			'field_type'=>'number',
	   			'field_value'=>'normal',
	   			'field_size'=>11,
	   			'mandatory'=>' mandatory ',
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'classification;class_paper_id',//the reference table and the field to be displayed
	   			'compute_result'=>'no',
	   			'on_add'=>'hidden',
	   			'on_edit'=>'hidden',
	   			'on_list'=>'hidden',
	   			'on_view'=>'hidden'
	   	);
	   	
		$fields['class_intent_rel_relation_id']=array(
	   			'field_title'=>'Relation',
	   			'field_type'=>'number',
	   			'field_value'=>'normal',
	   			'field_size'=>11,
	   			'mandatory'=>' mandatory ',
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'ref_intent_relation;ref_value',//the reference table and the field to be displayed
				'compute_result'=>'no',
				'on_add'=>'enabled',
				'on_edit'=>'enabled',
				'on_list'=>'show'
		);
		
		$fields['class_intent_rel_intent1']=array(
				'field_title'=>'Intent 1',
				'field_type'=>'number',
				'field_value'=>'normal',
				'field_size'=>11,
				'mandatory'=>' mandatory ',
				'input_type'=>'select',
				'input_select_source'=>'table',
				'input_select_source_type'=>'drill_down',
				'drill_down_type'=>'not_linked',
				'input_select_values'=>'classification_intent;class_intent_intent_id',//the reference table and the field to be displayed
				'compute_result'=>'no',
				'on_add'=>'enabled',
				'on_edit'=>'enabled',
				'on_list'=>'show'
		);
	
		$fields['class_intent_rel_intent2']=array(
				'field_title'=>'Intent 2',
				'field_type'=>'number',
				'field_value'=>'normal',
				'field_size'=>11,
				'mandatory'=>' mandatory ',
				'input_type'=>'select',
				'input_select_source'=>'table',
				'input_select_source_type'=>'drill_down',//drill_down
				'drill_down_type'=>'not_linked',//can not add or delete the child value from here			
				'input_select_values'=>'classification_intent;class_intent_intent_id',//the reference table and the field to be displayed
				'compute_result'=>'no',
				'on_add'=>'enabled',
				'on_edit'=>'enabled',
				'on_list'=>'show'
		);
		
		
		
		$fields['class_intent_rel_note']=array(
				'field_title'=>'Note',
				'field_type'=>'text',
				'field_value'=>'normal',
				'on_add'=>'enabled',
				'on_edit'=>'enabled',
				'field_size'=>500,
				'on_list'=>'show',
				'input_type'=>'textarea'
		);
		
		$fields['class_intent_rel_active']=array(
	   			'field_title'=>'Active',
	   			'field_type'=>'0_1',
	   			'field_value'=>'normal',
				
	   			'on_add'=>'not_set',
	   			'on_edit'=>'not_set',
	   			'on_list'=>'hidden',
				'on_view'=>'hidden'
	   	);
		
	   

	   
	   	$config['fields']=$fields;
	   	
	
	return $config;
	
}