	<!-- page content -->
        <div class="right_col" role="main" >
         <!-- gauge.js -->
    <script src="<?php echo site_url();?>cside/vendors/bernii/gauge.js/dist/gauge.min.js"></script>
    
        <?php top_msg();  ?> 
        
        <div class="row" >
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="dashboard_graph">

                <div class="row x_title">
                 <?php  //header_perspective('screen');?>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 bg-white">
                  <div class="x_title">
                    <h3> <?php echo lng('Project').' : '.$configuration['project_title'] ." - Quality assessment  "?> </h3>
                    <div class="clearfix"></div>
                  </div>

                  <?php
                  
                  box_header('Actions','',12,12);
                  	if(!empty($action_but_screen)){
                  		box_header('Quality assessment','',12,12);
                  ?>
                  
                    
                    
                      <?php
                     
                      	foreach ($action_but_screen as $key => $button) {
                      		echo $button."<div class='col-md-1 col-sm-1 col-xs-1'></div>";
                      	}
                      	
                     ?>
                     
                  
                <?php 
                box_footer();
                  	} ?> 
                
                 <?php
                  	if(!empty($action_but_validate)){
                  		box_header('Validation','',12,12);
                  ?>
                  
                    
                    
                      <?php
                     
                      	foreach ($action_but_validate as $key => $button) {
                      		echo $button."<div class='col-md-1 col-sm-1 col-xs-1'></div>";
                      	}
                      	
                     ?>
                     
                  
                <?php 
                box_footer();
                  	} 
                box_footer()
                ?> 
                  
                
                </div>
                

                <div class="clearfix"></div>
              </div>
            </div>

          </div>
          
          
          <br />
          <div class="row">
            
            <?php 
            if(!empty($qa_completion))
            add_completion_gauge($qa_completion,'id_qa_completion');
            
             if(!empty($qa_completion_val))
            add_completion_gauge($qa_completion_val,'id_validation_completion');
           
            
            ?>
                 
       <!--  </div>
         <div class="row">
         -->     
            <?php 
            if(!empty($gen_qa_completion))
            add_completion_gauge($gen_qa_completion,'id_gen_screen_completion');
            
             if(!empty($gen_qa_completion_val))
            add_completion_gauge($gen_qa_completion_val,'id_gen_validation_completion');
           
            
            ?>
                 
         </div>
        </div>
        
        <!-- /page content -->