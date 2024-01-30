	<!-- page content -->
        <div class="right_col" role="main">
        
        <?php top_msg(); 
        
        $configuration = get_appconfig();
        
        //print_test($configuration);
      //  print_test($users);
        
        ?> 
        
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="dashboard_graph">

                <div class="row x_title">
                  <div class="col-md-6">
                    <h3><?php echo lng('Choose project')?></h3>
                  </div>
                  
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 bg-white">
                 <br/><br/>
				<?php 
				if(!empty($projects['list'])){
					echo '<table class="table  table-hover">';
					foreach ($projects['list'] as $key => $value) {
						echo "<tr><td><div class='alert alert-success alert-dismissible fade in'><h2>".anchor('project/set_project2/'.$value['project_label'].'/'.$value['project_id'].'/'.$value['project_title'],$value['project_title'],"title='".$value['project_description']."'")."</h2></div></td></tr>";
					}
					echo '</table>';
				}
				?>
                  
                </div>
                

                <div class="clearfix"></div>
              </div>
            </div>

          </div>
          
         <br/>
        
        </div>
       

        <!-- /page content -->