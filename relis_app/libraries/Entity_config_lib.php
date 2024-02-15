<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/*
	This class provides methods to retrieve table configurations and installation configurations. 
	It handles predefined table configurations through a switch statement and includes corresponding configuration files for each table.
*/
class Entity_config_lib
{
	public function __construct()
	{
		$this->CI =& get_instance();

	}


	/*
		retrieve the configuration for a specific table. 
		It takes two parameters: $_table (the table name) and $target_db (the target database, defaulting to 'current').
	*/
	public function get_table_config($_table, $target_db = 'current')
	{
		return $this->CI->entity_configuration_lib->get_table_configuration($_table, $target_db);
		exit;
		$table_configurations = array();
		switch ($_table) {
			/*	case 'users':
						  require_once("entity_config/user_config.php");
						  $table_configurations['users']=get_user();
						  break;
					  
					  case 'usergroup':
						  require_once("entity_config/usergroup_config.php");
						  $table_configurations['usergroup']=get_usergroup();
						  break;
						  
					  case 'user_project':
						  require_once("entity_config/user_project_config.php");
						  $table_configurations['user_project']=get_user_project();
						  break;		
						  
					  case 'project':
						  require_once("entity_config/project_config.php");
						  $table_configurations['project']=get_project();
						  break;
					  */

			case 'logs':
				require_once("entity_config/logs_config.php");
				$table_configurations['logs'] = get_logs();
				break;
			case 'str_mng':
				require_once("entity_config/str_mng_config.php");
				$table_configurations['str_mng'] = get_str_mng();
				break;


			case 'config':
				require_once("entity_config/config_config.php");
				$table_configurations['config'] = get_configuration();
				break;


			// relis project
			case 'papers':
				require_once("entity_config/relis/paper_config.php");
				$table_configurations['papers'] = get_papers();
				break;

			case 'author':
				require_once("entity_config/relis/author_config.php");
				$table_configurations['author'] = get_author();
				break;

			case 'venue':
				require_once("entity_config/relis/venue_config.php");
				$table_configurations['venue'] = get_venue();
				break;

			case 'paper_author':
				require_once("entity_config/relis/paper_author_config.php");
				$table_configurations['paper_author'] = get_paper_author();
				break;


			case 'exclusion':
				require_once("entity_config/relis/exclusion_config.php");
				$table_configurations['exclusion'] = get_exclusion();
				break;

			case 'inclusion':
				require_once("entity_config/relis/inclusion_config.php");
				$table_configurations['inclusion'] = get_inclusion();
				break;

			case 'assignation':
				require_once("entity_config/relis/assignation_config.php");
				$table_configurations['assignation'] = get_assignation();
				break;

			case 'assignment_screen':
				require_once("entity_config/relis/assignment_screen_config.php");
				$table_configurations['assignment_screen'] = get_assignment_screening();
				break;

			case 'assignment_screen_validate':
				require_once("entity_config/relis/assignment_screen_config.php");
				$table_configurations['assignment_screen_validate'] = get_assignment_screening('assignment_screen_validate', 'Paper assignment for screening validation');
				break;

			case 'screening':
				require_once("entity_config/relis/screening_config.php");
				$table_configurations['screening'] = get_screening();
				break;

			case 'screening_validate':
				require_once("entity_config/relis/screening_config.php");
				$table_configurations['screening_validate'] = get_screening('screening_validate', 'Screening validation');
				break;
			case 'operations':
				require_once("entity_config/relis/operations_config.php");
				$table_configurations['operations'] = get_operation();
				break;
			//--------------------------------	

			default:

				$continue = TRUE;
				$target_db = ($target_db == 'current') ? project_db() : $target_db;



				if ($target_db != 'default') {
					//reference tables
					$reftables = $this->CI->DBConnection_mdl->get_reference_tables_list($target_db);


					foreach ($reftables as $key => $value) {
						if ($_table == $value['reftab_label']) {
							require_once("entity_config/refferences_config.php");
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
						if ($value['on_list'] == 'show' and !(isset($value['number_of_values']) and $value['number_of_values'] != 1)) {
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
			set_top_msg('Error : Page "' . $_table . '" not found!', 'error');
			redirect('home');

		} else {
			return $config;
		}
	}

	//Method is responsible for retrieving the installation configuration for a specified target database
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
			$project_specific_config_folder = get_ci_config('project_specific_config_folder');
			require_once($project_specific_config_folder . "install_config_" . $target_db . ".php");
			require_once("table_config/project/install_config_" . $target_db . ".php");
			$res = call_user_func('get_classification_' . $target_db);

			$result = $this->clean_install_config($res);
		}

		return $result;

	}

	/*
		method is similar to get_install_config(), but it includes a different installation configuration file specific to the target database.
	*/
	public function get_new_install_config($target_db = 'current')
	{
		$target_db = ($target_db == 'current') ? project_db() : $target_db;
		if ($target_db == 'default') {


			$result = array();

		} else {
			$project_specific_config_folder = get_ci_config('project_specific_config_folder');

			require_once($project_specific_config_folder . "temp/install_config_" . $target_db . ".php");
			$res = call_user_func('get_classification_' . $target_db);

			$result = $this->clean_install_config($res);
		}

		return $result;

	}

	/*
		accepts a file name as a parameter and includes the corresponding installation configuration file. 
		It then calls the get_classification() function to retrieve the classification data and passes it through the clean_install_config() method to obtain the cleaned result.
	*/
	public function get_new_install_config_old($file_name)
	{
		require_once("table_config/project/temp/" . $file_name . ".php");
		$res = get_classification();

		//print_test($result);
		$result = $this->clean_install_config($res);
		return $result;

	}

	/*
		used to clean up the installation configuration. 
		It performs various operations on the installation configuration data, 
		such as reorganizing reference tables, etc.
	*/
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