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

class Llm_lib {
    
    private $CI;
    private $config;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }
    
    /**
     * Test LLM API key and configuration
     */
    public function test_api_key($llm_config_id, $api_key = null) {
        $this->CI->db2 = $this->CI->load->database(project_db(), TRUE);
        
        // Get LLM configuration
        $config = $this->CI->db2->where('llm_config_id', $llm_config_id)
                                ->where('llm_config_active', 1)
                                ->get('llm_config')
                                ->row_array();
        
        if (!$config) {
            return array('success' => false, 'message' => 'LLM configuration not found');
        }
        
        // Use provided API key or get from session
        if ($api_key) {
            $config['api_key'] = $api_key;
        } else {
            $config['api_key'] = $this->CI->session->userdata('llm_api_key_' . $llm_config_id);
        }
        
        if (!$config['api_key']) {
            return array('success' => false, 'message' => 'API key not provided');
        }
        
        // Test with a simple prompt
        $test_prompt = "Hello";
        $result = $this->make_request($config, $test_prompt);
        
        return $result;
    }
    
    /**
     * Test LLM API key directly with provided configuration
     */
    public function test_api_key_direct($config) {
        if (!$config['api_key']) {
            return array('success' => false, 'message' => 'API key not provided');
        }
        
        if (!$config['provider_name'] || !$config['model_name']) {
            return array('success' => false, 'message' => 'Provider and model are required');
        }
        
        // Test with a simple prompt
        $test_prompt = "Hello";
        $result = $this->make_request($config, $test_prompt);
        
        return $result;
    }
    
    /**
     * Make request to LLM API
     */
    public function make_request($config, $prompt, $max_tokens = null) {
        $provider = $config['provider_name'];
        $model = $config['model_name'];
        $api_key = $config['api_key'];
        $endpoint = isset($config['api_endpoint']) && $config['api_endpoint'] ? $config['api_endpoint'] : $this->get_default_endpoint($provider);
        $max_tokens = $max_tokens ?: (isset($config['max_tokens']) ? $config['max_tokens'] : 100);
        $temperature = isset($config['temperature']) ? $config['temperature'] : 0.2;
        
        $headers = $this->get_headers($provider, $api_key);
        $body = $this->get_request_body($provider, $model, $prompt, $max_tokens, $temperature);
        
        // Debug information
        error_log("LLM Request - Provider: $provider, Model: $model, Endpoint: $endpoint");
        error_log("LLM Headers: " . json_encode($headers));
        error_log("LLM Body: " . json_encode($body));
        
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ));
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return array('success' => false, 'message' => 'CURL Error: ' . $error);
        }
        
        if ($http_code !== 200) {
            // Log the error for debugging
            error_log("LLM API Error - HTTP Code: " . $http_code . ", Response: " . $response);
            return array('success' => false, 'message' => 'HTTP Error: ' . $http_code . ' - ' . $response);
        }
        
        $response_data = json_decode($response, true);
        
        if (!$response_data) {
            error_log("LLM Response - Invalid JSON: " . $response);
            return array('success' => false, 'message' => 'Invalid JSON response: ' . $response);
        }
        
        error_log("LLM Response Data: " . json_encode($response_data));
        
        return $this->parse_response($provider, $response_data);
    }
    
    /**
     * Get default API endpoint for provider
     */
    private function get_default_endpoint($provider) {
        $endpoints = array(
            'openai' => 'https://api.openai.com/v1/chat/completions',
            'anthropic' => 'https://api.anthropic.com/v1/messages',
            'google' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent',
            'openrouter' => 'https://openrouter.ai/api/v1/chat/completions'
        );
        
        return isset($endpoints[$provider]) ? $endpoints[$provider] : '';
    }
    
    /**
     * Get headers for API request
     */
    private function get_headers($provider, $api_key) {
        $headers = array(
            'Content-Type: application/json'
        );
        
        switch ($provider) {
            case 'openai':
                $headers[] = 'Authorization: Bearer ' . $api_key;
                break;
            case 'openrouter':
                $headers[] = 'Authorization: Bearer ' . $api_key;
                $headers[] = 'HTTP-Referer: https://relis-app.com';
                $headers[] = 'X-Title: ReLiS LLM Integration';
                break;
            case 'anthropic':
                $headers[] = 'x-api-key: ' . $api_key;
                $headers[] = 'anthropic-version: 2023-06-01';
                break;
            case 'google':
                $headers[] = 'Authorization: Bearer ' . $api_key;
                break;
        }
        
        return $headers;
    }
    
    /**
     * Get request body for API
     */
    private function get_request_body($provider, $model, $prompt, $max_tokens, $temperature) {
        switch ($provider) {
            case 'openai':
            case 'openrouter':
                return array(
                    'model' => $model,
                    'messages' => array(
                        array('role' => 'user', 'content' => $prompt)
                    ),
                    'max_tokens' => (int)$max_tokens,
                    'temperature' => (float)$temperature
                );
                
            case 'anthropic':
                return array(
                    'model' => $model,
                    'max_tokens' => (int)$max_tokens,
                    'temperature' => (float)$temperature,
                    'messages' => array(
                        array('role' => 'user', 'content' => $prompt)
                    )
                );
                
            case 'google':
                return array(
                    'contents' => array(
                        array(
                            'parts' => array(
                                array('text' => $prompt)
                            )
                        )
                    ),
                    'generationConfig' => array(
                        'maxOutputTokens' => (int)$max_tokens,
                        'temperature' => (float)$temperature
                    )
                );
                
            default:
                return array();
        }
    }
    
    /**
     * Parse API response
     */
    private function parse_response($provider, $response_data) {
        $content = '';
        $usage = array();
        
        switch ($provider) {
            case 'openai':
            case 'openrouter':
                if (isset($response_data['choices'][0]['message']['content'])) {
                    $content = $response_data['choices'][0]['message']['content'];
                }
                if (isset($response_data['usage'])) {
                    $usage = $response_data['usage'];
                }
                break;
                
            case 'anthropic':
                if (isset($response_data['content'][0]['text'])) {
                    $content = $response_data['content'][0]['text'];
                }
                if (isset($response_data['usage'])) {
                    $usage = $response_data['usage'];
                }
                break;
                
            case 'google':
                if (isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $response_data['candidates'][0]['content']['parts'][0]['text'];
                }
                if (isset($response_data['usageMetadata'])) {
                    $usage = $response_data['usageMetadata'];
                }
                break;
        }
        
        if (empty($content)) {
            return array('success' => false, 'message' => 'No content in response');
        }
        
        return array(
            'success' => true,
            'content' => $content,
            'usage' => $usage,
            'raw_response' => $response_data
        );
    }
    
    /**
     * Estimate cost for LLM request
     */
    public function estimate_cost($llm_config_id, $estimated_tokens) {
        $this->CI->db2 = $this->CI->load->database(project_db(), TRUE);
        
        $config = $this->CI->db2->where('llm_config_id', $llm_config_id)
                                ->where('llm_config_active', 1)
                                ->get('llm_config')
                                ->row_array();
        
        if (!$config) {
            return array('success' => false, 'message' => 'LLM configuration not found');
        }
        
        $cost_per_1k = (float)$config['cost_per_1k_tokens'];
        $estimated_cost = ($estimated_tokens / 1000) * $cost_per_1k;
        
        return array(
            'success' => true,
            'estimated_tokens' => $estimated_tokens,
            'cost_per_1k_tokens' => $cost_per_1k,
            'estimated_cost' => $estimated_cost,
            'formatted_cost' => '$' . number_format($estimated_cost, 6)
        );
    }
    
    /**
     * Get available LLM configurations
     */
    public function get_active_configs() {
        $this->CI->db2 = $this->CI->load->database(project_db(), TRUE);
        
        return $this->CI->db2->where('llm_config_active', 1)
                             ->where('is_active', 1)
                             ->order_by('provider_name', 'ASC')
                             ->get('llm_config')
                             ->result_array();
    }
    
    /**
     * Get LLM configuration by ID
     */
    public function get_config($llm_config_id) {
        $this->CI->db2 = $this->CI->load->database(project_db(), TRUE);
        
        return $this->CI->db2->where('llm_config_id', $llm_config_id)
                             ->where('llm_config_active', 1)
                             ->get('llm_config')
                             ->row_array();
    }
    
    /**
     * Store API key temporarily in session
     */
    public function store_api_key_temp($llm_config_id, $api_key) {
        $this->CI->session->set_userdata('llm_api_key_' . $llm_config_id, $api_key);
    }
    
    /**
     * Clear stored API key
     */
    public function clear_api_key($llm_config_id) {
        $this->CI->session->unset_userdata('llm_api_key_' . $llm_config_id);
    }
    
    /**
     * Convert inclusion criteria to question using LLM
     */
    public function convert_inclusion_criteria_to_question($criteria_id, $llm_config_id, $api_key = null) {
        $this->CI->db2 = $this->CI->load->database(project_db(), TRUE);
        
        // Get criteria details
        $criteria = $this->CI->db2->where('ref_id', $criteria_id)
                                 ->where('ref_active', 1)
                                 ->get('ref_inclusioncriteria')
                                 ->row_array();
        
        if (!$criteria) {
            return array('success' => false, 'message' => 'Inclusion criteria not found');
        }
        
        // Get LLM configuration
        $config = $this->CI->db2->where('llm_config_id', $llm_config_id)
                                ->where('llm_config_active', 1)
                                ->get('llm_config')
                                ->row_array();
        
        if (!$config) {
            return array('success' => false, 'message' => 'LLM configuration not found');
        }
        
        // Use provided API key or get from session
        if ($api_key) {
            $config['api_key'] = $api_key;
        } else {
            $config['api_key'] = $this->CI->session->userdata('llm_api_key_' . $llm_config_id);
        }
        
        if (!$config['api_key']) {
            return array('success' => false, 'message' => 'API key not provided');
        }
        
        // Generate prompt for criteria to question conversion
        $prompt = $this->generate_criteria_to_question_prompt($criteria, 'inclusion');
        
        // Make LLM request
        $result = $this->make_request($config, $prompt, 200);
        
        if (!$result['success']) {
            return $result;
        }
        
        // Extract question from response
        $question = $this->extract_question_from_response($result['content']);
        
        if (!$question) {
            return array('success' => false, 'message' => 'Could not extract question from LLM response');
        }
        
        // Update criteria with generated question
        $update_data = array(
            'inclusion_question' => $question,
            'question_generated_time' => date('Y-m-d H:i:s'),
            'question_generated_by_llm' => $llm_config_id
        );
        
        $this->CI->db2->where('ref_id', $criteria_id)
                      ->update('ref_inclusioncriteria', $update_data);
        
        return array(
            'success' => true,
            'question' => $question,
            'criteria_id' => $criteria_id,
            'usage' => $result['usage']
        );
    }
    
    /**
     * Convert exclusion criteria to question using LLM
     */
    public function convert_exclusion_criteria_to_question($criteria_id, $llm_config_id, $api_key = null) {
        $this->CI->db2 = $this->CI->load->database(project_db(), TRUE);
        
        // Get criteria details
        $criteria = $this->CI->db2->where('ref_id', $criteria_id)
                                 ->where('ref_active', 1)
                                 ->get('ref_exclusioncrieria')
                                 ->row_array();
        
        if (!$criteria) {
            return array('success' => false, 'message' => 'Exclusion criteria not found');
        }
        
        // Get LLM configuration
        $config = $this->CI->db2->where('llm_config_id', $llm_config_id)
                                ->where('llm_config_active', 1)
                                ->get('llm_config')
                                ->row_array();
        
        if (!$config) {
            return array('success' => false, 'message' => 'LLM configuration not found');
        }
        
        // Use provided API key or get from session
        if ($api_key) {
            $config['api_key'] = $api_key;
        } else {
            $config['api_key'] = $this->CI->session->userdata('llm_api_key_' . $llm_config_id);
        }
        
        if (!$config['api_key']) {
            return array('success' => false, 'message' => 'API key not provided');
        }
        
        // Generate prompt for criteria to question conversion
        $prompt = $this->generate_criteria_to_question_prompt($criteria, 'exclusion');
        
        // Make LLM request
        $result = $this->make_request($config, $prompt, 200);
        
        if (!$result['success']) {
            return $result;
        }
        
        // Extract question from response
        $question = $this->extract_question_from_response($result['content']);
        
        if (!$question) {
            return array('success' => false, 'message' => 'Could not extract question from LLM response');
        }
        
        // Update criteria with generated question
        $update_data = array(
            'exclusion_question' => $question,
            'question_generated_time' => date('Y-m-d H:i:s'),
            'question_generated_by_llm' => $llm_config_id
        );
        
        $this->CI->db2->where('ref_id', $criteria_id)
                      ->update('ref_exclusioncrieria', $update_data);
        
        return array(
            'success' => true,
            'question' => $question,
            'criteria_id' => $criteria_id,
            'usage' => $result['usage']
        );
    }
    
    /**
     * Generate prompt for converting criteria to question
     */
    private function generate_criteria_to_question_prompt($criteria, $type) {
        $criteria_type = ucfirst($type);
        $category_labels = array(
            'topic_relevance' => 'Topic Relevance',
            'type_of_study' => 'Type of Study',
            'quality_of_study' => 'Quality of Study'
        );
        
        $category = isset($category_labels[$criteria['criteria_category']]) 
                   ? $category_labels[$criteria['criteria_category']] 
                   : 'General';
        
        $prompt = "You are an expert in systematic literature reviews. Your task is to convert a {$criteria_type} criterion into a clear, specific question that can be used to evaluate research papers.

{$criteria_type} Criterion:
- Title: {$criteria['ref_value']}
- Description: {$criteria['ref_desc']}
- Category: {$category}

Please convert this criterion into a single, clear question that a reviewer can answer with 'Yes' or 'No' when evaluating a research paper. The question should be:

1. Specific and unambiguous
2. Directly related to the criterion
3. Easy to understand and apply
4. Formatted as a complete question ending with a question mark

Output only the question, nothing else. Do not include explanations or additional text.

Example format: 'Does the study focus on [specific topic/concept]?'";

        return $prompt;
    }
    
    /**
     * Extract question from LLM response
     */
    private function extract_question_from_response($response) {
        // Clean up the response
        $response = trim($response);
        
        // Remove any markdown formatting
        $response = preg_replace('/\*\*(.*?)\*\*/', '$1', $response);
        $response = preg_replace('/\*(.*?)\*/', '$1', $response);
        
        // Remove any leading numbers or bullets
        $response = preg_replace('/^[\d\.\-\*\s]+/', '', $response);
        
        // Ensure it ends with a question mark
        if (!preg_match('/\?$/', $response)) {
            $response .= '?';
        }
        
        // Basic validation - should be a reasonable length and contain question words
        if (strlen($response) < 10 || strlen($response) > 500) {
            return false;
        }
        
        // Check if it contains question indicators
        $question_words = array('does', 'do', 'is', 'are', 'was', 'were', 'can', 'could', 'should', 'would', 'will', 'how', 'what', 'when', 'where', 'why', 'which', 'who');
        $response_lower = strtolower($response);
        
        $has_question_word = false;
        foreach ($question_words as $word) {
            if (strpos($response_lower, $word) !== false) {
                $has_question_word = true;
                break;
            }
        }
        
        if (!$has_question_word) {
            return false;
        }
        
        return $response;
    }
    
    /**
     * Convert multiple criteria to questions in batch
     */
    public function convert_criteria_batch_to_questions($criteria_ids, $criteria_type, $llm_config_id, $api_key = null) {
        $results = array();
        $success_count = 0;
        $error_count = 0;
        
        foreach ($criteria_ids as $criteria_id) {
            if ($criteria_type === 'inclusion') {
                $result = $this->convert_inclusion_criteria_to_question($criteria_id, $llm_config_id, $api_key);
            } else {
                $result = $this->convert_exclusion_criteria_to_question($criteria_id, $llm_config_id, $api_key);
            }
            
            $results[] = array(
                'criteria_id' => $criteria_id,
                'result' => $result
            );
            
            if ($result['success']) {
                $success_count++;
            } else {
                $error_count++;
            }
            
            // Add small delay to avoid rate limiting
            usleep(500000); // 0.5 seconds
        }
        
        return array(
            'success' => $error_count === 0,
            'total_processed' => count($criteria_ids),
            'success_count' => $success_count,
            'error_count' => $error_count,
            'results' => $results
        );
    }
}
