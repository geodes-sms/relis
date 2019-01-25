	<!-- page content -->
        <div class="right_col" role="main">
        <?php top_msg(); ?>
         <?php 
         if (!empty($message_error)) {
         	echo '<br/><br/><br/><div class="alert alert-danger alert-dismissible fade in" role="alert">
				<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>' . $message_error . '
			</div>';
         
         	
         } elseif (!empty($message_success)) {
         	echo '<br/><br/><br/><div class="alert alert-success alert-dismissible fade in" role="alert">
				<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>' . $message_success . '
			</div>';
         
         }
         
         ?>
        
          <div class="">
            
            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" style="min-height:600px;">
                  <div class="x_title">
                   <h2><?php echo !empty($title)?lng($title):""?></h2> 
                   <?php 
 if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    
                    }  ?>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content" style="min-height:400px ">
				
                  <?php 
				  if(!empty($commands)){
					  echo "<ul>";
					  foreach ($commands as $key=>$value){
						  echo "<li>";
						  echo anchor('home/manage_editor/'.$key,$key);
						  echo "</li>";
					  }
					  echo "</ul>";
				  }
				  
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
                 if(!empty($allow_manual_sript)){
                  $attributes = array('class' => 'form-horizontal');
                  echo form_open('home/manage_editor',$attributes);
                 echo form_hidden(array( 'command_type' => 'other'));
                  $checked="";
                  if(isset($return_table) and $return_table)
                  	$checked=" checked ";
                  ?>
                  
                  
                  <div class="form-group">
                        <?php 
                        
                        	echo input_form_bm('Other command','script','script','',1000);
                       
                        ?>
                        
                   </div>
            
                  <div class="ln_solid"></div>
                      <div class="form-group">
                                               
                        
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          
                          <button type="submit" class="btn btn-success"><?php echo lng('Submit') ?></button>
                        </div>
                  </div>
                  
                  <?php 
                  echo form_close();
                 }
				 
                  if(isset($command_response)){
					echo "
					<hr/>
					<div>".$command_response."</div>";
                  
                  }
                  ?>
                   
        
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->