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

class Screening extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    //handle the logic and data preparation for the screening page, including progress tracking, action button generation, and project configuration retrieval.
    public function screening()
    {
        $project_published = project_published();
        //	
        //update_paper_status_all();
        //$this->session->set_userdata('working_perspective','screen');
        if (!($this->session->userdata('project_db'))) {
            redirect('project/projects_list');
        }
        if ($this->session->userdata('working_perspective') == 'class') {
            redirect('home');
        }
        if (!active_screening_phase()) {
            redirect('screening/screening_select');
        }
        //$this->calculate_kappa();
        $data['screening_phase_info'] = active_screening_phase_info();
        //print_test($screening_phase_info);
        /*
         * Recuperation du nombre de papiers par catégorie
         */
        //	$screening_completion=$this->get_user_completion(active_user_id(),active_screening_phase(),'Screening');
        $screening_completion = $this->get_user_completion(active_user_id(), active_screening_phase(), 'Screening');
        //print_test($screening_completion);
        //print_test($screening_completion);
        if (!empty($screening_completion['all_papers'])) {
            $data['screening_completion']['title'] = "My screening progress";
            $data['screening_completion']['all_papers'] = array(
                'value' => $screening_completion['all_papers'],
                'title' => 'All',
                'url' => 'element/entity_list/list_my_assignments'
            );
            $data['screening_completion']['pending_papers'] = array(
                'value' => $screening_completion['all_papers'] - $screening_completion['papers_done'],
                'title' => 'Pending',
                'url' => 'element/entity_list/list_my_pending_screenings'
            );
            $data['screening_completion']['done_papers'] = array(
                'value' => $screening_completion['papers_done'],
                'title' => 'Screened',
                'url' => 'element/entity_list/list_my_screenings'
            );
            $data['screening_completion']['conflict_papers'] = array(
                'value' => $screening_completion['papers_in_conflict'],
                'title' => 'Conflicts',
                //'url'=>'element/entity_list/list_papers_screen_conflict'
                'url' => 'element/entity_list/list_papers_screen_my_conflict'
            );
            $data['screening_completion']['gauge_all'] = $screening_completion['all_papers'];
            $data['screening_completion']['gauge_done'] = $screening_completion['papers_done'] - $screening_completion['papers_in_conflict'];
        }
        //general screening completion
        $general_screening_completion = $this->get_user_completion(0, active_screening_phase(), 'Screening');
        //print_test($general_screening_completion);
        if (!empty($general_screening_completion['all_papers'])) {
            $data['general_screening_completion']['title'] = "Overall screening assignment  progress";
            $data['general_screening_completion']['all_papers'] = array(
                'value' => $general_screening_completion['all_papers'],
                'title' => 'All',
                'url' => 'element/entity_list/list_assignments'
            );
            $data['general_screening_completion']['pending_papers'] = array(
                'value' => $general_screening_completion['all_papers'] - $general_screening_completion['papers_done'],
                'title' => 'Pending',
                'url' => 'element/entity_list/list_all_pending_screenings'
            );
            $data['general_screening_completion']['done_papers'] = array(
                'value' => $general_screening_completion['papers_done'],
                'title' => 'Screened',
                'url' => 'element/entity_list/list_screenings'
            );
            $data['general_screening_completion']['conflict_papers'] = array(
                'value' => $general_screening_completion['papers_in_conflict'],
                'title' => 'Conflicts',
                'url' => 'element/entity_list/list_papers_screen_conflict'
            );
            $data['general_screening_completion']['gauge_all'] = $general_screening_completion['all_papers'];
            $data['general_screening_completion']['gauge_done'] = $general_screening_completion['papers_done'] - $general_screening_completion['papers_in_conflict'];
            //	$data['general_screening_completion']['gauge_done']=0;
        }
        if (get_appconfig_element('screening_validation_on')) {
            $validation_completion = $this->get_user_completion(active_user_id(), active_screening_phase(), 'screen_validation');
            $general_validation_completion = $this->get_user_completion(0, active_screening_phase(), 'screen_validation');
        }
        //print_test($validation_completion);
        if (!empty($validation_completion['all_papers'])) {
            $data['validation_completion']['title'] = "My validations progress";
            $data['validation_completion']['all_papers'] = array(
                'value' => $validation_completion['all_papers'],
                'title' => 'All',
                'url' => 'element/entity_list/list_my_validations_assignment'
            );
            $data['validation_completion']['pending_papers'] = array(
                'value' => $validation_completion['all_papers'] - $validation_completion['papers_done'],
                'title' => 'Pending',
                'url' => 'element/entity_list/list_my_pending_validation'
            );
            $data['validation_completion']['done_papers'] = array(
                'value' => $validation_completion['papers_done'],
                'title' => 'Validated',
                'url' => 'element/entity_list/list_my_done_validation'
            );
            $data['validation_completion']['gauge_all'] = $validation_completion['all_papers'];
            $data['validation_completion']['gauge_done'] = $validation_completion['papers_done'];
        }
        ////general screening validation completion
        //print_test($validation_completion);
        if (!empty($general_validation_completion['all_papers'])) {
            $data['general_validation_completion']['title'] = "Overall validations progress";
            $data['general_validation_completion']['all_papers'] = array(
                'value' => $general_validation_completion['all_papers'],
                'title' => 'All',
                'url' => 'element/entity_list/list_assignments_validation'
            );
            $data['general_validation_completion']['pending_papers'] = array(
                'value' => $general_validation_completion['all_papers'] - $general_validation_completion['papers_done'],
                'title' => 'Pending',
                'url' => 'element/entity_list/list_pending_screenings_validation'
            );
            $data['general_validation_completion']['done_papers'] = array(
                'value' => $general_validation_completion['papers_done'],
                'title' => 'Validated',
                'url' => 'element/entity_list/list_screenings_validation'
            );
            $data['general_validation_completion']['gauge_all'] = $general_validation_completion['all_papers'];
            $data['general_validation_completion']['gauge_done'] = $general_validation_completion['papers_done'];
        }
        //print_test($data);
        //$shortut operations
        $action_but = array();
        if (can_manage_project() and !$project_published)
            $action_but['assign_screen'] = get_top_button('all', 'Assign papers for screening', 'screening/assignment_screen', 'Assign papers', 'fa-mail-forward', '', ' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ', False);
        if (can_review_project() and !$project_published)
            $action_but['screen'] = get_top_button('all', 'Screen papers', 'screening/screen_paper', 'Screen', 'fa-search', '', ' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ', False);
        if (can_manage_project() or get_appconfig_element('screening_result_on')) {
            $action_but['screen_result'] = get_top_button('all', 'Screening progress', 'screening/screen_completion', 'Progress', 'fa-tasks', '', ' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ', False);
            $action_but['screen_completion'] = get_top_button('all', 'Screening Statistics', 'screening/screen_result', 'Statistics', 'fa-th', '', ' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ', False);
        }
        $data['action_but_screen'] = $action_but;
        $action_but = array();
        if (get_appconfig_element('screening_validation_on')) {
            if (can_validate_project() and !$project_published) {
                $action_but['assign_screen'] = get_top_button('all', 'Assign papers for validation', 'screening/validate_screen_set', 'Assign papers', 'fa-mail-forward', '', ' btn-primary action_butt col-md-2 col-sm-2 col-xs-12 ', False);
                $action_but['screen'] = get_top_button('all', 'Validate screening', 'screening/screen_paper_validation', 'Validate', 'fa-check-square-o', '', ' btn-primary action_butt col-md-2 col-sm-2 col-xs-12 ', False);
            }
            $action_but['screen_result'] = get_top_button('all', 'Validation progress', 'screening/screen_completion/validate', 'Progress', 'fa-tasks', '', ' btn-primary action_butt col-md-2 col-sm-2 col-xs-12 ', False);
            $action_but['screen_completion'] = get_top_button('all', 'Validation Statistics', 'screening/screen_validation_result', 'Statistics', 'fa-th', '', ' btn-primary action_butt  col-md-2 col-sm-2 col-xs-12', False);
            $data['action_but_validate'] = $action_but;
        }
        //	print_test($action_but);
        $data['configuration'] = get_project_config($this->session->userdata('project_db'));
        /*
         * Récuperation des participants dans l'application
         */
        /*
         * Chargement de la vue qui va s'afficher
         *
         */
        $data['page'] = 'screening/h_screening';
        $this->load->view('shared/body', $data);
    }

    // Generate a list of screening phases and their completion statues in the Home page, as well as quality assessment and classification phases.
    public function screening_select()
    {
        $project_published = project_published();
        //debug_comment_diaplay();
        $screening_phases = $this->db_current->order_by('screen_phase_order', 'ASC')
            ->get_where('screen_phase', array('screen_phase_active' => 1))
            ->result_array();
        $this->session->set_userdata('working_perspective', 'screen');
        $phases_list = array();
        $yes_no = array('0' => '', '1' => 'Yes');
        $i = 1;
        if (get_appconfig_element('screening_on')) {
            foreach ($screening_phases as $k => $phase) {
                //print_test($phase);
                //	print_test($phase);
                $select_but = "";
                $open_but = "";
                $close_but = "";
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
                //classification completion
                $all_papers = $this->Paper_dataAccess->count_papers('all');
                $processed_papers = $this->Paper_dataAccess->count_papers('processed');
                if (!empty($all_papers)) {
                    $class_perc = !empty($all_papers) ? round(($processed_papers * 100 / $all_papers), 2) . " %" : '-';
                } else {
                    $class_perc = "-";
                }
                if ($phase['phase_state'] == 'Open') {
                    $select_but = get_top_button('all', 'Go to the phase', 'screening/select_screen_phase/' . $phase['screen_phase_id'], 'Go to', 'fa-send', '', ' btn-info ', False);
                    $close_but = get_top_button('all', 'Lock the phase', 'screening/screening_phase_manage/' . $phase['screen_phase_id'] . '/2', 'Close', 'fa-lock', '', ' btn-danger ', False);
                } else {
                    $open_but = get_top_button('all', 'Unlock the phase', 'screening/screening_phase_manage/' . $phase['screen_phase_id'], 'Open', 'fa-unlock', '', ' btn-success ', False);
                }
                if (!can_manage_project() or $project_published) {
                    $close_but = "";
                    $open_but = "";
                }
                $temp = array(
                    //		'num'=>$i,
                    'Title' => "Screening : " . $phase['phase_title'],
                    'State' => $phase['phase_state'],
                    //	'Final phase'=>$yes_no[$phase['screen_phase_final']],
                    'User_completion' => $user_perc,
                    'Gen_completion' => $gen_perc,
                    'action' => $open_but . $close_but . $select_but,
                );
                array_push($phases_list, $temp);
                $i++;
            }
        }
        //quality assessment
        if (get_appconfig_element('qa_on')) {
            $active_user_id = active_user_id();
            $completion = $this->manager_lib->get_qa_completion('QA');
            //print_test($completion);
            $general_completion = $completion['general_completion'];
            $user_completion = $completion['user_completion'];
            if (!empty($general_completion['all'])) {
                $done = (!empty($general_completion['done'])) ? $general_completion['done'] : 0;
                $gen_qa_perc = !empty($general_completion['all']) ? round(($done * 100 / $general_completion['all']), 2) . " %" : '-';
            } else {
                $gen_qa_perc = "-";
            }
            if (!empty($user_completion[$active_user_id]['all'])) {
                $done = (!empty($user_completion[$active_user_id]['done'])) ? $user_completion[$active_user_id]['done'] : 0;
                $usr_qa_perc = !empty($user_completion[$active_user_id]['all']) ? round(($done * 100 / $user_completion[$active_user_id]['all']), 2) . " %" : '-';
            } else {
                $usr_qa_perc = "-";
            }
            $select_but = "";
            $open_but = "";
            $close_but = "";
            if (get_appconfig_element('qa_open')) {
                $select_but = get_top_button('all', 'Go to QA', 'op/set_perspective/qa', 'Go to', 'fa-send', '', ' btn-info ', False);
                $close_but = get_top_button('all', 'Lock the phase', 'quality_assessment/activate_qa/0', 'Close', 'fa-lock', '', ' btn-danger ', False);
                $qa_state = "Open";
            } else {
                $open_but = get_top_button('all', 'Unlock the phase', 'quality_assessment/activate_qa', 'Open', 'fa-unlock', '', ' btn-success ', False);
                $qa_state = "Closed";
            }
            if (!can_manage_project() or $project_published) {
                $close_but = "";
                $open_but = "";
            }
            $qa = array(
                //		'num'=>$i,
                'Title' => 'Quality assessment',
                'State' => $qa_state,
                //'Final phase'=>'',
                'User_completion' => $usr_qa_perc,
                'Gen_completion' => $gen_qa_perc,
                'action' => $open_but . $close_but . $select_but,
            );
            array_push($phases_list, $qa);
            $i++;
        }
        //classification completion
        $all_papers = $this->Paper_dataAccess->count_papers('all');
        $processed_papers = $this->Paper_dataAccess->count_papers('processed');
        if (!empty($all_papers)) {
            $class_perc = !empty($all_papers) ? round(($processed_papers * 100 / $all_papers), 2) . " %" : '-';
        } else {
            $class_perc = "-";
        }
        $my_class_completion = $this->get_classification_completion('class', '');
        if (!empty($my_class_completion['all_papers'])) {
            $class_perc_mine = !empty($my_class_completion['all_papers']) ? round(($my_class_completion['processed_papers'] * 100 / $my_class_completion['all_papers']), 2) . " %" : '-';
        } else {
            $class_perc_mine = "-";
        }
        //add clasificsation phase
        $select_but = "";
        $open_but = "";
        $close_but = "";
        if (get_appconfig_element('classification_on')) {
            $select_but = get_top_button('all', 'Go to classification', 'op/set_perspective/class', 'Go to', 'fa-send', '', ' btn-info ', False);
            $close_but = get_top_button('all', 'Lock the phase', 'data_extraction/activate_classification/0', 'Close', 'fa-lock', '', ' btn-danger ', False);
            $class_state = "Open";
        } else {
            $open_but = get_top_button('all', 'Unlock the phase', 'data_extraction/activate_classification', 'Open', 'fa-unlock', '', ' btn-success ', False);
            $class_state = "Closed";
        }
        if (!can_manage_project() or $project_published) {
            $close_but = "";
            $open_but = "";
        }
        $class = array(
            //	'num'=>$i,
            'Title' => 'Classification',
            'State' => $class_state,
            //'Final phase'=>'',
            'User_completion' => $class_perc_mine,
            'Gen_completion' => $class_perc,
            'action' => $open_but . $close_but . $select_but,
        );
        array_push($phases_list, $class);
        if (!empty($phases_list)) {
            //	array_unshift($phases_list, array('#','Title','State','Screening final phase','My completion','General completion'));
            array_unshift($phases_list, array(lng('Phases'), lng('State'), lng('My completion'), lng('Overall  completion')));
        }
        //	print_test($phases_list);
        $data['phases_list'] = $phases_list;
        $data['configuration'] = get_project_config($this->session->userdata('project_db'));
        /*
         * Récuperation des participants dans l'application
         */
        $data['users'] = $this->User_dataAccess->get_users_all();
        foreach ($data['users'] as $key => $value) {
            if (!(user_project($this->session->userdata('project_id'), $value['user_id']))) {
                unset($data['users'][$key]);
            } else {
                $data['users'][$key]['usergroup_name'] = get_user_role($data['users'][$key]['user_id']);
            }
        }
        //print_test($data['users']);
        /*
         * Chargement de la vue qui va s'afficher
         *
         */
        //publish project
        $data['top_buttons'] = "";
        if (has_user_role('Project admin') or has_usergroup(1)) {
            if (project_published()) {
                $publish_but = get_top_button(
                    'all',
                    'Reopen project',
                    'project/publish_project/0/0',
                    'Reopen project',
                    ' fa-folder-open ',
                    '',
                    ' btn-warning ',
                    False
                );
            } else {
                $publish_but = get_top_button(
                    'all',
                    'Publish project',
                    'project/publish_project',
                    'Publish project',
                    'fa-send',
                    '',
                    ' btn-info ',
                    False
                );
            }
            $data['top_buttons'] = $publish_but;
        }
        $this->session->set_userdata('current_screen_phase', '');
        $data['page'] = 'screening/h_screening_select';
        $this->load->view('shared/body', $data);
    }

    /**
     * calculate the completion status of a user's assigned papers in a screening phase. 
     * It provides information about the number of papers assigned, the number of papers that have been screened, 
     * and the number of papers that are in conflict.
     */
    function get_user_completion($user_id, $screening_phase, $phase_type = 'Screening')
    {
        $my_assignations = $this->Screening_dataAccess->get_user_assigned_papers($user_id, $phase_type, $screening_phase);
        $total_papers = count($my_assignations);
        $papers_screened = 0;
        $conflicts = 0;
        foreach ($my_assignations as $key => $value) {
            if ($value['screening_status'] == 'Done') {
                $papers_screened++;
                if ($value['paper_status'] == 'In conflict') {
                    $conflicts++;
                }
            }
        }
        $result['all_papers'] = $total_papers;
        $result['papers_done'] = $papers_screened;
        $result['papers_in_conflict'] = $conflicts;
        return $result;
    }

    //retrieve the completion status of classification papers based on the provided type and user
    private function get_classification_completion($type = 'class', $user = '')
    {
        /*//all
                    
                    if(($user=='all')){
                        if($type=='validation'){
                            $papers_all = $this->db_current->order_by('assigned_id', 'ASC')
                            ->get_where('assigned', array('assigned_active'=>1,'assignment_type'=>'Validation'))
                            ->num_rows();
                            
                            $papers_done = $this->db_current->order_by('assigned_id', 'ASC')
                            ->get_where('view_class_validation_done', array('assigned_active'=>1))
                            ->num_rows();
                        }else{
                            $papers_all=$this->Paper_dataAccess->count_papers('all');
                            $papers_done=$this->Paper_dataAccess->count_papers('processed');
                        }
                        
                    }else{
                        if(empty($user)){
                            $user=active_user_id();
                        }
                        
                        if($type=='validation'){
                            $papers_all = $this->db_current->order_by('assigned_id', 'ASC')
                            ->get_where('assigned', array('assigned_active'=>1,'assignment_type'=>'Validation','assigned_user_id'=>$user))
                            ->num_rows();
                        
                            $papers_done = $this->db_current->order_by('assigned_id', 'ASC')
                            ->get_where('view_class_validation_done', array('assigned_active'=>1,'assigned_user_id'=>$user))
                            ->num_rows();
                        }else{
                            $papers_all = $this->db_current->order_by('assigned_id', 'ASC')
                            ->get_where('assigned', array('assigned_active'=>1,'assignment_type'=>'Classification','assigned_user_id'=>$user))
                            ->num_rows();
                                
                            $papers_done = $this->db_current->order_by('assigned_id', 'ASC')
                            ->get_where('view_class_assignment_done', array('assigned_active'=>1,'assignment_type'=>'Classification','assigned_user_id'=>$user))
                            ->num_rows();
                        }
                    }
                    $res['all_papers']=$papers_all;
                    $res['processed_papers']=$papers_done;
                    $res['pending_papers']=0;
                    if(!empty($res['all_papers']))
                        $res['pending_papers']=$papers_all - $papers_done;
                    */
        $res = $this->manager_lib->get_classification_completion($type, $user);
        return $res;
        //print_test($res);
    }

    /**
     * handle the selection of a screen phase and update the session variable accordingly. 
     * It then redirects the user to the appropriate page based on the selected screen phase
     */
    public function select_screen_phase($screen_phase_id)
    {
        if (!empty($screen_phase_id)) {
            $this->session->set_userdata('current_screen_phase', $screen_phase_id);
            redirect('screening/screening');
        } else {
            redirect('screening/screening_select');
        }
    }

    //manage the state (open or closed) of a specific screening phase 
    public function screening_phase_manage($screen_phase_id, $op = 1)
    {
        if ($op == 1) //open the phase
        {
            $State = 'Open';
        } else {
            $State = 'Closed';
        }
        $res = $this->db_current->update(
            'screen_phase',
            array('phase_state' => $State),
            array(
                'screen_phase_id' => $screen_phase_id
            )
        );
        redirect('screening/screening_select');
    }

    //test fonction used to merge my csv file with the screening result
    private function screen_mine()
    {
        echo "brice";
        $all_file = "cside/screen/all.csv";
        $accept_file = "cside/screen/accepted.txt";
        $Taccepeted = array();
        $Tall = array();
        ini_set('auto_detect_line_endings', TRUE);
        $fp = fopen($all_file, 'rb');
        $i = 1;
        $last_count = 0;
        while ((($Tline = (fgetcsv($fp, 0, ";", '"')))) !== false) {
            $Tline = array_map("utf8_encode", $Tline);
            //print_test($Tline);
            $Tall[$i] = $Tline;
            $i++;
            if ($i == 1000)
                exit;
        }
        //print_test($Tall);
        //exit;
        $fa = fopen($accept_file, 'rb');
        $i = 1;
        $last_count = 0;
        while ((($Tline = (fgetcsv($fa, 0, "$", '"')))) !== false) {
            $Tline = array_map("utf8_encode", $Tline);
            //print_test($Tline);
            $Taccepeted[$i] = $Tline;
            $i++;
            if ($i == 1000)
                exit;
        }
        //echo count($Taccepeted);
        //print_test($Taccepeted);
        $final_added = array();
        //mapping
        $j = 1;
        foreach ($Taccepeted as $key => $value) {
            $title = trim($value[0]);
            $result = "not fund";
            foreach ($Tall as $key_all => $value_all) {
                if ($title == trim($value_all[1])) {
                    $result = "found";
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

    /**
     * This function retrieves screening information for calculating the kappa statistic, 
     * which measures inter-rater agreement between screeners
     */
    public function get_screen_for_kappa()
    {
        $screening_phase_info = active_screening_phase_info();
        $current_phase = active_screening_phase();
        //	print_test($screening_phase_info);

        $result = $this->Screening_dataAccess->select_from_screening_paper($current_phase);

        //	print_test($result);
        $result_kappa = array();
        foreach ($result as $key => $value) {
            if (!isset($result_kappa[$value['paper_id']])) {
                $result_kappa[$value['paper_id']] = array(
                    'Included' => 0,
                    'Excluded' => 0,
                );
            }
            if (!empty($value['screening_decision']) and ($value['screening_decision'] == 'Included' or $value['screening_decision'] == 'Excluded')) {
                $result_kappa[$value['paper_id']][$value['screening_decision']] += 1;
            }
        }
        //print_test($result_kappa);
        $result_kappa_clean = array();
        foreach ($result_kappa as $k => $v) {
            array_push($result_kappa_clean, array($v['Included'], $v['Excluded']));
        }
        //print_test($result_kappa_clean);
        return $result_kappa_clean;
    }

    //prepare and load data for displaying a form or view related to pre-assigning papers for screening.
    public function pre_assignment_screen($data = array())
    {
        $data['screening_phases'] = $this->manager_lib->get_reference_select_values('screen_phase;phase_title', True, False);
        $source_papers = $data['screening_phases'];
        $source_papers[''] = "All papers";
        $data['source_papers'] = $source_papers;
        $data['page_title'] = lng('Assign papers for screening (Step 1)');
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        $data['page'] = 'screening/pre_assign_papers_screen_auto';
        //	print_test($data);
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /**
     * The purpose of this function is to prepare and load data for displaying a form or view related to assigning papers for screening.
     */
    public function assignment_screen($data = array())
    {
        if (!active_screening_phase()) {
            redirect('home');
            exit;
        }
        /*$error=FALSE;
               if(empty($data))
               {
                  if($this->input->post ()){
                  $post_arr = $this->input->post();
                  //print_test($post_arr);
                  if(empty( $post_arr['screening_phase'] )){
                  $data['err_msg'] = lng(' Please provide  "The screening phase" concerned !');
                  $this->pre_assignment_screen($data);
                  $error=True;
                  }else{
                  $data['screening_phase']=$post_arr['screening_phase'];
                  $data['papers_sources']=empty($post_arr['papers_sources'])?'all':$post_arr['papers_sources'];
                  }
                  }else{
                  $data['err_msg'] = lng(' Please fill the form !');
                  $this->pre_assignment_screen($data);
                  $error=True;
                  }
                  $post_arr = $this->input->post ();
                  //print_test($post_arr);
                  }else{
                  if(empty($data['screening_phase']) OR empty($data['papers_sources']) ){
                  $data['err_msg'] = lng(' Please fill the form !');
                  $this->pre_assignment_screen($data);
                  $error=True;
                  }
                  }
                  //
                  */
        $screening_phase_info = active_screening_phase_info();
        $creening_phase_id = active_screening_phase();
        $data['screening_phase'] = $creening_phase_id;
        //$screening phases
        $screening_phases = $this->db_current->order_by('screen_phase_order', 'ASC')
            ->get_where('screen_phase', array('screen_phase_active' => 1))
            ->result_array();
        //$creening_phase_id=8;
        $previous_phase = 0;
        $previous_phase_title = "";
        foreach ($screening_phases as $k => $phase) {
            if ($phase['screen_phase_id'] == $creening_phase_id) {
                break;
            } elseif ($phase['phase_type'] != 'Validation') {
                $previous_phase = $phase['screen_phase_id'];
                $previous_phase_title = $phase['phase_title'];
            }
        }
        if ($previous_phase == 0) {
            $paper_source = 'all';
            $paper_source_status = 'all';
            $previous_phase_title = " ";
        } else {
            $paper_source = $previous_phase;
            $paper_source_status = $screening_phase_info['source_paper_status'];
            $previous_phase_title = " from $previous_phase_title";
        }
        $append_title = "( $paper_source_status papers  $previous_phase_title )";
        //print_test($previous_phase);
        //print_test($screening_phase_info);
        //print_test($screening_phases);
        $data['papers_sources'] = $paper_source;
        $data['paper_source_status'] = $paper_source_status;
        $data['screening_phase'] = $creening_phase_id;
        $papers = $this->get_papers_to_screen($paper_source, $paper_source_status, '', 'Screening');
        $data['paper_source'] = $paper_source;
        $paper_list[0] = array('Key', 'Title');
        foreach ($papers['to_assign'] as $key => $value) {
            $paper_list[$key + 1] = array($value['bibtexKey'], $value['title']);
        }
        $data['paper_list'] = $paper_list;
        $user_table_config = get_table_configuration('users');
        $users = $this->DBConnection_mdl->get_list($user_table_config, '_', 0, -1);
        $_assign_user = array();

        foreach ($users['list'] as $key => $value) {


            if ((user_project($this->session->userdata('project_id'), $value['user_id'])) and !has_user_role('Guest', $value['user_id'])) {
                $_assign_user[$value['user_id']] = $value['user_name'];
            }
        }

        $data['users'] = $_assign_user;
        $data['number_papers'] = count($papers['to_assign']);
        $data['number_papers_assigned'] = count($papers['assigned']);
        $data['reviews_per_paper'] = get_appconfig_element('screening_reviewer_number');
        $data['page_title'] = lng('Assign papers for screening ' . $append_title);
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        //$data['left_menu_perspective']='z_left_menu_screening';
        //$data['project_perspective']='screening';
        $data['page'] = 'screening/assign_papers_screen_auto';
        //	print_test($data);
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /**
     * The purpose of this function is to retrieve and organize papers for screening based on the provided source, 
     * source status, current phase, and assignment role
     */
    function get_papers_to_screen($source = 'all', $source_status = 'all', $current_phase = "", $assignment_role = "")
    {
        //$source_status="Included";
        //$source='1';
        if (empty($current_phase)) {
            $current_phase = active_screening_phase();
        }

        $all_papers = $this->Screening_dataAccess->select_screening_all_papers($source, $source_status);

        $result['all_papers'] = $all_papers;
        // get papers already assigned in current phase
        $condition = "";
        if (!empty($assignment_role)) {
            $condition = " AND assignment_role = '$assignment_role'";
        }

        $paper_assigned = $this->Screening_dataAccess->select_screening_paper($current_phase, $condition);

        //	$result['paper_assigned']=$paper_assigned;
        $det_paper_to_assign = array();
        $det_paper_assigned = array();
        if (empty($paper_assigned)) //no paper already assigned'
        {
            $det_paper_to_assign = $all_papers;
        } else {
            foreach ($all_papers as $key_all => $paper_all) {
                $found = False;
                foreach ($paper_assigned as $key_assigned => $value_assigned) {
                    if ($paper_all['id'] == $value_assigned['paper_id']) {
                        $found = True;
                        array_push($det_paper_assigned, $paper_all);
                        break;
                    }
                }
                if (!$found) {
                    array_push($det_paper_to_assign, $paper_all);
                }
            }
        }
        $result['assigned'] = $det_paper_assigned;
        $result['to_assign'] = $det_paper_to_assign;
        return $result;
    }

    //The purpose of this function is to handle the saving of paper assignments for screening
    function save_assignment_screen()
    {
        $post_arr = $this->input->post();
        //	print_test($post_arr); exit;
        $users = array();
        $i = 1;
        if (empty($post_arr['reviews_per_paper'])) {
            $data['err_msg'] = lng(' Please provide  "Reviews per paper" ');
            $data['screening_phase'] = empty($post_arr['screening_phase']) ? "" : $post_arr['screening_phase'];
            $data['papers_sources'] = empty($post_arr['papers_sources']) ? "" : $post_arr['papers_sources'];
            $this->assignment_screen($data);
        } else {
            // Get selected users
            while ($i <= $post_arr['number_of_users']) {
                if (!empty($post_arr['user_' . $i])) {
                    array_push($users, $post_arr['user_' . $i]);
                }
                $i++;
            }
            //Verify if selected users is > of required reviews per paper
            if (count($users) < $post_arr['reviews_per_paper']) {
                $data['err_msg'] = lng('The Reviews per paper cannot exceed the number of selected users  ');
                $data['screening_phase'] = empty($post_arr['screening_phase']) ? "" : $post_arr['screening_phase'];
                $data['papers_sources'] = empty($post_arr['papers_sources']) ? "" : $post_arr['papers_sources'];
                $this->assignment_screen($data);
            } else {
                $currect_screening_phase = $post_arr['screening_phase'];
                $papers_sources = $post_arr['papers_sources'];
                $paper_source_status = $post_arr['paper_source_status'];
                $reviews_per_paper = $post_arr['reviews_per_paper'];
                //Get all papers
                //	$papers=$this->get_papers_to_screen($papers_sources);
                $papers = $this->get_papers_to_screen($papers_sources, $paper_source_status);
                //	print_test($papers); exit;
                $assign_papers = array();
                $this->db2 = $this->load->database(project_db(), TRUE);
                $operation_code = active_user_id() . "_" . time();
                foreach ($papers['to_assign'] as $key => $value) {
                    $assign_papers[$key]['paper'] = $value['id'];
                    $assign_papers[$key]['users'] = array();
                    $assignment_save = array(
                        'paper_id' => $value['id'],
                        'user_id' => '',
                        'assignment_note' => '',
                        'assignment_type' => 'Normal',
                        'operation_code' => $operation_code,
                        'assignment_mode' => 'auto',
                        'screening_phase' => $currect_screening_phase,
                        'assigned_by' => $this->session->userdata('user_id')
                    );
                    $j = 1;
                    //the table to save assignments
                    $table_name = get_table_configuration('screening', 'current', 'table_name');
                    //print_test($table_name);
                    while ($j <= $reviews_per_paper) {
                        $temp_user = ($key % count($users)) + $j;
                        if ($temp_user >= count($users))
                            $temp_user = $temp_user - count($users);
                        array_push($assign_papers[$key]['users'], $users[$temp_user]);
                        $assignment_save['user_id'] = $users[$temp_user];
                        //print_test($assignment_save);
                        $this->db2->insert($table_name, $assignment_save);
                        $j++;
                    }
                }
                $operation_arr = array(
                    'operation_code' => $operation_code,
                    'operation_type' => 'assign_papers',
                    'user_id' => active_user_id(),
                    'operation_desc' => 'Assign papers for screening'
                );
                $res2 = $this->manage_mdl->add_operation($operation_arr);
                set_top_msg('Assignement done');
                redirect('screening/screening');
                //	print_test($assign_papers);
                //echo count($assign_papers);
            }
        }
    }

    //handle the editing of a screen
    function edit_screen($screen_id, $operation_type = 'edit_screen')
    {
        $data['content_item'] = $this->DBConnection_mdl->get_row_details('get_detail_screen', $screen_id, True);
        $data['operation_source'] = $operation_type;
        //print_test($data); exit;
        $this->screen_paper($operation_type, $data);
    }

    function screen_paper_validation()
    {
        $this->screen_paper('screen_validation');
    }

    //handle the display and management of a paper screening
    function screen_paper($screen_type = 'simple_screen', $data = array())
    {
        $op = check_operation($screen_type, 'Edit');
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        $table_config = get_table_configuration($ref_table);
        //print_test($table_config);
        $data['screen_type'] = $screen_type;
        //Get screening criteria
        $exclusion_crit = $this->manager_lib->get_reference_select_values('exclusioncrieria;ref_value');
        $inclusion_crit = $this->manager_lib->get_reference_select_values('inclusioncriteria;ref_value');
        $data['exclusion_criteria'] = $exclusion_crit;
        $data['inclusion_criteria'] = $inclusion_crit;
        if (!empty($data['content_item'])) {
            //edit screening: used for conflict resolution
            $data['the_paper'] = $data['content_item']['paper_id'];
            $data['screening_id'] = $data['content_item']['screening_id'];
            $data['assignment_id'] = $data['content_item']['screening_id'];
            $data['assignment_note'] = $data['content_item']['assignment_note'];
            $data['screening_phase'] = $data['content_item']['screening_phase'];
            $page_title = "Update screening";
            $data['operation_type'] = 'edit';
        } else {
            $my_assignations = $this->Screening_dataAccess->get_user_assigned_papers(active_user_id(), $screen_type, active_screening_phase());
            //print_test($my_assignations);
            $paper_to_screen = 0;
            $screening_id = 0;
            $total_papers = count($my_assignations);
            if ($total_papers < 1) {
                $page_title = ($screen_type == 'screen_validation') ? "No papers assigned to you for validation" : "No papers assigned to you for screening";
            } else {
                $papers_screened = 0;
                foreach ($my_assignations as $key => $value) {
                    if ($value['screening_status'] == 'Done') {
                        $papers_screened++;
                    } else {
                        if (empty($paper_to_screen)) {
                            $paper_to_screen = $value['paper_id'];
                            $screening_id = $value['screening_id'];
                            $assignment_note = $value['assignment_note'];
                        }
                    }
                }
                if (empty($paper_to_screen)) { //all papers have been screened
                    $page_title = ($screen_type == 'screen_validation') ? "Validation - All papers have been screened" : "All papers have been screened";
                    //	$page_title="All papers have been screened";
                } else {
                    //$page_title=($screen_type=='screen_validation')?"Screening validation":"Screening";
                    $screening_detail = $this->DBConnection_mdl->get_row_details('get_detail_screen', $screening_id, TRUE);
                    $data['screening_phase'] = $screening_detail['screening_phase'];
                    $data['the_paper'] = $paper_to_screen;
                    $data['assignment_id'] = $screening_id;
                    $data['screening_id'] = $screening_id;
                    $data['assignment_note'] = !empty($assignment_note) ? $assignment_note : "";
                    $data['operation_type'] = 'new';
                }
                $data['screen_completion'] = (int) ($papers_screened * 100 / $total_papers);
                $data['paper_screened'] = $papers_screened;
                $data['all_papers'] = $total_papers;
            }
        }
        $screening_phase_info = active_screening_phase_info();
        $displayed_fieds = explode('|', $screening_phase_info['displayed_fields']);
        //print_test($screening_phase_info);
        //print_test($fieds);
        $data['screening_phase_info'] = $screening_phase_info;
        // $search_string = $this->DBConnection_mdl->get_row_details('ref_papers_sources', $data['the_paper']);
        if (!empty($data['the_paper'])) {
            // $this->highlight_search_term($content_item['title'], $search_query);
            $paper_detail = $this->DBConnection_mdl->get_row_details('papers', $data['the_paper']);
            // fetching the search query from the `ref_papers_sources` table
            $search_query = $this->DBConnection_mdl->get_row_details('get_detail_papers_sources', $paper_detail['papers_sources'], TRUE)['ref_search_query'];
            $data['paper_title'] = $paper_detail['bibtexKey'] . " - " . $this->highlight_search_term($paper_detail['title'], $search_query);
            // TODO: Change this screen to highlight the string
            if (in_array('Abstract', $displayed_fieds))
                $data['paper_abstract'] = $this->highlight_search_term($paper_detail['abstract'], $search_query);
            if (in_array('Bibtex', $displayed_fieds))
                $data['paper_bibtex'] = $paper_detail['bibtex'];
            if (in_array('Link', $displayed_fieds))
                $data['paper_link'] = $paper_detail['doi'];
            if (in_array('Preview', $displayed_fieds))
                $data['paper_preview'] = $this->highlight_search_term($paper_detail['preview'], $search_query);
        }
        if (isset($table_config['operations'][$ref_table_operation]['page_title'])) {
            if (!empty($page_title)) {
                $data['page_title'] = $page_title . " - " . $screening_phase_info['phase_title'];
            } else {
                $data['page_title'] = lng($table_config['operations'][$ref_table_operation]['page_title']) . " - " . $screening_phase_info['phase_title'];
            }
        } else {
            $data['page_title'] = lng('Screening');
        }
        $data['top_buttons'] = get_top_button('back', 'Back', 'h_screening');
        $data['page'] = 'screening/screen_paper';
        if (!empty($table_config['operations'][$ref_table_operation]['page_template'])) {
            $data['page'] = $table_config['operations'][$ref_table_operation]['page_template'];
        }
        //setting the page of redirection after saving
        if (!empty($table_config['operations'][$ref_table_operation]['redirect_after_save'])) {
            $after_save_redirect = $table_config['operations'][$ref_table_operation]['redirect_after_save'];
            if (!empty($data['screening_id'])) {
                $after_save_redirect = str_replace('~current_element~', $data['screening_id'], $after_save_redirect);
            }
            if (!empty($data['the_paper'])) {
                $after_save_redirect = str_replace('~current_paper~', $data['the_paper'], $after_save_redirect);
            }
        } else {
            $after_save_redirect = "home";
        }
        $this->session->set_userdata('after_save_redirect', $after_save_redirect);
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    //save the screening decision made for a paper, update the relevant tables, and redirect the user to the appropriate page after saving
    public function save_screening()
    {
        $post_arr = $this->input->post();
        $decision_source = 'new_screen';
        if ($post_arr['screen_type'] == 'edit_screen') {
            $decision_source = 'edit_screen';
        } elseif ($post_arr['screen_type'] == 'resolve_conflict') {
            $decision_source = 'conflict_resolution';
        }
        //print_test($post_arr);
        //exit;
        if (empty($post_arr['criteria_ex']) and $post_arr['decision'] == 'excluded') {
            set_top_msg('Please choose the exclusion criteria', "error");
            if ($post_arr['screen_type'] == 'simple_screen') {
                redirect('screening/screen_paper');
                exit;
            } else {
                redirect('screening/edit_screen/' . $post_arr['screening_id'] . '/' . $post_arr['screen_type']);
                exit;
            }
        } else {
            if (!empty($post_arr['screen_type']) and $post_arr['screen_type'] == 'screen_validation') {
                $screening_table = 'screening_validate';
                $assignment_table = 'assignment_screen_validate';
            } else {
                $screening_table = 'screening';
                $assignment_table = 'assignment_screen';
            }
            $this->db2 = $this->load->database(project_db(), TRUE);
            $screening_phase = !empty($post_arr['screening_phase']) ? $post_arr['screening_phase'] : 1;
            $exclusion_criteria = ($post_arr['decision'] == 'excluded') ? $post_arr['criteria_ex'] : NULL;
            $inclusion_criteria = ($post_arr['decision'] == 'accepted') ? $post_arr['criteria_in'] : NULL;
            $screening_decision = ($post_arr['decision'] == 'excluded') ? 'Excluded' : 'Included';
            $screening_save = array(
                'screening_note' => $post_arr['note'],
                'screening_decision' => $screening_decision,
                'exclusion_criteria' => $exclusion_criteria,
                'inclusion_criteria' => $inclusion_criteria,
                'screening_time' => bm_current_time('Y-m-d H:i:s'),
                'screening_status' => 'Done',
            );
            //print_test($screening_save); exit;
            $res = $this->db2->update('screening_paper', $screening_save, array('screening_id' => $post_arr['screening_id']));
            $screen_phase_detail = $this->DBConnection_mdl->get_row_details('get_screen_phase_detail', $screening_phase, TRUE);
            $screening_phase_last_status = $screen_phase_detail['screen_phase_final'];
            $paper_status = get_paper_screen_status_new($post_arr['paper_id'], $screening_phase);
            $query_screen_decision = $this->db2->get_where('screen_decison', array('paper_id' => $post_arr['paper_id'], 'screening_phase' => $screening_phase, 'decision_active' => 1), 1)->row_array();
            //screen history append
            $Tscreen_history = array(
                'decision_source' => $decision_source,
                'user' => active_user_id(),
                'decision' => $screening_decision,
                'criteria' => $exclusion_criteria,
                'criteria2' => $inclusion_criteria,
                'note' => $post_arr['note'],
                'paper_status' => $paper_status,
                'screening_time' => bm_current_time('Y-m-d H:i:s'),
            );
            $Json_screen_history = json_encode($Tscreen_history);
            if (empty($query_screen_decision)) {
                $this->db2->insert('screen_decison', array('paper_id' => $post_arr['paper_id'], 'screening_phase' => $screening_phase, 'screening_decision' => $paper_status, 'decision_source' => $decision_source, 'decision_history' => $Json_screen_history));
            } else {
                if (!empty($query_screen_decision['decision_history']))
                    $Json_screen_history = $query_screen_decision['decision_history'] . "~~__" . $Json_screen_history;
                if ($query_screen_decision['screening_decision'] != $paper_status) {
                    $this->db2->update('screen_decison', array('screening_decision' => $paper_status, 'decision_source' => $decision_source, 'decision_history' => $Json_screen_history), array('paper_id' => $post_arr['paper_id'], 'screening_phase' => $screening_phase, 'decision_active' => 1));
                } else {
                    $this->db2->update('screen_decison', array('decision_history' => $Json_screen_history), array('paper_id' => $post_arr['paper_id'], 'screening_phase' => $screening_phase, 'decision_active' => 1));
                }
            }
            if ($screening_phase_last_status or $paper_status == 'Excluded') {
                if ($paper_status == 'Included') {
                    $this->db2->update('paper', array('screening_status' => $paper_status, 'classification_status' => 'To classify'), array('id' => $post_arr['paper_id']));
                } else {
                    $paper_status = (($paper_status != 'Included' and $paper_status != 'Excluded') ? 'Pending' : $paper_status);
                    $this->db2->update('paper', array('screening_status' => $paper_status, 'classification_status' => 'Waiting'), array('id' => $post_arr['paper_id']));
                }
            }
        }
        $after_save_redirect = $this->session->userdata('after_save_redirect');
        if (!empty($after_save_redirect)) {
            $this->session->set_userdata('after_save_redirect', '');
            redirect($after_save_redirect);
        } elseif (!(empty($post_arr['operation_type'])) and $post_arr['operation_type'] == 'edit') {
            set_top_msg('Element updated');
            if ($post_arr['operation_source'] == 'display_paper_screen') {
                redirect('screening/display_paper_screen/' . $post_arr['paper_id']);
            } else {
                redirect('screening/list_screen/mine_screen');
            }
        } else {
            set_top_msg('Element saved');
            if (!empty($post_arr['screen_type']) and $post_arr['screen_type'] == 'screen_validation') {
                redirect('screening/screen_paper_validation');
            } else {
                redirect('screening/screen_paper');
            }
        }
    }

    //responsible for removing a screening entry from the database
    public function remove_screening($screen_id)
    {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $config = "screening";
        $screen_detail = $this->DBConnection_mdl->get_row_details($config, $screen_id);
        $this->db2->update('screening', array('screening_active' => 0), array('	screening_id' => $screen_id));
        $this->db2->update('assignment_screen', array('screening_done' => 0), array('assignment_id' => $screen_detail['assignment_id']));
        update_paper_status_status($screen_detail['paper_id']);
        redirect('screening/list_screen/mine_screen');
    }

    //handle the removal of screening validation entries from the database
    public function remove_screening_validation($screen_id)
    {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $config = "screening_validate";
        $screen_detail = $this->DBConnection_mdl->get_row_details($config, $screen_id);
        $this->db2->update('screening_validate', array('screening_active' => 0), array('	screening_id' => $screen_id));
        $this->db2->update('assignment_screen_validate', array('screening_done' => 0), array('assignment_id' => $screen_detail['assignment_id']));
        redirect('screening/list_screen/screen_validation');
    }

    /*
     * Fonction globale pour afficher la liste des élément suivant la structure de la table
     *
     * Input: $ref_table: nom de la configuration d'une page (ex papers, author)
     * 			$val : valeur de recherche si une recherche a été faite sur la table en cours
     * 			$page: la page affiché : ulilisé dans la navigation
     */
    public function list_screen($list_cat = 'mine_screen', $val = "_", $page = 0, $dynamic_table = 1)
    {
        $ref_table = "screening";
        $papers_list = False;
        if ($list_cat == 'assign_validation') {
            $ref_table = "assignment_screen_validate";
        } elseif ($list_cat == 'screen_validation') {
            $ref_table = "screening_validate";
        } elseif ($list_cat == 'mine_screen' or $list_cat == 'all_screen') {
            $ref_table = "screening";
        } elseif ($list_cat == 'mine_assign' or $list_cat == 'all_assign') {
            $ref_table = "assignment_screen";
        } elseif ($list_cat == 'screen_paper' or $list_cat == 'screen_paper_pending' or $list_cat == 'screen_paper_review' or $list_cat == 'screen_paper_included' or $list_cat == 'screen_paper_excluded' or $list_cat == 'screen_paper_conflict') {
            $papers_list = True;
            $ref_table = "papers";
        }
        /*
         * Vérification si il y a une condition de recherche
         */
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
            $url = "screening/list_screen/$list_cat" . "/" . $val . "/0/" . $dynamic_table;
            redirect($url);
        }
        /*
         * Récupération de la configuration(structure) de la table à afficher
         */
        $ref_table_config = get_table_config($ref_table);
        $table_id = $ref_table_config['table_id'];
        /*
         * Appel du model pour récupérer la liste à aficher dans la Base de donnés
         */
        $rec_per_page = ($dynamic_table) ? -1 : 0;
        $extra_condition = "";
        if ($list_cat == 'mine_screen' or $list_cat == 'mine_assign') {
            $extra_condition = " AND ( user_id ='" . active_user_id() . "') ";
        }
        if ($list_cat == 'screen_paper') {
            $data = $this->Paper_dataAccess->get_papers('screen', $ref_table_config, $val, $page, $rec_per_page);
            $page_title = "All papers";
        } elseif ($list_cat == 'screen_paper_pending') {
            //$data=$this->Paper_dataAccess->get_papers('screen',$ref_table_config,$val,$page,$rec_per_page);
            $extra_condition = " AND ( screening_status ='Pending') ";
            $data = $this->manage_mdl->get_list($ref_table_config, $val, $page, $rec_per_page, $extra_condition);
            $page_title = "Pending papers";
        } elseif ($list_cat == 'screen_paper_review') {
            //$data=$this->Paper_dataAccess->get_papers('screen',$ref_table_config,$val,$page,$rec_per_page);
            $extra_condition = " AND ( screening_status ='In review') ";
            $data = $this->manage_mdl->get_list($ref_table_config, $val, $page, $rec_per_page, $extra_condition);
            $page_title = "Papers in review";
        } elseif ($list_cat == 'screen_paper_included') {
            //$data=$this->Paper_dataAccess->get_papers('screen',$ref_table_config,$val,$page,$rec_per_page);
            $extra_condition = " AND ( screening_status ='Included') ";
            $data = $this->manage_mdl->get_list($ref_table_config, $val, $page, $rec_per_page, $extra_condition);
            $page_title = "Papers included";
        } elseif ($list_cat == 'screen_paper_excluded') {
            $extra_condition = " AND ( screening_status ='Excluded') ";
            $data = $this->manage_mdl->get_list($ref_table_config, $val, $page, $rec_per_page, $extra_condition);
            $page_title = "Papers excluded";
        } elseif ($list_cat == 'screen_paper_conflict') {
            $extra_condition = " AND ( screening_status ='In conflict') ";
            $data = $this->manage_mdl->get_list($ref_table_config, $val, $page, $rec_per_page, $extra_condition);
            $page_title = "Papers in conflict";
        } elseif (!empty($extra_condition)) { //pour le string_management une fonction spéciale
            //todo verifier comment le spécifier dans config
            $data = $this->manage_mdl->get_list($ref_table_config, $val, $page, $rec_per_page, $extra_condition);
        } else {
            $data = $this->DBConnection_mdl->get_list($ref_table_config, $val, $page, $rec_per_page);
        }
        //print_test($data);
        /*
         * récupération des correspondances des clés externes pour l'affichage  suivant la structure de la table
         */
        $dropoboxes = array();
        foreach ($ref_table_config['fields'] as $k => $v) {
            if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['on_list'] == 'show') {
                if ($v['input_select_source'] == 'array') {
                    $dropoboxes[$k] = $v['input_select_values'];
                } elseif ($v['input_select_source'] == 'table') {
                    //print_test($v);
                    $dropoboxes[$k] = $this->manager_lib->get_reference_select_values($v['input_select_values']);
                } elseif ($v['input_select_source'] == 'yes_no') {
                    $dropoboxes[$k] = array(
                        '0' => "No",
                        '1' => "Yes"
                    );
                }
            }
        }
        /*
         * Vérification des liens (links) a afficher sur la liste
         */
        $list_links = array();
        $add_link = false;
        $add_link_url = "";
        foreach ($ref_table_config['links'] as $link_type => $link) {
            if (!empty($link['on_list'])) { {
                    $link['type'] = $link_type;
                    if (empty($link['title'])) {
                        $link['title'] = lng_min($link['label']);
                    }
                    $push_link = false;
                    switch ($link_type) {
                        case 'add':
                            $add_link = true; //will appear as a top button
                            if (empty($link['url']))
                                $add_link_url = 'manager/add_element/' . $ref_table;
                            else
                                $add_link_url = $link['url'];
                            break;
                        case 'view':
                            if (!isset($link['icon']))
                                $link['icon'] = 'folder';
                            if (empty($link['url']))
                                $link['url'] = 'manager/display_element/' . $ref_table . '/';
                            $push_link = true;
                            if ($papers_list) {
                                $link['url'] = 'screening/display_paper_screen/';
                            }
                            break;
                        case 'edit':
                            if (!isset($link['icon']))
                                $link['icon'] = 'pencil';
                            if (empty($link['url']))
                                $link['url'] = 'manager/edit_element/' . $ref_table . '/';
                            if ($list_cat == 'mine_assign') {
                                $link['url'] = 'relis/manager/edit_assignment_mine/';
                            } elseif ($list_cat == 'all_assign') {
                                $link['url'] = 'relis/manager/edit_assignment_all/';
                            }
                            $push_link = true;
                            if ($papers_list) //do not put the link on list papers
                                $push_link = false;
                            break;
                        case 'delete':
                            if (!isset($link['icon']))
                                $link['icon'] = 'trash';
                            if (empty($link['url']))
                                $link['url'] = 'manager/delete_element/' . $ref_table . '/';
                            $push_link = true;
                            if ($papers_list) //do not put the link on list papers
                                $push_link = false;
                            break;
                        case 'add_child':
                            if (!isset($link['icon']))
                                $link['icon'] = 'plus';
                            if (!empty($link['url'])) {
                                $link['url'] = 'manager/add_element_child/' . $link['url'] . "/" . $ref_table . "/";
                                $push_link = true;
                            }
                            break;
                        default:
                            break;
                    }
                    if ($push_link)
                        array_push($list_links, $link);
                }
            }
        }
        /*
         * Préparation de la liste à afficher sur base du contenu et  stucture de la table
         */
        /**
         * @var array $field_list va contenir les champs à afficher
         */
        $field_list = array();
        $field_list_header = array();
        foreach ($ref_table_config['fields'] as $k => $v) {
            if ($v['on_list'] == 'show') {
                array_push($field_list, $k);
                array_push($field_list_header, $v['field_title']);
            }
        }
        //print_test($field_list);
        $i = 1;
        $list_to_display = array();
        foreach ($data['list'] as $key => $value) {
            $element_array = array();
            foreach ($field_list as $key_field => $v_field) {
                if (isset($value[$v_field])) {
                    if (isset($dropoboxes[$v_field][$value[$v_field]])) {
                        $element_array[$v_field] = $dropoboxes[$v_field][$value[$v_field]];
                    } else {
                        $element_array[$v_field] = $value[$v_field];
                    }
                } else {
                    $element_array[$v_field] = "";
                    if (isset($ref_table_config['fields'][$v_field]['number_of_values']) and $ref_table_config['fields'][$v_field]['number_of_values'] != 1) {
                        if (isset($ref_table_config['fields'][$v_field]['input_select_values']) and isset($ref_table_config['fields'][$v_field]['input_select_key_field'])) {
                            // récuperations des valeurs de cet element
                            $M_values = $this->manager_lib->get_element_multi_values($ref_table_config['fields'][$v_field]['input_select_values'], $ref_table_config['fields'][$v_field]['input_select_key_field'], $data['list'][$key][$table_id]);
                            $S_values = "";
                            foreach ($M_values as $k_m => $v_m) {
                                if (isset($dropoboxes[$v_field][$v_m])) {
                                    $M_values[$k_m] = $dropoboxes[$v_field][$v_m];
                                }
                                $S_values .= empty($S_values) ? $M_values[$k_m] : " | " . $M_values[$k_m];
                            }
                            $element_array[$v_field] = $S_values;
                        }
                    }
                }
            }
            /*
             * Ajout des liens(links) sur la liste
             */
            $action_button = "";
            $arr_buttons = array();
            foreach ($list_links as $key_l => $value_l) {
                if (!empty($value_l['icon']))
                    $value_l['label'] = icon($value_l['icon']) . ' ' . lng_min($value_l['label']);
                array_push(
                    $arr_buttons,
                    array(
                        'url' => $value_l['url'] . $value[$table_id],
                        'label' => $value_l['label'],
                        'title' => $value_l['title']
                    )
                );
            }
            if ($list_cat == 'screen_paper' or $list_cat == 'screen_paper_pending' or $list_cat == 'screen_paper_review' or $list_cat == 'screen_paper_included' or $list_cat == 'screen_paper_excluded' or $list_cat == 'screen_paper_conflict') {
                $screening_res = get_paper_screen_result($element_array[$table_id]);
                //	print_test($screening_res);
                $element_array['reviews'] = $screening_res['reviewers'];
                $element_array['decision'] = $screening_res['screening_result'];
            }
            $action_button = create_button_link_dropdown($arr_buttons, lng_min('Action'));
            if (!empty($action_button))
                $element_array['links'] = $action_button;
            if (isset($element_array[$table_id])) {
                $element_array[$table_id] = $i + $page;
            }
            array_push($list_to_display, $element_array);
            $i++;
        }
        $data['list'] = $list_to_display;
        /*
         * Ajout de l'entête de la liste
         */
        if (!empty($data['list'])) {
            //$array_header=$ref_table_config['header_list_fields'];
            $array_header = $field_list_header;
            if ($list_cat == 'screen_paper' or $list_cat == 'screen_paper_pending' or $list_cat == 'screen_paper_review' or $list_cat == 'screen_paper_included' or $list_cat == 'screen_paper_excluded' or $list_cat == 'screen_paper_conflict') {
                array_push($array_header, 'Reviewers');
                array_push($array_header, 'Decision');
            }
            if (!empty($data['list'][$key]['links'])) {
                array_push($array_header, '');
            }
            if (!$dynamic_table) {
                array_unshift($data['list'], $array_header);
            } else {
                $data['list_header'] = $array_header;
            }
        }
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = "";
        if ($ref_table == "str_mng") { //todo à corriger
            if ($this->session->userdata('language_edit_mode') == 'yes') {
                $data['top_buttons'] .= get_top_button('all', 'Close edition mode', 'config/update_edition_mode/no', 'Close edition mode', 'fa-ban', '', ' btn-warning ');
            } else {
                $data['top_buttons'] .= get_top_button('all', 'Open edition mode', 'config/update_edition_mode/yes', 'Open edition mode', 'fa-check', '', ' btn-dark ');
            }
        } else {
            if ($add_link)
                $data['top_buttons'] .= get_top_button('add', 'Add new', $add_link_url);
        }
        if (activate_update_stored_procedure())
            $data['top_buttons'] .= get_top_button('all', 'Update stored procedure', 'home/update_stored_procedure/' . $ref_table, 'Update stored procedure', 'fa-check', '', ' btn-dark ');
        $data['top_buttons'] .= get_top_button('close', 'Close', 'home');
        /*
         * Titre de la page
         */
        if (isset($ref_table_config['entity_title']['list'])) {
            $data['page_title'] = lng($ref_table_config['entity_title']['list']);
        } else {
            $data['page_title'] = lng("List of " . $ref_table_config['reference_title']);
        }
        if ($list_cat == 'mine_screen') {
            $data['page_title'] = "My screenings";
        } elseif ($list_cat == 'mine_assign') {
            $data['page_title'] = "Papers assigned to me for screening";
        }
        if (!empty($page_title))
            $data['page_title'] = $page_title;
        /*
         * Configuration pour l'affichage des lien de navigation
         */
        $data['valeur'] = ($val == "_") ? "" : $val;
        /*
         * Si on a besoin de faire urecherche sur la liste specifier la vue où se trouve le formulaire de recherche
         */
        if (!$dynamic_table and !empty($ref_table_config['search_by'])) {
            $data['search_view'] = 'general/search_view';
        }
        /*
         * La vue qui va s'afficher
         */
        if (!$dynamic_table) {
            $data['nav_pre_link'] = 'screening/list_screen/' . $list_cat . '/' . $val . '/';
            $data['nav_page_position'] = 6;
            $data['page'] = 'general/list';
        } else {
            $data['page'] = 'general/list_dt';
        }
        if (admin_config($ref_table))
            $data['left_menu_admin'] = True;
        /*
         * Chargement de la vue avec les données préparés dans le controleur
         */
        $this->load->view('shared/body', $data);
    }

    //responsible for calculating and displaying the completion progress of screening or screening validation for users
    public function screen_completion($type = 'screening')
    {
        if ($type == 'validate') {
            $assignments = $this->Screening_dataAccess->get_user_assigned_papers(0, 'screen_validation', active_screening_phase());
        } else {
            $assignments = $this->Screening_dataAccess->get_user_assigned_papers(0, 'simple_screen', active_screening_phase());
        }
        //print_test($assignments);
        //print_test($assignments);
        //exit;
        $assignment_id = 0;
        $total_papers = count($assignments);
        $papers_screened = 0;
        $assign_per_user = array();
        foreach ($assignments as $key => $value) {
            if (!isset($assign_per_user[$value['user_id']])) {
                $assign_per_user[$value['user_id']]['total_papers'] = 1;
                if ($value['screening_status'] == 'Done') {
                    $assign_per_user[$value['user_id']]['papers_screened'] = 1;
                    $papers_screened++;
                } else {
                    $assign_per_user[$value['user_id']]['papers_screened'] = 0;
                }
            } else {
                $assign_per_user[$value['user_id']]['total_papers']++;
                if ($value['screening_status'] == 'Done') {
                    $assign_per_user[$value['user_id']]['papers_screened']++;
                    $papers_screened++;
                }
            }
        }
        $users = $this->manager_lib->get_reference_select_values('users;user_name');
        //	print_test($users);
        //print_test($assign_per_user);
        foreach ($assign_per_user as $key_a => $value_a) {
            $assign_per_user[$key_a]['completion'] = (int) ($value_a['papers_screened'] * 100 / $value_a['total_papers']);
            $assign_per_user[$key_a]['user'] = $users[$key_a];
        }
        $assign_per_user['total'] = array(
            'total_papers' => $total_papers,
            'papers_screened' => $papers_screened,
            'completion' => !empty($total_papers) ? (int) ($papers_screened * 100 / $total_papers) : 0,
            'user' => '<b>Total</b>',
        );
        //	print_test($assign_per_user);
        $data['completion_screen'] = $assign_per_user;
        //print_test($data['completion_screen']);
        $data['page_title'] = ($type == 'validate') ? lng('Screening validation progress') : lng('Screening Progress');
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        $data['left_menu_perspective'] = 'left_menu_screening';
        $data['project_perspective'] = 'screening';
        $data['page'] = 'screening/screen_completion';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /**
     * The purpose of this function is to gather and display screening statistics and results, 
     * including the number and percentage of papers for each screening status and decision source, 
     * results per user, and results per exclusion/inclusion criteria.
     */
    public function screen_result($type = 1, $api = 0)
    {
        $users = $this->manager_lib->get_reference_select_values('users;user_name');
        $exclusion_crit = $this->manager_lib->get_reference_select_values('exclusioncrieria;ref_value');
        $inclusion_crit = $this->manager_lib->get_reference_select_values('inclusioncriteria;ref_value');
        //print_test($users);
        $ref_table_config = get_table_configuration('papers');
        $ref_table_config['current_operation'] = 'list_papers_screen';
        $papers = $this->DBConnection_mdl->get_list_mdl($ref_table_config, '_', 0, -1);
        //print_test($papers);
        $excluded_conflict = 0;
        $included_conflict = 0;
        $result = array();
        $result['total'] = 0;
        foreach ($papers['list'] as $key => $value) {
            if (!empty($value['screening_status'])) {
                if (empty($result[$value['screening_status']])) {
                    $result[$value['screening_status']] = 1;
                } else {
                    $result[$value['screening_status']] = $result[$value['screening_status']] + 1;
                }
                $result['total']++;
                if ($value['decision_source'] == 'conflict_resolution' and $value['screening_status'] == 'Included') {
                    $included_conflict++;
                } elseif ($value['decision_source'] == 'conflict_resolution' and $value['screening_status'] == 'Excluded') {
                    $excluded_conflict++;
                }
            }
        }
        //  list to be displayed for global result
        $data['screening_result'] = array(
            '0' => array(
                'title' => 'Decision',
                'nbr' => 'Papers',
                'pourc' => '%',
            ),
            'Included' => array(
                'title' => anchor('element/entity_list/list_papers_screen_included', '<u><b>Included</b></u>'),
                'nbr' => !empty($result['Included']) ? $result['Included'] : 0,
                'pourc' => !empty($result['Included']) ? round(($result['Included'] * 100 / $result['total']), 2) : 0,
            ),
            'Excluded' => array(
                'title' => anchor('element/entity_list/list_papers_screen_excluded', '<u><b>Excluded</b></u>'),
                'nbr' => !empty($result['Excluded']) ? $result['Excluded'] : 0,
                'pourc' => !empty($result['Excluded']) ? round(($result['Excluded'] * 100 / $result['total']), 2) : 0,
            ),
            'conflict' => array(
                'title' => anchor('element/entity_list/list_papers_screen_conflict', '<u><b>In conflict</b></u>'),
                'nbr' => !empty($result['In conflict']) ? $result['In conflict'] : 0,
                'pourc' => !empty($result['In conflict']) ? round(($result['In conflict'] * 100 / $result['total']), 2) : 0,
            ),
            'review' => array(
                'title' => anchor('element/entity_list/list_papers_screen_review', '<u><b>In review</b></u>'),
                'nbr' => !empty($result['In review']) ? $result['In review'] : 0,
                'pourc' => !empty($result['In review']) ? round(($result['In review'] * 100 / $result['total']), 2) : 0,
            ),
            'pending' => array(
                'title' => anchor('element/entity_list/list_papers_screen_pending', '<u><b>Pending</b></u>'),
                'nbr' => !empty($result['Pending']) ? $result['Pending'] : 0,
                'pourc' => !empty($result['Pending']) ? round(($result['Pending'] * 100 / $result['total']), 2) : 0,
            ),
            'total' => array(
                'title' => '<b>Total</b>',
                'nbr' => "<b>" . (!empty($result['total']) ? $result['total'] : 0) . "</b>",
                'pourc' => '',
            )
        );
        $data['screening_conflict_resolution'] = array(
            '0' => array(
                'title' => 'Decision',
                'nbr' => 'Nbr',
            ),
            'Included' => array(
                //'title'=>'Resolved included',
                'title' => anchor('element/entity_list/list_papers_screen_included_after_conflict', '<u><b>Resolved included</b></u>'),
                'nbr' => $included_conflict,
            ),
            'Excluded' => array(
                //'title'=>'Resolved excluded',
                'title' => anchor('element/entity_list/list_papers_screen_excluded_after_conflict', '<u><b>Resolved excluded</b></u>'),
                'nbr' => $excluded_conflict,
            ),
            'conflict' => array(
                //'title'=>'Pending conflicts',
                'title' => anchor('element/entity_list/list_papers_screen_conflict', '<u><b>Pending conflicts</b></u>'),
                'nbr' => !empty($result['In conflict']) ? $result['In conflict'] : 0,
            )
        );
        $ref_table_config_s = get_table_configuration('screening');
        $ref_table_config_s['current_operation'] = 'list_screenings';
        $screenings = $this->DBConnection_mdl->get_list_mdl($ref_table_config_s, '_', 0, -1);
        //print_test($screenings);exit;
        //$screenings=$this->DBConnection_mdl->get_list(get_table_config('screening'),'_',0,-1);
        $res_screening['total'] = 0;
        $res_screening['users'] = array();
        $res_screening['criteria'] = array();
        $res_screening['in_criteria'] = array();
        $res_screening['all_criteria'] = 0;
        $res_screening['all_criteria_two'] = 0;
        $key = 0;
        //	print_test($screenings);
        foreach ($screenings['list'] as $key => $value) {
            $res_screening['total']++;
            if (empty($res_screening['users'][$value['user_id']][$value['screening_decision']])) {
                $res_screening['users'][$value['user_id']][$value['screening_decision']] = 1;
            } else {
                $res_screening['users'][$value['user_id']][$value['screening_decision']] = $res_screening['users'][$value['user_id']][$value['screening_decision']] + 1;
            }
            // exclusion critéria
            if ($value['screening_decision'] == 'Excluded' and !empty($value['exclusion_criteria'])) {
                if (empty($res_screening['criteria'][$value['exclusion_criteria']])) {
                    //	echo "<p>bbb</p>";
                    $res_screening['criteria'][$value['exclusion_criteria']] = 1;
                } else {
                    //	echo "<p>cccc</p>";
                    $res_screening['criteria'][$value['exclusion_criteria']] = $res_screening['criteria'][$value['exclusion_criteria']] + 1;
                }
                $res_screening['all_criteria']++;
                //critérias per user
                if (empty($res_screening['users'][$value['user_id']]['criteria'][$value['exclusion_criteria']])) {
                    //	echo "<p>bbb</p>";
                    $res_screening['users'][$value['user_id']]['criteria'][$value['exclusion_criteria']] = 1;
                } else {
                    //	echo "<p>cccc</p>";
                    $res_screening['users'][$value['user_id']]['criteria'][$value['exclusion_criteria']] = $res_screening['users'][$value['user_id']]['criteria'][$value['exclusion_criteria']] + 1;
                }
            }
            // inclusion criteria
            if ($value['screening_decision'] == 'Included' and !empty($value['inclusion_criteria'])) {
                if (empty($res_screening['in_criteria'][$value['inclusion_criteria']])) {
                    //	echo "<p>bbb</p>";
                    $res_screening['in_criteria'][$value['inclusion_criteria']] = 1;
                } else {
                    //	echo "<p>cccc</p>";
                    $res_screening['in_criteria'][$value['inclusion_criteria']] = $res_screening['in_criteria'][$value['inclusion_criteria']] + 1;
                }
                $res_screening['all_criteria_two']++;
                //critérias per user
                if (empty($res_screening['users'][$value['user_id']]['in_criteria'][$value['inclusion_criteria']])) {
                    //	echo "<p>bbb</p>";
                    $res_screening['users'][$value['user_id']]['in_criteria'][$value['inclusion_criteria']] = 1;
                } else {
                    //	echo "<p>cccc</p>";
                    $res_screening['users'][$value['user_id']]['in_criteria'][$value['inclusion_criteria']] = $res_screening['users'][$value['user_id']]['in_criteria'][$value['inclusion_criteria']] + 1;
                }
            }
        }
        //  list to be displayed for  result per user
        $result_per_user = array();
        if (!empty($res_screening['users']))
            ; {
            $result_per_user[0] = array(
                'user' => 'User ',
                'accepted' => 'Included',
                'excluded' => 'Excluded',
                'conflict' => 'In conflict',
            );
            $i = 1;
            foreach ($res_screening['users'] as $key => $value) {
                $user_screening_completion = $this->get_user_completion($key, active_screening_phase(), 'Screening');
                $result_per_user[$i] = array(
                    'user' => !empty($users[$key]) ? $users[$key] : 'User ' . $key,
                    'accepted' => !empty($value['Included']) ? $value['Included'] : 0,
                    'excluded' => !empty($value['Excluded']) ? $value['Excluded'] : 0,
                    'conflict' => !empty($user_screening_completion['papers_in_conflict']) ? $user_screening_completion['papers_in_conflict'] : 0,
                );
                $i++;
            }
        }
        $data['result_per_user'] = $result_per_user;
        $result_per_criteria = array();
        if (!empty($res_screening['criteria'])) {
            $result_per_criteria[0] = array(
                'criteria' => 'Criteria ',
                'Nbr' => 'Nbr',
                'pourc' => '%'
            );
            $i = 1;
            foreach ($res_screening['criteria'] as $key => $value) {
                $result_per_criteria[$i] = array(
                    'criteria' => !empty($exclusion_crit[$key]) ? $exclusion_crit[$key] : 'Criteria ' . $key,
                    'Nbr' => $value,
                    'pourc' => !empty($res_screening['all_criteria']) ? round(($value * 100 / $res_screening['all_criteria']), 2) : 0,
                );
                $i++;
            }
        }
        $result_per_criteria_two = array();
        if (!empty($res_screening['in_criteria'])) {
            $result_per_criteria_two[0] = array(
                'criteria' => 'Criteria ',
                'Nbr' => 'Nbr',
                'pourc' => '%'
            );
            $i = 1;
            foreach ($res_screening['in_criteria'] as $key => $value) {
                $result_per_criteria_two[$i] = array(
                    'criteria' => !empty($inclusion_crit[$key]) ? $inclusion_crit[$key] : 'Criteria ' . $key,
                    'Nbr' => $value,
                    'pourc' => !empty($res_screening['all_criteria_two']) ? round(($value * 100 / $result['Included']), 2) : 0,
                );
                $i++;
            }
        }
        //test if kappa is enabled
        if (get_appconfig_element('use_kappa')) {
            $kappa = $this->calculate_kappa();
            $kappa_meaning = '-';
            //	print_test($kappa_meaning);
            $k_display = "";
            if (!empty($kappa)) {
                $kappa_meaning = $this->kappa_meaning($kappa);
                $k_display = " -   Kappa : $kappa ($kappa_meaning)";
            }
            $data['kappa'] = $kappa;
            $data['kappa_meaning'] = $kappa_meaning;
        }
        $data['result_per_criteria'] = $result_per_criteria;
        $data['result_per_criteria_two'] = $result_per_criteria_two;
        $data['page_title'] = lng('Screening Statistics'); //.$k_display;
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        $data['left_menu_perspective'] = 'left_menu_screening';
        $data['project_perspective'] = 'screening';
        $data['page'] = 'screening/screen_result';
        if ($api)
            print_test($data);
        else {
            /*
             * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
             */
            $this->load->view('shared/body', $data);
        }
    }

    //gather and display validation statistics and results, including the list of screenings with paper details, validation decisions, and match status
    public function screen_validation_result()
    {
        //Get all papers
        $res_papers = $this->get_papers_to_screen();
        $papers = array();
        foreach ($res_papers['all_papers'] as $key => $value) {
            $papers[$value['id']] = array(
                'bibtexKey' => $value['bibtexKey'],
                'title' => $value['title'],
                'screening_status' => $value['paper_status'],
                'classification_status' => $value['classification_status']
            );
        }
        //Get result of the validation
        $ref_table_config = get_table_configuration('screening');
        $ref_table_config['current_operation'] = 'list_screenings_validation'; //operation Defined in configuration for screening
        $res_screenings = $this->DBConnection_mdl->get_list_mdl($ref_table_config, '_', 0, -1);
        //Verify matches and differences
        $screenings = array();
        $nbr_all = 0;
        $nbr_matched = 0;
        $i = 1;
        foreach ($res_screenings['list'] as $key => $value) {
            if (!empty($papers[$value['paper_id']])) {
                $screenings[$key] = array(
                    'num' => $i,
                    //'paper'=>$papers[$value['paper_id']]['bibtexKey']." - ".$papers[$value['paper_id']]['title'],
                    'paper' => string_anchor(
                        'screening/display_paper_screen/' . $value['paper_id'],
                        $papers[$value['paper_id']]['bibtexKey'] . " - " . $papers[$value['paper_id']]['title'],
                        80
                    ),
                    //'screening_decision'=>$papers[$value['paper_id']]['screening_status'],
                    'validation_descision' => $value['screening_decision']
                );
                if ($screenings[$key]['validation_descision'] == screening_validation_source_paper_status()) {
                    $nbr_matched++;
                    $screenings[$key]['matched'] = 'Yes';
                } else {
                    $screenings[$key]['matched'] = 'No';
                }
                $but[0] = array(
                    'url' => 'screening/display_paper_screen/' . $value['paper_id'],
                    'label' => icon('folder') . ' ' . 'View',
                    'title' => 'Display'
                );
                //	$screenings[$key]['butt']=create_button_link_dropdown($but,lng_min('Action'));
                $nbr_all++;
                $i++;
            }
        }
        $match_percentage = 0;
        if (!empty($nbr_all))
            $match_percentage = round($nbr_matched * 100 / $nbr_all, 2);
        //Validation score per user
        $validation_score_user = $this->screening_validation_score();
        foreach ($validation_score_user as $key => $value) {
            if (!empty($value['all_papers'])) {
                $percentage = round($value['matches'] * 100 / $value['all_papers'], 2);
                //$validation_score_user[$key]['percentage']=$percentage;
                $validation_score_user[$key]['percentage_title'] = "$percentage % : " . $value['matches'] . ' ' . lng("matches out of") . ' ' . $value['all_papers'];
            } else {
                //$validation_score_user[$key]['percentage']='';
                $validation_score_user[$key]['percentage_title'] = '';
            }
            unset($validation_score_user[$key]['matches']);
            unset($validation_score_user[$key]['all_papers']);
        }
        if (!empty($validation_score_user)) {
            array_unshift($validation_score_user, array('Reviewer', 'Score'));
        }
        $data['validation_score'] = $validation_score_user;
        //print_test($validation_score_user);
        $data['list'] = $screenings;
        $data['nombre'] = count($screenings);
        $data['list_header'] = array('#', 'Papers', 'Validation decision', 'Matched');
        $data['top_buttons'] = get_top_button('close', 'Close', 'home');
        $data['result_page_title'] = lng("General validation score") . " -  $match_percentage  % :  $nbr_matched " . lng("matches out of") . " $nbr_all ";
        $data['page_title'] = lng("Validation Statistics");
        $data['page'] = 'relis/validation_result';
        $this->load->view('shared/body', $data);
    }

    /*
     * display the details of a paper in the screening process
     * Fonction spécialisé  pour l'affichage d'un papier
     * Input:	$ref_id: id du papier
     * Input:	$display_type: type d'affishage si la valeur est 'det' lhystorique du papier sera affiché
     */
    public function display_paper_screen($ref_id, $display_type = 'det')
    {
        $project_published = project_published();
        //	print_test(get_paper_screen_result($ref_id));
        $ref_table = "papers";
        /*
         * Récupération de la configuration(structure) de la table des papiers
         */
        $table_config = get_table_configuration($ref_table);
        /*
         * Appel de la fonction  récupérer les informations sur le papier afficher
         */
        $table_config['current_operation'] = 'detail_paper';
        $paper_data = $this->manager_lib->get_detail($table_config, $ref_id);
        //	print_test($paper_data);
        /*
         * Préparations des informations à afficher
         */
        //venue
        $venue = "";
        $authors = "";
        foreach ($paper_data as $key => $value) {
            if ($value['field_id'] == 'venueId' and !empty($value['val2'][0])) {
                $venue = $value['val2'][0];
            } elseif ($value['field_id'] == 'authors' and !empty($value['val2'])) {
                if (count($value['val2']) > 1) {
                    $authors = '<table class="table table-hover" ><tr><td> ' . $value['val2'][0] . '</td></tr>';
                    foreach ($value['val2'] as $k => $v) {
                        if ($k > 0) {
                            $authors .= "<tr><td> " . $v . '</td></tr>';
                        }
                    }
                    $authors .= "</table>";
                } else {
                    $authors = " : " . $value['val2'][0];
                }
            }
        }
        $content_item = $this->DBConnection_mdl->get_row_details('get_detail_papers', $ref_id, TRUE);
        $paper_name = $content_item['bibtexKey'] . " - " . $content_item['title'];
        $paper_excluded = False;
        if ($content_item['paper_excluded'] == '1') {
            $paper_excluded = True;
        }
        $data['paper_excluded'] = $paper_excluded;
        $item_data = array();
        $array['title'] = $content_item['bibtexKey'] . " - " . $content_item['title'];
        if (!empty($content_item['doi'])) {
            $paper_link = $content_item['doi'];
            if ((strpos($paper_link, 'http://') === FALSE) && (strpos($paper_link, 'https://') === FALSE)) {
                $paper_link = "//" . $paper_link;
            }
            $array['title'] .= '<ul class="nav navbar-right panel_toolbox">
				<li>
					<a title="Go to the page" href="' . $paper_link . '" target="_blank" >
				 		<img src="' . base_url() . 'cside/images/pdf.jpg"/>
					</a>
				</li>
				</ul>';
        }
        array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Abstract') . " :</b> <br/><br/>" . $content_item['abstract'];
        // $this->highlight_search_term($content_item['abstract'], "model");
        array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Preview') . " :</b> <br/><br/>" . $content_item['preview'];
        array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Venue') . " </b> " . $venue;
        //array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Authors') . " </b> " . $authors;
        //array_push($item_data, $array);
        $data['item_data'] = $item_data;
        //print_test($data);
        $screening_phase = active_screening_phase();
        if (active_screening_phase()) {
            //$screening_phase=1;
            //$res_screen=get_paper_screen_result($ref_id);
            $res_screen = get_paper_screen_status_new($ref_id, $screening_phase, 'all');
            //	print_test($res_screen);
            if (trim($res_screen['screening_result']) == 'In conflict' and !$project_published) {
                $my_paper = FALSE;
                foreach ($res_screen['screenings'] as $key => $value) {
                    if (has_usergroup(1) or is_project_creator(active_user_id(), project_db()) or $value['user_id'] == active_user_id()) {
                        $res_screen['screenings'][$key]['edit_link'] = create_button_link('screening/edit_screen/' . $value['screening_id'] . '/resolve_conflict', 'Edit', "btn-info", "Update decision");
                    } else {
                        $res_screen['screenings'][$key]['edit_link'] = "";
                    }
                }
                $data['screen_edit_link'] = TRUE;
            }
            if (
                (has_usergroup(1)
                    or is_project_creator(active_user_id(), project_db()) or can_manage_project(active_user_id(), project_db()))
                and !$project_published
            )
                $data['assign_new_button'] = get_top_button('add', 'Add a reviewer', 'element/add_element_child/add_reviewer/' . $ref_id, 'Add a reviewer') . " ";
            $data['screenings'] = $res_screen['screenings'];
            $data['screening_result'] = $res_screen['screening_result'];
        } else {
            $data['screening_result'] = $content_item['screening_status'];
        }
        if ($display_type == 'det') {
            if (active_screening_phase()) {
                $data['screen_history'] = get_paper_screen_history($ref_id, $screening_phase);
            } else {
                $data['screen_history'] = get_paper_screen_status__all($ref_id);
            }
            //print_test($data['screen_history']);
        }
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = "";
        if (!empty($table_config['links']['edit']) and !empty($table_config['links']['edit']['on_view']) and ($table_config['links']['edit']['on_view'] == True)) {
            //$data ['top_buttons'] .= get_top_button ( 'edit', $table_config['links']['edit']['title'], 'manager/edit_element/' . $ref_table.'/'.$ref_id )." ";
        }
        if (!empty($table_config['links']['delete']) and !empty($table_config['links']['delete']['on_view']) and ($table_config['links']['delete']['on_view'] == True)) {
            //$data ['top_buttons'] .= get_top_button ( 'delete', $table_config['links']['delete']['title'], 'manage/delete_element/' . $ref_table.'/'.$ref_id )." ";
        }
        $data['top_buttons'] .= get_top_button('back', 'Back', 'home');
        /*
         * Titre de la page
         */
        $data['page_title'] = lng('Paper');
        /*
         * La vue qui va s'afficher
         */
        $data['page'] = 'screening/display_paper_screen';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    //The purpose of this function is to assign papers for validation in the screening process
    public function validate_screen_set($data = array())
    {
        if (!active_screening_phase()) {
            redirect('home');
            exit;
        }
        $screening_phase_info = active_screening_phase_info();
        //print_test($screening_phase_info);
        $screening_phase_id = active_screening_phase();
        $paper_source = $screening_phase_id;
        $paper_source_status = screening_validation_source_paper_status(); //Excluded
        $phase_title = $screening_phase_info['phase_title'];
        $append_title = "( $paper_source_status papers  from $phase_title )";
        //echo $append_title;
        $data['papers_sources'] = $paper_source;
        $data['paper_source_status'] = $paper_source_status;
        $data['screening_phase'] = $screening_phase_id;
        if (has_user_role('validator')) {
            $data['assign_to_connected'] = True;
        } else {
            $data['assign_to_connected'] = False;
        }
        $papers = $this->get_papers_to_screen($paper_source, $paper_source_status, '', 'Validation');
        //	print_test($papers['assigned']);
        $data['paper_source'] = $paper_source;
        $paper_list[0] = array('Key', 'Title');
        foreach ($papers['to_assign'] as $key => $value) {
            $paper_list[$key + 1] = array($value['bibtexKey'], $value['title']);
        }
        $data['paper_list'] = $paper_list;
        $user_table_config = get_table_configuration('users');
        $users = $this->DBConnection_mdl->get_list($user_table_config, '_', 0, -1);
        $_assign_user = array();
        foreach ($users['list'] as $key => $value) {
            if ((user_project($this->session->userdata('project_id'), $value['user_id'])) and can_validate_project($value['user_id'])) {
                $_assign_user[$value['user_id']] = $value['user_name'];
            }
        }
        //	print_test($users);
        $data['users'] = $_assign_user;
        $data['number_papers'] = count($papers['to_assign']);
        $data['number_papers_assigned'] = count($papers['assigned']);
        $data['percentage_of_papers'] = get_appconfig_element('validation_default_percentage');
        $data['papers_categories'] = array('Excluded' => 'Excluded', 'Included' => 'Included', 'all' => 'All');
        $data['page_title'] = lng('Assign papers for validation ' . $append_title);
        $data['top_buttons'] = get_top_button('back', 'Back', 'home');
        //$data['left_menu_perspective']='z_left_menu_screening';
        //$data['project_perspective']='screening';
        $data['page'] = 'screening/assign_papers_screen_validation';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    //responsible for assigning papers for validation based on the previous screening phase.
    public function validate_screen_from_previous_set($data = array())
    { //old vesion updated
        if (!active_screening_phase()) {
            redirect('home');
            exit;
        }
        $screening_phase_info = active_screening_phase_info();
        $creening_phase_id = active_screening_phase();
        $data['screening_phase'] = $creening_phase_id;
        //print_test($screening_phase_info);
        //$screening phases
        $screening_phases = $this->db_current->order_by('screen_phase_order', 'ASC')
            ->get_where('screen_phase', array('screen_phase_active' => 1))
            ->result_array();
        $previous_phase = 0;
        $previous_phase_title = "";
        if ($screening_phase_info['source_paper'] == 'Previous phase') {
            foreach ($screening_phases as $k => $phase) {
                if ($phase['screen_phase_id'] == $creening_phase_id) {
                    break;
                } elseif ($phase['phase_type'] != 'Validation') {
                    $previous_phase = $phase['screen_phase_id'];
                    $previous_phase_title = $phase['phase_title'];
                }
            }
        }
        if ($previous_phase == 0) {
            $paper_source = 'all';
            $paper_source_status = $screening_phase_info['source_paper_status'];
            $previous_phase_title = " ";
        } else {
            $paper_source = $previous_phase;
            $paper_source_status = $screening_phase_info['source_paper_status'];
            $previous_phase_title = " from $previous_phase_title";
        }
        $append_title = "( $paper_source_status papers  $previous_phase_title )";
        //echo $append_title;
        $data['papers_sources'] = $paper_source;
        $data['paper_source_status'] = $paper_source_status;
        $data['screening_phase'] = $creening_phase_id;
        $papers = $this->get_papers_to_screen($paper_source, $paper_source_status);
        //print_test($papers);
        $data['paper_source'] = $paper_source;
        $paper_list[0] = array('Key', 'Title');
        foreach ($papers['to_assign'] as $key => $value) {
            $paper_list[$key + 1] = array($value['bibtexKey'], $value['title']);
        }
        $data['paper_list'] = $paper_list;
        //	$papers=$this->Paper_dataAccess->get_papers('screen','papers','_',0,-1);
        //print_test($papers);
        $user_table_config = get_table_configuration('users');
        $users = $this->DBConnection_mdl->get_list($user_table_config, '_', 0, -1);
        $_assign_user = array();
        foreach ($users['list'] as $key => $value) {
            if ((user_project($this->session->userdata('project_id'), $value['user_id']))) {
                $_assign_user[$value['user_id']] = $value['user_name'];
            }
        }
        //	print_test($users);
        $data['users'] = $_assign_user;
        $data['number_papers'] = count($papers['to_assign']);
        $data['number_papers_assigned'] = count($papers['assigned']);
        $data['percentage_of_papers'] = 20;
        $data['papers_categories'] = array('Excluded' => 'Excluded', 'Included' => 'Included', 'all' => 'All');
        $data['page_title'] = lng('Set screening validation ' . $append_title);
        $data['top_buttons'] = get_top_button('back', 'Back', 'home');
        //$data['left_menu_perspective']='z_left_menu_screening';
        //$data['project_perspective']='screening';
        $data['page'] = 'screening/assign_papers_screen_validation';
        //print_test($data);
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    //The purpose of this function is to save the assignment of papers for validation
    function save_assign_screen_validation()
    {
        $post_arr = $this->input->post();
        $users = array();
        $i = 1;
        $percentage = intval($post_arr['percentage']);
        if (empty($percentage)) {
            $data['err_msg'] = lng(' Please provide  "Percentage of papers" ');
            $this->validate_screen_set($data);
        } elseif ($percentage > 100 or $percentage <= 0) {
            $data['err_msg'] = lng("Please provide a correct value of percentage");
            $this->validate_screen_set($data);
        } else {
            // Get selected users
            if (!empty($post_arr['assign_papers_to'])) { // assign to connected user
                array_push($users, $post_arr['assign_papers_to']);
            } else {
                while ($i <= $post_arr['number_of_users']) {
                    if (!empty($post_arr['user_' . $i])) {
                        array_push($users, $post_arr['user_' . $i]);
                    }
                    $i++;
                }
            }
            //Verify if selected users is > of required reviews per paper
            if (count($users) < 1) {
                $data['err_msg'] = lng('Please select at least one user  ');
                $this->validate_screen_set($data);
            } else {
                $currect_screening_phase = $post_arr['screening_phase'];
                $papers_sources = $post_arr['papers_sources'];
                $paper_source_status = $post_arr['paper_source_status'];
                $screening_phase_info = active_screening_phase_info();
                $phase_title = $screening_phase_info['phase_title'];
                $reviews_per_paper = 1;
                $papers_all = $this->get_papers_to_screen($papers_sources, $paper_source_status, '', 'Validation');
                $papers = $papers_all['to_assign'];
                $papers_to_validate_nbr = round(count($papers) * $percentage / 100);
                $operation_description = "Assign $percentage % ($papers_to_validate_nbr) of " . $paper_source_status . " papers for $phase_title";
                //	print_test($papers);
                shuffle($papers); // randomize the list
                $assign_papers = array();
                $this->db2 = $this->load->database(project_db(), TRUE);
                $operation_code = active_user_id() . "_" . time();
                foreach ($papers as $key => $value) {
                    if ($key < $papers_to_validate_nbr) {
                        $assign_papers[$key]['paper'] = $value['id'];
                        $assign_papers[$key]['users'] = array();
                        $assignment_save = array(
                            'paper_id' => $value['id'],
                            'user_id' => '',
                            'assignment_note' => '',
                            'assignment_type' => screening_validator_assignment_type(),
                            'operation_code' => $operation_code,
                            'assignment_mode' => 'auto',
                            'assignment_role' => 'Validation',
                            'screening_phase' => $currect_screening_phase,
                            'assigned_by' => $this->session->userdata('user_id')
                        );
                        $j = 1;
                        //the table to save assignments
                        $table_name = get_table_configuration('screening', 'current', 'table_name');
                        while ($j <= $reviews_per_paper) {
                            $temp_user = ($key % count($users)) + $j;
                            if ($temp_user >= count($users))
                                $temp_user = $temp_user - count($users);
                            array_push($assign_papers[$key]['users'], $users[$temp_user]);
                            $assignment_save['user_id'] = $users[$temp_user];
                            //print_test($assignment_save);
                            $this->db2->insert($table_name, $assignment_save);
                            $j++;
                        }
                    }
                }
                //	print_test();
                $operation_arr = array(
                    'operation_code' => $operation_code,
                    'operation_type' => 'assign_papers_validation',
                    'user_id' => active_user_id(),
                    'operation_desc' => $operation_description
                );
                //print_test($operation_arr);
                $res2 = $this->manage_mdl->add_operation($operation_arr);
                set_top_msg('Operation completed');
                redirect('screening/screening');
            }
        }
    }

    //calculating the validation score for each user in a specific screening phase
    function screening_validation_score($user = 'all', $screening_phase = 0)
    {
        if (empty($screening_phase))
            $screening_phase = active_screening_phase();
        //Get all users
        $users = $this->manager_lib->get_reference_select_values('users;user_name');

        //Get all screenings
        $all_screenings = $this->Screening_dataAccess->get_all_screenings($screening_phase);

        //get all validations
        $all_validations = $this->Screening_dataAccess->get_all_validations($screening_phase);

        //validation result per paper
        $papers_validation = array();
        foreach ($all_validations as $key => $value) {
            $papers_validation[$value['paper_id']] = $value;
        }
        $user_score = array();
        //get user score
        foreach ($all_screenings as $key_screen => $value_screen) {
            if (!empty($papers_validation[$value_screen['paper_id']])) //Verify if the paper have been assigned for validation
            {
                if (empty($user_score[$value_screen['user_id']])) {
                    $user_score[$value_screen['user_id']]['name'] = !empty($users[$value_screen['user_id']]) ? $users[$value_screen['user_id']] : $value_screen['user_id'];
                    $user_score[$value_screen['user_id']]['all_papers'] = 0;
                    $user_score[$value_screen['user_id']]['matches'] = 0;
                }
                $user_score[$value_screen['user_id']]['all_papers']++;
                if ($value_screen['screening_decision'] == $papers_validation[$value_screen['paper_id']]['screening_decision']) {
                    $user_score[$value_screen['user_id']]['matches']++;
                }
            }
        }
        if ($user == 'all') {
            return $user_score;
        } else {
            if (!empty($user_score[$user])) {
                return $user_score[$user];
            } else {
                return null;
            }
        }
    }

    //calculate the kappa statistic, which measures the agreement between two or more raters (in this case, users) in a screening phase
    public function calculate_kappa()
    {
        $matrice = $this->get_screen_for_kappa();
        if (empty($matrice)) {
            return 0;
        } else {
            //print_test($matrice);
            $N = count($matrice);
            $k = count($matrice[0]);
            $n = 0;
            foreach ($matrice[0] as $key => $value) {
                $n += $value;
            }
            //print_test($N);
            //print_test($n);
            //print_test($k);
            if ($n == 1) {
                $kappa = 'one user';
            } else {
                $p = array();
                for ($j = 0; $j < $k; $j++) {
                    $p[$j] = 0.0;
                    for ($i = 0; $i < $N; $i++) {
                        $p[$j] = $p[$j] + $matrice[$i][$j];
                    }
                    $p[$j] = $p[$j] / ($N * $n);
                }
                //	print_test($p);
                $P = array();
                for ($j = 0; $j < $N; $j++) {
                    $P[$j] = 0.0;
                    for ($i = 0; $i < $k; $i++) {
                        $P[$j] = $P[$j] + ($matrice[$j][$i] * $matrice[$j][$i]);
                    }
                    $P[$j] = ($P[$j] - $n) / ($n * ($n - 1));
                }
                //	print_test($P);
                $Pbar = array_sum($P) / $N;
                //	print_test($Pbar);
                $PbarE = 0.0;
                foreach ($p as $key => $value) {
                    $PbarE += $value * $value;
                }
                //print_test($PbarE);
                //added to avoid division by zero
                if ($PbarE == 1)
                    $PbarE = 2;
                $kappa = ($Pbar - $PbarE) / (1 - $PbarE);
                $kappa = round($kappa, 2);
            }
            return $kappa;
        }
        //	print_test($kappa);
    }

    /**
     * handle the process of saving a screening phase, including form validation, error handling, and database operations for insertion or update
     */
    public function save_phase_screen()
    {
        /*
         * Récuperation des valeurs soumis dans le formulaire
         */
        $post_arr = $this->input->post();
        //	print_test($post_arr);
        $table_config = get_table_configuration($post_arr['table_config']);
        $current_operation = $post_arr['current_operation'];
        $this->load->library('form_validation');
        $other_check = true;
        $this->form_validation->set_rules('phase_title', '"' . $table_config['fields']['phase_title']['field_title'] . '"', 'trim|required');
        $this->form_validation->set_rules('source_paper', '"' . $table_config['fields']['source_paper']['field_title'] . '"', 'trim|required');
        $this->form_validation->set_rules('source_paper_status', '"' . $table_config['fields']['source_paper_status']['field_title'] . '"', 'trim|required');
        $other_check = true;
        $data['err_msg'] = "";
        if (empty($post_arr['displayed_fields_vals'])) {
            $other_check = false;
            $data['err_msg'] .= lng('You have to select at least one field to be displayed') . ' <br/>';
        }
        $this->db2 = $this->load->database(project_db(), TRUE);
        $phases = $this->db2->order_by('screen_phase_order', 'ASC')->get_where('screen_phase', array('screen_phase_active' => 1))->result_array();
        //print_test($phases);
        $last_order = 0;
        $final_phase_exist = False;
        foreach ($phases as $key => $value) {
            $last_order = $value['screen_phase_order'];
            if (!empty($value['screen_phase_final'])) {
                if (!(!empty($post_arr['screen_phase_id']) and $post_arr['screen_phase_id'] == $value['screen_phase_id'])) {
                    $final_phase_exist = true;
                }
            }
        }
        if ($final_phase_exist and !empty($post_arr['screen_phase_final'])) {
            $other_check = false;
            $data['err_msg'] .= lng('There is already a final phase ! ') . ' <br/>';
        }
        //print_test($data);
        //exit;
        if ($this->form_validation->run() == FALSE or !$other_check) {
            /*
             * Si la validation du formulaire n'est pas concluante , retour au formulaire de saisie
             */
            $data['content_item'] = $post_arr;
            if ($this->session->userdata('submit_mode') and $this->session->userdata('submit_mode') == 'modal') {
                $submit_mode = 'modal';
            } else {
                $submit_mode = '';
            }
            if (($table_config['operations'][$current_operation]['operation_type']) == 'Add') {
                $this->add_element($current_operation, $data, $post_arr['operation_type'], $submit_mode);
            } elseif ($table_config['operations'][$current_operation]['operation_type'] == 'Edit') {
                $this->add_element($current_operation, $data, $post_arr['operation_type'], $submit_mode, 'Edit');
            }
        } else {
            //Correct go for save
            $fields = implode("|", $post_arr['displayed_fields_vals']);
            $order = !empty($post_arr['screen_phase_order']) ? $post_arr['screen_phase_order'] : $last_order + 10;
            $to_save = array(
                'phase_title' => $post_arr['phase_title'],
                'description' => $post_arr['description'],
                'source_paper' => $post_arr['source_paper'],
                'source_paper_status' => $post_arr['source_paper_status'],
                'displayed_fields' => $fields,
                'screen_phase_order' => $order,
                'phase_type' => $post_arr['phase_type'],
                'screen_phase_final' => $post_arr['screen_phase_final'],
                'added_by' => $post_arr['added_by'],
            );
            //	print_test($to_save); exit;
            if ($post_arr['operation_type'] == 'new') {
                $res = $this->db2->insert('screen_phase', $to_save);
            } else {
                $res = $this->db2->update(
                    'screen_phase',
                    $to_save,
                    array(
                        'screen_phase_id' => $post_arr['screen_phase_id']
                    )
                );
            }
            //	print_test($to_save);
            redirect($post_arr['redirect_after_save']);
        }
    }

    //handles the process of displaying a form for adding or editing an element based on the operation type and the table configuration
    public function add_element($operation_name, $data = [], $operation = 'new', $display_type = "normal", $op_type = "Add")
    {
        $is_guest = check_guest();
        $op = check_operation($operation_name, $op_type);
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        $table_config = get_table_configuration($ref_table);
        if (!$is_guest) {
            if ($ref_table == 'papers') { //Use bibler for papers management
                //redirect("paper/bibler_add_paper");
            }
            if (admin_config($ref_table))
                $data['left_menu_admin'] = True;
            /*
             * charger la manière d'affichage du formulaire
             */
            $this->session->set_userdata('submit_mode', $display_type);
            /*
             * Récupération de la configuration(structure) de la table concerné
             */
            //print_test($table_config);
            $table_config['config_id'] = $ref_table;
            $table_config['current_operation'] = $ref_table_operation;
            $type_op = $operation == 'new' ? "on_add" : "on_edit";
            /*
             * récupération des valeurs qui vont apparaitres dans les dropdown boxes
             * recovery of the values that will appear in the dropdown boxes
             */
            foreach ($table_config['operations'][$ref_table_operation]['fields'] as $k => $v_field) {
                if (!empty($table_config['fields'][$k])) {
                    $v = $table_config['fields'][$k];
                    if (!empty($v['input_type']) and $v['input_type'] == 'select') {
                        if ($v['input_select_source'] == 'table') {
                            if (isset($table_config['fields'][$k]['multi-select']) and $table_config['fields'][$k]['multi-select'] == 'Yes') {
                                $table_config['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], False, False, True);
                            } else {
                                $table_config['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], True, False);
                            }
                        }
                    }
                }
            }
            /*
             * Prépartions des valeurs qui vont apparaitres dans le formulaire
             * Preparation of the values that will appear in the form
             */
            //	$title_append=$table_config['reference_title_min'];
            $data['table_config'] = $table_config;
            //print_test($data);
            //		exit;
            $data['save_function'] = isset($table_config['operations'][$ref_table_operation]['save_function']) ? $table_config['operations'][$ref_table_operation]['save_function'] : 'element/save_element';
            /*
             * Titre de la page
             */
            if (isset($table_config['operations'][$ref_table_operation]['page_title'])) {
                $data['page_title'] = lng($table_config['operations'][$ref_table_operation]['page_title']);
            } else {
                $data['page_title'] = lng("List of " . $table_config['entity_label']);
            }
            /*	if ($operation == 'new') {
                             // La fonction qui va traiter l'enregistrement dans la DB;
                             //$data['save_function']=isset($table_config['save_new_function']) ? $table_config['save_new_function']:'manager/save_element';
                             if(isset($table_config['entity_title']['add'])){
                                 $data['page_title']=lng($table_config['entity_title']['add']);
                             }else{
                                 $data ['page_title'] = lng('Add '.$title_append);
                             }
                         } else {
                             //$data['save_function']=isset($table_config['save_edit_function']) ? $table_config['save_edit_function']:'manager/save_element';
                             if(isset($table_config['entity_title']['edit'])){
                                 $data['page_title']=lng($table_config['entity_title']['edit']);
                             }else{
                                 $data ['page_title'] = lng('Edit '.$title_append);
                             }
                         }
                         */
            if (!empty($table_config['operations'][$ref_table_operation]['redirect_after_save'])) {
                $after_save_redirect = $table_config['operations'][$ref_table_operation]['redirect_after_save'];
                if (!empty($data['current_element'])) {
                    $after_save_redirect = str_replace('~current_element~', $data['current_element'], $after_save_redirect);
                }
            } else {
                $after_save_redirect = "home";
            }
            $this->session->set_userdata('after_save_redirect', $after_save_redirect);
            $data['operation_type'] = $operation;
            /*
             * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
             * Creation of the buttons that will be displayed at the top of the page (top_buttons)
             */
            //$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
            $data['top_buttons'] = $this->create_top_buttons($table_config['operations'][$ref_table_operation]['top_links']);
            /*
             * La vue qui va s'afficher
             */
            $data['page'] = 'general/frm_entity';
            if (!empty($table_config['operations'][$ref_table_operation]['page_template'])) {
                $data['page'] = $table_config['operations'][$ref_table_operation]['page_template'];
            }
            /*
             * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
             */
            if ($display_type == 'modal') {
                $this->load->view('general/frm_entity_modal', $data);
            } else {
                $this->load->view('shared/body', $data);
            }
        } else {
            set_top_msg('No access to this operation!', 'error');
            if (!empty($table_config['operations'][$ref_table_operation]['redirect_after_save'])) {
                $redirect_url = $table_config['operations'][$ref_table_operation]['redirect_after_save'];
                if (!empty($source_id)) {
                    $redirect_url = str_replace('~current_element~', $source_id, $redirect_url);
                }
                redirect($redirect_url);
            } else {
                redirect('home');
            }
        }
    }

    //generate the HTML markup for the top buttons displayed in the form
    private function create_top_buttons($top_links, $current_element = 0)
    {
        $project_published = project_published();
        //print_test($top_links);
        $top_buttons = "";
        foreach ($top_links as $key => $value) {
            if (!empty($value['url'])) {
                $icon = !empty($value['icon']) ? " fa-" . $value['icon'] . " " : "";
                if (in_array($key, array('add', 'edit', 'close', 'delete', 'back'))) {
                    $type = $key;
                } else {
                    $type = "all";
                }
                $title = !empty($value['title']) ? $value['title'] : "";
                $label = !empty($value['label']) ? $value['label'] : "";
                $icon = !empty($value['icon']) ? $value['icon'] : "";
                $url = !empty($value['url']) ? $value['url'] : "";
                if (!empty($current_element)) {
                    $url = str_replace('~current_element~', $current_element, $url);
                }
                if (!$project_published or (in_array($key, array('all_published', 'close', 'back')))) {
                    $top_buttons .= get_top_button($type, $title, $url, $label, $icon);
                }
            }
        }
        return $top_buttons;
    }

    // Function to hightlight the searched term in the result
    private function highlight_search_term($text, $search_string)
    {
        if ($search_string == null) {
            return $text;
        } else {
            $exclude_chars = array('(', ')', 'AND', 'OR', 'NOT', '+', '-', '&&', '||', '!', '{', '}', '[', ']', '^', '~', '?', ':', '/', '$');
            $search_string = str_replace($exclude_chars, '', " " . $search_string . " ");
            $terms = preg_match_all('/"[^"]+"|\s?(\w*[^a-zA-Z0-9_ ])+?\s|\s?\w+?\s/i', $search_string, $matches, PREG_SET_ORDER, 0);
            // print_test($matches);
            foreach ($matches as $match) {
                $match = trim($match[0]);
                if ($match[0] == '"') {
                    if ($match[1] == '*' || $match[strlen($match) - 2] == '*') {
                        $match = str_replace('*', '\w*', $match);
                    }
                    $match = str_replace('"', '\b', $match);
                } elseif ($match[0] == '*' || $match[strlen($match) - 1] == '*') {
                    $match = str_replace('*', '\w*', $match);
                } else {
                    $match = '\b' . $match . '\b';
                }
                $re = '/(' . $match . ')|(\xFE' . $match . ')/i';
                // using ASCII 254 in start and 220 at end as a "placeholder" for the hihlight code tag
                $text = preg_replace_callback($re, function ($findings) {
                    // "\xFE$1\xDC"
                    if ($findings[0][0] == chr(254)) {
                        return $findings[0];
                    } else {
                        return "\xFE" . $findings[0] . "\xDC";
                    }
                }, $text);
            }
            // Hihglihting the search term
            return preg_replace_callback('/\xFE[a-zA-Z0-9_ ]*\xDC/i', function ($matches) {
                $matches[0] = trim($matches[0], chr(254));
                $matches[0] = trim($matches[0], chr(220));
                $final_str = '<span style="background-color: yellow; color: black">' . $matches[0] . '</span>';
                return $final_str;
            }, $text);
        }
    }

    //determine the interpretation or meaning of a Cohen's kappa coefficient based on its value
    public function kappa_meaning($kappa)
    {
        $interpretation = '';
        if ($kappa < 0)
            $interpretation = 'Poor';
        elseif (0.01 <= $kappa and $kappa <= 0.2)
            $interpretation = 'Slight';
        elseif (0.21 <= $kappa and $kappa <= 0.4)
            $interpretation = 'Fair';
        elseif (0.41 <= $kappa and $kappa <= 0.6)
            $interpretation = 'Moderate';
        elseif (0.61 <= $kappa and $kappa <= 0.8)
            $interpretation = 'Substantial';
        elseif (0.81 <= $kappa and $kappa < 1)
            $interpretation = 'Almost perfect';
        elseif ($kappa >= 1)
            $interpretation = 'Perfect';
        elseif ($kappa == 'one user')
            $interpretation = 'Just one participant';
        else
            $interpretation = 'something went wrong...';
        return $interpretation;
    }
}