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

    function edit_screening_config($config, $phase_id, $affected_phases) {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $config_save = array(
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
            if (!$phase_id) $config_save['screening_on'] = $config['screening_on'];
            $this->db2->where($phase_id ? 'screen_phase_id' : 'config_id', $phase_id ? $phase_id : $config['config_id']);
            $res = $this->db2->update($phase_id ? 'screen_phase_config' : 'config', $config_save);


        if ($config['screening_inclusion_mode'] == 'None' || $config['screening_inclusion_mode'] == 'All') {
            $affected_phases_str = implode(',', array_map('intval', $affected_phases));
            $sql = "DELETE FROM screen_inclusion_mapping 
            WHERE screening_id IN (
                SELECT screening_id 
                FROM screening_paper 
                WHERE screening_phase IN ($affected_phases_str)
            )";
            $this->db2->query($sql);
        }
    }

    function reset_screening($affected_phases) {

        $this->db->where('screening_status', 'done');
        $this->db->where_in('screening_phase', $affected_phases);
        $this->db->update('screening_paper', array("screening_status" => "Reseted"));

        $affected_phases_str = implode(',', array_map('intval', $affected_phases));
            $sql = "DELETE FROM screen_inclusion_mapping 
            WHERE screening_id IN (
                SELECT screening_id 
                FROM screening_paper 
                WHERE screening_phase IN ($affected_phases_str)
            )";
            $this->db2->query($sql);
    }

    function keep_one_criterion($affected_phases, $from_all = false) {
        $affected_phases_str = implode(',', array_map('intval', $affected_phases));
        //if previous inclusion mode is 'All', insert first selected criteria as the one criterion
        if ($from_all) {
            $sql = "INSERT INTO screen_inclusion_mapping (screening_id, criteria_id, mapping_active)
            SELECT sp.screening_id, ric.ref_id, 1
            FROM screening_paper sp WHERE screen_phase_id IN ($affected_phases_str)
            CROSS JOIN (
                SELECT ref_id
                FROM ref_inclusioncriteria
                WHERE ref_active = 1
                ORDER BY ref_id
                LIMIT 1
            ) ric
            WHERE sp.screening_status = 'Done';";
        } else { //if previous mode is 'Any', delete extra criteria until there is only one left.
            $sql = "
            DELETE FROM screen_inclusion_mapping
            WHERE (screening_id, criteria_id) IN (
                SELECT screening_id, criteria_id
                FROM (
                    SELECT 
                        screening_id, 
                        criteria_id,
                        ROW_NUMBER() OVER(PARTITION BY screening_id ORDER BY criteria_id) AS row_num
                    FROM screen_inclusion_mapping
                    WHERE screening_id IN (
                        SELECT screening_id
                        FROM screen_paper
                        WHERE screen_phase IN ($affected_phases_str)
                    )
                ) AS subquery
                WHERE row_num > 1
            );
        ";
        }
        
        return $this->db_current->query($sql);
    }

    function count_inclusion_criteria() {
        $sql = 'SELECT COUNT(*) AS count FROM ref_inclusioncriteria where ref_active=1';
        return $this->db_current->query($sql)->row()->count;
    }

    public function get_criteria_array($screening_id) {
        $sql = 'SELECT criteria_id FROM screen_inclusion_mapping WHERE screening_id = ?';
        $query = $this->db_current->query($sql, array($screening_id));
        $result = $query->result_array();
        $criteria_ids = array_column($result, 'criteria_id');
        return $criteria_ids;
    }

    public function set_default_criterion($affected_phases) {
        $affected_phases_str = implode(',', array_map('intval', $affected_phases));

        $sql = "
            INSERT INTO ref_inclusioncriteria (ref_value, ref_active)
                SELECT 'Default', 0
                    WHERE NOT EXISTS (
                        SELECT 1
                        FROM ref_inclusioncriteria
                        WHERE ref_value = 'Default'
                    );
        ";
        $this->db_current->query($sql);

        $sql = "
            INSERT INTO screen_inclusion_mapping (screening_id, criteria_id)
                SELECT sp.screening_id, (SELECT ref_id FROM ref_inclusioncriteria WHERE ref_value = 'Default')
                FROM screening_paper sp
                WHERE sp.screening_status = 'Done' AND sp.screening_id IN ($affected_phases_str);
        ";
        $this->db_current->query($sql);

    }

    //sets all active criteria in mapping table for screenings marked 'Done'
    public function set_all_criteria($affected_phases) {

        if (empty($affected_phases)) {
            return 0;
        }

        //delete affected records in screen_inclusion_mapping table
        $this->db_current->select('screening_id');
        $this->db_current->from('screening_paper');
        $this->db_current->where_in('screening_phase', $affected_phases);
        $subquery = $this->db_current->get_compiled_select();
        $this->db_current->where("screening_id IN ($subquery)", NULL, FALSE);
        $this->db_current->delete('screen_inclusion_mapping');

        $affected_phases_str = implode(',', $affected_phases);

        // Define the raw SQL query
        $sql = "INSERT INTO screen_inclusion_mapping (screening_id, criteria_id, mapping_active)
                SELECT sp.screening_id, ric.ref_id, 1
                FROM screening_paper sp
                JOIN ref_inclusioncriteria ric ON ric.ref_active = 1
                WHERE sp.screening_status = 'Done'
                AND sp.screening_phase IN ($affected_phases_str)";

        $this->db_current->query($sql);

        // Return the number of affected rows
        return $this->db_current->affected_rows();
    }

}