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
 * :Author: Brice Michel Bigendako
 * --------------------------------------------------------------------------
 * Functions used to access review data via API in JSON
 */

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Api extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		redirect('api/protocol');
	}

	/**
	 * The purpose of this function is to generate a report for a project, 
	 * providing information about the project's schema and data. 
	 * The report can be returned in either JSON format or as an array, depending on the value of the $type parameter.
	 */
	public function report($project_label = 'mt_all', $type = "json")
	{
		$project = $this->get_project($project_label);
		if (empty($project)) {
			$protocol['message'] = 'Project does not exist';
		} else {
			$this->db_project = $this->load->database($project_label, TRUE);
			$protocol = array();
			$protocol['schema'] = $this->get_scheme($project_label);
			$protocol['data'] = $this->get_data($project_label);
		}
		$json_protocol = json_encode($protocol);
		if ($type == 'array') {
			print_test($protocol);
		} else {
			echo $json_protocol;
		}
	}

	/**
	 * The purpose of this function is to generate a protocol for a project, 
	 * providing information about the project's details, participants, research questions, 
	 * papers, screening, QA, and data extraction.
	 */
	public function protocol($project_label = 'mt_all', $type = "json")
	{
		$project = $this->get_project($project_label);
		if (empty($project)) {
			$protocol['message'] = 'Project does not exist';
		} else {
			$this->db_project = $this->load->database($project_label, TRUE);
			$protocol = array();
			//	print_test($project);
			$protocol['message'] = 'OK';
			$protocol['project_id'] = $project['project_label'];
			$protocol['project_name'] = $project['project_title'];
			$protocol['project_description'] = $project['project_description'];
			$protocol['participant'] = $this->get_users($project['project_id']);
			$protocol['research_question'] = array();
			$this->db_project->select('ref_value as question,ref_desc as description');
			$protocol['research_question'] = $this->db_project
				->get_where('research_question', array('ref_active' => 1))
				->result_array();
			$protocol['papers_search'] = $this->get_papers_search();
			if ($this->get_config_element($project_label, 'screening_on')) {
				$protocol['screening'] = $this->get_screening($project_label);
			} else {
				$protocol['screening'] = array();
			}
			if ($this->get_config_element($project_label, 'qa_on')) {
				$protocol['qa'] = $this->get_qa();
			} else {
				$protocol['qa'] = array();
			}
			$protocol['data_extraction'] = $this->get_classification($project_label);
		}
		$json_protocol = json_encode($protocol);
		if ($type == 'array') {
			print_test($protocol);
		} else {
			echo $json_protocol;
		}
	}

	/**
	 * this function fetches project information from the database based on the project label and returns it as an associative array.
	 */
	private function get_project($project)
	{
		$res = $this->db->get_where('projects', array('project_active' => 1, 'project_label' => $project))
			->row_array();
		return $res;
	}

	/**
	 * retrieves user information for a given project ID from the database and returns it as an associative array.
	 */
	private function get_users($project_id)
	{
		$sql = "SELECT U.user_id,U.user_name,P.user_role 
				FROM users U,userproject P
				WHERE U.user_id=P.user_id AND  P.project_id=$project_id  
						AND   P.userproject_active=1  AND U.user_active=1";
		echo $sql;
		$users = $this->db->query($sql)->row_array();
		return $users;
	}

	//retrieves paper search information for a given project ID
	private function get_papers_search($project_id = NULL)
	{
		$paper_serch = array();
		$this->db_project->select('ref_value as value');
		$paper_serch['search_strategy'] = $this->db_project
			->get_where('ref_search_strategy', array('ref_active' => 1))
			->result_array();
		$this->db_project->select('ref_value as value ');
		$paper_serch['papers_source'] = $this->db_project
			->get_where('ref_papers_sources', array('ref_active' => 1))
			->result_array();
		return $paper_serch;
	}

	//retrieves the schema (field structure) for a given project ID
	private function get_scheme($project_id)
	{
		$scheme = array();
		$res_install_config = $this->entity_configuration_lib->get_install_config($project_id);
		$scheme = $this->get_field_schema($res_install_config, 'classification');
		return $scheme;
	}
	
	//retrieves the field schema (structure) for a given entity configuration.
	private function get_field_schema($res_install_config, $config = 'classification')
	{
		$scheme = array();
		if (!empty($res_install_config['config'][$config]['fields'])) {
			//print_test($res_install_config);
			foreach ($res_install_config['config'][$config]['fields'] as $key => $value) {
				if (
					!empty($value['on_list']) and $value['on_list'] == 'show'
					and $key != $res_install_config['config']['classification']['table_id']
					and !empty($value['category_type'])
				) {
					$scheme[$key]['title'] = $value['field_title'];
					$type = "Text";
					if (!empty($value['number_of_values']) and $value['number_of_values'] != 1) {
						$scheme[$key]['multi_value'] = 'Yes';
						if (
							!empty($value['input_select_source_type'])
							and $value['input_select_source_type'] == 'normal'
						) {
							$source_values = $value['input_select_values'];
							$conf = explode(";", $source_values);
							//print_test($conf);
							$ref_table = $conf[0];
							$fields = $conf[1];
							$ref_table_source = $res_install_config['config'][$ref_table]['fields'][$fields]['input_select_values'];
							$scheme[$key]['values'] = $this->get_reference_values($ref_table_source);
						}
					} else {
						$scheme[$key]['multi_value'] = 'No';
					}
					if (!empty($value['input_select_source'])) {
						if ($value['input_select_source'] == 'yes_no') {
							$type = "Boolean";
						} elseif ($value['input_select_source'] == 'array') {
							$type = "List";
							$scheme[$key]['values'] = $value['input_select_values'];
						} else {
							$type = "List";
							//print_test($value);
							if ($value['category_type'] == 'IndependantDynamicCategory') {
								$scheme[$key]['values'] = $this->get_reference_values
								($value['input_select_values']);
							} elseif ($value['category_type'] == 'DependentDynamicCategory') {
								$conf = explode(";", $value['input_select_values']);
								$type = 'ListDependant';
								//print_test($conf);
								$ref_table = $conf[0];
								$fields = $conf[1];
								$scheme[$key]['values'] = $ref_table;
							}
						}
					} else {
						if ($value['input_type'] == 'date') {
							$type = "Date";
						} elseif (
							$value['input_type'] == 'text'
							and ($value['field_type'] == 'int' or $value['field_type'] == 'real')
						) {
							$type = "Number";
						}
					}
					//With sub categories
					$scheme[$key]['category'] = $type;
					if (
						!empty($value['input_select_source_type'])
						and $value['input_select_source_type'] == 'drill_down'
					) {
						$source_values = $value['input_select_values'];
						$conf = explode(";", $source_values);
						//print_test($conf);
						$ref_table = $conf[0];
						$fields = $conf[1];
						$scheme[$key]['category'] = 'Subcategory';
						$scheme[$key]['sub_categories'] = $this->get_field_schema($res_install_config, $ref_table);
					}
				}
			}
		}
		return $scheme;
	}

	//retrieves QA (Quality Assurance) related information for a project.
	private function get_qa($project = NULL)
	{
		$this->db_project->select('question ');
		$qa['questions'] = $this->db_project->order_by('question_id', 'ASC')
			->get_where('qa_questions', array('question_active' => 1))
			->result_array();
		$this->db_project->select('response,score ');
		$qa['responses'] = $this->db_project->order_by('score', 'DESC')
			->get_where('qa_responses', array('response_active' => 1))
			->result_array();
		$this->db_project->select('paper_id');
		$qa['papers'] = $this->db_project
			->get_where('view_papers_in_qa', array('qa_assignment_active' => 1))
			->num_rows();
		return $qa;
	}

	//retrieves reference values from a reference table based on the provided reference configuration.
	private function get_reference_values($reference)
	{
		//print_test($reference);
		$conf = explode(";", $reference);
		//print_test($conf);
		$ref_table = $conf[0];
		$fields = $conf[1];
		$this->db_project->select($fields);
		$res_values = $this->db_project
			->get_where($ref_table, array('ref_active' => 1))
			->result_array();
		$value = array();
		foreach ($res_values as $key => $val) {
			$value[$key] = $val[$fields];
		}
		//$value=array();
		return $value;
	}

	//retrieves classification information for a specific project label.
	private function get_classification($project_label)
	{
		$classification = array();
		$stored_proc_count = " CALL count_papers_class('','0')";
		$data = $this->db_project->query($stored_proc_count);
		mysqli_next_result($this->db_project->conn_id);
		$res = $data->row_array();
		if (!empty($res['nbr'])) {
			$classification['papers'] = $res['nbr'];
		} else {
			$classification['papers'] = 0;
		}
		$classification['schema'] = $this->get_scheme($project_label);
		//print_test($scheme);
		return $classification;
	}

	//retrieves screening information for a specific project ID
	private function get_screening($project_id)
	{
		//$this->initialise_user($project_id);
		$screening = array();
		$screening_phases = $this->db_project->order_by('screen_phase_order', 'ASC')
			->get_where('screen_phase', array('screen_phase_active' => 1))
			->result_array();
		$this->db_project->select('ref_value as criteria');
		$screening['exclusion_criteria'] = $this->db_project
			->get_where('ref_exclusioncrieria', array('ref_active' => 1))
			->result_array();
		$this->db_project->select('ref_value as criteria');
		$screening['exclusion_criteria'] = $this->db_project
			->get_where('ref_inclusioncriteria', array('ref_active' => 1))
			->result_array();
		foreach ($screening_phases as $key => $phase) {
			$screen = array();
			$screen['phase_title'] = $phase['phase_title'];
			$screen['phase_description'] = $phase['description'];
			$screening_res = $this->db_project
				->get_where(
					'view_paper_decision',
					array('paper_active' => 1, 'screening_phase' => $phase['screen_phase_id'])
				)
				->result_array();
			$result = array();
			$result['Total'] = 0;
			$result['Included'] = 0;
			$result['Excluded'] = 0;
			$result['In Review'] = 0;
			$result['Pending'] = 0;
			foreach ($screening_res as $key_res => $v_screen) {
				//print_test($v_screen);
				if (!empty($v_screen['screening_status'])) {
					if (empty($result[$v_screen['screening_status']])) {
						$result[$v_screen['screening_status']] = 1;
					} else {
						$result[$v_screen['screening_status']] = $result[$v_screen['screening_status']] + 1;
					}
					$result['Total']++;
				}
			}
			$screen['kappa'] = 1;
			$screen['result'] = $result;
			$screening[$key] = $screen;
			//$this->session->set_userdata('current_screen_phase',$phase['screen_phase_id']);
			//	$this->load->library('../controllers/relis/manager');
			//$screen=$this->manager->screen_result(1,1);
			//print_test($scree);
		}
		//$this->discon();
		return $screening;
	}

	//retrieves a specific configuration element for a given project ID
	private function get_config_element($project_id, $element = "all")
	{
		$config = $this->db_project->get_where('config', array('config_active' => 1))
			->row_array();
		if (!empty($config)) {
			if ($element == 'all') {
				return $config;
			} else {
				return $config[$element];
			}
		} else {
			return "0";
		}
	}

	//retrieves data from a database table based on a specified project and operation.
	private function get_data($project = 'mt_all')
	{
		$this->initialise_user($project);
		$operation_name = 'list_classification';
		$val = "_";
		$page = 0;
		$dynamic_table = 1;
		//Verification de l'operatoion pour recuperer la config correspondante
		$op = check_operation($operation_name, 'List');
		$ref_table = $op['tab_ref'];
		$ref_table_operation = $op['operation_id'];
		//Vérification si il y a une condition de recherche
		$val = urldecode(urldecode($val));
		$filter = array();
		if (isset($_POST['search_all'])) {
			$filter = $this->input->post();
			unset($filter['search_all']);
			$val = "_";
			if (isset($filter['valeur']) and !empty($filter['valeur'])) {
				$val = $filter['valeur'];
				$val = urlencode(urlencode($val));
			}
			/*
			 * mis à jours de l'url en ajoutant la valeur recherché dans le lien puis rechargement de l'url
			 */
			$url = "element/entity_list_data/" . $operation_name . "/" . $val . "/0/";
			redirect($url);
		}
		/*
		 * Récupération de la configuration(structure) de la table à afficher
		 */
		$ref_table_config = get_table_configuration($ref_table);
		$table_id = $ref_table_config['table_id'];
		//Affichage de tous les element
		$rec_per_page = -1;
		$ref_table_config['current_operation'] = $ref_table_operation;
		//récupertaion de la liste
		$data = $this->DBConnection_mdl->get_list_mdl($ref_table_config, $val, $page, $rec_per_page);
		/*
		 * récupération des correspondances des clés externes pour l'affichage  suivant la structure de la table
		 */
		$dropoboxes = array();
		foreach ($ref_table_config['operations'][$ref_table_operation]['fields'] as $k_field => $v) {
			if (!empty($ref_table_config['fields'][$k_field])) {
				$field_det = $ref_table_config['fields'][$k_field];
				if (!empty($field_det['input_type']) and $field_det['input_type'] == 'select') {
					if ($field_det['input_select_source'] == 'array') {
						$dropoboxes[$k_field] = $field_det['input_select_values'];
					} elseif ($field_det['input_select_source'] == 'table') {
						$dropoboxes[$k_field] =
							$this->manager_lib->get_reference_select_values($field_det['input_select_values']);
					} elseif ($field_det['input_select_source'] == 'yes_no') {
						$dropoboxes[$k_field] = array(
							'0' => "No",
							'1' => "Yes"
						);
					}
				}
			}
		}
		/*
		 * Préparation de la liste à afficher sur base du contenu et  stucture de la table
		 */
		//list of the field to be displayed
		$field_list = array();
		//list of the label of field to be displayed
		$field_list_header = array();
		foreach ($ref_table_config['operations'][$ref_table_operation]['fields'] as $k => $v) {
			if (!empty($ref_table_config['fields'][$k])) {
				array_push($field_list, $k);
				$field_header = !empty($v['field_title']) ? $v['field_title'] : $ref_table_config['fields'][$k]['field_title'];
				array_push($field_list_header, $field_header);
			}
		}
		$i = 1;
		$list_to_display = array();
		foreach ($data['list'] as $key => $value) {
			$element_array = array();
			foreach ($field_list as $key_field => $v_field) {
				if (isset($value[$v_field])) {
					if (isset($dropoboxes[$v_field][$value[$v_field]])) {
						$element_array[$v_field] = $dropoboxes[$v_field][$value[$v_field]];
					} elseif (empty($value[$v_field]) and empty($ref_table_config['fields'][$v_field]['display_null'])) {
						$element_array[$v_field] = "";
					} else {
						$element_array[$v_field] = $value[$v_field];
					}
				} else {
					$element_array[$v_field] = "";
					if (
						(isset($ref_table_config['fields'][$v_field]['number_of_values'])
							and $ref_table_config['fields'][$v_field]['number_of_values'] != 1)
						or (isset($ref_table_config['fields'][$v_field]['category_type'])
							and $ref_table_config['fields'][$v_field]['category_type'] == 'WithSubCategories')
					) { //recuperation pour les multivalues et les champs avec subcategory
						if (
							isset($ref_table_config['fields'][$v_field]['input_select_values'])
							and isset($ref_table_config['fields'][$v_field]['input_select_key_field'])
						) {
							// récuperations des valeurs de cet element
							$M_values = $this->manager_lib->get_element_multi_values($ref_table_config['fields'][$v_field]['input_select_values'], $ref_table_config['fields'][$v_field]['input_select_key_field'], $data['list'][$key][$table_id]);
							$S_values = "";
							$Array_values = array();
							foreach ($M_values as $k_m => $v_m) {
								if (isset($dropoboxes[$v_field][$v_m])) {
									$M_values[$k_m] = $dropoboxes[$v_field][$v_m];
								}
								$S_values .= empty($S_values) ? $M_values[$k_m] : " | " . $M_values[$k_m];
								array_push($Array_values, $M_values[$k_m]);
							}
							$element_array[$v_field] = $Array_values;
						}
					}
				}
			}
			if (isset($element_array[$table_id])) {
				$element_array[$table_id] = $i + $page;
			}
			array_push($list_to_display, $element_array);
			$i++;
		}
		if (!empty($list_to_display))
			array_unshift($list_to_display, $field_list_header);
		//print_test($list_to_display);
		$this->discon();
		return $list_to_display;
	}

	//initializes the user session with the necessary user data. 
	private function initialise_user($project)
	{
		/*
		 * Vérification si login et password sont correct
		 */
		$user_id = 6;
		$user = $this->DBConnection_mdl->get_row_details(
			'get_user_detail'
			,
			$user_id,
			true,
			'users'
		);
		if (empty($user)) {
			$data['err_msg'] = 'Username or Password not correct !';
			$this->load->view('user/login', $data);
		} else {
			$this->session->set_userdata($user);
			$this->session->set_userdata('page_msg_err', '');
			$this->session->set_userdata('last_url', "");
			//	$this->session->set_userdata('msg'," Logged in successfully");
			$this->session->set_userdata('submit_mode', 'normal');
			$this->session->set_userdata('language_edit_mode', 'no');
			$this->session->set_userdata('language_edit_mode', 'class');
			//used for redirection after saving data
			$this->session->set_userdata('after_save_redirect', '');
			$this->session->set_userdata('current_screen_phase', '');
			$this->session->set_userdata('debug_paper_code', 'init');
			$this->session->set_userdata('debug_paper_url', 'init');
			$this->session->set_userdata('active_language', 'en');
			$this->session->set_userdata('project_db', $project);
			$default_lang = 'en';
			set_log('Connection', 'API connected', 0);
		}
	}

	//performs user session cleanup and logout.
	private function discon()
	{
		$this->session->sess_destroy();
		$this->session->set_userdata('user_id', 0);
	}
}