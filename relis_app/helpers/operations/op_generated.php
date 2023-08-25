<?php
//retrieves the generated tables and reference tables from the database and creates corresponding operations for each table
function get_operations_generated() {
	$ci = get_instance();
	$sql="select * from installation_info where install_active=1 ";
	$ci->db2 = $ci->load->database(project_db(), TRUE);
	$res=$ci->db2->query($sql)->row_array();
	$generated_tables=array();
	$generated_tables=array();
	$operations=array();
	if(!empty($res)){
		$generated_tables=json_decode($res['generated_tables']);
		$generated_reference_tables=json_decode($res['reference_tables']);
		$generated_tables= array_merge_recursive($generated_tables,$generated_reference_tables);
	}
	
	foreach ( $generated_tables as $key => $config) {
		
		$operations['list_'.$config]=array(
				'type'=>'List',
				'tab_ref'=>$config,
				'operation_id'=>'list_'.$config
		);
		
		$operations['edit_'.$config]=array(
				'type'=>'Edit',
				'tab_ref'=>$config,
				'operation_id'=>'edit_'.$config
		);
		$operations['edit_'.$config]=array(
				'type'=>'EditChild',
				'tab_ref'=>$config,
				'operation_id'=>'edit_'.$config
		);
		
		
		$operations['add_'.$config]=array(
				'type'=>'Add',
				'tab_ref'=>$config,
				'operation_id'=>'add_'.$config
		);
		
		$operations['add_'.$config]=array(
				'type'=>'AddChild',
				'tab_ref'=>$config,
				'operation_id'=>'add_'.$config
		);
		$operations['detail_'.$config]=array(
				'type'=>'Detail',
				'tab_ref'=>$config,
				'operation_id'=>'detail_'.$config
		);
		
		$operations['remove_'.$config]=array(
				'type'=>'Remove',
				'tab_ref'=>$config,
				'operation_id'=>'remove_'.$config
		);
	}
	
	foreach ( $generated_reference_tables as $key => $config) {
	
		$operations['list_'.$config]=array(
				'type'=>'List',
				'tab_ref'=>$config,
				'operation_id'=>'list_'.$config
		);
	
		$operations['edit_'.$config]=array(
				'type'=>'Edit',
				'tab_ref'=>$config,
				'operation_id'=>'edit_'.$config
		);
		
	
	
		$operations['add_'.$config]=array(
				'type'=>'Add',
				'tab_ref'=>$config,
				'operation_id'=>'add_'.$config
		);
	
		
		$operations['detail_'.$config]=array(
				'type'=>'Detail',
				'tab_ref'=>$config,
				'operation_id'=>'detail_'.$config
		);
	
		$operations['remove_'.$config]=array(
				'type'=>'Remove',
				'tab_ref'=>$config,
				'operation_id'=>'remove_'.$config
		);
	}

	$operations['new_classification']=array(
			'type'=>'AddChild',
			'tab_ref'=>'classification',
			'operation_id'=>'new_classification'
	);
	$operations['update_classification']=array(
			'type'=>'EditChild',
			'tab_ref'=>'classification',
			'operation_id'=>'update_classification'
	);//print_test($operations);
	
	return $operations;
}
