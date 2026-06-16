<?php
/* ReLiS - Issue #103 - Assignment constraint entity configuration */

function get_assignment_constraint()
{
    $config['config_id']           = 'assignment_constraint';
    $config['table_name']          = 'assignment_constraint';
    $config['table_id']            = 'constraint_id';
    $config['table_active_field']  = 'constraint_active';
    $config['main_field']          = 'constraint_type';
    $config['entity_label_plural'] = 'Assignment Rules';
    $config['entity_label']        = 'Assignment Rule';
    $config['order_by']            = ' constraint_priority ASC, constraint_id DESC ';

    // ─── Fields ──────────────────────────────────────────────────
    $fields['constraint_id'] = array(
        'field_title'   => '#',
        'field_type'    => 'int',
        'field_size'    => 11,
        'field_value'   => 'auto_increment',
        'default_value' => 'auto_increment'
    );

    $fields['constraint_scope'] = array(
        'field_title'         => 'Scope',
        'field_type'          => 'text',
        'field_size'          => 40,
        'mandatory'           => ' mandatory ',
        'input_type'          => 'select',
        'input_select_source' => 'array',
        'input_select_values' => array(
            'screening'                 => 'Screening',
            'screening_validation'      => 'Screening validation',
            'qa'                        => 'Quality assessment',
            'qa_validation'             => 'QA validation',
            'classification'            => 'Classification',
            'classification_validation' => 'Classification validation',
        ),
    );

    $fields['phase_id'] = array(
        'field_title'         => 'Phase',
        'field_type'          => 'int',
        'field_size'          => 11,
        'input_type'          => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'screen_phase;phase_title',
    );

    $fields['constraint_type'] = array(
        'field_title'         => 'Type',
        'field_type'          => 'text',
        'field_size'          => 60,
        'mandatory'           => ' mandatory ',
        'input_type'          => 'select',
        'input_select_source' => 'array',
        'input_select_values' => array(
            'min_tag_per_paper'                        => 'Min N reviewers with tag',
            'max_tag_per_paper'                        => 'Max N reviewers with tag',
            'tag_combination'                          => 'Tag combination (OR)',
            'same_user_from_previous_phase'            => 'Same user from previous phase',
            'force_different_user_from_previous_phase' => 'Force different user from previous phase',
        ),
    );

    $fields['constraint_params'] = array(
        'field_title' => 'Parameters',
        'field_type'  => 'text',
        'input_type'  => 'textarea',
    );

    $fields['constraint_priority'] = array(
        'field_title'   => 'Priority',
        'field_type'    => 'int',
        'field_size'    => 11,
        'input_type'    => 'number',
        'default_value' => 100
    );

    $fields['constraint_active'] = array(
        'field_title'   => 'Active',
        'field_type'    => 'int',
        'field_size'    => 1,
        'field_value'   => '1',
        'default_value' => '1'
    );

    $config['fields'] = $fields;

    // ─── Operations ──────────────────────────────────────────────
    $operations['add_assignment_constraint'] = array(
        'operation_type'            => 'Add',
        'operation_title'           => 'Add a new assignment rule',
        'page_title'                => 'Add a new assignment rule',
        'save_function' => 'element/save_assignment_constraint',
        'page_template' => 'general/frm_assignment_constraint',
        'redirect_after_save'       => 'element/entity_list/list_assignment_constraint',
        'db_save_model'             => 'add_assignment_constraint',
        'generate_stored_procedure' => True,

        'fields' => array(
            'constraint_id'       => array('mandatory' => '',          'field_state' => 'hidden'),
            'constraint_scope'    => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
            'phase_id'            => array('mandatory' => '',          'field_state' => 'enabled'),
            'constraint_type'     => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
            'constraint_params'   => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
            'constraint_priority' => array('mandatory' => '',          'field_state' => 'enabled'),
        ),

        'top_links' => array(
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'close', 'url' => 'home')
        ),
    );

    $operations['edit_assignment_constraint'] = array(
        'operation_type'            => 'Edit',
        'operation_title'           => 'Edit assignment rule',
        'page_title'                => 'Edit assignment rule',
        'save_function' => 'element/save_assignment_constraint',
        'page_template' => 'general/frm_assignment_constraint',
        'redirect_after_save'       => 'element/entity_list/list_assignment_constraint',
        'data_source'               => 'get_detail_assignment_constraint',
        'db_save_model'             => 'update_assignment_constraint',
        'generate_stored_procedure' => True,

        'fields' => array(
            'constraint_id'       => array('mandatory' => '',          'field_state' => 'hidden'),
            'constraint_scope'    => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
            'phase_id'            => array('mandatory' => '',          'field_state' => 'enabled'),
            'constraint_type'     => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
            'constraint_params'   => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
            'constraint_priority' => array('mandatory' => '',          'field_state' => 'enabled'),
        ),

        'top_links' => array(
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'close', 'url' => 'home')
        ),
    );

    $operations['list_assignment_constraint'] = array(
        'operation_type'            => 'List',
        'operation_title'           => 'List assignment rules',
        'page_title'                => 'Assignment Rules',
        'data_source'               => 'get_list_assignment_constraint',
        'generate_stored_procedure' => True,

        'fields' => array(
            'constraint_id' => array(
                'link' => array(
                    'url'      => 'element/display_element/detail_assignment_constraint/',
                    'id_field' => 'constraint_id',
                    'trim'     => '0'
                )
            ),
            'constraint_scope'    => array(),
            'constraint_type'     => array(),
            'constraint_priority' => array(),
        ),
        'order_by' => 'constraint_priority ASC, constraint_id DESC ',

        'list_links' => array(
            'view' => array(
                'label' => 'View',
                'title' => 'View',
                'icon'  => 'folder',
                'url'   => 'element/display_element/detail_assignment_constraint/',
            ),
            'edit' => array(
                'label' => 'Edit',
                'title' => 'Edit',
                'icon'  => 'pencil',
                'url'   => 'element/edit_element/edit_assignment_constraint/',
            ),
            'delete' => array(
                'label' => 'Delete',
                'title' => 'Delete rule',
                'url'   => 'element/delete_element/remove_assignment_constraint/'
            ),
        ),

        'top_links' => array(
            'add' => array(
                'label' => '', 'title' => 'Add a new rule', 'icon' => 'add',
                'url'   => 'element/add_element/add_assignment_constraint',
            ),
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'add', 'url' => 'home')
        ),
    );

    $operations['detail_assignment_constraint'] = array(
        'operation_type'            => 'Detail',
        'operation_title'           => 'Assignment rule details',
        'page_title'                => 'Assignment Rule',
        'page_template' => 'general/detail_assignment_constraint',
        'data_source'               => 'get_detail_assignment_constraint',
        'generate_stored_procedure' => True,

        'fields' => array(
            'constraint_scope'    => array(),
            'phase_id'            => array(),
            'constraint_type'     => array(),
            'constraint_params'   => array(),
            'constraint_priority' => array(),
        ),

        'top_links' => array(
            'edit' => array(
                'label' => '', 'title' => 'Edit', 'icon' => 'edit',
                'url'   => 'element/edit_element/edit_assignment_constraint/~current_element~',
            ),
            'delete' => array(
                'label' => '', 'title' => 'Delete', 'icon' => 'trash',
                'url'   => 'element/delete_element/remove_assignment_constraint/~current_element~',
            ),
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'add', 'url' => 'home')
        ),
    );

    $operations['remove_assignment_constraint'] = array(
        'operation_type'            => 'Remove',
        'operation_title'           => 'Remove an assignment rule',
        'redirect_after_delete'     => 'element/entity_list/list_assignment_constraint',
        'db_delete_model'           => 'remove_assignment_constraint',
        'generate_stored_procedure' => True,
    );

    $config['operations'] = $operations;
    return $config;
}