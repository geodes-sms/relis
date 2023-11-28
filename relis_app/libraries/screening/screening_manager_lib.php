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
class Screening_manager_lib
{
	public function __construct()
	{
		$this->CI =& get_instance();


	}

	//generate a left menu structure specifically for the screening phase
	function get_left_menu_screen()
	{
		$project_published = project_published();
		$menu = array();

		$menu['general'] = array(
			'label' => 'General'
		);


		$menu['general']['menu']['home'] = array('label' => 'Dashboard', 'url' => 'screening/screening', 'icon' => 'th');

		if (get_appconfig_element('screening_on') and can_review_project())


			//if(get_appconfig_element('assign_papers_on'))


			if (can_manage_project() or get_appconfig_element('screening_result_on')) {
				$menu['general']['menu']['papers'] = array('label' => 'Papers in this phase', 'url' => '', 'icon' => 'newspaper-o');

				$menu['general']['menu']['papers']['sub_menu']['all_papers'] = array('label' => 'All', 'url' => 'element/entity_list/list_papers_screen', '');
				$menu['general']['menu']['papers']['sub_menu']['screen_paper_pending'] = array('label' => 'Pending', 'url' => 'element/entity_list/list_papers_screen_pending', '');
				$menu['general']['menu']['papers']['sub_menu']['screen_paper_review'] = array('label' => 'Under Review', 'url' => 'element/entity_list/list_papers_screen_review', '');
				$menu['general']['menu']['papers']['sub_menu']['screen_paper_included'] = array('label' => 'Included', 'url' => 'element/entity_list/list_papers_screen_included', '');
				$menu['general']['menu']['papers']['sub_menu']['screen_paper_excluded'] = array('label' => 'Excluded', 'url' => 'element/entity_list/list_papers_screen_excluded', '');
				$menu['general']['menu']['papers']['sub_menu']['screen_paper_conflict'] = array('label' => 'In Conflict', 'url' => 'element/entity_list/list_papers_screen_conflict', '');
			}




		if (active_screening_phase()) {
			$phase_info = active_screening_phase_info();

			$menu['general']['menu']['papers_screen'] = array('label' => 'Screening', 'url' => '', 'icon' => 'search');

			if (can_review_project() and !$project_published) {
				$menu['general']['menu']['papers_screen']['sub_menu']['screen'] = array('label' => 'Screen', 'url' => 'screening/screen_paper', 'icon' => '');



				$menu['general']['menu']['papers_screen']['sub_menu']['my_assignment'] = array('label' => 'My assignments', 'url' => 'element/entity_list/list_my_assignments', '');
				$menu['general']['menu']['papers_screen']['sub_menu']['my_screen'] = array('label' => 'My screened', 'url' => 'element/entity_list/list_my_screenings', '');
				$menu['general']['menu']['papers_screen']['sub_menu']['my_screen_pending'] = array('label' => 'My Pending', 'url' => 'element/entity_list/list_my_pending_screenings', '');
			}

			if (can_manage_project() or get_appconfig_element('screening_result_on')) {
				$menu['general']['menu']['papers_screen']['sub_menu']['all_assignments'] = array('label' => 'All Assignments', 'url' => 'element/entity_list/list_assignments', '');
				$menu['general']['menu']['papers_screen']['sub_menu']['all_screen'] = array('label' => 'All Screened', 'url' => 'element/entity_list/list_screenings', '');
				//$menu['general']['menu']['papers_screen']['sub_menu']['all_screen_pending']=array( 'label'=>'All pendings', 'url'=>'element/entity_list/list_all_pending_screenings', '');
				$menu['general']['menu']['papers_screen']['sub_menu']['completion'] = array('label' => 'Progress', 'url' => 'screening/screen_completion', '');

			}

			$menu['general']['menu']['result'] = array('label' => 'Statistics', 'url' => 'screening/screen_result', 'icon' => 'th');

			if (get_appconfig_element('screening_validation_on')) {

				$menu['general']['menu']['papers_screen_validate'] = array('label' => 'Validation', 'url' => '', 'icon' => 'check-square-o');

				if (can_validate_project() and !$project_published) {
					$menu['general']['menu']['papers_screen_validate']['sub_menu']['screen_validate'] = array('label' => 'Validate', 'url' => 'screening/screen_paper_validation', 'icon' => '');
					//$menu['general']['menu']['papers_screen_validate']['sub_menu']['validate_screen_assign']=array( 'label'=>'Assign papers for validation', 'url'=>'screening/validate_screen_set', 'icon'=>'');
				}
				//$menu['general']['menu']['papers_screen_validate']['sub_menu']['screen_validate']=array( 'label'=>'Screen', 'url'=>'screening/screen_paper_validation', 'icon'=>'');
				$menu['general']['menu']['papers_screen_validate']['sub_menu']['screen_validate_assignments'] = array('label' => 'All Assignments', 'url' => 'element/entity_list/list_assignments_validation', 'icon' => '');
				$menu['general']['menu']['papers_screen_validate']['sub_menu']['screen_validate_screenings'] = array('label' => 'Validated Papers', 'url' => 'element/entity_list/list_screenings_validation', 'icon' => '');
				$menu['general']['menu']['papers_screen_validate']['sub_menu']['screen_validate_completion'] = array('label' => 'Progress ', 'url' => 'screening/screen_completion/validate', 'icon' => '');
				$menu['general']['menu']['papers_screen_validate']['sub_menu']['screen_validate_result'] = array('label' => 'Statistics', 'url' => 'screening/screen_validation_result', 'icon' => '');
			}
			//$menu['general']['menu']['result']=array('label'=>'Result','url'=>'screening/screen_result','icon'=>'bar-chart');

			if (can_review_project() and !$project_published) { //Guest cannot sccess administration
				$menu['adm'] = array(
					'label' => 'ADMINISTRATION'
				);

				$menu['adm']['menu']['plan'] = array('label' => 'Planning', 'url' => '', 'icon' => 'th');
				if (can_manage_project())
					$menu['adm']['menu']['plan']['sub_menu']['assignment_screen'] = array('label' => 'Assign Screening', 'url' => 'screening/assignment_screen', 'icon' => '');

				if (can_validate_project())
					$menu['adm']['menu']['plan']['sub_menu']['validate_screen_assign'] = array('label' => 'Assign Validation', 'url' => 'screening/validate_screen_set', 'icon' => '');

				$menu['adm']['menu']['plan']['sub_menu']['inclusioncriteria'] = array('label' => 'Inclusion Criteria', 'url' => 'element/entity_list/list_inclusioncriteria', 'icon' => '');

				$menu['adm']['menu']['plan']['sub_menu']['exclusioncrieria'] = array('label' => 'Exclusion Criteria', 'url' => 'element/entity_list/list_exclusioncrieria', 'icon' => '');

				if (can_validate_project())
					$menu['adm']['menu']['plan']['sub_menu']['general'] = array('label' => 'Settings ', 'url' => 'element/display_element/configurations/1', 'icon' => '');
			}
		}

		//*
		//$menu['screen']['menu']['screen_result']=array( 'label'=>'Screening result', 'url'=>'home', '');
		//
		/*
					$menu['settings']=array('label'=>'Go To');
					
					$menu['settings']['menu']['admin']=array('label'=>'General view','url'=>'screening/screening_select','icon'=>'paper-plane');
					
					*/
		return $menu;
	}

	//responsible for generating a left menu
	function get_left_menu_screen_select()
	{
		$can_manage_project = can_manage_project();
		$project_published = project_published();
		$menu = array();

		$menu['general'] = array(
			'label' => 'General'
		);
		$menu['general']['menu']['home'] = array('label' => 'Project', 'url' => 'screening/screening_select', 'icon' => 'home');

		if (get_appconfig_element('import_papers_on') and $can_manage_project and !$project_published) {
			$menu['general']['menu']['import_papers'] = array('label' => 'Import Papers', 'url' => '', 'icon' => 'upload');
			$menu['general']['menu']['import_papers']['sub_menu']['csv'] = array('label' => 'Import CSV', 'url' => 'paper/import_papers', '');
			$menu['general']['menu']['import_papers']['sub_menu']['bibtex'] = array('label' => 'Import BibTeX', 'url' => 'paper/import_bibtext', '');
			$menu['general']['menu']['import_papers']['sub_menu']['endnote'] = array('label' => 'Import EndNote', 'url' => 'paper/import_endnote', '');
		}
		$menu['general']['menu']['papers'] = array('label' => 'Papers', 'url' => '', 'icon' => 'newspaper-o');
		$menu['general']['menu']['papers']['sub_menu']['all_papers'] = array('label' => 'All', 'url' => 'element/entity_list/list_all_papers', '');
		$menu['general']['menu']['papers']['sub_menu']['screen_paper_pending'] = array('label' => 'Pending', 'url' => 'element/entity_list/list_pending_papers', '');
		$menu['general']['menu']['papers']['sub_menu']['screen_paper_included'] = array('label' => 'Included', 'url' => 'element/entity_list/list_included_papers', '');
		$menu['general']['menu']['papers']['sub_menu']['screen_paper_excluded'] = array('label' => 'Excluded', 'url' => 'element/entity_list/list_excluded_papers', '');
		$menu['general']['menu']['venues'] = array('label' => 'Venues', 'url' => 'element/entity_list/list_venues', 'icon' => 'th');
		$menu['general']['menu']['authors'] = array('label' => 'Authors', 'url' => 'element/entity_list/list_authors', 'icon' => 'users');
		$menu['general']['menu']['authors']['sub_menu']['all_authors'] = array('label' => 'All', 'url' => 'element/entity_list/list_authors', '');
		$menu['general']['menu']['authors']['sub_menu']['first_authors'] = array('label' => 'First authors', 'url' => 'element/entity_list/list_first_authors', '');
		$menu['general']['menu']['authors']['sub_menu']['affiliation'] = array('label' => 'Affiliations', 'url' => 'element/entity_list/list_affiliation', '');

		if ($can_manage_project) {
			//
			////$menu['general']['menu']['venues']=array('label'=>'Venues','url'=>'element/entity_list/list_venues','icon'=>'list');
			if (can_manage_project())
				$menu['general']['menu']['sql_query'] = array('label' => 'Query Database', 'url' => 'home/sql_query', 'icon' => 'database');


			$menu['settings'] = array('label' => 'ADMINISTRATION');
			$menu['general']['menu']['users'] = array('label' => 'Users', 'url' => 'element/entity_list/list_users_current_projects', 'icon' => 'user');

			$menu['settings']['menu']['configuration'] = array('label' => 'Planning', 'url' => 'element/display_element/configurations/1', 'icon' => 'th');
			$menu['settings']['menu']['configuration']['sub_menu']['settings'] = array('label' => 'Settings ', 'url' => 'element/display_element/configurations/1', 'icon' => '');
			//$menu['settings']['menu']['configuration']['sub_menu']['users']=array('label'=>'Papers configuration','url'=>'element/display_element/config_papers/1','icon'=>'');

			//	if(get_appconfig_element('screening_on'))
			//$menu['settings']['menu']['configuration']['sub_menu']['screen']=array('label'=>'Screening configuration','url'=>'element/display_element/config_screening/1','icon'=>'');

			//$menu['settings']['menu']['configuration']['sub_menu']['qa']=array('label'=>'QA configuration','url'=>'element/display_element/config_qa/1','icon'=>'');
			//$menu['settings']['menu']['configuration']['sub_menu']['class']=array('label'=>'Classification configuration','url'=>'element/display_element/config_class/1','icon'=>'');

			//$menu['settings']['menu']['configuration']['sub_menu']['dsl']=array('label'=>'DSL configuration','url'=>'element/display_element/config_dsl/1','icon'=>'');


			//$menu['settings']['menu']['configuration']['sub_menu']['space']=array('label'=>'_______________','url'=>'','icon'=>'');
			$menu['settings']['menu']['configuration']['sub_menu']['research_question'] = array('label' => 'Research Questions', 'url' => 'element/entity_list/list_research_question', 'icon' => '');

			if (get_appconfig_element('screening_on'))
				$menu['settings']['menu']['configuration']['sub_menu']['screen_phases'] = array('label' => 'Screening Phases', 'url' => 'element/entity_list/list_screen_phases', 'icon' => '');

			$menu['settings']['menu']['configuration']['sub_menu']['exclusioncrieria'] = array('label' => 'Exclusion Criteria', 'url' => 'element/entity_list/list_exclusioncrieria', 'icon' => '');
			$menu['settings']['menu']['configuration']['sub_menu']['inclusioncrieria'] = array('label' => 'Inclusion Criteria', 'url' => 'element/entity_list/list_inclusioncriteria', 'icon' => '');


			$menu['settings']['menu']['configuration']['sub_menu']['papers_sources'] = array('label' => 'Papers Sources', 'url' => 'element/entity_list/list_papers_sources', 'icon' => '');
			$menu['settings']['menu']['configuration']['sub_menu']['search_strategy'] = array('label' => 'Search Strategies', 'url' => 'element/entity_list/list_search_strategy', 'icon' => '');

			$menu['settings']['menu']['operations'] = array('label' => 'Operations Management', 'url' => 'element/entity_list/list_operations', 'icon' => 'reorder');
			$menu['settings']['menu']['str_mng'] = array('label' => 'Label Management', 'url' => 'element/entity_list/list_str_mng', 'icon' => 'text-width');
			$menu['settings']['menu']['install_form_editor'] = array('label' => 'Update Project Config', 'url' => 'install/install_form_editor', 'icon' => 'refresh');

			$menu['settings']['menu']['Configuration_managment'] = array('label' => 'Configuration_managment', 'url' => 'admin/list_configurations', 'icon' => 'cog');
			if (debug_coment_active())
				$menu['settings']['menu']['debug'] = array('label' => 'Debug Comment', 'url' => 'element/entity_list/list_debug', 'icon' => 'cogs');

		}

		return $menu;
	}
}