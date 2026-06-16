<?php

function get_operations_assignment()
{
    // ─── reviewer_tag ────────────────────────────────────────────
    $operations['list_reviewer_tag'] = array(
        'type'         => 'List',
        'tab_ref'      => 'reviewer_tag',
        'operation_id' => 'list_reviewer_tag'
    );
    $operations['add_reviewer_tag'] = array(
        'type'         => 'Add',
        'tab_ref'      => 'reviewer_tag',
        'operation_id' => 'add_reviewer_tag'
    );
    $operations['edit_reviewer_tag'] = array(
        'type'         => 'Edit',
        'tab_ref'      => 'reviewer_tag',
        'operation_id' => 'edit_reviewer_tag'
    );
    $operations['detail_reviewer_tag'] = array(
        'type'         => 'Detail',
        'tab_ref'      => 'reviewer_tag',
        'operation_id' => 'detail_reviewer_tag'
    );
    $operations['remove_reviewer_tag'] = array(
        'type'         => 'Remove',
        'tab_ref'      => 'reviewer_tag',
        'operation_id' => 'remove_reviewer_tag'
    );

    // ─── userproject_tag ─────────────────────────────────────────
    $operations['list_userproject_tag'] = array(
        'type'         => 'List',
        'tab_ref'      => 'userproject_tag',
        'operation_id' => 'list_userproject_tag'
    );
    $operations['add_userproject_tag'] = array(
        'type'         => 'Add',
        'tab_ref'      => 'userproject_tag',
        'operation_id' => 'add_userproject_tag'
    );
    $operations['edit_userproject_tag'] = array(
        'type'         => 'Edit',
        'tab_ref'      => 'userproject_tag',
        'operation_id' => 'edit_userproject_tag'
    );
    $operations['detail_userproject_tag'] = array(
        'type'         => 'Detail',
        'tab_ref'      => 'userproject_tag',
        'operation_id' => 'detail_userproject_tag'
    );
    $operations['remove_userproject_tag'] = array(
        'type'         => 'Remove',
        'tab_ref'      => 'userproject_tag',
        'operation_id' => 'remove_userproject_tag'
    );

    // ─── assignment_constraint ───────────────────────────────────
    $operations['list_assignment_constraint'] = array(
        'type'         => 'List',
        'tab_ref'      => 'assignment_constraint',
        'operation_id' => 'list_assignment_constraint'
    );
    $operations['add_assignment_constraint'] = array(
        'type'         => 'Add',
        'tab_ref'      => 'assignment_constraint',
        'operation_id' => 'add_assignment_constraint'
    );
    $operations['edit_assignment_constraint'] = array(
        'type'         => 'Edit',
        'tab_ref'      => 'assignment_constraint',
        'operation_id' => 'edit_assignment_constraint'
    );
    $operations['detail_assignment_constraint'] = array(
        'type'         => 'Detail',
        'tab_ref'      => 'assignment_constraint',
        'operation_id' => 'detail_assignment_constraint'
    );
    $operations['remove_assignment_constraint'] = array(
        'type'         => 'Remove',
        'tab_ref'      => 'assignment_constraint',
        'operation_id' => 'remove_assignment_constraint'
    );

    return $operations;
}