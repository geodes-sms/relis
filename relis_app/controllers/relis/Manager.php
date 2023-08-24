<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This controller contains the definition of function used for systematic mapping
 * @author Brice
 * @since 09/02/2017
 */
class Manager extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//$this->load->library('paper');
	}

	/*
	 * Affichage du formulaire pour modifier une exclusion d'un papier
	 * $ref_id: id de l'exclusion
	 */
	public function edit_exclusion($ref_id)
	{
		/*
		 * Appel de la fonction du model pour recupérer la ligne à modifier
		 */
		$data['content_item'] = $this->DBConnection_mdl->get_row_details('exclusion', $ref_id);
		//print_test($data);
		/*
		 * Appel de la fonction d'affichage du formulaire
		 */
		$this->new_exclusion($data['content_item']['exclusion_paper_id'], $data, 'edit');
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

	//remove an exclusion and including the associated paper
	public function remove_exclusion($id, $paper_id)
	{
		$res = $this->DBConnection_mdl->remove_element($id, 'exclusion');
		$res1 = $this->Paper_dataAccess->include_paper($paper_id);
		set_top_msg(lng_min('Exclusion cancelled'));
		redirect('data_extraction/display_paper/' . $paper_id);
	}

	//this function is responsible for removing an assignment from a paper
	public function remove_assignation($id, $paper_id)
	{
		$is_guest = check_guest();
		if (!$is_guest) {
			$res = $this->DBConnection_mdl->remove_element($id, 'remove_class_assignment', true);
			set_top_msg(lng_min('Assignment removed'));
			redirect('data_extraction/display_paper/' . $paper_id);
		} else {
			set_top_msg('No access to this operation!', 'error');
			redirect('data_extraction/display_paper/' . $paper_id);
		}
	}

	/*
	 * Fonction  pour afficher la page avec un formulaire pour assigner un papier à un utilisateur
	 *
	 * Input: 	$paper_id: l'id du papier
	 * 			$data : informations sur le papier si la fonction est utilisé pour la mis à jour(modification)
	 * 			$operation: type de l'opération ajout (new) ou modification(edit)
	 *
	 */
	public function new_assignation($paper_id, $data = "", $operation = "new")
	{
		$ref_table_child = 'assignation';
		$child_field = 'assigned_paper_id';
		$ref_table_parent = 'papers';
		/*
		 * Récupération de la configuration(structure) de la table assignation
		 */
		$table_config_child = get_table_config($ref_table_child);
		$table_config_child['config_id'] = $ref_table_child;
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
		$data['operation_source'] = "assignation";
		$data['child_field'] = $child_field;
		$data['table_config_parent'] = $ref_table_parent;
		$data['parent_id'] = $paper_id;
		$parrent_names = $this->manager_lib->get_reference_select_values($table_config_child['fields'][$child_field]['input_select_values']);
		/*
		 * Titre de la page
		 */
		if ($operation == 'edit') {
			$data['page_title'] = lng('Edit the assignation to the paper : ' . $parrent_names[$paper_id]);
		} else {
			$data['page_title'] = 'Assign a user to the paper :' . $parrent_names[$paper_id];
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
		 * Chargement de la vue avec les données préparés dans le controleur
		 */
		$this->load->view('shared/body', $data);
	}

	/* ""software"  "program"  "engineering"  "design*"  "construct*"  "requirement*" 		"architecture"  "test*"  "maintain"  "maintenance"  "configuration management"  "		quality"" */
	/* (("software" OR "program") AND ("engineering" OR "design*" OR "construct*" OR "requirement*" OR
	   "architecture" OR "test*" OR "maintain" OR "maintenance" OR "configuration management" OR "
	   quality")) */

	//used to edit a specific assignment
	public function edit_assignment_mine($assignment_id)
	{
		$this->session->set_userdata('after_save_redirect', "screening/list_screen/mine_assign");
		redirect("manager/edit_element/assignment_screen/$assignment_id");
	}

	//used to edit an assignment for all users (not just the current user)
	public function edit_assignment_all($assignment_id)
	{
		$this->session->set_userdata('after_save_redirect', "screening/list_screen/all_assign");
		redirect("manager/edit_element/assignment_screen/$assignment_id");
	}
}
