<?php
/* ReLiS - Issue #103 - Userproject tag (user ↔ tag join) configuration */

function get_userproject_tag()
{
    $config['table_name']         = 'userproject_tag';
    $config['table_id']           = 'userproject_tag_id';
    $config['table_active_field'] = 'userproject_tag_active';
    $config['reference_title']    = 'User tags';
    $config['reference_title_min']= 'User tag';

    $config['order_by'] = 'userproject_tag_id DESC';

    $config['links']['edit'] = array(
        'label' => 'Edit', 'title' => 'Edit',
        'on_list' => True, 'on_view' => True
    );

    $fields['userproject_tag_id'] = array(
        'field_title' => '#',
        'field_type'  => 'number',
        'field_value' => 'auto_increment',
        'on_add' => 'hidden', 'on_edit' => 'hidden',
        'on_list' => 'show', 'on_view' => 'hidden',
    );

    $fields['userproject_id'] = array(
        'field_title' => 'User in project',
        'field_type'  => 'number',
        'field_value' => 'normal',
        'field_size'  => 11,
        'mandatory'   => 'mandatory',
        'input_type'  => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'userproject;CONCAT(user_id," - role:",user_role)',
        'on_add' => 'enabled', 'on_edit' => 'disabled', 'on_list' => 'show'
    );

    $fields['tag_id'] = array(
        'field_title' => 'Tag',
        'field_type'  => 'number',
        'field_value' => 'normal',
        'field_size'  => 11,
        'mandatory'   => 'mandatory',
        'input_type'  => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'reviewer_tag;tag_name',
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['assigned_by'] = array(
        'field_title' => 'Assigned by',
        'field_type'  => 'number',
        'field_value' => 'active_user',
        'field_size'  => 11,
        'input_type'  => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'users;user_name',
        'on_add' => 'hidden', 'on_edit' => 'not_set', 'on_list' => 'show'
    );

    $fields['assigned_time'] = array(
        'field_title' => 'Assigned time',
        'field_type'  => 'text',
        'field_value' => 'normal',
        'input_type'  => 'date',
        'on_add' => 'not_set', 'on_edit' => 'not_set', 'on_list' => 'show'
    );

    $fields['userproject_tag_active'] = array(
        'field_title' => 'Active',
        'field_type'  => '0_1',
        'field_value' => 'normal',
        'on_add' => 'not_set', 'on_edit' => 'not_set',
        'on_list' => 'hidden', 'on_view' => 'hidden'
    );

    $config['fields'] = $fields;
    return $config;
}
