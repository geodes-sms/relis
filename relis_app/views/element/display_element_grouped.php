	<!-- page content -->
        <div class="right_col" role="main">
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
                 
                  $project_published=project_published();
                  
                  if(!empty($table_config['current_operation']) AND !empty($table_config['operations'][$table_config['current_operation']]['fields_groups'])){
                 // print_test($table_config);
                  $field_groups=$table_config['operations'][$table_config['current_operation']]['fields_groups'];
                  $fields=$table_config['operations'][$table_config['current_operation']]['fields'];
                  }
                 // print_test($fields);
                //  print_test($field_groups);
                  foreach($item_data as $k=>$v){
                  	if(!empty($fields[$v['field_id']])){
                  		$fields[$v['field_id']]['value']=$v;
                  	}
                  }
                //  print_test($fields);
                  foreach ($field_groups as $key_g => $value_G) {
                  	//echo "<tr>";
                  	//echo "<th style='width:20%'><u>".$value_G['title']."</u></th><td></td>";
                  //	echo "</tr>";
                  $title_but="";
                  if(!empty($value_G['edit']) AND !$project_published){
                  	$title_but=get_top_button('edit','Edit',$value_G['edit'],'Edit');
                  	$title_but="<ul class='nav navbar-right panel_toolbox'>$title_but</ul>";
                  }
                  box_header($value_G['title'],'',12,12,12,$title_but);
                  	echo "<table class='table table-striped'>";
                  	foreach($fields as $k=>$v){
                  		if($v['group'] == $key_g) {
                  		echo "<tr>";
                  		echo "<th style='width:20%'>".$v['value']['title']."</th>";
                  		if(empty($v['value']['val2']) OR count($v['value']['val2'])==0){
                  			echo "<td></td>";
                  		}elseif(count($v['value']['val2'])==1){
                  			echo "<td>: ".$v['value']['val2'][0]."</td>";
                  		}else{
                  			echo "<td>  <table class='table table-hover'>";
                  			$j=1;
                  			foreach ($v['value']['val2'] as $key => $value) {
                  				if($j==1)
                  					echo "<tr><td style='border-top:0px'>: - $value</tr></td>";
                  					else
                  						echo "<tr><td style='border-top:0px'> &nbsp - $value</tr></td>";
                  						$j++;
                  			}
                  	
                  			echo "</table></td>";
                  			echo "</tr>";
                  		}
                  		 
                  		} 
                  	}
                  	
                  	 echo "</table>";
                  	box_footer();
                  }
                  
                 
                  	
                  
                  
					?>
                   
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->