<?php 

/*
	The function creates a configuration array with various settings for managing references. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with references.
		- table_id: The primary key field for the references table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing references.
		- reference_title_min: A shorter version of the reference title.
*/
function get_refference($table,$title) {

	$config['table_name']=$table;
	$config['table_id']='ref_id';
	$config['table_active_field']='ref_active';//to detect deleted records
	$config['reference_title']=$title;
	$config['reference_title_min']=$title;
	 
	/*
		The configuration also includes settings for the list view:
			- order_by: The sorting order for the references in the list view.
			- links: An array defining links for adding, editing, viewing, deleting references.
			- The configuration includes a fields array, which defines the fields of the references table.
	*/
	$config['order_by']='ref_value ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by']='ref_value';// separer les champs par virgule
	
	$config['links']['add']=array(
			'label'=>'Add '.$title,
			'title'=>'Add ',
			'on_list'=>True,
			'on_view'=>True
	);
	
	$config['links']['view']=array(
			'label'=>'View',
			'title'=>'View',
			'on_list'=>True,
			'on_view'=>True
	);
	$config['links']['edit']=array(
			'label'=>'Edit '.$title,
			'title'=>'Edit ',
			'on_list'=>True,
			'on_view'=>True
	);

	

	$config['links']['delete']=array(
			'label'=>'Delete',
			'title'=>'Delete',
			'on_list'=>True,
			'on_view'=>True
	);
	 
	$fields['ref_id']=array(
			'field_title'=>'#',
			'field_type'=>'number',
			'field_value'=>'auto_increment',
			'on_add'=>'hidden',
			'on_edit'=>'hidden',
			'on_list'=>'show',
			'on_view'=>'hidden'
	);
	 
	 
	$fields['ref_value']=array(
			'field_title'=>'Value',
			'field_type'=>'text',
			'field_value'=>'normal',
			'on_add'=>'enabled',
			'on_edit'=>'enabled',
			'on_list'=>'show',
			'field_size'=>100,
			'mandatory'=>' mandatory '
	);
	 
	$fields['ref_desc']=array(
			'field_title'=>'Description',
			'field_type'=>'text',
			'field_value'=>'normal',
			'on_add'=>'enabled',
			'on_edit'=>'enabled',
			'on_list'=>'show',
			'field_size'=>240,
			'input_type'=>'textarea'
	);



//
//    $fields['ref_method']=array(
//        'field_title'=>'Method',
//        'field_type'=>'text',
//        'field_value'=>'method',
//        'field_size'=>200,
//        'input_type'=>'select',
//        'input_select_source'=>'array',
//        'input_select_values'=>array(
//            'Automatic' => 'Automatic',
//            'Manual' => 'Manual',
//
//        ),
//        'mandatory'=>' mandatory ',
//
//
//    );
//
//
//
//    $fields['ref_search_query']=array(
//        'field_title'=>'Search Query',
//        'field_type'=>'text',
//        'field_value'=>'normal',
//        'on_add'=>'enabled',
//        'on_edit'=>'enabled',
//        'on_list'=>'show',
//        'field_size'=>240,
//        'input_type'=>'textarea'
//    );
//


    $fields['ref_active']=array(
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