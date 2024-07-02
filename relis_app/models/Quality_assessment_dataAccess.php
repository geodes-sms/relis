<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Quality_assessment_dataAccess extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function select_qa_papers()
    {
        $sql = "SELECT P.* FROM paper P,qa_assignment Q WHERE P.id=Q.paper_id AND Q.qa_status='Done' AND P.paper_active=1 AND Q.qa_assignment_active=1 ";
        $all_papers = $this->db_current->query($sql)->result_array();
        return $all_papers;
    }
 
    function select_qa_assignments($category, $status)
    {
        if ($category == 'QA_Val') {
            if ($status == 'pending') {
                $extra_condition .= " AND Q.validation IS NULL";
            } elseif ($status == 'done') {
                $extra_condition .= " AND Q.validation IS NOT NULL";
            }
            $sql = "SELECT Q.*,Q.	qa_validation_assignment_id as assignment_id,Q.validation as status,P.title FROM qa_validation_assignment Q,paper P where Q.paper_id=P.id AND 	qa_validation_active=1 AND paper_active=1 $extra_condition ";
        } else {
            if ($status == 'pending') {
                $extra_condition .= " AND Q.qa_status	='Pending'";
            } elseif ($status == 'done') {
                $extra_condition .= " AND Q.qa_status ='Done'";
            }
            $sql = "SELECT Q.*,Q.qa_assignment_id as assignment_id,P.title,P.screening_status as status FROM qa_assignment Q,paper P where Q.paper_id=P.id AND qa_assignment_active=1 AND paper_active=1 $extra_condition ";
        }
        //echo $sql;
        $assignments = $this->db_current->query($sql)->result_array();
        return $assignments;
    }

    function count_qa($paper_id)
    {
        $sql = "SELECT COUNT(*) AS nbr FROM
		qa_questions Q LEFT JOIN qa_result R ON(Q.question_id=R.question AND R.qa_active=1 AND R.paper_id=$paper_id)
		WHERE Q.question_active=1 AND paper_id IS NULL  ";
        $result = $this->db_current->query($sql)->row_array();
        return $result;
    }
}