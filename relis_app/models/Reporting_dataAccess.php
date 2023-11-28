<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class reporting_dataAccess extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function prepare_paper_export()
    {
        $sql = "select id,bibtexKey,title, P.year as paper_year,GROUP_CONCAT(DISTINCT A.author_name SEPARATOR ' | ') as authors ,V.venue_fullName,
				S.ref_value as papers_sources ,T.ref_value as search_strategy ,
				GROUP_CONCAT(DISTINCT  G.assigned_user_id SEPARATOR '|') as reviewers
				FROM paper P
				JOIN classification C ON (C.class_paper_id=P.id AND C.class_active = 1 ) 
				LEFT JOIN assigned G ON (G.assigned_paper_id =P.id AND  G.assigned_active =1 )
				LEFT JOIN ref_papers_sources S ON (S.ref_id	 =P.papers_sources AND  S.ref_active =1 )
				LEFT JOIN  ref_search_strategy T ON (T.ref_id	 =P.search_strategy AND  T.ref_active =1 )
				LEFT JOIN venue V ON (V.venue_id =P.venueId AND  venue_active =1 )
				LEFT JOIN paperauthor ON (paperauthor.paperId =P.id AND  paperauthor_active =1 )
				LEFT JOIN author A ON (paperauthor.authorId =A.author_id AND  	author_active =1 )
				WHERE P.paper_active=1
				GROUP BY P.id ";
        $paper_data = $this->db2->query($sql);
        return $paper_data;
    }

    function prepare_paper_export2()
    {
        $sql = "SELECT id,bibtexKey,title,preview,P.screening_status ,S.exclusion_by as user_id,S.exclusion_criteria,T.ref_value as criteria, S.exclusion_note
		FROM  paper P
		INNER JOIN exclusion S ON (P.id = S.exclusion_paper_id AND S.exclusion_active=1 )
		LEFT JOIN  ref_exclusioncrieria T ON ( S.exclusion_criteria = T.ref_id)
		WHERE paper_active =1 AND P.paper_excluded = 1  ORDER BY title ";
        //echo $sql; exit;
        $data = $this->db2->query($sql);
        return $data;
    }

    function prepare_paper_export3()
    {
        $sql = "SELECT id,bibtexKey,title,doi,preview,abstract,year FROM  paper WHERE paper_active =1";
        $data = $this->db2->query($sql);
        return $data;
    }

    function prepare_paper_export4($extra_sql)
    {
        $sql = "SELECT id,bibtexKey,title,doi,preview,abstract,year,bibtex FROM  paper
		WHERE paper_active =1 $extra_sql ";
        //echo $sql; exit;
        $data = $this->db2->query($sql);
        return $data;
    } 

    function prepare_paper_export5()
    {
        $sql = "SELECT id,bibtexKey,title,preview,P.screening_status ,S.user_id,S.exclusion_criteria,T.ref_value as criteria,S.screening_note
		FROM  paper P
		INNER JOIN screening_paper S ON (P.id = S.paper_id AND S.screening_active=1 )
		LEFT JOIN  ref_exclusioncrieria T ON ( S.exclusion_criteria = T.ref_id)
		WHERE paper_active =1 AND P.screening_status = 'Excluded'  ORDER BY title ";
        //echo $sql; exit;
        $data = $this->db2->query($sql);
        return $data;
    }

    function prepare_paper_export6()
    {
        $sql = "SELECT id,bibtexKey,title,doi,preview,bibtex FROM `paper`WHERE `paper_active` =1";
        $data = $this->db2->query($sql);
        return $data;
    }
}