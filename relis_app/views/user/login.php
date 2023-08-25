<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ReLiS | Login </title>

    <!-- Bootstrap -->
    <link href="<?php echo site_url();?>cside/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo site_url();?>cside/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo site_url();?>cside/css/custom.css" rel="stylesheet">
    <link href="<?php echo site_url();?>cside/css/my_styles.css" rel="stylesheet">
  </head>

  <body style="background:#F7F7F7;">
  <div style="width:90% ; margin:auto; padding-top: 20px;">
	<div class="row">
	<div class=" col-md-12 col-sm-12 col-xs-12 login_content">
  <h1>ReLiS</h1>
  </div>
  </div>
  <div class="row">
	<div class="login_description col-md-7 col-sm-7 col-xs-12">
  <p >
  <b>ReLiS</b> stands for <i>"Revue Littéraire Systématique"</i> which is French for <i>"Systematic Literature Review"</i>.
   When a researcher wants to address a research problem, he starts by looking at what already exists 
   in the scientific literature (published papers) on the topic. 
   ReLiS is a tool that helps him considerably reduce the effort to analyze the corpus of papers, 
   typically varying between hunderds and thousands depending on the research topic.
    ReLiS allows the user to follow a systematic process and automate the review process as much as possible .
  
  </p>
  <div class="row">
  <br/>
  <h4>Process overview</h4>
      <iframe width="90%" height="315" src="https://www.youtube.com/embed/U5zOmk2vWy8" frameborder="0" allowfullscreen></iframe>
      </div>
  </div>
      
  <div id="wrapper" class="col-md-5 col-sm-5 col-xs-12">
  
        <div id="login" class=" form my_login">
        
          <section class="login_content">
          
            <form action="<?php echo base_url()?>user/check_form" method="POST">
             
              <h2><?php echo lng_min('Log in')?></h2>
			  <?php
                    	if(validation_errors() OR !empty($err_msg) OR ($this->session->userdata('page_msg_err')) )
						{
							echo '<div class="alert alert-danger" style="text-align:center">';
							echo validation_errors();
							
							 if (isset($err_msg))echo $err_msg;
							 
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
                <a href="<?php echo base_url()?>user/new_user"><u>Create account</u></a>
                <a href="<?php echo base_url()?>user/demo_user"><u>Demo user</u></a>
              </div>
              <div class="clearfix"></div>
              
            </form>
          </section>
        </div>
         
      </div>
      <div class="clearfix"></div>
        <div class="separator"> </div>
      </div>
      
      <div class="row">
		
		<div class="col-md-4 col-sm-4 col-xs-12">
			<a href="http://geodes.iro.umontreal.ca" target="_BLANK" title="GEODES Software Engineering Research Group">
			<img src="<?php echo site_url();?>cside/images/geodes.png" />
			</a>
		</div>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<a href="http://diro.umontreal.ca" target="_BLANK" title="Department of Computer Science and Operations Research">
			<img src="<?php echo site_url();?>cside/images/diro.jpg" />
			</a>
		</div>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<a href="http://umontreal.ca" target="_BLANK" title="Université de Montréal">
			<img src="<?php echo site_url();?>cside/images/logoudem.gif" />
			</a>
		</div>
      </div>
      </div>

  </body>
</html>