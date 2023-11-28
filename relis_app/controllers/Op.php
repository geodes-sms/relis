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

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Op extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	/*
		set the working perspective in the session userdata and then redirect the user to the appropriate page
		The available perspectives are 'screen' for screening, 'class' for classification, and 'qa' for quality assessment
	*/
	public function set_perspective($perspective = 'screen')
	{
		if (!empty($perspective) and $perspective == 'class') {
			$this->session->set_userdata('working_perspective', $perspective);
		} elseif (!empty($perspective) and $perspective == 'qa') {
			$this->session->set_userdata('working_perspective', $perspective);
		} else {
			$this->session->set_userdata('working_perspective', 'screen');
		}
		if ($perspective == 'screen') {
			redirect('screening/screening');
		} else {
			redirect('home');
		}
	}

	//delete a child element and update the parent element if required
	public function delete_drilldown($operation_name, $child_id, $parent_id, $parent_field)
	{
		$this->delete_element($table_config_child, $child_id, FALSE);
		if ($update_parrent == 'yes') {
			$parent_config = get_table_config($table_config_parent);
			$array_drill = array(
				'operation_type' => 'edit',
				'table_config' => $table_config_parent,
				'table_name' => $parent_config['table_name'],
				'table_id' => $parent_config['table_id'],
				$parent_config['table_id'] => $parent_id,
				$parent_field => 0
			);
			$res_drill = $this->manage_mdl->save_reference($array_drill);
		}
		redirect('manager/display_element/' . $table_config_parent . '/' . $parent_id);
	}

	///----------------------------- to be updated
	private function zz()
	{
	}

	//fetch reference select values for various fields, considering nested select fields and different configuration scenarios
	private function zget_reference_select_values($config, $start_with_empty = True, $get_leaf = False, $multiselect = False)
	{
		$conf = explode(";", $config);
		//print_test($conf);
		$ref_table = $conf[0];
		$fields = $conf[1];
		$ref_table_config = get_table_config($ref_table);
		//for_array
		if ($get_leaf) {
			while (!empty($ref_table_config['fields'][$fields]['input_type']) and $ref_table_config['fields'][$fields]['input_type'] == 'select' and $ref_table_config['fields'][$fields]['input_select_source'] == 'table') {
				$config = $ref_table_config['fields'][$fields]['input_select_values'];
				$conf = explode(";", $config);
				//print_test($conf);
				$ref_table = $conf[0];
				$fields = $conf[1];
				$ref_table_config = get_table_config($ref_table);
				//echo "<h1>$fields</h1>";
			}
		}
		if ($multiselect and isset($ref_table_config['fields'][$fields]['input_select_source']) and $ref_table_config['fields'][$fields]['input_select_source'] == 'array') {
			$result = array();
			$result = $ref_table_config['fields'][$fields]['input_select_values'];
			//print_test($result);
			//exit;
		} else {
			if ($multiselect and isset($ref_table_config['fields'][$fields]['input_select_source']) and $ref_table_config['fields'][$fields]['input_select_source'] == 'table') {
				$config = $ref_table_config['fields'][$fields]['input_select_values'];
				$conf = explode(";", $config);
				//print_test($conf);
				$ref_table = $conf[0];
				$fields = $conf[1];
				$ref_table_config = get_table_config($ref_table);
			}
			//	$extra_condition="";
			$res = $this->DBConnection_mdl->get_reference_select_values($ref_table_config, $fields);
			$result = array();
			if ($res and $start_with_empty)
				$result[''] = "Select...";
			$_stable_config = $ref_table_config;
			$_fields = $fields;
			foreach ($res as $key => $value) {
				$ref_table_config = $_stable_config;
				$fields = $_fields;
				//print_test($ref_table_config);
				$result[$value['refId']] = $value['refDesc'];
				while (!empty($ref_table_config['fields'][$fields]['input_type']) and $ref_table_config['fields'][$fields]['input_type'] == 'select' and $ref_table_config['fields'][$fields]['input_select_source'] == 'table') {
					//	echo "<h1>bbbb</h1>";
					//print_test($result);
					$config = $ref_table_config['fields'][$fields]['input_select_values'];
					$conf = explode(";", $config);
					$ref_table = $conf[0];
					$fields = $conf[1];
					$ref_table_config = get_table_config($ref_table);
					$res2 = $this->manage_mdl->get_reference_value($ref_table_config['table_name'], $value['refDesc'], $fields, $ref_table_config['table_id']);
					$result[$value['refId']] = $res2;
				}
				if (isset($ref_table_config['fields'][$fields]['input_select_source']) and $ref_table_config['fields'][$fields]['input_select_source'] == 'array') {
					$select_values = $ref_table_config['fields'][$fields]['input_select_values'];
					$result[$value['refId']] = $select_values[$result[$value['refId']]];
				}
			}
		}
		//print_test($result);
		return $result;
	}

	//delete the specified child element from the child table ($table_config_child) and update the parent element in the parent table ($table_config_parent).
	public function remove_drilldown($child_id, $table_config_child, $table_config_parent, $parent_id, $parent_field, $update_parrent = 'yes', $modal = 'no')
	{
		$this->delete_element($table_config_child, $child_id, FALSE);
		if ($update_parrent == 'yes') {
			$parent_config = get_table_config($table_config_parent);
			$array_drill = array(
				'operation_type' => 'edit',
				'table_config' => $table_config_parent,
				'table_name' => $parent_config['table_name'],
				'table_id' => $parent_config['table_id'],
				$parent_config['table_id'] => $parent_id,
				$parent_field => 0
			);
			$res_drill = $this->manage_mdl->save_reference($array_drill);
		}
		redirect('manager/display_element/' . $table_config_parent . '/' . $parent_id);
	}

	//remove a picture associated with a specific element in a table
	public function remove_picture($ref_table, $table_name, $table_id, $field, $element_id)
	{
		$table_name = mysql_real_escape_string($table_name);
		$table_id = mysql_real_escape_string($table_id);
		$field = mysql_real_escape_string($field);
		$element_id = mysql_real_escape_string($element_id);
		$sql = "UPDATE $table_name SET $field = NULL WHERE $table_id ='" . $element_id . "'";
		$res = $this->manage_mdl->run_query($sql, False, 'default');
		if ($res) {
			set_top_msg(lng_min("Success - picture removed"));
		} else {
			set_top_msg(lng_min(" Operation failed "), 'error');
		}
		//redirect ( 'element/display_element/' .$ref_table.'/'.$element_id  );
		redirect('element/display_element/' . $ref_table . '/' . $element_id);
	}
}