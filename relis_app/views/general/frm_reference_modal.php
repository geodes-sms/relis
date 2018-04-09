	<?php $this->load->view('header_modal');?>
	<!-- page content -->
        <div class="right_colz" role="main">
          <div class="">
          
         
            
          
            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" >
                                  
                  
                  
                  <div class="x_content" style="min-height:400px ">
                  
                 
                  <?php
                  $attributes = array('class' => 'form-horizontal form_content');
                  //echo form_open('manage/save_ref',$attributes);
                  echo form_open_multipart('manager/save_element',$attributes);

                  $this->load->view('general/frm_reference_body');
                
                    ?>
                    
                   
                      
                      
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                        <a href="#" data-dismiss="modal"><button type="button" class="btn btn-primary">close</button></a>
               
                       <button class="btn btn-success" id="submit_but">Submit</button>
                          
                        </div>
                      </div>
                    
                    
                    
                    
                    <?php 
                    
                    
                    echo form_close();
                    
					
					?>
                   
                  </div>
                  
                  <!-- source http://www.krizna.com/demo/jquery-ajax-form-submit-using-twitter-bootstrap-modal/ -->
                  <script>
                  $(function() {
                	
                	 $("button#submit_but").click(function(event){
                    	
                		 event.preventDefault();
                		 
                	         $.ajax({
                	     type: "POST",
                	 url: "<?php echo base_url();?>manager/save_element",
                        	 
                	 data: $('form.form_content').serialize(),
                	         success: function(msg){
                	               
                	                var msg_index = msg.indexOf("_relis_outputmessage")
                	              //  alert (msg_index);
                	              //  alert (msg);
                	        	  if(msg_index == -1 || msg_index > 100 ){
                    	        	  //Data not stored
                	        		  $(".modal-body").html(msg);
                       	       }else{
                       	    	//Operation done
                       	    	location.reload(); 
                           	       
                           	       }
                	        
                	         },
                	 error: function (xhr, ajaxOptions, thrownError) {
                	        	 alert("loading failure");
                	             alert(xhr.status);
                	             alert(thrownError);
                	           }
                	       });
                	 });
                	});
                	</script>
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->
        <?php $this->load->view('footer_modal'); ?>