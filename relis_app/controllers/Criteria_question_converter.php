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

class Criteria_question_converter extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('Llm_lib');
        $this->load->library('session');
        $this->load->helper('url');
    }
    
    /**
     * Display the criteria to question conversion interface
     */
    public function index() {
        $data = array();
        
        // Get available LLM configurations
        $data['llm_configs'] = $this->llm_lib->get_active_configs();
        
        // Get inclusion criteria without questions
        $this->db = $this->load->database(project_db(), TRUE);
        $data['inclusion_criteria'] = $this->db->where('ref_active', 1)
                                              ->where('(inclusion_question IS NULL OR inclusion_question = "")')
                                              ->get('ref_inclusioncriteria')
                                              ->result_array();
        
        // Get exclusion criteria without questions
        $data['exclusion_criteria'] = $this->db->where('ref_active', 1)
                                              ->where('(exclusion_question IS NULL OR exclusion_question = "")')
                                              ->get('ref_exclusioncrieria')
                                              ->result_array();
        
        // Get criteria with questions for display
        $data['inclusion_with_questions'] = $this->db->where('ref_active', 1)
                                                    ->where('inclusion_question IS NOT NULL')
                                                    ->where('inclusion_question != ""')
                                                    ->get('ref_inclusioncriteria')
                                                    ->result_array();
        
        $data['exclusion_with_questions'] = $this->db->where('ref_active', 1)
                                                    ->where('exclusion_question IS NOT NULL')
                                                    ->where('exclusion_question != ""')
                                                    ->get('ref_exclusioncrieria')
                                                    ->result_array();
        
        $this->load->view('admin/criteria_question_converter', $data);
    }
    
    /**
     * Convert single inclusion criteria to question
     */
    public function convert_inclusion_criteria() {
        $criteria_id = $this->input->post('criteria_id');
        $llm_config_id = $this->input->post('llm_config_id');
        $api_key = $this->input->post('api_key');
        
        if (!$criteria_id || !$llm_config_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Missing required parameters'
                )));
            return;
        }
        
        $result = $this->llm_lib->convert_inclusion_criteria_to_question($criteria_id, $llm_config_id, $api_key);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
    
    /**
     * Convert single exclusion criteria to question
     */
    public function convert_exclusion_criteria() {
        $criteria_id = $this->input->post('criteria_id');
        $llm_config_id = $this->input->post('llm_config_id');
        $api_key = $this->input->post('api_key');
        
        if (!$criteria_id || !$llm_config_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Missing required parameters'
                )));
            return;
        }
        
        $result = $this->llm_lib->convert_exclusion_criteria_to_question($criteria_id, $llm_config_id, $api_key);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
    
    /**
     * Convert multiple criteria to questions in batch
     */
    public function convert_criteria_batch() {
        $criteria_ids = $this->input->post('criteria_ids');
        $criteria_type = $this->input->post('criteria_type');
        $llm_config_id = $this->input->post('llm_config_id');
        $api_key = $this->input->post('api_key');
        
        if (!$criteria_ids || !$criteria_type || !$llm_config_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Missing required parameters'
                )));
            return;
        }
        
        // Convert string to array if needed
        if (is_string($criteria_ids)) {
            $criteria_ids = json_decode($criteria_ids, true);
        }
        
        $result = $this->llm_lib->convert_criteria_batch_to_questions($criteria_ids, $criteria_type, $llm_config_id, $api_key);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
    
    /**
     * Test LLM API key
     */
    public function test_api_key() {
        $llm_config_id = $this->input->post('llm_config_id');
        $api_key = $this->input->post('api_key');
        
        if (!$llm_config_id || !$api_key) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Missing required parameters'
                )));
            return;
        }
        
        // Store API key temporarily
        $this->llm_lib->store_api_key_temp($llm_config_id, $api_key);
        
        $result = $this->llm_lib->test_api_key($llm_config_id, $api_key);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
    
    /**
     * Estimate cost for batch conversion
     */
    public function estimate_cost() {
        $criteria_count = $this->input->post('criteria_count');
        $llm_config_id = $this->input->post('llm_config_id');
        
        if (!$criteria_count || !$llm_config_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Missing required parameters'
                )));
            return;
        }
        
        // Estimate tokens per criteria (rough estimate: 100 tokens for prompt + 50 for response)
        $estimated_tokens_per_criteria = 150;
        $total_estimated_tokens = $criteria_count * $estimated_tokens_per_criteria;
        
        $result = $this->llm_lib->estimate_cost($llm_config_id, $total_estimated_tokens);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
    
    /**
     * Get modal content for criteria question generation
     */
    public function get_modal_content() {
        $criteria_type = $this->input->get('type'); // 'inclusion' or 'exclusion'
        
        if (!$criteria_type || !in_array($criteria_type, ['inclusion', 'exclusion'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Invalid criteria type'
                )));
            return;
        }
        
        $data = array();
        
        // Get available LLM configurations
        $data['llm_configs'] = $this->llm_lib->get_active_configs();
        $data['criteria_type'] = $criteria_type;
        
        // Get criteria without questions
        $this->db = $this->load->database(project_db(), TRUE);
        $table = ($criteria_type === 'inclusion') ? 'ref_inclusioncriteria' : 'ref_exclusioncrieria';
        $question_field = ($criteria_type === 'inclusion') ? 'inclusion_question' : 'exclusion_question';
        
        $data['criteria'] = $this->db->where('ref_active', 1)
                                   ->where("($question_field IS NULL OR $question_field = '')")
                                   ->get($table)
                                   ->result_array();
        
        // Get criteria with questions for display
        $data['criteria_with_questions'] = $this->db->where('ref_active', 1)
                                                   ->where("$question_field IS NOT NULL")
                                                   ->where("$question_field != ''")
                                                   ->get($table)
                                                   ->result_array();
        
        $modal_content = $this->load->view('admin/criteria_question_modal', $data, TRUE);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success' => true,
                'content' => $modal_content
            )));
    }

    /**
     * Clear generated question for a criteria
     */
    public function clear_question() {
        $criteria_id = $this->input->post('criteria_id');
        $criteria_type = $this->input->post('criteria_type');
        
        if (!$criteria_id || !$criteria_type) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Missing required parameters'
                )));
            return;
        }
        
        $this->db = $this->load->database(project_db(), TRUE);
        
        $table = ($criteria_type === 'inclusion') ? 'ref_inclusioncriteria' : 'ref_exclusioncrieria';
        $question_field = ($criteria_type === 'inclusion') ? 'inclusion_question' : 'exclusion_question';
        
        $update_data = array(
            $question_field => null,
            'question_generated_time' => null,
            'question_generated_by_llm' => null
        );
        
        $this->db->where('ref_id', $criteria_id)
                 ->update($table, $update_data);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success' => true,
                'message' => 'Question cleared successfully'
            )));
    }
}
