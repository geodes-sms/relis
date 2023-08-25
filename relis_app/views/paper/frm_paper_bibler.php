	<!-- page content -->
        <div class="right_col" role="main">
         <?php top_msg();    ?> 
          <div class="">
          
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
                 
                 $attributes = array('class' => 'form-horizontal form_content');
                  
                 $fct_save=isset($save_function)?$save_function:'paper/bibler_save_paper';
                 
              
                 
                 echo form_open_multipart($fct_save,$attributes);
                 echo form_hidden(array( 'operation_type' => isset($operation_type)?$operation_type:'new'));
                 echo form_hidden(array( 'id' => isset($content_item['id'])?$content_item['id']:''));
                 
                
                 echo input_form_bm('Key','bibtexKey','bibtexKey',isset($content_item['bibtexKey'])?$content_item['bibtexKey']:'',30, ' mandatory' );
                 echo input_form_bm('Title','title','title',isset($content_item['title'])?$content_item['title']:'',200, ' mandatory' );
                 echo input_form_bm('Link','doi','doi',isset($content_item['doi'])?$content_item['doi']:'',200, ' ' );
                              
                echo input_textarea_bm("Bibtex",'bibtex','bibtex',isset($content_item['bibtex'])?$content_item['bibtex']:'',200);
                echo input_textarea_bm("Abstract",'abstract','abstract',isset($content_item['abstract'])?$content_item['abstract']:'',1000);
                echo input_textarea_bm("Preview",'preview','preview',isset($content_item['bibtex'])?$content_item['bibtex']:'',200);
                  
                 
                 echo "<hr/>";
                 
                            	
           //     $this->load->view('general/frm_reference_body');
                ?>
                      
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                          <button type="reset" class="btn btn-primary">Reset</button>
                          <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                      </div>
                    
                    
                    
                    
                    <?php 
                    
                    
                    echo form_close();
                    
					
					?>
                   
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->