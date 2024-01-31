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
class Qa_manager_lib
{
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	/*
		calculates and returns the completion statistics for a given category of QA. 
		It provides information on the overall completion and user-specific completion, including the total number of QA, 
		number of completed QA, and number of pending QA.
	*/
	function get_qa_completion($category = 'QA')
	{
		$res_qa = $this->get_qa_result('all', 0, $category);
		$user_res = array();
		$all_res = array();

		foreach ($res_qa['qa_list'] as $key => $value) {

			if (empty($all_res['all'])) {
				$all_res['all'] = 1;
			} else {
				$all_res['all']++;
			}

			if (empty($user_res[$value['user_id']]['all'])) {
				$user_res[$value['user_id']]['all'] = 1;
			} else {
				$user_res[$value['user_id']]['all']++;
			}

			if (!empty($value['paper_done'])) {
				if (empty($user_res[$value['user_id']]['done'])) {
					$user_res[$value['user_id']]['done'] = 1;
				} else {
					$user_res[$value['user_id']]['done']++;
				}

				if (empty($all_res['done'])) {
					$all_res['done'] = 1;
				} else {
					$all_res['done']++;
				}

			} else {
				if (empty($user_res[$value['user_id']]['pending'])) {
					$user_res[$value['user_id']]['pending'] = 1;
				} else {
					$user_res[$value['user_id']]['pending']++;
				}

				if (empty($all_res['pending'])) {
					$all_res['pending'] = 1;
				} else {
					$all_res['pending']++;
				}
			}
		}
		$result['general_completion'] = $all_res;
		$result['user_completion'] = $user_res;
		return $result;


	}

	//retrieves QA results based on the specified parameters, including the type, ID, category, and status.
	function get_qa_result($type = "mine", $id = 0, $category = 'QA', $add_Link = True, $status = 'all')
	{
		//print_test($type);
		//get qa results
		$project_published = project_published();

		$qa_result = $this->CI->db_current->order_by('qa_id', 'ASC')
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


		if ($category == 'QA_Val') {

			if ($status == 'pending') {
				$extra_condition .= " AND Q.validation IS NULL";
			} elseif ($status == 'done') {
				$extra_condition .= " AND Q.validation IS NOT NULL";
			}

			$sql = "SELECT Q.*,Q.	qa_validation_assignment_id as assignment_id,Q.validation as status,P.title FROM qa_validation_assignment Q,paper P where Q.paper_id=P.id AND 	qa_validation_active=1 AND paper_active=1 $extra_condition ";
		} else {

			if ($status == 'pending') {
				$extra_condition .= " AND Q.qa_status	='Pending'";
			} elseif ($status == 'done') {
				$extra_condition .= " AND Q.qa_status ='Done'";
			}
			$sql = "SELECT Q.*,Q.qa_assignment_id as assignment_id,P.title,P.screening_status as status FROM qa_assignment Q,paper P where Q.paper_id=P.id AND qa_assignment_active=1 AND paper_active=1 $extra_condition ";
		}


		$assignments = $this->CI->db_current->query($sql)->result_array();


		$qa_questions = $this->CI->db_current->order_by('question_id', 'ASC')
			->get_where('qa_questions', array('question_active' => 1))
			->result_array();

		$qa_responses = $this->CI->db_current->order_by('score', 'DESC')
			->get_where('qa_responses', array('response_active' => 1))
			->result_array();

		//print_test($assignments);
		//	print_test($qa_questions);
		//	print_test($qa_responses);
		$users = $this->get_reference_select_values('users;user_name', FALSE, False);



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
					if ($project_published) {
						$link = "";
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
			if ($category == 'QA_Val') {
				if (!empty($v_assign['validation'])) {
					$paper_done = 1;
					$paper_completed++;
				}
			} else {
				if (empty($q_pending)) {
					$paper_done = 1;
					$paper_completed++;
				}
			}
			$all_qa[$v_assign['assignment_id']]['paper_done'] = $paper_done;

		}


		$data['qa_list'] = $all_qa;
		$data['paper_completed'] = $paper_completed;

		return $data;
	}

	//generate a left menu structure specifically for the Quality Assessment (QA) phase
	function get_left_menu_qa()
	{
		$project_published = project_published();
		$can_manage_project = can_manage_project();
		$menu = array();

		$menu['general'] = array(
			'label' => 'General'
		);
		$menu['general']['menu']['home'] = array('label' => 'Dashboard', 'url' => 'quality_assessment/qa', 'icon' => 'th');

		$menu['general']['menu']['papers'] = array('label' => 'Papers in this phase', 'url' => '', 'icon' => 'newspaper-o');

		$menu['general']['menu']['papers']['sub_menu']['all_papers'] = array('label' => 'All', 'url' => 'element/entity_list/list_qa_papers', '');
		$menu['general']['menu']['papers']['sub_menu']['screen_paper_pending'] = array('label' => 'Pending', 'url' => 'element/entity_list/list_qa_papers_pending', '');
		$menu['general']['menu']['papers']['sub_menu']['screen_paper_included'] = array('label' => 'Assessed', 'url' => 'element/entity_list/list_qa_papers_done', '');
		$menu['general']['menu']['papers']['sub_menu']['screen_paper_excluded'] = array('label' => 'Excluded', 'url' => 'quality_assessment/qa_conduct_result/excluded', '');



		$menu['general']['menu']['qa'] = array('label' => 'Quality assessment', 'url' => '', 'icon' => 'list');
		if (!$project_published)
			$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_pending'] = array('label' => 'Assess', 'url' => 'quality_assessment/qa_conduct_list/mine/0/pending', '');
		$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list'] = array('label' => 'My Assignments', 'url' => 'quality_assessment/qa_conduct_list', '');
		$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_done'] = array('label' => 'My Assessed', 'url' => 'quality_assessment/qa_conduct_list/mine/0/done', '');

		if ($can_manage_project) {
			$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_all'] = array('label' => 'All Assignments', 'url' => 'quality_assessment/qa_conduct_list/all', '');
			//$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_all_pending']=array( 'label'=>'All QA - pending', 'url'=>'quality_assessment/qa_conduct_list/all/0/pending', '');
			$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_all_done'] = array('label' => 'All Assessed', 'url' => 'quality_assessment/qa_conduct_list/all/0/done', '');
			$menu['general']['menu']['qa']['sub_menu']['progress'] = array('label' => 'Progress', 'url' => 'quality_assessment/qa_completion', '');
		}

		$menu['general']['menu']['list_qa_result'] = array('label' => 'Results', 'url' => 'quality_assessment/qa_conduct_result', 'icon' => 'th');
		if (get_appconfig_element('qa_validation_on')) {
			$menu['general']['menu']['qa_val'] = array('label' => 'Validation', 'url' => '', 'icon' => 'check-square-o');

			//$menu['general']['menu']['qa_val']['sub_menu']['list_qa_assignment']=array( 'label'=>'Assignment for quality assessment Validation', 'url'=>'element/entity_list/list_qa_validation_assignment', '');
			if (can_validate_project())
				if (!$project_published)
					$menu['general']['menu']['qa_val']['sub_menu']['qa_conduct_list_pending'] = array('label' => 'Validate', 'url' => 'quality_assessment/qa_conduct_list_val/mine/0/pending', '');
			$menu['general']['menu']['qa_val']['sub_menu']['qa_conduct_list_mine'] = array('label' => 'All Assignments', 'url' => 'quality_assessment/qa_conduct_list_val/all', '');

			//$menu['general']['menu']['qa_val']['sub_menu']['qa_conduct_list_mine_pending']=array( 'label'=>'All validations - pending', 'url'=>'quality_assessment/qa_conduct_list_val/all/0/pending', '');
			$menu['general']['menu']['qa_val']['sub_menu']['qa_conduct_list_mine_done'] = array('label' => 'Validated papers', 'url' => 'quality_assessment/qa_conduct_list_val/all/0/done', '');
			$menu['general']['menu']['qa_val']['sub_menu']['progress'] = array('label' => 'Progress', 'url' => 'quality_assessment/qa_completion/validate', '');

			$menu['general']['menu']['qa_val']['sub_menu']['list_qa_result'] = array('label' => 'Results', 'url' => 'element/entity_list/list_qa_validation', '');
		}
		if ($can_manage_project) {
			//	$menu['general']['menu']['questions']=array( 'label'=>'Questions', 'url'=>'element/entity_list/list_qa_questions', 'icon'=>'question-circle');
			//	$menu['general']['menu']['responses']=array( 'label'=>'Responses', 'url'=>'element/entity_list/list_qa_responses', 'icon'=>'check-circle');
		}



		if ($can_manage_project and !$project_published) {
			$menu['adm'] = array(
				'label' => 'ADMINISTRATION'
			);
			$menu['adm']['menu']['plan'] = array('label' => 'Planning', 'url' => '', 'icon' => 'th');

			$menu['adm']['menu']['plan']['sub_menu']['qa_assignment_set'] = array('label' => 'Assign for QA ', 'url' => 'quality_assessment/qa_assignment_set', 'icon' => '');
			$menu['adm']['menu']['plan']['sub_menu']['qa_assignment_validation_set'] = array('label' => 'Assign Validation', 'url' => 'quality_assessment/qa_assignment_validation_set', 'icon' => '');
			$menu['adm']['menu']['plan']['sub_menu']['questions'] = array('label' => 'Questions', 'url' => 'element/entity_list/list_qa_questions', 'icon' => '');
			$menu['adm']['menu']['plan']['sub_menu']['responses'] = array('label' => 'Answers', 'url' => 'element/entity_list/list_qa_responses', 'icon' => '');
			$menu['adm']['menu']['plan']['sub_menu']['general'] = array('label' => 'Settings ', 'url' => 'element/display_element/configurations/1', 'icon' => '');


		}
		return $menu;
	}
}