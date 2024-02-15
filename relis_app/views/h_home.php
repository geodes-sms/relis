<!-- page content -->
<div style="" class="right_col " role="main">

  <?php top_msg(); ?>
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel home_page" style="padding:40px;padding-top:20px;">

      <?php if (!empty($home_info)) {
        ?>
        <div class="row">

          <?php
          // box_header("ReLiS A tool for conducting Systematic Review","",12,12,12);
          ?>
          <h1 class="lead">
            <?php echo !empty($home_info['info_title']) ? $home_info['info_title'] : "" ?>
          </h1>
          <hr />
          <p class="" style="text_align:justify">
            <?php echo !empty($home_info['info_desc']) ? $home_info['info_desc'] : "" ?>
          </p>

        </div>
      <?php }

      if (!empty($home_features)) {
        ?>

        <div class="row">

          <hr />
          <h1 class="lead">
            <?php echo lng('What can you do in ReLiS?') ?>
          </h1>

          <div class="dashboard-widget-content what_relis">

            <ul class="list-unstyled timeline widget">
              <?php
              foreach ($home_features as $key => $feature) {
                ?>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>
                          <?php echo !empty($feature['info_title']) ? $feature['info_title'] : "" ?>
                        </a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt">
                        <?php echo !empty($feature['info_desc']) ? $feature['info_desc'] : "" ?>
                      </p>
                    </div>
                  </div>
                </li>

              <?php
              }

              ?>


            </ul>
          </div>


        </div>


      <?php

      }


      if (!empty($home_ref)) {
        ?>
        <div class="row">

          <h1 class="lead">
            <?php echo !empty($home_ref['info_title']) ? $home_ref['info_title'] : "" ?>
          </h1>

          <p class="" style="text_align:justify">
            <?php echo !empty($home_ref['info_desc']) ? $home_ref['info_desc'] : "" ?>
          </p>

        </div>
      <?php }

      ?>


    </div>
  </div>

  <div class="row udm_logo" style="padding:3px">

    <div class="col-md-4 col-sm-4 col-xs-12">
      <a href="http://geodes.iro.umontreal.ca" target="_BLANK" title="GEODES Software Engineering Research Group">
        <img src="<?php echo site_url(); ?>cside/images/geodes.png" />
      </a>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12" style="text-align:center">
      <a href="http://diro.umontreal.ca" target="_BLANK" title="Department of Computer Science and Operations Research">
        <img src="<?php echo site_url(); ?>cside/images/diro_logo.png" />
      </a>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12" style="text-align:right">
      <a href="http://umontreal.ca" target="_BLANK" title="Université de Montréal">
        <img style="height:40px" src="<?php echo site_url(); ?>cside/images/udem_logo.png" />
      </a>
    </div>
  </div>
</div>
<!-- /page content -->