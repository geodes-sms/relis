<?php

/*
	The function creates a configuration array with various settings for managing venues in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with venues.
		- table_id: The primary key field for the venue table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- reference_title: The title used for referencing venues.
		- reference_title_min: A shorter version of the reference title.
		- Etc.
*/
function get_venue()
{

	$config['table_name'] = 'venue';
	$config['table_id'] = 'venue_id';
	$config['table_active_field'] = 'venue_active'; //to detect deleted records
	$config['reference_title'] = 'Venues';
	$config['reference_title_min'] = 'Venue';

	/*
		The configuration also includes settings for the list view:
			- order_by: The sorting order for the venues in the list view.
			- links: An array defining links for editing, viewing, deleting, etc. venues.
			- The configuration includes a fields array, which defines the fields of the venue table.
	  */
	$config['order_by'] = 'venue_abbreviation ASC '; //mettre la valeur à mettre dans la requette
	$config['search_by'] = 'venue_abbreviation,venue_fullName'; // separer les champs par virgule

	$config['links']['edit'] = array(
		'label' => 'Edit',
		'title' => 'Edit Venue',
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

	$fields['venue_id'] = array(
		'field_title' => '#',
		'field_type' => 'number',
		'field_value' => 'auto_increment',
		'on_add' => 'hidden',
		'on_edit' => 'hidden',
		'on_view' => 'hidden',
		'on_list' => 'show'
	);


	$fields['venue_abbreviation'] = array(
		'field_title' => 'Abbreviation',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 10,
		'mandatory' => ' mandatory '
	);

	$fields['venue_fullName'] = array(
		'field_title' => 'Full name',
		'field_type' => 'text',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show',
		'field_size' => 200,
		'mandatory' => ' mandatory ',
		'input_type' => 'textarea'
	);

	$fields['venue_year'] = array(
		'field_title' => 'Year',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'show'
	);
	$fields['venue_volume'] = array(
		'field_title' => 'Volume',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);

	$fields['venue_totalNumPapers'] = array(
		'field_title' => 'Papers number',
		'field_type' => 'number',
		'field_value' => 'normal',
		'on_add' => 'enabled',
		'on_edit' => 'enabled',
		'on_list' => 'hidden'
	);


	$fields['venue_active'] = array(
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