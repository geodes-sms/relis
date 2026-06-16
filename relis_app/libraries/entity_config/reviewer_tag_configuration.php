<?php
/* ReLiS - Issue #103 - Reviewer tag entity configuration */

function get_reviewer_tag()
{
    $config['config_id']           = 'reviewer_tag';
    $config['table_name']          = 'reviewer_tag';
    $config['table_id']            = 'tag_id';
    $config['table_active_field']  = 'tag_active';
    $config['main_field']          = 'tag_name';
    $config['entity_label_plural'] = 'Reviewer Tags';
    $config['entity_label']        = 'Reviewer Tag';
    $config['order_by']            = ' tag_name ASC ';

    // ─── Fields ──────────────────────────────────────────────────
    $fields['tag_id'] = array(
        'field_title'   => '#',
        'field_type'    => 'int',
        'field_size'    => 11,
        'field_value'   => 'auto_increment',
        'default_value' => 'auto_increment'
    );

    $fields['tag_name'] = array(
        'field_title' => 'Name',
        'field_type'  => 'text',
        'field_size'  => 50,
        'input_type'  => 'text',
        'mandatory'   => ' mandatory '
    );

    $fields['tag_description'] = array(
        'field_title' => 'Description',
        'field_type'  => 'text',
        'field_size'  => 250,
        'input_type'  => 'textarea'
    );

    $fields['tag_color'] = array(
        'field_title'   => 'Color',
        'field_type'    => 'text',
        'field_size'    => 7,
        'input_type'    => 'text',
        'default_value' => '#888888'
    );

    $fields['tag_active'] = array(
        'field_title'   => 'Active',
        'field_type'    => 'int',
        'field_size'    => 1,
        'field_value'   => '1',
        'default_value' => '1'
    );

    $config['fields'] = $fields;

    // ─── Operations ──────────────────────────────────────────────
    $operations['add_reviewer_tag'] = array(
        'operation_type'            => 'Add',
        'operation_title'           => 'Add a new reviewer tag',
        'operation_description'     => 'Add a new reviewer tag',
        'page_title'                => 'Add a new reviewer tag',
        'save_function'             => 'element/save_element',
        'page_template'             => 'general/frm_reviewer_tag',
        'redirect_after_save'       => 'element/entity_list/list_reviewer_tag',
        'db_save_model'             => 'add_reviewer_tag',
        'generate_stored_procedure' => True,

        'fields' => array(
            'tag_id'          => array('mandatory' => '',          'field_state' => 'hidden'),
            'tag_name'        => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
            'tag_description' => array('mandatory' => '',          'field_state' => 'enabled'),
            'tag_color'       => array('mandatory' => '',          'field_state' => 'enabled'),
        ),

        'top_links' => array(
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'close', 'url' => 'home')
        ),
    );

    $operations['edit_reviewer_tag'] = array(
        'operation_type'            => 'Edit',
        'operation_title'           => 'Edit reviewer tag',
        'operation_description'     => 'Edit reviewer tag',
        'page_title'                => 'Edit reviewer tag',
        'save_function'             => 'element/save_element',
        'page_template'             => 'general/frm_reviewer_tag',
        'redirect_after_save'       => 'element/entity_list/list_reviewer_tag',
        'data_source'               => 'get_detail_reviewer_tag',
        'db_save_model'             => 'update_reviewer_tag',
        'generate_stored_procedure' => True,

        'fields' => array(
            'tag_id'          => array('mandatory' => '',          'field_state' => 'hidden'),
            'tag_name'        => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
            'tag_description' => array('mandatory' => '',          'field_state' => 'enabled'),
            'tag_color'       => array('mandatory' => '',          'field_state' => 'enabled'),
        ),

        'top_links' => array(
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'close', 'url' => 'home')
        ),
    );

    $operations['list_reviewer_tag'] = array(
        'operation_type'            => 'List',
        'operation_title'           => 'List reviewer tags',
        'page_title'                => 'Reviewer Tags',
        'data_source'               => 'get_list_reviewer_tag',
        'generate_stored_procedure' => True,

        'fields' => array(
            'tag_name' => array(
                'link' => array(
                    'url'      => 'element/display_element/detail_reviewer_tag/',
                    'id_field' => 'tag_id',
                    'trim'     => '0'
                )
            ),
            'tag_description' => array(),
            'tag_color'       => array(),
        ),
        'order_by' => 'tag_name ASC ',

        'list_links' => array(
            'view' => array(
                'label' => 'View',
                'title' => 'View tag',
                'icon'  => 'folder',
                'url'   => 'element/display_element/detail_reviewer_tag/',
            ),
            'edit' => array(
                'label' => 'Edit',
                'title' => 'Edit',
                'icon'  => 'pencil',
                'url'   => 'element/edit_element/edit_reviewer_tag/',
            ),
            'delete' => array(
                'label' => 'Delete',
                'title' => 'Delete the tag',
                'url'   => 'element/delete_element/remove_reviewer_tag/'
            ),
        ),

        'top_links' => array(
            'add' => array(
                'label' => '',
                'title' => 'Add a new tag',
                'icon'  => 'add',
                'url'   => 'element/add_element/add_reviewer_tag',
            ),
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'add', 'url' => 'home')
        ),
    );

    $operations['detail_reviewer_tag'] = array(
        'operation_type'            => 'Detail',
        'operation_title'           => 'Reviewer tag details',
        'page_title'                => 'Reviewer Tag',
        'data_source'               => 'get_detail_reviewer_tag',
        'generate_stored_procedure' => True,

        'fields' => array(
            'tag_name'        => array(),
            'tag_description' => array(),
            'tag_color'       => array(),
        ),

        'top_links' => array(
            'edit' => array(
                'label' => '', 'title' => 'Edit', 'icon' => 'edit',
                'url'   => 'element/edit_element/edit_reviewer_tag/~current_element~',
            ),
            'delete' => array(
                'label' => '', 'title' => 'Delete', 'icon' => 'trash',
                'url'   => 'element/delete_element/remove_reviewer_tag/~current_element~',
            ),
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'add', 'url' => 'home')
        ),
    );

    $operations['remove_reviewer_tag'] = array(
        'operation_type'            => 'Remove',
        'operation_title'           => 'Remove a reviewer tag',
        'redirect_after_delete'     => 'element/entity_list/list_reviewer_tag',
        'db_delete_model'           => 'remove_reviewer_tag',
        'generate_stored_procedure' => True,
    );

    $config['operations'] = $operations;
    return $config;
}