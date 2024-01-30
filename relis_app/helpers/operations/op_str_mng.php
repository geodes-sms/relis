<?php
//The get_operations_str_mng() function generates operations for strategic management tasks.
function get_operations_str_mng() {
	

	$operations['list_str_mng']=array(
			'type'=>'List',
			'tab_ref'=>'str_mng',
			'operation_id'=>'list_str_mng'
	);
	
	$operations['edit_str_mng']=array(
			'type'=>'Edit',
			'tab_ref'=>'str_mng',
			'operation_id'=>'edit_str_mng'
	);
	

	$operations['add_str_mng']=array(
			'type'=>'Add',
			'tab_ref'=>'str_mng',
			'operation_id'=>'add_str_mng'
	);
	
	
	$operations['detail_str_mng']=array(
			'type'=>'Detail',
			'tab_ref'=>'str_mng',
			'operation_id'=>'detail_str_mng'
	);
	
	$operations['remove_str_mng']=array(
			'type'=>'Remove',
			'tab_ref'=>'str_mng',
			'operation_id'=>'remove_str_mng'
	);
	
	return $operations;
}
