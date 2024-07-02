<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ReLiS</title>

    <!-- Bootstrap -->
    <link href="<?php echo site_url();?>cside/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo site_url();?>cside/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo site_url();?>cside/css/custom.css" rel="stylesheet">
    <link href="<?php echo site_url();?>cside/css/my_styles.css" rel="stylesheet">
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
                  <li><a href="<?php echo base_url()?>user/user_help"><i class="fa fa-question-circle"></i> Getting Started </span></a></li>
                  <li><a href="<?php echo base_url()?>user/login"><i class="fa fa-sign-in"></i> Go to ReLiS </span></a></li>
                  <li><a href="<?php echo base_url()?>user/new_user"><i class="fa fa-plus"></i><i class="fa fa-user"></i>Create Account </span></a></li>
                  <li><a href="<?php echo base_url()?>user/demo_user"></i><i class="fa fa-user"></i>Demo user </span></a></li>
                  
                </ul>
              </div>
              
            </div>
            <!-- /sidebar menu -->

          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">

          <div class="nav_menu ">
            <nav class="" role="navigation">
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
<!-- 
              <ul class="nav navbar-nav navbar-right">
              <li class="">
                  <a href="<?php //echo base_url()?>user/login" class=" "> Log in </a>                 
              </li>
              <li class="">
                  <a href="<?php //echo base_url()?>auth/new_user"> Create account </a>                 
              </li>
             
              </ul>
   -->
            </nav>
          </div>

        </div>
        <!-- /top navigation -->

 
 
 
 
 
 
 
 
 
 