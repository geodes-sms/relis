
        <!-- page content -->
        <div class="right_col" role="main">
          
          <?php top_msg(); ?> 

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" style="min-height:800px;">
                  
                  
               <div id="wrapper">
        <div id="login" class=" form">
          <section class="login_content">
            <form action="<?php echo base_url()?>auth/check_create_user" method="POST">
              <h2>ReLiS create account</h2><br/>
              
			  <?php
                    	if(validation_errors() OR isset($err_msg) OR ($this->session->userdata('page_msg_err')) )
						{
							echo '<div class="alert alert-danger" style="text-align:center">';
							echo validation_errors();
							
							 if (isset($err_msg))
							 		echo $err_msg;
							 
							 if (($this->session->userdata('page_msg_err'))){
							 	echo $this->session->userdata('page_msg_err');
								$this->session->set_userdata('page_msg_err','');
							 }
							echo "</div>";
						}
                    	?>
                    	<br/>
              <div>
              <?php echo lng_min('Name')?>
                <input type="text" class="form-control" placeholder="" name="user_name" maxlength="49" 
                	value="<?php echo  !empty($content_item['user_name'])?$content_item['user_name']:""?>" required="" />
              </div>
              <div>
              <?php echo lng_min('Email')?>
                <input type="text" class="form-control" placeholder="" name="user_mail" maxlength="99"
                value="<?php echo  !empty($content_item['user_mail'])?$content_item['user_mail']:""?>" required="" />
              </div>
              <div>
              <?php echo lng_min('Username')?>
                <input type="text" class="form-control" placeholder="" name="user_username" maxlength="19"
                 value="<?php echo  !empty($content_item['user_username'])?$content_item['user_username']:""?>" required="" />
              </div>
              
              
              <div>
              	 <?php echo lng_min('Password')?>
                <input type="password" class="form-control" placeholder="" name="user_password" 
                value="" required="" />
              </div>
               <div>
              	<?php echo lng_min('Validate password')?>
                <input type="password" class="form-control" placeholder="" name="user_password_validate" 
                value="" required="" />
              </div>
            
              <div>
                <button type="submit" class="btn btn-primary submit" ><?php echo lng_min('Create')?></button>
     
               	<button type="reset" class="btn btn-default " onclick="location.href='<?php echo base_url()?>home';" ><?php echo lng_min('Back')?></button>
               
                
              </div>
              
             
              <div class="clearfix"></div>
              <div class="separator">

                
                <div class="clearfix"></div>
                <br />
                <div>
                  
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>   
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
          
           <!-- /page content -->
       