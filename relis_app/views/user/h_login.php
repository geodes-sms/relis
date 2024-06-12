       <!-- page content -->
        <div class="right_col" role="main">
  <?php top_msg(); ?>        

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" style="height:600px;">
               
               
               <div id="wrapper">
        <div id="login" class=" form">
          <section class="login_content">
            <form action="<?php echo base_url()?>user/check_form" method="POST">
              <h1>Log in</h1><br/>
              
			  <?php
                    	if(validation_errors() OR !empty($err_msg) OR ($this->session->userdata('page_msg_err')) )
						{
							echo '<div class="alert alert-danger" style="text-align:center">';
							echo validation_errors();
							
							 if (!empty($err_msg))echo $err_msg;
							 
							 if (($this->session->userdata('page_msg_err'))){
							 	echo $this->session->userdata('page_msg_err');
								$this->session->set_userdata('page_msg_err','');
							 }
							echo "</div>";
						}
						if(!empty($success_msg)){
							echo '<div class="alert alert-success" style="text-align:center">';
							echo $success_msg;
							echo "</div>";
						}
                    	?>
                    	<br/>
              <div> 
                <input type="text" class="form-control" placeholder="<?php echo lng_min('Username')?>" name="user_username" value="" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="<?php echo lng_min('Password')?>" name="user_password" value="" required="" />
              </div>
              <div>
                <button type="submit" class="btn btn-default submit" ><?php echo lng_min('Log in')?></button>
                
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
  