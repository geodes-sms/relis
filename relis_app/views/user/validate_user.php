<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ReLis | validation </title>

    <!-- Bootstrap -->
    <link href="<?php echo site_url();?>cside/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo site_url();?>cside/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo site_url();?>cside/css/custom.css" rel="stylesheet">
  </head>

  <body style="background:#F7F7F7;">
    <div class="">
      <a class="hiddenanchor" id="toregister"></a>
      <a class="hiddenanchor" id="tologin"></a>

      <div id="wrapper">
        <div id="login" class=" form">
          <section class="login_content">
            <form action="<?php echo base_url()?>user/check_validation" method="POST">
              <h2>Account validation</h2><br/>
              
			  <?php
                    	if(validation_errors() OR !empty($err_msg) OR ($this->session->userdata('page_msg_err')) )
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
						if(!empty($success_msg)){
							echo '<div class="alert alert-success" style="text-align:center">';
							echo $success_msg;
							echo "</div>";
						}
						echo form_hidden(array( 'user_id' => isset($user_id)?$user_id:'0'));
						 
                    	?>
                    	<br/>
              <div>
              <?php echo lng_min('Validation code')?>
                <input type="text" class="form-control" placeholder="" name="validation_code" 
                	value="" required="" />
              </div>
              
              <div>
                <button type="submit" class="btn btn-primary submit" ><?php echo lng_min('Send')?></button>
     
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
  </body>
</html>