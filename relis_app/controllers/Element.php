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
 * ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Element extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /*
     * Fonction pour l'affichage d'un élément
     * Input :	$ref_table : nom de la structure de l'element à afficher
     * 			$ref_id : id de l'élément
     *
     */
    public function display_element($operation_name, $ref_id, $allow_redirect = "yes")
    {
        $op = check_operation($operation_name, 'Detail');
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        // todo correction gestion des utilisateurs
        //if(admin_config($ref_table))
        //$data['left_menu_admin']=True;
        /*
         * todo corriger cette redirection
         * 
         * Redirection vers la fonction spécialise pour l'affichage d'un papier si l'element à afficher est un papier
         */
        if ($ref_table == 'papers' and $allow_redirect == 'yes') {
            //redirect('data_extraction/display_paper/'.$ref_id);
        } elseif ($ref_table == 'classification' and $allow_redirect == 'yes') {
            $paper_id = $this->Data_extraction_dataAccess->get_classification_paper($ref_id);
            redirect('data_extraction/display_paper/' . $paper_id);
        }
        if (!($this->session->userdata('project_db')) and $ref_table == 'config') {
            redirect('home');
        }
        //print_test($op);
        $table_config = get_table_configuration($ref_table);
        $table_config['current_operation'] = $ref_table_operation;
        //	exit;
        /*
         * Appel de la fonction  récupérer la ligne à afficher
         */
        //print_test($table_config);
        $item_data = $this->manager_lib->get_detail($table_config, $ref_id, False, False);
        $data['item_data'] = $item_data;
        //print_test($data);exit;
        /*
         * Récupération de la configuration(structure) de la table de l'élément
         */
        //	$table_config=get_table_configuration($ref_table);
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = "";
        //$data ['top_buttons'] .= get_top_button ( 'back', 'Back', 'manage' );
        if (!empty($table_config['operations'][$ref_table_operation]['top_links']))
            $data['top_buttons'] = $this->create_top_buttons($table_config['operations'][$ref_table_operation]['top_links'], $ref_id);
        /*
         * Titre de la page
         */
        if (isset($table_config['operations'][$ref_table_operation]['page_title'])) {
            $data['page_title'] = lng($table_config['operations'][$ref_table_operation]['page_title']);
        } else {
            $data['page_title'] = lng("List of " . $table_config['entity_label']);
        }
        /*
         * La vue qui va s'afficher
         */
        $data['table_config'] = $table_config;
        //print_test($data);
        $data['page'] = 'element/display_element';
        if (!empty($table_config['operations'][$ref_table_operation]['page_template'])) {
            $data['page'] = $table_config['operations'][$ref_table_operation]['page_template'];
        }
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /*
     * Fonction  pour afficher la page avec un formulaire d'ajout ou modification d'un élément
     *
     * Input: 	$ref_table: nom de la structure de la table pour l'élément à afficher
     * 			$data : informations sur l'élément si la fonction est utilisé pour la mis à jour(modification)
     * 			$operation: type de l'opération ajout (new) ou modification(edit)
     * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
     */
    public function add_element_modal($operation_name, $data = [], $operation = 'new', $display_type = "normal", $op_type = "Add")
    {
        $this->add_element($operation_name, $data, $operation, 'modal', $op_type);
    }

    //handles the process of adding a new element to a reference table.
    public function add_element($operation_name, $data = [], $operation = 'new', $display_type = "normal", $op_type = "Add")
    {
        $is_guest = check_guest();
        $op = check_operation($operation_name, $op_type);
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        $table_config = get_table_configuration($ref_table);
        if (!$is_guest) {
            if ($ref_table == 'papers') { //Use bibler for papers management
                //redirect("paper/bibler_add_paper");
            }
            if (admin_config($ref_table))
                $data['left_menu_admin'] = True;
            /*
             * charger la manière d'affichage du formulaire
             */
            $this->session->set_userdata('submit_mode', $display_type);
            /*
             * Récupération de la configuration(structure) de la table concerné
             */
            //print_test($table_config);
            $table_config['config_id'] = $ref_table;
            $table_config['current_operation'] = $ref_table_operation;
            $type_op = $operation == 'new' ? "on_add" : "on_edit";
            /*
             * récupération des valeurs qui vont apparaitres dans les dropdown boxes
             * recovery of the values that will appear in the dropdown boxes
             */
            foreach ($table_config['operations'][$ref_table_operation]['fields'] as $k => $v_field) {
                if (!empty($table_config['fields'][$k])) {
                    $v = $table_config['fields'][$k];
                    if (!empty($v['input_type']) and $v['input_type'] == 'select') {
                        if ($v['input_select_source'] == 'table') {
                            if (isset($table_config['fields'][$k]['multi-select']) and $table_config['fields'][$k]['multi-select'] == 'Yes') {
                                $table_config['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], False, False, True);
                            } else {
                                $table_config['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], True, False);
                            }
                        }
                    }
                }
            }
            /*
                      * Prépartions des valeurs qui vont apparaitres dans le formulaire
                      * Preparation of the values that will appear in the form
                      */
            //	$title_append=$table_config['reference_title_min'];
            $data['table_config'] = $table_config;
            //print_test($data);
            //		exit;
            $data['save_function'] = isset($table_config['operations'][$ref_table_operation]['save_function']) ? $table_config['operations'][$ref_table_operation]['save_function'] : 'element/save_element';
            /*
             * Titre de la page
             */
            if (isset($table_config['operations'][$ref_table_operation]['page_title'])) {
                $data['page_title'] = lng($table_config['operations'][$ref_table_operation]['page_title']);
            } else {
                $data['page_title'] = lng("List of " . $table_config['entity_label']);
            }
            /*	if ($operation == 'new') {
                             // La fonction qui va traiter l'enregistrement dans la DB;
                             //$data['save_function']=isset($table_config['save_new_function']) ? $table_config['save_new_function']:'manager/save_element';
                             if(isset($table_config['entity_title']['add'])){
                                 $data['page_title']=lng($table_config['entity_title']['add']);
                             }else{
                                 $data ['page_title'] = lng('Add '.$title_append);
                             }
                         } else {
                             //$data['save_function']=isset($table_config['save_edit_function']) ? $table_config['save_edit_function']:'manager/save_element';
                             if(isset($table_config['entity_title']['edit'])){
                                 $data['page_title']=lng($table_config['entity_title']['edit']);
                             }else{
                                 $data ['page_title'] = lng('Edit '.$title_append);
                             }
                         }
                         */
            if (!empty($table_config['operations'][$ref_table_operation]['redirect_after_save'])) {
                $after_save_redirect = $table_config['operations'][$ref_table_operation]['redirect_after_save'];
                if (!empty($data['current_element'])) {
                    $after_save_redirect = str_replace('~current_element~', $data['current_element'], $after_save_redirect);
                }
            } else {
                $after_save_redirect = "home";
            }
            $this->session->set_userdata('after_save_redirect', $after_save_redirect);
            $data['operation_type'] = $operation;
            /*
                      * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
                      * Creation of the buttons that will be displayed at the top of the page (top_buttons)
                      */
            //$data ['top_buttons'] = get_top_button ( 'back', 'Back', 'manage' );
            $data['top_buttons'] = $this->create_top_buttons($table_config['operations'][$ref_table_operation]['top_links']);
            /*
             * La vue qui va s'afficher
             */
            $data['page'] = 'general/frm_entity';
            if (!empty($table_config['operations'][$ref_table_operation]['page_template'])) {
                $data['page'] = $table_config['operations'][$ref_table_operation]['page_template'];
            }
            /*
             * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
             */
            if ($display_type == 'modal') {
                $this->load->view('general/frm_entity_modal', $data);
            } else {
                $this->load->view('shared/body', $data);
            }
        } else {
            set_top_msg('No access to this operation!', 'error');
            if (!empty($table_config['operations'][$ref_table_operation]['redirect_after_save'])) {
                $redirect_url = $table_config['operations'][$ref_table_operation]['redirect_after_save'];
                if (!empty($source_id)) {
                    $redirect_url = str_replace('~current_element~', $source_id, $redirect_url);
                }
                redirect($redirect_url);
            } else {
                redirect('home');
            }
        }
    }
    
    /*
     * Fonction  pour afficher la page avec un formulaire d'ajout d'un élément avec une clé externe provenant de l'element  parent (exemple ajout d'un utilisateur à partir d'un groupe d'utilisateur)
     *Function to display the page with a form for adding an element with an external key from the parent element (example adding a user from a user group)
     * Input: 	$ref_table_child:le nom de la structure de l'élément enfant
     * 			$child_field: le nom de la clé externe dans la table enfant
     * 			$ref_table_parent:le nom de la structure de l'élement parent
     * 			$parent_id: l'id de l'élément parent
     * 			$data : informations sur l'élément enfant si la fonction est utilisé pour la mis à jour(modification)
     * 			$operation: type de l'opération ajout (new) ou modification(edit)
     * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
     */
    /*
           * spécialisation de la fonction add_ref_child lorsque le formulaire s'affiche en pop up
           * specialization of the add_ref_child function when the form is displayed as a pop-up
           */
    public function add_element_child_modal($ref_table_child = "users", $child_field = "user_usergroup", $ref_table_parent = "usergroup", $parent_id = 2, $data = "", $operation = "new", $display_type = "normal")
    {
        $this->add_element_child($ref_table_child, $child_field, $ref_table_parent, $parent_id, $data, $operation, "modal");
    }
    
    //public function add_element_child($operation_name,$child_field,$ref_table_parent,$parent_id, $data = "",$operation="new",$display_type="normal") {
    
    //handles the process of adding a new child element with an external key from the parent table (example adding a user from a user group)
    public function add_element_child($operation_name, $parent_id, $data = array(), $operation = "new", $display_type = "normal")
    {
        if ((!is_array($data)) and $data == '_')
            $data = array();
        $op = check_operation($operation_name, 'AddChild');
        $ref_table_child = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        $is_guest = check_guest();
        if (!$is_guest) {
            if (admin_config($ref_table_child))
                $data['left_menu_admin'] = True;
            /*
             * chargement de la manière d'affichage du formulaire
             * loading form display way
             */
            $this->session->set_userdata('submit_mode', $display_type);
            /*
             * Récupération de la configuration(structure) de la table enfant
             * Retrieving the configuration (structure) of the child table
             */
            $table_config_child = get_table_configuration($ref_table_child);
            $table_config_child['current_operation'] = $ref_table_operation;
            //print_test($table_config_child['operations'][$ref_table_operation]); exit;
            $child_field = $table_config_child['operations'][$ref_table_operation]['master_field'];
            $ref_table_parent = $table_config_child['operations'][$ref_table_operation]['parent_config'];
            $table_config_child['config_id'] = $ref_table_child;
            foreach ($table_config_child['operations'][$ref_table_operation]['fields'] as $k => $v_field) {
                if ($v_field['field_state'] != 'hidden' and !empty($table_config_child['fields'][$k])) {
                    $v = $table_config_child['fields'][$k];
                    if (!empty($v['input_type']) and $v['input_type'] == 'select') {
                        if ($v['input_select_source'] == 'table') {
                            $filter = array();
                            if (!empty($v['category_type']) and $v['category_type'] = 'DependentDynamicCategory' and !empty($v['filter_field'])) {
                                $filter = array(
                                    'filter_field' => $v['filter_field'],
                                    'filter_value' => $parent_id,
                                );
                            }
                            if (isset($table_config_child['fields'][$k]['multi-select']) and $table_config_child['fields'][$k]['multi-select'] == 'Yes') {
                                $table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], False, False, True, $filter);
                            } else {
                                $table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], True, False, FALSE, $filter);
                            }
                        }
                    }
                }
            }
            //To remove users not in current project
            if ($operation_name == 'add_reviewer') {
                if (!empty($table_config_child['fields']['user_id']['input_select_values']) and is_array($table_config_child['fields']['user_id']['input_select_values'])) {
                    foreach ($table_config_child['fields']['user_id']['input_select_values'] as $key_user => $value_user) {
                        if (!empty($key_user)) {
                            if (!(user_project($this->session->userdata('project_id'), $key_user))) {
                                unset($table_config_child['fields']['user_id']['input_select_values'][$key_user]);
                            }
                        }
                    }
                }
            }
            /*
             * Prépartions des valeurs qui vont apparaitres dans le formulaire
             * Preparation of the values that will appear in the form
             */
            //print_test($data);
            $data['content_item'][$child_field] = $parent_id;
            $data['table_config'] = $table_config_child;
            $data['operation_type'] = $operation;
            $data['operation_source'] = "parent";
            $data['child_field'] = $child_field;
            $data['table_config_parent'] = $ref_table_parent;
            $data['parent_id'] = $parent_id;
            /*
                      * Titre de la page
                      *  Page title
                      */
            $current_parent_name = "";
            if (!empty($table_config_child['operations'][$ref_table_operation]['parent_detail_source']) and !empty($table_config_child['operations'][$ref_table_operation]['parent_detail_source_field'])) {
                $parent_detail = $this->DBConnection_mdl->get_row_details($table_config_child['operations'][$ref_table_operation]['parent_detail_source'], $parent_id, true, $ref_table_parent);
                //print_test($parent_detail);
                if (!empty($parent_detail[$table_config_child['operations'][$ref_table_operation]['parent_detail_source_field']])) {
                    $current_parent_name = $parent_detail[$table_config_child['operations'][$ref_table_operation]['parent_detail_source_field']];
                }
            }
            if (isset($table_config_child['operations'][$ref_table_operation]['page_title'])) {
                $data['page_title'] = lng($table_config_child['operations'][$ref_table_operation]['page_title']);
            } else {
                $data['page_title'] = lng("Add " . $table_config_child['entity_label']);
            }
            $data['page_title'] = str_replace('~current_parent_name~', $current_parent_name, $data['page_title']);
            /*
                      * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
                      * Creation of buttons that will be displayed at the top of the page (top_buttons)
                      */
            $data['top_buttons'] = $this->create_top_buttons($table_config_child['operations'][$ref_table_operation]['top_links']);
            if (!empty($table_config_child['operations'][$ref_table_operation]['redirect_after_save'])) {
                $after_save_redirect = $table_config_child['operations'][$ref_table_operation]['redirect_after_save'];
                $after_save_redirect = str_replace('~current_element~', $parent_id, $after_save_redirect);
            } else {
                $after_save_redirect = "home";
            }
            $this->session->set_userdata('after_save_redirect', $after_save_redirect);
            /*
             * La vue qui va s'afficher
             * The view that will be displayed
             */
            $data['page'] = 'general/frm_entity';
            if (!empty($table_config_child['operations'][$ref_table_operation]['page_template'])) {
                $data['page'] = $table_config_child['operations'][$ref_table_operation]['page_template'];
            }
            /*
             * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
             * Loading of the view with the data prepared in the controller according to the type of display: (modal popup or not)
             */
            if ($display_type == 'modal') {
                $this->load->view('general/frm_entity_modal', $data);
            } else {
                $this->load->view('shared/body', $data);
            }
        } else {
            set_top_msg('No access to this operation!', 'error');
            if (!empty($table_config_child['operations'][$ref_table_operation]['redirect_after_save'])) {
                $after_save_redirect = $table_config_child['operations'][$ref_table_operation]['redirect_after_save'];
                $after_save_redirect = str_replace('~current_element~', $parent_id, $after_save_redirect);
            } else {
                $after_save_redirect = "home";
            }
            redirect($after_save_redirect);
        }
    }

    //handles the process of adding a new child element with an external key from the parent table (example adding a user from a user group)
    public function add_element_child_old($ref_table_child, $child_field, $ref_table_parent, $parent_id, $data = "", $operation = "new", $display_type = "normal")
    {
        if (admin_config($ref_table_child))
            $data['left_menu_admin'] = True;
        /*
         * chargement de la manière d'affichage du formulaire
         */
        $this->session->set_userdata('submit_mode', $display_type);
        /*
         * Récupération de la configuration(structure) de la table enfant
         */
        $table_config_child = get_table_config($ref_table_child);
        $table_config_child['config_id'] = $ref_table_child;
        $table_config_parent = get_table_config($ref_table_parent);
        $table_config_child['fields'][$child_field]['on_add'] = "hidden";
        $table_config_child['fields'][$child_field]['on_edit'] = "hidden";
        $table_config_child['fields'][$child_field]['input_type'] = "text";
        /*
         * récupération des valeurs qui vont apparaitre dans les champs select
         */
        //print_test($table_config_parent);
        foreach ($table_config_child['fields'] as $k => $v) {
            if (!empty($v['input_type']) and $v['input_type'] == 'select') {
                //	print_test($v);
                if ($v['input_select_source'] == 'table') {
                    if (isset($table_config_child['fields'][$k]['multi-select']) and $table_config_child['fields'][$k]['multi-select'] == 'Yes') {
                        $table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], False, False, True);
                    } else {
                        $table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values']);
                    }
                }
            }
        }
        /*
         * Prépartions des valeurs qui vont apparaitres dans le formulaire
         */
        $data['content_item'][$child_field] = $parent_id;
        $data['table_config'] = $table_config_child;
        $data['operation_type'] = $operation;
        $data['operation_source'] = "parent";
        $data['child_field'] = $child_field;
        $data['table_config_parent'] = $ref_table_parent;
        $data['parent_id'] = $parent_id;
        /*
                        * Titre de la page
               
                        */
        if (isset($table_config_parent['entity_title']['add_child'])) {
            $data['page_title'] = lng($table_config_parent['entity_title']['add_child']);
        } else {
            $data['page_title'] = lng('Add ' . $table_config_child['reference_title_min']);
        }
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        /*
         * La vue qui va s'afficher
         */
        $data['page'] = 'general/frm_reference';
        //print_test($data);
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        if ($display_type == 'modal') {
            $this->load->view('general/frm_reference_modal', $data);
        } else {
            $this->load->view('shared/body', $data);
        }
    }

    /*
     * spécialisation de la fonction add_ref_drilldown lorsque le formulaire s'affiche en pop up
     */
    public function add_element_drilldown_modal($ref_table_child, $ref_table_parent, $parent_field, $parent_id, $data = "", $operation = "new")
    {
        $this->add_element_drilldown($ref_table_child, $ref_table_parent, $parent_field, $parent_id, $data, $operation, "modal");
    }

    /*
     * Fonction  pour afficher la page avec un formulaire d'ajout d'un élément avec une clé externe provenant de l'élément enfant
     *
     * Input: 	$ref_table_child:le nom de la structure de l'élément enfant
     * 			$ref_table_parent:le nom de la structure de l'élément parent
     * 			$child_field: le champs qui va prendre la clé de l'element enfant
     *			$parent_id: l'id de l'element parent
     * 			$data : informations sur l'element enfant si la fonction est utilisé pour la mis à jour(modification)
     * 			$operation: type de l'opération ajout (new) ou modification(edit)
     * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
     */
    public function add_element_drilldown($operation_name, $parent_id, $data = "", $operation = "new", $display_type = "normal", $op_type = "AddDrill")
    {
        $init_op_type = $op_type;
        if ($op_type == 'EditChild_validation') {
            $op_type = 'EditChild';
        }
        $op = check_operation($operation_name, $op_type);
        $ref_table_child = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        if (admin_config($ref_table_child))
            $data['left_menu_admin'] = True;
        //print_test($op);
        //print_test($data);
        //exit;
        /*
         * chargement de la manière d'affichage du formulaire
         */
        $this->session->set_userdata('submit_mode', $display_type);
        /*
         * Récupération de la configuration(structure) de la table enfant
         */
        $table_config_child = get_table_configuration($ref_table_child);
        $table_config_child['config_id'] = $ref_table_child;
        if ($init_op_type == 'EditChild_validation') {
            $content_item = $this->DBConnection_mdl->get_row_details($table_config_child['operations'][$ref_table_operation]['data_source'], $parent_id, true);
            //	print_test($data);
            //	print_test($content_item);
            if (!empty($content_item)) {
                foreach ($content_item as $key => $value) {
                    if (!isset($data['content_item'][$key]))
                        $data['content_item'][$key] = $value;
                }
            }
            //		print_test($data);
            //	exit;
        }
        $table_config_child['current_operation'] = $ref_table_operation;
        //print_test($table_config_child['operations'][$ref_table_operation]); exit;
        $parent_field = $table_config_child['operations'][$ref_table_operation]['master_field'];
        $ref_table_parent = $table_config_child['operations'][$ref_table_operation]['parent_config'];
        /*
         * Récupération de la configuration(structure) de la table parent
         */
        $table_config_parent = get_table_configuration($ref_table_parent);
        $op_type = ($operation == 'new') ? 'on_add' : 'on_edit';
        /*
         * récupération des valeurs qui vont apparaitre dans les champs select
         */
        /*foreach ($table_config_child['fields'] as $k => $v) {
                
                        if(!empty($v['input_type']) AND $v['input_type']=='select' AND ($v[$op_type]!='hidden' AND $v[$op_type]!='not_set') ){
                            if($v['input_select_source']=='table' ){
                                if(isset($table_config_child['fields'][$k]['multi-select']) AND $table_config_child['fields'][$k]['multi-select']=='Yes' )
                                {
                                    $table_config_child['fields'][$k]['input_select_values']= $this->manager_lib->get_reference_select_values($v['input_select_values'],False,False,True);
                                        
                                        
                                }else{
                                    $table_config_child['fields'][$k]['input_select_values']= $this->manager_lib->get_reference_select_values($v['input_select_values']);
                                }
                                    
                            }
                        }
                
                    }*/
        foreach ($table_config_child['operations'][$ref_table_operation]['fields'] as $k => $v_field) {
            if (!empty($table_config_child['fields'][$k])) {
                $v = $table_config_child['fields'][$k];
                if (!empty($v['input_type']) and $v['input_type'] == 'select') {
                    if ($v['input_select_source'] == 'table') {
                        $filter = array();
                        if (!empty($v['category_type']) and $v['category_type'] = 'DependentDynamicCategory' and !empty($v['filter_field'])) {
                            $filter = array(
                                'filter_field' => $v['filter_field'],
                                'filter_value' => $parent_id,
                            );
                        }
                        if (isset($table_config_child['fields'][$k]['multi-select']) and $table_config_child['fields'][$k]['multi-select'] == 'Yes') {
                            $table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], False, False, True, $filter);
                        } else {
                            $table_config_child['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], True, False, False, $filter);
                        }
                    }
                }
            }
        }
        /*
         * Prépartions des valeurs qui vont apparaitres dans le formulaire
         */
        $data['parent_field'] = $parent_field;
        $data['parent_id'] = $parent_id;
        $data['parent_table'] = $table_config_parent['table_name'];
        $data['table_config'] = $table_config_child;
        $data['operation_type'] = $operation;
        $data['operation_source'] = "drilldown";
        $data['parent_field'] = $parent_field;
        $data['table_config_parent'] = $ref_table_parent;
        /*
         * Titre de la page
         */
        if (isset($table_config_child['operations'][$ref_table_operation]['page_title'])) {
            $data['page_title'] = lng($table_config_child['operations'][$ref_table_operation]['page_title']);
        } else {
            $data['page_title'] = lng($table_config_child['entity_label']);
        }
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = $this->create_top_buttons($table_config_child['operations'][$ref_table_operation]['top_links']);
        if (!empty($table_config_child['operations'][$ref_table_operation]['redirect_after_save'])) {
            $after_save_redirect = $table_config_child['operations'][$ref_table_operation]['redirect_after_save'];
            $after_save_redirect = str_replace('~current_element~', $parent_id, $after_save_redirect);
        } else {
            $after_save_redirect = "home";
        }
        $this->session->set_userdata('after_save_redirect', $after_save_redirect);
        /*
         * La vue qui va s'afficher
         */
        $data['page'] = 'general/frm_entity';
        if (!empty($table_config_child['operations'][$ref_table_operation]['page_template'])) {
            $data['page'] = $table_config_child['operations'][$ref_table_operation]['page_template'];
        }
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        if ($display_type == 'modal') {
            $this->load->view('general/frm_entity_modal', $data);
        } else {
            $this->load->view('shared/body', $data);
        }
    }

    /*
     * Affichage du formulaire pour modifier un élément
     * $ref_table: le nom de la structure de  la table de l'élément
     * $ref_id: id de l'élement
     * $display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
     */
    public function edit_element($operation_name, $ref_id, $display_type = "normal")
    {
        $is_guest = check_guest();
        $op = check_operation($operation_name, 'Edit');
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        $table_config = get_table_configuration($ref_table);
        if (!$is_guest) {
            /*
             * Récupération de la configuration(structure) de la table de l'element
             * Retrieving the configuration (structure) of the element table
             */
            if ($ref_table == 'papers') {
                ///redirect("paper/bibler_edit_paper/".$ref_id."/");
            }
            if ($ref_table_operation == 'edit_user_min' and $ref_id != active_user_id()) {
                set_top_msg('No access to this operation!', 'error');
                redirect('home');
            }
            /*
             * Appel de la fonction du model pour récupérer la ligne à modifier
             * Call of the model function to retrieve the row to modify
             */
            //$data ['content_item'] = $this->DBConnection_mdl->get_row_details($ref_table,$ref_id);
            $data['content_item'] = $this->DBConnection_mdl->get_row_details($table_config['operations'][$ref_table_operation]['data_source'], $ref_id, true, $ref_table);
            //	print_test($data);
            //$table_config['current_operation']=$ref_table_operation;
            if (!empty($table_config['operations'][$ref_table_operation]['support_drilldown'])) {
                $table_config['current_operation'] = $table_config['operations'][$ref_table_operation]['drilldown_source'];
                $item_data = $this->manager_lib->get_detail($table_config, $ref_id, True, True);
                //print_test($item_data);
                foreach ($item_data as $key => $value) {
                    if (
                        !empty($table_config['operations'][$ref_table_operation]['fields'][$value['field_id']]) and
                        $table_config['operations'][$ref_table_operation]['fields'][$value['field_id']]['field_state'] == 'drill_down'
                    ) {
                        $data['drill_down_values'][$value['field_id']] = $value['val2'];
                    }
                }
            }
            //print_test($data);
            foreach ($table_config['operations'][$ref_table_operation]['fields'] as $key => $v_field) {
                $v = $table_config['fields'][$key];
                if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['input_select_source'] == 'table') {
                    if (!empty($v['multi-select']) and $v['multi-select'] == 'Yes') {
                        $Tvalues_source = explode(';', $v['input_select_values']);
                        $source_table_config = get_table_configuration($Tvalues_source[0]);
                        $input_select_key_field = $v['input_select_key_field'];
                        $input_child_field = $Tvalues_source[1];
                        $extra_condition = " AND $input_select_key_field ='" . $ref_id . "'";
                        $res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $input_child_field, $extra_condition);
                        $data['content_item'][$key] = array();
                        foreach ($res_values as $key_r => $value_r) {
                            array_push($data['content_item'][$key], $value_r['refDesc']);
                        }
                    }
                }
            }
            $data['current_element'] = $ref_id;
            //print_test($data); exit;
            /*
             * Calling the form display function
             */
            $this->add_element($operation_name, $data, 'edit', $display_type, 'Edit');
        } else {
            set_top_msg('No access to this operation!', 'error');
            if (!empty($table_config['operations'][$ref_table_operation]['redirect_after_save'])) {
                $redirect_url = $table_config['operations'][$ref_table_operation]['redirect_after_save'];
                if (!empty($source_id)) {
                    $redirect_url = str_replace('~current_element~', $source_id, $redirect_url);
                }
                redirect($redirect_url);
            } else {
                redirect('home');
            }
        }
    }

    //handles the process of saving the submitted form data into the database.
    public function save_element()
    {
        /*
         * Récuperation des valeurs soumis dans le formulaire
         */
        $post_arr = $this->input->post();
        //print_test($post_arr); exit;
        /*
         * Récupération de la configuration (structure ) de la table qui est concerné
         */
        $table_config = get_table_configuration($post_arr['table_config']);
        $current_operation = $post_arr['current_operation'];
        //print_test($post_arr); 
        //print_test($table_config); exit;
        if (!empty($post_arr[$table_config['table_id']])) {
            $data['current_element'] = $post_arr[$table_config['table_id']];
            //print_test($data);
        }
        if ($post_arr['operation_type'] == 'new') {
            $var_check = "on_add";
        } else {
            $var_check = "on_edit";
        }
        $operation_type = $post_arr['operation_type'];
        //print_test($post_arr); exit;
        /*
         * Validation du formulaire: vérification si les valeurs sont rémplis correctement
         */
        $this->load->library('form_validation');
        $other_check = true;
        $data['err_msg'] = ''; //for users
        $images_to_upload = array();
        $multi_select_values = array();
        foreach ($table_config['operations'][$current_operation]['fields'] as $key => $value) {
            $validation = "trim";
            $field_info = $table_config['fields'][$key];
            if ($value['field_state'] == 'enabled') {
                if (!empty($value['mandatory']) and (trim($value['mandatory']) == "mandatory")) {
                    if ((isset($field_info['multi-select']) and isset($field_info['multi-select']) == 'Yes')) {
                        if (empty($post_arr[$key])) {
                            $other_check = false;
                            $data['err_msg'] .= " The Field '" . $field_info['field_title'] . "' is required<br/>";
                        }
                    } else {
                        $validation .= "|required";
                    }
                }
                $this->form_validation->set_rules($key, '"' . $field_info['field_title'] . '"', $validation);
                if (isset($value['pattern']) and $value['pattern'] == 'valid_email') {
                    if (!empty($post_arr[$key])) {
                        $this->form_validation->set_rules($key, $field_info['field_title'], 'trim|valid_email');
                    }
                }
                if (
                    !empty($field_info['category_type']) and $field_info['category_type'] == 'FreeCategory'
                    and !empty($field_info['field_type']) and $field_info['field_type'] == 'int'
                ) {
                    if (!empty($post_arr[$key])) {
                        $this->form_validation->set_rules($key, $field_info['field_title'], 'trim|integer');
                    }
                }
                if (
                    !empty($field_info['category_type']) and $field_info['category_type'] == 'FreeCategory'
                    and !empty($field_info['field_type']) and $field_info['field_type'] == 'real'
                ) {
                    if (!empty($post_arr[$key])) {
                        $this->form_validation->set_rules($key, $field_info['field_title'], 'callback_numeric_wcomma');
                    }
                }
            }
            if (isset($field_info['multi-select']) and $field_info['multi-select'] == 'Yes') { //multi- select
                if (!empty($post_arr[$key])) {
                    $multi_select_values[$key]['values'] = $post_arr[$key];
                    $multi_select_values[$key]['config'] = $field_info;
                } else {
                    $multi_select_values[$key]['values'] = array();
                    $multi_select_values[$key]['config'] = $field_info;
                }
            }
            if (isset($field_info['input_type']) and $field_info['input_type'] == 'image' and !empty($_FILES[$key]['name'])) {
                $post_arr[$key] = "";
                if (empty($_FILES[$key]['tmp_name'])) {
                    $other_check = false;
                    $data['err_msg'] .= lng('Problem with uploading image') . ' <br/>';
                } elseif (!file_exists($_FILES[$key]['tmp_name'])) {
                    $other_check = false;
                    $data['err_msg'] .= lng('Problem with uploading image') . ' <br/>';
                } elseif (!empty($_FILES[$key]['name'])) {
                    $images_to_upload[$key]['info'] = $_FILES[$key];
                    $file_extension = "";
                    switch ($_FILES[$key]['type']) {
                        case 'image/jpeg':
                            $file_extension = '.jpg';
                            break;
                        case 'image/png':
                            $file_extension = '.PNG';
                            break;
                        case 'image/gif':
                            $file_extension = '.gif';
                            break;
                    }
                    $image_upload_path = $this->config->item('image_upload_path');
                    if ($post_arr['table_config'] == 'author') {
                        $file_name = substr(Slug($post_arr['author_name']), 0, 20) . time();
                    } elseif ($post_arr['table_config'] == 'users') {
                        $file_name = substr(Slug($post_arr['user_name']), 0, 20) . time();
                    } else {
                        $file_name = time();
                    }
                    $config['file_name'] = $file_name . $file_extension;
                    $config['upload_path'] = FCPATH . $image_upload_path;
                    $config['allowed_types'] = 'gif|jpg|png';
                    $config['overwrite'] = TRUE;
                    $config['remove_spaces'] = TRUE;
                    $config['max_filename'] = '0';
                    $config['max_size'] = $this->config->item('image_max_size');
                    $config['max_width'] = '0'; // should be 360 at destination
                    $config['max_height'] = '0'; // should be 300 at destination
                    $images_to_upload[$key]['config'] = $config;
                    $images_to_upload[$key]['picture_name'] = $file_name;
                    //save the name of the picture used
                    $post_arr[$key] = $file_name;
                }
            }
        }
        if ($post_arr['table_config'] == 'users') {
            $this->form_validation->set_rules('user_username', $table_config['fields']['user_username']['field_title'], 'trim|required|min_length[2]|max_length[12]');
            if (!(empty($post_arr['user_password']) and $post_arr['operation_type'] == 'edit')) {
                $this->form_validation->set_rules('user_password', $table_config['fields']['user_password']['field_title'], 'trim|required|matches[user_password_val]');
                $this->form_validation->set_rules('user_password_val', $table_config['fields']['user_password']['field_title'] . ' Confirmation', 'trim|required');
            }
            //	if(!empty($post_arr['user_mail'])){
            //		$this->form_validation->set_rules('user_mail', 'Email', 'trim|valid_email');
            //	}
            ///vérify if the username is unique
            if (!empty($post_arr['user_username']) and ($post_arr['operation_type'] == 'new') and !$this->user_lib->login_available($post_arr['user_username'])) {
                $data['err_msg'] .= 'Username already used <br/>';
                $other_check = FALSE;
            }
        }
        if (!empty($table_config['operations'][$current_operation]['check_exist'])) {
            $record_exist = $this->check_record_exist($table_config['operations'][$current_operation]['check_exist'], $table_config, $post_arr);
            //	print_test($record_exist);
            if ($record_exist != '0') {
                $data['err_msg'] .= $table_config['operations'][$current_operation]['check_exist']['message'] . "<br/>";
                $other_check = FALSE;
            }
        }
        //	s
//	exit;
        $operation_source = $post_arr['operation_source'];
        $parent_id = $post_arr['parent_id'];
        //	print_test($table_config); exit;
        if (isset($post_arr['table_config_parent']))
            $table_config_parent = $post_arr['table_config_parent'];
        if ($this->form_validation->run() == FALSE or !$other_check) {
            /*
             * Si la validation du formulaire n'est pas concluante , retour au formulaire de saisie
             */
            $data['content_item'] = $post_arr;
            if ($this->session->userdata('submit_mode') and $this->session->userdata('submit_mode') == 'modal') {
                $submit_mode = 'modal';
                //$this->add_element ($post_arr ['table_config'],$data,$post_arr['operation_type'],'modal' );
            } else {
                //$this->add_element ($post_arr ['table_config'],$data,$post_arr['operation_type'] );
                $submit_mode = '';
            }
            if (($table_config['operations'][$current_operation]['operation_type']) == 'Add') {
                $this->add_element($current_operation, $data, $post_arr['operation_type'], $submit_mode);
            } elseif ($table_config['operations'][$current_operation]['operation_type'] == 'Edit') {
                $this->add_element($current_operation, $data, $post_arr['operation_type'], $submit_mode, 'Edit');
            } elseif ($table_config['operations'][$current_operation]['operation_type'] == 'AddChild') {
                $this->add_element_child($current_operation, $post_arr['parent_id'], $data, $post_arr['operation_type'], $submit_mode);
            } elseif ($table_config['operations'][$current_operation]['operation_type'] == 'EditChild') {
                $this->add_element_drilldown($current_operation, $post_arr['parent_id'], $data, $post_arr['operation_type'], $submit_mode, 'EditChild_validation');
            }
        } else {
            /*
             * Si la validation du formulaire est concluante, proceder à l'enregistrement
             */
            /*
             * Si le formulaire contient des images commencer par les redimentionner puis les sauvergarder
             */
            $image_upload_result = TRUE;
            if (!empty($images_to_upload)) {
                foreach ($images_to_upload as $k_img => $v_img) {
                    $this->load->library('upload', $v_img['config']);
                    if (!$this->upload->do_upload($k_img)) {
                        $image_upload_result = False;
                        $error = array(
                            'error' => $this->upload->display_errors()
                        );
                        $data['err_msg'] = $error['error'];
                        //echo $data ['err_msg'] ;
                    } else {
                        // resizing images
                        // image size after resizing
                        /*$thumb_size=$this->config->item('image_thumb_size');
                                                            $medium_size=$this->config->item('image_medium_size');
                                                            $big_size=$this->config->item('image_big_size');
                                                            */
                        $image_default_size = $this->config->item('image_default_size');
                        $image_upload_path = $this->config->item('image_upload_path');
                        $image_data = $this->upload->data();
                        $config_default = array(
                            'source_image' => $image_data['full_path'],
                            'new_image' => FCPATH . $image_upload_path . $v_img['picture_name'] . "_resized.png",
                            'maintain_ratio' => true,
                            'width' => $image_default_size,
                            'height' => $image_default_size
                        );
                        $this->load->library('image_lib');
                        //$this->image_lib->initialize($config_thumb);
                        //$this->image_lib->clear();
                        $this->image_lib->initialize($config_default);
                        if (!$this->image_lib->resize()) {
                            die($this->image_lib->display_errors());
                        }
                        $res_image = $config_default['new_image'];
                        $fp = fopen($res_image, 'r');
                        $data_img = fread($fp, filesize($res_image));
                        //$data_img = addslashes($data_img);
                        fclose($fp);
                        $post_arr[$k_img] = $data_img;
                        //remove temp images
                        unlink($config_default['new_image']);
                        //unlink($config_medium['new_image']);
                        //unlink($config_big['new_image']);
                        unlink($config_default['source_image']);
                    }
                }
            }
            if (!$image_upload_result) {
                /*
                 * Si le chagement d'image de marche pas retour au formulaire d'ajout
                 */
                $data['content_item'] = $post_arr;
                if (($table_config['operations'][$current_operation]['operation_type']) == 'Add') {
                    $this->add_element($current_operation, $data, $post_arr['operation_type'], $submit_mode);
                } elseif ($table_config['operations'][$current_operation]['operation_type'] == 'Edit') {
                    $this->add_element($current_operation, $data, $post_arr['operation_type'], $submit_mode, 'Edit');
                } elseif ($table_config['operations'][$current_operation]['operation_type'] == 'AddChild') {
                    $this->add_element_child($current_operation, $post_arr['parent_id'], $data, $post_arr['operation_type']);
                } elseif ($table_config['operations'][$current_operation]['operation_type'] == 'EditChild') {
                    $this->add_element_drilldown($current_operation, $post_arr['parent_id'], $data, $post_arr['operation_type'], $submit_mode, 'EditChild_validation');
                }
            } else {
                /*
                 * Pour les utilisateur, si lors de la modification on a pas saisie un mot de passe ou ajouter une nouvelle photo , on garde les anciennes valeurs
                 */
                if ($post_arr['table_config'] == 'users') {
                    if (!(empty($post_arr['user_password']) and $post_arr['operation_type'] == 'edit')) {
                        $post_arr['user_password'] = md5($post_arr['user_password']);
                    } else {
                        if (!empty($post_arr['user_password_old'])) {
                            $post_arr['user_password'] = $post_arr['user_password_old'];
                        } else {
                            unset($post_arr['user_password']);
                        }
                    }
                    if ($post_arr['operation_type'] == 'edit' and empty($post_arr['user_picture'])) {
                        $post_arr['user_picture'] = !empty($post_arr['user_picture_old']) ? $post_arr['user_picture_old'] : "";
                    }
                    unset($post_arr['user_password_val']);
                    unset($post_arr['user_picture_old']);
                }
                /*
                 * Préparation des données avant l'appel de la foction qui va suvegarder les valeurs dans la BD
                 */
                $drill_table_config_parent = isset($post_arr['table_config_parent']) ? $post_arr['table_config_parent'] : "";
                $drill_parent_field = isset($post_arr['parent_field']) ? $post_arr['parent_field'] : "";
                $drill_parent_id = isset($post_arr['parent_id']) ? $post_arr['parent_id'] : 0;
                $drill_parent_table = isset($post_arr['parent_table']) ? $post_arr['parent_table'] : "";
                unset($post_arr['operation_source']);
                unset($post_arr['child_field']);
                unset($post_arr['table_config_parent']);
                unset($post_arr['parent_id']);
                unset($post_arr['parent_field']);
                unset($post_arr['parent_table']);
                $post_arr['table_name'] = $table_config['table_name'];
                $post_arr['table_id'] = $table_config['table_id'];
                //	print_test($post_arr); //exit;
                /*
                 * Appel de la fonction dna le modèle pour suvegarder les données dans la BD
                 */
                $saved_res = $this->DBConnection_mdl->save_reference_mdl($post_arr, 'get_id');
                $success_msg = !empty($table_config['operations'][$current_operation]['success_message']) ? $table_config['operations'][$current_operation]['success_message'] : 'Success';
                if ($saved_res) {
                    echo ("Enregistrement reussit");
                    set_top_msg($success_msg);
                    if ($operation_source == 'exclusion') {
                        /*
                         * Pour l'exclusion d'un papier après la sauvegarde des info sur l'exclusion on appelle une fonction pour mettre à jour le papier
                         */
                        $res = $this->Paper_dataAccess->exclude_paper($parent_id);
                    }
                    /*
                     * Pour l'enregistrement d'un élément enfant on met à jour la clef externe dans élément parent
                     */
                    if ($operation_source == 'drilldown' and $operation_type == 'new') {
                        $table_config_parent = get_table_config($drill_table_config_parent);
                        $parent_table_id = $table_config_parent['table_id'];
                        $array_drill = array(
                            'operation_type' => 'edit',
                            'table_config' => $drill_table_config_parent,
                            'table_name' => $table_config_parent['table_name'],
                            'table_id' => $parent_table_id,
                            $parent_table_id => $drill_parent_id,
                            $drill_parent_field => $saved_res,
                        );
                        $res_drill = $this->manage_mdl->save_reference($array_drill);
                    }
                    /*
                     * Si le formulaire contient des champs multi-select : on appel une fonction pour sauvegarder ces valeurs dans leur table
                     */
                    if (!empty($multi_select_values)) {
                        $this->save_multi_select($multi_select_values, $saved_res);
                    }
                    $message_modal = "modal_relis_outputmessage_correct";
                } else {
                    // erreur d'enregistrement
                    $message_modal = "modal_relis_outputmessage_error";
                }
                //if($post_arr ['table_config'] =='config')
                //update_paper_status_all();
                if ($this->session->userdata('submit_mode') and $this->session->userdata('submit_mode') == 'modal') {
                    /*
                     * Si le formulaire sauvegardé est affiché en popup on retourne le message d'erreur
                     */
                    echo $message_modal;
                } else {
                    /*
                     * Si le formulaire sauvegardé n'est affiché en popup on redirige vers la page d'affichage suivant l'élément enregistre
                     */
                    $after_after_save_redirect = $this->session->userdata('after_save_redirect');
                    if (!empty($after_after_save_redirect)) {
                        $this->session->set_userdata('after_save_redirect', '');
                        redirect($after_after_save_redirect);
                    } elseif ($operation_source == 'paper' or $operation_source == 'assignation' or $operation_source == 'exclusion') {
                        redirect('data_extraction/display_paper/' . $parent_id);
                    } elseif ($operation_source == 'drilldown') {
                        redirect('manager/display_element/' . $drill_table_config_parent . '/' . $drill_parent_id);
                    } elseif ($operation_source == 'parent' and !empty($table_config_parent) and !empty($parent_id)) {
                        redirect('manager/display_element/' . $table_config_parent . '/' . $parent_id);
                    } else {
                        if ($table_config['table_name'] == 'paper') {
                            //redirect ( 'paper/list_paper');
                            redirect('data_extraction/display_paper/' . $saved_res);
                        } else {
                            redirect('manager/entity_list/' . $post_arr['table_config']);
                        }
                    }
                }
            }
        }
    }

    //valide whether a string consists of only numeric characters and/or a decimal point
    function numeric_wcomma($str)
    {
        //return preg_match('/^[0-9,]+$/', $str);
        if (preg_match('/^[0-9\\.]+$/', $str)) {
            return True;
        } else {
            $this->form_validation->set_message('numeric_wcomma', 'The {field} field must be a decimal number');
            return False;
        }
    }

    /*
     * Fonction pour la suppression d'un element
     * Input: 	$ref_table : nom de la structure de la table ou se trouve l'élément à supprimer
     * 			$row_id : id de l'élément à supprimer
     * 			$redirect: Y/N rediriger vers la liste d'éléments
     * 			$source_id: element ou p'operation a debuté pour la redirection
     */
    public function delete_element($operation_name, $row_id, $source_id = 0)
    {
        $op = check_operation($operation_name, 'Remove');
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        $table_configuration = get_table_configuration($ref_table);
        //	print_test($op);exit;
        /*
         * Appel de la foction dans le model pour appeler la requetter  de suppression de l'élément
         * Call of the function in the model to call the request to delete the element
         */
        //$res=$this->DBConnection_mdl->remove_element($row_id,$ref_table);
        $is_guest = check_guest();
        if (!$is_guest) {
            $res = $this->DBConnection_mdl->remove_element($row_id, $table_configuration['operations'][$ref_table_operation]['db_delete_model'], True);
        }
        /*
         * Message de confirmation ou erreur
         */
        if ($res) {
            set_top_msg(lng_min("Success"));
        } else {
            set_top_msg(lng_min(" Operation failed "), 'error');
        }
        /*
         *
         * Rédirection après l'opération si $redirect=true
         * Redirection after operation if $ redirect = true
         */
        //if($redirect){
        if (!empty($table_configuration['operations'][$ref_table_operation]['redirect_after_delete'])) {
            $redirect_url = $table_configuration['operations'][$ref_table_operation]['redirect_after_delete'];
            if (!empty($source_id)) {
                $redirect_url = str_replace('~current_element~', $source_id, $redirect_url);
            }
            redirect($redirect_url);
        } else {
            redirect('home');
        }
        //	}
    }

    /*
     * Fonction globale pour afficher la liste des élément suivant la structure de la table
     *
     * Input: $ref_table: nom de la configuration d'une page (ex papers, author)
     * 			$val : valeur de recherche si une recherche a été faite sur la table en cours
     * 			$page: la page affiché : ulilisé dans la navigation
     */
    public function entity_list($operation_name, $val = "_", $page = 0, $dynamic_table = 1)
    {
        $project_published = project_published();
        //print_test($project_published);
        $op = check_operation($operation_name, 'List');
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        if (admin_config($ref_table))
            $data['left_menu_admin'] = True;
        /*
                     * Vérification si il y a une condition de recherche
                     * Checking if there is a search condition
                     */
        $val = urldecode(urldecode($val));
        $filter = array();
        if (isset($_POST['search_all'])) {
            $filter = $this->input->post();
            unset($filter['search_all']);
            $val = "_";
            if (isset($filter['valeur']) and !empty($filter['valeur'])) {
                $val = $filter['valeur'];
                $val = urlencode(urlencode($val));
            }
            /*
             * mis à jours de l'url en ajoutant la valeur recherché dans le lien puis rechargement de l'url
             */
            $url = "element/entity_list/" . $operation_name . "/" . $val . "/0/";
            redirect($url);
        }
        /*
         * Récupération de la configuration(structure) de la table à afficher
         * Retrieving the configuration (structure) of the table to display
         */
        $ref_table_config = get_table_configuration($ref_table);
        //print_test($ref_table_config);
        $table_id = $ref_table_config['table_id'];
        if (!empty($ref_table_config['operations'][$ref_table_operation]['table_display_style'])) {
            if ($ref_table_config['operations'][$ref_table_operation]['table_display_style'] == 'dynamic_table') {
                $dynamic_table = 1;
            } else {
                $dynamic_table = 0;
            }
        }
        /*
                     * Appel du model pour récupérer la liste à aficher dans la Base de donnés
                     * Calling the model to retrieve the list to display in the Database
                     */
        $rec_per_page = ($dynamic_table) ? -1 : 0;
        $ref_table_config['current_operation'] = $ref_table_operation;
        //		 Fetching and storing the data from venue table into $data variable
        $data = $this->DBConnection_mdl->get_list_mdl($ref_table_config, $val, $page, $rec_per_page);
        //	print_test($data);
        /*
         * récupération des correspondances des clés externes pour l'affichage  suivant la structure de la table
         * retrieval of the correspondences of the external keys for display according to the structure of the table
         */
        $dropoboxes = array();
        foreach ($ref_table_config['operations'][$ref_table_operation]['fields'] as $k_field => $v) {
            if (!empty($ref_table_config['fields'][$k_field])) {
                $field_det = $ref_table_config['fields'][$k_field];
                if (!empty($field_det['input_type']) and $field_det['input_type'] == 'select') {
                    if ($field_det['input_select_source'] == 'array') {
                        //print_test($v);
                        $dropoboxes[$k_field] = $field_det['input_select_values'];
                    } elseif ($field_det['input_select_source'] == 'table') {
                        $dropoboxes[$k_field] = $this->manager_lib->get_reference_select_values($field_det['input_select_values']);
                        //	print_test($v);
                    } elseif ($field_det['input_select_source'] == 'yes_no') {
                        $dropoboxes[$k_field] = array(
                            '0' => "No",
                            '1' => "Yes"
                        );
                    }
                }
            }
        }
        /*
         * Vérification des liens (links) a afficher sur la liste
         * Checking the links to display on the list
         */
        $list_links = array();
        $add_link = false;
        $add_link_url = "";
        $is_guest = check_guest();
        foreach ($ref_table_config['operations'][$ref_table_operation]['list_links'] as $link_type => $link) {
            $link['type'] = $link_type;
            if (empty($link['title'])) {
                $link['title'] = lng_min($link['label']);
            }
            $push_link = false;
            switch ($link_type) {
                case 'view':
                    if (!isset($link['icon']))
                        $link['icon'] = 'folder';
                    if (empty($link['url']))
                        $link['url'] = 'element/display_element/' . $ref_table . '/';
                    $push_link = true;
                    break;
                case 'edit':
                    if (!$is_guest) {
                        if (!isset($link['icon']))
                            $link['icon'] = 'pencil';
                        if (empty($link['url']))
                            $link['url'] = 'element/edit_element/' . $ref_table . '/';
                        $push_link = true;
                    }
                    break;
                case 'delete':
                    if (!$is_guest) {
                        $link['delete_alert'] = True;
                        if (!isset($link['icon']))
                            $link['icon'] = 'trash';
                        if (empty($link['url']))
                            $link['url'] = 'element/delete_element/' . $ref_table . '/';
                        $push_link = true;
                    }
                    break;
                case 'add_child':
                    if (!$is_guest) {
                        if (!isset($link['icon']))
                            $link['icon'] = 'plus';
                        if (!empty($link['url'])) {
                            $link['url'] = 'element/add_element_child/' . $link['url'] . "/" . $ref_table . "/";
                            $push_link = true;
                        }
                    }
                    break;
                default:
                    break;
            }
            if ($push_link and (!$project_published or $link_type == 'view'))
                array_push($list_links, $link);
        }
        /*
         * Préparation de la liste à afficher sur base du contenu et  stucture de la table
         * Preparation of the list to display based on the content and structure of the table
         */
        /**
         * @var array $field_list va contenir les champs à afficher
         * @var array $ field_list will contain the fields to display
         */
        $link_field_list = array();
        $field_list = array();
        $field_list_header = array();
        foreach ($ref_table_config['operations'][$ref_table_operation]['fields'] as $k => $v) {
            //print_test($k);
            //print_test($v);
            if (!empty($ref_table_config['fields'][$k])) {
                //$field_det=$ref_table_config['fields'][$k];
                array_push($field_list, $k);
                $field_header = !empty($v['field_title']) ? $v['field_title'] : $ref_table_config['fields'][$k]['field_title'];
                array_push($field_list_header, $field_header);
                if (!empty($v['link'])) {
                    $link_field_list[$k] = $v;
                }
            }
        }
        //print_test($link_field_list);
        $i = 1;
        $list_to_display = array();
        foreach ($data['list'] as $key => $value) {
            //print_test($value);
            $element_array = array();
            $element_array['links'] = '';
            foreach ($field_list as $key_field => $v_field) {
                //print_test($v_field);
                if (isset($value[$v_field])) {
                    if (isset($dropoboxes[$v_field][$value[$v_field]])) {
                        $element_array[$v_field] = $dropoboxes[$v_field][$value[$v_field]];
                    } elseif (empty($value[$v_field]) and empty($ref_table_config['fields'][$v_field]['display_null'])) {
                        $element_array[$v_field] = "";
                    } else {
                        $element_array[$v_field] = $value[$v_field];
                    }
                } else {
                    $element_array[$v_field] = "";
                    if (
                        (isset($ref_table_config['fields'][$v_field]['number_of_values']) and $ref_table_config['fields'][$v_field]['number_of_values'] != 1) or
                        (isset($ref_table_config['fields'][$v_field]['category_type']) and $ref_table_config['fields'][$v_field]['category_type'] == 'WithSubCategories')
                    ) { //recuperation pour les multivalues et les champs avec subcategory
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
                if (!empty($link_field_list[$v_field]))
                    $element_array[$v_field] = string_anchor(
                        $link_field_list[$v_field]['link']['url'] . $value[$link_field_list[$v_field]['link']['id_field']],
                        $element_array[$v_field],
                        $link_field_list[$v_field]['link']['trim']
                    );
            }
            //print_test($element_array);
            /*
             * Ajout des liens(links) sur la liste
             * Adding links to the list
             */
            $action_button = "";
            $arr_buttons = array();
            foreach ($list_links as $key_l => $value_l) {
                //setting redo link for list of operations
                if ($ref_table == 'operations' and !empty($value['operation_state'])) {
                    if ($value['operation_state'] == 'Cancelled') {
                        $value_l['icon'] = 'repeat';
                        $value_l['label'] = 'Redo';
                        $value_l['url'] = 'manager/undo_cancel_operation/';
                    }
                }
                if (!empty($value_l['icon']))
                    $value_l['label'] = icon($value_l['icon']) . ' ' . lng_min($value_l['label']);
                array_push(
                    $arr_buttons,
                    array(
                        'url' => $value_l['url'] . $value[$table_id],
                        'label' => $value_l['label'],
                        'title' => $value_l['title'],
                        'delete_alert' => !empty($value_l['delete_alert']) ? True : False,
                        'btn_type' => !empty($value_l['btn_type']) ? $value_l['btn_type'] : 'btn-info'
                    )
                );
                //print_test($value_l);
            }
            $action_button = create_button_link_dropdown($arr_buttons, lng_min('Action'));
            //print_test($action_button);
            if (!empty($action_button)) {
                $element_array['links'] = $action_button;
            } else {
                unset($element_array['links']);
            }
            if (isset($element_array[$table_id])) {
                $element_array[$table_id] = $i + $page;
            }
            array_push($list_to_display, $element_array);
            $i++;
        }
        $data['list'] = $list_to_display;
        /*
         * Ajout de l'entête de la liste
         * Adding the list header
         */
        if (!empty($data['list'])) {
            //$array_header=$ref_table_config['header_list_fields'];
            $array_header = $field_list_header;
            if (!empty($data['list'][$key]['links'])) {
                array_unshift($array_header, '');
            }
            if (!$dynamic_table) {
                array_unshift($data['list'], $array_header);
            } else {
                $data['list_header'] = $array_header;
            }
        }
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         * Creation of the buttons that will be displayed at the top of the page (top_buttons)
         */
        $data['top_buttons'] = "";
        //if($ref_table=="str_mng"){  //todo à corriger
        //	if($this->session->userdata('language_edit_mode')=='yes'){
        //		$data ['top_buttons'] .= get_top_button ( 'all', 'Close edition mode', 'config/update_edition_mode/no','Close edition mode','fa-ban','',' btn-warning ' );
        //	}else{
        //		$data ['top_buttons'] .= get_top_button ( 'all', 'Open edition mode', 'config/update_edition_mode/yes','Open edition mode','fa-check','',' btn-dark ' );
        //	}
        //}else{
        //	if($add_link)
        //		$data ['top_buttons'] .= get_top_button ( 'add', 'Add new', $add_link_url);
        //}
        //	
        if (!empty($ref_table_config['operations'][$ref_table_operation]['top_links']))
            $data['top_buttons'] = $this->create_top_buttons($ref_table_config['operations'][$ref_table_operation]['top_links']);
        /*
         * Titre de la page
         * Page title
         */
        if (isset($ref_table_config['operations'][$ref_table_operation]['page_title'])) {
            $data['page_title'] = lng($ref_table_config['operations'][$ref_table_operation]['page_title']);
        } else {
            $data['page_title'] = lng("List of " . $ref_table_config['entity_label']);
        }
        /*
                     * Configuration pour l'affichage des lien de navigation
                     * Configuration for displaying navigation links
                     */
        $data['valeur'] = ($val == "_") ? "" : $val;
        /*
         * Si on a besoin de faire urecherche sur la liste specifier la vue où se trouve le formulaire de recherche
         * If you need to search the list, specify the view where the search form is located
         */
        if (!$dynamic_table and !empty($ref_table_config['search_by'])) {
            $data['search_view'] = 'general/search_view';
        }
        /*
                     * La vue qui va s'afficher
                     * The view that will be displayed
                     */
        if (!$dynamic_table) {
            $data['nav_pre_link'] = 'element/entity_list/' . $operation_name . '/' . $val . '/';
            $data['nav_page_position'] = 5;
            $data['page'] = 'general/list';
            if (!empty($ref_table_config['operations'][$ref_table_operation]['page_template'])) {
                $data['page'] = $ref_table_config['operations'][$ref_table_operation]['page_template'];
            }
        } else {
            $data['page'] = 'general/list_dt';
        }
        if (admin_config($ref_table))
            $data['left_menu_admin'] = True;
        //print_test($data);
        /*
         * Chargement de la vue avec les données préparés dans le controleur
         * Loading the view with the data prepared in the controller
         */
        $this->load->view('shared/body', $data);
    }

    /*
     * Pour recuperer sous forme array le contenu d'une liste
     *
     * Input: $operation_name: l'operation concerner dans l'entity config
     * 			$val : valeur de recherche si une recherche a été faite sur la table en cours
     * 			$page: la page affiché : ulilisé dans la navigation
     */
    public function entity_list_data($operation_name, $val = "_", $page = 0, $dynamic_table = 1)
    {
        //Verification de l'operatoion pour recuperer la config correspondante	
        $op = check_operation($operation_name, 'List');
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        //Vérification si il y a une condition de recherche
        $val = urldecode(urldecode($val));
        $filter = array();
        if (isset($_POST['search_all'])) {
            $filter = $this->input->post();
            unset($filter['search_all']);
            $val = "_";
            if (isset($filter['valeur']) and !empty($filter['valeur'])) {
                $val = $filter['valeur'];
                $val = urlencode(urlencode($val));
            }
            /*
             * mis à jours de l'url en ajoutant la valeur recherché dans le lien puis rechargement de l'url
             */
            $url = "element/entity_list_data/" . $operation_name . "/" . $val . "/0/";
            redirect($url);
        }
        /*
         * Récupération de la configuration(structure) de la table à afficher
         */
        $ref_table_config = get_table_configuration($ref_table);
        $table_id = $ref_table_config['table_id'];
        //Affichage de tous les element
        $rec_per_page = -1;
        $ref_table_config['current_operation'] = $ref_table_operation;
        //récupertaion de la liste
        $data = $this->DBConnection_mdl->get_list_mdl($ref_table_config, $val, $page, $rec_per_page);
        /*
         * récupération des correspondances des clés externes pour l'affichage  suivant la structure de la table
         */
        $dropoboxes = array();
        foreach ($ref_table_config['operations'][$ref_table_operation]['fields'] as $k_field => $v) {
            if (!empty($ref_table_config['fields'][$k_field])) {
                $field_det = $ref_table_config['fields'][$k_field];
                if (!empty($field_det['input_type']) and $field_det['input_type'] == 'select') {
                    if ($field_det['input_select_source'] == 'array') {
                        $dropoboxes[$k_field] = $field_det['input_select_values'];
                    } elseif ($field_det['input_select_source'] == 'table') {
                        $dropoboxes[$k_field] =
                            $this->manager_lib->get_reference_select_values($field_det['input_select_values']);
                    } elseif ($field_det['input_select_source'] == 'yes_no') {
                        $dropoboxes[$k_field] = array(
                            '0' => "No",
                            '1' => "Yes"
                        );
                    }
                }
            }
        }
        /*
         * Préparation de la liste à afficher sur base du contenu et  stucture de la table
         */
        //list of the field to be displayed
        $field_list = array();
        //list of the label of field to be displayed
        $field_list_header = array();
        foreach ($ref_table_config['operations'][$ref_table_operation]['fields'] as $k => $v) {
            if (!empty($ref_table_config['fields'][$k])) {
                array_push($field_list, $k);
                $field_header = !empty($v['field_title']) ? $v['field_title'] : $ref_table_config['fields'][$k]['field_title'];
                array_push($field_list_header, $field_header);
            }
        }
        $i = 1;
        $list_to_display = array();
        foreach ($data['list'] as $key => $value) {
            $element_array = array();
            foreach ($field_list as $key_field => $v_field) {
                if (isset($value[$v_field])) {
                    if (isset($dropoboxes[$v_field][$value[$v_field]])) {
                        $element_array[$v_field] = $dropoboxes[$v_field][$value[$v_field]];
                    } elseif (empty($value[$v_field]) and empty($ref_table_config['fields'][$v_field]['display_null'])) {
                        $element_array[$v_field] = "";
                    } else {
                        $element_array[$v_field] = $value[$v_field];
                    }
                } else {
                    $element_array[$v_field] = "";
                    if (
                        (isset($ref_table_config['fields'][$v_field]['number_of_values'])
                            and $ref_table_config['fields'][$v_field]['number_of_values'] != 1)
                        or (isset($ref_table_config['fields'][$v_field]['category_type'])
                            and $ref_table_config['fields'][$v_field]['category_type'] == 'WithSubCategories')
                    ) { //recuperation pour les multivalues et les champs avec subcategory
                        if (
                            isset($ref_table_config['fields'][$v_field]['input_select_values'])
                            and isset($ref_table_config['fields'][$v_field]['input_select_key_field'])
                        ) {
                            // récuperations des valeurs de cet element
                            $M_values = $this->manager_lib->get_element_multi_values($ref_table_config['fields'][$v_field]['input_select_values'], $ref_table_config['fields'][$v_field]['input_select_key_field'], $data['list'][$key][$table_id]);
                            $S_values = "";
                            $Array_values = array();
                            foreach ($M_values as $k_m => $v_m) {
                                if (isset($dropoboxes[$v_field][$v_m])) {
                                    $M_values[$k_m] = $dropoboxes[$v_field][$v_m];
                                }
                                $S_values .= empty($S_values) ? $M_values[$k_m] : " | " . $M_values[$k_m];
                                array_push($Array_values, $M_values[$k_m]);
                            }
                            $element_array[$v_field] = $Array_values;
                        }
                    }
                }
            }
            if (isset($element_array[$table_id])) {
                $element_array[$table_id] = $i + $page;
            }
            array_push($list_to_display, $element_array);
            $i++;
        }
        array_unshift($list_to_display, $field_list_header);
        print_test($list_to_display);
    }

    //gets data from the database, generates graphs based on the specified configuration, and displays the list and graph in a view
    public function entity_list_graph_saved($operation_name, $val = "_", $page = 0, $graph = 'all')
    {
        //$graph='all';
        $dynamic_table = 1;
        $op = check_operation($operation_name, 'List');
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        /*
         * Vérification si il y a une condition de recherche
         */
        $val = urldecode(urldecode($val));
        $filter = array();
        if (isset($_POST['search_all'])) {
            $filter = $this->input->post();
            unset($filter['search_all']);
            $val = "_";
            if (isset($filter['valeur']) and !empty($filter['valeur'])) {
                $val = $filter['valeur'];
                $val = urlencode(urlencode($val));
            }
            /*
             * mis à jours de l'url en ajoutant la valeur recherché dans le lien puis rechargement de l'url
             */
            $url = "element/entity_list/" . $operation_name . "/" . $val . "/0/" . $graph;
            redirect($url);
        }
        /*
         * Récupération de la configuration(structure) de la table à afficher
         */
        $ref_table_config = get_table_configuration($ref_table);
        //print_test($ref_table_config['report']);
        $table_id = $ref_table_config['table_id'];
        //	exit;
        /*
         * Appel du model pour récupérer la liste à aficher dans la Base de donnés
         */
        $rec_per_page = ($dynamic_table) ? -1 : 0;
        $ref_table_config['current_operation'] = $ref_table_operation;
        $data = $this->DBConnection_mdl->get_list_mdl($ref_table_config, $val, $page, $rec_per_page);
        //print_test($data);
        /*
         * récupération des correspondances des clés externes pour l'affichage  suivant la structure de la table
         */
        $graph_config = array(
            '9' => array(
                'type' => 'compare',
                'title' => 'Domain per year',
                'values' => array(
                    'field' => 'domain',
                    'style' => 'select',
                    'input_select_values' => 'ref_domain;ref_value',
                    'operation' => 'count'
                ),
                'reference' => array(
                    'field' => 'year',
                    'style' => 'free',
                    'operation' => 'count'
                ),
            ),
            '0' => array(
                'type' => 'simple',
                'title' => 'Source language',
                'values' => array(
                    'field' => 'source_language',
                    'style' => 'select',
                ),
            ),
            '7' => array(
                'type' => 'simple',
                'title' => 'Year',
                'values' => array(
                    'field' => 'year',
                    'style' => 'free',
                    'operation' => 'count'
                ),
            ),
            '8' => array(
                'type' => 'simple',
                'title' => 'Domain',
                'values' => array(
                    'field' => 'domain',
                    'style' => 'select',
                    'input_select_values' => 'ref_domain;ref_value',
                    'operation' => 'count'
                ),
            ),
            '2' => array(
                'type' => 'simple',
                'title' => 'Transformation language',
                'values' => array(
                    'field' => 'trans_language',
                    'style' => 'select',
                    'input_select_values' => 'ref_transformation_language;ref_value',
                    'input_select_values_multi' => 'trans_language;trans_language',
                    'operation' => 'count'
                ),
            ),
            '19' => array(
                'type' => 'compare',
                'title' => 'Transformation language',
                'values' => array(
                    'field' => 'trans_language',
                    'style' => 'select',
                    'input_select_values' => 'ref_transformation_language;ref_value',
                    'input_select_values_multi' => 'trans_language;trans_language',
                    'operation' => 'count'
                ),
                'reference' => array(
                    'field' => 'year',
                    'style' => 'free',
                    'operation' => 'count'
                ),
            ),
        );
        $graph_config = array(
            '9' => array(
                'type' => 'compare',
                'title' => 'Domain per year',
                'id' => 'domain_year',
                'values' => array(
                    'field' => 'domain',
                    'style' => 'select',
                    'input_select_values' => 'ref_domain;ref_value',
                    'title' => 'Domain',
                    'operation' => 'count'
                ),
                'reference' => array(
                    'field' => 'year',
                    'style' => 'free',
                    'title' => 'Year',
                    'operation' => 'count'
                ),
                'referencea' => array(
                    'field' => 'source_language',
                    'style' => 'free',
                    'title' => 'Source lang',
                    'style' => 'select',
                ),
            ),
            '2' => array(
                'type' => 'simple',
                'id' => 'graph_trans',
                'title' => 'Transformation language',
                'link' => False,
                'values' => array(
                    'field' => 'trans_language',
                    'style' => 'select',
                    'input_select_values' => 'ref_transformation_language;ref_value',
                    'input_select_values_multi' => 'trans_language;trans_language',
                    'operation' => 'count'
                ),
            ),
            '7' => array(
                'type' => 'simple',
                'id' => 'graph_year',
                'title' => 'Year',
                'link' => True,
                'values' => array(
                    'field' => 'year',
                    'style' => 'free',
                    'operation' => 'count'
                ),
            ),
            '8' => array(
                'type' => 'simple',
                'id' => 'graph_domain',
                'title' => 'Domain',
                'link' => True,
                'values' => array(
                    'field' => 'domain',
                    'style' => 'select',
                    'input_select_values' => 'ref_domain;ref_value',
                    'operation' => 'count'
                ),
            ),
            '12' => array(
                'type' => 'simple',
                'id' => 'graph_source_lang',
                'link' => True,
                'title' => 'Source language',
                'values' => array(
                    'field' => 'source_language',
                    'style' => 'select',
                ),
            ),
        );
        if (!empty($ref_table_config['report'])) {
            $graph_config = $ref_table_config['report'];
            //	$graph_config['trans_lang']['values']['input_select_values_multi']='intent;intent';
            //print_test($graph_config);
            $fields_list_graph = array();
            $fields_list_graph_all = array();
            foreach ($graph_config as $key => $value) {
                if (!empty($value['values']['field']) and $value['values']['style'] == 'select') {
                    $fields_list_graph[$value['values']['field']] = $value['values'];
                }
                $fields_list_graph_all[$value['values']['field']] = $value['values'];
                if (!empty($value['reference']['field']) and $value['reference']['style'] == 'select') {
                    $fields_list_graph[$value['reference']['field']] = $value['reference'];
                }
                if (!empty($value['reference']['field'])) {
                    $fields_list_graph_all[$value['reference']['field']] = $value['reference'];
                }
            }
            //$fields_list=array('domain'=>'domain','source_language'=>'source_language');
            //print_test($fields_list_graph);
            $dropoboxes = array();
            foreach ($fields_list_graph as $k_field => $v) {
                //foreach ($ref_table_config['operations'][$ref_table_operation]['fields'] as $k_field => $v) {
                if (!empty($ref_table_config['fields'][$k_field])) {
                    $field_det = $ref_table_config['fields'][$k_field];
                    if (!empty($field_det['input_type']) and $field_det['input_type'] == 'select') {
                        if ($field_det['input_select_source'] == 'array') {
                            //print_test($v);
                            $dropoboxes[$k_field] = $field_det['input_select_values'];
                        } elseif ($field_det['input_select_source'] == 'table') {
                            $input_select_values = !empty($v['input_select_values']) ? $v['input_select_values'] : $field_det['input_select_values'];
                            $dropoboxes[$k_field] = $this->manager_lib->get_reference_select_values($input_select_values, False);
                            //	print_test($v);
                        } elseif ($field_det['input_select_source'] == 'yes_no') {
                            $dropoboxes[$k_field] = array(
                                '0' => "No",
                                '1' => "Yes"
                            );
                        }
                    }
                }
            }
            //	print_test($dropoboxes);
            /*
             * Préparation de la liste à afficher sur base du contenu et  stucture de la table
             */
            /**
             * @var array $field_list va contenir les champs à afficher
             */
            $field_list = array();
            $field_list_header = array();
            foreach ($ref_table_config['operations'][$ref_table_operation]['fields'] as $k => $v) {
                if (!empty($ref_table_config['fields'][$k])) {
                    array_push($field_list, $k);
                    $field_header = !empty($v['field_title']) ? $v['field_title'] : $ref_table_config['fields'][$k]['field_title'];
                    array_push($field_list_header, $field_header);
                }
            }
            //print_test($link_field_list);
            $i = 1;
            $list_to_display = array();
            $T_result = array();
            foreach ($data['list'] as $key => $value) {
                //Current_problem
                foreach ($graph_config as $key_graph => $value_graph) {
                    $title_graph = $value_graph['id'];
                    if ($value_graph['type'] == 'simple') {
                        $v_field = $value_graph['values']['field'];
                        if (isset($value[$v_field])) {
                            $T_result[$title_graph][$value[$v_field]] = !empty($T_result[$title_graph][$value[$v_field]]) ? $T_result[$title_graph][$value[$v_field]] + 1 : 1;
                        } else {
                            $element_array[$v_field] = "";
                            if (
                                (isset($ref_table_config['fields'][$v_field]['number_of_values']) and $ref_table_config['fields'][$v_field]['number_of_values'] != 1) or
                                (isset($ref_table_config['fields'][$v_field]['category_type']) and $ref_table_config['fields'][$v_field]['category_type'] == 'WithSubCategories')
                            ) { //recuperation pour les multivalues et les champs avec subcategory
                                //print_test($key_graph);
                                $graph_config[$key_graph]['multi_value'] = True;
                                if (isset($ref_table_config['fields'][$v_field]['input_select_values']) and isset($ref_table_config['fields'][$v_field]['input_select_key_field'])) {
                                    if (!empty($value_graph['values']['input_select_values_multi'])) {
                                        $input_select_values_multi = $value_graph['values']['input_select_values_multi'];
                                    } else {
                                        $input_select_values_multi = $ref_table_config['fields'][$v_field]['input_select_values'];
                                    }
                                    $M_values = $this->manager_lib->get_element_multi_values($input_select_values_multi, $ref_table_config['fields'][$v_field]['input_select_key_field'], $data['list'][$key][$table_id], 1);
                                    foreach ($M_values as $key_M => $value_M) {
                                        $T_result[$title_graph][$value_M] = !empty($T_result[$title_graph][$value_M]) ? $T_result[$title_graph][$value_M] + 1 : 1;
                                    }
                                }
                            }
                        }
                    } elseif ($value_graph['type'] == 'compare') { // for compare the referemce must be single value
                        $v_field = $value_graph['values']['field'];
                        $ref_field = $value_graph['reference']['field'];
                        if (isset($value[$ref_field])) {
                            $reference_value = $value[$ref_field];
                            if (isset($value[$v_field])) {
                                $T_result[$title_graph][$reference_value][$value[$v_field]] = !empty($T_result[$title_graph][$reference_value][$value[$v_field]]) ? $T_result[$title_graph][$reference_value][$value[$v_field]] + 1 : 1;
                            } else {
                                if (
                                    (isset($ref_table_config['fields'][$v_field]['number_of_values']) and $ref_table_config['fields'][$v_field]['number_of_values'] != 1) or
                                    (isset($ref_table_config['fields'][$v_field]['category_type']) and $ref_table_config['fields'][$v_field]['category_type'] == 'WithSubCategories')
                                ) { //recuperation pour les multivalues et les champs avec subcategory
                                    $graph_config[$key_graph]['multi_value'] = True;
                                    if (isset($ref_table_config['fields'][$v_field]['input_select_values']) and isset($ref_table_config['fields'][$v_field]['input_select_key_field'])) {
                                        if (!empty($value_graph['values']['input_select_values_multi'])) {
                                            $input_select_values_multi = $value_graph['values']['input_select_values_multi'];
                                        } else {
                                            $input_select_values_multi = $ref_table_config['fields'][$v_field]['input_select_values'];
                                        }
                                        $M_values = $this->manager_lib->get_element_multi_values($input_select_values_multi, $ref_table_config['fields'][$v_field]['input_select_key_field'], $data['list'][$key][$table_id], 1);
                                        //	$element_array[$v_field]=$M_values;
                                        foreach ($M_values as $key_M => $value_M) {
                                            $T_result[$reference_value][$value_M] = !empty($T_result[$reference_value][$value_M]) ? $T_result[$reference_value][$value_M] + 1 : 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //test display all elements
                $element_array = array();
                foreach ($fields_list_graph_all as $key_field => $zv_field) {
                    $v_field = $zv_field['field'];
                    //print_test($v_field);
                    if (isset($value[$v_field])) {
                        $element_array[$v_field] = $value[$v_field];
                    } else {
                        $element_array[$v_field] = "";
                        if (
                            (isset($ref_table_config['fields'][$v_field]['number_of_values']) and $ref_table_config['fields'][$v_field]['number_of_values'] != 1) or
                            (isset($ref_table_config['fields'][$v_field]['category_type']) and $ref_table_config['fields'][$v_field]['category_type'] == 'WithSubCategories')
                        ) { //recuperation pour les multivalues et les champs avec subcategory
                            if (isset($ref_table_config['fields'][$v_field]['input_select_values']) and isset($ref_table_config['fields'][$v_field]['input_select_key_field'])) {
                                if (!empty($zv_field['input_select_values_multi'])) {
                                    $input_select_values_multi = $zv_field['input_select_values_multi'];
                                } else {
                                    $input_select_values_multi = $ref_table_config['fields'][$v_field]['input_select_values'];
                                }
                                $M_values = $this->manager_lib->get_element_multi_values($input_select_values_multi, $ref_table_config['fields'][$v_field]['input_select_key_field'], $data['list'][$key][$table_id], 1);
                                $element_array[$v_field] = $M_values;
                            }
                        }
                    }
                }
                array_push($list_to_display, $element_array);
                $i++;
            }
            //print_test($T_result);	
            //Clean graph
            //exit;
            $result = array();
            $Tzresult = array();
            //print_test($graph_config);
            foreach ($graph_config as $key => $value) {
                $value['link'] = !empty($value['multi_value']) ? False : True;
                //	print_test($value);
                if (!empty($T_result[$value['id']])) {
                    if ($value['type'] == 'simple') {
                        $data = array();
                        //ksort($T_result[$value['id']]);
                        ksort($T_result[$value['id']]);
                        foreach ($T_result[$value['id']] as $k => $v_data) {
                            $data[$k]['field'] = $k;
                            $data[$k]['nombre'] = $v_data;
                            $data[$k]['title'] = !empty($dropoboxes[$value['values']['field']][$k]) ? $dropoboxes[$value['values']['field']][$k] : $k;
                        }
                        $result[$value['id']] = array(
                            'id' => $value['id'],
                            'title' => $value['title'],
                            'link' => !empty($value['link']) ? True : False,
                            'type' => $value['type'],
                            //'type'=>'zzzz',	
                            'field' => $value['values']['field'],
                            'data' => $data,
                            'chart' => $value['chart'],
                        );
                    } else {
                        ksort($T_result[$value['id']]);
                        //print_test($T_result[$value['id']]);
                        $p_data = array();
                        foreach ($T_result[$value['id']] as $k => $v_data) {
                            ksort($v_data);
                            $data_n = array();
                            foreach ($v_data as $kn => $vn_data) {
                                $data_n[$kn]['field'] = $kn;
                                $data_n[$kn]['nombre'] = $vn_data;
                                $data_n[$kn]['title'] = !empty($dropoboxes[$value['values']['field']][$kn]) ? $dropoboxes[$value['values']['field']][$kn] : $k;
                            }
                            $p_data[$k]['field'] = $k;
                            $p_data[$k]['title'] = !empty($dropoboxes[$value['reference']['field']][$k]) ? $dropoboxes[$value['reference']['field']][$k] : $k;
                            $p_data[$k]['data'] = $data_n;
                        }
                        $result[$value['id']] = array(
                            'id' => $value['id'],
                            'title' => $value['title'],
                            'link' => !empty($value['link']) ? True : False,
                            'reference_title' => $value['reference']['title'],
                            'values_title' => $value['values']['title'],
                            'type' => $value['type'],
                            //'type'=>'zzzz',
                            'field' => $value['values']['field'],
                            'p_data' => $p_data,
                            'chart' => $value['chart'],
                        );
                        //print_test($result[$value['id']]);
                    }
                }
            }
            //	print_test($result);
            //exit;
        } else {
            $result = array();
        }
        $data['graph_result'] = $result;
        // print_test($result);
        /*
         * Ajout de l'entête de la liste
         */
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = "";
        if (!empty($ref_table_config['operations'][$ref_table_operation]['top_links']))
            $data['top_buttons'] = $this->create_top_buttons($ref_table_config['operations'][$ref_table_operation]['top_links']);
        /*
         * Titre de la page
         */
        $data['page_title'] = lng('Result');
        /*
         * Configuration pour l'affichage des lien de navigation
         */
        $data['valeur'] = ($val == "_") ? "" : $val;
        /*
         * La vue qui va s'afficher
         */
        $data['has_graph'] = 'yes';
        if (!empty($val) and $val == 'line') {
            $data['page'] = 'reporting/result_list_graph_line';
        } else {
            $data['page'] = 'reporting/result_list_graph';
        }
        $this->load->view('shared/body', $data);
    }

    //fonction pour afficher un graphique à partir de la liste- specialisé pour la classifcation
    //function to display a graph from the list - specialized for classifcation
    public function entity_list_graph($operation_name, $val = "_", $page = 0, $graph = 'all')
    {
        $dynamic_table = 1; //permet de recuperer toute la liste pas de filtre
        //récupertaion de la configuration correspondant à l'opération
        $op = check_operation($operation_name, 'List');
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        /*
         * Vérification si il y a une condition de recherche pour redidiger la page 
         * et appliquer la condition de recherche
         * Utilisable pour les tableux nob dynamique
         */
        $val = urldecode(urldecode($val));
        $filter = array();
        if (isset($_POST['search_all'])) {
            $filter = $this->input->post();
            unset($filter['search_all']);
            $val = "_";
            if (isset($filter['valeur']) and !empty($filter['valeur'])) {
                $val = $filter['valeur'];
                $val = urlencode(urlencode($val));
            }
            /*
             * mis à jours de l'url en ajoutant la valeur recherché dans le lien puis rechargement de l'url
             */
            $url = "element/entity_list/" . $operation_name . "/" . $val . "/0/" . $graph;
            redirect($url);
        }
        /*
         * Récupération de la configuration(structure) de la table à afficher
         */
        $ref_table_config = get_table_configuration($ref_table);
        $table_id = $ref_table_config['table_id'];
        /*
         * Appel du model pour récupérer la liste à aficher dans la Base de donnés
         */
        $rec_per_page = ($dynamic_table) ? -1 : 0;
        $ref_table_config['current_operation'] = $ref_table_operation;
        $data = $this->DBConnection_mdl->get_list_mdl($ref_table_config, $val, $page, $rec_per_page);
        /*
         * verification si il y a des rapport associés à l'entités
         * Si oui il faut les generer
         */
        if (!empty($ref_table_config['report'])) {
            //configuration des rapport
            $graph_config = $ref_table_config['report'];
            //les champs dans le rapports qui prennent des valeurs dans le tables de references
            $fields_list_graph = array();
            //tous les champs dans le rapport
            $fields_list_graph_all = array();
            //recuperation des champs du rapports
            foreach ($graph_config as $key => $value) {
                if (!empty($value['values']['field']) and $value['values']['style'] == 'select') {
                    $fields_list_graph[$value['values']['field']] = $value['values'];
                }
                //	$fields_list_graph_all[$value['values']['field']]=$value['values'];
                if (!empty($value['reference']['field']) and $value['reference']['style'] == 'select') {
                    $fields_list_graph[$value['reference']['field']] = $value['reference'];
                }
                if (!empty($value['reference']['field'])) {
                    //	$fields_list_graph_all[$value['reference']['field']]=$value['reference'];
                }
            }
            //recuperation des valeurs des tables de réferences
            $dropoboxes = array();
            foreach ($fields_list_graph as $k_field => $v) {
                if (!empty($ref_table_config['fields'][$k_field])) {
                    $field_det = $ref_table_config['fields'][$k_field];
                    if (!empty($field_det['input_type']) and $field_det['input_type'] == 'select') {
                        if ($field_det['input_select_source'] == 'array') {
                            $dropoboxes[$k_field] = $field_det['input_select_values'];
                        } elseif ($field_det['input_select_source'] == 'table') {
                            $input_select_values = !empty($v['input_select_values']) ? $v['input_select_values'] : $field_det['input_select_values'];
                            $dropoboxes[$k_field] = $this->manager_lib->get_reference_select_values($input_select_values, False);
                        } elseif ($field_det['input_select_source'] == 'yes_no') {
                            $dropoboxes[$k_field] = array(
                                '0' => "No",
                                '1' => "Yes"
                            );
                        }
                    }
                }
            }
            //add extra drop boxes
            //synthese des valeurs de la liste suivant le rapport à faire
            $i = 1;
            $T_result = array();
            foreach ($data['list'] as $key => $value) {
                foreach ($graph_config as $key_graph => $value_graph) {
                    $title_graph = $value_graph['id'];
                    if ($value_graph['type'] == 'simple') {
                        $v_field = $value_graph['values']['field'];
                        if (isset($value[$v_field])) {
                            $T_result[$title_graph][$value[$v_field]] = !empty($T_result[$title_graph][$value[$v_field]]) ? $T_result[$title_graph][$value[$v_field]] + 1 : 1;
                        } else {
                            $element_array[$v_field] = "";
                            if (
                                (isset($ref_table_config['fields'][$v_field]['number_of_values']) and $ref_table_config['fields'][$v_field]['number_of_values'] != 1) or
                                (isset($ref_table_config['fields'][$v_field]['category_type']) and $ref_table_config['fields'][$v_field]['category_type'] == 'WithSubCategories')
                            ) { //recuperation pour les multivalues et les champs avec subcategory
                                $graph_config[$key_graph]['multi_value'] = True;
                                if (isset($ref_table_config['fields'][$v_field]['input_select_values']) and isset($ref_table_config['fields'][$v_field]['input_select_key_field'])) {
                                    if (!empty($value_graph['values']['input_select_values_multi'])) {
                                        $input_select_values_multi = $value_graph['values']['input_select_values_multi'];
                                    } else {
                                        $input_select_values_multi = $ref_table_config['fields'][$v_field]['input_select_values'];
                                    }
                                    $M_values = $this->manager_lib->get_element_multi_values($input_select_values_multi, $ref_table_config['fields'][$v_field]['input_select_key_field'], $data['list'][$key][$table_id], 1);
                                    foreach ($M_values as $key_M => $value_M) {
                                        $T_result[$title_graph][$value_M] = !empty($T_result[$title_graph][$value_M]) ? $T_result[$title_graph][$value_M] + 1 : 1;
                                    }
                                }
                            }
                        }
                    } elseif ($value_graph['type'] == 'compare') { // for compare the referemce must be single value
                        $v_field = $value_graph['values']['field'];
                        $ref_field = $value_graph['reference']['field'];
                        if (isset($value[$ref_field])) {
                            $reference_value = $value[$ref_field];
                            if (isset($value[$v_field])) {
                                $T_result[$title_graph][$reference_value][$value[$v_field]] = !empty($T_result[$title_graph][$reference_value][$value[$v_field]]) ? $T_result[$title_graph][$reference_value][$value[$v_field]] + 1 : 1;
                            } else {
                                if (
                                    (isset($ref_table_config['fields'][$v_field]['number_of_values']) and $ref_table_config['fields'][$v_field]['number_of_values'] != 1) or
                                    (isset($ref_table_config['fields'][$v_field]['category_type']) and $ref_table_config['fields'][$v_field]['category_type'] == 'WithSubCategories')
                                ) { //recuperation pour les multivalues et les champs avec subcategory
                                    $graph_config[$key_graph]['multi_value'] = True;
                                    if (isset($ref_table_config['fields'][$v_field]['input_select_values']) and isset($ref_table_config['fields'][$v_field]['input_select_key_field'])) {
                                        if (!empty($value_graph['values']['input_select_values_multi'])) {
                                            $input_select_values_multi = $value_graph['values']['input_select_values_multi'];
                                        } else {
                                            $input_select_values_multi = $ref_table_config['fields'][$v_field]['input_select_values'];
                                        }
                                        $M_values = $this->manager_lib->get_element_multi_values($input_select_values_multi, $ref_table_config['fields'][$v_field]['input_select_key_field'], $data['list'][$key][$table_id], 1);
                                        foreach ($M_values as $key_M => $value_M) {
                                            $T_result[$reference_value][$value_M] = !empty($T_result[$reference_value][$value_M]) ? $T_result[$reference_value][$value_M] + 1 : 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $i++;
            }
            //print_test($T_result);	
            $result = array();
            $Tzresult = array();
            foreach ($graph_config as $key => $value) {
                $value['link'] = !empty($value['multi_value']) ? False : True;
                if (!empty($T_result[$value['id']])) {
                    if ($value['type'] == 'simple') {
                        $data = array();
                        ksort($T_result[$value['id']]);
                        foreach ($T_result[$value['id']] as $k => $v_data) {
                            $data[$k]['field'] = $k;
                            $data[$k]['nombre'] = $v_data;
                            $data[$k]['title'] = !empty($dropoboxes[$value['values']['field']][$k]) ? $dropoboxes[$value['values']['field']][$k] : $k;
                        }
                        $result[$value['id']] = array(
                            'id' => $value['id'],
                            'title' => $value['title'],
                            'link' => !empty($value['link']) ? True : False,
                            'type' => $value['type'],
                            'field' => $value['values']['field'],
                            'data' => $data,
                            'chart' => $value['chart'],
                        );
                    } else {
                        ksort($T_result[$value['id']]);
                        $p_data = array();
                        foreach ($T_result[$value['id']] as $k => $v_data) {
                            ksort($v_data);
                            $data_n = array();
                            foreach ($v_data as $kn => $vn_data) {
                                $data_n[$kn]['field'] = $kn;
                                $data_n[$kn]['nombre'] = $vn_data;
                                $data_n[$kn]['title'] = !empty($dropoboxes[$value['values']['field']][$kn]) ? $dropoboxes[$value['values']['field']][$kn] : $k;
                            }
                            $p_data[$k]['field'] = $k;
                            $p_data[$k]['title'] = !empty($dropoboxes[$value['reference']['field']][$k]) ? $dropoboxes[$value['reference']['field']][$k] : $k;
                            $p_data[$k]['data'] = $data_n;
                        }
                        $result[$value['id']] = array(
                            'id' => $value['id'],
                            'title' => $value['title'],
                            'link' => !empty($value['link']) ? True : False,
                            'reference_title' => $value['reference']['title'],
                            'values_title' => $value['values']['title'],
                            'type' => $value['type'],
                            'field' => $value['values']['field'],
                            'p_data' => $p_data,
                            'chart' => $value['chart']
                        );
                    }
                }
            }
        } else {
            $result = array();
        }
        $data['graph_result'] = $result;
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = "";
        if (!empty($ref_table_config['operations'][$ref_table_operation]['top_links']))
            $data['top_buttons'] = $this->create_top_buttons($ref_table_config['operations'][$ref_table_operation]['top_links']);
        /*
         * Titre de la page
         */
        $data['page_title'] = lng('Result');
        /*
         * Configuration pour l'affichage des lien de navigation
         */
        $data['valeur'] = ($val == "_") ? "" : $val;
        /*
         * La vue qui va s'afficher
         */
        $data['has_graph'] = 'yes';
        if (!empty($val) and $val == 'line') {
            $data['page'] = 'reporting/result_list_graph_line';
        } else {
            $data['page'] = 'reporting/result_list_graph';
        }
        $this->load->view('shared/body', $data);
    }

    /*
     * Fonction  pour afficher la page avec un formulaire un élément enfant
     * Function to display the page with a form a child element
     *
     * Input: 	$ref_table:le nom de la structure de l'élément enfant
     * 			$ref_table_parent:le nom de la structure de l'élément parent
     * 			$parent_field: le champs qui va prendre la clé de l'element enfant
     *			$parent_id: l'id de l'élément parent
     *			$ref_id: id de l'element à modifier
     * 			$display_type: indique comment le formulaire va être afficher normal ou modal(pop- up)
     */
    //	public function edit_drilldown($ref_table,$ref_table_parent,$parent_field,$parent_id,$ref_id,$display_type="normal") {
    public function edit_drilldown($operation_name, $ref_id, $parent_id, $display_type = "normal")
    {
        $op = check_operation($operation_name, 'EditChild');
        $ref_table = $op['tab_ref'];
        $ref_table_operation = $op['operation_id'];
        $this->session->set_userdata('submit_mode', $display_type);
        /*
         * Récupération de la configuration(structure) de la table de l'element
         */
        $table_config = get_table_configuration($ref_table);
        /*
         * Appel de la fonction du model pour récupérer la ligne à modifier
         */
        //	$data ['content_item'] = $this->DBConnection_mdl->get_row_details($ref_table,$ref_id);
        $data['content_item'] = $this->DBConnection_mdl->get_row_details($table_config['operations'][$ref_table_operation]['data_source'], $ref_id, true);
        if (!empty($table_config['operations'][$ref_table_operation]['support_drilldown'])) {
            $table_config['current_operation'] = $table_config['operations'][$ref_table_operation]['drilldown_source'];
            $item_data = $this->manager_lib->get_detail($table_config, $ref_id, True, True);
            //print_test($item_data);
            foreach ($item_data as $key => $value) {
                if (
                    !empty($table_config['operations'][$ref_table_operation]['fields'][$value['field_id']]) and
                    $table_config['operations'][$ref_table_operation]['fields'][$value['field_id']]['field_state'] == 'drill_down'
                ) {
                    $data['drill_down_values'][$value['field_id']] = $value['val2'];
                }
            }
        }
        /*
         * Récuperation des valeurs pour les champs multi-select
         */
        foreach ($table_config['operations'][$ref_table_operation]['fields'] as $key => $v_field) {
            $v = $table_config['fields'][$key];
            if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['input_select_source'] == 'table') {
                if (!empty($v['multi-select']) and $v['multi-select'] == 'Yes') {
                    $Tvalues_source = explode(';', $v['input_select_values']);
                    $source_table_config = get_table_configuration($Tvalues_source[0]);
                    $input_select_key_field = $v['input_select_key_field'];
                    $input_child_field = $Tvalues_source[1];
                    $extra_condition = " AND $input_select_key_field ='" . $ref_id . "'";
                    $res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $input_child_field, $extra_condition);
                    $data['content_item'][$key] = array();
                    foreach ($res_values as $key_r => $value_r) {
                        array_push($data['content_item'][$key], $value_r['refDesc']);
                    }
                }
            }
        }
        $data['current_element'] = $ref_id;
        /*
         * Appel de la fonction d'affichage du formulaire
         */
        $this->add_element_drilldown($operation_name, $parent_id, $data, 'edit', $display_type, 'EditChild');
    }

    //retrieves multi-values associated with an element from a table based on the provided configuration, key field, and element ID
    private function zget_element_multi_values($config, $key_field, $element_id)
    {
        $Tvalues_source = explode(';', $config);
        $source_table_config = get_table_config($Tvalues_source[0]);
        $extra_condition = " AND $key_field ='" . $element_id . "'";
        $res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $source_table_config['table_id'], $extra_condition);
        $results = array();
        foreach ($res_values as $value) {
            array_push($results, $value['refDesc']);
        }
        //print_test($results);
        return $results;
    }

    /*
     * Fonction pour afficher une ligne d'une table avec remplacement des clès externes par leurs correspondances
     */
    private function zget_element_detail($ref_table, $ref_id, $editable = True, $modal_link = False)
    {
        //récuperation de la configuration de l'entité
        $table_config = get_table_config($ref_table);
        //	print_test($table_config);
        $dropoboxes = array();
        // récupération des valeurs pour les champs avec la clé enregistre dans la table (pour pouvoir afficher le label)
        foreach ($table_config['fields'] as $k => $v) {
            if (!empty($v['input_type']) and $v['input_type'] == 'select' and !(isset($v['on_view']) and $v['on_view'] == 'hidden')) {
                if ($v['input_select_source'] == 'array') {
                    $dropoboxes[$k] = $v['input_select_values'];
                } elseif ($v['input_select_source'] == 'yes_no') {
                    $dropoboxes[$k] = array(
                        0 => 'No',
                        1 => 'Yes'
                    );
                } elseif ($v['input_select_source'] == 'table') {
                    // recherches des auteurs par papier
                    if ($ref_table == 'papers' and $k == 'authors') {
                        $this->db3 = $this->load->database(project_db(), TRUE);
                        //todo generaliser pour tout les multivalues (car les autres prennemt beaucoups de temps)
                        $sql = "select P.paperauthor_id ,A.author_name from paperauthor P,author A where P.paperId=$ref_id AND P.authorId=A.author_id AND A.author_active=1 AND P.paperauthor_active=1 ";
                        $res_author = $this->db3->query($sql)->result_array();
                        $t_array = array('' => 'Select ...');
                        //print_test($res_author);
                        foreach ($res_author as $key_a => $value_a) {
                            $t_array[$value_a['paperauthor_id']] = $value_a['author_name'];
                        }
                        //print_test($t_array);
                        $dropoboxes[$k] = $t_array;
                    } else {
                        $dropoboxes[$k] = $this->manager_lib->get_reference_select_values($v['input_select_values']);
                    }
                }
            }
        }
        //$detail_result = $this->manage_mdl->get_reference_details ( $table_config['table_name'],$table_config['table_id'],$ref_id );
        $detail_result = $this->DBConnection_mdl->get_row_details($ref_table, $ref_id);
        $content_item = $detail_result;
        $item_data = array();
        foreach ($dropoboxes as $k => $v) {
            $content_item[$k . '_idd'] = 0;
            if (isset($content_item[$k])) {
                if (isset($v[$content_item[$k]])) {
                    $content_item[$k . '_idd'] = $content_item[$k];
                    $content_item[$k] = $v[$content_item[$k]];
                }
            } else {
                $content_item[$k] = "";
            }
        }
        //print_test($content_item);
        foreach ($table_config['fields'] as $key => $value) {
            $array = array();
            //print_test($value);
            if (!(isset($value['on_view']) and $value['on_view'] == 'hidden')) {
                $array['title'] = $value['field_title'];
                $array['edit'] = 0;
                //for multi values
                if (isset($value['number_of_values']) and ($value['number_of_values'] == '*' or $value['number_of_values'] != '1') and !empty($value['input_select_key_field'])) {
                    $Tvalues_source = explode(';', $value['input_select_values']);
                    //echo "<h1>".$Tvalues_source[0]."<h1>";
                    $source_table_config = get_table_config($Tvalues_source[0]);
                    $input_select_key_field = $value['input_select_key_field'];
                    $extra_condition = " AND $input_select_key_field ='" . $ref_id . "'";
                    $res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $input_select_key_field, $extra_condition);
                    // set add button
                    $add_button = create_button_link('manage/add_element_child/' . $Tvalues_source[0] . '/' . $value['input_select_key_field'] . '/' . $ref_table . '/' . $ref_id, '<i class="fa fa-plus"></i> Add', "btn-success", 'Add ');
                    if ($ref_table == 'classification') { //use modal for classification
                        $modal_title = "Add : " . $value['field_title'];
                        $add_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="element/add_element_child_modal/' . $Tvalues_source[0] . '/' . $value['input_select_key_field'] . '/' . $ref_table . '/' . $ref_id . '"  data-modal_title="' . $modal_title . '" ><i class="fa fa-plus"></i>Add</a>';
                    }
                    $k_row = 0;
                    if (!(isset($value['multi-select']) and $value['multi-select'] == "Yes") and $editable) {
                        $array['val2'][0] = "<span> " . $add_button . "</span>";
                        $k_row = 1;
                        $array['edit'] = 1;
                    }
                    // Get values if label  are from other tables 
                    foreach ($res_values as $key_v => $value_v) {
                        if (isset($dropoboxes[$key][$value_v['refId']]))
                            $array['val2'][$k_row] = $dropoboxes[$key][$value_v['refId']];
                        if (isset($value_v['refId']) and $value['input_select_source'] == 'table' and isset($value['input_select_source_type'])) {
                            //print_test($value);
                            $Tconfig = explode(';', $value['input_select_values']);
                            //echo $content_item[$key.'_idd'];
                            if ($value_v['refId'] != 0) {
                                $edit_button = create_button_link('manage/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $value_v['refId'], '<i class="fa fa-pencil"></i> Edit', "btn-info", 'Edit ');
                                $delete_button = create_button_link('manage/remove_drilldown/' . $value_v['refId'] . '/' . $Tconfig[0] . '/' . $ref_table . '/' . $ref_id . '/' . $key . '/no', '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');
                                if ($ref_table == 'classification') { //use modal for classification
                                    $modal_title = "Edit : " . $value['field_title'];
                                    $edit_button = '<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manage/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $value_v['refId'] . '/modal"  data-modal_title="' . $modal_title . '" ><i class="fa fa-pencil"></i>Edit</a>';
                                    $delete_button = create_button_link('manage/remove_drilldown/' . $value_v['refId'] . '/' . $Tconfig[0] . '/' . $ref_table . '/' . $ref_id . '/' . $key . '/no/yes', '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');
                                }
                                if ((isset($value['multi-select']) and $value['multi-select'] == "Yes") or !$editable) {
                                    $edit_button = "";
                                    $delete_button = "";
                                }
                                if (isset($value['input_select_source_type']) and $value['input_select_source_type'] == 'drill_down')
                                    $array['val2'][$k_row] = "<span class='drilldown_link'>" . anchor('manage/view_ref/' . $Tconfig[0] . '/' . $value_v['refId'], $array['val2'][$k_row]) . "</span> <div class='navbar-right'>$edit_button $delete_button</div>";
                                else
                                    $array['val2'][$k_row] .= " <div class='navbar-right'>$edit_button $delete_button</div>";
                            }
                        }
                        $k_row++;
                    }
                } else {
                    $array['val'] = isset($content_item[$key]) ? " " . $content_item[$key] : ' ';
                    $array['val2'][0] = isset($content_item[$key]) ? " " . $content_item[$key] : ' ';
                    // for images
                    if (isset($value['input_type']) and $value['input_type'] == 'image') {
                        if (!empty($content_item[$key])) {
                            //$img=$this->config->item('image_upload_path').$content_item[$key]."_thumb.jpg";
                            //$array['val2'][0]= img($img);
                            $delete_picture_button = get_top_button('all', 'Remove picture', 'manager/remove_picture/' . $ref_table . '/' . $table_config['table_name'] . '/' . $table_config['table_id'] . '/' . $key . '/' . $ref_id, '', 'fa-close', '', 'btn-danger', FALSE);
                            //$array['val2'][0]= '<img src="data:image/png;base64,'.base64_encode( $content_item[$key]).'"/> '.$delete_picture_button;
                            $array['val2'][0] = '<img src="' . display_picture_from_db($content_item[$key]) . '"/> ' . $delete_picture_button;
                        }
                    }
                    ///echo $content_item[$key.'_idd'];
                    if (isset($content_item[$key . '_idd']) and $value['input_select_source'] == 'table' and isset($value['input_select_source_type']) and $value['input_select_source_type'] == 'drill_down') {
                        //print_test($value);
                        $Tconfig = explode(';', $value['input_select_values']);
                        //echo $content_item[$key.'_idd'];
                        if ($content_item[$key . '_idd'] != 0) {
                            if ((isset($value['drill_down_type']) and $value['drill_down_type'] == 'not_linked') or !$editable) {
                                $edit_button = "";
                                $delete_button = "";
                            } else {
                                $edit_button = create_button_link('manage/edit_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '/' . $content_item[$key . '_idd'], '<i class="fa fa-pencil"></i> Edit', "btn-info", 'Edit ');
                                $delete_button = create_button_link('manage/remove_drilldown/' . $content_item[$key . '_idd'] . '/' . $Tconfig[0] . '/' . $ref_table . '/' . $ref_id . '/' . $key, '<i class="fa fa-times"></i> Remove', "btn-danger", 'Remove ', 'onlist', 'alert_ok');
                            }
                            $array['val'] = "<span class='drilldown_link'>" . anchor('manage/view_ref/' . $Tconfig[0] . '/' . $content_item[$key . '_idd'], $array['val']) . "</span> <div class='navbar-right'>$edit_button.$delete_button</div>";
                            $array['val2'][0] = "<span class='drilldown_link'>" . anchor('manage/view_ref/' . $Tconfig[0] . '/' . $content_item[$key . '_idd'], $array['val2'][0]) . "</span> <div class='navbar-right'>$edit_button.$delete_button</div>";
                        } else {
                            if ((isset($value['drill_down_type']) and $value['drill_down_type'] == 'not_linked') or !$editable) {
                                $add_button = "";
                            } else {
                                $array['edit'] = 1;
                                $add_button = create_button_link('manage/add_ref_drilldown/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id, '<i class="fa fa-plus"></i> Add', "btn-success", 'Add ');
                                if ($ref_table == 'classification') { //use modal for classification
                                    $modal_title = "Add : " . $value['field_title'];
                                    $add_button = '<a  class="btn btn-xs btn-success" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manage/add_ref_drilldown_modal/' . $Tconfig[0] . '/' . $ref_table . '/' . $key . '/' . $ref_id . '"  data-modal_title="' . $modal_title . '" ><i class="fa fa-plus"></i>Add</a>';
                                }
                            }
                            $array['val'] = "<span>: " . $add_button . "</span>";
                            $array['val2'][0] = "<span>: " . $add_button . "</span>";
                        }
                    }
                }
                array_push($item_data, $array);
                //array_push($item_data2, $array);
            }
        }
        //print_test($item_data);
        return $item_data;
    }

    /*
        generates HTML code for a top buttons based on the provided configuration ($top_links) and the current element ID ($current_element).
    */
    private function create_top_buttons($top_links, $current_element = 0)
    {
        $project_published = project_published();
        //print_test($top_links);
        $top_buttons = "";
        foreach ($top_links as $key => $value) {
            if (!empty($value['url'])) {
                $icon = !empty($value['icon']) ? " fa-" . $value['icon'] . " " : "";
                if (in_array($key, array('add', 'edit', 'close', 'delete', 'back'))) {
                    $type = $key;
                } else {
                    $type = "all";
                }
                $title = !empty($value['title']) ? $value['title'] : "";
                $label = !empty($value['label']) ? $value['label'] : "";
                $icon = !empty($value['icon']) ? $value['icon'] : "";
                $url = !empty($value['url']) ? $value['url'] : "";
                if (!empty($current_element)) {
                    $url = str_replace('~current_element~', $current_element, $url);
                }
                if (!$project_published or (in_array($key, array('all_published', 'close', 'back')))) {
                    $top_buttons .= get_top_button($type, $title, $url, $label, $icon);
                }
            }
        }
        return $top_buttons;
    }

    /*
     * Fonction pour enregistrer les valeurs dans les champs multi-select
     */
    private function save_multi_select($multi_select_values, $parent_id)
    {
        foreach ($multi_select_values as $k => $v) {
            $new_values = $v['values'];
            $Tvalues_source = explode(';', $v['config']['input_select_values']);
            $child_tab_config = $Tvalues_source[0];
            $input_select_key_field = $v['config']['input_select_key_field'];
            $input_child_field = $Tvalues_source[1];
            $source_table_config = get_table_config($child_tab_config);
            $extra_condition = " AND $input_select_key_field ='" . $parent_id . "'";
            $res_values = $this->DBConnection_mdl->get_reference_select_values($source_table_config, $input_child_field, $extra_condition);
            //print_test($res_values);
            $old_values = array();
            $tobe_removed = array();
            foreach ($res_values as $key_r => $value_r) {
                array_push($old_values, $value_r['refDesc']);
                if (!in_array($value_r['refDesc'], $new_values)) {
                    array_push($tobe_removed, $value_r['refId']);
                }
            }
            $tobe_added = array_diff($new_values, $old_values);
            foreach ($tobe_removed as $k_rem => $v_rem) {
                $res = $this->DBConnection_mdl->remove_element($v_rem, $child_tab_config);
            }
            //adding new values
            foreach ($tobe_added as $k_add => $v_add) {
                $array_add = array();
                $array_add['operation_type'] = 'new';
                $array_add['table_config'] = $child_tab_config;
                $array_add[$input_select_key_field] = $parent_id;
                $array_add[$input_child_field] = $v_add;
                $saved_res = $this->DBConnection_mdl->save_reference($array_add, 'get_id');
                //print_test($array_add);
            }
        }
    }

    /*
        checks the existence of a record in a table, 
        count the matching records and returns the number of existing records
    */
    function check_record_exist($exist_config, $table_config, $post_array)
	{
		$table_active_field = $table_config['table_active_field'];
		$table_id = $table_config['table_id'];
		$table_name = $table_config['table_name'];
		$operation_type = $post_array['operation_type'];
		$table_config['table_active_field'];
		$sql = "select * from $table_name WHERE $table_active_field = 1 ";
		foreach ($exist_config['fields'] as $key => $value) {
			if (!empty($post_array[$value])) {
				$sql .= " AND ($value = ' " . $post_array[$value] . "') ";
			}
		}
		if ($operation_type == 'edit') {
			$sql .= " AND ( $table_id <>  '" . $post_array[$table_id] . "' ) ";
		}
		if (admin_config($table_config['config_id'])) {
			$res = $this->db->query($sql)->num_rows();
		} else {
			$res = $this->db_current->query($sql)->num_rows();
		}
		//echo $sql;
		//print_test($res);
		//print_test($post_array);
		//print_test($exist_config);
		return $res;
	}
}