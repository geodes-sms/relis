<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
class Relis_mdl extends CI_Model
{	
	function __construct()
	{
		parent::__construct();
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
	
		
	
}