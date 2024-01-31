<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Config extends CI_Controller
{
	/*
	 * En cours de réalisation utilisé pour la géneralisation de l'application
	 */
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		redirect('manage/liste_ref/class_scheme');
	}

	//function is used to generate the configuration and reference tables for the classification structure.
	public function generate_config()
	{
		echo "<h1>Code and DB generation</h1>";
		////get main fields
		$data = array();
		$tab_config = array();
		$tab_config_new = array();
		$current_conf = 'main';
		$result = $this->get_subcategories('main');
		$configurations = array('main');
		$configurations_all['main']['title'] = 'Classification';
		$configurations_all['main']['type'] = 'main';
		$configurations_all['main']['parent'] = 'none';
		$configurations_all['main']['principal_field'] = 'class_paper_id';
		//$configurations['main']['principal_field']='class_paper_id';
		print_test($result);
		foreach ($result as $key => $value) {
			$label = $value['scheme_label'];
			$data[$label]['main'] = $value;
			$res_conf = $this->get_configuration_structure($value);
			print_test($res_conf);
			$res_conf['config_category'] = 'main';
			$tab_config['main'][$label] = $res_conf;
			if ($res_conf['type'] == 'simple_drilldown' or $res_conf['type'] == 'multivalues' or $res_conf['type'] == 'multivalues_drilldown')
				$label = 'ex_' . $label;
			$tab_config_new[$label] = $res_conf;
			//echo "<h1> $label ".in_array($res_conf['subcategory']['name'], $configurations)."</h1>";
			while (!empty($res_conf['subcategory']) and (!in_array($res_conf['subcategory']['name'], $configurations))) {
				array_unshift($configurations, $res_conf['subcategory']['name']);
				$configurations_all[$res_conf['subcategory']['name']]['title'] = $res_conf['subcategory']['title'];
				$configurations_all[$res_conf['subcategory']['name']]['type'] = $res_conf['subcategory']['type'];
				$configurations_all[$res_conf['subcategory']['name']]['parent'] = $res_conf['config_category'];
				foreach ($res_conf['subcategory']['fields'] as $k => $v) {
					//print_test($v);
					$label1 = $v['scheme_label'];
					//$data[$label1]['main']=$v;
					$res_conf1 = $this->get_configuration_structure($v, $res_conf['subcategory']['name']);
					$res_conf1['config_category'] = $res_conf['subcategory']['name'];
					$tab_config[$res_conf['subcategory']['name']][$label1] = $res_conf1;
					if (!empty($res_conf1['principal_field']) and $res_conf1['principal_field'] == 'yes') {
						$configurations_all[$res_conf['subcategory']['name']]['principal_field'] = $res_conf1['field_label'];
					}
					if ($res_conf1['type'] == 'simple_drilldown' or $res_conf1['type'] == 'multivalues' or $res_conf1['type'] == 'multivalues_drilldown')
						$label1 = 'ex_' . $label1;
					$tab_config_new[$label1] = $res_conf1;
				}
			}
		}
		//print_test($configurations);
		//print_test($configurations_all);
		//print_test($tab_config_new);
		//	print_test($tab_config);
		//	print_test($configurations);
		//print_test($data);
		//	exit;
		echo "<h1>classification structure</h1>";
		$fin_result = $this->get_final_configuration($tab_config_new, $configurations_all);
		print_test($fin_result);
		echo "<h1>Configuration  tables</h1>";
		foreach ($fin_result['config'] as $key => $value) {
			$sql['$key'] = $this->create_table_config($value);
			echo $sql['$key'] . "<br/><br/>";
		}
		echo "<h1>reference tables</h1>";
		foreach ($fin_result['ref_tables'] as $key => $value) {
			$ref_sql['$key'] = $this->create_ref_table($key, $value);
			echo $ref_sql['$key'] . "<br/><br/>";
		}
	}

	//retrieves subcategories from the database based on the provided category name and returns the result as an array.
	private function get_subcategories($cat)
	{
		$sql = "select * from classification_scheme WHERE scheme_parent LIKE '" . $cat . "' AND scheme_active=1 ORDER BY scheme_order ASC";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}

	//generate the configuration structure for categories and subcategories by determining field details
	private function get_configuration_structure($value, $parent = "main")
	{
		//print_test($value);
		$result = array();
		$label = $parent == 'main' ? $value['scheme_label'] : $parent . "_" . $value['scheme_label'];
		$field_type = !empty($value['scheme_size']) ? $value['scheme_type'] : 'string';
		$subcategories = $this->get_subcategories($label);
		$is_drilldown = !empty($subcategories);
		$is_multivalues = (!empty($value['scheme_number_of_values']) and $value['scheme_number_of_values'] != 1);
		if (!empty($value['principal_field'])) {
			$result['principal_field'] = $value['principal_field'];
		}
		$result['field_label'] = $value['scheme_label'];
		$result['field_title'] = $value['scheme_title'];
		$result['field_type'] = 'text';
		$result['field_value'] = 'normal';
		$result['input_type'] = 'text';
		if (!empty($value['scheme_mandatory'])) {
			$result['mandatory'] = "mandatory";
		}
		$result['number_of_values'] = '1';
		$result['field_size'] = !empty($value['scheme_size']) ? $value['scheme_size'] : '11';
		$result['on_list'] = 'show';
		$result['on_view'] = 'show';
		$result['on_add'] = 'enabled';
		$result['on_edit'] = 'enabled';
		$result['subcategory'] = array();
		if (!$is_drilldown and !$is_multivalues) { //not drilldown
			if ($value['scheme_category'] == 'free') {
				if ($value['scheme_type'] == 'boolean') //yes_no
				{
					$result['type'] = 'boolean';
					$result['field_type'] = 'text';
					$result['field_value'] = '0_1';
					$result['field_size'] = '1';
					$result['input_type'] = 'select';
					$result['input_select_source'] = 'yes_no';
					$result['input_select_values'] = '';
				} else {
					$result['type'] = 'free';
					if ($field_type == 'real' or $field_type == 'int') {
						$result['field_type'] = 'number';
					} elseif ($field_type == 'text') {
						$result['input_type'] = 'textarea';
					} elseif ($field_type == 'color' or $field_type == 'date') {
						$result['input_type'] = $field_type;
					}
				}
			} elseif ($value['scheme_category'] == 'static') {
				$result['type'] = 'static';
				$result['input_type'] = 'select';
				$result['input_select_source'] = 'array';
				$Tvalues = explode(';', $value['scheme_source']);
				foreach ($Tvalues as $k => $v) {
					$Tvalues[$k] = $v;
					$val[$v] = $v;
				}
				$result['input_select_values'] = $val;
			} else { //dynamic
				$result['type'] = 'dynamic';
				$source_config = $value['scheme_source'];
				$main_field = $value['scheme_source_main_field'];
				$result['input_type'] = 'select';
				$result['input_select_source'] = 'table';
				$result['input_select_values'] = $source_config;
				$result['main_field'] = $main_field;
			}
		} elseif ($is_drilldown and !$is_multivalues) {
			$result['type'] = 'simple_drilldown';
			$result['input_type'] = 'select';
			$result['input_select_source'] = 'table';
			$result['input_select_source_type'] = 'drill_down';
			$result['input_select_values'] = $label;
			$temp_val = $value;
			//$temp_val['scheme_label']=$value['scheme_label']."_".$value['scheme_label'];
			$temp_val['principal_field'] = 'yes';
			array_unshift($subcategories, $temp_val);
			$result['subcategory']['fields'] = $subcategories;
			$result['subcategory']['type'] = 'drill_down';
			$result['subcategory']['name'] = $value['scheme_label'];
			$result['subcategory']['title'] = $value['scheme_title'];
		} elseif ($is_multivalues) {
			$result['type'] = 'multivalues';
			$result['input_type'] = 'select';
			$result['input_select_source'] = 'table';
			$result['number_of_values'] = '*';
			$result['input_select_values'] = $label;
			$result['input_select_key_field'] = 'ZZZZ determiner';
			$temp_val = $value;
			$temp_val['principal_field'] = 'yes';
			//$temp_val['scheme_label']=$value['scheme_label']."_".$value['scheme_label'];
			$temp_val['scheme_number_of_values'] = 1;
			array_unshift($subcategories, $temp_val);
			//external key
			$result['subcategory']['fields'] = $subcategories;
			$result['subcategory']['name'] = $value['scheme_label'];
			$result['subcategory']['type'] = 'multivalue';
			$result['subcategory']['title'] = $value['scheme_title'];
			if ($is_drilldown) {
				$result['type'] = 'multivalues_drilldown';
				$result['input_select_source_type'] = 'drill_down';
			}
		}
		//print_test($result);
		return $result;
	}

	//generating the final configuration structure based on the configuration details and the list of reference tables
	private function get_final_configuration($conf_stucture, $configurations_all)
	{
		$config = array();
		$reference_tables_list = array();
		$config['classification'] = $this->get_classification_model();
		//fill principal table
		$fields = array();
		foreach ($conf_stucture as $key => $value) {
			$temp_val = $value;
			$config_category = $value['config_category'];
			unset($temp_val['subcategory']);
			//static , yes no and and  free OK;
			if ($value['type'] == 'dynamic') {
				if (!isset($conf_stucture[$value['input_select_values']]) or $value['input_select_values'] == 'new') //reference_table
				{
					if ($value['input_select_values'] == 'new') {
						$reference_table = "ref_" . $key;
					} else {
						$reference_table = "ref_" . $value['input_select_values'];
					}
					$reference_tables_list[$reference_table] = $value['field_title'];
					$temp_val['input_select_values'] = $reference_table . ";ref_value";
				}
				$temp_val['compute_result'] = 'yes';
			} elseif ($value['type'] == 'simple_drilldown' or $value['type'] == 'multivalues' or $value['type'] == 'multivalues_drilldown') {
				$temp_val['input_select_values'] = $value['field_label'] . ";" . $configurations_all[$value['field_label']]['principal_field'];
				if ($value['type'] == 'multivalues' or $value['type'] == 'multivalues_drilldown') {
					if ($configurations_all[$value['field_label']]['parent'] == 'main') {
						$input_select_key_field = $value['field_label'] . "_classification_id";
					} else {
						//not supported now
						$input_select_key_field = $value['field_label'] . "_" . $configurations_all[$value['field_label']]['parent'];
						$input_select_key_field = "";
					}
					$temp_val['input_select_key_field'] = $input_select_key_field;
				}
			}
			if ($config_category == 'main') {
				$config['classification']['fields']['class_' . $key] = $temp_val;
			} else {
				if (empty($config[$config_category])) {
					//create new configuration
					$config[$config_category] = $this->get_configuration_model($config_category, $configurations_all[$config_category]['title'], $configurations_all[$config_category]['type'], $configurations_all[$config_category]['parent']);
				}
				$config[$config_category]['fields'][$key] = $temp_val;
			}
		}
		$result['config'] = $config;
		$result['ref_tables'] = $reference_tables_list;
		//	print_test($result);
		return $result;
	}

	//defines the configuration structure for the "classification" table, including its table configuration, field configurations, and display options
	private function get_classification_model()
	{
		$config['table_name'] = 'classification';
		$config['table_id'] = 'class_id';
		$config['table_active_field'] = 'class_active'; //to detect deleted records
		$config['reference_title'] = 'Classifications';
		$config['reference_title_min'] = 'Classification';
		//Concerne l'affichage
		$config['order_by'] = 'class_id ASC '; //mettre la valeur à mettre dans la requette
		//$config['search_by']='class_year';// separer les champs par virgule
		$config['links']['edit'] = array(
			'label' => 'Edit',
			'title' => 'Edit classification',
			'on_list' => True,
			'on_view' => True
		);
		$config['links']['view'] = array(
			'label' => 'View',
			'title' => 'View',
			'on_list' => True,
			'on_view' => True
		);
		$fields['class_id'] = array(
			'field_title' => '#',
			'field_type' => 'number',
			'field_value' => 'auto_increment',
			//pour l'affichage
			'on_add' => 'hidden',
			'on_edit' => 'hidden',
			'on_list' => 'show',
			'on_view' => 'hidden',
		);
		$fields['class_paper_id'] = array(
			'field_title' => 'Paper',
			'field_type' => 'number',
			'field_value' => 'normal',
			'input_type' => 'select', //select
			'input_select_source' => 'table',
			//'input_select_values'=>'papers;title',//the reference table and the field to be displayed
			'input_select_values' => 'papers;CONCAT_WS(" - ",bibtexKey,title)', //the reference table and the field to be displayed
			'field_size' => 11,
			'mandatory' => ' mandatory ',
			//pour l'affichage
			'on_add' => 'enabled',
			'on_edit' => 'enabled',
			'on_list' => 'show'
		);
		$fields['class_active'] = array(
			'field_title' => 'Active',
			'field_type' => '0_1',
			'field_value' => 'normal',
			'on_add' => 'not_set',
			'on_edit' => 'not_set',
			'on_list' => 'hidden',
			'on_view' => 'hidden'
		);
		$config['fields'] = $fields;
		return $config;
	}

	//generates the configuration structure for a specific configuration table based on the provided label, title, source, and parent. 
	private function get_configuration_model($conf_label, $conf_title, $conf_source = "multivalue", $conf_parent = "none")
	{
		$config['table_name'] = '' . $conf_label;
		$config['table_id'] = $conf_label . '_id';
		$config['table_active_field'] = $conf_label . '_active'; //to detect deleted records
		$config['reference_title'] = $conf_title;
		$config['reference_title_min'] = $conf_title;
		//Concerne l'affichage
		$config['order_by'] = $conf_label . '_id ASC '; //mettre la valeur à mettre dans la requette
		$config['links']['edit'] = array(
			'label' => 'Edit',
			'title' => 'Edit classification',
			'on_list' => True,
			'on_view' => True
		);
		$config['links']['view'] = array(
			'label' => 'View',
			'title' => 'View',
			'on_list' => True,
			'on_view' => True
		);
		$fields[$conf_label . '_id'] = array(
			'field_title' => '#',
			'field_type' => 'number',
			'field_value' => 'auto_increment',
			//pour l'affichage
			'on_add' => 'hidden',
			'on_edit' => 'hidden',
			'on_list' => 'show',
			'on_view' => 'hidden',
		);
		if ($conf_source == 'multivalue' and $conf_parent == 'main') {
			$fields[$conf_label . '_classification_id'] = array(
				'field_title' => 'Classification',
				'field_type' => 'number',
				'field_value' => 'normal',
				'field_size' => 11,
				'mandatory' => ' mandatory ',
				'input_type' => 'select',
				'input_select_source' => 'table',
				'input_select_values' => 'classification;class_paper_id', //the reference table and the field to be displayed
				'compute_result' => 'no',
				'on_add' => 'hidden',
				'on_edit' => 'hidden',
				'on_list' => 'hidden',
				'on_view' => 'hidden'
			);
		}
		$fields[$conf_label . '_active'] = array(
			'field_title' => 'Active',
			'field_type' => '0_1',
			'field_value' => 'normal',
			'on_add' => 'not_set',
			'on_edit' => 'not_set',
			'on_list' => 'hidden',
			'on_view' => 'hidden'
		);
		$config['fields'] = $fields;
		return $config;
	}

	//dynamically generates the SQL query for creating a configuration table based on the provided configuration array. 
	private function create_table_config($config)
	{
		//	print_test($config);
		$table_id = $config['table_id'];
		$del_line = "DROP TABLE IF EXISTS " . $config['table_name'] . ";";
		$sql = "CREATE TABLE IF NOT EXISTS " . $config['table_name'] . " (
		$table_id int(11) NOT NULL AUTO_INCREMENT,";
		$field_default = "   ";
		$field_type = "  ";
		foreach ($config['fields'] as $key => $value) {
			if ($key != $table_id and $key != $config['table_active_field']) {
				//start with select
				if ($value['input_type'] == 'select') {
					if ($value['input_select_source'] == 'array') { //static
						$i = 1;
						$field_type = " enum(";
						foreach ($value['input_select_values'] as $k => $v) {
							if ($i == 1)
								$field_type .= "'" . $k . "'";
							else
								$field_type .= ",'" . $k . "'";
							$i++;
						}
						$field_type .= ") ";
						$field_default = "   DEFAULT NULL ";
					} else { //dynamic
						$field_type = " int(11) ";
						$field_default = "  DEFAULT '0' ";
					}
				} else { //Free category
					if (!empty($value['field_value']) and $value['field_value'] == '0_1') { //Yes_no
					} elseif ($value['field_type'] == 'number') {
						$field_type = " int(" . $value['field_size'] . ") ";
						$field_default = "   DEFAULT '0' ";
					} else {
						$field_type = " varchar(" . $value['field_size'] . ") ";
						$field_default = "   DEFAULT NULL ";
					}
				}
				if (!(isset($value['type']) and $value['type'] == 'multivalues' and $value['type'] == 'multivalues_drilldown')) {
					$sql .= " " . $key . " $field_type $field_default,";
				}
			}
		}
		$sql .= " " . $config['table_active_field'] . " int(1) NOT NULL DEFAULT '1',";
		$sql .= " PRIMARY KEY ($table_id)";
		$sql .= ") ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		return "$del_line $sql";
	}

	//dynamically generates the SQL query for creating a reference table based on the provided reference configuration and description
	private function create_ref_table($ref_conf, $desc)
	{
		$table_name = "z" . $ref_conf;
		//	
		$del_line = "DROP TABLE IF EXISTS " . $table_name . ";";
		$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
		`ref_id` int(11) NOT NULL AUTO_INCREMENT,
		  `ref_value` varchar(50) NOT NULL,
		  `ref_desc` varchar(250) DEFAULT NULL,
		  `ref_active` int(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`ref_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		//Add in the list of reference tables
		$req = "select * from ref_tables where reftab_active=1 AND reftab_label='$ref_conf'";
		$res = $this->db->query($req)->result_array();
		//print_test($res);
		//if(empty($res))
		$sql .= " INSERT INTO ref_tables (reftab_label, reftab_table, reftab_desc, reftab_active) VALUES
('" . $ref_conf . "', '" . $table_name . "', '" . $desc . "', 1);";
		return "$del_line $sql";
	}

	//updates the edition mode in the session
	public function update_edition_mode($value = "no")
	{
		$this->session->set_userdata('language_edit_mode', $value);
		redirect('element/entity_list/list_str_mng');
	}
}