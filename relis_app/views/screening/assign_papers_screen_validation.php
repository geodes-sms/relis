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
                    <h2 id="page_title_1"><?php echo isset($page_title) ? $page_title :"" ; ?></h2>
                    <?php
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";

                    }
                    ?>



                    <div class="clearfix"></div>
                  </div>



                  <div class="x_content" style="min-height:400px ">

                 <div class="tab-pane active" id="home">

                     <p><b> Number of papers to assign: <span id="number_papers"><?php echo $number_papers; ?></span></b>
                         <br/><i> Number of papers already assigned: <span id="number_papers_assigned"><?php echo $number_papers_assigned; ?></span></i><br/></p>

                     <?php
                         $attributes = array('class' => 'form-horizontal form_content');
                         echo form_open_multipart('screening/save_assign_screen_validation',$attributes);


                         if(validation_errors() OR !empty($err_msg))
                         {
                         	echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
					<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>
					<strong>'.lng('Error').'!</strong>';
                         	echo validation_errors();
                         	if (isset($err_msg))echo $err_msg;
                         	echo "</div>";
                         }

                         echo form_hidden(array( 'number_of_users' => count($users)));
                         echo form_hidden(array( 'screening_phase' => $screening_phase));
                         echo form_hidden(array( 'papers_sources' => $papers_sources));
                         echo form_hidden(array( 'paper_source_status' => $paper_source_status));


                        if(empty($assign_to_connected)){
                        	echo ' <p class="lead">Select validator(s) </p>';
                        	$i=1;
		                        foreach ($users as $user_id => $user_name) {
                                    if (get_appconfig_element('assign_to_non_screened_validator_on')){
                                        echo checkbox_form_bm($user_name.' (papers available to assign: ' . count($user_papers_map[$user_id]) . ')','user_'.$i,'user_'.$user_id,$user_id);
                                    }else {
                                        echo checkbox_form_bm($user_name, 'user_' . $i, 'user_' . $user_id, $user_id);
                                    }
                                    $i++;
		                        }


		                         echo "<hr/>";
                        }else{
                        	echo form_hidden(array( 'assign_papers_to' => active_user_id()));

                        }

                     if ($paper_source_status == 'Excluded') {
                         // Validation by exclusion criteria toggle switch
                         $label = "Validation by exclusion criteria";
                         $name = 'validation_by_exclusion_criteria_toggle';
                         $id = 'validation_by_exclusion_criteria_toggle';

                         echo '<div class="form-group">';
                         echo form_label($label, $name, array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'));
                         echo '<div class="col-md-6 col-sm-6 col-xs-12">';
                         echo '<input type="checkbox" id="' . $id . '" name="' . $name . '" class="js-switch" />';
                         echo '</div></div>';

                         // Choose exclusion criteria multi-select dropdown
                         echo '<div class="form-group" id="exclusion_criteria_div" style="display:none;">';
                         echo form_label('Choose exclusion criteria', 'choose_exclusion_criteria', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'));
                         echo '<div class="col-md-6 col-sm-6 col-xs-12">';
                         echo '<select name="choose_exclusion_criteria[]" id="choose_exclusion_criteria" class="form-control select2_multiple" multiple>';
                         foreach ($validation_by_exclusion_criteria as $value) {
                             echo "<option value=\"$value\">$value</option>";
                         }
                         echo '</select>';
                         echo '</div></div>';
                         echo '<div id="selected_criteria" class="col-md-6 col-sm-6 col-xs-12"></div>';
                         echo "<hr/>";
                     }

                    //     echo dropdown_form_bm("Paper to validate",'paper_to_validate','paper_to_validate',$papers_categories,'Excluded',' 1 mandatory');

                        echo input_form_bm(lng('Percentage of papers(%)'),'percentage','percentage',$percentage_of_papers);


                    ?>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">

                          <button  class="btn btn-success">Assign</button>
                        </div>
                      </div>

                    <?php
                    echo form_close();

                    echo "<hr/>";
                    echo "<h2>Preview of papers to assign</h2>";

                    if(!empty($paper_list)){
                    	$tmpl = array (
                    			'table_open'  => '<table class="table table-striped table-hover" id="paper_list_table">',
                    			'table_close'  => '</table>'
                    	);

                    	$this->table->set_template($tmpl);

                    	echo $this->table->generate($paper_list);
                    }

                    ?>

                        </div>




                    <div class="clearfix"></div>

                  </div>
                    <script>
                        $(document).ready(function() {
                            $('#validation_by_exclusion_criteria_toggle').change(function() {
                                if ($(this).is(':checked')) {
                                    $('#exclusion_criteria_div').show();
                                    $('#choose_exclusion_criteria').attr('required', true);
                                } else {
                                    $('#exclusion_criteria_div').hide();
                                    $('#choose_exclusion_criteria').removeAttr('required');
                                    resetPageToInitialState();
                                }
                            });

                            $('#choose_exclusion_criteria').change(function() {
                                var selectedCriteria = $(this).val();
                                fetchPapersBasedOnCriteria(selectedCriteria);
                            });

                            function fetchPapersBasedOnCriteria(criteriaArray) {
                                $.ajax({
                                    url: '<?php echo site_url('screening/get_papers_by_criteria'); ?>',
                                    type: 'POST',
                                    data: { validation_by_criteria: criteriaArray },
                                    dataType: 'json',
                                    success: function(response) {
                                        updatePageContent(response);
                                    },
                                    error: function() {
                                        alert('Error fetching data');
                                    }
                                });
                            }

                            function updatePageContent(data) {
                                $('#number_papers').text(data.number_papers);
                                $('#number_papers_assigned').text(data.number_papers_assigned);
                                $('#page_title_1').text(data.page_title);

                                var table = $('#paper_list_table');
                                table.empty();
                                data.paper_list.forEach(function(paper) {
                                    table.append('<tr><td>' + paper[0] + '</td><td>' + paper[1] + '</tr>');
                                });
                            }
                        });

                    </script>


                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->