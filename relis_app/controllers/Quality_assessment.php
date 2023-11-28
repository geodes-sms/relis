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
 * --------------------------------------------------------------------------
 *
 */

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Quality_assessment extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	//function is used to activate or deactivate the QA (Quality Assurance) process
	public function activate_qa($value = 1)
	{
		if ($value != 1)
			$value = 0;
		set_appconfig_element('qa_open', $value);
		redirect('screening/screening_select');
	}

	/*
						function retrieves completion information for QA (Quality Assurance) or QA validation
						retrieves completion data, calculates completion percentages, and presents the information in a view for QA progress
				   */
	public function qa_completion($type = 'qa')
	{
		if ($type == 'validate') {
			$completion = $this->manager_lib->get_qa_completion('QA_Val');
		} else {
			$completion = $this->manager_lib->get_qa_completion('QA');
		}
		$users = $this->manager_lib->get_reference_select_values('users;user_name');
		$per_user_completion = array();
		if (!empty($completion['user_completion'])) {
			foreach ($completion['user_completion'] as $key => $value) {
				if (!empty($value['all'])) {
					$per_user_completion[$key]['total_papers'] = $value['all'];
					$per_user_completion[$key]['papers_screened'] = !empty($value['done']) ? $value['done'] : 0;
					$per_user_completion[$key]['completion'] = (int) ($per_user_completion[$key]['papers_screened'] * 100 / $per_user_completion[$key]['total_papers']);
					$per_user_completion[$key]['user'] = $users[$key];
				}
			}
			$total_papers = $completion['general_completion']['all'];
			$papers_screened = !empty($completion['general_completion']['done']) ? $completion['general_completion']['done'] : 0;
			$per_user_completion['total'] = array(
				'total_papers' => $total_papers,
				'papers_screened' => $papers_screened,
				'completion' => !empty($total_papers) ? (int) ($papers_screened * 100 / $total_papers) : 0,
				'user' => '<b>Total</b>',
			);
		}
		$data['completion_screen'] = $per_user_completion;
		$data['page_title'] = ($type == 'validate') ? lng('QA validation progress') : lng('QA progress');
		$data['top_buttons'] = get_top_button('back', 'Back', 'manage');
		//$data['left_menu_perspective']='left_menu_screening';
		//$data['project_perspective']='screening';
		$data['page'] = 'screening/screen_completion';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	/*
					   responsible for setting up the assignment of papers for quality assessment validation.
				   */
	public function qa_assignment_validation_set($data = array())
	{
		//d
		//$sql="SELECT * from paper  where paper_active = 1 AND screening_status='Included' ";
		$papers_for_qa = $this->get_papers_for_qa_validation();
		//	print_test($papers_for_qa);
		$data['paper_list'] = $papers_for_qa['papers_to_assign_display'];
		$user_table_config = get_table_configuration('users');
		$users = $this->DBConnection_mdl->get_list($user_table_config, '_', 0, -1);
		$_assign_user = array();

		foreach ($users['list'] as $key => $value) {
			if ((user_project($this->session->userdata('project_id'), $value['user_id'])) and can_review_project($value['user_id']) and !has_user_role('Guest',$value['user_id'])) {

				$_assign_user[$value['user_id']] = $value['user_name'];
			}
		}
		//	print_test($users);
		$data['users'] = $_assign_user;
		$data['number_papers'] = $papers_for_qa['count_papers_to_assign'];
		$data['number_papers_assigned'] = $papers_for_qa['count_papers_assigned'];
		$data['percentage_of_papers'] = get_appconfig_element('qa_validation_default_percentage');
		$data['page_title'] = lng('Assign papers for quality assessment validation ');
		$data['top_buttons'] = get_top_button('back', 'Back', 'home');
		$data['page'] = 'quality_assessment/assign_papers_qa_validation';
		//	print_test($papers_assigned_array);
		//print_test($data);
		//exit;
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	//this function prepares the necessary data and loads the view for assigning papers for quality assessment
	public function qa_assignment_set($data = array())
	{
		//d
		//$sql="SELECT * from paper  where paper_active = 1 AND screening_status='Included' ";
		$papers_for_qa = $this->get_papers_for_qa();
		//	print_test($papers_for_qa);
		$data['paper_list'] = $papers_for_qa['papers_to_assign_display'];
		$user_table_config = get_table_configuration('users');
		$users = $this->DBConnection_mdl->get_list($user_table_config, '_', 0, -1);
		$_assign_user = array();
		foreach ($users['list'] as $key => $value) {
			if ((user_project($this->session->userdata('project_id'), $value['user_id'])) and can_review_project($value['user_id']) and !has_user_role('Guest',$value['user_id'])) {

				$_assign_user[$value['user_id']] = $value['user_name'];
			}
		}
		//	print_test($users);
		$data['users'] = $_assign_user;
		$data['number_papers'] = $papers_for_qa['count_papers_to_assign'];
		$data['number_papers_assigned'] = $papers_for_qa['count_papers_assigned'];
		$data['percentage_of_papers'] = 100;
		$data['page_title'] = lng('Assign papers for quality assessment ');
		$data['top_buttons'] = get_top_button('back', 'Back', 'home');
		$data['page'] = 'quality_assessment/assign_papers_qa';
		//	print_test($papers_assigned_array);
		//print_test($data);
		//exit;
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	/*
					   retrieves the papers that are eligible for assignment in the quality assessment process. It returns an array containing the necessary information for assigning papers
				   */
	private function get_papers_for_qa()
	{
		//papers already assigned
		$papers_assigned = $this->db_current->order_by('qa_assignment_id', 'ASC')
			->get_where('qa_assignment', array('qa_assignment_active' => 1, 'assignment_type' => 'QA'))
			->result_array();
		$papers_assigned_array = array();
		foreach ($papers_assigned as $key => $value) {
			$papers_assigned_array[$value['paper_id']] = $value['assigned_to'];
		}
		//all papers
		$all_papers = $this->db_current->order_by('id', 'ASC')
			->get_where('paper', array('paper_active' => 1, 'screening_status' => 'Included'))
			->result_array();
		$paper_to_assign = array();
		$paper_to_assign_display[0] = array('Key', 'Title');
		foreach ($all_papers as $key => $value) {
			if (empty($papers_assigned_array[$value['id']])) { //exclude papers already assigned
				$paper_to_assign_display[$key + 1] = array($value['bibtexKey'], $value['title']);
				$paper_to_assign[$key] = $value['id'];
			}
		}
		$result['count_all_papers'] = count($all_papers);
		$result['count_papers_assigned'] = count($papers_assigned_array);
		$result['count_papers_to_assign'] = count($paper_to_assign); // we remove the header
		$result['papers_to_assign_display'] = $paper_to_assign_display;
		$result['papers_to_assign'] = $paper_to_assign;
		return $result;
	}

	//retrieve the papers that are eligible for validation in the quality assessment process
	private function get_papers_for_qa_validation()
	{
		//papers already assigned
		$papers_assigned = $this->db_current->order_by('qa_validation_assignment_id', 'ASC')
			->get_where('qa_validation_assignment', array('qa_validation_active' => 1))
			->result_array();
		$papers_assigned_array = array();
		foreach ($papers_assigned as $key => $value) {
			$papers_assigned_array[$value['paper_id']] = $value['assigned_to'];
		}

		//all papers
		$all_papers = $this->Quality_assessment_dataAccess->select_qa_papers();

		$paper_to_assign = array();
		$paper_to_assign_display[0] = array('Key', 'Title');
		foreach ($all_papers as $key => $value) {
			if (empty($papers_assigned_array[$value['id']])) { //exclude papers already assigned
				$paper_to_assign_display[$key + 1] = array($value['bibtexKey'], $value['title']);
				$paper_to_assign[$key] = $value['id'];
			}
		}
		$result['count_all_papers'] = count($all_papers);
		$result['count_papers_assigned'] = count($papers_assigned_array);
		$result['count_papers_to_assign'] = count($paper_to_assign); // we remove the header
		$result['papers_to_assign_display'] = $paper_to_assign_display;
		$result['papers_to_assign'] = $paper_to_assign;
		return $result;
	}

	//save the assignments of papers for quality assessment.
	function qa_assignment_save()
	{
		$post_arr = $this->input->post();
		//print_test($post_arr); exit;
		$users = array();
		$i = 1;
		$percentage = intval($post_arr['percentage']);
		if (empty($percentage)) {
			$percentage = 100;
		}
		// Get selected users
		while ($i <= $post_arr['number_of_users']) {
			if (!empty($post_arr['user_' . $i])) {
				array_push($users, $post_arr['user_' . $i]);
			}
			$i++;
		}
		//Verify if selected users is > of required reviews per paper
		if (count($users) < 1) {
			$data['err_msg'] = lng('Please select at least one user  ');
			$this->qa_assignment_set($data);
		} else {
			$reviews_per_paper = 1;
			$papers_all = $this->get_papers_for_qa();
			$papers = $papers_all['papers_to_assign'];
			//		print_test($papers);
			$papers_to_validate_nbr = round(count($papers) * $percentage / 100);
			$operation_description = "Assign  papers for QA";
			//	print_test($papers);
			shuffle($papers); // randomize the list
			//		print_test($papers);exit;
			//	print_test($papers);
			$assign_papers = array();
			$this->db2 = $this->load->database(project_db(), TRUE);
			$operation_code = active_user_id() . "_" . time();
			foreach ($papers as $key => $value) {
				if ($key < $papers_to_validate_nbr) {
					//$assign_papers[$key]['paper']=$value['id'];
					//$assign_papers[$key]['users']=array();
					$assignment_save = array(
						'paper_id' => $value,
						'assigned_to' => '',
						'assigned_by' => active_user_id(),
						'operation_code' => $operation_code,
						'assignment_mode' => 'auto',
					);
					$j = 1;
					//the table to save assignments
					$table_name = get_table_configuration('qa_assignment', 'current', 'table_name');
					while ($j <= $reviews_per_paper) {
						$temp_user = ($key % count($users)) + $j;
						if ($temp_user >= count($users))
							$temp_user = $temp_user - count($users);
						$assignment_save['assigned_to'] = $users[$temp_user];
						//	print_test($assignment_save);
						$this->db2->insert($table_name, $assignment_save);
						$j++;
					}
				}
			}
			//exit;
			//	print_test();
			$operation_arr = array(
				'operation_code' => $operation_code,
				'operation_type' => 'assign_qa',
				'user_id' => active_user_id(),
				'operation_desc' => $operation_description
			);
			//print_test($operation_arr);
			$res2 = $this->manage_mdl->add_operation($operation_arr);
			set_top_msg('Operation completed');
			redirect('home');
		}
	}

	//save the assignments of papers for quality assessment validation.
	function qa_validation_assignment_save()
	{
		$post_arr = $this->input->post();
		//print_test($post_arr); exit;
		$users = array();
		$i = 1;
		$percentage = intval($post_arr['percentage']);
		if (empty($percentage)) {
			$data['err_msg'] = lng(' Please provide  "Percentage of papers" ');
			$this->qa_assignment_validation_set($data);
		} elseif ($percentage > 100 or $percentage <= 0) {
			$data['err_msg'] = lng("Please provide a correct value of percentage");
			$this->qa_assignment_validation_set($data);
		} else {
			// Get selected users
			while ($i <= $post_arr['number_of_users']) {
				if (!empty($post_arr['user_' . $i])) {
					array_push($users, $post_arr['user_' . $i]);
				}
				$i++;
			}
			//Verify if selected users is > of required reviews per paper
			if (count($users) < 1) {
				$data['err_msg'] = lng('Please select at least one user  ');
				$this->qa_assignment_validation_set($data);
			} else {
				$reviews_per_paper = 1;
				$papers_all = $this->get_papers_for_qa_validation();
				$papers = $papers_all['papers_to_assign'];
				//		print_test($papers);
				$papers_to_validate_nbr = round(count($papers) * $percentage / 100);
				$operation_description = "Assign  papers for QA validation";
				//	print_test($papers);
				shuffle($papers); // randomize the list
				//		print_test($papers);exit;
				//	print_test($papers);
				$assign_papers = array();
				$this->db2 = $this->load->database(project_db(), TRUE);
				$operation_code = active_user_id() . "_" . time();
				foreach ($papers as $key => $value) {
					if ($key < $papers_to_validate_nbr) {
						$assignment_save = array(
							'paper_id' => $value,
							'assigned_to' => '',
							'assigned_by' => active_user_id(),
							'operation_code' => $operation_code,
							'assignment_mode' => 'auto',
						);
						$j = 1;
						//the table to save assignments
						$table_name = get_table_configuration('qa_validation_assignment', 'current', 'table_name');
						while ($j <= $reviews_per_paper) {
							$temp_user = ($key % count($users)) + $j;
							if ($temp_user >= count($users))
								$temp_user = $temp_user - count($users);
							$assignment_save['assigned_to'] = $users[$temp_user];
							//	print_test($assignment_save);
							$this->db2->insert($table_name, $assignment_save);
							$j++;
						}
					}
				}
				//exit;
				//	print_test();
				$operation_arr = array(
					'operation_code' => $operation_code,
					'operation_type' => 'assign_qa_validation',
					'user_id' => active_user_id(),
					'operation_desc' => $operation_description
				);
				//print_test($operation_arr);
				$res2 = $this->manage_mdl->add_operation($operation_arr);
				set_top_msg('Operation completed');
				redirect('home');
			}
		}
	}

	/**
	 * display the list of QA conduct results. 
	 * 	The $type parameter specifies the type of results to be displayed, such as 'mine', 'id', or 'excluded'. 
	 * 	The $id parameter is used when displaying results for a specific QA assessment. 
	 * 	The $status parameter determines the status of the results to be displayed, such as 'pending', 'done', or 'all'
	 */
	function qa_conduct_list($type = "mine", $id = 0, $status = 'all')
	{
		$data = $this->get_qa_result($type, $id, 'QA', True, $status);
		//print_test($data);
		if ($type == 'id' and !empty($id)) {
			$data['top_buttons'] = get_top_button('close', 'Close', 'quality_assessment/qa_conduct_result');
		} else {
			$data['top_buttons'] = get_top_button('close', 'Close', 'home');
		}
		$this->session->set_userdata('after_save_redirect', "quality_assessment/qa_conduct_list/$type/$id/$status");
		if ($type == 'excluded') {
			$data['page_title'] = lng("Quality assessment  - papers excluded");
		} else {
			$data['page_title'] = lng("Quality assessment ") . (($status == 'pending' || $status == 'done') ? " - $status" : '');
		}
		$data['page'] = 'quality_assessment/quality_assessment';
		$this->load->view('shared/body', $data);
	}

	//display the list of quality assessment validation (QA_Val) conduct results
	function qa_conduct_list_val($type = "mine", $id = 0, $status = 'all')
	{
		$data = $this->get_qa_result($type, $id, 'QA_Val', FALSE, $status);
		//print_test($data);
		if ($type == 'id' and !empty($id)) {
			$data['top_buttons'] = get_top_button('close', 'Close', 'element/entity_list/list_qa_validation');
		} else {
			$data['top_buttons'] = get_top_button('close', 'Close', 'home');
		}
		$this->session->set_userdata('after_save_redirect', "quality_assessment/qa_conduct_list_val/$type/$id/$status");
		if ($type == 'excluded') {
			$data['page_title'] = lng("Quality assessment validation - papers excluded");
		} else {
			$data['page_title'] = lng("Quality assessment validation") . (($status == 'pending' || $status == 'done') ? " - $status" : '');
		}
		$data['page'] = 'quality_assessment/quality_assessment_validation';
		$this->load->view('shared/body', $data);
	}

	/**
	 * display the detailed information and results of a specific quality assessment (QA) conduct. It takes one parameter, $id, which represents the ID of the QA conduct
	 */
	function qa_conduct_detail($id = 0)
	{
		$data = $this->get_qa_result('id', $id);
		$Included = true;
		foreach ($data['qa_list'] as $key => $value) {
			if ($value['status'] == 'Excluded_QA')
				$Included = false;
		}
		//	print_test($data);
		$data['top_buttons'] = "";
		if (!project_published() and can_manage_project()) {
			if ($Included) {
				$data['top_buttons'] .= get_top_button('all', "Exclude the paper", 'quality_assessment/qa_exlusion/' . $id, 'Exclude', " fa-minus", '', 'btn-danger') . " ";
			} else {
				$data['top_buttons'] .= get_top_button('all', 'Cancel the exclusion', 'quality_assessment/qa_exlusion/' . $id . "/0", 'Cancel the exclusion', " fa-undo", '', 'btn-dark') . " ";
			}
		}
		$data['top_buttons'] .= get_top_button('close', 'Close', 'quality_assessment/qa_conduct_result');
		$this->session->set_userdata('after_save_redirect', "quality_assessment/qa_conduct_detail/$id");
		$data['page_title'] = lng("Quality assessment");
		$data['page'] = 'quality_assessment/quality_assessment';
		$this->load->view('shared/body', $data);
	}

	/**
	 * display the overall result of quality assessment (QA) conducts. 
	 * It takes one optional parameter, $type, which determines the type of result to display. The default value for $type is "all"
	 */
	function qa_conduct_result($type = "all")
	{
		//$type="all";
		$data = $this->get_qa_result($type);
		//print_test($data);
		$qa_cutt_off_score = get_appconfig_element('qa_cutt_off_score');
		$data['qa_cutt_off_score'] = $qa_cutt_off_score;
		//print_test($data);
		$data['top_buttons'] = "";
		if (!project_published() and can_manage_project() and $type == 'all') {
			$data['top_buttons'] .= get_top_button(
				'all',
				"Exclude low quality papers",
				'quality_assessment/qa_exclude_low_quality_validation',
				'Exclude low quality',
				" fa-minus",
				'',
				'btn-danger'
			) . " ";
		}
		$data['top_buttons'] .= get_top_button('close', 'Close', 'home');
		if ($type == 'excluded') {
			$data['page_title'] = lng("Quality assessment - excluded papers ") . " : " . lng('Cut-off score') . " : $qa_cutt_off_score ";
		} else {
			$data['page_title'] = lng("Result of quality assessment ") . " - " . lng('Cut-off score') . " : $qa_cutt_off_score ";
		}
		$data['page'] = 'quality_assessment/quality_assessment_result';
		$this->load->view('shared/body', $data);
	}

	//retrieve the QA result data
	private function get_qa_result($type = "mine", $id = 0, $category = 'QA', $add_Link = True, $status = 'all')
	{
		return $this->manager_lib->get_qa_result($type, $id, $category, $add_Link, $status);
	}

	//retrieve the QA result data based on the provided parameters
	private function get_qa_result_old($type = "mine", $id = 0, $category = 'QA', $add_Link = True, $status = 'all')
	{
		//print_test($type);
		//get qa results
		$qa_result = $this->db_current->order_by('qa_id', 'ASC')
			->get_where('qa_result', array('qa_active' => 1))
			->result_array();
		//Put result in searchable array
		$array_qa_result = array();
		foreach ($qa_result as $key_result => $v_result) {
			$array_qa_result[$v_result['paper_id']][$v_result['question']][$v_result['response']] = 1;
		}
		//print_test($qa_result);
		//	print_test($array_qa_result);
		//get_assignments
		if ($type == 'id' and !empty($id)) {
			$extra_condition = " AND paper_id= '" . $id . "' ";
		} elseif ($type == 'all') {
			$extra_condition = " AND screening_status='Included' ";
		} elseif ($type == 'excluded') {
			$extra_condition = " AND screening_status='Excluded_QA' ";
		} else {
			$extra_condition = " AND screening_status='Included'  AND assigned_to= '" . active_user_id() . "' ";
		}

		//echo $sql;
		$assignments = $this->Quality_assessment_dataAccess->select_qa_assignments($category, $status);

		$qa_questions = $this->db_current->order_by('question_id', 'ASC')
			->get_where('qa_questions', array('question_active' => 1))
			->result_array();
		$qa_responses = $this->db_current->order_by('score', 'DESC')
			->get_where('qa_responses', array('response_active' => 1))
			->result_array();
		//print_test($assignments);
		//	print_test($qa_questions);
		//	print_test($qa_responses);
		$users = $this->manager_lib->get_reference_select_values('users;user_name', FALSE, False);
		$all_qa = array();
		$all_qa_html = array();
		$paper_completed = 0;
		foreach ($assignments as $key_assign => $v_assign) {
			$all_qa[$v_assign['assignment_id']] = array(
				'paper_id' => $v_assign['paper_id'],
				'title' => $v_assign['title'],
				'status' => $v_assign['status'],
				'user' => !empty($users[$v_assign['assigned_to']]) ? $users[$v_assign['assigned_to']] : '',
				'user_id' => !empty($users[$v_assign['assigned_to']]) ? $v_assign['assigned_to'] : '',
			);
			$questions = array();
			$q_result_score = 0;
			$q_done = 0;
			$q_pending = 0;
			foreach ($qa_questions as $k_question => $v_question) {
				$questions[$v_question['question_id']] = array(
					'question' => $v_question,
				);
				$responses = array();
				$q_result = !empty($array_qa_result[$v_assign['paper_id']][$v_question['question_id']]) ? 1 : 0;
				$question_asw = 0;
				foreach ($qa_responses as $k_response => $v_response) {
					if (empty($array_qa_result[$v_assign['paper_id']][$v_question['question_id']][$v_response['response_id']])) { //see if the response have been chosed for the question
						$res = 0;
						if ($add_Link)
							$link = "quality_assessment/qa_conduct_save/$q_result/" . $v_assign['paper_id'] . '/' . $v_question['question_id'] . '/' . $v_response['response_id'];
						else
							$link = "";
					} else {
						$res = 1;
						$link = "";
						$q_result_score += $v_response['score'];
						$question_asw = 1;
					}
					$responses[$v_response['response_id']] = array(
						'response' => $v_response,
						'result' => $res,
						'link' => $link,
					);
				}
				$questions[$v_question['question_id']]['responses'] = $responses;
				$questions[$v_question['question_id']]['q_result'] = $q_result;
				if ($question_asw) {
					$q_completed = 1;
					$q_done++;
				} else {
					$q_completed = 0;
					$q_pending++;
				}
				$questions[$v_question['question_id']]['completed'] = $q_completed;
			}
			$all_qa[$v_assign['assignment_id']]['q_result_score'] = $q_result_score;
			;
			$all_qa[$v_assign['assignment_id']]['questions'] = $questions;
			$paper_done = 0;
			if (empty($q_pending)) {
				$paper_done = 1;
				$paper_completed++;
			}
			$all_qa[$v_assign['assignment_id']]['paper_done'] = $paper_done;
		}
		$data['qa_list'] = $all_qa;
		$data['paper_completed'] = $paper_completed;
		return $data;
	}

	//responsible for saving the QA result for a specific paper, question, and response.
	function qa_conduct_save($update, $paper_id, $question, $response)
	{
		$qa_result = array(
			'paper_id' => $paper_id,
			'question' => $question,
			'response' => $response,
			'done_by' => active_user_id()
		);
		if (!$update) {
			$this->db_current->insert('qa_result', $qa_result);
		} else {
			$this->db_current->update('qa_result', $qa_result, array('paper_id' => $paper_id, 'question' => $question));
		}
		$after_after_save_redirect = $this->session->userdata('after_save_redirect');
		if (!empty($after_after_save_redirect)) {
			$this->session->set_userdata('after_save_redirect', '');
		} else {
			$after_after_save_redirect = "quality_assessment/qa_conduct_list";
		}
		//update assignment
		if ($this->qa_done_for_paper($paper_id)) {
			$this->db_current->update('qa_assignment', array('qa_status' => 'Done'), array('paper_id' => $paper_id));
		} else {
			//$this->db_current->update('qa_assignment',array('qa_status'=>'Pending'),array('paper_id'=>$paper_id));
		}
		header("Location: " . base_url() . $after_after_save_redirect . '.html#paper_' . $paper_id);
		die();
	}

	//Verify if all questions have been answered for the paper
	private function qa_done_for_paper($paper_id)
	{
		$result = $this->Quality_assessment_dataAccess->count_qa($paper_id);

		//print_test($result);
		if (empty($result['nbr'])) {
			return TRUE; //all questions have been responded
		} else {
			return FALSE;
		}
	}

	//exclude or cancel the exclusion of a paper from quality assessment
	function qa_exlusion($paper_id, $op = 1)
	{
		if ($op == 1) {
			$this->db_current->update('paper', array('screening_status' => 'Excluded_QA', 'classification_status' => 'Waiting'), array('id' => $paper_id));
		} else {
			$this->db_current->update('paper', array('screening_status' => 'Included', 'classification_status' => 'To classify'), array('id' => $paper_id));
		}
		$after_after_save_redirect = "quality_assessment/qa_conduct_result";
		redirect($after_after_save_redirect);
	}

	//exclude all papers with low quality
	function qa_exclude_low_quality()
	{
		//s
		$qa_result = $this->get_qa_result('all');
		//print_test($qa_result);
		$qa_cutt_off_score = get_appconfig_element('qa_cutt_off_score');
		$excluded = 0;
		if (!empty($qa_result['qa_list'])) {
			foreach ($qa_result['qa_list'] as $key => $value) {
				if ($value['q_result_score'] < $qa_cutt_off_score) {
					$this->db_current->update(
						'paper',
						array(
							'screening_status' => 'Excluded_QA',
							'classification_status' => 'Waiting'
						),
						array('id' => $value['paper_id'])
					);
					$excluded++;
				}
			}
		}
		if ($excluded > 0) {
			set_top_msg("Completed " . $excluded . " paper(s) excluded");
		} else {
			set_top_msg("No paper to exclude!");
		}
		$after_after_save_redirect = "quality_assessment/qa_conduct_result";
		redirect($after_after_save_redirect);
	}

	//display a confirmation page for excluding low quality papers from the quality assessment process
	function qa_exclude_low_quality_validation()
	{
		$data['page'] = 'install/frm_install_result';
		//$data['left_menu_admin']=True;
		$data['array_warning'] = array('You want to delete All papers with low quality : The opération cannot be undone !');
		$data['array_success'] = array();
		$data['next_operation_button'] = "";
		$data['page_title'] = lng('Exclude low quality papers');
		$data['next_operation_button'] = " &nbsp &nbsp &nbsp" . get_top_button('all', 'Continue to delete', 'quality_assessment/qa_exclude_low_quality', 'Continue', '', '', ' btn-success ', FALSE);
		$data['next_operation_button'] .= get_top_button('all', 'Cancel', 'quality_assessment/qa_conduct_result', 'Cancel', '', '', ' btn-danger ', FALSE);
		$this->load->view('shared/body', $data);
	}

	//perform the validation of a quality assessment for a specific paper.
	function qa_validate($paper_id, $op = 1)
	{
		if ($op == 1) {
			$this->db_current->update('qa_validation_assignment', array('validation' => 'Correct', 'validation_note' => '', 'validation_time' => bm_current_time()), array('paper_id' => $paper_id));
		} else {
			$assignment = $this->db_current->get_where(
				'qa_validation_assignment',
				array('qa_validation_active' => 1, 'paper_id' => $paper_id)
			)
				->row_array();
			if (!empty($assignment['qa_validation_assignment_id'])) {
				redirect('element/edit_element/qa_not_valid/' . $assignment['qa_validation_assignment_id']);
			}
			//$this->db_current->update('qa_validation_assignment',array('validation'=>'Not Correct'),array('paper_id'=>$paper_id));
		}
		if (!empty($after_after_save_redirect)) {
			$this->session->set_userdata('after_save_redirect', '');
		} else {
			$after_after_save_redirect = "quality_assessment/qa_conduct_list_val";
		}
		header("Location: " . base_url() . $after_after_save_redirect . '.html#paper_' . $paper_id);
		die();
	}

	//handles various aspects related to the QA perspective, including completion status, action buttons, project publishing status
	public function qa() //quality assessment
	{
		$project_published = project_published();
		//update_paper_status_all();
		//$this->session->set_userdata('working_perspective','screen');
		$completion = $this->manager_lib->get_qa_completion('QA');
		$general_completion = $completion['general_completion'];
		$user_completion = $completion['user_completion'];
		//print_test($user_completion);
		$active_user_id = active_user_id();
		if (!empty($user_completion[$active_user_id]['all'])) {
			$data['qa_completion']['title'] = "My completion";
			$data['qa_completion']['all_papers'] = array(
				'value' => $user_completion[$active_user_id]['all'],
				'title' => 'All',
				'url' => 'quality_assessment/qa_conduct_list'
			);
			$data['qa_completion']['pending_papers'] = array(
				'value' => !empty($user_completion[$active_user_id]['pending']) ? $user_completion[$active_user_id]['pending'] : 0,
				'title' => 'Pending',
				'url' => 'quality_assessment/qa_conduct_list/mine/0/pending'
			);
			$data['qa_completion']['done_papers'] = array(
				'value' => !empty($user_completion[$active_user_id]['done']) ? $user_completion[$active_user_id]['done'] : 0,
				'title' => 'Done',
				'url' => 'quality_assessment/qa_conduct_list/mine/0/done'
			);
			$data['qa_completion']['gauge_all'] = $user_completion[$active_user_id]['all'];
			$data['qa_completion']['gauge_done'] = !empty($user_completion[$active_user_id]['done']) ? $user_completion[$active_user_id]['done'] : 0;
		}
		if (!empty($general_completion['all'])) {
			$data['gen_qa_completion']['title'] = "Overall completion";
			$data['gen_qa_completion']['all_papers'] = array(
				'value' => $general_completion['all'],
				'title' => 'All',
				'url' => 'quality_assessment/qa_conduct_list/all'
			);
			$data['gen_qa_completion']['pending_papers'] = array(
				'value' => !empty($general_completion['pending']) ? $general_completion['pending'] : 0,
				'title' => 'Pending',
				'url' => 'quality_assessment/qa_conduct_list/all/0/pending'
			);
			$data['gen_qa_completion']['done_papers'] = array(
				'value' => !empty($general_completion['done']) ? $general_completion['done'] : 0,
				'title' => 'Done',
				'url' => 'quality_assessment/qa_conduct_list/all/0/done'
			);
			$data['gen_qa_completion']['gauge_all'] = $general_completion['all'];
			$data['gen_qa_completion']['gauge_done'] = !empty($general_completion['done']) ? $general_completion['done'] : 0;
		}
		if (get_appconfig_element('qa_validation_on')) {
			$completion_val = $this->manager_lib->get_qa_completion('QA_Val');
			//print_test($completion_val);
			$general_completion_val = $completion_val['general_completion'];
			$user_completion_val = $completion_val['user_completion'];
		}
		if (!empty($user_completion_val[$active_user_id]['all'])) {
			$data['qa_completion_val']['title'] = "My validation completion";
			$data['qa_completion_val']['all_papers'] = array(
				'value' => $user_completion_val[$active_user_id]['all'],
				'title' => 'All',
				'url' => 'quality_assessment/qa_conduct_list_val'
			);
			$data['qa_completion_val']['pending_papers'] = array(
				'value' => !empty($user_completion_val[$active_user_id]['pending']) ? $user_completion_val[$active_user_id]['pending'] : 0,
				'title' => 'Pending',
				'url' => 'quality_assessment/qa_conduct_list_val/mine/0/pending'
			);
			$data['qa_completion_val']['done_papers'] = array(
				'value' => !empty($user_completion_val[$active_user_id]['done']) ? $user_completion_val[$active_user_id]['done'] : 0,
				'title' => 'Done',
				'url' => 'quality_assessment/qa_conduct_list_val/mine/0/done'
			);
			$data['qa_completion_val']['gauge_all'] = $user_completion_val[$active_user_id]['all'];
			$data['qa_completion_val']['gauge_done'] = !empty($user_completion_val[$active_user_id]['done']) ? $user_completion_val[$active_user_id]['done'] : 0;
		}
		if (!empty($general_completion_val['all'])) {
			$data['gen_qa_completion_val']['title'] = "Overall validation completion";
			$data['gen_qa_completion_val']['all_papers'] = array(
				'value' => $general_completion_val['all'],
				'title' => 'All',
				'url' => 'quality_assessment/qa_conduct_list_val/all'
			);
			$data['gen_qa_completion_val']['pending_papers'] = array(
				'value' => !empty($general_completion_val['pending']) ? $general_completion_val['pending'] : 0,
				'title' => 'Pending',
				'url' => 'quality_assessment/qa_conduct_list_val/all/0/pending'
			);
			$data['gen_qa_completion_val']['done_papers'] = array(
				'value' => !empty($general_completion_val['done']) ? $general_completion_val['done'] : 0,
				'title' => 'Done',
				'url' => 'quality_assessment/qa_conduct_list_val/all/0/done'
			);
			$data['gen_qa_completion_val']['gauge_all'] = $general_completion_val['all'];
			$data['gen_qa_completion_val']['gauge_done'] = !empty($general_completion_val['done']) ? $general_completion_val['done'] : 0;
		}
		$action_but = array();
		if (can_manage_project() and !$project_published)
			$action_but['assign_screen'] = get_top_button('all', 'Assign papers for QA', 'quality_assessment/qa_assignment_set', 'Assign papers', 'fa-mail-forward', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
		if (can_review_project() and !$project_published)
			$action_but['screen'] = get_top_button('all', 'Classify', 'quality_assessment/qa_conduct_list/mine/0/pending', 'Assess', 'fa-search', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
		//$action_but['screen_result']=get_top_button ( 'all', 'Screening progress', 'screening/screen_completion','Progress','fa-tasks','',' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ' ,False);
		$action_but['screen_completion'] = get_top_button('all', 'Result', 'quality_assessment/qa_conduct_result', 'Result', 'fa-th', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
		$data['action_but_screen'] = $action_but;
		$action_but = array();
		if (get_appconfig_element('qa_validation_on')) {
			if (can_validate_project() and !$project_published) {
				$action_but['assign_screen'] = get_top_button('all', 'Assign papers for validation', 'quality_assessment/qa_assignment_validation_set', 'Assign papers', 'fa-mail-forward', '', ' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ', False);
				$action_but['screen'] = get_top_button('all', 'Validate', 'quality_assessment/qa_conduct_list_val/mine/0/pending', 'Validate', 'fa-check-square-o', '', ' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ', False);
			}
			$action_but['screen_completion'] = get_top_button('all', 'Result', 'element/entity_list/list_qa_validation', 'Result', 'fa-th', '', ' btn-primary action_butt  col-md-3 col-sm-3 col-xs-12', False);
			$data['action_but_validate'] = $action_but;
		}
		if (!($this->session->userdata('project_db'))) {
			redirect('project/projects_list');
		}
		if ($this->session->userdata('working_perspective') == 'class') {
			redirect('home');
		}
		$data['configuration'] = get_project_config($this->session->userdata('project_db'));
		/*
		 * Chargement de la vue qui va s'afficher
		 *
		 */
		$data['page'] = 'quality_assessment/h_qa';
		$this->load->view('shared/body', $data);
	}
}