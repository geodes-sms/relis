<?php
//The get_operations_operations() function generates operations for the 'operations' table
function get_operations_operations() {
	

	$operations['list_operations']=array(
			'type'=>'List',
			'tab_ref'=>'operations',
			'operation_id'=>'list_operations'
	);
	
	
	return $operations;
}
