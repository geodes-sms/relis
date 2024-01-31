<?php
//The get_operations_info() function generates operations for the 'info' table
function get_operations_info() {
	

	$operations['list_info']=array(
			'type'=>'List',
			'tab_ref'=>'info',
			'operation_id'=>'list_info'
	);
	
	$operations['add_info']=array(
			'type'=>'Add',
			'tab_ref'=>'info',
			'operation_id'=>'add_info'
	);
	$operations['edit_info']=array(
			'type'=>'Edit',
			'tab_ref'=>'info',
			'operation_id'=>'edit_info'
	);
	
	
	$operations['detail_info']=array(
			'type'=>'Detail',
			'tab_ref'=>'info',
			'operation_id'=>'detail_info'
	);
	
	$operations['remove_info']=array(
			'type'=>'Remove',
			'tab_ref'=>'info',
			'operation_id'=>'remove_info'
	);
	return $operations;
}
