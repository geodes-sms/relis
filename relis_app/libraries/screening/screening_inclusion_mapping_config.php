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
 *  :Author: Abdelhamid Rouatbi
 */

/*
	The function creates a configuration array with various settings for managing screening_decisions in a system. 
	Here are the key components of the configuration:
		- table_name: The name of the table associated with screening_decision.
		- table_id: The primary key field for the screening_decision table.
		- table_active_field: The field used to determine whether a record is active or deleted.
		- The configuration includes a fields array, which defines the fields of the screen_decision table.
		- etc.
*/
function get_screening_inclusion_mapping_config()
{
	$config['config_id'] = 'screening_inclusion_mapping';
	$config['table_name'] = 'screen_inclusion_mapping';
	$config['table_id'] = 'inclusion_mapping_id';
	$config['table_active_field'] = 'mapping_active'; //to detect deleted records

	$config['entity_label'] = 'Screening';
	$config['entity_label_plural'] = 'Screening';



	$fields['inclusion_mapping_id'] = array(
		'field_title' => '#',
		'field_type' => 'int',
		'field_size' => 11,
		'field_value' => 'auto_increment',
		'default_value' => 'auto_increment'
	);

	$fields['screening_id'] = array(
		'field_title' => 'screening id',
		'field_type' => 'int',
		'field_size' => 11,
		'mandatory' => ' mandatory ',


	);

	$fields['criteria_id'] = array( // assigned to
		'category_type' => 'IndependantDynamicCategory',
		'field_title' => 'Criteria',
		'field_type' => 'int',
		'field_size' => 11,
		'mandatory' => ' mandatory ',
		'number_of_values' => 1,
		'input_type' => 'select',
		'input_select_source' => 'table',
		'input_select_values' => 'inclusioncriteria;ref_value',
	);

	$fields['mapping_active'] = array(
		'field_title' => 'Active',
		'field_type' => 'int',
		'field_size' => '1',
		'field_value' => '1',
		'default_value' => '1'
	);

	$config['fields'] = $fields;

	/*
		The $operations array defines different operations or actions that can be performed on screening_decision. 
		Each operation has a type, title, description, page title, save function, page template, and other settings.
	*/
	

	return $config;

}