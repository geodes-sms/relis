<div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="<?php echo site_url();?>user.html" class="site_title"><i class="fa fa-book"></i> <span>ReLiS</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <a href="<?php echo base_url();?>element/display_element/detail_user_min_ed/<?php echo active_user_id()?>">
            <div class="profile">
              <div class="profile_pic">
              <?php 
              $images=site_url()."cside/images/img.jpg";
              
              if(($this->session->userdata('user_picture'))){
              	$user_picture=$this->session->userdata('user_picture');
              	if(!empty($user_picture)){
              	
              	$images=display_picture_from_db($user_picture);'data:image/png;base64,'.base64_encode( $user_picture);
              	}
                            }
							
				$left_menu = $this->manager_lib->get_left_menu_qa();
             
              ?>
                <img src="<?php echo $images;?>" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                
                <h2><?php 
                echo $this->session->userdata('user_name')." " ; ?></h2>
              </div>
            </div>
            </a>
            <!-- /menu profile quick info -->

            <br />
            <br />
            <br />
            <br />
           
            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
			
			<?php
			
			foreach ($left_menu as $key_menu_section => $value_menu_section) {
			?>	
				<div class="menu_section">
                <h3><?php echo lng($value_menu_section['label']) ?></h3>
				<?php 
				if(!empty($value_menu_section['menu'])){
				
				?>
				 	<ul class="nav side-menu">
				 <?php 
				 foreach ($value_menu_section['menu'] as $key_menu => $value_menu) {
				 	$label=(!empty($value_menu['label'])?lng($value_menu['label']):"");
				 	$icon=(!empty($value_menu['icon'])?icon($value_menu['icon']):"");
				 	$url=(!empty($value_menu['url'])?($value_menu['url']):"");
				 	
				 	if(empty($value_menu['sub_menu'])){				 		
				 		echo "<li>".anchor($url,$icon.$label)."</li>";
				 	}else{
				 	
				 		echo '<li><a>'.$icon.$label.'<span class="fa fa-chevron-down"></span></a>';
				 		
				 		?>
				 		 <ul class="nav child_menu">
				 		 <?php 
				 		 foreach ($value_menu['sub_menu'] as $key_sub_menu => $value_sub_menu) {
				 		 	
				 		 	$label=(!empty($value_sub_menu['label'])?lng($value_sub_menu['label']):"");
				 		 	$icon=(!empty($value_sub_menu['icon'])?icon($value_sub_menu['icon']):"");
				 		 	$url=(!empty($value_sub_menu['url'])?($value_sub_menu['url']):"");
				 		 	
				 		 	echo "<li>".anchor($url,$icon.$label)."</li>";
				 		 
				 		 }
				 		 ?>			 		 
				 		 
				 		 </ul>
				 		</li>
				 		<?php 
				 		
				 	}
				 	
				 }
				 
				 ?>	
				
					</ul>
				<?php
					
					
					
					
				}				
				
				?>
				
				</div>
			<?php 	
			}
			
			
			
			?>
			
		

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="<?php echo lng_min('Main')?>" href="<?php echo base_url() ?>project/projects_list.html">
                <span class="glyphicon glyphicon-transfer" aria-hidden="true"></span>
              </a>
              
              <a  data-toggle="tooltip" data-placement="top" title="<?php echo lng_min('Home')?>" href="<?php echo base_url()?>screening/screening_select.html">
                <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
              </a>
              
              <a  data-toggle="tooltip" data-placement="top" title="<?php echo lng_min('Dashboard')?>" href="<?php echo base_url()?>home.html">
                <span class="glyphicon glyphicon-th" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="<?php echo lng_min('Log Out')?>" href="<?php echo base_url()?>/user/discon">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>