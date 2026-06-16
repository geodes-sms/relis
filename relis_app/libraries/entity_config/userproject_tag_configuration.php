<?php
/* ReLiS - Issue #103 - Userproject tag entity configuration */

function get_userproject_tag()
{
    $config['config_id']           = 'userproject_tag';
    $config['table_name']          = 'userproject_tag';
    $config['table_id']            = 'userproject_tag_id';
    $config['table_active_field']  = 'userproject_tag_active';
    $config['main_field']          = 'userproject_tag_id';
    $config['entity_label_plural'] = 'User Tag Assignments';
    $config['entity_label']        = 'User Tag Assignment';
    $config['order_by']            = ' userproject_tag_id DESC ';

    // ─── Fields ──────────────────────────────────────────────────
    $fields['userproject_tag_id'] = array(
        'field_title'   => '#',
        'field_type'    => 'int',
        'field_size'    => 11,
        'field_value'   => 'auto_increment',
        'default_value' => 'auto_increment'
    );

    $fields['user_id'] = array(
        'field_title'         => 'User',
        'field_type'          => 'int',
        'field_size'          => 11,
        'mandatory'           => ' mandatory ',
        'input_type'          => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'users;user_name',
    );

    $fields['tag_id'] = array(
        'field_title'         => 'Tag',
        'field_type'          => 'int',
        'field_size'          => 11,
        'mandatory'           => ' mandatory ',
        'input_type'          => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'reviewer_tag;tag_name',
    );

    $fields['userproject_tag_active'] = array(
        'field_title'   => 'Active',
        'field_type'    => 'int',
        'field_size'    => 1,
        'field_value'   => '1',
        'default_value' => '1'
    );

    $config['fields'] = $fields;

    // ─── Operations ──────────────────────────────────────────────
    $operations['add_userproject_tag'] = array(
        'operation_type'            => 'Add',
        'operation_title'           => 'Assign a tag to a user',
        'page_title'                => 'Assign tag to user',
        'save_function'             => 'element/save_element',
        'page_template'             => 'general/frm_userproject_tag',
        'redirect_after_save'       => 'element/entity_list/list_userproject_tag',
        'db_save_model'             => 'add_userproject_tag',
        'generate_stored_procedure' => True,

        'fields' => array(
            'userproject_tag_id' => array('mandatory' => '',          'field_state' => 'hidden'),
            'user_id'     => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
            'tag_id'             => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
        ),

        'top_links' => array(
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'close', 'url' => 'home')
        ),
    );

    $operations['edit_userproject_tag'] = array(
        'operation_type'            => 'Edit',
        'operation_title'           => 'Edit user tag assignment',
        'page_title'                => 'Edit user tag assignment',
        'save_function'             => 'element/save_element',
        'page_template'             => 'general/frm_userproject_tag',
        'redirect_after_save'       => 'element/entity_list/list_userproject_tag',
        'data_source'               => 'get_detail_userproject_tag',
        'db_save_model'             => 'update_userproject_tag',
        'generate_stored_procedure' => True,

        'fields' => array(
            'userproject_tag_id' => array('mandatory' => '',          'field_state' => 'hidden'),
            'user_id'     => array('mandatory' => 'mandatory', 'field_state' => 'disabled'),
            'tag_id'             => array('mandatory' => 'mandatory', 'field_state' => 'enabled'),
        ),

        'top_links' => array(
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'close', 'url' => 'home')
        ),
    );

    $operations['list_userproject_tag'] = array(
        'operation_type'            => 'List',
        'operation_title'           => 'List user tag assignments',
        'page_title'                => 'User Tag Assignments',
        'data_source'               => 'get_list_userproject_tag',
        'generate_stored_procedure' => True,

        'fields' => array(
            'userproject_tag_id' => array(
                'link' => array(
                    'url'      => 'element/display_element/detail_userproject_tag/',
                    'id_field' => 'userproject_tag_id',
                    'trim'     => '0'
                )
            ),
            'user_id' => array(),
            'tag_id'         => array(),
        ),
        'order_by' => 'userproject_tag_id DESC ',

        'list_links' => array(
            'view' => array(
                'label' => 'View',
                'title' => 'View',
                'icon'  => 'folder',
                'url'   => 'element/display_element/detail_userproject_tag/',
            ),
            'edit' => array(
                'label' => 'Edit',
                'title' => 'Edit',
                'icon'  => 'pencil',
                'url'   => 'element/edit_element/edit_userproject_tag/',
            ),
            'delete' => array(
                'label' => 'Delete',
                'title' => 'Remove tag from user',
                'url'   => 'element/delete_element/remove_userproject_tag/',
            ),
        ),

        'top_links' => array(
            'add' => array(
                'label' => '', 'title' => 'Assign a tag to a user', 'icon' => 'add',
                'url'   => 'element/add_element/add_userproject_tag',
            ),
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'add', 'url' => 'home')
        ),
    );

    $operations['detail_userproject_tag'] = array(
        'operation_type'            => 'Detail',
        'operation_title'           => 'User tag assignment details',
        'page_title'                => 'User Tag Assignment',
        'data_source'               => 'get_detail_userproject_tag',
        'generate_stored_procedure' => True,

        'fields' => array(
            'user_id' => array(),
            'tag_id'         => array(),
        ),

        'top_links' => array(
            'edit' => array(
                'label' => '', 'title' => 'Edit', 'icon' => 'edit',
                'url'   => 'element/edit_element/edit_userproject_tag/~current_element~',
            ),
            'delete' => array(
                'label' => '', 'title' => 'Delete', 'icon' => 'trash',
                'url'   => 'element/delete_element/remove_userproject_tag/~current_element~',
            ),
            'back' => array('label' => '', 'title' => 'Close', 'icon' => 'add', 'url' => 'home')
        ),
    );

    $operations['remove_userproject_tag'] = array(
        'operation_type'            => 'Remove',
        'operation_title'           => 'Remove a user tag assignment',
        'redirect_after_delete'     => 'element/entity_list/list_userproject_tag',
        'db_delete_model'           => 'remove_userproject_tag',
        'generate_stored_procedure' => True,
    );

    $config['operations'] = $operations;
    return $config;
}