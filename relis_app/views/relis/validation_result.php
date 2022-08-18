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
                  
                  
                  
                  
                  
                  
                  
                  <div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel">
							<div class="x_title">
							<h2>
							<?php 
							echo $result_page_title;
							
							?>
							</h2>
							<ul class='nav navbar-right panel_toolbox'>
							<li><a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </li></ul>
							<div class="clearfix"></div>
							</div>
							<div class="x_content" id="div_display" style="display: ;">
							<?php 
							
							if(isset($nombre) AND $nombre>0)
							{
								//echo $this->table->generate($list);
							           
								?>
							                  <div class="table-responsive">	
							                 <table id="datatable-responsive" class="table table-striped table-bordered  nowrap"  style="width:100%; border-spacing:0px;
							    border-collapse: separate;">
							                      <thead>
							                        <tr>
							                        <?php 
							                        foreach ($list_header as $key => $value) {
							                        	echo "<th>".$value."</th>";
							                        }
							                      
							                        ?>
							                        </tr>   	
							     	
							                    </thead>
							                    <tbody>
							                    	<?php 
							                    	
							                    	foreach ($list as $key_row => $row) {
							                    		echo"<tr>";
							                    		foreach ($row as $k_cel => $v_cell) {
							                    			echo "<td>".$v_cell."</td>";
							                    		}
							                    		
							                    		echo"</tr>";
							                    	}
							                    	?>
							                   </tbody> 
							                    </table>	
							                    </div>
							                    	
							                    	
							                    	<?php 
							                    }else{
							                    	echo "<p>".lng('No records found')." !</p>";
							                    							}
							?>
							
							</div>
						</div>
					</div>
				</div>
                  
                <?php 
                if(!empty($validation_score)){
	                echo"<div class='row'>";
		                echo box_header("Validation score per reviewer",'',12,12,12);
		                 
		                $tmpl = array (
		                		'table_open'  => '<table class="table table-striped table-hover">',
		                		'table_close'  => '</table>'
		                );
		                 
		                $this->table->set_template($tmpl);
		                
		                echo $this->table->generate($validation_score);
		                
		                 
		                
		                echo  box_footer();	                
	                echo"</div>";
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