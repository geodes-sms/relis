<?php 
function get_operation() {
	
		$config['table_name']='operations';
	   	$config['table_id']='operation_id';
	   	$config['table_active_field']='operation_active';//to detect deleted records
	   	$config['reference_title']='Operations';
	   	$config['reference_title_min']='Operation';
	   	
	   	
	   	//Concerne l'affichage
	   		  
	   	$config['order_by']='operation_id DESC '; //mettre la valeur à mettre dans la requette
	   	$config['links']['edit']=array(
	   			'label'=>'Edit',
	   			'title'=>'Edit assignment',
	   			'on_list'=>False,
	   			'on_view'=>False
	   	);	   	
	   	$config['links']['view']=array(
	   			'label'=>'View',
	   			'title'=>'View',
	   			'on_list'=>False,
	   			'on_view'=>False
	   	);
	   	
	   	
	   	
	   	$fields['operation_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'number',
	   			'field_value'=>'auto_increment',
	   			
	   			//pour l'affichage
	   			'on_add'=>'hidden',
	   			'on_edit'=>'hidden',
	   			'on_list'=>'show',
	   			'on_view'=>'hidden',
	   	);
	   	
	   	$fields['operation_code']=array( 
	   			'field_title'=>'Paper excluded',
	   			'field_type'=>'text',
	   			'field_value'=>'normal',
	   			'mandatory'=>'mandatory',
	   			'field_size'=>20,
	   			'initial_value'=> '01',
	   			'on_add'=>'enabled',
	   			'on_edit'=>'not_set',
	   			'on_list'=>'hidden'
	   	);
		
		$fields['operation_type']=array(
	   			'field_title'=>'Operation type',
	   			'field_type'=>'text',
	   			'field_value'=>'normal',
	   			'field_size'=>20,
	   			'input_type'=>'select',
	   			'input_select_source'=>'array',
	   			'input_select_values'=>array('import_paper'=>'Import papers',
												'assign_papers' => 'Assign papers for screening',
												'assign_papers_validation' => 'Assign papers for screening validation'
				),
				'initial_value'=>'import_paper',
				'on_add'=>'enabled',
				'on_edit'=>'disabled',
				'on_list'=>'show',
				'on_view'=>'show',
	   	);
		
		$fields['operation_desc']=array(
				'field_title'=>'Description',
				'field_type'=>'text',
				'field_value'=>'normal',
				'field_size'=>200,
				'input_type'=>'text',	
		
				'on_add'=>'enabled',
				'on_edit'=>'enabled',
				'on_list'=>'show'
		);
		
		$fields['user_id']=array(
	   			'field_title'=>'User',
	   			'field_type'=>'number',
	   			'field_value'=>'active_user',
	   			'field_size'=>11,
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'users;user_name',//the reference table and the field to be displayed
				'on_add'=>'hidden',
				'on_edit'=>'not_set',
				'on_list'=>'show'
	   	);
		
		
		
		$fields['operation_time']=array(
	   			'field_title'=>'Time',
	   			'field_type'=>'text',
	   			'field_value'=>'normal',
	   			'field_size'=>30,
	   			'input_type'=>'date',			
				
	   			'on_add'=>'not_set',
	   			'on_edit'=>'not_set',
	   			'on_list'=>'show'
	   	);
		
		$fields['operation_active']=array(
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