	<!-- page content -->
        <div class="right_col" role="main">
          <div class="">
          
          <div class="page-title">
              
              <?php 
                   if(isset($search_view)){
                   		$this->load->view($search_view);
                   	}
                ?>
              
            </div>
            
            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" >
                  <div class="x_title">
                    <h2><?php echo isset($page_title) ? $page_title :"" ; ?></h2>
                    <?php 
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    
                    }                    
                    ?>
                    
                    
                    
                    <div class="clearfix"></div>
                  </div>
                  
                  
                  
                  <div class="x_content" style="min-height:400px ">
                  
                 
                  <?php
                            	
                            	
                if(validation_errors() OR !empty($err_msg))
				{
				 echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
					<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>
					<strong>Error!</strong>';
							echo validation_errors();
							 if (isset($err_msg))echo $err_msg;
							echo "</div>";
				}
                            	
                    
                    $attributes = array('class' => 'form-horizontal');
                    //echo form_open('manage/save_ref',$attributes);
                    echo form_open_multipart('manage/save_ref',$attributes);
                    echo form_hidden(array( 'operation_type' => isset($operation_type)?$operation_type:'new'));
                    echo form_hidden(array( 'table_config' => $table_config['config_id']));
                    echo form_hidden(array( 'operation_source' => isset($operation_source)?$operation_source:'own'));
                    echo form_hidden(array( 'child_field' => isset($child_field)?$child_field:''));
                    echo form_hidden(array( 'table_config_parent' => isset($table_config_parent)?$table_config_parent:''));
                    echo form_hidden(array( 'parent_id' => isset($parent_id)?$parent_id:''));
                    echo form_hidden(array( 'parent_field' => isset($parent_field)?$parent_field:''));
                    echo form_hidden(array( 'parent_table' => isset($parent_table)?$parent_table:''));
                    
                    if($table_config['table_name']=='relis_ref_values'){
                    	echo form_hidden(array( 'ref_category' => $table_config['config_id']));
                    	
                    }
                    
                    if($operation_type=='new'){
                    	$var_check="on_add";
                    }else{
                    	$var_check="on_edit";              	
                    }
                    
                    foreach ($table_config['fields'] as $key => $value) {
                    	
                    	if($value[$var_check]!='not_set'){

                    		
                    		if($value['field_value']=='active_user'){	
                    			$user_id= $this->session->userdata('user_id');
                    		echo form_hidden(array( $key => $user_id));
                    		
                    		}elseif($value[$var_check]=='hidden'){	
                    		echo form_hidden(array( $key => isset($content_item[$key])?$content_item[$key]:''));
                    		
                    		}else {
                    			
	                    		if($value[$var_check]=='disabled'){	
	                    			$readonly='readonly';
	                    		
	                    		}else{
	                    			$readonly=' ';
	                    		}
	                    		
	                    	if(!empty($value['field_size'])){
	                    		$size=$value['field_size'];
	                    	}else{
	                    		$size=100;
	                    	}
	                    	
	                    	if(!empty($value['mandatory'])){
	                    		$mandatory=" ".$value['mandatory']. " ";
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
                    		
	                    	

	                    	if(!empty($value['field_type']) AND $value['field_type'] =='number' AND  $input_type=='text'){
	                    		$extra_class .= " droite ";
	                    	}
                    		
                    		if($input_type=='password'){
                    			echo input_password_bm($value['field_title'],$key,$key,"",$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			echo input_password_bm($value['field_title']." Confirmation ",$key."_val",$key."_val","",$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    		
                    		}elseif($input_type=='textarea'){

                    			echo input_textarea_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:'',$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			
                    			
                    		}elseif($input_type=='date'){

                    			echo input_datepicker_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:'',$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			
                    			
                    		}elseif($input_type=='color'){

                    			echo input_colorpicker_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:'',$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			
                    			
                    		
                    		}elseif($input_type=='image'){

                    			echo input_image_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:'',$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			
                    			
                    		}elseif($input_type=='select'){
                    			
                    			if($value['field_value']=='0_1' ){
                    				
                    			echo checkbox_form_bm($value['field_title'],$key,$key,$value['input_select_values'],isset($content_item[$key])?$content_item[$key]:'',$extra_class.$mandatory,$readonly);
                    				 
                    			}else{
                    			
                    			if(isset($value['multi-select']) AND $value['multi-select']=='Yes'){
                    				echo dropdown_multi_form_bm($value['field_title'],$key,$key,$value['input_select_values'],isset($content_item[$key])?$content_item[$key]:'',$extra_class.$mandatory,$readonly);
                    				 
                    			}else{
                    				echo dropdown_form_bm($value['field_title'],$key,$key,$value['input_select_values'],isset($content_item[$key])?$content_item[$key]:'',$extra_class.$mandatory,$readonly);
                    				 
                    			}

                    				
                    			}
                    		}else{
                    			echo input_form_bm($value['field_title'],$key,$key,isset($content_item[$key])?$content_item[$key]:'',$size, $extra_class.$mandatory ,$readonly,$place_holder);
                    			 
                    			
                    		}
                    		
                    	?>
                    	
                    	
                    	
                    	<?php 
                    		
                    		
                    		}
                    		
                    	}
                    }
                    
                    //echo input_ysiwyg_bm();
                    //add extra fields
                    
                   
                    if(!empty($extra_fields)){
                    
                    //	echo "<table class='table table-striped'>";
                    	foreach($extra_fields as $k=>$v){
                    
                    	//	echo "<tr>";
                    		echo '<div class="form-group">';
                    	//	echo "<th style='width:20%'>".$v['title']."</th>";
                    		echo '<label class="control-label col-md-3 col-sm-3 col-xs-12">'.$v['title'].'</label>';
                    		if(empty($v['val2']) OR count($v['val2'])==0){
                    			//echo "<td></td>";
                    			echo '<div class="col-md-6 col-sm-6 col-xs-12"></div>';
                    		}elseif(count($v['val2'])==1){
                    			//echo "<td>".$v['val2'][0]."</td>";
                    			echo '<div class="col-md-6 col-sm-6 col-xs-12">'.$v['val2'][0]."</div>";
                    		}else{
                    			//echo '<td> <table class="table table-hover">';
                    			echo '<div class="col-md-6 col-sm-6 col-xs-12"> <table class="table table-hover">';
                    			foreach ($v['val2'] as $key => $value) {
                    				echo "<tr><td style='border-top:0px; border-bottom:1px solid #ddd'> - $value</tr></td>";
                    			}
                    				
                    			//echo "</table></td>";
                    			echo "</table></div>";
                    			//echo "<tr>";
                    			echo "</div>";
                    		}
                    	}
                    	//echo "</table>"."<br/><br/>";
                    
                    }
                    ?>
                    
                   
                      
                      
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          <button type="reset" class="btn btn-primary">Reset</button>
                          <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                      </div>
                    
                    
                    
                    
                    <?php 
                    
                    
                    echo form_close();
                    
					
					?>
                   
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->