	<!-- page content -->
        <div class="right_col" role="main">
        <?php top_msg();    ?> 
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
                  
                  
                  
                  <div class="x_content" style="min-height:1000px ">
                  <p class="lead"><?php echo $number_of_papers.lng(' Papers found');
                  if(!empty($uploaded_papers_exist)){
                  	echo "<span class='red'> with ".count($uploaded_papers_exist) . " <u><a class='red' href='#exist'>already added</a><span></u>";
                  	
                  }
                  if(!empty($uploaded_papers_error)){
                  	if(empty($uploaded_papers_exist)){
                  		$with= "with";
                  	}else{
                  		$with= "and";
                  	}
                  	echo "<span class='red'> $with ".count($uploaded_papers_error) . " <u><a class='red' href='#error'>errors</a><span></u>";
                  	
                  }
                  ?> </p>
                           <form class="form-horizontal" method="post" action="../../paper/import_papers_save_bibtext" enctype="multipart/form-data">
                        <?php 
                         
                
                       echo '<div class="ln_solid"></div>';
                       echo form_hidden(array( 'data_array' => isset($json_values)?$json_values:''));
                       
                       if(!empty($source_papers)){
                       		
                       		echo dropdown_form_bm('Papers source','papers_sources','papers_sources',$source_papers);
                       }
                       
                        
                       if(!empty($search_strategy)){
                       	 
                       	echo dropdown_form_bm('Search strategy used','search_strategy','search_strategy',$search_strategy);
                       }
                        
                     ?>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          
                          <button class="btn btn-success">Save</button>
                        </div>
                      </div>
                    
                    
                    </form>
                    
                  <p class="lead" >Preview of the uploaded file content</p>
                         
                          
                          <?php  
                           
		                    $tmpl = array (
		                    		'table_open'  => '<table class="table table-striped table-hover ">',
		                    		'table_close'  => '</table>'
		                    );
		                  
		                    $this->table->set_template($tmpl);
		                   echo $this->table->generate($uploaded_papers);       
                          // print_test($csv_papers)?>
                 
                   <?php 
                   if(!empty($uploaded_papers_exist)){
                   	?>
                   	<p class="lead red" id="exist">Papers already added: </p>
                     <?php 

                     $tmpl = array (
                     		'table_open'  => '<table class="table table-striped table-hover red">',
                     		'table_close'  => '</table>'
                     );
                     $this->table->set_template($tmpl);
		                  echo $this->table->generate($uploaded_papers_exist); 
                          
                   	
                   }
                   ?>
                   <?php 
                   if(!empty($uploaded_papers_error)){
                   	?>
                   	<p class="lead red" id="error">Error found - please check the uploaded file: </p>
                     <?php 

                     $tmpl = array (
                     		'table_open'  => '<table class="table table-striped table-hover red">',
                     		'table_close'  => '</table>'
                     );
                     $this->table->set_template($tmpl);
		                  echo $this->table->generate($uploaded_papers_error); 
                          
                   	
                   }
                   ?>
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->