        <!-- page content -->
        <div class="right_col" role="main">
          
          <?php top_msg(); 
          
           if(!empty($home_help)){

           	box_header("Getting Started","",12,12,12);
          ?>
          
            <div class="row">
 
             <div class="dashboard-widget-content what_relis">

                    <ul class="list-unstyled timeline widget">
                    <?php 
                    foreach ($home_help as $key => $feature) {
                    ?>
                    
                    <li>
                        <div class="block" >
                          <div class="block_content">
                            <h2 class="title">
                             <a href="<?php echo base_url()?>user/help_det/<?php echo $feature['info_id']?>"><?php echo !empty($feature['info_title'])?$feature['info_title']:""?></a>
                            </h2>
                            <div class="byline"></div>
                            <p class="excerpt">
                          
                            </p>
                          </div>
                        </div>
                      </li>
                      
                    <?php 
                    }
                    
                    ?>
                      
                     
                    </ul>
                    
                    <p>For support please contact:  <i> <?php echo get_ci_config('support_contact')?></i></p>
              </div>
            	
                            
            </div> 
          
          
          <?php 
          	box_footer();
         
           }
          
          ?>

           
          </div>
        <!-- /page content -->