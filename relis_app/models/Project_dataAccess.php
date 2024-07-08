<?php
/* This class is responsible for accessing the database to get project data*/

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Project_dataAccess extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function update_project_public_field($op, $project_id)
    {
        $sql = "UPDATE  projects SET project_public =  $op WHERE  project_id = $project_id ";
        $res_sql = $this->manage_mdl->run_query($sql, false, 'default');
        return $res_sql;
    }

    function select_project_id_by_label($project_short_name)
    {
        $sql = "SELECT project_id from projects where project_label LIKE '" . $project_short_name . "' AND project_active=1 ";
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    function create_project_db($database_name)
    {
        $sql = "CREATE DATABASE $database_name";
        $res_sql = $this->manage_mdl->run_query($sql);
        return $res_sql;
    }

    function get_project_config($project_label){
			
        $sql="SELECT *  from  projects WHERE  	project_active=1 AND project_label LIKE '$project_label' ";
        
        $result=$this->db->query ( $sql )->row_array ();
        
        return $result;
    }

    function update_installation_info($project_short_name)
    {
        $sql_install_info = "UPDATE installation_info SET  install_active=0 where install_active = 1 ; ";
        $res_sql = $this->manage_mdl->run_query($sql_install_info, false, $project_short_name);
        return $res_sql;
    }

    function insert_installation_info($ref_tables, $generated_tables, $foreign_key_constraints, $project_short_name)
    {
        $sql_install_info = "INSERT INTO installation_info (reference_tables,generated_tables,foreign_key_constraint) VALUES ('" . json_encode($ref_tables) . "','" . json_encode($generated_tables) . "','" . json_encode($foreign_key_constraints) . "')   ; ";
        //echo $sql_install_info;
        $res_sql = $this->manage_mdl->run_query($sql_install_info, false, $project_short_name);
        return $res_sql;
    }

    function insert_into_project($project_short_name, $project_title, $creator)
    {
        $sql_add_project = "INSERT INTO projects  (project_label,project_title,project_description,project_creator) VALUES ('" . $project_short_name . "','" . $project_title . "','" . $project_title . "'," . $creator . ")";
        $res_sql = $this->manage_mdl->run_query($sql_add_project, false, 'default');
        return $res_sql;
    }

    function insert_into_userproject($creator, $project_id)
    {
        $sql_add_user_project = "INSERT INTO userproject  (	user_id,project_id,	user_role,added_by	 )
									VALUES ('" . $creator . "','" . $project_id . "','Project admin','" . $creator . "')";
        // echo $sql_add_user_project;
        $res_sql = $this->manage_mdl->run_query($sql_add_user_project, false, 'default');
        return $res_sql;
    }

    function update_config($editor_url, $editor_generated_path, $project_short_name)
    {
        $sql = "UPDATE  config SET  editor_url='" . $editor_url . "' , editor_generated_path= '" . $editor_generated_path . "'";
        $res_sql = $this->manage_mdl->run_query($sql, false, $project_short_name);
        return $res_sql;
    }

    function update_userproject($project_id)
    {
        $sql = "UPDATE userproject SET userproject_active =0 where  project_id=$project_id";
        $res_sql = $this->manage_mdl->run_query($sql);
        return $res_sql;
    }

    function drop_project_db($database_name)
    {
        $sql = "DROP DATABASE $database_name";
        $res_sql = $this->manage_mdl->run_query($sql);
        return $res_sql;
    }

    function drop_table_if_existe($table_name, $target_db)
    {
        $del_line = "DROP TABLE IF EXISTS " . $table_name . ";";
        $res_sql = $this->manage_mdl->run_query($del_line, False, $target_db);
        return $res_sql;
    }

    function create_table_if_not_existe($table_name, $target_db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
		`ref_id` int(11) NOT NULL AUTO_INCREMENT,
		  `ref_value` varchar(50) NOT NULL,
		  `ref_desc` varchar(250) DEFAULT NULL,
		  `ref_active` int(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`ref_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        $res_sql = $this->manage_mdl->run_query($sql, False, $target_db);
        return $res_sql;
    }

    function insert_to_table($table_name)
    {
        $sql1 = "INSERT INTO " . $table_name . " ( ref_value, ref_desc) VALUES ";
        return $sql1;
    }

    function delete_update_qa()
    {
        $sql = "DELETE FROM qa_result";
        $sql2 = "UPDATE qa_assignment SET qa_assignment_active=0, qa_status='Pending'";
        $this->db3->query($sql);
        $this->db3->query($sql2);
    }

    function get_last_added_project()
    {
        $sql = "SELECT 	project_id FROM projects where 	project_active =1 
				ORDER BY project_id DESC LIMIT 1";
        $res = $this->db->query($sql)->row_array();
        return $res;
    }
}