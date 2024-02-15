<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');
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
 */
class Data_extraction_manager_lib
{
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	/*
		retrieve the completion status of paper classifications or validations for either a specific user or all users. 
		The completion status includes the total number of papers, the number of processed papers, and the number of pending papers
	*/
	function get_classification_completion($type = 'class', $user = '')
	{
		//all
		if (($user == 'all')) {
			if ($type == 'validation') {
				$papers_all = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('assigned', array('assigned_active' => 1, 'assignment_type' => 'Validation'))
					->num_rows();

				$papers_done = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('view_class_validation_done', array('assigned_active' => 1))
					->num_rows();
			} else {
				$papers_all = $this->CI->paper_dataAccess->count_papers('all');
				$papers_done = $this->CI->paper_dataAccess->count_papers('processed');
			}

		} else {
			if (empty($user)) {
				$user = active_user_id();
			}

			if ($type == 'validation') {
				$papers_all = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('assigned', array('assigned_active' => 1, 'assignment_type' => 'Validation', 'assigned_user_id' => $user))
					->num_rows();

				$papers_done = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('view_class_validation_done', array('assigned_active' => 1, 'assigned_user_id' => $user))
					->num_rows();
			} else {


				$sql = "select assigned_id 
						from assigned,paper 
						where
							paper.id= assigned.assigned_paper_id
							AND paper.paper_excluded=0
							AND assigned_active=1 
							AND assignment_type='Classification'
							AND assigned_user_id = '$user'
						";
				$papers_all = $this->CI->db_current->query($sql)->num_rows();

				$papers_done = $this->CI->db_current->order_by('assigned_id', 'ASC')
					->get_where('view_class_assignment_done', array('assigned_active' => 1, 'assignment_type' => 'Classification', 'assigned_user_id' => $user))
					->num_rows();
			}
		}
		$res['all_papers'] = $papers_all;
		$res['processed_papers'] = $papers_done;
		$res['pending_papers'] = 0;
		if (!empty($res['all_papers']))
			$res['pending_papers'] = $papers_all - $papers_done;

		return $res;
		//print_test($res);

	}

}