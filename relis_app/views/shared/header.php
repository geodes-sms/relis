<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ReLis | App</title>

    <!-- Bootstrap -->
    <link href="<?php echo site_url();?>cside/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo site_url();?>cside/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

     <!-- Switchery -->
    <link href="<?php echo site_url();?>cside/vendors/switchery/dist/switchery.min.css" rel="stylesheet">
    
    <!-- Select2 -->
    <link href="<?php echo site_url();?>cside/vendors/select2/dist/css/select2.min.css" rel="stylesheet">
     <!-- iCheck -->
    <link href="<?php echo site_url();?>cside/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- PNotify -->
    <link href="<?php echo site_url();?>cside/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="<?php echo site_url();?>cside/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="<?php echo site_url();?>cside/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
     <!-- Datatables -->
    <link href="<?php echo site_url();?>cside/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo site_url();?>cside/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    
   
    <!-- Custom Theme Style -->
    <link href="<?php echo site_url();?>cside/css/custom.css" rel="stylesheet">
    
    
    <link href="<?php echo site_url();?>cside/css/my_styles.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="<?php echo site_url();?>cside/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo site_url();?>cside/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
       <?php if (isset($has_graph))
			{
			
			?>
	
	<script src="<?php echo site_url(); ?>cside/js/highcharts.js"></script>
	
	<script src="<?php echo site_url(); ?>cside/js/drilldown.js"></script>
	<script src="<?php echo site_url(); ?>cside/js/exporting.js"></script>
	
	<?php }?>
    
  </head>
  
  	
<body class="nav-md">
  
 

    <div class="container body">
      <?php 
    $background="";
  //  print_test($this->session->userdata('working_perspective'));
    if($this->session->userdata('working_perspective')=='screen')
    
    	{
    		$background='style="background-color: gray;"';
    	}
    
   /*
    if (! empty($project_perspective) AND $project_perspective='screening'){
    	$background='style="background-color: gray;"';
    }*/
    ?>
      <div class="main_container"  <?php echo $background?>>
        
        
       <?php if( project_db()!='default'){
       	
       	if($this->session->userdata('working_perspective')=='screen')
       	
       	{
       		$this->load->view('screening/left_menu_screening');
       	}elseif($this->session->userdata('working_perspective')=='qa')
       	
       	{
       		$this->load->view('quality_assessment/left_menu_qa');
       	}else{
       		$this->load->view('left_menu');
       		 
       	}
       }else{
       	$this->load->view('left_menu_admin');
       }  ?> 

        <!-- top navigation -->
        <div class="top_nav">

          <div class="nav_menu">
            <nav class="" role="navigation">
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
				
              </div>
             
              <ul class="nav navbar-nav navbar-right" >
              	  
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <?php 
                  $images=site_url()."cside/images/img.jpg";
                  
                  if(($this->session->userdata('user_picture'))){
              	$user_picture=$this->session->userdata('user_picture');
              	if(!empty($user_picture)){
              	$images=site_url().$this->config->item('image_upload_path').$user_picture."_thumb.jpg";
              	$images='data:image/png;base64,'.base64_encode( $user_picture);
              	}
                            }
             
              ?>
                    <img src="<?php echo $images;?>" alt=""><?php echo $this->session->userdata('user_name')." " ; ?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    
                    <li><a href="<?php echo base_url()?>/user/discon""><i class="fa fa-sign-out pull-right"></i><?php echo lng_min('Log Out')?></a>
                    </li>
                    <li><a href="<?php echo base_url();?>element/display_element/detail_user_min_ed/<?php echo active_user_id()?>"></i><?php echo lng_min('Profile')?></a>
                    </li>
                  </ul>
                </li>
                <li role="presentation" class="dropdown">
                  <span class="dropdown-toggle info-number" aria-expanded="false">
                    <?php $this->load->view('lang'); ?>                  
                  </span>                  
                </li >
			 	<?php 
			 	if(debug_coment_active())
			 		echo "<li>".debug_comment_button().'</li>';
				if($this->session->userdata('project_title')){
				?>
					<li>
					 
	              	<h2 style="padding-top: 10px;  padding-right: 100px ;"><?php echo $this->session->userdata('project_title')?> &nbsp  &nbsp</h2> 
	              
	              	</li>
                <?php 
				}
                ?>
                </ul>
                </li>

              </ul>
            </nav>
          </div>

        </div>
        <!-- /top navigation -->