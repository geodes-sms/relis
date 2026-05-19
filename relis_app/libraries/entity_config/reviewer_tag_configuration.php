<?php
/* ReLiS - Issue #103 - Reviewer tag entity configuration */

function get_reviewer_tag()
{
    $config['table_name']         = 'reviewer_tag';
    $config['table_id']           = 'tag_id';
    $config['table_active_field'] = 'tag_active';
    $config['reference_title']    = 'Reviewer tags';
    $config['reference_title_min']= 'Reviewer tag';

    $config['order_by'] = 'tag_rank DESC, tag_name ASC';

    $config['links']['edit'] = array(
        'label' => 'Edit', 'title' => 'Edit tag',
        'on_list' => True, 'on_view' => True
    );
    $config['links']['view'] = array(
        'label' => 'View', 'title' => 'View',
        'on_list' => True, 'on_view' => True
    );

    $fields['tag_id'] = array(
        'field_title' => '#',
        'field_type'  => 'number',
        'field_value' => 'auto_increment',
        'on_add' => 'hidden', 'on_edit' => 'hidden',
        'on_list' => 'show',  'on_view' => 'hidden',
    );

    $fields['tag_name'] = array(
        'field_title' => 'Name',
        'field_type'  => 'text',
        'field_value' => 'normal',
        'field_size'  => 50,
        'mandatory'   => 'mandatory',
        'input_type'  => 'text',
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['tag_description'] = array(
        'field_title' => 'Description',
        'field_type'  => 'text',
        'field_value' => 'normal',
        'field_size'  => 250,
        'input_type'  => 'textarea',
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['tag_color'] = array(
        'field_title' => 'Color',
        'field_type'  => 'text',
        'field_value' => 'normal',
        'field_size'  => 7,
        'input_type'  => 'text',
        'initial_value' => '#888888',
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['tag_is_hierarchical'] = array(
        'field_title' => 'Hierarchical',
        'field_type'  => '0_1',
        'field_value' => 'normal',
        'input_type'  => 'select',
        'input_select_source' => 'yes_no',
        'initial_value' => 0,
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['tag_rank'] = array(
        'field_title' => 'Rank',
        'field_type'  => 'number',
        'field_value' => 'normal',
        'field_size'  => 11,
        'input_type'  => 'number',
        'initial_value' => 0,
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['added_by'] = array(
        'field_title' => 'Added by',
        'field_type'  => 'number',
        'field_value' => 'active_user',
        'field_size'  => 11,
        'input_type'  => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'users;user_name',
        'on_add' => 'hidden', 'on_edit' => 'not_set', 'on_list' => 'hidden'
    );

    $fields['added_time'] = array(
        'field_title' => 'Added time',
        'field_type'  => 'text',
        'field_value' => 'normal',
        'input_type'  => 'date',
        'on_add' => 'not_set', 'on_edit' => 'not_set', 'on_list' => 'hidden'
    );

    $fields['tag_active'] = array(
        'field_title' => 'Active',
        'field_type'  => '0_1',
        'field_value' => 'normal',
        'on_add' => 'not_set', 'on_edit' => 'not_set',
        'on_list' => 'hidden', 'on_view' => 'hidden'
    );

    $config['fields'] = $fields;
    return $config;
}
