<!-- Select2 -->
<script src="<?php echo site_url(); ?>cside/vendors/select2/dist/js/select2.full.min.js"></script>
<!-- page content -->
<div class="right_col" role="main">
    <?php top_msg(); ?>
    <div class="">

        <div class="page-title">


        </div>

        <div class="clearfix"></div>

        <div class="row">

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <?php //header_perspective('screen');?>
                        <h2>
                            <?php echo isset($page_title) ? $page_title : ""; ?>
                        </h2>
                        <?php
                        if (isset($top_buttons)) {
                            echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                        }
                        ?>



                        <div class="clearfix"></div>

                    </div>



                    <div class="x_content" style="min-height:400px ">
                        <?php
                        if (!empty($screening_phase_info['description'])) {
                            ?>
                            <p><b><i>
                                        <?php echo $screening_phase_info['description'] ?>
                                    </i></b></p>
                            <hr />

                            <?php
                        }
                        if (isset($screen_completion)) {
                            ?>
                            <div>
                                <p><b><i>Screening completion :
                                            <?php echo "$paper_screened /  $all_papers -> " . $screen_completion ?>%
                                        </i></b></p>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-success" data-transitiongoal="20"
                                        style=" width:<?php echo $screen_completion ?>%" aria-valuenow="20"></div>
                                </div>

                            </div>
                            <div class="ln_solid"></div>
                        <?php }
                        if (!empty($the_paper)) {
                            ?>
                            <div class='whole'>
                                <div class="paper">
                                    <p class='lead '>Paper :
                                        <?php echo $paper_title ?>
                                    </p>


                                    <?php
                                    if (!empty($paper_abstract)) {
                                        echo " <p><b>Abstract :</b> <br/>";
                                        echo $paper_abstract;
                                        echo " </p><br/><br/>";
                                    }

                                    if (!empty($paper_preview)) {
                                        echo " <p><b>Preview :</b> <br/>";
                                        echo $paper_preview;
                                        echo " </p><br/><br/>";
                                    }

                                    if (!empty($paper_bibtex)) {
                                        echo " <p><b>Bibtex :</b> <br/>";
                                        echo $paper_bibtex;
                                        echo " </p><br/><br/>";
                                    }


                                    if (!empty($assignment_note)) {
                                        echo "<p><b>Assignment note :</b> <br/>";
                                        echo $assignment_note . " </p>";

                                    }

                                    if (!empty($paper_link)) {
                                        if ((strpos($paper_link, 'http://') === FALSE) && (strpos($paper_link, 'https://') === FALSE)) {
                                            $paper_link = "//" . $paper_link;
                                        }
                                        ?>
                                        <p style="text-align: right; padding:20px;">
                                            <a href="<?php echo $paper_link ?>" target="_blank" style="color:#aa7777;"><img
                                                    src='<?php echo base_url() ?>cside/images/pdf.jpg' /></a>
                                        </p>

                                    <?php } ?>
                                    <a href="<?php echo base_url() . "element/edit_element/edit_paper/" . $the_paper . "?from=screen_paper" ?>"><button class="btn btn-info btn-lg" type="button">Edit</button></a>
                                </div>
                                <div class='vl_solid'></div>
                                <div class='decision'>
                                    <!--                            <div class="ln_solid"></div>-->
                                    <p class="lead">Decision</p>
                                    <?php
                                    $attributes = array('class' => 'form-horizontal form_content', 'onsubmit' => " return  validate_screen()");

                                    echo form_open('screening/save_screening', $attributes); ?>


                                    <!-- <form class="form-horizontal" action="save_screening" method="POST" onsubmit=" return  validate_screen()"> -->
                                    <div style='text-align:center' class="screen_decision">

                                        <button class="btn  btn-lg" type="button" onclick="include_paper()">Include</button>
                                        &nbsp &nbsp &nbsp
                                        <button class="btn btn-danger btn-lg" type="button"
                                            onclick="exclude_paper()">Excluded</button>
                                        <br />
                                    </div>
                                    <br /><br />

                                    <?php

                                    echo '<div class="exclusion_crit" >' . dropdown_form_bm('Excluded criteria', 'criteria_ex', 'criteria_ex', $exclusion_criteria, !empty($content_item['exclusion_criteria']) ? $content_item['exclusion_criteria'] : 0) . "</div>";

                                    if (!empty($inclusion_criteria)) {
                                        echo '<div class="inclusion_crit" style="display: none">' . dropdown_form_bm('Included criteria', 'criteria_in', 'criteria_in', $inclusion_criteria, !empty($content_item['inclusion_criteria']) ? $content_item['inclusion_criteria'] : 0) . "</div>";
                                    }

                                    echo input_textarea_bm('Note ', 'note', 'note', !empty($content_item['screening_note']) ? $content_item['screening_note'] : '');

                                    //  echo  form_hidden(array( 'decision' => 'exclude'));
                                
                                    //  echo checkbox_form_bm('Not sure ','idccx','idccx');
                                    $save_caption = (!empty($operation_type) and $operation_type == 'edit') ? lng('Save') : lng('Save and Next');
                                    ?>
                                    <input type="hidden" name="screening_id" id="screening_id"
                                        value="<?php echo $screening_id ?>" />
                                    <input type="hidden" name="decision" id="decision"
                                        value="<?php echo !empty($content_item['decision']) ? $content_item['decision'] : 'excluded' ?>" />
                                    <input type="hidden" name="operation_type" id="operation_type"
                                        value="<?php echo !empty($operation_type) ? $operation_type : 'new' ?>" />
                                    <input type="hidden" name="screening_phase" id="screening_phase"
                                        value="<?php echo !empty($screening_phase) ? $screening_phase : '1' ?>" />
                                    <input type="hidden" name="operation_source" id="operation_source"
                                        value="<?php echo !empty($operation_source) ? $operation_source : 'list_screen/mine_screen' ?>" />
                                    <input type="hidden" name="paper_id" id="paper_id" value="<?php echo $the_paper ?>" />
                                    <input type="hidden" name="assignment_id" id="assignment_id"
                                        value="<?php echo $assignment_id ?>" />
                                    <input type="hidden" name="screen_type" id="screen_type"
                                        value="<?php echo $screen_type ?>" />
                                    <div class="ln_solid"></div>
                                    <div style='text-align:center'>

                                        <button class="btn btn-info btn-lg" type="submit">
                                            <?php echo $save_caption; ?>
                                        </button>
                                        <br />
                                    </div>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>

                    </div>






                    <div style="display:none">
                        <div style='text-align:center' class="screen_decision_include">

                            <button class="btn btn-success btn-lg" type="button"
                                onclick="include_paper()">Included</button> &nbsp &nbsp &nbsp
                            <button class="btn  btn-lg" type="button" onclick="exclude_paper()">Exclude</button>
                            <br />
                        </div>

                        <div style='text-align:center' class="screen_decision_exclude">

                            <button class="btn  btn-lg" type="button" onclick="include_paper()">Include</button> &nbsp
                            &nbsp &nbsp
                            <button class="btn btn-danger btn-lg" type="button"
                                onclick="exclude_paper()">Excluded</button>
                            <br />

                        </div>
                    </div>


                    <script>

                        function validate_screen() {

                            if ($('.exclusion_crit').css('display') != 'none') {
                                var crit = $('#criteria_ex').val();

                                if (crit == '0' || crit == '') {
                                    alert("You must select an exclusion criteria");
                                    return false;
                                } else {
                                    return true;
                                }
                            } else {
                                return true;
                            }


                        }

                        function include_paper() {

                            var content = $('.screen_decision_include').html();
                            $('.screen_decision').html(content);
                            $('.exclusion_crit').hide();
                            $('.inclusion_crit').show();
                            $('#decision').val('accepted');

                        }

                        function exclude_paper() {
                            var content = $('.screen_decision_exclude').html();
                            $('.screen_decision').html(content);
                            $('.exclusion_crit').show();
                            $('.inclusion_crit').hide();
                            $('#decision').val('excluded');
                        }


                        <?php if (!empty($content_item) and $content_item['screening_decision'] == 'Included') { ?>
                            $(document).ready(function () {
                                include_paper();
                            });

                        <?php } ?>

                    </script>





                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page content -->