	<!-- page content -->
        <div class="right_col <?php echo $paper_excluded?" red ":""; ?>" role="main">
          <div class="">
          <?php top_msg(); ?>
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
                   //print_test($item_data);         	
                            	
                  $tmpl = array (
                  		'table_open'  => '<table class="table table-striped projects">',
                  		'table_close'  => '</table>'
                  );
                  	
                  $this->table->set_template($tmpl);
                  	
                  echo $this->table->generate($item_data);
                  
             ?>
				
				
				
				
				
            
                
                
                  </div>
                  
                  
                 
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->