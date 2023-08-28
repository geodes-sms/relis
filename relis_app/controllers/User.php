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
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller
{
    private $validationCodeValidityLimit = 86400; // (24* 60 * 60) ;

    function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    /*
     * Home page
     * 
     */
    public function index()
    {
        $data['home_info'] = $this->db->order_by('info_order', 'ASC')
            ->get_where('info', array('info_active' => 1, 'info_type' => 'Home'))
            ->row_array();
        $data['home_ref'] = $this->db->order_by('info_order', 'ASC')
            ->get_where('info', array('info_active' => 1, 'info_type' => 'Reference'))
            ->row_array();
        $data['home_features'] = $this->db->order_by('info_order', 'ASC')
            ->get_where('info', array('info_active' => 1, 'info_type' => 'Features'))
            ->result_array();
        $data['page'] = 'h_home';
        $this->load->view('shared/h_body', $data);
        return;
    }

    /**
     * The purpose of this function is to clean up non-validated user accounts and associated user creation records that have exceeded the validation code validity limit, 
     * ensuring that only valid and active accounts remain in the system
     */
    private function clearNonValidatedAccounts()
    {
        $limitDate = date('Y-m-d H:i:s', time() + $this->validationCodeValidityLimit);
        $this->db->where('user_state', 0);
        $this->db->where('user_active', 1);
        $this->db->where('creation_time < ', $limitDate);
        $this->db->delete('users');
        $this->db->where('user_creation_active', 1);
        $this->db->where('confirmation_expiration < ', time() + $this->validationCodeValidityLimit);
        $this->db->delete('user_creation');
        return;
    }

    /*
     * Authentication page
     *
     */
    public function login()
    {
        if (($this->session->userdata('user_id'))) {
            redirect('home');
            return;
        }
        $this->clearNonValidatedAccounts();
        $data['page'] = 'user/h_login';
        $this->load->view('shared/h_body', $data);
        return;
    }

    /**
     * The purpose of this function is to retrieve and display the user help information from the database on the user help page.
     */
    public function user_help()
    {
        $data['home_help'] = $this->db->order_by('info_order', 'ASC')
            ->get_where('info', array('info_active' => 1, 'info_type' => 'Help'))
            ->result_array();
        $data['page'] = 'h_help';
        /*
         * Chargement de la vue d'authentification
         */
        $this->load->view('shared/h_body', $data);
    }

    //retrieve and display the detailed information about a specific help topic based on the provided help ID.
    public function help_det($help_id)
    {
        $data['help_info'] = $this->db->order_by('info_order', 'ASC')
            ->get_where('info', array('info_active' => 1, 'info_id' => $help_id))
            ->row_array();
        //	print_test($data);
        $data['top_buttons'] = get_top_button('back', 'Back', 'user/help', '', '', '', '', FALSE);
        $data['page'] = 'h_help_det';
        /*
         * Chargement de la vue d'authentification
         */
        $this->load->view('shared/h_body', $data);
    }

    public function help()
    {
        $data['home_help'] = $this->db->order_by('info_order', 'ASC')
            ->get_where('info', array('info_active' => 1, 'info_type' => 'Help'))
            ->result_array();

        $data['page'] = 'h_help';

        /*
         * Chargement de la vue d'authentification
         */
        $this->load->view('shared/h_body', $data);
    }

    /*
     * Fonction de verification du mot de passe et nomt d'utilisateur entrée pour la connexion
     */
    public function check_form()
    {
        /*
         * Récupération des valeurs saisie per l'utilisateur
         */
        $content = $this->input->post();
        /*
         * Verification si toutes les valeurs ont été saisie avec "form validator" de codeIgniter
         */
        $this->form_validation->set_rules('user_username', 'Username', 'trim|required');
        $this->form_validation->set_rules('user_password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            /*
             * Si toutes les valeurs ne sont pas saisies  retour vers le formulaire de connexion
             */
            $data['page'] = 'user/h_login';
            $this->load->view('shared/h_body', $data);
        } else {
            /*
             * Vérification si login et password sont correct
             */
            $user = $this->User_dataAccess->check_user_credentials($this->input->post());
            //print_test($user);
            if (empty($user)) {
                $data['err_msg'] = 'Username or Password not correct !';
                $data['page'] = 'user/h_login';
                $this->load->view('shared/h_body', $data);
            } else {
                if (empty($user['user_state'])) {
                    $this->validate_user($user);
                } else {
                    $this->session->set_userdata($user);
                    $this->session->set_userdata('page_msg_err', '');
                    $this->session->set_userdata('last_url', "");
                    $this->session->set_userdata('msg', " Logged in successfully");
                    $this->session->set_userdata('submit_mode', 'normal');
                    $this->session->set_userdata('language_edit_mode', 'no');
                    $this->session->set_userdata('language_edit_mode', 'class');
                    //used for redirection after saving data
                    $this->session->set_userdata('after_save_redirect', '');
                    $this->session->set_userdata('current_screen_phase', '');
                    $this->session->set_userdata('debug_paper_code', 'init');
                    $this->session->set_userdata('debug_paper_url', 'init');
                    //$this->session->set_userdata('project_db','mt');
                    //$this->session->set_userdata('project_db','stm');
                    //	$configuration = get_appconfig();
                    if (!empty($user['user_default_lang']))
                        $default_lang = $user['user_default_lang'];
                    else
                        $default_lang = 'en';
                    if (get_adminconfig_element('first_connect')) {
                        admin_initial_db_setup();
                    }
                    $this->session->set_userdata('active_language', $default_lang);
                    set_log('Connection', 'User connected');
                    redirect('home');
                }
            }
        }
    }

    /*
     * Fonction de verification de connection pour un utilisateur de demonstration
     */
    public function demo_user()
    {
        /*
         * Vérification si login et password sont correct
         */
        $user_id = 2;
        $user = $this->DBConnection_mdl->get_row_details(
            'get_user_detail'
            ,
            $user_id,
            true,
            'users'
        );
        if (empty($user)) {
            $data['err_msg'] = 'Username or Password not correct !';
            $this->load->view('user/login', $data);
        } else {
            if (empty($user['user_state'])) {
                $this->validate_user($user);
            } else {
                //$this->session->sess_destroy();
                $this->session->set_userdata($user);
                $this->session->set_userdata('page_msg_err', '');
                $this->session->set_userdata('last_url', "");
                $this->session->set_userdata('msg', " Logged in successfully");
                $this->session->set_userdata('submit_mode', 'normal');
                $this->session->set_userdata('language_edit_mode', 'no');
                $this->session->set_userdata('language_edit_mode', 'class');
                //used for redirection after saving data
                $this->session->set_userdata('after_save_redirect', '');
                $this->session->set_userdata('current_screen_phase', '');
                $this->session->set_userdata('debug_paper_code', 'init');
                $this->session->set_userdata('debug_paper_url', 'init');
                //$this->session->set_userdata('project_db','mt');
                //$this->session->set_userdata('project_db','stm');
                //	$configuration = get_appconfig();
                if (!empty($user['user_default_lang']))
                    $default_lang = $user['user_default_lang'];
                else
                    $default_lang = 'en';
                if (get_adminconfig_element('first_connect')) {
                    admin_initial_db_setup();
                }
                $this->session->set_userdata('active_language', $default_lang);
                set_log('Connection', 'User connected');
                redirect('home');
            }
        }
    }

    //simulate the login process for a demo user and redirect them to a specific project after successful login.
    public function demo_user_toproject($project_id)
    {
        /*
         * Vérification si login et password sont correct
         */
        $user_id = 5;
        $user = $this->DBConnection_mdl->get_row_details(
            'get_user_detail'
            ,
            $user_id,
            true,
            'users'
        );
        if (empty($user)) {
            $data['err_msg'] = 'Username or Password not correct !';
            $this->load->view('user/login', $data);
        } else {
            if (empty($user['user_state'])) {
                $this->validate_user($user);
            } else {
                //	$this->session->sess_destroy();
                $this->session->set_userdata($user);
                $this->session->set_userdata('page_msg_err', '');
                $this->session->set_userdata('last_url', "");
                $this->session->set_userdata('msg', " Logged in successfully");
                $this->session->set_userdata('submit_mode', 'normal');
                $this->session->set_userdata('language_edit_mode', 'no');
                $this->session->set_userdata('language_edit_mode', 'class');
                //used for redirection after saving data
                $this->session->set_userdata('after_save_redirect', '');
                $this->session->set_userdata('current_screen_phase', '');
                $this->session->set_userdata('debug_paper_code', 'init');
                $this->session->set_userdata('debug_paper_url', 'init');
                //$this->session->set_userdata('project_db','mt');
                //$this->session->set_userdata('project_db','stm');
                //	$configuration = get_appconfig();
                if (!empty($user['user_default_lang']))
                    $default_lang = $user['user_default_lang'];
                else
                    $default_lang = 'en';
                if (get_adminconfig_element('first_connect')) {
                    admin_initial_db_setup();
                }
                $this->session->set_userdata('active_language', $default_lang);
                set_log('Connection', 'User connected');
                redirect('project/set_project/' . $project_id);
            }
        }
    }

    /*
     * 
     * Déconnexion : réinitialisation de la session de l'utilisateur
     * 
     */
    public function discon()
    {
        set_log('Disconnection', 'User disconnected');
        $this->session->sess_destroy();
        $this->session->set_userdata('user_id', 0);
        redirect('user');
    }

    //To delete
    //capture and store the screen dimensions and server request time for further use within the application
    public function add_screen_size($height = 0, $width = 0, $loadTime = 0)
    {
        $this->session->set_userdata('screen_height', $height);
        $this->session->set_userdata('screen_width', $width);
        $this->session->set_userdata('server_request_time', $_SERVER['REQUEST_TIME']);
        $this->session->set_userdata('server_request_time_ready', time());
        echo "done";
    }

    //To delete
    //captures the server request time and the time at which the page finished loading and stores them in the session userdata.
    public function add_screen_size_load()
    {
        $this->session->set_userdata('server_request_time', $_SERVER['REQUEST_TIME']);
        $this->session->set_userdata('server_request_time_load', time());
        echo "done";
    }

    /**
     * Form to create a user account
     *
     * @param array $data
     */
    public function new_user($data = [])
    {
        if (($this->session->userdata('user_id'))) {
            //If there is an open user session the user is redirected to the home page
            redirect('home');
        } else {
            $data['page'] = 'user/h_create_user';
            $this->load->view('shared/h_body', $data);
        }
        return;
    }

    /**
     *Add new user
     */
    public function check_create_user()
    {
        $post_arr = $this->input->post();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('user_mail', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('user_username', 'Username', 'trim|required');
        $this->form_validation->set_rules('user_password', 'Password', 'trim|required|matches[user_password_validate]');
        $this->form_validation->set_rules('user_password_validate', 'Validate password', 'trim|required');
        $data['err_msg'] = '';
        if ($this->form_validation->run() == FALSE) {
            $data['content_item'] = $post_arr;
            $this->new_user($data);
            return;
        }
        $recaptchaResponse = trim($this->input->post('g-recaptcha-response'));
        $validatedCaptcha = $this->validateCaptcha($recaptchaResponse);
        if (!$validatedCaptcha) {
            $data['content_item'] = $post_arr;
            $data['err_msg'] .= 'Sorry Recaptcha Unsuccessful !! <br/>';
            $this->new_user($data);
            return;
        }
        if (
            !empty($post_arr['user_username'])
            and !$this->user_lib->login_available($post_arr['user_username'])
        ) {
            $data['content_item'] = $post_arr;
            $data['err_msg'] .= 'Username already used <br/>';
            $this->new_user($data);
            return;
        }
        $user_array = array(
            'user_name' => $post_arr['user_name'],
            'user_mail' => $post_arr['user_mail'],
            'user_username' => $post_arr['user_username'],
            'user_usergroup' => 2,
            'user_state' => 0,
            'user_password' => md5($post_arr['user_password'])
        );
        //add user in the db
        $this->db->insert('users', $user_array);
        $user_id = $this->db->insert_id();
        $confimation_code = $res = $this->bm_lib->random_str(12);
        ;
        $this->db->insert(
            'user_creation',
            array(
                'creation_user_id' => $user_id,
                'confirmation_code' => $confimation_code,
                'confirmation_expiration' => time() + $this->validationCodeValidityLimit,
                'confirmation_try' => 0,
            )
        );
        //send confirmation mail
        $this->sendConfirmationMail($user_array, $confimation_code);
        $data['user_id'] = $user_id;
        $data['success_msg'] = 'A validation code has been sent to your email:<br/>
                                Please enter the validation. The code is valid for 24 hours';
        $this->validate_user($data);
        return;
    }

    //send a confirmation email to a user with their validation code
    private function sendConfirmationMail($userInfo, $confirmationCode)
    {
        $message = "
                    <h2>Relis Validation message</h2>
                    <p>
                    Welcome to ReLiS:<br/>
                    Your validation code is : <b>$confirmationCode</b><br/>
                    This validation code is active for 6 hours
                    </p>";
        $subject = "Validation code";
        $destination = [$userInfo['user_mail']];
        $res = $this->user_lib->send_mail($subject, $message, $destination);
        return;
    }

    //test the email sending functionality by sending a test email to the specified email address.
    public function testMail()
    {
        $message = "
                    <h2>Relis Validation message</h2>
                    <p>
                    Welcome to ReLiS:<br/>
                    Here is a f..g test message
                    </p>";
        $subject = "test validation code";
        $destination = ['bbigendako@gmail.com'];
        $res = $this->bm_lib->send_mail($subject, $message, $destination);
        print_test($res);
        return;
    }

    //used to validate a reCAPTCHA response
    private function validateCaptcha($recaptchaResponse)
    {
        //TODO put recaptcha secret in the config file
        $secret = '6LcKU-4UAAAAAJwSMlWsIkPTByYC6aPwAy4z_PnF';
        $credential = array(
            'secret' => $secret,
            'response' => $recaptchaResponse
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($credential));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        $status = json_decode($response, true);
        return $status['success'];
    }

    /*
     * Form for account validation
     */
    public function validate_user($data = [])
    {
        $data['page'] = 'user/validate_user';
        $this->load->view('user/validate_user', $data);
        return;
    }

    /*
     * Process validation form data
     */
    public function check_validation()
    {
        /*
         * Récupération des valeurs saisie per l'utilisateur
         */
        $post_arr = $this->input->post();
        //	print_test($post_arr);
        $data = $post_arr;
        $data['err_msg'] = ''; //for users
        if (!empty($post_arr['user_id']) and !empty($post_arr['validation_code'])) {
            $res = $this->db->get_where(
                'user_creation',
                array('creation_user_id' => $post_arr['user_id'], 'user_creation_active' => 1)
            )
                ->row_array();
            if (!empty($res)) {
                if ($res['confirmation_code'] == $post_arr['validation_code']) {
                    //Validation success
                    //activate user
                    $this->db->update(
                        'users',
                        array('user_state' => 1),
                        array('user_id' => $post_arr['user_id'])
                    );
                    //remove activation user informations
                    $this->db->update(
                        'user_creation',
                        array('user_creation_active' => 0),
                        array('user_creation_id' => $res['user_creation_id'])
                    );
                    $data['success_msg'] = "Accound validated you can now user ReLiS";
                    $data['page'] = 'user/h_login';
                    $this->load->view('shared/h_body', $data);
                } else {
                    $data['err_msg'] .= 'Wrong validation code';
                    //increment confirmation attempts
                    $this->db->update(
                        'user_creation',
                        array('confirmation_try' => $res['confirmation_try'] + 1),
                        array('user_creation_id' => $res['user_creation_id'])
                    );
                    $this->validate_user($data);
                }
            } else {
                $data['err_msg'] .= 'Validation Error please provide validation code';
                $this->validate_user($data);
            }
        } else {
            $data['err_msg'] .= 'Validation Error please provide validation code';
            $this->validate_user($data);
        }
    }
}