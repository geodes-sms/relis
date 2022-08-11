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
          
           echo box_header("Result");
           
           $tmpl = array (
           		'table_open'  => '<table class="table table-striped table-hover">',
           		'table_close'  => '</table>'
           );
           
           $this->table->set_template($tmpl);
           if(!empty($screening_result))
           {
           		echo $this->table->generate($screening_result);
           }else{
                  echo "<p>Empty!</p>";
           } 
           
           
          
           echo  box_footer();
            
           if(isset($kappa) ){
	           echo box_header("Agreement : ".$kappa_meaning);
	            
	           //echo "<h1>$kappa_meaning</h1>";
	           echo "<h2>Kappa: $kappa</h2>";
	           echo  box_footer();
           }
           
           
           echo box_header("Decision per user");
         
           $this->table->set_template($tmpl);
           if(!empty($result_per_user))
           {
           	echo $this->table->generate($result_per_user);
           }else{
                    	echo "<p>Empty!</p>";
                    							} 
         
           echo  box_footer();
           
           
           

           ?>
                            </div>
                             <div class="row">
                       <?php 
           if(!empty($screening_conflict_resolution))
           {

	           echo box_header("Conflict resolution");                
	           $this->table->set_template($tmpl);           
	           echo $this->table->generate($screening_conflict_resolution); 
	           echo  box_footer();
            
           }
           
       
           
           echo box_header("Statistics on Exclusion Criteria");
           $tmpl = array (
           		'table_open'  => '<table class="table table-striped table-hover">',
           		'table_close'  => '</table>'
           );
            
           $this->table->set_template($tmpl);
           if(!empty($result_per_criteria))
           {
           	echo $this->table->generate($result_per_criteria);
           }else{
           	echo "<p>Empty!</p>";
           }
           echo  box_footer();

           echo box_header("Statistics on Inclusion Criteria");
           $tmpl = array (
                   'table_open'  => '<table class="table table-striped table-hover">',
                    'table_close'  => '</table>'
           );
           $this->table->set_template($tmpl);
                       if(!empty($result_per_criteria_two))
                       {
                           echo $this->table->generate($result_per_criteria_two);
                       }else{
                           echo "<p>Empty!</p>";
                       }

           echo  box_footer();



                       ?>
                  
                  
                  
                  </div>
                
                
                
                
                
                
                
                   
                   
                   
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->