<?php
// Check if the session variable 'rsae_gpl' is not set
if (!isset($rsae_gpl)) {
  // Redirect to the previous page
  set_top_msg(lng_min("Error RSAE GPL variable is not set."), 'error');
  redirect($_SERVER['HTTP_REFERER']);
}
?>
<!-- page content -->
<div class="right_col" role="main">
  <?php top_msg();    ?>
  <div class="">

    <div class="page-title">


    </div>

    <div class="clearfix"></div>

    <div class="row">

      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><?php echo ucfirst($rsae_gpl) ?> export configuration</h2>

            <div class="clearfix"></div>
          </div>



          <div class="x_content" style="min-height:400px ">
            <h4>Select the scale for each category of the data extraction form</h4><br>

            <?php

            $rsae_gpl = $rsae_gpl;

            $attributes = array('class' => 'form-horizontal form_content');

            $fct_save = 'reporting/rsae_' . $rsae_gpl . '_export';

            echo form_open_multipart($fct_save, $attributes);

            $options = array(
              'Continuous' => 'Continuous',
              'Nominal' => 'Nominal',
              'Text' => 'Text'
            );



            foreach ($category as $key => $value) {
              // Determine the selected option based on conditions
              if (
                ($value['field_type'] === 'text' && $value['category_type'] != 'FreeCategory') ||
                ($value['field_type'] === 'int' && $value['category_type'] != 'FreeCategory') ||
                ($value['field_type'] === 'int' && $value['category_type'] === 'FreeCategory' && $value['input_type'] === 'select')
              ) {
                $selectedOption = 'Nominal';
              } elseif (
                ($value['field_type'] === 'int' || $value['field_type'] === 'real') &&
                $value['category_type'] === 'FreeCategory' &&
                $value['input_type'] != 'select'
              ) {
                $selectedOption = 'Continuous';
              } elseif ($value['field_type'] === 'text' && $value['category_type'] === 'FreeCategory') {
                $selectedOption = 'Text';
              } else {
                $selectedOption = '';
              }



              echo dropdown_form_bm(
                $value['field_title'],
                $value['field_title'],
                "",
                $options,
                $selectedOption,
                "",
                ""
              );
            }
            ?>

            <div class="ln_solid"></div>
            <div class="form-group">
              <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                <button type="submit" class="btn btn-success">Generate <?php echo ucfirst($rsae_gpl); ?></button>
              </div>
            </div>




            <?php


            echo form_close();


            ?>

          </div>






        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->