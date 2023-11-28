<?php
//The get_operations_paper() function generates operations for the 'papers' table
function get_operations_paper() {
	//paper

	$operations['list_papers']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers'
	);
	$operations['list_all_papers']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_all_papers'
	);
	
	$operations['list_pending_papers']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_pending_papers'
	);
	
	$operations['list_included_papers']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_included_papers'
	);
	
	$operations['list_excluded_papers']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_excluded_papers'
	);
	
	$operations['list_papers_screen']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers_screen'
	);
	
	$operations['list_papers_screen_included']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers_screen_included'
	);
	
	$operations['list_papers_screen_excluded']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers_screen_excluded'
	);
	
	$operations['list_papers_screen_pending']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers_screen_pending'
	);
	
	$operations['list_papers_screen_review']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers_screen_review'
	);
	
	$operations['list_papers_screen_conflict']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers_screen_conflict'
	);
	$operations['list_papers_screen_my_conflict']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers_screen_my_conflict'
	);
	
	$operations['list_papers_screen_included_after_conflict']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers_screen_included_after_conflict'
	);
	
	$operations['list_papers_screen_excluded_after_conflict']=array(
			'type'=>'List',
			'tab_ref'=>'papers',
			'operation_id'=>'list_papers_screen_excluded_after_conflict'
	);
	
	
	
	
	
	
	$operations['add_paper']=array(
			'type'=>'Add',
			'tab_ref'=>'papers',
			'operation_id'=>'add_paper'
	);
	
	$operations['edit_paper']=array(
			'type'=>'Edit',
			'tab_ref'=>'papers',
			'operation_id'=>'edit_paper'
	);
	$operations['edit_paper_det']=array(
			'type'=>'Edit',
			'tab_ref'=>'papers',
			'operation_id'=>'edit_paper_det'
	);
	$operations['detail_paper']=array(
			'type'=>'Detail',
			'tab_ref'=>'papers',
			'operation_id'=>'detail_paper'
	);
	
	$operations['remove_paper']=array(
			'type'=>'Remove',
			'tab_ref'=>'papers',
			'operation_id'=>'remove_paper'
	);
	
	
	return $operations;
	
	
	
}
