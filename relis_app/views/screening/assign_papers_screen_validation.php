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
                     <?php  //header_perspective('screen');?>
                    <h2><?php echo isset($page_title) ? $page_title :"" ; ?></h2>
                    <?php 
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    
                    }                    
                    ?>
                    
                    
                    
                    <div class="clearfix"></div>
                  </div>
                  
                  
                  
                  <div class="x_content" style="min-height:400px ">
                  
                 <div class="tab-pane active" id="home">
                        
                          <p><b> Number of papers to assign :<?php echo $number_papers ?></b>
                           <br/><i> Number of papers already assigned :<?php echo $number_papers_assigned ?></i><br/></p>
                          <?php 
                         $attributes = array('class' => 'form-horizontal form_content');
                         echo form_open_multipart('screening/save_assign_screen_validation',$attributes);
                         
                         
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
                         
                         echo form_hidden(array( 'number_of_users' => count($users)));
                         echo form_hidden(array( 'screening_phase' => $screening_phase));
                         echo form_hidden(array( 'papers_sources' => $papers_sources));
                         echo form_hidden(array( 'paper_source_status' => $paper_source_status));
                         
                       
                        if(empty($assign_to_connected)){
                        	echo ' <p class="lead">Select validator(s) </p>';
                        	$i=1;
		                        foreach ($users as $user_id => $user_name) {
		                        	echo checkbox_form_bm($user_name,'user_'.$i,'user_'.$user_id,$user_id);
		                        	$i++;
		                        }
		                       
		                  
		                         echo "<hr/>";
                        }else{
                        	echo form_hidden(array( 'assign_papers_to' => active_user_id()));
                        	
                        }
                         
                    //     echo dropdown_form_bm("Paper to validate",'paper_to_validate','paper_to_validate',$papers_categories,'Excluded',' 1 mandatory');
                          
                        echo input_form_bm(lng('Percentage of papers(%)'),'percentage','percentage',$percentage_of_papers);
                         
                        
                    ?>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          
                          <button  class="btn btn-success">Assign</button>
                        </div>
                      </div>
                    
                    <?php 
                    echo form_close();
                    
                    echo "<hr/>";
                    echo "<h2>Preview of papers to assign</h2>";
                    
                    if(!empty($paper_list)){
                    	$tmpl = array (
                    			'table_open'  => '<table class="table table-striped table-hover">',
                    			'table_close'  => '</table>'
                    	);
                    	 
                    	$this->table->set_template($tmpl);
                    
                    	echo $this->table->generate($paper_list);
                    }
                    
                    ?>
                   
                        </div>
                  



                    <div class="clearfix"></div>
                   
                  </div>
                  
                 
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->