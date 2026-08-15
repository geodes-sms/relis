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
                        if ($null_screen_type_error) {
                            echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
					          <button class="close" aria-label="Close" data-dismiss="alert" type="button">
					          <span aria-hidden="true">×</span>
					          </button>
					          <strong>Error!</strong>
					          <p>All the fields should be filled</p></div>';
                        }

                        echo form_open('screen_phases/save_screen_phase', $attributes);
                        echo input_form_bm("title", "title", 1);
                        if ($lock_screen_types == true) {
                            foreach ($screen_type as $field) {
                                echo '<input type="hidden" name="displayed_fields[]" value="'. $field .'" />';
                            }
                        } else {
                            echo dropdown_multi_form_bm("Fields", "displayed_fields", 2, $screen_type);
                        }

                        if (count($parent) == 1) {
                            echo '<input type="hidden" name="parent" value="'. array_key_first($parent) .'" />';
                        } else {
                            echo dropdown_form_bm("parent", "parent", 3, $parent);
                        }

                        if (count($child) == 1) {
                            echo '<input type="hidden" name="child" value="'. array_key_first($child) .'" />';
                        } else {
                            echo dropdown_form_bm("Child", "child", 3, $child);
                        }

                        echo '<input type="hidden" name="add_type" value="'. $add_type .'" />';
                        echo '<input type="hidden" name="id" value="'. $id .'" />';
                        ?>

                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 col-sm-offset-3">
                                <button class="btn btn-info" type="submit">
                                    Save
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