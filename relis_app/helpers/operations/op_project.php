<?php
//The get_operations_project() function generates operations for the 'project' and 'user_project' tables.
function get_operations_project() {
	//Projects

	$operations['list_projects']=array(
			'type'=>'List',
			'tab_ref'=>'project',
			'operation_id'=>'list_projects'
	);
	
	$operations['add_project']=array(
			'type'=>'Add',
			'tab_ref'=>'project',
			'operation_id'=>'add_project'
	);
	
	$operations['edit_project']=array(
			'type'=>'Edit',
			'tab_ref'=>'project',
			'operation_id'=>'edit_project'
	);
	
	$operations['detail_project']=array(
			'type'=>'Detail',
			'tab_ref'=>'project',
			'operation_id'=>'detail_project'
	);
	
	$operations['remove_project']=array(
			'type'=>'Remove',
			'tab_ref'=>'project',
			'operation_id'=>'remove_project'
	);
	
	
	//User project operations
	
	$operations['add_userproject']=array(
			'type'=>'Add',
			'tab_ref'=>'user_project',
			'operation_id'=>'add_userproject'
	);
	
	
	$operations['edit_userproject']=array(
			'type'=>'Edit',
			'tab_ref'=>'user_project',
			'operation_id'=>'edit_userproject'
	);
	
	
	$operations['list_userprojects']=array(
			'type'=>'List',
			'tab_ref'=>'user_project',
			'operation_id'=>'list_userprojects'
	);
	
	$operations['detail_userproject']=array(
			'type'=>'Detail',
			'tab_ref'=>'user_project',
			'operation_id'=>'detail_userproject'
	);
	
	
	$operations['remove_userproject']=array(
			'type'=>'Remove',
			'tab_ref'=>'user_project',
			'operation_id'=>'remove_userproject'
	);
	$operations['remove_userproject_c']=array(
			'type'=>'Remove',
			'tab_ref'=>'user_project',
			'operation_id'=>'remove_userproject_c'
	);
	$operations['remove_userproject_p']=array(
			'type'=>'Remove',
			'tab_ref'=>'user_project',
			'operation_id'=>'remove_userproject_p'
	);
	
	$operations['project_to_user']=array(
			'type'=>'AddChild',
			'tab_ref'=>'user_project',
			'operation_id'=>'project_to_user'
	);
	
	$operations['edit_project_to_user']=array(
			'type'=>'EditChild',
			'tab_ref'=>'user_project',
			'operation_id'=>'edit_project_to_user'
	);
	
	
	$operations['list_users_current_projects']=array(
			'type'=>'List',
			'tab_ref'=>'user_project',
			'operation_id'=>'list_users_current_projects'
	);
	
	$operations['add_user_current_project']=array(
			'type'=>'Add',
			'tab_ref'=>'user_project',
			'operation_id'=>'add_user_current_project'
	);
	
	$operations['edit_user_current_project']=array(
			'type'=>'Edit',
			'tab_ref'=>'user_project',
			'operation_id'=>'edit_user_current_project'
	);
	
	$operations['remove_user_current_project']=array(
			'type'=>'Remove',
			'tab_ref'=>'user_project',
			'operation_id'=>'remove_user_current_project'
	);
	return $operations;
	
	
	
}
