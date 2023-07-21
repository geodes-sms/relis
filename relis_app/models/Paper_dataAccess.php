<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Paper_dataAccess extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function insert_to_paper($v_bibtex_key, $v_title, $v_preview, $v_bibtex, $v_abstract, $v_paper_link, $year, $papers_sources, $search_strategy, $active_user, $added_active_phase, $operation_code, $classification_status)
	{
		$sql = "INSERT INTO `paper` (`bibtexKey`, `title`,  `preview`,`bibtex`, `abstract`, `doi`, `year`, `papers_sources`, `search_strategy`, `added_by`, `addition_mode`, `added_active_phase`,`operation_code`,`classification_status`)
				VALUES('$v_bibtex_key','$v_title','$v_preview','$v_bibtex','$v_abstract','$v_paper_link','$year','$papers_sources','$search_strategy',$active_user,'Automatic','$added_active_phase','$operation_code','$classification_status')";
		//echo "$sql <br/><br/>";
		$res_sql = $this->manage_mdl->run_query($sql, False, project_db());
		return $res_sql;
	}

	function clear_papers_temp()
	{
		$sql = "UPDATE paper SET paper_active=3 where paper_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE paperauthor SET 	paperauthor_active=3 where paperauthor_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE ref_affiliation SET 	ref_active=3 where ref_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE assigned SET assigned_active=3 where assigned_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE classification SET class_active=3 where class_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE exclusion SET exclusion_active=3 where exclusion_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE qa_assignment SET qa_assignment_active=3 where qa_assignment_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE qa_result SET qa_active=3 where qa_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE qa_validation_assignment SET qa_validation_active=3 where qa_validation_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE screening_paper SET 	screening_active = 3  where 	screening_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE screen_decison SET 	decision_active = 3  where 	decision_active=1 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE  venue SET 	 venue_active = 3  where 	 venue_active=1 ";
		$res = $this->db_current->query($sql);
	}

	function clear_papers()
	{
		/////////////////For deleting authors and venues and everything when all papers are deleted.
		$sql = "DELETE FROM author WHERE 	author_active=1 OR author_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM venue WHERE 	venue_active=1 OR venue_active=3 ";
		$res = $this->db_current->query($sql);
		////// $sql="DELETE FROM paper WHERE 	paper_active=1 OR paper_active=3 ";
		////// $res=$this->db_current->query($sql);
		$sql = "DELETE FROM paperauthor WHERE 	paperauthor_active=1 OR paperauthor_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM assigned WHERE assigned_active=1 OR assigned_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM classification WHERE class_active=1 OR class_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM exclusion WHERE exclusion_active=1 OR exclusion_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM qa_assignment WHERE qa_assignment_active=1 OR qa_assignment_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM qa_result WHERE qa_active=1 OR qa_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM qa_validation_assignment WHERE qa_validation_active=1 OR qa_validation_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM screening_paper WHERE 	screening_active = 1  OR 	screening_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM screen_decison WHERE 	decision_active = 1  OR 	decision_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "DELETE FROM paper WHERE paper_active = 1 OR  paper_active=3 ";
		$res = $this->db_current->query($sql);
	}

	function cancel_clear_papers()
	{
		$sql = "UPDATE paper SET paper_active=1 where paper_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE paperauthor SET 	paperauthor_active=1 where paperauthor_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE assigned SET assigned_active=1 where assigned_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE classification SET class_active=1 where class_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE exclusion SET exclusion_active=1 where exclusion_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE qa_assignment SET qa_assignment_active=1 where qa_assignment_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE qa_result SET qa_active=1 where qa_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE qa_validation_assignment SET qa_validation_active=1 where qa_validation_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE screening_paper SET 	screening_active = 1  where 	screening_active=3 ";
		$res = $this->db_current->query($sql);
		$sql = "UPDATE screen_decison SET 	decision_active = 1  where 	decision_active=3 ";
		$res = $this->db_current->query($sql);
	}

	/*
	 * Fonction pour récuperer la liste de papiers suivant la catégorie
	 * Function to retrieve the list of papers according to the category
	 */
	function get_papers($paper_cat = "all", $ref_table_config, $val = '_', $page = 0, $rec_per_page = 0)
	{
		if ($rec_per_page == 0) {
			$rec_per_page = $this->config->item('rec_per_page');
		} elseif ($rec_per_page == -1) {
			$rec_per_page = 0;
		}


		$excluded = '_';
		if ($paper_cat == "excluded") {
			$excluded = 1;
		} else {

			$excluded = 0;
		}

		if ($val != '_') {
			$search = trim($val);
		} else {
			$search = NULL;
		}


		if ($paper_cat == "processed") {
			$stored_proc_list = " CALL get_list_papers_processed(" . $page . "," . $rec_per_page . ",'" . $search . "')";
			$stored_proc_count = " CALL count_papers_processed('" . $search . "')";
		} elseif ($paper_cat == "pending") {

			$stored_proc_list = " CALL get_list_papers_pending(" . $page . "," . $rec_per_page . ",'" . $search . "')";
			$stored_proc_count = " CALL count_papers_pending('" . $search . "')";
		} elseif ($paper_cat == "assigned_me") {


			$user_assigned_id = $this->session->userdata('user_id');

			$stored_proc_list = " CALL get_list_papers_assigned(" . $user_assigned_id . "," . $page . "," . $rec_per_page . ",'" . $search . "')";
			$stored_proc_count = " CALL count_papers_assigned(" . $user_assigned_id . ",'" . $search . "')";
		} elseif ($paper_cat == "screen") {

			$stored_proc_list = " CALL get_list_papers(" . $page . "," . $rec_per_page . ",'" . $search . "','" . $excluded . "')";
			$stored_proc_count = " CALL count_papers('" . $search . "','" . $excluded . "')";

		} else {

			$stored_proc_list = " CALL get_list_papers_class(" . $page . "," . $rec_per_page . ",'" . $search . "','" . $excluded . "')";
			$stored_proc_count = " CALL count_papers_class('" . $search . "','" . $excluded . "')";

		}
		$this->db2 = $this->load->database(project_db(), TRUE);

		$data = $this->db2->query($stored_proc_count);

		mysqli_next_result($this->db2->conn_id);
		$res = $data->row_array();
		if (!empty($res['nbr'])) {
			$result['nombre'] = $res['nbr'];
		} else {
			$result['nombre'] = 0;
		}

		$data = $this->db2->query($stored_proc_list);

		mysqli_next_result($this->db2->conn_id);
		$result['list'] = $data->result_array();

		return $result;
	}

	/*
	 * Fonction pour exclure un papier
	 * Function to exclude a paper
	 */
	function exclude_paper($id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$result = $this->db2->query("CALL exclude_paper(" . $id . ") ");

		return $result;
	}


	/*
	 * Fonction pour inclure un papier qui était exclus
	 * Function to include a paper that was excluded
	 */
	function include_paper($id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$result = $this->db2->query("CALL include_paper(" . $id . ") ");


		return $result;
	}

	function select_from_paper($bibtexKey)
	{
		$res = $this->db_current->query('SELECT * FROM paper WHERE BINARY bibtexKey = BINARY "' . $bibtexKey . '" and  paper_active=1')->row_array();
		return $res;
	}

	function select_from_author($author_name)
	{
		$res = $this->db_current->query('SELECT * FROM author WHERE BINARY author_name = BINARY "' . $author_name . '" and  author_active=1')->row_array();
		return $res;
	}

	/*
	 * Fonction pour retourner le nombre de papiers suivant la catégorie(all,pending,processed, ...)
	 * Function to return the number of papers according to the category (all, pending, processed, ...)
	 */
	function count_papers($paper_cat = "all")
	{

		$excluded = '_';
		if ($paper_cat == "excluded") {
			$excluded = 1;
		} else {

			$excluded = 0;
		}


		$search = NULL;



		if ($paper_cat == "processed") {

			$stored_proc_count = " CALL count_papers_processed('" . $search . "')";
		} elseif ($paper_cat == "pending") {


			$stored_proc_count = " CALL count_papers_pending('" . $search . "')";
		} elseif ($paper_cat == "assigned_me") {


			$user_assigned_id = $this->session->userdata('user_id');

			$stored_proc_count = " CALL count_papers_assigned(" . $user_assigned_id . ",'" . $search . "')";
		} elseif ($paper_cat == "screen") {

			$stored_proc_count = " CALL count_papers('" . $search . "','" . $excluded . "')";

		} else {

			$stored_proc_count = " CALL count_papers_class('" . $search . "','" . $excluded . "')";

		}
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query($stored_proc_count);

		mysqli_next_result($this->db2->conn_id);
		$res = $data->row_array();
		if (!empty($res['nbr'])) {
			$result = $res['nbr'];
		} else {
			$result = 0;
		}


		return $result;
		//print_test($result); exit;
	}
}