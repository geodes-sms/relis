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
 * This controller contain all the pages user can access before connection to the application
 * - homepage
 * - authentification page
 * - help page
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
	class Home extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
		}
		
		
		
		/**
		 * Classification home page
		 */
		public function index()
		{
			if (!($this->session->userdata('project_db')) OR $this->session->userdata('project_db') == 'default') {
				redirect('manager/projects_list');
			}
			if ($this->session->userdata('working_perspective') == 'screen') {
				redirect('home/screening');
			}
			if ($this->session->userdata('working_perspective') == 'qa') {
				redirect('home/qa');
			}
			$left_menu                  = $this->manager_lib->get_left_menu();
			$project_published          = project_published();
			$my_class_completion        = $this->DBConnection_mdl->count_papers('all');
			$data['processed_papers']   = $this->DBConnection_mdl->count_papers('processed');
			$data['pending_papers']     = $this->DBConnection_mdl->count_papers('pending');
			$data['assigned_me_papers'] = $this->DBConnection_mdl->count_papers('assigned_me');
			$data['excluded_papers']    = $this->DBConnection_mdl->count_papers('excluded');
			$gen_class_completion       = $this->manager_lib->get_classification_completion('class', 'all');
			$my_class_completion        = $this->manager_lib->get_classification_completion('class', '');
			if (get_appconfig_element('class_validation_on')) {
				$gen_validation_completion = $this->manager_lib->get_classification_completion('validation', 'all');
				$my_validation_completion  = $this->manager_lib->get_classification_completion('validation', '');
			}
			if (!empty($my_class_completion['all_papers'])) {
				$data['classification_completion']['title']          = "My classification completion";
				$data['classification_completion']['all_papers']     = array(
						'value' => $my_class_completion['all_papers'],
						'title' => 'All',
						'url' => 'op/entity_list/list_class_assignment_mine'
				);
				$data['classification_completion']['pending_papers'] = array(
						'value' => $my_class_completion['pending_papers'],
						'title' => 'Pending',
						'url' => 'op/entity_list/list_class_assignment_pending_mine'
				);
				$data['classification_completion']['done_papers']    = array(
						'value' => $my_class_completion['processed_papers'],
						'title' => 'Processed',
						'url' => 'op/entity_list/list_class_assignment_done_mine'
				);
				$data['classification_completion']['gauge_all']      = $my_class_completion['all_papers'];
				$data['classification_completion']['gauge_done']     = $my_class_completion['processed_papers'];
			}
			if (!empty($gen_class_completion['all_papers'])) {
				$data['gen_classification_completion']['title']          = "Overall classification completion";
				$data['gen_classification_completion']['all_papers']     = array(
						'value' => $gen_class_completion['all_papers'],
						'title' => 'All',
						'url' => 'op/entity_list/list_class_assignment'
				);
				$data['gen_classification_completion']['pending_papers'] = array(
						'value' => $gen_class_completion['pending_papers'],
						'title' => 'Pending',
						'url' => 'op/entity_list/list_class_assignment_pending'
				);
				$data['gen_classification_completion']['done_papers']    = array(
						'value' => $gen_class_completion['processed_papers'],
						'title' => 'Processed',
						'url' => 'op/entity_list/list_class_assignment_done'
				);
				$data['gen_classification_completion']['gauge_all']      = $gen_class_completion['all_papers'];
				$data['gen_classification_completion']['gauge_done']     = $gen_class_completion['processed_papers'];
			}
			if (!empty($my_validation_completion['all_papers'])) {
				$data['my_validation_completion']['title']          = "My validation completion";
				$data['my_validation_completion']['all_papers']     = array(
						'value' => $my_validation_completion['all_papers'],
						'title' => 'All',
						'url' => 'op/entity_list/list_class_validation_mine'
				);
				$data['my_validation_completion']['pending_papers'] = array(
						'value' => $my_validation_completion['pending_papers'],
						'title' => 'Pending',
						'url' => ''
				);
				$data['my_validation_completion']['done_papers']    = array(
						'value' => $my_validation_completion['processed_papers'],
						'title' => 'Processed',
						'url' => ''
				);
				$data['my_validation_completion']['gauge_all']      = $my_validation_completion['all_papers'];
				$data['my_validation_completion']['gauge_done']     = $my_validation_completion['processed_papers'];
			}
			if (!empty($gen_validation_completion['all_papers'])) {
				$data['gen_validation_completion']['title']          = "Overall validation completion";
				$data['gen_validation_completion']['all_papers']     = array(
						'value' => $gen_validation_completion['all_papers'],
						'title' => 'All',
						'url' => 'op/entity_list/list_class_validation'
				);
				$data['gen_validation_completion']['pending_papers'] = array(
						'value' => $gen_validation_completion['pending_papers'],
						'title' => 'Pending',
						'url' => ''
				);
				$data['gen_validation_completion']['done_papers']    = array(
						'value' => $gen_validation_completion['processed_papers'],
						'title' => 'Processed',
						'url' => ''
				);
				$data['gen_validation_completion']['gauge_all']      = $gen_validation_completion['all_papers'];
				$data['gen_validation_completion']['gauge_done']     = $gen_validation_completion['processed_papers'];
			}
			$action_but = array();
			if (can_manage_project() AND !$project_published)
				$action_but['assign_screen'] = get_top_button('all', 'Assign papers for classification', 'relis/manager/class_assignment_set', 'Assign papers', 'fa-mail-forward', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
				if (can_review_project() AND !$project_published)
					$action_but['screen'] = get_top_button('all', 'Classify', 'op/entity_list/list_class_assignment_pending_mine', 'Classify', 'fa-search', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
					if (can_manage_project()) {
						$action_but['screen_completion'] = get_top_button('all', 'Result', 'op/entity_list/list_classification', 'Result', 'fa-th', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
					}
					$data['action_but_screen'] = $action_but;
					$action_but                = array();
					if (get_appconfig_element('class_validation_on')) {
						if (can_validate_project() AND !$project_published) {
							$action_but['assign_screen'] = get_top_button('all', 'Assign papers for validation', 'relis/manager/class_assignment_validation_set', 'Assign papers', 'fa-mail-forward', '', ' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ', False);
							$action_but['screen']        = get_top_button('all', 'Validate', 'op/entity_list/list_class_validation_mine', 'Validate', 'fa-check-square-o', '', ' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ', False);
						}
						$action_but['screen_completion'] = get_top_button('all', 'Result', 'op/entity_list/list_class_validation', 'Result', 'fa-th', '', ' btn-primary action_butt  col-md-3 col-sm-3 col-xs-12', False);
						$data['action_but_validate']     = $action_but;
					}
					$data['configuration'] = get_project_config($this->session->userdata('project_db'));
					$data['users']         = $this->DBConnection_mdl->get_users_all();
					foreach ($data['users'] as $key => $value) {
						if (!(user_project($this->session->userdata('project_id'), $value['user_id'])) OR $value['user_usergroup'] == 1) {
							unset($data['users'][$key]);
						}
					}
					$data['page'] = 'general/home';
					$this->load->view('body', $data);
		}
			
		
		public function screening()
		{
			$project_published = project_published();
			if (!($this->session->userdata('project_db'))) {
				redirect('manager/projects_list');
			}
			if ($this->session->userdata('working_perspective') == 'class') {
				redirect('home');
			}
			if (!active_screening_phase()) {
				redirect('home/screening_select');
			}
			$data['screening_phase_info'] = active_screening_phase_info();
			$screening_completion         = $this->get_user_completion(active_user_id(), active_screening_phase(), 'Screening');
			if (!empty($screening_completion['all_papers'])) {
				$data['screening_completion']['title']           = "My screening progress";
				$data['screening_completion']['all_papers']      = array(
						'value' => $screening_completion['all_papers'],
						'title' => 'All',
						'url' => 'op/entity_list/list_my_assignments'
				);
				$data['screening_completion']['pending_papers']  = array(
						'value' => $screening_completion['all_papers'] - $screening_completion['papers_done'],
						'title' => 'Pending',
						'url' => 'op/entity_list/list_my_pending_screenings'
				);
				$data['screening_completion']['done_papers']     = array(
						'value' => $screening_completion['papers_done'],
						'title' => 'Screened',
						'url' => 'op/entity_list/list_my_screenings'
				);
				$data['screening_completion']['conflict_papers'] = array(
						'value' => $screening_completion['papers_in_conflict'],
						'title' => 'Conflicts',
						'url' => 'op/entity_list/list_papers_screen_conflict'
				);
				$data['screening_completion']['gauge_all']       = $screening_completion['all_papers'];
				$data['screening_completion']['gauge_done']      = $screening_completion['papers_done'] - $screening_completion['papers_in_conflict'];
			}
			$general_screening_completion = $this->get_user_completion(0, active_screening_phase(), 'Screening');
			if (!empty($general_screening_completion['all_papers'])) {
				$data['general_screening_completion']['title']           = "Overall screening assignment  progress";
				$data['general_screening_completion']['all_papers']      = array(
						'value' => $general_screening_completion['all_papers'],
						'title' => 'All',
						'url' => 'op/entity_list/list_assignments'
				);
				$data['general_screening_completion']['pending_papers']  = array(
						'value' => $general_screening_completion['all_papers'] - $general_screening_completion['papers_done'],
						'title' => 'Pending',
						'url' => 'op/entity_list/list_all_pending_screenings'
				);
				$data['general_screening_completion']['done_papers']     = array(
						'value' => $general_screening_completion['papers_done'],
						'title' => 'Screened',
						'url' => 'op/entity_list/list_screenings'
				);
				$data['general_screening_completion']['conflict_papers'] = array(
						'value' => $general_screening_completion['papers_in_conflict'],
						'title' => 'Conflicts',
						'url' => 'op/entity_list/list_papers_screen_conflict'
				);
				$data['general_screening_completion']['gauge_all']       = $general_screening_completion['all_papers'];
				$data['general_screening_completion']['gauge_done']      = $general_screening_completion['papers_done'] - $general_screening_completion['papers_in_conflict'];
			}
			if (get_appconfig_element('screening_validation_on')) {
				$validation_completion         = $this->get_user_completion(active_user_id(), active_screening_phase(), 'screen_validation');
				$general_validation_completion = $this->get_user_completion(0, active_screening_phase(), 'screen_validation');
			}
			if (!empty($validation_completion['all_papers'])) {
				$data['validation_completion']['title']          = "My validations progress";
				$data['validation_completion']['all_papers']     = array(
						'value' => $validation_completion['all_papers'],
						'title' => 'All',
						'url' => 'op/entity_list/list_my_validations_assignment'
				);
				$data['validation_completion']['pending_papers'] = array(
						'value' => $validation_completion['all_papers'] - $validation_completion['papers_done'],
						'title' => 'Pending',
						'url' => 'op/entity_list/list_my_pending_validation'
				);
				$data['validation_completion']['done_papers']    = array(
						'value' => $validation_completion['papers_done'],
						'title' => 'Validated',
						'url' => 'op/entity_list/list_my_done_validation'
				);
				$data['validation_completion']['gauge_all']      = $validation_completion['all_papers'];
				$data['validation_completion']['gauge_done']     = $validation_completion['papers_done'];
			}
			if (!empty($general_validation_completion['all_papers'])) {
				$data['general_validation_completion']['title']          = "Overall validations progress";
				$data['general_validation_completion']['all_papers']     = array(
						'value' => $general_validation_completion['all_papers'],
						'title' => 'All',
						'url' => 'op/entity_list/list_assignments_validation'
				);
				$data['general_validation_completion']['pending_papers'] = array(
						'value' => $general_validation_completion['all_papers'] - $general_validation_completion['papers_done'],
						'title' => 'Pending',
						'url' => 'op/entity_list/list_pending_screenings_validation'
				);
				$data['general_validation_completion']['done_papers']    = array(
						'value' => $general_validation_completion['papers_done'],
						'title' => 'Validated',
						'url' => 'op/entity_list/list_screenings_validation'
				);
				$data['general_validation_completion']['gauge_all']      = $general_validation_completion['all_papers'];
				$data['general_validation_completion']['gauge_done']     = $general_validation_completion['papers_done'];
			}
			$action_but = array();
			if (can_manage_project() AND !$project_published)
				$action_but['assign_screen'] = get_top_button('all', 'Assign papers for screening', 'relis/manager/assignment_screen', 'Assign papers', 'fa-mail-forward', '', ' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ', False);
				if (can_review_project() AND !$project_published)
					$action_but['screen'] = get_top_button('all', 'Screen papers', 'relis/manager/screen_paper', 'Screen', 'fa-search', '', ' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ', False);
					if (can_manage_project() OR get_appconfig_element('screening_result_on')) {
						$action_but['screen_result']     = get_top_button('all', 'Screening progress', 'relis/manager/screen_completion', 'Progress', 'fa-tasks', '', ' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ', False);
						$action_but['screen_completion'] = get_top_button('all', 'Screening Statistics', 'relis/manager/screen_result', 'Statistics', 'fa-th', '', ' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ', False);
					}
					$data['action_but_screen'] = $action_but;
					$action_but                = array();
					if (get_appconfig_element('screening_validation_on')) {
						if (can_validate_project() AND !$project_published) {
							$action_but['assign_screen'] = get_top_button('all', 'Assign papers for validation', 'relis/manager/validate_screen_set', 'Assign papers', 'fa-mail-forward', '', ' btn-primary action_butt col-md-2 col-sm-2 col-xs-12 ', False);
							$action_but['screen']        = get_top_button('all', 'Validate screening', 'relis/manager/screen_paper_validation', 'Validate', 'fa-check-square-o', '', ' btn-primary action_butt col-md-2 col-sm-2 col-xs-12 ', False);
						}
						$action_but['screen_result']     = get_top_button('all', 'Validation progress', 'relis/manager/screen_completion/validate', 'Progress', 'fa-tasks', '', ' btn-primary action_butt col-md-2 col-sm-2 col-xs-12 ', False);
						$action_but['screen_completion'] = get_top_button('all', 'Validation Statistics', 'relis/manager/screen_validation_result', 'Statistics', 'fa-th', '', ' btn-primary action_butt  col-md-2 col-sm-2 col-xs-12', False);
						$data['action_but_validate']     = $action_but;
					}
					$data['configuration'] = get_project_config($this->session->userdata('project_db'));
					$data['page']          = 'relis/h_screening';
					$this->load->view('body', $data);
		}
		public function qa()
		{
			$project_published  = project_published();
			$completion         = $this->manager_lib->get_qa_completion('QA');
			$general_completion = $completion['general_completion'];
			$user_completion    = $completion['user_completion'];
			$active_user_id     = active_user_id();
			if (!empty($user_completion[$active_user_id]['all'])) {
				$data['qa_completion']['title']          = "My completion";
				$data['qa_completion']['all_papers']     = array(
						'value' => $user_completion[$active_user_id]['all'],
						'title' => 'All',
						'url' => 'relis/manager/qa_conduct_list'
				);
				$data['qa_completion']['pending_papers'] = array(
						'value' => !empty($user_completion[$active_user_id]['pending']) ? $user_completion[$active_user_id]['pending'] : 0,
						'title' => 'Pending',
						'url' => 'relis/manager/qa_conduct_list/mine/0/pending'
				);
				$data['qa_completion']['done_papers']    = array(
						'value' => !empty($user_completion[$active_user_id]['done']) ? $user_completion[$active_user_id]['done'] : 0,
						'title' => 'Done',
						'url' => 'relis/manager/qa_conduct_list/mine/0/done'
				);
				$data['qa_completion']['gauge_all']      = $user_completion[$active_user_id]['all'];
				$data['qa_completion']['gauge_done']     = !empty($user_completion[$active_user_id]['done']) ? $user_completion[$active_user_id]['done'] : 0;
			}
			if (!empty($general_completion['all'])) {
				$data['gen_qa_completion']['title']          = "Overall completion";
				$data['gen_qa_completion']['all_papers']     = array(
						'value' => $general_completion['all'],
						'title' => 'All',
						'url' => 'relis/manager/qa_conduct_list/all'
				);
				$data['gen_qa_completion']['pending_papers'] = array(
						'value' => !empty($general_completion['pending']) ? $general_completion['pending'] : 0,
						'title' => 'Pending',
						'url' => 'relis/manager/qa_conduct_list/all/0/pending'
				);
				$data['gen_qa_completion']['done_papers']    = array(
						'value' => !empty($general_completion['done']) ? $general_completion['done'] : 0,
						'title' => 'Done',
						'url' => 'relis/manager/qa_conduct_list/all/0/done'
				);
				$data['gen_qa_completion']['gauge_all']      = $general_completion['all'];
				$data['gen_qa_completion']['gauge_done']     = !empty($general_completion['done']) ? $general_completion['done'] : 0;
			}
			if (get_appconfig_element('qa_validation_on')) {
				$completion_val         = $this->manager_lib->get_qa_completion('QA_Val');
				$general_completion_val = $completion_val['general_completion'];
				$user_completion_val    = $completion_val['user_completion'];
			}
			if (!empty($user_completion_val[$active_user_id]['all'])) {
				$data['qa_completion_val']['title']          = "My validation completion";
				$data['qa_completion_val']['all_papers']     = array(
						'value' => $user_completion_val[$active_user_id]['all'],
						'title' => 'All',
						'url' => 'relis/manager/qa_conduct_list_val'
				);
				$data['qa_completion_val']['pending_papers'] = array(
						'value' => !empty($user_completion_val[$active_user_id]['pending']) ? $user_completion_val[$active_user_id]['pending'] : 0,
						'title' => 'Pending',
						'url' => 'relis/manager/qa_conduct_list_val/mine/0/pending'
				);
				$data['qa_completion_val']['done_papers']    = array(
						'value' => !empty($user_completion_val[$active_user_id]['done']) ? $user_completion_val[$active_user_id]['done'] : 0,
						'title' => 'Done',
						'url' => 'relis/manager/qa_conduct_list_val/mine/0/done'
				);
				$data['qa_completion_val']['gauge_all']      = $user_completion_val[$active_user_id]['all'];
				$data['qa_completion_val']['gauge_done']     = !empty($user_completion_val[$active_user_id]['done']) ? $user_completion_val[$active_user_id]['done'] : 0;
			}
			if (!empty($general_completion_val['all'])) {
				$data['gen_qa_completion_val']['title']          = "Overall validation completion";
				$data['gen_qa_completion_val']['all_papers']     = array(
						'value' => $general_completion_val['all'],
						'title' => 'All',
						'url' => 'relis/manager/qa_conduct_list_val/all'
				);
				$data['gen_qa_completion_val']['pending_papers'] = array(
						'value' => !empty($general_completion_val['pending']) ? $general_completion_val['pending'] : 0,
						'title' => 'Pending',
						'url' => 'relis/manager/qa_conduct_list_val/all/0/pending'
				);
				$data['gen_qa_completion_val']['done_papers']    = array(
						'value' => !empty($general_completion_val['done']) ? $general_completion_val['done'] : 0,
						'title' => 'Done',
						'url' => 'relis/manager/qa_conduct_list_val/all/0/done'
				);
				$data['gen_qa_completion_val']['gauge_all']      = $general_completion_val['all'];
				$data['gen_qa_completion_val']['gauge_done']     = !empty($general_completion_val['done']) ? $general_completion_val['done'] : 0;
			}
			$action_but = array();
			if (can_manage_project() AND !$project_published)
				$action_but['assign_screen'] = get_top_button('all', 'Assign papers for QA', 'relis/manager/qa_assignment_set', 'Assign papers', 'fa-mail-forward', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
				if (can_review_project() AND !$project_published)
					$action_but['screen'] = get_top_button('all', 'Classify', 'relis/manager/qa_conduct_list/mine/0/pending', 'Assess', 'fa-search', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
					$action_but['screen_completion'] = get_top_button('all', 'Result', 'relis/manager/qa_conduct_result', 'Result', 'fa-th', '', ' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ', False);
					$data['action_but_screen']       = $action_but;
					$action_but                      = array();
					if (get_appconfig_element('qa_validation_on')) {
						if (can_validate_project() AND !$project_published) {
							$action_but['assign_screen'] = get_top_button('all', 'Assign papers for validation', 'relis/manager/qa_assignment_validation_set', 'Assign papers', 'fa-mail-forward', '', ' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ', False);
							$action_but['screen']        = get_top_button('all', 'Validate', 'relis/manager/qa_conduct_list_val/mine/0/pending', 'Validate', 'fa-check-square-o', '', ' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ', False);
						}
						$action_but['screen_completion'] = get_top_button('all', 'Result', 'op/entity_list/list_qa_validation', 'Result', 'fa-th', '', ' btn-primary action_butt  col-md-3 col-sm-3 col-xs-12', False);
						$data['action_but_validate']     = $action_but;
					}
					if (!($this->session->userdata('project_db'))) {
						redirect('manager/projects_list');
					}
					if ($this->session->userdata('working_perspective') == 'class') {
						redirect('home');
					}
					$data['configuration'] = get_project_config($this->session->userdata('project_db'));
					$data['page']          = 'relis/h_qa';
					$this->load->view('body', $data);
		}
		function get_user_completion($user_id, $screening_phase, $phase_type = 'Screening')
		{
			$my_assignations = $this->Relis_mdl->get_user_assigned_papers($user_id, $phase_type, $screening_phase);
			$total_papers    = count($my_assignations);
			$papers_screened = 0;
			$conflicts       = 0;
			foreach ($my_assignations as $key => $value) {
				if ($value['screening_status'] == 'Done') {
					$papers_screened++;
					if ($value['paper_status'] == 'In conflict') {
						$conflicts++;
					}
				}
			}
			$result['all_papers']         = $total_papers;
			$result['papers_done']        = $papers_screened;
			$result['papers_in_conflict'] = $conflicts;
			return $result;
		}
		public function screening_select()
		{
			$project_published = project_published();
			$screening_phases  = $this->db_current->order_by('screen_phase_order', 'ASC')->get_where('screen_phase', array(
					'screen_phase_active' => 1
			))->result_array();
			$this->session->set_userdata('working_perspective', 'screen');
			$phases_list = array();
			$yes_no      = array(
					'0' => '',
					'1' => 'Yes'
			);
			$i           = 1;
			if (get_appconfig_element('screening_on')) {
				foreach ($screening_phases as $k => $phase) {
					$select_but      = "";
					$open_but        = "";
					$close_but       = "";
					$user_completion = $this->get_user_completion(active_user_id(), $phase['screen_phase_id'], '');
					if (!empty($user_completion['all_papers'])) {
						$user_perc = !empty($user_completion['all_papers']) ? round((($user_completion['papers_done'] - $user_completion['papers_in_conflict']) * 100 / $user_completion['all_papers']), 2) . " %" : '-';
					} else {
						$user_perc = "-";
					}
					$user_completion = $this->get_user_completion(0, $phase['screen_phase_id'], '');
					if (!empty($user_completion['all_papers'])) {
						$gen_perc = !empty($user_completion['all_papers']) ? round((($user_completion['papers_done'] - $user_completion['papers_in_conflict']) * 100 / $user_completion['all_papers']), 2) . " %" : '-';
					} else {
						$gen_perc = "-";
					}
					$all_papers       = $this->DBConnection_mdl->count_papers('all');
					$processed_papers = $this->DBConnection_mdl->count_papers('processed');
					if (!empty($all_papers)) {
						$class_perc = !empty($all_papers) ? round(($processed_papers * 100 / $all_papers), 2) . " %" : '-';
					} else {
						$class_perc = "-";
					}
					if ($phase['phase_state'] == 'Open') {
						$select_but = get_top_button('all', 'Go to the phase', 'home/select_screen_phase/' . $phase['screen_phase_id'], 'Go to', 'fa-send', '', ' btn-info ', False);
						$close_but  = get_top_button('all', 'Lock the phase', 'home/screening_phase_manage/' . $phase['screen_phase_id'] . '/2', 'Close', 'fa-lock', '', ' btn-danger ', False);
					} else {
						$open_but = get_top_button('all', 'Unlock the phase', 'home/screening_phase_manage/' . $phase['screen_phase_id'], 'Open', 'fa-unlock', '', ' btn-success ', False);
					}
					if (!can_manage_project() OR $project_published) {
						$close_but = "";
						$open_but  = "";
					}
					$temp = array(
							'Title' => "Screening : " . $phase['phase_title'],
							'State' => $phase['phase_state'],
							'User_completion' => $user_perc,
							'Gen_completion' => $gen_perc,
							'action' => $open_but . $close_but . $select_but
					);
					array_push($phases_list, $temp);
					$i++;
				}
			}
			if (get_appconfig_element('qa_on')) {
				$active_user_id     = active_user_id();
				$completion         = $this->manager_lib->get_qa_completion('QA');
				$general_completion = $completion['general_completion'];
				$user_completion    = $completion['user_completion'];
				if (!empty($general_completion['all'])) {
					$done        = (!empty($general_completion['done'])) ? $general_completion['done'] : 0;
					$gen_qa_perc = !empty($general_completion['all']) ? round(($done * 100 / $general_completion['all']), 2) . " %" : '-';
				} else {
					$gen_qa_perc = "-";
				}
				if (!empty($user_completion[$active_user_id]['all'])) {
					$done        = (!empty($user_completion[$active_user_id]['done'])) ? $user_completion[$active_user_id]['done'] : 0;
					$usr_qa_perc = !empty($user_completion[$active_user_id]['all']) ? round(($done * 100 / $user_completion[$active_user_id]['all']), 2) . " %" : '-';
				} else {
					$usr_qa_perc = "-";
				}
				$select_but = "";
				$open_but   = "";
				$close_but  = "";
				if (get_appconfig_element('qa_open')) {
					$select_but = get_top_button('all', 'Go to QA', 'manager/set_perspective/qa', 'Go to', 'fa-send', '', ' btn-info ', False);
					$close_but  = get_top_button('all', 'Lock the phase', 'manager/activate_qa/0', 'Close', 'fa-lock', '', ' btn-danger ', False);
					$qa_state   = "Open";
				} else {
					$open_but = get_top_button('all', 'Unlock the phase', 'manager/activate_qa', 'Open', 'fa-unlock', '', ' btn-success ', False);
					$qa_state = "Closed";
				}
				if (!can_manage_project() OR $project_published) {
					$close_but = "";
					$open_but  = "";
				}
				$qa = array(
						'Title' => 'Quality assessment',
						'State' => $qa_state,
						'User_completion' => $usr_qa_perc,
						'Gen_completion' => $gen_qa_perc,
						'action' => $open_but . $close_but . $select_but
				);
				array_push($phases_list, $qa);
				$i++;
			}
			$all_papers       = $this->DBConnection_mdl->count_papers('all');
			$processed_papers = $this->DBConnection_mdl->count_papers('processed');
			if (!empty($all_papers)) {
				$class_perc = !empty($all_papers) ? round(($processed_papers * 100 / $all_papers), 2) . " %" : '-';
			} else {
				$class_perc = "-";
			}
			$my_class_completion = $this->manager_lib->get_classification_completion('class', '');
			if (!empty($my_class_completion['all_papers'])) {
				$class_perc_mine = !empty($my_class_completion['all_papers']) ? round(($my_class_completion['processed_papers'] * 100 / $my_class_completion['all_papers']), 2) . " %" : '-';
			} else {
				$class_perc_mine = "-";
			}
			$select_but = "";
			$open_but   = "";
			$close_but  = "";
			if (get_appconfig_element('classification_on')) {
				$select_but  = get_top_button('all', 'Go to classification', 'manager/set_perspective/class', 'Go to', 'fa-send', '', ' btn-info ', False);
				$close_but   = get_top_button('all', 'Lock the phase', 'manager/activate_classification/0', 'Close', 'fa-lock', '', ' btn-danger ', False);
				$class_state = "Open";
			} else {
				$open_but    = get_top_button('all', 'Unlock the phase', 'manager/activate_classification', 'Open', 'fa-unlock', '', ' btn-success ', False);
				$class_state = "Closed";
			}
			if (!can_manage_project() OR $project_published) {
				$close_but = "";
				$open_but  = "";
			}
			$class = array(
					'Title' => 'Classification',
					'State' => $class_state,
					'User_completion' => $class_perc_mine,
					'Gen_completion' => $class_perc,
					'action' => $open_but . $close_but . $select_but
			);
			array_push($phases_list, $class);
			if (!empty($phases_list)) {
				array_unshift($phases_list, array(
						lng('Phases'),
						lng('State'),
						lng('My completion'),
						lng('Overall  completion')
				));
			}
			$data['phases_list']   = $phases_list;
			$data['configuration'] = get_project_config($this->session->userdata('project_db'));
			$data['users']         = $this->DBConnection_mdl->get_users_all();
			foreach ($data['users'] as $key => $value) {
				if (!(user_project($this->session->userdata('project_id'), $value['user_id'])) OR $value['user_usergroup'] == 1) {
					unset($data['users'][$key]);
				} else {
					$data['users'][$key]['usergroup_name'] = get_user_role($data['users'][$key]['user_id']);
				}
			}
			$data['top_buttons'] = "";
			if (has_user_role('Project admin') OR has_usergroup(1)) {
				if (project_published()) {
					$publish_but = get_top_button('all', 'Reopen project', 'manager/publish_project/0/0', 'Reopen project', ' fa-folder-open ', '', ' btn-warning ', False);
				} else {
					$publish_but = get_top_button('all', 'Publish project', 'manager/publish_project', 'Publish project', 'fa-send', '', ' btn-info ', False);
				}
				$data['top_buttons'] = $publish_but;
			}
			$this->session->set_userdata('current_screen_phase', '');
			$data['page'] = 'relis/h_screening_select';
			$this->load->view('body', $data);
		}
		public function select_screen_phase($screen_phase_id)
		{
			if (!empty($screen_phase_id)) {
				$this->session->set_userdata('current_screen_phase', $screen_phase_id);
				redirect('home/screening');
			} else {
				redirect('home/screening_select');
			}
		}
		public function screening_phase_manage($screen_phase_id, $op = 1)
		{
			if ($op == 1) {
				$State = 'Open';
			} else {
				$State = 'Closed';
			}
			$res = $this->db_current->update('screen_phase', array(
					'phase_state' => $State
			), array(
					'screen_phase_id' => $screen_phase_id
			));
			redirect('home/screening_select');
		}
		
		public function set_project($projet_label, $project_id = 0, $project_title = "")
		{
			if (!empty($projet_label)) {
				$this->session->set_userdata('project_db', $projet_label);
				$this->session->set_userdata('project_id', $project_id);
				$this->session->set_userdata('project_title', urldecode(urldecode($project_title)));
			}
			redirect('home/screening');
		}
		public function change_lang()
		{
			if ($this->session->userdata('active_language') AND $this->session->userdata('active_language') == 'fr') {
				$this->session->set_userdata('active_language', 'en');
			} else {
				$this->session->set_userdata('active_language', 'fr');
			}
		}
		public function update_stored_procedure($config = "all")
		{
			if ($config == 'all') {
				$configs   = array(
						'author',
						'venue',
						'users',
						'usergroup',
						'papers',
						'classification',
						'exclusion',
						'assignation',
						'paper_author',
						'logs',
						'str_mng',
						'config',
						'user_project'
				);
				$reftables = $this->DBConnection_mdl->get_reference_tables_list();
				foreach ($reftables as $key => $value) {
					array_push($configs, $value['reftab_label']);
				}
			} else {
				$configs = array(
						$config
				);
			}
			print_test($configs);
			foreach ($configs as $k => $config) {
				$this->manage_stored_procedure_lib->create_stored_procedure_get($config);
				if ($config == 'papers')
					$this->manage_stored_procedure_lib->create_stored_procedure_count($config);
					$this->manage_stored_procedure_lib->create_stored_procedure_remove($config);
					$this->manage_stored_procedure_lib->create_stored_procedure_add($config);
					$this->manage_stored_procedure_lib->create_stored_procedure_update($config);
					$this->manage_stored_procedure_lib->create_stored_procedure_detail($config);
			}
		}
		public function create_table_config($config, $target_db = 'current')
		{
			$res = $this->manage_stored_procedure_lib->create_table_config(get_table_config($config), $target_db);
			echo $res;
		}
		
		
		public function sql_query($query_type = "single")
		{
			$data['query_type'] = $query_type;
			if ($query_type != 'multi') {
				$data['top_buttons'] = get_top_button('all', 'Switch to multi query!', 'home/sql_query/multi', 'Switch to multi query!', ' fa-exchange', '', ' btn-info ');
				$data['title']       = 'Query database - single SQL query';
			} else {
				$data['top_buttons'] = get_top_button('all', 'Switch to single query!', 'home/sql_query/', 'Switch to single query!', ' fa-exchange', '', ' btn-info ');
				$data['title']       = lng_min('Query database - multiple SQL queries');
			}
			$data['page'] = 'sql';
			$this->load->view('body', $data);
		}
		public function sql_query_response()
		{
			$post_arr   = $this->input->post();
			$sql        = "";
			$sql        = $post_arr['sql_field'];
			$query_type = $post_arr['query_type'];
			if (isset($post_arr['return_table'])) {
				$return_table = 1;
			} else {
				$return_table = 0;
			}
			$data['query_type'] = $query_type;
			if (!empty($sql)) {
				$data['sql_field']    = $sql;
				$data['return_table'] = $return_table;
				if ($query_type != 'multi') {
					$res = $this->manage_mdl->run_query($sql, $return_table);
				} else {
					$delimiter       = $post_arr['delimiter'];
					$T_queries       = explode(!empty($delimiter) ? $delimiter : ';', $sql);
					$error           = 0;
					$all             = 0;
					$t_error_message = " ";
					foreach ($T_queries as $key => $v_sql) {
						$v_sql = trim($v_sql);
						if (!empty($v_sql)) {
							$T_res = $this->manage_mdl->run_query($v_sql);
							if ($T_res['code'] != 0) {
								$error++;
								$t_error_message .= " <br/> - " . $T_res['message'];
							}
							$all++;
						}
					}
					if ($error == 0) {
						$res['code']    = 0;
						$res['message'] = $all . ' query executed!';
					} else {
						$res['code']    = 1;
						$res['message'] = ($all - $error) . " Succeded - $error Errors<br/>" . $t_error_message;
					}
				}
			} else {
				$res['code']    = 1;
				$res['message'] = lng_min('Query was empty');
			}
			if ($res['code'] == 0) {
				$data['message_success'] = "Success";
				$data['message_error']   = "";
				$array_header            = array();
				if ($return_table) {
					$data['display_list'] = "OK";
					if (!empty($res['message']) AND is_array($res['message']) AND count($res['message']) > 0) {
						foreach ($res['message'][0] as $key => $value) {
							array_push($array_header, $key);
						}
						array_unshift($res['message'], $array_header);
						$data['list'] = $res['message'];
					}
				}
			} else {
				$data['message_error']   = "Error: " . $res['message'];
				$data['message_success'] = "";
			}
			if ($query_type != 'multi') {
				$data['top_buttons'] = get_top_button('all', 'Switch to multi query!', 'home/sql_query/multi', 'Switch to multi query!', ' fa-exchange', '', ' btn-info ');
				$data['title']       = lng_min('Run SQL query');
			} else {
				$data['top_buttons'] = get_top_button('all', 'Switch to single query!', 'home/sql_query/', 'Switch to single query!', ' fa-exchange', '', ' btn-info ');
				$data['title']       = lng_min('Run multiple SQL queries');
			}
			$data['page'] = 'sql';
			$this->load->view('body', $data);
		}
		public function export($type = 1)
		{
			$data['t_type']                = $type;
			$data['page_title']            = lng('Exports');
			$data['top_buttons']           = get_top_button('back', 'Back', 'home');
			$data['left_menu_perspective'] = '';
			$data['project_perspective']   = 'screening';
			$data['page']                  = 'export';
			$this->load->view('body', $data);
		}
		private function screen_mine()
		{
			echo "brice";
			$all_file    = "cside/screen/all.csv";
			$accept_file = "cside/screen/accepted.txt";
			$Taccepeted  = array();
			$Tall        = array();
			ini_set('auto_detect_line_endings', TRUE);
			$fp         = fopen($all_file, 'rb');
			$i          = 1;
			$last_count = 0;
			while ((($Tline = (fgetcsv($fp, 0, ";", '"')))) !== false) {
				$Tline    = array_map("utf8_encode", $Tline);
				$Tall[$i] = $Tline;
				$i++;
				if ($i == 1000)
					exit;
			}
			$fa         = fopen($accept_file, 'rb');
			$i          = 1;
			$last_count = 0;
			while ((($Tline = (fgetcsv($fa, 0, "$", '"')))) !== false) {
				$Tline          = array_map("utf8_encode", $Tline);
				$Taccepeted[$i] = $Tline;
				$i++;
				if ($i == 1000)
					exit;
			}
			$final_added = array();
			$j           = 1;
			foreach ($Taccepeted as $key => $value) {
				$title  = trim($value[0]);
				$result = "not fund";
				foreach ($Tall as $key_all => $value_all) {
					if ($title == trim($value_all[1])) {
						$result          = "found";
						$final_added[$j] = $value_all;
					}
				}
				echo " <h3>$j - $result</h3>";
				$j++;
			}
			print_test($final_added);
			$f_new = fopen("cside/screen/paper_to_classify.csv", 'w+');
			foreach ($final_added as $val) {
				fputcsv($f_new, $val, ";");
			}
			fclose($f_new);
		}
		
		public function get_screen_for_kappa()
		{
			$screening_phase_info = active_screening_phase_info();
			$current_phase        = active_screening_phase();
			$sql                  = "select paper_id,user_id,screening_decision
			FROM screening_paper
			WHERE  assignment_mode='auto' AND  screening_status='done' AND screening_phase = $current_phase AND screening_active=1";
			echo $sql;
			$result       = $this->db_current->query($sql)->result_array();
			$result_kappa = array();
			foreach ($result as $key => $value) {
				if (!isset($result_kappa[$value['paper_id']])) {
					$result_kappa[$value['paper_id']] = array(
							'Included' => 0,
							'Excluded' => 0
					);
				}
				if (!empty($value['screening_decision']) AND ($value['screening_decision'] == 'Included' OR $value['screening_decision'] == 'Excluded')) {
					$result_kappa[$value['paper_id']][$value['screening_decision']] += 1;
				}
			}
			$result_kappa_clean = array();
			foreach ($result_kappa as $k => $v) {
				array_push($result_kappa_clean, array(
						$v['Included'],
						$v['Excluded']
				));
			}
			return $result_kappa_clean;
		}
		
		private function mres_escape($value)
		{
			$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
			$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
		
			return str_replace($search, $replace, $value);
		}
		
		public function update_edition_mode($value = "no")
		{
			$this->session->set_userdata('language_edit_mode', $value);
			redirect('op/entity_list/list_str_mng');
		}
	}
