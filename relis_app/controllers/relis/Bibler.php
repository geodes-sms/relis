<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This controller contains the definition of function used for bibler integration in Relis
 * @authors Brice & Felix
 * @since 29/03/2017
 */
class Bibler extends CI_Controller {

	function __construct()
	{
		parent::__construct();
					
	}
	
	public function add_paper( $data = "", $operation ='new',$display_type="normal") {
		$ref_table="papers";
		if(admin_config($ref_table))
			$data['left_menu_admin']=True;
			/*
			 * charger la manière d'affichage du formulaire
			 */
			$this->session->set_userdata('submit_mode',$display_type);
	
	
			/*
			 * Récupération de la configuration(structure) de la table concerné
			 */
			$table_config=get_table_config($ref_table);
			//print_test($table_config);
			$table_config['config_id']=$ref_table;
	
			$type_op=$operation=='new'?"on_add":"on_edit";
	
	
			/*
			 * récupération des valeurs qui vont apparaitres dans les dropdown boxes
			 */
			foreach ($table_config['fields'] as $k => $v) {
	
				if(!empty($v['input_type']) AND $v['input_type']=='select'){
					if($v['input_select_source']=='table' AND ($v[$type_op]=='enabled' OR $v[$type_op]=='disabled')){
							
							
						if(isset($table_config['fields'][$k]['multi-select']) AND $table_config['fields'][$k]['multi-select']=='Yes' )
						{
							$table_config['fields'][$k]['input_select_values']= $this->manager_lib->get_reference_select_values($v['input_select_values'],False,False,True);
	
	
						}else{
							$table_config['fields'][$k]['input_select_values']= $this->manager_lib->get_reference_select_values($v['input_select_values'],True,False);
	
						}
					}
				}
					
			}
	
				
			/*
			 * Prépartions des valeurs qui vont apparaitres dans le formulaire
			 */
			$title_append=$table_config['reference_title_min'];
				
			$data['table_config']=$table_config;
	
	
			/*
			 * Titre de la page
			 */
			if ($operation == 'new') {
				// La fonction qui va traiter l'enregistrement dans la DB;
					
				
					
				if(isset($table_config['entity_title']['add'])){
					$data['page_title']=lng($table_config['entity_title']['add']);
				}else{
					$data ['page_title'] = lng('Add '.$title_append);
				}
			} else {
				if(isset($table_config['entity_title']['edit'])){
					$data['page_title']=lng($table_config['entity_title']['edit']);
				}else{
					$data ['page_title'] = lng('Edit '.$title_append);
				}
			}
	
			$data['save_function']='relis/bibler/save_paper';
			$data['operation_type']=$operation;
	
			/*
			 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
			 */
			$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
	
	
			/*
			 * La vue qui va s'afficher
			 */
			
			$data ['page'] = 'relis/frm_paper_bibler';
	
				
			/*
			 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
			 */
			if($display_type=='modal'){
				$this->load->view ( 'general/frm_reference_modal', $data );
			}else{
				$this->load->view ( 'body', $data );
			}
	
	}
	
	
	
	public function edit_paper($ref_id,$display_type="normal") {
		$ref_table='papers';
		/*
		 * Récupération de la configuration(structure) de la table de l'element
		 */
		$table_config=get_table_config($ref_table);
	
	
		/*
		 * Appel de la fonction du model pour récupérer la ligne à modifier
		 */
		$data ['content_item'] = $this->DBConnection_mdl->get_row_details($ref_table,$ref_id);
		
		/*
		 * Appel de la fonction d'affichage du formulaire
		 */
		$this->add_paper ($data, 'edit' ,$display_type);
	}
	
	public function save_paper(){
		
		/*
		 * Récuperation des valeurs soumis dans le formulaire
		 */
		$post_arr = $this->input->post ();
		print_test($post_arr);
		
		
		$operation_type=$post_arr['operation_type'];
		
		if($operation_type=='edit'){
			//Modification
		}else{
			
			//Ajout d'un nouveau papier
		}
		
		
		
	}
	
}
