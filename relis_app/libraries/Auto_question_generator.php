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

class Auto_question_generator {
    
    private $CI;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('Llm_lib');
        $this->CI->load->config('auto_question_config');
    }
    
    /**
     * Generate question for criteria using hybrid approach (parser + LLM)
     */
    public function generate_question_for_criteria($criteria_id, $criteria_type, $criteria_data) {
        // First try simple parser
        $parsed_question = $this->parse_simple_criteria($criteria_data);
        
        if ($parsed_question) {
            // Update database with parsed question
            $this->update_criteria_question($criteria_id, $criteria_type, $parsed_question, 'parser');
            return array('success' => true, 'question' => $parsed_question, 'method' => 'parser');
        }
        
        // Fall back to LLM
        return $this->generate_question_with_llm($criteria_id, $criteria_type);
    }
    
    /**
     * Simple parser for common criteria patterns
     */
    private function parse_simple_criteria($criteria_data) {
        $title = $criteria_data['ref_value'];
        $description = $criteria_data['ref_desc'];
        $category = $criteria_data['criteria_category'];
        
        // Convert to lowercase for pattern matching
        $title_lower = strtolower($title);
        $desc_lower = strtolower($description);
        
        // Pattern 1: "Studies about X" -> "Does the study focus on X?"
        if (preg_match('/studies?\s+(?:about|on|regarding|focusing\s+on)\s+(.+)/i', $title, $matches)) {
            return "Does the study focus on " . trim($matches[1]) . "?";
        }
        
        // Pattern 2: "Published in X" -> "Was the study published in X?"
        if (preg_match('/published\s+in\s+(.+)/i', $title, $matches)) {
            return "Was the study published in " . trim($matches[1]) . "?";
        }
        
        // Pattern 3: "Written in X language" -> "Is the study written in X language?"
        if (preg_match('/written\s+in\s+(.+?)\s+language/i', $title, $matches)) {
            return "Is the study written in " . trim($matches[1]) . " language?";
        }
        
        // Pattern 4: "Year X or later" -> "Was the study published in year X or later?"
        if (preg_match('/(\d{4})\s+or\s+later/i', $title, $matches)) {
            return "Was the study published in year " . $matches[1] . " or later?";
        }
        
        // Pattern 5: "Peer-reviewed" -> "Is the study peer-reviewed?"
        if (preg_match('/peer.?reviewed/i', $title)) {
            return "Is the study peer-reviewed?";
        }
        
        // Pattern 6: "Too short" -> "Is the study too short?"
        if (preg_match('/too\s+short/i', $title)) {
            return "Is the study too short?";
        }
        
        // Pattern 7: "Not about X" -> "Is the study about X?"
        if (preg_match('/not\s+(?:about|on)\s+(.+)/i', $title, $matches)) {
            return "Is the study about " . trim($matches[1]) . "?";
        }
        
        // Pattern 8: "Randomized controlled trial" -> "Is this a randomized controlled trial?"
        if (preg_match('/randomized\s+controlled\s+trial/i', $title)) {
            return "Is this a randomized controlled trial?";
        }
        
        // Pattern 9: "Case study" -> "Is this a case study?"
        if (preg_match('/case\s+study/i', $title)) {
            return "Is this a case study?";
        }
        
        // Pattern 10: "Systematic review" -> "Is this a systematic review?"
        if (preg_match('/systematic\s+review/i', $title)) {
            return "Is this a systematic review?";
        }
        
        // Pattern 11: "Meta-analysis" -> "Is this a meta-analysis?"
        if (preg_match('/meta.?analysis/i', $title)) {
            return "Is this a meta-analysis?";
        }
        
        // Pattern 12: "Full text available" -> "Is the full text available?"
        if (preg_match('/full\s+text\s+available/i', $title)) {
            return "Is the full text available?";
        }
        
        // Pattern 13: "Abstract available" -> "Is the abstract available?"
        if (preg_match('/abstract\s+available/i', $title)) {
            return "Is the abstract available?";
        }
        
        // Pattern 14: "Human subjects" -> "Does the study involve human subjects?"
        if (preg_match('/human\s+subjects/i', $title)) {
            return "Does the study involve human subjects?";
        }
        
        // Pattern 15: "Animal studies" -> "Does the study involve animals?"
        if (preg_match('/animal\s+studies/i', $title)) {
            return "Does the study involve animals?";
        }
        
        return null; // No pattern matched
    }
    
    /**
     * Generate question using LLM
     */
    private function generate_question_with_llm($criteria_id, $criteria_type) {
        // Check if auto generation is enabled
        $config = $this->CI->config->item('auto_question_generation');
        if (!$config['enabled']) {
            return array('success' => false, 'message' => 'Auto question generation is disabled');
        }
        
        // Check if we should only use parser
        if ($config['fallback_to_parser_only']) {
            return array('success' => false, 'message' => 'LLM generation disabled, parser only mode');
        }
        
        // Get LLM configuration
        $llm_configs = $this->CI->llm_lib->get_active_configs();
        
        if (empty($llm_configs)) {
            return array('success' => false, 'message' => 'No active LLM configurations found');
        }
        
        // Use configured default or first available
        $llm_config_id = $config['default_llm_config_id'];
        $default_llm_config = null;
        
        foreach ($llm_configs as $config_item) {
            if ($config_item['llm_config_id'] == $llm_config_id) {
                $default_llm_config = $config_item;
                break;
            }
        }
        
        if (!$default_llm_config) {
            $default_llm_config = $llm_configs[0];
            $llm_config_id = $default_llm_config['llm_config_id'];
        }
        
        // Try to get API key from session, config, or use default
        $api_key = $this->CI->session->userdata('llm_api_key_' . $llm_config_id);
        
        if (!$api_key && !empty($config['default_api_key'])) {
            $api_key = $config['default_api_key'];
        }
        
        if (!$api_key) {
            return array('success' => false, 'message' => 'No API key found for LLM generation');
        }
        
        // Generate question based on criteria type
        if ($criteria_type == 'inclusioncriteria') {
            $result = $this->CI->llm_lib->convert_inclusion_criteria_to_question($criteria_id, $llm_config_id, $api_key);
        } else {
            $result = $this->CI->llm_lib->convert_exclusion_criteria_to_question($criteria_id, $llm_config_id, $api_key);
        }
        
        if ($result['success']) {
            $this->update_criteria_question($criteria_id, $criteria_type, $result['question'], 'llm');
        }
        
        return $result;
    }
    
    /**
     * Update criteria with generated question
     */
    private function update_criteria_question($criteria_id, $criteria_type, $question, $method) {
        $this->CI->db = $this->CI->load->database(project_db(), TRUE);
        
        $table = ($criteria_type == 'inclusioncriteria') ? 'ref_inclusioncriteria' : 'ref_exclusioncrieria';
        $question_field = ($criteria_type == 'inclusioncriteria') ? 'inclusion_question' : 'exclusion_question';
        
        $update_data = array(
            $question_field => $question,
            'question_generated_time' => date('Y-m-d H:i:s'),
            'question_generated_by_llm' => ($method == 'llm') ? 1 : null
        );
        
        $this->CI->db->where('ref_id', $criteria_id)
                     ->update($table, $update_data);
    }
}
