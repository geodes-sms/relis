	<!-- Select2 -->
    <script src="<?php echo site_url();?>cside/vendors/select2/dist/js/select2.full.min.js"></script>
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
                  
                  
                  
                  <div class="row">
                  
                  
             
            
           <?php 
           if(!empty($completion_screen)){
           echo box_header("Completion","",12,12,12);
           foreach ($completion_screen as $key => $value) {
           	echo $value['user'].":  ".$value['papers_screened']." / ".$value['total_papers']." -> ". $value['completion']."%";
           ?>
           
            <div class="progress">
           <div class="progress-bar progress-bar-success" data-transitiongoal="20" style=" width:<?php echo $value['completion']?>%" aria-valuenow="20"></div>
           </div>
           
           <?php 
           	
           	
           }
           
           ?>
                     
           
            <?php 
           echo  box_footer();
            
           }
           
           
           
           
           ?>
                  
                  
                  
                  </div>
                
                
                
                
                
                
                
                
                   
                   
                   
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->