<?php
/* This class is responsible for accessing the database to get classification data*/

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Data_extraction_dataAccess extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	/*
	 * Fonction pour récupérer la classification d'un papier
	 * Function to retrieve the classification of a paper
	 */
	function get_classifications($paper_id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_classifications(" . $paper_id . ") ");
		mysqli_next_result($this->db2->conn_id);
		$results = $data->result_array();

		return $results;
	}

	function get_classification_scheme()
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_classification_scheme() ");

		mysqli_next_result($this->db2->conn_id);
		$results = $data->result_array();

		return $results;
	}

	/*
	 * Fonction pour appeler la procédure stockée qui récupère les intentions pour une classification donnée
	 * INPUT : $classification_id: l'identifiant de la classification
	 */
	/*
	 * Function to call the stored procedure that retrieves the intents for a given classification
	 * INPUT: $ classification_id: the classification identifier
	 */
	function get_classification_intents($classification_id)
	{

		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL getMTIntents(" . $classification_id . ") ");

		mysqli_next_result($this->db2->conn_id);
		$result = $data->result_array();


		return $result;
	}

	/*
	 * Fonction pour pour récupérer les caractéristiques du'un papier associé à une classification
	 * Function to retrieve the characteristics of a paper associated with a classification
	 */
	function get_classification_paper($classification_id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$data = $this->db2->query("CALL get_classification_paper(" . $classification_id . ") ");
		mysqli_next_result($this->db2->conn_id);

		$res = $data->row_array();
		print_test($res);

		if (!empty($res))
			return $res['class_paper_id'];
		else
			return 0;
	}

	function get_result_classification($field){
			
		$this->db3 = $this->load->database(project_db(), TRUE);
	
		$data=$this->db3->query ( "CALL get_result_count('".$field."') " );
		
		mysqli_next_result( $this->db3->conn_id );
		$result=$data->result_array();	
		//print_test($result);
		return $result;
	}

	function get_classifications2($paper_id)
	{
		$result = $this->Data_extraction_dataAccess->get_classifications($paper_id);
		return $result;
	}

	function get_classification_scheme2(){
			
		$result=$this->Data_extraction_dataAccess->get_classification_scheme();
		
		return $result;

		/*
		$sql="SELECT *  from  classification_scheme WHERE  	scheme_active=1 ORDER BY field_order ASC ";
	
		$result=$this->db->query ( $sql )->result_array ();
	
		return $result;
		*/
	}

	function get_classification_intents2($classification_id){
		$this->db3 = $this->load->database(project_db(), TRUE);
		$data=$this->db3->query ( "CALL getMTIntents(".$classification_id.") " );
			
		mysqli_next_result( $this->db3->conn_id );
		$result=$data->result_array();
		return $result;
	}

	function get_classification_paper2($classification_id){
		$result=$this->Data_extraction_dataAccess->get_classification_paper($classification_id);
			
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
}