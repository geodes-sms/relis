	<!-- page content -->
        <div class="right_col" role="main">
        
         
        <?php top_msg(); ?>
        <!-- top tiles -->
         
        
        
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
               
                  foreach ($graph_result as $result_key => $result_value) {
              // 	print_test($result_value);
                 
                  ?>
                  <div class="row">
                   <div class="col-md-12 col-sm-12 col-xs-12">
                   
                   <div class="x_panel">
							<div class="x_title">
							<h2>
							<?php echo lng($result_value['title']); ?>
							</h2>
							<ul class='nav navbar-right panel_toolbox'>
							 
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li></ul>
							<div class="clearfix"></div>
							</div>
							<div class="x_content" style="display: ;">
                <?php
                $class_md='col-md-12 col-sm-12 col-xs-12';
                if($result_value['type']=='simple' AND empty($result_value['display_table']))
                {
                	$class_md='col-md-8 col-sm-8 col-xs-12';
                ?>  
					<div class="col-md-4 col-sm-4 col-xs-12">
						
							
							<?php 
							
							
							if(empty($result_value['data'])){
								 
								echo lng("No value !");
							}else{
								 
								
							
									echo "<table class='table table-striped'>";
									
									foreach($result_value['data'] as $k=>$v){
										echo "<tr>";
										if(!empty($result_value['link'])){
										$link=anchor("data_extraction/search_classification/".$result_value['field']."/".$v['field'],"<u>".$v['title']."</u>");
										}else{
											$link= $v['title'];
										}
										echo "<th style='width:70%'>".$link."</th><td>".$v['nombre']."</td>";
										echo "</tr>";
										 
									}
									echo "</table>";
							
								
							}
							?>
							
						
					</div>
				<?php 
                }
				?>
				
					<div class="<?php echo $class_md ?>">
						
							
							<?php 
							if($result_value['type']=='simple')
							{
								foreach ($result_value['chart'] as $key_chart => $value_chart) {
									//print_test($result_value);
									if($value_chart=='pie')
										pie_graph($result_value);
									if($value_chart=='bar')
										column_graph($result_value);
									
									if($value_chart=='line')
											line_graph($result_value);
								}
								
								
							}else{
								foreach ($result_value['chart'] as $key_chart => $value_chart) {
									//print_test($result_value);
									if($value_chart=='bar')
										multi_collumn($result_value);
										
									if($value_chart=='line')
										line_graph_multi($result_value);
											
									if($value_chart=='pie')
										pie_drilldown($result_value);
								}
								
								
							}
							
							//$this->session->set_userdata('graph_categorie_show',$result_key);
								
							//$this->load->view('reporting/graph_page_result');
							?>
						
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