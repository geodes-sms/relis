<?php
/* ReLiS - A Tool for conducting systematic literature reviews and mapping studies.
 * Copyright (C) 2018  Eugene Syriani
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * --------------------------------------------------------------------------
 *
 *  :Author: Brice Michel Bigendako
 */

/*
	This function returns a configuration array for managing flags in a system.
*/
function get_flags()
{
    $config['config_id'] = 'flags';
    $config['table_name'] = 'flag';
    $config['table_id'] = 'id';
    $config['table_active_field'] = 'flag_active';

    $config['entity_label_plural'] = 'Flags';
    $config['entity_label'] = 'Flag';
    $config['reference_title'] = 'Flags';
    $config['reference_title_min'] = 'Flag';

    $config['links']['view'] = array(
        'url' => 'flag/display_flag/',
        'label' => 'View',
        'title' => 'View',
        'on_list' => True,
        'on_view' => True
    );

    // List view configuration
    $config['order_by'] = ' id ASC ';
    $config['search_by'] = 'id,paper_id,flag_category_id';


    // Definition of Fields
    $fields['id'] = array(
        'field_title' => '#',
        'field_type' => 'int',
        'field_size' => 11,
        'field_value' => 'auto_increment',
        'on_list' => 'show',
        'default_value' => 'auto_increment'
    );

    $fields['paper_id'] = array(
        'field_title' => 'Paper',
        'field_type' => 'int',
        'field_size' => 11,
        'input_type' => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'papers;title',
        'on_list' => 'show',
        'mandatory' => ' mandatory '
    );

    $fields['flag_category_id'] = array(
        'field_title' => 'Flag',
        'field_type' => 'int',
        'field_size' => 11,
        'input_type' => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'flag_category;ref_value',
        'on_list' => 'show',
        'mandatory' => ' mandatory '
    );

    $fields['added_by'] = array(
        'field_title' => 'Added by',
        'field_type' => 'number',
        'field_size' => 11,
        'field_value' => active_user_id(),
        'input_type' => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'users;user_name',
        'on_list' => 'hidden',
    );

    $fields['timestamp'] = array(
        'field_title' => 'Timestamp',
        'field_type' => 'time',
        'default_value' => 'CURRENT_TIMESTAMP',
        'field_value' => bm_current_time('Y-m-d H:i:s'),
        'field_size' => 20,
        'mandatory' => ' mandatory ',
        'on_list' => 'show',
    );

    $fields['flag_active'] = array(
        'field_title' => 'Active',
        'field_type' => 'int',
        'field_size' => '1',
        'field_value' => '1',
        'default_value' => '1',
        'on_list' => 'hidden',
    );

    $config['fields'] = $fields;

    $operations['list_flagged_papers'] = array(
        'operation_type' => 'List',
        'operation_title' => 'List flagged papers',
        'operation_description' => 'List flagged papers',
        'page_title' => 'Flagged Papers',

        'table_display_style' => 'dynamic_table',

        'data_source' => 'get_list_flagged_papers',
        'generate_stored_procedure' => True,

        'fields' => array(
            'id'=>array(),
            'paper_id' => array(
                'link' => array(
                    'url' => 'screening/display_paper_screen/',
                    'id_field' => 'paper_id',
                    'trim' => trim_nbr_car()
                )
            ),
            'flag_category_id' => array(),
            'added_by' => array(),
            'timestamp' => array(),
        ),
        'order_by' => 'id ASC ',
        'search_by' => 'id',

        'list_links' => array(
            'edit' => array(
                'label' => 'Edit',
                'title' => 'Edit',
                'icon' => 'edit',
                'url' => 'element/edit_element/edit_flag/',
            ),
            'delete' => array(
                'label' => 'Delete',
                'title' => 'Delete the flag',
                'url' => 'element/delete_element/remove_flag/'
            )
        )
    );

    $operations['remove_flag'] = array(
        'operation_type' => 'Remove',
        'operation_title' => 'Remove a Flag',
        'operation_description' => 'Remove an flag from the displayed list',
        'redirect_after_delete' => 'element/entity_list/list_flagged_papers',
        'db_delete_model' => 'remove_flag',
        'generate_stored_procedure' => True,
    );

    $operations['edit_flag'] = array(
        'operation_type' => 'Edit',
        'operation_title' => 'Edit flag',
        'operation_description' => 'Edit flag',
        'page_title' => 'Edit flag ',
        'save_function' => 'element/save_element',
        'page_template' => 'general/frm_entity',
        // 'redirect_after_save'=>'element/entity_list/list_papers',
        // 'redirect_after_save'=>'element/entity_list/list_all_papers',
        // Editing from screening or all papers
        'redirect_after_save' => 'element/entity_list/list_flagged_papers',
        'data_source' => 'get_detail_flags',
        'db_save_model' => 'update_flag',

        //'display_reset_button'=>true,
        //'submit_button_title'=>'Save',

        'generate_stored_procedure' => True,

        'fields' => array(
            'id' => array('mandatory' => '', 'field_state' => 'hidden'),
            'paper_id' => array('mandatory' => '', 'field_state' => 'disabled'),
            'flag_category_id' => array('mandatory' => '', 'field_state' => 'enabled'),
            //'added_by' => array('mandatory' => '', 'field_state' => 'hidden'),
            //'timestamp' => array('mandatory' => '', 'field_state' => 'hidden'),
        ),

        'top_links' => array(

            'back' => array(
                'label' => '',
                'title' => 'Close',
                'icon' => 'close',
                'url' => 'home',
            )

        ),

    );

    $operations['detail_flag'] = array(
        'operation_type' => 'Detail',
        'operation_title' => 'Characteristics of a flag',
        'operation_description' => 'Characteristics of a flag',
        'page_title' => 'Flag ',

        'data_source' => 'get_detail_flags',
        'generate_stored_procedure' => True,

        'fields' => array(
            'id' => array(),
            'paper_id' => array(),
            'flag_category_id' => array(),
            'added_by' => array(),
            'timestamp' => array(),
        ),


        'top_links' => array(
            'back' => array(
                'label' => '',
                'title' => 'Close',
                'icon' => 'add',
                'url' => 'home',
            ),
        ),
    );

    $config['operations'] = $operations;

    return $config;
}