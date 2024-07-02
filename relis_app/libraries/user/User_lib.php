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
 
//provides functionalities related to authentication, email sending within a CodeIgniter application
class User_lib 
{
	public function __construct()
	{ 

		$this->CI =& get_instance();

		/*
				  checks if the current class (retrieved using $this->CI->router->fetch_class()) is not in the list of "free_controllers" specified in the configuration file. 
				  The configuration item 'free_controllers' holds an array of class names that do not require authentication.
				  If it's not a free controller, it calls the checkauthentification() method.
			  */
		if (!in_array($this->CI->router->fetch_class(), $this->CI->config->item('free_controllers'))) {

			$this->checkauthentification();
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
		   checks if the user is authenticated. 
		   If the user is not authenticated (determined by the absence of the 'user_id' in the session data), 
		   it redirects the user to the authentication page.
		*/
	function checkauthentification()
	{
		if (!($this->CI->session->userdata('user_id'))) {
			redirect('user');
		}
	}

	/*
	 * checks if a username is already taken or not
	 */
	function login_available($login)
	{
		$result = $this->CI->User_dataAccess->login_available($login);

		return $result;
	}


	public function send_mail($subject, $message, $destination)
	{
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

		if ($ci->email->send()) {
			//echo "Email sent successfully.";
			return 1;
		} else {

			//echo $ci->email->print_debugger();
			return 0;
		}
	}
}