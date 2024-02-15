<?php
//The get_operations_venue() function generates operations for managing venues.
function get_operations_venue() {
	//venue

	$operations['list_venues']=array(
			'type'=>'List',
			'tab_ref'=>'venue',
			'operation_id'=>'list_venues'
	);
	
	$operations['add_venue']=array(
			'type'=>'Add',
			'tab_ref'=>'venue',
			'operation_id'=>'add_venue'
	);
	
	$operations['edit_venue']=array(
			'type'=>'Edit',
			'tab_ref'=>'venue',
			'operation_id'=>'edit_venue'
	);
	
	$operations['detail_venue']=array(
			'type'=>'Detail',
			'tab_ref'=>'venue',
			'operation_id'=>'detail_venue'
	);
	
	$operations['remove_venue']=array(
			'type'=>'Remove',
			'tab_ref'=>'venue',
			'operation_id'=>'remove_venue'
	);
	
	
	return $operations;
}
