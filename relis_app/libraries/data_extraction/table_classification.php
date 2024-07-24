<?php
function get_classification()
{

	$config['table_name'] = 'classification';
	$config['table_id'] = 'class_id';
	$config['table_active_field'] = 'class_active'; //to detect deleted records
	$config['reference_title'] = 'Classifications';
	$config['reference_title_min'] = 'Classification';


	//Concerne l'affichage

	$config['order_by'] = 'class_id ASC '; //mettre la valeur à mettre dans la requette
	//$config['search_by']='class_year';// separer les champs par virgule
	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit classification',
		'on_list' => False,
		'on_view' => True
	);
	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);



	$fields['class_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',

		//pour l'affichage
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$fields['class_paper_id'] = array(
		'field_title' => 'Paper',
		'field_type' => 'number',
		'field_value' => 'normal',
		'input_type' => 'select', //select
		'input_select_source' => 'table',
		//'input_select_values'=>'papers;title',//the reference table and the field to be displayed
		'input_select_values' => 'papers;CONCAT_WS(" - ",bibtexKey,title)', //the reference table and the field to be displayed
		'field_size' => 11,
		'mandatory' => ' mandatory ',


		//pour l'affichage
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'

	);

	$fields['class_name'] = array(
		'field_title' => 'Transformation name',
		'field_type' => 'text',
		'field_value' => 'normal',
		'mandatory' => ' mandatory ',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 500,
		'on_list' => 'show',
		'input_type' => 'text'
	);

	$fields['class_domain'] = array(
		'field_title' => 'Domain',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'ref_domain;ref_value', //the reference table and the field to be displayed
		'compute_result' => 'yes',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['class_language'] = array(
		'field_title' => 'Transformation Language',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'ref_language;ref_value', //the reference table and the field to be displayed
		'compute_result' => 'yes',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['class_sourceLang'] = array(
		'field_title' => 'Source language',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'ref_language;ref_value', //the reference table and the field to be displayed
		'compute_result' => 'yes',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$fields['class_targetLang'] = array(
		'field_title' => 'Target language',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'ref_language;ref_value', //the reference table and the field to be displayed
		'compute_result' => 'yes',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);



	$fields['class_scope'] = array(
		'field_title' => 'Scope',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		//'mandatory'=>' mandatory',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal', //drill_down
		'input_select_values' => 'classification_scope;class_scope_scope_id', //the reference table and the field to be displayed
		'input_select_key_field' => 'class_scope_classification_id',
		'number_of_values' => '*',
		'compute_result' => 'no',
		'on_add' => 'enabled',
		//not_set for drill_down
		'on_edit' => 'enabled',
		//not_set for drill_down
		'on_list' => 'hidden', //for  number of values this must be hidden on list unless ther is an error while getting list from database
		'multi-select' => 'Yes'

	);

	$fields['class_isIndustrial'] = array(
		'field_title' => 'Industrial',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$fields['class_isHOT'] = array(
		'field_title' => 'HOT',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$fields['class_isBiderectional'] = array(
		'field_title' => 'Bidirectional',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$fields['class_implementationAvailable'] = array(
		'field_title' => 'Implementation Available',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$fields['class_intent'] = array(
		'field_title' => 'Intent',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down', //drill_down
		'input_select_values' => 'classification_intent;class_intent_intent_id', //the reference table and the field to be displayed
		'input_select_key_field' => 'class_intent_classification_id',
		'number_of_values' => '*',
		'compute_result' => 'no',
		'on_add' => 'not_set',
		//not_set for drill_down
		'on_edit' => 'not_set',
		//not_set for drill_down
		'on_list' => 'not_set' //for  number of values this must be hidden on list unless ther is an error while getting list from database
	);

	$fields['class_intent_relation'] = array(
		'field_title' => 'Intent relation',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down', //drill_down
		'input_select_values' => 'classification_intent_relation;class_intent_rel_relation_id', //the reference table and the field to be displayed
		'input_select_key_field' => 'class_intent_rel_classification_id',
		'number_of_values' => '*',
		'compute_result' => 'no',
		'on_add' => 'not_set',
		//not_set for drill_down
		'on_edit' => 'not_set',
		//not_set for drill_down
		'on_list' => 'hidden' //for  number of values this must be hidden on list unless ther is an error while getting list from database
	);

	$fields['class_note'] = array(
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 500,
		'on_list' => 'show',
		'input_type' => 'textarea'
	);
	$fields['class_date'] = array(
		'field_title' => 'Date',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'field_size' => 20,
		'on_list' => 'hidden',
		'on_view' => 'hidden',
		'input_type' => 'date'
	);

	$fields['class_color'] = array(
		'field_title' => 'Color',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'field_size' => 20,
		'on_list' => 'hidden',
		'on_view' => 'hidden',
		'input_type' => 'color'
	);
	$fields['class_active'] = array(
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