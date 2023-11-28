<?php
function get_classification_scheme()
{

	$config['table_name'] = 'classification_scheme';
	$config['table_id'] = 'scheme_id';
	$config['table_active_field'] = 'scheme_active'; //to detect deleted records
	$config['reference_title'] = 'Fields';
	$config['reference_title_min'] = 'Field';


	$config['order_by'] = 'field_order ASC '; //mettre la valeur à mettre dans la requette

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit classification',
		'on_list' => True,
		'on_view' => True
	);
	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);

	$config['links']['delete'] = array(
		'label' => 'Delete',
		'title' => 'Delete',
		'on_list' => True,
		'on_view' => True
	);


	$fields['scheme_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',

		//pour l'affichage
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$fields['field_label'] = array(
		'field_title' => 'field_label',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 50,
		'mandatory' => ' mandatory ',

		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show'
	);
	$fields['field_title'] = array(
		'field_title' => 'field_title',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 100,
		'mandatory' => ' mandatory ',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$fields['field_type'] = array(
		'field_title' => 'field_type',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 1,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'text' => "text",
			'number' => "number"
		)
	);


	$fields['field_value'] = array(
		'field_title' => 'field_value',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden',
		'field_size' => 1,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'number' => "normal",
			'text' => "auto_increment",
			'text' => "active_user"
		)
	);

	$fields['input_type'] = array(
		'field_title' => 'input_type',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 1,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'text' => "text",
			'select' => "select",
			'textarea' => "textarea"
		)
	);
	$fields['input_select_source'] = array(
		'field_title' => 'input_select_source',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'' => "text",
			'array' => "array",
			'table' => "table",
			'yes_no' => "Yes - No"
		)
	);



	$fields['input_select_values'] = array(
		'field_title' => 'input_select_values',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 200,

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$fields['field_size'] = array(
		'field_title' => 'field_size',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 5,

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['field_order'] = array(
		'field_title' => 'Order',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 2,
		'mandatory' => ' mandatory ',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['mandatory'] = array(
		'field_title' => 'mandatory',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'' => "No",
			'mandatory' => "Yes"
		)
	);

	$fields['compute_result'] = array(
		'field_title' => 'compute_result',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'' => "No",
			'yes' => "Yes"
		)
	);

	$fields['result_graph'] = array(
		'field_title' => 'result_graph',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'no_chart' => "no_chart",
			'pie_chart' => "pie_chart",
			'bar_chart' => "bar_chart"

		)
	);
	$fields['on_list'] = array(
		'field_title' => 'on_list',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'hidden' => "Hide",
			'show' => "Show"
		)
	);


	$fields['scheme_active'] = array(
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