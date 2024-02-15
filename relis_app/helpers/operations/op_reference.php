<?php
//The get_operations_reference() function generates operations for various reference tables.
function get_operations_reference() {
	//exclusion criteria

	$operations['list_exclusioncrieria']=array(
			'type'=>'List',
			'tab_ref'=>'exclusioncrieria',
			'operation_id'=>'list_exclusioncrieria'
	);
	
	$operations['add_exclusioncrieria']=array(
			'type'=>'Add',
			'tab_ref'=>'exclusioncrieria',
			'operation_id'=>'add_exclusioncrieria'
	);
	
	$operations['edit_exclusioncrieria']=array(
			'type'=>'Edit',
			'tab_ref'=>'exclusioncrieria',
			'operation_id'=>'edit_exclusioncrieria'
	);
	
	$operations['detail_exclusioncrieria']=array(
			'type'=>'Detail',
			'tab_ref'=>'exclusioncrieria',
			'operation_id'=>'detail_exclusioncrieria'
	);
	
	$operations['remove_exclusioncrieria']=array(
			'type'=>'Remove',
			'tab_ref'=>'exclusioncrieria',
			'operation_id'=>'remove_exclusioncrieria'
	);
	
	//inclusion criteria
	
	$operations['list_inclusioncriteria']=array(
			'type'=>'List',
			'tab_ref'=>'inclusioncriteria',
			'operation_id'=>'list_inclusioncriteria'
	);
	
	$operations['add_inclusioncriteria']=array(
			'type'=>'Add',
			'tab_ref'=>'inclusioncriteria',
			'operation_id'=>'add_inclusioncriteria'
	);
	
	$operations['edit_inclusioncriteria']=array(
			'type'=>'Edit',
			'tab_ref'=>'inclusioncriteria',
			'operation_id'=>'edit_inclusioncriteria'
	);
	
	$operations['detail_inclusioncriteria']=array(
			'type'=>'Detail',
			'tab_ref'=>'inclusioncriteria',
			'operation_id'=>'detail_inclusioncriteria'
	);
	
	$operations['remove_inclusioncriteria']=array(
			'type'=>'Remove',
			'tab_ref'=>'inclusioncriteria',
			'operation_id'=>'remove_inclusioncriteria'
	);
	
	//Research question
	
	$operations['list_research_question']=array(
			'type'=>'List',
			'tab_ref'=>'research_question',
			'operation_id'=>'list_research_question'
	);
	
	$operations['add_research_question']=array(
			'type'=>'Add',
			'tab_ref'=>'research_question',
			'operation_id'=>'add_research_question'
	);
	
	$operations['edit_research_question']=array(
			'type'=>'Edit',
			'tab_ref'=>'research_question',
			'operation_id'=>'edit_research_question'
	);
	
	$operations['detail_research_question']=array(
			'type'=>'Detail',
			'tab_ref'=>'research_question',
			'operation_id'=>'detail_research_question'
	);
	
	$operations['remove_research_question']=array(
			'type'=>'Remove',
			'tab_ref'=>'research_question',
			'operation_id'=>'remove_research_question'
	);
	
	//affiliation

	$operations['list_affiliation']=array(
			'type'=>'List',
			'tab_ref'=>'affiliation',
			'operation_id'=>'list_affiliation'
	);
	
	$operations['add_affiliation']=array(
			'type'=>'Add',
			'tab_ref'=>'affiliation',
			'operation_id'=>'add_affiliation'
	);
	
	$operations['edit_affiliation']=array(
			'type'=>'Edit',
			'tab_ref'=>'affiliation',
			'operation_id'=>'edit_affiliation'
	);
	
	$operations['detail_affiliation']=array(
			'type'=>'Detail',
			'tab_ref'=>'affiliation',
			'operation_id'=>'detail_affiliation'
	);
	
	$operations['remove_affiliation']=array(
			'type'=>'Remove',
			'tab_ref'=>'affiliation',
			'operation_id'=>'remove_affiliation'
	);
	
	//papers_sources

	$operations['list_papers_sources']=array(
			'type'=>'List',
			'tab_ref'=>'papers_sources',
			'operation_id'=>'list_papers_sources'
	);
	
	$operations['add_papers_sources']=array(
			'type'=>'Add',
			'tab_ref'=>'papers_sources',
			'operation_id'=>'add_papers_sources'
	);
	
	$operations['edit_papers_sources']=array(
			'type'=>'Edit',
			'tab_ref'=>'papers_sources',
			'operation_id'=>'edit_papers_sources'
	);
	
	$operations['detail_papers_sources']=array(
			'type'=>'Detail',
			'tab_ref'=>'papers_sources',
			'operation_id'=>'detail_papers_sources'
	);
	
	$operations['remove_papers_sources']=array(
			'type'=>'Remove',
			'tab_ref'=>'papers_sources',
			'operation_id'=>'remove_papers_sources'
	);
	
	//search_strategy

	$operations['list_search_strategy']=array(
			'type'=>'List',
			'tab_ref'=>'search_strategy',
			'operation_id'=>'list_search_strategy'
	);
	
	$operations['add_search_strategy']=array(
			'type'=>'Add',
			'tab_ref'=>'search_strategy',
			'operation_id'=>'add_search_strategy'
	);
	
	$operations['edit_search_strategy']=array(
			'type'=>'Edit',
			'tab_ref'=>'search_strategy',
			'operation_id'=>'edit_search_strategy'
	);
	
	$operations['detail_search_strategy']=array(
			'type'=>'Detail',
			'tab_ref'=>'search_strategy',
			'operation_id'=>'detail_search_strategy'
	);
	
	$operations['remove_search_strategy']=array(
			'type'=>'Remove',
			'tab_ref'=>'search_strategy',
			'operation_id'=>'remove_search_strategy'
	);
	
	
	return $operations;
	
	
	
}
