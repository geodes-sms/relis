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
		$url = "cside/export_r/" . $file_name;
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
		readfile($url);
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
        try {
            $f_new = fopen("cside/export_r/relis_classification_" . project_db() . ".csv", 'w');
            if (!$f_new) {
                throw new Exception('Could not open file relis_classification_'. project_db() . ".csv");
            }
            // Iterate over the data, writting each line to the text stream
            foreach ($list_to_display as $val) {
                fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
            }
            fclose($f_new);
            set_top_msg(lng_min('File generated'));
        }
        catch (Exception $e) {
            set_top_msg(lng_min("Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()), 'error');
        }
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
        try {
            // Iterate over the data, writting each line to the text stream
            $f_new = fopen("cside/export_r/export_classification_" . project_db() . ".csv", 'w');
            if (!$f_new) {
                throw new Exception('Could not open file export_classification_'. project_db() . ".csv");
            }
            foreach ($result as $val) {
                fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
            }
            fclose($f_new);
        }
        catch (Exception $e) {
            set_top_msg(lng_min("Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()), 'error');
        }
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
        try {
            // Create a stream opening it with read / write mode
            $f_new = fopen($filename, 'w');
            if (!$f_new) {
                throw new Exception('Could not open file '. $filename);
            }
            // Iterate over the data, writting each line to the text stream
            foreach ($papers as $val) {
                fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
            }
            // Close the stream
            fclose($f_new);
            set_top_msg(lng_min('File generated'));
        }
        catch (Exception $e) {
            set_top_msg(lng_min("Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()), 'error');
        }
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
        try {
            // Create a stream opening it with read / write mode
            $f_new = fopen("cside/export_r/relis_paper_" . project_db() . ".csv", 'w');
            if (!$f_new) {
                throw new Exception('Could not open file relis_paper_'. project_db() . ".csv");
            }
            // Iterate over the data, writing each line to the text stream
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
        }
        catch (Exception $e) {
            set_top_msg(lng_min("Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()), 'error');
        }
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
        try {
            // Create a stream opening it with read / write mode
            $f_new = fopen($filename, 'w');
            if (!$f_new) {
                throw new Exception('Could not open file ' . $filename);
            }
            // Iterate over the data, writting each line to the text stream
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
        }
        catch (Exception $e) {
            set_top_msg(lng_min("Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()), 'error');
        }
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
        try {
            $f_new = fopen($filename, 'w');
            if (!$f_new) {
                throw new Exception('Could not open file relis_paper_excluded_screen_'. project_db() . ".csv");
            }
            // Iterate over the data, writing each line to the text stream
            foreach ($papers as $val) {
                fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
            }
            // Close the stream
            fclose($f_new);
            set_top_msg(lng_min('File generated'));
        }
        catch (Exception $e) {
            set_top_msg(lng_min("Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()), 'error');
        }
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
        try {
            // Iterate over the data, writting each line to the text stream
            $f_new = fopen("cside/export_r/export_paper_" . project_db() . ".csv", 'w');
            if (!$f_new) {
                throw new Exception('Could not open file export_paper_'. project_db() . ".csv");
            }
            foreach ($result as $val) {
                fputcsv($f_new, $val, get_appconfig_element('csv_field_separator_export'));
            }
            // Rewind the stream
            //rewind($stream);
            // You can now echo it's content
            //echo stream_get_contents($stream);
            // Close the stream
            fclose($f_new);
        }
        catch (Exception $e) {
            set_top_msg(lng_min("Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()), 'error');
        }
		redirect('home/export');
	}

	public function r_export_configurations($data = "", $operation = "new", $display_type = "normal")
	{
		$res_install_config = $this->entity_configuration_lib->get_install_config();
		$fields = $res_install_config['config']['classification']['fields'];
		// Remove the first 2 elements using array_slice()
		$fields = array_slice($fields, 2);

		// Remove the last 3 elements using array_slice() and a negative offset
		$fields = array_slice($fields, 0, -3);

		$data = $this->session->userdata('redirect_values');

		$data["category"] = $fields;

		$data['page'] = 'relis/r_export_configurations';

		$this->load->view('shared/body', $data);

	}

	public function result_r_config_file()
	{
		$r_post_arr = $this->input->post();
		//var_dump($_POST);
		unset($r_post_arr[0]);
		$r_post_arr['Publication.year'] = 'Continuous';
		$r_post_arr['Venue'] = 'Nominal';
		$r_post_arr['Search.Type'] = 'Nominal';

		$output_file = "cside/export_r/relis_r_config_" . project_db() . ".R";

		// Initialize the two separate data arrays for nominal and continuous scales
		$nominal_df = array();
		$continuous_df = array();

		// Loop through the data to populate the separate arrays
		foreach ($r_post_arr as $title => $scale) {
			$title = preg_replace('/[\s_\-?]/', '.', trim($title, '_'));
			if ($scale === 'Nominal') {
				$nominal_df[] = $title;
			} elseif ($scale === 'Continuous') {
				$continuous_df[] = $title;
			}
		}
        try {
            // Open the new R script file for writing
            $output_file_handle = fopen($output_file, "w");
            if (!$output_file_handle) {
                throw new Exception('Could not open file ' . $output_file);
            }
            fwrite($output_file_handle, "source('relis_r_lib_" . project_db() . ".R') # Replace this with the name of your imported library file\n");
            fwrite($output_file_handle, "\n# The following lines consist of all the tests/plots corresponding to each category. Uncomment the required lines. \n");
            // Descriptive statistics
            fwrite($output_file_handle, "\n# Descriptive statistics\n");
            fwrite($output_file_handle, "\n# Frequency tables~Descriptive stats(Nominal variables)\n");
            foreach ($nominal_df as $title) {
                $line = "# desc_distr_vector[['$title']]\n";
                fwrite($output_file_handle, $line);
            }

            fwrite($output_file_handle, "\n# Bar Plots~Descriptive stats(Nominal variables)\n");
            foreach ($nominal_df as $title) {
                $line = "# bar_plot_vector[['$title']]\n";
                fwrite($output_file_handle, $line);
            }

            fwrite($output_file_handle, "\n# Statistics~Descriptive stats(Continuous variables)\n");
            foreach ($continuous_df as $title) {
                $line = "# statistics_vector[['$title']]\n";
                fwrite($output_file_handle, $line);
            }

            fwrite($output_file_handle, "\n# Box Plots~Descriptive stats(Continuous variables)\n");
            foreach ($continuous_df as $title) {
                $line = "# box_plot_vector[['$title']]\n";
                fwrite($output_file_handle, $line);
            }

            fwrite($output_file_handle, "\n# Violin Plots~Descriptive stats(Continuous variables)\n");
            foreach ($continuous_df as $title) {
                $line = "# violin_plot_vector[['$title']]\n";
                fwrite($output_file_handle, $line);
            }

            fwrite($output_file_handle, "\n#######################################################################################\n");

            // Evolution statistics
            fwrite($output_file_handle, "\n# Evolution statistics\n");
            fwrite($output_file_handle, "\n# Frequency tables~Evolution stats(Nominal variables)\n");
            foreach ($nominal_df as $title) {
                $line = "# evo_distr_vector[['$title']]\n";
                fwrite($output_file_handle, $line);
            }

            fwrite($output_file_handle, "\n# Evolution Plots~Evolution stats(Nominal variables)\n");
            foreach ($nominal_df as $title) {
                $line = "# evolution_plot_vector[['$title']]\n";
                fwrite($output_file_handle, $line);
            }

            fwrite($output_file_handle, "\n#######################################################################################\n");

            // Comparative statistics
            fwrite($output_file_handle, "\n# Comparative statistics\n");
            fwrite($output_file_handle, "# Frequency Tables~Comparative stats(Nominal variables)\n");
            foreach ($nominal_df as $title_1) {
                foreach ($nominal_df as $title_2) {
                    if ($title_1 !== $title_2) {
                        $line = "# comp_distr_vector[['$title_1']][['$title_2']]\n";
                        fwrite($output_file_handle, $line);
                    }
                }
            }

            fwrite($output_file_handle, "\n# Stacked Bar Plots~Comparative stats(Nominal variables)\n");
            foreach ($nominal_df as $title_1) {
                foreach ($nominal_df as $title_2) {
                    if ($title_1 !== $title_2) {
                        $line = "# stacked_bar_plot_vector[['$title_1']][['$title_2']]\n";
                        fwrite($output_file_handle, $line);
                    }
                }
            }

            fwrite($output_file_handle, "\n# Grouped Bar Plots~Comparative stats(Nominal variables)\n");
            foreach ($nominal_df as $title_1) {
                foreach ($nominal_df as $title_2) {
                    if ($title_1 !== $title_2) {
                        $line = "# grouped_bar_plot_vector[['$title_1']][['$title_2']]\n";
                        fwrite($output_file_handle, $line);
                    }
                }
            }

            fwrite($output_file_handle, "\n# Bubble Charts~Comparative stats(Nominal variables)\n");
            foreach ($nominal_df as $title_1) {
                foreach ($nominal_df as $title_2) {
                    if ($title_1 !== $title_2) {
                        $line = "# bubble_chart_vector[['$title_1']][['$title_2']]\n";
                        fwrite($output_file_handle, $line);
                    }
                }
            }

            fwrite($output_file_handle, "\n# Fisher's Exact Test~Comparative stats(Nominal variables)\n");
            foreach ($nominal_df as $title_1) {
                foreach ($nominal_df as $title_2) {
                    if ($title_1 !== $title_2) {
                        $line = "# fisher_exact_test_vector[['$title_1']][['$title_2']]\n";
                        fwrite($output_file_handle, $line);
                    }
                }
            }

            fwrite($output_file_handle, "\n# Shapiro Wilk's Correlation Test~Comparative stats(Continuous variables)\n");
            foreach ($continuous_df as $title) {
                $line = "# shapiro_wilk_test_vector[['$title']]\n";
                fwrite($output_file_handle, $line);
            }

            fwrite($output_file_handle, "\n# Pearson's Correlation Test~Comparative stats(Continuous variables)\n");
            foreach ($continuous_df as $title_1) {
                foreach ($continuous_df as $title_2) {
                    if ($title_1 !== $title_2) {
                        $line = "# pearson_cor_test_vector[['$title_1']][['$title_2']]\n";
                        fwrite($output_file_handle, $line);
                    }
                }
            }

            fwrite($output_file_handle, "\n# Spearman's Correlation Test~Comparative stats(Continuous variables)\n");
            foreach ($continuous_df as $title_1) {
                foreach ($continuous_df as $title_2) {
                    if ($title_1 !== $title_2) {
                        $line = "# spearman_cor_test_vector[['$title_1']][['$title_2']]\n";
                        fwrite($output_file_handle, $line);
                    }
                }
            }

            fwrite($output_file_handle, "\n#######################################################################################");

            // Close the file after writing
            fclose($output_file_handle);


            //write_file($output_file, $script);
            set_top_msg(lng_min('File generated'));
            $this->result_r_lib($r_post_arr);
        }
        catch (Exception $e) {
            set_top_msg(lng_min("Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()), 'error');
        }
		redirect('reporting/result_export');

	}

	public function result_r_lib($r_post_arr)
	{
		$output_file = "cside/export_r/relis_r_lib_" . project_db() . ".R";
        try {
            $output_file_handle = fopen($output_file, "w");
            if (!$output_file_handle) {
                throw new Exception('Could not open file ' . $output_file);
            }
            fwrite($output_file_handle, '#Install and load the necessary packages
		packgs <- c("tidyverse", "qdapRegex", "data.table", "janitor", "dplyr", "ggplot2", "cowplot", "psych")
		install.packages(setdiff(packgs, unique(data.frame(installed.packages())$Package)))
		lapply(packgs, library, character.only=TRUE)
		
		# Importing data.csv
		relis_data <- read.csv("relis_classification_' . project_db() . '.csv", header = TRUE) # Replace this with the name of your imported data file
		rm(relis_classification_' . project_db() . ')           # Replace this with the name of your imported data file
		config_file <- data.frame(Column_name = c(');


            $iteration = 1;
            foreach ($r_post_arr as $title => $scale) {
                $title = preg_replace('/[\s_\-?]/', '.', trim($title, '_'));
                $word = '"' . $title . '"';
                fwrite($output_file_handle, $word);

                if ($iteration != count($r_post_arr)) {
                    fwrite($output_file_handle, ',');
                    $iteration++;
                }
            }

            fwrite($output_file_handle, ') , 
		Scale = c(');

            $iteration = 1;
            foreach ($r_post_arr as $title => $scale) {
                $title = preg_replace('/[\s_\-?]/', '.', trim($title, '_'));
                $word = '"' . $scale . '"';
                fwrite($output_file_handle, $word);

                if ($iteration != count($r_post_arr)) {
                    fwrite($output_file_handle, ',');
                    $iteration++;
                }
            }


            fwrite($output_file_handle, ')) 
		# Beautifying Title
		config_file <- config_file %>%
		  mutate(Title = str_replace_all(trimws(Column_name), "\\\\.", " "))
		
		# Split config file based on data type
		nominal_df <- subset(config_file, Scale == "Nominal")
		continuous_df <- subset(config_file, Scale == "Continuous")
			
		# DESCRIPTIVE STATS
		# Available functions
		# Function to extract current column and organize data
		beautify_data_desc <- function(data, config_file, i) {
		  # Split the values by the "|" character
		  split_values <- str_split(data[[config_file$Column_name[i]]], "\\\\|")
		  
		  # Flatten the split values into a single vector and remove leading and trailing whitespaces
		  flattened_values <- str_trim(unlist(split_values))
		  
		  # Generate the frequency table
		  table_to_add <- tabyl(flattened_values)
		  
		  table_to_add["percent"] <- lapply(table_to_add["percent"], function (x) x*100)
		  colnames(table_to_add) <- c("Value", "n", "Percentage")
		  
		  return(table_to_add)
		}
		
		beautify_data_desc_cont <- function(data, config_file, i) {
		  table_to_add <- data[ , config_file$Column_name[i]]
		  table_to_add <- data.frame(data = table_to_add)
		  
		  return(table_to_add)
		}
		
		# Function to generate bar plots
		generate_bar_plot <- function(data, config_file, i) {
		  table_to_add <- beautify_data_desc(data, config_file, i)
		
		  p <- ggplot(data = table_to_add, aes(x = Value, y = Percentage, fill = n)) +
			geom_bar(stat = "identity") +
			labs(title = paste(config_file$Title[[i]], "~ Bar plot"), x = config_file$Title[[i]], y = "Percentage") +
			theme_minimal()  
		  
		  return(p)
		}
		
		# Function to generate box plots
		generate_box_plot <- function(data, config_file, i) {
		  table_to_add <- beautify_data_desc_cont(data, config_file, i)
		  
		  p <- ggplot(table_to_add, aes(x = "x", y = data)) + geom_boxplot()+
			stat_summary(fun = "mean", geom = "point", shape = 8, size = 2, color = "red") +
			labs(title = paste(config_file$Title[[i]], "~ Box plot"), y = config_file$Title[[i]], x = "") +
			theme_minimal()
		  
		  return(p)
		}
		
		# Function to generate violin plots
		generate_violin_plot <- function(data, config_file, i) {
		  table_to_add <- beautify_data_desc_cont(data, config_file, i)
		  
		  p <- ggplot(table_to_add, aes(x = "x", y = data)) + geom_violin()+
			stat_summary(fun = "mean", geom = "point", shape = 8, size = 2, color = "red")+
			labs(title = paste(config_file$Title[[i]], "~ Violin plot"), y = config_file$Title[[i]], x = "") +
			theme_minimal()
		  
		  return(p)
		}
		
		###########################################################################################
		# MAIN code
		# Initialize lists to store frequency tables and bar plots for nominal data
		desc_distr_vector <- list()
		bar_plot_vector <- list()
		
		# Generate frequency table and bar plot for each variable
		for (i in 1 : nrow(nominal_df)) {
		  
		  # Frequency table
		  desc_distr_vector[[nominal_df$Column_name[i]]] <- beautify_data_desc(relis_data, nominal_df, i)
		  
		  # Bar plot
		  bar_plot_vector[[nominal_df$Column_name[i]]] <- generate_bar_plot(relis_data, nominal_df, i)
			
		}
		###############################################################################################
		# Initialize lists to store frequency tables and plots for continuous data
		statistics_vector <- list()
		box_plot_vector <- list()
		violin_plot_vector <- list()
		for (i in 1 : nrow(continuous_df)) {
		  # Calculate descriptive statistics
		  statistics_vector[[continuous_df$Column_name[i]]] <- describe(beautify_data_desc_cont(relis_data, continuous_df, i))
		  
		  # Generate plots for each continuous variable
		  box_plot_vector[[continuous_df$Column_name[i]]] <- generate_box_plot(relis_data, continuous_df, i)
		  
		  violin_plot_vector[[continuous_df$Column_name[i]]] <- generate_violin_plot(relis_data, continuous_df, i)
		}  
		
		#################################################################################################
		#################################################################################################
		# EVOLUTION STATS
		# Available functions
		# Function to extract current column and organize data
		beautify_data_evo <- function(data, config_file, i) {
		  table_to_add <- data.frame(data$Publication.year, data[[config_file$Column_name[i]]])
		  colnames(table_to_add) <- c("Year", "Value")
		  table_to_add <- subset(table_to_add, Value != "")
		
		  table_to_add <- table_to_add %>%
			separate_rows(Value, sep = "\\\\s*\\\\|\\\\s*") %>%
			count(Year, Value, name = "Frequency")
		  
		  return(table_to_add)
		}
		
		# Function to generate distribution table
		expand_data <- function(data, config_file, i) {
		  table_to_add <- beautify_data_evo(data, config_file, i)
		  
		  y<- pivot_wider(table_to_add, names_from = "Value", values_from = "Frequency") %>%
			mutate_all(~ replace(., is.na(.), 0))  # Replace NA with 0
		  
		  return(y)
		}
		
		# Function to generate evolution plots
		generate_evo_plot <- function(data, config_file, i) {
		  table_to_add <- beautify_data_evo(data, config_file, i)
		  
		  shape_vector <- rep(1:6, length.out = length(unique(table_to_add$Value)))
		  
		  p <- ggplot(data = table_to_add, aes(x = Year, y = Frequency, color = Value, shape = Value, group = Value, linetype = Value)) + 
			geom_line(stat = "identity", size = 1.1) +
			geom_point(size = 2) +
			scale_shape_manual(values = shape_vector) + 
			labs(title = paste(config_file$Title[[i]], "~ Evolution plot"), x = "Year", y = "Frequency") +
			theme_minimal() 
		  
		  return(p)
		}
		
		#####################################################################################################
		# MAIN code
		# Initialize lists to store frequency tables and bar plots
		evo_distr_vector <- list()
		evolution_plot_vector <- list()
		
		# Generate frequency table and line chart for each variable
		for (i in 1 : nrow(nominal_df)) {
		  # Frequency table
		  evo_distr_vector[[nominal_df$Column_name[i]]] <- expand_data(relis_data, nominal_df, i)
		  
		  # Evolution plots
		  evolution_plot_vector[[nominal_df$Column_name[i]]] <- generate_evo_plot(relis_data, nominal_df, i) 
		}
		#######################################################################################################
		#######################################################################################################
		# COMPARATIVE STATS
		# Available functions
		# Function to subset required data
		beautify_data <- function(data, config_file, i, j) {
		  subset_data <- data[, c(config_file$Column_name[i], config_file$Column_name[j])]
		  colnames(subset_data) <- c("variable_1", "variable_2")
		  
		  subset_data <- subset_data[subset_data$variable_1 != "" & subset_data$variable_2 != "", ]
		  
		  subset_data <- subset_data %>%
			separate_rows(variable_1, sep = "\\\\s*\\\\|\\\\s*") %>%
			separate_rows(variable_2, sep = "\\\\s*\\\\|\\\\s*") %>%
			count(variable_1, variable_2, name = "Freq")
		  
		  return(subset_data)
		}
		
		# Function to generate stacked bar plots
		generate_stacked_bar_plot <- function(data, config_file, i, j) {
		  subset_data <- beautify_data(data, config_file, i, j)
		  
		  p <- ggplot(subset_data, aes(x = variable_1, y = Freq, fill = variable_2)) +
			geom_bar(stat = "identity") +
			labs(title = paste(config_file$Title[i], "and", config_file$Title[j], "~ Stacked bar plot"),
				 x = config_file$Title[i], y = "Frequency", fill = config_file$Title[j]) +
			theme_minimal()
		  
		  return(p)
		}
		
		# Function to generate grouped bar plots
		generate_grouped_bar_plot <- function(data, config_file, i, j) {
		  subset_data <- beautify_data(data, config_file, i, j)
		  
		  p <- ggplot(subset_data, aes(x = variable_1, y = Freq, fill = variable_2)) +
			geom_bar(stat = "identity", position = "dodge") +
			labs(title = paste(config_file$Title[i], "and", config_file$Title[j], "~ Grouped bar plot"),
				 x = config_file$Title[i], y = "Frequency", fill = config_file$Title[j]) +
			theme_minimal()
		  
		  return(p)
		}
		
		# Function to generate bubble charts
		generate_bubble_chart <- function(data, config_file, i, j) {
		  subset_data <- beautify_data(data, config_file, i, j)
		  
		  p <- ggplot(subset_data, aes(x = variable_1, y = variable_2, size = Freq)) +
			geom_point() +
			labs(title = paste(config_file$Title[i], "and", config_file$Title[j], "~ Bubble Chart"),
				 x = config_file$Title[i], y = config_file$Title[j], size = "Frequency") +
			theme_minimal()
		  
		  return(p)
		}
		
		
		
		# Function to conduct Fisher\'s exact test
		fisher_exact_test <- function(data, config_file, i, j) {
		  subset_data <- beautify_data(data, config_file, i, j)
		  if (nrow(subset_data)==1 && is.na(subset_data$variable_1) && is.na(subset_data$variable_2)) {
			return(NA)
		  }
		  
		  contingency_table <- xtabs(Freq ~ variable_1 + variable_2, data = subset_data)
		  
		  fisher_exact_test_result <- fisher.test(contingency_table, simulate.p.value = TRUE)
		  
		  return(fisher_exact_test_result)
		}
		
		# Function to conduct Shapiro Wilk\'s test
		shapiro_wilk_test <- function(data, config_file, i) {
		  subset_data <- data[[config_file$Column_name[i]]]
		  
		  shapiro_result <- shapiro.test(subset_data)
		  
		  return(shapiro_result)
		}
		
		# Function to conduct Spearman\'s correlation test
		spearman_cor_test <- function(data, config_file, i, j) {
		  column_1 <- data[[config_file$Column_name[i]]]
		  column_2 <- data[[config_file$Column_name[j]]]
		  
		  spearman_result <- cor.test(column_1, column_2, method = "spearman", exact = FALSE)
		  
		  return(spearman_result)
		}
		
		# Function to conduct Pearson\'s correlation test
		pearson_cor_test <- function(data, config_file, i, j) {
		  column_1 <- data[[config_file$Column_name[i]]]
		  column_2 <- data[[config_file$Column_name[j]]]
		  
		  pearson_result <- cor.test(column_1, column_2, method = "pearson")
		  
		  return(pearson_result)
		}
		
		#################################################################################
		# MAIN code
		# Initialize vectors to store statistical data for nominal columns
		comp_distr_vector <- list()
		stacked_bar_plot_vector <- list()
		grouped_bar_plot_vector <- list()
		bubble_chart_vector <- list()
		fisher_exact_test_vector <- list()
		
		# Generate plots for all possible combinations of nominal data
		for (i in 1 : nrow(nominal_df)) {
		
			comp_distr_vector[[nominal_df$Column_name[i]]] <- list()
			stacked_bar_plot_vector[[nominal_df$Column_name[i]]] <- list()
			bubble_chart_vector[[nominal_df$Column_name[i]]] <- list()
			grouped_bar_plot_vector[[nominal_df$Column_name[i]]] <- list()
			fisher_exact_test_vector[[nominal_df$Column_name[i]]] <- list()
			
			for (j in 1 : nrow(nominal_df)) {
			  if(j!=i)
			  {
				  comp_distr_vector[[nominal_df$Column_name[i]]][[nominal_df$Column_name[j]]] <- beautify_data(relis_data, nominal_df, i, j)
				  
				  stacked_bar_plot_vector[[nominal_df$Column_name[i]]][[nominal_df$Column_name[j]]] <-  generate_stacked_bar_plot(relis_data, nominal_df, i, j)
				  
				  bubble_chart_vector[[nominal_df$Column_name[i]]][[nominal_df$Column_name[j]]] <- generate_bubble_chart(relis_data, nominal_df, i, j)
				  
				  grouped_bar_plot_vector[[nominal_df$Column_name[i]]][[nominal_df$Column_name[j]]] <-  generate_grouped_bar_plot(relis_data, nominal_df, i, j)
				  
				  fisher_exact_test_vector[[nominal_df$Column_name[i]]][[nominal_df$Column_name[j]]] <- fisher_exact_test(relis_data, nominal_df, i, j)
			  }
			}
		
		}
		
		#######################################################################################################
		# Initialize vectors to store statistical data for continuous columns
		shapiro_wilk_test_vector <- list()
		pearson_cor_test_vector <- list()
		spearman_cor_test_vector <- list()
		
		# Run tests for all possible combinations of continuous data
		for (i in 1 : nrow(continuous_df)) {
		  shapiro_wilk_test_vector[[continuous_df$Column_name[i]]] <- shapiro_wilk_test(relis_data, continuous_df, i)
		}
		
		for (i in 1 : nrow(continuous_df)) {
		  
		  spearman_cor_test_vector[[continuous_df$Column_name[i]]] <- list()
		  pearson_cor_test_vector[[continuous_df$Column_name[i]]] <- list()
		  
		  for (j in 1 : nrow(continuous_df)) {
			if(j!=i)
			{
			  if(shapiro_wilk_test_vector[[continuous_df$Column_name[i]]]$p.value > 0.05  && shapiro_wilk_test_vector[[continuous_df$Column_name[j]]]$p.value > 0.05)
			  {
				pearson_cor_test_vector[[continuous_df$Column_name[i]]][[continuous_df$Column_name[j]]] <- pearson_cor_test(relis_data, continuous_df, i, j)
			  }
			  else
			  {
				spearman_cor_test_vector[[continuous_df$Column_name[i]]][[continuous_df$Column_name[j]]] <- spearman_cor_test(relis_data, continuous_df, i, j)
			  }
		
			}
		  }
		  
		}
		##########################################################################################################');

            // Close the file after writing
            fclose($output_file_handle);

            set_top_msg(lng_min('File generated'));
        }
        catch (Exception $e) {
            set_top_msg(lng_min("Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage()), 'error');
        }
		redirect('reporting/result_export');
	}
}