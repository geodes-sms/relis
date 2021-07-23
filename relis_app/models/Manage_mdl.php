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
class Manage_mdl extends CI_Model
{	
	function __construct()
	{
		parent::__construct();
	}
		
	
		
		function get_list($ref_table_config,$val='_',$page=0,$rec_per_page=0,$extra_condition=''){
				
			if(admin_config($ref_table_config,FALSE)){
				$this->db3 = $this->load->database('default', TRUE);
			}else{
				$this->db3 = $this->load->database(project_db(), TRUE);
			}
			//$extra_condition=str_replace("'", "\'", $extra_condition);
			$sql_append=" AND ".$ref_table_config['table_active_field']."=1 ".$extra_condition;
		
				
			if($val!='_'){
				if(!empty($ref_table_config['search_by']))
				{
		
					$fields=explode(",",$ref_table_config['search_by']);
		
					$i=0;
					$sql_append.=" AND ( ";
					foreach ($fields as $field_name) {
						if($i==0){
							$sql_append.= " ($field_name LIKE '%".trim($val)."%' ) ";}
							else{
								$sql_append.= " OR ($field_name LIKE '%".trim($val)."%' ) ";}
								$i=1;
					}
		
					$sql_append.=" ) ";
		
		
				}
			}
			$sql_append=str_replace("'", "\'", $sql_append);
		
			if(empty($ref_table_config['order_by']))
			{
				$order_by="";
			}else{
				$order_by= " ORDER BY ".$ref_table_config['order_by'];
			}
		
			
			$data=$this->db3->query ( "CALL count_list('".$ref_table_config['table_name']."',' ".$sql_append." ') " );
				
			mysqli_next_result( $this->db3->conn_id );
			$result=$data->row_array();
		
			if($rec_per_page==0){
				$rec_per_page=$this->config->item('rec_per_page');
			}
			
			if($rec_per_page == -1)
				$rec_per_page = 400000;
		
		//	$data=$this->db3->query ( "CALL get_list('".$ref_table_config['table_name']."','".$ref_table_config['view_list_fields']."',' ".$sql_append.$order_by." LIMIT $page ,".$rec_per_page." ') " );
			$data=$this->db3->query ( "CALL get_list('".$ref_table_config['table_name']."','*',' ".$sql_append.$order_by." LIMIT $page ,".$rec_per_page." ') " );
		
			mysqli_next_result( $this->db3->conn_id );
			$result['list']=$data->result_array();
				
			return $result;
		
		}
		
		/*
		function count_papers($paper_cat="all"){
			
			$result=$this->DBConnection_mdl->count_papers($paper_cat);
				
				
			return $result;
			/*
			
			if($paper_cat=="processed"){
				$table="view_paper_processed";
			}elseif($paper_cat=="pending"){
			
				$table="view_paper_pending";
			}elseif($paper_cat=="assigned_me"){
			
				$table="view_paper_assigned";
			
				
				$this->db->where('assigned_user_id', $this->session->userdata('user_id'));
			}else{
			
				$table='paper';
			}
			
			
			if($paper_cat=="excluded"){
				$this->db->where('paper_excluded', 1);
				
			}else{
			
				$this->db->where('paper_excluded', 0);
			}
			
			
			$this->db->where('paper_active', 1);
			
			$this->db->from($table);
			
			$res= $this->db->count_all_results();
			
			return $res;
			
		}
		
		*/
		
		/*
		function get_reference_select_values($ref_table_config,$ref_table_field,$extra_condition=""){
			
			
			$result=$this->DBConnection_mdl->get_reference_select_values($ref_table_config,$ref_table_field,$extra_condition);
			
			
			return $result;
			
			
		
		}
		*/
		
		function save_reference($content,$type='normal') {
			$config=$content['table_config'];
			if(admin_config($config)){
				$this->db3 = $this->load->database('default', TRUE);
			}else{
				$this->db3 = $this->load->database(project_db(), TRUE);
			}
			$table_name= $content ['table_name'];
			$table_id= $content ['table_id'];
			
			unset ( $content ['table_name'] );
			unset ( $content ['table_id'] );
			unset ( $content ['table_config'] );
			
	
			
			if (  $content ['operation_type']=='new' ) {
				
				unset ( $content ['operation_type'] );
				
				$res = $this->db3->insert ( $table_name, $content );
				
				$id= $this->db3->insert_id ();
					
				
			} else {
				unset ( $content ['operation_type'] );
				$id=$content [$table_id];
				
				$res = $this->db3->update ( $table_name, $content, array (
						$table_id =>$id
				) );
				
				
			}
			if($type=='get_id'){
				return $id;
			}else{
				return $res;
			}
		}
		/*
		function get_reference_details($table_name,$table_id,$ref_id) {
			
			$result=$this->DBConnection_mdl->get_reference_details($table_name,$table_id,$ref_id);
				
			return $result;
			
		}
		*/
		function get_reference_tables_list(){
			$result=$this->DBConnection_mdl->get_reference_tables_list();
			
			return $result;
			
		}
		
		function get_classifications($paper_id){
			$result=$this->DBConnection_mdl->get_classifications($paper_id);
				
			return $result;
			
		}
		
		function get_exclusion($paper_id){
			$result=$this->DBConnection_mdl->get_exclusion($paper_id);
			
			return $result;
			
		}
		
		
		
		
		function get_assignations($paper_id){
			
			$result=$this->DBConnection_mdl->get_assignations($paper_id);
			
			return $result;
			
		}
		
		function get_reference_value($table,$id,$field="ref_value",$table_id="ref_id"){
			
			if(admin_config($table,TRUE,'table')){
				$this->db3 = $this->load->database('default', TRUE);
			}else{
				$this->db3 = $this->load->database(project_db(), TRUE);
			}
			$data=$this->db3->query ( "CALL get_reference_value('".$table."','".$id."','".$field."','".$table_id."') " );
			
			mysqli_next_result( $this->db3->conn_id );
				
			$res=$data->row_array();
			
			if(!empty($res[$field])){
				
				return $res[$field];
			}else{
				
				return "";
			}
			
		}
		
		/*
		function get_reference_corresponding_table($ref_config){
			$result=$this->DBConnection_mdl->get_reference_corresponding_table($ref_config);
				
			return $result;
			/*
			$sql="Select * from ref_tables WHERE reftab_label='$ref_config' AND  	reftab_active=1 ";
				
			$res=$this->db->query ( $sql )->row_array ();
			
			return $res;
				
		}*/
		
		function get_classification_paper($classification_id){
			$result=$this->DBConnection_mdl->get_classification_paper($classification_id);
				
			return $result;
			
			/*
			$sql="Select class_paper_id from classification WHERE class_id='$classification_id' ";
		
			$res=$this->db->query ( $sql )->row_array ();
			
			if(!empty($res))
				return $res['class_paper_id'];
			else 
				return 0;
			*/
		
		}
		
		
		function get_result_classification($field){
			
			$this->db3 = $this->load->database(project_db(), TRUE);
		
			$data=$this->db3->query ( "CALL get_result_count('".$field."') " );
			
			mysqli_next_result( $this->db3->conn_id );
			$result=$data->result_array();	
			//print_test($result);
			return $result;
		}
		
		function get_classification_intents($classification_id){
			$this->db3 = $this->load->database(project_db(), TRUE);
			$data=$this->db3->query ( "CALL getMTIntents(".$classification_id.") " );
				
			mysqli_next_result( $this->db3->conn_id );
			$result=$data->result_array();
			
			
				
			
			return $result;
		}
		
		/**
		 * A wrapper that simply executes the 
		 * 			db->escape("...")
		 * function for multi database environments (Loading is performed
		 * the same way as done within `run_query` function below).
		 *
		 * HINT: Probably obtaining the "normal" database, $this->db
		 * would be enough anyway, as all databases are using the same
		 * driver (so escaping would be done anyway in the expected way).
		 * 
		 * @author Daniel Hofstetter daniel.hofstetter@sbg.ac.at
		 * 
		 * @param  string $value     the value to be escaped as string.
		 * @param  string $target_db the target database named by a string, 
		 * 													 or 'current' default. 
		 * @return string            the escaped value as string.
		 */
		function query_escape($value, $target_db = 'current')
		{
				return $this->load_database($target_db)->escape($value);
		}
		/**
		 * A wrapper that simply executes the 
		 * 			db->escape_str("...")
		 * function for multi database environments (Loading is performed
		 * the same way as done within `run_query` function below).
		 *
		 * HINT: Probably obtaining the "normal" database, $this->db
		 * would be enough anyway, as all databases are using the same
		 * driver (so escaping would be done anyway in the expected way).
		 * 
		 * @author Daniel Hofstetter daniel.hofstetter@sbg.ac.at
		 * 
		 * @param  string $value     the value to be escaped as string.
		 * @param  string $target_db the target database named by a string, 
		 * 													 or 'current' default. 
		 * @return string            the escaped value as string.
		 */
		function query_escape_str($value, $target_db = 'current')
		{
				return $this->load_database($target_db)->escape_str($value);
		}
		
		/**
		 * Load the database specified by the identifier ('current' by default, to
		 * use the currents project db).
		 * The target_db parameter is translated automatically by using the
		 * `get_targetdb` helper function.
		 * 
		 * @author Daniel Hofstetter daniel.hofstetter@sbg.ac.at
		 * 
		 * @param  string $target_db An identifier to specify which db to load.
		 * @return object|CI_DB_mysql_driver the Database object, FALSE on failure. 
		 */
		private function load_database($target_db = 'current')
		{
				return $this->load->database(get_targetdb($target_db), TRUE);
		}
		
		/**
		 * [run_query description]
		 * @param  [type]  $sql          [description]
		 * @param  boolean $result_table [description]
		 * @param  string  $target_db    [description]
		 * @return [type]                [description]
		 */
		function run_query($sql,$result_table=False,$target_db='current'){
			
			$this->db2 = $this->load_database($target_db);
			
			if ( !($resu = $this->db2->simple_query($sql)))
			{
				
				$error = $this->db2->error(); // Has keys 'code' and 'message'
				
				return $error;
			}else{
				$json = json_encode($resu);
				$result['code']="0";
				$result['message']="";
				
				if($result_table){
					
					
					if(strpos($json, 'num_rows'))
					$result['message']=$this->db2->query($sql)->result_array();
					
					
				}
				
				return $result;
				
			}
			
			
		}
		
		function get_classification_scheme(){
			
			$result=$this->DBConnection_mdl->get_classification_scheme();
			
			return $result;

			/*
			$sql="SELECT *  from  classification_scheme WHERE  	scheme_active=1 ORDER BY field_order ASC ";
		
			$result=$this->db->query ( $sql )->result_array ();
		
			return $result;
			*/
		}
		
		function get_project_config($project_label){
			
			$sql="SELECT *  from  projects WHERE  	project_active=1 AND project_label LIKE '$project_label' ";
			
			$result=$this->db->query ( $sql )->row_array ();
			
			return $result;
		}
		
		function add_operation($content){
			$operation_code=$content['operation_code'];
			$operation_type=$content['operation_type'];
			$user_id=$content['user_id'];
			$operation_desc=$content['operation_desc'];
			
			$sql= "INSERT INTO operations (operation_code , operation_type , operation_desc , user_id) VALUES ('$operation_code' , '$operation_type' , '$operation_desc' , $user_id)";
			
			$this->db2 = $this->load->database(project_db(), TRUE);
			if ( !($resu = $this->db2->insert('operations',$content)))
			{
			
				$error = $this->db2->error(); // Has keys 'code' and 'message'
			
				return $error;
			}else{
				
				return 1;
			}
			
			
		}
		
		
	
}
