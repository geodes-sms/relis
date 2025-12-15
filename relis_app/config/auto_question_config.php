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
|--------------------------------------------------------------------------
| Auto Question Generation Configuration
|--------------------------------------------------------------------------
|
| Configuration for automatic question generation when criteria are added
|
*/

$config['auto_question_generation'] = array(
    'enabled' => true,
    'default_llm_config_id' => 1, // Set to your default LLM configuration ID
    'default_api_key' => '', // Set your default API key here (optional)
    'fallback_to_parser_only' => false, // If true, only use parser, don't try LLM
    'log_generation' => true // Log all generation attempts
);

