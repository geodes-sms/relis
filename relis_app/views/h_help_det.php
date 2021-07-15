        <!-- page content -->
        <div class="right_col" role="main">
          
          <?php top_msg(); ?> 
 <div class="col-md-12 col-sm-12 col-xs-12">
             <div class="x_panel home_page" style="min-height:600px;">
            <div class="row">

             <?php 
              if(!empty($help_info))
             {
             	$button =!empty($top_buttons)?$top_buttons:"";
             	
                          box_header($button."  ".(!empty($help_info['info_title'])?$help_info['info_title']:""),"",12,12,12);
                          ?>
                          
                          <p class="" style="text_align:justify">
             				 <?php echo !empty($help_info['info_desc'])?$help_info['info_desc']:""?>
             			</p>
                         
                       <?php
					
					if(!empty($help_info['info_link'])){
					?>
					<hr/>
                       <video width="" height="520" controls="controls" margin="">
                       <source src="http://localhost/relis/relis_dev/cside/media/video/<?php echo $help_info['info_link'];?>" type="video/mp4">
                       </video>
             		<?php 
             
             }
             box_footer();
             }
             ?>
             
            </div>               
                </div>
              </div>
            </div>
        <!-- /page content -->