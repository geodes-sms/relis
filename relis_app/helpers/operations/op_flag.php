<?php

function get_operation_flag() {

    $operations['list_flagged_papers']=array(
        'type'=>'List',
        'tab_ref'=>'flags',
        'operation_id'=>'list_flagged_papers'
    );

    $operations['remove_flag']=array(
        'type'=>'Remove',
        'tab_ref'=>'flags',
        'operation_id'=>'remove_flag'
    );

    $operations['edit_flag']=array(
        'type'=>'Edit',
        'tab_ref'=>'flags',
        'operation_id'=>'edit_flag'
    );

    $operations['detail_flag']=array(
        'type'=>'Detail',
        'tab_ref'=>'flags',
        'operation_id'=>'detail_flag'
    );

    return $operations;
}