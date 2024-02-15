<?php
/*
	function returns an array of operations related to the debug entity
*/
function get_operations_debug() {
	//author

	$operations['list_debug']=array(
			'type'=>'List',
			'tab_ref'=>'debug',
			'operation_id'=>'list_debug'
	);
	
	$operations['add_debug']=array(
			'type'=>'Add',
			'tab_ref'=>'debug',
			'operation_id'=>'add_debug'
	);
	
	$operations['edit_debug']=array(
			'type'=>'Edit',
			'tab_ref'=>'debug',
			'operation_id'=>'edit_debug'
	);
	
	$operations['detail_debug']=array(
			'type'=>'Detail',
			'tab_ref'=>'debug',
			'operation_id'=>'detail_debug'
	);
	
	$operations['remove_debug']=array(
			'type'=>'Remove',
			'tab_ref'=>'debug',
			'operation_id'=>'remove_debug'
	);
	
	return $operations;
	
	
	
}
