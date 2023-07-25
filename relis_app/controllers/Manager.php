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
class Manager extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	/*
	 * Fonction globale pour afficher la liste des élément suivant la structure de la table
	 *
	 * Input: $ref_table: nom de la configuration d'une page (ex papers, author)
	 * 			$val : valeur de recherche si une recherche a été faite sur la table en cours
	 * 			$page: la page affiché : ulilisé dans la navigation
	 */
	public function entity_list($ref_table, $val = "_", $page = 0, $dynamic_table = 0)
	{
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
			$url = "manager/entity_list/" . $ref_table . "/" . $val . "/0/";
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
		if ($ref_table == "str_mng") { //pour le string_management une fonction spéciale
			//todo verifier comment le spécifier dans config
			$data = $this->DBConnection_mdl->get_list_str_mng($ref_table_config, $val, $page, $rec_per_page, $this->session->userdata('active_language'));
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
							break;
						case 'edit':
							if (!isset($link['icon']))
								$link['icon'] = 'pencil';
							if (empty($link['url']))
								$link['url'] = 'manager/edit_element/' . $ref_table . '/';
							$push_link = true;
							break;
						case 'delete':
							if (!isset($link['icon']))
								$link['icon'] = 'trash';
							if (empty($link['url']))
								$link['url'] = 'manager/delete_element/' . $ref_table . '/';
							$push_link = true;
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
				array_push($arr_buttons, array(
					'url' => $value_l['url'] . $value[$table_id],
					'label' => $value_l['label'],
					'title' => $value_l['title']
				));
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
		if ($this->session->userdata('working_perspective') == 'class') {
			$data['top_buttons'] .= get_top_button('close', 'Close', 'home');
		} else {
			$data['top_buttons'] .= get_top_button('close', 'Close', 'screening/screening');
		}
		/*
		 * Titre de la page
		 */
		if (isset($ref_table_config['entity_title']['list'])) {
			$data['page_title'] = lng($ref_table_config['entity_title']['list']);
		} else {
			$data['page_title'] = lng("List of " . $ref_table_config['reference_title']);
		}
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
			$data['nav_pre_link'] = 'manager/entity_list/' . $ref_table . '/' . $val . '/';
			$data['nav_page_position'] = 5;
			$data['page'] = 'general/list';
		} else {
			$data['page'] = 'general/list_dt';
		}
		if (admin_config($ref_table))
			$data['left_menu_admin'] = True;
		//print_test($data);
		/*
		 * Chargement de la vue avec les données préparés dans le controleur
		 */
		$this->load->view('shared/body', $data);
	}

	/*
	 * Fonction pour l'affichage d'un élément
	 * Input :	$ref_table : nom de la structure de l'element à afficher
	 * 			$ref_id : id de l'élément
	 *
	 */
	public function display_element($ref_table, $ref_id, $allow_redirect = "yes")
	{
		// todo correction gestion des utilisateurs
		if (admin_config($ref_table))
			$data['left_menu_admin'] = True;
		/*
		 * todo corriger cette redirection
		 * 
		 * Redirection vers la fonction spécialise pour l'affichage d'un papier si l'element à afficher est un papier
		 */
		if ($ref_table == 'papers' and $allow_redirect == 'yes') {
			redirect('data_extraction/display_paper/' . $ref_id);
		} elseif ($ref_table == 'classification' and $allow_redirect == 'yes') {
			$paper_id = $this->Data_extraction_dataAccess->get_classification_paper($ref_id);
			redirect('data_extraction/display_paper/' . $paper_id);
		}
		if (!($this->session->userdata('project_db')) and $ref_table == 'config') {
			redirect('home');
		}
		/*
		 * Appel de la fonction  récupérer la ligne à afficher
		 */
		$item_data = $this->manager_lib->get_element_detail($ref_table, $ref_id);
		$data['item_data'] = $item_data;
		/*
		 * Récupération de la configuration(structure) de la table de l'élément
		 */
		$table_config = get_table_config($ref_table);
		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data['top_buttons'] = "";
		if (!empty($table_config['links']['add_child']['url']) and !empty($table_config['links']['add_child']['on_view']) and ($table_config['links']['add_child']['on_view'] == True)) {
			$data['top_buttons'] .= get_top_button('all', $table_config['links']['add_child']['title'], 'manager/add_element_child/' . $table_config['links']['add_child']['url'] . "/" . $ref_table . '/' . $ref_id, $table_config['links']['add_child']['label']) . " ";
		}
		if (!empty($table_config['links']['edit']) and !empty($table_config['links']['edit']['on_view']) and ($table_config['links']['edit']['on_view'] == True)) {
			$pre_url = isset($table_config['links']['edit']['url']) ? $table_config['links']['edit']['url'] : 'manager/edit_element/' . $ref_table . '/';
			$data['top_buttons'] .= get_top_button('edit', $table_config['links']['edit']['title'], $pre_url . $ref_id) . " ";
		}
		if (!empty($table_config['links']['delete']) and !empty($table_config['links']['delete']['on_view']) and ($table_config['links']['delete']['on_view'] == True)) {
			if ($ref_table == 'project') {
				$data['top_buttons'] .= get_top_button('delete', $table_config['links']['delete']['title'], 'project/remove_project_validation/' . $ref_id) . " ";
			} else {
				$pre_url = isset($table_config['links']['delete']['url']) ? $table_config['links']['delete']['url'] : 'manager/delete_element/';
				$data['top_buttons'] .= get_top_button('delete', $table_config['links']['delete']['title'], $pre_url . $ref_table . '/' . $ref_id) . " ";
			}
		}
		$data['top_buttons'] .= get_top_button('back', 'Back', 'manage');
		/*
		 * Titre de la page
		 */
		if (isset($table_config['entity_title']['view'])) {
			$data['page_title'] = lng($table_config['entity_title']['view']);
		} else {
			$data['page_title'] = lng($table_config['reference_title_min']);
		}
		/*
		 * La vue qui va s'afficher
		 */
		$data['page'] = 'element/display_element';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	/*
	 * Fonction  pour afficher la page avec un formulaire d'ajout ou modification d'un élément
	 *
	 * Input: 	$ref_table: nom de la structure de la table pour l'élément à afficher
	 * 			$data : informations sur l'élément si la fonction est utilisé pour la mis à jour(modification)
	 * 			$operation: type de l'opération ajout (new) ou modification(edit)
	 * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */
	public function add_element($ref_table, $data = "", $operation = 'new', $display_type = "normal")
	{
		if ($ref_table == 'papers') { //Use bibler for papers management
			redirect("paper/bibler_add_paper");
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
		$table_config = get_table_config($ref_table);
		//print_test($table_config);
		$table_config['config_id'] = $ref_table;
		$type_op = $operation == 'new' ? "on_add" : "on_edit";
		/*
		 * récupération des valeurs qui vont apparaitres dans les dropdown boxes
		 */
		foreach ($table_config['fields'] as $k => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select') {
				if ($v['input_select_source'] == 'table' and ($v[$type_op] == 'enabled' or $v[$type_op] == 'disabled')) {
					if (isset($table_config['fields'][$k]['multi-select']) and $table_config['fields'][$k]['multi-select'] == 'Yes') {
						$table_config['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], False, False, True);
					} else {
						$table_config['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], True, False);
					}
				}
			}
		}
		/*
		 * Prépartions des valeurs qui vont apparaitres dans le formulaire
		 */
		$title_append = $table_config['reference_title_min'];
		$data['table_config'] = $table_config;
		/*
		 * Titre de la page
		 */
		if ($operation == 'new') {
			// La fonction qui va traiter l'enregistrement dans la DB;
			$data['save_function'] = isset($table_config['save_new_function']) ? $table_config['save_new_function'] : 'manager/save_element';
			if (isset($table_config['entity_title']['add'])) {
				$data['page_title'] = lng($table_config['entity_title']['add']);
			} else {
				$data['page_title'] = lng('Add ' . $title_append);
			}
		} else {
			$data['save_function'] = isset($table_config['save_edit_function']) ? $table_config['save_edit_function'] : 'manager/save_element';
			if (isset($table_config['entity_title']['edit'])) {
				$data['page_title'] = lng($table_config['entity_title']['edit']);
			} else {
				$data['page_title'] = lng('Edit ' . $title_append);
			}
		}
		$data['operation_type'] = $operation;
		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data['top_buttons'] = get_top_button('back', 'Back', 'manage');
		/*
		 * La vue qui va s'afficher
		 */
		$data['page'] = 'general/frm_reference';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		if ($display_type == 'modal') {
			$this->load->view('general/frm_reference_modal', $data);
		} else {
			$this->load->view('shared/body', $data);
		}
	}

	/*
	 * Fonction  pour afficher la page avec un formulaire d'ajout d'un élément avec une clé externe provenant de l'element  parent (exemple ajout d'un utilisateur à partir d'un groupe d'utilisateur)
	 *
	 * Input: 	$ref_table_child:le nom de la structure de l'élément enfant
	 * 			$child_field: le nom de la clé externe dans la table enfant
	 * 			$ref_table_parent:le nom de la structure de l'élement parent
	 * 			$parent_id: l'id de l'élément parent
	 * 			$data : informations sur l'élément enfant si la fonction est utilisé pour la mis à jour(modification)
	 * 			$operation: type de l'opération ajout (new) ou modification(edit)
	 * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */
	/*
	 * spécialisation de la fonction add_ref_child lorsque le formulaire s'affiche en pop up
	 */
	public function add_element_child_modal($ref_table_child = "users", $child_field = "user_usergroup", $ref_table_parent = "usergroup", $parent_id = 2, $data = "", $operation = "new", $display_type = "normal")
	{
		$this->add_element_child($ref_table_child, $child_field, $ref_table_parent, $parent_id, $data, $operation, "modal");
	}

	//The purpose of this function is to render a form for adding a child element to a parent element in a reference table
	public function add_element_child($ref_table_child, $child_field, $ref_table_parent, $parent_id, $data = "", $operation = "new", $display_type = "normal")
	{
		if (admin_config($ref_table_child))
			$data['left_menu_admin'] = True;
		/*
		 * chargement de la manière d'affichage du formulaire
		 */
		$this->session->set_userdata('submit_mode', $display_type);
		/*
		 * Récupération de la configuration(structure) de la table enfant
		 */
		$table_config_child = get_table_config($ref_table_child);
		$table_config_child['config_id'] = $ref_table_child;
		$table_config_parent = get_table_config($ref_table_parent);
		$table_config_child['fields'][$child_field]['on_add'] = "hidden";
		$table_config_child['fields'][$child_field]['on_edit'] = "hidden";
		$table_config_child['fields'][$child_field]['input_type'] = "text";
		/*
		 * récupération des valeurs qui vont apparaitre dans les champs select
		 */
		//print_test($table_config_parent);
		foreach ($table_config_child['fields'] as $k => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select') {
				//	print_test($v);
				if ($v['input_select_source'] == 'table') {
					if (isset($table_config_child['fields'][$k]['multi-select']) and $table_config_child['fields'][$k]['multi-select'] == 'Yes') {
						$table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], False, False, True);
					} else {
						$table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values']);
					}
				}
			}
		}
		/*
		 * Prépartions des valeurs qui vont apparaitres dans le formulaire
		 */
		$data['content_item'][$child_field] = $parent_id;
		$data['table_config'] = $table_config_child;
		$data['operation_type'] = $operation;
		$data['operation_source'] = "parent";
		$data['child_field'] = $child_field;
		$data['table_config_parent'] = $ref_table_parent;
		$data['parent_id'] = $parent_id;
		/*
			   * Titre de la page
			  
			   */
		if (isset($table_config_parent['entity_title']['add_child'])) {
			$data['page_title'] = lng($table_config_parent['entity_title']['add_child']);
		} else {
			$data['page_title'] = lng('Add ' . $table_config_child['reference_title_min']);
		}
		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data['top_buttons'] = get_top_button('back', 'Back', 'manage');
		/*
		 * La vue qui va s'afficher
		 */
		$data['page'] = 'general/frm_reference';
		//print_test($data);
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		if ($display_type == 'modal') {
			$this->load->view('general/frm_reference_modal', $data);
		} else {
			$this->load->view('shared/body', $data);
		}
	}

	/*
	 * spécialisation de la fonction add_ref_drilldown lorsque le formulaire s'affiche en pop up
	 */
	public function add_element_drilldown_modal($ref_table_child, $ref_table_parent, $parent_field, $parent_id, $data = "", $operation = "new")
	{
		$this->add_element_drilldown($ref_table_child, $ref_table_parent, $parent_field, $parent_id, $data, $operation, "modal");
	}

	/*
	 * Fonction  pour afficher la page avec un formulaire d'ajout d'un élément avec une clé externe provenant de l'élément enfant
	 *
	 * Input: 	$ref_table_child:le nom de la structure de l'élément enfant
	 * 			$ref_table_parent:le nom de la structure de l'élément parent
	 * 			$child_field: le champs qui va prendre la clé de l'element enfant
	 *			$parent_id: l'id de l'element parent
	 * 			$data : informations sur l'element enfant si la fonction est utilisé pour la mis à jour(modification)
	 * 			$operation: type de l'opération ajout (new) ou modification(edit)
	 * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */
	public function add_element_drilldown($ref_table_child, $ref_table_parent, $parent_field, $parent_id, $data = "", $operation = "new", $display_type = "normal")
	{
		/*
		 * chargement de la manière d'affichage du formulaire
		 */
		$this->session->set_userdata('submit_mode', $display_type);
		/*
		 * Récupération de la configuration(structure) de la table enfant
		 */
		$table_config_child = get_table_config($ref_table_child);
		$table_config_child['config_id'] = $ref_table_child;
		/*
		 * Récupération de la configuration(structure) de la table parent
		 */
		$table_config_parent = get_table_config($ref_table_parent);
		$op_type = ($operation == 'new') ? 'on_add' : 'on_edit';
		/*
		 * récupération des valeurs qui vont apparaitre dans les champs select
		 */
		foreach ($table_config_child['fields'] as $k => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select' and ($v[$op_type] != 'hidden' and $v[$op_type] != 'not_set')) {
				if ($v['input_select_source'] == 'table') {
					if (isset($table_config_child['fields'][$k]['multi-select']) and $table_config_child['fields'][$k]['multi-select'] == 'Yes') {
						$table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], False, False, True);
					} else {
						$table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values']);
					}
				}
			}
		}
		/*
		 * Prépartions des valeurs qui vont apparaitres dans le formulaire
		 */
		$data['parent_field'] = $parent_field;
		$data['parent_id'] = $parent_id;
		$data['parent_table'] = $table_config_parent['table_name'];
		$data['table_config'] = $table_config_child;
		$data['operation_type'] = $operation;
		$data['operation_source'] = "drilldown";
		$data['parent_field'] = $parent_field;
		$data['table_config_parent'] = $ref_table_parent;
		$data['parent_id'] = $parent_id;
		/*
		 * Titre de la page
		 */
		if ($operation == 'new')
			$data['page_title'] = lng('Add ' . $table_config_parent['fields'][$parent_field]['field_title']);
		else
			$data['page_title'] = lng('Edit ' . $table_config_parent['fields'][$parent_field]['field_title']);
		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data['top_buttons'] = get_top_button('back', 'Back', 'manage');
		/*
		 * La vue qui va s'afficher
		 */
		$data['page'] = 'general/frm_reference';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		if ($display_type == 'modal') {
			$this->load->view('general/frm_reference_modal', $data);
		} else {
			$this->load->view('shared/body', $data);
		}
	}

	/*
	 * Affichage du formulaire pour modifier un élément
	 * $ref_table: le nom de la structure de  la table de l'élément
	 * $ref_id: id de l'élement
	 * $display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */
	public function edit_element($ref_table, $ref_id, $display_type = "normal")
	{
		/*
		 * Récupération de la configuration(structure) de la table de l'element
		 */
		if ($ref_table == 'papers') {
			redirect("paper/bibler_edit_paper/" . $ref_id . "/");
		}
		$table_config = get_table_config($ref_table);
		/*
		 * Appel de la fonction du model pour récupérer la ligne à modifier
		 */
		$data['content_item'] = $this->DBConnection_mdl->get_row_details($ref_table, $ref_id);
		/*
		 * Récuperation des valeurs pour les champs multi-select
		 */
		foreach ($table_config['fields'] as $key => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['input_select_source'] == 'table') {
				if (!empty($v['multi-select']) and $v['multi-select'] == 'Yes') {
					$Tvalues_source = explode(';', $v['input_select_values']);
					$source_table_config = get_table_config($Tvalues_source[0]);
					$input_select_key_field = $v['input_select_key_field'];
					$input_child_field = $Tvalues_source[1];
					$extra_condition = " AND $input_select_key_field ='" . $ref_id . "'";
					$res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $input_child_field, $extra_condition);
					$data['content_item'][$key] = array();
					foreach ($res_values as $key_r => $value_r) {
						array_push($data['content_item'][$key], $value_r['refDesc']);
					}
				}
			}
		}
		/*
		 * Appel de la fonction d'affichage du formulaire
		 */
		$this->add_element($ref_table, $data, 'edit', $display_type);
	}

	/*
	 * Fonction  pour afficher la page avec un formulaire un élément enfant
	 *
	 * Input: 	$ref_table:le nom de la structure de l'élément enfant
	 * 			$ref_table_parent:le nom de la structure de l'élément parent
	 * 			$parent_field: le champs qui va prendre la clé de l'element enfant
	 *			$parent_id: l'id de l'élément parent
	 *			$ref_id: id de l'element à modifier
	 * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */
	public function edit_drilldown($ref_table, $ref_table_parent, $parent_field, $parent_id, $ref_id, $display_type = "normal")
	{
		$this->session->set_userdata('submit_mode', $display_type);
		/*
		 * Récupération de la configuration(structure) de la table de l'element
		 */
		$table_config = get_table_config($ref_table);
		/*
		 * Appel de la fonction du model pour récupérer la ligne à modifier
		 */
		$data['content_item'] = $this->DBConnection_mdl->get_row_details($ref_table, $ref_id);
		/*
		 * Récuperation des valeurs pour les champs multi-select
		 */
		foreach ($table_config['fields'] as $key => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['input_select_source'] == 'table') {
				if (!empty($v['multi-select']) and $v['multi-select'] == 'Yes') {
					$Tvalues_source = explode(';', $v['input_select_values']);
					$source_table_config = $this->ref_table_config($Tvalues_source[0]);
					$input_select_key_field = $v['input_select_key_field'];
					$input_child_field = $Tvalues_source[1];
					$extra_condition = " AND $input_select_key_field ='" . $ref_id . "'";
					$res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $input_child_field, $extra_condition);
					$data['content_item'][$key] = array();
					foreach ($res_values as $key_r => $value_r) {
						array_push($data['content_item'][$key], $value_r['refDesc']);
					}
				}
			}
		}
		/*
		 * Appel de la fonction d'affichage du formulaire
		 */
		$this->add_element_drilldown($ref_table, $ref_table_parent, $parent_field, $parent_id, $data, 'edit', $display_type);
	}

	//handle the validation, saving, and additional actions associated with saving form data in the specified table
	public function save_element()
	{
		/*
		 * Récuperation des valeurs soumis dans le formulaire
		 */
		$post_arr = $this->input->post();
		//print_test($post_arr); exit;
		/*
		 * Récupération de la configuration (structure ) de la table qui est concerné
		 */
		$table_config = get_table_config($post_arr['table_config']);
		if ($post_arr['operation_type'] == 'new') {
			$var_check = "on_add";
		} else {
			$var_check = "on_edit";
		}
		$operation_type = $post_arr['operation_type'];
		/*
		 * Validation du formulaire: vérification si les valeurs sont rémplis correctement
		 */
		$this->load->library('form_validation');
		$other_check = true;
		$data['err_msg'] = ''; //for users
		$images_to_upload = array();
		$multi_select_values = array();
		foreach ($table_config['fields'] as $key => $value) {
			$validation = "trim";
			if ($value[$var_check] == 'enabled') {
				if (!empty($value['mandatory']) and (trim($value['mandatory']) == "mandatory")) {
					if ((isset($value['multi-select']) and isset($value['multi-select']) == 'Yes')) {
						if (empty($post_arr[$key])) {
							$other_check = false;
							$data['err_msg'] .= " The Field '" . $value['field_title'] . "' is required<br/>";
						}
					} else {
						$validation .= "|required";
					}
				}
				$this->form_validation->set_rules($key, '"' . $value['field_title'] . '"', $validation);
			}
			if (isset($value['input_type']) and $value['input_type'] == 'email') {
				if (!empty($post_arr[$key])) {
					$this->form_validation->set_rules($key, $value['field_title'], 'trim|valid_email');
				}
			}
			if ((isset($value['multi-select']) and isset($value['multi-select']) == 'Yes')) { //multi- select
				if (!empty($post_arr[$key])) {
					$multi_select_values[$key]['values'] = $post_arr[$key];
					$multi_select_values[$key]['config'] = $value;
				} else {
					$multi_select_values[$key]['values'] = array();
					$multi_select_values[$key]['config'] = $value;
				}
			}
			if (isset($value['input_type']) and $value['input_type'] == 'image' and !empty($_FILES[$key]['name'])) {
				$post_arr[$key] = "";
				if (empty($_FILES[$key]['tmp_name'])) {
					$other_check = false;
					$data['err_msg'] .= lng('Problem with uploading image') . ' <br/>';
				} elseif (!file_exists($_FILES[$key]['tmp_name'])) {
					$other_check = false;
					$data['err_msg'] .= lng('Problem with uploading image') . ' <br/>';
				} elseif (!empty($_FILES[$key]['name'])) {
					$images_to_upload[$key]['info'] = $_FILES[$key];
					$file_extension = "";
					switch ($_FILES[$key]['type']) {
						case 'image/jpeg':
							$file_extension = '.jpg';
							break;
						case 'image/png':
							$file_extension = '.PNG';
							break;
						case 'image/gif':
							$file_extension = '.gif';
							break;
					}
					$image_upload_path = $this->config->item('image_upload_path');
					if ($post_arr['table_config'] == 'author') {
						$file_name = substr(Slug($post_arr['author_name']), 0, 20) . time();
					} elseif ($post_arr['table_config'] == 'users') {
						$file_name = substr(Slug($post_arr['user_name']), 0, 20) . time();
					} else {
						$file_name = time();
					}
					$config['file_name'] = $file_name . $file_extension;
					$config['upload_path'] = FCPATH . $image_upload_path;
					$config['allowed_types'] = 'gif|jpg|png';
					$config['overwrite'] = TRUE;
					$config['remove_spaces'] = TRUE;
					$config['max_filename'] = '0';
					$config['max_size'] = $this->config->item('image_max_size');
					$config['max_width'] = '0'; // should be 360 at destination
					$config['max_height'] = '0'; // should be 300 at destination
					$images_to_upload[$key]['config'] = $config;
					$images_to_upload[$key]['picture_name'] = $file_name;
					//save the name of the picture used
					$post_arr[$key] = $file_name;
				}
			}
		}
		//print_test($post_arr);
		if ($post_arr['table_config'] == 'users') {
			$this->form_validation->set_rules('user_username', $table_config['fields']['user_username']['field_title'], 'trim|required|min_length[2]|max_length[12]');
			if (!(empty($post_arr['user_password']) and $post_arr['operation_type'] == 'edit')) {
				$this->form_validation->set_rules('user_password', $table_config['fields']['user_password']['field_title'], 'trim|required|matches[user_password_val]');
				$this->form_validation->set_rules('user_password_val', $table_config['fields']['user_password']['field_title'] . ' Confirmation', 'trim|required');
			}
			//	if(!empty($post_arr['user_mail'])){
			//		$this->form_validation->set_rules('user_mail', 'Email', 'trim|valid_email');
			//	}
			///vérify if the username is unique
			if (!empty($post_arr['user_username']) and ($post_arr['operation_type'] == 'new') and !$this->bm_lib->login_available($post_arr['user_username'])) {
				$data['err_msg'] .= 'Username already used <br/>';
				$other_check = FALSE;
			}
		}
		$operation_source = $post_arr['operation_source'];
		$parent_id = $post_arr['parent_id'];
		if (isset($post_arr['table_config_parent']))
			$table_config_parent = $post_arr['table_config_parent'];
		if ($this->form_validation->run() == FALSE or !$other_check) {
			/*
			 * Si la validation du formulaire n'est pas concluante , retour au formulaire de saisie
			 */
			$data['content_item'] = $post_arr;
			if ($post_arr['operation_source'] == 'parent') {
				if ($this->session->userdata('submit_mode') and $this->session->userdata('submit_mode') == 'modal') {
					$this->add_element_child_modal($post_arr['table_config'], $post_arr['child_field'], $post_arr['table_config_parent'], $post_arr['parent_id'], $data, $post_arr['operation_type']);
				} else {
					$this->add_element_child($post_arr['table_config'], $post_arr['child_field'], $post_arr['table_config_parent'], $post_arr['parent_id'], $data, $post_arr['operation_type']);
				}
			} elseif ($post_arr['operation_source'] == 'drilldown') {
				if ($this->session->userdata('submit_mode') and $this->session->userdata('submit_mode') == 'modal') {
					$this->add_ref_drilldown_modal($post_arr['table_config'], $post_arr['table_config_parent'], $post_arr['parent_field'], $post_arr['parent_id'], $data, $post_arr['operation_type']);
				} else {
					$this->add_ref_drilldown($post_arr['table_config'], $post_arr['table_config_parent'], $post_arr['parent_field'], $post_arr['parent_id'], $data, $post_arr['operation_type']);
				}
			} elseif ($post_arr['operation_source'] == 'paper') {
				if ($this->session->userdata('submit_mode') and $this->session->userdata('submit_mode') == 'modal') {
					$this->session->set_userdata('redirect_values', $data);
					redirect("data_extraction/new_classification_modal/$parent_id/sess_redirect/" . $post_arr['operation_type']);
					//$this->add_classification_modal ($parent_id,$data,$post_arr['operation_type'] );
				} else {
					$this->session->set_userdata('redirect_values', $data);
					redirect("data_extraction/new_classification/$parent_id/sess_redirect/" . $post_arr['operation_type']);
					//$this->add_classification ($parent_id,$data,$post_arr['operation_type'] );
				}
			} elseif ($post_arr['operation_source'] == 'exclusion') {
				$this->new_exclusion($parent_id, $data, $post_arr['operation_type']);
			} else {
				if ($this->session->userdata('submit_mode') and $this->session->userdata('submit_mode') == 'modal') {
					$this->add_element($post_arr['table_config'], $data, $post_arr['operation_type'], 'modal');
				} else {
					$this->add_element($post_arr['table_config'], $data, $post_arr['operation_type']);
				}
			}
		} else {
			/*
			 * Si la validation du formulaire est concluante, proceder à l'enregistrement
			 */
			/*
			 * Si le formulaire contient des images commencer par les redimentionner puis les sauvergarder
			 */
			$image_upload_result = TRUE;
			if (!empty($images_to_upload)) {
				foreach ($images_to_upload as $k_img => $v_img) {
					$this->load->library('upload', $v_img['config']);
					if (!$this->upload->do_upload($k_img)) {
						$image_upload_result = False;
						$error = array(
							'error' => $this->upload->display_errors()
						);
						$data['err_msg'] = $error['error'];
						//echo $data ['err_msg'] ;
					} else {
						// resizing images
						// image size after resizing
						/*$thumb_size=$this->config->item('image_thumb_size');
										  $medium_size=$this->config->item('image_medium_size');
										  $big_size=$this->config->item('image_big_size');
										  */
						$image_default_size = $this->config->item('image_default_size');
						$image_upload_path = $this->config->item('image_upload_path');
						$image_data = $this->upload->data();
						$config_default = array(
							'source_image' => $image_data['full_path'],
							'new_image' => FCPATH . $image_upload_path . $v_img['picture_name'] . "_resized.jpg",
							'maintain_ratio' => true,
							'width' => $image_default_size,
							'height' => $image_default_size
						);
						/*
										   $config_thumb=array(
												   'source_image'=>$image_data['full_path'],
												   'new_image' => FCPATH.$image_upload_path.$v_img['picture_name']."_thumb.jpg",
												   'maintain_ratio' => true,
												   'width' => $thumb_size,
												   'height' => $thumb_size
										   );
					  
										   $config_medium=array(
												   'source_image'=>$image_data['full_path'],
												   'new_image' => FCPATH.$image_upload_path.$v_img['picture_name']."_med.jpg",
												   'maintain_ration' => true,
												   'width' => $medium_size,
												   'height' => $medium_size
										   );
										  
									   
										   $config_big=array(
												   'source_image'=>$image_data['full_path'],
												   'new_image' => FCPATH.$image_upload_path.$v_img['picture_name']."_big.jpg",
												   'maintain_ration' => true,
												   'width' => $big_size,
												   'height' =>$big_size
										   );
										  */
						$this->load->library('image_lib');
						$this->image_lib->initialize($config_thumb);
						//	print_test($config_thumb);
						/*
										  if ( ! $this->image_lib->resize())
										  {
											  die($this->image_lib->display_errors());
										  }
					 
										  $this->image_lib->clear();
										   
										  $this->image_lib->initialize($config_medium);
										   
										  if ( ! $this->image_lib->resize())
										  {
											  die($this->image_lib->display_errors());
										  }
					 
					 
										  $this->image_lib->clear();
										   
										  $this->image_lib->initialize($config_big);
										  if ( ! $this->image_lib->resize())
										  {
											  die($this->image_lib->display_errors());
										  }
										  */
						$this->image_lib->clear();
						$this->image_lib->initialize($config_default);
						if (!$this->image_lib->resize()) {
							die($this->image_lib->display_errors());
						}
						$res_image = $config_default['new_image'];
						$fp = fopen($res_image, 'r');
						$data_img = fread($fp, filesize($res_image));
						$data_img = addslashes($data_img);
						fclose($fp);
						$post_arr[$k_img] = $data_img;
						//remove temp images
						unlink($config_default['new_image']);
						//unlink($config_medium['new_image']);
						//unlink($config_big['new_image']);
						unlink($config_default['source_image']);
					}
				}
			}
			if (!$image_upload_result) {
				/*
				 * Si le chagement d'image de marche pas retour au formulaire d'ajout
				 */
				$data['content_item'] = $post_arr;
				if ($post_arr['operation_source'] == 'parent') {
					$this->add_element_child($post_arr['table_config'], $post_arr['child_field'], $post_arr['table_config_parent'], $post_arr['parent_id'], $data, $post_arr['operation_type']);
				} elseif ($post_arr['operation_source'] == 'drilldown') {
					$this->add_element_drilldown($post_arr['table_config'], $post_arr['table_config_parent'], $post_arr['parent_field'], $post_arr['parent_id'], $data, $post_arr['operation_type']);
				} elseif ($post_arr['operation_source'] == 'paper') {
					$this->load->library('../controllers/relis/manager');
					$this->manager->new_classification($parent_id, $data, $post_arr['operation_type']);
				} elseif ($post_arr['operation_source'] == 'exclusion') {
					$this->new_exclusion($parent_id, $data, $post_arr['operation_type']);
				} else {
					$this->add_element($post_arr['table_config'], $data, $post_arr['operation_type']);
				}
			} else {
				/*
				 * Pour les utilisateur, si lors de la modification on a pas saisie un mot de passe ou ajouter une nouvelle photo , on garde les anciennes valeurs
				 */
				if ($post_arr['table_config'] == 'users') {
					if (!(empty($post_arr['user_password']) and $post_arr['operation_type'] == 'edit')) {
						$post_arr['user_password'] = md5($post_arr['user_password']);
					} else {
						if (!empty($post_arr['user_password_old'])) {
							$post_arr['user_password'] = $post_arr['user_password_old'];
						} else {
							unset($post_arr['user_password']);
						}
					}
					if ($post_arr['operation_type'] == 'edit' and empty($post_arr['user_picture'])) {
						$post_arr['user_picture'] = !empty($post_arr['user_picture_old']) ? $post_arr['user_picture_old'] : "";
					}
					unset($post_arr['user_password_val']);
					unset($post_arr['user_picture_old']);
				}
				/*
				 * Préparation des données avant l'appel de la foction qui va suvegarder les valeurs dans la BD
				 */
				$drill_table_config_parent = isset($post_arr['table_config_parent']) ? $post_arr['table_config_parent'] : "";
				$drill_parent_field = isset($post_arr['parent_field']) ? $post_arr['parent_field'] : "";
				$drill_parent_id = isset($post_arr['parent_id']) ? $post_arr['parent_id'] : 0;
				$drill_parent_table = isset($post_arr['parent_table']) ? $post_arr['parent_table'] : "";
				unset($post_arr['operation_source']);
				unset($post_arr['child_field']);
				unset($post_arr['table_config_parent']);
				unset($post_arr['parent_id']);
				unset($post_arr['parent_field']);
				unset($post_arr['parent_table']);
				$post_arr['table_name'] = $table_config['table_name'];
				$post_arr['table_id'] = $table_config['table_id'];
				//	print_test($post_arr); exit;
				/*
				 * Appel de la fonction dna le modèle pour suvegarder les données dans la BD
				 */
				$saved_res = $this->DBConnection_mdl->save_reference($post_arr, 'get_id');
				if ($saved_res) {
					echo ("Enregistrement reussit");
					set_top_msg("Success");
					if ($operation_source == 'exclusion') {
						/*
						 * Pour l'exclusion d'un papier après la sauvegarde des info sur l'exclusion on appelle une fonction pour mettre à jour le papier
						 */
						$res = $this->Paper_dataAccess->exclude_paper($parent_id);
					}
					/*
					 * Pour l'enregistrement d'un élément enfant on met à jour la clef externe dans élément parent
					 */
					if ($operation_source == 'drilldown' and $operation_type == 'new') {
						$table_config_parent = get_table_config($drill_table_config_parent);
						$parent_table_id = $table_config_parent['table_id'];
						$array_drill = array(
							'operation_type' => 'edit',
							'table_config' => $drill_table_config_parent,
							'table_name' => $table_config_parent['table_name'],
							'table_id' => $parent_table_id,
							$parent_table_id => $drill_parent_id,
							$drill_parent_field => $saved_res,
						);
						$res_drill = $this->manage_mdl->save_reference($array_drill);
					}
					/*
					 * Si le formulaire contient des champs multi-select : on appel une fonction pour sauvegarder ces valeurs dans leur table
					 */
					if (!empty($multi_select_values)) {
						$this->save_multi_select($multi_select_values, $saved_res);
					}
					$message_modal = "modal_relis_outputmessage_correct";
				} else {
					// erreur d'enregistrement
					$message_modal = "modal_relis_outputmessage_error";
				}
				if ($post_arr['table_config'] == 'config')
					update_paper_status_all();
				if ($this->session->userdata('submit_mode') and $this->session->userdata('submit_mode') == 'modal') {
					/*
					 * Si le formulaire sauvegardé est affiché en popup on retourne le message d'erreur
					 */
					echo $message_modal;
				} else {
					/*
					 * Si le formulaire sauvegardé n'est affiché en popup on redirige vers la page d'affichage suivant l'élément enregistre
					 */
					$after_after_save_redirect = $this->session->userdata('after_save_redirect');
					if (!empty($after_after_save_redirect)) {
						$this->session->set_userdata('after_save_redirect', '');
						redirect($after_after_save_redirect);
					} elseif ($operation_source == 'paper' or $operation_source == 'assignation' or $operation_source == 'exclusion') {
						redirect('data_extraction/display_paper/' . $parent_id);
					} elseif ($operation_source == 'drilldown') {
						redirect('manager/display_element/' . $drill_table_config_parent . '/' . $drill_parent_id);
					} elseif ($operation_source == 'parent' and !empty($table_config_parent) and !empty($parent_id)) {
						redirect('manager/display_element/' . $table_config_parent . '/' . $parent_id);
					} else {
						if ($table_config['table_name'] == 'paper') {
							//redirect ( 'paper/list_paper');
							redirect('data_extraction/display_paper/' . $saved_res);
						} else {
							redirect('manager/entity_list/' . $post_arr['table_config']);
						}
					}
				}
			}
		}
	}

	/*
	 * Fonction  pour afficher la page avec un formulaire pour l'exclusion d'un papier
	 *
	 * Input: 	$paper_id: l'id du papier
	 * 			$data : informations sur le papier si la fonction est utilisé pour la mis à jour(modification)
	 * 			$operation: type de l'opération ajout (new) ou modification(edit)
	 *
	 */
	public function new_exclusion($paper_id, $data = array(), $operation = "new")
	{
		$ref_table_child = 'exclusion';
		$child_field = 'exclusion_paper_id';
		$ref_table_parent = 'papers';
		/*
		 * Récupération de la configuration(structure) de la table exclusion
		 */
		$table_config_child = get_table_config($ref_table_child);
		$table_config_child['config_id'] = $ref_table_child;
		$is_guest = check_guest();
		if (!$is_guest) {
			/*
			 * Récupération de la configuration(structure) de la table des papiers
			 */
			$table_config_parent = get_table_config($ref_table_parent);
			$table_config_child['fields'][$child_field]['on_add'] = "hidden";
			$table_config_child['fields'][$child_field]['on_edit'] = "hidden";
			$table_config_child['fields'][$child_field]['input_type'] = "text";
			foreach ($table_config_child['fields'] as $k => $v) {
				if (!empty($v['input_type']) and $v['input_type'] == 'select') {
					if ($v['input_select_source'] == 'table') {
						$table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values']);
					}
				}
			}
			/*
			 * Prépartions des valeurs qui vont apparaitres dans le formulaire
			 */
			$data['content_item'][$child_field] = $paper_id;
			$data['table_config'] = $table_config_child;
			$data['operation_type'] = $operation;
			$data['operation_source'] = "exclusion";
			$data['child_field'] = $child_field;
			$data['table_config_parent'] = $ref_table_parent;
			$data['parent_id'] = $paper_id;
			/*
			 * Titre de la page
			 */
			$parrent_names = $this->manager_lib->get_reference_select_values($table_config_child['fields'][$child_field]['input_select_values']);
			if ($operation == 'edit') {
				$data['page_title'] = 'Edit Exclusion of the ' . $table_config_parent['reference_title_min'] . " : " . $parrent_names[$paper_id];
			} else {
				$data['page_title'] = 'Exclusion of the ' . $table_config_parent['reference_title_min'] . " : " . $parrent_names[$paper_id];
			}
			/*
			 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
			 */
			$data['top_buttons'] = get_top_button('back', 'Back', 'manage');
			/*
			 * La vue qui va s'afficher
			 */
			$data['page'] = 'general/frm_reference';
			/*
			 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
			 */
			$this->load->view('shared/body', $data);
		} else {
			set_top_msg('No access to this operation!', 'error');
			redirect('data_extraction/display_paper/' . $paper_id);
		}
	}

	/*
	 * Fonction pour enregistrer les valeurs dans les champs multi-select
	 */
	private function save_multi_select($multi_select_values, $parent_id)
	{
		foreach ($multi_select_values as $k => $v) {
			$new_values = $v['values'];
			$Tvalues_source = explode(';', $v['config']['input_select_values']);
			$child_tab_config = $Tvalues_source[0];
			$input_select_key_field = $v['config']['input_select_key_field'];
			$input_child_field = $Tvalues_source[1];
			$source_table_config = get_table_config($child_tab_config);
			$extra_condition = " AND $input_select_key_field ='" . $parent_id . "'";
			$res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $input_child_field, $extra_condition);
			//print_test($res_values);
			$old_values = array();
			$tobe_removed = array();
			foreach ($res_values as $key_r => $value_r) {
				array_push($old_values, $value_r['refDesc']);
				if (!in_array($value_r['refDesc'], $new_values)) {
					array_push($tobe_removed, $value_r['refId']);
				}
			}
			$tobe_added = array_diff($new_values, $old_values);
			foreach ($tobe_removed as $k_rem => $v_rem) {
				$res = $this->DBConnection_mdl->remove_element($v_rem, $child_tab_config);
			}
			//adding new values
			foreach ($tobe_added as $k_add => $v_add) {
				$array_add = array();
				$array_add['operation_type'] = 'new';
				$array_add['table_config'] = $child_tab_config;
				$array_add[$input_select_key_field] = $parent_id;
				$array_add[$input_child_field] = $v_add;
				$saved_res = $this->DBConnection_mdl->save_reference($array_add, 'get_id');
				//print_test($array_add);
			}
		}
	}

	/*
	 * Fonction pour la suppression d'un element
	 * Input: 	$ref_table : nom de la structure de la table ou se trouve l'élément à supprimer
	 * 			$row_id : id de l'élément à supprimer
	 * 			$redirect: Y/N rediriger vers la liste d'éléments
	 */
	public function delete_element($ref_table, $row_id, $redirect = true)
	{
		/*
		 * Appel de la foction dans le model pour appeler la requetter  de suppression de l'élément
		 */
		$res = $this->DBConnection_mdl->remove_element($row_id, $ref_table);
		/*
		 * Message de confirmation ou erreur
		 */
		if ($res) {
			set_top_msg(lng_min("Success"));
		} else {
			set_top_msg(lng_min(" Operation failed "), 'error');
		}
		/*
		 *
		 * Rédirection après l'opération si $redirect=true
		 */
		if ($redirect)
			redirect('manager/entity_list/' . $ref_table);
	}

	//retrieve the select values for a given field based on the provided configuration, including handling nested select fields and array-based select sources
	///----------------------------- to be updated
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

	//useful when dealing with multi-valued fields in an element, allowing you to retrieve and work with all the associated values
	private function zget_element_multi_values($config, $key_field, $element_id)
	{
		$Tvalues_source = explode(';', $config);
		$source_table_config = get_table_config($Tvalues_source[0]);
		$extra_condition = " AND $key_field ='" . $element_id . "'";
		$res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $source_table_config['table_id'], $extra_condition);
		$results = array();
		foreach ($res_values as $value) {
			array_push($results, $value['refDesc']);
		}
		//print_test($results);
		return $results;
	}

	/*
	 * Fonction pour afficher une ligne d'une table avec remplacement des clès externes par leurs correspondances
	 */
	private function zget_element_detail($ref_table, $ref_id, $editable = True, $modal_link = False)
	{
		//récuperation de la configuration de l'entité
		$table_config = get_table_config($ref_table);
		//	print_test($table_config);
		$dropoboxes = array();
		// récupération des valeurs pour les champs avec la clé enregistre dans la table (pour pouvoir afficher le label)
		foreach ($table_config['fields'] as $k => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select' and !(isset($v['on_view']) and $v['on_view'] == 'hidden')) {
				if ($v['input_select_source'] == 'array') {
					$dropoboxes[$k] = $v['input_select_values'];
				} elseif ($v['input_select_source'] == 'yes_no') {
					$dropoboxes[$k] = array(
						0 => 'No',
						1 => 'Yes'
					);
				} elseif ($v['input_select_source'] == 'table') {
					// recherches des auteurs par papier
					if ($ref_table == 'papers' and $k == 'authors') {
						$this->db3 = $this->load->database(project_db(), TRUE);
						//todo generaliser pour tout les multivalues (car les autres prennemt beaucoups de temps)
						$sql = "select P.paperauthor_id ,A.author_name from paperauthor P,author A where P.paperId=$ref_id AND P.authorId=A.author_id AND A.author_active=1 AND P.paperauthor_active=1 ";
						$res_author = $this->db3->query($sql)->result_array();
						$t_array = array('' => 'Select ...');
						//print_test($res_author);
						foreach ($res_author as $key_a => $value_a) {
							$t_array[$value_a['paperauthor_id']] = $value_a['author_name'];
						}
						//print_test($t_array);
						$dropoboxes[$k] = $t_array;
					} else {
						$dropoboxes[$k] = $this->manager_lib->get_reference_select_values($v['input_select_values']);
					}
				}
			}
		}
		//$detail_result = $this->manage_mdl->get_reference_details ( $table_config['table_name'],$table_config['table_id'],$ref_id );
		$detail_result = $this->DBConnection_mdl->get_row_details($ref_table, $ref_id);
		$content_item = $detail_result;
		$item_data = array();
		foreach ($dropoboxes as $k => $v) {
			$content_item[$k . '_idd'] = 0;
			if (isset($content_item[$k])) {
				if (isset($v[$content_item[$k]])) {
					$content_item[$k . '_idd'] = $content_item[$k];
					$content_item[$k] = $v[$content_item[$k]];
				}
			} else {
				$content_item[$k] = "";
			}
		}
		//print_test($content_item);
		foreach ($table_config['fields'] as $key => $value) {
			$array = array();
			//print_test($value);
			if (!(isset($value['on_view']) and $value['on_view'] == 'hidden')) {
				$array['title'] = $value['field_title'];
				$array['edit'] = 0;
				//for multi values
				if (isset($value['number_of_values']) and ($value['number_of_values'] == '*' or $value['number_of_values'] != '1') and !empty($value['input_select_key_field'])) {
					$Tvalues_source = explode(';', $value['input_select_values']);
					//echo "<h1>".$Tvalues_source[0]."<h1>";
					$source_table_config = get_table_config($Tvalues_source[0]);
					$input_select_key_field = $value['input_select_key_field'];
					$extra_condition = " AND $input_select_key_field ='" . $ref_id . "'";
					$res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $input_select_key_field, $extra_condition);
					// set add button
					$add_button = create_button_link('manage/add_element_child/' . $Tvalues_source[0] . '/' . $value['input_select_key_field'] . '/' . $ref_table . '/' . $ref_id, '<i class="fa fa-plus"></i> Add', "btn-success", 'Add ');
					if ($ref_table == 'classification') { //use modal for classification
						$modal_title = "Add : " . $value['field_title'];
						$add_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="element/add_element_child_modal/' . $Tvalues_source[0] . '/' . $value['input_select_key_field'] . '/' . $ref_table . '/' . $ref_id . '"  data-modal_title="' . $modal_title . '" ><i class="fa fa-plus"></i>Add</a>';
					}
					$k_row = 0;
					if (!(isset($value['multi-select']) and $value['multi-select'] == "Yes") and $editable) {
						$array['val2'][0] = "<span> " . $add_button . "</span>";
						$k_row = 1;
						$array['edit'] = 1;
					}
					// Get values if label  are from other tables 
					foreach ($res_values as $key_v => $value_v) {
						if (isset($dropoboxes[$key][$value_v['refId']]))
							$array['val2'][$k_row] = $dropoboxes[$key][$value_v['refId']];
						if (isset($value_v['refId']) and $value['input_select_source'] == 'table' and isset($value['input_select_source_type'])) {
							//print_test($value);
							$Tconfig = explode(';', $value['input_select_values']);
							//echo $content_item[$key.'_idd'];
							if ($value_v['refId'] != 0) {
								$edit_button = create_button_link('manage/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $value_v['refId'], '<i class="fa fa-pencil"></i> Edit', "btn-info", 'Edit ');
								$delete_button = create_button_link('manage/remove_drilldown/' . $value_v['refId'] . '/' . $Tconfig[0] . '/' . $ref_table . '/' . $ref_id . '/' . $key . '/no', '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');
								if ($ref_table == 'classification') { //use modal for classification
									$modal_title = "Edit : " . $value['field_title'];
									$edit_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manage/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $value_v['refId'] . '/modal"  data-modal_title="' . $modal_title . '" ><i class="fa fa-pencil"></i>Edit</a>';
									$delete_button = create_button_link('manage/remove_drilldown/' . $value_v['refId'] . '/' . $Tconfig[0] . '/' . $ref_table . '/' . $ref_id . '/' . $key . '/no/yes', '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');
								}
								if ((isset($value['multi-select']) and $value['multi-select'] == "Yes") or !$editable) {
									$edit_button = "";
									$delete_button = "";
								}
								if (isset($value['input_select_source_type']) and $value['input_select_source_type'] == 'drill_down')
									$array['val2'][$k_row] = "<span class='drilldown_link'>" . anchor('manage/view_ref/' . $Tconfig[0] . '/' . $value_v['refId'], $array['val2'][$k_row]) . "</span> <div class='navbar-right'>$edit_button $delete_button</div>";
								else
									$array['val2'][$k_row] .= " <div class='navbar-right'>$edit_button $delete_button</div>";
							}
						}
						$k_row++;
					}
				} else {
					$array['val'] = isset($content_item[$key]) ? " " . $content_item[$key] : ' ';
					$array['val2'][0] = isset($content_item[$key]) ? " " . $content_item[$key] : ' ';
					// for images
					if (isset($value['input_type']) and $value['input_type'] == 'image') {
						if (!empty($content_item[$key])) {
							//$img=$this->config->item('image_upload_path').$content_item[$key]."_thumb.jpg";
							//$array['val2'][0]= img($img);
							$delete_picture_button = get_top_button('all', 'Remove picture', 'manager/remove_picture/' . $ref_table . '/' . $table_config['table_name'] . '/' . $table_config['table_id'] . '/' . $key . '/' . $ref_id, '', 'fa-close', '', 'btn-danger', FALSE);
							//$array['val2'][0]= '<img src="data:image/png;base64,'.base64_encode( $content_item[$key]).'"/> '.$delete_picture_button;
							$array['val2'][0] = '<img src="' . display_picture_from_db($content_item[$key]) . '"/> ' . $delete_picture_button;
						}
					}
					///echo $content_item[$key.'_idd'];
					if (isset($content_item[$key . '_idd']) and $value['input_select_source'] == 'table' and isset($value['input_select_source_type']) and $value['input_select_source_type'] == 'drill_down') {
						//print_test($value);
						$Tconfig = explode(';', $value['input_select_values']);
						//echo $content_item[$key.'_idd'];
						if ($content_item[$key . '_idd'] != 0) {
							if ((isset($value['drill_down_type']) and $value['drill_down_type'] == 'not_linked') or !$editable) {
								$edit_button = "";
								$delete_button = "";
							} else {
								$edit_button = create_button_link('manage/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $content_item[$key . '_idd'], '<i class="fa fa-pencil"></i> Edit', "btn-info", 'Edit ');
								$delete_button = create_button_link('manage/remove_drilldown/' . $content_item[$key . '_idd'] . '/' . $Tconfig[0] . '/' . $ref_table . '/' . $ref_id . '/' . $key, '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');
							}
							$array['val'] = "<span class='drilldown_link'>" . anchor('manage/view_ref/' . $Tconfig[0] . '/' . $content_item[$key . '_idd'], $array['val']) . "</span> <div class='navbar-right'>$edit_button.$delete_button</div>";
							$array['val2'][0] = "<span class='drilldown_link'>" . anchor('manage/view_ref/' . $Tconfig[0] . '/' . $content_item[$key . '_idd'], $array['val2'][0]) . "</span> <div class='navbar-right'>$edit_button.$delete_button</div>";
						} else {
							if ((isset($value['drill_down_type']) and $value['drill_down_type'] == 'not_linked') or !$editable) {
								$add_button = "";
							} else {
								$array['edit'] = 1;
								$add_button = create_button_link('manage/add_ref_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id, '<i class="fa fa-plus"></i> Add', "btn-success", 'Add ');
								if ($ref_table == 'classification') { //use modal for classification
									$modal_title = "Add : " . $value['field_title'];
									$add_button = '<a  class="btn btn-xs btn-success" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manage/add_ref_drilldown_modal/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '"  data-modal_title="' . $modal_title . '" ><i class="fa fa-plus"></i>Add</a>';
								}
							}
							$array['val'] = "<span>: " . $add_button . "</span>";
							$array['val2'][0] = "<span>: " . $add_button . "</span>";
						}
					}
				}
				array_push($item_data, $array);
				//array_push($item_data2, $array);
			}
		}
		//print_test($item_data);
		return $item_data;
	}

	//ensure that the drilldown element is removed from the child table and updates the parent element accordingly
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
		if ($modal == 'yes') {
			//todo à corriger
			//redirect ( 'manager/edit_element/'.$parent_id  );
			redirect('manager/display_element/' . $table_config_parent . '/' . $parent_id);
		} else {
			redirect('manager/display_element/' . $table_config_parent . '/' . $parent_id);
		}
	}

	//used to remove a picture associated with an element in a table. 
	public function remove_picture($ref_table, $table_name, $table_id, $field, $element_id)
	{
		$table_name = mysql_real_escape_string($table_name);
		$table_id = mysql_real_escape_string($table_id);
		$field = mysql_real_escape_string($field);
		$element_id = mysql_real_escape_string($element_id);
		$sql = "UPDATE $table_name SET $field = NULL WHERE $table_id ='" . $element_id . "'";
		$res = $this->db->query($sql);
		if ($res) {
			set_top_msg(lng_min("Success - picture removed"));
		} else {
			set_top_msg(lng_min(" Operation failed "), 'error');
		}
		redirect('manager/display_element/' . $ref_table . '/' . $element_id);
	}

	//assignment screen creation page
	public function new_assignment_screen($paper_id, $redirect = "paper_screen")
	{
		if (!empty($paper_id) and $redirect == 'paper_screen') {
			$this->session->set_userdata('after_save_redirect', "screening/display_paper_screen/$paper_id");
			$data['content_item']['paper_id'] = $paper_id;
		} else {
			$this->session->set_userdata('after_save_redirect', "screening/display_paper_screen/all_assign");
		}
		$data['content_item']['assignment_mode'] = 'manualy_single';
		//redirect("manager/add_element/assignment_screen");
		$this->add_element('assignment_screen', $data);
	}

	//cancel different types of operations and update the corresponding records and operation state in the database
	public function cancel_operation($operations_id, $active_value = 0)
	{
		//get operation detail
		$sql = "SELECT * FROM operations WHERE 	operation_id=$operations_id";
		$res = $this->db_current->query($sql)->row_array();
		print_test($res);
		if (!empty($res)) {
			$sql = "";
			if ($res['operation_type'] == 'assign_papers_validation' or $res['operation_type'] == 'assign_papers') { //asssign papers
				$sql = "UPDATE screening_paper set screening_active = $active_value 
				where 	operation_code LIKE '" . $res['operation_code'] . "'";
			} elseif ($res['operation_type'] == 'assign_qa') { //assignment for QA
				$sql = "UPDATE qa_assignment set qa_assignment_active = $active_value  
				where 	operation_code LIKE '" . $res['operation_code'] . "'";
			} elseif ($res['operation_type'] == 'assign_qa_validation') { //assignment for QA validation
				$sql = "UPDATE qa_validation_assignment set qa_validation_active = $active_value  
				where 	operation_code LIKE '" . $res['operation_code'] . "'";
			} elseif ($res['operation_type'] == 'assign_class' or $res['operation_type'] == 'assign_class_validation') { //assignment for classification
				$sql = "UPDATE assigned set assigned_active = $active_value  
				where 	operation_code LIKE '" . $res['operation_code'] . "'";
			} elseif ($res['operation_type'] == 'import_paper') { //import papers
				$sql = "UPDATE paper set paper_active = $active_value  
				where 	operation_code LIKE '" . $res['operation_code'] . "'";
			}
			if (!empty($sql)) {
				$res = $this->manage_mdl->run_query($sql);
				$operation_state = !empty($active_value) ? 'Active' : 'Cancelled';
				$sql_update_operation = $sql = "UPDATE operations set operation_state = '" . $operation_state . "'  where 	operation_id = $operations_id ";
				$res = $this->manage_mdl->run_query($sql);
				set_top_msg(lng_min("Operation done"));
			} else {
				set_top_msg(lng_min("Operation not supported"), 'error');
			}
		} else {
			set_top_msg(lng_min("Error : operation not found"), 'error');
		}
		redirect('element/entity_list/list_operations');
	}

	//undo the cancellation of an operation
	public function undo_cancel_operation($operations_id)
	{
		$this->cancel_operation($operations_id, 1);
	}

	// display a confirmation message for clearing all logs and provide options to proceed or cancel the operation
	function clear_logs_validation()
	{
		$data['page'] = 'install/frm_install_result';
		$data['left_menu_admin'] = True;
		$data['array_warning'] = array('You want to clear All logs : The opération cannot be undone !');
		$data['array_success'] = array();
		$data['next_operation_button'] = "";
		$data['page_title'] = lng('Clear logs ');
		$data['next_operation_button'] = " &nbsp &nbsp &nbsp" . get_top_button('all', 'Continue uninstall', 'manager/clear_logs', 'Continue to clear', '', '', ' btn-success ', FALSE);
		$data['next_operation_button'] .= get_top_button('all', 'Cancel', 'element/entity_list/list_logs', 'Cancel', '', '', ' btn-danger ', FALSE);
		$this->load->view('shared/body', $data);
	}

	//deactivate all active logs in the system and provide feedback to the user that the logs have been cleaned
	public function clear_logs()
	{
		$sql = "UPDATE log SET log_active=0 where log_active=1 ";
		$res = $this->db->query($sql);
		set_top_msg('Logs cleaned');
		redirect('element/entity_list/list_logs');
	}
}