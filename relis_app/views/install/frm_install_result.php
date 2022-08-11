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
					if(!empty($array_warning)){
					echo "<h1>Warning : </h1>";
					//print_test($array_error);
					foreach ($array_warning as $key => $value) {
						echo "<p class='red' >$value</p>";
					}
					
					} 
					if(!empty($array_error)){
					echo "<h1>Error : </h1>";
					//print_test($array_error);
					foreach ($array_error as $key => $value) {
						echo "<p class='red' >$value</p>";
					}
					
					}elseif(!empty($array_success)){
						
					echo "<h1>Success : </h1>";
					foreach ($array_success as $key => $value) {
						
						echo "<p class='green' >$value</p>";
					}
					//print_test($array_success);
					}
					
					if(!empty($next_operation_button)){
					echo "<br/><br/>";
					echo $next_operation_button;
					
					}
					?>
                   
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->