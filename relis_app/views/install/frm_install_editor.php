	<!-- page content -->
        <div class="right_col" role="main">
          <div class="">
          
          <div class="page-title">
              
             
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
                   
                    echo form_open_multipart('install/save_install_form_editor',$attributes);
                   
                
                    
                    if(!empty($project_result) AND empty($project_published)){
                    	echo "<div class='form-group '><label id='selected_config' for='selected_config' class='control-label col-md-3 col-sm-3 col-xs-12'> Select the configuration file generated</label>
                  		<div class='col-md-6 col-sm-6 col-xs-12'>
                  		<select id='selected_config' name='selected_config' class=' select2_group form-control  '>
                  		
                  		";
                    $path_separator=path_separator();// used to diferenciate windows and linux server
                    foreach ($project_result as $project => $project_detail) {
						$dir=$project_detail['dir'];
						
						if(!empty($project_detail['generated'])){
							echo "<optgroup label='".$project."'>";
							foreach ($project_detail['generated'] as $key => $value) {
								echo  "<option value='".$dir.$path_separator."src-gen".$path_separator.$value."'>$value</option>";
							}
							echo "</optgroup>";
						}
					
					}
                    	echo "</select>
                   			</div>
							</div>";
                     
                    ?>
                    
                   
                      
                      
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                      </div>
                    
                    
                    
                    
                    <?php 
                    
                    }
                    echo form_close();
                    
					
					?>
                   
                  </div>
                   
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->