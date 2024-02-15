<?php

/*
	defines a configuration array for classification.
	- The $reference_tables array is initialized to store reference table information.
	- The $config array is initialized to store the overall configuration settings.
*/
function get_classification()
{
	$reference_tables = array();
	//$reference_tables2=array();
	$config = array();
	$result['project_title'] = 'Model transformation';
	$config['classification']['table_name'] = 'classification';
	$config['classification']['table_id'] = 'class_id';
	$config['classification']['table_active_field'] = 'class_active';
	$config['classification']['reference_title'] = 'Classifications';
	$config['classification']['reference_title_min'] = 'Classification';
	$config['classification']['main_field'] = 'class_paper_id';
	$config['classification']['order_by'] = 'class_id ASC ';
	$config['classification']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit classification',
		'on_list' => False,
		'on_view' => True
	);
	$config['classification']['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);
	$config['classification']['fields']['class_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);
	$config['classification']['fields']['class_paper_id'] = array(
		'field_title' => 'Paper',
		'field_type' => 'number',
		'field_value' => 'normal',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'papers;CONCAT_WS(" - ",bibtexKey,title)',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	//project specific area
	$config['classification']['fields']['transformation_name'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Transformation name',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'mandatory' => ' mandatory ',
		'field_size' => 100,
		'input_type' => 'text',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['classification']['fields']['domain'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Domain',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'Domain',
		'compute_result' => 'yes',
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	//$reference_tables[ 'Domain']='Domain';
	$reference_tables['Domain']['ref_name'] = 'Domain';
	$initial_values = array();
	array_push($initial_values, "Artificial Intelligence");
	array_push($initial_values, "Collaborative systeme");
	array_push($initial_values, "Compilation");
	array_push($initial_values, "E-commerce");
	array_push($initial_values, "Anny");
	if (empty($reference_tables['Domain']['values'])) {
		$reference_tables['Domain']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Domain']['values'])) {
				array_push($reference_tables['Domain']['values'], $value);
			}
		}
	}
	$config['classification']['fields']['Goal'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Goal',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'Types of goals',
		'compute_result' => 'yes',
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	//$reference_tables[ 'Types of goals']='Types of goals';
	$reference_tables['Types of goals']['ref_name'] = 'Types of goals';
	$initial_values = array();
	array_push($initial_values, "Research");
	array_push($initial_values, "Production");
	array_push($initial_values, "Implementation");
	array_push($initial_values, "Application test");
	if (empty($reference_tables['Types of goals']['values'])) {
		$reference_tables['Types of goals']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Types of goals']['values'])) {
				array_push($reference_tables['Types of goals']['values'], $value);
			}
		}
	}
	$config['transformation_language']['table_name'] = 'transformation_language';
	$config['transformation_language']['table_id'] = 'transformation_language_id';
	$config['transformation_language']['table_active_field'] = 'transformation_language_active';
	$config['transformation_language']['reference_title'] = 'Transformation language';
	$config['transformation_language']['reference_title_min'] = 'Transformation language';
	$config['transformation_language']['order_by'] = 'transformation_language_id ASC';
	$config['transformation_language']['main_field'] = 'transformation_language';
	$config['transformation_language']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit',
		'on_list' => False,
		'on_view' => True
	);
	$config['transformation_language']['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);
	$config['transformation_language']['fields']['transformation_language_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);
	$config['transformation_language']['fields']['parent_field_id'] = array( // a verifier
		'category_type' => 'ParentExternalKey',
		'field_title' => 'Parent',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'classification', //a verifier
		'compute_result' => 'no',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['transformation_language']['fields']['transformation_language'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Transformation language',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'Language',
		'compute_result' => 'yes',
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	//$reference_tables[ 'Language']='Language';
	$reference_tables['Language']['ref_name'] = 'Language';
	$initial_values = array();
	array_push($initial_values, "Acceleo");
	array_push($initial_values, "Xpand");
	array_push($initial_values, "Java");
	array_push($initial_values, "Code Smith");
	if (empty($reference_tables['Language']['values'])) {
		$reference_tables['Language']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Language']['values'])) {
				array_push($reference_tables['Language']['values'], $value);
			}
		}
	}
	$config['transformation_language']['fields']['transformation_language_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['classification']['fields']['transformation_language'] = array(
		'category_type' => 'WithMultiValues',
		'field_title' => 'Transformation language',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '*',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'multi-select' => 'Yes',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'input_select_key_field' => 'parent_field_id', //a corriger
		'input_select_values' => 'transformation_language',
		'compute_result' => 'no',
		'on_list' => 'hidden'
	);
	$config['source_language']['table_name'] = 'source_language';
	$config['source_language']['table_id'] = 'source_language_id';
	$config['source_language']['table_active_field'] = 'source_language_active';
	$config['source_language']['reference_title'] = 'Source language';
	$config['source_language']['reference_title_min'] = 'Source language';
	$config['source_language']['order_by'] = 'source_language_id ASC';
	$config['source_language']['main_field'] = 'source_language';
	$config['source_language']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit',
		'on_list' => False,
		'on_view' => True
	);
	$config['source_language']['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);
	$config['source_language']['fields']['source_language_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);
	$config['source_language']['fields']['parent_field_id'] = array( // a verifier
		'category_type' => 'ParentExternalKey',
		'field_title' => 'Parent',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'classification', //a verifier
		'compute_result' => 'no',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['source_language']['fields']['source_language'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Source language',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'Language',
		'compute_result' => 'yes',
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	//$reference_tables[ 'Language']='Language';
	$reference_tables['Language']['ref_name'] = 'Language';
	$initial_values = array();
	array_push($initial_values, "Acceleo");
	array_push($initial_values, "Xpand");
	array_push($initial_values, "Java");
	array_push($initial_values, "Code Smith");
	array_push($initial_values, "Xtend");
	array_push($initial_values, "EMF Text");
	if (empty($reference_tables['Language']['values'])) {
		$reference_tables['Language']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Language']['values'])) {
				array_push($reference_tables['Language']['values'], $value);
			}
		}
	}
	$config['source_language']['fields']['source_language_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['classification']['fields']['source_language'] = array(
		'category_type' => 'WithMultiValues',
		'field_title' => 'Source language',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '2',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'multi-select' => 'Yes',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'input_select_key_field' => 'parent_field_id', //a corriger
		'input_select_values' => 'source_language',
		'compute_result' => 'no',
		'on_list' => 'hidden'
	);
	$config['classification']['fields']['target_language'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Transformation language',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'Language',
		'compute_result' => 'yes',
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	//$reference_tables[ 'Language']='Language';
	$reference_tables['Language']['ref_name'] = 'Language';
	$initial_values = array();
	if (empty($reference_tables['Language']['values'])) {
		$reference_tables['Language']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Language']['values'])) {
				array_push($reference_tables['Language']['values'], $value);
			}
		}
	}
	$config['Scope']['table_name'] = 'Scope';
	$config['Scope']['table_id'] = 'Scope_id';
	$config['Scope']['table_active_field'] = 'Scope_active';
	$config['Scope']['reference_title'] = '';
	$config['Scope']['reference_title_min'] = '';
	$config['Scope']['order_by'] = 'Scope_id ASC';
	$config['Scope']['main_field'] = 'Scope';
	$config['Scope']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit',
		'on_list' => False,
		'on_view' => True
	);
	$config['Scope']['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);
	$config['Scope']['fields']['Scope_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);
	$config['Scope']['fields']['parent_field_id'] = array( // a verifier
		'category_type' => 'ParentExternalKey',
		'field_title' => 'Parent',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'classification', //a verifier
		'compute_result' => 'no',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['Scope']['fields']['Scope'] = array(
		'category_type' => 'StaticCategory',
		'field_title' => 'Scope',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '0',
		'number_of_values' => '1',
		'mandatory' => ' mandatory ',
		'field_size' => 10,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Exogenous' => "Exogenous",
			'Inplace' => "Inplace",
			'Outplace' => "Outplace",
		),
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	$config['Scope']['fields']['Scope_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['classification']['fields']['Scope'] = array(
		'category_type' => 'WithMultiValues',
		'field_title' => 'Scope',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '*',
		'mandatory' => ' mandatory ',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'multi-select' => 'Yes',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'input_select_key_field' => 'parent_field_id', //a corriger
		'input_select_values' => 'Scope',
		'compute_result' => 'no',
		'on_list' => 'hidden'
	);
	$config['classification']['fields']['status'] = array(
		'category_type' => 'StaticCategory',
		'field_title' => 'Emplementation Status',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 10,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'done' => "done",
			'pending' => "pending",
			'on going' => "on going",
		),
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	$config['classification']['fields']['Industrial'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Industrial',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 1,
		'input_type' => 'bool',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['classification']['fields']['hot'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Is HOT',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 1,
		'input_type' => 'bool',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['classification']['fields']['note'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 500,
		'input_type' => 'textarea',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['intent']['table_name'] = 'intent';
	$config['intent']['table_id'] = 'intent_id';
	$config['intent']['table_active_field'] = 'intent_active';
	$config['intent']['reference_title'] = 'Intent';
	$config['intent']['reference_title_min'] = 'Intent';
	$config['intent']['order_by'] = 'intent_id ASC';
	$config['intent']['main_field'] = 'intent';
	$config['intent']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit',
		'on_list' => False,
		'on_view' => True
	);
	$config['intent']['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);
	$config['intent']['fields']['intent_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);
	$config['intent']['fields']['parent_field_id'] = array( // a verifier
		'category_type' => 'ParentExternalKey',
		'field_title' => 'Parent',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'classification', //a verifier
		'compute_result' => 'no',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['intent']['fields']['intent'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Intent',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'Intents',
		'compute_result' => 'yes',
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	//$reference_tables[ 'Intents']='Intents';
	$reference_tables['Intents']['ref_name'] = 'Intents';
	$initial_values = array();
	array_push($initial_values, "Intent1");
	array_push($initial_values, "Intent2");
	array_push($initial_values, "Updating");
	array_push($initial_values, "Upgrading Application");
	if (empty($reference_tables['Intents']['values'])) {
		$reference_tables['Intents']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Intents']['values'])) {
				array_push($reference_tables['Intents']['values'], $value);
			}
		}
	}
	$config['intent']['fields']['name_used'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Name used by the author',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 100,
		'input_type' => 'text',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['intent']['fields']['note'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 500,
		'input_type' => 'textarea',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['intent']['fields']['intent_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['classification']['fields']['intent'] = array(
		'category_type' => 'WithMultiValues',
		'field_title' => 'Intent',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '*',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'input_select_key_field' => 'parent_field_id', //a corriger
		'input_select_values' => 'intent',
		'compute_result' => 'no',
		'on_list' => 'hidden'
	);
	$config['relation']['table_name'] = 'relation';
	$config['relation']['table_id'] = 'relation_id';
	$config['relation']['table_active_field'] = 'relation_active';
	$config['relation']['reference_title'] = 'Relation';
	$config['relation']['reference_title_min'] = 'Relation';
	$config['relation']['main_field'] = 'relation';
	$config['relation']['order_by'] = 'relation_id ASC';
	$config['relation']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit',
		'on_list' => False,
		'on_view' => True
	);
	$config['relation']['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);
	$config['relation']['fields']['relation_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);
	$config['relation']['fields']['parent_field_id'] = array( // a verifier
		'category_type' => 'ParentExternalKey',
		'field_title' => 'Parent',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'classification', //a verifier
		'compute_result' => 'no',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['relation']['fields']['relation'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Relation',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'Relations',
		'compute_result' => 'yes',
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	//$reference_tables[ 'Relations']='Relations';
	$reference_tables['Relations']['ref_name'] = 'Relations';
	$initial_values = array();
	array_push($initial_values, "Relation1");
	array_push($initial_values, "Relation2");
	array_push($initial_values, "Relation3");
	if (empty($reference_tables['Relations']['values'])) {
		$reference_tables['Relations']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Relations']['values'])) {
				array_push($reference_tables['Relations']['values'], $value);
			}
		}
	}
	$config['relation']['fields']['Intent1'] = array(
		'category_type' => 'DependentDynamicCategory',
		'field_title' => 'Intent1',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'mandatory' => ' mandatory ',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'intent;intent', //� corriger seul les category sur le root sont support�s pour le moment
		'compute_result' => 'no',
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	$config['relation']['fields']['intent2'] = array(
		'category_type' => 'DependentDynamicCategory',
		'field_title' => 'Intent2',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'mandatory' => ' mandatory ',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'normal',
		'input_select_values' => 'intent;intent', //� corriger seul les category sur le root sont support�s pour le moment
		'compute_result' => 'no',
		'on_list' => 'show',
		'on_add' => 'enabled',
		'on_edit' => 'enabled'
	);
	$config['relation']['fields']['note'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 500,
		'input_type' => 'textarea',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['relation']['fields']['relation_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['classification']['fields']['relation'] = array(
		'category_type' => 'WithSubCategories',
		'field_title' => 'Relation',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down',
		'input_select_key_field' => 'parent_field_id', //a corriger
		'input_select_values' => 'relation',
		'compute_result' => 'no',
		'multi-select' => 'no',
		'on_list' => 'hidden',
		'on_add' => 'not_set',
		'on_edit' => 'not_set'
	);
	$config['classification']['fields']['mail'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Test pattern Email',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'mandatory' => '',
		'field_size' => 100,
		'input_type' => 'text',
		'pattern' => '[^@]+@[^@]+\.[a-zA-Z]{2,6}',
		'pattern_info' => 'Correct email format',
		'initial_value' => 'bbigendako@gmail.com',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['classification']['fields']['test_real'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Test Real',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 10,
		'input_type' => 'real',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['classification']['fields']['test_int'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Test Int',
		'field_type' => 'number',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 10,
		'input_type' => 'int',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['classification']['fields']['test_color'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Test Color',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 11,
		'input_type' => 'color',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['classification']['fields']['test_date'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Test Date',
		'field_type' => 'text',
		'field_value' => 'normal',
		'number_of_values' => '1',
		'field_size' => 100,
		'input_type' => 'date',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	//--end project specific area
	$config['classification']['fields']['class_active'] = array(
		'field_title' => 'Active',
		'field_type' => '0_1',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$result['config'] = $config;
	$result['reference_tables'] = $reference_tables;
	//$result[ 'reference_tables2' ] =$reference_tables2;

	return $result;
}