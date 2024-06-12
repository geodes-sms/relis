<?php
function get_papers()
{

	$config['table_name'] = 'paper';
	$config['table_id'] = 'id';
	$config['table_active_field'] = 'paper_active'; //to detect deleted records
	$config['reference_title'] = 'Papers';
	$config['reference_title_min'] = 'Paper';

	//list view
	$config['order_by'] = ' id ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'bibtexKey,title,preview'; // separer les champs par virgule

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit paper',
		'on_list' => True,
		'on_view' => True
	);

	$config['links']['view'] = array(
		'url' => 'paper/view_paper',
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
		'field_title' => 'Abstract',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
		'on_list' => 'hidden',
		'input_type' => 'textarea'
	);
	$fields['bibtex'] = array(
		'field_title' => 'Preview',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
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

	$fields['venueId'] = array(
		'field_title' => 'Venue',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'hidden',
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