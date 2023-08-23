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
 *
 * This controller contain all the pages user can access before connection to the application
 */

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Reporting extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	/*
	 * Affichage des résultat(statistique).
	 * generate a graph displaying the result of paper classification
	 */
	public function result_graph()
	{
		/*
		 * Recupération du nombre de papiers par catégories
		 */
		$data['all_papers'] = $this->Paper_dataAccess->count_papers('all');
		$data['processed_papers'] = $this->Paper_dataAccess->count_papers('processed');
		$data['pending_papers'] = $this->Paper_dataAccess->count_papers('pending');
		$data['assigned_me_papers'] = $this->Paper_dataAccess->count_papers('assigned_me');
		$data['excluded_papers'] = $this->Paper_dataAccess->count_papers('excluded');
		/*
		 * Stucture de la table des classification
		 */
		$table_config = get_table_configuration('classification');
		$result_fin = array();
		foreach ($table_config['fields'] as $key_conf => $value_conf) {
			if (!(!empty($value_conf['compute_result']) and $value_conf['compute_result'] == 'no')) {
				if (isset($value_conf['number_of_values']) and ($value_conf['number_of_values'] == '1') and ($value_conf['input_type'] == 'select') and ($value_conf['input_select_source'] == 'table' or $value_conf['input_select_source'] == 'array' or $value_conf['input_select_source'] == 'yes_no')) {
					$ref_field = $key_conf;
					if ($value_conf['input_select_source'] == 'array') {
						$result = $this->Data_extraction_dataAccess->get_result_classification($key_conf);
						foreach ($result as $key => $value) {
							$result[$key]['field_desc'] = $value['field'];
						}
					} elseif ($value_conf['input_select_source'] == 'yes_no') {
						$result = $this->Data_extraction_dataAccess->get_result_classification($key_conf);
						$yes_no = array("No", 'Yes');
						foreach ($result as $key => $value) {
							$result[$key]['field_desc'] = $yes_no[$value['field']];
						}
					} else {
						$conf = explode(";", $value_conf['input_select_values']);
						$ref_config = $conf[0];
						$ref_table = $this->DBConnection_mdl->get_reference_corresponding_table($ref_config);
						$ref_table_name = $ref_table['reftab_table'];
						$ref_table_desc = $ref_table['reftab_desc'];
						$result = $this->Data_extraction_dataAccess->get_result_classification($ref_field);
						foreach ($result as $key => $value) {
							$result[$key]['field_desc'] = $this->manage_mdl->get_reference_value($ref_table_name, $result[$key]['field']);
						}
					}
					$result_fin[$ref_config . $key_conf]['name'] = $value_conf['field_title'];
					$result_fin[$ref_config . $key_conf]['field_name'] = $ref_field;
					$result_fin[$ref_config . $key_conf]['rows'] = $result;
				}
			}
		}
		//print_test($result_fin);
		/*
		 * La page contient des graphique cette valeur permettra le chargement de la librarie highcharts
		 */
		$data['has_graph'] = 'yes';
		$data['result_table'] = $result_fin;
		$data['page'] = 'reporting/result_graph';
		$this->load->view('shared/body', $data);
	}

	//display the export options for the result data
	public function result_export($type = 1)
	{
		$data['t_type'] = $type;
		$data['page_title'] = lng('Exports');
		$data['top_buttons'] = get_top_button('back', 'Back', 'home');
		$data['left_menu_perspective'] = 'z_left_menu_screening';
		$data['project_perspective'] = 'screening';
		$data['page'] = 'relis/result_export';
		/*
		 * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
		 */
		$this->load->view('shared/body', $data);
	}

	//enable the user to download the specified file
	public function download($file_name)
	{
		$url = base_url() . "cside/export_r/" . $file_name;
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
		echo readfile($url);
		//header("Location: $url");
	}

	//retrieves the classification data, prepares the necessary information for export, creates a CSV file, and writes the data to the file
	public function result_export_classification()
	{
		//get classification
		$table_ref = "classification";
		$ref_table_config = get_table_config($table_ref);
		$table_id = $ref_table_config['table_id'];
		//$this->db2 = $this->load->database(project_db(), TRUE);
		//$data=$this->db2->query ( "CALL get_list_".$table_ref."(0,0,'') " );
		//mysqli_next_result( $this->db2->conn_id );
		//$result=$data->result_array();
		//print_test($result);
		echo $table_ref;
		$data = $this->DBConnection_mdl->get_list($ref_table_config, '_', 0, -1, '');
		//print_test($data);
		//exit;
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
			;
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
		//prepare paper info 
		$this->db2 = $this->load->database(project_db(), TRUE);

		$paper_data = $this->Reporting_dataAccess->prepare_paper_export();

		//rearange
		$users = $this->manager_lib->get_reference_select_values('users;user_name');
		$paper_res = $paper_data->result_array();
		$arrangedPapers = array();
		foreach ($paper_res as $key => $value_p) {
			$user_names = "";
			if (!empty($value_p['reviewers'])) {
				foreach (explode('|', $value_p['reviewers']) as $k => $p_user_id) {
					if ($k == 0) {
						$user_names .= !empty($users[$p_user_id]) ? $users[$p_user_id] : '';
					} else {
						$user_names .= !empty($users[$p_user_id]) ? ' | ' . $users[$p_user_id] : '';
					}
				}
			}
			$arrangedPapers[$value_p['id']] = $value_p;
			$arrangedPapers[$value_p['id']]['reviewers'] = $user_names;
		}
		$i = 1;
		$list_to_display = array();
		foreach ($data['list'] as $key => $value) {
			$element_array = array();
			$element_array['nbr'] = $i;
			$element_array['bibtexKey'] = !empty($arrangedPapers[$value['class_paper_id']]) ? $arrangedPapers[$value['class_paper_id']]['bibtexKey'] : '';
			$element_array['title'] = !empty($arrangedPapers[$value['class_paper_id']]) ? $arrangedPapers[$value['class_paper_id']]['title'] : '';
			$element_array['paper_year'] = !empty($arrangedPapers[$value['class_paper_id']]) ? $arrangedPapers[$value['class_paper_id']]['paper_year'] : '';
			$element_array['authors'] = !empty($arrangedPapers[$value['class_paper_id']]) ? $arrangedPapers[$value['class_paper_id']]['authors'] : '';
			$element_array['venue_fullName'] = !empty($arrangedPapers[$value['class_paper_id']]) ? $arrangedPapers[$value['class_paper_id']]['venue_fullName'] : '';
			$element_array['papers_sources'] = !empty($arrangedPapers[$value['class_paper_id']]) ? $arrangedPapers[$value['class_paper_id']]['papers_sources'] : '';
			$element_array['search_strategy'] = !empty($arrangedPapers[$value['class_paper_id']]) ? $arrangedPapers[$value['class_paper_id']]['search_strategy'] : '';
			$element_array['reviewers'] = !empty($arrangedPapers[$value['class_paper_id']]) ? $arrangedPapers[$value['class_paper_id']]['reviewers'] : '';
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
			if (isset($element_array['class_id'])) {
				unset($element_array['class_id']);
			}
			if (isset($element_array['class_paper_id'])) {
				unset($element_array['class_paper_id']);
			}
			array_push($list_to_display, $element_array);
			$i++;
		}
		//!!!!!!!!!!!!!!!!!!! this is like a hardcode it doesnt follow anny pathern 
		unset($field_list_header[0]);
		unset($field_list_header[1]);
		$other_fields = array('nbr', 'Key', 'Title', 'Publication year', 'Author/s', 'Venue', 'Source', 'Search Type', 'Reviewer/s');
		$field_list_header = array_merge($other_fields, $field_list_header);
		/*
		 * Ajout de l'entête de la liste
		 */
		if (!empty($data['list'])) {
			array_unshift($list_to_display, $field_list_header);
		}
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen("cside/export_r/relis_classification_" . project_db() . ".csv", 'w+');
		foreach ($list_to_display as $val) {
			fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
		}
		fclose($f_new);
		set_top_msg(lng_min('File generated'));
		redirect('reporting/result_export');
	}

	//export classification data from the "classification" table to a CSV file, which can then be downloaded or used for further analysis
	public function result_export_classification2()
	{
		//get classification
		$table_ref = "classification";
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_list_" . $table_ref . "(0,0,'') ");
		mysqli_next_result($this->db2->conn_id);
		$result = $data->result_array();
		//print_test($result);
		$ref_table_config = $this->ref_table_config($table_ref);
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
		//print_test($dropoboxes);
		foreach ($result as $key => $value) {
			/*
			 * Remplacement des clés externes par leurs correspondances
			 */
			foreach ($dropoboxes as $k => $v) {
				if ($result[$key][$k]) {
					if (isset($v[$result[$key][$k]])) {
						$result[$key][$k] = $v[$result[$key][$k]];
					}
				} else {
					if ($ref_table_config['fields'][$k]['field_value'] == "0_1") {
						$result[$key][$k] = "No";
					} else {
						$result[$key][$k] = "";
					}
				}
			}
		}
		$array_header = $ref_table_config['header_list_fields'];
		//print_test($result);
		//print_test($array_header);
		array_unshift($result, $array_header);
		//print_test($result);
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen("cside/export_r/export_classification_" . project_db() . ".csv", 'w+');
		foreach ($result as $val) {
			fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
		}
		fclose($f_new);
		redirect('home/export');
	}

	//exporting the excluded papers with their classification information to a CSV file
	public function result_export_excluded_class()
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$extra_sql = "";
		$users = $this->manager_lib->get_reference_select_values('users;user_name');

		$data = $this->Reporting_dataAccess->prepare_paper_export2();

		//	mysqli_next_result( $this->db2->conn_id );
		$result = $data->result_array();
		$papers = array();
		$i = 1;
		foreach ($result as $key => $value) {
			$user = !empty($users[$value['user_id']]) ? $users[$value['user_id']] : $value['user_id'];
			if (empty($papers[$value['id']])) {
				$papers[$value['id']] = array(
					'num' => $i,
					'bibtexKey' => $value['bibtexKey'],
					'title' => $value['title'],
					'preview' => $value['preview'],
					'user' => $user,
					'criteria' => $value['criteria'],
					'exclusion_note' => $value['exclusion_note'],
				);
				$i++;
			}
		}
		$array_header = array('#', "key", 'Title', 'Preview', 'Excluded By', 'Criteria', 'Exclusion note');
		array_unshift($papers, $array_header);
		$filename = "cside/export_r/relis_paper_excluded_class_" . project_db() . ".csv";
		//print_test($papers);
		// Create a stream opening it with read / write mode
		$stream = fopen('data://text/plain,' . "", 'w+');
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen($filename, 'w+');
		foreach ($papers as $val) {
			fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
		}
		// Close the stream
		fclose($f_new);
		set_top_msg(lng_min('File generated'));
		redirect('reporting/result_export');
	}

	//retrieve the necessary data about the papers, creates a CSV file, and writes the data to the file
	public function result_export_papers()
	{
		$table_ref = "papers";
		$this->db2 = $this->load->database(project_db(), TRUE);

		$data = $this->Reporting_dataAccess->prepare_paper_export3();

		$result = $data->result_array();
		$array_header = array('#', "key", 'Title', 'Link', 'Preview', 'Abstract', 'Year');
		array_unshift($result, $array_header);
		// Create a stream opening it with read / write mode
		$stream = fopen('data://text/plain,' . "", 'w+');
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen("cside/export_r/relis_paper_" . project_db() . ".csv", 'w+');
		$i = 0;
		foreach ($result as $val) {
			if ($i > 0) {
				$val['id'] = $i;
			}
			fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
			$i++;
		}
		fclose($f_new);
		set_top_msg(lng_min('File generated'));
		redirect('reporting/result_export');
	}

	public function result_export_included_papers_bib()
	{
		$this->result_export_papers_bib('Included');
	}

	public function result_export_excluded_papers_bib()
	{
		$this->result_export_papers_bib('Excluded');
	}

	//exporting the BibTeX information of papers based on their status (included or excluded).
	public function result_export_papers_bib($status = "")
	{
		//get classification
		$table_ref = "papers";
		$this->db2 = $this->load->database(project_db(), TRUE);
		$extra_sql = "";
		if (!empty($status)) {
			if ($status == 'Excluded') {
				$extra_sql = " AND ( screening_status  = '$status' OR  paper_excluded = 1 )";
			} else {
				$extra_sql = " AND screening_status  = '$status' AND paper_excluded=0 ";
			}
			$filename = "cside/export_r/relis_paper_bibtex_" . $status . '_' . project_db() . ".bib";
		} else {
			$filename = "cside/export_r/relis_paper_bibtex_" . project_db() . ".bib";
			//$extra_sql=" AND paper_excluded=0 ";
		}

		$data = $this->Reporting_dataAccess->prepare_paper_export4($extra_sql);

		//	mysqli_next_result( $this->db2->conn_id );
		$result = $data->result_array();
		//print_test($result);
		//$array_header=array('#',"key",'Title','Link','Preview','Abstract','Year','Bibtex');
		//array_unshift($result, $array_header);
		// Create a stream opening it with read / write mode
		$stream = fopen('data://text/plain,' . "", 'w+');
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen($filename, 'w+');
		foreach ($result as $val) {
			//print_test($val);
			if (!empty($val['bibtex'])) {
				fputs($f_new, $val['bibtex'] . "\n");
			}
		}
		// Rewind the stream
		//rewind($stream);
		// You can now echo it's content
		//echo stream_get_contents($stream);
		// Close the stream
		fclose($f_new);
		set_top_msg(lng_min('File generated'));
		redirect('reporting/result_export');
	}

	//exports the information of papers that have been excluded during the screening process
	public function result_export_excluded_screen()
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$extra_sql = "";
		$users = $this->manager_lib->get_reference_select_values('users;user_name');

		$data = $this->Reporting_dataAccess->prepare_paper_export5();

		//	mysqli_next_result( $this->db2->conn_id );
		$result = $data->result_array();
		$papers = array();
		$i = 1;
		foreach ($result as $key => $value) {
			$user = !empty($users[$value['user_id']]) ? $users[$value['user_id']] : $value['user_id'];
			if (empty($papers[$value['id']])) {
				$papers[$value['id']] = array(
					'num' => $i,
					'bibtexKey' => $value['bibtexKey'],
					'title' => $value['title'],
					'preview' => $value['preview'],
				);
				$i++;
			}
			$papers[$value['id']]['user_' . $value['user_id']] = $user;
			$papers[$value['id']]['criteria_' . $value['user_id']] = $value['criteria'];
			$papers[$value['id']]['screening_note' . $value['user_id']] = $value['screening_note'];
		}
		$array_header = array('#', "key", 'Title', 'Preview', 'Excluded By', 'Criteria', 'Note');
		array_unshift($papers, $array_header);
		$filename = "cside/export_r/relis_paper_excluded_screen_" . project_db() . ".csv";
		//print_test($papers);
		// Create a stream opening it with read / write mode
		$stream = fopen('data://text/plain,' . "", 'w+');
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen($filename, 'w+');
		foreach ($papers as $val) {
			fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
		}
		// Close the stream
		fclose($f_new);
		set_top_msg(lng_min('File generated'));
		redirect('reporting/result_export');
	}

	//export paper data from the "papers" table to a CSV file, which can then be downloaded or used for further analysis
	public function result_export_papers2()
	{
		//get classification
		$table_ref = "papers";
		$this->db2 = $this->load->database(project_db(), TRUE);

		$data = $this->Reporting_dataAccess->prepare_paper_export6();

		//	mysqli_next_result( $this->db2->conn_id );
		$result = $data->result_array();
		//print_test($result);
		$array_header = array('#', "key", 'Title', 'Link', 'Abstract', 'Preview');
		//print_test($result);
		//print_test($array_header);
		array_unshift($result, $array_header);
		//print_test($result);
		// Create a stream opening it with read / write mode
		$stream = fopen('data://text/plain,' . "", 'w+');
		// Iterate over the data, writting each line to the text stream
		$f_new = fopen("cside/export_r/export_paper_" . project_db() . ".csv", 'w+');
		foreach ($result as $val) {
			fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
		}
		// Rewind the stream
		//rewind($stream);
		// You can now echo it's content
		//echo stream_get_contents($stream);
		// Close the stream
		fclose($f_new);
		redirect('home/export');
	}
}
