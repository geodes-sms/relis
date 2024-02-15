<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');
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
class Table_ref_lib
{
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	//The method allows for dynamic loading of different table configurations and provides field information for generating views.
	public function ref_table_config($_table, $target_db = 'current')
	{
		old_version('Old config for ' . $_table);
		$table_configurations = array();
		switch ($_table) {
			/*
						case 'users':
							require_once("table_config/table_users.php");
							$table_configurations['users']=get_users();
							break;
							
						case 'usergroup':
							require_once("table_config/table_usergroup.php");
							$table_configurations['usergroup']=get_usergroup();
							break;
							
						case 'user_project':
							require_once("table_config/table_user_project.php");
							$table_configurations['user_project']=get_user_project();
							break;
							
						case 'logs': 
						require_once("table_config/table_logs.php");
						$table_configurations['logs']=get_logs();
						break;
						
						case 'author': 
						require_once("table_config/table_author.php");
						$table_configurations['author']=get_author();
						break;
				
						case 'venue': 
						require_once("table_config/table_venue.php");
						$table_configurations['venue']=get_venue();
						break;
				
						case 'papers': 
						require_once("table_config/table_papers.php");
						$table_configurations['papers']=get_papers();
						break;
						
						
						case 'paper_author': 
						require_once("table_config/table_paper_author.php");
						$table_configurations['paper_author']=get_paper_author();
						break;
						
						
						case 'project': 
						require_once("table_config/table_project.php");
						$table_configurations['project']=get_project();
						break;
					*/

			case 'exclusion':
				require_once("table_config/table_exclusion.php");
				$table_configurations['exclusion'] = get_exclusion();
				break;

			case 'inclusion':
				require_once("table_config/table_inclusion.php");
				$table_configurations['inclusion'] = get_inclusion();
				break;

			case 'assignation':
				require_once("table_config/table_assignation.php");
				$table_configurations['assignation'] = get_assignation();
				break;

			case 'str_mng':
				require_once("table_config/table_str_mng.php");
				$table_configurations['str_mng'] = get_str_mng();
				break;


			case 'config':
				require_once("table_config/table_config.php");
				$table_configurations['config'] = get_configuration();
				break;


			default:
				$continue = TRUE;
				$target_db = ($target_db == 'current') ? project_db() : $target_db;

				//echo "<h1>$target_db - $_table</h1>";

				if ($target_db != 'default') {
					//reference tables
					$reftables = $this->CI->DBConnection_mdl->get_reference_tables_list($target_db);


					foreach ($reftables as $key => $value) {
						if ($_table == $value['reftab_label']) {
							require_once("table_config/table_refferences.php");
							$table_configurations[$value['reftab_label']] = get_refference($value['reftab_table'], $value['reftab_desc']);
							$continue = FALSE;
						}
					}
				}
				//get generated configuration
				if ($continue) {
					$generated_config = $this->get_install_config($target_db);
					if (!empty($generated_config)) {
						foreach ($generated_config['config'] as $k_conf => $v_conf) {
							$table_configurations[$k_conf] = $v_conf;
						}
					}
				}



				break;
		}


		//	print_test($table_configurations);
		//	exit;


		$table_configurations[$_table]['config_label'] = $_table;
		//get fields to be selected

		if (!empty($table_configurations[$_table])) {
			$config = $table_configurations[$_table];

			if (empty($config['view_list_fields'])) {
				$config['view_list_fields'] = "";
				$config['header_list_fields'] = array();
				$i = 0;
				if (!empty($config['fields'])) {
					foreach ($config['fields'] as $key => $value) {
						if ($value['on_list'] == 'show') {
							if ($i == 0)
								$config['view_list_fields'] .= " " . $key;
							else
								$config['view_list_fields'] .= " , " . $key;

							array_push($config['header_list_fields'], lng($value['field_title']));
						}
						$i++;
					}
				}
			}
		} else {

			$config = array();
		}

		//print_test($config);
		if (empty($config['fields'])) {
			set_top_msg('Error : Page "' . $_table . '" not found! IN OLD', 'error');
			redirect('home');

		} else {
			return $config;
		}
	}

	//retrieves the installation configuration for a specified database ($target_db) and returns the cleaned result. 
	public function get_install_config($target_db = 'current')
	{
		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		if ($target_db == 'default') {

			//require_once("table_config/project/install_config_".$target_db.".php");
			//$res=get_classification();

			//print_test($result);
			//$result=$this->clean_install_config($res);

			$result = array();

		} else {
			require_once("table_config/project/install_config_" . $target_db . ".php");
			$res = call_user_func('get_classification_' . $target_db);

			$result = $this->clean_install_config($res);
		}

		return $result;

	}

	//etrieves a new installation configuration from a specified file and returns the cleaned result
	public function get_new_install_config($file_name)
	{
		require_once("table_config/project/temp/" . $file_name . ".php");
		$res = get_classification();

		//print_test($result);
		$result = $this->clean_install_config($res);
		return $result;

	}

	//performs cleaning operations on the installation configuration data, ensuring that reference tables and input select values are correctly represented and linked within the configuration
	private function clean_install_config($install_config)
	{
		//cleaning reference tables

		$reference_tab = array();
		if (!empty($install_config['reference_tables'])) {
			foreach ($install_config['reference_tables'] as $key_ref => $ref_values) {
				$ref = 'ref_' . Slug($key_ref);
				$reference_tab[$ref] = $ref_values;
			}
		}
		$install_config['reference_tables'] = $reference_tab;

		if (!empty($install_config['reference_tables'])) {
			foreach ($install_config['config'] as $key_config => $config_values) {
				foreach ($config_values['fields'] as $key_field => $value_field) {
					if (isset($value_field['category_type']) and $value_field['category_type'] == 'IndependantDynamicCategory') {
						//Get reference table
						$ref_table = 'ref_' . Slug($value_field['input_select_values']) . ';ref_value';
						$install_config['config'][$key_config]['fields'][$key_field]['input_select_values'] = $ref_table;

					} elseif (isset($value_field['category_type']) and ($value_field['category_type'] == 'WithMultiValues' or $value_field['category_type'] == 'WithSubCategories' or $value_field['category_type'] == 'ParentExternalKey')) {

						$input_select_values = trim($value_field['input_select_values']);

						$main_field = $install_config['config'][$input_select_values]['main_field'];
						$install_config['config'][$key_config]['fields'][$key_field]['input_select_values'] = $input_select_values . ";" . $main_field;
					}
				}
			}
		}
		return $install_config;
	}

}