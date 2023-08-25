<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_dataAccess extends CI_Model
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
	 * Fonction pour vérifier si un nom d'utilisateur est déjà utilisé ou pas
	 * Function to check if a username is already in use or not
	 */
	function login_available($login)
	{


		$data = $this->db->query("CALL check_login('" . $login . "')");

		mysqli_next_result($this->db->conn_id);
		$result = $data->row_array();
		if ($result['number'] > 0) {
			return false;
		} else {
			return true;

		}

	}

	/*
	 * Fonction pour récupérer la liste des utilisateurs
	 * Function to retrieve the list of users
	 */
	function get_users_all()
	{


		$data = $this->db->query("CALL get_list_users_all() ");

		mysqli_next_result($this->db->conn_id);
		$result = $data->result_array();


		return $result;
	}
}