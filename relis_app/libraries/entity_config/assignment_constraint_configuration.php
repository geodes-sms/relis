<?php
/* ReLiS - Issue #103 - Assignment constraint entity configuration */

function get_assignment_constraint()
{
    $config['table_name']         = 'assignment_constraint';
    $config['table_id']           = 'constraint_id';
    $config['table_active_field'] = 'constraint_active';
    $config['reference_title']    = 'Assignment constraints';
    $config['reference_title_min']= 'Assignment constraint';

    $config['order_by'] = 'constraint_priority ASC, constraint_id DESC';

    $config['links']['edit'] = array(
        'label' => 'Edit', 'title' => 'Edit',
        'on_list' => True, 'on_view' => True
    );
    $config['links']['view'] = array(
        'label' => 'View', 'title' => 'View',
        'on_list' => True, 'on_view' => True
    );

    $fields['constraint_id'] = array(
        'field_title' => '#',
        'field_type'  => 'number',
        'field_value' => 'auto_increment',
        'on_add' => 'hidden', 'on_edit' => 'hidden',
        'on_list' => 'show', 'on_view' => 'hidden',
    );

    $fields['constraint_scope'] = array(
        'field_title' => 'Scope',
        'field_type'  => 'text',
        'field_value' => 'normal',
        'field_size'  => 40,
        'mandatory'   => 'mandatory',
        'input_type'  => 'select',
        'input_select_source'  => 'array',
        'input_select_values'  => array(
            'screening' => 'Screening',
            'screening_validation' => 'Screening validation',
            'qa' => 'Quality assessment',
            'qa_validation' => 'QA validation',
            'classification' => 'Classification',
            'classification_validation' => 'Classification validation',
        ),
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['phase_id'] = array(
        'field_title' => 'Phase (screening only)',
        'field_type'  => 'number',
        'field_value' => 'normal',
        'field_size'  => 11,
        'input_type'  => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'screen_phase;phase_title',
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['constraint_type'] = array(
        'field_title' => 'Type',
        'field_type'  => 'text',
        'field_value' => 'normal',
        'field_size'  => 60,
        'mandatory'   => 'mandatory',
        'input_type'  => 'select',
        'input_select_source'  => 'array',
        'input_select_values'  => array(
            'min_tag_per_paper' => 'Minimum N reviewers with tag',
            'max_tag_per_paper' => 'Maximum N reviewers with tag',
            'tag_combination' => 'Tag combination (X tag A OR Y tag B)',
            'same_user_from_previous_phase' => 'Same user from previous phase',
            'force_different_user_from_previous_phase' => 'Different user from previous',
        ),
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    // JSON params : stocké en TEXT, édité via formulaire custom (cf. étape 7)
    $fields['constraint_params'] = array(
        'field_title' => 'Parameters (JSON)',
        'field_type'  => 'text',
        'field_value' => 'normal',
        'input_type'  => 'textarea',
        'mandatory'   => 'mandatory',
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['constraint_priority'] = array(
        'field_title' => 'Priority',
        'field_type'  => 'number',
        'field_value' => 'normal',
        'field_size'  => 11,
        'input_type'  => 'number',
        'initial_value' => 100,
        'on_add' => 'enabled', 'on_edit' => 'enabled', 'on_list' => 'show'
    );

    $fields['created_by'] = array(
        'field_title' => 'Created by',
        'field_type'  => 'number',
        'field_value' => 'active_user',
        'field_size'  => 11,
        'input_type'  => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'users;user_name',
        'on_add' => 'hidden', 'on_edit' => 'not_set', 'on_list' => 'show'
    );

    $fields['creation_time'] = array(
        'field_title' => 'Created time',
        'field_type'  => 'text',
        'field_value' => 'normal',
        'input_type'  => 'date',
        'on_add' => 'not_set', 'on_edit' => 'not_set', 'on_list' => 'hidden'
    );

    $fields['constraint_active'] = array(
        'field_title' => 'Active',
        'field_type'  => '0_1',
        'field_value' => 'normal',
        'on_add' => 'not_set', 'on_edit' => 'not_set',
        'on_list' => 'hidden', 'on_view' => 'hidden'
    );

    $config['fields'] = $fields;
    return $config;
}
