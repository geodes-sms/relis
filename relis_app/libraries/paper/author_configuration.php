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
	This function returns a configuration array for managing authors in a system. 
	It defines various operations and fields related to authors, including adding, editing, listing, and deleting authors.
	The function creates a configuration array with various settings for managing authors in a system. Here are the key components of the configuration:
		- table_name: The name of the table associated with authors.
		- table_id: The primary key field for the author table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- order_by: The sorting order for the authors in the list view.
		- The configuration includes a fields array, which defines the fields of the author table.
		- etc.
*/
function get_author()
{
	$config['config_id'] = 'author';
	$config['table_name'] = 'author';
	$config['table_id'] = 'author_id';
	$config['table_active_field'] = 'author_active';
	$config['main_field'] = 'author_name';


	$config['entity_label_plural'] = 'Authors';
	$config['entity_label'] = 'Author';

	//list view
	$config['order_by'] = ' author_name ASC '; //mettre la valeur à mettre dans la requette


	$fields['author_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'
	);

	$fields['author_name'] = array(
		'field_title' => 'Name',
		'field_type' => 'text',
		'field_size' => 50,
		'input_type' => 'text',
		'mandatory' => ' mandatory '
	);

	$fields['author_desc'] = array(
		'field_title' => 'Affiliation',
		'field_type' => 'text',
		'field_size' => 200,
		'input_type' => 'text',
		'input_type' => 'textarea'
	);
	
	$fields['author_desc'] = array(
		'field_title' => 'Affiliation',
		'field_type' => 'int',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'affiliation;ref_value',
	);

	$fields['author_picture'] = array(
		'field_title' => 'Picture',
		'field_type' => 'image',
		'input_type' => 'image',
	);
	$fields['paper_nbr'] = array(
		'field_title' => 'Number of papers',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'normal',
		'input_type' => 'text',
		'not_in_db' => True,


	);

	$fields['author_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);
	$config['fields'] = $fields;

	/*
		The $table_views array defines different views for listing authors, including all authors, authors with specific classifications, and first authors.
	*/
	$table_views = array();

	$table_views['view_all_authors'] = array(
		'name' => 'view_all_authors',
		'desc' => '',
		'script' => '
			select A.* , COUNT(Q.paperId) as paper_nbr FROM author A LEFT JOIN paperauthor Q ON (Q.authorId=A.author_id AND Q.paperauthor_active=1) INNER JOIN paper P ON(Q.paperId=P.id AND P.paper_active=1 )  WHERE author_active=1 GROUP BY A.author_id '
	);

	$table_views['view_authors_class'] = array(
		'name' => 'view_authors_class',
		'desc' => '',
		'script' => '
			select A.* , COUNT(Q.paperId) as paper_nbr FROM author A LEFT JOIN paperauthor Q ON (Q.authorId=A.author_id AND Q.paperauthor_active=1) INNER JOIN paper P ON(Q.paperId=P.id AND P.paper_active=1 AND P.classification_status <> \'Waiting\' AND P.paper_excluded=0)  WHERE author_active=1 GROUP BY A.author_id '
	);

	$table_views['view_first_authors'] = array(
		'name' => 'view_first_authors',
		'desc' => '',
		'script' => '
			select A.* , COUNT(Q.paperId) as paper_nbr FROM author A LEFT JOIN paperauthor Q ON (Q.authorId=A.author_id AND Q.paperauthor_active=1 AND Q.author_rank=1 ) INNER JOIN paper P ON(Q.paperId=P.id AND P.paper_active=1)  WHERE author_active=1 GROUP BY A.author_id '
	);

	$table_views['view_first_authors_class'] = array(
		'name' => 'view_first_authors_class',
		'desc' => '',
		'script' => '
			select A.* , COUNT(Q.paperId) as paper_nbr FROM author A LEFT JOIN paperauthor Q ON (Q.authorId=A.author_id AND Q.paperauthor_active=1 AND Q.author_rank=1) INNER JOIN paper P ON(Q.paperId=P.id AND P.paper_active=1 AND P.classification_status <> \'Waiting\' AND P.paper_excluded=0 )  WHERE author_active=1 GROUP BY A.author_id '
	);

	$config['table_views'] = $table_views;

	/*
		The $operations array defines various operations that can be performed on authors, such as adding, editing, listing, 
		and deleting authors. Each operation has its own configuration, including title, description, page template, 
		data source, and field settings.
	*/
	$operations['add_author'] = array(
		'operation_type' => 'Add',
		'operation_title' => 'Add a new author',
		'operation_description' => 'Add a new author',
		'page_title' => 'Add a new author',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',
		'redirect_after_save' => 'element/entity_list/list_authors',
		'db_save_model' => 'add_author',

		'generate_stored_procedure' => True,

		'fields' => array(
			'author_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'author_name' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'author_desc' => array('mandatory' => '', 'field_state' => 'enabled'),
			'author_picture' => array('mandatory' => '', 'field_state' => 'enabled')

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



	$operations['edit_author'] = array(
		'operation_type' => 'Edit',
		'operation_title' => 'Edit author',
		'operation_description' => 'Edit author',
		'page_title' => 'Edit author ',
		'save_function' => 'element/save_element',
		'page_template' => 'general/frm_entity',

		'redirect_after_save' => 'element/entity_list/list_authors',
		'data_source' => 'get_detail_author',
		'db_save_model' => 'update_author',

		'generate_stored_procedure' => True,

		'fields' => array(
			'author_id' => array('mandatory' => '', 'field_state' => 'hidden'),
			'author_name' => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
			'author_desc' => array('mandatory' => '', 'field_state' => 'enabled'),
			'author_picture' => array('mandatory' => '', 'field_state' => 'enabled')

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

	$operations['list_authors'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List authors',
		'page_title' => 'All authors',
		'table_name' => 'view_all_authors',

		//'page_template'=>'list',

		'data_source' => 'get_list_authors',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'author_id'=>array(),
			//'author_name'=>array(),
			'author_name' => array(
				'link' => array(
					'url' => 'element/display_element/detail_author/',
					'id_field' => 'author_id',
					'trim' => '0'
				)
			),
			'author_desc' => array(),
			'paper_nbr' => array()

		),
		'order_by' => 'author_name ASC ',
		//'search_by'=>'author_name',

		'list_links' => array(
			/*'view'=>array(
								   'label'=>'View',
								   'title'=>'Disaly element',
								   'icon'=>'folder',
								   'url'=>'element/display_element/detail_author/',
						   ),
						   'edit'=>array(
								   'label'=>'Edit',
								   'title'=>'Edit',
								   'icon'=>'edit',
								   'url'=>'element/edit_element/edit_author/',
						   ),
						   'delete'=>array(
								   'label'=>'Delete',
								   'title'=>'Delete the user',
								   'url'=>'element/delete_element/remove_author/'
						   )
						   */
		),

		'top_links' => array(
			'add' => array(
				'label' => '',
				'title' => 'Add a new author',
				'icon' => 'add',
				'url' => 'element/add_element/add_author',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);


	$operations['list_first_authors'] = array(
		'operation_type' => 'List',
		'operation_title' => 'List of first authors',
		'page_title' => 'First authors',
		'table_name' => 'view_first_authors',
		'data_source' => 'get_list_first_authors',
		'generate_stored_procedure' => True,

		'fields' => array(
			//'author_id'=>array(),
			//'author_name'=>array(),
			'author_name' => array(
				'link' => array(
					'url' => 'element/display_element/detail_author/',
					'id_field' => 'author_id',
					'trim' => '0'
				)
			),
			'author_desc' => array(),
			'paper_nbr' => array()

		),
		'order_by' => 'author_name ASC ',

		'list_links' => array(),
		'top_links' => array(

			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			)

		),
	);

	$operations['list_authors_class'] = $operations['list_first_authors'];
	$operations['list_authors_class']['page_title'] = 'All authors in classification';
	$operations['list_authors_class']['table_name'] = 'view_authors_class';
	$operations['list_authors_class']['data_source'] = 'get_list_authors_class';

	$operations['list_first_authors_class'] = $operations['list_first_authors'];
	$operations['list_first_authors_class']['page_title'] = 'First authors in classification';
	$operations['list_first_authors_class']['table_name'] = 'view_first_authors_class';
	$operations['list_first_authors_class']['data_source'] = 'get_list_first_authors_class';

	$operations['detail_author'] = array(
		'operation_type' => 'Detail',
		'operation_title' => 'Characteristics of an author',
		'operation_description' => 'Characteristics of an author',
		'page_title' => 'Author ',

		'data_source' => 'get_detail_author',
		'generate_stored_procedure' => True,

		'fields' => array(
			'author_name' => array(),
			'author_desc' => array(),
			'author_picture' => array()

		),


		'top_links' => array(
			'edit' => array(
				'label' => '',
				'title' => 'Edit',
				'icon' => 'edit',
				'url' => 'element/edit_element/edit_author/~current_element~',
			),
			'delete' => array(
				'label' => '',
				'title' => 'Delete',
				'icon' => 'trash',
				'url' => 'element/delete_element/remove_author/~current_element~',
			),
			'back' => array(
				'label' => '',
				'title' => 'Close',
				'icon' => 'add',
				'url' => 'home',
			),



		),
	);


	$operations['remove_author'] = array(
		'operation_type' => 'Remove',
		'operation_title' => 'Remove an author',
		'operation_description' => 'Remove an author from the displayed list',
		'redirect_after_delete' => 'element/entity_list/list_authors',
		'db_delete_model' => 'remove_author',
		'generate_stored_procedure' => True,


	);

	$config['operations'] = $operations;

	return $config;

}