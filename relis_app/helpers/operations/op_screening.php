<?php
//The get_operations_screening() function generates operations for screening-related tasks.
function get_operations_screening() {
	//Screening phases

	$operations['list_screen_phases']=array(
			'type'=>'List',
			'tab_ref'=>'screen_phase',
			'operation_id'=>'list_screen_phases'
	);
	
	$operations['add_screen_phase']=array(
			'type'=>'Add',
			'tab_ref'=>'screen_phase',
			'operation_id'=>'add_screen_phase'
	);
	$operations['add_validation_phase']=array(
			'type'=>'Add',
			'tab_ref'=>'screen_phase',
			'operation_id'=>'add_validation_phase'
	);
	
	$operations['edit_screen_phase']=array(
			'type'=>'Edit',
			'tab_ref'=>'screen_phase',
			'operation_id'=>'edit_screen_phase'
	);
	
	$operations['detail_screen_phase']=array(
			'type'=>'Detail',
			'tab_ref'=>'screen_phase',
			'operation_id'=>'detail_screen_phase'
	);
	
	$operations['remove_screen_phase']=array(
			'type'=>'Remove',
			'tab_ref'=>'screen_phase',
			'operation_id'=>'remove_screen_phase'
	);
	
	
	//Assignments
	
	$operations['list_assignments']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_assignments'
	);
	
	$operations['list_my_screenings']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_my_screenings'
	);
	
	$operations['list_my_assignments']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_my_assignments'
	);
	
	$operations['new_assignment']=array(
			'type'=>'Add',
			'tab_ref'=>'screening',
			'operation_id'=>'new_assignment'
	);
	
	$operations['edit_assignment']=array(
			'type'=>'Edit',
			'tab_ref'=>'screening',
			'operation_id'=>'edit_assignment'
	);
	
	$operations['display_assignment']=array(
			'type'=>'Detail',
			'tab_ref'=>'screening',
			'operation_id'=>'display_assignment'
	);
	
	
	$operations['add_reviewer']=array(
			'type'=>'AddChild',
			'tab_ref'=>'screening',
			'operation_id'=>'add_reviewer'
	);
	$operations['remove_assignment']=array(
			'type'=>'Remove',
			'tab_ref'=>'screening',
			'operation_id'=>'remove_assignment'
	);
	$operations['remove_assignment_val']=array(
			'type'=>'Remove',
			'tab_ref'=>'screening',
			'operation_id'=>'remove_assignment_val'
	);
	//screenings
	$operations['list_screenings']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_screenings'
	);
	$operations['display_screening']=array(
			'type'=>'Detail',
			'tab_ref'=>'screening',
			'operation_id'=>'display_screening'
	);
	$operations['list_decisions']=array(
			'type'=>'List',
			'tab_ref'=>'screen_decison',
			'operation_id'=>'list_decisions'
	);
	
	$operations['new_decision']=array(
			'type'=>'Add',
			'tab_ref'=>'screen_decison',
			'operation_id'=>'new_decision'
	);
	
	
	$operations['simple_screen']=array(
			'type'=>'Edit',
			'tab_ref'=>'screening',
			'operation_id'=>'screen_paper'
	);
	
	$operations['screen_validation']=array(
			'type'=>'Edit',
			'tab_ref'=>'screening',
			'operation_id'=>'validate_screen'
	);
	
	$operations['edit_screen']=array(
			'type'=>'Edit',
			'tab_ref'=>'screening',
			'operation_id'=>'edit_screen'
	);
	
	$operations['resolve_conflict']=array(
			'type'=>'Edit',
			'tab_ref'=>'screening',
			'operation_id'=>'resolve_conflict'
	);
	
	$operations['list_assignments_validation']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_assignments_validation'
	);
	
	
	$operations['list_screenings_validation']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_screenings_validation'
	);
	
	$operations['list_my_pending_screenings']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_my_pending_screenings'
	);
	
	$operations['list_my_pending_validation']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_my_pending_validation'
	);
	
	$operations['list_my_done_validation']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_my_done_validation'
	);
	$operations['list_my_validations_assignment']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_my_validations_assignment'
	);
	
	$operations['list_pending_screenings_validation']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_pending_screenings_validation'
	);
	
	$operations['list_all_pending_screenings']=array(
			'type'=>'List',
			'tab_ref'=>'screening',
			'operation_id'=>'list_all_pending_screenings'
	);
	return $operations;
	
	
	
}
