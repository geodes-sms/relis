<?php
//The get_operations_qa() function generates operations for the 'qa_questions', 'qa_responses', 'qa_result', 'qa_assignment', and 'qa_validation_assignment' tables.
function get_operations_qa() {
	
	//qa questions

	$operations['list_qa_questions']=array(
			'type'=>'List',
			'tab_ref'=>'qa_questions',
			'operation_id'=>'list_qa_questions'
	);
	
	$operations['add_qa_questions']=array(
			'type'=>'Add',
			'tab_ref'=>'qa_questions',
			'operation_id'=>'add_qa_questions'
	);
	
	$operations['edit_qa_questions']=array(
			'type'=>'Edit',
			'tab_ref'=>'qa_questions',
			'operation_id'=>'edit_qa_questions'
	);
	
	$operations['detail_qa_questions']=array(
			'type'=>'Detail',
			'tab_ref'=>'qa_questions',
			'operation_id'=>'detail_qa_questions'
	);
	
	$operations['remove_qa_questions']=array(
			'type'=>'Remove',
			'tab_ref'=>'qa_questions',
			'operation_id'=>'remove_qa_questions'
	);
	
	//qa responses

	$operations['list_qa_responses']=array(
			'type'=>'List',
			'tab_ref'=>'qa_responses',
			'operation_id'=>'list_qa_responses'
	);
	
	$operations['add_qa_responses']=array(
			'type'=>'Add',
			'tab_ref'=>'qa_responses',
			'operation_id'=>'add_qa_responses'
	);
	
	$operations['edit_qa_responses']=array(
			'type'=>'Edit',
			'tab_ref'=>'qa_responses',
			'operation_id'=>'edit_qa_responses'
	);
	
	$operations['detail_qa_responses']=array(
			'type'=>'Detail',
			'tab_ref'=>'qa_responses',
			'operation_id'=>'detail_qa_responses'
	);
	
	$operations['remove_qa_responses']=array(
			'type'=>'Remove',
			'tab_ref'=>'qa_responses',
			'operation_id'=>'remove_qa_responses'
	);
	
	//qa result

	$operations['list_qa_result']=array(
			'type'=>'List',
			'tab_ref'=>'qa_result',
			'operation_id'=>'list_qa_result'
	);
	
	$operations['add_qa_result']=array(
			'type'=>'Add',
			'tab_ref'=>'qa_result',
			'operation_id'=>'add_qa_result'
	);
	
	$operations['edit_qa_result']=array(
			'type'=>'Edit',
			'tab_ref'=>'qa_result',
			'operation_id'=>'edit_qa_result'
	);
	
	$operations['detail_qa_result']=array(
			'type'=>'Detail',
			'tab_ref'=>'qa_result',
			'operation_id'=>'detail_qa_result'
	);
	
	$operations['remove_qa_result']=array(
			'type'=>'Remove',
			'tab_ref'=>'qa_result',
			'operation_id'=>'remove_qa_result'
	);
	
	//qa assignment

	
	$operations['add_qa_assignment']=array(
			'type'=>'Add',
			'tab_ref'=>'qa_assignment',
			'operation_id'=>'add_qa_assignment'
	);
	
	$operations['edit_qa_assignment']=array(
			'type'=>'Edit',
			'tab_ref'=>'qa_assignment',
			'operation_id'=>'edit_qa_assignment'
	);
	
	$operations['detail_qa_assignment']=array(
			'type'=>'Detail',
			'tab_ref'=>'qa_assignment',
			'operation_id'=>'detail_qa_assignment'
	);
	
	$operations['remove_qa_assignment']=array(
			'type'=>'Remove',
			'tab_ref'=>'qa_assignment',
			'operation_id'=>'remove_qa_assignment'
	);
	$operations['list_qa_papers']=array(
			'type'=>'List',
			'tab_ref'=>'qa_assignment',
			'operation_id'=>'list_qa_papers'
	);
	$operations['list_qa_papers_done']=array(
			'type'=>'List',
			'tab_ref'=>'qa_assignment',
			'operation_id'=>'list_qa_papers_done'
	);
	$operations['list_qa_papers_pending']=array(
			'type'=>'List',
			'tab_ref'=>'qa_assignment',
			'operation_id'=>'list_qa_papers_pending'
	);
	
	
	
	
	$operations['list_qa_validation']=array(
			'type'=>'List',
			'tab_ref'=>'qa_validation_assignment',
			'operation_id'=>'list_qa_validation'
	);
	$operations['list_qa_validation_assignment']=array(
			'type'=>'List',
			'tab_ref'=>'qa_validation_assignment',
			'operation_id'=>'list_qa_validation_assignment'
	);
	$operations['remove_qa_validation_assignment']=array(
			'type'=>'Remove',
			'tab_ref'=>'qa_validation_assignment',
			'operation_id'=>'remove_qa_validation_assignment'
	);
	$operations['qa_not_valid']=array(
			'type'=>'Edit',
			'tab_ref'=>'qa_validation_assignment',
			'operation_id'=>'qa_not_valid'
	);
	
	return $operations;
	
	
	
}
