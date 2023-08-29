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
 */

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Data_extraction extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	//turn the classification feature on anf off
	public function activate_classification($value = 1)
	{

		if ($value != 1)
			$value = 0;

		set_appconfig_element('classification_on', $value);

		redirect('screening/screening_select');
	}

	//remove a classification feature for a project
	public function remove_classification($id, $paper_id)
	{
		$res = $this->DBConnection_mdl->remove_element($id, 'classification');
		set_top_msg(lng_min('Classification removed'));
		redirect('data_extraction/display_paper/' . $paper_id);
	}

	//remove a classification record from the database
	public function remove_classification2($id, $paper_id)
	{
		//$res=$this->manage_mdl->remove_element($id,'classification','class_id','class_active');
		$res = $this->DBConnection_mdl->remove_element($id, 'classification');
		redirect('paper/view_paper/' . $paper_id);
	}

	/*
	 * spécialisation de la fonction add_classification lorsque le formulaire s'affiche en pop up
	 */
	public function new_classification_modal($parent_id, $data = "", $operation = "new")
	{
		$this->new_classification($parent_id, $data, $operation, 'modal');
	}

	/*
	 * Fonction  pour afficher la page avec un formulaire d'ajout d'une classification
	 *
	 * Input: $parent_id: l'id du papier à qui on va ajouter une classification
	 * 			$data : informations sur l'élément si la fonction est utilisé pour la mis à jour(modification)
	 * 			$operation: type de l'opération ajout (new) ou modification(edit)
	 * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */
	public function new_classification($parent_id, $data = "", $operation = "new", $display_type = "normal")
	{
		$ref_table_child = 'classification';
		$child_field = 'class_paper_id';
		$ref_table_parent = 'papers';
		/*
		 * Récupération de la configuration(structure) de la table classification
		 */
		$table_config_child = get_table_config($ref_table_child);
		$table_config_child['config_id'] = $ref_table_child;
		//	print_test($table_config_child);
		//si les valeurs provienne d'une redirection apres tenetative d'enregistrement
		if (!empty($data) and $data == 'sess_redirect') {
			$data = $this->session->userdata('redirect_values');
			if (isset($data['content_item']['class_id'])) {
				$classification_id = $data['content_item']['class_id'];
				$element_detail = $this->manager_lib->get_element_detail($ref_table_child, $classification_id, true, true);
				$drill_down_values = array();
				foreach ($table_config_child['fields'] as $key => $v) {
					if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['input_select_source'] == 'table') {
						if (!empty($v['on_edit']) and $v['on_edit'] == 'drill_down') {
							//Recuperation des valeurs pour les drilldown
							foreach ($element_detail as $key_el => $value_el) {
								if ($value_el['field_id'] == $key) {
									$drill_down_values[$key] = $value_el['val2'];
								}
							}
						}
					}
				}
				//print_test($drill_down_values);
				$data['drill_down_values'] = $drill_down_values;
			}
		}
		/*
		 * chargement de la manière d'affichage du formulaire
		 */
		$this->session->set_userdata('submit_mode', $display_type);
		//print_test($table_config_child);
		/*
		 * Récupération de la configuration(structure) de la table des papiers
		 */
		$table_config_parent = get_table_config($ref_table_parent);
		$table_config_child['fields'][$child_field]['on_add'] = "hidden";
		$table_config_child['fields'][$child_field]['on_edit'] = "hidden";
		$table_config_child['fields'][$child_field]['input_type'] = "text";
		/*
		 * récupération des valeurs qui vont apparaitre dans les champs select
		 */
		foreach ($table_config_child['fields'] as $k => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select') {
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
		$data['operation_source'] = "paper";
		$data['child_field'] = $child_field;
		$data['table_config_parent'] = $ref_table_parent;
		$data['parent_id'] = $parent_id;
		/*
		 * Titre de la page
		 */
		$parrent_names = $this->manager_lib->get_reference_select_values($table_config_child['fields'][$child_field]['input_select_values']);
		if ($operation == 'edit') {
			$data['page_title'] = lng("Edit  classification for the paper : ") . $parrent_names[$parent_id];
			$this->session->set_userdata('after_save_redirect', 'data_extraction/display_paper/' . $parent_id);
		} else {
			//	$data ['page_title'] = lng('Add a '.$table_config_child['reference_title_min']." to the ".$table_config_parent['reference_title_min'])." : ".$parrent_names[$parent_id];
			$data['page_title'] = lng("Add a classification  to the paper : ") . $parrent_names[$parent_id];
			$this->session->set_userdata('after_save_redirect', 'data_extraction/list_classification');
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
		if ($display_type == 'modal') {
			$this->load->view('frm_reference_modal', $data);
		} else {
			$this->load->view('shared/body', $data);
		}
	}

	/*
	 * Affichage du formulaire pour modifier une classification
	 * $ref_id: id de la classification
	 * $display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */
	public function edit_classification($ref_id, $display_type = "normal")
	{
		$this->session->set_userdata('submit_mode', $display_type);
		/*
		 * Récupération de la configuration(structure) de la table classification
		 */
		$ref_table = "classification";
		$table_config = get_table_config($ref_table);
		//print_test($table_config);
		/*
		 * Appel de la fonction du model pour récupérer la ligne à modifier
		 */
		$data['content_item'] = $this->DBConnection_mdl->get_row_details($ref_table, $ref_id);
		$element_detail = $this->manager_lib->get_element_detail($ref_table, $ref_id, true, true);
		//	print_test($element_detail);
		$drill_down_values = array();
		foreach ($table_config['fields'] as $key => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['input_select_source'] == 'table') {
				/*
				 * Récuperation des valeurs pour les champs multi-select
				 */
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
				} elseif (!empty($v['on_edit']) and $v['on_edit'] == 'drill_down') {
					//Recuperation des valeurs pour les drilldown
					foreach ($element_detail as $key_el => $value_el) {
						if ($value_el['field_id'] == $key) {
							$drill_down_values[$key] = $value_el['val2'];
						}
					}
				}
			}
		}
		//print_test($drill_down_values);
		$data['drill_down_values'] = $drill_down_values;
		/*
		 * Appel de la fonction d'affichage du formulaire
		 */
		$this->new_classification($data['content_item']['class_paper_id'], $data, 'edit', $display_type);
	}

	/*
	 * Affichage du formulaire pour modifier une classification
	 * $ref_id: id de la classification
	 * $display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */
	public function edit_classification2($ref_id, $display_type = "normal")
	{
		old_version();
		$this->session->set_userdata('submit_mode', $display_type);
		/*
		 * Récupération de la configuration(structure) de la table classification
		 */
		$ref_table = "classification";
		$table_config = $this->ref_table_config($ref_table);
		//print_test($table_config);
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
		$extra_fields = array();
		$classification_data = $this->get_reference_detail('classification', $ref_id);
		foreach ($classification_data as $key => $value) {
			if ($value['edit'] == 1)
				array_push($extra_fields, $value);
		}
		$data['extra_fields'] = $extra_fields;
		/*
		 * Appel de la fonction d'affichage du formulaire
		 */
		$this->add_classification($data['content_item']['class_paper_id'], $data, 'edit', $display_type);
	}

	/*
	 * Fonction utilisé pour faire une recherche dans la liste des classifications
	 *
	 * fields: le champs où on effectue la recherche
	 * $value: la valeur recherché
	 */
	public function search_classification($field, $value)
	{
		$condition = array(
			'classification_search_field' => $field,
			'classification_search_value' => $value
		);
		/*
		 * Chargement des critères de recherche dans une session
		 */
		$this->session->set_userdata($condition);
		/*
		 * Appel de la fonction d'affichagage de liste des classification en tenant comptes des critères de recherches mis en session
		 */
		$this->list_classification('search_cat');
	}

	/*
	 * Fonction  pour afficher la liste des classifications faites
	 *
	 * Input: $list_type: indique si une recherche à été faites ou pas.  si $list_type= 'normal' on affiche toute la liste
	 * 			$val : valeur de recherche si une recherche a été faite
	 * 			$page: la page à affiché : ulilisé par les lien de navigation
	 */
	public function list_classification($list_type = 'normal', $val = "_", $page = 0, $dynamic_table = 1)
	{
		// nouvelle fonction pour afficher la liste des classification elle utilise les data tables
		////redirect("data_extraction/list_classification_dt/$list_type/$val/$page");
		$ref_table = 'classification';
		$val = urldecode(urldecode($val));
		$filter = array();
		if (isset($_POST['search_all'])) {
			$filter = $this->input->post();
			// print_test($filter);exit;
			unset($filter['search_all']);
			$val = "_";
			if (isset($filter['valeur']) and !empty($filter['valeur'])) {
				$val = $filter['valeur'];
				$val = urlencode(urlencode($val));
			}
			$url = "data_extraction/list_classification/" . $ref_table . "/" . $val . "/0/";
			redirect($url);
		}
		$ref_table_config = get_table_config($ref_table);
		$table_id = $ref_table_config['table_id'];
		$condition = array();
		$extra_condition = "";
		$sup_title = "";
		if ($list_type == 'search_cat') {
			if ($this->session->userdata('classification_search_field') and $this->session->userdata('classification_search_value')) {
				$field = $this->session->userdata('classification_search_field');
				$value = $this->session->userdata('classification_search_value');
				$extra_condition = " AND ( " . $field . "='" . $value . "') ";
				$value_desc = $value;
				if (!empty($ref_table_config['fields'][$field]['input_type']) and $ref_table_config['fields'][$field]['input_type'] == 'select') {
					$values = $this->manager_lib->get_reference_select_values($ref_table_config['fields'][$field]['input_select_values']);
					$value_desc = $values[$value];
					$sup_title = " for \"" . $ref_table_config['fields'][$field]['field_title'] . "\" :  $value_desc";
				}
			}
		}
		$rec_per_page = ($dynamic_table) ? -1 : 0;
		if (!empty($extra_condition)) {
			$data = $this->manage_mdl->get_list($ref_table_config, $val, $page, $rec_per_page, $extra_condition);
		} else {
			$data = $this->DBConnection_mdl->get_list($ref_table_config, $val, $page, $rec_per_page, $extra_condition);
		}
		//for  dropboxes
		//print_test($ref_table_config);
		$dropoboxes = array();
		foreach ($ref_table_config['fields'] as $k => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['on_list'] == 'show') {
				if ($v['input_select_source'] == 'array') {
					$dropoboxes[$k] = $v['input_select_values'];
				} elseif ($v['input_select_source'] == 'table') {
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
		$view_link_url = "";
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
			$view_link_url = "";
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
				if ($value_l['type'] == 'view')
					$view_link_url = $value_l['url'] . $value[$table_id];
			}
			$action_button = create_button_link_dropdown($arr_buttons, lng_min('Action'));
			$element_array['links'] = $action_button;
			if (isset($element_array['class_paper_id']) and !empty($view_link_url)) {
				$element_array['class_paper_id'] = anchor($view_link_url, "<u><b>" . $element_array['class_paper_id'] . "</b></u>", 'title="' . lng_min('Display element') . '")');
			}
			if (isset($element_array[$table_id])) {
				$element_array[$table_id] = $i + $page;
			}
			array_push($list_to_display, $element_array);
			$i++;
		}
		$data['list'] = $list_to_display;
		//print_test($data); exit;
		/*
		 * Ajout de l'entête de la liste
		 */
		if (!empty($data['list'])) {
			$array_header = $field_list_header;
			;
			if (trim($data['list'][$key]['links']) != "") {
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
		//This feature is not used for classification
		//if($add_link)
		//$data ['top_buttons'] .= get_top_button ( 'add', 'Add new', 'manager/add_element/'.$ref_table );
		$data['top_buttons'] .= get_top_button('close', 'Close', 'home');
		/*
		 * Titre de la page
		 */
		$data['page_title'] = $ref_table_config['reference_title'] . $sup_title;
		if (activate_update_stored_procedure())
			$data['top_buttons'] .= get_top_button('all', 'Update stored procedure', 'home/update_stored_procedure/' . $ref_table, 'Update stored procedure', 'fa-check', '', ' btn-dark ');
		$data['valeur'] = ($val == "_") ? "" : $val;
		if (!$dynamic_table and !empty($ref_table_config['search_by'])) {
			$data['search_view'] = 'general/search_view';
		}
		/*
		 * La vue qui va s'afficher
		 */
		if (!$dynamic_table) {
			$data['nav_pre_link'] = 'data_extraction/list_classification2/' . $list_type . '/' . $val . '/';
			$data['nav_page_position'] = 6;
			$data['page'] = 'general/list';
		} else {
			$data['page'] = 'general/list_dt';
		}
		/*
		 * Chargement de la vue avec les données préparés dans le controleur
		 */
		$this->load->view('shared/body', $data);
	}

	/*
	 * Fonction  pour afficher la liste des classifications faites
	 *
	 * Input: $list_type: indique si une recherche à été faites ou pas.  si $list_type= 'normal' on affiche toute la liste
	 * 			$val : valeur de recherche si une recherche a été faite 
	 * 			$page: la page à affiché : ulilisé par les lien de navigation
	 */
	public function list_classification2($list_type = 'normal', $val = "_", $page = 0)
	{
		// nouvelle fonction pour afficher la liste des classification elle utilise les data tables
		redirect("data_extraction/list_classification_dt/$list_type/$val/$page");
		$ref_table = 'classification';
		$val = urldecode(urldecode($val));
		$filter = array();
		if (isset($_POST['search_all'])) {
			$filter = $this->input->post();
			// print_test($filter);exit;
			unset($filter['search_all']);
			$val = "_";
			if (isset($filter['valeur']) and !empty($filter['valeur'])) {
				$val = $filter['valeur'];
				$val = urlencode(urlencode($val));
			}
			$url = "manage/liste_ref/" . $ref_table . "/" . $val . "/0/";
			redirect($url);
		}
		$ref_table_config = $this->ref_table_config($ref_table);
		//	print_test($ref_table_config);
		$table_id = $ref_table_config['table_id'];
		$condition = array();
		$extra_condition = "";
		$sup_title = "";
		if ($list_type == 'search_cat') {
			if ($this->session->userdata('classification_search_field') and $this->session->userdata('classification_search_value')) {
				$field = $this->session->userdata('classification_search_field');
				$value = $this->session->userdata('classification_search_value');
				$extra_condition = " AND ( " . $field . "='" . $value . "') ";
				$value_desc = $value;
				if (!empty($ref_table_config['fields'][$field]['input_type']) and $ref_table_config['fields'][$field]['input_type'] == 'select') {
					$values = $this->get_reference_select_values($ref_table_config['fields'][$field]['input_select_values']);
					//	print_test($values);
					$value_desc = $values[$value];
					$sup_title = " for \"" . $ref_table_config['fields'][$field]['field_title'] . "\" :  $value_desc";
				}
			}
		}
		if (!empty($extra_condition)) {
			$data = $this->manage_mdl->get_list($ref_table_config, $val, $page, 0, $extra_condition);
		} else {
			$data = $this->DBConnection_mdl->get_list($ref_table_config, $val, $page, 0, $extra_condition);
		}
		//for select dropboxes
		//print_test($ref_table_config);
		$dropoboxes = array();
		foreach ($ref_table_config['fields'] as $k => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['on_list'] == 'show') {
				if ($v['input_select_source'] == 'array') {
					$dropoboxes[$k] = $v['input_select_values'];
				} elseif ($v['input_select_source'] == 'table') {
					$dropoboxes[$k] = $this->get_reference_select_values($v['input_select_values']);
				}
			}
			;
		}
		//print_test($dropoboxes);
		//See links to be added;
		$add_child_link = False;
		$edit_link = False;
		$view_link = False;
		$delete_link = False;
		//view link
		if (!empty($ref_table_config['links']['view']) and !empty($ref_table_config['links']['view']['on_list']) and ($ref_table_config['links']['view']['on_list'] == True)) {
			if (!empty($ref_table_config['links']['view']['url'])) {
				$view_link_url = $ref_table_config['links']['view']['url'];
			} else {
				$view_link_url = 'manage/view_ref/' . $ref_table;
			}
			$view_link_label = $ref_table_config['links']['view']['label'];
			$view_link_title = $ref_table_config['links']['view']['title'];
			$view_link = True;
		}
		//edit link
		if (!empty($ref_table_config['links']['edit']) and !empty($ref_table_config['links']['edit']['on_list']) and ($ref_table_config['links']['edit']['on_list'] == True)) {
			//$edit_link_url=$ref_table_config['links']['edit']['url'];
			$edit_link_label = $ref_table_config['links']['edit']['label'];
			$edit_link_title = $ref_table_config['links']['edit']['title'];
			$edit_link = True;
		}
		//addchild link
		if (!empty($ref_table_config['links']['add_child']['url']) and !empty($ref_table_config['links']['add_child']['on_list']) and ($ref_table_config['links']['add_child']['on_list'] == True)) {
			$child_link_url = 'manage/add_ref_child/' . $ref_table_config['links']['add_child']['url'] . "/" . $ref_table;
			$child_link_label = $ref_table_config['links']['add_child']['label'];
			$child_link_title = $ref_table_config['links']['add_child']['title'];
			$add_child_link = True;
		}
		//delete link
		if (!empty($ref_table_config['links']['delete']) and !empty($ref_table_config['links']['delete']['on_list']) and ($ref_table_config['links']['delete']['on_list'] == True)) {
			$delete_link_label = $ref_table_config['links']['delete']['label'];
			$delete_link_title = $ref_table_config['links']['delete']['title'];
			$delete_link = True;
		}
		$i = 1;
		//print_test($data);
		foreach ($data['list'] as $key => $value) {
			$add_child_button = "";
			$edit_button = "";
			$view_button = "";
			$action_button = "";
			$arr_buttons = array();
			if ($view_link) {
				array_push(
					$arr_buttons,
					array(
						'url' => $view_link_url . '/' . $value[$table_id],
						'label' => '<i class="fa fa-folder"></i> ' . $view_link_label,
						'title' => $view_link_title
					)
				);
				///$view_button = create_button_link('manage/view_ref/' . $ref_table.'/'.$value [$table_id],'<i class="fa fa-folder"></i>'.$view_link_label,"btn-primary",$view_link_title);
			}
			if ($edit_link) {
				array_push(
					$arr_buttons,
					array(
						'url' => 'manage/edit_ref/' . $ref_table . '/' . $value[$table_id],
						'label' => '<i class="fa fa-pencil"></i> ' . $edit_link_label,
						'title' => $edit_link_title
					)
				);
				////$edit_button = create_button_link('manage/edit_ref/' . $ref_table.'/'.$value [$table_id],'<i class="fa fa-pencil"></i>'.$edit_link_label,"btn-info",$edit_link_title);
			}
			if ($add_child_link) {
				array_push(
					$arr_buttons,
					array(
						'url' => $child_link_url . '/' . $value[$table_id],
						'label' => '<i class="fa fa-plus"></i> ' . $child_link_label,
						'title' => $child_link_title
					)
				);
				///$add_child_button = create_button_link($child_link_url.'/'.$value [$table_id],'<i class="fa fa-plus"></i>'.$child_link_label,"btn-dark",$child_link_title);
			}
			if ($delete_link) {
				array_push(
					$arr_buttons,
					array(
						'url' => 'manage/delete_ref/' . $ref_table . '/' . $value[$table_id],
						'label' => '<i class="fa fa-trash"></i> ' . $delete_link_label,
						'title' => $delete_link_title
					)
				);
				////$edit_button = create_button_link('manage/edit_ref/' . $ref_table.'/'.$value [$table_id],'<i class="fa fa-pencil"></i>'.$edit_link_label,"btn-info",$edit_link_title);
			}
			$action_button = create_button_link_dropdown($arr_buttons);
			//$data['list'][$key]['edit']=$view_button.$edit_button.$add_child_button.$action_button;
			$data['list'][$key]['edit'] = $action_button;
			$data['list'][$key][$table_id] = $i + $page;
			//print_test($dropoboxes);
			foreach ($dropoboxes as $k => $v) {
				//	print_test($v);
				if ($data['list'][$key][$k]) {
					if (isset($v[$data['list'][$key][$k]])) {
						if ($k == 'class_paper_id') {
							$data['list'][$key][$k] = anchor('paper/view_paper/' . $data['list'][$key][$k], "<u><b>" . $v[$data['list'][$key][$k]] . "</b></u>");
						} else {
							////ppp
							$data['list'][$key][$k] = $v[$data['list'][$key][$k]];
						}
					}
				} else {
					$data['list'][$key][$k] = "";
				}
			}
			$i++;
		}
		//print_test($data);
		if (!empty($data['list'])) {
			$array_header = $ref_table_config['header_list_fields'];
			if (trim($data['list'][$key]['edit']) != "") {
				array_push($array_header, '');
			}
			array_unshift($data['list'], $array_header);
		}
		$data['top_buttons'] = "";
		$data['top_buttons'] .= get_top_button('add', 'Add new', 'manage/add_ref/' . $ref_table);
		$data['top_buttons'] .= get_top_button('close', 'Close', 'home');
		//print_test($data['list']);
		$data['page_title'] = $ref_table_config['reference_title'] . $sup_title;
		$data['nav_pre_link'] = 'data_extraction/list_classification2/' . $list_type . '/' . $val . '/';
		$data['nav_page_position'] = 5;
		$data['valeur'] = ($val == "_") ? "" : $val;
		if (!empty($ref_table_config['search_by'])) {
			$data['search_view'] = 'search_papers';
		}
		$data['page'] = 'liste';
		$this->load->view('shared/body', $data);
	}

	/*
	 * Fonction  pour afficher la liste des classifications utilisant un Java script datatable
	 *
	 * Input: $list_type: indique si une recherche à été faites ou pas.  si $list_type= 'normal' on affiche toute la liste
	 * 			$val : valeur de recherche si une recherche a été faite
	 * 			$page: la page à affiché : ulilisé par les lien de navigation
	 */
	public function list_classification_dt($list_type = 'normal', $val = "_", $page = 0)
	{
		$ref_table = 'classification';
		/*
		 * Vérification si il y a une condition de recherche
		 */
		$val = urldecode(urldecode($val));
		$filter = array();
		if (isset($_POST['search_all'])) {
			$filter = $this->input->post();
			// print_test($filter);exit;
			unset($filter['search_all']);
			$val = "_";
			if (isset($filter['valeur']) and !empty($filter['valeur'])) {
				$val = $filter['valeur'];
				$val = urlencode(urlencode($val));
			}
			/*
			 * mis à jours de l'url en ajoutant la valeur recherché dans le lien puis rechargement de l'url
			 */
			$url = "manage/liste_ref/" . $ref_table . "/" . $val . "/0/";
			redirect($url);
		}
		/*
		 * Récupération de la configuration(structure) de la table à afficher
		 */
		$ref_table_config = $this->ref_table_config($ref_table);
		$table_id = $ref_table_config['table_id'];
		/*
		 * Vérification des critères de  recherche supplementaire 
		 */
		$condition = array();
		$extra_condition = "";
		$sup_title = "";
		if ($list_type == 'search_cat') {
			if ($this->session->userdata('classification_search_field') and $this->session->userdata('classification_search_value')) {
				$field = $this->session->userdata('classification_search_field');
				$value = $this->session->userdata('classification_search_value');
				$extra_condition = " AND ( " . $field . "='" . $value . "') ";
				$value_desc = $value;
				if (!empty($ref_table_config['fields'][$field]['input_type']) and $ref_table_config['fields'][$field]['input_type'] == 'select') {
					if ($ref_table_config['fields'][$field]['input_select_source'] == 'table') {
						$values = $this->get_reference_select_values($ref_table_config['fields'][$field]['input_select_values']);
					} elseif ($ref_table_config['fields'][$field]['input_select_source'] == 'yes_no') {
						$values = array("No", 'Yes');
					}
					$value_desc = $value;
					if (!empty($values[$value]))
						$value_desc = $values[$value];
					$sup_title = " for \"" . $ref_table_config['fields'][$field]['field_title'] . "\" :  $value_desc";
				}
			}
		}
		/*
		 * Appel du model pour récuperer la liste à afficher dans la Base de données
		 */
		if (!empty($extra_condition)) {
			$data = $this->manage_mdl->get_list($ref_table_config, $val, $page, -1, $extra_condition);
		} else {
			$data = $this->DBConnection_mdl->get_list($ref_table_config, $val, $page, -1, $extra_condition);
		}
		//print_test($data);
		/*
		 * récupération des correspondances des clès externes pour l'affichage  suivant la structure de la table
		 */
		$dropoboxes = array();
		foreach ($ref_table_config['fields'] as $k => $v) {
			if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['on_list'] == 'show') {
				if ($v['input_select_source'] == 'array') {
					$dropoboxes[$k] = $v['input_select_values'];
				} elseif ($v['input_select_source'] == 'table') {
					$dropoboxes[$k] = $this->get_reference_select_values($v['input_select_values']);
				} elseif ($v['input_select_source'] == 'yes_no') {
					$dropoboxes[$k] = array(
						'0' => "No",
						'1' => "Yes"
					);
				}
			}
			;
		}
		/*
		 * Vérification des liens (links) a afficher sur la liste
		 */
		$add_child_link = False;
		$edit_link = False;
		$view_link = False;
		$delete_link = False;
		//view link
		if (!empty($ref_table_config['links']['view']) and !empty($ref_table_config['links']['view']['on_list']) and ($ref_table_config['links']['view']['on_list'] == True)) {
			if (!empty($ref_table_config['links']['view']['url'])) {
				$view_link_url = $ref_table_config['links']['view']['url'];
			} else {
				$view_link_url = 'manage/view_ref/' . $ref_table;
			}
			$view_link_label = $ref_table_config['links']['view']['label'];
			$view_link_title = $ref_table_config['links']['view']['title'];
			$view_link = True;
		}
		//edit link
		if (!empty($ref_table_config['links']['edit']) and !empty($ref_table_config['links']['edit']['on_list']) and ($ref_table_config['links']['edit']['on_list'] == True)) {
			//$edit_link_url=$ref_table_config['links']['edit']['url'];
			$edit_link_label = $ref_table_config['links']['edit']['label'];
			$edit_link_title = $ref_table_config['links']['edit']['title'];
			$edit_link = True;
		}
		//addchild link
		if (!empty($ref_table_config['links']['add_child']['url']) and !empty($ref_table_config['links']['add_child']['on_list']) and ($ref_table_config['links']['add_child']['on_list'] == True)) {
			$child_link_url = 'manage/add_ref_child/' . $ref_table_config['links']['add_child']['url'] . "/" . $ref_table;
			$child_link_label = $ref_table_config['links']['add_child']['label'];
			$child_link_title = $ref_table_config['links']['add_child']['title'];
			$add_child_link = True;
		}
		//delete link
		if (!empty($ref_table_config['links']['delete']) and !empty($ref_table_config['links']['delete']['on_list']) and ($ref_table_config['links']['delete']['on_list'] == True)) {
			$delete_link_label = $ref_table_config['links']['delete']['label'];
			$delete_link_title = $ref_table_config['links']['delete']['title'];
			$delete_link = True;
		}
		$i = 1;
		/*
		 * Préparation de la liste à afficher sur base du contenu et  stucture de la table
		 */
		foreach ($data['list'] as $key => $value) {
			/*
			 * Ajout des liens(links) sur la liste
			 */
			$add_child_button = "";
			$edit_button = "";
			$view_button = "";
			$action_button = "";
			$arr_buttons = array();
			if ($view_link) {
				array_push(
					$arr_buttons,
					array(
						'url' => $view_link_url . '/' . $value[$table_id],
						'label' => '<i class="fa fa-folder"></i> ' . $view_link_label,
						'title' => $view_link_title
					)
				);
			}
			if ($edit_link) {
				array_push(
					$arr_buttons,
					array(
						'url' => 'manage/edit_ref/' . $ref_table . '/' . $value[$table_id],
						'label' => '<i class="fa fa-pencil"></i> ' . $edit_link_label,
						'title' => $edit_link_title
					)
				);
			}
			if ($add_child_link) {
				array_push(
					$arr_buttons,
					array(
						'url' => $child_link_url . '/' . $value[$table_id],
						'label' => '<i class="fa fa-plus"></i> ' . $child_link_label,
						'title' => $child_link_title
					)
				);
			}
			if ($delete_link) {
				array_push(
					$arr_buttons,
					array(
						'url' => 'manage/delete_ref/' . $ref_table . '/' . $value[$table_id],
						'label' => '<i class="fa fa-trash"></i> ' . $delete_link_label,
						'title' => $delete_link_title
					)
				);
			}
			$action_button = create_button_link_dropdown($arr_buttons);
			//$extra_values=$this->get_extra_fields($data ['list'] [$key] [$table_id]);
			//$data ['list'] [$key]['class_scope']=$extra_values['class_scope'];
			//$data ['list'] [$key]['class_intent']=$extra_values['class_intent'];
			//$data ['list'] [$key]['class_intent_relation']=$extra_values['class_intent_relation'];
			$data['list'][$key]['edit'] = $action_button;
			$data['list'][$key][$table_id] = $i + $page;
			unset($data['list'][$key][$table_id]);
			/*
			 * Remplacement des clés externes par leurs correspondances
			 */
			foreach ($dropoboxes as $k => $v) {
				if ($data['list'][$key][$k]) {
					if (isset($v[$data['list'][$key][$k]])) {
						if ($k == 'class_paper_id') {
							//Pour le nom du papier affichage des 15 premiers caractères
							$the_title = $v[$data['list'][$key][$k]];
							$display = substr($the_title, 0, 15) . "...";
							$data['list'][$key][$k] = anchor('paper/view_paper/' . $data['list'][$key][$k], "<u><b>" . $display . "</b></u>", "title='" . $the_title . "'");
						} else {
							$data['list'][$key][$k] = $v[$data['list'][$key][$k]];
						}
					}
				} else {
					if ($ref_table_config['fields'][$k]['field_value'] == "0_1") {
						$data['list'][$key][$k] = "No";
					} else {
						$data['list'][$key][$k] = "";
					}
				}
			}
			$i++;
		}
		/*
		 * Ajout de l'entête de la liste
		 */
		if (!empty($data['list'])) {
			$array_header = $ref_table_config['header_list_fields'];
			unset($array_header[0]);
			//array_push($array_header,lng('Scope'));
			//array_push($array_header,lng('Intent'));
			//array_push($array_header,lng('Intent relation'));
			if (trim($data['list'][$key]['edit']) != "") {
				array_push($array_header, '');
			}
			$data['list_header'] = $array_header;
			//array_unshift($data['list'],$array_header);
		}
		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data['top_buttons'] = "";
		$data['top_buttons'] .= get_top_button('close', 'Close', 'home');
		/*
		 * Titre de la page
		 */
		$data['page_title'] = $ref_table_config['reference_title'] . $sup_title;
		/*
		 * La vue qui va s'afficher
		 */
		$data['page'] = 'liste_dt';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur
		 */
		$this->load->view('shared/body', $data);
	}

	//calculate and display the completion progress for user classification tasks
	public function class_completion($type = 'class')
	{
		$users = $this->manager_lib->get_reference_select_values('users;user_name', FALSE);
		//print_test($users);
		$per_user_completion = array();
		if ($type == 'validate') {
			$gen_completion = $this->manager_lib->get_classification_completion('validation', 'all');
			if (!empty($gen_completion['all_papers'])) {
				foreach ($users as $key => $value) {
					$user_completion = $this->manager_lib->get_classification_completion('validation', $key);
					if (!empty($user_completion['all_papers'])) {
						$per_user_completion[$key]['total_papers'] = $user_completion['all_papers'];
						$per_user_completion[$key]['papers_screened'] = $user_completion['processed_papers'];
						$per_user_completion[$key]['completion'] = (int) ($per_user_completion[$key]['papers_screened'] * 100 / $per_user_completion[$key]['total_papers']);
						$per_user_completion[$key]['user'] = $value;
					}
					$per_user_completion['total']['total_papers'] = $gen_completion['all_papers'];
					$per_user_completion['total']['papers_screened'] = $gen_completion['processed_papers'];
					$per_user_completion['total']['completion'] = (int) ($per_user_completion['total']['papers_screened'] * 100 / $per_user_completion['total']['total_papers']);
					$per_user_completion['total']['user'] = 'Total';
				}
			}
		} else {
			$gen_completion = $this->manager_lib->get_classification_completion('class', 'all');
			if (!empty($gen_completion['all_papers'])) {
				foreach ($users as $key => $value) {
					$user_completion = $this->manager_lib->get_classification_completion('class', $key);
					if (!empty($user_completion['all_papers'])) {
						$per_user_completion[$key]['total_papers'] = $user_completion['all_papers'];
						$per_user_completion[$key]['papers_screened'] = $user_completion['processed_papers'];
						$per_user_completion[$key]['completion'] = (int) ($per_user_completion[$key]['papers_screened'] * 100 / $per_user_completion[$key]['total_papers']);
						$per_user_completion[$key]['user'] = $value;
					}
				}
				$per_user_completion['total']['total_papers'] = $gen_completion['all_papers'];
				$per_user_completion['total']['papers_screened'] = $gen_completion['processed_papers'];
				$per_user_completion['total']['completion'] = (int) ($per_user_completion['total']['papers_screened'] * 100 / $per_user_completion['total']['total_papers']);
				$per_user_completion['total']['user'] = 'Total';
			}
		}
		$data['completion_screen'] = $per_user_completion;
		$data['page_title'] = ($type == 'validate') ? lng('Classification validation progress') : lng('Classification progress');
		$data['top_buttons'] = get_top_button('back', 'Back', 'manage');
		//$data['left_menu_perspective']='left_menu_screening';
		//$data['project_perspective']='screening';
		$data['page'] = 'screening/screen_completion';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	//function to facilitate the assignment of papers for classification validation
	public function class_assignment_validation_set($data = array())
	{
		$papers_for_qa = $this->get_papers_for_class_validation();
		//	print_test($papers_for_qa);
		$data['paper_list'] = $papers_for_qa['papers_to_assign_display'];
		$user_table_config = get_table_configuration('users');
		$users = $this->DBConnection_mdl->get_list($user_table_config, '_', 0, -1);
		$_assign_user = array();
		foreach ($users['list'] as $key => $value) {
			if ((user_project($this->session->userdata('project_id'), $value['user_id'])) and can_review_project($value['user_id'])) {
				$_assign_user[$value['user_id']] = $value['user_name'];
			}
		}
		//	print_test($users);
		$data['users'] = $_assign_user;
		$data['number_papers'] = $papers_for_qa['count_papers_to_assign'];
		$data['number_papers_assigned'] = $papers_for_qa['count_papers_assigned'];
		$data['percentage_of_papers'] = get_appconfig_element('class_validation_default_percentage');
		$data['page_title'] = lng('Assign papers for classification validation ');
		$data['top_buttons'] = get_top_button('back', 'Back', 'home');
		$data['page'] = 'data_extraction/assign_papers_class_validation';
		//	print_test($papers_assigned_array);
		//print_test($data);
		//exit;
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	//retrieves the papers to be assigned, fetches eligible users, provides assignment information, and loads the view to display the assignment interface
	public function class_assignment_set($data = array())
	{
		//d
		//$sql="SELECT * from paper  where paper_active = 1 AND screening_status='Included' ";
		$papers_for_qa = $this->get_papers_for_classification();
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
		$data['page_title'] = lng('Assign papers for classification');
		$data['top_buttons'] = get_top_button('back', 'Back', 'home');
		$data['page'] = 'data_extraction/assign_papers_class';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	//retrieves and filters papers for classification assignment. 
	//It determines which papers have already been assigned, retrieves all papers that are eligible for assignment
	private function get_papers_for_classification()
	{
		//papers already assigned
		$papers_assigned = $this->db_current->order_by('assigned_id', 'ASC')
			->get_where('assigned', array('assigned_active' => 1, 'assignment_type' => 'Classification'))
			->result_array();
		$papers_assigned_array = array();
		foreach ($papers_assigned as $key => $value) {
			$papers_assigned_array[$value['assigned_paper_id']] = $value['assigned_user_id'];
		}
		//all papers
		$all_papers = $this->db_current->order_by('id', 'ASC')
			->get_where('paper', array('paper_active' => 1, 'classification_status' => 'To classify', 'paper_excluded' => '0'))
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

	//etrieves and filters papers for classification validation assignment. 
	//It determines which papers have already been assigned for validation, retrieves all papers that are eligible for validation assignment
	private function get_papers_for_class_validation()
	{
		//papers already assigned
		$papers_assigned = $this->db_current->order_by('assigned_id', 'ASC')
			->get_where('assigned', array('assigned_active' => 1, 'assignment_type' => 'Validation'))
			->result_array();
		$papers_assigned_array = array();
		foreach ($papers_assigned as $key => $value) {
			$papers_assigned_array[$value['assigned_paper_id']] = $value['assigned_user_id'];
		}
		//all papers
		//all papers
		$all_papers = $this->db_current->order_by('id', 'ASC')
			->get_where('view_paper_processed`', array('paper_active' => 1))
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

	/*
					handles the form submission to assign papers for classification.
					retrieves the form data, validates the selected users, determines 
					the number of papers to assign, assigns papers to users
				*/
	function class_assignment_save()
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
			$papers_all = $this->get_papers_for_classification();
			$papers = $papers_all['papers_to_assign'];
			//		print_test($papers);
			$papers_to_validate_nbr = round(count($papers) * $percentage / 100);
			$operation_description = "Assign  papers for classification";
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
						'assigned_paper_id' => $value,
						'assigned_user_id' => '',
						'assigned_by' => active_user_id(),
						'operation_code' => $operation_code,
						'assignment_mode' => 'auto',
					);
					$j = 1;
					//the table to save assignments
					$table_name = get_table_configuration('assignation', 'current', 'table_name');
					while ($j <= $reviews_per_paper) {
						$temp_user = ($key % count($users)) + $j;
						if ($temp_user >= count($users))
							$temp_user = $temp_user - count($users);
						$assignment_save['assigned_user_id'] = $users[$temp_user];
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
				'operation_type' => 'assign_class',
				'user_id' => active_user_id(),
				'operation_desc' => $operation_description
			);
			//print_test($operation_arr);
			$res2 = $this->manage_mdl->add_operation($operation_arr);
			set_top_msg('Operation completed');
			redirect('home');
		}
	}

	/*
					handles the form submission to assign papers for classification validation. 
					It retrieves the form data, validates the selected users and percentage, 
					determines the number of papers to assign, assigns papers to users
				*/
	function class_validation_assignment_save()
	{
		$post_arr = $this->input->post();
		//print_test($post_arr); exit;
		$users = array();
		$i = 1;
		$percentage = intval($post_arr['percentage']);
		if (empty($percentage)) {
			$data['err_msg'] = lng(' Please provide  "Percentage of papers" ');
			$this->class_assignment_validation_set($data);
		} elseif ($percentage > 100 or $percentage <= 0) {
			$data['err_msg'] = lng("Please provide a correct value of percentage");
			$this->class_assignment_validation_set($data);
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
				$this->class_assignment_validation_set($data);
			} else {
				$reviews_per_paper = 1;
				$papers_all = $this->get_papers_for_class_validation();
				$papers = $papers_all['papers_to_assign'];
				//		print_test($papers);
				$papers_to_validate_nbr = round(count($papers) * $percentage / 100);
				$operation_description = "Assign  papers for qa";
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
							'assigned_paper_id' => $value,
							'assigned_user_id' => '',
							'assigned_by' => active_user_id(),
							'operation_code' => $operation_code,
							'assignment_mode' => 'auto',
							'assignment_type' => 'Validation',
						);
						$j = 1;
						//the table to save assignments
						$table_name = get_table_configuration('assignation', 'current', 'table_name');
						while ($j <= $reviews_per_paper) {
							$temp_user = ($key % count($users)) + $j;
							if ($temp_user >= count($users))
								$temp_user = $temp_user - count($users);
							$assignment_save['assigned_user_id'] = $users[$temp_user];
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
					'operation_type' => 'assign_class_validation',
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

	/*
					updating the validation status of a paper in the database. 
					It marks the paper as correct if $op is equal to 1
				*/
	function class_validate($paper_id, $op = 1)
	{
		if ($op == 1) {
			$this->db_current->update('assigned', array('validation' => 'Correct', 'validation_note' => '', 'validation_time' => bm_current_time()), array('assigned_paper_id' => $paper_id));
		} else {
			//Get_assignment_id
			$assignment = $this->db_current->get_where(
				'assigned',
				array('assigned_active' => 1, 'assignment_type' => 'Validation', 'assigned_paper_id' => $paper_id)
			)
				->row_array();
			//print_test($assignment); exit;
			//$this->db_current->update('assigned',array('validation'=>'Not Correct','validation_time'=>bm_current_time()),array('assigned_paper_id'=>$paper_id));
			if (!empty($assignment['assigned_id'])) {
				redirect('element/edit_element/class_not_valid/' . $assignment['assigned_id']);
			}
		}
		$after_after_save_redirect = "element/entity_list/list_class_validation";
		redirect($after_after_save_redirect);
	}

	//The purpose of this function is to display a paper or document for validation
	public function display_paper_validation($ref_id)
	{
		$this->display_paper($ref_id, 'validation');
	}

	/*
	 * Fonction spécialisé  pour l'affichage d'un papier
	 * Input:	$ref_id: id du papier
	 */
	public function display_paper($ref_id, $op_type = 'class')
	{
		$table_configuration = get_table_configuration('classification');
		//print_test($table_configuration);
		//	$brice=check_operation('add_classification','Add');
		//	print_test($brice);
		//	print_test(get_table_config('classification'));
		$project_published = project_published();
		$ref_table = "papers";
		/*
		 * Récupération de la configuration(structure) de la table des papiers
		 */
		$table_config = get_table_configuration($ref_table);
		//print_test(get_table_config('classification'));
		/*
		 * Appel de la fonction  récupérer les informations sur le papier afficher
		 */
		$paper_data = $this->manager_lib->get_element_detail('papers', $ref_id);
		//print_test($paper_data);
		/*
		 * Préparations des informations à afficher
		 */
		//venue
		$venue = "";
		foreach ($paper_data as $key => $value) {
			if ($value['title'] == 'Venue' and !empty($value['val2'][0])) {
				$venue = $value['val2'][0];
			}
		}
		//Authors
		$authors = "";
		foreach ($paper_data as $key => $value) {
			if ($value['title'] == 'Author' and !empty($value['val2'])) {
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
		$content_item = $this->DBConnection_mdl->get_row_details($ref_table, $ref_id);
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
		array_push($item_data, $array);
		$array['title'] = "<b>" . lng('Preview') . " :</b> <br/><br/>" . $content_item['preview'];
		array_push($item_data, $array);
		$array['title'] = "<b>" . lng('Venue') . " </b> " . $venue;
		//array_push($item_data, $array);
		$array['title'] = "<b>" . lng('Authors') . " </b> " . $authors;
		//array_push($item_data, $array);
		$data['item_data'] = $item_data;
		/*
		 * Informations sur l'exclusion du papier si le papier est exclu
		 */
		if ($op_type == 'class') {
			$exclusion = $this->DBConnection_mdl->get_exclusion($ref_id);
			$table_config3 = get_table_config("exclusion");
			$dropoboxes = array();
			foreach ($table_config3['fields'] as $k => $v) {
				if (!empty($v['input_type']) and $v['input_type'] == 'select' and $k != 'exclusion_paper_id') {
					if ($v['input_select_source'] == 'array') {
						$dropoboxes[$k] = $v['input_select_values'];
					} elseif ($v['input_select_source'] == 'table') {
						$dropoboxes[$k] = $this->manager_lib->get_reference_select_values($v['input_select_values']);
					}
				}
			}
			$T_item_data_exclusion = array();
			$T_remove_exclusion_button = array();
			$item_data_exclusion = array();
			$delete_exclusion = "";
			$edit_exclusion = "";
			if (!empty($exclusion)) {
				//put values from reference tables
				foreach ($dropoboxes as $k => $v) {
					if (($exclusion[$k])) {
						if (isset($v[$exclusion[$k]])) {
							$exclusion[$k] = $v[$exclusion[$k]];
						}
					} else {
						$exclusion[$k] = "";
					}
				}
				foreach ($table_config3['fields'] as $k_t => $v_t) {
					if (!(isset($v_t['on_view']) and $v_t['on_view'] == 'hidden') and $k_t != 'exclusion_paper_id') {
						$array['title'] = $v_t['field_title'];
						$array['val'] = isset($exclusion[$k_t]) ? ": " . $exclusion[$k_t] : ': ';
						array_push($item_data_exclusion, $array);
					}
				}
				$delete_exclusion = get_top_button('delete', 'Cancel the exclusion', 'relis/manager/remove_exclusion/' . $exclusion['exclusion_id'] . "/" . $ref_id, 'Cancel the exclusion') . " ";
				$edit_exclusion = get_top_button('edit', 'Edit the exclusion', 'relis/manager/edit_exclusion/' . $exclusion['exclusion_id'], 'Edit the exclusion') . " ";
			}
			$data['data_exclusion'] = $item_data_exclusion;
			$data['remove_exclusion_button'] = $edit_exclusion . $delete_exclusion;
		}
		/*
		 * Information sur la classification du papier si le papiers est déjà classé
		 */
		$classification = $this->Data_extraction_dataAccess->get_classifications($ref_id);
		//print_test($classification);
		if (!empty($classification)) {
			//$classification_data=$this->manager_lib->get_element_detail('classification', $classification[0]['class_id'],False,True);
			$table_classification = get_table_configuration('classification');
			$table_classification['current_operation'] = 'detail_classification';
			$classification_data = $this->manager_lib->get_detail($table_classification, $classification[0]['class_id'], FALSE, True);
			//print_test(get_table_config('classification'));
			$data['classification_data'] = $classification_data;
			$delete_button = get_top_button('delete', 'Remove the classification', 'data_extraction/remove_classification/' . $classification[0]['class_id'] . "/" . $ref_id, 'Remove the classification') . " ";
			$edit_button = get_top_button('edit', 'Edit the classification', 'data_extraction/edit_classification/' . $classification[0]['class_id'], 'Edit the classification') . " ";
			$edit_button = get_top_button('edit', 'Edit the classification', 'element/edit_drilldown/update_classification/' . $classification[0]['class_id'] . '/' . $ref_id, 'Edit the classification') . " ";
			$data['classification_button'] = $edit_button . " " . $delete_button;
		} else {
			//if(!empty(	$table_config['links']['add_child']['url']) AND !empty($table_config['links']['add_child']['on_view'])  AND ($table_config['links']['add_child']['on_view']== True) ){
			$data['classification_button'] = get_top_button('add', 'Add classification', 'data_extraction/new_classification/' . $ref_id, 'Add classification') . " ";
			;
			$data['classification_button'] = get_top_button('add', 'Add classification', 'element/add_element_child/new_classification/' . $ref_id, 'Add classification') . " ";
			;
			//}
		}
		if ($op_type != 'class' or $project_published) {
			$data['classification_button'] = "";
		}
		/*
		 * Informations sur l'assignation du papier si le papier est assigné à un utilisateur
		 */
		if ($op_type == 'class') {
			$assignation = $this->DBConnection_mdl->get_assignations($ref_id);
			$table_config3 = get_table_config("assignation");
			$table_config_assignation = get_table_configuration("assignation");
			$table_config_assignation['current_operation'] = 'detail_class_assignment';
			$dropoboxes = array();
			foreach ($table_config3['fields'] as $k => $v) {
				if (!empty($v['input_type']) and $v['input_type'] == 'select' and $k != 'class_paper_id') {
					if ($v['input_select_source'] == 'array') {
						$dropoboxes[$k] = $v['input_select_values'];
					} elseif ($v['input_select_source'] == 'table') {
						$dropoboxes[$k] = $this->manager_lib->get_reference_select_values($v['input_select_values']);
					}
				}
				;
			}
			$T_item_data_assignation = array();
			$T_remove_assignation_button = array();
			foreach ($assignation as $k_class => $v_class) {
				$assignation_data = $this->manager_lib->get_detail($table_config_assignation, $v_class['assigned_id'], FALSE, True);
				$T_item_data_assignation[$k_class] = $assignation_data;
				$delete_button = get_top_button('delete', 'Remove the assignment', 'relis/manager/remove_assignation/' . $v_class['assigned_id'] . "/" . $ref_id, 'Remove the assignment') . " ";
				$edit_button = get_top_button('edit', 'Edit the assignment', 'element/edit_element/edit_assignment_class/' . $v_class['assigned_id'], 'Edit the assignment') . " ";
				$T_remove_assignation_button[$k_class] = $edit_button . $delete_button;
			}
			$data['data_assignations'] = $T_item_data_assignation;
			if (!$project_published) {
				$data['remove_assignation_button'] = $T_remove_assignation_button;
				$data['add_assignation_buttons'] = get_top_button('all', "Assign to a user", 'element/add_element_child/new_assignment_class/' . $ref_id, ' Assign to someone ', " fa-plus ", "  ", 'btn-success') . " ";
			}
		}
		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data['top_buttons'] = "";
		//$data ['add_assignation_buttons']=get_top_button ( 'all', "Assigne to a user", 'relis/manager/new_assignation/'.$ref_id ,' Assigne to someone '," fa-plus ","  ",'btn-success' )." ";
		if (!$project_published) {
			if ($op_type == 'class') {
				if (!$paper_excluded) {
					$data['top_buttons'] .= get_top_button('all', "Exclude the paper", 'relis/manager/new_exclusion/' . $ref_id, 'Exclude', " fa-minus", '', 'btn-danger') . " ";
					if (!empty($table_config['links']['edit']) and !empty($table_config['links']['edit']['on_view']) and ($table_config['links']['edit']['on_view'] == True)) {
						$data['top_buttons'] .= get_top_button('edit', $table_config['links']['edit']['title'], 'manager/edit_element/' . $ref_table . '/' . $ref_id) . " ";
					}
					if (!empty($table_config['links']['delete']) and !empty($table_config['links']['delete']['on_view']) and ($table_config['links']['delete']['on_view'] == True)) {
						$data['top_buttons'] .= get_top_button('delete', $table_config['links']['delete']['title'], 'manage/delete_element/' . $ref_table . '/' . $ref_id) . " ";
					}
				}
				$data['page_title'] = lng('Paper');
			} else {
				$data['page_title'] = lng('Paper - Validation');
				//$data ['classification_button'].=create_button ( 'Correct', 'quality_assessment/qa_validate/'.$ref_id,'Correct',' btn-success');
				if (can_validate_project()) {
					$data['classification_button'] .= get_top_button('all', "Correct", 'data_extraction/class_validate/' . $ref_id, 'Correct', " ", '', 'btn-success') . " ";
					$data['classification_button'] .= get_top_button('all', "Not correct", 'data_extraction/class_validate/' . $ref_id . '/0', 'Not correct', " ", '', 'btn-danger') . " ";
				}
				//$data ['classification_button'].=create_button ( 'Not correct', 'quality_assessment/qa_validate/'.$ref_id.'/0','Not correct',' btn-danger');
			}
		} else {
			if ($op_type == 'class') {
				$data['page_title'] = lng('Paper');
			} else {
				$data['page_title'] = lng('Paper - Validation');
			}
		}
		$data['op_type'] = $op_type;
		$data['top_buttons'] .= get_top_button('back', 'Back', 'manage');
		/*
		 * Titre de la page
		 */
		//	$data ['page_title'] = lng($table_config['reference_title_min']);
		if ($paper_excluded) {
			$data['page_title'] = lng("Paper excluded");
		}
		/*
		 * La vue qui va s'afficher
		 */
		$data['page'] = 'paper/display_paper';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	/**
	 * retrieve the classification scheme, process the field configurations, and display the resulting field configuration data for testing or further processing
	 */
	public function get_classification_scheme()
	{
		$result = $this->Data_extraction_dataAccess->get_classification_scheme();
		$fields = array();
		foreach ($result as $key => $value) {
			$temp = $value;
			unset($temp['scheme_id']);
			unset($temp['field_label']);
			unset($temp['scheme_active']);
			unset($temp['field_order']);
			if ($value['input_type'] == 'select') {
				if ($value['input_select_source'] == 'yes_no') {
					$temp['input_select_source'] = "array";
					$temp['input_select_values'] = array(
						"" => "Select",
						0 => 'No',
						1 => 'Yes',
					);
				} elseif ($value['input_select_source'] == 'array') {
					$Tvalues = explode(',', $value['input_select_values']);
					foreach ($Tvalues as $k => $v) {
						$Tvalues[$k] = $v;
						$val[$v] = $v;
					}
					$temp['input_select_values'] = $val;
				}
			}
			$fields[$value['field_label']] = $temp;
		}
		print_test($fields);
	}
}