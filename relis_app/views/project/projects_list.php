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
                  <h2><?php echo isset($page_title) ? $page_title :"" ; ?></h2>
                    <?php 
                    old_version();
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    
                    }                    
                    ?>
                  
                </div>
                
				<div class="col-md-12 col-sm-12 col-xs-12 bg-white">
                 <?php 
                // print_test($projects['list'] );
                 
                 if(empty($projects['list'] )){
                 	// add new project button
                 	echo"<div style='text-align:center; padding:20px;'>";
                 	echo"<p>";
                 	echo lng('No project available!');
                 	echo"</p><br/><br/>";
                 	echo $add_project_button;
                 	echo"</div>";
                 }
               //  print_test($projects);
                 foreach ($projects['list'] as $key => $value) {
                 //	print_test($value);
                 ?>
                 <a href="<?php echo base_url().'project/set_project2/'.$value['project_label'].'/'.$value['project_id'].'/'.$value['project_title'] ?>">
                 <div class="col-md-3 col-sm-12 col-xs-12 col-md-offset-1">
                        <div class="thumbnail">
                          <div class="image view view-first">
                            <img style="height: 100%; display: block;" src="<?php echo base_url().$value['icon']?>" alt="<?php echo $value['project_description']?>" />
                            <div class="mask" >
                              <p><?php echo $value['project_description']?></p>
                              <div class="tools tools-bottom">
                              <?php 
                              echo anchor('project/set_project2/'.$value['project_label'].'/'.$value['project_id'].'/'.$value['project_title'],' &nbsp &nbsp <i class="fa fa-paper-plane"></i> ','title="'.lng_min('Go to the project').'"');
                              if(is_project_creator($value['project_label']) or has_usergroup(1)){
                              echo anchor('manage/view_ref/project/'.$value['project_id'],'  &nbsp &nbsp <i class="fa fa-folder"></i>','title="'.lng_min('View').'"');
                              echo anchor('manage/edit_ref/project/'.$value['project_id'],' &nbsp &nbsp <i class="fa fa-pencil"></i>','title="'.lng_min('Edit').'"');
                              echo anchor('project/remove_project_validation/'.$value['project_id'],' &nbsp &nbsp <i class="fa fa-trash-o"></i>','title="'.lng_min('Uninstall').'"');
                              }
                              ?>
                              </div>
                            </div>
                          </div>
                          <div class="caption">
                            <p><?php echo $value['project_title']?></p>
                          </div>
                        </div>
                      </div>
                      </a>
                 <?php }?>
                 
                  
                </div>
                
                

                <div class="clearfix"></div>
              </div>
            </div>

          </div>
          
         <br/>
        
        </div>
       

        <!-- /page content -->