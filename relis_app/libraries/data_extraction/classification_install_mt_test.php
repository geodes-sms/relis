<?php //mt_test

/*
	defines a configuration array that specifies various settings for classification. 
	It includes configuration for entities such as 'classification', 'goal', 'intent', and 'domain', along with their corresponding fields
*/
function get_classification_mt_test()
{
	$reference_tables = array(); //from nowit will worklike this
	$config = array();
	$result['project_title'] = 'MDE Test';
	$result['project_short_name'] = 'mt_test';
	$config['classification']['table_name'] = 'classification';
	$config['classification']['table_id'] = 'class_id';
	$config['classification']['table_active_field'] = 'class_active';
	$config['classification']['main_field'] = 'class_paper_id';

	$config['classification']['entity_label'] = 'Classifications';
	$config['classification']['entity_label_plural'] = 'Classification';


	$config['classification']['reference_title'] = 'Classifications';
	$config['classification']['reference_title_min'] = 'Classification';

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
		'default_value' => 'auto_increment',

		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$config['classification']['fields']['class_paper_id'] = array(
		'field_title' => 'Paper',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'papers;CONCAT_WS(" - ",bibtexKey,title)',
		'mandatory' => ' mandatory ',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	//project specific area


	$config['classification']['fields']['transformation_name'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Transformation name',
		'input_type' => 'text',
		'field_size' => 100,
		'field_type' => 'text',
		//'number_of_values'=>'1',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',

		'mandatory' => ' mandatory ',

		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['number_trans'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Transformation number',
		'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'int',
		//'number_of_values'=>'1',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',

		'mandatory' => ' mandatory ',

		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['ration'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Ratio',
		'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'real',
		//'number_of_values'=>'1',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',

		'mandatory' => ' mandatory ',

		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['year'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Year',
		'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'int',
		//'number_of_values'=>'0',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',

		'mandatory' => ' mandatory ',

		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['pub_date'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Publication date',
		'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'text',
		'input_type' => 'date',
		//'number_of_values'=>'0',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',


		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['classification']['fields']['venue'] = array(
		'category_type' => 'StaticCategory',
		'field_title' => 'Venue type',
		'field_type' => 'text',
		//'field_value'=>'normal',
		'number_of_values' => '1', // a  verifier
		'field_size' => 20,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Conference' => "Conference",
			'Journal' => "Journal",
			'Book' => "Book",
			'Other' => "Other",
		),
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$config['goal']['table_name'] = 'goal';
	$config['goal']['table_id'] = 'goal_id';
	$config['goal']['table_active_field'] = 'goal_active';
	$config['goal']['main_field'] = 'goal';
	$config['goal']['order_by'] = 'goal_id ASC ';


	$config['goal']['reference_title'] = 'Goal';
	$config['goal']['reference_title_min'] = 'Goal';

	$config['goal']['entity_label_plural'] = 'Goal';
	$config['goal']['entity_label'] = 'Goal';


	$config['goal']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit ',
		'on_list' => False,
		'on_view' => True
	);

	$config['goal']['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);

	$config['goal']['fields']['goal_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment',
		//to clean
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden'
	);
	$config['goal']['fields']['parent_field_id'] = array(
		'category_type' => 'ParentExternalKey',
		'field_title' => 'Parent',
		'field_type' => 'int',
		//'field_value'=>'normal',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'classification',
		//to clean
		'compute_result' => 'no',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);

	$config['goal']['fields']['goal'] = array(
		'category_type' => 'StaticCategory',
		'field_title' => 'Goal',
		'field_type' => 'text',
		//'field_value'=>'normal',
		'number_of_values' => '1', // a  verifier
		'field_size' => 20,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Translation' => "Translation",
			'Reverse engineering' => "Reverse engineering",
			'Synthesis' => "Synthesis",
			'Other' => "Other",
		),
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$config['goal']['fields']['goal_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		//to clean
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['goal']['operations'] = array();

	$config['classification']['fields']['goal'] = array(
		'category_type' => 'WithMultiValues',
		'field_title' => 'Goal',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'number_of_values' => '2', //a verifier


		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'goal',
		'input_select_key_field' => 'parent_field_id',
		'input_select_source_type' => 'normal',
		'multi-select' => 'Yes',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'compute_result' => 'no',
		'on_list' => 'hidden'
	);

	$config['classification']['fields']['domain'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Domain',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'number_of_values' => '0', //a verifier
		'mandatory' => ' mandatory ',

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'Domain',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$reference_tables['Domain']['ref_name'] = 'Domain';
	$initial_values = array();
	array_push($initial_values, "Artificial Intelligence");
	array_push($initial_values, "Collaborative system");
	array_push($initial_values, "Compilation");
	array_push($initial_values, "E-commerce");
	array_push($initial_values, "Any");
	if (empty($reference_tables['Domain']['values'])) {
		$reference_tables['Domain']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Domain']['values'])) {
				array_push($reference_tables['Domain']['values'], $value);
			}
		}
	}


	$config['intent']['table_name'] = 'intent';
	$config['intent']['table_id'] = 'intent_id';
	$config['intent']['table_active_field'] = 'intent_active';
	$config['intent']['main_field'] = 'intent';
	$config['intent']['order_by'] = 'intent_id ASC ';


	$config['intent']['reference_title'] = 'Intent';
	$config['intent']['reference_title_min'] = 'Intent';

	$config['intent']['entity_label'] = 'Intent';
	$config['intent']['entity_label_plural'] = 'Intent';

	$config['intent']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit ',
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
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment',
		//to clean
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden'
	);
	$config['intent']['fields']['parent_field_id'] = array(
		'category_type' => 'ParentExternalKey',
		'field_title' => 'Parent',
		'field_type' => 'int',
		'field_size' => 11,
		//	'field_value'=>'normal',

		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'classification',
		'compute_result' => 'no',
		//to clean
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);

	$config['intent']['fields']['intent'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Intent',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'number_of_values' => '0', //a verifier

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'Intents',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$reference_tables['Intents']['ref_name'] = 'Intents';
	$initial_values = array();
	array_push($initial_values, "Translation");
	array_push($initial_values, "Simulation");
	array_push($initial_values, "Migration");
	array_push($initial_values, "Composition");
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
		'input_type' => 'text',
		'field_size' => 100,
		'field_type' => 'text',
		//'number_of_values'=>'0',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',


		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['intent']['fields']['note'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Note',
		'input_type' => 'text',
		'field_size' => 500,
		'field_type' => 'text',
		'input_type' => 'textarea',
		//'number_of_values'=>'0',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',


		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['intent']['fields']['intent_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['intent']['operations'] = array();

	$config['classification']['fields']['intent'] = array(
		'category_type' => 'WithSubCategories',
		'field_title' => 'Intent',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'number_of_values' => '1', //a verifier

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down',
		'input_select_values' => 'intent',
		'input_select_key_field' => 'parent_field_id',
		'compute_result' => 'no',
		'multi-select' => 'no',
		'on_list' => 'hidden',
		'on_add' => 'not_set',
		'on_edit' => 'not_set'
	);

	$config['intent_relation']['table_name'] = 'intent_relation';
	$config['intent_relation']['table_id'] = 'intent_relation_id';
	$config['intent_relation']['table_active_field'] = 'intent_relation_active';
	$config['intent_relation']['main_field'] = 'intent_relation';
	$config['intent_relation']['order_by'] = 'intent_relation_id ASC ';


	$config['intent_relation']['reference_title'] = 'Intent relation';
	$config['intent_relation']['reference_title_min'] = 'Intent relation';

	$config['intent_relation']['entity_label'] = 'Intent relation';
	$config['intent_relation']['entity_label_plural'] = 'Intent relation';

	$config['intent_relation']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit ',
		'on_list' => False,
		'on_view' => True
	);

	$config['intent_relation']['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);
	$config['intent_relation']['fields']['intent_relation_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment',
		//to clean
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden'
	);
	$config['intent_relation']['fields']['parent_field_id'] = array(
		'category_type' => 'ParentExternalKey',
		'field_title' => 'Parent',
		'field_type' => 'int',
		'field_size' => 11,
		//	'field_value'=>'normal',

		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'classification',
		'compute_result' => 'no',
		//to clean
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);

	$config['intent_relation']['fields']['intent_relation'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Intent relation',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'number_of_values' => '0', //a verifier

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'Relation',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$reference_tables['Relation']['ref_name'] = 'Relation';
	$initial_values = array();
	array_push($initial_values, "Relation 1");
	array_push($initial_values, "Relation2");
	array_push($initial_values, "Relation 3");
	if (empty($reference_tables['Relation']['values'])) {
		$reference_tables['Relation']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Relation']['values'])) {
				array_push($reference_tables['Relation']['values'], $value);
			}
		}
	}


	$config['intent_relation']['fields']['intent_1'] = array(
		'category_type' => 'DependentDynamicCategory',
		'field_title' => 'Intent 1',
		'field_type' => 'int',
		'field_size' => 11,
		//	'field_value'=>'normal',
		'number_of_values' => '0',

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'intent', //à corriger seul les category sur le root sont supportés pour le moment
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'compute_result' => 'no',
		'on_list' => 'show'
	);



	$config['intent_relation']['fields']['intent_2'] = array(
		'category_type' => 'DependentDynamicCategory',
		'field_title' => 'Intent 2',
		'field_type' => 'int',
		'field_size' => 11,
		//	'field_value'=>'normal',
		'number_of_values' => '0',

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'intent', //à corriger seul les category sur le root sont supportés pour le moment
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'compute_result' => 'no',
		'on_list' => 'show'
	);




	$config['intent_relation']['fields']['note'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Note',
		'input_type' => 'text',
		'field_size' => 500,
		'field_type' => 'text',
		'input_type' => 'textarea',
		//'number_of_values'=>'0',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',


		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['intent_relation']['fields']['intent_relation_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['intent_relation']['operations'] = array();

	$config['classification']['fields']['intent_relation'] = array(
		'category_type' => 'WithSubCategories',
		'field_title' => 'Intent relation',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'number_of_values' => '1', //a verifier

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down',
		'input_select_values' => 'intent_relation',
		'input_select_key_field' => 'parent_field_id',
		'compute_result' => 'no',
		'multi-select' => 'no',
		'on_list' => 'hidden',
		'on_add' => 'not_set',
		'on_edit' => 'not_set'
	);


	$config['classification']['fields']['industrial'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Industrial',
		'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'bool',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '1',
		//'number_of_values'=>'1',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',


		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['bidirectional'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Bidirectional',
		'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'bool',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '1',
		//'number_of_values'=>'1',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',


		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['note'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Note',
		'input_type' => 'text',
		'field_size' => 500,
		'field_type' => 'text',
		'input_type' => 'textarea',
		//'number_of_values'=>'1',//a verifier
		'number_of_values' => '1', //tous les ont une seule valeur
		//'field_value'=>'normal',


		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	// end project specific area
	$config['classification']['fields']['class_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',

		//'field_value'=>'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);
	$config['classification']['operations'] = array();
	$result['config'] = $config;

	$result['reference_tables'] = $reference_tables;

	//SCREENING area


	//SCREENING area

	return $result;
}