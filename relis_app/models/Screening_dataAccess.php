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

    function select_screening_all_papers($source, $source_status, $phase)
    {
        $all_papers = array();

        if ($source == 'all') {
            //rechercher dans papers
            //$papers=$this->DBConnection_mdl->get_papers('screen','papers','_',0,-1);
            $condition = "";
            if ($source_status != 'all') {
                $condition = " AND screening_status = '$source_status'";
            }
            $sql = "SELECT P.*,screening_status as paper_status from paper P where paper_active = 1 AND initial_phase_id = $phase $condition ";
            $all_papers=$this->db_current->query($sql)->result_array();
        } else 
        {
            $condition = "";
            if ($source_status != 'all') {
                $condition = " AND S.screening_decision = '$source_status'";
            }



            foreach($source as $phase_ID)
            {
                $sql = "SELECT decison_id,screening_decision as paper_status,P.* from screen_decison S
                LEFT JOIN paper P ON(S.paper_id=P.id  )
                WHERE screening_phase=$phase_ID AND  decision_active=1 AND P.paper_active=1 $condition
                ";
                //rechercher dans screen et la decision dans screen decision
                $all_papers += $this->db_current->query($sql)->result_array();
            }
        }


    
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


    //additions for parallel screening phases feature
    function match_initial_phase_id($phase_id)
	{
        
		// $papers = $this->Screening_dataAccess->get_papers_by_initial_phase($phase_id);
		// if(!empty($papers['initial_phase_id']))
		// {return $papers['initial_phase_id'];}
		// else {return -1;}

        $this->db2 = $this->load->database(project_db(), TRUE);
        $this->db2->select('t1.screen_phase_id');
        $this->db2->from('screen_phase t1');
        $this->db2->where('t1.next_phase_id IS NOT NULL', null, false);
        $this->db2->where('t1.screen_phase_id', $phase_id);
        $this->db2->where_not_in('t1.screen_phase_id', "SELECT DISTINCT t2.next_phase_id FROM screen_phase t2 WHERE t2.next_phase_id IS NOT NULL", false);
        $query = $this->db2->get();
        $result = $query->row();
        return ($result) ? $result->screen_phase_id : null;
        

	}

    function get_phase_by_initial_phase_id($initial_phase_id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);
		$query = $this->db2->select('*')
			->from('screen_phase')
			->where('screen_phase_id', $initial_phase_id)
			->where('screen_phase_active', 1)
			->get();

		if ($query->num_rows() > 0) {
			$phase = $query->result_array();
		} else {
			$phase = array(); // No results found
		}
        
		return $phase;		

	}

    function get_papers_by_initial_phase($current_phase_id)
	{
		$this->db2 = $this->load->database(project_db(), TRUE);

		$query = $this->db2->select('*')
			->from('paper')
			->where('initial_phase_id', $current_phase_id)
			->where('paper_active', 1)
			->get();

		if ($query->num_rows() > 0) {
			$papers = $query->result_array();
		} else {
			$papers = array(); // No results found
		}

		return $papers;
	}

    function find_prev_phases($phase_id)
    {
        $this->db2 = $this->load->database(project_db(), TRUE);

        $query = $this->db2->select('screen_phase_id')
            ->from('screen_phase')
            ->where('next_phase_id', $phase_id)
            ->where('screen_phase_active', 1)
            ->get();
        
        $phases = $query->result(); // Use result() to get all results

        $previous_phases = array(); // Initialize an array to store previous phases

        foreach ($phases as $phase) {
            $previous_phases[] = $phase->screen_phase_id;
        }

        return $previous_phases;   
    }

    function already_assigned_papers($phaseID)
    {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $query =$this->db2->select('paper_id')
                ->from('screening_paper')
                ->where('screening_phase', $phaseID)
                ->where('screening_active',1)
                ->get();
        $paper_id = $query->result_array();
        return $paper_id;     
    }

    function get_paperid_from_prev_phases_to_assign($phaseID)
    {
        $this->db2 = $this->load->database(project_db(), TRUE);

        $query = $this->db2->select('paper_id')
            ->from('screening_paper')
            ->where('screening_phase', $phaseID)
            ->where('screening_active', 1)
            ->where('screening_decision', 'Included')
            ->where('screening_status', 'Done')
            ->get();

        $paper_IDs = $query->result_array();

        $paper_ids = []; // Initialize an array to store paper IDs

        // Extract paper_id values into the $paper_ids array
        foreach ($paper_IDs as $paper) {
            $paper_ids[] = $paper['paper_id'];
        }

        return $paper_ids;
    }


    function get_papers($paper_IDs)
    {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $papers = array(); // Initialize an array to store the results

        foreach ($paper_IDs as $paper_id) {
            $query = $this->db2->select('*')
                ->from('paper')
                ->where('id', $paper_id)
                ->get();

            if ($query->num_rows() > 0) {
                $papers[] = $query->row_array(); // Append the result to the array
            }
        }

        return $papers;
    }

    function getData_forDFS()
    {
        $this->db2 = $this->load->database(project_db(), TRUE);
        $data = array();

        $query = $this->db2->select('screen_phase_id,next_phase_id')
                ->from('screen_phase')
                ->where('screen_phase_active', 1)
                ->order_by('screen_phase_id', 'asc')
                ->get();
        $data[]=$query->result_array();     
        
        return $data;
    
    }

    //NEW FUNCTIONS
    function addEdge(&$graph,$u, $v) {
        if (!array_key_exists($u, $graph)) {
            $graph[$u] = array();
        }
        array_push($graph[$u], $v);
    }

    function isCyclicUtil($v, &$visited, &$recStack, $graph) {
        $visited[$v] = true;
        $recStack[$v] = true;

        if (isset($graph[$v])) {
            foreach ($graph[$v] as $neighbor) {
                if (!isset($visited[$neighbor]) || !$visited[$neighbor]) {
                    if ($this->isCyclicUtil($neighbor, $visited, $recStack, $graph)) {
                        return true;
                    }
                } elseif ($recStack[$neighbor]) {
                    return true;
                }
            }
        }

        $recStack[$v] = false;
        return false;
    }

    function isCyclic($graph) {
        $visited = array();
        $recStack = array();

        foreach ($graph as $node => $value) {
            $visited[$node] = false;
            $recStack[$node] = false;
        }

        foreach ($graph as $node => $value) {
            if (!isset($visited[$node]) || !$visited[$node]) {
                if ($this->isCyclicUtil($node, $visited, $recStack, $graph)) {
                    return true;
                }
            }
        }

        return false;
    }

    function get_next_screen_phase_id(){
        $this->db2 = $this->load->database(project_db(), TRUE);

        $query = $this->db2->query("SHOW TABLE STATUS LIKE 'screen_phase'");

        $row = $query->row_array();
        
        
        // Get the next auto-increment value  
        return $row['Auto_increment'];
    }

}