<?php
/*
	function returns an array of operations related to the configuration entity
*/
function get_operations_configuration() {
	//Projects

	
	$operations['edit_configuration']=array(
			'type'=>'Edit',
			'tab_ref'=>'config',
			'operation_id'=>'edit_configuration'
	);
	
	$operations['configurations']=array(
			'type'=>'Detail',
			'tab_ref'=>'config',
			'operation_id'=>'configurations'
	);
	$operations['edit_conf_papers']=array(
			'type'=>'Edit',
			'tab_ref'=>'config',
			'operation_id'=>'edit_conf_papers'
	);
	$operations['edit_config_dsl']=array(
			'type'=>'Edit',
			'tab_ref'=>'config',
			'operation_id'=>'edit_config_dsl'
	);
	$operations['edit_config_screening']=array(
			'type'=>'Edit',
			'tab_ref'=>'config',
			'operation_id'=>'edit_config_screening'
	);
	
	$operations['config_papers']=array(
			'type'=>'Detail',
			'tab_ref'=>'config',
			'operation_id'=>'config_papers'
	);
	$operations['config_dsl']=array(
			'type'=>'Detail',
			'tab_ref'=>'config',
			'operation_id'=>'config_dsl'
	);
	$operations['config_screening']=array(
			'type'=>'Detail',
			'tab_ref'=>'config',
			'operation_id'=>'config_screening'
	);
	$operations['edit_config_qa']=array(
			'type'=>'Edit',
			'tab_ref'=>'config',
			'operation_id'=>'edit_config_qa'
	);
	$operations['config_qa']=array(
			'type'=>'Detail',
			'tab_ref'=>'config',
			'operation_id'=>'config_qa'
	);
	$operations['edit_config_class']=array(
			'type'=>'Edit',
			'tab_ref'=>'config',
			'operation_id'=>'edit_config_class'
	);
	$operations['config_class']=array(
			'type'=>'Detail',
			'tab_ref'=>'config',
			'operation_id'=>'config_class'
	);
	
	
	$operations['edit_admin_config']=array(
			'type'=>'Edit',
			'tab_ref'=>'config_admin',
			'operation_id'=>'edit_admin_config'
	);
	$operations['admin_config']=array(
			'type'=>'Detail',
			'tab_ref'=>'config_admin',
			'operation_id'=>'admin_config'
	);
	return $operations;
	
	
	
}
