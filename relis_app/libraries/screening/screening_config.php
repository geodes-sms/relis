<?php

/*
	The function creates a configuration array with various settings for managing screening in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with paper.
		- table_id: The primary key field for the screening table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing screenings.
		- reference_title_min: A shorter version of the reference title.
		- Etc.
*/
function get_screening_set($table = 'screening', $title = 'Screening')
{

	$config['table_name'] = $table;
	$config['table_id'] = 'screening_id';
	$config['table_active_field'] = 'screening_active'; //to detect deleted records
	$config['reference_title'] = $title;
	$config['reference_title_min'] = $title;

	$config['entity_title']['add'] = 'new ' . $title;
	$config['entity_title']['edit'] = 'Edit ' . $title;
	$config['entity_title']['view'] = $title;
	$config['entity_title']['list'] = $title;

	/*
		   The configuration also includes settings for the list view:
			   - order_by: The sorting order for the screenings in the list view.
			   - links: An array defining links for editing, viewing, deleting, etc. screenings.
			   - The configuration includes a fields array, which defines the fields of the screening table.
	   */
	$config['order_by'] = 'screening_id DESC '; //mettre la valeur à mettre dans la requette

	$config['links']['view'] = array(
		'label' => 'View',
		'title' => 'View',
		'on_list' => True,
		'on_view' => True
	);

	$url = ($table == 'screening_validate') ? 'screening/remove_screening_validation/' : 'screening/remove_screening/';
	$config['links']['delete'] = array(
		'url' => $url,
		'label' => 'Cancel',
		'title' => 'Remove screening',
		'on_list' => True,
		'on_view' => True
	);

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit ',
		'url' => 'screening/edit_screen/',
		'on_list' => FALSE,
		'on_view' => FALSE
	);
	$fields['screening_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',

		//pour l'affichage
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden',
	);

	$fields['paper_id'] = array(
		'field_title' => 'Paper',
		'field_type' => 'number',
		'field_value' => 'normal',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'papers;CONCAT_WS(" - ",bibtexKey,title)', //the reference table and the field to be displayed
		'field_size' => 11,
		'mandatory' => ' mandatory ',


		//pour l'affichage
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show'

	);


	$fields['user_id'] = array( // assigned to
		'field_title' => 'Screened by',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'users;user_name', //the reference table and the field to be displayed
		'mandatory' => ' mandatory ',
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_list' => 'show'
	);

	$fields['decision'] = array(
		'field_title' => 'Decision',
		'field_type' => 'text',
		'field_value' => 'active_user',
		'field_size' => 30,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'accepted' => 'Accepted',
			'excluded' => 'Excluded',

		),
		'initial_value' => 'accepted',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$fields['exclusion_criteria'] = array(
		'field_title' => 'Exclusion criteria',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'ref_exclusioncrieria;ref_value', //the reference table and the field to be displayed

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['inclusion_criteria'] = array(
		'field_title' => 'Inclusion criteria',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => ' ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'ref_inclusioncriteria;ref_value', //the reference table and the field to be displayed

		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);


	$fields['note'] = array(
		'field_title' => 'Note',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 200,
		'input_type' => 'textarea',


		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);

	$fields['assignment_id'] = array(
		'field_title' => 'Assigment',
		'field_type' => 'number',
		'field_value' => 'normal',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'assignment_screen;user_id',
		'field_size' => 11,
		'mandatory' => ' mandatory ',


		//pour l'affichage
		'on_add' => 'enabled',
		'on_edit' => 'disabled',
		'on_view' => 'hidden',
		'on_list' => 'hidden'

	);




	$fields['screening_time'] = array(
		'field_title' => 'Time',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 50,
		'input_type' => 'date',


		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'show'
	);

	$fields['screening_active'] = array(
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