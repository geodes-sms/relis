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
 *  :Author: LLM Integration Team
 */

defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Configuration for LLM (Large Language Model) settings
 * This manages LLM provider configuration for AI-powered screening
 */
function get_config_llm_configuration()
{
    $config['config_id'] = 'llm_config';
    $config['table_name'] = 'llm_config';
    $config['table_id'] = 'llm_config_id';
    $config['table_active_field'] = 'llm_config_active';
    $config['main_field'] = 'provider_name';
    
    $config['entity_label'] = 'LLM Configuration';
    $config['entity_label_plural'] = 'LLM Configurations';
    
    // List view configuration
    $config['order_by'] = 'llm_config_id ASC';
    
    // Define fields
    $fields['llm_config_id'] = array(
        'field_title' => '#',
        'field_type' => 'int',
        'field_size' => 11,
        'field_value' => 'auto_increment',
        'default_value' => 'auto_increment'
    );
    
    $fields['provider_name'] = array(
        'field_title' => 'Provider',
        'field_type' => 'text',
        'field_size' => 50,
        'input_type' => 'select',
        'input_select_source' => 'array',
        'input_select_values' => array(
            'openai' => 'OpenAI (GPT)',
            'anthropic' => 'Anthropic (Claude)',
            'google' => 'Google (Gemini)',
            'openrouter' => 'OpenRouter (Multiple Models)'
        ),
        'mandatory' => 'mandatory',
        'field_value' => 'openai',
        'default_value' => 'openai'
    );
    
    $fields['model_name'] = array(
        'field_title' => 'Model',
        'field_type' => 'text',
        'field_size' => 100,
        'input_type' => 'select',
        'input_select_source' => 'array',
        'input_select_values' => array(
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            'gpt-4' => 'GPT-4',
            'gpt-4-turbo' => 'GPT-4 Turbo',
            'claude-3-sonnet' => 'Claude-3 Sonnet',
            'claude-3-haiku' => 'Claude-3 Haiku',
            'gemini-pro' => 'Gemini Pro',
            'openai/gpt-3.5-turbo' => 'OpenRouter: GPT-3.5 Turbo',
            'openai/gpt-4' => 'OpenRouter: GPT-4',
            'anthropic/claude-3-sonnet' => 'OpenRouter: Claude-3 Sonnet',
            'anthropic/claude-3-haiku' => 'OpenRouter: Claude-3 Haiku',
            'google/gemini-pro' => 'OpenRouter: Gemini Pro'
        ),
        'mandatory' => 'mandatory',
        'field_value' => 'gpt-3.5-turbo',
        'default_value' => 'gpt-3.5-turbo'
    );
    
    $fields['api_key'] = array(
        'field_title' => 'API Key',
        'field_type' => 'text',
        'field_size' => 255,
        'input_type' => 'password',
        'mandatory' => 'mandatory',
        'field_value' => '',
        'not_in_db' => true, // Don't store in database for security
        'input_help' => 'API key is not stored permanently for security'
    );
    
    $fields['api_endpoint'] = array(
        'field_title' => 'API Endpoint',
        'field_type' => 'text',
        'field_size' => 255,
        'input_type' => 'text',
        'field_value' => '',
        'default_value' => '',
        'input_help' => 'Custom API endpoint (optional)'
    );
    
    $fields['max_tokens'] = array(
        'field_title' => 'Max Tokens',
        'field_type' => 'int',
        'field_size' => 11,
        'input_type' => 'number',
        'field_value' => '1000',
        'default_value' => '1000',
        'input_help' => 'Maximum tokens for LLM responses'
    );
    
    $fields['temperature'] = array(
        'field_title' => 'Temperature',
        'field_type' => 'decimal',
        'field_size' => '3,2',
        'input_type' => 'number',
        'field_value' => '0.7',
        'default_value' => '0.7',
        'input_help' => 'Creativity level (0.0 = focused, 1.0 = creative)'
    );
    
    $fields['is_active'] = array(
        'field_title' => 'Active',
        'field_type' => 'int',
        'field_size' => 1,
        'input_type' => 'select',
        'input_select_source' => 'array',
        'input_select_values' => array(
            '1' => 'Yes',
            '0' => 'No'
        ),
        'field_value' => '1',
        'default_value' => '1'
    );
    
    $fields['cost_per_1k_tokens'] = array(
        'field_title' => 'Cost per 1K Tokens ($)',
        'field_type' => 'decimal',
        'field_size' => '10,6',
        'input_type' => 'number',
        'field_value' => '0.002',
        'default_value' => '0.002',
        'input_help' => 'Cost per 1000 tokens for cost estimation'
    );
    
    $fields['added_by'] = array(
        'field_title' => 'Created by',
        'field_type' => 'number',
        'field_size' => 11,
        'field_value' => active_user_id(),
        'input_type' => 'select',
        'input_select_source' => 'table',
        'input_select_values' => 'users;user_name',
        'mandatory' => 'mandatory'
    );
    
    $fields['add_time'] = array(
        'field_title' => 'Created on',
        'field_type' => 'time',
        'default_value' => 'CURRENT_TIMESTAMP',
        'field_value' => bm_current_time('Y-m-d H:i:s'),
        'field_size' => 20,
        'mandatory' => 'mandatory'
    );
    
    $fields['llm_config_active'] = array(
        'field_title' => 'Active',
        'field_type' => 'int',
        'field_size' => '1',
        'field_value' => '1',
        'default_value' => '1'
    );
    
    $config['fields'] = $fields;
    
    // Define operations
    $operations['list_llm_configs'] = array(
        'operation_type' => 'List',
        'operation_title' => 'List LLM Configurations',
        'operation_description' => 'List all LLM provider configurations',
        'page_title' => 'LLM Configurations',
        'data_source' => 'get_list_llm_configs',
        'generate_stored_procedure' => true,
        'fields' => array(
            'llm_config_id' => array(),
            'provider_name' => array(
                'link' => array(
                    'url' => 'element/display_element/detail_llm_config/',
                    'id_field' => 'llm_config_id',
                    'trim' => '0'
                )
            ),
            'model_name' => array(),
            'is_active' => array(),
            'cost_per_1k_tokens' => array(),
            'add_time' => array()
        ),
        'order_by' => 'llm_config_id DESC',
        'search_by' => 'provider_name,model_name',
        'list_links' => array(
            'edit' => array(
                'label' => 'Edit',
                'title' => 'Edit LLM Configuration',
                'id_field' => 'llm_config_id',
                'url' => 'element/edit_element/edit_llm_config/'
            ),
            'delete' => array(
                'label' => 'Remove',
                'title' => 'Delete LLM Configuration',
                'url' => 'element/delete_element/remove_llm_config/'
            ),
            'test' => array(
                'label' => 'Test',
                'title' => 'Test LLM Configuration',
                'url' => 'admin/test_llm_config/'
            )
        ),
        'top_links' => array(
            'add_llm' => array(
                'label' => '+ Add LLM Configuration',
                'title' => 'Add new LLM provider configuration',
                'url' => 'element/add_element/add_llm_config/',
                'icon' => 'plus'
            )
        )
    );
    
    $operations['add_llm_config'] = array(
        'operation_type' => 'Add',
        'operation_title' => 'Add LLM Configuration',
        'operation_description' => 'Add new LLM provider configuration',
        'page_title' => 'Add LLM Configuration',
        'fields' => array(
            'provider_name' => array(),
            'model_name' => array(),
            'api_key' => array(),
            'api_endpoint' => array(),
            'max_tokens' => array(),
            'temperature' => array(),
            'is_active' => array(),
            'cost_per_1k_tokens' => array(),
            'description' => array()
        ),
        'redirect_after_save' => 'element/list_elements/list_llm_configs'
    );
    
    $operations['edit_llm_config'] = array(
        'operation_type' => 'Edit',
        'operation_title' => 'Edit LLM Configuration',
        'operation_description' => 'Edit existing LLM provider configuration',
        'page_title' => 'Edit LLM Configuration',
        'fields' => array(
            'provider_name' => array(),
            'model_name' => array(),
            'api_key' => array(),
            'api_endpoint' => array(),
            'max_tokens' => array(),
            'temperature' => array(),
            'is_active' => array(),
            'cost_per_1k_tokens' => array(),
            'description' => array()
        ),
        'redirect_after_save' => 'element/list_elements/list_llm_configs'
    );
    
    $operations['detail_llm_config'] = array(
        'operation_type' => 'Detail',
        'operation_title' => 'LLM Configuration Details',
        'operation_description' => 'View LLM configuration details',
        'page_title' => 'LLM Configuration Details',
        'fields' => array(
            'provider_name' => array(),
            'model_name' => array(),
            'api_endpoint' => array(),
            'max_tokens' => array(),
            'temperature' => array(),
            'is_active' => array(),
            'cost_per_1k_tokens' => array(),
            'description' => array(),
            'added_by' => array(),
            'add_time' => array()
        )
    );
    
    $config['operations'] = $operations;
    
    return $config;
}
