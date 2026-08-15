<script src="https://d3js.org/d3.v6.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dagre-d3/0.6.4/dagre-d3.min.js"></script>
<script src="<?php echo site_url();?>cside/js/phases.js"></script>
<link rel="stylesheet" href="<?php echo site_url();?>cside/css/phases_tree.css">
<!-- page content -->
        <div class="right_col" role="main">
        
        <?php top_msg();  ?> 
        
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="dashboard_graph">

                <div class="row x_title">
                 <?php  header_perspective('screen');?>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 bg-white">
                  <div class="x_title">
                    <h3> <?php echo lng('Project').' : '.$configuration['project_title'] ?> </h3>
                     <?php 
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    
                    }                    
                    ?>
                    <div class="clearfix"></div>
                  </div>

                  <div class="col-md-8 col-sm-8 col-xs-6">
                    
                      <p style="text-align: justify"><b><?php echo lng('Description')?>:</b><br/>
                      <?php echo $configuration['project_description'] ?>
                      </p>
                      
                   
                  </div>
                   <?php 
                            $creator=array();
                            foreach ($users as $k => $v) {
                            	if($v['user_id']==$configuration['project_creator']){
                            	$creator=$v;
                            	}
                            }
                  if(!empty($creator)) {
                            $images_creator=site_url()."cside/images/img.jpg";
                             
                            if(!empty($creator['user_picture'])){
                            	$user_picture=$creator['user_picture'];
                            
                            	//$images_creator=site_url().$this->config->item('image_upload_path').$user_picture."_thumb.jpg";
                            	$images_creator=display_picture_from_db($creator['user_picture']);
                            }
                            
                            ?>
                
                <div class="col-md-4 col-sm-4 col-xs-12">
	                <div class="col-md-12 col-sm-12 col-xs-12 profile_details">
	                        <div class=" col-sm-12 well profile_view">
	                          <div class="col-sm-12">
	                            <h4 class="brief"><i><?php echo lng('Designer')?></i></h4>
	                           
	                            <div class="left col-xs-7">
	                              <h2><?php echo $creator['user_name']?></h2>
	                              
	                              <ul class="list-unstyled">
	                                <li><i class="fa fa-comments-o"></i> Email: <?php echo $creator['user_mail']?></li>
	                               
	                              </ul>
	                            </div> 
	                            <div class="right col-xs-5 text-center">
	                              <img  src="<?php echo $images_creator?>" alt="" class="img-circle img-responsive" width="100" height="100" >
	                            </div>
	                          </div>
	                          <div class="col-xs-12 bottom text-center">
	                            <div class="col-xs-12 col-sm-6 emphasis">
	                              
	                            </div>
	                            <div class="col-xs-12 col-sm-6 emphasis">
	                              
	                              <a href="../element/display_element/detail_user_min/<?php echo $creator['user_id']?>"><button type="button" class="btn btn-primary btn-xs">
	                                <i class="fa fa-user"> </i> View Profile
	                              </button></a>
	                            </div>
	                          </div>
	                       </div>
	               </div>
               </div>
          <?php }?>
                  
                </div>
                

                <div class="clearfix"></div>
              </div>
            </div>

          </div>
          
          
          <br />
         <div class="row">
         
         
         	<div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel tile  overflow_hidden">
                <div class="x_title">
                  <h2><?php echo lng('Project phases')?></h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                
                
                
                <!-- screening phases-->

                    <?php if (isset($phases_tree)): ?>
                        <?php echo checkbox_form_bm("Show inactives phases", "show_inactives_phases", "show_inactives_phases");?>
                        <div id="graph-container" style="width: 100%; overflow: auto; position: relative;">
                            <svg width="100%"><g></g></svg>
                        </div>
                        <script>
                            const baseUrl = "<?= base_url(); ?>";
                            const phasesTree = <?= json_encode($phases_tree); ?>;
                            const phasesList = <?= json_encode($phases_list); ?>;
                            const qa = phasesList.find(phase => phase.Title === 'Quality assessment');
                            const classification = phasesList.find(phase => phase.Title === 'Classification');
                            var graph = createTree(phasesTree, classification, qa, '', document.querySelector("#show_inactives_phases").checked);

                            var render = new dagreD3.render();
                            var svg = d3.select("svg");
                            var svgGroup = svg.select("g");

                            render(svgGroup, graph);

                            var graphHeight = graph.graph().height;
                            svg.attr("height", graphHeight + 40);

                            var xCenterOffset = (svg.node().getBoundingClientRect().width - graph.graph().width) / 2;
                            svgGroup.attr("transform", "translate(" + xCenterOffset + ", 20)");

                            document.querySelector("#show_inactives_phases").addEventListener('change', () => {
                                graph = createTree(phasesTree, classification, qa, '', document.querySelector("#show_inactives_phases").checked);
                                render(svgGroup, graph)
                                graphHeight = graph.graph().height;
                                svg.attr("height", graphHeight + 40);
                                xCenterOffset = (svg.node().getBoundingClientRect().width - graph.graph().width) / 2;
                                svgGroup.attr("transform", "translate(" + xCenterOffset + ", 20)");
                            })
                        </script>

                    <?php elseif (!empty($phases_list)): ?>
                        <?php
                        $tmpl = array (
                                'table_open'  => '<table class="table table-striped">',
                                'table_close' => '</table>'
                        );
                        $this->table->set_template($tmpl);
                        echo $this->table->generate($phases_list);
                        ?>
                    <?php endif; ?>
         		 <!-- /top tiles -->
                
                
                
                
                 
                </div>
              </div>
            </div>
            
            
            
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel tile  overflow_hidden">
                <div class="x_title">
                  <h2><?php echo lng('Participants')?></h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                
                
                	<?php 
                  foreach ($users as $key => $value) {
                  	
                  	$images=site_url()."cside/images/img.jpg";
                  	
                  	if(!empty($value['user_picture'])){
                  		$user_picture=$value['user_picture'];
                  		
                  		//$images=site_url().$this->config->item('image_upload_path').$user_picture."_thumb.jpg";
                  		
                  		$images=display_picture_from_db($user_picture);
                  	}
                  ?>
                  
                  
                  
                   <div class="col-md-6 col-sm-6 col-xs-12 profile_details">
                        <div class=" col-sm-12 well profile_view">
                          <div class="col-sm-12">
                            <h4 class="briefs"><i><?php echo $value['usergroup_name']?></i></h4>
                            <div class="left col-xs-7">
                              <h2><?php echo $value['user_name']?></h2>
                              
                              <ul class="list-unstyled">
                                <li><i class="fa fa-comments-o"></i> Email: <?php echo $value['user_mail']?></li>
                               
                              </ul>
                            </div> 
                            <div class="right col-xs-5 text-center">
                              <img  src="<?php echo $images?>" alt="" class="img-circle img-responsive" width="100" height="100" >
                            </div>
                          </div>
                          <div class="col-xs-12 bottom text-center">
                            <div class="col-xs-12 col-sm-6 emphasis">
                              
                            </div>
                            <?php if (has_usergroup(1)){?>
                            <div class="col-xs-12 col-sm-6 emphasis">
                              
                              <a href="../element/display_element/detail_user_min/<?php echo $value['user_id']?>"><button type="button" class="btn btn-primary btn-xs">
                                <i class="fa fa-user"> </i> View Profile
                              </button></a>
                            </div>
                            <?php }?>
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
        </script>
      
        <!-- /page content -->