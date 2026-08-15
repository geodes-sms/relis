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
                        if ($error) {
                            echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
					<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>
					<strong>Error!</strong>
					<p>Every fields should be filled</p></div>';
                        }
                        echo form_open('screen_phases/save_modification', $attributes);

                        echo input_form_bm("Title", "title", 1, $phase['phase_title']);

                        if ($editable_displayed_fields) {
                            echo dropdown_multi_form_bm('Displayed Fields',
                                'displayed_fields',
                                1,
                                $displayed_fields,
                                explode("|",$phase['displayed_fields'])
                            );
                            echo '<div class="form-group" style="margin-bottom: 10px;">
                                  <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3 col-sm-offset-3">
                                  <p class="text-warning" style="margin-top: 5px; font-size: 12px;">
                                  <i class="fa fa-exclamation-triangle"></i> <b>Thoses phases will also be updated </b> : ' . $siblings_titles . '.
                                  </p>
                                  </div>
                                  </div>';
                        } else {
                            echo '<label class="control-label col-md-3 col-sm-3 col-xs-12">Displayed Fields</label>
                                  <div class="col-md-9 col-sm-9 col-xs-12" style="padding-top: 8px;">
                                  <span class="label label-default" style="font-size: 12px; padding: 5px 8px;">
                                  ' . str_replace("|", ", ", $phase['displayed_fields']) . '
                                  </span>
                                  </div>';
                        }

                        echo '<input type="hidden" name="phase_id" value="'.$phase['screen_phase_id'].'">';
                        ?>

                        <div class="form-group" style="clear: both; padding-top: 20px;">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button class="btn btn-success" type="submit">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            </div>
                        </div>
                        </form>

                        <?php if ($phase['used'] == 0) {
                            echo '<div class="ln_solid" style="margin: 30px 0;"></div>';
                            echo '<div class="form-group">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12" style="text-align: right;">Configuration</label>
                                  <div class="col-md-9 col-sm-9 col-xs-12">
                                  <a title="Toggle Configuration" id="toggle-button" class="btn btn-info" href="' . base_url() . '/screen_phases/toggle_phase_config/' . $phase['screen_phase_id'] . '">
                                        <i class="fa fa-gear"></i> Toggle Configuration type
                                  </a>
                                  
                                  <p style="margin-top: 12px; font-size: 13px;">
                                  <a href="' . base_url() . '/screening/route_config/' . $phase['screen_phase_id'] . '" class="label label-primary" style="font-size: 12px; padding: 5px 10px; display: inline-block;" title="Cliquez pour modifier la configuration">
                                  <i class="fa fa-pencil"></i> ' . $config_type . '
                                  </a></p>
                                  </div></div>';
                        } ?>

                    </div>











                </div>






            </div>
        </div>
    </div>
</div>
</div>
<!-- /page content -->