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

class Paper extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->load->library('paper');
    }

    /*
     * Fonction  pour afficher la liste des papiers utilisant un Java script datatable
     *
     * Input: $paper_cat: indique la categorie à afficher
     * 			$val : valeur de recherche si une recherche a été faite
     * 			$page: la page à affiché : ulilisé par les lien de navigation
     */
    public function list_paper($paper_cat = 'all', $val = "_", $page = 0, $dynamic_table = 0)
    {
        $ref_table = "papers";
        /*
         * Vérification si il y a une recherche faite
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
            $url = "paper/list_paper/" . $paper_cat . "/" . $val . "/0/";
            redirect($url);
        }
        /*
         * Récupération de la configuration(structure) de la table à afficher
         */
        $ref_table_config = get_table_config($ref_table);
        $table_id = $ref_table_config['table_id'];
        /*
         * Appel du model pour récuperer la liste à afficher dans la Base de données
         */
        $rec_per_page = ($dynamic_table) ? -1 : 0;
        $data = $this->Paper_dataAccess->get_papers($paper_cat, $ref_table_config, $val, $page, $rec_per_page);
        //for select dropboxes
        /*
         * récupération des correspondances des clès externes
         */
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
        }
        /*
         * Vérification des liens (links) a afficher sur la liste
         */
        $list_links = array();
        $add_link = false;
        $add_link_url = "";
        $view_link_url = "";
        foreach ($ref_table_config['links'] as $link_type => $link) {
            if (!empty($link['on_list'])) { {
                    $link['type'] = $link_type;
                    if (empty($link['title'])) {
                        $link['title'] = lng_min($link['label']);
                    }
                    $push_link = false;
                    switch ($link_type) {
                        case 'add':
                            $add_link = true; //will appear as a top button
                            if (empty($link['url']))
                                $add_link_url = 'manager/add_element/' . $ref_table;
                            else
                                $add_link_url = $link['url'];
                            break;
                        case 'view':
                            if (!isset($link['icon']))
                                $link['icon'] = 'folder';
                            if (empty($link['url']))
                                $link['url'] = 'manager/display_element/' . $ref_table . '/';
                            $push_link = true;
                            break;
                        case 'edit':
                            if (!isset($link['icon']))
                                $link['icon'] = 'pencil';
                            if (empty($link['url']))
                                $link['url'] = 'manager/edit_element/' . $ref_table . '/';
                            $push_link = true;
                            break;
                        case 'delete':
                            if (!isset($link['icon']))
                                $link['icon'] = 'trash';
                            if (empty($link['url']))
                                $link['url'] = 'manager/delete_element/' . $ref_table . '/';
                            $push_link = true;
                            break;
                        case 'add_child':
                            if (!isset($link['icon']))
                                $link['icon'] = 'plus';
                            if (!empty($link['url'])) {
                                $link['url'] = 'manager/add_element_child/' . $link['url'] . "/" . $ref_table . "/";
                                $push_link = true;
                            }
                            break;
                        default:
                            break;
                    }
                    if ($push_link)
                        array_push($list_links, $link);
                }
            }
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
        $i = 1;
        $list_to_display = array();
        foreach ($data['list'] as $key => $value) {
            $element_array = array();
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
            /*
             * Ajout des liens(links) sur la liste
             */
            $action_button = "";
            $arr_buttons = array();
            $view_link_url = "";
            foreach ($list_links as $key_l => $value_l) {
                if ($value_l['type'] == 'view') {
                    $view_link_url = $value_l['url'] . $value[$table_id];
                } else {
                    if (!empty($value_l['icon']))
                        $value_l['label'] = icon($value_l['icon']) . ' ' . lng_min($value_l['label']);
                    array_push(
                        $arr_buttons,
                        array(
                            'url' => $value_l['url'] . $value[$table_id],
                            'label' => $value_l['label'],
                            'title' => $value_l['title']
                        )
                    );
                }
            }
            $action_button = create_button_link_dropdown($arr_buttons, lng_min('Action'));
            $element_array['links'] = $action_button;
            if (isset($element_array['title']) and !empty($view_link_url)) {
                $element_array['title'] = anchor($view_link_url, "<u><b>" . $element_array['title'] . "</b></u>", 'title="' . lng_min('Display element') . '")');
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
         */
        if (!empty($data['list'])) {
            $array_header = $field_list_header;
            ;
            if (trim($data['list'][$key]['links']) != "") {
                array_push($array_header, '');
            }
            if (!$dynamic_table) {
                array_unshift($data['list'], $array_header);
            } else {
                $data['list_header'] = $array_header;
            }
        }
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = "";
        if ($data['nombre'] == 0 and $paper_cat == 'all') {
            //$data ['top_buttons'] .= get_top_button ( 'all', 'Add test papers', 'install/create_default_papers','test papers');
        }
        if ($add_link)
            $data['top_buttons'] .= get_top_button('add', 'Add new', $add_link_url);
        if (activate_update_stored_procedure())
            $data['top_buttons'] .= get_top_button('all', 'Update stored procedure', 'home/update_stored_procedure/' . $ref_table, 'Update stored procedure', 'fa-check', '', ' btn-dark ');
        $data['top_buttons'] .= get_top_button('close', 'Close', 'home');
        /*
         * Titre de la page
         */
        if ($paper_cat == 'pending') {
            $data['page_title'] = $ref_table_config['reference_title'] . ' - Pending';
        } elseif ($paper_cat == "processed") {
            $data['page_title'] = $ref_table_config['reference_title'] . ' - Classified';
        } elseif ($paper_cat == "assigned_me") {
            $data['page_title'] = $ref_table_config['reference_title'] . ' - Assigned to me';
        } elseif ($paper_cat == "excluded") {
            $data['page_title'] = $ref_table_config['reference_title'] . ' - Excluded';
        } else {
            $data['page_title'] = $ref_table_config['reference_title'];
        }
        $data['valeur'] = ($val == "_") ? "" : $val;
        if (!$dynamic_table and !empty($ref_table_config['search_by'])) {
            $data['search_view'] = 'general/search_view';
        }
        /*
         * La vue qui va s'afficher
         */
        if (!$dynamic_table) {
            $data['nav_pre_link'] = 'paper/list_paper/' . $paper_cat . '/' . $val . '/';
            $data['nav_page_position'] = 6;
            $data['page'] = 'general/list';
        } else {
            $data['page'] = 'general/list_dt';
        }
        /*
         * Chargement de la vue avec les données préparés dans le controleur
         */
        $this->load->view('shared/body', $data);
    }

    /*
     * Fonction  pour afficher la liste des papiers utilisant un Java script datatable
     *
     * Input: $paper_cat: indique la categorie à afficher
     * 			$val : valeur de recherche si une recherche a été faite
     * 			$page: la page à affiché : ulilisé par les lien de navigation
     */
    public function list_paper2($paper_cat = 'all', $val = "_", $page = 0)
    {
        $ref_table = "papers";
        /*
         * Vérification si il y a une recherche faite
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
            $url = "paper/list_paper/" . $paper_cat . "/" . $val . "/0/";
            redirect($url);
        }
        /*
         * Récupération de la configuration(structure) de la table à afficher
         */
        $ref_table_config = $this->ref_table_config($ref_table);
        $table_id = $ref_table_config['table_id'];
        /*
         * Appel du model pour récuperer la liste à afficher dans la Base de données
         */
        $data = $this->Paper_dataAccess->get_papers($paper_cat, $ref_table_config, $val, $page);
        //for select dropboxes
        /*
         * récupération des correspondances des clès externes 
         */
        $dropoboxes = array();
        foreach ($ref_table_config['fields'] as $k => $v) {
            if (!empty($v['input_type']) and $v['input_type'] == 'select' and $v['on_list'] == 'show') {
                if ($v['input_select_source'] == 'array') {
                    $dropoboxes[$k] = $v['input_select_values'];
                } elseif ($v['input_select_source'] == 'table') {
                    $dropoboxes[$k] = $this->get_reference_select_values($v['input_select_values']);
                }
            }
            ;
        }
        /*
         * Vérification des liens (links) a afficher sur la liste
         */
        $add_child_link = False;
        $edit_link = False;
        $view_link = False;
        $delete_link = False;
        //view link
        if (!empty($ref_table_config['links']['view']) and !empty($ref_table_config['links']['view']['on_list']) and ($ref_table_config['links']['view']['on_list'] == True)) {
            if (!empty($ref_table_config['links']['view']['url'])) {
                $view_link_url = $ref_table_config['links']['view']['url'];
            } else {
                $view_link_url = 'manage/view_ref/' . $ref_table;
            }
            $view_link_label = $ref_table_config['links']['view']['label'];
            $view_link_title = $ref_table_config['links']['view']['title'];
            $view_link = True;
        }
        //edit link
        if (!empty($ref_table_config['links']['edit']) and !empty($ref_table_config['links']['edit']['on_list']) and ($ref_table_config['links']['edit']['on_list'] == True)) {
            $edit_link_label = $ref_table_config['links']['edit']['label'];
            $edit_link_title = $ref_table_config['links']['edit']['title'];
            $edit_link = True;
        }
        //delete link
        if (!empty($ref_table_config['links']['delete']) and !empty($ref_table_config['links']['delete']['on_list']) and ($ref_table_config['links']['delete']['on_list'] == True)) {
            $delete_link_label = $ref_table_config['links']['delete']['label'];
            $delete_link_title = $ref_table_config['links']['delete']['title'];
            $delete_link = True;
        }
        //addchild link
        if (!empty($ref_table_config['links']['add_child']['url']) and !empty($ref_table_config['links']['add_child']['on_list']) and ($ref_table_config['links']['add_child']['on_list'] == True)) {
            $child_link_url = 'manage/add_ref_child/' . $ref_table_config['links']['add_child']['url'] . "/" . $ref_table;
            $child_link_label = $ref_table_config['links']['add_child']['label'];
            $child_link_title = $ref_table_config['links']['add_child']['title'];
            $add_child_link = True;
        }
        /*
         * Préparation de la liste à afficher sur base du contenu et  stucture de la table
         */
        $i = 1;
        foreach ($data['list'] as $key => $value) {
            /*
             * Ajout des liens(links) sur la liste
             */
            $add_child_button = "";
            $edit_button = "";
            $view_button = "";
            $action_button = "";
            $arr_buttons = array();
            $data['list'][$key]['bibtexKey'] = anchor($view_link_url . '/' . $value[$table_id], "<u><b>" . $data['list'][$key]['bibtexKey'] . "</b></u>");
            if ($view_link) {
                array_push(
                    $arr_buttons,
                    array(
                        'url' => $view_link_url . '/' . $value[$table_id],
                        'label' => '<i class="fa fa-folder"></i> ' . $view_link_label,
                        'title' => $view_link_title
                    )
                );
            }
            if ($edit_link) {
                array_push(
                    $arr_buttons,
                    array(
                        'url' => 'manage/edit_ref/' . $ref_table . '/' . $value[$table_id],
                        'label' => '<i class="fa fa-pencil"></i> ' . $edit_link_label,
                        'title' => $edit_link_title
                    )
                );
            }
            if ($add_child_link) {
                array_push(
                    $arr_buttons,
                    array(
                        'url' => $child_link_url . '/' . $value[$table_id],
                        'label' => '<i class="fa fa-plus"></i> ' . $child_link_label,
                        'title' => $child_link_title
                    )
                );
            }
            if ($delete_link) {
                array_push(
                    $arr_buttons,
                    array(
                        'url' => 'manage/delete_ref/' . $ref_table . '/' . $value[$table_id],
                        'label' => '<i class="fa fa-trash"></i> ' . $delete_link_label,
                        'title' => $delete_link_title
                    )
                );
            }
            $action_button = create_button_link_dropdown($arr_buttons);
            $data['list'][$key]['edit'] = $action_button;
            $data['list'][$key][$table_id] = $i + $page;
            /*
             * Remplacement des clés externes par leurs correspondances
             */
            foreach ($dropoboxes as $k => $v) {
                if ($data['list'][$key][$k]) {
                    if (isset($v[$data['list'][$key][$k]])) {
                        $data['list'][$key][$k] = $v[$data['list'][$key][$k]];
                    }
                } else {
                    $data['list'][$key][$k] = "";
                }
            }
            $i++;
        }
        /*
         * Ajout de l'entête de la liste
         */
        if (!empty($data['list'])) {
            $array_header = $ref_table_config['header_list_fields'];
            if (trim($data['list'][$key]['edit']) != "") {
                array_push($array_header, '');
            }
            array_unshift($data['list'], $array_header);
        }
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = "";
        if ($data['nombre'] == 0 and $paper_cat == 'all') {
            //$data ['top_buttons'] .= get_top_button ( 'all', 'Add test papers', 'install/create_default_papers','test papers');
        }
        $data['top_buttons'] .= get_top_button('add', 'Add new', 'manage/add_ref/' . $ref_table);
        $data['top_buttons'] .= get_top_button('close', 'Close', 'home');
        /*
         * Titre de la page
         */
        if ($paper_cat == 'pending' or $paper_cat == 'processed') {
            $data['page_title'] = $ref_table_config['reference_title'] . ' - ' . $paper_cat;
        } elseif ($paper_cat == "assigned_me") {
            $data['page_title'] = $ref_table_config['reference_title'] . ' - Assigned to me';
        } elseif ($paper_cat == "excluded") {
            $data['page_title'] = $ref_table_config['reference_title'] . ' - Excluded';
        } else {
            $data['page_title'] = $ref_table_config['reference_title'];
        }
        $data['nav_pre_link'] = 'paper/list_paper/' . $paper_cat . '/' . $val . '/';
        $data['nav_page_position'] = 5;
        $data['valeur'] = ($val == "_") ? "" : $val;
        if (!empty($ref_table_config['search_by'])) {
            $data['search_view'] = 'search_view';
        }
        /*
         * La vue qui va s'afficher
         */
        $data['page'] = 'liste';
        /*
         * Chargement de la vue avec les données préparés dans le controleur
         */
        $this->load->view('shared/body', $data);
    }

    /*
     * Fonction spécialisé  pour l'affichage d'un papier
     * Input:	$ref_id: id du papier
     */
    public function view_paper($ref_id)
    {
        $ref_table = "papers";
        /*
         * Récupération de la configuration(structure) de la table des papiers
         */
        $table_config = $this->ref_table_config($ref_table);
        /*
         * Appel de la fonction  récupérer les informations sur le papier afficher
         */
        $paper_data = $this->get_reference_detail('papers', $ref_id);
        /*
         * Préparations des informations à afficher
         */
        //venue
        $venue = "";
        foreach ($paper_data as $key => $value) {
            if ($value['title'] == 'Venue' and !empty($value['val2'][0])) {
                $venue = $value['val2'][0];
            }
        }
        //Authors
        $authors = "";
        foreach ($paper_data as $key => $value) {
            if ($value['title'] == 'Author' and !empty($value['val2'])) {
                if (count($value['val2'] > 1)) {
                    $authors = '<table class="table table-hover" ><tr><td> ' . $value['val2'][0] . '</td></tr>';
                    foreach ($value['val2'] as $k => $v) {
                        if ($k > 0) {
                            $authors .= "<tr><td> " . $v . '</td></tr>';
                        }
                    }
                    $authors .= "</table>";
                } else {
                    $authors = " : " . $value['val2'][0];
                }
            }
        }
        $content_item = $this->DBConnection_mdl->get_row_details($ref_table, $ref_id);
        $paper_name = $content_item['bibtexKey'] . " - " . $content_item['title'];
        $paper_excluded = False;
        if ($content_item['paper_excluded'] == '1') {
            $paper_excluded = True;
        }
        $data['paper_excluded'] = $paper_excluded;
        $item_data = array();
        $array['title'] = $content_item['bibtexKey'] . " - " . $content_item['title'];
        if (!empty($content_item['doi'])) {
            $array['title'] .= '<ul class="nav navbar-right panel_toolbox">
				<li>
					<a title="Go to the page" href="' . $content_item['doi'] . '" target="_blank" >
				 		<img src="' . base_url() . 'cside/images/pdf.jpg"/>
					</a>
				</li>
				</ul>';
        }
        array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Abstract') . " :</b> <br/><br/>" . $content_item['preview'];
        array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Preview') . " :</b> <br/><br/>" . $content_item['bibtex'];
        array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Venue') . " </b> " . $venue;
        //array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Authors') . " </b> " . $authors;
        //array_push($item_data, $array);
        $data['item_data'] = $item_data;
        /*
         * Informations sur l'exclusion du papier si le papier est exclu
         */
        $exclusion = $this->DBConnection_mdl->get_exclusion($ref_id);
        $table_config3 = $this->ref_table_config("exclusion");
        $dropoboxes = array();
        foreach ($table_config3['fields'] as $k => $v) {
            if (!empty($v['input_type']) and $v['input_type'] == 'select' and $k != 'exclusion_paper_id') {
                if ($v['input_select_source'] == 'array') {
                    $dropoboxes[$k] = $v['input_select_values'];
                } elseif ($v['input_select_source'] == 'table') {
                    $dropoboxes[$k] = $this->get_reference_select_values($v['input_select_values']);
                }
            }
            ;
        }
        $T_item_data_exclusion = array();
        $T_remove_exclusion_button = array();
        $item_data_exclusion = array();
        $delete_exclusion = "";
        $edit_exclusion = "";
        if (!empty($exclusion)) {
            //put values from reference tables
            foreach ($dropoboxes as $k => $v) {
                if (($exclusion[$k])) {
                    if (isset($v[$exclusion[$k]])) {
                        $exclusion[$k] = $v[$exclusion[$k]];
                    }
                } else {
                    $exclusion[$k] = "";
                }
            }
            foreach ($table_config3['fields'] as $k_t => $v_t) {
                if (!(isset($v_t['on_view']) and $v_t['on_view'] == 'hidden') and $k_t != 'exclusion_paper_id') {
                    $array['title'] = $v_t['field_title'];
                    $array['val'] = isset($exclusion[$k_t]) ? ": " . $exclusion[$k_t] : ': ';
                    array_push($item_data_exclusion, $array);
                }
            }
            $delete_exclusion = get_top_button('delete', 'Cancel the exclusion', 'manage/remove_exclusion/' . $exclusion['exclusion_id'] . "/" . $ref_id, 'Cancel the exclusion') . " ";
            $edit_exclusion = get_top_button('edit', 'Edit the exclusion', 'manage/edit_exclusion/' . $exclusion['exclusion_id'], 'Edit the exclusion') . " ";
        }
        $data['data_exclusion'] = $item_data_exclusion;
        $data['remove_exclusion_button'] = $edit_exclusion . $delete_exclusion;
        /*
         * Informations sur inclusion du papier
         */
        $inclusion = $this->DBConnection_mdl->get_inclusion($ref_id);
        $table_config3 = $this->ref_table_config("inclusion");
        $dropoboxes = array();
        foreach ($table_config3['fields'] as $k => $v) {
            if (!empty($v['input_type']) and $v['input_type'] == 'select' and $k != 'inclusion_paper_id') {
                if ($v['input_select_source'] == 'array') {
                    $dropoboxes[$k] = $v['input_select_values'];
                } elseif ($v['input_select_source'] == 'table') {
                    $dropoboxes[$k] = $this->get_reference_select_values($v['input_select_values']);
                }
            }
            ;
        }
        $T_item_data_inclusion = array();
        $T_remove_inclusion_button = array();
        $item_data_inclusion = array();
        $delete_inclusion = "";
        $edit_inclusion = "";
        if (!empty($inclusion)) {
            //put values from reference tables
            foreach ($dropoboxes as $k => $v) {
                if (($inclusion[$k])) {
                    if (isset($v[$inclusion[$k]])) {
                        $inclusion[$k] = $v[$inclusion[$k]];
                    }
                } else {
                    $inclusion[$k] = "";
                }
            }
            foreach ($table_config3['fields'] as $k_t => $v_t) {
                if (!(isset($v_t['on_view']) and $v_t['on_view'] == 'hidden') and $k_t != 'inclusion_paper_id') {
                    $array['title'] = $v_t['field_title'];
                    $array['val'] = isset($inclusion[$k_t]) ? ": " . $inclusion[$k_t] : ': ';
                    array_push($item_data_inclusion, $array);
                }
            }
            $delete_inclusion = get_top_button('delete', 'Cancel the inclusion', 'manage/remove_inclusion/' . $inclusion['inclusion_id'] . "/" . $ref_id, 'Cancel the inclusion') . " ";
            $edit_inclusion = get_top_button('edit', 'Edit the inclusion', 'manage/edit_inclusion/' . $inclusion['inclusion_id'], 'Edit the inclusion') . " ";
        }
        $data['data_inclusion'] = $item_data_inclusion;
        $data['remove_inclusion_button'] = $edit_inclusion . $delete_inclusion;
        /*
         * Information sur la clstification du papier si le papiers est déjà classé
         */
        $classification = $this->Data_extraction_data_access->get_classifications($ref_id);
        if (!empty($classification)) {
            $classification_data = $this->get_reference_detail('classification', $classification[0]['class_id'], True);
            $data['classification_data'] = $classification_data;
            $delete_button = get_top_button('delete', 'Remove the classification', 'data_extraction/remove_classification2/' . $classification[0]['class_id'] . "/" . $ref_id, 'Remove the classification') . " ";
            $edit_button = get_top_button('edit', 'Edit the classification', 'data_extraction/edit_classification2/' . $classification[0]['class_id'], 'Edit the classification') . " ";
            $data['classification_button'] = $edit_button . " " . $delete_button;
        } else {
            if (!empty($table_config['links']['add_child']['url']) and !empty($table_config['links']['add_child']['on_view']) and ($table_config['links']['add_child']['on_view'] == True)) {
                //$data ['classification_button'] = '<li><a><button type="button" class="btn btn-success" data-toggle="modal" data-target="#relisformModal"  data-modal_link="manage/add_classification_modal/'.$ref_id.'"  data-operation_type="1" data-modal_title="Add classification  to : '.$paper_name.'" ><i class="fa fa-plus"></i> '.$table_config['links']['add_child']['label'] .' </button></a></li> ';
                $data['classification_button'] = get_top_button('add', 'Add classification', 'manage/add_classification/' . $ref_id, 'Add classification') . " ";
                ;
            }
        }
        /*
         * Informations sur l'assignation du papier si le papier est assigné à un utilisateur
         */
        $assignation = $this->DBConnection_mdl->get_assignations($ref_id);
        $table_config3 = $this->ref_table_config("assignation");
        $dropoboxes = array();
        foreach ($table_config3['fields'] as $k => $v) {
            if (!empty($v['input_type']) and $v['input_type'] == 'select' and $k != 'class_paper_id') {
                if ($v['input_select_source'] == 'array') {
                    $dropoboxes[$k] = $v['input_select_values'];
                } elseif ($v['input_select_source'] == 'table') {
                    $dropoboxes[$k] = $this->get_reference_select_values($v['input_select_values']);
                }
            }
            ;
        }
        $T_item_data_assignation = array();
        $T_remove_assignation_button = array();
        foreach ($assignation as $k_class => $v_class) {
            //put values from reference tables
            foreach ($dropoboxes as $k => $v) {
                if (($assignation[$k_class][$k])) {
                    $assignation[$k_class][$k] = $v[$assignation[$k_class][$k]];
                } else {
                    $assignation[$k_class][$k] = "";
                }
            }
            $item_data_assignation = array();
            foreach ($table_config3['fields'] as $k_t => $v_t) {
                if (!(isset($v_t['on_view']) and $v_t['on_view'] == 'hidden') and $k_t != 'assigned_paper_id') {
                    $array['title'] = $v_t['field_title'];
                    $array['val'] = isset($v_class[$k_t]) ? ": " . $assignation[$k_class][$k_t] : ': ';
                    array_push($item_data_assignation, $array);
                }
            }
            $T_item_data_assignation[$k_class] = $item_data_assignation;
            $delete_button = get_top_button('delete', 'Remove the assignation', 'manage/remove_assignation/' . $v_class['assigned_id'] . "/" . $ref_id, 'Remove the assignation') . " ";
            $edit_button = get_top_button('edit', 'Edit the assignation', 'manage/edit_assignation/' . $v_class['assigned_id'], 'Edit the assignation') . " ";
            $T_remove_assignation_button[$k_class] = $edit_button . $delete_button;
        }
        $data['data_assignations'] = $T_item_data_assignation;
        $data['remove_assignation_button'] = $T_remove_assignation_button;
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = "";
        $data['add_assignation_buttons'] = get_top_button('all', "Assigne to a user", 'manage/new_assignation/' . $ref_id, ' Assigne to someone ', " fa-plus ", "  ", 'btn-success') . " ";
        if (!$paper_excluded) {
            $data['top_buttons'] .= get_top_button('all', "Exclude the paper", 'manage/new_exclusion/' . $ref_id, 'Exclude', " fa-minus", '', 'btn-danger') . " ";
            if (!empty($table_config['links']['edit']) and !empty($table_config['links']['edit']['on_view']) and ($table_config['links']['edit']['on_view'] == True)) {
                $data['top_buttons'] .= get_top_button('edit', $table_config['links']['edit']['title'], 'manage/edit_ref/' . $ref_table . '/' . $ref_id) . " ";
            }
            if (!empty($table_config['links']['delete']) and !empty($table_config['links']['delete']['on_view']) and ($table_config['links']['delete']['on_view'] == True)) {
                $data['top_buttons'] .= get_top_button('delete', $table_config['links']['delete']['title'], 'manage/delete_ref/' . $ref_table . '/' . $ref_id) . " ";
            }
        }
        $data['top_buttons'] .= get_top_button('back', 'Back', 'manage');
        /*
         * Titre de la page
         */
        $data['page_title'] = lng($table_config['reference_title_min']);
        if ($paper_excluded) {
            $data['page_title'] = lng("Paper excluded");
        }
        /*
         * La vue qui va s'afficher
         */
        $data['page'] = 'paper/view_paper';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    //prepare the necessary data and load the appropriate view for importing papers from a CSV file
    public function import_papers()
    {
        $headder = array('Row', 'Field1', 'Field2', 'Field3', 'Field4', 'Field5', 'Field6');
        $data['page_title'] = lng('Import papers - CSV');
        //	$data ['top_buttons'] = get_top_button ( 'all', 'Import BibTeX', 'paper/import_bibtext','Import BibTeX','fa-upload' );
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        $data['page'] = 'paper/import_papers_1';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    //import bibliographic data from EndNote files
    public function import_endnote()
    {
        $this->import_bibtext('endnote');
    }

    //import bibliographic data from BibTeX or EndNote files
    public function import_bibtext($format = 'bibtex')
    {
        $headder = array('Row', 'Field1', 'Field2', 'Field3', 'Field4', 'Field5', 'Field6');
        $data['import_format'] = $format;
        if (!empty($format) and $format == 'endnote') {
            $data['page_title'] = lng('Import papers - Endnote');
        } else {
            $data['page_title'] = lng('Import papers - BibTeX');
        }
        //$data ['top_buttons'] = get_top_button ( 'all', 'Import CSV', 'paper/import_papers','Import CSV','fa-upload' );
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        $data['page'] = 'paper/import_bibtex_1';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /*
        handle the process of importing papers from a BibTeX or EndNote file, 
        inserting them into the database, and displaying appropriate messages to the user
    */
    public function import_papers_save_bibtext()
    {
        $post_arr = $this->input->post();
        //use save bibtext to get the right answer
        $data_array = json_decode($post_arr['data_array'], True);
        $papers_sources = (!empty($post_arr['papers_sources']) ? $post_arr['papers_sources'] : NULL);
        $search_strategy = (!empty($post_arr['search_strategy']) ? $post_arr['search_strategy'] : NULL);
        //	$paper_start_from = ((!empty($post_arr['paper_start_from']) AND is_numeric($post_arr['paper_start_from']))?$post_arr['paper_start_from']:2);
        $active_user = active_user_id();
        $added_active_phase = get_active_phase();
        $operation_code = active_user_id() . "_" . time();
        $default_key_prefix = get_appconfig_element('key_paper_prefix');
        $default_key_prefix = ($default_key_prefix == '0') ? '' : $default_key_prefix;
        $default_key_serial = get_appconfig_element('key_paper_serial');
        $serial_key = $default_key_serial;
        //set classification status
        if (get_appconfig_element('screening_on')) {
            $classification_status = 'Waiting';
            $screening_status = 'Pending';
        } else {
            $classification_status = 'To classify';
            $screening_status = 'Included';
        }
        //echo $classification_status;
        //exit;
        $i = 1;
        $imported = 0;
        $exist = 0;
        foreach ($data_array as $key => $paper) {
            $paper['papers_sources'] = $papers_sources;
            $paper['search_strategy'] = $search_strategy;
            $paper['operation_code'] = $operation_code;
            $res = $this->insert_paper_bibtext($paper);
            if ($res == '1') {
                $imported++;
            } else {
                $exist++;
            }
        }
        // update the operation tab
        $operation_arr = array(
            'operation_code' => $operation_code,
            'operation_type' => 'import_paper',
            'user_id' => active_user_id(),
            'operation_desc' => 'Paper import before screening'
        );
        $res2 = $this->manage_mdl->add_operation($operation_arr);
        if (!empty($imported)) {
            set_top_msg(" $imported papers imported successfully");
        }
        if (!empty($exist)) {
            set_top_msg(" $exist papers already exist", 'error');
        }
        redirect('screening/screening');
    }

    /*
        handle the process of importing papers from a CSV file, 
        inserting them into the database, and displaying appropriate messages to the user
    */
    public function import_papers_save_csv()
    {
        $post_arr = $this->input->post();
        //print_test($post_arr); exit;
        $data_array = json_decode($post_arr['data_array']);
        $paper_title = $post_arr['paper_title'];
        $bibtexKey = $post_arr['bibtexKey'];
        $paper_link = $post_arr['paper_link'];
        $paper_abstract = $post_arr['paper_abstract'];
        $bibtex = $post_arr['bibtex'];
        $paper_key = $post_arr['paper_key'];
        $paper_author = $post_arr['paper_author'];
        $year = $post_arr['year'];
        //$paper_start_from=$post_arr['paper_start_from'];
        $papers_sources = (!empty($post_arr['papers_sources']) ? $post_arr['papers_sources'] : NULL);
        $search_strategy = (!empty($post_arr['search_strategy']) ? $post_arr['search_strategy'] : NULL);
        $paper_start_from = ((!empty($post_arr['paper_start_from']) and is_numeric($post_arr['paper_start_from'])) ? $post_arr['paper_start_from'] : 2);
        $active_user = active_user_id();
        $added_active_phase = get_active_phase();
        $operation_code = active_user_id() . "_" . time();
        $default_key_prefix = get_appconfig_element('key_paper_prefix');
        $default_key_prefix = ($default_key_prefix == '0') ? '' : $default_key_prefix;
        $default_key_serial = get_appconfig_element('key_paper_serial');
        $serial_key = $default_key_serial;
        //set classification status
        if (get_appconfig_element('screening_on')) {
            $classification_status = 'Waiting';
            $screening_status = 'Pending';
        } else {
            $classification_status = 'To classify';
            $screening_status = 'Included';
        }
        //echo $classification_status;
        //exit;
        $i = 1;
        $imported = 0;
        foreach ($data_array as $key => $value) {
            if ($key >= ($paper_start_from - 1)) {
                $value['zz'] = "";
                //$v_bibtex_key=!empty($value[$bibtexKey])?$this->mres_escape($value[$bibtexKey]):'paper_'.$i;
                if (!empty($value[$bibtexKey])) {
                    $v_bibtex_key = $this->mres_escape($value[$bibtexKey]);
                } else {
                    $v_bibtex_key = $default_key_prefix . $serial_key;
                    $serial_key++;
                }
                //	$v_bibtex_key=!empty($value[$bibtexKey])?$this->mres_escape($value[$bibtexKey]):$default_key_prefix.($default_key_serial+$i);
                $v_title = $this->mres_escape($value[$paper_title]);
                //$v_title=$value[$paper_title];
                $v_paper_link = $this->mres_escape($value[$paper_link]);
                $v_preview = !empty($value[$paper_author]) ? "<b>Authors:</b><br/>" . $this->mres_escape($value[$paper_author]) . " <br/>" : "";
                $v_preview .= !empty($value[$paper_key]) ? "<b>Key words:</b><br/>" . $this->mres_escape($value[$paper_key]) . " <br/>" : "";
                $v_abstract = $this->mres_escape($value[$paper_abstract]);
                $v_bibtex = $this->mres_escape($value[$bibtex]);
                $year = (!empty($value[$year]) and is_numeric($value[$year])) ? $this->mres_escape($value[$year]) : NULL;

                $res_sql = $this->Paper_dataAccess->insert_to_paper($v_bibtex_key, $v_title, $v_preview, $v_bibtex, $v_abstract, $v_paper_link, $year, $papers_sources, $search_strategy, $active_user, $added_active_phase, $operation_code, $classification_status);
                $imported++;
                //print_test($res_sql);
                $i++;
            }
        }
        if ($serial_key != $default_key_serial) {
            set_appconfig_element('key_paper_serial', $serial_key);
        }
        // update the operation tab
        $operation_arr = array(
            'operation_code' => $operation_code,
            'operation_type' => 'import_paper',
            'user_id' => active_user_id(),
            'operation_desc' => 'Paper import before screening'
        );
        $res2 = $this->manage_mdl->add_operation($operation_arr);
        if (!empty($imported)) {
            set_top_msg(" $imported papers imported successfully");
        }
        //print_test($res2);
        redirect('screening/screening');
    }

    //Load a bibtext file connect to bibler to get the JSON
    public function import_papers_load_bibtext()
    {
        $post_arr = $this->input->post();
        $error_array = array();
        $success_array = array();
        $array_tab_preview = array();
        $array_tab_values = array();
        if (!empty($post_arr['from_endnote'])) {
            $redirect = "paper/import_endnote";
        } else {
            $redirect = "paper/import_bibtext";
        }
        //exit;
        if (empty($_FILES["paper_file"]['tmp_name'])) {
            echo set_top_msg(lng_min("No file selected"), 'error');
            redirect($redirect);
            exit;
        }
        if ($_FILES["paper_file"]["error"] > 0) {
            //echo "Error: " . $_FILES["file"]["error"] . "<br />";
            echo set_top_msg("Error: " . file_upload_error($_FILES["install_config"]["error"]), 'error');
            array_push($error_array, "Error: " . file_upload_error($_FILES["install_config"]["error"]));
            redirect($redirect);
            exit;
        } else {
            $bibtextString = file_get_contents($_FILES["paper_file"]['tmp_name']);
            //Call bibler to convert into json and return then conert into array
            if (!empty($post_arr['from_endnote'])) {
                $Tpapers = $this->get_bibler_result($bibtextString, "endnote");
            } else {
                $Tpapers = $this->get_bibler_result($bibtextString, "multi_bibtex");
            }
            //		vv
            $data['json_values'] = $json_papers = json_encode($Tpapers['paper_array']);
            ;
            // convert json into array
            /////$T_papers=json_decode($bibtextString);
            //z
            $data['uploaded_papers'] = $Tpapers['paper_preview_sucess'];
            $data['uploaded_papers_exist'] = $Tpapers['paper_preview_exist'];
            //add papers duplicated
            //print_test($Tpapers);
            //print_test($data['uploaded_papers']);
            $data['uploaded_papers_error'] = $Tpapers['paper_preview_error'];
            $data['number_of_papers'] = count($Tpapers['paper_array']);
        }
        if (get_appconfig_element('source_papers_on')) {
            $data['source_papers'] = $this->manager_lib->get_reference_select_values('papers_sources;ref_value', True, False);
            //print_test($data['source_papers']);
        }
        if (get_appconfig_element('search_strategy_on')) {
            $data['search_strategy'] = $this->manager_lib->get_reference_select_values('search_strategy;ref_value', True, False);
            //print_test($data['search_strategy']);
        }
        $data['page_title'] = lng('Import papers - BibTeX');
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        $data['page'] = 'paper/import_bibtext_2';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /*
        handle the loading and processing of CSV files for importing papers. 
        It validates the file type, reads and parses the CSV data, prepares 
        the necessary data for preview and field matching
    */
    public function import_papers_load_csv()
    {
        $error_array = array();
        $success_array = array();
        $array_tab_preview = array();
        $array_tab_values = array();
        //print_test($_FILES["paper_file"]);
        if (empty($_FILES["paper_file"]['tmp_name'])) {
            echo set_top_msg(lng_min("No file selected"), 'error');
            redirect('paper/import_papers');
            exit;
        }
        if ($_FILES["paper_file"]["error"] > 0) {
            //echo "Error: " . $_FILES["file"]["error"] . "<br />";
            array_push($error_array, "Error: " . file_upload_error($_FILES["install_config"]["error"]));
        } elseif ($_FILES["paper_file"]["type"] !== "application/vnd.ms-excel") {
            //echo "File must be a .php";
            array_push($error_array, "File must be a csv file");
        } else {
            ini_set('auto_detect_line_endings', TRUE);
            $fp = fopen($_FILES['paper_file']['tmp_name'], 'rb');
            $i = 1;
            $last_count = 0;
            //	while ( (($line = utf8_encode(fgets($fp))) !== false) AND $i<5) {
            while ((($Tline = (fgetcsv($fp, 0, get_appconfig_element('csv_field_separator'), get_ci_config("csv_string_dellimitter")))) !== false) and $i < 11) {
                $Tline = array_map("utf8_encode", $Tline);
                if ($last_count < count($Tline)) {
                    $last_count = count($Tline);
                }
                $i++;
            }
            $array_header = array();
            $array_header_opt = array('zz' => "No field selected");
            for ($j = 1; $j <= $last_count; $j++) {
                array_push($array_header, 'Field ' . $j);
                array_push($array_header_opt, 'Field ' . $j);
            }
            //print_test($array_header);
            array_push($array_tab_preview, $array_header);
            $i = 1;
            rewind($fp);
            //while ( (($line = fgets($fp)) !== false)) {
            while ((($Tline = (fgetcsv($fp, 0, get_appconfig_element('csv_field_separator'), get_ci_config("csv_string_dellimitter")))) !== false)) {
                $Tline = array_map("utf8_encode", $Tline);
                if ($i < 11) {
                    array_push($array_tab_preview, $Tline);
                }
                array_push($array_tab_values, $Tline);
                $i++;
            }
            //print_test($array_tab_values);
            $data['json_values'] = json_encode($array_tab_values);
        }
        $csv_papers = array();
        $data['csv_papers'] = $array_tab_preview;
        if (!empty($array_header)) {
            $data['csv_fields'] = $array_header;
            $data['csv_fields_opt'] = $array_header_opt;
        } else {
            $data['csv_fields'] = array();
            $data['csv_fields_opt'] = array();
        }
        $data['paper_config_fields'] = array(
            'paper_title' => array('title' => "Paper title ", "mandatory" => TRUE),
            'bibtexKey' => array('title' => "Paper key <i style='font-size:0.8em'>(If not available It will be generated)</i>", "mandatory" => False),
            'paper_link' => array('title' => "Link", "mandatory" => False),
            'year' => array('title' => "Year", "mandatory" => False),
            'paper_abstract' => array('title' => "Abstract", "mandatory" => False),
            'bibtex' => array('title' => "Bibtex", "mandatory" => False),
            'paper_key' => array('title' => "Key words", "mandatory" => False),
            'paper_author' => array('title' => "Authors", "mandatory" => False)
        );
        if (get_appconfig_element('source_papers_on')) {
            $data['source_papers'] = $this->manager_lib->get_reference_select_values('papers_sources;ref_value', True, False);
            //print_test($data['source_papers']);
        }
        if (get_appconfig_element('search_strategy_on')) {
            $data['search_strategy'] = $this->manager_lib->get_reference_select_values('search_strategy;ref_value', True, False);
            //print_test($data['search_strategy']);
        }
        $data['page_title'] = lng('Import papers - match fields');
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        $data['page'] = 'paper/import_papers_2';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    /*
     * Page pour ajouter un papier avec bibtex
     */
    public function add_paper_bibtex($data = array())
    {
        $data['top_buttons'] = get_top_button('close', 'Back', 'element/entity_list/list_all_papers');
        $data['title'] = 'Add BibTeX';
        $data['page'] = 'paper/bibtex_form';
        $this->load->view('shared/body', $data);
    }

    /*
        handles the saving of a paper from a BibTeX entry. 
        It checks if the BibTeX field is empty, parses the BibTeX entry, 
        inserts the paper into the database if the parsing is successful, 
        and displays the appropriate success or error message
    */
    public function save_bibtex_paper()
    {
        $post_arr = $this->input->post();
        $data['message_error'] = "";
        $data['message_success'] = "";
        if (empty($post_arr['bibtext'])) {
            $data['message_error'] .= "Bibtex field empty.<br/>";
            $this->add_paper_bibtex($data);
        } else {
            $bibtex = $post_arr['bibtext'];
            $bibtex_result = $this->get_bibler_result($bibtex);
            //	print_r($bibtex_result);
            if (!empty($bibtex_result['bibtext'])) {
                $data['bibtext'] = $bibtex_result['bibtext'];
            }
            //	print_test($bibtex_result);exit;
            if (empty($bibtex_result['error']) and !empty($bibtex_result)) {
                $insert_res = $this->insert_paper_bibtext($bibtex_result['paper_array']);
                if ($insert_res == 1) {
                    $data['message_success'] .= "Paper added";
                } else {
                    $data['message_error'] .= $insert_res;
                }
            } else {
                $data['message_error'] .= $bibtex_result['error_msg'];
            }
            $this->add_paper_bibtex($data);
        }
    }

    /* 
        handles the saving of a paper from a BibTeX entry using the Bibler web service. 
        It checks if the BibTeX field is empty, connects to the web service to process the BibTeX entry, 
        inserts the paper into the database if the response is successful and contains the necessary fields, 
        and displays the appropriate success or error message
    */
    public function save_bibtex_paper_saved()
    {
        $post_arr = $this->input->post();
        $data['message_error'] = "";
        $data['message_success'] = "";
        if (empty($post_arr['bibtext'])) {
            $data['message_error'] .= "Bibtex field empty.<br/>";
            $this->add_paper_bibtex($data);
        } else {
            $bibtex = $post_arr['bibtext'];
            $init_time = microtime();
            $i = 1;
            $res = "init";
            while ($i < 10) {
                //$res=$this->biblerproxy_lib->addEntry($bibtex);
                //$res=$this->biblerproxy_lib->bibtextobibtex($bibtex);
                //$res=$this->biblerproxy_lib->bibtextosql($bibtex);
                //$res=$this->biblerproxy_lib->addEntry($bibtex);
                //$res=$this->biblerproxy_lib->previewEntry($bibtex);
                //$res=$this->biblerproxy_lib->bibtextocsv($bibtex);
                //$res=$this->biblerproxy_lib->bibtextohtml($bibtex);
                //$res=$this->biblerproxy_lib->formatBibtex($bibtex);
                $res = $this->biblerproxy_lib->createentryforreliS($bibtex);
                $correct = False;
                if (strpos($res, 'Internal Server Error') !== false or empty($res)) {
                    //	echo " error - ".$i;
                    $i++;
                } else {
                    //	echo " ok - ".$i;
                    $correct = True;
                    $i = 20;
                }
                //usleep(500);
            }
            $end_time = microtime();
            //print_test($res);
            //	echo "<h1>".($end_time - $init_time)."</h1>";
            ini_set('auto_detect_line_endings', TRUE);
            if ($correct) {
                //print_test($res);
                $res = str_replace("True,", "'True',", $res);
                $res = str_replace("False,", "'False',", $res);
                $res = $this->biblerproxy_lib->fixJSON($res);
                //tou correct the error in venu from the webservice
                //$res=substr($res,0,strpos($res,', "venue_full":')).'}';
                $Tres = json_decode($res, True);
                if (json_last_error() === JSON_ERROR_NONE) {
                    //print_test($Tres);
                    $data['bibtext'] = $bibtex;
                    $paper_array = array();
                    if (
                        !empty($Tres['result_code'])
                        //	AND $Tres['result_code']=='True'
                        and !empty($Tres['entry']['entrykey'])
                    ) {
                        //bibtex decoded
                        $year = !empty($Tres['entry']['year']) ? $Tres['entry']['year'] : "";
                        if (!empty($Tres['venue_full'])) {
                            $venue_id = $this->add_venue($Tres['venue_full'], $year);
                            $paper_array['venueId'] = $venue_id;
                        }
                        $paper_array['bibtexKey'] = $Tres['entry']['entrykey'];
                        $paper_array['title'] = !empty($Tres['entry']['title']) ? $Tres['entry']['title'] : "";
                        $paper_array['preview'] = !empty($Tres['preview']) ? $Tres['preview'] : "";
                        $paper_array['bibtex'] = !empty($Tres['bibtex']) ? $Tres['bibtex'] : "";
                        $paper_array['abstract'] = !empty($Tres['entry']['abstract']) ? $Tres['entry']['abstract'] : "";
                        $paper_array['doi'] = !empty($Tres['entry']['paper']) ? $Tres['entry']['paper'] : "";
                        $paper_array['year'] = $year;
                        $paper_array['authors'] = !empty($Tres['authors']) ? $Tres['authors'] : "";
                        $insert_res = $this->insert_paper_bibtext($paper_array);
                        if ($insert_res == 1) {
                            $data['message_success'] .= "Paper added";
                        } else {
                            $data['message_error'] .= $insert_res;
                        }
                    } else {
                        $data['message_error'] .= "Error: chect your Bibtext format.<br/>";
                    }
                    $this->add_paper_bibtex($data);
                } else {
                    //echo json_last_error();
                    $json_errodr = "";
                    switch (json_last_error()) {
                        case JSON_ERROR_NONE:
                            $json_error = 'No errors';
                            break;
                        case JSON_ERROR_DEPTH:
                            $json_error = 'Maximum stack depth exceeded';
                            break;
                        case JSON_ERROR_STATE_MISMATCH:
                            $json_error = 'Underflow or the modes mismatch';
                            break;
                        case JSON_ERROR_CTRL_CHAR:
                            $json_error = 'Unexpected control character found';
                            break;
                        case JSON_ERROR_SYNTAX:
                            $json_error = 'Syntax error, malformed JSON';
                            break;
                        case JSON_ERROR_UTF8:
                            $json_error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                            break;
                        default:
                            $json_error = 'Unknown error';
                            break;
                    }
                    $data['message_error'] .= "JSON Error : " . $json_error . ".<br/>";
                    $this->add_paper_bibtex($data);
                }
            } else {
                $data['message_error'] .= "Unable to connect to Bibler web service.<br/>";
                $this->add_paper_bibtex($data);
            }
        }
    }

    /*
     * perform a search in the database to check if a paper with the given BibTeX key and title already exists
     */
    private function paper_exist($paper_array)
    {
        $bibtexKey = $paper_array['bibtexKey'];
        $exist = False;
        $stopsearch = False;
        $i = 1;
        while (!$stopsearch) {
            $res = $this->Paper_dataAccess->select_from_paper($bibtexKey);

            if (empty($res)) {
                $stopsearch = True;
                $exist = False;
            } else {
                if ($res['title'] == $paper_array['title']) {
                    $stopsearch = True;
                    $exist = True;
                } else {
                    $bibtexKey = $paper_array['bibtexKey'] . '_' . $i;
                }
            }
            $i++;
        }
        return $exist;
    }

    /**
     * checks if a paper already exists based on the BibTeX key and title, 
     * and then inserts the paper into the database with the appropriate values and relationships with authors
     */
    private function insert_paper_bibtext($paper_array)
    {
        //check papers_exist
        //print_test($paper_array);
        $authors = $paper_array['authors'];
        unset($paper_array['authors']);
        $bibtexKey = $paper_array['bibtexKey'];
        $exist = False;
        $stopsearch = False;
        $i = 1;
        while (!$stopsearch) {
            $res = $this->Paper_dataAccess->select_from_paper($bibtexKey);

            if (empty($res)) {
                $stopsearch = True;
                $exist = False;
            } else {
                if ($res['title'] == $paper_array['title']) {
                    $stopsearch = True;
                    $exist = True;
                } else {
                    $bibtexKey = $paper_array['bibtexKey'] . '_' . $i;
                }
            }
            $i++;
        }
        if (!$exist) {
            //add venue
            if (!empty($paper_array['venue'])) {
                $venue_id = $this->add_venue($paper_array['venue'], $paper_array['year']);
                $paper_array['venueId'] = $venue_id;
            }
            unset($paper_array['venue']);
            $paper_array['added_by'] = active_user_id();
            $paper_array['bibtexKey'] = $bibtexKey;
            //set classification status
            if (get_appconfig_element('screening_on')) {
                $paper_array['classification_status'] = 'Waiting';
                $paper_array['screening_status'] = 'Pending';
            } else {
                $paper_array['classification_status'] = 'To classify';
                $paper_array['screening_status'] = 'Included';
            }
            $this->db_current->insert('paper', $paper_array);
            $paper_id = $this->db_current->insert_id();
            if (!empty($authors)) {
                $this->add_author($paper_id, $authors);
            }
            return 1;
        } else {
            return 'Paper already exit';
        }
        //print_test($res);
    }

    //add authors to the database for a given paper ID
    private function add_author($paper_id, $author_array)
    {
        //check author exist
        foreach ($author_array as $key => $author) {
            $author_name = $author['first_name'] . ' ' . $author['last_name'];
            $res = $this->Paper_dataAccess->select_from_author($author_name);

            //print_test($res);
            if (empty($res['author_id'])) {
                $this->db_current->insert('author', array('author_name' => $author_name));
                $author_id = $this->db_current->insert_id();
            } else {
                $author_id = $res['author_id'];
            }
            if (!empty($author_id)) {
                $this->db_current->insert(
                    'paperauthor',
                    array(
                        'paperId' => $paper_id,
                        'authorId' => $author_id,
                        'author_rank' => $key + 1,
                    )
                );
            }
            //print_test($res);
        }
    }

    //add a venue to the database if it does not already exist.
    private function add_venue($venue, $year = 0)
    {
        $res = $this->db_current->get_where(
            'venue',
            array('venue_fullName' => $venue, 'venue_active' => 1)
        )
            ->row_array();
        $array_venue = array('venue_fullName' => $venue);
        if (!empty($year)) {
            $array_venue['venue_year'] = $year;
        }
        if (empty($res['venue_id'])) {
            $this->db_current->insert('venue', $array_venue);
            return $venue_id = $this->db_current->insert_id();
        } else {
            return $res['venue_id'];
        }
    }

    /**
     * ensure that the string is properly escaped before using it in database queries or other contexts where special characters may cause issues
     */
    private function mres_escape($value)
    {
        $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");
        return str_replace($search, $replace, $value);
    }

    /**
     * delete all papers in the system. The confirmation message is displayed to the user with options to continue or cancel the deletion
     */
    function clear_papers_validation()
    {
        $data['page'] = 'install/frm_install_result';
        $data['left_menu_admin'] = True;
        $data['array_warning'] = array('You want to delete All papers : The opération cannot be undone !');
        $data['array_success'] = array();
        $data['next_operation_button'] = "";
        $data['page_title'] = lng('Clear logs ');
        $data['next_operation_button'] = " &nbsp &nbsp &nbsp" . get_top_button('all', 'Continue to delete', 'paper/clear_papers', 'Continue to delete', '', '', ' btn-success ', FALSE);
        $data['next_operation_button'] .= get_top_button('all', 'Cancel', 'element/entity_list/list_all_papers', 'Cancel', '', '', ' btn-danger ', FALSE);
        $this->load->view('shared/body', $data);
    }

    /*
        soft delete of papers and related data by marking them as inactive rather than permanently deleting them from the database.
    */
    public function clear_papers_temp()
    {
        $this->Paper_dataAccess->clear_papers_temp();
        set_top_msg('All papers deleted');
        redirect('element/entity_list/list_all_papers');
    }

    /**
     * responsible for permanently deleting papers and related data from the database
     */
    public function clear_papers()
    {
        $this->Paper_dataAccess->clear_papers();
        set_top_msg('All papers deleted');
        // set_top_msg('Clear papers cancelled');
        redirect('element/entity_list/list_all_papers');
    }

    /**
     * used when the user decides to cancel the operation of clearing papers and wants to restore the previously marked inactive records
     */
    public function cancel_clear_papers()
    {
        $this->Paper_dataAccess->cancel_clear_papers();
        set_top_msg('Clear papers cancelled');
        redirect('element/entity_list/list_all_papers');
    }

    //This function is responsible for rendering the form for adding or editing a bibtex paper in ReLiS.
    public function bibler_add_paper($data = "", $operation = 'new', $display_type = "normal")
    {
        $ref_table = "papers";
        if (admin_config($ref_table))
            $data['left_menu_admin'] = True;
        /*
         * charger la manière d'affichage du formulaire
         */
        $this->session->set_userdata('submit_mode', $display_type);
        /*
         * Récupération de la configuration(structure) de la table concerné
         */
        $table_config = get_table_config($ref_table);
        //print_test($table_config);
        $table_config['config_id'] = $ref_table;
        $type_op = $operation == 'new' ? "on_add" : "on_edit";
        /*
         * récupération des valeurs qui vont apparaitres dans les dropdown boxes
         */
        foreach ($table_config['fields'] as $k => $v) {
            if (!empty($v['input_type']) and $v['input_type'] == 'select') {
                if ($v['input_select_source'] == 'table' and ($v[$type_op] == 'enabled' or $v[$type_op] == 'disabled')) {
                    if (isset($table_config['fields'][$k]['multi-select']) and $table_config['fields'][$k]['multi-select'] == 'Yes') {
                        $table_config['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], False, False, True);
                    } else {
                        $table_config['fields'][$k]['input_select_values'] = $this->manager_lib->get_reference_select_values($v['input_select_values'], True, False);
                    }
                }
            }
        }
        /*
         * Prépartions des valeurs qui vont apparaitres dans le formulaire
         */
        $title_append = $table_config['reference_title_min'];
        $data['table_config'] = $table_config;
        /*
         * Titre de la page
         */
        if ($operation == 'new') {
            // La fonction qui va traiter l'enregistrement dans la DB;
            if (isset($table_config['entity_title']['add'])) {
                $data['page_title'] = lng($table_config['entity_title']['add']);
            } else {
                $data['page_title'] = lng('Add ' . $title_append);
            }
        } else {
            if (isset($table_config['entity_title']['edit'])) {
                $data['page_title'] = lng($table_config['entity_title']['edit']);
            } else {
                $data['page_title'] = lng('Edit ' . $title_append);
            }
        }
        $data['save_function'] = 'paper/bibler_save_paper';
        $data['operation_type'] = $operation;
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = get_top_button('back', 'Back', 'manage');
        /*
         * La vue qui va s'afficher
         */
        $data['page'] = 'paper/frm_paper_bibler';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        if ($display_type == 'modal') {
            $this->load->view('general/frm_reference_modal', $data);
        } else {
            $this->load->view('shared/body', $data);
        }
    }

    //function is used to edit an existing bibtex paper.
    public function bibler_edit_paper($ref_id, $display_type = "normal")
    {
        $ref_table = 'papers';
        /*
         * Récupération de la configuration(structure) de la table de l'element
         */
        $table_config = get_table_config($ref_table);
        /*
         * Appel de la fonction du model pour récupérer la ligne à modifier
         */
        $data['content_item'] = $this->DBConnection_mdl->get_row_details($ref_table, $ref_id);
        /*
         * Appel de la fonction d'affichage du formulaire
         */
        $this->add_paper($data, 'edit', $display_type);
    }

    //saving the submitted form data for a bibtex paper
    public function bibler_save_paper()
    {
        /*
         * Récuperation des valeurs soumis dans le formulaire
         */
        $post_arr = $this->input->post();
        print_test($post_arr);
        $operation_type = $post_arr['operation_type'];
        if ($operation_type == 'edit') {
            //Modification
        } else {
            //Ajout d'un nouveau papier
        }
    }

    //displaying a minimal version of the paper details
    public function display_paper_min($ref_id, $display_type = 'det')
    {
        //	print_test(get_paper_screen_result($ref_id));
        $ref_table = "papers";
        /*
         * Récupération de la configuration(structure) de la table des papiers
         */
        $table_config = get_table_configuration($ref_table);
        /*
         * Appel de la fonction  récupérer les informations sur le papier afficher
         */
        $table_config['current_operation'] = 'detail_paper';
        $paper_data = $this->manager_lib->get_detail($table_config, $ref_id);
        //	print_test($paper_data);
        /*
         * Préparations des informations à afficher
         */
        //venue
        $venue = "";
        $authors = "";
        foreach ($paper_data as $key => $value) {
            if ($value['field_id'] == 'venueId' and !empty($value['val2'][0])) {
                $venue = $value['val2'][0];
            } elseif ($value['field_id'] == 'authors' and !empty($value['val2'])) {
                if (count($value['val2']) > 1) {
                    $authors = '<table class="table table-hover" ><tr><td> ' . $value['val2'][0] . '</td></tr>';
                    foreach ($value['val2'] as $k => $v) {
                        if ($k > 0) {
                            $authors .= "<tr><td> " . $v . '</td></tr>';
                        }
                    }
                    $authors .= "</table>";
                } else {
                    $authors = " : " . $value['val2'][0];
                }
            }
        }
        $content_item = $this->DBConnection_mdl->get_row_details('get_detail_papers', $ref_id, TRUE);
        //get_detail_paper
        //print_test($content_item);
        $paper_name = $content_item['bibtexKey'] . " - " . $content_item['title'];
        $paper_excluded = False;
        if ($content_item['paper_excluded'] == '1') {
            $paper_excluded = True;
        }
        $data['paper_excluded'] = $paper_excluded;
        $item_data = array();
        $array['title'] = $content_item['bibtexKey'] . " - " . $content_item['title'];
        if (!empty($content_item['doi'])) {
            $paper_link = $content_item['doi'];
            if ((strpos($paper_link, 'http://') === FALSE) && (strpos($paper_link, 'https://') === FALSE)) {
                $paper_link = "//" . $paper_link;
            }
            $array['title'] .= '<ul class="nav navbar-right panel_toolbox">
				<li>
					<a title="Go to the page" href="' . $paper_link . '" target="_blank" >
				 		<img src="' . base_url() . 'cside/images/pdf.jpg"/>
					</a>
				</li>
				</ul>';
        }
        array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Abstract') . " :</b> <br/><br/>" . $content_item['abstract'];
        array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Preview') . " :</b> <br/><br/>" . $content_item['preview'];
        array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Venue') . " </b> " . $venue;
        //array_push($item_data, $array);
        $array['title'] = "<b>" . lng('Authors') . " </b> " . $authors;
        //array_push($item_data, $array);
        $data['item_data'] = $item_data;
        /*
         * Création des boutons qui vont s'afficher en haut de la page (top_buttons)
         */
        $data['top_buttons'] = "";
        $data['top_buttons'] .= get_top_button('back', 'Back', 'home');
        /*
         * Titre de la page
         */
        $data['page_title'] = lng('Paper');
        /*
         * La vue qui va s'afficher
         */
        $data['page'] = 'paper/display_paper_min';
        /*
         * Chargement de la vue avec les données préparés dans le controleur suivant le type d'affichage : (popup modal ou pas)
         */
        $this->load->view('shared/body', $data);
    }

    //enables retrieving paper information and preview data from the Bibler web services and save the paper
    private function get_bibler_result($bibtex, $operation = "single")
    {
        //clean the bibtex content
        $bibtex = strstr($bibtex, '@');
        $error = 1;
        $error_msg = "";
        $paper_array = array();
        $paper_preview_sucess = array(); //for import only
        $paper_preview_exist = array(); //for import only
        $paper_preview_error = array(); //for import only
        $init_time = microtime();
        $i = 1;
        $res = "init";
        while ($i < 10) { //up to ten attempt to connect to server if the connection does not work
            if ($operation == 'endnote') {
                $res = $this->biblerproxy_lib->importendnotestringforrelis($bibtex);
            } else {
                $res = $this->biblerproxy_lib->importbibtexstringforrelis($bibtex);
            }
            $correct = False;
            //if there is an error messag in the result retry
            if (strpos($res, 'Internal Server Error') !== false or empty($res)) {
                $i++;
            } else {
                //if there no error messag in the result retry
                $correct = True;
                $i = 20;
            }
            //usleep(500);
        }
        $end_time = microtime();
        ini_set('auto_detect_line_endings', TRUE);
        if ($correct) {
            $Tres = json_decode($res, True);
            if (json_last_error() === JSON_ERROR_NONE) {
                if ($operation == 'single') {
                    //for single add just consider the first element
                    if (!empty($Tres['papers'][0])) {
                        $Tres = $Tres['papers'][0];
                    }
                    $result['bibtext'] = $bibtex;
                    $paper_array = array();
                    if (
                        !empty($Tres['result_code'])
                        and !empty($Tres['entry']['entrykey'])
                    ) {
                        $error = 0;
                        $year = !empty($Tres['entry']['year']) ? $Tres['entry']['year'] : "";
                        $paper_array['bibtexKey'] = str_replace('\\', '', $Tres['entry']['entrykey']);
                        $title = !empty($value['entry']['title']) ? $value['entry']['title'] : "";
                        $title = str_replace('{', '', $title);
                        $title = str_replace('\\', '', $title);
                        $paper_array['title'] = str_replace('}', '', $title);
                        $paper_array['preview'] = !empty($Tres['preview']) ? $Tres['preview'] : "";
                        $paper_array['bibtex'] = !empty($Tres['bibtex']) ? $Tres['bibtex'] : "";
                        $paper_array['abstract'] = !empty($Tres['entry']['abstract']) ? $Tres['entry']['abstract'] : "";
                        $paper_array['doi'] = !empty($Tres['entry']['paper']) ? $Tres['entry']['paper'] : "";
                        $paper['venue'] = !empty($value['venue_full']) ? $value['venue_full'] : "";
                        $paper_array['year'] = $year;
                        $paper_array['authors'] = !empty($Tres['authors']) ? $Tres['authors'] : "";
                    } else {
                        $msg = (!empty($Tres['result_msg']) ? $Tres['result_msg'] : "");
                        $error_msg .= "Error: check your Bibtext .<br/>" . $msg;
                    }
                } else {
                    if (empty($Tres['error']) and !empty($Tres['papers'])) {
                        //exit;
                        $paper = array();
                        $i_ok = 1;
                        $i_ok_pupli = 1;
                        $i_Nok = 1;
                        foreach ($Tres['papers'] as $key => $value) {
                            if (!empty($value['entry']['entrykey'])) {
                                if (!empty($value['result_code'])) {
                                    $error = 0;
                                    $year = !empty($value['entry']['year']) ? $value['entry']['year'] : "";
                                    /*	if(!empty($value['venue_full'])){
                                                                                           $venue_id=$this->add_venue($value['venue_full'],$year);
                                                                                           $paper['venueId']=$venue_id;
                                                                                           }*/
                                    $paper['bibtexKey'] = str_replace('\\', '', $value['entry']['entrykey']);
                                    $title = !empty($value['entry']['title']) ? $value['entry']['title'] : "";
                                    $title = str_replace('{', '', $title);
                                    $title = str_replace('\\', '', $title);
                                    $paper['title'] = str_replace('}', '', $title);
                                    $paper['preview'] = !empty($value['preview']) ? $value['preview'] : "";
                                    $paper['bibtex'] = !empty($value['bibtex']) ? $value['bibtex'] : "";
                                    $paper['abstract'] = !empty($value['entry']['abstract']) ? $value['entry']['abstract'] : "";
                                    $paper['doi'] = !empty($value['entry']['paper']) ? $value['entry']['paper'] : "";
                                    $paper['venue'] = !empty($value['venue_full']) ? $value['venue_full'] : "";
                                    $paper['year'] = $year;
                                    $paper['authors'] = !empty($value['authors']) ? $value['authors'] : "";
                                    array_push($paper_array, $paper);
                                    if ($this->paper_exist($paper)) {
                                        array_push($paper_preview_exist, array('i' => $i_ok_pupli, 'key' => $paper['bibtexKey'], 'preview' => $paper['preview']));
                                        $i_ok_pupli++;
                                    } else {
                                        array_push($paper_preview_sucess, array('i' => $i_ok, 'key' => $paper['bibtexKey'], 'preview' => $paper['preview']));
                                        $i_ok++;
                                    }
                                } else {
                                    $preview = !empty($value['preview']) ? $value['preview'] : "";
                                    $bibtexKey = !empty($value['bibtexKey']) ? str_replace('\\', '', $value['entry']['entrykey']) : "";
                                    array_push(
                                        $paper_preview_error,
                                        array(
                                            'i' => $i_Nok,
                                            'key' => $bibtexKey,
                                            'preview' => $preview,
                                            'msg' => $value['result_msg']
                                        )
                                    );
                                    $i_Nok++;
                                }
                            }
                        }
                    } else {
                        $error_msg .= "Error: No papers found.<br/>";
                        $error = 0;
                    }
                    //$paper_array=$Tres;
                }
            } else {
                $json_error = "";
                switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                        $json_error = 'No errors';
                        break;
                    case JSON_ERROR_DEPTH:
                        $json_error = 'Maximum stack depth exceeded';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        $json_error = 'Underflow or the modes mismatch';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        $json_error = 'Unexpected control character found';
                        break;
                    case JSON_ERROR_SYNTAX:
                        $json_error = 'Syntax error, malformed JSON';
                        break;
                    case JSON_ERROR_UTF8:
                        $json_error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                        break;
                    default:
                        $json_error = 'Unknown error';
                        break;
                }
                $error_msg .= "JSON Error : " . $json_error . ".<br/>";
            }
        } else {
            $error_msg .= "Unable to connect to Bibler web service.<br/>";
            $this->add_paper_bibtex($data);
        }
        $result['error'] = $error;
        $result['error_msg'] = $error_msg;
        $result['paper_array'] = $paper_array;
        $result['paper_preview_sucess'] = $paper_preview_sucess;
        $result['paper_preview_exist'] = $paper_preview_exist;
        $result['paper_preview_error'] = $paper_preview_error;
        return $result;
    }

    //test the interaction with remote BibTex system by invoking different methods of the Biblerproxy_lib library
    public function test_bibler()
    {
        $bibtex = "@INPROCEEDINGS{4631716,
author={N. Omar and S. S. Hasbullah},
booktitle={2008 International Symposium on Information Technology},
title={SRL TOOL: Heuristics-based Semantic Role Labeling through natural language processing},
year={2008},
volume={2},
pages={1-7},
abstract={The Semantic Role Labeling (SRL Tool) is developed to label the semantic roles that exist in English sentences. This paper proposed a set of new heuristics to assist the semantic role labeling using natural language processing. The preliminary result shows that the use of heuristics can improve the process of assigning the correct semantic roles. This application tool is useful for researchers in Natural Language processing field and also for experts or students in Linguistics.},
keywords={Books;Computational linguistics;Computer science;Humans;Information science;Joining processes;Labeling;Lifting equipment;Natural language processing;Testing},
doi={10.1109/ITSIM.2008.4631716},
ISSN={2155-8973},
month={Aug},}
		
";
        $bibtex = "@INPROCEEDINGS{4631716,
author={N. Omar and S. S. Hasbullah},
booktitle={2008 International Symposium on Information Technology},
title={SRL TOOL: Heuristics-based Semantic Role Labeling through natural language processing},
year={2008},
volume={2},
pages={1-7},
abstract={The Semantic Role Labeling (SRL Tool) is developed to label the semantic roles that exist in English sentences. This paper proposed a set of new heuristics to assist the semantic role labeling using natural language processing. The preliminary result shows that the use of heuristics can improve the process of assigning the correct semantic roles. This application tool is useful for researchers in Natural Language processing field and also for experts or students in Linguistics.},
keywords={Books;Computational linguistics;Computer science;Humans;Information science;Joining processes;Labeling;Lifting equipment;Natural language processing;Testing},
doi={10.1109/ITSIM.2008.4631716},
ISSN={2155-8973},
month={Aug},}
		
";
        $bibdtex = "@article {1519,
	title = {Classification d{\textquoteright}offres d{\textquoteright}emploi},
	year = {2017},
	month = {2017},
	type = {Technical report},
	abstract = {Les ressources humaines utilisent de plus en plus les donn{\'e}es intelligentes et les techniques du big data pour faciliter le recrutement. Ainsi, gr{\^a}ce aux profils des r{\'e}seaux sociaux, les recruteurs peuvent identifier des candidats potentiels qui ne sont pas actifs en termes de recherche d{\textquoteright}emploi mais qui pourraient {\^e}tre quand m{\^e}me int{\'e}ress{\'e}s par une opportunit{\'e}. Leur int{\'e}r{\^e}t pour une offre non sollicit{\'e}e est d{\textquoteright}autant plus grand lorsque cette derni{\`e}re correspond bien {\`a} leur profil et {\`a} leur secteur d{\textquoteright}activit{\'e}. Afin d{\textquoteright}am{\'e}liorer les r{\'e}sultats d{\textquoteright}un tel syst{\`e}me de recommandation appariant offres d{\textquoteright}emploi et profils suivant les comp{\'e}tences et les exp{\'e}riences requises, nous proposons de d{\'e}tecter automatiquement le secteur d{\textquoteright}activit{\'e}s des offres {\`a} l{\textquoteright}aide de techniques d{\textquoteright}apprentissage supervis{\'e}.
},
	keywords = {Automatic classification, E-recruitment, Recommendation systems},
	url = {http://rali.iro.umontreal.ca/rali/node/1519/},
	pdf = {http://rali.iro.umontreal.ca/rali/sites/default/files/publis/classification_offre_emploi.pdf},
	author = {Annette Casagrande and Fabrizio Gotti and Guy Lapalme}
}
";
        //print_test($bibtex);
        $init_time = microtime();
        $i = 1;
        $res = "init";
        while ($i < 10) {
            //$res=$this->biblerproxy_lib->addEntry($bibtex);
            //$res=$this->biblerproxy_lib->bibtextobibtex($bibtex);
            //$res=$this->biblerproxy_lib->bibtextosql($bibtex);
            //$res=$this->biblerproxy_lib->addEntry($bibtex);
            //$res=$this->biblerproxy_lib->previewEntry($bibtex);
            //$res=$this->biblerproxy_lib->bibtextocsv($bibtex);
            //$res=$this->biblerproxy_lib->bibtextohtml($bibtex);
            //$res=$this->biblerproxy_lib->formatBibtex($bibtex);
            $res = $this->biblerproxy_lib->createentryforreliS($bibtex);
            echo "zzzz";
            print_test($res);
            echo "yyyy";
            $correct = False;
            if (strpos($res, 'Internal Server Error') !== false) {
                echo " error - " . $i;
                $i++;
            } else {
                echo " ok - " . $i;
                $correct = True;
                $i = 20;
            }
            usleep(500);
        }
        $end_time = microtime();
        print_test($res);
        //	echo "<h1>".($end_time - $init_time)."</h1>";
        ini_set('auto_detect_line_endings', TRUE);
        if ($correct) {
            //$fp = fopen('test_'.time().'.txt', 'w+');
            //fputs($fp, $res);
            $res = str_replace("True,", "'True',", $res);
            $res = str_replace("False,", "'False',", $res);
            $res = $this->biblerproxy_lib->fixJSON($res);
            $Tres = json_decode($res, True);
            if (json_last_error() === JSON_ERROR_NONE) {
                //do something with $json. It's ready to use
                //print_test();
                echo "<pre>";
                print_r($Tres);
                echo "</pre>";
            } else {
                //yep, it's not JSON. Log error or alert someone or do nothing
                echo json_last_error();
                echo "<p>Not a valid Json</p>";
            }
        }
        echo "<hr/>";
    }
}