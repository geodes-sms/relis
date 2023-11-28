<?php
/*
	The get_operations_author() function returns an array of operations related to the author and paper_author entities.
*/
function get_operations_author() {
	//author

	$operations['list_authors']=array(
			'type'=>'List',
			'tab_ref'=>'author',
			'operation_id'=>'list_authors'
	);
	
	$operations['list_first_authors']=array(
			'type'=>'List',
			'tab_ref'=>'author',
			'operation_id'=>'list_first_authors'
	);
	
	$operations['list_first_authors_class']=array(
			'type'=>'List',
			'tab_ref'=>'author',
			'operation_id'=>'list_first_authors_class'
	);
	
	$operations['list_authors_class']=array(
			'type'=>'List',
			'tab_ref'=>'author',
			'operation_id'=>'list_authors_class'
	);
	$operations['add_author']=array(
			'type'=>'Add',
			'tab_ref'=>'author',
			'operation_id'=>'add_author'
	);
	
	$operations['edit_author']=array(
			'type'=>'Edit',
			'tab_ref'=>'author',
			'operation_id'=>'edit_author'
	);
	
	$operations['detail_author']=array(
			'type'=>'Detail',
			'tab_ref'=>'author',
			'operation_id'=>'detail_author'
	);
	
	$operations['remove_author']=array(
			'type'=>'Remove',
			'tab_ref'=>'author',
			'operation_id'=>'remove_author'
	);

	
	//Paper_author
	
	$operations['list_paper_authors']=array(
			'type'=>'List',
			'tab_ref'=>'paper_authors',
			'operation_id'=>'list_paper_authors'
	);
	
	$operations['add_paper_author']=array(
			'type'=>'Add',
			'tab_ref'=>'paper_author',
			'operation_id'=>'add_paper_author'
	);
	
	$operations['edit_paper_author']=array(
			'type'=>'Edit',
			'tab_ref'=>'paper_author',
			'operation_id'=>'edit_paper_author'
	);
	
	$operations['detail_paper_author']=array(
			'type'=>'Detail',
			'tab_ref'=>'paper_author',
			'operation_id'=>'detail_paper_author'
	);
	
	$operations['remove_paper_author']=array(
			'type'=>'Remove',
			'tab_ref'=>'paper_author',
			'operation_id'=>'remove_paper_author'
	);
	
	return $operations;
}
