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
                        <li class="active"><a href="#home" data-toggle="tab">Automatic</a>
                        </li>
                        <li><a href="#profile" data-toggle="tab">Manually</a>
                        </li>
                        
                      </ul>
                    </div>

                    <div class="col-xs-9">
                      <!-- Tab panes -->
                      <div class="tab-content">
                        <div class="tab-pane active" id="home">
                          <p class="lead">Assign papers - Automatic</p>
                          <p><b> Number of papers :<u><?php echo $number_papers ?></u></b><br/></p>
                          <form class="form-horizontal"  method="post" action="../manage/list_paper_screen/all/_/0/2">
                         <?php 
                       
                        echo checkbox_form_bm('Admin','idcc','idcc');
                        echo checkbox_form_bm('Alice','idcc','idcc');
                        echo checkbox_form_bm('Bob','idcc','idcc');
                        echo checkbox_form_bm('Eve','idcc','idcc');
                         echo "<hr/>";
                        
                        echo input_form_bm('Reviews per paper','idcc','idcc','2');
                         
                        
                    ?>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          
                          <button  class="btn btn-success">Assign</button>
                        </div>
                      </div>
                    
                    
                    </form>
                          
                        </div>
                        
                        
                        <div class="tab-pane" id="profile">
                        	 
                        
                        
                        
                           <p class="lead">Assign papers - Manually</p>
                           <p>Assign papers to each user :<b> Number of papers :<u><?php echo $number_papers ?></u></b><br/></p>
                           
                           
                           <form class="form-horizontal" method="post" action="../manage/list_paper_screen/all/_/0/2">
                        <?php 
                        $csv_fields=array();
                        
                        echo input_form_bm('Admin ','idcc','idcc','1-229');
                        echo input_form_bm('Alice ','idcc','idcc','1-100 , 300-329');
                        echo input_form_bm('Bob ','idcc','idcc','230-457 ');
                        echo input_form_bm('Eve ','idcc','idcc','230-457 ');
                        
                    ?>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          
                          <button  class="btn btn-success">Assign</button>
                        </div>
                      </div>
                    
                    
                    </form>	 
                        	 
                        	 
                        	 
                    
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