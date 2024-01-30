<?php
//The get_operations_logs() function generates operations for the 'logs' table
function get_operations_logs() {
	

	$operations['list_logs']=array(
			'type'=>'List',
			'tab_ref'=>'logs',
			'operation_id'=>'list_logs'
	);
	
	$operations['add_logs']=array(
			'type'=>'Add',
			'tab_ref'=>'logs',
			'operation_id'=>'add_logs'
	);
	
	
	$operations['detail_logs']=array(
			'type'=>'Detail',
			'tab_ref'=>'logs',
			'operation_id'=>'detail_logs'
	);
	
	return $operations;
}
