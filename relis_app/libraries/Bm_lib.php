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
class Bm_lib
{
	public function __construct()
	{
		
		$this->CI =& get_instance();
		
		if(!in_array($this->CI->router->fetch_class(), $this->CI->config->item('free_controllers'))){
			
			$this->checkauthentification();
			
		}
		
		$sections = array(
		'config'  => FALSE,
		'http_headers'  => FALSE,
		'session_data' => FALSE,
		'queries' => FALSE
		);
//	echo 	print_test($this->CI->load->database('default',true));
		$f = APPPATH.'config/database.php';
		include($f);
		$db_settings = $db;
		//print_test($db_settings);
	//	print_test(empty($db_settings[project_db()]));
		//exit;
		if(!empty($db_settings[project_db()])){
			
			$this->CI->db_current= $this->CI->load->database(project_db(), TRUE);
		}else{
		//	set_top_msg("There is a problem with the selected project!",'error');
		
			$this->CI->db_current= $this->CI->load->database('default', TRUE);
			$this->CI->session->set_userdata ( 'project_db','' );
			$this->CI->session->set_userdata ( 'project_id','' );
			$this->CI->session->set_userdata ( 'project_title','' );
			echo "<script>alert('There is a problem with the selected project!');window.location.href='".base_url()."home';</script>";
			//redirect('home');
			exit;
		}
	}
		
	/*
	 * Vérifiaction si l'utilisateur encours est authentifier, si non redirection vers la page d'authentification
	 */
	function checkauthentification()
	{
		if(!($this->CI->session->userdata('user_id'))){
			redirect('auth');
		}
	}

	
	/*
	 * Géneration de la pagination des pages
	 */
		function get_pagination($url, $total_rows,$uri_segment=3){

		$this->CI->load->library('pagination');
		$config['uri_segment'] = $uri_segment;
		$config['base_url'] = base_url().$url;
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
		$config['cur_tag_open']="<li class='paginate_button active'><a>";
		$config['cur_tag_close']="</a></li>";
		$config['num_links'] = 3;
		
		$this->CI->pagination->initialize($config);
		echo $this->CI->pagination->create_links();
		
		}
		
		
		/*
		 * Verification si un nom d'utilisateur est déjà utilisé ou pas
		 */
		function login_available($login){
				$result=$this->CI->DBConnection_mdl->login_available($login);
				
				return $result;
				
			}
			
			
	public function send_mail($subject,$message,$destination) {
		$ci = get_instance();
		$ci->load->library('email');
		$config['protocol'] = get_ci_config('mail_protocol');
		$config['smtp_host'] = get_ci_config('mail_host');
		$config['smtp_port'] = get_ci_config('mail_port');
		$config['smtp_user'] = get_ci_config('mail_user');
		$config['smtp_pass'] = get_ci_config('mail_password');
		$config['charset'] = "utf-8";
		$config['mailtype'] = "html";
		$config['newline'] = "\r\n";

		$mailFrom = get_ci_config('mail_from');
		$replyTo = get_ci_config('mail_reply_to');

		$ci->email->initialize($config);
			
		$ci->email->from($mailFrom, 'ReLiS');
		$ci->email->to($destination);
		$ci->email->reply_to($replyTo, 'ReLiS');
		$ci->email->subject($subject);
		$ci->email->message($message);
			
		if($ci->email->send()){
			//echo "Email sent successfully.";
			return 1;
		}
		else{
			
			//echo $ci->email->print_debugger();
			return 0;
		}
		
	}
	
	/**
	 * Generate a random string, 	
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
	