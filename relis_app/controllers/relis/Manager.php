<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This controller contains the definition of function used for systematic mapping
 * @author Brice
 * @since 09/02/2017
 */
class Manager extends CI_Controller {

	function __construct()
	{
		parent::__construct();
			
	}


	/*
	 * Fonction  pour afficher la liste des papiers utilisant un Java script datatable
	 *
	 * Input: $paper_cat: indique la categorie à afficher
	 * 			$val : valeur de recherche si une recherche a été faite
	 * 			$page: la page à affiché : ulilisé par les lien de navigation
	 */

	public function list_paper($paper_cat='all',$val = "_", $page = 0, $dynamic_table=0){



		$ref_table="papers";

		/*
		 * Vérification si il y a une recherche faite
		 */
		$val = urldecode ( urldecode ( $val ) );
		$filter = array ();
		if (isset ( $_POST ['search_all'] )) {
			$filter = $this->input->post ();

			unset ( $filter ['search_all'] );

			$val = "_";
			if (isset ( $filter ['valeur'] ) and ! empty ( $filter ['valeur'] )) {
				$val = $filter ['valeur'];
				$val = urlencode ( urlencode ( $val ) );
			}

			/*
			 * mis à jours de l'url en ajoutant la valeur recherché dans le lien puis rechargement de l'url
			 */
			$url = "relis/manager/list_paper/" . $paper_cat ."/". $val ."/0/";

			redirect ( $url );
		}


		/*
		 * Récupération de la configuration(structure) de la table à afficher
		 */
		$ref_table_config=get_table_config($ref_table);

		$table_id=$ref_table_config['table_id'];


		/*
		 * Appel du model pour récuperer la liste à afficher dans la Base de données
		 */

		$rec_per_page=($dynamic_table)?-1:0;

		$data=$this->DBConnection_mdl->get_papers($paper_cat,$ref_table_config,$val,$page,$rec_per_page);
		
		//for select dropboxes

		/*
		 * récupération des correspondances des clès externes
		 */
		$dropoboxes=array();
		foreach ($ref_table_config['fields'] as $k => $v) {

			if(!empty($v['input_type']) AND $v['input_type']=='select' AND $v['on_list']=='show'){
				if($v['input_select_source']=='array'){
					$dropoboxes[$k]=$v['input_select_values'];
				}elseif($v['input_select_source']=='table'){
					$dropoboxes[$k]= $this->manager_lib->get_reference_select_values($v['input_select_values']);
				}elseif($v['input_select_source']=='yes_no'){
					$dropoboxes[$k]=array('0'=>"No",
							'1'=>"Yes"
					);
				}
			}

		}

		/*
		 * Vérification des liens (links) a afficher sur la liste
		 */


		$list_links=array();
		$add_link = false;
		$add_link_url="";
		$view_link_url="";

		foreach ($ref_table_config['links'] as $link_type => $link) {
			if(!empty($link['on_list'])){
				{
					$link['type']=$link_type;


					if(empty($link['title'])){
						$link['title']=lng_min($link['label']);
					}


					$push_link=false;

					switch ($link_type) {
						case 'add':

							$add_link=true; //will appear as a top button

							if(empty($link['url']))
								$add_link_url='manager/add_element/' . $ref_table;
								else
									$add_link_url=$link['url'];

									break;

						case 'view':
							if(!isset($link['icon']))
								$link['icon']='folder';

									
									
								if(empty($link['url']))
									$link['url']='manager/display_element/' . $ref_table.'/';



									$push_link=true;

									break;

						case 'edit':

							if(!isset($link['icon']))
								$link['icon']='pencil';

									
								if(empty($link['url']))
									$link['url']='manager/edit_element/' . $ref_table.'/';

									$push_link=true;
									break;

						case 'delete':

							if(!isset($link['icon']))
								$link['icon']='trash';

									

								if(empty($link['url']))
									$link['url']='manager/delete_element/' . $ref_table.'/';

									$push_link=true;
									break;

						case 'add_child':

							if(!isset($link['icon']))
								$link['icon']='plus';

								if(!empty($link['url'])){

									$link['url']='manager/add_element_child/'.$link['url']."/". $ref_table."/";

									$push_link=true;
								}

								break;

						default:

							break;
					}

					if($push_link)
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
		$field_list=array();
		$field_list_header=array();
		foreach ($ref_table_config['fields'] as $k => $v) {

			if( $v['on_list']=='show'){

				array_push($field_list, $k);
				array_push($field_list_header, $v['field_title']);

			}

		}

		$i=1;
		$list_to_display=array();
		foreach ($data['list'] as $key => $value) {

			$element_array=array();
			foreach ($field_list as $key_field=> $v_field) {
				if(isset($value[$v_field])){
					if(isset($dropoboxes[$v_field][$value[$v_field]]) ){
						$element_array[$v_field]=$dropoboxes[$v_field][$value[$v_field]];
					}else{
						$element_array[$v_field]=$value[$v_field];
					}


				}else{



					$element_array[$v_field]="";

					if(isset($ref_table_config['fields'][$v_field]['number_of_values']) AND $ref_table_config['fields'][$v_field]['number_of_values']!=1){
							
						if(isset($ref_table_config['fields'][$v_field]['input_select_values']) AND isset($ref_table_config['fields'][$v_field]['input_select_key_field']))
						{
							// récuperations des valeurs de cet element
							$M_values=$this->manager_lib->get_element_multi_values($ref_table_config['fields'][$v_field]['input_select_values'],$ref_table_config['fields'][$v_field]['input_select_key_field'],$data ['list'] [$key] [$table_id]);
							$S_values="";
							foreach ($M_values as $k_m => $v_m) {
								if(isset($dropoboxes[$v_field][$v_m]) ){
									$M_values[$k_m]=$dropoboxes[$v_field][$v_m];
								}
									
								$S_values.=empty($S_values)?$M_values[$k_m]:" | ".$M_values[$k_m];
							}

							$element_array[$v_field]=$S_values;
						}
							
					}




				}
					
					
					
					
			}


			/*
			 * Ajout des liens(links) sur la liste
			 */

			$action_button="";
			$arr_buttons=array();
			$view_link_url="";

			foreach ($list_links as $key_l => $value_l) {
				if($value_l['type']=='view'){
					$view_link_url=$value_l['url'].$value [$table_id];
				}else{
					if(!empty($value_l['icon']))
						$value_l['label']= icon($value_l['icon']).' '.lng_min($value_l['label']);
							
						array_push($arr_buttons, array(
								'url'=> $value_l['url'].$value [$table_id],
								'label'=>$value_l['label'],
								'title'=>$value_l['title']

						)	);
				}
					
			}


			$action_button=create_button_link_dropdown($arr_buttons,lng_min('Action'));
			$element_array['links']=$action_button;

			if(isset($element_array['title']) AND !empty($view_link_url)){
				$element_array['title']=anchor($view_link_url,"<u><b>".$element_array['title']."</b></u>",'title="'.lng_min('Display element').'")');
			}
			if(isset($element_array[$table_id])){
				$element_array[$table_id]=$i + $page;
			}

			array_push($list_to_display,$element_array);
			$i++;



		}

		$data ['list']=$list_to_display;

		/*
		 * Ajout de l'entête de la liste
		 */
		if(!empty($data['list'])){
			$array_header=$field_list_header;;
			if(trim($data['list'][$key]['links']) !=""){
				array_push($array_header,'');
			}

			if(!$dynamic_table){
				array_unshift($data['list'],$array_header);
			}else{
				$data['list_header']=$array_header;
			}

		}

		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */

		$data ['top_buttons']="";
		if($data['nombre']==0 AND $paper_cat== 'all'){
			//$data ['top_buttons'] .= get_top_button ( 'all', 'Add test papers', 'install/create_default_papers','test papers');
		}

		if($add_link)
			$data ['top_buttons'] .= get_top_button ( 'add', 'Add new', $add_link_url );


			if(activate_update_stored_procedure())
				$data ['top_buttons'] .= get_top_button ( 'all', 'Update stored procedure', 'home/update_stored_procedure/'.$ref_table,'Update stored procedure','fa-check','',' btn-dark ' );

				$data ['top_buttons'] .= get_top_button ( 'close', 'Close', 'home' );



				/*
				 * Titre de la page
				 */
				if($paper_cat== 'pending'   ){

					$data['page_title']=$ref_table_config['reference_title'].' - Pending';

				}elseif($paper_cat== "processed" ){
					$data['page_title']=$ref_table_config['reference_title'].' - Classified';

				}elseif($paper_cat== "assigned_me" ){
					$data['page_title']=$ref_table_config['reference_title'].' - Assigned to me';

				}elseif($paper_cat== "excluded" ){
					$data['page_title']=$ref_table_config['reference_title'].' - Excluded';

				}else{

					$data['page_title']=$ref_table_config['reference_title'];
				}





				$data ['valeur']=($val=="_")?"":$val;

				if(!$dynamic_table AND  !empty($ref_table_config['search_by'])){
					$data ['search_view'] = 'general/search_view';}


					/*
					 * La vue qui va s'afficher
					 */
					if(!$dynamic_table){
						$data ['nav_pre_link'] = 'relis/manager/list_paper/' .$paper_cat.'/' . $val . '/';
						$data ['nav_page_position'] = 6;
						$data['page']='general/list';
					}else{
						$data['page']='general/list_dt';
					}
					/*
					 * Chargement de la vue avec les données préparés dans le controleur
					 */
					$this->load->view('body',$data);
	}



	public function display_paper_validation($ref_id) {

		$this->display_paper($ref_id,'validation');

	}
	/*
	 * Fonction spécialisé  pour l'affichage d'un papier
	 * Input:	$ref_id: id du papier
	 */
	public function display_paper($ref_id,$op_type='class') {
		$table_configuration=get_table_configuration('classification');
		//print_test($table_configuration);
		//	$brice=check_operation('add_classification','Add');
		//	print_test($brice);
		//	print_test(get_table_config('classification'));
		$project_published=project_published();
		$ref_table="papers";

		/*
		 * Récupération de la configuration(structure) de la table des papiers
		 */
		$table_config=get_table_configuration($ref_table);

		//print_test(get_table_config('classification'));
		/*
		 * Appel de la fonction  récupérer les informations sur le papier afficher
		 */
		$paper_data=$this->manager_lib->get_element_detail('papers',$ref_id);

		//print_test($paper_data);


		/*
		 * Préparations des informations à afficher
		 */

		//venue
		$venue="";
		foreach ($paper_data as $key => $value) {
			if($value['title']=='Venue' AND !empty($value['val2'][0])){
				$venue=$value['val2'][0];
			}
		}

		//Authors
		$authors="";
		foreach ($paper_data as $key => $value) {

			if($value['title']=='Author' AND !empty($value['val2'])){

				if(count($value['val2']>1)){
					$authors='<table class="table table-hover" ><tr><td> '.$value['val2'][0].'</td></tr>';
					foreach ($value['val2'] as $k => $v) {
						if($k>0){
							$authors.="<tr><td> ".$v.'</td></tr>';
						}
					}

					$authors.="</table>";
				}else{

					$authors=" : ".$value['val2'][0];
				}

			}
		}






		$content_item = $this->DBConnection_mdl->get_row_details ( $ref_table,$ref_id );

		$paper_name=$content_item['bibtexKey']." - ".$content_item['title'];
		$paper_excluded=False;
		if($content_item['paper_excluded']=='1'){
			$paper_excluded=True;
		}

		$data['paper_excluded']=$paper_excluded;
		$item_data=array();


		$array['title']=$content_item['bibtexKey']." - ".$content_item['title'];

		if(!empty($content_item['doi'])){
            $paper_link = $content_item['doi'];
            if( (strpos($paper_link,'http://') === FALSE) && (strpos($paper_link,'https://') === FALSE)){
                $paper_link = "//".$paper_link;
            }

			$array['title'].='<ul class="nav navbar-right panel_toolbox">
				<li>
					<a title="Go to the page" href="' . $paper_link . '" target="_blank" >
				 		<img src="'.base_url().'cside/images/pdf.jpg"/>

					</a>
				</li>

				</ul>';
		}
			

		array_push($item_data, $array);

		$array['title']="<b>".lng('Abstract')." :</b> <br/><br/>".$content_item['abstract'];
		array_push($item_data, $array);
		$array['title']="<b>".lng('Preview')." :</b> <br/><br/>".$content_item['preview'];
		array_push($item_data, $array);

		$array['title']="<b>".lng('Venue')." </b> ".$venue;
		//array_push($item_data, $array);

		$array['title']="<b>".lng('Authors')." </b> ".$authors;
		//array_push($item_data, $array);

			

		$data['item_data']=$item_data;










		/*
		 * Informations sur l'exclusion du papier si le papier est exclu
		 */
		if($op_type=='class'){
			$exclusion = $this->DBConnection_mdl->get_exclusion ($ref_id );

			$table_config3=get_table_config("exclusion");
			$dropoboxes=array();
			foreach ($table_config3['fields'] as $k => $v) {

				if(!empty($v['input_type']) AND $v['input_type']=='select' AND $k!='exclusion_paper_id'){
					if($v['input_select_source']=='array'){
						$dropoboxes[$k]=$v['input_select_values'];
					}elseif($v['input_select_source']=='table'){
						$dropoboxes[$k]= $this->manager_lib->get_reference_select_values($v['input_select_values']);
					}
				}
					
			}


			$T_item_data_exclusion=array();
			$T_remove_exclusion_button =array();
			$item_data_exclusion=array();
			$delete_exclusion="";
			$edit_exclusion="";

			if (!empty($exclusion)) {

				//put values from reference tables
				foreach ($dropoboxes as $k => $v) {
					if(($exclusion[$k])){
						if(isset($v[$exclusion[$k]])){
							$exclusion[$k]=$v[$exclusion[$k]];}
					}
					else{
						$exclusion[$k]="";
					}
				}


				foreach ($table_config3['fields'] as $k_t => $v_t) {

					if(!(isset($v_t['on_view']) AND $v_t['on_view']=='hidden' ) AND  $k_t!='exclusion_paper_id'){

						$array['title']=$v_t['field_title'];
						$array['val']=isset($exclusion[$k_t])?": ".$exclusion[$k_t]:': ';

						array_push($item_data_exclusion, $array);

					}
				}

				$delete_exclusion= get_top_button ( 'delete', 'Cancel the exclusion', 'relis/manager/remove_exclusion/'.$exclusion['exclusion_id']."/".$ref_id , 'Cancel the exclusion')." ";

				$edit_exclusion= get_top_button ( 'edit', 'Edit the exclusion', 'relis/manager/edit_exclusion/'.$exclusion['exclusion_id'], 'Edit the exclusion')." ";


			}


			$data['data_exclusion']=$item_data_exclusion;
			$data['remove_exclusion_button']=$edit_exclusion.$delete_exclusion;



		}


		/*
		 * Information sur la classification du papier si le papiers est déjà classé
		 */

		$classification = $this->DBConnection_mdl->get_classifications ($ref_id );

		//print_test($classification);
		if(!empty($classification)){

			//$classification_data=$this->manager_lib->get_element_detail('classification', $classification[0]['class_id'],False,True);

			$table_classification=get_table_configuration('classification');

			$table_classification['current_operation']='detail_classification';

			$classification_data=$this->manager_lib->get_detail($table_classification, $classification[0]['class_id'],FALSE,True);

			//print_test(get_table_config('classification'));

			$data['classification_data']=$classification_data;

			$delete_button= get_top_button ( 'delete', 'Remove the classification', 'relis/manager/remove_classification/'.$classification[0]['class_id']."/".$ref_id , 'Remove the classification')." ";

			$edit_button= get_top_button ( 'edit', 'Edit the classification', 'relis/manager/edit_classification/'.$classification[0]['class_id'], 'Edit the classification')." ";
			$edit_button= get_top_button ( 'edit', 'Edit the classification', 'op/edit_drilldown/update_classification/'.$classification[0]['class_id'].'/'.$ref_id, 'Edit the classification')." ";

			$data['classification_button']=$edit_button." ".$delete_button;
		}else{
			//if(!empty(	$table_config['links']['add_child']['url']) AND !empty($table_config['links']['add_child']['on_view'])  AND ($table_config['links']['add_child']['on_view']== True) ){

			$data ['classification_button'] =get_top_button ( 'add', 'Add classification', 'relis/manager/new_classification/'.$ref_id, 'Add classification')." ";;
			$data ['classification_button'] =get_top_button ( 'add', 'Add classification', 'op/add_element_child/new_classification/'.$ref_id, 'Add classification')." ";;

			//}
		}

		if($op_type !='class' OR $project_published ){
			$data ['classification_button']="";
		}



		/*
		 * Informations sur l'assignation du papier si le papier est assigné à un utilisateur
		 */
		if($op_type=='class'){
			$assignation = $this->DBConnection_mdl->get_assignations ($ref_id );


			$table_config3=get_table_config("assignation");


			$table_config_assignation=get_table_configuration("assignation");

			$table_config_assignation['current_operation']='detail_class_assignment';
			$dropoboxes=array();
			foreach ($table_config3['fields'] as $k => $v) {

				if(!empty($v['input_type']) AND $v['input_type']=='select' AND $k!='class_paper_id'){
					if($v['input_select_source']=='array'){
						$dropoboxes[$k]=$v['input_select_values'];
					}elseif($v['input_select_source']=='table'){
						$dropoboxes[$k]= $this->manager_lib->get_reference_select_values($v['input_select_values']);
					}
				}
				;
			}

			$T_item_data_assignation=array();
			$T_remove_assignation_button =array();
			foreach ($assignation as $k_class => $v_class) {
					
				$assignation_data=$this->manager_lib->get_detail($table_config_assignation,$v_class['assigned_id'],FALSE,True);
					

				$T_item_data_assignation[$k_class]=$assignation_data;


				$delete_button= get_top_button ( 'delete', 'Remove the assignment', 'relis/manager/remove_assignation/'.$v_class['assigned_id']."/".$ref_id , 'Remove the assignment')." ";

				$edit_button= get_top_button ( 'edit', 'Edit the assignment', 'op/edit_element/edit_assignment_class'.$v_class['assigned_id'], 'Edit the assignment')." ";

				$T_remove_assignation_button[$k_class]=$edit_button.$delete_button;

			}

			$data['data_assignations']=$T_item_data_assignation;
			if(!$project_published){
				$data['remove_assignation_button']=$T_remove_assignation_button;

				$data ['add_assignation_buttons']=get_top_button ( 'all', "Assign to a user", 'op/add_element_child/new_assignment_class/'.$ref_id ,' Assign to someone '," fa-plus ","  ",'btn-success' )." ";
			}

		}



		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data ['top_buttons']="";

		//$data ['add_assignation_buttons']=get_top_button ( 'all', "Assigne to a user", 'relis/manager/new_assignation/'.$ref_id ,' Assigne to someone '," fa-plus ","  ",'btn-success' )." ";
		if(!$project_published){
			if($op_type=='class'){
				if(!$paper_excluded){

					$data ['top_buttons'].=get_top_button ( 'all', "Exclude the paper", 'relis/manager/new_exclusion/'.$ref_id ,'Exclude'," fa-minus",'','btn-danger' )." ";


					if(!empty(	$table_config['links']['edit']) AND !empty($table_config['links']['edit']['on_view'])  AND ($table_config['links']['edit']['on_view']== True) ){

						$data ['top_buttons'] .= get_top_button ( 'edit', $table_config['links']['edit']['title'], 'manager/edit_element/' . $ref_table.'/'.$ref_id )." ";

					}

					if(!empty(	$table_config['links']['delete']) AND !empty($table_config['links']['delete']['on_view'])  AND ($table_config['links']['delete']['on_view']== True) ){

						$data ['top_buttons'] .= get_top_button ( 'delete', $table_config['links']['delete']['title'], 'manage/delete_element/' . $ref_table.'/'.$ref_id )." ";

					}

				}
				$data ['page_title'] = lng('Paper');
			}else{
				$data ['page_title'] = lng('Paper - Validation');
				//$data ['classification_button'].=create_button ( 'Correct', 'relis/manager/qa_validate/'.$ref_id,'Correct',' btn-success');
				if(can_validate_project()){
					$data ['classification_button'].=get_top_button ( 'all', "Correct", 'relis/manager/class_validate/'.$ref_id,'Correct'," ",'','btn-success' )." ";
					$data ['classification_button'].=get_top_button ( 'all', "Not correct", 'relis/manager/class_validate/'.$ref_id.'/0','Not correct'," ",'','btn-danger' )." ";
				}
				//$data ['classification_button'].=create_button ( 'Not correct', 'relis/manager/qa_validate/'.$ref_id.'/0','Not correct',' btn-danger');

			}
		}else{
			if($op_type=='class'){
				$data ['page_title'] = lng('Paper');
			}else{
				$data ['page_title'] = lng('Paper - Validation');
			}
		}
		$data['op_type']=$op_type;
		$data ['top_buttons'] .= get_top_button ( 'back', 'Back', 'manage' );



		/*
		 * Titre de la page
		 */
		//	$data ['page_title'] = lng($table_config['reference_title_min']);


		if($paper_excluded){
			$data ['page_title'] = lng("Paper excluded");
		}


		/*
		 * La vue qui va s'afficher
		 */
		$data ['page'] = 'relis/display_paper';


		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}



	/*
	 * Affichage du formulaire pour modifier une exclusion d'un papier
	 * $ref_id: id de l'exclusion
	 */
	public function edit_exclusion($ref_id) {

		/*
		 * Appel de la fonction du model pour recupérer la ligne à modifier
		 */
		$data ['content_item'] = $this->DBConnection_mdl->get_row_details('exclusion',$ref_id);
		//print_test($data);

		/*
		 * Appel de la fonction d'affichage du formulaire
		 */
		$this->new_exclusion ( $data ['content_item']['exclusion_paper_id'], $data, 'edit' );
	}



	/*
	 * Fonction  pour afficher la page avec un formulaire pour l'exclusion d'un papier
	 *
	 * Input: 	$paper_id: l'id du papier
	 * 			$data : informations sur le papier si la fonction est utilisé pour la mis à jour(modification)
	 * 			$operation: type de l'opération ajout (new) ou modification(edit)
	 *
	 */
	public function new_exclusion($paper_id, $data = "",$operation="new") {

		$ref_table_child= 'exclusion';
		$child_field= 'exclusion_paper_id';
		$ref_table_parent= 'papers';

		/*
		 * Récupération de la configuration(structure) de la table exclusion
		 */
		$table_config_child=get_table_config($ref_table_child);
		$table_config_child['config_id']=$ref_table_child;


		/*
		 * Récupération de la configuration(structure) de la table des papiers
		 */
		$table_config_parent=get_table_config($ref_table_parent);

		$table_config_child['fields'][$child_field]['on_add']="hidden";
		$table_config_child['fields'][$child_field]['on_edit']="hidden";
		$table_config_child['fields'][$child_field]['input_type']="text";




		foreach ($table_config_child['fields'] as $k => $v) {

			if(!empty($v['input_type']) AND $v['input_type']=='select'){
				if($v['input_select_source']=='table'){
					$table_config_child['fields'][$k]['input_select_values']= $this->manager_lib->get_reference_select_values($v['input_select_values']);
				}
			}

		}


		/*
		 * Prépartions des valeurs qui vont apparaitres dans le formulaire
		 */
		$data['content_item'][$child_field]=$paper_id;
		$data['table_config']=$table_config_child;
		$data['operation_type']=$operation;
		$data['operation_source']="exclusion";
		$data['child_field']=$child_field;
		$data['table_config_parent']=$ref_table_parent;
		$data['parent_id']=$paper_id;



		/*
		 * Titre de la page
		 */
		$parrent_names=$this->manager_lib->get_reference_select_values($table_config_child['fields'][$child_field]['input_select_values']);
		if($operation=='edit'){
			$data ['page_title'] = 'Edit Exclusion of the '.$table_config_parent['reference_title_min']." : ".$parrent_names[$paper_id];
		}else{
			$data ['page_title'] = 'Exclusion of the '.$table_config_parent['reference_title_min']." : ".$parrent_names[$paper_id];

		}




		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );


		/*
		 * La vue qui va s'afficher
		 */
		$data ['page'] = 'general/frm_reference';



		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}


	public function remove_exclusion ($id,$paper_id){
		$res=$this->DBConnection_mdl->remove_element($id,'exclusion');
		$res1=$this->DBConnection_mdl->include_paper($paper_id);

		set_top_msg(lng_min('Exclusion cancelled'));

		redirect ( 'relis/manager/display_paper/' .$paper_id  );
	}

	public function remove_assignation ($id,$paper_id){
		$res=$this->DBConnection_mdl->remove_element($id,'remove_class_assignment',true);
		set_top_msg(lng_min('Assignment removed'));
		redirect ( 'relis/manager/display_paper/' .$paper_id  );
	}

	public function remove_classification ($id,$paper_id){
		$res=$this->DBConnection_mdl->remove_element($id,'classification');
		set_top_msg(lng_min('Classification removed'));
		redirect ( 'relis/manager/display_paper/' .$paper_id  );
	}

	/*
	 * Fonction  pour afficher la page avec un formulaire pour assigner un papier à un utilisateur
	 *
	 * Input: 	$paper_id: l'id du papier
	 * 			$data : informations sur le papier si la fonction est utilisé pour la mis à jour(modification)
	 * 			$operation: type de l'opération ajout (new) ou modification(edit)
	 *
	 */

	public function new_assignation($paper_id, $data = "",$operation="new") {

		$ref_table_child= 'assignation';
		$child_field= 'assigned_paper_id';
		$ref_table_parent= 'papers';


		/*
		 * Récupération de la configuration(structure) de la table assignation
		 */
		$table_config_child=get_table_config($ref_table_child);
		$table_config_child['config_id']=$ref_table_child;

		/*
		 * Récupération de la configuration(structure) de la table des papiers
		 */
		$table_config_parent=get_table_config($ref_table_parent);

		$table_config_child['fields'][$child_field]['on_add']="hidden";
		$table_config_child['fields'][$child_field]['on_edit']="hidden";
		$table_config_child['fields'][$child_field]['input_type']="text";



		/*
		 * récupération des valeurs qui vont apparaitre dans les champs select
		 */
		foreach ($table_config_child['fields'] as $k => $v) {

			if(!empty($v['input_type']) AND $v['input_type']=='select'){
				if($v['input_select_source']=='table'){
					$table_config_child['fields'][$k]['input_select_values']= $this->manager_lib->get_reference_select_values($v['input_select_values']);
				}
			}

		}

		/*
		 * Prépartions des valeurs qui vont apparaitres dans le formulaire
		 */
		$data['content_item'][$child_field]=$paper_id;
		$data['table_config']=$table_config_child;
		$data['operation_type']=$operation;
		$data['operation_source']="assignation";
		$data['child_field']=$child_field;
		$data['table_config_parent']=$ref_table_parent;
		$data['parent_id']=$paper_id;


		$parrent_names=$this->manager_lib->get_reference_select_values($table_config_child['fields'][$child_field]['input_select_values']);

		/*
		 * Titre de la page
		 */
		if($operation=='edit'){
			$data ['page_title'] = lng('Edit the assignation to the paper : '.$parrent_names[$paper_id]);
		}else{
			$data ['page_title'] = 'Assign a user to the paper :'.$parrent_names[$paper_id];

		}





		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );


		/*
		 * La vue qui va s'afficher
		 */
		$data ['page'] = 'general/frm_reference';


		/*
		 * Chargement de la vue avec les données préparés dans le controleur
		 */
		$this->load->view ( 'body', $data );
	}


	/*
	 * spécialisation de la fonction add_classification lorsque le formulaire s'affiche en pop up
	 */
	public function new_classification_modal($parent_id, $data = "",$operation="new") {

		$this->new_classification ( $parent_id, $data, $operation,'modal' );

	}


	/*
	 * Fonction  pour afficher la page avec un formulaire d'ajout d'une classification
	 *
	 * Input: $parent_id: l'id du papier à qui on va ajouter une classification
	 * 			$data : informations sur l'élément si la fonction est utilisé pour la mis à jour(modification)
	 * 			$operation: type de l'opération ajout (new) ou modification(edit)
	 * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */

	public function new_classification($parent_id, $data = "",$operation="new",$display_type="normal") {


		$ref_table_child= 'classification';
		$child_field= 'class_paper_id';
		$ref_table_parent= 'papers';

		/*
		 * Récupération de la configuration(structure) de la table classification
		 */
		$table_config_child=get_table_config($ref_table_child);
		$table_config_child['config_id']=$ref_table_child;

		//	print_test($table_config_child);

		//si les valeurs provienne d'une redirection apres tenetative d'enregistrement
			
		if(!empty($data) AND $data=='sess_redirect'){
			$data=$this->session->userdata('redirect_values');

			if(isset($data['content_item']['class_id'])){

				$classification_id=$data['content_item']['class_id'];


				$element_detail=$this->manager_lib->get_element_detail($ref_table_child,$classification_id,true,true);

				$drill_down_values=array();

				foreach ($table_config_child['fields'] as $key => $v) {
					if(!empty($v['input_type']) AND $v['input_type']=='select' AND $v['input_select_source']=='table'){
						if (!empty($v['on_edit']) AND $v['on_edit']=='drill_down'){
							//Recuperation des valeurs pour les drilldown

							foreach ($element_detail as $key_el => $value_el) {
								if($value_el['field_id']==$key){

									$drill_down_values[$key]=$value_el['val2'];
								}
							}

						}


					}

				}

				//print_test($drill_down_values);
				$data['drill_down_values']=$drill_down_values;

			}
		}


		/*
		 * chargement de la manière d'affichage du formulaire
		 */

		$this->session->set_userdata('submit_mode',$display_type);



		//print_test($table_config_child);


		/*
		 * Récupération de la configuration(structure) de la table des papiers
		 */
		$table_config_parent=get_table_config($ref_table_parent);

		$table_config_child['fields'][$child_field]['on_add']="hidden";
		$table_config_child['fields'][$child_field]['on_edit']="hidden";
		$table_config_child['fields'][$child_field]['input_type']="text";




		/*
		 * récupération des valeurs qui vont apparaitre dans les champs select
		 */
		foreach ($table_config_child['fields'] as $k => $v) {

			if(!empty($v['input_type']) AND $v['input_type']=='select'){
				if($v['input_select_source']=='table'){
					if(isset($table_config_child['fields'][$k]['multi-select']) AND $table_config_child['fields'][$k]['multi-select']=='Yes' )
					{
						$table_config_child['fields'][$k]['input_select_values']= $this->manager_lib->get_reference_select_values($v['input_select_values'],False,False,True);


					}else{
						$table_config_child['fields'][$k]['input_select_values']= $this->manager_lib->get_reference_select_values($v['input_select_values']);


					}
				}
			}

		}
		/*
		 * Prépartions des valeurs qui vont apparaitres dans le formulaire
		 */

		$data['content_item'][$child_field]=$parent_id;
		$data['table_config']=$table_config_child;
		$data['operation_type']=$operation;
		$data['operation_source']="paper";
		$data['child_field']=$child_field;
		$data['table_config_parent']=$ref_table_parent;
		$data['parent_id']=$parent_id;



		/*
		 * Titre de la page
		 */

		$parrent_names=$this->manager_lib->get_reference_select_values($table_config_child['fields'][$child_field]['input_select_values']);

		if($operation=='edit'){
			$data ['page_title'] = lng("Edit  classification for the paper : ").$parrent_names[$parent_id];
			$this->session->set_userdata('after_save_redirect','relis/manager/display_paper/'.$parent_id);
		}else{
			//	$data ['page_title'] = lng('Add a '.$table_config_child['reference_title_min']." to the ".$table_config_parent['reference_title_min'])." : ".$parrent_names[$parent_id];
			$data ['page_title'] = lng("Add a classification  to the paper : ") . $parrent_names[$parent_id];
			$this->session->set_userdata('after_save_redirect','relis/manager/list_classification');

		}



		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );

		/*
		 * La vue qui va s'afficher
		 */
		$data ['page'] = 'general/frm_reference';


		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		if($display_type=='modal'){
			$this->load->view ( 'frm_reference_modal', $data );}else{
				$this->load->view ( 'body', $data );
			}

	}



	/*
	 * Affichage du formulaire pour modifier une classification
	 * $ref_id: id de la classification
	 * $display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
	 */
	public function edit_classification($ref_id,$display_type="normal") {

		$this->session->set_userdata('submit_mode',$display_type);


		/*
		 * Récupération de la configuration(structure) de la table classification
		 */
		$ref_table="classification";
		$table_config=get_table_config($ref_table);


		//print_test($table_config);
		/*
		 * Appel de la fonction du model pour récupérer la ligne à modifier
		 */
		$data ['content_item'] = $this->DBConnection_mdl->get_row_details($ref_table,$ref_id);

		$element_detail=$this->manager_lib->get_element_detail($ref_table,$ref_id,true,true);
		//	print_test($element_detail);



		$drill_down_values=array();

		foreach ($table_config['fields'] as $key => $v) {
			if(!empty($v['input_type']) AND $v['input_type']=='select' AND $v['input_select_source']=='table'){
				/*
				 * Récuperation des valeurs pour les champs multi-select
				 */
				if(!empty($v['multi-select']) AND $v['multi-select']=='Yes' )
				{

					$Tvalues_source=explode(';', $v['input_select_values']);
					$source_table_config=get_table_config($Tvalues_source[0]);
					$input_select_key_field=$v['input_select_key_field'];
					$input_child_field=$Tvalues_source[1];

					$extra_condition=" AND $input_select_key_field ='".$ref_id."'";

					$res_values=$this->DBConnection_mdl->get_reference_select_values($source_table_config,$input_child_field,$extra_condition);
					$data ['content_item'][$key]=array();

					foreach ($res_values as $key_r => $value_r) {
						array_push($data ['content_item'][$key], $value_r['refDesc']);
					}

				}elseif (!empty($v['on_edit']) AND $v['on_edit']=='drill_down'){
					//Recuperation des valeurs pour les drilldown

					foreach ($element_detail as $key_el => $value_el) {
						if($value_el['field_id']==$key){

							$drill_down_values[$key]=$value_el['val2'];
						}
					}

				}


					




			}

		}

		//print_test($drill_down_values);
		$data['drill_down_values']=$drill_down_values;


		/*
		 * Appel de la fonction d'affichage du formulaire
		 */
		$this->new_classification ( $data ['content_item']['class_paper_id'], $data, 'edit',$display_type );
	}

	/*
	 * Fonction utilisé pour faire une recherche dans la liste des classifications
	 *
	 * fields: le champs où on effectue la recherche
	 * $value: la valeur recherché
	 */
	public function search_classification($field,$value){

		$condition=array('classification_search_field'=>$field,
				'classification_search_value'=>$value
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

	public function list_classification($list_type='normal',$val = "_", $page = 0,$dynamic_table=1){

		// nouvelle fonction pour afficher la liste des classification elle utilise les data tables
		////redirect("manage/list_classification_dt/$list_type/$val/$page");


		$ref_table='classification';

		$val = urldecode ( urldecode ( $val ) );
		$filter = array ();
		if (isset ( $_POST ['search_all'] )) {
			$filter = $this->input->post ();
			// print_test($filter);exit;
			unset ( $filter ['search_all'] );

			$val = "_";
			if (isset ( $filter ['valeur'] ) and ! empty ( $filter ['valeur'] )) {
				$val = $filter ['valeur'];
				$val = urlencode ( urlencode ( $val ) );
			}


			$url = "relis/manager/list_classification/" . $ref_table ."/". $val ."/0/";

			redirect ( $url );
		}


		$ref_table_config=get_table_config($ref_table);


		$table_id=$ref_table_config['table_id'];


		$condition=array();
		$extra_condition="";
		$sup_title="";

		if($list_type=='search_cat')
		{

			if( $this->session->userdata('classification_search_field') AND $this->session->userdata('classification_search_value') ){

				$field=$this->session->userdata('classification_search_field');
				$value=$this->session->userdata('classification_search_value');

				$extra_condition =" AND ( ".$field."='".$value."') ";

				$value_desc=$value;

				if(!empty($ref_table_config['fields'][$field]['input_type']) AND $ref_table_config['fields'][$field]['input_type']=='select' ){
					$values=  $this->manager_lib->get_reference_select_values($ref_table_config['fields'][$field]['input_select_values']);


					$value_desc=$values[$value];


					$sup_title = " for \"". $ref_table_config['fields'][$field]['field_title']."\" :  $value_desc";
				}


			}


		}


		$rec_per_page=($dynamic_table)?-1:0;
			
		if(!empty($extra_condition)){

			$data=$this->manage_mdl->get_list($ref_table_config,$val,$page,$rec_per_page,$extra_condition);
		}else{
			$data=$this->DBConnection_mdl->get_list($ref_table_config,$val,$page,$rec_per_page,$extra_condition);
		}




		//for  dropboxes
		//print_test($ref_table_config);
		$dropoboxes=array();
		foreach ($ref_table_config['fields'] as $k => $v) {

			if(!empty($v['input_type']) AND $v['input_type']=='select' AND $v['on_list']=='show'){
				if($v['input_select_source']=='array'){
					$dropoboxes[$k]=$v['input_select_values'];
				}elseif($v['input_select_source']=='table'){
					$dropoboxes[$k]= $this->manager_lib->get_reference_select_values($v['input_select_values']);
				}elseif($v['input_select_source']=='yes_no'){
					$dropoboxes[$k]=array('0'=>"No",
							'1'=>"Yes"
					);
				}
			}

		}


		/*
		 * Vérification des liens (links) a afficher sur la liste
		 */


		$list_links=array();
		$add_link = false;
		$add_link_url="";
		$view_link_url="";

		foreach ($ref_table_config['links'] as $link_type => $link) {
			if(!empty($link['on_list'])){
				{
					$link['type']=$link_type;


					if(empty($link['title'])){
						$link['title']=lng_min($link['label']);
					}


					$push_link=false;

					switch ($link_type) {
						case 'add':

							$add_link=true; //will appear as a top button

							if(empty($link['url']))
								$add_link_url='manager/add_element/' . $ref_table;
								else
									$add_link_url=$link['url'];

									break;

						case 'view':
							if(!isset($link['icon']))
								$link['icon']='folder';

									
									
								if(empty($link['url']))
									$link['url']='manager/display_element/' . $ref_table.'/';



									$push_link=true;

									break;

						case 'edit':

							if(!isset($link['icon']))
								$link['icon']='pencil';

									
								if(empty($link['url']))
									$link['url']='manager/edit_element/' . $ref_table.'/';

									$push_link=true;
									break;

						case 'delete':

							if(!isset($link['icon']))
								$link['icon']='trash';

									

								if(empty($link['url']))
									$link['url']='manager/delete_element/' . $ref_table.'/';

									$push_link=true;
									break;

						case 'add_child':

							if(!isset($link['icon']))
								$link['icon']='plus';

								if(!empty($link['url'])){

									$link['url']='manager/add_element_child/'.$link['url']."/". $ref_table."/";

									$push_link=true;
								}

								break;

						default:

							break;
					}

					if($push_link)
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
		$field_list=array();
		$field_list_header=array();
		foreach ($ref_table_config['fields'] as $k => $v) {

			if( $v['on_list']=='show'){

				array_push($field_list, $k);
				array_push($field_list_header, $v['field_title']);

			}

		}

		$i=1;
		$list_to_display=array();


		foreach ($data['list'] as $key => $value) {
			$element_array=array();

			foreach ($field_list as $key_field=> $v_field) {
				if(isset($value[$v_field])){
					if(isset($dropoboxes[$v_field][$value[$v_field]]) ){
						$element_array[$v_field]=$dropoboxes[$v_field][$value[$v_field]];
					}else{
						$element_array[$v_field]=$value[$v_field];
					}


				}else{



					$element_array[$v_field]="";

					if(isset($ref_table_config['fields'][$v_field]['number_of_values']) AND $ref_table_config['fields'][$v_field]['number_of_values']!=1){
							
						if(isset($ref_table_config['fields'][$v_field]['input_select_values']) AND isset($ref_table_config['fields'][$v_field]['input_select_key_field']))
						{
							// récuperations des valeurs de cet element
							$M_values=$this->manager_lib->get_element_multi_values($ref_table_config['fields'][$v_field]['input_select_values'],$ref_table_config['fields'][$v_field]['input_select_key_field'],$data ['list'] [$key] [$table_id]);
							$S_values="";
							foreach ($M_values as $k_m => $v_m) {
								if(isset($dropoboxes[$v_field][$v_m]) ){
									$M_values[$k_m]=$dropoboxes[$v_field][$v_m];
								}
									
								$S_values.=empty($S_values)?$M_values[$k_m]:" | ".$M_values[$k_m];
							}

							$element_array[$v_field]=$S_values;
						}
							
					}




				}
					
					
					
					
			}

			/*
			 * Ajout des liens(links) sur la liste
			 */

			$action_button="";
			$arr_buttons=array();
			$view_link_url="";

			foreach ($list_links as $key_l => $value_l) {
					
				if(!empty($value_l['icon']))
					$value_l['label']= icon($value_l['icon']).' '.lng_min($value_l['label']);

					array_push($arr_buttons, array(
							'url'=> $value_l['url'].$value [$table_id],
							'label'=>$value_l['label'],
							'title'=>$value_l['title']

					)	);

					if($value_l['type']=='view')
						$view_link_url=$value_l['url'].$value [$table_id];
			}

			$action_button=create_button_link_dropdown($arr_buttons,lng_min('Action'));

			$element_array['links']=$action_button;
			if(isset($element_array['class_paper_id']) AND !empty($view_link_url)){
				$element_array['class_paper_id']=anchor($view_link_url,"<u><b>".$element_array['class_paper_id']."</b></u>",'title="'.lng_min('Display element').'")');
			}
			if(isset($element_array[$table_id])){
				$element_array[$table_id]=$i + $page;
			}
			array_push($list_to_display,$element_array);
			$i++;


		}


		$data ['list']=$list_to_display;
		//print_test($data); exit;

		/*
		 * Ajout de l'entête de la liste
		 */
		if(!empty($data['list'])){
			$array_header=$field_list_header;;
			if(trim($data['list'][$key]['links']) !=""){
				array_push($array_header,'');
			}

			if(!$dynamic_table){
				array_unshift($data['list'],$array_header);
			}else{
				$data['list_header']=$array_header;
			}



		}



		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */

		$data ['top_buttons']="";

		//This feature is not used for classification

		//if($add_link)
		//$data ['top_buttons'] .= get_top_button ( 'add', 'Add new', 'manager/add_element/'.$ref_table );


		$data ['top_buttons'] .= get_top_button ( 'close', 'Close', 'home' );



		/*
		 * Titre de la page
		 */

		$data['page_title']=$ref_table_config['reference_title'].$sup_title;

		if(activate_update_stored_procedure())
			$data ['top_buttons'] .= get_top_button ( 'all', 'Update stored procedure', 'home/update_stored_procedure/'.$ref_table,'Update stored procedure','fa-check','',' btn-dark ' );


			$data ['valeur']=($val=="_")?"":$val;

			if(!$dynamic_table AND  !empty($ref_table_config['search_by'])){
				$data ['search_view'] = 'general/search_view';}
					
				/*
				 * La vue qui va s'afficher
				 */
					
				if(!$dynamic_table){
					$data ['nav_pre_link'] = 'manage/list_classification/' .$list_type.'/' . $val . '/';
					$data ['nav_page_position'] = 6;


					$data['page']='general/list';
				}else{
					$data['page']='general/list_dt';
				}
				/*
				 * Chargement de la vue avec les données préparés dans le controleur
				 */
				$this->load->view('body',$data);
	}



	public function import_papers(){
		$headder=array('Row','Field1','Field2','Field3','Field4','Field5','Field6');
			
			
		$data ['page_title'] = lng('Import papers - CSV');
		//	$data ['top_buttons'] = get_top_button ( 'all', 'Import BibTeX', 'relis/manager/import_bibtext','Import BibTeX','fa-upload' );
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
		$data ['page'] = 'relis/import_papers_1';



		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}
	public function import_endnote(){

		$this->import_bibtext('endnote');
	}

	public function import_bibtext($format='bibtex'){
		$headder=array('Row','Field1','Field2','Field3','Field4','Field5','Field6');
			
		$data['import_format']=	$format;
		if(!empty($format) AND $format=='endnote'){
			$data ['page_title'] = lng('Import papers - Endnote');
		}else{
			$data ['page_title'] = lng('Import papers - BibTeX');
		}

		//$data ['top_buttons'] = get_top_button ( 'all', 'Import CSV', 'relis/manager/import_papers','Import CSV','fa-upload' );
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
		$data ['page'] = 'relis/import_bibtex_1';



		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}

	private function mres_escape($value)
	{
		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
		$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

		return str_replace($search, $replace, $value);
	}

	public function import_papers_save_bibtext(){
		$post_arr = $this->input->post ();


		//use save bibtext to get the right answer

		$data_array=json_decode($post_arr['data_array'],True);

		$papers_sources = (!empty($post_arr['papers_sources'])?$post_arr['papers_sources']:NULL);
		$search_strategy = (!empty($post_arr['search_strategy'])?$post_arr['search_strategy']:NULL);
		//	$paper_start_from = ((!empty($post_arr['paper_start_from']) AND is_numeric($post_arr['paper_start_from']))?$post_arr['paper_start_from']:2);

		$active_user=active_user_id();
		$added_active_phase=get_active_phase();
		$operation_code=active_user_id()."_".time();

		$default_key_prefix= get_appconfig_element('key_paper_prefix');
		$default_key_prefix=($default_key_prefix=='0')?'':$default_key_prefix;

		$default_key_serial= get_appconfig_element('key_paper_serial');
		$serial_key=$default_key_serial;

		//set classification status
		if(get_appconfig_element('screening_on')){

			$classification_status='Waiting';
			$screening_status='Pending';
		}else{
			$classification_status='To classify';

			$screening_status='Included';
		}

		//echo $classification_status;
		//exit;
		$i=1;
		$imported=0;
		$exist=0;
		foreach ($data_array as $key => $paper) {
			$paper['papers_sources']=$papers_sources;
			$paper['search_strategy']=$search_strategy;
			$paper['operation_code']=$operation_code;


			$res=$this->insert_paper_bibtext($paper);
			if($res=='1'){
				$imported ++;
			}else{
				$exist++;

			}


		}


		// update the operation tab
		$operation_arr=array('operation_code'=>$operation_code,
				'operation_type'=>'import_paper',
				'user_id'=>active_user_id(),
				'operation_desc'=>'Paper import before screening'

		);
		$res2 = $this->manage_mdl->add_operation($operation_arr);

		if(!empty($imported))
		{
			set_top_msg(" $imported papers imported successfully");
		}

		if(!empty($exist))
		{
			set_top_msg(" $exist papers already exist",'error');
		}
		redirect('home/screening');

	}

	public function import_papers_save_csv(){
		$post_arr = $this->input->post ();
		//print_test($post_arr); exit;
		$data_array=json_decode($post_arr['data_array']);

		$paper_title=$post_arr['paper_title'];
		$bibtexKey=$post_arr['bibtexKey'];
		$paper_link=$post_arr['paper_link'];
		$paper_abstract=$post_arr['paper_abstract'];
		$bibtex=$post_arr['bibtex'];
		$paper_key=$post_arr['paper_key'];
		$paper_author=$post_arr['paper_author'];
		$year=$post_arr['year'];

		//$paper_start_from=$post_arr['paper_start_from'];

		$papers_sources = (!empty($post_arr['papers_sources'])?$post_arr['papers_sources']:NULL);
		$search_strategy = (!empty($post_arr['search_strategy'])?$post_arr['search_strategy']:NULL);
		$paper_start_from = ((!empty($post_arr['paper_start_from']) AND is_numeric($post_arr['paper_start_from']))?$post_arr['paper_start_from']:2);

		$active_user=active_user_id();
		$added_active_phase=get_active_phase();
		$operation_code=active_user_id()."_".time();

		$default_key_prefix= get_appconfig_element('key_paper_prefix');
		$default_key_prefix=($default_key_prefix=='0')?'':$default_key_prefix;

		$default_key_serial= get_appconfig_element('key_paper_serial');
		$serial_key=$default_key_serial;

		//set classification status
		if(get_appconfig_element('screening_on')){

			$classification_status='Waiting';
			$screening_status='Pending';
		}else{
			$classification_status='To classify';

			$screening_status='Included';
		}

		//echo $classification_status;
		//exit;
		$i=1;
		$imported=0;
		foreach ($data_array as $key => $value) {
			if($key >= ($paper_start_from -1 )) {

				$value['zz']="";


				//$v_bibtex_key=!empty($value[$bibtexKey])?$this->mres_escape($value[$bibtexKey]):'paper_'.$i;

				if(!empty($value[$bibtexKey])){
					$v_bibtex_key=$this->mres_escape($value[$bibtexKey]);
				}else{
					$v_bibtex_key= $default_key_prefix.$serial_key;

					$serial_key++;
				}

				//	$v_bibtex_key=!empty($value[$bibtexKey])?$this->mres_escape($value[$bibtexKey]):$default_key_prefix.($default_key_serial+$i);

				$v_title=$this->mres_escape($value[$paper_title]);
				//$v_title=$value[$paper_title];
				$v_paper_link=$this->mres_escape($value[$paper_link]);

				$v_preview=!empty($value[$paper_author])?"<b>Authors:</b><br/>".$this->mres_escape($value[$paper_author])." <br/>":"";
				$v_preview.=!empty($value[$paper_key])?"<b>Key words:</b><br/>".$this->mres_escape($value[$paper_key])." <br/>":"";

				$v_abstract=$this->mres_escape($value[$paper_abstract]);
				$v_bibtex=$this->mres_escape($value[$bibtex]);
				$year=(!empty($value[$year]) AND is_numeric($value[$year] ))?$this->mres_escape($value[$year]):NULL;

				$sql="INSERT INTO `paper` (`bibtexKey`, `title`,  `preview`,`bibtex`, `abstract`, `doi`, `year`, `papers_sources`, `search_strategy`, `added_by`, `addition_mode`, `added_active_phase`,`operation_code`,`classification_status`)
				VALUES
				('$v_bibtex_key','$v_title','$v_preview','$v_bibtex','$v_abstract','$v_paper_link','$year','$papers_sources','$search_strategy',$active_user,'Automatic','$added_active_phase','$operation_code','$classification_status')";

				//echo "$sql <br/><br/>";
				$res_sql = $this->manage_mdl->run_query($sql,False,project_db());
				$imported++;
				//print_test($res_sql);
				$i++;
			}

		}

		if($serial_key!=$default_key_serial){
			set_appconfig_element('key_paper_serial',$serial_key);
		}

		// update the operation tab
		$operation_arr=array('operation_code'=>$operation_code,
				'operation_type'=>'import_paper',
				'user_id'=>active_user_id(),
				'operation_desc'=>'Paper import before screening'

		);
		$res2 = $this->manage_mdl->add_operation($operation_arr);

		if(!empty($imported))
		{
			set_top_msg(" $imported papers imported successfully");
		}

		//print_test($res2);
		redirect('home/screening');

	}

	//Load a bibtext file connect to bibler to get the JSON

	public function import_papers_load_bibtext(){
		$post_arr = $this->input->post ();

		$error_array=array();
		$success_array=array();
		$array_tab_preview=array();
		$array_tab_values=array();

		if(!empty($post_arr['from_endnote'])){
			$redirect="relis/manager/import_endnote";
		}else{
			$redirect="relis/manager/import_bibtext";
		}

		//exit;
		if(empty($_FILES["paper_file"]['tmp_name'])){
			echo set_top_msg(lng_min("No file selected") , 'error');

			redirect($redirect);

			exit;
		}
		if ($_FILES["paper_file"]["error"] > 0)
		{
			//echo "Error: " . $_FILES["file"]["error"] . "<br />";
			echo set_top_msg("Error: " . file_upload_error($_FILES["install_config"]["error"]) , 'error');
			array_push($error_array,"Error: " . file_upload_error($_FILES["install_config"]["error"]));
			redirect($redirect);

			exit;
		}
		else
		{
			$bibtextString=file_get_contents($_FILES["paper_file"]['tmp_name']);

			//Call bibler to convert into json and return then conert into array

			if(!empty($post_arr['from_endnote'])){
				$Tpapers=$this->get_bibler_result($bibtextString,"endnote");
			}else{
				$Tpapers=$this->get_bibler_result($bibtextString,"multi_bibtex");
			}


			//		vv



			$data['json_values']=$json_papers=json_encode($Tpapers['paper_array']);;

			// convert json into array
			/////$T_papers=json_decode($bibtextString);
			//z
			$data['uploaded_papers']=$Tpapers['paper_preview_sucess'];
			
			$data['uploaded_papers_exist']=$Tpapers['paper_preview_exist'];
			
			//add papers duplicated
			//print_test($Tpapers);
			//print_test($data['uploaded_papers']);
			
			
			$data['uploaded_papers_error']=$Tpapers['paper_preview_error'];

			$data['number_of_papers']=count($Tpapers['paper_array']);



		}

			

		if(get_appconfig_element('source_papers_on')){

			$data['source_papers']= $this->manager_lib->get_reference_select_values('papers_sources;ref_value',True,False);
			//print_test($data['source_papers']);
		}


		if(get_appconfig_element('search_strategy_on')){
			$data['search_strategy']= $this->manager_lib->get_reference_select_values('search_strategy;ref_value',True,False);
			//print_test($data['search_strategy']);
		}

		$data ['page_title'] = lng('Import papers - BibTeX');
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
		$data ['page'] = 'relis/import_bibtext_2';



		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}



	public function import_papers_load_csv(){

		$error_array=array();
		$success_array=array();
		$array_tab_preview=array();
		$array_tab_values=array();
		//print_test($_FILES["paper_file"]);
		if(empty($_FILES["paper_file"]['tmp_name'])){
			echo set_top_msg(lng_min("No file selected") , 'error');

			redirect('relis/manager/import_papers');

			exit;
		}
		if ($_FILES["paper_file"]["error"] > 0)
		{
			//echo "Error: " . $_FILES["file"]["error"] . "<br />";

			array_push($error_array,"Error: " . file_upload_error($_FILES["install_config"]["error"]));
		}
		elseif ($_FILES["paper_file"]["type"] !== "application/vnd.ms-excel")
		{
			//echo "File must be a .php";
			array_push($error_array,"File must be a csv file");
		}
		else
		{
			ini_set('auto_detect_line_endings',TRUE);
			$fp = fopen($_FILES['paper_file']['tmp_name'], 'rb');
			$i=1;
			$last_count=0;
			//	while ( (($line = utf8_encode(fgets($fp))) !== false) AND $i<5) {
			while ( (($Tline = (fgetcsv($fp,0,get_appconfig_element('csv_field_separator'),get_ci_config("csv_string_dellimitter")))) !== false) AND $i<11) {
				$Tline = array_map( "utf8_encode", $Tline );



				if($last_count < count($Tline)){
					$last_count=count($Tline);
				}
				$i++;
			}


			$array_header=array();
			$array_header_opt=array('zz'=>"No field selected");
			for ($j = 1; $j <= $last_count; $j++) {
				array_push($array_header, 'Field '.$j)	;
				array_push($array_header_opt, 'Field '.$j)	;
					
			}

			//print_test($array_header);
			array_push($array_tab_preview,$array_header);
			$i=1;
			rewind($fp);
			//while ( (($line = fgets($fp)) !== false)) {
			while ( (($Tline = (fgetcsv($fp,0,get_appconfig_element('csv_field_separator'),get_ci_config("csv_string_dellimitter")))) !== false)) {
				$Tline = array_map( "utf8_encode", $Tline );

				if($i<11){
					array_push($array_tab_preview,$Tline);
				}
				array_push($array_tab_values,$Tline);

				$i++;
			}
			//print_test($array_tab_values);

			$data['json_values']=json_encode($array_tab_values);
		}






			
		$csv_papers=array();
			
			
		$data['csv_papers']=$array_tab_preview;
		if(!empty($array_header)){
			$data['csv_fields']=$array_header;
			$data['csv_fields_opt']=$array_header_opt;

		}else{
			$data['csv_fields']=array();
			$data['csv_fields_opt']=array();

		}

		$data['paper_config_fields']=array(
				'paper_title'=>array('title'=>"Paper title ", "mandatory"=>TRUE),
				'bibtexKey'=>array('title'=>"Paper key <i style='font-size:0.8em'>(If not available It will be generated)</i>", "mandatory"=>False),
				'paper_link'=>array('title'=>"Link", "mandatory"=>False),
				'year'=>array('title'=>"Year", "mandatory"=>False),
				'paper_abstract'=>array('title'=>"Abstract", "mandatory"=>False),
				'bibtex'=>array('title'=>"Bibtex", "mandatory"=>False),
				'paper_key'=>array('title'=>"Key words", "mandatory"=>False),
				'paper_author'=>array('title'=>"Authors", "mandatory"=>False)
		);
			
		if(get_appconfig_element('source_papers_on')){

			$data['source_papers']= $this->manager_lib->get_reference_select_values('papers_sources;ref_value',True,False);
			//print_test($data['source_papers']);
		}


		if(get_appconfig_element('search_strategy_on')){
			$data['search_strategy']= $this->manager_lib->get_reference_select_values('search_strategy;ref_value',True,False);
			//print_test($data['search_strategy']);
		}

		$data ['page_title'] = lng('Import papers - match fields');
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
		$data ['page'] = 'relis/import_papers_2';



		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}



	/*
	 * Affichage des résultat(statistique)  en cours de réaliation------
	 */
	public function result_graph(){




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
		$table_config = get_table_configuration('classification');



		$result_fin=array();

		foreach ($table_config['fields'] as $key_conf => $value_conf) {
			if(!(!empty($value_conf['compute_result']) AND $value_conf['compute_result'] =='no')){

				if(isset($value_conf['number_of_values']) AND ($value_conf['number_of_values']=='1') AND ($value_conf['input_type'] =='select') AND ($value_conf['input_select_source'] =='table' OR $value_conf['input_select_source'] =='array'  OR $value_conf['input_select_source'] =='yes_no' )  ){
					$ref_field=$key_conf;
					if($value_conf['input_select_source'] =='array'){
						$result= $this->manage_mdl->get_result_classification($key_conf);
						foreach ($result as $key => $value) {
							$result[$key]['field_desc'] =$value['field'] ;
						}

					}elseif($value_conf['input_select_source'] =='yes_no'){

						$result= $this->manage_mdl->get_result_classification($key_conf);

						$yes_no=array("No",'Yes');
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



				}
			}

		}
			

		//print_test($result_fin);
			
		/*
		 * La page contient des graphique cette valeur permettra le chargement de la librarie highcharts
		 */
		$data['has_graph']='yes';
			
			
		$data['result_table']=$result_fin;
		$data['page']='relis/result_graph';
		$this->load->view('body',$data);

	}


	public function result_export($type=1){

		$data['t_type']=$type;
			
		$data ['page_title'] = lng('Exports');
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'home' );
		$data['left_menu_perspective']='z_left_menu_screening';
		$data['project_perspective']='screening';
		$data ['page'] = 'relis/result_export';



		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}

	public function download($file_name){
		$url=base_url()."cside/export_r/".$file_name;
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"".$file_name."\"");
		echo readfile($url);

		//header("Location: $url");
	}

	public function result_export_papers(){

		$table_ref="papers";
		$this->db2 = $this->load->database(project_db(), TRUE);
		$sql="SELECT id,bibtexKey,title,doi,preview,abstract,year
				FROM  paper WHERE paper_active =1";
		$data=$this->db2->query ( $sql );
		$result=$data->result_array();


		$array_header=array('#',"key",'Title','Link','Preview','Abstract','Year');

		array_unshift($result, $array_header);



		// Create a stream opening it with read / write mode
		$stream = fopen('data://text/plain,' . "", 'w+');

		// Iterate over the data, writting each line to the text stream
		$f_new = fopen("cside/export_r/relis_paper_".project_db().".csv", 'w+');
		$i=0;
		foreach ($result as $val) {
			if($i > 0){
				$val['id']=$i;
			}
			fputcsv($f_new, $val,get_appconfig_element('csv_field_separator_export'));
			$i++;
		}

		fclose($f_new);

		set_top_msg(lng_min('File generated'));

		redirect('relis/manager/result_export');

	}
	public function result_export_included_papers_bib(){
		$this->result_export_papers_bib('Included');
	}

	public function result_export_excluded_papers_bib(){
		$this->result_export_papers_bib('Excluded');
	}

	public function result_export_papers_bib($status=""){
		//get classification

		$table_ref="papers";
		$this->db2 = $this->load->database(project_db(), TRUE);
		$extra_sql="";
		if(!empty($status)){


			if($status=='Excluded'){
				$extra_sql=" AND ( screening_status  = '$status' OR  paper_excluded = 1 )";
			}else{
				$extra_sql=" AND screening_status  = '$status' AND paper_excluded=0 ";
			}

			$filename="cside/export_r/relis_paper_bibtex_".$status.'_'.project_db().".bib";
		}else{
			$filename="cside/export_r/relis_paper_bibtex_".project_db().".bib";

			//$extra_sql=" AND paper_excluded=0 ";
		}
		$sql="SELECT id,bibtexKey,title,doi,preview,abstract,year,bibtex FROM  paper
		WHERE paper_active =1 $extra_sql ";
		//echo $sql; exit;
		$data=$this->db2->query ( $sql );
		//	mysqli_next_result( $this->db2->conn_id );
		$result=$data->result_array();
		//print_test($result);


		//$array_header=array('#',"key",'Title','Link','Preview','Abstract','Year','Bibtex');

		//array_unshift($result, $array_header);



		// Create a stream opening it with read / write mode
		$stream = fopen('data://text/plain,' . "", 'w+');

		// Iterate over the data, writting each line to the text stream
		$f_new = fopen($filename, 'w+');
		foreach ($result as $val) {
			//print_test($val);
			if(!empty($val['bibtex'])){
				fputs($f_new, $val['bibtex']."\n");

			}
		}

		// Rewind the stream
		//rewind($stream);

		// You can now echo it's content
		//echo stream_get_contents($stream);

		// Close the stream
		fclose($f_new);

		set_top_msg(lng_min('File generated'));

		redirect('relis/manager/result_export');

	}
	
	
	
	
	public function result_export_excluded_class(){
	
	
		$this->db2 = $this->load->database(project_db(), TRUE);
		$extra_sql="";
		
		$users= $this->manager_lib->get_reference_select_values('users;user_name');
		
		$sql="SELECT id,bibtexKey,title,preview,P.screening_status ,S.exclusion_by as user_id,S.exclusion_criteria,T.ref_value as criteria, S.exclusion_note
		FROM  paper P
		INNER JOIN exclusion S ON (P.id = S.exclusion_paper_id AND S.exclusion_active=1 )
		LEFT JOIN  ref_exclusioncrieria T ON ( S.exclusion_criteria = T.ref_id)
		WHERE paper_active =1 AND P.paper_excluded = 1  ORDER BY title ";
		
		
		//echo $sql; exit;
		$data=$this->db2->query ( $sql );
		//	mysqli_next_result( $this->db2->conn_id );
		$result=$data->result_array();
		
		
		
		
		$papers = array();
		$i=1;
		foreach ($result as $key => $value) {
			$user=!empty($users[$value['user_id']])?$users[$value['user_id']]:$value['user_id'];
			if(empty ($papers[$value['id']])){
				$papers[$value['id']] = array(
						'num'=>$i,
						'bibtexKey'=>$value['bibtexKey'],
						'title'=>$value['title'],
						'preview'=>$value['preview'],
						'user'=>$user,
						'criteria'=>$value['criteria'],
						'exclusion_note'=>$value['exclusion_note'],
				);
				$i++;
			}
		}
		
		$array_header=array('#',"key",'Title','Preview','Excluded By','Criteria','Exclusion note');
		
		array_unshift($papers, $array_header);
		
		$filename="cside/export_r/relis_paper_excluded_class_".project_db().".csv";
		
		//print_test($papers);
		// Create a stream opening it with read / write mode
		$stream = fopen('data://text/plain,' . "", 'w+');
		
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen($filename, 'w+');
		foreach ($papers as $val) {
		
			fputcsv($f_new, $val,get_appconfig_element('csv_field_separator_export'));
		
		}
		
		// Close the stream
		fclose($f_new);
		
		set_top_msg(lng_min('File generated'));
		
		redirect('relis/manager/result_export');
	
	}
	
	public function result_export_excluded_screen(){
	
	
		$this->db2 = $this->load->database(project_db(), TRUE);
		$extra_sql="";
		
		$users= $this->manager_lib->get_reference_select_values('users;user_name');
		
		$sql="SELECT id,bibtexKey,title,preview,P.screening_status ,S.user_id,S.exclusion_criteria,T.ref_value as criteria,S.screening_note
		FROM  paper P
		INNER JOIN screening_paper S ON (P.id = S.paper_id AND S.screening_active=1 )
		LEFT JOIN  ref_exclusioncrieria T ON ( S.exclusion_criteria = T.ref_id)
		WHERE paper_active =1 AND P.screening_status = 'Excluded'  ORDER BY title ";
		
		
		//echo $sql; exit;
		$data=$this->db2->query ( $sql );
		//	mysqli_next_result( $this->db2->conn_id );
		$result=$data->result_array();
		
		$papers = array();
		$i=1;
		foreach ($result as $key => $value) {
			$user=!empty($users[$value['user_id']])?$users[$value['user_id']]:$value['user_id'];
			if(empty ($papers[$value['id']])){
				$papers[$value['id']] = array(
						'num'=>$i,
						'bibtexKey'=>$value['bibtexKey'],
						'title'=>$value['title'],
						'preview'=>$value['preview'],
				);
				$i++;
			}
				$papers[$value['id']]['user_' . $value['user_id']]=$user;
				$papers[$value['id']]['criteria_' . $value['user_id']]=$value['criteria'];
				$papers[$value['id']]['screening_note' . $value['user_id']]=$value['screening_note'];
			
			
		}
		
	
		$array_header=array('#',"key",'Title','Preview','Excluded By / Criteria / Note');
	
		array_unshift($papers, $array_header);
	
		$filename="cside/export_r/relis_paper_excluded_screen_".project_db().".csv";
		
		//print_test($papers);
		// Create a stream opening it with read / write mode
		$stream = fopen('data://text/plain,' . "", 'w+');
	
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen($filename, 'w+');
		foreach ($papers as $val) {
			
				fputcsv($f_new, $val,get_appconfig_element('csv_field_separator_export'));
				
		}
	
		// Close the stream
		fclose($f_new);
	
		set_top_msg(lng_min('File generated'));
	
		redirect('relis/manager/result_export');
	
	}


	public function result_export_classification(){
		//get classification

		$table_ref="classification";
		$ref_table_config=get_table_config($table_ref);
		$table_id=$ref_table_config['table_id'];
		//$this->db2 = $this->load->database(project_db(), TRUE);
		//$data=$this->db2->query ( "CALL get_list_".$table_ref."(0,0,'') " );
		//mysqli_next_result( $this->db2->conn_id );
		//$result=$data->result_array();
		//print_test($result);
		echo $table_ref;
		$data=$this->DBConnection_mdl->get_list($ref_table_config,'_',0,-1,'');

		//print_test($data);
		//exit;


		$dropoboxes=array();
		foreach ($ref_table_config['fields'] as $k => $v) {

			if(!empty($v['input_type']) AND $v['input_type']=='select' AND $v['on_list']=='show'){
				if($v['input_select_source']=='array'){
					$dropoboxes[$k]=$v['input_select_values'];
				}elseif($v['input_select_source']=='table'){
					$dropoboxes[$k]= $this->manager_lib->get_reference_select_values($v['input_select_values']);
				}elseif($v['input_select_source']=='yes_no'){
					$dropoboxes[$k]=array('0'=>"No",
							'1'=>"Yes"
					);
				}
			}
			;
		}



		/*
		 * Préparation de la liste à afficher sur base du contenu et  stucture de la table
		 */

		/**
		 * @var array $field_list va contenir les champs à afficher
		 */
		$field_list=array();
		$field_list_header=array();
		foreach ($ref_table_config['fields'] as $k => $v) {

			if( $v['on_list']=='show'){

				array_push($field_list, $k);
				array_push($field_list_header, $v['field_title']);

			}

		}
		//prepare paper info 
		$this->db2 = $this->load->database(project_db(), TRUE);
		$sql = "select id,bibtexKey,title, P.year as paper_year,GROUP_CONCAT(DISTINCT A.author_name SEPARATOR ' | ') as authors ,V.venue_fullName,
				S.ref_value as papers_sources ,T.ref_value as search_strategy ,
				GROUP_CONCAT(DISTINCT  G.assigned_user_id SEPARATOR '|') as reviewers
				FROM paper P
				JOIN classification C ON (C.class_paper_id=P.id AND C.class_active = 1 ) 
				LEFT JOIN assigned G ON (G.assigned_paper_id =P.id AND  G.assigned_active =1 )
				LEFT JOIN ref_papers_sources S ON (S.ref_id	 =P.papers_sources AND  S.ref_active =1 )
				LEFT JOIN  ref_search_strategy T ON (T.ref_id	 =P.search_strategy AND  T.ref_active =1 )
				LEFT JOIN venue V ON (V.venue_id =P.venueId AND  venue_active =1 )
				LEFT JOIN paperauthor ON (paperauthor.paperId =P.id AND  paperauthor_active =1 )
				LEFT JOIN author A ON (paperauthor.authorId =A.author_id AND  	author_active =1 )
				WHERE P.paper_active=1
				GROUP BY P.id ";
		$paper_data=$this->db2->query ( $sql );
		
		//rearange
		$users= $this->manager_lib->get_reference_select_values('users;user_name');
		$paper_res=$paper_data->result_array();
		$arrangedPapers=array();
		foreach ($paper_res as $key => $value_p) {
			$user_names="";
			if(!empty($value_p['reviewers'])){
				foreach (explode('|',$value_p['reviewers']) as $k =>  $p_user_id) {
					if($k==0){
						$user_names.=!empty($users[$p_user_id])? $users[$p_user_id]:'';
					}else{
					$user_names.=!empty($users[$p_user_id])? ' | '.$users[$p_user_id]:'';
					}
				}
			}
			$arrangedPapers[$value_p['id']]=$value_p;
			$arrangedPapers[$value_p['id']]['reviewers']=$user_names ;
		}
		
		$i=1;
		$list_to_display=array();
		foreach ($data['list'] as $key => $value) {
			$element_array=array();
			$element_array['nbr']=$i;
			$element_array['bibtexKey']=!empty($arrangedPapers[$value['class_paper_id']])? $arrangedPapers[$value['class_paper_id']]['bibtexKey']:'';
			$element_array['title']=!empty($arrangedPapers[$value['class_paper_id']])? $arrangedPapers[$value['class_paper_id']]['title']:'';
			$element_array['paper_year']=!empty($arrangedPapers[$value['class_paper_id']])? $arrangedPapers[$value['class_paper_id']]['paper_year']:'';
			$element_array['authors']=!empty($arrangedPapers[$value['class_paper_id']])? $arrangedPapers[$value['class_paper_id']]['authors']:'';
			$element_array['venue_fullName']=!empty($arrangedPapers[$value['class_paper_id']])? $arrangedPapers[$value['class_paper_id']]['venue_fullName']:'';
			$element_array['papers_sources']=!empty($arrangedPapers[$value['class_paper_id']])? $arrangedPapers[$value['class_paper_id']]['papers_sources']:'';
			$element_array['search_strategy']=!empty($arrangedPapers[$value['class_paper_id']])? $arrangedPapers[$value['class_paper_id']]['search_strategy']:'';
			$element_array['reviewers']=!empty($arrangedPapers[$value['class_paper_id']])? $arrangedPapers[$value['class_paper_id']]['reviewers']:'';
			
			foreach ($field_list as $key_field=> $v_field) {
				if(isset($value[$v_field])){
					if(isset($dropoboxes[$v_field][$value[$v_field]]) ){
						$element_array[$v_field]=$dropoboxes[$v_field][$value[$v_field]];
					}else{
						$element_array[$v_field]=$value[$v_field];
					}


				}else{



					$element_array[$v_field]="";

					if(isset($ref_table_config['fields'][$v_field]['number_of_values']) AND $ref_table_config['fields'][$v_field]['number_of_values']!=1){
							
						if(isset($ref_table_config['fields'][$v_field]['input_select_values']) AND isset($ref_table_config['fields'][$v_field]['input_select_key_field']))
						{
							// récuperations des valeurs de cet element
							$M_values=$this->manager_lib->get_element_multi_values($ref_table_config['fields'][$v_field]['input_select_values'],$ref_table_config['fields'][$v_field]['input_select_key_field'],$data ['list'] [$key] [$table_id]);
							$S_values="";
							foreach ($M_values as $k_m => $v_m) {
								if(isset($dropoboxes[$v_field][$v_m]) ){
									$M_values[$k_m]=$dropoboxes[$v_field][$v_m];
								}
									
								$S_values.=empty($S_values)?$M_values[$k_m]:" | ".$M_values[$k_m];
							}

							$element_array[$v_field]=$S_values;
						}
							
					}




				}
					
					
					
					
			}


			

			if(isset($element_array['class_id'])){
				unset($element_array['class_id']);
			}
			
			if(isset($element_array['class_paper_id'])){
				unset($element_array['class_paper_id']);
			}
			array_push($list_to_display,$element_array);
			$i++;


		}


		//!!!!!!!!!!!!!!!!!!! this is like a hardcode it doesnt follow anny pathern 

		unset($field_list_header[0]);
		unset($field_list_header[1]);
		$other_fields=array('nbr','Key','Title','Publication year','Author/s','Venue','Source','Search Type','Reviewer/s');
		$field_list_header = array_merge($other_fields,$field_list_header);
		

		
		/*
		 * Ajout de l'entête de la liste
		 */
		if(!empty($data['list'])){

			array_unshift($list_to_display,$field_list_header);

		}
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen("cside/export_r/relis_classification_".project_db().".csv", 'w+');
		foreach ($list_to_display as $val) {
			fputcsv($f_new, $val,get_appconfig_element('csv_field_separator_export'));
		}


		fclose($f_new);

		set_top_msg(lng_min('File generated'));
		redirect('relis/manager/result_export');

	}
	
	


	public function pre_assignment_screen($data=""){
		$data['screening_phases']= $this->manager_lib->get_reference_select_values('screen_phase;phase_title',True,False);

		$source_papers= $data['screening_phases'];
		$source_papers['']="All papers";
		$data['source_papers']=$source_papers;

		$data ['page_title'] = lng('Assign papers for screening (Step 1)');
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );

		$data ['page'] = 'relis/pre_assign_papers_screen_auto';

		//	print_test($data);

		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}

	public function assignment_screen($data=""){
		if(! active_screening_phase())
		{	redirect('home');
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


		$screening_phase_info=active_screening_phase_info();
		$creening_phase_id=active_screening_phase();

		$data['screening_phase']=$creening_phase_id;
		//$screening phases
		$screening_phases = $this->db_current->order_by('screen_phase_order', 'ASC')
		->get_where('screen_phase', array('screen_phase_active'=>1))
		->result_array();
		//$creening_phase_id=8;

		$previous_phase=0;
		$previous_phase_title="";

		foreach ($screening_phases as $k => $phase) {

			if($phase['screen_phase_id']==$creening_phase_id)	{
				break;
			}elseif($phase['phase_type']!='Validation'){
				$previous_phase=$phase['screen_phase_id'];
				$previous_phase_title=$phase['phase_title'];
			}

		}


		if($previous_phase == 0){
			$paper_source='all';
			$paper_source_status='all';
			$previous_phase_title=" ";
		}else{
			$paper_source=$previous_phase;
			$paper_source_status=$screening_phase_info['source_paper_status'];
			$previous_phase_title = " from $previous_phase_title";
		}

		$append_title="( $paper_source_status papers  $previous_phase_title )";

		//print_test($previous_phase);
		//print_test($screening_phase_info);
		//print_test($screening_phases);



		$data['papers_sources']=$paper_source;
		$data['paper_source_status']=$paper_source_status;
		$data['screening_phase']=$creening_phase_id;

		$papers=$this->get_papers_to_screen($paper_source,$paper_source_status,'','Screening');

		$data['paper_source']=$paper_source;
			
		$paper_list[0]=array('Key','Title');
			
		foreach ($papers['to_assign'] as $key => $value) {
			$paper_list[$key+1]=array($value['bibtexKey'],$value['title']);
		}
			
		$data['paper_list']=$paper_list;
		$user_table_config=get_table_configuration('users');
			
		$users=$this->DBConnection_mdl->get_list($user_table_config,'_',0,-1);
		$_assign_user=array();
		foreach ($users['list'] as $key => $value) {

			if( (user_project($this->session->userdata('project_id') ,$value['user_id'])) ){
					
				$_assign_user[$value['user_id']]=$value['user_name'];
			}
		}

		$data['users']=$_assign_user;
		$data['number_papers']=count($papers['to_assign']);
		$data['number_papers_assigned']=count($papers['assigned']);
		$data['reviews_per_paper']=get_appconfig_element('screening_reviewer_number');
			
			
			
		$data ['page_title'] = lng('Assign papers for screening '.$append_title);
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
		//$data['left_menu_perspective']='z_left_menu_screening';
		//$data['project_perspective']='screening';
		$data ['page'] = 'relis/assign_papers_screen_auto';

		//	print_test($data);

		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );

	}

	function get_papers_to_screen($source='all',$source_status='all',$current_phase="",$assignment_role=""){
		//$source_status="Included";
		//$source='1';
		if(empty($current_phase)){
			$current_phase=active_screening_phase();
		}
		if($source=='all'){
			//rechercher dans papers
			//$papers=$this->DBConnection_mdl->get_papers('screen','papers','_',0,-1);
			$condition ="";
			if($source_status != 'all'){

				$condition=" AND screening_status = '$source_status'";
			}
			$sql="SELECT P.*,screening_status as paper_status from paper P where paper_active = 1 $condition ";
		}else{


			$condition ="";

			if($source_status != 'all'){
					
				$condition=" AND S.screening_decision = '$source_status'";
			}



			$sql="SELECT decison_id,screening_decision as paper_status,P.* from screen_decison S
			LEFT JOIN paper P ON(S.paper_id=P.id  )
			WHERE screening_phase='$source'	AND  decision_active=1 AND P.paper_active=1 $condition
			";

			//rechercher dans screen et la decision dans screen decision
		}

		$all_papers=$this->db_current->query($sql)->result_array();

		$result['all_papers']=$all_papers;

		// get papers already assigned in current phase
		$condition="";
		if(!empty($assignment_role)){

			$condition=" AND assignment_role = '$assignment_role'";
		}

		$sql="Select DISTINCT (paper_id) from screening_paper WHERE screening_active =1 AND screening_phase = $current_phase  $condition GROUP BY paper_id";
		$paper_assigned=$this->db_current->query($sql)->result_array();


		//	$result['paper_assigned']=$paper_assigned;
		$det_paper_to_assign=array();
		$det_paper_assigned=array();
		if(empty($paper_assigned))//no paper already assigned'
		{
			$det_paper_to_assign=$all_papers;
		}else{
			foreach ($all_papers as $key_all => $paper_all) {
				$found=False;
				foreach ($paper_assigned as $key_assigned => $value_assigned) {
					if($paper_all['id']==$value_assigned['paper_id']){
						$found=True;
						array_push($det_paper_assigned, $paper_all);
						break;
					}

				}
				if(!$found){
					array_push($det_paper_to_assign, $paper_all);
				}
			}

		}

		$result['assigned']=$det_paper_assigned;

		$result['to_assign']=$det_paper_to_assign;

		return $result;
	}


	function save_assignment_screen(){

		$post_arr = $this->input->post ();
		//	print_test($post_arr); exit;
		$users=array();
		$i=1;
		if(empty( $post_arr['reviews_per_paper'] )){

			$data['err_msg'] = lng(' Please provide  "Reviews per paper" ');
			$data['screening_phase'] = empty( $post_arr['screening_phase'] )?"":$post_arr['screening_phase'];
			$data['papers_sources'] = empty( $post_arr['papers_sources'] )?"":$post_arr['papers_sources'];

			$this->assignment_screen($data);

		}else{

			// Get selected users
			While ($i <= $post_arr['number_of_users']) {
				if(!empty( $post_arr['user_'.$i])){
					array_push($users,$post_arr['user_'.$i]);
				}
				$i++;
			}

			//Verify if selected users is > of required reviews per paper
			if(count($users) < $post_arr['reviews_per_paper']){

				$data['err_msg'] = lng('The Reviews per paper cannot exceed the number of selected users  ');
				$data['screening_phase'] = empty( $post_arr['screening_phase'] )?"":$post_arr['screening_phase'];
				$data['papers_sources'] = empty( $post_arr['papers_sources'] )?"":$post_arr['papers_sources'];
				$this->assignment_screen($data);

			}else{
				$currect_screening_phase=$post_arr['screening_phase'];
				$papers_sources=$post_arr['papers_sources'];
				$paper_source_status=$post_arr['paper_source_status'];

				$reviews_per_paper=$post_arr['reviews_per_paper'];

				//Get all papers

				//	$papers=$this->get_papers_to_screen($papers_sources);
				$papers=$this->get_papers_to_screen($papers_sources,$paper_source_status);
				//	print_test($papers); exit;
				$assign_papers= array();
				$this->db2 = $this->load->database(project_db(), TRUE);
				$operation_code=active_user_id()."_".time();
				foreach ($papers['to_assign'] as $key => $value) {

					$assign_papers[$key]['paper']=$value['id'];

					$assign_papers[$key]['users']=array();

					$assignment_save=array(
							'paper_id'=>$value['id'],
							'user_id'=>'',
							'assignment_note'=>'',
							'assignment_type'=>'Normal',
							'operation_code'=>$operation_code,
							'assignment_mode'=>'auto',
							'screening_phase'=>$currect_screening_phase,
							'assigned_by'=>$this->session->userdata ( 'user_id' )

					);
					$j=1;

					//the table to save assignments

					$table_name=get_table_configuration('screening','current','table_name');
					//print_test($table_name);
					while($j<=$reviews_per_paper){


							
						$temp_user=($key % count($users)) + $j;
							
						if($temp_user >= count($users) )
							$temp_user = $temp_user - count($users);

							array_push($assign_papers[$key]['users'], $users[$temp_user]);

							$assignment_save['user_id']=$users[$temp_user];
							//print_test($assignment_save);


							$this->db2->insert($table_name,$assignment_save);


							$j++;
					}


				}

				$operation_arr=array('operation_code'=>$operation_code,
						'operation_type'=>'assign_papers',
						'user_id'=>active_user_id(),
						'operation_desc'=>'Assign papers for screening'

				);
				$res2 = $this->manage_mdl->add_operation($operation_arr);


				set_top_msg('Assignement done');
				redirect('home/screening');
				//	print_test($assign_papers);
				//echo count($assign_papers);
			}
		}
	}
	function edit_screen($screen_id,$operation_type='edit_screen'){

		$data ['content_item'] = $this->DBConnection_mdl->get_row_details('get_detail_screen',$screen_id,True);
		$data ['operation_source'] =$operation_type;
		//print_test($data); exit;
		$this->screen_paper ($operation_type,$data );

	}

	function screen_paper_validation(){
		$this->screen_paper('screen_validation');

	}

	function screen_paper($screen_type='simple_screen',$data=array()){

		$op=check_operation($screen_type,'Edit');
		$ref_table=$op['tab_ref'];
		$ref_table_operation=$op['operation_id'];
		$table_config=get_table_configuration($ref_table);

		//print_test($table_config);

		$data['screen_type']=$screen_type;

		//Get screening criteria
		$exclusion_crit=$this->manager_lib->get_reference_select_values('exclusioncrieria;ref_value');

		$data['exclusion_criteria']=$exclusion_crit;

		if(!empty($data['content_item'])){
			//edit screening: used for conflict resolution
			$data['the_paper']=$data['content_item']['paper_id'] ;
			$data['screening_id']=$data['content_item']['screening_id'];
			$data['assignment_id']=$data['content_item']['screening_id'];
			$data['assignment_note']=$data['content_item']['assignment_note'];
			$data['screening_phase']=$data['content_item']['screening_phase'];
			$page_title="Update screening";
			$data['operation_type']='edit';
		}else{


			$my_assignations=$this->Relis_mdl->get_user_assigned_papers(active_user_id(),$screen_type,active_screening_phase());
			//print_test($my_assignations);
			$paper_to_screen=0;
			$screening_id=0;
			$total_papers=count($my_assignations);



			if($total_papers<1){
				$page_title=($screen_type=='screen_validation')?"No papers assigned to you for validation":"No papers assigned to you for screening";

			}else{
				$papers_screened=0;
				foreach ($my_assignations as $key => $value) {

					if($value['screening_status']=='Done'){
						$papers_screened++;
					}else{
						if(empty($paper_to_screen)){
							$paper_to_screen=$value['paper_id'];
							$screening_id=$value['screening_id'];
							$assignment_note=$value['assignment_note'];
						}
					}
				}
					
				if(empty($paper_to_screen)){//all papers have been screened
					$page_title=($screen_type=='screen_validation')?"Validation - All papers have been screened":"All papers have been screened";

					//	$page_title="All papers have been screened";

				}else{
					//$page_title=($screen_type=='screen_validation')?"Screening validation":"Screening";


					$screening_detail= $this->DBConnection_mdl->get_row_details ( 'get_detail_screen',$screening_id ,TRUE);

					$data['screening_phase']=$screening_detail['screening_phase'];
					$data['the_paper']=$paper_to_screen;
					$data['assignment_id']=$screening_id;
					$data['screening_id']=$screening_id;
					$data['assignment_note']=!empty($assignment_note)?$assignment_note:"";


					$data['operation_type']='new';


				}
					
				$data['screen_completion']=(int)($papers_screened *100 / $total_papers);
				$data['paper_screened']=$papers_screened;
				$data['all_papers']= $total_papers;
					
					
					
					
			}

		}

		$screening_phase_info=active_screening_phase_info();
		$displayed_fieds=explode('|', $screening_phase_info['displayed_fields']);
		//print_test($screening_phase_info);
		//print_test($fieds);
		$data['screening_phase_info']=$screening_phase_info;
		if(!empty($data['the_paper'])){

			$paper_detail= $this->DBConnection_mdl->get_row_details ( 'papers',$data['the_paper'] );
			$data['paper_title']=$paper_detail['bibtexKey']." - ".$paper_detail['title'];

			if(in_array('Abstract', $displayed_fieds))
				$data['paper_abstract']=$paper_detail['abstract'];
					
				if(in_array('Bibtex', $displayed_fieds))
					$data['paper_bibtex']=$paper_detail['bibtex'];

					if(in_array('Link', $displayed_fieds))
						$data['paper_link']=$paper_detail['doi'];
							
						if(in_array('Preview', $displayed_fieds))
							$data['paper_preview']=$paper_detail['preview'];
		}

			
			
		if(isset($table_config['operations'][$ref_table_operation]['page_title'] )){
			if(!empty($page_title)){
				$data['page_title']=$page_title ." - ".$screening_phase_info['phase_title'];
			}else{
				$data['page_title']=lng($table_config['operations'][$ref_table_operation]['page_title'])." - ".$screening_phase_info['phase_title'];
			}

		}else{
			$data ['page_title'] = lng('Screening');
		}
			
			
			
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'h_screening' );
			
		$data ['page'] = 'relis/screen_paper';

		if(!empty($table_config['operations'][$ref_table_operation]['page_template'] )){

			$data['page']=$table_config['operations'][$ref_table_operation]['page_template'];
		}
			
			
		//setting the page of redirection after saving
		if(!empty($table_config['operations'][$ref_table_operation]['redirect_after_save'])){
			$after_save_redirect=$table_config['operations'][$ref_table_operation]['redirect_after_save'];
			if(!empty($data['screening_id'])){
				$after_save_redirect=str_replace('~current_element~', $data['screening_id'], $after_save_redirect);
			}
			if(!empty($data['the_paper'])){
				$after_save_redirect=str_replace('~current_paper~', $data['the_paper'], $after_save_redirect);
			}
		}else{
			$after_save_redirect="home";
		}

		$this->session->set_userdata('after_save_redirect',$after_save_redirect);
			
			
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );


	}

	public function save_screening(){

		$post_arr = $this->input->post ();
		$decision_source='new_screen';
		if($post_arr['screen_type']=='edit_screen')
		{
			$decision_source='edit_screen';

		}elseif($post_arr['screen_type']=='resolve_conflict'){
			$decision_source='conflict_resolution';
		}
		//print_test($post_arr);

		//exit;

		if(empty($post_arr['criteria_ex']) AND $post_arr['decision'] == 'excluded'){
			set_top_msg('Please choose the exclusion criteria',"error");
			if($post_arr['screen_type']=='simple_screen'){
				redirect('relis/manager/screen_paper');
				exit;
			}else{
					
				redirect('relis/manager/edit_screen/'.$post_arr['screening_id'].'/'.$post_arr['screen_type']);
				exit;
			}

		}else{

			if(!empty($post_arr['screen_type']) AND $post_arr['screen_type']=='screen_validation'){
				$screening_table='screening_validate';
				$assignment_table='assignment_screen_validate';

			}else{
				$screening_table='screening';
				$assignment_table='assignment_screen';
			}

			$this->db2 = $this->load->database(project_db(), TRUE);
			$screening_phase = !empty($post_arr['screening_phase'])?$post_arr['screening_phase']:1;
			$exclusion_criteria=($post_arr['decision'] == 'excluded')?$post_arr['criteria_ex']:NULL;
			$screening_decision=($post_arr['decision'] == 'excluded')?'Excluded':'Included';
			$screening_save=array(

					'screening_note'=>$post_arr['note'],
					'screening_decision'=>$screening_decision,
					'exclusion_criteria'=>$exclusion_criteria,
					'screening_time'=>bm_current_time('Y-m-d H:i:s'),
					'screening_status'=>'Done',
			);

			//print_test($screening_save); exit;

			$res = $this->db2->update('screening_paper',$screening_save,array('screening_id'=>$post_arr['screening_id']));


			$screen_phase_detail= $this->DBConnection_mdl->get_row_details ( 'get_screen_phase_detail',$screening_phase ,TRUE);

			$screening_phase_last_status=$screen_phase_detail['screen_phase_final'];


			$paper_status=get_paper_screen_status_new($post_arr['paper_id'] , $screening_phase);



			$query_screen_decision = $this->db2->get_where('screen_decison', array('paper_id' => $post_arr['paper_id'],'screening_phase' => $screening_phase,'decision_active'=>1), 1)->row_array();

			//screen history append

			$Tscreen_history=array(
					'decision_source'=>$decision_source,
					'user'=>active_user_id(),
					'decision'=>$screening_decision,
					'criteria'=>$exclusion_criteria,
					'note'=>$post_arr['note'],
					'paper_status'=>$paper_status,
					'screening_time'=>bm_current_time('Y-m-d H:i:s'),

			);

			$Json_screen_history=json_encode($Tscreen_history);

			if(empty($query_screen_decision)){
				$this->db2->insert('screen_decison',array('paper_id' => $post_arr['paper_id'],'screening_phase' => $screening_phase,'screening_decision' => $paper_status,'decision_source'=>$decision_source,'decision_history'=>$Json_screen_history));
					
			}else{
				if(!empty($query_screen_decision['decision_history']))
					$Json_screen_history=$query_screen_decision['decision_history']."~~__".$Json_screen_history;


					if($query_screen_decision['screening_decision'] !=$paper_status ){
						$this->db2->update('screen_decison',array('screening_decision' => $paper_status,'decision_source'=>$decision_source,'decision_history'=>$Json_screen_history),array('paper_id' => $post_arr['paper_id'],'screening_phase' => $screening_phase,'decision_active'=>1));
					}else{
						$this->db2->update('screen_decison',array('decision_history'=>$Json_screen_history),array('paper_id' => $post_arr['paper_id'],'screening_phase' => $screening_phase,'decision_active'=>1));

					}
			}


			if($screening_phase_last_status OR $paper_status=='Excluded'){
					
				if($paper_status=='Included'){
					$this->db2->update('paper',array('screening_status'=>$paper_status,'classification_status'=>'To classify'),array('id'=>$post_arr['paper_id']));
				}else{
					$paper_status=(($paper_status!='Included' AND $paper_status!='Excluded') ?'Pending':$paper_status);
					$this->db2->update('paper',array('screening_status'=>$paper_status,'classification_status'=>'Waiting'),array('id'=>$post_arr['paper_id']));
				}
					
			}
		}


		$after_save_redirect=$this->session->userdata('after_save_redirect');

		if(!empty($after_save_redirect)){
			$this->session->set_userdata('after_save_redirect','');
			redirect($after_save_redirect);

		}elseif(!(empty($post_arr['operation_type'])) AND $post_arr['operation_type']=='edit'){
			set_top_msg('Element updated');
			if($post_arr['operation_source']=='display_paper_screen'){
				redirect('relis/manager/display_paper_screen/'.$post_arr['paper_id']);
			}else{
				redirect('relis/manager/list_screen/mine_screen');
			}
		}else{
			set_top_msg('Element saved');
			if(!empty($post_arr['screen_type']) AND $post_arr['screen_type']=='screen_validation'){
				redirect('relis/manager/screen_paper_validation');
			}else{
				redirect('relis/manager/screen_paper');
			}
		}
	}

	public function  remove_screening($screen_id){

		$this->db2 = $this->load->database(project_db(), TRUE);
		$config="screening";

		$screen_detail= $this->DBConnection_mdl->get_row_details ( $config,$screen_id );

		$this->db2->update('screening',array('screening_active'=>0),array('	screening_id'=>$screen_id));

		$this->db2->update('assignment_screen',array('screening_done'=>0),array('assignment_id'=>$screen_detail['assignment_id']));


		update_paper_status_status($screen_detail['paper_id']);

		redirect('relis/manager/list_screen/mine_screen');
	}


	public function  remove_screening_validation($screen_id){

		$this->db2 = $this->load->database(project_db(), TRUE);
		$config="screening_validate";

		$screen_detail= $this->DBConnection_mdl->get_row_details ( $config,$screen_id );

		$this->db2->update('screening_validate',array('screening_active'=>0),array('	screening_id'=>$screen_id));

		$this->db2->update('assignment_screen_validate',array('screening_done'=>0),array('assignment_id'=>$screen_detail['assignment_id']));




		redirect('relis/manager/list_screen/screen_validation');
	}

	/*
	 * Fonction globale pour afficher la liste des élément suivant la structure de la table
	 *
	 * Input: $ref_table: nom de la configuration d'une page (ex papers, author)
	 * 			$val : valeur de recherche si une recherche a été faite sur la table en cours
	 * 			$page: la page affiché : ulilisé dans la navigation
	 */
	public function list_screen($list_cat='mine_screen',$val = "_", $page = 0 ,$dynamic_table=1){



		$ref_table="screening";
		$papers_list=False;

		if($list_cat=='assign_validation' ){

			$ref_table="assignment_screen_validate";

		}elseif($list_cat=='screen_validation' ){

			$ref_table="screening_validate";
		}elseif($list_cat=='mine_screen' OR $list_cat=='all_screen' ){

			$ref_table="screening";
		}elseif($list_cat=='mine_assign' OR $list_cat=='all_assign' ){

			$ref_table="assignment_screen";
		}elseif($list_cat=='screen_paper' OR $list_cat=='screen_paper_pending' OR $list_cat=='screen_paper_review' OR $list_cat=='screen_paper_included' OR $list_cat=='screen_paper_excluded' OR $list_cat=='screen_paper_conflict' ){

			$papers_list=True;
			$ref_table="papers";
		}
		/*
		 * Vérification si il y a une condition de recherche
		 */
		$val = urldecode ( urldecode ( $val ) );
		$filter = array ();
		if (isset ( $_POST ['search_all'] )) {
			$filter = $this->input->post ();

			unset ( $filter ['search_all'] );

			$val = "_";
			if (isset ( $filter ['valeur'] ) and ! empty ( $filter ['valeur'] )) {
				$val = $filter ['valeur'];
				$val = urlencode ( urlencode ( $val ) );
			}

			/*
			 * mis à jours de l'url en ajoutant la valeur recherché dans le lien puis rechargement de l'url
			 */
			$url = "relis/manager/list_screen/$list_cat"."/". $val ."/0/". $dynamic_table;

			redirect ( $url );
		}

		/*
		 * Récupération de la configuration(structure) de la table à afficher
		 */
		$ref_table_config=get_table_config($ref_table);


		$table_id=$ref_table_config['table_id'];




		/*
		 * Appel du model pour récupérer la liste à aficher dans la Base de donnés
		 */
		$rec_per_page=($dynamic_table)?-1:0;
		$extra_condition="";

		if($list_cat=='mine_screen' OR $list_cat=='mine_assign' ){
			$extra_condition =" AND ( user_id ='".active_user_id()."') ";
		}


		if($list_cat=='screen_paper'){
			$data=$this->DBConnection_mdl->get_papers('screen',$ref_table_config,$val,$page,$rec_per_page);
			$page_title="All papers";
		}elseif($list_cat=='screen_paper_pending'){
			//$data=$this->DBConnection_mdl->get_papers('screen',$ref_table_config,$val,$page,$rec_per_page);
			$extra_condition =" AND ( screening_status ='Pending') ";
			$data=$this->manage_mdl->get_list($ref_table_config,$val,$page,$rec_per_page,$extra_condition);
			$page_title="Pending papers";
		}elseif($list_cat=='screen_paper_review'){
			//$data=$this->DBConnection_mdl->get_papers('screen',$ref_table_config,$val,$page,$rec_per_page);
			$extra_condition =" AND ( screening_status ='In review') ";
			$data=$this->manage_mdl->get_list($ref_table_config,$val,$page,$rec_per_page,$extra_condition);
			$page_title="Papers in review";
		}elseif($list_cat=='screen_paper_included'){
			//$data=$this->DBConnection_mdl->get_papers('screen',$ref_table_config,$val,$page,$rec_per_page);
			$extra_condition =" AND ( screening_status ='Included') ";
			$data=$this->manage_mdl->get_list($ref_table_config,$val,$page,$rec_per_page,$extra_condition);
			$page_title="Papers included";
		}elseif($list_cat=='screen_paper_excluded'){
			$extra_condition =" AND ( screening_status ='Excluded') ";
			$data=$this->manage_mdl->get_list($ref_table_config,$val,$page,$rec_per_page,$extra_condition);
			$page_title="Papers excluded";
		}elseif($list_cat=='screen_paper_conflict'){
			$extra_condition =" AND ( screening_status ='In conflict') ";
			$data=$this->manage_mdl->get_list($ref_table_config,$val,$page,$rec_per_page,$extra_condition);
			$page_title="Papers in conflict";
		}elseif(!empty($extra_condition)){ //pour le string_management une fonction spéciale
			//todo verifier comment le spécifier dans config
			$data=$this->manage_mdl->get_list($ref_table_config,$val,$page,$rec_per_page,$extra_condition);

		}else{

			$data=$this->DBConnection_mdl->get_list($ref_table_config,$val,$page,$rec_per_page);
		}

		//print_test($data);

		/*
		 * récupération des correspondances des clés externes pour l'affichage  suivant la structure de la table
		 */



		$dropoboxes=array();
		foreach ($ref_table_config['fields'] as $k => $v) {

			if(!empty($v['input_type']) AND $v['input_type']=='select' AND $v['on_list']=='show'){


				if($v['input_select_source']=='array'){
					$dropoboxes[$k]=$v['input_select_values'];
				}elseif($v['input_select_source']=='table'){
					//print_test($v);
					$dropoboxes[$k]= $this->manager_lib->get_reference_select_values($v['input_select_values']);

				}elseif($v['input_select_source']=='yes_no'){
					$dropoboxes[$k]=array('0'=>"No",
							'1'=>"Yes"
					);
				}
			}

		}



		/*
		 * Vérification des liens (links) a afficher sur la liste
		 */


		$list_links=array();
		$add_link = false;
		$add_link_url="";
		foreach ($ref_table_config['links'] as $link_type => $link) {
			if(!empty($link['on_list'])){
				{
					$link['type']=$link_type;


					if(empty($link['title'])){
						$link['title']=lng_min($link['label']);
					}


					$push_link=false;

					switch ($link_type) {
						case 'add':

							$add_link=true; //will appear as a top button

							if(empty($link['url']))
								$add_link_url='manager/add_element/' . $ref_table;
								else
									$add_link_url=$link['url'];

									break;

						case 'view':
							if(!isset($link['icon']))
								$link['icon']='folder';

									
									
								if(empty($link['url']))
									$link['url']='manager/display_element/' . $ref_table.'/';

									$push_link=true;
									if($papers_list){
										$link['url']='relis/manager/display_paper_screen/';
									}


									break;

						case 'edit':

							if(!isset($link['icon']))
								$link['icon']='pencil';

									
								if(empty($link['url']))
									$link['url']='manager/edit_element/' . $ref_table.'/';

									if($list_cat=='mine_assign'){
										$link['url']='relis/manager/edit_assignment_mine/';
									}elseif($list_cat=='all_assign'){
										$link['url']='relis/manager/edit_assignment_all/';
									}
									$push_link=true;

									if($papers_list)//do not put the link on list papers
										$push_link=false;
										break;

						case 'delete':

							if(!isset($link['icon']))
								$link['icon']='trash';

									

								if(empty($link['url']))
									$link['url']='manager/delete_element/' . $ref_table.'/';

									$push_link=true;

									if($papers_list)//do not put the link on list papers
										$push_link=false;
										break;

						case 'add_child':

							if(!isset($link['icon']))
								$link['icon']='plus';

								if(!empty($link['url'])){

									$link['url']='manager/add_element_child/'.$link['url']."/". $ref_table."/";

									$push_link=true;
								}

								break;

						default:

							break;
					}

					if($push_link)
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
		$field_list=array();
		$field_list_header=array();
		foreach ($ref_table_config['fields'] as $k => $v) {

			if( $v['on_list']=='show'){

				array_push($field_list, $k);
				array_push($field_list_header, $v['field_title']);

			}

		}
		//print_test($field_list);
		$i=1;
		$list_to_display=array();

		foreach ($data['list'] as $key => $value) {

			$element_array=array();
			foreach ($field_list as $key_field=> $v_field) {
				if(isset($value[$v_field])){
					if(isset($dropoboxes[$v_field][$value[$v_field]]) ){
						$element_array[$v_field]=$dropoboxes[$v_field][$value[$v_field]];
					}else{
						$element_array[$v_field]=$value[$v_field];
					}


				}else{



					$element_array[$v_field]="";

					if(isset($ref_table_config['fields'][$v_field]['number_of_values']) AND $ref_table_config['fields'][$v_field]['number_of_values']!=1){
							
						if(isset($ref_table_config['fields'][$v_field]['input_select_values']) AND isset($ref_table_config['fields'][$v_field]['input_select_key_field']))
						{
							// récuperations des valeurs de cet element
							$M_values=$this->manager_lib->get_element_multi_values($ref_table_config['fields'][$v_field]['input_select_values'],$ref_table_config['fields'][$v_field]['input_select_key_field'],$data ['list'] [$key] [$table_id]);
							$S_values="";
							foreach ($M_values as $k_m => $v_m) {
								if(isset($dropoboxes[$v_field][$v_m]) ){
									$M_values[$k_m]=$dropoboxes[$v_field][$v_m];
								}

								$S_values.=empty($S_values)?$M_values[$k_m]:" | ".$M_values[$k_m];
							}

							$element_array[$v_field]=$S_values;
						}

					}




				}

					


			}

			/*
			 * Ajout des liens(links) sur la liste
			 */

			$action_button="";

			$arr_buttons=array();
			foreach ($list_links as $key_l => $value_l) {

				if(!empty($value_l['icon']))
					$value_l['label']= icon($value_l['icon']).' '.lng_min($value_l['label']);

					array_push($arr_buttons, array(
							'url'=> $value_l['url'].$value [$table_id],
							'label'=>$value_l['label'],
							'title'=>$value_l['title']

					)	);
			}


			if($list_cat=='screen_paper' OR $list_cat=='screen_paper_pending' OR $list_cat=='screen_paper_review' OR $list_cat=='screen_paper_included' OR $list_cat=='screen_paper_excluded' OR $list_cat=='screen_paper_conflict' ){
				$screening_res=get_paper_screen_result($element_array[$table_id]);
				//	print_test($screening_res);
				$element_array['reviews']=$screening_res['reviewers'];
				$element_array['decision']=$screening_res['screening_result'];
			}


			$action_button=create_button_link_dropdown($arr_buttons,lng_min('Action'));


			if(!empty($action_button))
				$element_array['links']=$action_button;
					
				if(isset($element_array[$table_id])){
					$element_array[$table_id]=$i + $page;
				}
					

					
				array_push($list_to_display,$element_array);
					
				$i++;
		}


		$data ['list']=$list_to_display;


		/*
		 * Ajout de l'entête de la liste
		 */
		if(!empty($data['list'])){
			//$array_header=$ref_table_config['header_list_fields'];
			$array_header=$field_list_header;
			if($list_cat=='screen_paper' OR $list_cat=='screen_paper_pending' OR $list_cat=='screen_paper_review' OR $list_cat=='screen_paper_included' OR $list_cat=='screen_paper_excluded' OR $list_cat=='screen_paper_conflict' ){
				array_push($array_header,'Reviewers');
				array_push($array_header,'Decision');
			}
			if(!empty($data['list'][$key]['links'])) {
				array_push($array_header,'');
			}


			if(!$dynamic_table){
				array_unshift($data['list'],$array_header);
			}else{
				$data['list_header']=$array_header;
			}
		}



		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */

		$data ['top_buttons']="";
		if($ref_table=="str_mng"){  //todo à corriger
			if($this->session->userdata('language_edit_mode')=='yes'){
				$data ['top_buttons'] .= get_top_button ( 'all', 'Close edition mode', 'config/update_edition_mode/no','Close edition mode','fa-ban','',' btn-warning ' );
			}else{
				$data ['top_buttons'] .= get_top_button ( 'all', 'Open edition mode', 'config/update_edition_mode/yes','Open edition mode','fa-check','',' btn-dark ' );
			}
		}else{
			if($add_link)
				$data ['top_buttons'] .= get_top_button ( 'add', 'Add new', $add_link_url);
		}


		if(activate_update_stored_procedure())
			$data ['top_buttons'] .= get_top_button ( 'all', 'Update stored procedure', 'home/update_stored_procedure/'.$ref_table,'Update stored procedure','fa-check','',' btn-dark ' );

			$data ['top_buttons'] .= get_top_button ( 'close', 'Close', 'home' );


			/*
			 * Titre de la page
			 */


			if(isset($ref_table_config['entity_title']['list'])){
				$data['page_title']=lng($ref_table_config['entity_title']['list']);
			}else{
				$data ['page_title'] = lng("List of ".$ref_table_config['reference_title']);
			}

			if($list_cat=='mine_screen' ){

				$data ['page_title']="My screenings";

			}elseif( $list_cat=='mine_assign' )
			{
				$data ['page_title']="Papers assigned to me for screening";

			}

			if(!empty($page_title))
				$data ['page_title']=$page_title;
					
				/*
				 * Configuration pour l'affichage des lien de navigation
				 */

				$data ['valeur']=($val=="_")?"":$val;


				/*
				 * Si on a besoin de faire urecherche sur la liste specifier la vue où se trouve le formulaire de recherche
				 */
				if(!$dynamic_table AND !empty($ref_table_config['search_by'])){
					$data ['search_view'] = 'general/search_view';
				}


				/*
				 * La vue qui va s'afficher
				 */

				if(!$dynamic_table){
					$data ['nav_pre_link'] = 'relis/manager/list_screen/' .$list_cat.'/' . $val . '/';
					$data ['nav_page_position'] = 6;

					$data['page']='general/list';
				}else{
					$data['page']='general/list_dt';
				}

				if(admin_config($ref_table))
					$data['left_menu_admin']=True;
					/*
					 * Chargement de la vue avec les données préparés dans le controleur
					 */
					$this->load->view('body',$data);
	}


	public function screen_completion($type='screening'){

		if($type=='validate'){
			$assignments=$this->Relis_mdl->get_user_assigned_papers(0,'screen_validation',active_screening_phase());
		}else{
			$assignments=$this->Relis_mdl->get_user_assigned_papers(0,'simple_screen',active_screening_phase());
		}


		//print_test($assignments);
		//print_test($assignments);
		//exit;
		$assignment_id=0;
		$total_papers=count($assignments);
		$papers_screened=0;

		$assign_per_user=array();
			
		foreach ($assignments as $key => $value) {

			if (! isset($assign_per_user[$value['user_id']])){
				$assign_per_user[$value['user_id']]['total_papers'] = 1;

				if($value['screening_status']=='Done'){
					$assign_per_user[$value['user_id']]['papers_screened']=1;
					$papers_screened ++;
				}else{
					$assign_per_user[$value['user_id']]['papers_screened']=0;
				}

			}else{
				$assign_per_user[$value['user_id']]['total_papers']++;

				if($value['screening_status']=='Done'){
					$assign_per_user[$value['user_id']]['papers_screened']++;
					$papers_screened ++;
				}

			}



		}

		$users=$this->manager_lib->get_reference_select_values('users;user_name');
			
		//	print_test($users);
		//print_test($assign_per_user);
		foreach ($assign_per_user as $key_a => $value_a) {
			$assign_per_user[$key_a]['completion']=(int)($value_a['papers_screened'] *100 / $value_a['total_papers'] );
			$assign_per_user[$key_a]['user']=$users[$key_a];
		}

		$assign_per_user['total']=array(
				'total_papers'=>$total_papers,
				'papers_screened'=>$papers_screened,
				'completion'=>!empty($total_papers)?(int)($papers_screened *100 / $total_papers ):0,
				'user'=>'<b>Total</b>',
		);
		//	print_test($assign_per_user);


		$data['completion_screen']=$assign_per_user;
		//print_test($data['completion_screen']);








		$data ['page_title']=($type=='validate')?lng('Screening validation progress'):lng('Screening Progress');

		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
		$data['left_menu_perspective']='left_menu_screening';
		$data['project_perspective']='screening';
		$data ['page'] = 'relis/screen_completion';



		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}




	public function qa_completion($type='qa'){

		if($type=='validate'){
			$completion=$this->manager_lib->get_qa_completion('QA_Val');

		}else{
			$completion=$this->manager_lib->get_qa_completion('QA');
		}


		$users=$this->manager_lib->get_reference_select_values('users;user_name');
		$per_user_completion=array();

		if(!empty($completion['user_completion'])){

			foreach ($completion['user_completion'] as $key => $value) {
				if(!empty($value['all'])){
					$per_user_completion[$key]['total_papers']=$value['all'];
					$per_user_completion[$key]['papers_screened']=!empty($value['done'])?$value['done']:0;
					$per_user_completion[$key]['completion']=(int)($per_user_completion[$key]['papers_screened'] *100 / $per_user_completion[$key]['total_papers'] );
					$per_user_completion[$key]['user']=$users[$key];

				}
			}
			$total_papers=$completion['general_completion']['all'];
			$papers_screened=!empty($completion['general_completion']['done'])?$completion['general_completion']['done']:0;

			$per_user_completion['total']=array(
					'total_papers'=>$total_papers,
					'papers_screened'=>$papers_screened,
					'completion'=>!empty($total_papers)?(int)($papers_screened *100 / $total_papers ):0,
					'user'=>'<b>Total</b>',
			);

		}




		$data['completion_screen']=$per_user_completion;






		$data ['page_title']=($type=='validate')?lng('QA validation progress'):lng('QA progress');

		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
		//$data['left_menu_perspective']='left_menu_screening';
		//$data['project_perspective']='screening';
		$data ['page'] = 'relis/screen_completion';



		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}


	public function class_completion($type='class'){



		$users=$this->manager_lib->get_reference_select_values('users;user_name',FALSE);
		//print_test($users);
		$per_user_completion=array();
		if($type=='validate'){
			$gen_completion=$this->manager_lib->get_classification_completion('validation','all');
			if(!empty($gen_completion['all_papers'])){
				foreach ($users as $key => $value) {
					$user_completion=$this->manager_lib->get_classification_completion('validation',$key);

					if(!empty($user_completion['all_papers'])){
						$per_user_completion[$key]['total_papers']=$user_completion['all_papers'];
						$per_user_completion[$key]['papers_screened']=$user_completion['processed_papers'];
						$per_user_completion[$key]['completion']=(int)($per_user_completion[$key]['papers_screened'] *100 / $per_user_completion[$key]['total_papers'] );
						$per_user_completion[$key]['user']=$value;
					}

					$per_user_completion['total']['total_papers']=$gen_completion['all_papers'];
					$per_user_completion['total']['papers_screened']=$gen_completion['processed_papers'];
					$per_user_completion['total']['completion']=(int)($per_user_completion['total']['papers_screened'] *100 / $per_user_completion['total']['total_papers'] );
					$per_user_completion['total']['user']='Total';

				}
			}




		}else{
			$gen_completion=$this->manager_lib->get_classification_completion('class','all');
			if(!empty($gen_completion['all_papers'])){
				foreach ($users as $key => $value) {
					$user_completion=$this->manager_lib->get_classification_completion('class',$key);
					if(!empty($user_completion['all_papers'])){
						$per_user_completion[$key]['total_papers']=$user_completion['all_papers'];
						$per_user_completion[$key]['papers_screened']=$user_completion['processed_papers'];
						$per_user_completion[$key]['completion']=(int)($per_user_completion[$key]['papers_screened'] *100 / $per_user_completion[$key]['total_papers'] );
						$per_user_completion[$key]['user']=$value;
					}
				}

				$per_user_completion['total']['total_papers']=$gen_completion['all_papers'];
				$per_user_completion['total']['papers_screened']=$gen_completion['processed_papers'];
				$per_user_completion['total']['completion']=(int)($per_user_completion['total']['papers_screened'] *100 / $per_user_completion['total']['total_papers'] );
				$per_user_completion['total']['user']='Total';
			}


		}




		$data['completion_screen']=$per_user_completion;






		$data ['page_title']=($type=='validate')?lng('Classification validation progress'):lng('Classification progress');

		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
		//$data['left_menu_perspective']='left_menu_screening';
		//$data['project_perspective']='screening';
		$data ['page'] = 'relis/screen_completion';



		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}


	/*
	 * Fonction spécialisé  pour l'affichage d'un papier
	 * Input:	$ref_id: id du papier
	 * Input:	$display_type: type d'affishage si la valeur est 'det' lhystorique du papier sera affiché
	 */
	public function display_paper_screen($ref_id,$display_type='det') {
		$project_published=project_published();

		//	print_test(get_paper_screen_result($ref_id));

		$ref_table="papers";

		/*
		 * Récupération de la configuration(structure) de la table des papiers
		 */
		$table_config=get_table_configuration($ref_table);


		/*
		 * Appel de la fonction  récupérer les informations sur le papier afficher
		 */
		$table_config['current_operation']='detail_paper';
		$paper_data=$this->manager_lib->get_detail($table_config,$ref_id);

		//	print_test($paper_data);


		/*
		 * Préparations des informations à afficher
		 */

		//venue
		$venue="";

		$authors="";
		foreach ($paper_data as $key => $value) {
			if($value['field_id']=='venueId' AND !empty($value['val2'][0])){
				$venue=$value['val2'][0];
			}elseif($value['field_id']=='authors' AND !empty($value['val2'])){

				if(count($value['val2']>1)){
					$authors='<table class="table table-hover" ><tr><td> '.$value['val2'][0].'</td></tr>';
					foreach ($value['val2'] as $k => $v) {
						if($k>0){
							$authors.="<tr><td> ".$v.'</td></tr>';
						}
					}

					$authors.="</table>";
				}else{

					$authors=" : ".$value['val2'][0];
				}

			}
		}







		$content_item = $this->DBConnection_mdl->get_row_details ( 'get_detail_papers',$ref_id,TRUE);
		//get_detail_paper
		//print_test($content_item);

		$paper_name=$content_item['bibtexKey']." - ".$content_item['title'];
		$paper_excluded=False;
		if($content_item['paper_excluded']=='1'){
			$paper_excluded=True;
		}

		$data['paper_excluded']=$paper_excluded;
		$item_data=array();

		$array['title']=$content_item['bibtexKey']." - ".$content_item['title'];

		if(!empty($content_item['doi'])){
            $paper_link = $content_item['doi'];
            if( (strpos($paper_link,'http://') === FALSE) && (strpos($paper_link,'https://') === FALSE)){
                $paper_link = "//".$paper_link;
            }
			$array['title'].='<ul class="nav navbar-right panel_toolbox">
				<li>
					<a title="Go to the page" href="'.$paper_link.'" target="_blank" >
				 		<img src="'.base_url().'cside/images/pdf.jpg"/>

					</a>
				</li>

				</ul>';
		}
			

		array_push($item_data, $array);

		$array['title']="<b>".lng('Abstract')." :</b> <br/><br/>".$content_item['abstract'];
		array_push($item_data, $array);
		$array['title']="<b>".lng('Preview')." :</b> <br/><br/>".$content_item['preview'];
		array_push($item_data, $array);

		$array['title']="<b>".lng('Venue')." </b> ".$venue;
		//array_push($item_data, $array);

		$array['title']="<b>".lng('Authors')." </b> ".$authors;
		//array_push($item_data, $array);

			

		$data['item_data']=$item_data;


		//print_test($data);
		$screening_phase=active_screening_phase();
		if(active_screening_phase()){
			//$screening_phase=1;
			//$res_screen=get_paper_screen_result($ref_id);
			$res_screen=get_paper_screen_status_new($ref_id,$screening_phase,'all');
			//	print_test($res_screen);

			if(trim($res_screen['screening_result'])=='In conflict' AND !$project_published){
				$my_paper=FALSE;
				foreach ($res_screen['screenings'] as $key => $value) {
					if(has_usergroup(1) OR is_project_creator(active_user_id() , project_db()) OR $value['user_id']==active_user_id()){

						$res_screen['screenings'][$key]['edit_link']=create_button_link('relis/manager/edit_screen/'.$value['screening_id'].'/resolve_conflict','Edit',"btn-info","Update decision") ;
					}else{

						$res_screen['screenings'][$key]['edit_link']="";
					}
				}
				$data['screen_edit_link']=TRUE;
					
			}

			if((has_usergroup(1)
					OR is_project_creator(active_user_id() , project_db()))
					AND !$project_published)
				$data ['assign_new_button'] =get_top_button ( 'add', 'Add a reviewer', 'op/add_element_child/add_reviewer/'.$ref_id, 'Add a reviewer')." ";

				$data['screenings']=$res_screen['screenings'];
				$data['screening_result']=$res_screen['screening_result'];
		}else{
			$data['screening_result']=$content_item['screening_status'];

		}


		if($display_type=='det'){

			if(active_screening_phase()){
				$data['screen_history']=get_paper_screen_history($ref_id,$screening_phase);
			}else{
				$data['screen_history']=get_paper_screen_status__all($ref_id);
			}
			//print_test($data['screen_history']);

		}





		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data ['top_buttons']="";
		if(!empty(	$table_config['links']['edit']) AND !empty($table_config['links']['edit']['on_view'])  AND ($table_config['links']['edit']['on_view']== True) ){

			//$data ['top_buttons'] .= get_top_button ( 'edit', $table_config['links']['edit']['title'], 'manager/edit_element/' . $ref_table.'/'.$ref_id )." ";

		}

		if(!empty(	$table_config['links']['delete']) AND !empty($table_config['links']['delete']['on_view'])  AND ($table_config['links']['delete']['on_view']== True) ){

			//$data ['top_buttons'] .= get_top_button ( 'delete', $table_config['links']['delete']['title'], 'manage/delete_element/' . $ref_table.'/'.$ref_id )." ";

		}




		$data ['top_buttons'] .= get_top_button ( 'back', 'Back', 'home' );



		/*
		 * Titre de la page
		 */
		$data ['page_title'] = lng('Paper');


		/*
		 * La vue qui va s'afficher
		 */
		$data ['page'] = 'relis/display_paper_screen';


		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}


	public function display_paper_min($ref_id,$display_type='det') {


		//	print_test(get_paper_screen_result($ref_id));

		$ref_table="papers";

		/*
		 * Récupération de la configuration(structure) de la table des papiers
		 */
		$table_config=get_table_configuration($ref_table);


		/*
		 * Appel de la fonction  récupérer les informations sur le papier afficher
		 */
		$table_config['current_operation']='detail_paper';
		$paper_data=$this->manager_lib->get_detail($table_config,$ref_id);

		//	print_test($paper_data);


		/*
		 * Préparations des informations à afficher
		 */

		//venue
		$venue="";

		$authors="";
		foreach ($paper_data as $key => $value) {
			if($value['field_id']=='venueId' AND !empty($value['val2'][0])){
				$venue=$value['val2'][0];
			}elseif($value['field_id']=='authors' AND !empty($value['val2'])){

				if(count($value['val2']>1)){
					$authors='<table class="table table-hover" ><tr><td> '.$value['val2'][0].'</td></tr>';
					foreach ($value['val2'] as $k => $v) {
						if($k>0){
							$authors.="<tr><td> ".$v.'</td></tr>';
						}
					}

					$authors.="</table>";
				}else{

					$authors=" : ".$value['val2'][0];
				}

			}
		}







		$content_item = $this->DBConnection_mdl->get_row_details ( 'get_detail_papers',$ref_id,TRUE);
		//get_detail_paper
		//print_test($content_item);

		$paper_name=$content_item['bibtexKey']." - ".$content_item['title'];
		$paper_excluded=False;
		if($content_item['paper_excluded']=='1'){
			$paper_excluded=True;
		}

		$data['paper_excluded']=$paper_excluded;
		$item_data=array();


		$array['title']=$content_item['bibtexKey']." - ".$content_item['title'];

		if(!empty($content_item['doi'])){
            $paper_link = $content_item['doi'];
            if( (strpos($paper_link,'http://') === FALSE) && (strpos($paper_link,'https://') === FALSE)){
                $paper_link = "//".$paper_link;
            }

			$array['title'].='<ul class="nav navbar-right panel_toolbox">
				<li>
					<a title="Go to the page" href="' . $paper_link . '" target="_blank" >
				 		<img src="'.base_url().'cside/images/pdf.jpg"/>

					</a>
				</li>

				</ul>';
		}
			

		array_push($item_data, $array);

		$array['title']="<b>".lng('Abstract')." :</b> <br/><br/>".$content_item['abstract'];
		array_push($item_data, $array);
		$array['title']="<b>".lng('Preview')." :</b> <br/><br/>".$content_item['preview'];
		array_push($item_data, $array);

		$array['title']="<b>".lng('Venue')." </b> ".$venue;
		//array_push($item_data, $array);

		$array['title']="<b>".lng('Authors')." </b> ".$authors;
		//array_push($item_data, $array);

			

		$data['item_data']=$item_data;





		/*
		 * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
		 */
		$data ['top_buttons']="";




		$data ['top_buttons'] .= get_top_button ( 'back', 'Back', 'home' );



		/*
		 * Titre de la page
		 */
		$data ['page_title'] = lng('Paper');


		/*
		 * La vue qui va s'afficher
		 */
		$data ['page'] = 'relis/display_paper_min';


		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}



	public function edit_assignment_mine($assignment_id){

		$this->session->set_userdata('after_save_redirect',"relis/manager/list_screen/mine_assign");

		redirect("manager/edit_element/assignment_screen/$assignment_id");
	}

	public function edit_assignment_all($assignment_id){

		$this->session->set_userdata('after_save_redirect',"relis/manager/list_screen/all_assign");

		redirect("manager/edit_element/assignment_screen/$assignment_id");
	}


	public function screen_result($type=1,$api=0){


		$users=$this->manager_lib->get_reference_select_values('users;user_name');
		$exclusion_crit=$this->manager_lib->get_reference_select_values('exclusioncrieria;ref_value');
		//print_test($users);
		$ref_table_config=get_table_configuration('papers');

		$ref_table_config['current_operation']='list_papers_screen';

		$papers=$this->DBConnection_mdl->get_list_mdl($ref_table_config,'_',0,-1);

		//print_test($papers);
		$excluded_conflict=0;
		$included_conflict=0;
		$result=array();
		$result['total']=0;
		foreach ($papers['list'] as $key => $value) {
			if(!empty($value['screening_status'])){
				if(empty($result[$value['screening_status']])){
					$result[$value['screening_status']]=1;
				}else{
					$result[$value['screening_status']]=$result[$value['screening_status']]+1;
				}

				$result['total']++;

				if($value['decision_source']=='conflict_resolution' AND $value['screening_status']=='Included' ){
					$included_conflict++;
				}elseif($value['decision_source']=='conflict_resolution' AND $value['screening_status']=='Excluded'){
					$excluded_conflict++;
				}
			}
		}

		//  list to be displayed for global result
		$data['screening_result']=array(
				'0'=>array(
						'title'=>'Decision',
						'nbr'=>'Papers',
						'pourc'=>'%',
				),
				'Included'=>array(
						'title'=>anchor('op/entity_list/list_papers_screen_included','<u><b>Included</b></u>'),
						'nbr'=>!empty($result['Included'])?$result['Included']:0,
						'pourc'=>!empty($result['Included'])?round(($result['Included']*100 / $result['total']),2):0,
				),
				'Excluded'=>array(
						'title'=>anchor('op/entity_list/list_papers_screen_excluded','<u><b>Excluded</b></u>'),
						'nbr'=>!empty($result['Excluded'])?$result['Excluded']:0,
						'pourc'=>!empty($result['Excluded'])?round(($result['Excluded']*100 / $result['total']),2):0,
				),
				'conflict'=>array(
						'title'=>anchor('op/entity_list/list_papers_screen_conflict','<u><b>In conflict</b></u>'),
						'nbr'=>!empty($result['In conflict'])?$result['In conflict']:0,
						'pourc'=>!empty($result['In conflict'])?round(($result['In conflict']*100 / $result['total']),2):0,
				),
				'review'=>array(
						'title'=>anchor('op/entity_list/list_papers_screen_review','<u><b>In review</b></u>'),
						'nbr'=>!empty($result['In review'])?$result['In review']:0,
						'pourc'=>!empty($result['In review'])?round(($result['In review']*100 / $result['total']),2):0,
				),
				'pending'=>array(
						'title'=>anchor('op/entity_list/list_papers_screen_pending','<u><b>Pending</b></u>'),
						'nbr'=>!empty($result['Pending'])?$result['Pending']:0,
						'pourc'=>!empty($result['Pending'])?round(($result['Pending']*100 / $result['total']),2):0,
				),
				'total'=>array(
						'title'=>'<b>Total</b>',
						'nbr'=>"<b>".(!empty($result['total'])?$result['total']:0)."</b>",
						'pourc'=>'',
				)
		);

		$data['screening_conflict_resolution']=array(
				'0'=>array(
						'title'=>'Decision',
						'nbr'=>'Nbr',

				),
				'Included'=>array(
						//'title'=>'Resolved included',
						'title'=>anchor('op/entity_list/list_papers_screen_included_after_conflict','<u><b>Resolved included</b></u>'),
						'nbr'=>$included_conflict,
				),
				'Excluded'=>array(
						//'title'=>'Resolved excluded',
						'title'=>anchor('op/entity_list/list_papers_screen_excluded_after_conflict','<u><b>Resolved excluded</b></u>'),
						'nbr'=>$excluded_conflict,
				),
				'conflict'=>array(
						//'title'=>'Pending conflicts',
						'title'=>anchor('op/entity_list/list_papers_screen_conflict','<u><b>Pending conflicts</b></u>'),
						'nbr'=>!empty($result['In conflict'])?$result['In conflict']:0,
				)
		);

		$ref_table_config_s=get_table_configuration('screening');

		$ref_table_config_s['current_operation']='list_screenings';

		$screenings=$this->DBConnection_mdl->get_list_mdl($ref_table_config_s,'_',0,-1);
		//print_test($screenings);exit;
		//$screenings=$this->DBConnection_mdl->get_list(get_table_config('screening'),'_',0,-1);

		$res_screening['total']=0;
		$res_screening['users']=array();
		$res_screening['criteria']=array();
		$res_screening['all_criteria']=0;
		$key=0;
		//	print_test($screenings);
		foreach ($screenings['list'] as $key => $value) {

			$res_screening['total']++;
			if(empty($res_screening['users'][$value['user_id']][$value['screening_decision']])){

				$res_screening['users'][$value['user_id']][$value['screening_decision']]=1;
			}else{
					
				$res_screening['users'][$value['user_id']][$value['screening_decision']] = $res_screening['users'][$value['user_id']][$value['screening_decision']]+1;
			}


			// exclusion critéria
			if($value['screening_decision']=='Excluded' AND !empty($value['exclusion_criteria'])){
				if(empty($res_screening['criteria'][$value['exclusion_criteria']])){
					//	echo "<p>bbb</p>";
					$res_screening['criteria'][$value['exclusion_criteria']]=1;
				}else{
					//	echo "<p>cccc</p>";
					$res_screening['criteria'][$value['exclusion_criteria']] = $res_screening['criteria'][$value['exclusion_criteria']]+1;
				}

				$res_screening['all_criteria']++;
				//critérias per user
				if(empty($res_screening['users'][$value['user_id']]['criteria'][$value['exclusion_criteria']])){
					//	echo "<p>bbb</p>";
					$res_screening['users'][$value['user_id']]['criteria'][$value['exclusion_criteria']]=1;
				}else{
					//	echo "<p>cccc</p>";
					$res_screening['users'][$value['user_id']]['criteria'][$value['exclusion_criteria']] = $res_screening['users'][$value['user_id']]['criteria'][$value['exclusion_criteria']]+1;
				}

			}

		}

		//  list to be displayed for  result per user
		$result_per_user=array();
			
		if(!empty ($res_screening['users'] ));
		{
			$result_per_user[0]=array(
					'user'=>'User ',
					'accepted'=>'Included',
					'excluded'=>'Excluded',
					'conflict'=>'In conflict',
			);
			$i=1;
			foreach ($res_screening['users'] as $key => $value) {
				$user_screening_completion=$this->get_user_completion($key,active_screening_phase(),'Screening');
				$result_per_user[$i]=array(
						'user'=>!empty($users[$key])?$users[$key]:'User '.$key,
						'accepted'=>!empty($value['Included'])?$value['Included']:0,
						'excluded'=>!empty($value['Excluded'])?$value['Excluded']:0,
						'conflict'=>!empty($user_screening_completion['papers_in_conflict'])?$user_screening_completion['papers_in_conflict']:0,
				);
				$i++;
			}

		}

		$data['result_per_user']=$result_per_user;

		$result_per_criteria=array();

		if(!empty ($res_screening['criteria'] ));
		{
			$result_per_criteria[0]=array(
					'criteria'=>'Criteria ',
					'Nbr'=>'Nbr',
					'pourc'=>'%'
			);
			$i=1;
			foreach ($res_screening['criteria'] as $key => $value) {
				$result_per_criteria[$i]=array(
						'criteria'=>!empty($exclusion_crit[$key])?$exclusion_crit[$key]:'Criteria '.$key,
						'Nbr'=>$value,
						'pourc'=>!empty($res_screening['all_criteria'])?round(($value*100/$res_screening['all_criteria']),2):0,
				);
				$i++;
			}

		}
		//test if kappa is enabled
		if(get_appconfig_element('use_kappa')){
			$kappa=$this->calculate_kappa();
			$kappa_meaning='-';

			//	print_test($kappa_meaning);
			$k_display="";
			if(!empty($kappa)){
				$kappa_meaning=$this->kappa_meaning($kappa);
				$k_display=" -   Kappa : $kappa ($kappa_meaning)";
			}

			$data['kappa']=$kappa;
			$data['kappa_meaning']=$kappa_meaning;
		}
		$data['result_per_criteria']=$result_per_criteria;
		$data ['page_title'] = lng('Screening Statistics');//.$k_display;
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
		$data['left_menu_perspective']='left_menu_screening';
		$data['project_perspective']='screening';

		$data ['page'] = 'relis/screen_result';

		if($api)
			print_test($data);
			else{
				/*
				 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
				 */
				$this->load->view ( 'body', $data );
			}
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
	public function validate_screen_set($data=""){
		if(! active_screening_phase())
		{	redirect('home');
		exit;
		}

		$screening_phase_info=active_screening_phase_info();

		//print_test($screening_phase_info);

		$screening_phase_id=active_screening_phase();

		$paper_source=$screening_phase_id;
		$paper_source_status=screening_validation_source_paper_status();//Excluded
		$phase_title = $screening_phase_info['phase_title'];



		$append_title="( $paper_source_status papers  from $phase_title )";
		//echo $append_title;
		$data['papers_sources']=$paper_source;
		$data['paper_source_status']=$paper_source_status;
		$data['screening_phase']=$screening_phase_id;

		if(has_user_role('validator')){
			$data['assign_to_connected']=True;
		}else{
			$data['assign_to_connected']=False;
		}

		$papers=$this->get_papers_to_screen($paper_source,$paper_source_status,'','Validation');

		//	print_test($papers['assigned']);

		$data['paper_source']=$paper_source;
			
		$paper_list[0]=array('Key','Title');
			
		foreach ($papers['to_assign'] as $key => $value) {
			$paper_list[$key+1]=array($value['bibtexKey'],$value['title']);
		}

		$data['paper_list']=$paper_list;


		$user_table_config=get_table_configuration('users');
			
		$users=$this->DBConnection_mdl->get_list($user_table_config,'_',0,-1);
		$_assign_user=array();
		foreach ($users['list'] as $key => $value) {
			if( (user_project($this->session->userdata('project_id') ,$value['user_id'])) AND can_validate_project($value['user_id']) ){

				$_assign_user[$value['user_id']]=$value['user_name'];
			}
		}
		//	print_test($users);
		$data['users']=$_assign_user;
		$data['number_papers']=count($papers['to_assign']);
		$data['number_papers_assigned']=count($papers['assigned']);
		$data['percentage_of_papers']=get_appconfig_element('validation_default_percentage');
		$data['papers_categories']=array('Excluded'=>'Excluded','Included'=>'Included','all'=>'All');



		$data ['page_title'] = lng('Assign papers for validation '.$append_title);
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'home' );
		//$data['left_menu_perspective']='z_left_menu_screening';
		//$data['project_perspective']='screening';
		$data ['page'] = 'relis/assign_papers_screen_validation';


		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}


	public function validate_screen_from_previous_set($data=""){//old vesion updated
		if(! active_screening_phase())
		{	redirect('home');
		exit;
		}

		$screening_phase_info=active_screening_phase_info();
		$creening_phase_id=active_screening_phase();

		$data['screening_phase']=$creening_phase_id;

		//print_test($screening_phase_info);
		//$screening phases
		$screening_phases = $this->db_current->order_by('screen_phase_order', 'ASC')
		->get_where('screen_phase', array('screen_phase_active'=>1))
		->result_array();


		$previous_phase=0;
		$previous_phase_title="";

		if($screening_phase_info['source_paper']=='Previous phase'){
			foreach ($screening_phases as $k => $phase) {
					
				if($phase['screen_phase_id']==$creening_phase_id)	{
					break;
				}elseif($phase['phase_type']!='Validation'){
					$previous_phase=$phase['screen_phase_id'];
					$previous_phase_title=$phase['phase_title'];
				}
					
			}
		}



		if($previous_phase == 0){
			$paper_source='all';
			$paper_source_status=$screening_phase_info['source_paper_status'];
			$previous_phase_title=" ";
		}else{
			$paper_source=$previous_phase;
			$paper_source_status=$screening_phase_info['source_paper_status'];
			$previous_phase_title = " from $previous_phase_title";
		}

		$append_title="( $paper_source_status papers  $previous_phase_title )";
		//echo $append_title;
		$data['papers_sources']=$paper_source;
		$data['paper_source_status']=$paper_source_status;
		$data['screening_phase']=$creening_phase_id;



		$papers=$this->get_papers_to_screen($paper_source,$paper_source_status);

		//print_test($papers);
		$data['paper_source']=$paper_source;
			
		$paper_list[0]=array('Key','Title');
			
		foreach ($papers['to_assign'] as $key => $value) {
			$paper_list[$key+1]=array($value['bibtexKey'],$value['title']);
		}

		$data['paper_list']=$paper_list;












		//	$papers=$this->DBConnection_mdl->get_papers('screen','papers','_',0,-1);
		//print_test($papers);



		$user_table_config=get_table_configuration('users');
			
		$users=$this->DBConnection_mdl->get_list($user_table_config,'_',0,-1);
		$_assign_user=array();
		foreach ($users['list'] as $key => $value) {
			if( (user_project($this->session->userdata('project_id') ,$value['user_id'])) ){

				$_assign_user[$value['user_id']]=$value['user_name'];
			}
		}
		//	print_test($users);
		$data['users']=$_assign_user;
		$data['number_papers']=count($papers['to_assign']);
		$data['number_papers_assigned']=count($papers['assigned']);
		$data['percentage_of_papers']=20;
		$data['papers_categories']=array('Excluded'=>'Excluded','Included'=>'Included','all'=>'All');



		$data ['page_title'] = lng('Set screening validation '.$append_title);
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'home' );
		//$data['left_menu_perspective']='z_left_menu_screening';
		//$data['project_perspective']='screening';
		$data ['page'] = 'relis/assign_papers_screen_validation';

		//print_test($data);

		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}


	function save_assign_screen_validation(){

		$post_arr = $this->input->post ();
			
		$users=array();
		$i=1;
		$percentage=intval($post_arr['percentage']);
		if(empty( $percentage)){

			$data['err_msg'] = lng(' Please provide  "Percentage of papers" ');
			$this->validate_screen_set($data);

		}elseif($percentage>100 OR $percentage<=0){
			$data['err_msg'] = lng("Please provide a correct value of percentage");
			$this->validate_screen_set($data);
		}
		else{

			// Get selected users
			if(!empty($post_arr['assign_papers_to'])){// assign to connected user
				array_push($users,$post_arr['assign_papers_to']);
			}else{
				While ($i <= $post_arr['number_of_users']) {
					if(!empty( $post_arr['user_'.$i])){
						array_push($users,$post_arr['user_'.$i]);
					}
					$i++;
				}
			}

			//Verify if selected users is > of required reviews per paper
			if(count($users) < 1){

				$data['err_msg'] = lng('Please select at least one user  ');
				$this->validate_screen_set($data);

			}else{

				$currect_screening_phase=$post_arr['screening_phase'];
				$papers_sources=$post_arr['papers_sources'];
				$paper_source_status=$post_arr['paper_source_status'];
				$screening_phase_info=active_screening_phase_info();
				$phase_title=$screening_phase_info['phase_title'];
				$reviews_per_paper=1;

				$papers_all=$this->get_papers_to_screen($papers_sources,$paper_source_status,'','Validation');

				$papers=$papers_all['to_assign'];
				$papers_to_validate_nbr= round( count($papers) * $percentage/100);

				$operation_description="Assign $percentage % ($papers_to_validate_nbr) of ".$paper_source_status." papers for $phase_title";
				//	print_test($papers);
				shuffle($papers); // randomize the list
					
				$assign_papers= array();
				$this->db2 = $this->load->database(project_db(), TRUE);
				$operation_code=active_user_id()."_".time();
				foreach ($papers as $key => $value) {
					if($key<$papers_to_validate_nbr)	{
						$assign_papers[$key]['paper']=$value['id'];

						$assign_papers[$key]['users']=array();
							
							
						$assignment_save=array(
								'paper_id'=>$value['id'],
								'user_id'=>'',
								'assignment_note'=>'',
								'assignment_type'=>screening_validator_assignment_type(),
								'operation_code'=>$operation_code,
								'assignment_mode'=>'auto',
								'assignment_role'=>'Validation',
								'screening_phase'=>$currect_screening_phase,
								'assigned_by'=>$this->session->userdata ( 'user_id' )

						);
						$j=1;

						//the table to save assignments

						$table_name=get_table_configuration('screening','current','table_name');
							

						while($j<=$reviews_per_paper){



							$temp_user=($key % count($users)) + $j;

							if($temp_user >= count($users) )
								$temp_user = $temp_user - count($users);

								array_push($assign_papers[$key]['users'], $users[$temp_user]);

								$assignment_save['user_id']=$users[$temp_user];
								//print_test($assignment_save);
								$this->db2->insert($table_name,$assignment_save);


								$j++;
						}

					}
				}

				//	print_test();

				$operation_arr=array('operation_code'=>$operation_code,
						'operation_type'=>'assign_papers_validation',
						'user_id'=>active_user_id(),
						'operation_desc'=>$operation_description

				);

				//print_test($operation_arr);
				$res2 = $this->manage_mdl->add_operation($operation_arr);


				set_top_msg('Operation completed');
				redirect('home/screening');

			}
		}
	}



	public function screen_validation_result(){

		//Get all papers
		$res_papers=$this->get_papers_to_screen();

		$papers=array();

		foreach ($res_papers['all_papers'] as $key => $value) {
			$papers[$value['id']]=array(
					'bibtexKey'=>$value['bibtexKey'],
					'title'=>$value['title'],
					'screening_status'=>$value['paper_status'],
					'classification_status'=>$value['classification_status']

			);
		}

		//Get result of the validation
		$ref_table_config=get_table_configuration('screening');
		$ref_table_config['current_operation']='list_screenings_validation'; //operation Defined in configuration for screening
		$res_screenings=$this->DBConnection_mdl->get_list_mdl($ref_table_config,'_',0,-1);

		//Verify matches and differences
		$screenings=array();
		$nbr_all=0;
		$nbr_matched=0;
		$i=1;
		foreach ($res_screenings['list'] as $key => $value) {
			if(!empty($papers[$value['paper_id']])  ){
				$screenings[$key]=array(
						'num'=>$i,
						//'paper'=>$papers[$value['paper_id']]['bibtexKey']." - ".$papers[$value['paper_id']]['title'],
						'paper'=>string_anchor('relis/manager/display_paper_screen/'.$value ['paper_id'],
								$papers[$value['paper_id']]['bibtexKey']." - ".$papers[$value['paper_id']]['title'],
								80),
							
						//'screening_decision'=>$papers[$value['paper_id']]['screening_status'],
						'validation_descision'=>$value['screening_decision']
							
				);
				if($screenings[$key]['validation_descision']==screening_validation_source_paper_status())
				{
					$nbr_matched++;
					$screenings[$key]['matched']='Yes';
				}else{
					$screenings[$key]['matched']='No';
				}
				$but[0]=array(
						'url'=> 'relis/manager/display_paper_screen/'.$value ['paper_id'],
						'label'=>icon('folder').' '.'View',
						'title'=>'Display'

				);

				//	$screenings[$key]['butt']=create_button_link_dropdown($but,lng_min('Action'));
				$nbr_all++;
				$i++;
			}
		}
		$match_percentage=0;
		if(!empty($nbr_all))
			$match_percentage=round($nbr_matched * 100 / $nbr_all,2);


			//Validation score per user
			$validation_score_user=$this->screening_validation_score();
			foreach ($validation_score_user as $key => $value) {
				if(!empty($value['all_papers'])){
					$percentage=round($value['matches'] * 100 / $value['all_papers'],2);
					//$validation_score_user[$key]['percentage']=$percentage;
					$validation_score_user[$key]['percentage_title']="$percentage % : ".$value['matches'] .' '.lng("matches out of") .' '. $value['all_papers'] ;
				}else{
					//$validation_score_user[$key]['percentage']='';
					$validation_score_user[$key]['percentage_title']='';
				}
				unset($validation_score_user[$key]['matches']);
				unset($validation_score_user[$key]['all_papers']);
			}
			if(!empty($validation_score_user)){
				array_unshift($validation_score_user, array('Reviewer','Score'));
			}

			$data['validation_score']=$validation_score_user;

			//print_test($validation_score_user);

			$data ['list']=$screenings;
			$data ['nombre']=count($screenings);
			$data['list_header']=array('#','Papers','Validation decision','Matched');

			$data ['top_buttons'] = get_top_button ( 'close', 'Close', 'home' );

			$data['result_page_title']=lng("General validation score")." -  $match_percentage  % :  $nbr_matched ".lng("matches out of")." $nbr_all ";
			$data['page_title']=lng("Validation Statistics");

			$data['page']='relis/validation_result';

			$this->load->view('body',$data);

	}


	function screening_validation_score($user='all',$screening_phase=0){

		if(empty($screening_phase))
			$screening_phase=active_screening_phase();


			//Get all users

			$users=$this->manager_lib->get_reference_select_values('users;user_name');


			//Get all screenings
			$sql="select * from screening_paper where assignment_role='Screening' AND screening_phase = $screening_phase AND screening_status='Done' AND   screening_active=1 ";

			$all_screenings = $this->db_current->query($sql)->result_array();

			//get all validations
			$sql="select * from screening_paper where assignment_role='Validation' AND screening_phase = $screening_phase AND screening_status='Done'  AND  screening_active=1 ";

			$all_validations = $this->db_current->query($sql)->result_array();

			//validation result per paper
			$papers_validation=array();
			foreach ($all_validations as $key => $value) {
				$papers_validation[$value['paper_id']]=$value;
			}



			$user_score=array();
			//get user score

			foreach ($all_screenings as $key_screen => $value_screen) {
				if(!empty($papers_validation[$value_screen['paper_id']]))//Verify if the paper have been assigned for validation
				{
					if(empty($user_score[$value_screen['user_id']])){
						$user_score[$value_screen['user_id']]['name']=!empty($users[$value_screen['user_id']])?$users[$value_screen['user_id']]:$value_screen['user_id'];
						$user_score[$value_screen['user_id']]['all_papers']=0;
						$user_score[$value_screen['user_id']]['matches']=0;
					}
					$user_score[$value_screen['user_id']]['all_papers']++;

					if($value_screen['screening_decision']==$papers_validation[$value_screen['paper_id']]['screening_decision']){
						$user_score[$value_screen['user_id']]['matches']++;
					}
				}
			}


			if($user=='all'){
				return $user_score;
			}else{
				if(!empty($user_score[$user])){
					return $user_score[$user];
				}else{
					return null;
				}
			}

	}

	public function qa_assignment_validation_set($data=""){
		//d

		//$sql="SELECT * from paper  where paper_active = 1 AND screening_status='Included' ";

		$papers_for_qa=$this->get_papers_for_qa_validation();

		//	print_test($papers_for_qa);

		$data['paper_list']=$papers_for_qa['papers_to_assign_display'];


		$user_table_config=get_table_configuration('users');
			
		$users=$this->DBConnection_mdl->get_list($user_table_config,'_',0,-1);
		$_assign_user=array();
		foreach ($users['list'] as $key => $value) {
			if( (user_project($this->session->userdata('project_id')  ,$value['user_id'])) AND can_review_project($value['user_id']) ){

				$_assign_user[$value['user_id']]=$value['user_name'];
			}
		}
		//	print_test($users);
		$data['users']=$_assign_user;
		$data['number_papers']=$papers_for_qa['count_papers_to_assign'];
		$data['number_papers_assigned']=$papers_for_qa['count_papers_assigned'];
		$data['percentage_of_papers']=get_appconfig_element('qa_validation_default_percentage');


		$data ['page_title'] = lng('Assign papers for quality assessment validation ');
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'home' );

		$data ['page'] = 'relis/assign_papers_qa_validation';

		//	print_test($papers_assigned_array);
		//print_test($data);
		//exit;
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}

	public function class_assignment_validation_set($data=""){


		$papers_for_qa=$this->get_papers_for_class_validation();

		//	print_test($papers_for_qa);

		$data['paper_list']=$papers_for_qa['papers_to_assign_display'];


		$user_table_config=get_table_configuration('users');
			
		$users=$this->DBConnection_mdl->get_list($user_table_config,'_',0,-1);
		$_assign_user=array();
		foreach ($users['list'] as $key => $value) {
			if( (user_project($this->session->userdata('project_id')  ,$value['user_id'])) AND can_review_project($value['user_id']) ){

				$_assign_user[$value['user_id']]=$value['user_name'];
			}
		}
		//	print_test($users);
		$data['users']=$_assign_user;
		$data['number_papers']=$papers_for_qa['count_papers_to_assign'];
		$data['number_papers_assigned']=$papers_for_qa['count_papers_assigned'];
		$data['percentage_of_papers']=get_appconfig_element('class_validation_default_percentage');


		$data ['page_title'] = lng('Assign papers for classification validation ');
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'home' );

		$data ['page'] = 'relis/assign_papers_class_validation';

		//	print_test($papers_assigned_array);
		//print_test($data);
		//exit;
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}


	public function qa_assignment_set($data=""){
		//d

		//$sql="SELECT * from paper  where paper_active = 1 AND screening_status='Included' ";

		$papers_for_qa=$this->get_papers_for_qa();

		//	print_test($papers_for_qa);

		$data['paper_list']=$papers_for_qa['papers_to_assign_display'];


		$user_table_config=get_table_configuration('users');
			
		$users=$this->DBConnection_mdl->get_list($user_table_config,'_',0,-1);
		$_assign_user=array();
		foreach ($users['list'] as $key => $value) {
			if( (user_project($this->session->userdata('project_id')  ,$value['user_id'])) AND can_review_project($value['user_id']) ){

				$_assign_user[$value['user_id']]=$value['user_name'];
			}
		}
		//	print_test($users);
		$data['users']=$_assign_user;
		$data['number_papers']=$papers_for_qa['count_papers_to_assign'];
		$data['number_papers_assigned']=$papers_for_qa['count_papers_assigned'];
		$data['percentage_of_papers']=100;


		$data ['page_title'] = lng('Assign papers for quality assessment ');
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'home' );

		$data ['page'] = 'relis/assign_papers_qa';

		//	print_test($papers_assigned_array);
		//print_test($data);
		//exit;
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}

	public function class_assignment_set($data=""){
		//d

		//$sql="SELECT * from paper  where paper_active = 1 AND screening_status='Included' ";

		$papers_for_qa=$this->get_papers_for_classification();

		//	print_test($papers_for_qa);

		$data['paper_list']=$papers_for_qa['papers_to_assign_display'];


		$user_table_config=get_table_configuration('users');
			
		$users=$this->DBConnection_mdl->get_list($user_table_config,'_',0,-1);
		$_assign_user=array();
		foreach ($users['list'] as $key => $value) {
			if( (user_project($this->session->userdata('project_id')  ,$value['user_id'])) AND can_review_project($value['user_id']) ){

				$_assign_user[$value['user_id']]=$value['user_name'];
			}
		}
		//	print_test($users);
		$data['users']=$_assign_user;
		$data['number_papers']=$papers_for_qa['count_papers_to_assign'];
		$data['number_papers_assigned']=$papers_for_qa['count_papers_assigned'];
		$data['percentage_of_papers']=100;


		$data ['page_title'] = lng('Assign papers for classification');
		$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'home' );

		$data ['page'] = 'relis/assign_papers_class';


		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view ( 'body', $data );
	}
	private function get_papers_for_classification(){
		//papers already assigned
		$papers_assigned = $this->db_current->order_by('assigned_id', 'ASC')
		->get_where('assigned', array('assigned_active'=>1,'assignment_type'=>'Classification'))
		->result_array();
		$papers_assigned_array=array();
		foreach ($papers_assigned as $key => $value) {
			$papers_assigned_array[$value['assigned_paper_id']]=$value['assigned_user_id'];
		}


		//all papers
		$all_papers = $this->db_current->order_by('id', 'ASC')
		->get_where('paper', array('paper_active'=>1,'classification_status'=>'To classify','paper_excluded'=>'0'))
		->result_array();


		$paper_to_assign=array();
		$paper_to_assign_display[0]=array('Key','Title');
		foreach ($all_papers as $key => $value) {
			if(empty($papers_assigned_array[$value['id']])){//exclude papers already assigned
				$paper_to_assign_display[$key+1]=array($value['bibtexKey'],$value['title']);
				$paper_to_assign[$key]=$value['id'];
			}
		}
		$result['count_all_papers']=count($all_papers);
		$result['count_papers_assigned']=count($papers_assigned_array);
		$result['count_papers_to_assign']=count($paper_to_assign);// we remove the header
		$result['papers_to_assign_display']=$paper_to_assign_display;
		$result['papers_to_assign']=$paper_to_assign;

		return $result;
	}

	private function get_papers_for_qa(){
		//papers already assigned
		$papers_assigned = $this->db_current->order_by('qa_assignment_id', 'ASC')
		->get_where('qa_assignment', array('qa_assignment_active'=>1,'assignment_type'=>'QA'))
		->result_array();
		$papers_assigned_array=array();
		foreach ($papers_assigned as $key => $value) {
			$papers_assigned_array[$value['paper_id']]=$value['assigned_to'];
		}


		//all papers
		$all_papers = $this->db_current->order_by('id', 'ASC')
		->get_where('paper', array('paper_active'=>1,'screening_status'=>'Included'))
		->result_array();


		$paper_to_assign=array();
		$paper_to_assign_display[0]=array('Key','Title');
		foreach ($all_papers as $key => $value) {
			if(empty($papers_assigned_array[$value['id']])){//exclude papers already assigned
				$paper_to_assign_display[$key+1]=array($value['bibtexKey'],$value['title']);
				$paper_to_assign[$key]=$value['id'];
			}
		}
		$result['count_all_papers']=count($all_papers);
		$result['count_papers_assigned']=count($papers_assigned_array);
		$result['count_papers_to_assign']=count($paper_to_assign);// we remove the header
		$result['papers_to_assign_display']=$paper_to_assign_display;
		$result['papers_to_assign']=$paper_to_assign;

		return $result;
	}
	private function get_papers_for_qa_validation(){
		//papers already assigned
		$papers_assigned = $this->db_current->order_by('qa_validation_assignment_id', 'ASC')
		->get_where('qa_validation_assignment', array('qa_validation_active'=>1))
		->result_array();
		$papers_assigned_array=array();
		foreach ($papers_assigned as $key => $value) {
			$papers_assigned_array[$value['paper_id']]=$value['assigned_to'];
		}


		//all papers
		$sql="SELECT P.* FROM paper P,qa_assignment Q WHERE P.id=Q.paper_id AND Q.qa_status='Done' AND P.paper_active=1 AND Q.qa_assignment_active=1 ";

		$all_papers = $this->db_current->query($sql)->result_array();


		$paper_to_assign=array();
		$paper_to_assign_display[0]=array('Key','Title');
		foreach ($all_papers as $key => $value) {
			if(empty($papers_assigned_array[$value['id']])){//exclude papers already assigned
				$paper_to_assign_display[$key+1]=array($value['bibtexKey'],$value['title']);
				$paper_to_assign[$key]=$value['id'];
			}
		}


		$result['count_all_papers']=count($all_papers);
		$result['count_papers_assigned']=count($papers_assigned_array);
		$result['count_papers_to_assign']=count($paper_to_assign);// we remove the header
		$result['papers_to_assign_display']=$paper_to_assign_display;
		$result['papers_to_assign']=$paper_to_assign;

		return $result;
	}

	private function get_papers_for_class_validation(){
		//papers already assigned
		$papers_assigned = $this->db_current->order_by('assigned_id', 'ASC')
		->get_where('assigned', array('assigned_active'=>1,'assignment_type'=>'Validation'))
		->result_array();
		$papers_assigned_array=array();
		foreach ($papers_assigned as $key => $value) {
			$papers_assigned_array[$value['assigned_paper_id']]=$value['assigned_user_id'];
		}


		//all papers
		//all papers
		$all_papers = $this->db_current->order_by('id', 'ASC')
		->get_where('view_paper_processed`', array('paper_active'=>1))
		->result_array();


		$paper_to_assign=array();
		$paper_to_assign_display[0]=array('Key','Title');
		foreach ($all_papers as $key => $value) {
			if(empty($papers_assigned_array[$value['id']])){//exclude papers already assigned
				$paper_to_assign_display[$key+1]=array($value['bibtexKey'],$value['title']);
				$paper_to_assign[$key]=$value['id'];
			}
		}


		$result['count_all_papers']=count($all_papers);
		$result['count_papers_assigned']=count($papers_assigned_array);
		$result['count_papers_to_assign']=count($paper_to_assign);// we remove the header
		$result['papers_to_assign_display']=$paper_to_assign_display;
		$result['papers_to_assign']=$paper_to_assign;

		return $result;
	}
	function qa_assignment_save(){

		$post_arr = $this->input->post ();
		//print_test($post_arr); exit;
		$users=array();
		$i=1;
		$percentage=intval($post_arr['percentage']);
		if(empty( $percentage)){
			$percentage=100;
		}


		// Get selected users
		While ($i <= $post_arr['number_of_users']) {
			if(!empty( $post_arr['user_'.$i])){
				array_push($users,$post_arr['user_'.$i]);
			}
			$i++;
		}
			
		//Verify if selected users is > of required reviews per paper
		if(count($users) < 1){

			$data['err_msg'] = lng('Please select at least one user  ');
			$this->qa_assignment_set($data);

		}else{


			$reviews_per_paper=1;

			$papers_all=$this->get_papers_for_qa();

			$papers=$papers_all['papers_to_assign'];

			//		print_test($papers);
			$papers_to_validate_nbr= round( count($papers) * $percentage/100);

			$operation_description="Assign  papers for QA";
			//	print_test($papers);
			shuffle($papers); // randomize the list
			//		print_test($papers);exit;
			//	print_test($papers);
			$assign_papers= array();
			$this->db2 = $this->load->database(project_db(), TRUE);
			$operation_code=active_user_id()."_".time();
			foreach ($papers as $key => $value) {
				if($key<$papers_to_validate_nbr)	{
					//$assign_papers[$key]['paper']=$value['id'];

					//$assign_papers[$key]['users']=array();


					$assignment_save=array(
							'paper_id'=>$value,
							'assigned_to'=>'',
							'assigned_by'=>active_user_id(),
							'operation_code'=>$operation_code,
							'assignment_mode'=>'auto',


					);
					$j=1;

					//the table to save assignments

					$table_name=get_table_configuration('qa_assignment','current','table_name');


					while($j<=$reviews_per_paper){



						$temp_user=($key % count($users)) + $j;

						if($temp_user >= count($users) )
							$temp_user = $temp_user - count($users);

							$assignment_save['assigned_to']=$users[$temp_user];
							//	print_test($assignment_save);

							$this->db2->insert($table_name,$assignment_save);


							$j++;
					}

				}
			}
			//exit;
			//	print_test();

			$operation_arr=array('operation_code'=>$operation_code,
					'operation_type'=>'assign_qa',
					'user_id'=>active_user_id(),
					'operation_desc'=>$operation_description

			);

			//print_test($operation_arr);
			$res2 = $this->manage_mdl->add_operation($operation_arr);


			set_top_msg('Operation completed');
			redirect('home');

		}

	}



	function class_assignment_save(){

		$post_arr = $this->input->post ();
		//print_test($post_arr); exit;
		$users=array();
		$i=1;
		$percentage=intval($post_arr['percentage']);
		if(empty( $percentage)){
			$percentage=100;
		}


		// Get selected users
		While ($i <= $post_arr['number_of_users']) {
			if(!empty( $post_arr['user_'.$i])){
				array_push($users,$post_arr['user_'.$i]);
			}
			$i++;
		}
			
		//Verify if selected users is > of required reviews per paper
		if(count($users) < 1){

			$data['err_msg'] = lng('Please select at least one user  ');
			$this->qa_assignment_set($data);

		}else{


			$reviews_per_paper=1;

			$papers_all=$this->get_papers_for_classification();

			$papers=$papers_all['papers_to_assign'];

			//		print_test($papers);
			$papers_to_validate_nbr= round( count($papers) * $percentage/100);

			$operation_description="Assign  papers for classification";
			//	print_test($papers);
			shuffle($papers); // randomize the list
			//		print_test($papers);exit;
			//	print_test($papers);
			$assign_papers= array();
			$this->db2 = $this->load->database(project_db(), TRUE);
			$operation_code=active_user_id()."_".time();
			foreach ($papers as $key => $value) {
				if($key<$papers_to_validate_nbr)	{
					//$assign_papers[$key]['paper']=$value['id'];

					//$assign_papers[$key]['users']=array();


					$assignment_save=array(
							'assigned_paper_id'=>$value,
							'assigned_user_id'=>'',
							'assigned_by'=>active_user_id(),
							'operation_code'=>$operation_code,
							'assignment_mode'=>'auto',


					);
					$j=1;

					//the table to save assignments

					$table_name=get_table_configuration('assignation','current','table_name');


					while($j<=$reviews_per_paper){



						$temp_user=($key % count($users)) + $j;

						if($temp_user >= count($users) )
							$temp_user = $temp_user - count($users);

							$assignment_save['assigned_user_id']=$users[$temp_user];
							//	print_test($assignment_save);

							$this->db2->insert($table_name,$assignment_save);


							$j++;
					}

				}
			}
			//exit;
			//	print_test();

			$operation_arr=array('operation_code'=>$operation_code,
					'operation_type'=>'assign_class',
					'user_id'=>active_user_id(),
					'operation_desc'=>$operation_description

			);

			//print_test($operation_arr);
			$res2 = $this->manage_mdl->add_operation($operation_arr);


			set_top_msg('Operation completed');
			redirect('home');

		}

	}
	function qa_validation_assignment_save(){

		$post_arr = $this->input->post ();
		//print_test($post_arr); exit;
		$users=array();
		$i=1;
		$percentage=intval($post_arr['percentage']);
		if(empty( $percentage)){

			$data['err_msg'] = lng(' Please provide  "Percentage of papers" ');
			$this->qa_assignment_validation_set($data);

		}elseif($percentage>100 OR $percentage<=0){
			$data['err_msg'] = lng("Please provide a correct value of percentage");
			$this->qa_assignment_validation_set($data);
		}
		else{

			// Get selected users
			While ($i <= $post_arr['number_of_users']) {
				if(!empty( $post_arr['user_'.$i])){
					array_push($users,$post_arr['user_'.$i]);
				}
				$i++;
			}

			//Verify if selected users is > of required reviews per paper
			if(count($users) < 1){

				$data['err_msg'] = lng('Please select at least one user  ');
				$this->qa_assignment_validation_set($data);

			}else{


				$reviews_per_paper=1;

				$papers_all=$this->get_papers_for_qa_validation();

				$papers=$papers_all['papers_to_assign'];

				//		print_test($papers);
				$papers_to_validate_nbr= round( count($papers) * $percentage/100);

				$operation_description="Assign  papers for QA validation";
				//	print_test($papers);
				shuffle($papers); // randomize the list
				//		print_test($papers);exit;
				//	print_test($papers);
				$assign_papers= array();
				$this->db2 = $this->load->database(project_db(), TRUE);
				$operation_code=active_user_id()."_".time();
				foreach ($papers as $key => $value) {
					if($key<$papers_to_validate_nbr)	{


						$assignment_save=array(
								'paper_id'=>$value,
								'assigned_to'=>'',
								'assigned_by'=>active_user_id(),
								'operation_code'=>$operation_code,
								'assignment_mode'=>'auto',


						);
						$j=1;

						//the table to save assignments

						$table_name=get_table_configuration('qa_validation_assignment','current','table_name');


						while($j<=$reviews_per_paper){



							$temp_user=($key % count($users)) + $j;

							if($temp_user >= count($users) )
								$temp_user = $temp_user - count($users);

								$assignment_save['assigned_to']=$users[$temp_user];
								//	print_test($assignment_save);

								$this->db2->insert($table_name,$assignment_save);


								$j++;
						}

					}
				}
				//exit;
				//	print_test();

				$operation_arr=array('operation_code'=>$operation_code,
						'operation_type'=>'assign_qa_validation',
						'user_id'=>active_user_id(),
						'operation_desc'=>$operation_description

				);

				//print_test($operation_arr);
				$res2 = $this->manage_mdl->add_operation($operation_arr);


				set_top_msg('Operation completed');
				redirect('home');

			}
		}
	}


	function class_validation_assignment_save(){

		$post_arr = $this->input->post ();
		//print_test($post_arr); exit;
		$users=array();
		$i=1;
		$percentage=intval($post_arr['percentage']);
		if(empty( $percentage)){

			$data['err_msg'] = lng(' Please provide  "Percentage of papers" ');
			$this->class_assignment_validation_set($data);

		}elseif($percentage>100 OR $percentage<=0){
			$data['err_msg'] = lng("Please provide a correct value of percentage");
			$this->class_assignment_validation_set($data);
		}
		else{

			// Get selected users
			While ($i <= $post_arr['number_of_users']) {
				if(!empty( $post_arr['user_'.$i])){
					array_push($users,$post_arr['user_'.$i]);
				}
				$i++;
			}

			//Verify if selected users is > of required reviews per paper
			if(count($users) < 1){

				$data['err_msg'] = lng('Please select at least one user  ');
				$this->class_assignment_validation_set($data);

			}else{


				$reviews_per_paper=1;

				$papers_all=$this->get_papers_for_class_validation();

				$papers=$papers_all['papers_to_assign'];

				//		print_test($papers);
				$papers_to_validate_nbr= round( count($papers) * $percentage/100);

				$operation_description="Assign  papers for qa";
				//	print_test($papers);
				shuffle($papers); // randomize the list
				//		print_test($papers);exit;
				//	print_test($papers);
				$assign_papers= array();
				$this->db2 = $this->load->database(project_db(), TRUE);
				$operation_code=active_user_id()."_".time();
				foreach ($papers as $key => $value) {
					if($key<$papers_to_validate_nbr)	{


						$assignment_save=array(
								'assigned_paper_id'=>$value,
								'assigned_user_id'=>'',
								'assigned_by'=>active_user_id(),
								'operation_code'=>$operation_code,
								'assignment_mode'=>'auto',
								'assignment_type'=>'Validation',

						);
						$j=1;

						//the table to save assignments

						$table_name=get_table_configuration('assignation','current','table_name');


						while($j<=$reviews_per_paper){



							$temp_user=($key % count($users)) + $j;

							if($temp_user >= count($users) )
								$temp_user = $temp_user - count($users);

								$assignment_save['assigned_user_id']=$users[$temp_user];
								//	print_test($assignment_save);

								$this->db2->insert($table_name,$assignment_save);


								$j++;
						}

					}
				}
				//exit;
				//	print_test();

				$operation_arr=array('operation_code'=>$operation_code,
						'operation_type'=>'assign_class_validation',
						'user_id'=>active_user_id(),
						'operation_desc'=>$operation_description

				);

				//print_test($operation_arr);
				$res2 = $this->manage_mdl->add_operation($operation_arr);


				set_top_msg('Operation completed');
				redirect('home');

			}
		}
	}

	function qa_conduct_list($type="mine",$id=0,$status='all'){


		$data =$this->get_qa_result($type,$id,'QA',True,$status);
		//print_test($data);
		if($type=='id' AND !empty($id)){
			$data ['top_buttons'] = get_top_button ( 'close', 'Close', 'relis/manager/qa_conduct_result' );
		}else{
			$data ['top_buttons'] = get_top_button ( 'close', 'Close', 'home' );

		}
		$this->session->set_userdata('after_save_redirect',"relis/manager/qa_conduct_list/$type/$id/$status");
		if($type=='excluded'){
			$data['page_title']=lng("Quality assessment  - papers excluded");
		}else{
			$data['page_title']=lng("Quality assessment ").(($status=='pending'||$status=='done')?" - $status":'');
		}

		$data['page']='relis/quality_assessment';


		$this->load->view('body',$data);
	}
	function qa_conduct_list_val($type="mine",$id=0,$status='all'){


		$data =$this->get_qa_result($type,$id,'QA_Val',FALSE,$status);
		//print_test($data);
		if($type=='id' AND !empty($id)){
			$data ['top_buttons'] = get_top_button ( 'close', 'Close', 'op/entity_list/list_qa_validation' );
		}else{
			$data ['top_buttons'] = get_top_button ( 'close', 'Close', 'home' );

		}
		$this->session->set_userdata('after_save_redirect',"relis/manager/qa_conduct_list_val/$type/$id/$status");
		if($type=='excluded'){
			$data['page_title']=lng("Quality assessment validation - papers excluded");
		}else{
			$data['page_title']=lng("Quality assessment validation").(($status=='pending'||$status=='done')?" - $status":'');
		}

		$data['page']='relis/quality_assessment_validation';


		$this->load->view('body',$data);
	}

	function qa_conduct_detail($id=0){


		$data =$this->get_qa_result('id',$id);

		$Included=true;
		foreach ($data['qa_list'] as $key => $value) {
			if($value['status']=='Excluded_QA')
				$Included=false;
		}
		//	print_test($data);
		$data ['top_buttons']="";
		if(! project_published() AND can_manage_project()){
			if($Included){
				$data ['top_buttons'].=get_top_button ( 'all', "Exclude the paper", 'relis/manager/qa_exlusion/'.$id ,'Exclude'," fa-minus",'','btn-danger' )." ";
					
			}else{
				$data ['top_buttons'].= get_top_button ( 'all', 'Cancel the exclusion', 'relis/manager/qa_exlusion/'.$id."/0" , 'Cancel the exclusion'," fa-undo",'','btn-dark')." "  ;

			}
		}
		$data ['top_buttons'] .= get_top_button ( 'close', 'Close', 'relis/manager/qa_conduct_result' );

		$this->session->set_userdata('after_save_redirect',"relis/manager/qa_conduct_detail/$id");

		$data['page_title']=lng("Quality assessment");

		$data['page']='relis/quality_assessment';


		$this->load->view('body',$data);
	}

	function qa_conduct_result($type="all"){

		//$type="all";
		$data =$this->get_qa_result($type);
		//print_test($data);
		$qa_cutt_off_score=get_appconfig_element('qa_cutt_off_score');
		$data['qa_cutt_off_score']=$qa_cutt_off_score;
		//print_test($data);
		$data ['top_buttons']="";


		if(! project_published() AND can_manage_project() AND $type=='all'){
			$data ['top_buttons'].=get_top_button( 'all', "Exclude low quality papers",
					'relis/manager/qa_exclude_low_quality_validation' ,'Exclude low quality',
					" fa-minus",'','btn-danger' )." ";
		}

			
		$data ['top_buttons'] .= get_top_button ( 'close', 'Close', 'home' );


		if($type=='excluded'){
			$data['page_title']=lng("Quality assessment - excluded papers ")." : ".lng('Cut-off score')." : $qa_cutt_off_score ";
		}else{
			$data['page_title']=lng("Result of quality assessment ")." - ".lng('Cut-off score')." : $qa_cutt_off_score ";
		}
		$data['page']='relis/quality_assessment_result';

		$this->load->view('body',$data);
	}

	private function get_qa_result($type="mine",$id=0,$category='QA',$add_Link=True,$status='all'){

		return $this->manager_lib->get_qa_result($type,$id,$category,$add_Link,$status);

			
	}






	private function get_qa_result_old($type="mine",$id=0,$category='QA',$add_Link=True,$status='all'){
		//print_test($type);
		//get qa results
		$qa_result = $this->db_current->order_by('qa_id', 'ASC')
		->get_where('qa_result', array('qa_active'=>1))
		->result_array();

		//Put result in searchable array
		$array_qa_result=array();
		foreach ($qa_result as $key_result => $v_result) {
			$array_qa_result[$v_result['paper_id']][$v_result['question']][$v_result['response']]=1;
		}
		//print_test($qa_result);
		//	print_test($array_qa_result);
		//get_assignments

		if($type=='id' AND !empty($id)){
			$extra_condition=" AND paper_id= '".$id."' ";
		}elseif($type=='all'){
			$extra_condition=" AND screening_status='Included' ";
		}elseif($type=='excluded'){
			$extra_condition=" AND screening_status='Excluded_QA' ";
		}else{
			$extra_condition=" AND screening_status='Included'  AND assigned_to= '".active_user_id()."' ";
		}


		if($category=='QA_Val'){

			if($status=='pending'){
				$extra_condition.=" AND Q.validation IS NULL";
			}elseif($status=='done'){
				$extra_condition.=" AND Q.validation IS NOT NULL";
			}

			$sql="SELECT Q.*,Q.	qa_validation_assignment_id as assignment_id,Q.validation as status,P.title FROM qa_validation_assignment Q,paper P where Q.paper_id=P.id AND 	qa_validation_active=1 AND paper_active=1 $extra_condition ";
		}else{

			if($status=='pending'){
				$extra_condition.=" AND Q.qa_status	='Pending'";
			}elseif($status=='done'){
				$extra_condition.=" AND Q.qa_status ='Done'";
			}
			$sql="SELECT Q.*,Q.qa_assignment_id as assignment_id,P.title,P.screening_status as status FROM qa_assignment Q,paper P where Q.paper_id=P.id AND qa_assignment_active=1 AND paper_active=1 $extra_condition ";
		}

		//echo $sql;
		$assignments =$this->db_current->query($sql)->result_array();


		$qa_questions = $this->db_current->order_by('question_id', 'ASC')
		->get_where('qa_questions', array('question_active'=>1))
		->result_array();

		$qa_responses = $this->db_current->order_by('score', 'DESC')
		->get_where('qa_responses', array('response_active'=>1))
		->result_array();

		//print_test($assignments);
		//	print_test($qa_questions);
		//	print_test($qa_responses);
		$users= $this->manager_lib->get_reference_select_values('users;user_name',FALSE,False);



		$all_qa=array();
		$all_qa_html=array();
		$paper_completed=0;
		foreach ($assignments as $key_assign => $v_assign) {


			$all_qa[$v_assign['assignment_id']]=array(
					'paper_id'=>$v_assign['paper_id'],
					'title'=>$v_assign['title'],
					'status'=>$v_assign['status'],
					'user'=>!empty($users[$v_assign['assigned_to']])?$users[$v_assign['assigned_to']]:'',
					'user_id'=>!empty($users[$v_assign['assigned_to']])?$v_assign['assigned_to']:'',

			);
			$questions=array();
			$q_result_score=0;
			$q_done=0;
			$q_pending=0;


			foreach ($qa_questions as $k_question => $v_question) {
				$questions[$v_question['question_id']]=array(
						'question'=>$v_question,
				);
				$responses=array();
				$q_result=!empty($array_qa_result[$v_assign['paper_id']][$v_question['question_id']])?1:0;
				$question_asw=0;
				foreach ($qa_responses as $k_response => $v_response) {
					if(empty($array_qa_result[$v_assign['paper_id']][$v_question['question_id']][$v_response['response_id']])){//see if the response have been chosed for the question
						$res=0;
						if($add_Link)
							$link="relis/manager/qa_conduct_save/$q_result/".$v_assign['paper_id'].'/'.$v_question['question_id'].'/'.$v_response['response_id'];
							else
								$link="";
					}else{
						$res=1;
						$link="";
						$q_result_score+=$v_response['score'];
						$question_asw=1;

					}
					$responses[$v_response['response_id']]=array(
							'response'=>$v_response,
							'result'=>$res,
							'link'=>$link,
					);


				}
				$questions[$v_question['question_id']]['responses']=$responses;
				$questions[$v_question['question_id']]['q_result']=$q_result;
				if($question_asw){
					$q_completed=1;
					$q_done++;
				}else{
					$q_completed=0;
					$q_pending++;
				}
				$questions[$v_question['question_id']]['completed']=$q_completed;
					
			}

			$all_qa[$v_assign['assignment_id']]['q_result_score']=$q_result_score;;
			$all_qa[$v_assign['assignment_id']]['questions']=$questions;
			$paper_done=0;
			if(empty($q_pending)){
				$paper_done=1;
				$paper_completed++;
			}
			$all_qa[$v_assign['assignment_id']]['paper_done']=$paper_done;

		}


		$data ['qa_list']=$all_qa;
		$data ['paper_completed']=$paper_completed;

		return $data;
	}

	function qa_conduct_save($update,$paper_id,$question,$response){
		$qa_result=array(
				'paper_id'=>$paper_id,
				'question'=>$question,
				'response'=>$response,
				'done_by'=>active_user_id()
		);

		if(!$update){

			$this->db_current->insert('qa_result',$qa_result);
		}else{

			$this->db_current->update('qa_result',$qa_result,array('paper_id'=>$paper_id,'question'=>$question));
		}

		$after_after_save_redirect=$this->session->userdata('after_save_redirect');

		if(!empty($after_after_save_redirect)){
			$this->session->set_userdata('after_save_redirect','');

		}else{
			$after_after_save_redirect="relis/manager/qa_conduct_list";
		}

		//update assignment
		if($this->qa_done_for_paper($paper_id)){
			$this->db_current->update('qa_assignment',array('qa_status'=>'Done'),array('paper_id'=>$paper_id));
		}else{
			//$this->db_current->update('qa_assignment',array('qa_status'=>'Pending'),array('paper_id'=>$paper_id));
		}
		header("Location: ".base_url().$after_after_save_redirect.'.html#paper_'.$paper_id);
		die();

	}



	//Verify if all questions have been answered for the paper
	private function qa_done_for_paper($paper_id){

		$sql="SELECT COUNT(*) AS nbr FROM
		qa_questions Q LEFT JOIN qa_result R ON(Q.question_id=R.question AND R.qa_active=1 AND R.paper_id=$paper_id)
		WHERE Q.question_active=1 AND paper_id IS NULL  ";

		$result =$this->db_current->query($sql)->row_array();
		//print_test($result);
		if(empty($result['nbr'])){
			return TRUE;					//all questions have been responded
		}else{
			return FALSE;
		}

	}


	function qa_exlusion($paper_id,$op=1){

		if($op==1){
			$this->db_current->update('paper',array('screening_status'=>'Excluded_QA','classification_status'=>'Waiting'),array('id'=>$paper_id));
		}else{
			$this->db_current->update('paper',array('screening_status'=>'Included','classification_status'=>'To classify'),array('id'=>$paper_id));
		}

		$after_after_save_redirect="relis/manager/qa_conduct_result";
		redirect($after_after_save_redirect);

	}
	//exclude all papers with low quality
	function qa_exclude_low_quality(){
		//s
		$qa_result =$this->get_qa_result('all');
		//print_test($qa_result);
		$qa_cutt_off_score=get_appconfig_element('qa_cutt_off_score');
		$excluded=0;
		if(!empty($qa_result['qa_list'])){
			foreach ($qa_result['qa_list'] as $key => $value) {
				if($value['q_result_score']<$qa_cutt_off_score){
					$this->db_current->update('paper',
							array('screening_status'=>'Excluded_QA',
									'classification_status'=>'Waiting'),
							array('id'=>$value['paper_id']));
					$excluded ++;

				}
			}
		}
		if($excluded>0){
			set_top_msg("Completed ".$excluded ." paper(s) excluded");
		}
		else {
			set_top_msg("No paper to exclude!");
		}

		$after_after_save_redirect="relis/manager/qa_conduct_result";
		redirect($after_after_save_redirect);

	}

	function qa_exclude_low_quality_validation(){

		$data ['page'] = 'install/frm_install_result';
		//$data['left_menu_admin']=True;

		$data['array_warning']=array('You want to delete All papers with low quality : The opération cannot be undone !');
		$data['array_success']=array();
		$data ['next_operation_button']="";



		$data ['page_title'] = lng('Exclude low quality papers');


		$data ['next_operation_button'] =" &nbsp &nbsp &nbsp". get_top_button ( 'all', 'Continue to delete', 'relis/manager/qa_exclude_low_quality','Continue','','',' btn-success ',FALSE );
		$data ['next_operation_button'] .= get_top_button ( 'all', 'Cancel', 'relis/manager/qa_conduct_result','Cancel','','',' btn-danger ',FALSE );


		$this->load->view ( 'body', $data );


	}


	function qa_validate($paper_id,$op=1){

		if($op==1){
			$this->db_current->update('qa_validation_assignment',array('validation'=>'Correct','validation_note'=>'','validation_time'=>bm_current_time()),array('paper_id'=>$paper_id));
		}else{


			$assignment = $this->db_current->get_where('qa_validation_assignment',
					array('qa_validation_active'=>1,'paper_id'=>$paper_id))
					->row_array();

					if(!empty($assignment['qa_validation_assignment_id'])){
						redirect('op/edit_element/qa_not_valid/'.$assignment['qa_validation_assignment_id']);
					}
					//$this->db_current->update('qa_validation_assignment',array('validation'=>'Not Correct'),array('paper_id'=>$paper_id));

		}

		if(!empty($after_after_save_redirect)){
			$this->session->set_userdata('after_save_redirect','');

		}else{
			$after_after_save_redirect="relis/manager/qa_conduct_list_val";
		}


		header("Location: ".base_url().$after_after_save_redirect.'.html#paper_'.$paper_id);
		die();

	}

	function class_validate($paper_id,$op=1){

		if($op==1){
			$this->db_current->update('assigned',array('validation'=>'Correct','validation_note'=>'','validation_time'=>bm_current_time()),array('assigned_paper_id'=>$paper_id));
		}else{

			//Get_assignment_id

			$assignment = $this->db_current->get_where('assigned',
					array('assigned_active'=>1,'assignment_type'=>'Validation','assigned_paper_id'=>$paper_id))
					->row_array();

					//print_test($assignment); exit;
					//$this->db_current->update('assigned',array('validation'=>'Not Correct','validation_time'=>bm_current_time()),array('assigned_paper_id'=>$paper_id));
					if(!empty($assignment['assigned_id'])){
						redirect('op/edit_element/class_not_valid/'.$assignment['assigned_id']);
					}
		}


		$after_after_save_redirect="op/entity_list/list_class_validation";

		redirect($after_after_save_redirect);
	}



	public function get_screen_for_kappa(){
			
			
		$screening_phase_info=active_screening_phase_info();
		$current_phase=active_screening_phase();
		//	print_test($screening_phase_info);
			
		$sql="select paper_id,user_id,screening_decision
		FROM screening_paper
		WHERE  assignment_mode='auto' AND  screening_status='done' AND screening_phase = $current_phase AND screening_active=1";
		//	echo $sql;
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

			

		$matrice= $this->get_screen_for_kappa();

		if(empty($matrice)){
			return 0 ;
		}else{
			//print_test($matrice);

			$N=count($matrice);
			$k=count($matrice[0]);
			$n=0;
			foreach ($matrice[0] as $key => $value) {
				$n+=$value;
			}


			//print_test($N);
			//print_test($n);
			//print_test($k);

			if($n==1){
				$kappa='one user';
			}else{
				$p=array();
					
				for ($j = 0; $j < $k; $j++) {
					$p[$j]=0.0;
					for ($i = 0; $i < $N; $i++) {
						$p[$j]=$p[$j]+$matrice[$i][$j];
					}

					$p[$j]=$p[$j]/($N*$n);
				}
					
				//	print_test($p);
					
					
				$P=array();
				for ($j = 0; $j < $N; $j++) {
					$P[$j]=0.0;
					for ($i = 0; $i < $k; $i++) {
						$P[$j]=$P[$j] + ($matrice[$j][$i] * $matrice[$j][$i] );
					}

					$P[$j]=($P[$j]-$n) / ($n*($n-1));
				}
					
				//	print_test($P);
					
				$Pbar = array_sum ($P) / $N;
					
				//	print_test($Pbar);
				$PbarE=0.0;
				foreach ($p as $key => $value) {
					$PbarE+= $value*$value;
				}
					
				//print_test($PbarE);

				//added to avoid division by zero
				if($PbarE==1)
					$PbarE=2;

					$kappa=($Pbar - $PbarE)/(1-$PbarE);

					$kappa=round($kappa,2);
			}
			return  $kappa;
		}
		//	print_test($kappa);
	}

	public function kappa_meaning($kappa){
		$interpretation = '';
		if ($kappa < 0)$interpretation = 'Poor';
		elseif( 0.01 <= $kappa AND $kappa <= 0.2)
		$interpretation = 'Slight';
		elseif( 0.21 <= $kappa AND $kappa <= 0.4) $interpretation = 'Fair';
		elseif( 0.41 <= $kappa AND $kappa <= 0.6) $interpretation = 'Moderate';
		elseif( 0.61 <= $kappa AND $kappa <= 0.8) $interpretation = 'Substantial';
		elseif( 0.81 <= $kappa AND $kappa < 1) $interpretation = 'Almost perfect';
		elseif( $kappa >= 1) $interpretation = 'Perfect';
		elseif( $kappa == 'one user') $interpretation = 'Just one participant';
		else $interpretation= 'something went wrong...';


		return $interpretation;

	}

	/*
	 * Page pour ajouter un papier avec bibtex
	 */
	public function add_paper_bibtex($data=array()){
			
		$data ['top_buttons'] = get_top_button ( 'close', 'Back', 'op/entity_list/list_all_papers' );
		$data['title']='Add BibTeX';

		$data['page']='relis/bibtex_form';
		$this->load->view('body',$data);
	}

	//save paper from bibtex


	private function get_bibler_result($bibtex,$operation="single"){


		//clean the bibtex content
		$bibtex=strstr($bibtex,'@');
		$error=1;
		$error_msg="";
		$paper_array=array();
		$paper_preview_sucess=array();//for import only
		$paper_preview_exist=array();//for import only
		$paper_preview_error=array();//for import only
		$init_time=microtime ();
		$i=1;
		$res="init";
		while($i<10){ //up to ten attempt to connect to server if the connection does not work
			if($operation =='endnote'){
                $res=$this->biblerproxy_lib->importendnotestringforrelis($bibtex);
			}
			else{
				$res=$this->biblerproxy_lib->importbibtexstringforrelis($bibtex);
			}


			$correct=False;
			//if there is an error messag in the result retry
			if (strpos($res, 'Internal Server Error') !== false OR empty($res) ){
				$i++;
			}else{
				//if there no error messag in the result retry
				$correct=True;
				$i=20;
			}
			//usleep(500);

		}

		$end_time=microtime ();
		ini_set('auto_detect_line_endings',TRUE);
		if($correct){
			$Tres = json_decode($res,True);
			if (json_last_error() === JSON_ERROR_NONE) {

				if($operation =='single' ){
				    //for single add just consider the first element
                    if( !empty ($Tres['papers'][0])){
                        $Tres = $Tres['papers'][0];
                    }

					$result['bibtext']=$bibtex;
					$paper_array=array();
                    if(!empty($Tres['result_code'])
                            AND !empty($Tres['entry']['entrykey'])){
                                $error=0;

                                $year=!empty($Tres['entry']['year']) ? $Tres['entry']['year'] : "";

                                $paper_array['bibtexKey']=str_replace('\\', '', $Tres['entry']['entrykey']);
                                $title=!empty($value['entry']['title']) ? $value['entry']['title'] : "";
                                $title=str_replace('{', '', $title);
                                $title=str_replace('\\', '', $title);
                                $paper_array['title']=str_replace('}', '', $title);
                                $paper_array['preview']=!empty($Tres['preview']) ? $Tres['preview'] : "";
                                $paper_array['bibtex']=!empty($Tres['bibtex']) ? $Tres['bibtex']: "";
                                $paper_array['abstract']=!empty($Tres['entry']['abstract']) ? $Tres['entry']['abstract'] : "";
                                $paper_array['doi']=!empty($Tres['entry']['paper']) ? $Tres['entry']['paper'] : "";
                                $paper['venue']=!empty($value['venue_full']) ? $value['venue_full'] : "";
                                $paper_array['year']=$year;
                                $paper_array['authors']=!empty($Tres['authors']) ? $Tres['authors']: "";

                    }else{
                        $msg = (!empty($Tres['result_msg']) ? $Tres['result_msg'] : "");
                        $error_msg.="Error: check your Bibtext .<br/>". $msg ;

                    }
				}else{
					if(empty($Tres['error']) AND !empty( $Tres['papers'] )){


						//exit;
						$paper=array();
						$i_ok=1;
						$i_ok_pupli=1;
						$i_Nok=1;



						foreach ($Tres['papers'] as $key => $value) {
							if(!empty($value['entry']['entrykey'])){
								if(!empty($value['result_code'])){

									$error=0;

									$year=!empty($value['entry']['year']) ? $value['entry']['year'] : "";

									/*	if(!empty($value['venue_full'])){
									 $venue_id=$this->add_venue($value['venue_full'],$year);
									 $paper['venueId']=$venue_id;
									 }*/
									$paper['bibtexKey']=str_replace('\\', '', $value['entry']['entrykey']);
									$title=!empty($value['entry']['title']) ? $value['entry']['title'] : "";
									$title=str_replace('{', '', $title);
									$title=str_replace('\\', '', $title);
									$paper['title']=str_replace('}', '', $title);
									$paper['preview']=!empty($value['preview']) ? $value['preview'] : "";
									$paper['bibtex']=!empty($value['bibtex']) ? $value['bibtex']: "";
									$paper['abstract']=!empty($value['entry']['abstract']) ? $value['entry']['abstract'] : "";
									$paper['doi']=!empty($value['entry']['paper']) ? $value['entry']['paper'] : "";
									$paper['venue']=!empty($value['venue_full']) ? $value['venue_full'] : "";
									$paper['year']=$year;
									$paper['authors']=!empty($value['authors']) ? $value['authors']: "";

									array_push($paper_array, $paper);
									
									if ($this->paper_exist($paper)){
										array_push($paper_preview_exist, array('i'=>$i_ok_pupli,'key'=>$paper['bibtexKey'],'preview'=>$paper['preview']));
										$i_ok_pupli++;
									}else{
										array_push($paper_preview_sucess, array('i'=>$i_ok,'key'=>$paper['bibtexKey'],'preview'=>$paper['preview']));
										$i_ok++;
									}
									
								}else{
									$preview=!empty($value['preview']) ? $value['preview'] : "";
									$bibtexKey=!empty($value['bibtexKey']) ? str_replace('\\', '', $value['entry']['entrykey']) : "";
									array_push($paper_preview_error, array('i'=>$i_Nok,'key'=>$bibtexKey,'preview'=>$preview,
											'msg'=> $value['result_msg'] ));
									$i_Nok++;
								}
							}
						}
					}else{
						$error_msg.="Error: No papers found.<br/>";
						$error=0;
					}

					//$paper_array=$Tres;
				}

			} else{

				$json_error="";
				switch (json_last_error()) {
					case JSON_ERROR_NONE:
						$json_error= 'No errors';
						break;
					case JSON_ERROR_DEPTH:
						$json_error= 'Maximum stack depth exceeded';
						break;
					case JSON_ERROR_STATE_MISMATCH:
						$json_error= 'Underflow or the modes mismatch';
						break;
					case JSON_ERROR_CTRL_CHAR:
						$json_error= 'Unexpected control character found';
						break;
					case JSON_ERROR_SYNTAX:
						$json_error= 'Syntax error, malformed JSON';
						break;
					case JSON_ERROR_UTF8:
						$json_error= 'Malformed UTF-8 characters, possibly incorrectly encoded';
						break;
					default:
						$json_error= 'Unknown error';
						break;
				}

				$error_msg.="JSON Error : ".$json_error.".<br/>";

			}

		}else{
			$error_msg.="Unable to connect to Bibler web service.<br/>";
			$this->add_paper_bibtex($data);
		}
		$result['error']=$error;
		$result['error_msg']=$error_msg;
		$result['paper_array']=$paper_array;
		$result['paper_preview_sucess']=$paper_preview_sucess;
		$result['paper_preview_exist']=$paper_preview_exist;
		$result['paper_preview_error']=$paper_preview_error;
		return $result;

	}


	public function  save_bibtex_paper(){
		$post_arr = $this->input->post ();
		$data['message_error']="";
		$data['message_success']="";
		if(empty($post_arr['bibtext']))
		{
			$data['message_error'].="Bibtex field empty.<br/>";
			$this->add_paper_bibtex($data);
		}else
		{
			$bibtex=$post_arr['bibtext'];

			$bibtex_result=$this->get_bibler_result($bibtex);
			//	print_r($bibtex_result);
			if(!empty($bibtex_result['bibtext']))
			{
				$data['bibtext']=$bibtex_result['bibtext'];
			}
			//	print_test($bibtex_result);exit;
			if(empty($bibtex_result['error']) AND !empty($bibtex_result)){
				$insert_res=$this->insert_paper_bibtext($bibtex_result['paper_array']);
				if($insert_res==1){
					$data['message_success'].="Paper added";
				}else{
					$data['message_error'].=$insert_res;
				}
			}else{
				$data['message_error'].=$bibtex_result['error_msg'];
			}

			$this->add_paper_bibtex($data);



		}

	}


	public function  save_bibtex_paper_saved(){


		$post_arr = $this->input->post ();
		$data['message_error']="";
		$data['message_success']="";
		if(empty($post_arr['bibtext']))
		{
			$data['message_error'].="Bibtex field empty.<br/>";
			$this->add_paper_bibtex($data);
		}else
		{
			$bibtex=$post_arr['bibtext'];



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
				$correct=False;
				if (strpos($res, 'Internal Server Error') !== false OR empty($res) ){

					//	echo " error - ".$i;
					$i++;
				}else{
					//	echo " ok - ".$i;
					$correct=True;
					$i=20;
				}
				//usleep(500);

			}

			$end_time=microtime ();
			//print_test($res);
			//	echo "<h1>".($end_time - $init_time)."</h1>";
			ini_set('auto_detect_line_endings',TRUE);
			if($correct){
				//print_test($res);
				$res=str_replace("True,", "'True',", $res);
				$res=str_replace("False,", "'False',", $res);
				$res = $this->biblerproxy_lib->fixJSON($res);

				//tou correct the error in venu from the webservice
				//$res=substr($res,0,strpos($res,', "venue_full":')).'}';
					
				$Tres = json_decode($res,True);
					
				if (json_last_error() === JSON_ERROR_NONE) {


					//print_test($Tres);

					$data['bibtext']=$bibtex;

					$paper_array=array();
					if(!empty($Tres['result_code'])
							//	AND $Tres['result_code']=='True'
							AND !empty($Tres['entry']['entrykey'])){
									
								//bibtex decoded
								$year=!empty($Tres['entry']['year']) ? $Tres['entry']['year'] : "";
								if(!empty($Tres['venue_full'])){

									$venue_id=$this->add_venue($Tres['venue_full'],$year);
									$paper_array['venueId']=$venue_id;
								}
								$paper_array['bibtexKey']=$Tres['entry']['entrykey'];
								$paper_array['title']=!empty($Tres['entry']['title']) ? $Tres['entry']['title'] : "";
								$paper_array['preview']=!empty($Tres['preview']) ? $Tres['preview'] : "";
								$paper_array['bibtex']=!empty($Tres['bibtex']) ? $Tres['bibtex']: "";
								$paper_array['abstract']=!empty($Tres['entry']['abstract']) ? $Tres['entry']['abstract'] : "";
								$paper_array['doi']=!empty($Tres['entry']['paper']) ? $Tres['entry']['paper'] : "";
								$paper_array['year']=$year;
								$paper_array['authors']=!empty($Tres['authors']) ? $Tres['authors']: "";


									
								$insert_res=$this->insert_paper_bibtext($paper_array);
								if($insert_res==1){
									$data['message_success'].="Paper added";
								}else{
									$data['message_error'].=$insert_res;
								}
					}else{
						$data['message_error'].="Error: chect your Bibtext format.<br/>";

					}

					$this->add_paper_bibtex($data);
				} else {

					//echo json_last_error();
					$json_errodr="";
					switch (json_last_error()) {
						case JSON_ERROR_NONE:
							$json_error= 'No errors';
							break;
						case JSON_ERROR_DEPTH:
							$json_error= 'Maximum stack depth exceeded';
							break;
						case JSON_ERROR_STATE_MISMATCH:
							$json_error= 'Underflow or the modes mismatch';
							break;
						case JSON_ERROR_CTRL_CHAR:
							$json_error= 'Unexpected control character found';
							break;
						case JSON_ERROR_SYNTAX:
							$json_error= 'Syntax error, malformed JSON';
							break;
						case JSON_ERROR_UTF8:
							$json_error= 'Malformed UTF-8 characters, possibly incorrectly encoded';
							break;
						default:
							$json_error= 'Unknown error';
							break;
					}


					$data['message_error'].="JSON Error : ".$json_error.".<br/>";
					$this->add_paper_bibtex($data);
				}

			}else{
				$data['message_error'].="Unable to connect to Bibler web service.<br/>";
				$this->add_paper_bibtex($data);
					
			}


		}

	}
	
	private function paper_exist($paper_array){
		$bibtexKey=$paper_array['bibtexKey'];
		$exist=False;
		$stopsearch=False;
		$i=1;
		while(!$stopsearch){
			$res = $this->db_current->query('SELECT * FROM paper WHERE BINARY bibtexKey = BINARY "'.$bibtexKey.'" and  paper_active=1')->row_array();
			if(empty($res)){
				$stopsearch=True;
				$exist=False;
			}else{
				if($res['title']==$paper_array['title']){
					$stopsearch=True;
					$exist=True;
				}else{
					$bibtexKey=$paper_array['bibtexKey'].'_'.$i;
				}
		
			}
			$i++;
		}
		
		return $exist;
	}

	private function insert_paper_bibtext($paper_array) {
		//check papers_exist
		//print_test($paper_array);
		$authors=$paper_array['authors'];
		unset($paper_array['authors']);

		$bibtexKey=$paper_array['bibtexKey'];
		$exist=False;
		$stopsearch=False;
		$i=1;
		while(!$stopsearch){
			$res = $this->db_current->query('SELECT * FROM paper WHERE BINARY bibtexKey = BINARY "'.$bibtexKey.'" and  paper_active=1')->row_array();
			if(empty($res)){
				$stopsearch=True;
				$exist=False;
			}else{
				if($res['title']==$paper_array['title']){
					$stopsearch=True;
					$exist=True;
				}else{
					$bibtexKey=$paper_array['bibtexKey'].'_'.$i;
				}

			}
			$i++;
		}


		if(!$exist){

			//add venue
			if(!empty($paper_array['venue'])){
				$venue_id=$this->add_venue($paper_array['venue'],$paper_array['year']);
				$paper_array['venueId']=$venue_id;
					
			}
			unset($paper_array['venue']);
			$paper_array['added_by']=active_user_id();
			$paper_array['bibtexKey']=$bibtexKey;
			//set classification status
			if(get_appconfig_element('screening_on')){

				$paper_array['classification_status']='Waiting';
				$paper_array['screening_status']='Pending';
			}else{
				$paper_array['classification_status']='To classify';

				$paper_array['screening_status']='Included';
			}
			$this->db_current->insert('paper', $paper_array);
			$paper_id=$this->db_current->insert_id();
			if(!empty($authors)){
				$this->add_author($paper_id, $authors);
					
			}
			return 1;
		}else{
			return 'Paper already exit';
		}
		//print_test($res);
	}

	private function add_author($paper_id,$author_array) {
		//check author exist
		foreach ($author_array as $key => $author) {

			$author_name=$author['first_name'].' '.$author['last_name'];
			$res = $this->db_current->query('SELECT * FROM author WHERE BINARY author_name = BINARY "'.$author_name.'" and  author_active=1')->row_array();


			//print_test($res);
			if(empty($res['author_id'])){
					
				$this->db_current->insert('author', array('author_name'=>$author_name));
				$author_id=$this->db_current->insert_id();
			}else{
				$author_id=$res['author_id'];
			}

			if(!empty($author_id)){
					
				$this->db_current->insert('paperauthor',
						array(	'paperId'=>$paper_id,
								'authorId'=>$author_id,
								'author_rank'=>$key+1,
						));
			}
			//print_test($res);
	 }

	}

	private function add_venue($venue,$year=0) {



		$res = $this->db_current->get_where('venue',
				array('venue_fullName' =>$venue,'venue_active'=>1))
				->row_array();

				$array_venue=array('venue_fullName'=>$venue);
				if(!empty($year)){
					$array_venue['venue_year']=$year;
				}
				if(empty($res['venue_id'])){

					$this->db_current->insert('venue', $array_venue);
					return $venue_id=$this->db_current->insert_id();
				}else{
					return $res['venue_id'];
				}



	}




}
