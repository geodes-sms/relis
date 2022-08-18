	<!-- page content -->
        <div class="right_col" role="main">
         <?php top_msg();    ?> 
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
                 
                 $attributes = array('class' => 'form-horizontal form_content');
                  //echo form_open('manage/save_ref',$attributes);
                 $fct_save=isset($save_function)?$save_function:'manager/save_element';
                 
                // echo  old_version('test save image');
                 //$fct_save='manager/save_element_picture';
                 
                 echo form_open_multipart($fct_save,$attributes);
                
                
                            	
                $this->load->view('general/frm_reference_body');
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