<?php
/* ReLiS - A Tool for conducting systematic literature reviews and mapping studies.
 * Copyright (C) 2018  Eugene Syriani
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * --------------------------------------------------------------------------
 *
 *  :Author: Brice Michel Bigendako
 */

/*
	This function returns a configuration array for managing configuration in a system. 
	The function creates a configuration array with various settings for managing configuration in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with configuration.
		- table_id: The primary key field for the configuration table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the configurations in the list view.
		- The configuration includes a fields array, which defines the fields of the table.
		- etc.
*/
function get_configuration()
{

	$config['config_id'] = 'config';
	$config['table_name'] = table_name('config');
	$config['table_id'] = 'config_id';
	$config['table_active_field'] = 'config_active'; //to detect deleted records
	$config['main_field'] = 'config_type';

	$config['entity_label'] = 'Configuration';
	$config['entity_label_plural'] = 'Configurations';

	//list view
	$config['order_by'] = 'config_id ASC '; //mettre la valeur à mettre dans la requette




	$fields['config_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'
	);


	$fields['config_type'] = array(
		'field_title' => 'Configuration type',
		'field_type' => 'text',
		'field_value' => 'default',
		'field_size' => 100,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);

	$fields['editor_url'] = array(
		'field_title' => 'Editor URL',
		'field_type' => 'text',
		'field_value' => 'default',
		'field_size' => 100,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	$fields['editor_generated_path'] = array(
		'field_title' => 'Editor workspace location',
		'field_type' => 'text',
		'field_value' => 'default',
		'field_size' => 100,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);

	$fields['csv_field_separator'] = array(
		'field_title' => 'CSV  separator for import',
		'field_type' => 'text',
		'field_value' => ';',
		'default_value' => ';',
		'field_size' => 2,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(';' => ';', ',' => ',')
	);
	$fields['csv_field_separator_export'] = array(
		'field_title' => 'CSV separator for export',
		'field_type' => 'text',
		'field_value' => ',',
		'default_value' => ',',
		'field_size' => 2,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(';' => ';', ',' => ',')
	);

	$fields['screening_screening_conflict_resolution'] = array(
		'field_title' => 'Screening conflict resolution mode',
		'field_type' => 'text',
		'field_value' => 'Unanimity',
		'default_value' => 'Unanimity',
		'field_size' => 50,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array('Unanimity' => 'Unanimity', 'Majority' => 'Majority'),

	);

	$fields['screening_conflict_type'] = array(
		'field_title' => 'Conflict criteria',
		'field_type' => 'text',
		'field_value' => 'IncludeExclude',
		'field_value' => 'IncludeExclude',
		'field_size' => 50,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array('IncludeExclude' => 'Inclusion - exclusion', 'ExclusionCriteria' => 'Exclusion criteria'),
		'initial_value' => 'IncludeExclude',

	);

	$fields['import_papers_on'] = array(
		'field_title' => 'Import papers enabled',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '1',
	);

	$fields['assign_papers_on'] = array(
		'field_title' => 'Paper assignment enabled',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);

	$fields['screening_on'] = array(
		'field_title' => 'Screening enabled',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);

	$fields['screening_result_on'] = array(
		'field_title' => 'Screening result enabled',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);
	$fields['screening_validation_on'] = array(
		'field_title' => 'Screening validation activated',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);



	$fields['classification_on'] = array(
		'field_title' => 'Classification activated',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '0',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);

	$fields['source_papers_on'] = array(
		'field_title' => 'Enable source field',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);



	$fields['search_strategy_on'] = array(
		'field_title' => 'Enable search strategy field',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);

	$fields['key_paper_prefix'] = array(
		'field_title' => 'Prefix of the paper key',
		'field_type' => 'text',
		'field_value' => 'Paper_',
		'default_value' => 'Paper_',
		'field_size' => 20,
		'input_type' => 'text',
		'mandatory' => '  '
	);

	$fields['key_paper_serial'] = array(
		'field_title' => 'Key paper serial',
		'field_type' => 'int',
		'field_value' => '1',
		'default_value' => '1',
		'field_size' => 10,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);

	$fields['validation_default_percentage'] = array(
		'field_title' => 'Default percentage of papers to validate',
		'field_type' => 'int',
		'field_value' => '20',
		'default_value' => '20',
		'field_size' => 3,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	$fields['screening_reviewer_number'] = array(
		'field_title' => 'Number of reviews per paper',
		'field_type' => 'int',
		'field_value' => '2',
		'default_value' => '2',
		'field_size' => 3,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);


	$fields['screening_status_to_validate'] = array(
		'field_title' => 'Screening status to validate',
		'field_type' => 'text',
		'field_value' => 'Excluded',
		'field_value' => 'Excluded',
		'field_size' => 50,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array('Excluded' => 'Excluded', 'Included' => 'Included'),
		'initial_value' => 'Excluded',

	);
	$fields['screening_validator_assignment_type'] = array(
		'field_title' => 'Validation mode',
		'field_type' => 'text',
		'field_value' => 'Normal',
		'field_value' => 'Normal',
		'field_size' => 50,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array('Normal' => 'Normal', 'Veto' => 'Veto', 'Info' => 'Info'),
		'initial_value' => 'Excluded',

	);

	$fields['use_kappa'] = array(
		'field_title' => 'Use kappa',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '0',
		'default_value' => '1',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	)

	;
	$fields['qa_on'] = array(
		'field_title' => 'Quality assessment enabled',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '0',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);

	$fields['qa_open'] = array(
		'field_title' => 'Quality assessment open',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);
	$fields['qa_validation_on'] = array(
		'field_title' => 'Validation enabled',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);
	$fields['qa_validation_default_percentage'] = array(
		'field_title' => 'Default percentage of papers to validate',
		'field_type' => 'int',
		'field_value' => '20',
		'default_value' => '20',
		'field_size' => 3,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	$fields['class_validation_on'] = array(
		'field_title' => 'Validation enabled',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '0',
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'input_select_values' => '',
	);
	$fields['class_validation_default_percentage'] = array(
		'field_title' => 'Default percentage of papers to validate',
		'field_type' => 'int',
		'field_value' => '20',
		'default_value' => '20',
		'field_size' => 3,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);
	$fields['qa_cutt_off_score'] = array(
		'field_title' => 'Cut-off score',
		'field_type' => 'real',
		'field_value' => '20',
		'default_value' => '3.2',
		'field_size' => 5,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);

	$fields['list_trim_nbr'] = array(
		'field_title' => 'Paper characters displayed ',
		'field_type' => 'int',
		'field_value' => '80',
		'default_value' => '80',
		'field_size' => 3,
		'input_type' => 'text',
		'mandatory' => ' '
	);

	$fields['config_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);
	$config['fields'] = $fields;


	$config['init_query'][0] = "INSERT INTO `config` 
		(`config_id`, `config_type`, `editor_url`, `editor_generated_path`) VALUES
		(1, 'default', 'http://127.0.0.1:8080/relis/texteditor', 'C:/dslforge_workspace')";

	/*
		The $operations array defines different operations or actions that can be performed on the configurations. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	$operations['configurations'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Configurations values',
		'operation_description' => 'Configurations values',
		'page_title' => 'Settings',

		'page_template' => 'element/display_element_grouped',

		'data_source' => 'get_detail_config',
		'generate_stored_procedure' => True,
		'fields_groups' => array(
			'papers' => array('title' => 'Papers', 'edit' => 'element/edit_element/edit_conf_papers/1'),
			'screen' => array('title' => 'Screening', 'edit' => 'element/edit_element/edit_config_screening/1'),
			'qa' => array('title' => 'Quality Assessment', 'edit' => 'element/edit_element/edit_config_qa/1'),
			'class' => array('title' => 'Classification', 'edit' => 'element/edit_element/edit_config_class/1'),
			'dsl' => array('title' => 'Project Config Editor', 'edit' => 'element/edit_element/edit_config_dsl/1'),
		),
		'fields' => array(


			'import_papers_on' => array('group' => 'papers'),
			'csv_field_separator' => array('group' => 'papers'),
			'csv_field_separator_export' => array('group' => 'papers'),
			'key_paper_prefix' => array('group' => 'papers'),
			'key_paper_serial' => array('group' => 'papers'),
			'list_trim_nbr' => array('group' => 'papers'),
			'source_papers_on' => array('group' => 'papers'),
			'search_strategy_on' => array('group' => 'papers'),


			'screening_on' => array('group' => 'screen'),
			'screening_result_on' => array('group' => 'screen'),
			'assign_papers_on' => array('group' => 'screen'),
			'screening_reviewer_number' => array('group' => 'screen'),
			'screening_conflict_type' => array('group' => 'screen'),
			'screening_screening_conflict_resolution' => array('group' => 'screen'),
			'use_kappa' => array('group' => 'screen'),
			'screening_validation_on' => array('group' => 'screen'),
			'screening_validator_assignment_type' => array('group' => 'screen'),
			'validation_default_percentage' => array('group' => 'screen'),

			'qa_on' => array('group' => 'qa'),
			'qa_cutt_off_score' => array('group' => 'qa'),
			'qa_validation_on' => array('group' => 'qa'),
			'qa_validation_default_percentage' => array('group' => 'qa'),

			'class_validation_on' => array('group' => 'class'),
			'class_validation_default_percentage' => array('group' => 'class'),

			'editor_url' => array('group' => 'dsl'),
			'editor_generated_path' => array('group' => 'dsl'),





		),


		'top_links' => array(
			/*'edit'=>array(
									  'label'=>'',
									  'title'=>'Edit',
									  'icon'=>'edit',
									  'url'=>'element/edit_element/edit_configuration/~current_element~',
							  ),*/
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => '',
				'url' => 'home',
			),



		),
	);



	$operations['config_papers'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Information for  papers',
		'operation_description' => 'Settings  for  papers',
		'page_title' => 'Settings - Papers',
		'data_source' => 'get_detail_config',
		'generate_stored_procedure' => False,

		'fields' => array(


			'import_papers_on' => array(),
			'csv_field_separator' => array(),
			'csv_field_separator_export' => array(),
			'key_paper_prefix' => array(),
			'key_paper_serial' => array(),
			'list_trim_nbr' => array(),
			'source_papers_on' => array(),
			'search_strategy_on' => array(),


		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_conf_papers/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => '',
				'url' => 'home',
			),



		),
	);



	$operations['config_dsl'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Information for  DSL',
		'operation_description' => 'DSL configuration',
		'page_title' => 'DSL configuration',
		'data_source' => 'get_detail_config',
		'generate_stored_procedure' => False,

		'fields' => array(


			'editor_url' => array(),
			'editor_generated_path' => array(),


		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_config_dsl/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => '',
				'url' => 'home',
			),



		),
	);
	$operations['config_qa'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Information for  QA',
		'operation_description' => 'QA configuration',
		'page_title' => 'QA configuration',
		'data_source' => 'get_detail_config',
		'generate_stored_procedure' => False,

		'fields' => array(
			//'config_id'=>array('mandatory'=>'','field_state'=>'hidden'),
			'qa_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'qa_open' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'qa_cutt_off_score' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'qa_validation_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'qa_validation_default_percentage' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),


		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_config_qa/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => '',
				'url' => 'home',
			),



		),
	);

	$operations['config_class'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Information for  Classification',
		'operation_description' => 'Classification configuration',
		'page_title' => 'Classification configuration',
		'data_source' => 'get_detail_config',
		'generate_stored_procedure' => False,

		'fields' => array(

			'classification_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'class_validation_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'class_validation_default_percentage' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),


		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_config_class/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => '',
				'url' => 'home',
			),



		),
	);


	$operations['config_screening'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Screening configuration',
		'operation_description' => 'Screening configuration',
		'page_title' => 'Screening configuration',
		'data_source' => 'get_detail_config',
		'generate_stored_procedure' => False,

		'fields' => array(


			'screening_on' => array(),
			'screening_validation_on' => array(),
			'screening_result_on' => array(),
			'screening_reviewer_number' => array(),
			'screening_conflict_type' => array(),
			'screening_screening_conflict_resolution' => array(),
			'use_kappa' => array(),
			'validation_default_percentage' => array(),
			'screening_validator_assignment_type' => array(),


		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_config_screening/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => '',
				'url' => 'home',
			),



		),
	);

	$operations['edit_configuration'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit configuration',
		'operation_description' => 'Edit configuration',
		'page_title' => 'Edit configuration ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/display_element/configurations/1',
		'data_source' => 'get_detail_config',
		'db_save_model' => 'update_config',

		'generate_stored_procedure' => True,

		'fields' => array(
			'config_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'config_type' => array('mandatory' => 'mandatory', 'field_state' => 'hidden'),
			'editor_url' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'editor_generated_path' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'csv_field_separator' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'csv_field_separator_export' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_screening_conflict_resolution' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_conflict_type' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'import_papers_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'assign_papers_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_result_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_validation_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'classification_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'source_papers_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'search_strategy_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'key_paper_prefix' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'key_paper_serial' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'validation_default_percentage' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_status_to_validate' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_validator_assignment_type' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),

		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);

	$operations['edit_conf_papers'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit configuration for papers',
		'operation_description' => 'Edit configuration for papers',
		'page_title' => 'Edit papers settings ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/display_element/configurations/1',
		'data_source' => 'get_detail_config',
		'db_save_model' => 'update_config_paper',

		'generate_stored_procedure' => True,

		'fields' => array(
			'config_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'import_papers_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'csv_field_separator' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'csv_field_separator_export' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'key_paper_prefix' => array('mandatory' => ' ', 'field_state' => 'enabled'),
			'key_paper_serial' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'list_trim_nbr' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'source_papers_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'search_strategy_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),

		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);

	$operations['edit_config_screening'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit screening configurations',
		'operation_description' => 'Edit screening configurations',
		'page_title' => 'Edit screening settings ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/display_element/configurations/1',
		'data_source' => 'get_detail_config',
		'db_save_model' => 'update_config_screening',

		'generate_stored_procedure' => True,

		'fields' => array(
			'config_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'screening_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_result_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'assign_papers_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_reviewer_number' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_conflict_type' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_screening_conflict_resolution' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'use_kappa' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_validation_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'screening_validator_assignment_type' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'validation_default_percentage' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),

		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);
	$operations['edit_config_qa'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit QA configurations',
		'operation_description' => 'Edit QA configurations',
		'page_title' => 'Edit Quality Assessment settings ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/display_element/configurations/1',
		'data_source' => 'get_detail_config',
		'db_save_model' => 'update_config_qa',

		'generate_stored_procedure' => True,

		'fields' => array(
			'config_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'qa_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			//'qa_open'=>array('mandatory'=>'mandatory','field_state'=>'enabled'),
			'qa_cutt_off_score' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'qa_validation_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'qa_validation_default_percentage' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),


		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);
	$operations['edit_config_class'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit classification configurations',
		'operation_description' => 'Edit classification configurations',
		'page_title' => 'Edit classification settings ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/display_element/configurations/1',
		'data_source' => 'get_detail_config',
		'db_save_model' => 'update_config_class',
		'db_save_model' => 'update_config_class',

		'generate_stored_procedure' => True,

		'fields' => array(
			'config_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			//	'classification_on'=>array('mandatory'=>'','field_state'=>'enabled'),
			'class_validation_on' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'class_validation_default_percentage' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),


		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);

	$operations['edit_config_dsl'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit DSL configurations',
		'operation_description' => 'Edit DSL configurations',
		'page_title' => 'Edit DSL configurations ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/display_element/configurations/1',
		'data_source' => 'get_detail_config',
		'db_save_model' => 'update_config_dsl',

		'generate_stored_procedure' => True,

		'fields' => array(
			'config_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'editor_url' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'editor_generated_path' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),

		),

		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'close',
				'url' => 'home',
			)

		),

	);


	$config['operations'] = $operations;
	return $config;

}