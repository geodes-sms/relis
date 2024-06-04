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

    function edit_screening_config($config) {
        $this->db2 = $this->load->database(project_db(), TRUE);
        /*$query = $this->db->query("CALL update_config_screening("
            . $config['config_id'] . ","
            . $config['config_id'] . ","
            . $config['screening_on'] . ","
            . $config['screening_result_on'] . ","
            . $config['assign_papers_on'] . ","
            . $config['screening_reviewer_number'] . ","
            . $config['screening_inclusion_mode'] . ","
            . $config['screening_conflict_type'] . ","
            . $config['screening_screening_conflict_resolution'] . ","
            . $config['use_kappa'] . ","
            . $config['screening_validation_on'] . ","
            . $config['screening_validator_assignment_type'] . ","
            . $config['validation_default_percentage'] . ","
        )");*/
        $config_save = array(
            'screening_on' => $config['screening_on'],
            'screening_result_on' => $config['screening_result_on'],
            'assign_papers_on' => $config['assign_papers_on'],
            'screening_reviewer_number' => $config['screening_reviewer_number'],
            'screening_inclusion_mode' => $config['screening_inclusion_mode'],
            'screening_conflict_type' => $config['screening_conflict_type'],
            'screening_screening_conflict_resolution' => $config['screening_screening_conflict_resolution'],
            'use_kappa' => $config['use_kappa'],
            'screening_validation_on' => $config['screening_validation_on'],
            'screening_validator_assignment_type' => $config['screening_validator_assignment_type'],
            'validation_default_percentage' => $config['validation_default_percentage']
        );
        return $res = $this->db2->update('config', $config_save, array('config_id' => $config['config_id']));
    }

    function reset_screening($screening_phase = null) {
        $sql = 'UPDATE screening_paper SET screening_status = "Reseted"' . ($screening_phase != '' ? ' WHERE screening_phase=' . $screening_phase : '');
        return $this->db_current->query($sql);
    }

    function keep_one_criterion() {
        $sql = '
        DELETE FROM screen_inclusion_mapping
        WHERE (screening_id, criteria_id) IN (
            SELECT screening_id, criteria_id
            FROM (
                SELECT 
                    screening_id, 
                    criteria_id,
                    ROW_NUMBER() OVER(PARTITION BY screening_id ORDER BY criteria_id) AS row_num
                FROM screen_inclusion_mapping
            ) AS subquery
            WHERE row_num > 1
        );
        ';
        return $this->db_current->query($sql);
    }

    function count_inclusion_criteria() {
        $sql = 'SELECT COUNT(*) AS count FROM ref_inclusioncriteria where ref_active=1';
        return $this->db_current->query($sql)->row()->count;
    }

    function update_unique_criteria($inclusion_mode) {
        $sql = $inclusion_mode == 'One' ? '
        IF NOT EXISTS (
            SELECT *
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_NAME = "unique_criteria" AND TABLE_NAME = "screen_inclusion_mapping"
        ) THEN
            ALTER TABLE screen_inclusion_mapping
            ADD CONSTRAINT unique_criteria UNIQUE (screening_id);
        END IF;
        '
        : 'ALTER TABLE screen_inclusion_mapping DROP CONSTRAINT IF EXISTS unique_criteria';
        $this->db_current->query($sql);
    }
}