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
                  
                 <p class="lead"><?php echo lng('Load a CSV file')?> </p>
                           <form class="form-horizontal" method="post" action="../../paper/import_papers_load_csv" enctype="multipart/form-data">
                        <?php 
                         
                    echo input_image_bm(lng('Upload the CSV file'),'paper_file','paper_file','',1, 'mandatory');
                     ?>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          
                          <button class="btn btn-success"><?php echo lng('Upload')  ?></button>
                        </div>
                      </div>
                    
                    
                    </form>
                   
                   
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->