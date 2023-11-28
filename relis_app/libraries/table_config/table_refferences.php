<?php

function get_refference($table, $title)
{

	$config['table_name'] = $table;
	$config['table_id'] = 'ref_id';
	$config['table_active_field'] = 'ref_active'; //to detect deleted records
	$config['reference_title'] = $title;
	$config['reference_title_min'] = $title;

	//list view
	$config['order_by'] = 'ref_value ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'ref_value'; // separer les champs par virgule

	$config['links']['edit'] = array(
		'label' => 'Edit ' . $title,
		'title' => 'Edit ',
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

	$fields['ref_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden'
	);


	$fields['ref_value'] = array(
		'field_title' => 'Value',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 100,
		'mandatory' => ' mandatory '
	);

	$fields['ref_desc'] = array(
		'field_title' => 'Description',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 240,
		'input_type' => 'textarea'
	);



	$fields['ref_active'] = array(
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