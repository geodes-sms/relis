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
                  
                 
                   <div class="col-xs-3">
                      <!-- required for floating -->
                      <!-- Nav tabs -->
                      <ul class="nav nav-tabs tabs-left">
                       <li class="active"><a href="#profile" data-toggle="tab">From CSV</a>
                        </li>
                        <li ><a href="#home" data-toggle="tab">From Bibler</a>
                        </li>
                       
                        
                      </ul>
                    </div>

                    <div class="col-xs-9">
                      <!-- Tab panes -->
                      <div class="tab-content">
                        <div class="tab-pane " id="home">
                          <p class="lead">Import from Bibler</p>
                          
                          <form class="form-horizontal" method="post" action="../install/zimport_papers">
                        <?php 
                         
                    echo input_image_bm(lng('papers SQL file'),'install_config','install_config','',1, 'mandatory');
                    echo input_image_bm(lng('authors SQL  file'),'install_config','install_config','',1, 'mandatory');
                    echo input_image_bm(lng('papers_authors SQL  file'),'install_config','install_config','',1, 'mandatory');
                     ?>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          
                          <button  class="btn btn-success">Import</button>
                        </div>
                      </div>
                    
                    
                    </form>
                          
                        </div>
                        
                        
                        <div class="tab-pane active" id="profile">
                        	 
                        	 
                        	 
                        	 
                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                      <ul id="myTab1" class="nav nav-tabs bar_tabs right" role="tablist">
                        <li role="presentation1" class=""><a href="#tab_content11" id="home-tabb" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Match fields</a>
                        </li>
                        <li role="presentation1" class="active"><a href="#tab_content22" role="tab" id="profile-tabb" data-toggle="tab" aria-controls="profile" aria-expanded="false">Import csv file</a>
                        </li>
                        
                      </ul>
                      <div id="myTabContent2" class="tab-content">
                        
                        <div role="tabpanel" class="tab-pane fade active in" id="tab_content22" aria-labelledby="profile-tab">
                          
                          
                          <p class="lead">Import papers from csv files </p>
                           <form class="form-horizontal">
                        <?php 
                         
                    echo input_image_bm(lng('Upload the CSV file'),'install_config','install_config','',1, 'mandatory');
                     ?>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          
                          <a  href="#tab_content11" id="home-tabb" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true"><button class="btn btn-success">Upload</button></a>
                        </div>
                      </div>
                    
                    
                    </form>
                          
                          
                          
                        </div>
                        <div role="tabpanel" class="tab-pane fade " id="tab_content11" aria-labelledby="home-tab">
                          <p class="lead">Preview of the uploaded csv file content</p>
                         
                          
                          <?php  
                           
		                    $tmpl = array (
		                    		'table_open'  => '<table class="table table-striped table-hover">',
		                    		'table_close'  => '</table>'
		                    );
		                  
		                    $this->table->set_template($tmpl);
		                   echo $this->table->generate($csv_papers);       
                          // print_test($csv_papers)?>
                          
                     <hr/>     
                           <p class="lead">Match csv fields</p>
                           
                           
                           
                           <form class="form-horizontal" method="post" action="../install/zimport_papers">
                        <?php 
                        echo dropdown_form_bm('Key','idcc','idcc',$csv_fields);
                        echo dropdown_form_bm('Paper title','idcc','idcc',$csv_fields);
                        echo dropdown_form_bm('DOI','idcc','idcc',$csv_fields);
                        echo dropdown_form_bm('Preview','idcc','idcc',$csv_fields);
                        echo dropdown_form_bm('Abstract','idcc','idcc',$csv_fields);
                        echo dropdown_form_bm('Authors','idcc','idcc',$csv_fields);
                        echo input_form_bm('Authors separator','idcc','idcc',';');
                        echo input_form_bm('Start inport from row ','idcc','idcc','2');
                         
                        
                    ?>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          
                          <button  class="btn btn-success">Save papers</button>
                        </div>
                      </div>
                    
                    
                    </form>
                           
                           
                           
                          
                        </div>
                        
                      </div>
                    </div>	 
                        	 
                        	 
                        	 
                        	 
                        	 
                        	 
                    
                      </div>
                      
                       
                      </div>
                    </div>

                    <div class="clearfix"></div>
                   
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->