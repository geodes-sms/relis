<!-- page content -->
<div class="right_col" role="main">
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

            <div class="tab-pane active" id="home">

              <p><b> Number of papers to assign :
                  <?php echo $number_papers ?>
                </b>
                <br /><i> Number of papers already assigned :
                  <?php echo $number_papers_assigned ?>
                </i><br />
              </p>
              <p class="lead">Select reviewers </p>
              <?php
              $attributes = array('class' => 'form-horizontal form_content');
              echo form_open_multipart('screening/save_assignment_screen', $attributes);


              if (validation_errors() or !empty($err_msg)) {
                echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
					<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>
					<strong>' . lng('Error') . '!</strong>';
                echo validation_errors();
                if (isset($err_msg))
                  echo $err_msg;
                echo "</div>";
              }

              echo form_hidden(array('number_of_users' => count($users)));
              echo form_hidden(array('screening_phase' => $screening_phase));
              echo form_hidden(array('papers_sources' => $papers_sources));
              echo form_hidden(array('paper_source_status' => $paper_source_status));


              $i = 1;
              foreach ($users as $user_id => $user_name) {
                echo checkbox_form_bm($user_name, 'user_' . $i, 'user_' . $user_id, $user_id);
                $i++;
              }

              echo input_form_bm(lng('Reviews per paper'), 'reviews_per_paper', 'reviews_per_paper', $reviews_per_paper);


              ?>
              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">

                  <button class="btn btn-success">Assign</button>
                </div>


              </div>

              <?php
              echo form_close();

              echo "<hr/>";
              echo "<h2>Preview of papers to be assigned</h2>";

              if (!empty($paper_list)) {
                $tmpl = array(
                  'table_open' => '<table class="table table-striped table-hover">',
                  'table_close' => '</table>'
                );

                $this->table->set_template($tmpl);

                echo $this->table->generate($paper_list);
              }
              ?>

            </div>




            <div class="clearfix"></div>

          </div>


        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->