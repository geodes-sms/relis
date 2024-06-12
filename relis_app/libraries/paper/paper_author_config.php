<?php 
/*
	The function creates a configuration array with various settings for managing paper-author in a system. Here are the key components of the configuration:
		- table_name: The name of the table associated with paper-author.
		- table_id: The primary key field for the paperauthor table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing paperauthor.
		- reference_title_min: A shorter version of the reference title.
*/
function get_paper_author() {
	
		$config['table_name']='paperauthor';
	   	$config['table_id']='paperauthor_id';
	   	$config['table_active_field']='paperauthor_active';//to detect deleted records
	   	$config['reference_title']='Paper author';
	   	$config['reference_title_min']='Paper author';
	   	
	   	
	   	/*
			The configuration also includes settings for the list view:
				- order_by: The sorting order for the paper-authors in the list view.
				- links: An array defining links for editing and viewing paper-authors.
				- The configuration includes a fields array, which defines the fields of the paperauthor table.
		*/
	   		  
	   	$config['order_by']='paperauthor_id ASC '; //mettre la valeur à mettre dans la requette
	   
	   	$config['links']['edit']=array(
	   			'label'=>'Edit',
	   			'title'=>'Edit',
	   			'on_list'=>True,
	   			'on_view'=>True
	   	);	   	
	   	$config['links']['view']=array(
	   			'label'=>'View',
	   			'title'=>'View',
	   			'on_list'=>True,
	   			'on_view'=>True
	   	);
	   	
	   	
	   	
	   	$fields['paperauthor_id']=array(
	   			'field_title'=>'#',
	   			'field_type'=>'number',
	   			'field_value'=>'auto_increment',
	   			
	   			//pour l'affichage
	   			'on_add'=>'hidden',
	   			'on_edit'=>'hidden',
	   			'on_list'=>'show',
	   			'on_view'=>'hidden',
	   	);
	   
	   	$fields['paperId']=array(
	   			'field_title'=>'Paper',
	   			'field_type'=>'number',
	   			'field_value'=>'normal',
	   			'field_size'=>11,
	   			'mandatory'=>' mandatory ',
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
	   			'input_select_values'=>'papers;id',//the reference table and the field to be displayed
	   			'compute_result'=>'no',
	   			'on_add'=>'hidden',
	   			'on_edit'=>'hidden',
	   			'on_list'=>'hidden',
	   			'on_view'=>'hidden'
	   	);
	   	
		$fields['authorId']=array(
	   			'field_title'=>'Author',
	   			'field_type'=>'number',
	   			'field_value'=>'normal',
	   			'field_size'=>11,
	   			'mandatory'=>' mandatory ',
	   			'input_type'=>'select',
	   			'input_select_source'=>'table',
				'input_select_source_type'=>'drill_down',
				'drill_down_type'=>'not_linked',
	   			'input_select_values'=>'author;author_name',//the reference table and the field to be displayed
				'compute_result'=>'no',
				'on_add'=>'enabled',
				'on_edit'=>'enabled',
				'on_list'=>'show'
		);
	
		
		
	   	
		$fields['paperauthor_active']=array(
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