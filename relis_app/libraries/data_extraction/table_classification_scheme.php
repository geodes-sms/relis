<?php
function get_classification_scheme()
{

	$config['table_name'] = 'classification_scheme';
	$config['table_id'] = 'scheme_id';
	$config['table_active_field'] = 'scheme_active'; //to detect deleted records
	$config['reference_title'] = 'Classification scheme';
	$config['reference_title_min'] = 'Field';


	$config['order_by'] = 'scheme_parent, scheme_order ASC '; //mettre la valeur à mettre dans la requette

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



	$fields['scheme_label'] = array(
		'field_title' => 'Label',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 90,
		'mandatory' => ' mandatory ',

		'on_add' => 'hidden',
		'on_edit' => 'disabled',
		'on_list' => 'show'
	);
	$fields['scheme_title'] = array(
		'field_title' => 'Title',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 90,
		'mandatory' => ' mandatory ',

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$fields['scheme_category'] = array(
		'field_title' => 'Category',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'hidden',
		'on_edit' => 'disabled',
		'on_list' => 'show',
		'field_size' => 10,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'free' => "Free",
			'static' => "Static list",
			'dynamic' => "Dynamic list"
		)
	);
	$fields['scheme_parent'] = array(
		'field_title' => 'Parent',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 90,
		'on_add' => 'hidden',
		'on_edit' => 'disabled',
		'on_list' => 'show'
	);


	$fields['scheme_mandatory'] = array(
		'field_title' => 'Mandatory',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);




	$fields['scheme_type'] = array(
		'field_title' => 'Type',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 10,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'int' => "Int",
			'string' => "String",
			'text' => "Text",
			'boolean' => "Boolean",
			'real' => "Real",
			'date' => "Date",
			'color' => "Color",
		)
	);

	$fields['scheme_size'] = array(
		'field_title' => 'Field size',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);

	$fields['scheme_source'] = array(
		'field_title' => 'Source',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 90,
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);

	$fields['scheme_source_main_field'] = array(
		'field_title' => 'Source main field',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 90,
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);

	$fields['scheme_number_of_values'] = array(
		'field_title' => 'Number of values',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 2,
		'default' => '1',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);



	$fields['scheme_order'] = array(
		'field_title' => 'Order',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 2,
		'mandatory' => ' mandatory ',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
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