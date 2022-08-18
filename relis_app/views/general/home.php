	<!-- page content -->
        <div class="right_col" role="main">
        <!-- gauge.js -->
    <script src="<?php echo site_url();?>cside/vendors/bernii/gauge.js/dist/gauge.min.js"></script>
     
        <?php top_msg();  ?> 
        
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="dashboard_graph">

                <div class="row x_title">
                 <?php  header_perspective();?>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 bg-white">
                  <div class="x_title">
                    <h3> <?php echo lng('Project').' : '.$configuration['project_title'] ?> - Classification</h3>
                    <div class="clearfix"></div>
                  </div>

                 
                   <?php
                  
                  box_header('Actions','',12,12);
                  	if(!empty($action_but_screen)){
                  		box_header('Classification','',12,12);
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
            if(!empty($classification_completion))
            add_completion_gauge($classification_completion,'id_completion');
            if(!empty($my_validation_completion))
            	add_completion_gauge($my_validation_completion,'id_my_completion');
            	 
            
            if(!empty($gen_classification_completion))
            add_completion_gauge($gen_classification_completion,'id_gen_completion');
            
            
            if(!empty($gen_validation_completion))
            		add_completion_gauge($gen_validation_completion,'id_gen_validation_completion');
            ?>
           
         </div>
        
        </div>
        