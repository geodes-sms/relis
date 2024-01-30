<?php
function get_modeltransformation()
{

	$config['table_name'] = 'modeltransformation';
	$config['table_id'] = 'mt_id';
	$config['table_active_field'] = 'mt_active'; //to detect deleted records
	$config['reference_title'] = 'Model transformations';
	$config['reference_title_min'] = 'Model transformation ';

	//list view
	$config['order_by'] = 'mt_name ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'mt_name'; // separer les champs par virgule

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit model transformations',
		'on_list' => True,
		'on_view' => True
	);

	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);

	$fields['mt_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden'
	);


	$fields['mt_name'] = array(
		'field_title' => 'Name',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 50,
		'mandatory' => ' mandatory '
	);

	$fields['mt_language'] = array(
		'field_title' => 'Language',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 50,
		'mandatory' => '  '
	);

	$fields['mt_sourceLang'] = array(
		'field_title' => 'Source Language',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 50,
		'mandatory' => '  '
	);

	$fields['mt_targetLang'] = array(
		'field_title' => 'Target Lanuage',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 50,
		'mandatory' => '  '
	);
	$fields['mt_domain'] = array(
		'field_title' => 'Domain',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 50,
		'mandatory' => '  '
	);

	$fields['mt_scope'] = array(
		'field_title' => 'Scope',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 20,
		'mandatory' => '  ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'' => 'Select...',
			'exogenous' => "Exogenous",
			'inplace' => "Inplace",
			'outplace' => "Outplace"

		)
	);


	$fields['mt_isHOT'] = array(
		'field_title' => 'Is HOT',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 20,
		'mandatory' => '  ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'' => 'Select...',
			'1' => "Yes",
			'0' => "No"
		)
	);

	$fields['mt_isBidrectional'] = array(
		'field_title' => 'Is Bidrectional',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 20,
		'mandatory' => '  ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'' => 'Select...',
			'1' => "Yes",
			'0' => "No"
		)
	);

	$fields['mt_isImplementationAvailable'] = array(
		'field_title' => 'Implementation Available',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 20,
		'mandatory' => '  ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'' => 'Select...',
			'1' => "Yes",
			'0' => "No"
		)
	);


	$fields['mt_isIndustrial'] = array(
		'field_title' => 'Is Industrial',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'field_size' => 20,
		'mandatory' => '  ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'' => 'Select...',
			'1' => "Yes",
			'0' => "No"
		)
	);


	$fields['mt_paperId'] = array(
		'field_title' => 'Author',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 20,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'author;author_name' //the reference table and the field to be displayed
	);

	$fields['mt_note'] = array(
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => '',
		'field_size' => 200,
		'mandatory' => '  ',
		'input_type' => 'textarea'
	);

	$fields['mt_active'] = array(
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