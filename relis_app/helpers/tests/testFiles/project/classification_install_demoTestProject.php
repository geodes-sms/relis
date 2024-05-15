<?php //demoTestProject
function get_classification_demoTestProject()
{
	$reference_tables = array(); //from nowit will worklike this
	$config = array();
	$result['class_action'] = 'override';
	$result['screen_action'] = 'override';
	$result['qa_action'] = 'override';
	$result['project_title'] = 'Demo Test Project';
	$result['project_short_name'] = 'demoTestProject';
	$config['classification']['table_name'] = 'classification';
	$config['classification']['config_id'] = 'classification';
	$config['classification']['table_id'] = 'class_id';
	$config['classification']['table_active_field'] = 'class_active';
	$config['classification']['main_field'] = 'class_paper_id';

	$config['classification']['entity_label'] = 'Classification';
	$config['classification']['entity_label_plural'] = 'Classifications';


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


	$config['classification']['fields']['has_choco'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Has chocolate',
		'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'bool',
		'field_value' => '0_1',
		'field_size' => 1,
		'field_type' => 'int',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '1',
		//'number_of_values'=>'',//a verifier
		'number_of_values' => '1', //tous les Freecategory ont une seule valeur
		//'field_value'=>'normal',

		'mandatory' => ' mandatory ',

		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['temperature'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Temperature',
		'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'real',
		//'number_of_values'=>'',//a verifier
		'number_of_values' => '1', //tous les Freecategory ont une seule valeur
		//'field_value'=>'normal',

		'mandatory' => ' mandatory ',

		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['start'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Start date',
		// 'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'text',
		'input_type' => 'date',
		//'number_of_values'=>'',//a verifier
		'number_of_values' => '1', //tous les Freecategory ont une seule valeur
		//'field_value'=>'normal',

		'mandatory' => ' mandatory ',

		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['code'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Code',
		'input_type' => 'text',
		'field_size' => 10,
		'field_type' => 'text',
		//'number_of_values'=>'',//a verifier
		'number_of_values' => '1', //tous les Freecategory ont une seule valeur
		//'field_value'=>'normal',


		'pattern' => '[A-Z]+[0-9]*',

		'initial_value' => 'AB9',
		'field_value' => 'AB9',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$config['classification']['fields']['brand'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Brand',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'number_of_values' => '1', //a verifier

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'Brand',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$reference_tables['Brand']['ref_name'] = 'Brand';
	$initial_values = array();
	array_push($initial_values, "Ferrero");
	array_push($initial_values, "Ghirardelli");
	array_push($initial_values, "Godiva");
	array_push($initial_values, "Hersheys");
	array_push($initial_values, "Leonidas");
	array_push($initial_values, "Lindt");
	array_push($initial_values, "Nestle");
	if (empty($reference_tables['Brand']['values'])) {
		$reference_tables['Brand']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['Brand']['values'])) {
				array_push($reference_tables['Brand']['values'], $value);
			}
		}
	}


	$config['classification']['fields']['cocoa_origin'] = array(
		'category_type' => 'StaticCategory',
		'field_title' => 'Cocoa origin',
		'field_type' => 'text',
		//'field_value'=>'normal',
		'number_of_values' => '0', // a  verifier
		'field_size' => 20,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Cote dIvoire' => "Cote dIvoire",
			'Indonesia' => "Indonesia",
			'Ghana' => "Ghana",
			'Nigeria' => "Nigeria",
			'Cameroon' => "Cameroon",
			'Brazil' => "Brazil",
			'Ecuador' => "Ecuador",
			'Mexico' => "Mexico",
			'Dominican Republic' => "Dominican Republic",
			'Peru' => "Peru",
		),
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$config['classification']['fields']['cocoa_level'] = array(
		'category_type' => 'StaticCategory',
		'field_title' => 'Cocoa level',
		'field_type' => 'text',
		//'field_value'=>'normal',
		'number_of_values' => '1', // a  verifier
		'mandatory' => ' mandatory ',
		'field_size' => 20,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'35%' => "35%",
			'40%' => "40%",
			'45%' => "45%",
			'50%' => "50%",
			'55%' => "55%",
			'60%' => "60%",
			'65%' => "65%",
			'70%' => "70%",
			'75%' => "75%",
			'80%' => "80%",
			'90%' => "90%",
			'95%' => "95%",
			'100%' => "100%",
		),
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$config['classification']['fields']['types'] = array(
		'category_type' => 'StaticCategory',
		'field_title' => 'Types',
		'field_type' => 'text',
		//'field_value'=>'normal',
		'number_of_values' => '0', // a  verifier
		'field_size' => 20,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Raw' => "Raw",
			'Dark' => "Dark",
			'Milk' => "Milk",
			'White' => "White",
			'Baking' => "Baking",
			'Modeling' => "Modeling",
			'Organic' => "Organic",
			'Compound' => "Compound",
			'Couverture' => "Couverture",
			'Ruby' => "Ruby",
		),
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$config['variety']['table_name'] = 'variety';
	$config['variety']['table_id'] = 'variety_id';
	$config['variety']['table_active_field'] = 'variety_active';
	$config['variety']['main_field'] = 'variety';
	$config['variety']['order_by'] = 'variety_id ASC ';


	$config['variety']['reference_title'] = 'Variety';
	$config['variety']['reference_title_min'] = 'Variety';

	$config['variety']['entity_label_plural'] = 'Variety';
	$config['variety']['entity_label'] = 'Variety';


	$config['variety']['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit ',
		'on_list' => False,
		'on_view' => True
	);

	$config['variety']['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);

	$config['variety']['fields']['variety_id'] = array(
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
	$config['variety']['fields']['parent_field_id'] = array(
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

	$config['variety']['fields']['variety'] = array(
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Variety',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'number_of_values' => '1', //a verifier

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'variety',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$reference_tables['variety']['ref_name'] = 'variety';
	$initial_values = array();
	array_push($initial_values, "Bitter");
	array_push($initial_values, "Bittersweet");
	array_push($initial_values, "Semi-sweet");
	array_push($initial_values, "Sweet");
	if (empty($reference_tables['variety']['values'])) {
		$reference_tables['variety']['values'] = $initial_values;
	} else {
		foreach ($initial_values as $key => $value) {
			if (!in_array($value, $reference_tables['variety']['values'])) {
				array_push($reference_tables['variety']['values'], $value);
			}
		}
	}


	$config['variety']['fields']['level1'] = array(
		'category_type' => 'DependentDynamicCategory',
		'field_title' => 'Level 1',
		'field_type' => 'int',
		'field_size' => 11,
		//	'field_value'=>'normal',
		'number_of_values' => '1',

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'cocoa_level', //� corriger seul les category sur le root sont support�s pour le moment
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'compute_result' => 'no',
		'on_list' => 'show'
	);



	$config['variety']['fields']['level2'] = array(
		'category_type' => 'DependentDynamicCategory',
		'field_title' => 'Level 2',
		'field_type' => 'int',
		'field_size' => 11,
		//	'field_value'=>'normal',
		'number_of_values' => '1',

		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'cocoa_level', //� corriger seul les category sur le root sont support�s pour le moment
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'compute_result' => 'no',
		'on_list' => 'show'
	);



	$config['variety']['fields']['variety_active'] = array(
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
	$config['variety']['operations'] = array();

	$config['classification']['fields']['variety'] = array(
		'category_type' => 'WithMultiValues',
		'field_title' => 'Variety',
		'field_type' => 'int',
		'field_size' => 11,
		//'field_value'=>'normal',
		'number_of_values' => '2', //a verifier


		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'variety',
		'input_select_key_field' => 'parent_field_id',
		'input_select_source_type' => 'drill_down',
		//'number_of_values'=>'*',//a verifier
		'on_add' => 'drill_down',
		'on_edit' => 'drill_down',
		'compute_result' => 'no',
		'on_list' => 'show'
	);


	$config['classification']['fields']['venue'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Venue',
		'input_type' => 'text',
		'field_size' => 100,
		'field_type' => 'text',
		//'number_of_values'=>'',//a verifier
		'number_of_values' => '1', //tous les Freecategory ont une seule valeur
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
		'field_size' => 4,
		'field_type' => 'int',
		//'number_of_values'=>'',//a verifier
		'number_of_values' => '1', //tous les Freecategory ont une seule valeur
		//'field_value'=>'normal',

		'mandatory' => ' mandatory ',

		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$config['classification']['fields']['citation'] = array(
		'category_type' => 'FreeCategory',
		'field_title' => 'Number of citations',
		'input_type' => 'text',
		'field_size' => 20,
		'field_type' => 'int',
		//'number_of_values'=>'1',//a verifier
		'number_of_values' => '1', //tous les Freecategory ont une seule valeur
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
		'number_of_values' => '1', //tous les Freecategory ont une seule valeur
		//'field_value'=>'normal',


		'pattern' => '',

		'initial_value' => '',
		'field_value' => '',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	// end project specific area
	$config['classification']['fields']['user_id'] = array(
		'field_title' => 'Classified by',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => active_user_id(),
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name',
		'mandatory' => ' mandatory ',

		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);

	$config['classification']['fields']['classification_time'] = array(
		'field_title' => 'Classification time',
		'field_type' => 'time',
		'default_value' => 'CURRENT_TIMESTAMP',
		'field_value' => bm_current_time('Y-m-d H:i:s'),
		'field_size' => 20,
		'mandatory' => ' mandatory ',

		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'on_view' => 'hidden'
	);

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

	//QA area


	$qa = array();
	$qa['cutt_off_score'] = '5';
	$qa['questions'] = array();
	array_push(
		$qa['questions'],
		array(
			'title' => "Is the paper about chocolate?",
		)
	);
	array_push(
		$qa['questions'],
		array(
			'title' => "Is the chocolate brand available?",
		)
	);
	array_push(
		$qa['questions'],
		array(
			'title' => "Is there a validation of the methodology?",
		)
	);
	$qa['responses'] = array();
	array_push(
		$qa['responses'],
		array(
			'title' => "Yes",
			'score' => "3",
		)
	);
	array_push(
		$qa['responses'],
		array(
			'title' => "Bearly",
			'score' => "1.5",
		)
	);
	array_push(
		$qa['responses'],
		array(
			'title' => "No",
			'score' => "0",
		)
	);
	$result['qa'] = $qa;

	//QA area


	//SCREENING area


	$screening = array();
	$screening['review_per_paper'] = '2';
	$screening['conflict_type'] = 'ExclusionCriteria';
	$screening['conflict_resolution'] = 'Unanimity';
	$screening['validation_assigment_mode'] = 'Normal';
	$screening['validation_percentage'] = '10';
	$screening['exclusion_criteria'] = array();
	array_push($screening['exclusion_criteria'], "EC1: Too short ");
	array_push($screening['exclusion_criteria'], "EC2: Not abour chocolate");
	$screening['source_papers'] = array();
	array_push($screening['source_papers'], "Google Scholar");
	array_push($screening['source_papers'], "Chocolate DB");
	$screening['source_papers'] = array();
	array_push($screening['source_papers'], "Google Scholar");
	array_push($screening['source_papers'], "Chocolate DB");
	$screening['phases'] = array();
	array_push(
		$screening['phases'],
		array(
			'title' => "Title",
			'description' => "Screen by title",
			'fields' => 'Title|',
		)
	);

	$result['screening'] = $screening;

	//SCREENING area

	//REPORTING
	$report = array();
	$report['year']['type'] = 'simple';
	$report['year']['title'] = 'Papers per year';
	$report['year']['id'] = 'year';
	$report['year']['link'] = 'false';
	$report['year']['values']['field'] = 'year';
	$report['year']['values']['style'] = 'select';
	$report['year']['values']['title'] = 'Year';
	$charts = array();
	array_push($charts, "line");
	$report['year']['chart'] = $charts;
	$report['year_venue']['type'] = 'compare';
	$report['year_venue']['title'] = 'Venue per year';
	$report['year_venue']['id'] = 'year_venue';
	$report['year_venue']['link'] = 'false';
	$report['year_venue']['values']['field'] = 'venue';
	$report['year_venue']['values']['style'] = 'select';
	$report['year_venue']['values']['title'] = 'Venue';
	$report['year_venue']['reference']['field'] = 'year';
	$report['year_venue']['reference']['style'] = 'select';
	$report['year_venue']['reference']['title'] = 'Year';
	$charts = array();
	array_push($charts, "bar");
	$report['year_venue']['chart'] = $charts;
	$report['year_citation']['type'] = 'compare';
	$report['year_citation']['title'] = 'Citations per year';
	$report['year_citation']['id'] = 'year_citation';
	$report['year_citation']['link'] = 'false';
	$report['year_citation']['values']['field'] = 'citation';
	$report['year_citation']['values']['style'] = 'select';
	$report['year_citation']['values']['title'] = 'Number of citations';
	$report['year_citation']['reference']['field'] = 'year';
	$report['year_citation']['reference']['style'] = 'select';
	$report['year_citation']['reference']['title'] = 'Year';
	$charts = array();
	array_push($charts, "bar");
	$report['year_citation']['chart'] = $charts;
	$report['has_choco']['type'] = 'simple';
	$report['has_choco']['title'] = 'Has chocolate';
	$report['has_choco']['id'] = 'has_choco';
	$report['has_choco']['link'] = 'false';
	$report['has_choco']['values']['field'] = 'has_choco';
	$report['has_choco']['values']['style'] = 'select';
	$report['has_choco']['values']['title'] = 'Has chocolate';
	$charts = array();
	array_push($charts, "pie");
	$report['has_choco']['chart'] = $charts;
	$report['temperature']['type'] = 'simple';
	$report['temperature']['title'] = 'Temperature';
	$report['temperature']['id'] = 'temperature';
	$report['temperature']['link'] = 'false';
	$report['temperature']['values']['field'] = 'temperature';
	$report['temperature']['values']['style'] = 'select';
	$report['temperature']['values']['title'] = 'Temperature';
	$charts = array();
	array_push($charts, "pie");
	$report['temperature']['chart'] = $charts;
	$report['year_brand']['type'] = 'compare';
	$report['year_brand']['title'] = 'Brand per year';
	$report['year_brand']['id'] = 'year_brand';
	$report['year_brand']['link'] = 'false';
	$report['year_brand']['values']['field'] = 'brand';
	$report['year_brand']['values']['style'] = 'select';
	$report['year_brand']['values']['title'] = 'Brand';
	$report['year_brand']['reference']['field'] = 'year';
	$report['year_brand']['reference']['style'] = 'select';
	$report['year_brand']['reference']['title'] = 'Year';
	$charts = array();
	array_push($charts, "line");
	$report['year_brand']['chart'] = $charts;
	$report['level_origin']['type'] = 'compare';
	$report['level_origin']['title'] = 'Cocoa level per origin';
	$report['level_origin']['id'] = 'level_origin';
	$report['level_origin']['link'] = 'false';
	$report['level_origin']['values']['field'] = 'cocoa_level';
	$report['level_origin']['values']['style'] = 'select';
	$report['level_origin']['values']['title'] = 'Cocoa level';
	$report['level_origin']['reference']['field'] = 'cocoa_origin';
	$report['level_origin']['reference']['style'] = 'select';
	$report['level_origin']['reference']['title'] = 'Cocoa origin';
	$charts = array();
	array_push($charts, "bar");
	$report['level_origin']['chart'] = $charts;
	$report['level_types']['type'] = 'compare';
	$report['level_types']['title'] = 'Cocoa types per level';
	$report['level_types']['id'] = 'level_types';
	$report['level_types']['link'] = 'false';
	$report['level_types']['values']['field'] = 'types';
	$report['level_types']['values']['style'] = 'select';
	$report['level_types']['values']['title'] = 'Types';
	$report['level_types']['reference']['field'] = 'cocoa_origin';
	$report['level_types']['reference']['style'] = 'select';
	$report['level_types']['reference']['title'] = 'Cocoa origin';
	$charts = array();
	array_push($charts, "bar");
	$report['level_types']['chart'] = $charts;
	$result['report'] = $report;
	//REPORTING

	return $result;
}
