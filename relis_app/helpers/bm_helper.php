<?php
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
function print_test($array) {
	echo "<pre>";
	print_r ( $array );
	echo "</pre>";
}
function td_nombre($nbr, $decimal = 0, $sep_dec = '.', $sep_mill = ",") {
	if($nbr){
		return "<div class='pull-right'>" . number_format ( $nbr, $decimal, $sep_dec, $sep_mill ) . "</div>";
	}else{// s'il n'y a rien
		return "<div class='pull-right'>" . $nbr . "</div>";
	}
	
}
function nombre($nbr, $decimal = 0, $sep_dec = '.', $sep_mill = " ") {
	return  number_format ( $nbr, $decimal, $sep_dec, $sep_mill );
}
function td_right($text) {
	return "<div style='text-align:right'>" . $text . "</div>";
}


function Slug($string) // function for URL Friendly Username
{
	$string = strtolower ( trim ( preg_replace ( '~[^0-9a-z]+~i', '_', html_entity_decode ( preg_replace ( '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities ( $string, ENT_QUOTES, 'UTF-8' ) ), ENT_QUOTES, 'UTF-8' ) ), '_' ) );
	if (strlen ( $string ) > 50)
		$string = substr ( $string, 0, 50 );
	
	return $string;
}

function remove_secial_caracters($string){
	$string =preg_replace ( '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities ( $string, ENT_QUOTES, 'UTF-8' ) );
	return $string;
}

function get_top_button($type='add',$title='',$link='',$label=" ",$icon=' fa-plus ',$icon2='',$bt_class=' btn-info ',$get_li=True){
	$button="";
	$label=trim($label);
	$title=trim($title);
	
	if(!empty($label))
		$label=lng_min($label);

	if(!empty($title))
		$title=lng_min($title);
	switch ($type) {
		case 'add':
			$button=anchor($link,'<button class="btn btn-success"><i class="fa fa-plus"></i>'.$label.'</button>','title="'.$title.'"');

			break;
		case 'edit':
			$button=anchor($link,'<button class="btn btn-info"><i class="fa fa-pencil"></i>'.$label.'</button>','title="'.$title.'"');

			break;
		case 'close':
			$button=anchor($link,'<button class="btn btn-danger"><i class="fa fa-close"></i> </button>','title="'.$title.'"  ');
			break;

		case 'delete':
			$button=anchor($link,'<button type="button" class="btn btn-danger"><i class="fa fa-trash"></i> '.$label.'
                          </button>','title="'.$title.'"  onClick="return confirm_delete()" ');
			break;

		case 'back'://	$button='<a href="javascript:history.go(-1)" title="'.$title.'"><button type="button" class="btn btn-danger"><i class="icon-arrow-left  icon-white"></i></button></a>';
			$button='<a href="javascript:history.go(-1)" title="Back"><button class="btn btn-danger"><i class="fa fa-arrow-circle-left"></i></button></a>';
			$ci = get_instance();
			
			if ($ci->session->userdata('previous_page')){
			$button='<a href="'.base_url().$ci->session->userdata('previous_page').'" title="Back"><button class="btn btn-danger"><i class="fa fa-arrow-circle-left"></i></button></a>';
			}else{
				$button='<a href="'.base_url().'home" title="Back"><button class="btn btn-danger"><i class="fa fa-arrow-circle-left"></i></button></a>';
					
				
			}
			
			$button='<a href="javascript:history.go(-1)" title="Back"><button class="btn btn-danger"><i class="fa fa-arrow-circle-left"></i></button></a>';
				
			break;

	 case 'all':
	 	$button=anchor($link,'<button class="btn '.$bt_class.'">  <i class=" fa '.$icon.'"></i> <i class=" fa '.$icon2.' "></i>'.$label.'</button></li>','title="'.$title.'"');

	 	break;
	}


	if($get_li)	
	return "<li>".$button."</li>";
	else 
		return $button;
}

function create_button($label,$link,$title='',$bt_class=' btn-info ',$icon='',$icon2=''){
if(empty($link))
	return '<button class="btn '.$bt_class.'">  <i class=" fa '.$icon.'"></i> <i class=" fa '.$icon2.' "></i>'.$label.'</button></li>';
else
	return anchor($link,'<button class="btn '.$bt_class.'">  <i class=" fa '.$icon.'"></i> <i class=" fa '.$icon2.' "></i>'.$label.'</button></li>','title="'.$title.'"');
	
}

function set_previous_page(){
	$ci = get_instance();
	$prev=str_replace("/relis/ci_relis/", "", $_SERVER['REQUEST_URI']);
	
	
	//$ci->session->set_userdata('previous_page',$prev);
	
	
	if($ci->session->userdata('current_page')){
		if($prev != $ci->session->userdata('current_page') )
		$ci->session->set_userdata('previous_page',$ci->session->userdata('current_page'));
	}else{
		$ci->session->set_userdata('previous_page','home');
	}
	
	$ci->session->set_userdata('current_page',$prev);
	
	//$ci->session->set_userdata('previous_page',$_SERVER['QUERY_STRING']);
}

function form_bm_just_test($label,$text="") {

	



		
		$bm = '
						<div class="form-group">'. form_label ( $label, 'label', array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) )  . '<div class="col-md-6 col-sm-6 col-xs-12">' .$text . '</div></div>';
	
		return $bm;				
}
//Forms

function input_form_bm($label, $name, $id, $value = "", $max = 100, $classe = " ", $readonly = 'bm',$place_holder="",$pattern="",$pattern_info="") {

	if(strpos($classe,'mandatory')){
		$mandatory='<span class="mandatory"> *</span>';
		$classe=str_replace('mandatory', "", $classe);
	}else{
		$mandatory="";
	}
	
	
	
if(!empty($pattern)){
	$bm = '
						<div class="form-group">'. form_label ( $label.$mandatory, $name, array (
									'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
							) )  . '<div class="col-md-6 col-sm-6 col-xs-12">' . form_input ( array (
									'name' => $name,
									'id' => $id,
									'class' => 'form-control col-md-7 col-xs-12 ' . $classe,
									'maxlength' => $max,
									'value' => set_value ( $name, $value ),
									$readonly => 'true' ,
									'placeholder' => $place_holder,
									'pattern' => $pattern,
									'title'=>$pattern_info,
							) ) . '</div></div>';
	
}else{
	$bm = '
						<div class="form-group">'. form_label ( $label.$mandatory, $name, array (
									'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
							) )  . '<div class="col-md-6 col-sm-6 col-xs-12">' . form_input ( array (
									'name' => $name,
									'id' => $id,
									'class' => 'form-control col-md-7 col-xs-12 ' . $classe,
									'maxlength' => $max,
									'value' => set_value ( $name, $value ),
									$readonly => 'true' ,
									'placeholder' => $place_holder
							) ) . '</div></div>';
}
						
						return $bm;
}

function input_password_bm($label, $name, $id, $value = "", $max = 100, $classe = " ", $readonly = 'bm',$place_holder="") {

	if(strpos($classe,'mandatory')){
		$mandatory='<span class="mandatory"> *</span>';
		$classe=str_replace('mandatory', "", $classe);
	}else{
		$mandatory="";
	}
	
	
	$bm = '
						<div class="form-group">'. form_label ( $label.$mandatory, $name, array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) )  . '<div class="col-md-6 col-sm-6 col-xs-12">' . form_password ( array (
								'name' => $name,
								'id' => $id,
								'class' => 'form-control col-md-7 col-xs-12 ' . $classe,
								'maxlength' => $max,
								'value' => set_value ( $name, $value ),
								$readonly => 'true' ,
								'placeholder' => $place_holder
									
						) ) . '</div></div>';

						return $bm;
}




function input_textarea_bm($label, $name, $id, $value = "", $max = 100, $classe = " ", $readonly = 'bm',$place_holder="") {
	if(strpos($classe,'mandatory')){
		$mandatory='<span class="mandatory"> *</span>';
		$classe=str_replace('mandatory', "", $classe);
	}else{
		$mandatory="";
	}


	$bm = '
						<div class="form-group ">' . form_label ( $label.$mandatory, $name, array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) ) . ' <div class="col-md-6 col-sm-6 col-xs-12">
									<textarea id="'.$id.'"  name="'.$name.'"   maxlength="'.$max.'"  class="form-control col-md-7 col-xs-12 '.$classe.'" '.$readonly.' placeholder="'.$place_holder.'">'.$value.'</textarea>'.'
					
									</div></div>';




	return $bm;
}




function dropdown_form_bm($label, $name, $id, $values = array(), $selected = 0, $classe = " ",$readonly="") {

	if(strpos($classe,'mandatory')){
		$mandatory='<span class="mandatory"> *</span>';
		$classe=str_replace('mandatory', "", $classe);

	}else{
		$mandatory="";
	}

	if($readonly=='readonly'){
		$readonly=' disabled=disabled ';
	}else{
		$readonly="";
	}
	$bm = '

						<div class="form-group ">' . form_label ( $label.$mandatory, $name, array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) ) . '<div class="col-md-6 col-sm-6 col-xs-12">' . form_dropdown ( $name, $values, $selected, 'id="' . $id . '" class="form-control col-md-7 col-xs-12  select2_single '.$classe.' "  '.$readonly ) . '</div></div>';

	return $bm;
}

function dropdown_multi_form_bm($label, $name, $id, $values = array(), $selected = array(), $classe = " ",$readonly="",$number_of_values="*") {

	if(strpos($classe,'mandatory')){
		$mandatory='<span class="mandatory"> *</span>';
		$classe=str_replace('mandatory', "", $classe);

	}else{
		$mandatory="";
	}

	if($readonly=='readonly'){
		$readonly=' disabled=disabled ';
	}else{
		$readonly="";
	}
	
	$bm = '

						<div class="form-group ">'  . form_label ( $label.$mandatory, $name, array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) ) . '<div class="col-md-6 col-sm-6 col-xs-12">' . form_dropdown ( $name."[]", $values, $selected, 'id="' . $id . '"  multiple="multiple" class=" class_'.$id.' form-control col-md-7 col-xs-12 select2_multiplexx '.$classe.' "  '.$readonly ) . '</div></div>';

	$number_selected="";
	if($number_of_values!="*"){
		$number_selected="maximumSelectionLength: $number_of_values ,";
	}
	$script='
			<script>
			 $(".class_'.$id.'").select2({
          
          placeholder: "'.lng_min('Select multi').' ...",
          '.$number_selected .'
          allowClear: true
        });
			</script>';
	
	return $bm.$script;
}

function input_datepicker_bm($label, $name, $id, $value = "", $max = 100, $classe = " ", $readonly = 'bm') {
	
	if(strpos($classe,'mandatory')){
		$mandatory='<span class="mandatory"> *</span>';
		$classe=str_replace('mandatory', "", $classe);
	}else{
		$mandatory="";
	}
	
	if($readonly=='readonly'){
		$datepicker="";
	}else{
		$datepicker="datepicker";
	}
	

	$bm = '
						<div class="form-group">'. form_label ( $label.$mandatory, $name, array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) )  . '<div class="col-md-6 col-sm-6 col-xs-12"><div class="input-group">
								
								<input name="' . $name . '" id="' . $id . '"  class=" '.$datepicker.' form-control  droite ' . $classe . '" readonly type="text" value="' . $value . '">
									<span class="input-group-addon add-on  "><i class="fa fa-calendar" ></i></span>							
								</div></div></div>';

						return $bm;
}

function input_colorpicker_bm($label, $name, $id, $value = "", $max = 20, $classe = " ", $readonly = 'bm') {

	if(strpos($classe,'mandatory')){
		$mandatory='<span class="mandatory"> *</span>';
		$classe=str_replace('mandatory', "", $classe);
	}else{
		$mandatory="";
	}


	$bm = '
						<div class="form-group">'. form_label ( $label.$mandatory, $name, array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) )  . '<div class="col-md-6 col-sm-6 col-xs-12  "><div  id="color_p" class="input-group  colorpicker-component" >

								<input name="' . $name . '" id="' . $id . '"  class=" form-control  droite ' . $classe . '" type="text" value="' . $value . '">
									 <span class="input-group-addon  " ><i></i></span>
								</div></div></div>';

	return $bm;
}

function input_image_bm($label, $name, $id, $value = "", $max = 20, $classe = " ", $readonly = 'bm') {

	if(strpos($classe,'mandatory')){
		$mandatory='<span class="mandatory"> *</span>';
		$classe=str_replace('mandatory', "", $classe);
	}else{
		$mandatory="";
	}


	$bm = '
						<div class="form-group">'. form_label ( $label.$mandatory, $name, array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) )  . '<div class="col-md-6 col-sm-6 col-xs-12  "><div >

								<input class="input-file uniform_on ' . $classe . ' " id="' . $id . '" name="' . $name . '" type="file">
									 
								</div></div></div>';

	return $bm;
}


function checkbox_form_bm($label, $name, $id, $values = 1, $selected = 0, $classe = " ",$readonly="") {
	if(empty($values))
		$values=1;
	if(strpos($classe,'mandatory')){
		$mandatory='<span class="mandatory"> *</span>';
		$classe=str_replace('mandatory', "", $classe);

	}else{
		$mandatory="";
	}

	if($readonly=='readonly'){
		$readonly=' disabled=disabled ';
	}else{
		$readonly="";
	}
	
	if($selected=='1'){
		$val=1;
		$checked=" checked ";
	}else{
		$checked=" ";
		$val=0;
	}
	
	
	
	
	/*$bm = '

						<div class="form-group ">' . form_label ( $label.$mandatory, $name, array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) ) . '<div class="col-md-6 col-sm-6 col-xs-12">' . form_dropdown ( $name, $values, $selected, 'id="' . $id . '" class="form-control col-md-7 col-xs-12  '.$classe.' "  '.$readonly ) . '</div></div>';   
						
						*/

	$bm = '

						<div class="form-group "><input name="'. $name. '" value="0" type="hidden"  />' . form_label ( $label.$mandatory, $name, array (
								'class' => 'control-label col-md-3 col-sm-3 col-xs-12'
						) ) . '<div class="col-md-6 col-sm-6 col-xs-12"> <input name="'. $name. '" value="'. $values. '" type="checkbox" class="js-switch '.$classe.' " '.$checked.$readonly.' /></div></div>';

	
	
	
	return $bm;
}





function create_button_link($url,$label,$button_class="btn-info",$title="",$type="onlist",$alert_message="") {
	//print_test($alert_message);
	if(empty($alert_message)){
		$button=anchor ( $url, $label, 'class=" btn '.$button_class.' btn-xs " , title=" '.$title.' "' );
	}else{
		$button=anchor ( $url, $label, 'class=" btn '.$button_class.' btn-xs " , title=" '.$title.' " onClick="return confirm_delete()" ' );
	
	}

	return $button;
}

function create_button_link_dropdown($arr_buttons,$btn_label="Action",$li_button=FALSE) {

	//print_test($arr_buttons);
	if(!empty($arr_buttons)){
		
		//print_test($arr_buttons);
	
	//print_test(count($alert_message));
		if(count($arr_buttons)==1){
			$alert_message=!empty($arr_buttons[0]['delete_alert'])?"Delete the record?":"";
			$button=create_button_link($arr_buttons[0]['url'],
										$arr_buttons[0]['label'],
										!empty($arr_buttons[0]['btn_type'])?$arr_buttons[0]['btn_type']:'btn-info',
										$arr_buttons[0]['title'],'onlist',$alert_message);
			
		}else{
			if($li_button){
			$button='<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle btn-xs" aria-expanded="false" data-toggle="dropdown" type="button">
				'.$btn_label.'<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
		<ul class="dropdown-menu" role="menu">';
			
			foreach ($arr_buttons as $key => $value) {
				$button.='<li>'.anchor ( $value['url'], !empty($value['label'])?($value['label']):"", 'class=""  title=" '.!empty($value['title'])?($value['title']):"".' "' ).'</li>';
			}
			
		
			
			
			$button.='</ul></div>'; //close the UL
			}else{
				
				$button="";
				foreach ($arr_buttons as $key => $value) {
					$alert_message=!empty($arr_buttons[$key]['delete_alert'])?"Delete the record":"";
					
					//print_test($alert_message);
					//$button.='<li>'.anchor ( $value['url'], !empty($value['label'])?($value['label']):"", 'class=""  title=" '.!empty($value['title'])?($value['title']):"".' "' ).'</li>';
					$button.=create_button_link($value['url'], $value['label'],!empty($value['btn_type'])?$value['btn_type']:'btn-info',$value['title'],'onlist',$alert_message);
				}
			}
		}
		
		
	}else{
		$button="";
	}

	

	return $button;
}

function set_log($log_type,$log_event,$log_publish=1,$log_user_id=0,$log_poste_id=0){

	$ci = get_instance();

	$log=array();
	$log['log_type']=$log_type;
	$log['log_event']=$log_event;
	$log['log_publish']=$log_publish;
	$log['log_time']=date('Y-m-d H:i:s');
	if(!empty($_SERVER['REMOTE_ADDR']))
	{
		$log['log_ip_address']= $_SERVER['REMOTE_ADDR'];
	}
	if($ci->session->userdata('user_agent')){
		$log['log_user_agent']= $ci->session->userdata('user_agent');
	}
	if($log_user_id){
		$log['log_user_id']=$log_user_id;
	}else{
		if($ci->session->userdata('user_id')){
			$log['log_user_id']=$ci->session->userdata('user_id');
		}
	}
	if($log_poste_id){
		$log['log_poste_id']=$log_poste_id;
	}
	$log['table_config']='logs';
	$log['operation_type']='new';
	$log['current_operation']='add_logs';
	//print_test($log);
	$ci->DBConnection_mdl->save_reference_mdl($log);
}

/**
 * Returns the database name to be used specified by using an identifier.
 * In case of 'current' provided (the default), the database associated with
 * the user's current project is returned.
 *
 * The value returned can be used to load a database using the Codeigniter
 * framework function (e.g. $this->load->database(get_targetdb(), TRUE)).
 *  
 * @param  string $target_db The identifier to specify which db name to be 
 *                           returned, or 'current' by default.
 * @return string            The db name as string.
 *
 * @author Daniel Hofstetter daniel.hofstetter@sbg.ac.at
 */
function get_targetdb($target_db = 'current')
{
		return ($target_db === 'current') ? project_db() : $target_db;
}

/**
 * Helper to return/determine the db associated with the project.
 * The "active" project is determined by inspection of the user's
 * session.
 *
 * If no db is found (e.g. currently not beeing in any project)
 * then 'default' gets returned (the value can later be used to 
 * load a specific database for further operations).
 * 
 * @return string the db associated with the project.
 */
function project_db() {
	
	$ci = get_instance ();
	if($ci->session->userdata ( 'project_db' ))
	return $ci->session->userdata ( 'project_db' );
	else 
	return 'default';
}

/**
 * Helper to retrieve the username for the currently logged in user.
 * @return string the user's username, or 'user_unknown'
 */
function active_user_name() 
{
	$ci = get_instance();
	if($ci->session->userdata ( 'user_username' ))
		return $ci->session->userdata ( 'user_username' );
		else
			return 'user_unknown';
}
/**
 * Retrieve the project id 
 * @return [type] [description]
 */
function active_project_id() {

	$ci = get_instance ();
	//TODO: Probably an error to be fixed
	if($ci->session->userdata ( 'user_id' ))
		return $ci->session->userdata ( 'project_id' );
		else
			return 0;
}
function active_user_id() {

	$ci = get_instance ();
	if($ci->session->userdata ( 'user_id' ))
		return $ci->session->userdata ( 'user_id' );
		else
			return 0;
}
function top_msg() {
	$ci = get_instance ();
	if ($ci->session->userdata ( 'msg_err' )) {
	/*	echo '<br/><br/><br/><div class="alert alert-danger alert-dismissible fade in" role="alert">
				<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>' . $ci->session->userdata ( 'msg_err' ) . '
			</div>';*/
		?>
		
		<script>
		$(document).ready(function() {
			new PNotify({
				title: '',
				text: '<?php echo $ci->session->userdata ( 'msg_err' );?>',
				type: 'error',
				styling: 'bootstrap3',
				 addclass: "stack-topleft"
						});
		
		});
		</script>
		
		<?php 
		$ci->session->set_userdata ( 'msg_err', '' );
	} if ($ci->session->userdata ( 'msg' )) {
		/*echo '<br/><br/><br/><div class="alert alert-success alert-dismissible fade in" role="alert">
				<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>' . $ci->session->userdata ( 'msg' ) . '
			</div>';
		*/
		?>
				
				<script>
				$(document).ready(function() {
					new PNotify({
						title: '',
						text: '<?php echo $ci->session->userdata ( 'msg' );?>',
						type: 'success',
						styling: 'bootstrap3',
						 addclass: "stack-topleft"
					});
				
				});
				</script>
				
				<?php
				
		$ci->session->set_userdata ( 'msg', '' );
	}
}

function set_top_msg($message,$type='success'){
	
	if(!empty($message))
		$message=lng_min($message);
	
	$ci = get_instance ();

	if($type=='success')
		$ci->session->set_userdata('msg',$message);
	else
		$ci->session->set_userdata('msg_err',$message);
}


//String management
function lng_min($str,$category="default",$lang='en'){
	$res=lng($str,$category,$lang,FALSE);
	return $res;
}
function lng($str,$category="default",$lang='en',$edit_allowed=True){
	if(empty($str)){
		return $str;
	}
	//return $str;
	$ci = get_instance ();
	if($ci->session->userdata('active_language')){
		$lang=$ci->session->userdata('active_language');
	}
	
	
	$res_str=$ci->DBConnection_mdl->get_str($str,$category,$lang);
	//print_test($res_str);
	if(!empty($res_str)){
		$res=$res_str['str_text'];
		$id=$res_str['str_id'];
		
	}else{
		
		$id=$ci->DBConnection_mdl->set_str($str,$category,$lang);
		
		$res= $str;
	}
	if($ci->session->userdata('language_edit_mode')=='yes' AND $edit_allowed){
		
	//	$res='<a style=" color:red" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="manager/edit_element/str_mng/'.$id.'/modal"  data-modal_title="Edit text ">'.$res.' </a>';
		$res='<a style=" color:red" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"  data-modal_link="op/edit_element/edit_str_mng/'.$id.'/modal"  data-modal_title="Edit text ">'.$res.' </a>';
	}
	return $res;
	
}

function edition_mode_active(){
	$ci = get_instance ();
	if($ci->session->userdata('language_edit_mode')=='yes'){
		return TRUE;
	}else{
		return false;
	}
	
}

function active_language(){
	$ci = get_instance ();
	return $ci->session->userdata('active_language');
}

function get_appconfig($element="all",$source="db"){
	$ci = get_instance ();
	
	$config=$ci->DBConnection_mdl->get_row_details('config','1');
	
	return $config;
}

function get_appconfig_element($element="all",$source="db"){
	$ci = get_instance ();
	
	$config=$ci->DBConnection_mdl->get_row_details('config','1');
	
	if(!empty($config[$element])){
		return $config[$element];
	}else{
			return "0";
		}
	
}

function get_adminconfig_element($element="all",$source="db"){
	$ci = get_instance ();

	$config=$ci->DBConnection_mdl->get_row_details('config_admin','1');

	if(!empty($config[$element])){
		return $config[$element];
	}else{
		return "0";
	}

}

function debug_coment_active(){
	$ci = get_instance ();

	$config=$ci->DBConnection_mdl->get_row_details('config_admin','1');

	if(!empty($config['track_comment_on'])){
		return $config['track_comment_on'];
	}else{
		return "0";
	}

}

function set_appconfig_element($element_label,$value,$source="db"){
	$ci = get_instance ();

	$sql="UPDATE config SET $element_label = '$value' WHERE config_id=1 ";
	
	$res=$ci->manage_mdl->run_query($sql);
	
}

function get_ci_config($element){
	$ci = get_instance ();

	$config=$ci->config->item($element);

	return $config;
}

function get_project_config($project_label){
	$ci = get_instance ();

	$config=$ci->manage_mdl->get_project_config($project_label);

	return $config;
}




function admin_config($config,$config_name=True,$type='config'){
	
	if($type=='table'){
		$admin_configs=array('users','usergroup','log','projects','userproject','config_admin');
	}else{
		$admin_configs=array('users','usergroup','logs','project','user_project','config_admin','remove_user_project');
		//remove_user_project to remove user while in current project : to avoid  deleting in project database
	}
	
	
	
	if($config_name){
		$cfg=$config;
	}else{
		$cfg=$config['config_label'];
		
	}
	if(($config=='str_mng' OR $config=='debug'  ) AND project_db() =='default'  ){
		
		return TRUE;
	}else{
		
			if(in_array($cfg,$admin_configs))
			{
				return TRUE;
			}else{
				return FALSE;
			}
	}

	
}	


/**
 * Function to verify if a user has access to a project
 * @param number $project_id : the id of the project
 * @param number $user : id of the user
 * @param string $user_role : if not null the role tha the user has in that project
 * @return boolean
 */
function user_project($project_id , $user=0,$user_role=""){
		$ci = get_instance ();
		//if user null it takes the connected user
		if($user==0){
			$user=$ci->session->userdata('user_id');
		}
		$sql="select project_id from userproject where userproject_active=1 
				AND user_id=$user ";
		
		$user_projects = $ci->db->query($sql)->num_rows();
		//print_test($user);
		
		
		
		if($user_projects > 0 ){
			if(!empty($user_role)){
				$sql="select project_id from userproject where userproject_active=1
				AND user_id=$user  AND user_role LIKE '$user_role'";
					
			}else{
				$sql="select project_id from userproject where userproject_active=1 AND user_id=$user AND project_id=$project_id ";
			
			}
			
			$user_projects = $ci->db->query($sql)->num_rows();
			
			if($user_projects>0){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			//
			//if an super admin does not have a projected assigned to him he can access
			// all projects
			//
			//if(has_usergroup(1,$user))
			//	return TRUE;
			//else 
				return False;
		}
		
	}
	
	function file_upload_error($code){
		$phpFileUploadErrors = array(
				0 => 'There is no error, the file uploaded with success',
				1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
				2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
				3 => 'The uploaded file was only partially uploaded',
				4 => 'No file was uploaded',
				6 => 'Missing a temporary folder',
				7 => 'Failed to write file to disk.',
				8 => 'A PHP extension stopped the file upload.',
		);
		if($phpFileUploadErrors[$code]){
			return $phpFileUploadErrors[$code];
		}else{
			return"Error code unknown!";
		}
	}
	
	function is_project_creator($project_id='current' , $user=0,$use='project_label'){
		$ci = get_instance ();
		if($project_id=='current'){
			$project_id=project_db();// active project
		}
		if($user==0){
			$user=$ci->session->userdata('user_id');
		}
		$sql="select project_id from projects where project_creator=$user AND $use='$project_id' AND project_active=1 ";
	
		$user_projects = $ci->db->query($sql)->num_rows();
	
		if($user_projects>0){
			return TRUE;
		}else{
			return FALSE;
		}
		
	}
	
	function project_published($project_id='current' ){
		$ci = get_instance ();
		if($project_id=='current'){
			$project_id=active_project_id();// active project
		}
		
		$sql="SELECT  	project_public FROM projects 
			WHERE  project_id='$project_id' ";
		//echo $sql;
		$res = $ci->db->query($sql)->row_array();
		//print_test($res);
		if($res['project_public']){
			return TRUE;
		}else{
			return FALSE;
		}
	
	}
	
	function has_usergroup($usergroup_id , $user=0){
		$ci = get_instance ();
	
		if($user==0){
			$user=$ci->session->userdata('user_id');
		}
		$sql="select user_id from users where user_usergroup=$usergroup_id AND user_id=$user AND user_active=1 ";
	//echo $sql; 
		$user_res = $ci->db->query($sql)->num_rows();
		//echo $user_res;
	//exit;
		if($user_res>0){
			return TRUE;
		}else{
			return FALSE;
		}
	
	}
	
	function has_user_role($role , $user=0, $project_id=0){
		
		$ci = get_instance ();
	
		if($user==0){
			$user=$ci->session->userdata('user_id');
		}
		
		if($project_id==0){
			$project_id=$ci->session->userdata('project_id');
		}
		
		if(!empty($project_id)){
			$sql="select userproject_id from userproject where user_id=$user AND  project_id=$project_id AND user_role LIKE '".$role."' AND    	userproject_active=1 ";
		//echo $sql; exit;
			$user_res = $ci->db->query($sql)->num_rows();
		//print_test($user_res);
			if($user_res>0){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	
	}
	
	function get_user_role( $user=0, $project_id=0){
		$ci = get_instance ();
	
		if($user==0){
			$user=$ci->session->userdata('user_id');
		}
	
		if($project_id==0){
			$project_id=$ci->session->userdata('project_id');
		}
	
		$sql="select userproject_id,user_role from userproject where user_id=$user AND  project_id=$project_id  AND    	userproject_active=1 ";
		//echo $sql; exit;
		$user_res = $ci->db->query($sql)->row_array();
		//print_test($user_res);
		if(!empty($user_res)){
			return $user_res['user_role'];
		}else{
			return '';
		}
	
	}
	
	function can_review_project($user=0, $project_id=0){
		
		if( has_user_role('Reviewer',$user,$project_id) OR has_user_role('Validator',$user,$project_id) OR  has_user_role('Project admin',$user,$project_id)  OR has_usergroup(1,$user))
			return true;
			else 
			return false;

	}
	function can_validate_project($user=0, $project_id=0){
	
		if( has_user_role('Validator',$user,$project_id) OR  has_user_role('Project admin',$user,$project_id)  OR has_usergroup(1,$user))
			return true;
			else
				return false;
	
	}
	
	function can_manage_project($user=0, $project_id=0){
	
	if(has_user_role('Project admin',$user,$project_id)  OR has_usergroup(1,$user))
			
			return TRUE;
		else
			return false;
	
	}
	

	
	function  save_metrics($info,$type="metric"){
		
		$message=time()."__--~~".$type."__--~~".$info;
		
		//$metrics_path = get_ci_config('metrics_save_path');
		
		project_db();
		$filename="cside/metrics/".date('Y_M_d')."/".active_user_name()."_".date('H').".txt";
		
		$dirname = dirname($filename);
		if (!is_dir($dirname))
		{
		    mkdir($dirname, 0755, true);
		}
		$f_new = fopen($filename,'a+');
		fputs($f_new, $message. "\n");
		fclose($f_new);
		//echo $message;
	}
	
	// -----
	
	// Récupération de la configuration d'une entité
	function get_table_config($_table,$target_db='current')
	{
		$ci = get_instance ();
		//return $ci->entity_config_lib->get_table_config($_table,$target_db);
		return $ci->entity_configuration_lib->get_table_configuration($_table,$target_db);
		
		
	}
	
	
	
	
	function  old_version($text="Old version"){
		
		echo '<h1 class="red">'.$text.'</h1>';
	}
	
	function display_picture_from_db($picture){
		//echo $picture;
		return 'data:image/png;base64,'.base64_encode( $picture);
	}
	
	function activate_update_stored_procedure(){
		
		//return true;
	}
	
	function get_relis_common_configs(){
		return get_ci_config('common_relis_configs');
	}
	
	function valid_install_configuration_file($config_table){
		
		if(!empty($config_table['project_title']) AND !empty($config_table['project_short_name']) AND !empty($config_table['config']['classification']['table_name']) AND !empty($config_table['config']['classification']['fields'])){
			return True;
		}else{
			return False;
			
		}
	}
	
	
	
	function header_perspective($type='gen'){
		$s_active="";
		$c_active="";
		$g_active="";
	
		if($type=='screen'){
			$s_active='active';
		}elseif($type=='class'){
			$c_active='active';
			$g_active='active';
				
		}else{
			$g_active='active';
			$c_active='active';
		}
	if($type=='screen' AND !get_appconfig_element('classification_on')){
		
	}else{
	/*	echo '<div class="" role="tabpanel" data-example-id="togglable-tabs">
                      <ul id="myTab1" class="nav nav-tabs bar_tabs right" role="tablist">
                       <li role="presentation" class="'.$g_active.'"><a href="'.base_url().'manager/set_perspective/class" id="home-tabb" >Classification</a>
                        </li>
                        <li role="presentation" class="'.$s_active.'"><a href="'.base_url().'manager/set_perspective"  id="profile-tabb" >Screening</a>
                        </li>
            
            
                      </ul>
                      <div id="myTabContent2" class="tab-content">
	
                      </div>
                    </div>';*/
	}
	}
	
	
	function get_paper_screen_result($paper_id){
		$ci = get_instance();
		$users=	$ci->manager_lib->get_reference_select_values('users;user_name');
		$criteria=$ci->manager_lib->get_reference_select_values('exclusioncrieria;ref_value');
		
		
		$ci->db2 = $ci->load->database(project_db(), TRUE);
		
		
		$sql= "select A.*,S.screening_id,S.decision,S.exclusion_criteria,S.note as screening_note,S.screening_time from assignment_screen A 	LEFT JOIN screening S ON (A.assignment_id = S.assignment_id AND S.	screening_active=1)  where A.paper_id = $paper_id AND 	A.assignment_active  ";
		
		$res_assignment=$ci->db2->query($sql)->result_array();
		
		//print_test($res_assignment);
		
		$pending=0;
		$accepted=0;
		$excluded=0;
		$reviewers="";
		$started=False;
		$exclude_crit=array();
		foreach ($res_assignment as $key => $value) {
				
			$res_assignment[$key]['user_name']=$users[$value['user_id']];
			$reviewers .=$users[$value['user_id']]." | ";
			
			$res_assignment[$key]['exclusion_criteria']=empty($value['exclusion_criteria'])?"":$criteria[$value['exclusion_criteria']];
			
			if (empty($value['screening_id'])){
				$pending++;
			}else{
				if($value['decision']=='accepted'){
					$accepted++;
				}else{
		
					$excluded++;
					$exclude_crit[$value['exclusion_criteria']]=isset($exclude_crit[$value['exclusion_criteria']])?($exclude_crit[$value['exclusion_criteria']]+1):1;
				}
			}
			$started=TRUE;
				
		}
		
		//print_test($exclude_crit);
		$paper_decision='Pending';
		if(!$started){
			$paper_decision="Pending";
		}
		
		elseif($pending>0){
			if($pending == count($res_assignment)){
				$paper_decision="Pending";
			}else{
				$paper_decision="In review";
			}
				
		}else
		{
			
			if(get_appconfig_element('screening_conflict_resolution')=='Majority'){
				
				if($accepted > $excluded){
					
					$paper_decision="Included";
					
				}elseif($accepted < $excluded){
					
					$paper_decision="Excluded";
					
				}else{
					$paper_decision="In conflict";
				}
				
			}else{
				if($accepted==0){
				$paper_decision="Excluded";
				if(get_appconfig_element('screening_conflict_type')=='ExclusionCriteria' AND count($exclude_crit) > 1){ //Conflict when different exclusion criteria
				
					$paper_decision="In conflict";
				
				}
				
			}elseif($excluded==0){
			
				$paper_decision="Included";
			}else{
			
				$paper_decision="In conflict";
			}
			}
		}
		
		
		
		
		$data['screenings']=$res_assignment;
		$data['screening_result']=$paper_decision;
		$data['reviewers']=$reviewers;
		//print_test($data);
		return $data;
	}
	
	function get_paper_screen_status($paper_id){
		$ci = get_instance();
		$ci->db2 = $ci->load->database(project_db(), TRUE);
	
	
		$sql= "select A.*,S.screening_id,S.decision,S.exclusion_criteria,S.note,S.screening_time from assignment_screen A 	LEFT JOIN screening S ON (A.assignment_id = S.assignment_id AND S.	screening_active=1)  where A.paper_id = $paper_id AND 	A.assignment_active  ";
	
		$res_assignment=$ci->db2->query($sql)->result_array();
	
		//print_test($res_assignment);
	
		$pending=0;
		$accepted=0;
		$excluded=0;
		$started=False;
		$exclude_crit=array();
		foreach ($res_assignment as $key => $value) {
				
			if (empty($value['screening_id'])){
				$pending++;
			}else{
				if($value['decision']=='accepted'){
					$accepted++;
				}else{
	
					$excluded++;
					$exclude_crit[$value['exclusion_criteria']]=isset($exclude_crit[$value['exclusion_criteria']])?($exclude_crit[$value['exclusion_criteria']]+1):1;
	
				}
			}
				
			$started=TRUE;
		}
	
	
		$paper_decision='Pending';
		if(!$started){
			$paper_decision="Pending";
		}
	
		elseif($pending>0){
			if($pending == count($res_assignment)){
				$paper_decision="Pending";
			}else{
				$paper_decision="In review";
			}
				
		}else
		{
				
			if(get_appconfig_element('screening_conflict_resolution')=='Majority'){
	
				if($accepted > $excluded){
						
					$paper_decision="Included";
						
				}elseif($accepted < $excluded){
						
					$paper_decision="Excluded";
						
				}else{
					$paper_decision="In conflict";
				}
	
			}else{
				if($accepted==0){
					$paper_decision="Excluded";
					if(get_appconfig_element('screening_conflict_type')=='ExclusionCriteria' AND count($exclude_crit) > 1){ //Conflict when different exclusion criteria
	
						$paper_decision="In conflict";
	
					}
	
				}elseif($excluded==0){
						
					$paper_decision="Included";
				}else{
						
					$paper_decision="In conflict";
				}
			}
		}
	
	
		//print_test($data);
		return $paper_decision;
	}
	
	
	function get_paper_current_decision($paper_id,$screening_phase=1){
		$ci->db2 = $ci->load->database(project_db(), TRUE);
		$sql= "select * from  screen_decison   where paper_id = $paper_id AND screening_phase = $screening_phase AND 	decision_active=1  ";
		$res_desision=$ci->db2->query($sql)->row_array();
		if(empty($res_desision)){
			return 0;
			
		}else{
			return $res_desision['screening_decision'];
		}
		
	}
	
	function get_paper_screen_history($paper_id,$screening_phase=1){
		$ci = get_instance();
		$ci->db2 = $ci->load->database(project_db(), TRUE);
		$query_screen_decision = $ci->db2->get_where('screen_decison', array('paper_id' => $paper_id,'screening_phase' => $screening_phase,'decision_active'=>1), 1)->row_array();
		$array_result=array();
		if(!empty($query_screen_decision['decision_history'])){
			$users=	$ci->manager_lib->get_reference_select_values('users;user_name');
			$criterias=$ci->manager_lib->get_reference_select_values('exclusioncrieria;ref_value');
			$decision_source_array=array(
					'new_screen'=>'Normal screening',
					'edit_screen'=>'Screening edition',
					'conflict_resolution'=>'Conflict resolution',
			);
			$T_json_decisons=explode("~~__", $query_screen_decision['decision_history']);
			
			foreach ($T_json_decisons as $key => $value_json_hist) {
				
				$T_decisons=json_decode($value_json_hist,True);
				
				$decision_source=!empty($decision_source_array[$T_decisons['decision_source']])?$decision_source_array[$T_decisons['decision_source']]:"";
				
				if(!empty($T_decisons['criteria']))
					$criteria=!empty($criterias[$T_decisons['user']])?$criterias[$T_decisons['user']]:"";
				else{
					$criteria="";
				}
				
				
				$user=!empty($users[$T_decisons['user']])?$users[$T_decisons['user']]:"";
				
				array_push($array_result, array(
						
						'user'=>$user,
						'decision'=>$T_decisons['decision'],
						'criteria'=>$criteria,
						'result'=>$T_decisons['paper_status'],
						'operation_source'=>$decision_source,
						'time'=>$T_decisons['screening_time'],
				));
				
					
			
			}
			// adding title
			if(!empty($array_result)){
			array_unshift($array_result,  array(
						
						'user'=>'User',
						'decision'=>'Decision',
						'criteria'=>'Criteria',
						'result'=>'Paper status',
						'operation_source'=>'Operation',
						'time'=>'Time',
				));
			
			}
			
			
			
			
		//	print_test($query_screen_decision);
		}
		
		return $array_result;
		
	}
	function get_paper_screen_status__all($paper_id){
		$ci = get_instance();
		$ci->db2 = $ci->load->database(project_db(), TRUE);
		$users=	$ci->manager_lib->get_reference_select_values('users;user_name');
		$criteria=$ci->manager_lib->get_reference_select_values('exclusioncrieria;ref_value');
		$sql= "select 	decison_id,screening_decision,decision_source,phase_title,phase_type from  screen_decison D,screen_phase P   where paper_id = $paper_id  AND 	screen_phase_id=screening_phase AND	decision_active=1  AND 	screen_phase_active=1 ORDER BY 	screen_phase_order ASC";
		
		$res_assignment=$ci->db2->query($sql)->result_array();
		$array_result=array();
		foreach ($res_assignment as $key => $value_decision) {
		
			
				array_push($array_result, array(
		
						'phase'=>$value_decision['phase_title'],
						'decision'=>$value_decision['screening_decision'],
						'type'=>$value_decision['phase_type'],
						'operation'=>$value_decision['decision_source'],));
		
					
					
		}
		// adding title
		if(!empty($array_result)){
			array_unshift($array_result,  array(
		
					'phase'=>'Phase',
					'decision'=>'Decision',
					'type'=>'Type',
					'operation'=>'Operation',
					
			));
				
		}
		return $array_result;
	}
	
	function get_paper_screen_status_new($paper_id,$screening_phase="",$return = 'paper_status'){
		if(empty($screening_phase))
			$screening_phase= active_screening_phase();
			
		$ci = get_instance();
		$ci->db2 = $ci->load->database(project_db(), TRUE);
	
		///---------
		
		$users=	$ci->manager_lib->get_reference_select_values('users;user_name');
		$criteria=$ci->manager_lib->get_reference_select_values('exclusioncrieria;ref_value');
		
		
		
		///-------
		
		
		//$sql= "select A.*,S.screening_id,S.decision,S.exclusion_criteria,S.note,S.screening_time from assignment_screen A 	LEFT JOIN screening S ON (A.assignment_id = S.assignment_id AND S.	screening_active=1)  where A.paper_id = $paper_id AND 	A.assignment_active  ";
		$sql= "select * from  screening_paper   where paper_id = $paper_id AND screening_phase = $screening_phase AND 	screening_active=1  ";
		
		$res_assignment=$ci->db2->query($sql)->result_array();
	
		
		$pending=0;
		$accepted=0;
		$excluded=0;
		$started=False;
		$exclude_crit=array();
		$reviewers="";
		
		$Veto=array(
				'number'=>0,
				'accepted'=>0,
				'excluded'=>0,
				'pending'=>0,
				'exclude_crit'=>array()
				
		);
		
		$Normal=array(
				'number'=>0,
				'accepted'=>0,
				'excluded'=>0,
				'pending'=>0,
				'exclude_crit'=>array()
		);
		
		$Info=array(
				'number'=>0,
				'accepted'=>0,
				'excluded'=>0,
				'pending'=>0,
				'exclude_crit'=>array()
		);
		
		
		foreach ($res_assignment as $key => $value) {
			//print_test($value);
			//---------
			$res_assignment[$key]['user_name']=$users[$value['user_id']];
			$reviewers .=$users[$value['user_id']]." | ";
			
			
			$res_assignment[$key]['exclusion_criteria']=empty($criteria[$value['exclusion_criteria']])?"":$criteria[$value['exclusion_criteria']];
			//----------
			//$ass_type=$value['assignment_type'];
			${$value['assignment_type']}['number']++;
			
			if (($value['screening_status'] !='Done')){
				$pending++;
				
				${$value['assignment_type']}['pending']++;
				
			}else{
				if($value['screening_decision']=='Included'){
					$accepted++;
					${$value['assignment_type']}['accepted']++;
				}else{
					${$value['assignment_type']}['excluded']++;
					$excluded++;
					${$value['assignment_type']}['exclude_crit'][$value['exclusion_criteria']]=isset(${$value['assignment_type']}['exclude_crit'][$value['exclusion_criteria']])?(${$value['assignment_type']}['exclude_crit'][$value['exclusion_criteria']]+1):1;
					$exclude_crit[$value['exclusion_criteria']]=isset($exclude_crit[$value['exclusion_criteria']])?($exclude_crit[$value['exclusion_criteria']]+1):1;
						
				}
			}
			
			$started=TRUE;
		}
	
	//	print_test($Veto);
	//	print_test($Normal);
	//	print_test($Info);
	
		//if there is a veto the veto value will be used else the normal values
		if(!empty($Veto['number'])){
			$pending=$Veto['pending'];
			$accepted=$Veto['accepted'];
			$excluded=$Veto['excluded'];
			$exclude_crit=$Veto['exclude_crit'];
			
		}else{
			$pending=$Normal['pending'];
			$accepted=$Normal['accepted'];
			$excluded=$Normal['excluded'];
			$exclude_crit=$Normal['exclude_crit'];
		}
		
		
		
		$paper_decision='Pending';
		if(!$started){
			$paper_decision="Pending";
		}
	
		elseif($pending>0){
			if($pending == count($res_assignment)){
				$paper_decision="Pending";
			}else{
				$paper_decision="In review";
			}
			
		}else
		{
			
			if(get_appconfig_element('screening_conflict_resolution')=='Majority'){
				
				if($accepted > $excluded){
					
					$paper_decision="Included";
					
				}elseif($accepted < $excluded){
					
					$paper_decision="Excluded";
					
				}else{
					$paper_decision="In conflict";
				}
				
			}else{
				if($accepted==0){
				$paper_decision="Excluded";
				if(get_appconfig_element('screening_conflict_type')=='ExclusionCriteria' AND count($exclude_crit) > 1){ //Conflict when different exclusion criteria
				
					$paper_decision="In conflict";
				
				}
				
			}elseif($excluded==0){
			
				$paper_decision="Included";
			}else{
			
				$paper_decision="In conflict";
			}
			}
		}
			
		if($return=='all'){
			$data['screenings']=$res_assignment;
			$data['screening_result']=$paper_decision;
			$data['reviewers']=$reviewers;
			//print_test($data);
			return $data;
		}else{
			
			return $paper_decision;
		}
		
	}
	
	
	function update_paper_status_status($paper_id){
		$ci = get_instance();
		$paper_status=get_paper_screen_status($paper_id);
		
		
		if($paper_status=='Included'){
			$ci->db2->update('paper',array('screening_status'=>$paper_status,'classification_status'=>'To classify'),array('id'=>$paper_id));
		}else{
		
			$ci->db2->update('paper',array('screening_status'=>$paper_status,'classification_status'=>'Waiting'),array('id'=>$paper_id));
		}
	}
	
	
	function update_paper_status_all(){
		$ci = get_instance();
		$papers=$ci->DBConnection_mdl->get_papers('screen',get_table_config('papers'),"_",0,-1);
	//	print_test($papers);
		foreach ($papers['list'] as $key => $value) {
			update_paper_status_status($value['id']);
		}
	}
	
	function bm_current_time($format='Y-m-d H:i:s'){
		return date($format);
	}
	
	
	function table_name($table_name){
		$table_index="relis_";
		$table_index="";
		
		return $table_index.$table_name;
		
		
	}
	
	
	
	
	
	// Récupération de la configuration d'une entité
	function get_table_configuration($_table,$target_db='current',$field="")
	{
	
		$ci = get_instance ();
		if(empty($field)){
			return $ci->entity_configuration_lib->get_table_configuration($_table,$target_db);
			}else{
				$tab_config=$ci->entity_configuration_lib->get_table_configuration($_table,$target_db);
				if(!empty($tab_config[$field])){
					return $tab_config[$field];
				}else{
					return NULL;
				}
			}
	}
	
	
	
	function create_table_configuration($config,$target_db='current')
	{
	
		$ci = get_instance ();
	
		return $ci->manage_stored_procedure_lib->create_table_configuration($config,$target_db);
	}
	
	function create_view($config,$target_db='current',$run_query=TRUE,$verbose=FALSE)
	{
	
		$ci = get_instance ();
	
		return $ci->manage_stored_procedure_lib->create_view($config,$target_db,$run_query,$verbose);
	}
	
	function generate_stored_procedure_list($config,$target_db='current',$run_query=TRUE,$verbose=TRUE)
	{
	
		$ci = get_instance ();
	
		return $ci->manage_stored_procedure_lib->generate_stored_procedure_list($config,$target_db,$run_query,$verbose);
	}
	
	function generate_stored_procedure_add($config,$target_db='current',$run_query=TRUE,$verbose=TRUE)
	{
	
		$ci = get_instance ();
	
		return $ci->manage_stored_procedure_lib->generate_stored_procedure_add($config,$target_db,$run_query,$verbose);
	}
	
	
	function generate_stored_procedure_update($config,$target_db='current',$run_query=TRUE,$verbose=TRUE)
	{
	
		$ci = get_instance ();
	
		return $ci->manage_stored_procedure_lib->generate_stored_procedure_update($config,$target_db,$run_query,$verbose);
	}
	
	function generate_stored_procedure_detail($config,$target_db='current',$run_query=TRUE,$verbose=TRUE)
	{
	
		$ci = get_instance ();
	
		return $ci->manage_stored_procedure_lib->generate_stored_procedure_detail($config,$target_db,$run_query,$verbose);
	}
	
	function generate_stored_procedure_remove($config,$target_db='current',$run_query=TRUE,$verbose=TRUE)
	{
	
		$ci = get_instance ();
	
		return $ci->manage_stored_procedure_lib->generate_stored_procedure_remove($config,$target_db,$run_query,$verbose);
	}
	
	function get_active_phase(){
		return "Screening";
	}

	function get_active_screening_phase(){
		return "Screening";
	}
	function active_screening_phase(){
		$ci = get_instance() ;	
		
		if($ci->session->userdata ( 'current_screen_phase' ))
			return $ci->session->userdata ( 'current_screen_phase' );
			else
			return '';
	}
	
	function active_screening_phase_info(){
		$ci = get_instance() ;
		
		$res=$ci->db_current->get_where('screen_phase',
						array('screen_phase_id' => active_screening_phase()), 1)->row_array();
						
		return $res;
	
	}
	
	function screening_validation_source_paper_status(){
		
		return 'Excluded';
	
	}
	
	function screening_validator_assignment_type(){
	
		return get_appconfig_element('screening_validator_assignment_type');
	
	}
	
	function debug_comment_button(){
		$ci = get_instance() ;
		$dir=$ci->router->fetch_directory();
		$class=$ci->router->fetch_class();
		$method=$ci->router->fetch_method();
	
		if($method=='entity_list'
				OR	$method=='display_element'
				OR	$method=='add_element'
				OR	$method=='add_element_child'
				OR	$method=='add_element_drilldown'
				OR	$method=='edit_element'
				OR	$method=='edit_drilldown'
				OR	$method=='delete_element'
				OR	$method=='delete_drilldown'
				){
						
					$method.='_'.$ci->uri->segment(3);
		}
		$url=current_url();
	
		$paper_code=slug($dir.$class.$method);
		$ci->session->set_userdata('debug_paper_code',$paper_code);
		$ci->session->set_userdata('debug_paper_url',$url);
	
	
		$add_button='<a  class="btn btn-xs btn-warning" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"
									data-modal_link="op/add_element_modal/add_debug"
									data-modal_title=" Debub comment" ><i class="fa fa-plus"></i>Add comment</a>';
	
		return $add_button;
	
	
	}
	
	
	function debug_comment_display(){
		$ci = get_instance() ;
		$dir=$ci->router->fetch_directory();
		$class=$ci->router->fetch_class();
		
		$method=$ci->router->fetch_method();
		
		if($method=='entity_list' 
			 OR	$method=='display_element'
			 OR	$method=='add_element'
			 OR	$method=='add_element_child'
			 OR	$method=='add_element_drilldown'
			 OR	$method=='edit_element'
			 OR	$method=='edit_drilldown'
			 OR	$method=='delete_element'
			 OR	$method=='delete_drilldown'
				){
			
			$method.='_'.$ci->uri->segment(3);
		}
		$url=current_url();
		
		$paper_code=slug($dir.$class.$method);
		$ci->session->set_userdata('debug_paper_code',$paper_code);
		$ci->session->set_userdata('debug_paper_url',$url);
		
		
		$add_button='<a  class="btn btn-xs btn-info" data-toggle="modal" data-target="#relisformModal" data-operation_type="2"
									data-modal_link="op/add_element_modal/add_debug"
									data-modal_title=" Debub comment" ><i class="fa fa-plus"></i>Add</a>';
		
	//	echo "<div>".$add_button."</div>";
		
		
		$sql="select debug_id,title,comment,creation_time,status from debug where page_code like '$paper_code' AND debug_active=1 ";
		
		$res=$ci->db_current->query($sql)->result_array();
		
		
		
		foreach ($res as $key => $value) {
			$res[$key]['link']=anchor('op/display_element/detail_debug/'.$value['debug_id'],'<u><b>Display</b></u>');
			unset($res[$key]['debug_id']);
		}
		
		if(!empty($res)){
			
			array_unshift($res, array('Title','Comment','Time','Status',''));
			//print_test($res);
			
			$tmpl = array (
					'table_open'  => '<table class="table table-striped table-hover">',
					'table_close'  => '</table>'
			);
			
			$ci->table->set_template($tmpl);
			echo " <br/> <hr/> <h3>Debug comments</h3>";
			echo $ci->table->generate($res);
			
			
		}
		
	
	}
	
	function get_debug_info($element){
		$ci = get_instance() ;
		return $ci->session->userdata($element);
	}
	
	//default caraters to display on list
	function trim_nbr_car(){
		$res=get_appconfig_element('list_trim_nbr');
		
		return $res;
	}
	
	function path_separator(){
		$syst=get_ci_config('server_OS');
		if($syst=='WINDOWS'){
			return '\\';
		}else{
			return '/';
		}
	}
	
