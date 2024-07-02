<?php

/*
	The function creates a configuration array with various settings for managing classification_languages. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with classification_languages.
		- table_id: The primary key field for the classification_language table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing classification_languages.
		- reference_title_min: A shorter version of the reference title.
*/
function get_classification_language()
{

	$config['table_name'] = 'classification_language';
	$config['table_id'] = 'class_lang_id';
	$config['table_active_field'] = 'class_lang_active'; //to detect deleted records
	$config['reference_title'] = 'Classification language';
	$config['reference_title_min'] = 'Classification language';


	/*
	  The configuration also includes settings for the list view:
		  - order_by: The sorting order in the list view.
		  - links: An array defining links for editing, viewing classification_language.
		  - The configuration includes a fields array, which defines the fields of the classification_language table.
  	*/
	$config['order_by'] = 'class_lang_id ASC '; //mettre la valeur à mettre dans la requette
	//$config['search_by']='class_year';// separer les champs par virgule
	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit classification language',
		'on_list' => True,
		'on_view' => True
	);
	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);



	$fields['class_lang_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',

		//pour l'affichage
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$fields['class_lang_name'] = array(
		'field_title' => 'Language name',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'ref_tool_or_language;ref_value', //the reference table and the field to be displayed
		'compute_result' => 'yes',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$fields['class_lang_category'] = array(
		'field_title' => 'Context',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'compute_result' => 'yes',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'object oriented' => 'Object Oriented',
			'Procedural' => 'Procedural',
			'Mixed' => 'Mixed'
		),
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$fields['class_lang_comment'] = array(
		'field_title' => 'Comment for language',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 200,
		'input_type' => 'textarea',


		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);


	$fields['class_lang_active'] = array(
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