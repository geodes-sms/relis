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
				
				
				
				
				
				
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel">
							<div class="x_title">
							<h2><?php echo lng('Screening')." - Result : $screening_result"?>
							
							</h2>
							<ul class='nav navbar-right panel_toolbox'>
							 <?php 
		                    if(isset($assign_new_button)){//put 
		                    	echo "$assign_new_button ";
		                    
		                    }                    
		                    ?>
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li></ul>
							<div class="clearfix"></div>
							</div>
							<div class="x_content" style="display: ;">
							<?php 
							
							
							
							
							
							if(empty($screenings)){
								if(isset($screenings))
								echo lng("No screening data");
							}else{
								
								echo "<table class='table table-striped'>";
								echo "<tr>";
								echo "<th >".lng(' User')."</th>";
								echo "<th >".lng(' Phase')."</th>";
								echo "<th>".lng('Assignment type')."</th>";
								echo "<th >".lng('Decision')."</th>";
								echo "<th >".lng('Criteria')."</th>";
								echo "<th >".lng('Timestamp')."</th>";
								if(!empty($screen_edit_link)){
									echo "<th ></th>";
								}
								echo "<tr>";
								foreach($screenings as $k=>$v){
									 
									echo "<tr>";
									echo "<td>".$v['user_name']."</td>";
									echo "<td >".$v['assignment_role']."</td>";
									echo "<td >".$v['assignment_type']."</td>";
									echo "<td >".$v['screening_decision']."</td>";
									echo "<td >".$v['exclusion_criteria']."</td>";
									echo "<td >".$v['screening_time']."</td>";
									if(!empty($screen_edit_link)){
										echo "<td style=''>".$v['edit_link']."</td>";
									}
									echo "<tr>";
									
								}
								echo "</table>"."<br/><br/>";
								
							
							}
							?>
							
							</div>
						</div>
					</div>
				</div>
				
				
				
				
				
				<?php 
				if(isset($screen_history)){
					
					?>
					
					<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel">
							<div class="x_title">
							<h2><?php echo lng('Screening history' )?>
							
							</h2>
							<ul class='nav navbar-right panel_toolbox'>
							 
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li></ul>
							<div class="clearfix"></div>
							</div>
							<div class="x_content" style="display: ;">
							<?php 
							
							
							
							
							
							if(empty($screen_history)){
								 
								
										echo lng("No history available for this paper");
								
							}else{
								
								$tmpl = array (
										'table_open'  => '<table class="table table-striped projects">',
										'table_close'  => '</table>'
								);
								 
								$this->table->set_template($tmpl);
								 
								echo $this->table->generate($screen_history);
							
							}
							?>
							
							</div>
						</div>
					</div>
				</div>
					
					
					<?php 
				}
				?>
				
				
            
                
                
                  </div>
                  
                  
                 
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->