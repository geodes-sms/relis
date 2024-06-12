<?php
/* This class is responsible for accessing the database to get screening data*/

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Screening_dataAccess extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function select_from_screening_paper($current_phase)
    {
        $sql = "select paper_id,user_id,screening_decision FROM screening_paper 
                WHERE  assignment_mode='auto' AND  screening_status='done' AND screening_phase = $current_phase AND screening_active=1";
        //echo $sql;
        $result = $this->db_current->query($sql)->result_array();
        return $result;
    }

    function select_screening_all_papers($source, $source_status)
    {
        if ($source == 'all') {
            //rechercher dans papers
            //$papers=$this->DBConnection_mdl->get_papers('screen','papers','_',0,-1);
            $condition = "";
            if ($source_status != 'all') {
                $condition = " AND screening_status = '$source_status'";
            }
            $sql = "SELECT P.*,screening_status as paper_status from paper P where paper_active = 1 $condition ";
        } else {
            $condition = "";
            if ($source_status != 'all') {
                $condition = " AND S.screening_decision = '$source_status'";
            }
            $sql = "SELECT decison_id,screening_decision as paper_status,P.* from screen_decison S
        LEFT JOIN paper P ON(S.paper_id=P.id  )
        WHERE screening_phase='$source'	AND  decision_active=1 AND P.paper_active=1 $condition
        ";
            //rechercher dans screen et la decision dans screen decision
        }
        $all_papers = $this->db_current->query($sql)->result_array();

        return $all_papers;
    }

    function select_screening_paper($current_phase, $condition)
    {
        $sql = "Select DISTINCT (paper_id) from screening_paper WHERE screening_active =1 AND screening_phase = $current_phase  $condition GROUP BY paper_id";
        $paper_assigned = $this->db_current->query($sql)->result_array();
        return $paper_assigned;
    }

    function get_all_screenings($screening_phase)
    {
        $sql = "select * from screening_paper where assignment_role='Screening' AND screening_phase = $screening_phase AND screening_status='Done' AND   screening_active=1 ";
        $all_screenings = $this->db_current->query($sql)->result_array();
        return $all_screenings;
    }

    function get_user_assigned_papers($user_id=0,$screen_type="simple_screen",$screening_phase=0){
        $screen_table=get_table_configuration('screening','current','table_name');
        $active_field=get_table_configuration('screening','current','table_active_field');
        
        $screening_decision_table='';
        $screening_decision_active_field='';
        
        //print_test($screen_table);
        $condition="";
        if(!empty($user_id))
        {
            $condition=" AND S.user_id = $user_id  ";
        }
        
        if(!empty($screening_phase))
        {
            $condition.=" AND S.screening_phase = $screening_phase  ";
        }
        
        
        $this->db3 = $this->load->database(project_db(), TRUE);
        if(!empty($screen_type)){
            if($screen_type=='screen_validation'){
                $condition.=" AND S.assignment_role = 'Validation'  ";
            }else{
                $condition.=" AND S.assignment_role = 'Screening' ";
            }
        }
        $sql= "select  S.*,IFNULL(D.screening_decision,'Pending') as paper_status from $screen_table S
        LEFT JOIN  screen_decison D ON (S.paper_id=D.paper_id AND S.screening_phase=D.screening_phase AND D.decision_active=1 ) 
        where 	$active_field=1   $condition  ";
        //echo "$sql";
        
        $res=$this->db3->query($sql)->result_array();
        return $res;
    
    }

    function get_all_validations($screening_phase)
    {
        $sql = "select * from screening_paper where assignment_role='Validation' AND screening_phase = $screening_phase AND screening_status='Done'  AND  screening_active=1 ";
        $all_validations = $this->db_current->query($sql)->result_array();
        return $all_validations;
    }
}