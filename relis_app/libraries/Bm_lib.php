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

 //provides functionalities related to authentication, pagination, email sending, and random string generation within a CodeIgniter application
 class Bm_lib
{
	public function __construct()
	{

		$this->CI =& get_instance();
		$this->CI->load->library('user/user_lib');

		/*
			checks if the current class (retrieved using $this->CI->router->fetch_class()) is not in the list of "free_controllers" specified in the configuration file. 
			The configuration item 'free_controllers' holds an array of class names that do not require authentication.
			If it's not a free controller, it calls the checkauthentification() method.
		*/
		if (!in_array($this->CI->router->fetch_class(), $this->CI->config->item('free_controllers'))) {

			$this->CI->user_lib->checkauthentification();
		}

		$sections = array(
			'config' => FALSE,
			'http_headers' => FALSE,
			'session_data' => FALSE,
			'queries' => FALSE
		);
		//	echo 	print_test($this->CI->load->database('default',true));
		$f = APPPATH . 'config/database.php';
		include($f);

		//The variable $db_settings is assigned the value of $db, which holds the database configuration settings.
		$db_settings = $db;
		//print_test($db_settings);
		//	print_test(empty($db_settings[project_db()]));
		//exit;

		//checks if the database settings for the current project (retrieved using project_db()) exist in $db_settings.
		if (!empty($db_settings[project_db()])) {

			$this->CI->db_current = $this->CI->load->database(project_db(), TRUE);
		} else {
			//	set_top_msg("There is a problem with the selected project!",'error');

			$this->CI->db_current = $this->CI->load->database('default', TRUE);
			$this->CI->session->set_userdata('project_db', '');
			$this->CI->session->set_userdata('project_id', '');
			$this->CI->session->set_userdata('project_title', '');
			echo "<script>alert('There is a problem with the selected project!');window.location.href='" . base_url() . "home';</script>";
			//redirect('home');
			exit;
		}
	}

	/*
	 * Géneration de la pagination des pages
	 */
	function get_pagination($url, $total_rows, $uri_segment = 3)
	{
		$this->CI->load->library('pagination');
		$config['uri_segment'] = $uri_segment;
		$config['base_url'] = base_url() . $url;
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $this->CI->config->item('rec_per_page');
		$config['first_link'] = 'First';
		$config['last_link'] = 'Last';
		$config['full_tag_open'] = '<ul class="pagination pagination_mine">';
		$config['full_tag_close'] = '</ul>';
		$config['num_tag_open'] = '<li class="paginate_button">';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li class="paginate_button">';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li class="paginate_button">';
		$config['next_tag_close'] = '</li>';

		$config['first_tag_open'] = '<li class="paginate_button">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li class="paginate_button">';
		$config['last_tag_close'] = '</li>';
		$config['cur_tag_open'] = "<li class='paginate_button active'><a>";
		$config['cur_tag_close'] = "</a></li>";
		$config['num_links'] = 3;

		$this->CI->pagination->initialize($config);
		echo $this->CI->pagination->create_links();
	}

	/**
	 * generates a random string of a specified length. 
	 * It takes an integer parameter $length that specifies the length of the random string.	
	 * @param int $length      How many characters do we want?
	 * @return string
	 */
	public function random_str($length)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}