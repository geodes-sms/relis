<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ReLiS | Login</title>

    <!-- Bootstrap -->
    <link href="<?php echo site_url();?>cside/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo site_url();?>cside/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo site_url();?>cside/css/custom.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="<?php echo site_url();?>user.html" class="site_title">
              	<i class="fa fa-book"></i> <span>ReLiS</span>
              </a>
            </div>

            <div class="clearfix"></div>

           <br/>
            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                
                <ul class="nav side-menu">
                  <li><a href="<?php echo base_url()?>user"><i class="fa fa-home"></i> About </span></a></li>
                  <li><a><i class="fa fa-question-circle"></i> Help </span></a></li>
                  <li><a href="<?php echo base_url()?>user/login"><i class="fa fa-sign-in"></i> Log In </span></a></li>
                  <li><a href="<?php echo base_url()?>user/new_user"><i class="fa fa-plus"></i><i class="fa fa-user"></i>Create Account </span></a></li>
                  
                </ul>
              </div>
              
            </div> 
            <!-- /sidebar menu -->

          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">

          <div class="nav_menu">
            <nav class="" role="navigation">
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
              <li class="">
                  <a href="<?php echo base_url()?>user/login" class=" "> Log in </a>                 
              </li>
              <li class="">
                  <a href="<?php echo base_url()?>user/new_user"> Create account </a>                 
              </li>
             
              </ul>
            </nav>
          </div>

        </div>
        <!-- /top navigation -->

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
                    	if(validation_errors() OR isset($err_msg) OR ($this->session->userdata('page_msg_err')) )
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
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
          	<?php echo lng('ReLiS - Revue Littéraire Systématique');
        	 // echo "Copirignt";
         	 ?>
           
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="<?php echo site_url();?>cside/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo site_url();?>cside/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo site_url();?>cside/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="<?php echo site_url();?>cside/vendors/nprogress/nprogress.js"></script>
    
    <!-- Custom Theme Scripts -->
    <script src="<?php echo site_url();?>cside/js/custom.js"></script>
  </body>
</html>