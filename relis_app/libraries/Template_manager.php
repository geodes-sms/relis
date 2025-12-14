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
 * Template manager for prompt generation system.
 * Handles template loading, variable replacement, and prompt building.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Template_manager
{
    private $templates_path;
    private $available_templates;

    public function __construct()
    {
        $this->templates_path = FCPATH;
        $this->load_available_templates();
    }

    /**
     * Load available templates
     */
    private function load_available_templates()
    {
        $this->available_templates = [
            'trustse' => [
                'id' => 'trustse',
                'name' => 'TrustSE Template',
                'description' => 'TrustSE systematic literature review template with specific format',
                'file' => 'trustse_prompt_template.txt',
                'variables' => [
                    'SLR_TOPIC',
                    'KEY_CONCEPTS_AND_DEFINITIONS',
                    'SELECTION_CRITERIA_TOPIC_RELEVANCE',
                    'SELECTION_CRITERIA_TYPE_OF_STUDY',
                    'SELECTION_CRITERIA_QUALITY_OF_STUDY',
                    'RESEARCH_QUESTIONS'
                ]
            ],
            'structured_slr' => [
                'id' => 'structured_slr',
                'name' => 'Structured SLR Template',
                'description' => 'Structured prompt with key concepts, criteria categories, and research questions',
                'file' => 'structured_slr_prompt_template.txt',
                'variables' => [
                    'SLR_TOPIC',
                    'KEY_CONCEPTS_SECTION',
                    'INCLUSION_CRITERIA_TYPES_OF_STUDY',
                    'INCLUSION_CRITERIA_QUALITY_OF_STUDY',
                    'INCLUSION_CRITERIA_TOPIC_RELEVANCE',
                    'EXCLUSION_CRITERIA_SECTION',
                    'RESEARCH_QUESTIONS_SECTION',
                    'PAPER_TITLE',
                    'PAPER_ABSTRACT'
                ]
            ],
            'comprehensive_slr' => [
                'id' => 'comprehensive_slr',
                'name' => 'Comprehensive SLR Template',
                'description' => 'Full systematic literature review prompt with all criteria and context',
                'file' => 'comprehensive_llm_prompt_template.txt',
                'variables' => [
                    'PROJECT_TITLE',
                    'PROJECT_DESCRIPTION', 
                    'RESEARCH_QUESTIONS',
                    'KEY_CONCEPTS',
                    'INCLUSION_CRITERIA',
                    'EXCLUSION_CRITERIA',
                    'PAPER_TITLE',
                    'PAPER_ABSTRACT'
                ]
            ],
            'simple_screening' => [
                'id' => 'simple_screening',
                'name' => 'Simple Screening Template',
                'description' => 'Basic screening prompt for quick paper evaluation',
                'file' => 'simple_screening_template.txt',
                'variables' => [
                    'PROJECT_TITLE',
                    'PROJECT_DESCRIPTION',
                    'KEY_CONCEPTS',
                    'INCLUSION_CRITERIA',
                    'EXCLUSION_CRITERIA',
                    'PAPER_TITLE',
                    'PAPER_ABSTRACT'
                ]
            ],
            'custom' => [
                'id' => 'custom',
                'name' => 'Custom Template',
                'description' => 'Create your own template',
                'file' => null,
                'variables' => []
            ]
        ];
    }

    /**
     * Get all available templates
     */
    public function get_available_templates()
    {
        return $this->available_templates;
    }

    /**
     * Get template by ID
     */
    public function get_template($template_id)
    {
        if (!isset($this->available_templates[$template_id])) {
            return null;
        }

        $template = $this->available_templates[$template_id];
        
        // Load template content if file exists
        if ($template['file'] && file_exists($this->templates_path . $template['file'])) {
            $template['content'] = file_get_contents($this->templates_path . $template['file']);
        }

        return $template;
    }

    /**
     * Build prompt from template and data
     */
    public function build_prompt($template_id, $data, $custom_variables = [])
    {
        $template = $this->get_template($template_id);
        if (!$template) {
            throw new Exception('Template not found: ' . $template_id);
        }

        if (!isset($template['content'])) {
            throw new Exception('Template content not found');
        }

        $prompt = $template['content'];

        // Replace standard variables
        $prompt = $this->replace_standard_variables($prompt, $data);

        // Replace custom variables
        $prompt = $this->replace_custom_variables($prompt, $custom_variables);

        return $prompt;
    }

    /**
     * Replace standard variables in template
     */
    private function replace_standard_variables($prompt, $data)
    {
        // Project information
        $prompt = str_replace('{PROJECT_TITLE}', $data['project_title'] ?? 'Systematic Literature Review', $prompt);
        $prompt = str_replace('{PROJECT_DESCRIPTION}', $data['project_description'] ?? '', $prompt);
        $prompt = str_replace('{SLR_TOPIC}', $data['slr_topic'] ?? '', $prompt);
        // Do not replace {RESEARCH_QUESTIONS} or {KEY_CONCEPTS} here; TrustSE/textual handlers below will handle these

        // TrustSE template variables
        $prompt = str_replace('{KEY_CONCEPTS_AND_DEFINITIONS}', $this->build_textual_key_concepts_section($data['key_concepts'] ?? []), $prompt);
        $prompt = str_replace('{SELECTION_CRITERIA_TOPIC_RELEVANCE}', $this->build_textual_criteria_section_by_category($data['inclusion_criteria'] ?? [], 'topic_relevance'), $prompt);
        
        // Conditional sections - only include if there's data
        $prompt = str_replace('{RESEARCH_QUESTIONS_SECTION}', $this->build_conditional_research_questions_section($data['research_questions'] ?? []), $prompt);
        $prompt = str_replace('{TYPE_OF_STUDY_SECTION}', $this->build_conditional_criteria_section($data['inclusion_criteria'] ?? [], 'type_of_study', 'Check the type of study'), $prompt);
        $prompt = str_replace('{QUALITY_OF_STUDY_SECTION}', $this->build_conditional_criteria_section($data['inclusion_criteria'] ?? [], 'quality_of_study', 'Assess the quality of the study'), $prompt);
        $prompt = str_replace('{MAKE_DECISION_SECTION}', $this->build_make_decision_section($data['inclusion_criteria'] ?? []), $prompt);

        // Structured template variables
        $prompt = str_replace('{KEY_CONCEPTS_SECTION}', $this->build_key_concepts_section($data['key_concepts'] ?? []), $prompt);
        $prompt = str_replace('{RESEARCH_QUESTIONS_SECTION}', $this->build_research_questions_section($data['research_questions'] ?? []), $prompt);
        
        // Criteria sections by category
        $prompt = str_replace('{INCLUSION_CRITERIA_TYPES_OF_STUDY}', $this->build_criteria_section_by_category($data['inclusion_criteria'] ?? [], 'types_of_study'), $prompt);
        $prompt = str_replace('{INCLUSION_CRITERIA_QUALITY_OF_STUDY}', $this->build_criteria_section_by_category($data['inclusion_criteria'] ?? [], 'quality_of_study'), $prompt);
        $prompt = str_replace('{INCLUSION_CRITERIA_TOPIC_RELEVANCE}', $this->build_criteria_section_by_category($data['inclusion_criteria'] ?? [], 'topic_relevance'), $prompt);
        $prompt = str_replace('{EXCLUSION_CRITERIA_SECTION}', $this->build_criteria_section($data['exclusion_criteria'] ?? [], 'exclusion'), $prompt);

        // Legacy criteria sections
        $prompt = str_replace('{INCLUSION_CRITERIA}', $this->build_criteria_section($data['inclusion_criteria'] ?? [], 'inclusion'), $prompt);
        $prompt = str_replace('{EXCLUSION_CRITERIA}', $this->build_criteria_section($data['exclusion_criteria'] ?? [], 'exclusion'), $prompt);

        // Paper information (for screening prompts)
        $prompt = str_replace('{PAPER_TITLE}', $data['paper_title'] ?? '{PAPER_TITLE}', $prompt);
        $prompt = str_replace('{PAPER_ABSTRACT}', $data['paper_abstract'] ?? '{PAPER_ABSTRACT}', $prompt);

        return $prompt;
    }

    /**
     * Replace custom variables in template
     */
    private function replace_custom_variables($prompt, $custom_variables)
    {
        foreach ($custom_variables as $key => $value) {
            $prompt = str_replace('{' . strtoupper($key) . '}', $value, $prompt);
        }
        return $prompt;
    }

    /**
     * Build criteria section for prompt
     */
    private function build_criteria_section($criteria, $type)
    {
        if (empty($criteria)) {
            return "No {$type} criteria specified.";
        }

        $section = "";
        $current_category = '';
        
        foreach ($criteria as $criterion) {
            // Add category header if changed
            if ($criterion['criteria_category'] !== $current_category) {
                if ($current_category !== '') {
                    $section .= "\n";
                }
                $current_category = $criterion['criteria_category'];
                $section .= "**" . strtoupper(str_replace('_', ' ', $current_category)) . " CRITERIA:**\n";
            }
            
            // Add criterion
            $section .= $criterion['ref_id'] . ". **" . $criterion['ref_value'] . "**\n";
            
            // Add description if available
            if (!empty($criterion['ref_desc'])) {
                $section .= "   - Description: " . $criterion['ref_desc'] . "\n";
            }
            
            // Add generated question if available
            $question_field = $type === 'inclusion' ? 'inclusion_question' : 'exclusion_question';
            if (!empty($criterion[$question_field])) {
                $section .= "   - Screening Question: '" . $criterion[$question_field] . "'?\n";
            }
            
            $section .= "\n";
        }
        
        return $section;
    }

    /**
     * Build criteria section by specific category
     */
    private function build_criteria_section_by_category($criteria, $category)
    {
        $filtered_criteria = array_filter($criteria, function($criterion) use ($category) {
            return $criterion['criteria_category'] === $category;
        });
        
        if (empty($filtered_criteria)) {
            return "No criteria specified for this category.";
        }

        $section = "";
        foreach ($filtered_criteria as $criterion) {
            $section .= "- **" . $criterion['ref_value'] . "**\n";
            
            // Add description if available
            if (!empty($criterion['ref_desc'])) {
                $section .= "  - " . $criterion['ref_desc'] . "\n";
            }
            
            // Add generated question if available
            if (!empty($criterion['inclusion_question'])) {
                $section .= "  - Question: '" . $criterion['inclusion_question'] . "'?\n";
            }
            
            $section .= "\n";
        }
        
        return $section;
    }

    /**
     * Build textual criteria section by category for TrustSE template
     */
    private function build_textual_criteria_section_by_category($criteria, $category)
    {
        $filtered_criteria = array_filter($criteria, function($criterion) use ($category) {
            return $criterion['criteria_category'] === $category;
        });
        
        if (empty($filtered_criteria)) {
            return "";
        }

        $questions = [];
        foreach ($filtered_criteria as $criterion) {
            // Only include criteria that have inclusion questions
            if (!empty($criterion['inclusion_question'])) {
                $question = rtrim($criterion['inclusion_question'], " ?.") . '?';
                $questions[] = $question;
            }
            // Skip criteria that don't have inclusion questions
        }
        
        // If no questions found, return empty string
        if (empty($questions)) {
            return "";
        }
        
        // Join questions with natural language
        if (count($questions) == 1) {
            return $questions[0];
        } elseif (count($questions) == 2) {
            return $questions[0] . " " . $questions[1];
        } else {
            $last_question = array_pop($questions);
            return implode(" ", $questions) . " " . $last_question;
        }
    }

    /**
     * Build key concepts section
     */
    private function build_key_concepts_section($key_concepts)
    {
        if (empty($key_concepts)) {
            return "No key concepts defined.";
        }

        $section = "";
        $current_category = '';
        
        foreach ($key_concepts as $concept) {
            // Add category header if changed
            if ($concept['concept_category'] !== $current_category) {
                if ($current_category !== '') {
                    $section .= "\n";
                }
                $current_category = $concept['concept_category'];
                if (!empty($current_category)) {
                    $section .= "**" . strtoupper(str_replace('_', ' ', $current_category)) . ":**\n";
                }
            }
            
            // Add concept
            $section .= "- **" . $concept['concept_name'] . "**";
            
            // Add definition if available
            if (!empty($concept['concept_definition'])) {
                $section .= ": " . $concept['concept_definition'];
            }
            
            $section .= "\n";
        }
        
        return $section;
    }

    /**
     * Build textual key concepts section for TrustSE template
     */
    private function build_textual_key_concepts_section($key_concepts)
    {
        if (empty($key_concepts)) {
            return "";
        }

        $concepts = [];
        foreach ($key_concepts as $concept) {
            if (!empty($concept['concept_definition'])) {
                $concepts[] = $concept['concept_definition'];
            }
        }
        
        return implode(" ", $concepts);
    }

    /**
     * Build research questions section
     */
    private function build_research_questions_section($research_questions)
    {
        if (empty($research_questions)) {
            return "No research questions defined.";
        }

        $section = "";
        $current_type = '';
        
        foreach ($research_questions as $question) {
            // Add type header if changed
            if ($question['question_type'] !== $current_type) {
                if ($current_type !== '') {
                    $section .= "\n";
                }
                $current_type = $question['question_type'];
                $section .= "**" . strtoupper(str_replace('_', ' ', $current_type)) . " RESEARCH QUESTIONS:**\n";
            }
            
            // Add question
            $section .= $question['question_order'] . ". " . $question['question_text'] . "\n";
        }
        
        return $section;
    }

    /**
     * Build textual research questions section for TrustSE template
     */
    private function build_textual_research_questions_section($research_questions)
    {
        if (empty($research_questions)) {
            return "";
        }

        $questions = [];
        foreach ($research_questions as $question) {
            $text = trim($question['question_text']);
            // Ensure ending period
            if ($text !== '' && !preg_match('/[\.!?]$/', $text)) {
                $text .= '.';
            }
            $questions[] = $text;
        }
        
        return implode(" ", $questions);
    }

    /**
     * Build conditional research questions section - only include if there are questions
     */
    private function build_conditional_research_questions_section($research_questions)
    {
        if (empty($research_questions)) {
            return "";
        }

        $questions_text = $this->build_textual_research_questions_section($research_questions);
        if (empty($questions_text)) {
            return "";
        }

        return "2. Evaluate if the article helps answer the research questions: " . $questions_text;
    }

    /**
     * Build conditional criteria section - only include if there are questions
     */
    private function build_conditional_criteria_section($criteria, $category, $section_title)
    {
        $questions_text = $this->build_textual_criteria_section_by_category($criteria, $category);
        if (empty($questions_text)) {
            return "";
        }

        // For now, use fixed step numbers - this could be improved with dynamic numbering
        $step_map = [
            'type_of_study' => '3',
            'quality_of_study' => '4'
        ];
        
        $step_number = $step_map[$category] ?? '3';
        return "{$step_number}. {$section_title}: " . $questions_text;
    }

    /**
     * Build make decision section - always include as final step
     */
    private function build_make_decision_section($criteria)
    {
        return "5. Make a decision: \nBased on the above criteria, should the article be included in the SLR?";
    }

    /**
     * Validate template variables
     */
    public function validate_template_variables($template_id, $data, $custom_variables = [])
    {
        $template = $this->get_template($template_id);
        if (!$template) {
            return ['valid' => false, 'error' => 'Template not found'];
        }

        $missing_variables = [];
        $required_variables = $template['variables'] ?? [];

        foreach ($required_variables as $variable) {
            $variable_name = strtolower(str_replace('_', '_', $variable));
            
            // Check if variable is provided in data or custom variables
            $found = false;
            
            // Check standard data fields
            if (isset($data[$variable_name])) {
                $found = true;
            }
            
            // Check custom variables
            if (isset($custom_variables[$variable_name])) {
                $found = true;
            }
            
            // Check for criteria variables (special case)
            if (in_array($variable, ['INCLUSION_CRITERIA', 'EXCLUSION_CRITERIA'])) {
                $criteria_type = strtolower(str_replace('_CRITERIA', '', $variable));
                if (isset($data[$criteria_type . '_criteria']) && !empty($data[$criteria_type . '_criteria'])) {
                    $found = true;
                }
            }
            
            if (!$found) {
                $missing_variables[] = $variable;
            }
        }

        return [
            'valid' => empty($missing_variables),
            'missing_variables' => $missing_variables
        ];
    }

    /**
     * Get template preview with sample data
     */
    public function get_template_preview($template_id)
    {
        $sample_data = [
            'project_title' => 'Sample Systematic Literature Review',
            'project_description' => 'This is a sample project description for preview purposes.',
            'research_questions' => "1. What are the main findings?\n2. What methodologies are used?",
            'key_concepts' => 'Sample concepts, methodology, analysis',
            'inclusion_criteria' => [
                [
                    'ref_id' => 1,
                    'ref_value' => 'Sample Inclusion Criterion',
                    'ref_desc' => 'This is a sample inclusion criterion description',
                    'criteria_category' => 'topic_relevance',
                    'inclusion_question' => 'Does this study meet the sample inclusion criterion?'
                ]
            ],
            'exclusion_criteria' => [
                [
                    'ref_id' => 1,
                    'ref_value' => 'Sample Exclusion Criterion',
                    'ref_desc' => 'This is a sample exclusion criterion description',
                    'criteria_category' => 'quality_of_study',
                    'exclusion_question' => 'Does this study violate the sample exclusion criterion?'
                ]
            ],
            'paper_title' => 'Sample Paper Title',
            'paper_abstract' => 'This is a sample paper abstract for preview purposes.'
        ];

        try {
            return $this->build_prompt($template_id, $sample_data);
        } catch (Exception $e) {
            return "Error generating preview: " . $e->getMessage();
        }
    }

    /**
     * Create custom template
     */
    public function create_custom_template($template_content, $template_name = 'Custom Template')
    {
        // For now, we'll just return a custom template object
        // In a full implementation, this could save to database
        return [
            'id' => 'custom_' . time(),
            'name' => $template_name,
            'description' => 'Custom template created by user',
            'file' => null,
            'content' => $template_content,
            'variables' => $this->extract_variables_from_content($template_content)
        ];
    }

    /**
     * Extract variables from template content
     */
    private function extract_variables_from_content($content)
    {
        preg_match_all('/\{([A-Z_]+)\}/', $content, $matches);
        return array_unique($matches[1]);
    }
}

