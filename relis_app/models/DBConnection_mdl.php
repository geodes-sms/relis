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
class DBConnection_mdl extends CI_Model
{
	function __construct()
	{
		parent::__construct();
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
	/*
	 * Function to call the stored procedure which retrieves the list of elements according to the parameters received
	 * Input: $ ref_table_config: the name given to the structure of the table to retrieve
	 * $ val: contains a search criteria if there is if not contains '_'
	 * $ page: return the list from which element?
	 * $ rec_per_page: number of items to recover
	 * $ extra_condition: other search criteria
	 */

	function get_list($ref_table_config, $val = '_', $page = 0, $rec_per_page = 0, $extra_condition = '')
	{


		$config = $ref_table_config['config_label'];

		if (!admin_config($config)) {
			$this->db2 = $this->load->database(project_db(), TRUE);
		}


		if ($val != '_') {
			$search = $val;
		} else {
			$search = "";
		}

		if (admin_config($config)) {
			$data = $this->db->query("CALL get_list_" . $config . "(0,0,'" . $search . "') ");
			mysqli_next_result($this->db->conn_id);

		} else {
			$data = $this->db2->query("CALL get_list_" . $config . "(0,0,'" . $search . "') ");
			mysqli_next_result($this->db2->conn_id);
		}


		$result['nombre'] = $data->num_rows();


		if ($rec_per_page == 0) {
			$rec_per_page = $this->config->item('rec_per_page');
		} elseif ($rec_per_page == -1) {
			$rec_per_page = 0;
		}

		if (admin_config($config)) {
			$data = $this->db->query("CALL get_list_" . $config . "(" . $page . "," . $rec_per_page . ",'" . $search . "') ");
			mysqli_next_result($this->db->conn_id);
		} else {
			$data = $this->db2->query("CALL get_list_" . $config . "(" . $page . "," . $rec_per_page . ",'" . $search . "') ");
			mysqli_next_result($this->db2->conn_id);
		}

		$result['list'] = $data->result_array();


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
	/*
	 * Function to call the stored procedure which retrieves the list of character strings according to the language
	 */

	function get_list_str_mng($ref_table_config, $val = '_', $page = 0, $rec_per_page = 0, $language = 'en')
	{

		$this->db2 = $this->load->database(project_db(), TRUE);
		$config = $ref_table_config['config_label'];


		if ($val != '_') {
			$search = $val;
		} else {
			$search = "";
		}

		$data = $this->db2->query("CALL get_list_str_mng(0,0,'" . $search . "','" . $language . "') ");
		mysqli_next_result($this->db2->conn_id);
		$result['nombre'] = $data->num_rows();


		if ($rec_per_page == 0) {
			$rec_per_page = $this->config->item('rec_per_page');
		} elseif ($rec_per_page == -1) {
			$rec_per_page = 0;
		}

		$data = $this->db2->query("CALL get_list_" . $config . "(" . $page . "," . $rec_per_page . ",'" . $search . "','" . $language . "') ");

		mysqli_next_result($this->db2->conn_id);
		$result['list'] = $data->result_array();


		return $result;

	}

	/*
	 * Fonction pour pour récupérer les personnés à qui un papier est assigné
	 * Function to retrieve the people to whom a paper is assigned
	 */
	function get_assignations($paper_id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_assignations(" . $paper_id . ") ");

		mysqli_next_result($this->db2->conn_id);
		$results = $data->result_array();

		return $results;
	}

	/*
	 * Fonction pour récupérer les informations sur l'exclusion d'un papier
	 * Function to retrieve information on the exclusion of a paper
	 */
	function get_exclusion($paper_id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_paper_exclusion_info(" . $paper_id . ") ");

		mysqli_next_result($this->db2->conn_id);

		$results = $data->row_array();

		return $results;
	}

	/*
	 * Fonction pour récupérer les informations sur l'inclusion d'un papier
	 */
	function get_inclusion($paper_id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_paper_inclusion_info(" . $paper_id . ") ");

		mysqli_next_result($this->db2->conn_id);

		$results = $data->row_array();

		return $results;
	}
	/*
	 * Fonction pour récupérer le nom de la table utilisé pas une table de reference
	 * Function to retrieve the name of the table used by a reference table
	 */
	function get_reference_corresponding_table($ref_config)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_reference_table('" . $ref_config . "') ");

		mysqli_next_result($this->db2->conn_id);

		$results = $data->row_array();
		return $results;

	}


	/*
	 * Fonction pour récupérer le detail d'un élément de la table de reference
	 * Function to retrieve the detail of an element from the reference table
	 */
	function get_reference_details($table_name, $table_id, $ref_id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_row('" . $table_name . "','" . $table_id . "','" . $ref_id . "') ");

		mysqli_next_result($this->db2->conn_id);

		$results = $data->row_array();

		//print_test($results);
		return $results;
	}

	/*
			  * Fonction pour récupérer le détail d'un élément de la table de référence
			  * Function to retrieve the detail of an element from the reference table

			  */
	function get_row_details($config, $ref_id, $stored_procedure_provided = False, $tab_config = "")
	{
		if (empty($tab_config)) {
			$tab_config = $config;
		}



		if (!admin_config($tab_config)) {
			$this->db2 = $this->load->database(project_db(), TRUE);

		}


		$stored_procedure = $stored_procedure_provided ? $config : "get_detail_" . $config;

		if (admin_config($tab_config)) {
			$data = $this->db->query("CALL " . $stored_procedure . "('" . $ref_id . "') ");
			mysqli_next_result($this->db->conn_id);

		} else {
			$data = $this->db2->query("CALL " . $stored_procedure . "('" . $ref_id . "') ");
			mysqli_next_result($this->db2->conn_id);
		}


		$results = $data->row_array();

		//print_test($results);
		return $results;
	}

	/*
	 * Fonction pour récupérer les valeurs à mettre dans un select box pour un element donnée;
	 * $ref_table_config : configuration de la table de l'élément
	 * $ref_table_field: le champs concerné
	 * $extra_condition: critère de recherche
	 */
	/*
	 *  Function to retrieve the values to put in a select box for a given element;
	 */
	function get_reference_select_values($ref_table_config, $ref_table_field, $extra_condition = "")
	{

		$extra_condition = str_replace("'", "\'", $extra_condition);

		$sql_append = " AND " . $ref_table_config['table_active_field'] . "=1 " . $extra_condition;



		if (empty($ref_table_config['order_by'])) {
			$order_by = "";
		} else {
			$order_by = " ORDER BY " . $ref_table_config['order_by'];
		}
		$config = $ref_table_config['config_label'];

		if (!admin_config($config)) {
			$this->db2 = $this->load->database(project_db(), TRUE);
		}

		if (admin_config($config)) {
			$data = $this->db->query("CALL get_list('" . $ref_table_config['table_name'] . "','" . $ref_table_config['table_id'] . " AS refId," . $ref_table_field . " AS refDesc ',' " . $sql_append . $order_by . " ') ");
			mysqli_next_result($this->db->conn_id);
		} else {
			$data = $this->db2->query("CALL get_list('" . $ref_table_config['table_name'] . "','" . $ref_table_config['table_id'] . " AS refId," . $ref_table_field . " AS refDesc ',' " . $sql_append . $order_by . " ') ");
			mysqli_next_result($this->db2->conn_id);

		}

		$result = $data->result_array();

		return $result;


	}


	/*
	 * Fonction pour récupérer la liste des tables de réferences
	 * Function to retrieve the list of reference tables
	 */
	function get_reference_tables_list($target_db = 'current')
	{

		$target_db = ($target_db == 'current') ? project_db() : $target_db;

		$this->db2 = $this->load->database($target_db, TRUE);
		$data = $this->db2->query("CALL get_reference_tables_list() ");

		mysqli_next_result($this->db2->conn_id);
		$result = $data->result_array();
		//print_test($result);
		return $result;
	}


	/*
	 * Fonction pour supprimer un élément
	 * Function to delete an element
	 */
	function remove_element($id, $config, $strored_procedure_provided = False)
	{
		if ($strored_procedure_provided) {
			$strored_procedure = $config;
		} else {
			$strored_procedure = " remove_" . $config;
		}
		if (!admin_config($config)) {
			$this->db2 = $this->load->database(project_db(), TRUE);
		}


		if (admin_config($config)) {
			$result = $this->db->query("CALL " . $strored_procedure . "(" . $id . ")");
		} else {
			$result = $this->db2->query("CALL " . $strored_procedure . "(" . $id . ")");
		}

		return $result;
	}


	/*
	 * Fonction pour appeler les procedures stockées utilisé pour suvegarder un nouvel élément, ou un élément modifier
	 * INPUT : $content : un tableux avec la stucture de la tables et les info à mettre
	 * $type: sortie attendue: Id de lélement enregistré ou resultat de la requête d'insertion ou de modification
	 */
	/*
	 * Function to call stored procedures used to save a new element, or an element to modify
	 * INPUT: $ content: a table with the structure of the tables and the info to put
	 * $ type: expected output: Id of the recorded element or result of the insertion or modification request
	 */
	function save_reference($content, $type = 'normal')
	{


		$config = $content['table_config'];
		if (admin_config($config)) {
			$this->db3 = $this->load->database('default', TRUE);
		} else {
			$this->db3 = $this->load->database(project_db(), TRUE);
		}

		$table_config = get_table_config($config);


		if ($content['operation_type'] == 'new') {
			$param = "";
			$i = 0;

			foreach ($table_config['fields'] as $key => $value) {


				if (($value['on_add'] != 'not_set' and $value['on_add'] != 'drill_down' and $value['on_add'] != 'disabled') and !((isset($value['multi-select']) and isset($value['multi-select']) == 'Yes'))) {
					if (!empty($content[$key])) {
						$val = $content[$key];
					} else {
						$val = NULL;
					}
					if ($i == 0) {

						$param .= "'" . mysqli_real_escape_string($this->db3->conn_id, $val) . "'";


					} else {
						$param .= ",'" . mysqli_real_escape_string($this->db3->conn_id, $val) . "'";

					}
					$i = 1;
				}
			}
			$stored_procedure = " CALL add_" . $config . "($param)";

		} else {
			$param = "";
			$i = 0;

			foreach ($table_config['fields'] as $key => $value) {


				if (($value['on_edit'] != 'not_set' and $value['on_edit'] != 'drill_down' and $value['on_edit'] != 'disabled') and !((isset($value['multi-select']) and isset($value['multi-select']) == 'Yes'))) {
					if (!empty($content[$key])) {
						$val = $content[$key];
					} else {
						$val = NULL;
					}
					if ($i == 0) {

						if (isset($value['input_type']) and $value['input_type'] == 'date' and empty($val)) {
							$param .= "NULL";
						} else {
							$param .= "'" . mysqli_real_escape_string($this->db3->conn_id, $val) . "'";
						}



					} else {


						if (isset($value['input_type']) and $value['input_type'] == 'date' and empty($val)) {
							$param .= ", " . "NULL";
						} else {
							$param .= ",'" . mysqli_real_escape_string($this->db3->conn_id, $val) . "'";
						}
					}
					$i = 1;
				}
			}
			$id = $content[$table_config['table_id']];
			$stored_procedure = " CALL update_" . $config . "($id , $param)";
		}

		//echo $stored_procedure;
		//exit;
		if ($content['operation_type'] == 'new') {

			$data = $this->db3->query($stored_procedure);
			//echo $this->db->last_query();
			mysqli_next_result($this->db3->conn_id);


			$res = $data->row_array();
			if (!empty($res)) {
				$result = 1;
				$id = $res['id_value'];
			} else {
				$result = 0;
				$id = 0;
			}


		} else {
			if ($this->db3->simple_query($stored_procedure)) {
				$result = 1;
			} else {
				$result = 0;
			}
		}


		//print_test($result); exit;

		if ($type == 'get_id') {
			return $id;
		} else {
			return $result;
		}
	}

	function save_reference_mdl($content, $type = 'normal')
	{


		$config = $content['table_config'];
		if (admin_config($config)) {
			$this->db3 = $this->load->database('default', TRUE);
		} else {
			$this->db3 = $this->load->database(project_db(), TRUE);
		}


		//print_test($content);

		//exit;
		$table_config = get_table_configuration($config);
		//print_test($table_config);
		$current_operation = $content['current_operation'];
		$param = "";
		$i = 0;

		foreach ($table_config['operations'][$current_operation]['fields'] as $key => $v_field) {

			$value = $table_config['fields'][$key];
			if ($v_field['field_state'] != 'drill_down' and $v_field['field_state'] != 'disabled' and !((isset($value['multi-select']) and isset($value['multi-select']) == 'Yes'))) {
				//print_test($key);
				if (!empty($content[$key])) {
					$val = $content[$key];
				} else {
					$val = NULL;
				}
				if ($i == 0) {

					if (isset($value['input_type']) and $value['input_type'] == 'date' and empty($val)) {
						$param .= "NULL";
					} else {
						$param .= "'" . mysqli_real_escape_string($this->db3->conn_id, $val) . "'";
					}



				} else {
					if (isset($value['input_type']) and $value['input_type'] == 'date' and empty($val)) {
						$param .= ", " . "NULL";
					} else {
						$param .= ",'" . mysqli_real_escape_string($this->db3->conn_id, $val) . "'";

					}

				}
				$i = 1;
			}

		}

		if ($content['operation_type'] == 'new') {

			if (!empty($table_config['operations'][$current_operation]['db_save_model'])) {
				$stored_procedure = " CALL " . $table_config['operations'][$current_operation]['db_save_model'] . "($param)";
			} else {
				$stored_procedure = " CALL add_" . $config . "($param)";
			}
		} else {


			$id = $content[$table_config['table_id']];

			if (!empty($table_config['operations'][$current_operation]['db_save_model'])) {
				$stored_procedure = " CALL " . $table_config['operations'][$current_operation]['db_save_model'] . "($id ,$param)";
			} else {
				$stored_procedure = " CALL update_" . $config . "($id , $param)";
			}
			//$stored_procedure=" CALL update_".$config."($id , $param)";
		}

		//echo $stored_procedure;
		//exit;
		if ($content['operation_type'] == 'new') {

			$data = $this->db3->query($stored_procedure);
			//echo $this->db->last_query();
			mysqli_next_result($this->db3->conn_id);


			$res = $data->row_array();
			if (!empty($res)) {
				$result = 1;
				$id = $res['id_value'];
			} else {
				$result = 0;
				$id = 0;
			}


		} else {
			if ($this->db3->simple_query($stored_procedure)) {
				$result = 1;
			} else {
				$result = 0;
			}
		}


		//print_test($result); exit;

		if ($type == 'get_id') {
			return $id;
		} else {
			return $result;
		}
	}

	/*
	 * Fonction pour récupérer la correspondance d'une chaine de caractère dans une langue donnée
	 * Function to retrieve the correspondence of a character string in a given language
	 */
	function get_str($str, $category = "default", $lang = 'en')
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_string('" . $str . "','" . $category . "','" . $lang . "')");
		mysqli_next_result($this->db2->conn_id);
		$result = $data->row_array();
		return $result;
	}

	/*
	 * Fonction pour ajouter la correspondance d'une chaine de caractère dans une langue donnée
	 * Function to add the correspondence of a character string in a given language
	 */
	function set_str($str, $category = "default", $lang = 'en')
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL add_string('','" . $str . "','" . $str . "','" . $lang . "','" . $category . "')");
		mysqli_next_result($this->db2->conn_id);
		$res = $data->row_array();
		if (!empty($res)) {
			$result = 1;

		} else {
			$result = 0;

		}

		return $result;
	}

	/*
	 * Fonction pour récuperer les champs supplementaires à afficher dans la liste des classification (Scope, Intent, Intent relation)
	 * Function to retrieve additional fields to display in the classification list (Scope, Intent, Intent relation)
	 */
	function get_extra_fields($class_id)
	{

		$this->db2 = $this->load->database(project_db(), TRUE);
		//scope
		$result['class_scope'] = " - ";
		$result['class_intent'] = " - ";
		$result['class_intent_relation'] = " - ";

		$data = $this->db2->query("CALL getMTScope('" . $class_id . "')");
		mysqli_next_result($this->db2->conn_id);
		$res = $data->result_array();

		$i = 1;
		foreach ($res as $key => $value) {
			if ($i == 1)
				$result['class_scope'] = $value['ref_value'];
			else
				$result['class_scope'] .= " | " . $value['ref_value'];

			$i++;
		}



		$data = $this->db2->query("CALL getMTIntents('" . $class_id . "')");
		mysqli_next_result($this->db2->conn_id);
		$res = $data->result_array();

		$i = 1;
		foreach ($res as $key => $value) {
			if ($i == 1)
				$result['class_intent'] = $value['ref_value'];
			else
				$result['class_intent'] .= " | " . $value['ref_value'];

			$i++;
		}



		$data = $this->db2->query("CALL getMTRelation('" . $class_id . "')");
		mysqli_next_result($this->db2->conn_id);
		$res = $data->result_array();

		$i = 1;
		foreach ($res as $key => $value) {
			if ($i == 1)
				$result['class_intent_relation'] = $value['ref_value'];
			else
				$result['class_intent_relation'] .= " | " . $value['ref_value'];

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
	/*
	 * Function to call the stored procedure which retrieves the list of elements according to the parameters received
	 */
	function get_list_mdl($ref_table_config, $val = '_', $page = 0, $rec_per_page = 0, $extra_condition = '')
	{

		$current_operation = $ref_table_config['current_operation'];


		$stored_procedure = $ref_table_config['operations'][$current_operation]['data_source'];
		$extra_parameters = "";
		if (!empty($ref_table_config['operations'][$current_operation]['conditions'])) {
			foreach ($ref_table_config['operations'][$current_operation]['conditions'] as $key_cond => $condition) {

				if (!$condition['add_on_generation']) {

					$extra_parameters .= " , '" . $condition['value'] . "'";

				}
			}
		}


		$config = $ref_table_config['config_label'];

		if (!admin_config($config)) {
			$this->db2 = $this->load->database(project_db(), TRUE);
		}


		if ($val != '_') {
			$search = $val;
		} else {
			$search = "";
		}


		if (admin_config($config)) {
			$data = $this->db->query("CALL " . $stored_procedure . "(0,0,'" . $search . "' " . $extra_parameters . ") ");
			mysqli_next_result($this->db->conn_id);

		} else {
			$data = $this->db2->query("CALL " . $stored_procedure . "(0,0,'" . $search . "' " . $extra_parameters . ") ");
			mysqli_next_result($this->db2->conn_id);
		}


		$result['nombre'] = $data->num_rows();


		if ($rec_per_page == 0) {
			$rec_per_page = $this->config->item('rec_per_page');
		} elseif ($rec_per_page == -1) {
			$rec_per_page = 0;
		}

		if (admin_config($config)) {
			$data = $this->db->query("CALL " . $stored_procedure . "(" . $page . "," . $rec_per_page . ",'" . $search . "' " . $extra_parameters . ") ");
			mysqli_next_result($this->db->conn_id);
		} else {
			$data = $this->db2->query("CALL " . $stored_procedure . "(" . $page . "," . $rec_per_page . ",'" . $search . "' " . $extra_parameters . ") ");
			mysqli_next_result($this->db2->conn_id);
		}

		$result['list'] = $data->result_array();


		return $result;

	}
}