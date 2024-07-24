<?php

/*
	The function creates a configuration array with various settings for managing paper in a system. Here are the key components of the configuration:
		- table_name: The name of the table associated with paper.
		- table_id: The primary key field for the paper table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing paper.
		- reference_title_min: A shorter version of the reference title.
		- Etc.
*/
function get_papers()
{

	$config['table_name'] = 'paper';
	$config['table_id'] = 'id';
	$config['table_active_field'] = 'paper_active';
	$config['reference_title'] = 'Papers';
	$config['reference_title_min'] = 'Paper';

	$config['entity_title']['add'] = 'Add new paper';
	$config['entity_title']['edit'] = 'Edit paper';
	$config['entity_title']['view'] = 'Paper detail';
	$config['entity_title']['list'] = 'List of papers';


	/*
		The configuration also includes settings for the list view:
			- order_by: The sorting order for the papers in the list view.
			- search_by: The fields to be used for searching papers, separated by commas
			- links: An array defining links for adding, editing, viewing, deleting, etc. papers.
			- The configuration includes a fields array, which defines the fields of the paper table.
	*/
	$config['order_by'] = ' id ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'bibtexKey,title,preview,abstract'; // separer les champs par virgule

	$config['links']['add'] = array(
		'label' => 'Add Papers',
		'title' => 'Add ',
		//'url'=> 'paper/bibler_add_paper',
		'on_list' => True,
		'on_view' => True
	);

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit paper',
		//	'url'=>'paper/bibler_edit_paper/',
		'on_list' => True,
		'on_view' => True
	);

	$config['links']['view'] = array(
		'url' => 'data_extraction/display_paper/',
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


	$config['links']['add_child'] = array(
		'url' => 'classification/class_paper_id', //part of the url mandatory for this case
		'label' => 'Add classification',
		'title' => 'Add a classification to the paper',
		'on_list' => False,
		'on_view' => True
	);

	$fields['id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden'
	);

	$fields['bibtexKey'] = array(
		'field_title' => 'Key',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 30,
		'mandatory' => ' mandatory ',
		'extra_class' => '',
		'place_holder' => '',
	);

	$fields['title'] = array(
		'field_title' => 'Title',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
		'mandatory' => ' mandatory ',
		'on_list' => 'show',
		'input_type' => ''
	);


	$fields['preview'] = array(
		'field_title' => 'Preview',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
		'on_list' => 'hidden',
		'input_type' => 'textarea'
	);
	$fields['bibtex'] = array(
		'field_title' => 'Bibtex',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
		'on_list' => 'hidden',
		'input_type' => 'textarea'
	);
	$fields['abstract'] = array(
		'field_title' => 'Abstract',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 1000,
		'on_list' => 'hidden',
		'input_type' => 'textarea'
	);
	$fields['doi'] = array(
		'field_title' => 'Link',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
		'on_list' => 'hidden',
		'input_type' => ''
	);

	$fields['screening_status'] = array(
		'field_title' => 'Screening status',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 20,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Pending' => 'Pending',
			'In review' => 'In review',
			'Included' => 'Included',
			'Excluded' => 'Excluded',
			'Excluded_QA' => 'Excluded in QA',
			'In conflict' => 'In conflict',
			'Resolved included' => 'Resolved included',
			'Resolved excluded' => 'Resolved excluded'
		),
		'initial_value' => 'Pending',
		'on_add' => 'hidden',
		'on_edit' => 'not_set',
		'on_list' => 'hidden'
	);

	$fields['classification_status'] = array(
		'field_title' => 'Screening status',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 20,
		'input_type' => 'select',
		'input_select_source' => 'array',
		'input_select_values' => array(
			'Waiting' => 'Waiting',
			'To classify' => 'To classify',
			'Classified' => 'Classified'
		),
		'initial_value' => 'Waiting',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_list' => 'hidden'
	);

	$fields['venueId'] = array(
		'field_title' => 'Venue',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden',
		'on_view' => 'hidden',
		'field_size' => 11,
		'mandatory' => '  ',
		'input_select_source_type' => 'drill_down',
		'drill_down_type' => 'not_linked',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'venue;venue_fullName' //the reference table and the field to be displayed
	);

	$fields['authors'] = array(
		'field_title' => 'Author',
		'field_type' => 'number',
		'field_value' => 'normal',
		'field_size' => 11,
		'mandatory' => '  ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_source_type' => 'drill_down', //drill_down
		'input_select_values' => 'paper_author;authorId', //the reference table and the field to be displayed
		'input_select_key_field' => 'paperId',
		'number_of_values' => '*',
		'compute_result' => 'no',
		'on_add' => 'not_set',
		//not_set for drill_down
		'on_edit' => 'not_set',
		//not_set for drill_down
		'on_list' => 'hidden', //for  number of values this must be hidden on list unless ther is an error while getting list from database
		'category_type' => 'WithMultiValues',
		'multi-select' => 'Yes'

	);
	$fields['paper_excluded'] = array(
		'field_title' => 'Paper excluded',
		'field_type' => 'text',
		'field_value' => '0_1',
		'field_size' => 2,
		'input_type' => 'select',
		'input_select_source' => 'yes_no',
		'initial_value' => 0,
		'input_select_values' => '',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden'
	);
	$fields['operation_code'] = array(
		//used  papers are imported in bulk in order to reverse the operation 
		'field_title' => 'Operation code',
		'field_type' => 'text',
		'field_value' => 'normal',
		'field_size' => 20,
		'initial_value' => '01',
		'on_add' => 'not_set',
		'on_edit' => 'not_set',
		'on_view' => 'hidden',
		'on_list' => 'hidden'
	);
	$fields['paper_active'] = array(
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