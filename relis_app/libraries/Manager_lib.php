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
class Manager_lib
{
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	/*
		responsible for retrieving reference select values based on a provided configuration. 
		It supports fetching values from multiple levels of reference tables and applies optional filters.
		Reference select values refer to the options or choices available for selection in a dropdown menu or select input field
	*/
	function get_reference_select_values($config, $start_with_empty = True, $get_leaf = False, $multiselect = False, $filter = array())
	{

		$conf = explode(";", $config);
		//	print_test($conf);
		$ref_table = $conf[0];
		$fields = $conf[1];
		$ref_table_config = get_table_configuration($ref_table);
		//for_array

		if ($get_leaf) {

			while (!empty($ref_table_config['fields'][$fields]['input_type']) and $ref_table_config['fields'][$fields]['input_type'] == 'select' and $ref_table_config['fields'][$fields]['input_select_source'] == 'table') {

				$config = $ref_table_config['fields'][$fields]['input_select_values'];

				$conf = explode(";", $config);
				//print_test($conf);
				$ref_table = $conf[0];
				$fields = $conf[1];
				$ref_table_config = get_table_configuration($ref_table);


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
				$ref_table_config = get_table_configuration($ref_table);



			}

			$extra_condition = "";

			//Pour les dipendantdynamiccategory ajouter une condition pour les enregistrements relatifs à cet element	
			if (!empty($filter) and !empty($ref_table_config['fields'][$filter['filter_field']])) {
				$extra_condition = " AND  " . $filter['filter_field'] . " = '" . $filter['filter_value'] . "'";
			}
			$res = $this->CI->DBConnection_mdl->get_reference_select_values($ref_table_config, $fields, $extra_condition);
			//print_test($res);	
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
					$ref_table_config = get_table_configuration($ref_table);


					$res2 = $this->CI->manage_mdl->get_reference_value($ref_table_config['table_name'], $value['refDesc'], $fields, $ref_table_config['table_id']);

					$result[$value['refId']] = $res2;


				}


				if (isset($ref_table_config['fields'][$fields]['input_select_source'])) {
					$source = $ref_table_config['fields'][$fields]['input_select_source'];

					if ($source == 'array') {
						$select_values = $ref_table_config['fields'][$fields]['input_select_values'];
					} elseif ($source == 'yes_no') {
						$select_values = array(0 => 'No', 1 => 'Yes');
					}

					if (!empty($select_values[$result[$value['refId']]]))
						$result[$value['refId']] = $select_values[$result[$value['refId']]];
				}



			}

		}
		//print_test($result);
		return $result;


	}

	//old version of get_reference_select_values
	function get_reference_select_values_old($config, $start_with_empty = True, $get_leaf = False, $multiselect = False)
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



			$res = $this->CI->DBConnection_mdl->get_reference_select_values($ref_table_config, $fields);

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


					$res2 = $this->CI->manage_mdl->get_reference_value($ref_table_config['table_name'], $value['refDesc'], $fields, $ref_table_config['table_id']);

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


	/*
		facilitates the retrieval of multiple values from a reference table based on a configuration and a specific element
	*/
	function get_element_multi_values($config, $key_field, $element_id, $display_field = False)
	{
		$Tvalues_source = explode(';', $config);

		$source_table_config = get_table_configuration($Tvalues_source[0]);
		if ($display_field)
			$display_field = $Tvalues_source[1];
		else
			$display_field = $source_table_config['table_id'];

		$extra_condition = " AND $key_field ='" . $element_id . "'";

		$res_values = $this->CI->DBConnection_mdl->get_reference_select_values($source_table_config, $display_field, $extra_condition);


		$results = array();

		foreach ($res_values as $value) {
			array_push($results, $value['refDesc']);
		}
		return $results;
	}

	/*
	 * Fonction pour afficher une ligne d'une table avec remplacement des clès externes par leurs correspondances
	 */
	function get_element_detail($ref_table, $ref_id, $editable = False, $modal_link = False)
	{
		//old_version();
		//récuperation de la configuration de l'entité
		$table_config = get_table_config($ref_table);




		$dropoboxes = array();

		// récupération des valeurs pour les champs avec la clé enregistre dans la table (pour pouvoir afficher le label)
		foreach ($table_config['fields'] as $k => $v) {

			//Rechercher pour les champs qu'on afficher seulement

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
						$this->CI->db3 = $this->CI->load->database(project_db(), TRUE);

						//todo generaliser pour tout les multivalues (car les autres prennemt beaucoups de temps)

						$sql = "select P.paperauthor_id ,A.author_name from paperauthor P,author A where P.paperId=$ref_id AND P.authorId=A.author_id AND A.author_active=1 AND P.paperauthor_active=1 ";
						$res_author = $this->CI->db3->query($sql)->result_array();
						$t_array = array('' => 'Select ...');

						foreach ($res_author as $key_a => $value_a) {
							$t_array[$value_a['paperauthor_id']] = $value_a['author_name'];
						}
						//print_test($t_array);
						$dropoboxes[$k] = $t_array;

					} else {
						$dropoboxes[$k] = $this->get_reference_select_values($v['input_select_values']);
					}
				}
			}

		}


		// récuperation de la base de donnée la ligne correspondant à l'enregistrement
		$detail_result = $this->CI->DBConnection_mdl->get_row_details($ref_table, $ref_id);

		$content_item = $detail_result;


		$item_data = array();


		foreach ($dropoboxes as $k => $v) {
			$content_item[$k . '_idd'] = 0;
			if (isset($content_item[$k])) { //si l'element est déjà present dans la ligne(Pas un multivalue) on lui donne la correspondance de la clée

				if (isset($v[$content_item[$k]])) {

					$content_item[$k . '_idd'] = $content_item[$k];

					$content_item[$k] = $v[$content_item[$k]];

				}
			} else { // element pas present on le creée avec une valeur vide (_idd reste a "0")

				$content_item[$k] = "";
			}
		}




		//	Preparation des valeurs à afficher

		foreach ($table_config['fields'] as $key => $value) {
			$array = array();

			// Champs qui sont à afficher
			if (!(isset($value['on_view']) and $value['on_view'] == 'hidden')) {


				$array['title'] = $value['field_title'];
				$array['field_id'] = $key;
				$array['edit'] = 0;


				//Pour les  multivalues
				if (isset($value['number_of_values']) and ($value['number_of_values'] == '*' or $value['number_of_values'] != '1') and !empty($value['input_select_key_field'])) {
					if ($value['number_of_values'] == "0" or $value['number_of_values'] == "-1" or $value['number_of_values'] == "*") {
						$max_number_of_value = 0;
					} else {
						$max_number_of_value = (int) $value['number_of_values'];
					}
					$Tvalues_source = explode(';', $value['input_select_values']);

					$source_table_config = get_table_config($Tvalues_source[0]);
					$input_select_key_field = $value['input_select_key_field'];

					$extra_condition = " AND $input_select_key_field ='" . $ref_id . "'";

					$res_values = $this->CI->DBConnection_mdl->get_reference_select_values($source_table_config, $input_select_key_field, $extra_condition);

					// Prepare the button to add new element
					$add_button = create_button_link('manager/add_element_child/' . $Tvalues_source[0] . '/' . $value['input_select_key_field'] . '/' . $ref_table . '/' . $ref_id, '<i class="fa fa-plus"></i> Add', "btn-success", 'Add ');

					if ($modal_link) { //use modal 
						$modal_title = "Add : " . $value['field_title'];
						$add_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manager/add_element_child_modal/' . $Tvalues_source[0] . '/' . $value['input_select_key_field'] . '/' . $ref_table . '/' . $ref_id . '"  data-modal_title="' . $modal_title . '" ><i class="fa fa-plus"></i>Add</a>';
					}

					$k_row = 0;
					//For multi select no need to add the add button there is an adapted field - And if the number of values is reached
					if (!(isset($value['multi-select']) and $value['multi-select'] == "Yes") and $editable and ($max_number_of_value == 0 or count($res_values) < $max_number_of_value)) {
						$array['val2'][0] = "<span> " . $add_button . "</span>";

						$k_row = 1;
						$array['edit'] = 1;
					}




					// Get values if label  are from other tables ()
					foreach ($res_values as $key_v => $value_v) {


						if (isset($dropoboxes[$key][$value_v['refId']]))
							$array['val2'][$k_row] = $dropoboxes[$key][$value_v['refId']];


						if (isset($value_v['refId']) and $value['input_select_source'] == 'table' and isset($value['input_select_source_type'])) {

							$Tconfig = explode(';', $value['input_select_values']);

							if ($value_v['refId'] != 0) {
								$edit_button = create_button_link('manager/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $value_v['refId'], '<i class="fa fa-pencil"></i> Edit', "btn-info", 'Edit ');

								$delete_button = create_button_link('manager/remove_drilldown/' . $value_v['refId'] . '/' . $Tconfig[0] . '/' . $ref_table . '/' . $ref_id . '/' . $key . '/no', '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');

								if ($modal_link) { //use modal for classification
									$modal_title = "Edit : " . $value['field_title'];
									$edit_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manager/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $value_v['refId'] . '/modal"  data-modal_title="' . $modal_title . '" ><i class="fa fa-pencil"></i>Edit</a>';

									$delete_button = create_button_link('manager/remove_drilldown/' . $value_v['refId'] . '/' . $Tconfig[0] . '/' . $ref_table . '/' . $ref_id . '/' . $key . '/no/yes', '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');

								}



								if ((isset($value['multi-select']) and $value['multi-select'] == "Yes") or !$editable) {
									$edit_button = "";
									$delete_button = "";
								}

								if (isset($value['input_select_source_type']) and $value['input_select_source_type'] == 'drill_down')
									$array['val2'][$k_row] = "<span class='drilldown_link'>" . anchor('manager/display_element/' . $Tconfig[0] . '/' . $value_v['refId'], $array['val2'][$k_row]) . "</span> <div class='navbar-right'>$edit_button $delete_button</div>";
								else
									$array['val2'][$k_row] .= " <div class='navbar-right'>$edit_button $delete_button</div>";


							}
						}


						$k_row++;

					}


				} else {

					//for single values

					$array['val'] = isset($content_item[$key]) ? " " . $content_item[$key] : ' ';
					$array['val2'][0] = isset($content_item[$key]) ? " " . $content_item[$key] : ' ';

					// for images
					if (isset($value['input_type']) and $value['input_type'] == 'image') {
						if (!empty($content_item[$key])) {
							//$img=$this->config->item('image_upload_path').$content_item[$key]."_thumb.jpg";
							//$array['val2'][0]= img($img);


							$delete_picture_button = get_top_button('all', 'Remove picture', 'manager/remove_picture/' . $ref_table . '/' . $table_config['table_name'] . '/' . $table_config['table_id'] . '/' . $key . '/' . $ref_id, '', 'fa-close', '', 'btn-danger', FALSE);


							$array['val2'][0] = '<img src="' . display_picture_from_db($content_item[$key]) . '"/> ' . $delete_picture_button;
						}

					}



					if (isset($content_item[$key . '_idd']) and $value['input_select_source'] == 'table' and isset($value['input_select_source_type']) and $value['input_select_source_type'] == 'drill_down') {

						$Tconfig = explode(';', $value['input_select_values']);
						// si la valeur est déjà enregistré
						if ($content_item[$key . '_idd'] != 0) {
							if ((isset($value['drill_down_type']) and $value['drill_down_type'] == 'not_linked') or !$editable) {
								$edit_button = "";
								$delete_button = "";
							} else {
								$edit_button = create_button_link('manager/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $content_item[$key . '_idd'], '<i class="fa fa-pencil"></i> Edit', "btn-info", 'Edit ');

								$delete_button = create_button_link('manager/remove_drilldown/' . $content_item[$key . '_idd'] . '/' . $Tconfig[0] . '/' . $ref_table . '/' . $ref_id . '/' . $key, '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');
							}

							$array['val'] = "<span class='drilldown_link'>" . anchor('manager/display_element/' . $Tconfig[0] . '/' . $content_item[$key . '_idd'], $array['val']) . "</span> <div class='navbar-right'>$edit_button.$delete_button</div>";
							$array['val2'][0] = "<span class='drilldown_link'>" . anchor('manager/display_element/' . $Tconfig[0] . '/' . $content_item[$key . '_idd'], $array['val2'][0]) . "</span> <div class='navbar-right'>$edit_button.$delete_button</div>";



						} else { // si la valeur n'est pas encore enregistré


							if ((isset($value['drill_down_type']) and $value['drill_down_type'] == 'not_linked') or !$editable) {
								$add_button = "";
							} else {
								$array['edit'] = 1;
								$add_button = create_button_link('manager/add_element_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id, '<i class="fa fa-plus"></i> Add', "btn-success", 'Add ');

								if ($ref_table == 'classification') { //use modal for classification
									$modal_title = "Add : " . $value['field_title'];
									$add_button = '<a  class="btn btn-xs btn-success" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manager/add_element_drilldown_modal/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '"  data-modal_title="' . $modal_title . '" ><i class="fa fa-plus"></i>Add</a>';
								}


							}
							$array['val'] = "<span>: " . $add_button . "</span>";
							$array['val2'][0] = "<span>: " . $add_button . "</span>";
						}
					}
				}

				array_push($item_data, $array);


			}
		}

		return $item_data;

	}


	/*
		calculates and returns the completion statistics for a given category of QA. 
		It provides information on the overall completion and user-specific completion, including the total number of QA, 
		number of completed QA, and number of pending QA.
	*/
	function get_qa_completion($category = 'QA')
	{
		$res_qa = $this->get_qa_result('all', 0, $category);
		$user_res = array();
		$all_res = array();

		foreach ($res_qa['qa_list'] as $key => $value) {

			if (empty($all_res['all'])) {
				$all_res['all'] = 1;
			} else {
				$all_res['all']++;
			}

			if (empty($user_res[$value['user_id']]['all'])) {
				$user_res[$value['user_id']]['all'] = 1;
			} else {
				$user_res[$value['user_id']]['all']++;
			}

			if (!empty($value['paper_done'])) {
				if (empty($user_res[$value['user_id']]['done'])) {
					$user_res[$value['user_id']]['done'] = 1;
				} else {
					$user_res[$value['user_id']]['done']++;
				}

				if (empty($all_res['done'])) {
					$all_res['done'] = 1;
				} else {
					$all_res['done']++;
				}

			} else {
				if (empty($user_res[$value['user_id']]['pending'])) {
					$user_res[$value['user_id']]['pending'] = 1;
				} else {
					$user_res[$value['user_id']]['pending']++;
				}

				if (empty($all_res['pending'])) {
					$all_res['pending'] = 1;
				} else {
					$all_res['pending']++;
				}
			}
		}
		$result['general_completion'] = $all_res;
		$result['user_completion'] = $user_res;
		return $result;


	}

	//retrieves QA results based on the specified parameters, including the type, ID, category, and status.
	function get_qa_result($type = "mine", $id = 0, $category = 'QA', $add_Link = True, $status = 'all')
	{
		//print_test($type);
		//get qa results
		$project_published = project_published();

		$qa_result = $this->CI->db_current->order_by('qa_id', 'ASC')
			->get_where('qa_result', array('qa_active' => 1))
			->result_array();

		//Put result in searchable array
		$array_qa_result = array();
		foreach ($qa_result as $key_result => $v_result) {
			$array_qa_result[$v_result['paper_id']][$v_result['question']][$v_result['response']] = 1;
		}
		//print_test($qa_result);
		//	print_test($array_qa_result);
		//get_assignments

		if ($type == 'id' and !empty($id)) {
			$extra_condition = " AND paper_id= '" . $id . "' ";
		} elseif ($type == 'all') {
			$extra_condition = " AND screening_status='Included' ";
		} elseif ($type == 'excluded') {
			$extra_condition = " AND screening_status='Excluded_QA' ";
		} else {
			$extra_condition = " AND screening_status='Included'  AND assigned_to= '" . active_user_id() . "' ";
		}


		if ($category == 'QA_Val') {

			if ($status == 'pending') {
				$extra_condition .= " AND Q.validation IS NULL";
			} elseif ($status == 'done') {
				$extra_condition .= " AND Q.validation IS NOT NULL";
			}

			$sql = "SELECT Q.*,Q.	qa_validation_assignment_id as assignment_id,Q.validation as status,P.title FROM qa_validation_assignment Q,paper P where Q.paper_id=P.id AND 	qa_validation_active=1 AND paper_active=1 $extra_condition ";
		} else {

			if ($status == 'pending') {
				$extra_condition .= " AND Q.qa_status	='Pending'";
			} elseif ($status == 'done') {
				$extra_condition .= " AND Q.qa_status ='Done'";
			}
			$sql = "SELECT Q.*,Q.qa_assignment_id as assignment_id,P.title,P.screening_status as status FROM qa_assignment Q,paper P where Q.paper_id=P.id AND qa_assignment_active=1 AND paper_active=1 $extra_condition ";
		}


		$assignments = $this->CI->db_current->query($sql)->result_array();


		$qa_questions = $this->CI->db_current->order_by('question_id', 'ASC')
			->get_where('qa_questions', array('question_active' => 1))
			->result_array();

		$qa_responses = $this->CI->db_current->order_by('score', 'DESC')
			->get_where('qa_responses', array('response_active' => 1))
			->result_array();

		//print_test($assignments);
		//	print_test($qa_questions);
		//	print_test($qa_responses);
		$users = $this->get_reference_select_values('users;user_name', FALSE, False);



		$all_qa = array();
		$all_qa_html = array();
		$paper_completed = 0;
		foreach ($assignments as $key_assign => $v_assign) {


			$all_qa[$v_assign['assignment_id']] = array(
				'paper_id' => $v_assign['paper_id'],
				'title' => $v_assign['title'],
				'status' => $v_assign['status'],
				'user' => !empty($users[$v_assign['assigned_to']]) ? $users[$v_assign['assigned_to']] : '',
				'user_id' => !empty($users[$v_assign['assigned_to']]) ? $v_assign['assigned_to'] : '',

			);
			$questions = array();
			$q_result_score = 0;
			$q_done = 0;
			$q_pending = 0;


			foreach ($qa_questions as $k_question => $v_question) {
				$questions[$v_question['question_id']] = array(
					'question' => $v_question,
				);
				$responses = array();
				$q_result = !empty($array_qa_result[$v_assign['paper_id']][$v_question['question_id']]) ? 1 : 0;
				$question_asw = 0;
				foreach ($qa_responses as $k_response => $v_response) {
					if (empty($array_qa_result[$v_assign['paper_id']][$v_question['question_id']][$v_response['response_id']])) { //see if the response have been chosed for the question
						$res = 0;
						if ($add_Link)
							$link = "quality_assessment/qa_conduct_save/$q_result/" . $v_assign['paper_id'] . '/' . $v_question['question_id'] . '/' . $v_response['response_id'];
						else
							$link = "";
					} else {
						$res = 1;
						$link = "";
						$q_result_score += $v_response['score'];
						$question_asw = 1;

					}
					if ($project_published) {
						$link = "";
					}
					$responses[$v_response['response_id']] = array(
						'response' => $v_response,
						'result' => $res,
						'link' => $link,
					);


				}
				$questions[$v_question['question_id']]['responses'] = $responses;
				$questions[$v_question['question_id']]['q_result'] = $q_result;
				if ($question_asw) {
					$q_completed = 1;
					$q_done++;
				} else {
					$q_completed = 0;
					$q_pending++;
				}
				$questions[$v_question['question_id']]['completed'] = $q_completed;

			}

			$all_qa[$v_assign['assignment_id']]['q_result_score'] = $q_result_score;
			;
			$all_qa[$v_assign['assignment_id']]['questions'] = $questions;
			$paper_done = 0;
			if ($category == 'QA_Val') {
				if (!empty($v_assign['validation'])) {
					$paper_done = 1;
					$paper_completed++;
				}
			} else {
				if (empty($q_pending)) {
					$paper_done = 1;
					$paper_completed++;
				}
			}
			$all_qa[$v_assign['assignment_id']]['paper_done'] = $paper_done;

		}


		$data['qa_list'] = $all_qa;
		$data['paper_completed'] = $paper_completed;

		return $data;
	}

	/*
	 * Fonction pour afficher une ligne d'une table avec remplacement des clès externes par leurs correspondances
	 */
	function get_detail($table_config, $ref_id, $editable = True, $modal_link = False)
	{


		//récuperation de la configuration de l'entité
		//$table_config=get_table_config($ref_table);

		$ref_table = $table_config['config_id'];
		$current_operation = $table_config['current_operation'];

		$dropoboxes = array();

		// récupération des valeurs pour les champs avec la clé enregistre dans la table (pour pouvoir afficher le label)
		foreach ($table_config['operations'][$current_operation]['fields'] as $k_field => $v) {

			if (!empty($table_config['fields'][$k_field])) {

				$field_det = $table_config['fields'][$k_field];

				//Rechercher pour les champs qu'on afficher seulement

				if (!empty($field_det['input_type']) and $field_det['input_type'] == 'select') {

					if ($field_det['input_select_source'] == 'array') {
						$dropoboxes[$k_field] = $field_det['input_select_values'];
					} elseif ($field_det['input_select_source'] == 'yes_no') {
						$dropoboxes[$k_field] = array(
							0 => 'No',
							1 => 'Yes'
						);
					} elseif ($field_det['input_select_source'] == 'table') {
						//print_test($field_det);

						// recherches des auteurs par papier
						if ($ref_table == 'papers' and $k_field == 'authors') {
							$this->CI->db3 = $this->CI->load->database(project_db(), TRUE);

							//todo generaliser pour tout les multivalues (car les autres prennemt beaucoups de temps)

							$sql = "select P.paperauthor_id ,A.author_name from paperauthor P,author A where P.paperId=$ref_id AND P.authorId=A.author_id AND A.author_active=1 AND P.paperauthor_active=1 ";
							$res_author = $this->CI->db3->query($sql)->result_array();
							$t_array = array('' => 'Select ...');

							foreach ($res_author as $key_a => $value_a) {
								$t_array[$value_a['paperauthor_id']] = $value_a['author_name'];
							}
							//print_test($t_array);
							$dropoboxes[$k_field] = $t_array;

						} else {
							$dropoboxes[$k_field] = $this->get_reference_select_values($field_det['input_select_values']);
						}
					}
				}
			}
		}

		//exit;
		// récuperation de la base de donnée la ligne correspondant à l'enregistrement
		$detail_result = $this->CI->DBConnection_mdl->get_row_details($table_config['operations'][$current_operation]['data_source'], $ref_id, true, $table_config['config_id']);

		$content_item = $detail_result;
		//	print_test($detail_result);
		$item_data = array();


		foreach ($dropoboxes as $k => $v) {
			$content_item[$k . '_idd'] = 0;
			if (isset($content_item[$k])) { //si l'element est déjà present dans la ligne(Pas un multivalue) on lui donne la correspondance de la clée

				if (isset($v[$content_item[$k]])) {

					$content_item[$k . '_idd'] = $content_item[$k];

					$content_item[$k] = $v[$content_item[$k]];



				} elseif (empty($content_item[$k])) { //avoid displaying zero when the field is empty
					$content_item[$k] = "";
				}
			} else { // element pas present on le creée avec une valeur vide (_idd reste a "0")

				$content_item[$k] = "";
			}
		}


		//print_test($content_item);
		//exit;

		//	Preparation des valeurs à afficher

		//foreach ($table_config['fields'] as $key => $value) {
		foreach ($table_config['operations'][$current_operation]['fields'] as $key => $value_field) {
			$value = $table_config['fields'][$key];
			$array = array();



			$array['title'] = $value['field_title'];
			$array['field_id'] = $key;
			$array['edit'] = 0;
			//print_test($key);
			//print_test($array);

			//Pour les  multivalues
			if (((isset($value['number_of_values']) and ($value['number_of_values'] == '*' or $value['number_of_values'] != '1')) and !empty($value['input_select_key_field'])) or (!empty($value['category_type']) and $value['category_type'] == 'WithSubCategories')) {
				if ($value['number_of_values'] == "0" or $value['number_of_values'] == "-1" or $value['number_of_values'] == "*") {
					$max_number_of_value = 0;
				} else {
					$max_number_of_value = (int) $value['number_of_values'];
				}
				$Tvalues_source = explode(';', $value['input_select_values']);

				$source_table_config = get_table_configuration($Tvalues_source[0]);
				$input_select_key_field = $value['input_select_key_field'];

				$extra_condition = " AND $input_select_key_field ='" . $ref_id . "'";

				$res_values = $this->CI->DBConnection_mdl->get_reference_select_values($source_table_config, $input_select_key_field, $extra_condition);

				// Prepare the button to add new element
				if (!empty($value_field['drilldown_add_link'])) {
					$add_button = create_button_link($value_field['drilldown_add_link'] . $ref_id, '<i class="fa fa-plus"></i> Add', "btn-success", 'Add ');
					//$add_button = create_button_link('manager/add_element_child/'.$Tvalues_source[0].'/'.$value['input_select_key_field'].'/'.$ref_table.'/'.$ref_id,'<i class="fa fa-plus"></i> Add',"btn-success",'Add ');

					if ($modal_link) { //use modal
						$modal_title = "Add  : " . $value['field_title'];
						$add_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  
									data-modal_link="manager/add_element_child_modal/' . $Tvalues_source[0] . '/' . $value['input_select_key_field'] . '/' . $ref_table . '/' . $ref_id . '"
									data-modal_title="' . $modal_title . '" ><i class="fa fa-plus"></i>Add</a>';

						$add_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"
									data-modal_link="' . $value_field['drilldown_add_link'] . $ref_id . '/_/new/modal"
									data-modal_title="' . $modal_title . '" ><i class="fa fa-plus"></i>Add</a>';
					}
				} else {
					$add_button = "";
				}


				$k_row = 0;
				//For multi select no need to add the add button there is an adapted field - And if the number of values is reached
				if (!empty($add_button) and !(isset($value['multi-select']) and $value['multi-select'] == "Yes") and $editable and ($max_number_of_value == 0 or count($res_values) < $max_number_of_value)) {
					$array['val2'][0] = "<span> " . $add_button . "</span>";

					$k_row = 1;
					$array['edit'] = 1;
				}




				// Get values if label  are from other tables ()

				foreach ($res_values as $key_v => $value_v) {
					$edit_button = "";
					$delete_button = "";

					//print_test($value_v);
					if (isset($dropoboxes[$key][$value_v['refId']]))
						$array['val2'][$k_row] = $dropoboxes[$key][$value_v['refId']];


					if (isset($value_v['refId']) and $value['input_select_source'] == 'table' and isset($value['input_select_source_type'])) {

						$Tconfig = explode(';', $value['input_select_values']);

						if ($value_v['refId'] != 0) {
							if (!empty($value_field['drilldown_edit_link'])) {
								$edit_button = create_button_link($value_field['drilldown_edit_link'] . $value_v['refId'] . '/' . $ref_id, '<i class="fa fa-pencil"></i> Edit', "btn-info", 'Edit ');

								if ($modal_link) { //use modal for classification
									$modal_title = "Edit : " . $value['field_title'];
									$edit_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manager/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $value_v['refId'] . '/modal"  data-modal_title="' . $modal_title . '" ><i class="fa fa-pencil"></i>Edit</a>';
									$edit_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="' . $value_field['drilldown_edit_link'] . $value_v['refId'] . '/' . $ref_id . '/modal"  data-modal_title="' . $modal_title . '" ><i class="fa fa-pencil"></i>Edit</a>';
								}

							}

							if (!empty($value_field['drilldown_remove_link'])) {
								//$delete_button = create_button_link('manager/remove_drilldown/'.$value_v['refId'].'/'.$Tconfig[0].'/'.$ref_table.'/'.$ref_id.'/'.$key.'/no','<i class="fa fa-times"></i> Remove',"btn-danger",'Remove ','onlist','alert_ok');
								$delete_button = create_button_link($value_field['drilldown_remove_link'] . $value_v['refId'] . '/' . $ref_id, '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');

								if ($modal_link) {
									//Todo correct
									//$delete_button = create_button_link('manager/remove_drilldown/'.$value_v['refId'].'/'.$Tconfig[0].'/'.$ref_table.'/'.$ref_id.'/'.$key.'/no/yes','<i class="fa fa-times"></i> Remove',"btn-danger",'Remove ','onlist','alert_ok');
									$delete_button = create_button_link($value_field['drilldown_remove_link'] . $value_v['refId'] . '/' . $ref_id, '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');

								}
							}


							if ((isset($value['multi-select']) and $value['multi-select'] == "Yes") or !$editable) {
								$edit_button = "";
								$delete_button = "";
							}

							if (isset($value['input_select_source_type']) and $value['input_select_source_type'] == 'drill_down' and !empty($value_field['drilldown_display_link']))
								$array['val2'][$k_row] = "<span class='drilldown_link'>" . anchor($value_field['drilldown_display_link'] . $value_v['refId'], $array['val2'][$k_row]) . "</span> <div class='navbar-right'>$edit_button $delete_button</div>";
							else
								$array['val2'][$k_row] .= " <div class='navbar-right'>$edit_button $delete_button</div>";


						}
					}


					$k_row++;

				}


			} else {

				//for single values

				$array['val'] = isset($content_item[$key]) ? " " . $content_item[$key] : ' ';
				$array['val2'][0] = isset($content_item[$key]) ? " " . $content_item[$key] : ' ';

				// for images
				if (isset($value['input_type']) and $value['input_type'] == 'image') {
					if (!empty($content_item[$key])) {
						//$img=$this->config->item('image_upload_path').$content_item[$key]."_thumb.jpg";
						//$array['val2'][0]= img($img);


						$delete_picture_button = get_top_button('all', 'Remove picture', 'op/remove_picture/' . $current_operation . '/' . $table_config['table_name'] . '/' . $table_config['table_id'] . '/' . $key . '/' . $ref_id, '', 'fa-close', '', 'btn-danger', FALSE);


						$array['val2'][0] = '<img src="' . display_picture_from_db($content_item[$key]) . '"/> ' . $delete_picture_button;
					}

				}



				if (isset($content_item[$key . '_idd']) and $value['input_select_source'] == 'table' and isset($value['input_select_source_type']) and $value['input_select_source_type'] == 'drill_down') {

					$Tconfig = explode(';', $value['input_select_values']);
					// si la valeur est déjà enregistré
					if ($content_item[$key . '_idd'] != 0) {
						$edit_button = "";
						$delete_button = "";

						if (!((isset($value['drill_down_type']) and $value['drill_down_type'] == 'not_linked') or !$editable)) {

							if (!empty($value_field['drilldown_edit_link'])) {
								$edit_button = create_button_link('manager/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $content_item[$key . '_idd'], '<i class="fa fa-pencil"></i> Edit', "btn-info", 'Edit ');
							}
							if (!empty($value_field['drilldown_remove_link'])) {
								//	$delete_button = create_button_link('manager/remove_drilldown/'.$content_item[$key.'_idd'].'/'.$Tconfig[0].'/'.$ref_table.'/'.$ref_id.'/'.$key,'<i class="fa fa-times"></i> Remove',"btn-danger",'Remove ','onlist','alert_ok');
								$delete_button = create_button_link($value_field['drilldown_remove_link'] . $content_item[$key . '_idd'] . '/' . $ref_id, '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');

							}
						}

						if (!empty($value_field['drilldown_display_link'])) {
							$array['val'] = "<span class='drilldown_link'>" . anchor($value_field['drilldown_display_link'] . $content_item[$key . '_idd'], $array['val']) . "</span> <div class='navbar-right'>$edit_button.$delete_button</div>";
							$array['val2'][0] = "<span class='drilldown_link'>" . anchor($value_field['drilldown_display_link'] . $content_item[$key . '_idd'], $array['val2'][0]) . "</span> <div class='navbar-right'>$edit_button.$delete_button</div>";
						}


					} else { // si la valeur n'est pas encore enregistré

						$add_button = "";
						if ((isset($value['drill_down_type']) and $value['drill_down_type'] == 'not_linked') or !$editable or empty($value_field['drilldown_add_link'])) {
							$add_button = "";
						} else {
							$array['edit'] = 1;
							//$add_button = create_button_link($value_field['drilldown_add_link'].$ref_id,'<i class="fa fa-plus"></i> Add',"btn-success",'Add ');

							if ($ref_table == 'classification') { //use modal for classification
								$modal_title = "Add : " . $value['field_title'];
								//TODO correct this line for classification
								//$add_button='<a  class="btn btn-xs btn-success" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manager/add_element_drilldown_modal/'.$Tconfig[0].'/'.$ref_table.'/'.$key.'/'.$ref_id.'"  data-modal_title="'.$modal_title.'" ><i class="fa fa-plus"></i>Add</a>';
							}


						}
						$array['val'] = "<span>: " . $add_button . "</span>";
						$array['val2'][0] = "<span>: " . $add_button . "</span>";
					}
				} else {



					if ($array['val'] == '0' and empty($value['display_null'])) { //Avoid displaying zero in empty number fields
						///print_test($array);
						$array['val'] = "";
						$array['val2'][0] = "";
					}


				}




			}


			array_push($item_data, $array);



		}

		return $item_data;

	}

	//returns the constructed $menu array representing the left menu structure, which can be used in the application's UI to generate the menu
	function get_left_menu()
	{
		$project_published = project_published();
		$menu = array();

		$menu['general'] = array(
			'label' => 'General'
		);


		$menu['general']['menu']['home'] = array('label' => 'Dashboard', 'url' => 'home', 'icon' => 'th');

		$menu['general']['menu']['papers'] = array('label' => 'Papers  in this phase', 'url' => '', 'icon' => 'newspaper-o');

		$menu['general']['menu']['papers']['sub_menu']['all_papers'] = array('label' => 'All', 'url' => 'paper/list_paper', '');
		$menu['general']['menu']['papers']['sub_menu']['pending'] = array('label' => 'Pending', 'url' => 'paper/list_paper/pending', '');

		$menu['general']['menu']['papers']['sub_menu']['processed'] = array('label' => 'Classified', 'url' => 'paper/list_paper/processed', '');

		$menu['general']['menu']['papers']['sub_menu']['excluded'] = array('label' => 'Excluded', 'url' => 'paper/list_paper/excluded', '');

		//$menu['general']['menu']['papers']['sub_menu']['assigned_me']=array( 'label'=>'Assigned to me', 'url'=>'paper/list_paper/assigned_me', '');

		//$menu['general']['menu']['papers']['sub_menu']['assigned_me']=array( 'label'=>'Assigned to me', 'url'=>'paper/list_paper/assigned_me', '');

		$menu['general']['menu']['venues'] = array('label' => 'Venues', 'url' => 'element/entity_list/list_venues', 'icon' => 'th');
		$menu['general']['menu']['authors'] = array('label' => 'Authors', 'url' => 'element/entity_list/list_authors', 'icon' => 'users');
		$menu['general']['menu']['authors']['sub_menu']['all_authors'] = array('label' => 'All', 'url' => 'element/entity_list/list_authors_class', '');
		$menu['general']['menu']['authors']['sub_menu']['first_authors'] = array('label' => 'First authors', 'url' => 'element/entity_list/list_first_authors_class', '');
		$menu['general']['menu']['authors']['sub_menu']['affiliation'] = array('label' => 'Affiliations', 'url' => 'element/entity_list/list_affiliation', '');


		$menu['general']['menu']['reference'] = array('label' => 'Reference Categories', 'url' => '', 'icon' => 'table');
		$reftables = $this->CI->manage_mdl->get_reference_tables_list();
		foreach ($reftables as $key => $value) {

			$menu['general']['menu']['reference']['sub_menu'][$value['reftab_label']] = array('label' => $value['reftab_desc'], 'url' => 'element/entity_list/list_' . $value['reftab_label'], 'icon' => '');
		}

		$menu['general']['menu']['class'] = array('label' => 'Classification', 'url' => '', 'icon' => 'search');
		if (!$project_published)
			$menu['general']['menu']['class']['sub_menu']['classify'] = array('label' => 'Classify', 'url' => 'element/entity_list/list_class_assignment_pending_mine', '');
		$menu['general']['menu']['class']['sub_menu']['my_classify'] = array('label' => 'My Assignments', 'url' => 'element/entity_list/list_class_assignment_mine', '');
		$menu['general']['menu']['class']['sub_menu']['my_classify_done'] = array('label' => 'My Classified', 'url' => 'element/entity_list/list_class_assignment_done_mine', '');
		$menu['general']['menu']['class']['sub_menu']['my_classify_pending'] = array('label' => 'My Pending', 'url' => 'element/entity_list/list_class_assignment_pending_mine', '');
		$menu['general']['menu']['class']['sub_menu']['all_classify'] = array('label' => 'All Assignments', 'url' => 'element/entity_list/list_class_assignment', '');
		$menu['general']['menu']['class']['sub_menu']['classify_progress'] = array('label' => 'Progress', 'url' => 'data_extraction/class_completion', '');


		$menu['general']['menu']['res'] = array('label' => 'Result', 'url' => '', 'icon' => 'pie-chart');

		$menu['general']['menu']['res']['sub_menu']['classify'] = array('label' => 'Table', 'url' => 'element/entity_list/list_classification', '');
		$menu['general']['menu']['res']['sub_menu']['evolution'] = array('label' => 'Chart', 'url' => 'element/entity_list_graph/list_classification', '');
		//$menu['general']['menu']['res']['sub_menu']['sistribution']=array( 'label'=>' Evolution', 'url'=>'element/entity_list_graph/list_classification/line', '');
		$menu['general']['menu']['res']['sub_menu']['export'] = array('label' => 'Export', 'url' => 'reporting/result_export', '');


		if (get_appconfig_element('class_validation_on')) {

			$menu['general']['menu']['qa_val'] = array('label' => 'Validation', 'url' => '', 'icon' => 'check-square-o');
			if (can_validate_project())
				$menu['general']['menu']['qa_val']['sub_menu']['validate'] = array('label' => 'Validate', 'url' => 'element/entity_list/list_class_validation_mine', 'icon' => '');
			$menu['general']['menu']['qa_val']['sub_menu']['all_assignement'] = array('label' => 'All Assignments', 'url' => 'element/entity_list/list_class_assignment_val', 'icon' => '');
			$menu['general']['menu']['qa_val']['sub_menu']['validated'] = array('label' => 'Validated papers', 'url' => 'element/entity_list/list_class_validation', 'icon' => '');
			$menu['general']['menu']['qa_val']['sub_menu']['classify_progress'] = array('label' => 'Progress', 'url' => 'data_extraction/class_completion/validate', '');
		}

		if (can_manage_project())
			$menu['general']['menu']['sql_query'] = array('label' => 'Query Database', 'url' => 'home/sql_query', 'icon' => 'database');


		$menu['adm'] = array(
			'label' => 'ADMINISTRATION'
		);

		$menu['adm']['menu']['plan'] = array('label' => 'Planning', 'url' => '', 'icon' => 'th');
		if (can_manage_project() and !$project_published)
			$menu['adm']['menu']['plan']['sub_menu']['assignment_screen'] = array('label' => 'Assign Classification', 'url' => 'data_extraction/class_assignment_set', 'icon' => '');

		if (can_validate_project() and !$project_published)
			$menu['adm']['menu']['plan']['sub_menu']['validate_screen_assign'] = array('label' => 'Assign Validation', 'url' => 'data_extraction/class_assignment_validation_set', 'icon' => '');

		$menu['adm']['menu']['plan']['sub_menu']['exclusioncrieria'] = array('label' => 'Exclusion Criteria', 'url' => 'element/entity_list/list_exclusioncrieria', 'icon' => '');
		if (can_manage_project())
			$menu['adm']['menu']['plan']['sub_menu']['general'] = array('label' => 'Settings ', 'url' => 'element/display_element/configurations/1', 'icon' => '');

		/*
			  $menu['general']['menu']['assignment_validation_set']=array( 'label'=>'Assign', 'url'=>'data_extraction/class_assignment_validation_set', 'icon'=>'');
				  
			  $menu['general']['menu']['assignment_set']=array( 'label'=>'Assign for classiffication', 'url'=>'data_extraction/class_assignment_set', 'icon'=>'user');
			  $menu['general']['menu']['assignment_validation_set']=array( 'label'=>'Assign for  validation', 'url'=>'data_extraction/class_assignment_validation_set', 'icon'=>'user');
			  $menu['general']['menu']['assignment_list']=array( 'label'=>'List of assignment', 'url'=>'element/entity_list/list_class_assignment', 'icon'=>'th');
			  $menu['general']['menu']['list_class_assignment_done']=array( 'label'=>'List of assignment done', 'url'=>'element/entity_list/list_class_assignment_done', 'icon'=>'th');
			  $menu['general']['menu']['list_class_assignment_pending']=array( 'label'=>'List of assignment pending', 'url'=>'element/entity_list/list_class_assignment_pending', 'icon'=>'th');
		  
			  
			  
			  $menu['general']['menu']['list_class_assignment_mine']=array( 'label'=>'List of assignment mine', 'url'=>'element/entity_list/list_class_assignment_mine', 'icon'=>'th');
			  $menu['general']['menu']['list_class_assignment_done_mine']=array( 'label'=>'List of my assignment done', 'url'=>'element/entity_list/list_class_assignment_done_mine', 'icon'=>'th');
			  $menu['general']['menu']['list_class_assignment_pending_mine']=array( 'label'=>'List of my assignment pending', 'url'=>'element/entity_list/list_class_assignment_pending_mine', 'icon'=>'th');
			  
			  $menu['general']['menu']['assignment_list_val']=array( 'label'=>'List of assignment validation', 'url'=>'element/entity_list/list_class_assignment_val', 'icon'=>'th');
			  //$menu['general']['menu']['list_val']=array( 'label'=>'List of validation', 'url'=>'element/entity_list/list_class_validation', 'icon'=>'check');
			  
			  
			  
			  $menu['general']['menu']['classification']=array('label'=>'Classification','url'=>'element/entity_list/list_classification','icon'=>'list');
			  //$menu['general']['menu']['classificationss']=array('label'=>'--Classification Graph','url'=>'element/entity_list_graph/list_classification','icon'=>'list');
			  
			  
			  
			  $menu['general']['menu']['result']=array('label'=>'Result','url'=>'','icon'=>'pie-chart');
			  
			  $menu['general']['menu']['result']['sub_menu']['result_graph']=array( 'label'=>'Graphs', 'url'=>'reporting/result_graph', '');
			  $menu['general']['menu']['result']['sub_menu']['result_graph']=array( 'label'=>'Graphs', 'url'=>'element/entity_list_graph/list_classification', '');
			  
			  $menu['general']['menu']['result']['sub_menu']['result_export']=array( 'label'=>'Export', 'url'=>'reporting/result_export', '');
			  */





		return $menu;
	}

	//generate a left menu structure specifically for the Quality Assessment (QA) phase
	function get_left_menu_qa()
	{
		$project_published = project_published();
		$can_manage_project = can_manage_project();
		$menu = array();

		$menu['general'] = array(
			'label' => 'General'
		);
		$menu['general']['menu']['home'] = array('label' => 'Dashboard', 'url' => 'quality_assessment/qa', 'icon' => 'th');

		$menu['general']['menu']['papers'] = array('label' => 'Papers in this phase', 'url' => '', 'icon' => 'newspaper-o');

		$menu['general']['menu']['papers']['sub_menu']['all_papers'] = array('label' => 'All', 'url' => 'element/entity_list/list_qa_papers', '');
		$menu['general']['menu']['papers']['sub_menu']['screen_paper_pending'] = array('label' => 'Pending', 'url' => 'element/entity_list/list_qa_papers_pending', '');
		$menu['general']['menu']['papers']['sub_menu']['screen_paper_included'] = array('label' => 'Assessed', 'url' => 'element/entity_list/list_qa_papers_done', '');
		$menu['general']['menu']['papers']['sub_menu']['screen_paper_excluded'] = array('label' => 'Excluded', 'url' => 'quality_assessment/qa_conduct_result/excluded', '');



		$menu['general']['menu']['qa'] = array('label' => 'Quality assessment', 'url' => '', 'icon' => 'list');
		if (!$project_published)
			$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_pending'] = array('label' => 'Assess', 'url' => 'quality_assessment/qa_conduct_list/mine/0/pending', '');
		$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list'] = array('label' => 'My Assignments', 'url' => 'quality_assessment/qa_conduct_list', '');
		$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_done'] = array('label' => 'My Assessed', 'url' => 'quality_assessment/qa_conduct_list/mine/0/done', '');

		if ($can_manage_project) {
			$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_all'] = array('label' => 'All Assignments', 'url' => 'quality_assessment/qa_conduct_list/all', '');
			//$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_all_pending']=array( 'label'=>'All QA - pending', 'url'=>'quality_assessment/qa_conduct_list/all/0/pending', '');
			$menu['general']['menu']['qa']['sub_menu']['qa_conduct_list_all_done'] = array('label' => 'All Assessed', 'url' => 'quality_assessment/qa_conduct_list/all/0/done', '');
			$menu['general']['menu']['qa']['sub_menu']['progress'] = array('label' => 'Progress', 'url' => 'quality_assessment/qa_completion', '');
		}

		$menu['general']['menu']['list_qa_result'] = array('label' => 'Results', 'url' => 'quality_assessment/qa_conduct_result', 'icon' => 'th');
		if (get_appconfig_element('qa_validation_on')) {
			$menu['general']['menu']['qa_val'] = array('label' => 'Validation', 'url' => '', 'icon' => 'check-square-o');

			//$menu['general']['menu']['qa_val']['sub_menu']['list_qa_assignment']=array( 'label'=>'Assignment for quality assessment Validation', 'url'=>'element/entity_list/list_qa_validation_assignment', '');
			if (can_validate_project())
				if (!$project_published)
					$menu['general']['menu']['qa_val']['sub_menu']['qa_conduct_list_pending'] = array('label' => 'Validate', 'url' => 'quality_assessment/qa_conduct_list_val/mine/0/pending', '');
			$menu['general']['menu']['qa_val']['sub_menu']['qa_conduct_list_mine'] = array('label' => 'All Assignments', 'url' => 'quality_assessment/qa_conduct_list_val/all', '');

			//$menu['general']['menu']['qa_val']['sub_menu']['qa_conduct_list_mine_pending']=array( 'label'=>'All validations - pending', 'url'=>'quality_assessment/qa_conduct_list_val/all/0/pending', '');
			$menu['general']['menu']['qa_val']['sub_menu']['qa_conduct_list_mine_done'] = array('label' => 'Validated papers', 'url' => 'quality_assessment/qa_conduct_list_val/all/0/done', '');
			$menu['general']['menu']['qa_val']['sub_menu']['progress'] = array('label' => 'Progress', 'url' => 'quality_assessment/qa_completion/validate', '');

			$menu['general']['menu']['qa_val']['sub_menu']['list_qa_result'] = array('label' => 'Results', 'url' => 'element/entity_list/list_qa_validation', '');
		}
		if ($can_manage_project) {
			//	$menu['general']['menu']['questions']=array( 'label'=>'Questions', 'url'=>'element/entity_list/list_qa_questions', 'icon'=>'question-circle');
			//	$menu['general']['menu']['responses']=array( 'label'=>'Responses', 'url'=>'element/entity_list/list_qa_responses', 'icon'=>'check-circle');
		}



		if ($can_manage_project and !$project_published) {
			$menu['adm'] = array(
				'label' => 'ADMINISTRATION'
			);
			$menu['adm']['menu']['plan'] = array('label' => 'Planning', 'url' => '', 'icon' => 'th');

			$menu['adm']['menu']['plan']['sub_menu']['qa_assignment_set'] = array('label' => 'Assign for QA ', 'url' => 'quality_assessment/qa_assignment_set', 'icon' => '');
			$menu['adm']['menu']['plan']['sub_menu']['qa_assignment_validation_set'] = array('label' => 'Assign Validation', 'url' => 'quality_assessment/qa_assignment_validation_set', 'icon' => '');
			$menu['adm']['menu']['plan']['sub_menu']['questions'] = array('label' => 'Questions', 'url' => 'element/entity_list/list_qa_questions', 'icon' => '');
			$menu['adm']['menu']['plan']['sub_menu']['responses'] = array('label' => 'Answers', 'url' => 'element/entity_list/list_qa_responses', 'icon' => '');
			$menu['adm']['menu']['plan']['sub_menu']['general'] = array('label' => 'Settings ', 'url' => 'element/display_element/configurations/1', 'icon' => '');


		}
		return $menu;
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

	//generating a left menu screen for the admin
	function get_left_menu_admin()
	{

		$menu = array();

		$menu['general'] = array(
			'label' => 'General'
		);


		$menu['general']['menu']['home'] = array('label' => 'Projects', 'url' => 'project/projects_list', 'icon' => 'home');

		//if(has_usergroup(1) OR has_usergroup(2))
		//$menu['general']['menu']['new_project_editor']=array('label'=>'New project','url'=>'project/new_project_editor','icon'=>'plus');

		if (has_usergroup(1)) {
			$menu['general']['menu']['usersn'] = array('label' => 'Users', 'url' => 'element/entity_list/list_all_users', 'icon' => 'user');
			//	$menu['general']['menu']['usergroup']=array('label'=>'User Groups','url'=>'element/entity_list/list_usergroups','icon'=>'users');
			$menu['general']['menu']['sql_query'] = array('label' => 'Query Database', 'url' => 'home/sql_query', 'icon' => 'database');

			$menu['adm'] = array('label' => 'Administration');


			$menu['adm']['menu']['logs'] = array('label' => 'Logs', 'url' => 'element/entity_list/list_logs', 'icon' => 'sliders');
			$menu['adm']['menu']['str_mng'] = array('label' => 'Label Mangement', 'url' => 'element/entity_list/list_str_mng', 'icon' => 'text-width');

			$menu['adm']['menu']['configuration'] = array('label' => 'Settings', 'url' => 'element/display_element/admin_config/1', 'icon' => 'cog');
			$menu['adm']['menu']['info'] = array('label' => 'Home page settings', 'url' => 'element/entity_list/list_info', 'icon' => 'info');
			$menu['adm']['menu']['Configuration_managment'] = array('label' => 'Configuration_managment', 'url' => 'admin/list_configurations', 'icon' => 'cog');

		}

		if (debug_coment_active()) {
			$menu['general']['menu']['debug'] = array('label' => 'Debug comment', 'url' => 'element/entity_list/list_debug', 'icon' => 'cogs');
		}
		return $menu;
	}

	/*
		retrieve the completion status of paper classifications or validations for either a specific user or all users. 
		The completion status includes the total number of papers, the number of processed papers, and the number of pending papers
	*/
	function get_classification_completion($type = 'class', $user = '')
	{
		//all
		if (($user == 'all')) {
			if ($type == 'validation') {
				$papers_all = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('assigned', array('assigned_active' => 1, 'assignment_type' => 'Validation'))
					->num_rows();

				$papers_done = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('view_class_validation_done', array('assigned_active' => 1))
					->num_rows();
			} else {
				$papers_all = $this->CI->Paper_dataAccess->count_papers('all');
				$papers_done = $this->CI->Paper_dataAccess->count_papers('processed');
			}

		} else {
			if (empty($user)) {
				$user = active_user_id();
			}

			if ($type == 'validation') {
				$papers_all = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('assigned', array('assigned_active' => 1, 'assignment_type' => 'Validation', 'assigned_user_id' => $user))
					->num_rows();

				$papers_done = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('view_class_validation_done', array('assigned_active' => 1, 'assigned_user_id' => $user))
					->num_rows();
			} else {


				$sql = "select assigned_id 
						from assigned,paper 
						where
							paper.id= assigned.assigned_paper_id
							AND paper.paper_excluded=0
							AND assigned_active=1 
							AND assignment_type='Classification'
							AND assigned_user_id = '$user'
						";
				$papers_all = $this->CI->db_current->query($sql)->num_rows();

				$papers_done = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('view_class_assignment_done', array('assigned_active' => 1, 'assignment_type' => 'Classification', 'assigned_user_id' => $user))
					->num_rows();
			}
		}
		$res['all_papers'] = $papers_all;
		$res['processed_papers'] = $papers_done;
		$res['pending_papers'] = 0;
		if (!empty($res['all_papers']))
			$res['pending_papers'] = $papers_all - $papers_done;

		return $res;
		//print_test($res);

	}

}