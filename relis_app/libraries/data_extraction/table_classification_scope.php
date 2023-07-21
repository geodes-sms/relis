<?php
function get_classification_scope()
{

	$config['table_name'] = 'classification_scope';
	$config['table_id'] = 'class_scope_id';
	$config['table_active_field'] = 'class_scope_active'; //to detect deleted records
	$config['reference_title'] = 'Scope';
	$config['reference_title_min'] = 'Scope';


	//Concerne l'affichage

	$config['order_by'] = 'class_scope_id ASC '; //mettre la valeur à mettre dans la requette
	//$config['search_by']='class_year';// separer les champs par virgule
	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit scope',
		'on_list' => True,
		'on_view' => True
	);
	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);



	$fields['class_scope_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',

		//pour l'affichage
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$fields['class_scope_classification_id'] = array(
		'field_title' => 'Classification',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'classification;class_paper_id', //the reference table and the field to be displayed
		'compute_result' => 'no',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);

	$fields['class_scope_scope_id'] = array(
		'field_title' => 'Scope',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',

		'input_select_values' => 'ref_scope;ref_value', //the reference table and the field to be displayed
		//'input_select_source'=>'array',
		//'input_select_values'=>array('1'=>'Brice','2'=>'Brice1','3'=>'Brice2','4'=>'Brice3'),

		//the reference table and the field to be displayed
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);




	$fields['class_scope_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',

		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);




	$config['fields'] = $fields;


	return $config;

}