<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
class DBConnection_mdl extends CI_Model
{	
	function __construct()
	{
		parent::__construct();
	}
	
	
	
	/*
	 * Vérification de la validité du login et password de l'utilisateur et retours des informations sur l'utilisateur
	 * 
	 * Input: un array avec login et password
	 * Output: un array avec les caractéristiques de l'utilisateur
	 */
		
	function check_user_credentials($user_credentials){
	
		$data=$this->db->query ( "CALL check_user_credentials('".$user_credentials['user_username']."','".md5($user_credentials['user_password'])."') " );
		
		mysqli_next_result( $this->db->conn_id );
		$result = $data->row_array();
		
		return $result;
	
	}
	
	
	
	/*
	 * Fonction pour appeler la procédure stockée qui récupère la liste d'éléments suivant les paramètres reçus
	 * Input: $ref_table_config: le nom donné à le stucture de la table à récuperer
	 * 		$val: contien un critère de recherche di il y en a si non contient '_'
	 * 		$page : retourner le liste à partir de quel element?
	 * 		$rec_per_page : nombre d'elements à recupérer
	 * 		$extra_condition : autre critères de recherche
	 * 		
	 */	

		function get_list($ref_table_config,$val='_',$page=0,$rec_per_page=0,$extra_condition=''){
				
			
			$config=$ref_table_config['config_label'];
			
			if(!admin_config($config)){
				$this->db2 = $this->load->database(project_db(), TRUE);
			}
			
			
			if($val!='_'){
				$search=$val;
			}else{
				$search="";
			}
			
			if(admin_config($config)){
				$data=$this->db->query ( "CALL get_list_".$config."(0,0,'".$search."') " );
				mysqli_next_result( $this->db->conn_id );
				
			}else{
				$data=$this->db2->query ( "CALL get_list_".$config."(0,0,'".$search."') " );
				mysqli_next_result( $this->db2->conn_id );			
			}
			
			
			$result['nombre']=$data->num_rows();

			
			if($rec_per_page==0){
				$rec_per_page=$this->config->item('rec_per_page');
			}elseif($rec_per_page==-1){
				$rec_per_page=0;
			}
			
			if(admin_config($config)){
				$data=$this->db->query ( "CALL get_list_".$config."(".$page.",".$rec_per_page.",'".$search."') " );		
				mysqli_next_result( $this->db->conn_id );
			}else{
				$data=$this->db2->query ( "CALL get_list_".$config."(".$page.",".$rec_per_page.",'".$search."') " );
				mysqli_next_result( $this->db2->conn_id );
			}
			
			$result['list']=$data->result_array();
			
		
			return $result;
		
		}
		
		

		/*
		 * Fonction pour appeler la procédure stockée qui récupère la liste des chaines de caractère suivant la langue
		 * Input: $ref_table_config: le nom donné à le stucture de la table à récuperer
		 * 		$val: contien un critère de recherche di il y en a si non contient '_'
		 * 		$page : retourner le liste à partir de quel element?
		 * 		$rec_per_page : nombre d'elements à recupérer
		 * 		$language : la langue recherché
		 *
		 */
		
		function get_list_str_mng($ref_table_config,$val='_',$page=0,$rec_per_page=0,$language='en'){
		
			$this->db2 = $this->load->database(project_db(), TRUE);
			$config=$ref_table_config['config_label'];
				
				
			if($val!='_'){
				$search=$val;
			}else{
				$search="";
			}
		
			$data=$this->db2->query ( "CALL get_list_str_mng(0,0,'".$search."','".$language."') " );
			mysqli_next_result( $this->db2->conn_id );
			$result['nombre']=$data->num_rows();
		
				
			if($rec_per_page==0){
				$rec_per_page=$this->config->item('rec_per_page');
			}elseif($rec_per_page==-1){
				$rec_per_page=0;
			}
				
			$data=$this->db2->query ( "CALL get_list_".$config."(".$page.",".$rec_per_page.",'".$search."','".$language."') " );
		
			mysqli_next_result( $this->db2->conn_id );
			$result['list']=$data->result_array();
				
		
			return $result;
		
		}
		
		
		/*
		 * Fonction pour appeler la procédure stockée qui récupère les intentions pour une classification donnée
		 * INPUT : $classification_id: l'identifiant de la classification
		 */
		function get_classification_intents($classification_id){

			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL getMTIntents(".$classification_id.") " );
			
			mysqli_next_result( $this->db2->conn_id );
			$result=$data->result_array();
				
			
			return $result;
		}
		
		
		/*
		 * Fonction pour récupérer la liste des utilisateurs
		 */
		function get_users_all(){

			
			$data=$this->db->query ( "CALL get_list_users_all() " );
			
			mysqli_next_result( $this->db->conn_id );
			$result=$data->result_array();
				
			
			return $result;
		}
				
		
		/*
		 * Fonction pour retourner le nombre de papiers suivant la catégorie(all,pending,processed, ...)
		 */
		function count_papers($paper_cat="all"){
			
			$excluded='_';
			if($paper_cat=="excluded"){
				$excluded = 1;
			}else{
			
				$excluded = 0;
			}
			
			
			$search=NULL;
			
			
				
			if($paper_cat=="processed"){
				
				$stored_proc_count =" CALL count_papers_processed('".$search."')";
			}elseif($paper_cat=="pending"){
					
				
				$stored_proc_count =" CALL count_papers_pending('".$search."')";
			}elseif($paper_cat=="assigned_me"){
					
			
				$user_assigned_id= $this->session->userdata('user_id');
			
				$stored_proc_count =" CALL count_papers_assigned(".$user_assigned_id.",'".$search."')";
			}elseif($paper_cat=="screen"){
					
				$stored_proc_count =" CALL count_papers('".$search."','".$excluded."')";
					
			}else{
					
				$stored_proc_count =" CALL count_papers_class('".$search."','".$excluded."')";
					
			}
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( $stored_proc_count );
			
			mysqli_next_result( $this->db2->conn_id );
			$res=$data->row_array();
			if(!empty($res['nbr'])){
				$result=$res['nbr'];
			}else{
				$result=0;
			}
			
			
			return $result;
			//print_test($result); exit;
		}
		
		/*
		 * Fonction pour exclure un papier
		 */
		function exclude_paper($id) {
			$this->db2 = $this->load->database(project_db(), TRUE);
			$result=$this->db2->query ( "CALL exclude_paper(".$id.") " );
		
			return $result;
		}
		
		
		/*
		 * Fonction pour inclure un papier qui était exclus
		 */
		function include_paper($id) {
			$this->db2 = $this->load->database(project_db(), TRUE);
			$result=$this->db2->query ( "CALL include_paper(".$id.") " );
				
			
			return $result;
		}
		
		/*
		 * Fonction pour pour récupérer les personnés à qui un papier est assigné
		 */
		function get_assignations($paper_id){
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL get_assignations(".$paper_id.") " );
			
			mysqli_next_result( $this->db2->conn_id );
			$results=$data->result_array();
			
			return $results;
		}
		
		
		/*
		 * Fonction pour pour récupérer les caractéristiques du'un papier associé à une classification
		 */
		function get_classification_paper($classification_id){
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL get_classification_paper(".$classification_id.") " );
			mysqli_next_result( $this->db2->conn_id );
		
			$res=$data->row_array();
			print_test($res);
				
			if(!empty($res))
				return $res['class_paper_id'];
			else
				return 0;
				
		
		}
		
		
		
		function get_classification_scheme(){
		
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL get_classification_scheme() " );
			
			mysqli_next_result( $this->db2->conn_id );
			$results=$data->result_array();
			
			return $results;
		}
		
		
		/*
		 * Fonction pour récupérer la classification d'un papier
		 */
		function get_classifications($paper_id){
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL get_classifications(".$paper_id.") " );
				
			mysqli_next_result( $this->db2->conn_id );
			$results=$data->result_array();
				
			return $results;
			
			
		}
		
		
		/*
		 * Fonction pour récupérer les informations sur l'exclusion d'un papier
		 */
		function get_exclusion($paper_id){
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL get_paper_exclusion_info(".$paper_id.") " );
				
			mysqli_next_result( $this->db2->conn_id );
			
			$results=$data->row_array();
		
			return $results;
		}
		
		
		/*
		 * Fonction pour récupérer le nom de la table utilisé pas une table de reference
		 */
		function get_reference_corresponding_table($ref_config){
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL get_reference_table('".$ref_config."') " );
				
			mysqli_next_result( $this->db2->conn_id );
			
			$results=$data->row_array();
			return $results;
		
		}
		
		
		/*
		 * Fonction pour récupérer le detail d'un élément de la table de reference
		 */
		function get_reference_details($table_name,$table_id,$ref_id) {
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL get_row('".$table_name."','".$table_id."','".$ref_id."') " );
				
			mysqli_next_result( $this->db2->conn_id );
			
			$results=$data->row_array();
			
			//print_test($results);
			return $results;
		}
		
		/*
		 * Fonction pour récupérer le détail d'un élément de la table de référence
		 */
		function get_row_details($config,$ref_id,$stored_procedure_provided=False,$tab_config="") {
			if(empty($tab_config)){
				$tab_config=$config;
			}
			
			
			
			if(!admin_config($tab_config)){
				$this->db2 = $this->load->database(project_db(), TRUE);
			
			}
			
			
			$stored_procedure=$stored_procedure_provided?$config:"get_detail_".$config;
			
			if(admin_config($tab_config)){
				$data=$this->db->query ( "CALL ".$stored_procedure."('".$ref_id."') " );				
				mysqli_next_result( $this->db->conn_id );
				
			}else{
				$data=$this->db2->query ( "CALL ".$stored_procedure."('".$ref_id."') " );				
				mysqli_next_result( $this->db2->conn_id );
			}
			
				
			$results=$data->row_array();
				
			//print_test($results);
			return $results;
		}
		
		
		
		/*
		 * Fonction pour récuperer la liste de papiers suivant la catégorie
		 */
		function get_papers($paper_cat="all",$ref_table_config,$val='_',$page=0,$rec_per_page=0){
				
		
			if($rec_per_page==0){
				$rec_per_page=$this->config->item('rec_per_page');
			}elseif($rec_per_page==-1){
				$rec_per_page=0;
			}
			
			
			$excluded='_';
			if($paper_cat=="excluded"){
				$excluded = 1;
			}else{
		
				$excluded = 0;
			}
				
			if($val!='_'){
			 $search=trim($val);
			}else{
				$search=NULL;
			}
		
			
			if($paper_cat=="processed"){
				$stored_proc_list =" CALL get_list_papers_processed(".$page.",".$rec_per_page.",'".$search."')";
				$stored_proc_count =" CALL count_papers_processed('".$search."')";
			}elseif($paper_cat=="pending"){
			
				$stored_proc_list =" CALL get_list_papers_pending(".$page.",".$rec_per_page.",'".$search."')";
				$stored_proc_count =" CALL count_papers_pending('".$search."')";
			}elseif($paper_cat=="assigned_me"){
			
				
				$user_assigned_id= $this->session->userdata('user_id');
				
				$stored_proc_list =" CALL get_list_papers_assigned(".$user_assigned_id.",".$page.",".$rec_per_page.",'".$search."')";
				$stored_proc_count =" CALL count_papers_assigned(".$user_assigned_id.",'".$search."')";
			}elseif($paper_cat=="screen"){
			
				$stored_proc_list =" CALL get_list_papers(".$page.",".$rec_per_page.",'".$search."','".$excluded."')";
				$stored_proc_count =" CALL count_papers('".$search."','".$excluded."')";
			
			}else{
			
				$stored_proc_list =" CALL get_list_papers_class(".$page.",".$rec_per_page.",'".$search."','".$excluded."')";
				$stored_proc_count =" CALL count_papers_class('".$search."','".$excluded."')";
			
			}
			$this->db2 = $this->load->database(project_db(), TRUE);
			
			$data=$this->db2->query ( $stored_proc_count );
				
			mysqli_next_result( $this->db2->conn_id );
			$res=$data->row_array();
			if(!empty($res['nbr'])){
				$result['nombre']=$res['nbr'];
			}else{
				$result['nombre']=0;
			}
		
			$data=$this->db2->query ( $stored_proc_list );
			
			mysqli_next_result( $this->db2->conn_id );
			$result['list']=$data->result_array();
			
			return $result;
				
				
		}
		
		/*
		 * Fonction pour récupérer les valeurs à mettre dans un select box pour un element donnée;
		 * $ref_table_config : configuration de la table de l'élément
		 * $ref_table_field: le champs concerné
		 * $extra_condition: critère de recherche
		 */
		function get_reference_select_values($ref_table_config,$ref_table_field,$extra_condition=""){
		
			$extra_condition=str_replace("'", "\'", $extra_condition);
			
			$sql_append=" AND ".$ref_table_config['table_active_field']."=1 ".$extra_condition;
		
		
		
			if(empty($ref_table_config['order_by']))
			{
				$order_by="";
			}else{
				$order_by= " ORDER BY ".$ref_table_config['order_by'];
			}
			$config=$ref_table_config['config_label'];
				
			if(!admin_config($config)){
				$this->db2 = $this->load->database(project_db(), TRUE);
			}
			
			if(admin_config($config)){
				$data=$this->db->query ( "CALL get_list('".$ref_table_config['table_name']."','".$ref_table_config['table_id']." AS refId,".$ref_table_field." AS refDesc ',' ".$sql_append.$order_by." ') " );		
				mysqli_next_result( $this->db->conn_id );
			}else{
				$data=$this->db2->query ( "CALL get_list('".$ref_table_config['table_name']."','".$ref_table_config['table_id']." AS refId,".$ref_table_field." AS refDesc ',' ".$sql_append.$order_by." ') " );
				mysqli_next_result( $this->db2->conn_id );
				
			}
			
			$result = $data->result_array();
		
			return $result;
				
		
		}
		
		
		/*
		 * Fonction pour récupérer la liste des tables de réferences
		 */
		function get_reference_tables_list($target_db='current'){
	
			$target_db=($target_db=='current')?project_db():$target_db;
			
			$this->db2 = $this->load->database($target_db, TRUE);
			$data=$this->db2->query ( "CALL get_reference_tables_list() " );

			mysqli_next_result( $this->db2->conn_id );
			$result = $data->result_array();
			//print_test($result);
			return $result;
		}
		
		
		/*
		 * Fonction pour supprimer un élément
		 */
		function remove_element($id,$config,$strored_procedure_provided=False) {
			if($strored_procedure_provided){
				$strored_procedure=$config;
			}else{
				$strored_procedure=" remove_".$config;
			}
			if(!admin_config($config)){
				$this->db2 = $this->load->database(project_db(), TRUE);
			}
			
			
			if(admin_config($config)){
				$result=$this->db->query ( "CALL ".$strored_procedure."(".$id.")" );
			}else{
				$result=$this->db2->query ( "CALL ".$strored_procedure."(".$id.")" );
			}
			
			return $result;
		}
		
		
		/*
		 * Fonction pour appeler les procedures stockées utilisé pour suvegarder un nouvel élément, ou un élément modifier
		 * INPUT : $content : un tableux avec la stucture de la tables et les info à mettre
		 * $type: sortie attendue: Id de lélement enregistré ou resultat de la requête d'insertion ou de modification
		 */
		function save_reference($content,$type='normal') {
				
			
			$config=$content['table_config'];
			if(admin_config($config)){
				$this->db3 = $this->load->database('default', TRUE);
			}else{
				$this->db3 = $this->load->database(project_db(), TRUE);
			}
				
			$table_config=get_table_config($config);
			
			
			if($content['operation_type']=='new'){
			$param="";
			$i=0;
				
				foreach ($table_config['fields'] as $key => $value) {
					
				
					if(($value['on_add']!='not_set' AND $value['on_add']!='drill_down' AND $value['on_add']!='disabled') AND !((isset($value['multi-select']) AND isset($value['multi-select'])=='Yes'))){
						if(!empty($content[$key])){
							$val=$content[$key];
						}else{
							$val=NULL;
						}
						if($i==0){
							
							$param.= "'".mysqli_real_escape_string($this->db3->conn_id,$val) . "'";
							
								
						}
						else{
							$param.= ",'".mysqli_real_escape_string($this->db3->conn_id,$val) . "'";
							
						}
						$i=1;
					}
				}
			$stored_procedure=" CALL add_".$config."($param)";
				
			}else{
				$param="";
				$i=0;
				
				foreach ($table_config['fields'] as $key => $value) {
						
					
					if(($value['on_edit']!='not_set' AND $value['on_edit']!='drill_down' AND $value['on_edit']!='disabled') AND !((isset($value['multi-select']) AND isset($value['multi-select'])=='Yes'))){
						if(!empty($content[$key])){
							$val=$content[$key];
						}else{
							$val=NULL;
						}
						if($i==0){

							if(isset($value['input_type']) AND $value['input_type']=='date' AND empty($val)){
								$param.= "NULL" ;
							}else{
								$param.= "'".mysqli_real_escape_string($this->db3->conn_id,$val) . "'";
							}
							
								
				
						}
						else{
							
							
							if(isset($value['input_type']) AND $value['input_type']=='date' AND empty($val)){
								$param.= ", "."NULL" ;
							}else{
								$param.= ",'".mysqli_real_escape_string($this->db3->conn_id,$val) . "'";
							}
						}
						$i=1;
					}
				}
				$id=$content[$table_config['table_id']];
				$stored_procedure=" CALL update_".$config."($id , $param)";
			}
			
			//echo $stored_procedure;
			//exit;
			if (  $content ['operation_type']=='new' ) {
		
				$data=$this->db3->query ( $stored_procedure);
				//echo $this->db->last_query();
				mysqli_next_result( $this->db3->conn_id );
				
				
				$res=$data->row_array();
				if(!empty($res)){
					$result=1;
					$id=$res['id_value'];
				}else{
					$result=0;
					$id=0;
				}
					
		
			} else {
				if($this->db3->simple_query ( $stored_procedure )){
					$result=1;
				}else{
					$result=0;
				}
			}
			
			
			//print_test($result); exit;
			
			if($type=='get_id'){
				return $id;
			}else{
				return $result;
			}
		}
		
		function save_reference_mdl($content,$type='normal') {
		
				
			$config=$content['table_config'];
			if(admin_config($config)){
				$this->db3 = $this->load->database('default', TRUE);
			}else{
				$this->db3 = $this->load->database(project_db(), TRUE);
			}
		
			
			//print_test($content);
			
			//exit;
			$table_config=get_table_configuration($config);
			//print_test($table_config);
			$current_operation=$content['current_operation'];
			$param="";
			$i=0;
			
			foreach ($table_config['operations'][$current_operation]['fields'] as $key => $v_field) {
			
				$value=$table_config['fields'][$key];
				if( $v_field['field_state']!='drill_down' AND $v_field['field_state']!='disabled' AND !((isset($value['multi-select']) AND isset($value['multi-select'])=='Yes'))){
					//print_test($key);
					if(!empty($content[$key])){
						$val=$content[$key];
					}else{
						$val=NULL;
					}
					if($i==0){
			
						if(isset($value['input_type']) AND $value['input_type']=='date' AND empty($val)){
							$param.= "NULL" ;
						}else{
							$param.= "'".mysqli_real_escape_string($this->db3->conn_id,$val) . "'";
						}
						
			
			
					}
					else{
						if(isset($value['input_type']) AND $value['input_type']=='date' AND empty($val)){
							$param.= ", "."NULL" ;
						}else{
							$param.= ",'".mysqli_real_escape_string($this->db3->conn_id,$val) . "'";
			
					}
					
				}
				$i=1;
			}
			
			}
			
			if($content['operation_type']=='new'){
				
				if(!empty($table_config['operations'][$current_operation]['db_save_model'])){
					$stored_procedure=" CALL ".$table_config['operations'][$current_operation]['db_save_model']."($param)";
				}else{
					$stored_procedure=" CALL add_".$config."($param)";
				}
			}else{
				
				
				$id=$content[$table_config['table_id']];
				
				if(!empty($table_config['operations'][$current_operation]['db_save_model'])){
					$stored_procedure=" CALL ".$table_config['operations'][$current_operation]['db_save_model']."($id ,$param)";
				}else{
					$stored_procedure=" CALL update_".$config."($id , $param)";
				}
				//$stored_procedure=" CALL update_".$config."($id , $param)";
			}
				
			//echo $stored_procedure;
			//exit;
			if (  $content ['operation_type']=='new' ) {
		
				$data=$this->db3->query ( $stored_procedure);
				//echo $this->db->last_query();
				mysqli_next_result( $this->db3->conn_id );
		
		
				$res=$data->row_array();
				if(!empty($res)){
					$result=1;
					$id=$res['id_value'];
				}else{
					$result=0;
					$id=0;
				}
					
		
			} else {
				if($this->db3->simple_query ( $stored_procedure )){
					$result=1;
				}else{
					$result=0;
				}
			}
				
				
			//print_test($result); exit;
				
			if($type=='get_id'){
				return $id;
			}else{
				return $result;
			}
		}
		
		/*
		 * Fonction pour récupérer la correspondance d'une chaine de caractère dans une langue donnée
		 */
		function get_str($str,$category="default",$lang='en'){
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL get_string('".$str."','".$category."','".$lang."')" );
			mysqli_next_result( $this->db2->conn_id );
			$result=$data->row_array();
			return $result;
		}
		
		/*
		 * Fonction pour ajouter la correspondance d'une chaine de caractère dans une langue donnée
		 */
		function set_str($str,$category="default",$lang='en'){
			$this->db2 = $this->load->database(project_db(), TRUE);
			$data=$this->db2->query ( "CALL add_string('','".$str."','".$str."','".$lang."','".$category."')" );
			mysqli_next_result( $this->db2->conn_id );
			$res=$data->row_array();
			if(!empty($res)){
				$result=1;
		
			}else{
				$result=0;
		
			}
		
			return $result;
		}
		
		
		/*
		 * Fonction pour vérifier si un nom d'utilisateur est déjà utilisé ou pas
		 */
		function login_available($login){
		
			
			$data=$this->db->query ( "CALL check_login('".$login."')" );
			
			mysqli_next_result( $this->db->conn_id );
			$result=$data->row_array();
			if($result['number']>0){
				return false;
			}else{
				return true;
		
			}
			
		}
		
		
		/*
		 * Fonction pour récuperer les champs supplementaires à afficher dans la liste des classification (Scope, Intent, Intent relation)
		 */
		function get_extra_fields($class_id ){
		
			$this->db2 = $this->load->database(project_db(), TRUE);
			//scope
			$result['class_scope']=" - ";
			$result['class_intent']=" - ";
			$result['class_intent_relation']=" - ";
		
			$data=$this->db2->query ( "CALL getMTScope('".$class_id."')" );
			mysqli_next_result( $this->db2->conn_id );
			$res=$data->result_array();
			
			$i=1;
			foreach ($res as $key => $value) {
				if($i==1)
					$result['class_scope']=$value['ref_value'];
				else
					$result['class_scope'].=" | ".$value['ref_value'];
					
				$i++;
			}
		
		
			
			$data=$this->db2->query ( "CALL getMTIntents('".$class_id."')" );
			mysqli_next_result( $this->db2->conn_id );
			$res=$data->result_array();
			
			$i=1;
			foreach ($res as $key => $value) {
				if($i==1)
					$result['class_intent']=$value['ref_value'];
				else
					$result['class_intent'].=" | ".$value['ref_value'];
					
				$i++;
			}
		
			
			
			$data=$this->db2->query ( "CALL getMTRelation('".$class_id."')" );
			mysqli_next_result( $this->db2->conn_id );
			$res=$data->result_array();
				
			$i=1;
			foreach ($res as $key => $value) {
				if($i==1)
					$result['class_intent_relation']=$value['ref_value'];
				else
					$result['class_intent_relation'].=" | ".$value['ref_value'];
		
				$i++;
			}
		
		
		
			return $result;
		
		
		
		}
		
		

		/*
		 * Fonction pour appeler la procédure stockée qui récupère la liste d'éléments suivant les paramètres reçus
		 * Input: $ref_table_config: le nom donné à le stucture de la table à récuperer
		 * 		$val: contien un critère de recherche di il y en a si non contient '_'
		 * 		$page : retourner le liste à partir de quel element?
		 * 		$rec_per_page : nombre d'elements à recupérer
		 * 		$extra_condition : autre critères de recherche
		 *
		 */
		
		function get_list_mdl($ref_table_config,$val='_',$page=0,$rec_per_page=0,$extra_condition=''){
		
			$current_operation=	$ref_table_config['current_operation'];
			
			
			$stored_procedure=$ref_table_config['operations'][$current_operation]['data_source'];
			$extra_parameters="";
			if(!empty($ref_table_config['operations'][$current_operation]['conditions'])){
				foreach ($ref_table_config['operations'][$current_operation]['conditions'] as $key_cond => $condition) {
					
					if(!$condition['add_on_generation']){
						
						$extra_parameters.=" , '".$condition['value']."'";
				
					}
				}
			}
			
			
			$config=$ref_table_config['config_label'];
				
			if(!admin_config($config)){
				$this->db2 = $this->load->database(project_db(), TRUE);
			}
				
				
			if($val!='_'){
				$search=$val;
			}else{
				$search="";
			}
			
			
			if(admin_config($config)){
				$data=$this->db->query ( "CALL ".$stored_procedure."(0,0,'".$search."' ".$extra_parameters.") " );
				mysqli_next_result( $this->db->conn_id );
		
			}else{
				$data=$this->db2->query ( "CALL ".$stored_procedure."(0,0,'".$search."' ".$extra_parameters.") " );
				mysqli_next_result( $this->db2->conn_id );
			}
				
				
			$result['nombre']=$data->num_rows();
		
				
			if($rec_per_page==0){
				$rec_per_page=$this->config->item('rec_per_page');
			}elseif($rec_per_page==-1){
				$rec_per_page=0;
			}
				
			if(admin_config($config)){
				$data=$this->db->query ( "CALL ".$stored_procedure."(".$page.",".$rec_per_page.",'".$search."' ".$extra_parameters.") " );
				mysqli_next_result( $this->db->conn_id );
			}else{
				$data=$this->db2->query ( "CALL ".$stored_procedure."(".$page.",".$rec_per_page.",'".$search."' ".$extra_parameters.") " );
				mysqli_next_result( $this->db2->conn_id );
			}
				
			$result['list']=$data->result_array();
				
		
			return $result;
		
		}
		
		
	
}