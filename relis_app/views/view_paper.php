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
                  
                  if($paper_excluded)
                  {
                  ?>
                  
                  
                  <div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel">
							<div class="x_title">
							<h2><?php echo lng('Exclusion Info')?>
							
							</h2>
							<ul class='nav navbar-right panel_toolbox'>
							 <?php 
		                    if(isset($remove_exclusion_button)){//put 
		                    	echo "$remove_exclusion_button ";
		                    
		                    }                    
		                    ?>
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li></ul>
							<div class="clearfix"></div>
							</div>
							<div class="x_content" style="display: ;">
							<?php 
							
							
							
							
							
							if(empty($data_exclusion)){
								 
								echo lng("No excluded items");
							}else{
								 
								
							
									echo "<table class='table table-striped'>";
									
									foreach($data_exclusion as $k=>$v){
										echo "<tr>";
										echo "<th style='width:20%'>".lng($v['title'])."</th><td>".$v['val']."</td>";
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
				
                  }else{
				?>
				
				
				
				
				
				
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel">
							<div class="x_title">
							<h2><?php echo lng('Classification')?>
							
							</h2>
							<ul class='nav navbar-right panel_toolbox'>
							
							 <?php 
		                    if(isset($classification_button)){
		                    	echo "$classification_button ";
		                    
		                    }                    
		                    ?>
		                    
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li></ul>
							<div class="clearfix"></div>
							</div>
							<div class="x_content" style="display: ;">
							<?php 
							
							
							
							
							
							if(empty($classification_data)){
								 
								echo lng("No classification");
							}else{
								
								echo "<table class='table table-striped'>";
								foreach($classification_data as $k=>$v){
									 
									echo "<tr>";
									echo "<th style='width:20%'>".lng($v['title'])."</th>";
									if(empty($v['val2']) OR count($v['val2'])==0){
										echo "<td></td>";
									}elseif(count($v['val2'])==1){
										echo "<td>".$v['val2'][0]."</td>";
									}else{
										echo "<td> <table class='table table-hover'>";
										foreach ($v['val2'] as $key => $value) {
											echo "<tr><td style='border-top:0px; border-bottom:1px solid #ddd'> - $value</tr></td>";
										}
										 
										echo "</table></td>";
										echo "<tr>";
									}
								}
								echo "</table>"."<br/><br/>";
								
								/* 
								foreach ($data_classifications as $key => $classification) {
									//print_test($classification);
									
									
							
									echo "<table class='table table-striped'>";
									if(isset($remove_classification_button[$key])){
										echo "<tr><th colspan='2'><ul class='nav navbar-right panel_toolbox'>".$remove_classification_button[$key]."</th><tr></ul>";
											
									}
									foreach($classification as $k=>$v){
										echo "<tr>";
										echo "<th style='width:20%'>".$v['title']."</th><td>".$v['val']."</td>";
										echo "<tr>";
										 
									}
									echo "</table>"."<br/><br/>";
							
								}
								
								*/
							}
							?>
							
							</div>
						</div>
					</div>
				</div>
				
				
				
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel">
							<div class="x_title">
							<h2>
							<?php 
							echo lng('Assignations');
							if(!empty($data_assignations)){
								 
								echo "(".count($data_assignations).")";
							}else{
								echo "(0)";
							}
							?>
							</h2>
							<ul class='nav navbar-right panel_toolbox'>
							 <?php 
		                    if(isset($add_assignation_buttons)){
		                    	echo "$add_assignation_buttons ";
		                    
		                    }                    
		                    ?>
							<li><a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                      </li></ul>
							<div class="clearfix"></div>
							</div>
							<div class="x_content" style="display: none;">
							<?php 
							
							
							
							
							
							if(empty($data_assignations)){
								 
								echo lng("No assignation");
							}else{
								 
								foreach ($data_assignations as $key => $assignation) {
									//print_test($classification);
									
									
							
									echo "<table class='table table-striped'>";
									if(isset($remove_assignation_button[$key])){
										echo "<tr><th colspan='2'><ul class='nav navbar-right panel_toolbox'>".$remove_assignation_button[$key]."</th><tr></ul>";
											
									}
									foreach($assignation as $k=>$v){
										echo "<tr>";
										echo "<th style='width:20%'>".lng($v['title'])."</th><td>".$v['val']."</td>";
										echo "<tr>";
										 
									}
									echo "</table>"."<br/><br/>";
							
								}
							}
							?>
							
							</div>
						</div>
					</div>
				</div>
                
                
                
                <?php }?>
                
                
                  </div>
                  
                  
                 
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->