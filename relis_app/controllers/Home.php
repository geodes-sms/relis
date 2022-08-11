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
	
	
	public function  test_bibler(){
		
		
		$bibtex="@INPROCEEDINGS{4631716,
author={N. Omar and S. S. Hasbullah},
booktitle={2008 International Symposium on Information Technology},
title={SRL TOOL: Heuristics-based Semantic Role Labeling through natural language processing},
year={2008},
volume={2},
pages={1-7},
abstract={The Semantic Role Labeling (SRL Tool) is developed to label the semantic roles that exist in English sentences. This paper proposed a set of new heuristics to assist the semantic role labeling using natural language processing. The preliminary result shows that the use of heuristics can improve the process of assigning the correct semantic roles. This application tool is useful for researchers in Natural Language processing field and also for experts or students in Linguistics.},
keywords={Books;Computational linguistics;Computer science;Humans;Information science;Joining processes;Labeling;Lifting equipment;Natural language processing;Testing},
doi={10.1109/ITSIM.2008.4631716},
ISSN={2155-8973},
month={Aug},}
		
";
		
		$bibtex="@INPROCEEDINGS{4631716,
author={N. Omar and S. S. Hasbullah},
booktitle={2008 International Symposium on Information Technology},
title={SRL TOOL: Heuristics-based Semantic Role Labeling through natural language processing},
year={2008},
volume={2},
pages={1-7},
abstract={The Semantic Role Labeling (SRL Tool) is developed to label the semantic roles that exist in English sentences. This paper proposed a set of new heuristics to assist the semantic role labeling using natural language processing. The preliminary result shows that the use of heuristics can improve the process of assigning the correct semantic roles. This application tool is useful for researchers in Natural Language processing field and also for experts or students in Linguistics.},
keywords={Books;Computational linguistics;Computer science;Humans;Information science;Joining processes;Labeling;Lifting equipment;Natural language processing;Testing},
doi={10.1109/ITSIM.2008.4631716},
ISSN={2155-8973},
month={Aug},}
		
";
		
		$bibdtex="@article {1519,
	title = {Classification d{\textquoteright}offres d{\textquoteright}emploi},
	year = {2017},
	month = {2017},
	type = {Technical report},
	abstract = {Les ressources humaines utilisent de plus en plus les donn{\'e}es intelligentes et les techniques du big data pour faciliter le recrutement. Ainsi, gr{\^a}ce aux profils des r{\'e}seaux sociaux, les recruteurs peuvent identifier des candidats potentiels qui ne sont pas actifs en termes de recherche d{\textquoteright}emploi mais qui pourraient {\^e}tre quand m{\^e}me int{\'e}ress{\'e}s par une opportunit{\'e}. Leur int{\'e}r{\^e}t pour une offre non sollicit{\'e}e est d{\textquoteright}autant plus grand lorsque cette derni{\`e}re correspond bien {\`a} leur profil et {\`a} leur secteur d{\textquoteright}activit{\'e}. Afin d{\textquoteright}am{\'e}liorer les r{\'e}sultats d{\textquoteright}un tel syst{\`e}me de recommandation appariant offres d{\textquoteright}emploi et profils suivant les comp{\'e}tences et les exp{\'e}riences requises, nous proposons de d{\'e}tecter automatiquement le secteur d{\textquoteright}activit{\'e}s des offres {\`a} l{\textquoteright}aide de techniques d{\textquoteright}apprentissage supervis{\'e}.
},
	keywords = {Automatic classification, E-recruitment, Recommendation systems},
	url = {http://rali.iro.umontreal.ca/rali/node/1519/},
	pdf = {http://rali.iro.umontreal.ca/rali/sites/default/files/publis/classification_offre_emploi.pdf},
	author = {Annette Casagrande and Fabrizio Gotti and Guy Lapalme}
}
";
		//print_test($bibtex);
		
		$init_time=microtime ();
		$i=1;
		$res="init";
		while($i<10){
			//$res=$this->biblerproxy_lib->addEntry($bibtex);
			//$res=$this->biblerproxy_lib->bibtextobibtex($bibtex);
			//$res=$this->biblerproxy_lib->bibtextosql($bibtex);
			//$res=$this->biblerproxy_lib->addEntry($bibtex);
			//$res=$this->biblerproxy_lib->previewEntry($bibtex);
			//$res=$this->biblerproxy_lib->bibtextocsv($bibtex);
			//$res=$this->biblerproxy_lib->bibtextohtml($bibtex);
			//$res=$this->biblerproxy_lib->formatBibtex($bibtex);
			$res=$this->biblerproxy_lib->createentryforreliS($bibtex);
			echo "zzzz";
			print_test($res);
			echo "yyyy";
			$correct=False;
			if (strpos($res, 'Internal Server Error') !== false ){
				 
				echo " error - ".$i;
				$i++;
			}else{
				echo " ok - ".$i;
				$correct=True;
				$i=20;
			}
			usleep(500);
			
		}
		
		$end_time=microtime ();
		print_test($res);
	//	echo "<h1>".($end_time - $init_time)."</h1>";
		ini_set('auto_detect_line_endings',TRUE);
		if($correct){
		
			//$fp = fopen('test_'.time().'.txt', 'w+');
			//fputs($fp, $res);
			$res=str_replace("True,", "'True',", $res);
			$res=str_replace("False,", "'False',", $res);
			$res = $this->biblerproxy_lib->fixJSON($res);
		
		
			$Tres = json_decode($res,True);
			if (json_last_error() === JSON_ERROR_NONE) {
				//do something with $json. It's ready to use
				//print_test();
				echo "<pre>";
				print_r($Tres);
				echo "</pre>";
			} else {
		
				//yep, it's not JSON. Log error or alert someone or do nothing
				echo json_last_error();
				echo "<p>Not a valid Json</p>";
			}
		
		
		
		}
		echo "<hr/>";
		
	}
	private function get_classification_completion($type='class',$user=''){
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
				$papers_all=$this->DBConnection_mdl->count_papers('all');
				$papers_done=$this->DBConnection_mdl->count_papers('processed');
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
		$res=$this->manager_lib->get_classification_completion($type,$user);	
		return $res;
		//print_test($res);
		
	}
	
	public function screening()
	{
		$project_published=project_published();
	//	
		//update_paper_status_all();
		//$this->session->set_userdata('working_perspective','screen');
		
		
		
		if(!($this->session->userdata ( 'project_db' ))){
	
			redirect('manager/projects_list');
		}
		
		if($this->session->userdata('working_perspective')=='class'){
				redirect('home');
			}
			
		if(! active_screening_phase()){
				redirect('home/screening_select');
		}
			
		//$this->calculate_kappa();
		$data['screening_phase_info']=active_screening_phase_info();
		
		//print_test($screening_phase_info);
		
			/*
			 * Recuperation du nombre de papiers par catégorie
			 */
			
		
		//	$screening_completion=$this->get_user_completion(active_user_id(),active_screening_phase(),'Screening');
			$screening_completion=$this->get_user_completion(active_user_id(),active_screening_phase(),'Screening');
			//print_test($screening_completion);
			//print_test($screening_completion);
			if(!empty($screening_completion['all_papers'])){
				$data['screening_completion']['title']="My screening progress";
				$data['screening_completion']['all_papers']=array('value'=>$screening_completion['all_papers'],
																	'title'=>'All',
																	'url'=>'op/entity_list/list_my_assignments'
																	);
				$data['screening_completion']['pending_papers']=array('value'=>$screening_completion['all_papers']-$screening_completion['papers_done'],
						'title'=>'Pending',
						'url'=>'op/entity_list/list_my_pending_screenings'
				);
				$data['screening_completion']['done_papers']=array('value'=>$screening_completion['papers_done'],
						'title'=>'Screened',
						'url'=>'op/entity_list/list_my_screenings'
				);
				$data['screening_completion']['conflict_papers']=array('value'=>$screening_completion['papers_in_conflict'],
						'title'=>'Conflicts',
						//'url'=>'op/entity_list/list_papers_screen_conflict'
						'url'=>'op/entity_list/list_papers_screen_my_conflict'
				);
				
				$data['screening_completion']['gauge_all']=$screening_completion['all_papers'];
				$data['screening_completion']['gauge_done']=$screening_completion['papers_done'] - $screening_completion['papers_in_conflict'];
			}
			
			
			//general screening completion
			$general_screening_completion=$this->get_user_completion(0,active_screening_phase(),'Screening');
			//print_test($general_screening_completion);
			if(!empty($general_screening_completion['all_papers'])){
				$data['general_screening_completion']['title']="Overall screening assignment  progress";
				$data['general_screening_completion']['all_papers']=array('value'=>$general_screening_completion['all_papers'],
						'title'=>'All',
						'url'=>'op/entity_list/list_assignments'
				);
				$data['general_screening_completion']['pending_papers']=array('value'=>$general_screening_completion['all_papers']-$general_screening_completion['papers_done'],
						'title'=>'Pending',
						'url'=>'op/entity_list/list_all_pending_screenings'
						
				);
				$data['general_screening_completion']['done_papers']=array('value'=>$general_screening_completion['papers_done'],
						'title'=>'Screened',
						'url'=>'op/entity_list/list_screenings'
				);
				$data['general_screening_completion']['conflict_papers']=array('value'=>$general_screening_completion['papers_in_conflict'],
						'title'=>'Conflicts',
						'url'=>'op/entity_list/list_papers_screen_conflict'
				);
			
				$data['general_screening_completion']['gauge_all']=$general_screening_completion['all_papers'];
				$data['general_screening_completion']['gauge_done']=$general_screening_completion['papers_done'] - $general_screening_completion['papers_in_conflict'];
			//	$data['general_screening_completion']['gauge_done']=0;
			}
			
			
			
			
			if(get_appconfig_element('screening_validation_on')){
				$validation_completion=$this->get_user_completion(active_user_id(),active_screening_phase(),'screen_validation');
				$general_validation_completion=$this->get_user_completion(0,active_screening_phase(),'screen_validation');
			}
			//print_test($validation_completion);
			if(!empty($validation_completion['all_papers'])){
				$data['validation_completion']['title']="My validations progress";
				$data['validation_completion']['all_papers']=array('value'=>$validation_completion['all_papers'],
						'title'=>'All',
						'url'=>'op/entity_list/list_my_validations_assignment'
				);
				$data['validation_completion']['pending_papers']=array('value'=>$validation_completion['all_papers']-$validation_completion['papers_done'],
						'title'=>'Pending',
						'url'=>'op/entity_list/list_my_pending_validation'
				);
				$data['validation_completion']['done_papers']=array('value'=>$validation_completion['papers_done'],
						'title'=>'Validated',
						'url'=>'op/entity_list/list_my_done_validation'
				);
			
				$data['validation_completion']['gauge_all']=$validation_completion['all_papers'];
				$data['validation_completion']['gauge_done']=$validation_completion['papers_done'];
			}
			
			
			////general screening validation completion
			//print_test($validation_completion);
			
			
			if(!empty($general_validation_completion['all_papers'])){
				$data['general_validation_completion']['title']="Overall validations progress";
				$data['general_validation_completion']['all_papers']=array('value'=>$general_validation_completion['all_papers'],
						'title'=>'All',
						'url'=>'op/entity_list/list_assignments_validation'
				);
				$data['general_validation_completion']['pending_papers']=array('value'=>$general_validation_completion['all_papers']-$general_validation_completion['papers_done'],
						'title'=>'Pending',
						'url'=>'op/entity_list/list_pending_screenings_validation'
				);
				$data['general_validation_completion']['done_papers']=array('value'=>$general_validation_completion['papers_done'],
						'title'=>'Validated',
						'url'=>'op/entity_list/list_screenings_validation'
				);
			
				$data['general_validation_completion']['gauge_all']=$general_validation_completion['all_papers'];
				$data['general_validation_completion']['gauge_done']=$general_validation_completion['papers_done'];
			}
			
			//print_test($data);
			
			//$shortut operations
			
			$action_but=array();
			if(can_manage_project() AND !$project_published)
			$action_but['assign_screen']=get_top_button ( 'all', 'Assign papers for screening', 'relis/manager/assignment_screen','Assign papers','fa-mail-forward','',' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ' ,False);
			
			if(can_review_project() AND !$project_published)
			$action_but['screen']=get_top_button ( 'all', 'Screen papers', 'relis/manager/screen_paper','Screen','fa-search','',' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ' ,False);
			
			if(can_manage_project() OR get_appconfig_element('screening_result_on') ){
				$action_but['screen_result']=get_top_button ( 'all', 'Screening progress', 'relis/manager/screen_completion','Progress','fa-tasks','',' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ' ,False);
				$action_but['screen_completion']=get_top_button ( 'all', 'Screening Statistics', 'relis/manager/screen_result','Statistics','fa-th','',' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ' ,False);
			}
			
			$data['action_but_screen']=$action_but;
			
			$action_but=array();
			if(get_appconfig_element('screening_validation_on') ){
				if(can_validate_project() AND !$project_published){
					$action_but['assign_screen']=get_top_button ( 'all', 'Assign papers for validation', 'relis/manager/validate_screen_set','Assign papers','fa-mail-forward','',' btn-primary action_butt col-md-2 col-sm-2 col-xs-12 ' ,False);
					$action_but['screen']=get_top_button ( 'all', 'Validate screening', 'relis/manager/screen_paper_validation','Validate','fa-check-square-o','',' btn-primary action_butt col-md-2 col-sm-2 col-xs-12 ' ,False);
				}
				$action_but['screen_result']=get_top_button ( 'all', 'Validation progress', 'relis/manager/screen_completion/validate','Progress','fa-tasks','',' btn-primary action_butt col-md-2 col-sm-2 col-xs-12 ' ,False);
				$action_but['screen_completion']=get_top_button ( 'all', 'Validation Statistics', 'relis/manager/screen_validation_result','Statistics','fa-th','',' btn-primary action_butt  col-md-2 col-sm-2 col-xs-12' ,False);
					
				$data['action_but_validate']=$action_but;
			
			}
		//	print_test($action_but);
			$data['configuration']=get_project_config($this->session->userdata ( 'project_db' ));
			/*
			 * Récuperation des participants dans l'application
			 */
		
	
			/*
			 * Chargement de la vue qui va s'afficher
			 *
			 */
			$data['page']='relis/h_screening';
			$this->load->view('body',$data);
	
		
	}
	

	
	public function qa()//quality assessment
	{
		$project_published=project_published();
		//update_paper_status_all();
		//$this->session->set_userdata('working_perspective','screen');
	
		$completion=$this->manager_lib->get_qa_completion('QA');
	
		$general_completion=$completion['general_completion'];
		$user_completion=$completion['user_completion'];
		//print_test($user_completion);
		$active_user_id=active_user_id();
		if(!empty($user_completion[$active_user_id]['all'])){
			
			$data['qa_completion']['title']="My completion";
			$data['qa_completion']['all_papers']=array('value'=>$user_completion[$active_user_id]['all'],
					'title'=>'All',
					'url'=>'relis/manager/qa_conduct_list'
			);
			$data['qa_completion']['pending_papers']=array('value'=>!empty($user_completion[$active_user_id]['pending'])?$user_completion[$active_user_id]['pending']:0,
					'title'=>'Pending',
					'url'=>'relis/manager/qa_conduct_list/mine/0/pending'
			);
			$data['qa_completion']['done_papers']=array('value'=>!empty($user_completion[$active_user_id]['done'])?$user_completion[$active_user_id]['done']:0,
					'title'=>'Done',
					'url'=>'relis/manager/qa_conduct_list/mine/0/done'
			);
			
		
			$data['qa_completion']['gauge_all']=$user_completion[$active_user_id]['all'];
			$data['qa_completion']['gauge_done']=!empty($user_completion[$active_user_id]['done'])?$user_completion[$active_user_id]['done']:0;
		}
		
		if(!empty($general_completion['all'])){
				
			$data['gen_qa_completion']['title']="Overall completion";
			$data['gen_qa_completion']['all_papers']=array('value'=>$general_completion['all'],
					'title'=>'All',
					'url'=>'relis/manager/qa_conduct_list/all'
			);
			$data['gen_qa_completion']['pending_papers']=array('value'=>!empty($general_completion['pending'])?$general_completion['pending']:0,
					'title'=>'Pending',
					'url'=>'relis/manager/qa_conduct_list/all/0/pending'
			);
			$data['gen_qa_completion']['done_papers']=array('value'=>!empty($general_completion['done'])?$general_completion['done']:0,
					'title'=>'Done',
					'url'=>'relis/manager/qa_conduct_list/all/0/done'
			);
				
		
			$data['gen_qa_completion']['gauge_all']=$general_completion['all'];
			$data['gen_qa_completion']['gauge_done']=!empty($general_completion['done'])?$general_completion['done']:0;
		}
		
		
		if(get_appconfig_element('qa_validation_on')){
		
		$completion_val=$this->manager_lib->get_qa_completion('QA_Val');
		//print_test($completion_val);
		$general_completion_val=$completion_val['general_completion'];
		$user_completion_val=$completion_val['user_completion'];
		}
		
		if(!empty($user_completion_val[$active_user_id]['all'])){
				
			$data['qa_completion_val']['title']="My validation completion";
			$data['qa_completion_val']['all_papers']=array('value'=>$user_completion_val[$active_user_id]['all'],
					'title'=>'All',
					'url'=>'relis/manager/qa_conduct_list_val'
			);
			$data['qa_completion_val']['pending_papers']=array('value'=>!empty($user_completion_val[$active_user_id]['pending'])?$user_completion_val[$active_user_id]['pending']:0,
					'title'=>'Pending',
					'url'=>'relis/manager/qa_conduct_list_val/mine/0/pending'
			);
			$data['qa_completion_val']['done_papers']=array('value'=>!empty($user_completion_val[$active_user_id]['done'])?$user_completion_val[$active_user_id]['done']:0,
					'title'=>'Done',
					'url'=>'relis/manager/qa_conduct_list_val/mine/0/done'
			);
				
		
			$data['qa_completion_val']['gauge_all']=$user_completion_val[$active_user_id]['all'];
			$data['qa_completion_val']['gauge_done']=!empty($user_completion_val[$active_user_id]['done'])?$user_completion_val[$active_user_id]['done']:0;
		}
		
		if(!empty($general_completion_val['all'])){
		
			$data['gen_qa_completion_val']['title']="Overall validation completion";
			$data['gen_qa_completion_val']['all_papers']=array('value'=>$general_completion_val['all'],
					'title'=>'All',
					'url'=>'relis/manager/qa_conduct_list_val/all'
			);
			$data['gen_qa_completion_val']['pending_papers']=array('value'=>!empty($general_completion_val['pending'])?$general_completion_val['pending']:0,
					'title'=>'Pending',
					'url'=>'relis/manager/qa_conduct_list_val/all/0/pending'
			);
			$data['gen_qa_completion_val']['done_papers']=array('value'=>!empty($general_completion_val['done'])?$general_completion_val['done']:0,
					'title'=>'Done',
					'url'=>'relis/manager/qa_conduct_list_val/all/0/done'
			);
		
		
			$data['gen_qa_completion_val']['gauge_all']=$general_completion_val['all'];
			$data['gen_qa_completion_val']['gauge_done']=!empty($general_completion_val['done'])?$general_completion_val['done']:0;
		}
	
		
		
		$action_but=array();
		if(can_manage_project() AND !$project_published)
			$action_but['assign_screen']=get_top_button ( 'all', 'Assign papers for QA', 'relis/manager/qa_assignment_set','Assign papers','fa-mail-forward','',' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ' ,False);
		
			if(can_review_project() AND !$project_published)
				$action_but['screen']=get_top_button ( 'all', 'Classify', 'relis/manager/qa_conduct_list/mine/0/pending','Assess','fa-search','',' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ' ,False);
					
				
					//$action_but['screen_result']=get_top_button ( 'all', 'Screening progress', 'relis/manager/screen_completion','Progress','fa-tasks','',' btn-info action_butt col-md-2 col-sm-2 col-xs-12 ' ,False);
				$action_but['screen_completion']=get_top_button ( 'all', 'Result', 'relis/manager/qa_conduct_result','Result','fa-th','',' btn-info action_butt col-md-3 col-sm-3 col-xs-12 ' ,False);
				
					
				$data['action_but_screen']=$action_but;
					
				$action_but=array();
				if(get_appconfig_element('qa_validation_on') ){
					if(can_validate_project() AND !$project_published){
						$action_but['assign_screen']=get_top_button ( 'all', 'Assign papers for validation', 'relis/manager/qa_assignment_validation_set','Assign papers','fa-mail-forward','',' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ' ,False);
						$action_but['screen']=get_top_button ( 'all', 'Validate', 'relis/manager/qa_conduct_list_val/mine/0/pending','Validate','fa-check-square-o','',' btn-primary action_butt col-md-3 col-sm-3 col-xs-12 ' ,False);
					}
					
					$action_but['screen_completion']=get_top_button ( 'all', 'Result', 'op/entity_list/list_qa_validation','Result','fa-th','',' btn-primary action_butt  col-md-3 col-sm-3 col-xs-12' ,False);
		
					$data['action_but_validate']=$action_but;
		
				}
		
		
		if(!($this->session->userdata ( 'project_db' ))){
	
			redirect('manager/projects_list');
		}
	
		if($this->session->userdata('working_perspective')=='class'){
			redirect('home');
		}
		
		$data['configuration']=get_project_config($this->session->userdata ( 'project_db' ));
				/*
				 * Chargement de la vue qui va s'afficher
				 *
				 */
				$data['page']='relis/h_qa';
				$this->load->view('body',$data);
	
	
	}
	
	function get_user_completion($user_id,$screening_phase,$phase_type='Screening'){
		
		$my_assignations=$this->Relis_mdl->get_user_assigned_papers($user_id,$phase_type,$screening_phase);
			$total_papers=count($my_assignations);
		$papers_screened=0;
		$conflicts=0;
		foreach ($my_assignations as $key => $value) {
				
			if($value['screening_status']=='Done'){
				$papers_screened++;
				if($value['paper_status']=='In conflict'){
					$conflicts++;
				}
			}
		}
		$result['all_papers']=$total_papers;
		$result['papers_done']=$papers_screened;
		$result['papers_in_conflict']=$conflicts;
		return $result;
	}
	
	public function screening_select()
	{
		$project_published=project_published();
		//debug_comment_diaplay();
		$screening_phases = $this->db_current->order_by('screen_phase_order', 'ASC')
												->get_where('screen_phase', array('screen_phase_active'=>1))
												->result_array();
		$this->session->set_userdata('working_perspective','screen');
		$phases_list=array();
		$yes_no=array('0'=>'','1'=>'Yes');
		$i=1;
		
		if(get_appconfig_element('screening_on')){
			foreach ($screening_phases as $k => $phase) {
				
				//print_test($phase);
				
			//	print_test($phase);
				$select_but="";
				$open_but="";
				$close_but="";
				
				$user_completion=$this->get_user_completion(active_user_id(), $phase['screen_phase_id'],'');
				if(!empty($user_completion['all_papers'])){
					$user_perc=!empty($user_completion['all_papers'])?round((($user_completion['papers_done']-$user_completion['papers_in_conflict'])*100 / $user_completion['all_papers']),2)." %":'-';
				}else{
					$user_perc="-";
				}
				
				
				$user_completion=$this->get_user_completion(0, $phase['screen_phase_id'],'');
				if(!empty($user_completion['all_papers'])){
					$gen_perc=!empty($user_completion['all_papers'])?round((($user_completion['papers_done']-$user_completion['papers_in_conflict'])*100 / $user_completion['all_papers']),2)." %":'-';
				}else{
					$gen_perc="-";
				}
				
				//classification completion
				
				$all_papers=$this->DBConnection_mdl->count_papers('all');
				$processed_papers=$this->DBConnection_mdl->count_papers('processed');
				if(!empty($all_papers)){
					$class_perc=!empty($all_papers)?round(($processed_papers*100 / $all_papers),2)." %":'-';
				}else{
					$class_perc="-";
				}
				
				if($phase['phase_state']=='Open'){
					$select_but=get_top_button ( 'all', 'Go to the phase', 'home/select_screen_phase/'.$phase['screen_phase_id'],'Go to','fa-send','',' btn-info ' ,False);
					$close_but=get_top_button ( 'all', 'Lock the phase', 'home/screening_phase_manage/'.$phase['screen_phase_id'].'/2','Close','fa-lock','',' btn-danger ' ,False);
				}else{
					
					$open_but=get_top_button ( 'all', 'Unlock the phase', 'home/screening_phase_manage/'.$phase['screen_phase_id'],'Open','fa-unlock','',' btn-success ' ,False);					
				}	
				
				if(!can_manage_project() OR $project_published){
					$close_but="";
					$open_but="";
				}
				$temp=array(
				//		'num'=>$i,
						
						'Title'=>"Screening : ".$phase['phase_title'],
						'State'=>$phase['phase_state'],
					//	'Final phase'=>$yes_no[$phase['screen_phase_final']],
						'User_completion'=>$user_perc,
						'Gen_completion'=>$gen_perc,
						'action'=>$open_but.$close_but.$select_but,
				);
				array_push($phases_list, $temp);
				
				$i++;
			}
		}
		
		//quality assessment
		
		
		if(get_appconfig_element('qa_on')){
			
			$active_user_id=active_user_id();
			$completion=$this->manager_lib->get_qa_completion('QA');
			
			//print_test($completion);
			
			$general_completion=$completion['general_completion'];
			$user_completion=$completion['user_completion'];
			if(!empty($general_completion['all'])){
				$done=(!empty($general_completion['done']))?$general_completion['done']:0;
				$gen_qa_perc=!empty($general_completion['all'])?round(($done*100 / $general_completion['all']),2)." %":'-';
			}else{
				$gen_qa_perc="-";
			}
			
			if(!empty($user_completion[$active_user_id]['all'])){
				$done=(!empty($user_completion[$active_user_id]['done']))?$user_completion[$active_user_id]['done']:0;
				$usr_qa_perc=!empty($user_completion[$active_user_id]['all'])?round(($done*100 / $user_completion[$active_user_id]['all']),2)." %":'-';
			}else{
				$usr_qa_perc="-";
			}
			
			$select_but="";
			$open_but="";
			$close_but="";
			if(get_appconfig_element('qa_open')){
				$select_but=get_top_button ( 'all', 'Go to QA', 'manager/set_perspective/qa','Go to','fa-send','',' btn-info ' ,False);
				$close_but=get_top_button ( 'all', 'Lock the phase', 'manager/activate_qa/0','Close','fa-lock','',' btn-danger ' ,False);
				$qa_state="Open";
			}else{
				$open_but=get_top_button ( 'all', 'Unlock the phase', 'manager/activate_qa','Open','fa-unlock','',' btn-success ' ,False);
				$qa_state="Closed";
			}
			
			if(!can_manage_project() OR $project_published){
				$close_but="";
				$open_but="";
			}
			$qa=array(
			//		'num'=>$i,
					'Title'=>'Quality assessment',
					'State'=>$qa_state,
					//'Final phase'=>'',
					'User_completion'=>$usr_qa_perc,
					'Gen_completion'=>$gen_qa_perc,
					'action'=>$open_but.$close_but.$select_but,
			);
			array_push($phases_list, $qa);
			
			$i++;
		}
		
		
		//classification completion
			
		$all_papers=$this->DBConnection_mdl->count_papers('all');
		$processed_papers=$this->DBConnection_mdl->count_papers('processed');
		if(!empty($all_papers)){
			$class_perc=!empty($all_papers)?round(($processed_papers*100 / $all_papers),2)." %":'-';
		}else{
			$class_perc="-";
		}
		$my_class_completion=$this->get_classification_completion('class','');
		
		if(!empty($my_class_completion['all_papers'])){
			$class_perc_mine=!empty($my_class_completion['all_papers'])?round(($my_class_completion['processed_papers']*100 / $my_class_completion['all_papers']),2)." %":'-';
		}else{
			$class_perc_mine="-";
		}
		
		//add clasificsation phase
		$select_but="";
		$open_but="";
		$close_but="";
		
		if(get_appconfig_element('classification_on')){
			$select_but=get_top_button ( 'all', 'Go to classification', 'manager/set_perspective/class','Go to','fa-send','',' btn-info ' ,False);
			$close_but=get_top_button ( 'all', 'Lock the phase', 'manager/activate_classification/0','Close','fa-lock','',' btn-danger ' ,False);
			$class_state="Open";
		}else{
			$open_but=get_top_button ( 'all', 'Unlock the phase', 'manager/activate_classification','Open','fa-unlock','',' btn-success ' ,False);				
			$class_state="Closed";
		}
		
		if(!can_manage_project() OR $project_published){
			$close_but="";
			$open_but="";
		}
		
		$class=array(
			//	'num'=>$i,
				'Title'=>'Classification',
				'State'=>$class_state,
				//'Final phase'=>'',
				'User_completion'=>$class_perc_mine,
				'Gen_completion'=>$class_perc,
				'action'=>$open_but.$close_but.$select_but,
		);
		array_push($phases_list, $class);
		
		
		
		
		
		
		if(!empty($phases_list)){
		//	array_unshift($phases_list, array('#','Title','State','Screening final phase','My completion','General completion'));
			array_unshift($phases_list, array(lng('Phases'),lng('State'),lng('My completion'),lng('Overall  completion')));
		}
		
	//	print_test($phases_list);
		$data['phases_list']=$phases_list;
		
			$data['configuration']=get_project_config($this->session->userdata ( 'project_db' ));
			/*
			 * Récuperation des participants dans l'application
			 */
			$data['users']=$this->DBConnection_mdl->get_users_all();
			
			foreach ($data['users'] as $key => $value) {
				
				if(! (user_project($this->session->userdata('project_id'),$value['user_id']))){
					unset($data['users'][$key]);
				}else{
					$data['users'][$key]['usergroup_name']=get_user_role($data['users'][$key]['user_id']);
				}
			}
	//print_test($data['users']);
			/*
			 * Chargement de la vue qui va s'afficher
			 *
			 */
			
			//publish project
			$data['top_buttons']="";
			if(has_user_role('Project admin') OR has_usergroup(1)){
				if(project_published())
				{
					$publish_but=get_top_button ( 'all', 'Reopen project',
								'admin/publish_project/0/0','Reopen project',
							' fa-folder-open ','', ' btn-warning ' ,False);
				}else{  
					$publish_but=get_top_button ( 'all', 'Publish project',
							'admin/publish_project','Publish project',
							'fa-send','',' btn-info ' ,False);
					
				}
					$data['top_buttons']=$publish_but;
			}
			
			
			$this->session->set_userdata('current_screen_phase','');
			
			$data['page']='relis/h_screening_select';
			$this->load->view('body',$data);
			
	
		
	}
	
	public function select_screen_phase($screen_phase_id){
		
		if(!empty($screen_phase_id)){
			$this->session->set_userdata('current_screen_phase',$screen_phase_id);
			redirect('home/screening');
		}else{
		
			redirect('home/screening_select');
		}
	}
	
	public function screening_phase_manage($screen_phase_id,$op=1){
		if($op==1)//open the phase
		{
			$State='Open';
		}else{
			$State='Closed';
			}
			
			$res = $this->db_current->update ( 'screen_phase', array('phase_state'=>$State), array (
					'screen_phase_id' =>$screen_phase_id
			) );
			
			redirect('home/screening_select');
	}
	
	
	public function choose_project(){
		
		
		redirect('manager/projects_list');
		
	
		
	}
	
	
	public function set_project($projet_label,$project_id=0,$project_title=""){
		if(!empty($projet_label)){
			$this->session->set_userdata('project_db',$projet_label);
			$this->session->set_userdata('project_id',$project_id);
			$this->session->set_userdata('project_title',urldecode ( urldecode ($project_title)));
			
		}
		
		redirect('home/screening');
	}
	 
	/*
	 * Fonction appélé par le bouton de changement de langue
	 */
	public function change_lang(){
		
		if($this->session->userdata('active_language') AND $this->session->userdata('active_language')=='fr' ){
		
			$this->session->set_userdata('active_language','en');
		}else{
			$this->session->set_userdata('active_language','fr');
		}
		
	}
	
	//Fonction pour mettre à jours les  stored procedures après modification de la configuration 
	public function update_stored_procedure($config="all"){
		
		if($config=='all'){
		$configs=array('author','venue','users','usergroup','papers','classification','exclusion','assignation','paper_author','logs','str_mng','config','user_project');
		$reftables=$this->DBConnection_mdl->get_reference_tables_list();
		
		foreach ($reftables as $key => $value) {
			array_push($configs, $value['reftab_label']);
		}
		
		}else{
			$configs=array($config);
			
		}
		
		print_test($configs);
		
		foreach ($configs as $k => $config) {
			
				/*
				 * Stored procedure to get list of element
				 */
				$this->manage_stored_procedure_lib->create_stored_procedure_get($config);
				
				/*
				 * Stored procedure to count number of elements (used for navigation link)
				 */
				if($config=='papers')
				$this->manage_stored_procedure_lib->create_stored_procedure_count($config);
				
				/*
				 * Stored procedure to remove element
				 */
				$this->manage_stored_procedure_lib->create_stored_procedure_remove($config);
			
				/*
				 * Stored procedure to add element
				 */
				$this->manage_stored_procedure_lib->create_stored_procedure_add($config);
			
				
				/*
				 * Stored procedure to update element
				 */
				$this->manage_stored_procedure_lib->create_stored_procedure_update($config);
				
				/*
				 * Stored procedure to get detail element (select row)
				 */
				$this->manage_stored_procedure_lib->create_stored_procedure_detail($config);
			
		}
		
		///do not forget to add the stored procedure for papers : assigned, processed, and pending and update the 
		
	}
	
	
	
	public function create_table_config($config,$target_db='current'){
		
		$res=$this->manage_stored_procedure_lib->create_table_config(get_table_config($config),$target_db);
		
		echo $res;
	
	}
	
	/*
	 * Utilisé pour  test lorsque on veux mettre des valeurs de test dans la classification
	 */
	public function test_values(){
		
		$i=1;
		
		for($i=1;$i<=1;$i++){
			/*
			 * Préparation des valeurs qui sont générés de façon aléatoire
			 
			$fields=array(
					
					'number_citation'=>rand(2 ,206)
					
		
			);
		
			print_test($fields);
		*/
			/*
			 * update des données
			 */
			//$headersaved = $this->db_current->update ( 'classification', $fields,array('class_paper_id'=>$i) );
			//print_test($headersaved);
		}
		
		$i=1;
		
		for($i=16;$i<=20;$i++){
		/*
		 * Préparation des valeurs qui sont générés de façon aléatoire
		 */	
		$fields=array(
			'class_paper_id'=>$i,	
			'transformation_name'=>"Test transformation $i",	
			'domain'=>rand(1 , 5),	
			//'trans_language'=>rand(1,4 ),	
			'source_language'=>rand(1,4 ),	
			'target_language'=>rand(1 , 4),	
			//'scope'=>rand(1 , 3),	
			'industrial'=>rand(0 ,1 ),	
			'bidirectional'=>rand(0 ,1),	
			'year'=>rand(2011 ,2016),	
			'number_citation'=>rand(2 ,2016),	
			'user_id'=>1	
				
		);
		
		//print_test($fields);
		
		/*
		 * Insertion des données
		 */
		$headersaved = $this->db_current->insert ( 'classification', $fields );
		print_test($headersaved);
		}
		
		$i=1;
		
		for($i=16;$i<=20;$i++){
			/*
			 * Préparation des valeurs qui sont générés de façon aléatoire
			 */
			
			$intent_numbers=rand(1,3);
			$j=1;
			for($j=1;$j<=$intent_numbers;$j++){
			$fields=array(
					'parent_field_id'=>$i,
					'name_used'=>"Intent $i $j",
					'intent'=>rand(1 , 4),
					'line_code'=>rand(2000,50000 ),
					'op_result'=>rand(1 , 3),				
		
			);
		
			//print_test($fields);
		
			/*
			 * Insertion des données
			 */
			$headersaved = $this->db_current->insert ( 'intent', $fields );
			print_test($headersaved);
			}
		}
		
		$i=1;
		
		for($i=16;$i<=20;$i++){
			/*
			 * Préparation des valeurs qui sont générés de façon aléatoire
			 */
				
			$intent_numbers=rand(1,4);
			$j=1;
			for($j=1;$j<=$intent_numbers;$j++){
				$fields=array(
						'parent_field_id'=>$i,
						'trans_language'=>rand(2 , 4),
						'trans_language'=>$j
		
				);
		
				//print_test($fields);
		
				/*
				 * Insertion des données
				 */
				$headersaved = $this->db_current->insert ( 'trans_language', $fields );
				print_test($headersaved);
			}
		}
	
	}
	
	
	public function test_icse(){
	
		$sql= "SELECT * FROM  `paper` WHERE  `classification_status` =  'To classify' AND  `paper_active` =1  ";
		
		$i=1;
		$pred=array(
				0=>'Predefined',
				1=>'Output-based',
				2=>'Rule-based',
		);
		$res=$this->db_current->query($sql)->result_array();
		foreach ($res as $key => $value) {
			$paper_id=$value['id'];
			
			//print_test($value);
			$temp=rand(0,2);
			$fields=array(
					'class_paper_id'=>$paper_id,
					'year'=>rand(2011,2017 ),
					'Tool'=>rand(1,5 ),
					'template_style'=>$pred[$temp],
					'user_id'=>rand(16,17)
			
			);
			
			print_test($fields);
			
			/*
			 * Insertion des données
			 */
			$headersaved = $this->db_current->insert ( 'classification', $fields );
			print_test($headersaved);
		}
		
	/*
		for($i=287;$i<=367;$i++){
			 
			$temp=rand(0,2);
			$fields=array(
					'class_paper_id'=>$i,
					'year'=>rand(2011,2017 ),
					'Tool'=>rand(1,5 ),
					'template_style'=>$pred[$temp],
					'user_id'=>rand(16,17)
	
			);
	
			print_test($fields);

			
			//$headersaved = $this->db_current->insert ( 'classification', $fields );
			//print_test($headersaved);
		}
	
		*/
	
	}
	
	
	
	/*
	 * Affichage des résultat(statistique)  en cours de réaliation------
	 */
	public function result(){
		
		old_version();
		//save_metrics("bricetest metrics");
	
		/*
		 * Recupération du nombre de papiers par catégories
		 */
		$data['all_papers']=$this->DBConnection_mdl->count_papers('all');
		$data['processed_papers']=$this->DBConnection_mdl->count_papers('processed');
		$data['pending_papers']=$this->DBConnection_mdl->count_papers('pending');
		$data['assigned_me_papers']=$this->DBConnection_mdl->count_papers('assigned_me');
		$data['excluded_papers']=$this->DBConnection_mdl->count_papers('excluded');
		
		
		/*
		 * Stucture de la table des classification
		 */
		$table_config = $this->table_ref_lib->ref_table_config('classification');
		
		//print_test($table_config);
		
		$result_fin=array();
		
		foreach ($table_config['fields'] as $key_conf => $value_conf) {
		
			//if(!empty($value_conf['compute_result']) AND $value_conf['compute_result']=='yes' AND ($value_conf['input_type'] =='select') AND ($value_conf['input_select_source'] =='table') ){
			
				if(isset($value_conf['number_of_values']) AND ($value_conf['number_of_values']=='1' OR $value_conf['number_of_values']=='0') AND ($value_conf['input_type'] =='select') AND ($value_conf['input_select_source'] =='table' OR $value_conf['input_select_source'] =='array'  OR $value_conf['input_select_source'] =='yes_no' )  ){
					//print_test($value_conf);
					$ref_field=$key_conf;
					if($value_conf['input_select_source'] =='array'){
						$result= $this->manage_mdl->get_result_classification($key_conf);
						foreach ($result as $key => $value) {
							$result[$key]['field_desc'] =$value['field'] ;
						}
							
					}elseif($value_conf['input_select_source'] =='yes_no'){
						
						$result= $this->manage_mdl->get_result_classification($key_conf);
						
						$yes_no=array("False",'True');
						foreach ($result as $key => $value) {
							$result[$key]['field_desc'] =$yes_no[$value['field'] ];
						}
					
					}else{
					
					$conf=explode(";", $value_conf['input_select_values']);
					
					
					$ref_config=$conf[0];
					
					$ref_table=$this->DBConnection_mdl->get_reference_corresponding_table($ref_config);
					
					$ref_table_name=$ref_table['reftab_table'];
					
					$ref_table_desc=$ref_table['reftab_desc'];
					
						
					$result= $this->manage_mdl->get_result_classification($ref_field);
					
								
					foreach ($result as $key => $value) {
							
							$result[$key]['field_desc'] = $this->manage_mdl->get_reference_value($ref_table_name,$result[$key]['field']) ;
							
							
					}
						
					}
					
					$result_fin[$ref_config.$key_conf]['name']=$value_conf['field_title'];
					$result_fin[$ref_config.$key_conf]['field_name']=$ref_field;
					$result_fin[$ref_config.$key_conf]['rows']=$result;
					
					
					//print_test($result);
					}
		
			}
			
		//print_test($result_fin);
		
			
			/*
			 * La page contient des graphique cette valeur permettra le chargement de la librarie highcharts  
			 */
			$data['has_graph']='yes';
			
			
			$data['result_table']=$result_fin;
			$data['page']='result';
			$this->load->view('body',$data);
			//$this->load->view('welcome_message');
			
			
			
			
			
		}
		
		
		/*
		 * Page permettant de saisir une requette sql et avoir le résultat
		 */
		public function sql_query($query_type="single"){
			
			
			$data['return_table']=1;
			$data['query_type']=$query_type;
			
			/*
			 * La vue qui va s'afficher
			 */
			if($query_type!='multi'){
			$data ['top_buttons'] = get_top_button ( 'all', 'Switch to multi query!', 'home/sql_query/multi','Switch to multi query!',' fa-exchange','',' btn-info ' );
			$data['title']='Query database - single SQL query';
			}else{
				$data ['top_buttons'] = get_top_button ( 'all', 'Switch to single query!', 'home/sql_query/','Switch to single query!',' fa-exchange','',' btn-info ' );
				$data['title']=lng_min('Query database - multiple SQL queries');
			}	
			$data['page']='sql';
			$this->load->view('body',$data);
		}
	
		/*
		 * Page de traitement de requete sql saisie et affichade du résultat
		 */
		public function sql_query_response(){
			
			/*
			 * Récupération de la réquette saisier
			 */
			$post_arr = $this->input->post ();
			//print_test($post_arr); 
			
			$sql="";
			$sql=$post_arr['sql_field'];
			$query_type=$post_arr['query_type'];
			
			/*
			 * Verification si il faut afficher le résultat ou pas
			 */
			if(isset($post_arr['return_table'])){
				$return_table=1;
			}else{
				$return_table=0;
			}
			$data['query_type']=$query_type;
			if(!empty($sql)){
				$data['sql_field']=$sql;
				$data['return_table']=$return_table;
			
				/*
				 * Appel du model manage_mdl->run_query  pour executer la requette et recuperer le resultat
				 */
				$pre_select_sql=" select* from ( ";
				$post_select_sql=" ) as T ";
				if($query_type!='multi'){
				if(! has_usergroup(1)){
					//if used is not super admin he can just execute select queries
					$sql =$pre_select_sql . $sql . $post_select_sql;
				}
				
				$res = $this->manage_mdl->run_query($sql,$return_table);
				}else{
					$delimiter=$post_arr['delimiter'];
					$T_queries=explode(!empty($delimiter)?$delimiter:';', $sql);
					//print_test($T_queries);
					$error=0;
					$all=0;
					$t_error_message=" ";
					foreach ($T_queries as $key => $v_sql) {
						$v_sql=trim($v_sql);
						if(!empty($v_sql)){
							if(! has_usergroup(1)){
								//if used is not super admin he can just execute select queries
								$v_sql =$pre_select_sql . $v_sql . $post_select_sql;
							}
							$T_res = $this->manage_mdl->run_query($v_sql);
							if($T_res['code']!=0){
								$error++;
								$t_error_message .= " <br/> - ".$T_res['message'];
							}
							$all++;
						}
						
					}
					
					if($error==0){
						$res['code']=0;
						$res['message']=$all .' query executed!';
					}else{
						$res['code']=1;
						$res['message']=($all- $error) ." Succeded - $error Errors<br/>".$t_error_message;
					}
				}
			}else{
				$res['code']=1;
				$res['message']=lng_min('Query was empty');
				
			}
		//	print_test($res);
			
			
			if($res['code']==0){//L'execution de la requette a réussit
				
				/*	
				 * Péparation du résultat à afficher
				 */
				
				$data['message_success']="Success";
				$data['message_error']="";
				$array_header=array();
				if($return_table ){
					$data['display_list']="OK";
					if( ! empty($res['message']) AND is_array($res['message']) AND count($res['message'])>0){
				
					
					foreach ($res['message'][0] as $key => $value) {
						
						array_push($array_header, $key);
					}
					
					array_unshift($res['message'],$array_header);
					
					$data['list']=$res['message'];
					
					
				}
				}
				
			}else{ //L'execution de la requette a echoué
				
				/*
				 * Préparation du message d'erreur à afficher
				 */
				$data['message_error']="Error: ".$res['message'] ;
				$data['message_success']="";
				
			}
			if($query_type!='multi'){
				$data ['top_buttons'] = get_top_button ( 'all', 'Switch to multi query!', 'home/sql_query/multi','Switch to multi query!',' fa-exchange','',' btn-info ' );
				$data['title']=lng_min('Run SQL query');
			}else{
				$data ['top_buttons'] = get_top_button ( 'all', 'Switch to single query!', 'home/sql_query/','Switch to single query!',' fa-exchange','',' btn-info ' );
				$data['title']=lng_min('Run multiple SQL queries');
			}
			
			
			
			$data['page']='sql';
			
			
			$this->load->view('body',$data);
		}
		
		
		public function export($type=1){
		
			$data['t_type']=$type;
			
			$data ['page_title'] = lng('Exports');
			$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'home' );
			$data['left_menu_perspective']='z_left_menu_screening';
			$data['project_perspective']='screening';
			$data ['page'] = 'export';
		
		
		
			/*
			 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
			 */
			$this->load->view ( 'body', $data );
		}
		
		
		//test fonction used to merge my csv file with the screening result
		private function screen_mine(){
			
			echo "brice";
			$all_file="cside/screen/all.csv";
			$accept_file="cside/screen/accepted.txt";
			$Taccepeted=array();
			$Tall=array();
			ini_set('auto_detect_line_endings',TRUE);
			
			$fp = fopen($all_file, 'rb');	
			$i=1;
			$last_count=0;
			while ( (($Tline = (fgetcsv($fp,0,";",'"')))) !== false) {
				$Tline = array_map( "utf8_encode", $Tline );
				//print_test($Tline);
				
				$Tall[$i]= $Tline;
				$i++;
				if($i==1000)
					exit;
			}
			//print_test($Tall);
			//exit;
			$fa = fopen($accept_file, 'rb');
			$i=1;
			$last_count=0;
			while ( (($Tline = (fgetcsv($fa,0,"$",'"')))) !== false) {
				$Tline = array_map( "utf8_encode", $Tline );
				//print_test($Tline);
			
				$Taccepeted[$i]= $Tline;
				$i++;
				if($i==1000)
					exit;
			}
			//echo count($Taccepeted);
			//print_test($Taccepeted);
			
			$final_added=array();
			//mapping
			$j=1;
			foreach ($Taccepeted as $key => $value) {
				$title=trim($value[0]);
				$result="not fund";
				foreach ($Tall as $key_all => $value_all) {
					if($title == trim($value_all[1]))
					{
						$result="found";
						$final_added[$j]=$value_all;
					}
				}
				
				echo " <h3>$j - $result</h3>";
				$j++;
			}
			
			print_test($final_added);
			
			$f_new = fopen("cside/screen/paper_to_classify.csv", 'w+');
			foreach ($final_added as $val) {
				fputcsv($f_new, $val,";");
			}
			
			
			fclose($f_new);
		}
		
		public function  metrics_view(){
			echo "<h1>list of files</h1>";
			
			$dir="C:/xampp/htdocs/relis/relis_multi_gen_01/cside/metrics_new";
			$dir="C:/xampp/htdocs/relis/relis_multi_gen_01/cside/metrics_new";
		
			if(is_dir($dir)){
				$files = array_diff(scandir($dir), array('.', '..',".metadata"));
				//$files = scandir($dir);
				//print_test($files);
				foreach ($files as $key => $value) {
					//directories per day
					$dir_f=$dir."/".$value;
					echo "<h2>$value</h2>";
					if(is_dir($dir_f)){
						$files_f = array_diff(scandir($dir_f), array('.', '..',".metadata"));
						foreach ($files_f as $key_f => $value_f) {
							
							
							if(strrpos($value_f, "dmin_") != '1' AND strrpos($value_f, "ser_unknown") != '1' ){
							$file=$dir."/".$value."/".$value_f;
							
							echo "<h2>".$file."</h2>";
							$this->metrics_file_content($file);
							}
						}
						//print_test($files_f);
					}else{
						echo"<p>nop inside</p>";
					}
					
					
				}
				
				
			}else{
				echo"nop";	
			}
		}
		
		public function metrics_file_content($file="C:/xampp/htdocs/relis/relis_multi_gen_01/cside/metrics_new/2016_Dec_10/pierre_13.txt"){
			//$file="C:/xampp/htdocs/relis/relis_multi_gen_01/cside/metrics_new/2016_Dec_11/younous_18.txt";
			
			ini_set('auto_detect_line_endings',TRUE);
				
			$fp = fopen($file, 'rb');
			$i=1;
			$last_count=0;
			$choosen_metrics=array();
			while  ( (($line = (fgets($fp)))) !== false) {
				
				$Tline=explode("__--~~", $line);
				
				$metrics=json_decode($Tline['2'],true);
				if(isset($metrics['server_info']['HTTP_USER_AGENT'])){
					
					//print_test($this->getBrowser($metrics['server_info']['HTTP_USER_AGENT']));
				}
				
				//print_test($metrics);
				$choosen_metrics['time']=isset($metrics['server_info']['REQUEST_TIME'])?$metrics['server_info']['REQUEST_TIME']:"";
				$client=isset($metrics['server_info']['HTTP_USER_AGENT'])? $this->getBrowser($metrics['server_info']['HTTP_USER_AGENT']) :"";
				$choosen_metrics['browser']=isset($client['name']) ? $client['name'] : "";
				$choosen_metrics['system']=isset($client['platform']) ? $client['platform'] : "";
				$choosen_metrics['page_url_source']=isset($metrics['server_info']['HTTP_REFERER'])?$metrics['server_info']['HTTP_REFERER']:"";
				$choosen_metrics['page_url']=isset($metrics['server_info']['REDIRECT_URL'])?$metrics['server_info']['REDIRECT_URL']:"";
				$choosen_metrics['status']=isset($metrics['server_info']['REDIRECT_STATUS'])?$metrics['server_info']['REDIRECT_STATUS']:"";
				$choosen_metrics['method']=isset($metrics['server_info']['REQUEST_METHOD'])?$metrics['server_info']['REQUEST_METHOD']:"";
				$choosen_metrics['user']=isset($metrics['session']['user_id'])?$metrics['session']['user_id']:"";
				$choosen_metrics['project']=isset($metrics['session']['project_db'])?$metrics['session']['project_db']:"admin";
				$choosen_metrics['screen_height']=isset($metrics['session']['screen_height'])?$metrics['session']['screen_height']:"";
				$choosen_metrics['screen_width']=isset($metrics['session']['screen_width'])?$metrics['session']['screen_width']:"";
			//	$choosen_metrics['profiler']=$metrics['profiler'];
				$choosen_metrics['metric_id']="";
				
				
				/*
				$pos_start=strrpos($metrics['profiler'],$start);
				$pos_end=strrpos($metrics['profiler'],$end);
				
				$got =substr($metrics['profiler'],$pos_start + strlen($start), $pos_end - $pos_start - strlen($start));
				echo "<h1>sss $got </h1>";
				*/
				if(!strstr($choosen_metrics['page_url'],'add_screen_size')){
					
					$start="COMPILE_CONTROLLER<div>";
					$end="</div></fieldset></div>";
					
					$pos_start=strrpos($metrics['profiler'],$start);
					$pos_end=strrpos($metrics['profiler'],$end);
					
					$choosen_metrics['page'] =substr($metrics['profiler'],$pos_start + strlen($start), $pos_end - $pos_start - strlen($start));
					
					$start="MEMORY_USAGE ";
					$end=" bytes</fieldset>";
					
					$pos_start=strrpos($metrics['profiler'],$start);
					$pos_end=strrpos($metrics['profiler'],$end);
					$choosen_metrics['memory_usage'] =str_replace(",", "", substr($metrics['profiler'],$pos_start + strlen($start), $pos_end - $pos_start - strlen($start)));
					
					
					$start="Total Execution Time</td><td>";
					$end="</td></tr></table>";
					
					$pos_start=strrpos($metrics['profiler'],$start);
					$pos_end=strrpos($metrics['profiler'],$end);
					$choosen_metrics['execution_time'] =substr($metrics['profiler'],$pos_start + strlen($start), $pos_end - $pos_start - strlen($start));
					
					
				print_test($choosen_metrics);
				
				
					$this->db4 = $this->load->database("spl", TRUE);
					$this->db4->insert ( 'metrics', $choosen_metrics );
				
				
				}
				
			}
		
		}
		
		
		public function  getStat(){
			$this->db4 = $this->load->database("spl", TRUE);
			$sql="SELECT DISTINCT page , count(*) as nombre from metrics GROUP BY page ORDER BY nombre DESC";
			//$sql="SELECT DISTINCT user , count(*) as nombre from metrics GROUP BY user ORDER BY nombre DESC";
			//$sql="SELECT DISTINCT user,page , count(*) as nombre from metrics GROUP BY user,page ORDER BY nombre DESC";
			
			$sql="SELECT DISTINCT hist , count(*) as nombre from metrics where hist_num=3  GROUP BY hist ORDER BY nombre DESC";
			$sql="SELECT DISTINCT hist , count(*) as nombre  ,AVG(date_diff_1) as date_diff_1_v,AVG(date_diff_2) as date_diff_2_v from metrics where hist_num=3 and page LIKE'manage/add_classification'  GROUP BY hist ORDER BY nombre DESC";
			
			$sql="SELECT DISTINCT hist , date_diff_1,date_diff_2 from metrics where hist_num=3 and page LIKE'manage/add_classification' AND hist like 'manage/list_paper -> manage/view_paper -> manage/add_classification' ";
			$sql="SELECT  hist , date_diff_1,date_diff_2 from metrics where hist_num=3 and page LIKE'manage/view_paper' AND hist like '%manage/add_classification -> manage/view_paper' ";
			$sql="SELECT  DISTINCT hist , count(*) as nombre  ,AVG(date_diff_1) as date_diff_1_v,AVG(date_diff_2) as date_diff_2_v from metrics where hist_num=3 and page LIKE'manage/view_paper'  GROUP BY hist ORDER BY nombre DESC";
			
			
			
			
			
			$sql="SELECT DISTINCT page , count(*) as nombre from metrics GROUP BY page ORDER BY nombre DESC";
			
			//-numbre total
			//$sql="SELECT  count(*) as nombre from metrics ";
			
			//-utilisateurs
			//$sql="SELECT DISTINCT  user from metrics";
			
			//- utilisateur par op�ration
			$sql="SELECT DISTINCT user , count(*) as nombre from metrics GROUP BY user ORDER BY nombre DESC";
			$sql="SELECT DISTINCT user,page , count(*) as nombre from metrics GROUP BY user,page ORDER BY nombre DESC";
			
			$sql="SELECT DISTINCT project , count(*) as nombre from metrics GROUP BY project ORDER BY nombre DESC";
			
			
			
			$sql="SELECT DISTINCT page , count(*) as nombre from metrics GROUP BY page ORDER BY nombre DESC";
			$sql="SELECT DISTINCT page , count(*) as nombre from metrics  GROUP BY page ORDER BY nombre DESC";
			$sql="SELECT DISTINCT project , count(*) as nombre from metrics GROUP BY project ORDER BY nombre DESC";
			
			$sql="SELECT DISTINCT page , count(*) as nombre from metrics  GROUP BY page ORDER BY nombre DESC";
			
			
			$sql="SELECT DISTINCT hist , count(*) as nombre   from metrics where hist_num=3 and page LIKE'manage/add_classification'  GROUP BY hist ORDER BY nombre DESC";
				
			$res=$this->db4->query($sql)->result_array();
			$tmpl = array (
					'table_open'  => '<table class="table table-striped table-hover">',
					'table_close'  => '</table>'
			);
			
			$this->table->set_template($tmpl);
			
			echo $this->table->generate($res);
			//print_test($res);
		}
		
		public function  getLienHist(){
			$this->db4 = $this->load->database("spl", TRUE);
			$sql="SELECT metric_id,user,time, page ,page_url_source,page_url from metrics  ORDER BY  user, time ASC";
			$res=$this->db4->query($sql)->result_array();
			$prev_time_1=0;
			$prev_time_2=0;
			$prev_page_1='';
			$prev_page_2='';
			$hist="";
			foreach ($res as $key => $value) {
				$hist=$value['page'];
				$hist_num=1;
				$value['date']=date('Y-m-d : H:i:s',$value['time']);
				if(!empty($prev_time_1)){
					$value['date_diff_1']=($value['time']-$prev_time_1);
					$value['hist_page_1']=$prev_page_1;
					if($value['date_diff_1']<3600){
					$hist=$prev_page_1." -> ".$hist;
					$hist_num++;
					}
				}else{
					$value['date_diff_1']="";
					$value['hist_page_1']="";
				}
				
				if(!empty($prev_time_2)){
					$value['date_diff_2']=($prev_time_1 - $prev_time_2);
					$value['hist_page_2']=$prev_page_2;
					
					if(!empty($prev_time_1) AND !empty($value['date_diff_1']) AND $value['date_diff_1']<3600){
						if($value['date_diff_2']<3600){
						$hist=$prev_page_2." -> ".$hist;
						$hist_num++;
						}
					}
				}else{
					$value['date_diff_2']="";
					$value['hist_page_2']="";
				}
				
				$value['hist']=$hist;
				$value['hist_num']=$hist_num;
				
				$prev_time_2=$prev_time_1;
				$prev_page_2=$prev_page_1;
				
				$prev_time_1=$value['time'];
				$prev_page_1= $value['page'];
				print_test($value);
				
				$res = $this->db4->update ( 'metrics', $value, array ('metric_id' =>$value['metric_id']	) );
			}
		//	print_test($res);
		}
		
		function getBrowser($u_agent)
		{
			//$u_agent = $_SERVER['HTTP_USER_AGENT'];
			$bname = 'Unknown';
			$ub = 'Unknown';
			$platform = 'Unknown';
			$version= "";
		
			//First get the platform?
			if (preg_match('/linux/i', $u_agent)) {
				$platform = 'linux';
			}
			elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
				$platform = 'mac';
			}
			elseif (preg_match('/windows|win32/i', $u_agent)) {
				$platform = 'windows';
			}
		
			// Next get the name of the useragent yes seperately and for good reason
			if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
			{
				$bname = 'Internet Explorer';
				$ub = "MSIE";
			}
			elseif(preg_match('/Firefox/i',$u_agent))
			{
				$bname = 'Mozilla Firefox';
				$ub = "Firefox";
			}
			elseif(preg_match('/Chrome/i',$u_agent))
			{
				$bname = 'Google Chrome';
				$ub = "Chrome";
			}
			elseif(preg_match('/Safari/i',$u_agent))
			{
				$bname = 'Apple Safari';
				$ub = "Safari";
			}
			elseif(preg_match('/Opera/i',$u_agent))
			{
				$bname = 'Opera';
				$ub = "Opera";
			}
			elseif(preg_match('/Netscape/i',$u_agent))
			{
				$bname = 'Netscape';
				$ub = "Netscape";
			}
		
			// finally get the correct version number
			$known = array('Version', $ub, 'other');
			$pattern = '#(?<browser>' . join('|', $known) .
			')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			if (!preg_match_all($pattern, $u_agent, $matches)) {
				// we have no matching number just continue
			}
		
			// see how many we have
			$i = count($matches['browser']);
			if ($i != 1) {
				//we will have two since we are not using 'other' argument yet
				//see if version is before or after the name
				if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
					$version= $matches['version'][0];
				}
				else {
					if(isset($matches['version'][1]))
					$version= $matches['version'][1];
					else
					$version="Unknown";
				}
			}
			else {
				$version= $matches['version'][0];
			}
		
			// check if we have a number
			if ($version==null || $version=="") {$version="?";}
		
			return array(
					'userAgent' => $u_agent,
					'name'      => $bname,
					'version'   => $version,
					'platform'  => $platform,
					'pattern'    => $pattern
			);
		}
		
		
		public function test_assignment(){
			$number_of_papers=46;
			$number_of_user=4;
			$User_per_papers=3;
			
			$papers=array();
			$i=1;
			while ($i<=$number_of_papers) {
				$papers[$i]['paper']="paper_".$i;
				
				$papers[$i]['users']=array();
				$j=1;
				while($j<=$User_per_papers){
					$temp_user=$i % $number_of_user + $j;
					
					if($temp_user > $number_of_user )
						$temp_user = $temp_user - $number_of_user;
						
					array_push($papers[$i]['users'], $temp_user);
					
					
					$j++;
				}
				
				
			$i++;
			}
			
			//print_test($papers);
			$nuser=array();
			foreach ($papers as $key => $value) {
				foreach ($value['users'] as $key_u => $value_u) {
					if(isset($nuser[$value_u])){
						$nuser[$value_u] ++;
					}else{
						$nuser[$value_u] =1;
					}
				}
				
			}
			
			print_test($nuser);
			
		}
		
		
		
		public function get_screen_for_kappa(){
			
			
			$screening_phase_info=active_screening_phase_info();
			$current_phase=active_screening_phase();
		//	print_test($screening_phase_info);
			
			$sql="select paper_id,user_id,screening_decision	
					FROM screening_paper 
					WHERE  assignment_mode='auto' AND  screening_status='done' AND screening_phase = $current_phase AND screening_active=1";
			echo $sql;
			$result=$this->db_current->query($sql)->result_array();
			
		//	print_test($result);
			$result_kappa=array();
			foreach ($result as $key => $value) {
				if(!isset($result_kappa[$value['paper_id']])){
					$result_kappa[$value['paper_id']]=array(
							'Included'=>0,
							'Excluded'=>0,
					);
				}
				
				if(!empty($value['screening_decision']) AND ($value['screening_decision']=='Included' OR $value['screening_decision']=='Excluded')){
					$result_kappa[$value['paper_id']][$value['screening_decision']]+=1;
				}
				
			}
			
			//print_test($result_kappa);
			$result_kappa_clean=array();
			foreach ($result_kappa as $k => $v) {
				array_push($result_kappa_clean, array($v['Included'],$v['Excluded']));
			}
			
			//print_test($result_kappa_clean);
			
			return $result_kappa_clean;
		}
		
		
		
		public function calculate_kappa(){
		
			
			$matrice=array(
					0=>array(2,0),
					1=>array(1,1),
					2=>array(0,2)
					
			);
			
			$matrice=array(
					0=>array(0,0,0,0,14),
					1=>array(0,2,6,4,2),
					2=>array(0,0,3,5,6),
					3=>array(0,3,9,2,0),
					4=>array(2,2,8,1,1),
					5=>array(7,7,0,0,0),
					6=>array(3,2,6,3,0),
					7=>array(2,5,3,2,2),
					8=>array(6,5,2,1,0),
					9=>array(6,5,2,1,0)
						
			);
			
		$matrice= $this->get_screen_for_kappa();
		
		print_test($matrice);
			
			$N=count($matrice);
			$k=count($matrice[0]);
			$n=0;
			foreach ($matrice[0] as $key => $value) {
				$n+=$value;
			}
			
			
			print_test($N);
			print_test($n);
			print_test($k);
			
			$p=array();
			
			for ($j = 0; $j < $k; $j++) {
				$p[$j]=0.0;
				for ($i = 0; $i < $N; $i++) {
					$p[$j]=$p[$j]+$matrice[$i][$j];
				}
				
				$p[$j]=$p[$j]/($N*$n);
			}
			
			print_test($p);
			
			
			$P=array();
			for ($j = 0; $j < $N; $j++) {
				$P[$j]=0.0;
				for ($i = 0; $i < $k; $i++) {
					$P[$j]=$P[$j] + ($matrice[$j][$i] * $matrice[$j][$i] );
				}
			
				$P[$j]=($P[$j]-$n) / ($n*($n-1));
			}
			
			print_test($P);
			
			$Pbar = array_sum ($P) / $N;
			
			print_test($Pbar);
			$PbarE=0.0;
			foreach ($p as $key => $value) {
				$PbarE+= $value*$value;
			}
			
			print_test($PbarE);
			
			$kappa=($Pbar - $PbarE)/(1-$PbarE);
			
			print_test($kappa);
		}
		
		public function test_mail_old() {
			$ci = get_instance();
			$ci->load->library('email');
			$config['protocol'] = "smtp";
			$config['smtp_host'] = "ssl://smtp.gmail.com";
			$config['smtp_port'] = "465";
			$config['smtp_user'] = "relisgeodes@gmail.com";
			$config['smtp_pass'] = "R3l1sApp";
			$config['charset'] = "utf-8";
			$config['mailtype'] = "html";
			$config['newline'] = "\r\n";
			
			$ci->email->initialize($config);
			
			$ci->email->from('relisgeodes@gmail.com', 'ReLiS');
			$list = array('bbigendako@gmail.com');
			$ci->email->to($list);
			$this->email->reply_to('relisgeodes@gmail.com', 'Explendid Videos');
			$ci->email->subject('This is an email test');
			$ci->email->message('It is working. Great!');
			
			if($ci->email->send()){
				echo "Email sent successfully.";
			}
			else{
				//	echo "Error in sending Email.";
				echo $ci->email->print_debugger();
			}
			//$res=$ci->email->send();
			//print_test($ci->email);
		}
		public function test_mail() {
			$message="
					<h2>Relis Validation message</h2>
					<p>
					Wecome to ReLiS:<br/>
					Your validation code is : <b>53653536363</b>
					</p>
					
					test message";
			$subject="Validation code";
			$destination=array('bbigendako@gmail.com','relisgeodes@gmail.com');
			$res=$this->bm_lib->send_mail($subject,$message,$destination);
			print_test($res);
		}
		public function test_randomstr() {
			
			$res=$this->bm_lib->random_str(10);
			print_test($res);
		}
		
		public function test_new_config($ref_table='new_users'){
			
			$ref_table_config=get_table_configuration($ref_table);
			print_test($ref_table_config);
		}
		
		
		
		public function import_edouard(){
				
		
			
			
			$transfo_kind=array(
					'Structurelle'=>1,
					'Comportementale'=>2,
					'Mixte'=>3,					
			);
			$mm_kind=array(
					'input specific / output general'=>1,
					'input specific / output specific'=>2,
					'input general / output general'=>3,					
					'input general / output specific'=>4,					
			);
			
			$model_kind=array(
					'Jouets'=>1,
					'Open source'=>3,
					'Industriels'=>2,					
			);
			
			$intent=array(
					'Abstraction'=>2,
					'Analysis'=>6,
					'Editing'=>7,					
					'Language Translation'=>4,					
					'Model Composition'=>9,					
					'Model Visualization'=>8,					
					'Refinement'=>1,					
					'Semantic Definition'=>3,					
			);
			
			$transfo_langauge=array(
					'Langage dédié (QVT…)'=>1,
					'Langage classique (Java…)'=>2,
					'Langage ad hoc'=>3,					
			);
			
			$validation=array(
					'No validation'=>2,
					'Validation empirique'=>1,
					'Validation théorique (formel)'=>3,					
			);
			$scope=array(
					'Exo/Out-place'=>3,
					'Endo/In-place'=>1,
					'Endo/Out-place'=>2,					
			);
			$orientation=array(
					'Académie'=>1,
					'Industrie'=>2				
			);
			
			
			
			
			$all_file="cside/test/classification_edouard.csv";
			
			ini_set('auto_detect_line_endings',TRUE);
				
			$fp = fopen($all_file, 'rb');
			$i=1;
			$last_count=0;
			$paper=array();
			$classification=array();
			$i=0;
			while ( (($Tline = (fgetcsv($fp,0,";",'"')))) !== false) {
			//	print_test($Tline);
				
				if($i>0){
					print_test("element:".$i);
				//$Tline = array_map( "utf8_encode", $Tline );
				
				$preview="";
				$preview=!empty($Tline[1])?"<b>Authors:</b><br/>".$this->mres_escape($Tline[1])." <br/>":"";
				$preview.=!empty($Tline[7])?"<b>Key words:</b><br/>".$this->mres_escape($Tline[7])." <br/>":"";
				
				$paper=array(
					'id'=>$i,	
					'bibtexKey'=>'paper_'.$Tline[0],	
					'title'=>$this->mres_escape($Tline[2]),	
					'preview'=>$preview,	
					'abstract'=>$this->mres_escape($Tline[6]),	
					'doi'=>$Tline[4],	
					'year'=>$Tline[3],	
					'added_by'=>1,	
					'addition_mode'=>'Automatic',	
					'classification_status'=>'To classify',	
					'operation_code'=>'1_'.time()	
						
						
				);
				
				print_test($paper);
			//	$res=$this->db_current->insert('paper',$paper);
			//	print_test($res);
				
				$classification=array(
						'class_paper_id'=>$i,
						'transfo_kind'=>$transfo_kind[$Tline[10]],
						'mm_kind'=>$mm_kind[$Tline[12]],
						'model_kind'=>$model_kind[$Tline[18]],
						'intent'=>$intent[$Tline[14]],
						'transfo_langauge'=>$transfo_langauge[$Tline[16]],
						'validation'=>$validation[$Tline[20]],
						'scope'=>$scope[$Tline[22]],
						'orientation'=>$orientation[$Tline[24]],
						
						'comment_transfo_kind'=>$this->mres_escape($Tline[11]),
						'comment_mm_kind'=>$this->mres_escape($Tline[13]),
						'comment_model_kind'=>$this->mres_escape($Tline[19]),
						'comment_intent'=>$this->mres_escape($Tline[15]),
						'comment_transfo_langauge'=>$this->mres_escape($Tline[17]),
						'comment_validation'=>$this->mres_escape($Tline[21]),
						'comment_scope'=>$this->mres_escape($Tline[23]),
						'comment_orientation'=>$this->mres_escape($Tline[25]),
						'year'=>$Tline[3],
				);
				
				print_test($classification);
			//	$res=$this->db_current->insert('classification',$classification);
			//	print_test($res);
				
				}
				$i++;
				
			}
			
		}
		
		
		public function import_lechanceux(){
		
			$template_style = array(
					'Predefined'=>1,
					'Output-based'=>2,
					'Rule-based'=>3,
			);
			$design_time = array('General purpose'=>1,
					'Domain specific'=>2,
					'Schema'=>3,
					'Programming Language'=>4);
			
			$run_time = array('General purpose'=>1,
					'Domain specific'=>2,
					'Structured data'=>3,
					'Source code'=>4);
			
			$output_type = array('Source code'=>1,
					'Structured data'=>2,
					'Natural language'=>3);
			
			$tool = array('Acceleo'=>1,
					'Xpand'=>2,
					'EGL'=>3,
					'JET'=>4,
					'MOFScript'=>5,
					'Other'=>6,
					'Programmed'=>7,
					'Simulink TLC'=>8,
					'StringTemplate'=>9,
					'T4'=>10,
					'Unspecified'=>11,
					'Velocity'=>12,
					'Rational'=>13,
					'XSLT'=>14,
					'Fujaba'=>15,
					'FreeMarker'=>16,
					'Rhapsody'=>17,
					'Xtend'=>18);
			
			$mde = array('Yes'=>1,
					'No'=>0);
		
			$context = array('Standalone'=>1,
					'Intermediate'=>2,
					'Last'=>3);
			
			$validation = array('Benchmark'=>1,
					'Case study'=>2,
					'User study'=>3,
					'No validation'=>4,
					'Formal'=>3);
			
			$scale = array('Small scale'=>1,
					'Large scale'=>2,
					'No application'=>3);
			
			$domain = array('Software engineering'=>1,
					'Embedded systems'=>2,
					'Web technology'=>3,
					'Networking'=>4,
					'Aspect-oriented systems'=>5,
					'Mobile systems'=>6,
					'Programming languages'=>7,
					'Testing'=>8,
					'Other'=>9,
					'Compilers'=>10,
					'Bio-medical'=>11,
					'Distributed systems'=>12,
					'Simulation '=>13,
					'Databases'=>14,
					'Security'=>15,
					'Artificial intelligence'=>16,
					'Refactoring'=>17,
					'Robotics'=>18,
					'Graphics'=>19);
			
			$orientation = array('Academic'=>1,
					'Industry'=>2);
			
			$publication_type = array('C'=>1,
					'J'=>2,
					'O'=>3	);
			
			$venue_type = array('MDE'=>1,
					'Other'=>2,
					'SE'=>3);
			
				
			$all_file="cside/test/classification_lechanceux.csv";
				
			ini_set('auto_detect_line_endings',TRUE);
		
			$fp = fopen($all_file, 'rb');
			$i=1;
			$last_count=0;
			$paper=array();
			$classification=array();
			$i=0;
			while ( (($Tline = (fgetcsv($fp,0,";",'"')))) !== false) {
					print_test($Tline);
				
				if($i>0){
					print_test("element:".$i);
					//$Tline = array_map( "utf8_encode", $Tline );
		
					$preview="";
					//$preview=!empty($Tline[1])?"<b>Authors:</b><br/>".$this->mres_escape($Tline[1])." <br/>":"";
					//$preview.=!empty($Tline[7])?"<b>Key words:</b><br/>".$this->mres_escape($Tline[7])." <br/>":"";
		
					$paper=array(
							'id'=>$i,
							'bibtexKey'=>'paper_'.$i,
							'title'=>$this->mres_escape($Tline[1]),
							//'preview'=>$preview,
							//'abstract'=>$this->mres_escape($Tline[6]),
							//'doi'=>$Tline[4],
							'year'=>$Tline[2],
							'added_by'=>1,
							'addition_mode'=>'Automatic',
							'classification_status'=>'To classify',
							'operation_code'=>'1_'.time()
		
		
					);
		
					print_test($paper);
					//	$res=$this->db_current->insert('paper',$paper);
					//	print_test($res);
					
					$classification=array(
							'class_paper_id'=>$i,
							'template_style'=>$template_style[$Tline[15]],
							'design_time'=>$design_time[$Tline[6]],
							'run_time'=>$run_time[$Tline[13]],
							'output_type'=>$output_type[$Tline[10]],
							'tool'=>$tool[$Tline[3]],
							'mde'=>$mde[$Tline[12]],
							'context'=>$context[$Tline[5]],
							'validation'=>$validation[$Tline[18]],
							'scale'=>$scale[$Tline[8]],
							'domain'=>$domain[$Tline[17]],
							'orientation'=>$orientation[$Tline[9]],
							'publication_type'=>$publication_type[$Tline[19]],
							'venue_type'=>$venue_type[$Tline[21]],
		
							
							'comment_template_style'=>$this->mres_escape($Tline[16]),
							'comment_design_time'=>$this->mres_escape($Tline[7]),
							'comment_run_time'=>$this->mres_escape($Tline[14]),
							'comment_output_type'=>$this->mres_escape($Tline[11]),
							'comment_tool'=>$this->mres_escape($Tline[4]),
							'publication_name'=>$this->mres_escape($Tline[20]),
							'year'=>$Tline[2],
					);
		
					print_test($classification);
					//	$res=$this->db_current->insert('classification',$classification);
					//	print_test($res);
					
				}
				
				$i++;
		
			}
				
		}
		
		private function mres_escape($value)
		{
			$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
			$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
		
			return str_replace($search, $replace, $value);
		}
		
		public function start_editor($value=1)
		{
			
			
			$commands=array(
				'start'=>"/u/relis/tomcat/bin/startup.sh",
				'stop'=>"/u/relis/tomcat/bin/shutdown.sh",
				'status'=>"netstat -lnp | grep 8080",
				'tail'=>" tail /u/relis/tomcat/logs/catalina.out",			
			);
			
			
			if($value==1)
				$cmd="/u/relis/tomcat/bin/startup.sh";
			elseif($value==2)
				$cmd="/u/relis/tomcat/bin/shutdown.sh";
			
			//$message = exec($cmd);
			//print_test($message);
		}
		
		
		/*
		 * Page permettant de lancer une commande
		 */
		public function manage_editor($request="zz",$run=1){
			if(!has_usergroup(1)){
				set_top_msg(" You have no access to this feature ",'error');
				redirect('home');
			}
			$commands=array(
				'Start'=>"/u/relis/tomcat/bin/startup.sh",
				'Stop'=>"/u/relis/tomcat/bin/shutdown.sh",
				'Status'=>"netstat -lnp | grep 8080",
				'Log'=>" tail /u/relis/tomcat/logs/catalina.out",			
				'g_status'=>"  cd /u/relis/public_html/relis_app  &&   git status",			
				'g_pull'=>"  cd /u/relis/public_html/relis_app  &&   git pull",			
			);
			$script="";
			
			$normal =FALSE;
			//print_test($request);
			if($this->input->post ()){
				$post_arr = $this->input->post ();
				
				if(!empty($post_arr['script'])){
					$script=$post_arr['script'];
				}
				 
			}elseif(!empty($request) AND !empty($commands[$request])){
				$normal=True;
				$script=$commands[$request];
				
			}
			
			if(!empty($script)){
				
				if($normal ){
					if($request=='Start'){
						//chech status
						$status = exec(trim($commands['Status']));
						if(!empty($status)){// server already runnning
							$message = "Server already running.<br/> If you cannot access the editor please wait until the end of the startup process!";
						}else{
							$message = exec(trim($script));
						}
					}elseif($request=='Status'){
						$status = exec(trim($commands['Status']));
						if(!empty($status)){// server already runnning
							$message = "Server  running";
						}else{
							$message = "Server not running";
						}
						//$message=$status;
					}else{
							$message = exec(trim($script));
					}
				}else{
					$message = exec(trim($script));
				}
				$data['command_response']=$message;
			}else{
				$data['command_response']='No command run';	
			}
			
			$data['commands']=$commands;
			if(active_user_id()==1){
				$data['allow_manual_sript']=true;
			}
				
			$data['title']=lng_min('Editor server');
			$data['page']='editor_command';
			$data ['top_buttons'] = get_top_button ( 'all', lng_min('Back to editor'), 'install/relis_editor/admin',lng_min('Back to editor'),' fa-exchange','',' btn-info ' );
			$data ['top_buttons'] .= get_top_button ( 'back', 'Back', 'manage' );
			$this->load->view('body',$data);
		}
		
		
		///Backup database
		
		
		
		/**
		 * Host where the database is located
		 */
		var $host;
		
		/**
		 * Username used to connect to database
		 */
		var $username;
		
		/**
		 * Password used to connect to database
		 */
		var $passwd;
		
		/**
		 * Database to backup
		 */
		var $dbName;
		
		/**
		 * Database charset
		 */
		var $charset;
		
		/**
		 * Database connection
		 */
		var $conn;
		
		/**
		 * Backup directory where backup files are stored
		 */
		var $backupDir;
		
		/**
		 * Output backup file
		 */
		var $backupFile;
		
		/**
		 * Use gzip compression on backup file
		 */
		var $gzipBackupFile;
		
		/**
		 * Content of standard output
		 */
		var $output;
		
		/**
		 * Disable foreign key checks
		 */
		var $disableForeignKeyChecks;
		
		/**
		 * Batch size, number of rows to process per iteration
		 */
		var $batchSize;
		
		public function backup_db(){
			//echo "backup ";
			//echo $this->db_current->database;
			//echo $this->db_current->password;
			//echo $this->db_current->username;
			$this->initialize_backup();
			$this->conn                    = $this->initializeDatabase();
			
			set_time_limit(900); // 15 minutes
			
			if (php_sapi_name() != "cli") {
				echo '<div style="font-family: monospace;">';
			}
			
			$result = $this->backupTables() ? 'OK' : 'KO';
			
			//echo "<h1>$result</h1>";
			//$backupDatabase->obfPrint('Backup result: ' . $result, 1);
			
			// Use $output variable for further processing, for example to send it by email
			//$output = $backupDatabase->getOutput();
			
			if (php_sapi_name() != "cli") {
				echo '</div>';
			}
		}
		
		private function  initialize_backup(){
			
			$this->host                    = $this->db_current->hostname;
			$this->username                = $this->db_current->username;
			$this->passwd                  = $this->db_current->password;
			$this->dbName                  = $this->db_current->database;
			$this->charset                 = 'utf8';
			//$this->conn                    = $this->initializeDatabase();
			$this->backupDir               = "C:/xampp/htdocs/relis/relis_dev/cside/metrics";
			$this->backupFile              = 'myphp-backup-'.$this->dbName.'-'.date("Ymd_His", time()).'.sql';
			$this->gzipBackupFile          = false;
			$this->disableForeignKeyChecks =  true;
			$this->batchSize               =  1000; // default 1000 rows
			//$this->output                  = '';
			
		}
		
		private function initializeDatabase() {
			try {
				$conn = mysqli_connect($this->host, $this->username, $this->passwd, $this->dbName);
				if (mysqli_connect_errno()) {
					throw new Exception('ERROR connecting database: ' . mysqli_connect_error());
					die();
				}
				if (!mysqli_set_charset($conn, $this->charset)) {
					mysqli_query($conn, 'SET NAMES '.$this->charset);
				}
			} catch (Exception $e) {
				print_r($e->getMessage());
				die();
			}
		
			return $conn;
		}
		
		/**
		 * Backup the whole database or just some tables
		 * Use '*' for whole database or 'table1 table2 table3...'
		 * @param string $tables
		 */
		public function backupTables($tables = '*') {
			try {
				/**
				 * Tables to export
				 */
				if($tables == '*') {
					$tables = array();
					$result = mysqli_query($this->conn, 'SHOW TABLES');
					while($row = mysqli_fetch_row($result)) {
						$tables[] = $row[0];
					}
				} else {
					$tables = is_array($tables) ? $tables : explode(',', str_replace(' ', '', $tables));
				}
		
				$sql = 'CREATE DATABASE IF NOT EXISTS `'.$this->dbName."`;\n\n";
				$sql .= 'USE `'.$this->dbName."`;\n\n";
		
				/**
				 * Disable foreign key checks
				 */
				if ($this->disableForeignKeyChecks === true) {
					$sql .= "SET foreign_key_checks = 0;\n\n";
				}
		
				/**
				 * Iterate tables
				 */
				foreach($tables as $table) {
					$this->obfPrint("Backing up `".$table."` table...".str_repeat('.', 50-strlen($table)), 0, 0);
		
					/**
					 * CREATE TABLE
					 */
					$sql .= 'DROP TABLE IF EXISTS `'.$table.'`;';
					$row = mysqli_fetch_row(mysqli_query($this->conn, 'SHOW CREATE TABLE `'.$table.'`'));
					$sql .= "\n\n".$row[1].";\n\n";
		
					/**
					 * INSERT INTO
					 */
		
					$row = mysqli_fetch_row(mysqli_query($this->conn, 'SELECT COUNT(*) FROM `'.$table.'`'));
					$numRows = $row[0];
		
					// Split table in batches in order to not exhaust system memory
					$numBatches = intval($numRows / $this->batchSize) + 1; // Number of while-loop calls to perform
		
					for ($b = 1; $b <= $numBatches; $b++) {
		
						$query = 'SELECT * FROM `' . $table . '` LIMIT ' . ($b * $this->batchSize - $this->batchSize) . ',' . $this->batchSize;
						$result = mysqli_query($this->conn, $query);
						$realBatchSize = mysqli_num_rows ($result); // Last batch size can be different from $this->batchSize
						$numFields = mysqli_num_fields($result);
		
						if ($realBatchSize !== 0) {
							$sql .= 'INSERT INTO `'.$table.'` VALUES ';
		
							for ($i = 0; $i < $numFields; $i++) {
								$rowCount = 1;
								while($row = mysqli_fetch_row($result)) {
									$sql.='(';
									for($j=0; $j<$numFields; $j++) {
										if (isset($row[$j])) {
											$row[$j] = addslashes($row[$j]);
											$row[$j] = str_replace("\n","\\n",$row[$j]);
											$row[$j] = str_replace("\r","\\r",$row[$j]);
											$row[$j] = str_replace("\f","\\f",$row[$j]);
											$row[$j] = str_replace("\t","\\t",$row[$j]);
											$row[$j] = str_replace("\v","\\v",$row[$j]);
											$row[$j] = str_replace("\a","\\a",$row[$j]);
											$row[$j] = str_replace("\b","\\b",$row[$j]);
											$sql .= '"'.$row[$j].'"' ;
										} else {
											$sql.= 'NULL';
										}
		
										if ($j < ($numFields-1)) {
											$sql .= ',';
										}
									}
		
									if ($rowCount == $realBatchSize) {
										$rowCount = 0;
										$sql.= ");\n"; //close the insert statement
									} else {
										$sql.= "),\n"; //close the row
									}
		
									$rowCount++;
								}
							}
		
							$this->saveFile($sql);
							$sql = '';
						}
					}
		
					/**
					 * CREATE TRIGGER
					 */
		
					// Check if there are some TRIGGERS associated to the table
					/*$query = "SHOW TRIGGERS LIKE '" . $table . "%'";
					 $result = mysqli_query ($this->conn, $query);
					 if ($result) {
					 $triggers = array();
					 while ($trigger = mysqli_fetch_row ($result)) {
					 $triggers[] = $trigger[0];
					 }
		
					 // Iterate through triggers of the table
					 foreach ( $triggers as $trigger ) {
					 $query= 'SHOW CREATE TRIGGER `' . $trigger . '`';
					 $result = mysqli_fetch_array (mysqli_query ($this->conn, $query));
					 $sql.= "\nDROP TRIGGER IF EXISTS `" . $trigger . "`;\n";
					 $sql.= "DELIMITER $$\n" . $result[2] . "$$\n\nDELIMITER ;\n";
					 }
		
					 $sql.= "\n";
		
					 $this->saveFile($sql);
					 $sql = '';
					 }*/
		
					$sql.="\n\n";
		
					///$this->obfPrint('OK');
				}
		
				/**
				 * Re-enable foreign key checks
				 */
				if ($this->disableForeignKeyChecks === true) {
					$sql .= "SET foreign_key_checks = 1;\n";
				}
		
				$this->saveFile($sql);
		
				if ($this->gzipBackupFile) {
					$this->gzipBackupFile();
				} else {
					print_test('Backup file succesfully saved to ' . $this->backupDir.'/'.$this->backupFile, 1, 1);
					set_log('backup','Database succesfully saved to ' . $this->backupDir.'/'.$this->backupFile);
				}
			} catch (Exception $e) {
				print_test($e->getMessage());
				return false;
			}
		
			return true;
		}
		
		
		
		/**
		 * Save SQL to file
		 * @param string $sql
		 */
		protected function saveFile(&$sql) {
			if (!$sql) return false;
		
			try {
		
				if (!file_exists($this->backupDir)) {
					mkdir($this->backupDir, 0777, true);
				}
		
				file_put_contents($this->backupDir.'/'.$this->backupFile, $sql, FILE_APPEND | LOCK_EX);
		
			} catch (Exception $e) {
				print_r($e->getMessage());
				return false;
			}
		
			return true;
		}
		
		/*
		 * Gzip backup file
		 *
		 * @param integer $level GZIP compression level (default: 9)
		 * @return string New filename (with .gz appended) if success, or false if operation fails
		 */
		protected function gzipBackupFile($level = 9) {
			if (!$this->gzipBackupFile) {
				return true;
			}
		
			$source = $this->backupDir . '/' . $this->backupFile;
			$dest =  $source . '.gz';
		
			$this->obfPrint('Gzipping backup file to ' . $dest . '... ', 1, 0);
		
			$mode = 'wb' . $level;
			if ($fpOut = gzopen($dest, $mode)) {
				if ($fpIn = fopen($source,'rb')) {
					while (!feof($fpIn)) {
						gzwrite($fpOut, fread($fpIn, 1024 * 256));
					}
					fclose($fpIn);
				} else {
					return false;
				}
				gzclose($fpOut);
				if(!unlink($source)) {
					return false;
				}
			} else {
				return false;
			}
		
			$this->obfPrint('OK');
			return $dest;
		}
		
		/**
		 * Prints message forcing output buffer flush
		 *
		 */
		public function obfPrint ($msg = '', $lineBreaksBefore = 0, $lineBreaksAfter = 1) {
			print_test($msg);
		}
		
		/**
		 * Returns full execution output
		 *
		 */
		public function getOutput() {
			return $this->output;
		}
		
}
