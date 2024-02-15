<?php

/*
	The function creates a configuration array with various settings for managing config in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with config.
		- table_id: The primary key field for the config table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing config.
		- reference_title_min: A shorter version of the reference title.
*/
function get_configuration()
{

	$config['table_name'] = 'config';
	$config['table_id'] = 'config_id';
	$config['table_active_field'] = 'config_active'; //to detect deleted records
	$config['reference_title'] = 'Configuration';
	$config['reference_title_min'] = 'Configuration';

	/*
		The configuration also includes settings for the list view:
			- order_by: The sorting order for the configs in the list view.
			- links: An array defining links for editing, viewing, deleting configs.
			- The configuration includes a fields array, which defines the fields of the config table.
	*/
	$config['order_by'] = 'config_id ASC '; //mettre la valeur à mettre dans la requette
	//	$config['search_by']='config_id';// separer les champs par virgule

	//  $config['links']['add_child']="users/user_usergroup;Add user";


	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit',
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
		'on_list' => False,
		'on_view' => False
	);
	$fields['config_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden'
	);


	$fields['config_type'] = array(
		'field_title' => 'Configuration type',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'initial_value' => 'default',
		'field_size' => 100,
		'mandatory' => ' mandatory '
	);

	$fields['editor_url'] = array(
		'field_title' => 'Editor location(url)',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 100,
		'mandatory' => ' mandatory '
	);
	$fields['editor_generated_path'] = array(
		'field_title' => 'Editor workspace',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 100
	);

	$fields['csv_field_separator'] = array(
		'field_title' => 'CSV  separator for import',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 2,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(';' => ';', ',' => ','),
		'initial_value' => ';',
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
	);
	$fields['csv_field_separator_export'] = array(
		'field_title' => 'CSV separator for export',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 2,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(';' => ';', ',' => ','),
		'initial_value' => ',',
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['screening_screening_conflict_resolution'] = array(
		'field_title' => 'Screening comfict type',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 50,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array('Unanimity' => 'Unanimity', 'Majority' => 'Majority'),
		'initial_value' => 'Unanimity',
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['screening_conflict_type'] = array(
		'field_title' => 'Screening comfict type',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 50,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array('IncludeExclude' => 'Inclusion - exclusion', 'ExclusionCriteria' => 'Exclusion criteria'),
		'initial_value' => 'IncludeExclude',
		'compute_result' => 'no',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['import_papers_on'] = array(
		'field_title' => 'import papers activated',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'initial_value' => 1,
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['assign_papers_on'] = array(
		'field_title' => 'assign papers activated',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'initial_value' => 1,
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['screening_on'] = array(
		'field_title' => 'Screening activated',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'initial_value' => 1,
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['screening_result_on'] = array(
		'field_title' => 'Screening result activated',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'initial_value' => 1,
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$fields['screening_validation_on'] = array(
		'field_title' => 'Screening validation activated',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'initial_value' => 1,
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);



	$fields['classification_on'] = array(
		'field_title' => 'Classification activated',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 1,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
		'initial_value' => 1,
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['config_active'] = array(
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