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
 *  
 * --------------------------------------------------------------------------
 */
function check_operation($operation,$type="List") {
	$operations=array();
	
	$operations['list_all_users']=array(
			'type'=>'List',
			'tab_ref'=>'users',
			'operation_id'=>'list_users'
	);
		
	
	
	$operations['list_usergroups']=array(
			'type'=>'List',
			'tab_ref'=>'usergroup',
			'operation_id'=>'list_usergroups'
	);
	
	$operations['detail_user']=array(
			'type'=>'Detail',
			'tab_ref'=>'users',
			'operation_id'=>'detail_user'
	);

	$operations['detail_user_min']=array(
			'type'=>'Detail',
			'tab_ref'=>'users',
			'operation_id'=>'detail_user_min'
	);
	$operations['detail_user_min_ed']=array(
			'type'=>'Detail',
			'tab_ref'=>'users',
			'operation_id'=>'detail_user_min_ed'
	);
	$operations['remove_user']=array(
			'type'=>'Remove',
			'tab_ref'=>'users',
			'operation_id'=>'remove_user'
	);
	
	$operations['add_user']=array(
			'type'=>'Add',
			'tab_ref'=>'users',
			'operation_id'=>'add_user'
	);
	$operations['edit_user']=array(
			'type'=>'Edit',
			'tab_ref'=>'users',
			'operation_id'=>'edit_user'
	);
	$operations['edit_user_min']=array(
			'type'=>'Edit',
			'tab_ref'=>'users',
			'operation_id'=>'edit_user_min'
	);
	
	

	
	include_once('operations/op_project.php');
	$operations=array_merge($operations,get_operations_project());
	
	include_once('operations/op_configuration.php');
	$operations=array_merge($operations,get_operations_configuration());
	
	include_once('operations/op_reference.php');
	$operations=array_merge($operations,get_operations_reference());
	
	
	include_once('operations/op_author.php');
	$operations=array_merge($operations,get_operations_author());
	
	include_once('operations/op_venue.php');
	$operations=array_merge($operations,get_operations_venue());
	

	include_once('operations/op_paper.php');
	$operations=array_merge($operations,get_operations_paper());
	
	include_once('operations/op_screening.php');
	$operations=array_merge($operations,get_operations_screening());
	
	
	include_once('operations/op_logs.php');
	$operations=array_merge($operations,get_operations_logs());
	
	
	include_once('operations/op_str_mng.php');
	$operations=array_merge($operations,get_operations_str_mng());
	
	include_once('operations/op_operations.php');
	$operations=array_merge($operations,get_operations_operations());
	
	include_once('operations/op_qa.php');
	$operations=array_merge($operations,get_operations_qa());
	
	include_once('operations/op_classification.php');
	$operations=array_merge($operations,get_operations_classification());
	
	include_once('operations/op_debug.php');
	$operations=array_merge($operations,get_operations_debug());
	
	include_once('operations/op_info.php');
	$operations=array_merge($operations,get_operations_info());
	
	if(project_db() != 'default')
	{	include_once('operations/op_generated.php');
		$operations=array_merge($operations,get_operations_generated());
	}
	if(isset($operations[$operation]) AND $operations[$operation]['type']==$type ){
			return $operations[$operation];
	}else{
			set_top_msg(lng_min(" Action not available!  ".$operation),'error');
			redirect('auth/');
			return false;
	}
	
	
}



function create_stored_procedures($entity_config,$target_db='current' ,$verbose=FALSE,$run_query=TRUE){
		
	$table_configuration=get_table_configuration($entity_config,$target_db);


	//	print_test($table_configuration);
	foreach ($table_configuration['operations'] as $operation_key=> $operation_value) {
		//	print_test($operation_value);
		if(!empty($operation_value['generate_stored_procedure'])){


			if( $operation_value['operation_type']=='List'){
				//print_test($operation_value);
				$list_config=array(
						'stored_procedure_name'=>$operation_value['data_source'],
						'fields'=>'*',
						'table_name'=>!empty($operation_value['table_name'])?$operation_value['table_name']:$table_configuration['table_name'],
						'table_active_field'=>$table_configuration['table_active_field'],
						'order_by'=>!empty($operation_value['order_by'])?$operation_value['order_by']:'',
						'search_by'=>!empty($operation_value['search_by'])?$operation_value['search_by']:'',
						'conditions'=>!empty($operation_value['conditions'])?$operation_value['conditions']:array(),

				);

			if ($verbose)
				print_test($list_config);
				
				generate_stored_procedure_list($list_config,$target_db,$run_query,$verbose);

			}elseif(in_array($operation_value['operation_type'], array('Add','Edit','EditChild','AddChild','AddDrill'))){

				$fields=array();
				foreach ($operation_value['fields'] as $key_f => $value_f) {
						
						
					if(!empty($table_configuration['fields'][$key_f]) AND $value_f['field_state']!='disabled' AND $value_f['field_state']!='drill_down' AND !(isset($table_configuration['fields'][$key_f]['multi-select']) AND isset($table_configuration['fields'][$key_f]['multi-select'])=='Yes')){

						//print_test($key_f);
						//print_test($table_configuration['fields'][$key_f]);
						$field_det=	$table_configuration['fields'][$key_f];
						$size="250";
						$type="VARCHAR";

						if($field_det['field_type']=='number' || $field_det['field_type']=='int' ){
								
							$type="INT";

						}elseif($field_det['field_type']=='real' ){
								
							$type="DOUBLE";

						}elseif($field_det['field_type']=='longtext' ){
								
							$type="LONGTEXT";

						}elseif($field_det['field_type']=='text'){
								
							if(!empty($field_det['field_size'])){

								$size=$field_det['field_size'] + 5;

							}

							$type= " VARCHAR($size)";
						}elseif( $field_det['input_type']== 'image'){

							$type=" LONGBLOB ";
						}
							
						$fields[$key_f]=$type;
							
							
					}
				}


				$add_config=array(
						'stored_procedure_name'=>$operation_value['db_save_model'],
						'fields'=>$fields,
						'table_name'=>!empty($operation_value['table_name'])?$operation_value['table_name']:$table_configuration['table_name'],
						'table_active_field'=>$table_configuration['table_active_field'],
						'table_id'=>$table_configuration['table_id']

				);
				
				if ($verbose)
				print_test($add_config);
				
				if(in_array($operation_value['operation_type'], array('Add','AddChild','AddDrill'))){
					generate_stored_procedure_add($add_config,$target_db,$run_query,$verbose);
				}else{
					generate_stored_procedure_update($add_config,$target_db,$run_query,$verbose);
				}
				
			}elseif( $operation_value['operation_type']=='Detail'){
				//print_test($operation_value);

				$size="11";
				$type="INT";
				$field_table_id=$table_configuration['fields'][$table_configuration['table_id']];

				if($field_table_id['field_type']=='number' || $field_table_id['field_type']=='int' ){

					$type="INT";

				}elseif($field_table_id['field_type']=='real' ){

					$type="DOUBLE";

				}elseif($field_table_id['field_type']=='text'){

					if(!empty($field_det['field_size'])){

						$size=$field_det['field_size'] + 5;

					}

					$type= " VARCHAR($size)";
				}


				$detail_config=array(
						'stored_procedure_name'=>$operation_value['data_source'],
						'fields'=>'*',
						'table_name'=>!empty($operation_value['table_name'])?$operation_value['table_name']:$table_configuration['table_name'],
						'table_active_field'=>$table_configuration['table_active_field'],
						'table_id'=>$table_configuration['table_id'],
						'table_id_type'=>$type
							
				);

				if ($verbose)
				print_test($detail_config);
				generate_stored_procedure_detail($detail_config,$target_db,$run_query,$verbose);

			}elseif( $operation_value['operation_type']=='Remove'){
				//print_test($operation_value);



				$remove_config=array(
						'stored_procedure_name'=>$operation_value['db_delete_model'],

						'table_name'=>!empty($operation_value['table_name'])?$operation_value['table_name']:$table_configuration['table_name'],
						'table_active_field'=>$table_configuration['table_active_field'],
						'table_id'=>$table_configuration['table_id']
							
				);

				if ($verbose)
				print_test($remove_config);
				generate_stored_procedure_remove($remove_config,$target_db,$run_query,$verbose);

			}
		}
	}


	
}


/*
 * Création des stored procedures pour la partie admin
 */

function admin_initial_db_setup($verbose=FALSE){
	$ci = get_instance ();
	$target_db='default';

	$configs=array('users','project','user_project','logs','str_mng'
				,'debug');
			
	

	foreach ($configs as $k => $config) {
			
		create_stored_procedures($config,$target_db ,False);


	}
	
	//change config value
	$sql="UPDATE config_admin SET first_connect = 0  ";
	$res=$ci->db->simple_query($sql);
}
