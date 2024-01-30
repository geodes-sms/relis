<?php
function get_exclusion()
{

	$config['table_name'] = 'exclusion';
	$config['table_id'] = 'exclusion_id';
	$config['table_active_field'] = 'exclusion_active'; //to detect deleted records
	$config['reference_title'] = 'Paper exclusions';
	$config['reference_title_min'] = 'Paper exclusion';


	//Concerne l'affichage

	$config['order_by'] = 'exclusion_id DESC '; //mettre la valeur à mettre dans la requette
	//$config['search_by']='class_year';// separer les champs par virgule
	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit exclusion',
		'on_list' => True,
		'on_view' => True
	);
	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);



	$fields['exclusion_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',

		//pour l'affichage
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$fields['exclusion_paper_id'] = array(
		'field_title' => 'Paper',
		'field_type' => 'number',
		'field_value' => 'normal',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'papers;CONCAT_WS(" - ",bibtexKey,title)',
		//the reference table and the field to be displayed
		'field_size' => 11,
		'mandatory' => ' mandatory ',


		//pour l'affichage
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show'

	);


	$fields['exclusion_criteria'] = array(
		'field_title' => 'Exclusion criteria',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'ref_exclusioncrieria;ref_value',
		//the reference table and the field to be displayed

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['exclusion_note'] = array(
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 200,
		'input_type' => 'textarea',


		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);

	$fields['exclusion_by'] = array(
		'field_title' => 'Excluded by',
		'field_type' => 'number',
		'field_value' => 'active_user',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		//the reference table and the field to be displayed
		'on_add' => 'hidden',
		'on_edit' => 'not_set',
		'on_list' => 'show'
	);
	$fields['exclusion_time'] = array(
		'field_title' => 'Time',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 200,
		'input_type' => 'date',


		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_list' => 'show'
	);


	$fields['exclusion_active'] = array(
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