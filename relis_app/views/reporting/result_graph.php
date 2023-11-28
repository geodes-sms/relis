	<!-- page content -->
        <div class="right_col" role="main">
        
         
        <?php top_msg(); ?>
        <!-- top tiles -->
          <div class="row tile_count centre">
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-list"></i> <?php echo lng('All papers') ?></span>
            
               <?php echo anchor('paper/list_paper',' <div class="count ">'.nombre($all_papers).'</div>')?>
             
            </div>
            <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count green">
              <span class="count_top"><i class="fa fa-check-circle-o"></i> <?php echo lng('Processed papers') ?></span>
              
              <?php echo anchor('paper/list_paper/processed',' <div class="count green">'.nombre($processed_papers).'</div>')?>
             
             
              
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-clock-o"></i> <?php echo lng('Pending papers') ?></span>
              <?php echo anchor('paper/list_paper/pending',' <div class="count ">'.nombre($pending_papers).'</div>')?>
             
              
            </div>
            
            <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> <?php echo lng('Assigned to me') ?>	</span>
              <?php echo anchor('paper/list_paper/assigned_me',' <div class="count ">'.nombre($assigned_me_papers).'</div>')?>
             
              
            </div>
            
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top red"><i class=" red fa fa-times"></i> <?php echo lng('Excluded papers') ?></span>
              <?php echo anchor('paper/list_paper/excluded',' <div class="count red ">'.nombre($excluded_papers).'</div>')?>
             
              
            </div>
           
          </div>
          <!-- /top tiles -->
        
        
        
        
        
          <div class="">
            
            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" style="min-height:600px;">
                  <div class="x_title">
                 
                   
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content" style="min-height:400px ">
                  <div class="row">
                  
                  
				
                  
                  
                 
                  
                  <?php 
               
                  foreach ($result_table as $result_key => $result_value) {
                  	
                  
                  ?>
                  <div class="row">
                   <div class="col-md-12 col-sm-12 col-xs-12">
                   
                   <div class="x_panel">
							<div class="x_title">
							<h2>
							<?php echo lng($result_value['name']); ?>
							</h2>
							<ul class='nav navbar-right panel_toolbox'>
							 
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li></ul>
							<div class="clearfix"></div>
							</div>
							<div class="x_content" style="display: ;">
                  
					<div class="col-md-4 col-sm-4 col-xs-12">
						
							
							<?php 
							
							
							if(empty($result_value['rows'])){
								 
								echo lng("No value !");
							}else{
								 
								
							
									echo "<table class='table table-striped'>";
									
									foreach($result_value['rows'] as $k=>$v){
										echo "<tr>";
										$link=anchor("data_extraction/search_classification/".$result_value['field_name']."/".$v['field'],"<u>".$v['field_desc']."</u>");
										echo "<th style='width:70%'>".$link."</th><td>".$v['nombre']."</td>";
										echo "</tr>";
										 
									}
									echo "</table>";
							
								
							}
							?>
							
						
					</div>
				
				
					<div class="col-md-8 col-sm-8 col-xs-12">
						
							
							<?php 
							$this->session->set_userdata('graph_categorie_show',$result_key);
								
							$this->load->view('reporting/graph_page_result'); ?>
							
							
							
	                  
	                  </div>
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