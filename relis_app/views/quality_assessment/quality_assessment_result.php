<!-- Select2 -->
<script src="<?php echo site_url(); ?>cside/vendors/select2/dist/js/select2.full.min.js"></script>
<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <?php top_msg(); ?>
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

            <div class="row">
              <?php
              if (!empty($qa_list)) {
                // Sorting function
                function sortListByScore($a, $b)
                {
                  return $b['q_result_score'] - $a['q_result_score'];
                }

                // Sort the data array by score
                usort($qa_list, 'sortListByScore');
                $tmpl = array(
                  'heading_cell_start' => '<th>',
                  'heading_cell_end' => '</th>',
                  'table_open' => '<table class="table  table-hover">',
                  'table_close' => '</table>'
                );
                //   print_test($qa_list)  ;
                $i = 1;
                $qa_tab = array();

                foreach ($qa_list as $k_assign => $v_assign) {
                  //print_test($v_assign);
                  if ($v_assign['q_result_score'] < $qa_cutt_off_score) {
                    $butt = 'btn-danger';
                  } else {
                    $butt = 'btn-info';
                  }
                  $score = "<span style='margin-right:10px'> <button type='button' class='btn btn-round $butt '> " . $v_assign['q_result_score'] . " </button></span>";
                  $completed = empty($v_assign['paper_done']) ? '' : "<button type='button' class='btn btn-round btn-success '> <i class='fa fa-check'></i> </button>";

                  $qa_tab[$k_assign]['paper'] = anchor('quality_assessment/qa_conduct_detail/' . $v_assign['paper_id'], '<u>' . $v_assign['title'] . '</u>');
                  $qa_tab[$k_assign]['score'] = $score;
                  $qa_tab[$k_assign]['done'] = $completed;

                }
                if (!empty($qa_list)) {
                  array_unshift($qa_tab, array('Paper', 'Score', 'Done'));
                }
                //   print_test($qa_tab);
                if (!empty($qa_tab)) {
                  $this->table->set_template($tmpl);
                  echo $this->table->generate($qa_tab);

                } else {
                  echo "No papers assigned for quality assessment ! ";
                }
                echo box_footer();
                $i++;

              }
              ?>





            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->