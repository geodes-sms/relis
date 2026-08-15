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
                    </div>

                    <div class="x_content" style="min-height:400px ">
                        <?php
                        $attributes = array('class' => 'form-horizontal form_content', 'onsubmit' => " return  validate_screen()");

                        if ($null_title_error) {
                            echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
					          <button class="close" aria-label="Close" data-dismiss="alert" type="button">
					          <span aria-hidden="true">×</span>
					          </button>
					          <strong>Error!</strong>
					          <p>The title cannot be empty</p></div>';
                        }

                        echo form_open('screen_phases/save_replacement', $attributes);

                        echo input_form_bm("Title", "new_title", 1, $phase_title);

                        echo '<input type="hidden" name="phase_id" value="'.$phase_id.'">';
                        ?>

                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 col-sm-offset-3">
                                <button class="btn btn-info" type="submit">
                                    Replace phase
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>











                </div>






            </div>
        </div>
    </div>
</div>
</div>
<!-- /page content -->