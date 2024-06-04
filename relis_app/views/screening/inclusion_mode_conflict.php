	<!-- Select2 -->
    <script src="<?php echo site_url();?>cside/vendors/select2/dist/js/select2.full.min.js"></script>
	<!-- page content -->
        <div class="right_col" role="main">
          <div class="">
          
          <div class="page-title">
              
             
            </div>
            
            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" >
                  <div class="x_title">
                     <?php  //header_perspective('screen');?>
                    <h2><?php echo isset($page_title) ? $page_title :"" ; ?></h2>
                    <?php 
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    }                    
                    ?>
                    <div class="clearfix"></div>
                    <h1>Inclusion mode conflict</h1>
                  </div>
                  <div class="x_content" style="min-height:400px ">
                  <div style='text-align:center'>
                  <h2>
                    Changing inclusion mode from <b>'<? echo $current_inclusion_mode ?>'</b> to <b>'<? echo $post_arr['screening_inclusion_mode'] ?>'</b>
                    rises conflict in database. How would you like to proceed ?
                    <br>
                  
                  <?php
                    $attributes = array('class' => 'form-horizontal form_content');
                    echo form_open('screening/solve_mode_conflict', $attributes);
                    $conflict_name = $current_inclusion_mode . $post_arr['screening_inclusion_mode'];
                    switch($conflict_name) {
                      case 'NoneOne' :
                          echo ' <br> You can choose to either reset the screening for all papers or continue with possible inconsistencies. <br><br>'; 
                          echo '<button class="btn  btn-lg" type="submit" name="reset">Reset Screening</button>';
                          echo '<button class="btn  btn-lg" type="submit" name="no_reset">Do not reset screening</button>';
                        break;
                      case 'NoneAny' :
                        echo ' <br> You can choose to either reset the screening for all papers or continue with possible inconsistencies. <br><br>'; 
                          echo '<button class="btn  btn-lg" type="submit" name="reset">Reset Screening</button>';
                          echo '<button class="btn  btn-lg" type="submit" name="no_reset">Do not reset screening</button>';
                        break;
                      case 'AnyOne' :
                        echo ' <br> You may choose to reset the screening, or keep the first criterion only. <br><br>'; 
                          echo '<button class="btn  btn-lg" type="submit" name="reset">Reset Screening</button>';
                          echo '<button class="btn  btn-lg" type="submit" name="keep_one">Keep first criterion</button>';
                        break;
                    }
                  ?>
                  <input type="hidden" name="config_array" id="config_array" value="<? echo htmlspecialchars(serialize($post_arr)) ?>">
                  </h2>
                  
<br />
</div>
<br /><br />
                  
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->  