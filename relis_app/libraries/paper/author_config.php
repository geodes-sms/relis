<?php
/*
	The get_author() function generates a configuration array for managing authors
	The function creates a configuration array with various settings for managing authors in a system. Here are the key components of the configuration:
		- table_name: The name of the table associated with authors.
		- table_id: The primary key field for the author table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing authors.
		- reference_title_min: A shorter version of the reference title.
*/
function get_author()
{

	$config['table_name'] = 'author';
	$config['table_id'] = 'author_id';
	$config['table_active_field'] = 'author_active'; //to detect deleted records
	$config['reference_title'] = 'Authors';
	$config['reference_title_min'] = 'Author';

	//list view
	/*
		The configuration also includes settings for the list view:
			- order_by: The sorting order for the authors in the list view.
			- search_by: The fields to be used for searching authors, separated by commas
			- links: An array defining links for editing and viewing, deleting authors.
			- The configuration includes a fields array, which defines the fields of the author table.
	*/
	$config['order_by'] = ' author_name ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'author_name,author_desc'; // separer les champs par virgule

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit author',
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

	$fields['author_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_list' => 'show',
		'on_view' => 'hidden'
	);

	$fields['author_name'] = array(
		'field_title' => 'Name',
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

	$fields['author_desc'] = array(
		'field_title' => 'Description',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
		'on_list' => 'show',
		'input_type' => 'textarea'
	);


	$fields['author_picture'] = array(
		'field_title' => 'Picture',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'field_size' => 200,
		'on_list' => 'hidden',
		'input_type' => 'image'
	);

	$fields['author_active'] = array(
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