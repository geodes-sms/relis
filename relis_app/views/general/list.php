	<!-- page content -->
        <div class="right_col brice" role="main">
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
                    <h2><?php echo isset($page_title) ? $page_title:"" ; ?></h2>
                    <?php 
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    
                    }                    
                    ?>
                    
                    
                    
                    <div class="clearfix"></div>
                  </div>
                  
                  
                  
                  <div class="x_content" style="min-height:400px ">
                  
                  

                    <?php 
                    
                    if(isset($nav_pre_link)) {//Navigation links
                    
                    	$nav_page_position=isset($nav_page_position)?$nav_page_position:3;
                    		
                    	?>
                    		<div id="nav1" class="col-md-5 col-xs-12 dataTables_paginate ">
                    	<?php
                    											
                    		$this->bm_lib->get_pagination($nav_pre_link,$nombre,$nav_page_position);
                    											?>											
                    		</div>
                    	<?php 
                    					}
                    
                    
                    $tmpl = array (
                    		'table_open'  => '<table class="table table-striped table-hover">',
                    		'table_close'  => '</table>'
                    );
                    
                    $this->table->set_template($tmpl);
                    if(isset($nombre) AND $nombre>0)
                    {
                    	echo $this->table->generate($list);
                    }else{
                    	echo "<p>No records found !</p>";
                    							} 
					
					?>
                   
                  </div>
                  
                   
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->