 <!-- Select2 -->
    <script src="<?php echo site_url();?>cside/vendors/select2/dist/js/select2.full.min.js"></script>
 <?php 
 			//print_test($table_config);
 			$current_operation=$table_config['current_operation'];
 			
 			if(validation_errors() OR !empty($err_msg))
				{
				 echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
					<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>
					<strong>'.lng('Error').'!</strong>';
							echo validation_errors();
							 if (isset($err_msg))echo $err_msg;
							echo "</div>";
				}
               // print_test($table_config);           	
				
				$redirect_after_save=isset($table_config['operations'][$current_operation]['redirect_after_save'])?$table_config['operations'][$current_operation]['redirect_after_save']:'home';
                  
				
				
				echo form_hidden(array( 'operation_type' => isset($operation_type)?$operation_type:'new'));
                    echo form_hidden(array( 'table_config' => $table_config['config_id']));
                    echo form_hidden(array( 'current_operation' => $table_config['current_operation']));
                    echo form_hidden(array( 'redirect_after_save' => $redirect_after_save));
                    echo form_hidden(array( 'operation_source' => isset($operation_source)?$operation_source:'own'));
                    echo form_hidden(array( 'child_field' => isset($child_field)?$child_field:''));
                    echo form_hidden(array( 'table_config_parent' => isset($table_config_parent)?$table_config_parent:''));
                    echo form_hidden(array( 'parent_id' => isset($parent_id)?$parent_id:''));
                    echo form_hidden(array( 'parent_field' => isset($parent_field)?$parent_field:''));
                    echo form_hidden(array( 'parent_table' => isset($parent_table)?$parent_table:''));
                    
                   
                    
                    /*if($table_config['table_name']=='relis_ref_values'){
                    	echo form_hidden(array( 'ref_category' => $table_config['config_id']));
                    	
                    }*/
                    
                   // print_test($content_item);
                    
                    if($operation_type=='new'){
                    	$var_check="on_add";
                    }else{
                    	
                    	$var_check="on_edit";  
                    	if($table_config['config_id']=="users"){
                    		
                    		echo form_hidden(array( 'user_password_old' => isset($content_item['user_password'])?$content_item['user_password']:''));
                    		echo form_hidden(array( 'user_picture_old' => isset($content_item['user_picture'])?$content_item['user_picture']:''));
                    		
                    	}
                    }
                    
                    foreach ($table_config['operations'][$current_operation]['fields'] as $key => $v_field) {
                    	$operation_field_value="_";
                    	
                    	$value=$table_config['fields'][$key];
                    	
                    	if(!empty($v_field['field_value'])){// a default value for the specific operation
                    		$value['field_value']=$v_field['field_value'];
                    		$operation_field_value=$v_field['field_value'];
                    	}
                    	if(!empty($v_field['field_title'])){
                    		$value['field_title']=$v_field['field_title'];
                    	}
                    		
						$value['field_title']=lng($value['field_title']);
						
						if($v_field['field_state']=='drill_down'){
							
						//	$value['field_value']='Wait for It ;)';
							if($var_check=='on_add'){
								echo form_bm_just_test($value['field_title'],'<i>'.lng('This field will  be enabled on update').'</i>');
							}else{
								
								if(!empty($drill_down_values[$key])){
									
									$text= "<table  class=' table-hover' style='width:100% ; border:1px solid #CCCCCC;  '>";
									foreach ($drill_down_values[$key] as $key_drill => $values_drill) {
										
										$text.="<tr><td style=' padding:3px 3px; ' >".$values_drill."</td></tr>";
									}
									$text.= "</table>";
									
									echo form_bm_just_test($value['field_title'],$text);
								}
								
								
							}
						}else{
							
                    		$initial_value=(!empty($value['field_value']))?$value['field_value']:"";
                    		$initial_value=($initial_value=='auto_increment')?"":$initial_value;
                    		//usee
                    		if((!empty($value['field_value'])) AND $value['field_value']=='active_user'){
                    			
                    			$user_id= $this->session->userdata('user_id');
                    			$content_item[$key]=$user_id;
                    		
                    		}
                    
                    		//Setting difault value for current operation 
                    		if(isset($operation_field_value) AND $operation_field_value!="_"){
                    			
                    			//if(isset($content_item[$key]) AND trim($content_item[$key])==""){
                    				$content_item[$key]=$operation_field_value;
                    			//}
                    		}
                    		
                    		if($v_field['field_state']=='hidden'){	
                    			
                    		echo form_hidden(array( $key => isset($content_item[$key])?$content_item[$key]:$initial_value));
                    		
                    		}else {
                    			
	                    		if($v_field['field_state']=='disabled'){	
	                    			$readonly='readonly';
	                    		
	                    		}else{
	                    			$readonly=' ';
	                    		}
	                    		
	                    	if(!empty($value['field_size'])){
	                    		$size=$value['field_size'];
	                    	}else{
	                    		$size=100;
	                    	}
	                    	
	                    	if(!empty($v_field['mandatory'])){
	                    		$mandatory=" mandatory ";
	                    	}else{
	                    		$mandatory=" ";
	                    	}
                    		
	                    	if(!empty($value['extra_class'])){
	                    		$extra_class=" ".$value['extra_class']. " ";
	                    	}else{
	                    		$extra_class=" ";
	                    	}
	                    	
	                    	if(!empty($value['place_holder'])){
	                    		$place_holder=" ".$value['place_holder']. " ";
	                    	}else{
	                    		$place_holder=" ";
	                    	}
                    		
	                    	if(!empty($value['input_type'])){
	                    		$input_type=$value['input_type'];
	                    	}else{
	                    		$input_type="text";
	                    	}
                    		
	                    	
	                    	$pattern=(!empty($value['pattern']))?$value['pattern']:"";
	                    		
	                    	$pattern_info=(!empty($value['pattern_info']))?$value['pattern_info']:"";
	                    
	                    	$number_of_values=(!empty($value['number_of_values']))?$value['number_of_values']:"1";

	                    	if(!empty($value['field_type']) AND ($value['field_type'] =='number' OR $value['field_type'] =='int'  OR $value['field_type'] =='real') AND ($input_type == 'text')  ){
	                    		$extra_class .= " droite ";
	                    	}
                    		
                    		if($input_type=='password'){
                    			echo input_password_bm($value['field_title'],$key,$key,"",$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			if($table_config['config_id']=="users"){
                    				echo input_password_bm($value['field_title']." ".lng("Confirmation"),$key."_val",$key."_val","",$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			}
                    		}elseif($input_type=='textarea'){

                    			echo input_textarea_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:$initial_value,$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			
                    			
                    		}elseif($input_type=='date'){

                    			echo input_datepicker_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:$initial_value,$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			
                    			
                    		}elseif($input_type=='color'){

                    			echo input_colorpicker_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:$initial_value,$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			
                    			
                    		
                    		}elseif($input_type=='image'){

                    			// for image edit save the old value and use it when used does not choose another image
                    			if($var_check=="on_edit"){
                    			//echo "zzzzzzzzzzzzzzzzzzzzzz".$content_item[$key];
                    				//echo form_hidden(array( $key.'_zsaved' => isset($content_item[$key])?$content_item[$key]:''));
                    				//echo form_hidden(array( $key.'_zsaved' => isset($content_item[$key])?$content_item[$key]:''));
                    			
                    			}
                    			 echo input_image_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:'',$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			
                    			
                    		}elseif($input_type=='select'){
                    			
                    			if($value['input_select_source']=='yes_no' ){
                    				
                    			echo checkbox_form_bm($value['field_title'],$key,$key,$value['input_select_values'],isset($content_item[$key])?$content_item[$key]:'',$extra_class.$mandatory,$readonly);
                    				 
                    			}else{
                    			
                    			if(isset($value['multi-select']) AND $value['multi-select']=='Yes'){
                    				echo dropdown_multi_form_bm($value['field_title'],$key,$key,$value['input_select_values'],isset($content_item[$key])?$content_item[$key]:'',$extra_class.$mandatory,$readonly,$number_of_values);
                    				 
                    			}else{
                                    $value['input_select_values'] = array(""=>"Select...")+$value['input_select_values'] ;
                    				echo dropdown_form_bm($value['field_title'],$key,$key,$value['input_select_values'],isset($content_item[$key])?$content_item[$key]:'',$extra_class.$mandatory,$readonly);
                    				 
                    			}

                    				
                    			}
                    		}else{
                    			echo input_form_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:$initial_value,$size, $extra_class.$mandatory ,$readonly,$place_holder,$pattern,$pattern_info);
                    			 
                    			
                    		}
                    		
                    	?>
                    	
                    	
                    	
                    	<?php 
                    		
                    		
                    		}
                    	}
                    	
                    }
                    
                   
                    
                    ?>
                    
                