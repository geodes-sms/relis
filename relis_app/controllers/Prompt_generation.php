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
 *  :Author: AI Assistant
 * --------------------------------------------------------------------------
 *
 * This controller handles prompt generation functionality for systematic literature reviews.
 * It allows users to generate prompts based on existing database values and templates.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Prompt_generation extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('DBConnection_mdl');
        $this->load->library('manager_lib');
        $this->load->library('template_manager');
        $this->load->helper('url');
        $this->load->helper('form');
        // Load default database connection for permission checking
        $this->load->database();
        // Load project-specific database connection for data operations
        $this->project_db = $this->load->database(project_db(), TRUE);
    }

    /**
     * Main prompt generation interface
     */
    public function index()
    {
        // Check permissions
        if (!can_manage_project()) {
            redirect('home');
        }

        // Get project information
        $data['project_info'] = $this->get_project_info();
        
        // Get inclusion criteria
        $data['inclusion_criteria'] = $this->get_inclusion_criteria();
        
        // Get exclusion criteria
        $data['exclusion_criteria'] = $this->get_exclusion_criteria();
        
        // Get available templates
        $data['templates'] = $this->template_manager->get_available_templates();
        
        // Get LLM configurations
        $data['llm_configs'] = $this->get_llm_configurations();
        
        // Get research questions
        $data['research_questions'] = $this->get_research_questions();
        
        // Get key concepts
        $data['key_concepts'] = $this->get_key_concepts();

        // Get SLR topic for display in the view
        $data['slr_topic'] = $this->get_slr_topic();
        
        // Set page data
        $data['page_title'] = 'Prompt Generation';
        $data['page'] = 'prompt_generation/index';
        $data['left_menu_admin'] = true;
        
        // Load the view
        $this->load->view('shared/body', $data);
    }

    /**
     * Generate prompt based on selected template and data
     */
    public function generate_prompt()
    {
        if (!can_manage_project()) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $template_id = $this->input->post('template_id');
        $llm_config_id = $this->input->post('llm_config_id');
        $include_criteria_ids = $this->input->post('include_criteria_ids') ?: [];
        $exclude_criteria_ids = $this->input->post('exclude_criteria_ids') ?: [];
        $custom_variables = $this->input->post('custom_variables') ?: [];

        try {
            // Get project data with criteria
            $project_data = $this->get_project_data_for_prompt($include_criteria_ids, $exclude_criteria_ids);
            
            // Get LLM config
            $llm_config = $this->get_llm_config($llm_config_id);
            
            // Generate the prompt using template manager
            $generated_prompt = $this->template_manager->build_prompt($template_id, $project_data, $custom_variables);
            
            // Save generated prompt
            $prompt_id = $this->save_generated_prompt($generated_prompt, $template_id, $llm_config_id);
            
            $template = $this->template_manager->get_template($template_id);
            $response = [
                'success' => true,
                'prompt' => $generated_prompt,
                'prompt_id' => $prompt_id,
                'template_name' => $template['name'],
                'llm_config_name' => $llm_config ? ($llm_config['provider_name'] . ' - ' . $llm_config['model_name']) : 'None'
            ];
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        $this->output->set_content_type('application/json');
        echo json_encode($response);
    }

    /**
     * Preview prompt without saving
     */
    public function preview_prompt()
    {
        if (!can_manage_project()) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $template_id = $this->input->post('template_id');
        $include_criteria_ids = $this->input->post('include_criteria_ids') ?: [];
        $exclude_criteria_ids = $this->input->post('exclude_criteria_ids') ?: [];
        $custom_variables = $this->input->post('custom_variables') ?: [];

        try {
            // Get project data with criteria
            $project_data = $this->get_project_data_for_prompt($include_criteria_ids, $exclude_criteria_ids);
            
            // Generate the prompt using template manager
            $generated_prompt = $this->template_manager->build_prompt($template_id, $project_data, $custom_variables);
            
            $template = $this->template_manager->get_template($template_id);
            $response = [
                'success' => true,
                'prompt' => $generated_prompt,
                'template_name' => $template['name']
            ];
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        $this->output->set_content_type('application/json');
        echo json_encode($response);
    }

    /**
     * Get list of saved prompts
     */
    public function get_saved_prompts()
    {
        if (!can_manage_project()) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        try {
            $prompts = $this->get_saved_prompts_from_db();
            $response = [
                'success' => true,
                'prompts' => $prompts
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        $this->output->set_content_type('application/json');
        echo json_encode($response);
    }

    /**
     * Get a single prompt by ID
     */
    public function get_prompt()
    {
        if (!can_manage_project()) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $prompt_id = $this->input->get('prompt_id');
        
        if (empty($prompt_id)) {
            $response = [
                'success' => false,
                'error' => 'Prompt ID is required'
            ];
        } else {
            try {
                $prompt = $this->get_prompt_from_db($prompt_id);
                if ($prompt) {
                    $response = [
                        'success' => true,
                        'prompt' => $prompt
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'error' => 'Prompt not found'
                    ];
                }
            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        $this->output->set_content_type('application/json');
        echo json_encode($response);
    }

    /**
     * Get a single prompt from database
     */
    private function get_prompt_from_db($prompt_id)
    {
        $project_id = active_project_id();
        
        // Get prompt from project database
        $result = $this->project_db->where('prompt_id', $prompt_id)
                          ->where('project_id', $project_id)
                          ->get('generated_prompts');
        $prompt = $result->row_array();
        
        if (!$prompt) {
            return null;
        }
        
        // Get user name from main database
        if (!empty($prompt['created_by'])) {
            $user_result = $this->db->select('user_name')
                                  ->where('user_id', $prompt['created_by'])
                                  ->get('users');
            $user = $user_result->row_array();
            $prompt['user_name'] = $user ? $user['user_name'] : 'Unknown User';
        } else {
            $prompt['user_name'] = 'Unknown User';
        }
        
        return $prompt;
    }

    /**
     * Delete a saved prompt
     */
    public function delete_prompt()
    {
        if (!can_manage_project()) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $prompt_id = $this->input->post('prompt_id');
        
        try {
            $this->delete_prompt_from_db($prompt_id);
            $response = [
                'success' => true,
                'message' => 'Prompt deleted successfully'
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        $this->output->set_content_type('application/json');
        echo json_encode($response);
    }

    /**
     * Save edited prompt
     */
    public function save_edited_prompt()
    {
        if (!can_manage_project()) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $edited_prompt = $this->input->post('edited_prompt');
        $existing_prompt_id = $this->input->post('prompt_id');
        $template_id = $this->input->post('template_id');
        $llm_config_id = $this->input->post('llm_config_id');
        
        if (empty($edited_prompt)) {
            $response = [
                'success' => false,
                'error' => 'Prompt content is required'
            ];
        } else {
            try {
                if (!empty($existing_prompt_id)) {
                    // Update existing prompt
                    $this->update_prompt_in_db($existing_prompt_id, $edited_prompt, $template_id, $llm_config_id);
                    $prompt_id = $existing_prompt_id;
                } else {
                    // Save as new if no existing prompt id
                    $prompt_id = $this->save_generated_prompt($edited_prompt, $template_id, $llm_config_id);
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Edited prompt saved successfully',
                    'prompt_id' => $prompt_id
                ];
            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        $this->output->set_content_type('application/json');
        echo json_encode($response);
    }

    /**
     * Get project information
     */
    private function get_project_info()
    {
        $project_id = active_project_id();
        $result = $this->db->where('project_id', $project_id)
                          ->get('projects');
        return $result->row_array();
    }

    /**
     * Get inclusion criteria
     */
    private function get_inclusion_criteria()
    {
        $result = $this->project_db->where('ref_active', 1)
                          ->order_by('criteria_category', 'ASC')
                          ->order_by('ref_value', 'ASC')
                          ->get('ref_inclusioncriteria');
        return $result->result_array();
    }

    /**
     * Get exclusion criteria
     */
    private function get_exclusion_criteria()
    {
        $result = $this->project_db->where('ref_active', 1)
                          ->order_by('criteria_category', 'ASC')
                          ->order_by('ref_value', 'ASC')
                          ->get('ref_exclusioncrieria');
        return $result->result_array();
    }


    /**
     * Get LLM configurations
     */
    private function get_llm_configurations()
    {
        $result = $this->project_db->where('is_active', 1)
                          ->where('llm_config_active', 1)
                          ->order_by('provider_name', 'ASC')
                          ->order_by('model_name', 'ASC')
                          ->get('llm_config');
        return $result->result_array();
    }


    /**
     * Get project data for prompt generation
     */
    private function get_project_data_for_prompt($include_criteria_ids = [], $exclude_criteria_ids = [])
    {
        $project_info = $this->get_project_info();
        
        $data = [
            'project_title' => $project_info['project_title'] ?? 'Systematic Literature Review',
            'project_description' => $project_info['project_description'] ?? '',
            'slr_topic' => $this->get_slr_topic(),
            'research_questions' => $this->get_research_questions(),
            'key_concepts' => $this->get_key_concepts()
        ];
        
        // Always load all criteria for template generation
        // If specific IDs are provided, filter them; otherwise use all active criteria
        if (!empty($include_criteria_ids) || !empty($exclude_criteria_ids)) {
            $criteria_data = $this->get_criteria_data_for_prompt($include_criteria_ids, $exclude_criteria_ids);
            $data['inclusion_criteria'] = $criteria_data['inclusion'];
            $data['exclusion_criteria'] = $criteria_data['exclusion'];
        } else {
            // Load all active criteria for template generation
            $data['inclusion_criteria'] = $this->get_inclusion_criteria();
            $data['exclusion_criteria'] = $this->get_exclusion_criteria();
        }
        
        return $data;
    }

    /**
     * Get criteria data for prompt generation
     */
    private function get_criteria_data_for_prompt($include_ids, $exclude_ids)
    {
        $inclusion_criteria = [];
        $exclusion_criteria = [];

        if (!empty($include_ids)) {
            $result = $this->project_db->where_in('ref_id', array_map('intval', $include_ids))
                              ->get('ref_inclusioncriteria');
            $inclusion_criteria = $result->result_array();
        }

        if (!empty($exclude_ids)) {
            $result = $this->project_db->where_in('ref_id', array_map('intval', $exclude_ids))
                              ->get('ref_exclusioncrieria');
            $exclusion_criteria = $result->result_array();
        }

        return [
            'inclusion' => $inclusion_criteria,
            'exclusion' => $exclusion_criteria
        ];
    }

    /**
     * Get LLM configuration
     */
    private function get_llm_config($llm_config_id)
    {
        $result = $this->project_db->where('llm_config_id', $llm_config_id)
                          ->get('llm_config');
        return $result->row_array();
    }


    /**
     * Get research questions
     */
    private function get_research_questions()
    {
        // Check if the research_question table exists (singular)
        $table_exists = $this->project_db->query("SHOW TABLES LIKE 'research_question'")->num_rows() > 0;
        
        if (!$table_exists) {
            // Table doesn't exist, return empty array
            return [];
        }
        
        // The research_question table uses standard RELIS reference table structure
        // with ref_id, ref_value, ref_desc, ref_active columns
        $result = $this->project_db->where('ref_active', 1)
                          ->order_by('ref_id', 'ASC')
                          ->get('research_question');
        
        // Transform the data to match expected format
        $questions = $result->result_array();
        $formatted_questions = [];
        
        foreach ($questions as $index => $question) {
            $formatted_questions[] = [
                'question_id' => $question['ref_id'],
                'question_text' => $question['ref_value'],
                'question_type' => 'primary', // Default type since not specified in table
                'question_order' => $index + 1,
                'question_description' => $question['ref_desc'] ?? '',
                'is_active' => $question['ref_active']
            ];
        }
        
        return $formatted_questions;
    }

    /**
     * Get SLR topic from config table
     */
    private function get_slr_topic()
    {
        $result = $this->project_db->select('project_topic')
                          ->where('config_active', 1)
                          ->get('config');
        $config = $result->row_array();
        
        return $config['project_topic'] ?? '';
    }

    /**
     * Get key concepts from config table
     */
    private function get_key_concepts()
    {
        // Key concepts are stored in the config table as project_key_concepts
        $result = $this->project_db->select('project_key_concepts')
                          ->where('config_active', 1)
                          ->get('config');
        $config = $result->row_array();
        
        if (!empty($config['project_key_concepts'])) {
            // Parse the key concepts text into structured format
            $key_concepts_text = $config['project_key_concepts'];
            
            // For now, return as a simple array with the text
            // In the future, this could be parsed into structured concepts
            return [
                [
                    'concept_name' => 'SLR Key Concepts',
                    'concept_definition' => $key_concepts_text,
                    'concept_category' => 'general',
                    'concept_order' => 1
                ]
            ];
        }
        
        return [];
    }

    /**
     * Save generated prompt to database
     */
    private function save_generated_prompt($prompt, $template_id, $llm_config_id)
    {
        $data = [
            'project_id' => active_project_id(),
            'template_id' => $template_id,
            'llm_config_id' => $llm_config_id,
            'generated_prompt' => $prompt,
            'created_by' => active_user_id(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->project_db->insert('generated_prompts', $data);
        return $this->project_db->insert_id();
    }

    /**
     * Update existing prompt in database
     */
    private function update_prompt_in_db($prompt_id, $prompt, $template_id, $llm_config_id)
    {
        $data = [
            'template_id' => $template_id,
            'llm_config_id' => $llm_config_id,
            'generated_prompt' => $prompt,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->project_db->where('prompt_id', (int)$prompt_id)
                          ->update('generated_prompts', $data);
    }

    /**
     * Get saved prompts from database
     */
    private function get_saved_prompts_from_db()
    {
        $project_id = active_project_id();
        
        // Get prompts from project database
        $result = $this->project_db->where('project_id', $project_id)
                          ->order_by('created_at', 'DESC')
                          ->get('generated_prompts');
        $prompts = $result->result_array();
        
        // Get user names from main database
        $user_ids = array_column($prompts, 'created_by');
        $users = [];
        if (!empty($user_ids)) {
            $user_result = $this->db->select('user_id, user_name')
                                  ->where_in('user_id', $user_ids)
                                  ->get('users');
            $users = array_column($user_result->result_array(), 'user_name', 'user_id');
        }
        
        // Get LLM config names from project database
        $llm_config_ids = array_column($prompts, 'llm_config_id');
        $llm_configs = [];
        if (!empty($llm_config_ids)) {
            $llm_result = $this->project_db->select('llm_config_id, provider_name, model_name')
                                         ->where_in('llm_config_id', $llm_config_ids)
                                         ->get('llm_config');
            $llm_configs = $llm_result->result_array();
            $llm_configs = array_column($llm_configs, null, 'llm_config_id');
        }
        
        // Combine the data
        foreach ($prompts as &$prompt) {
            $prompt['user_name'] = isset($users[$prompt['created_by']]) ? $users[$prompt['created_by']] : 'Unknown User';
            if (isset($llm_configs[$prompt['llm_config_id']])) {
                $prompt['provider_name'] = $llm_configs[$prompt['llm_config_id']]['provider_name'];
                $prompt['model_name'] = $llm_configs[$prompt['llm_config_id']]['model_name'];
            } else {
                $prompt['provider_name'] = '';
                $prompt['model_name'] = '';
            }
        }
        
        return $prompts;
    }

    /**
     * Delete prompt from database
     */
    private function delete_prompt_from_db($prompt_id)
    {
        $this->project_db->where('prompt_id', $prompt_id)
                 ->where('project_id', active_project_id())
                 ->delete('generated_prompts');
    }
}
