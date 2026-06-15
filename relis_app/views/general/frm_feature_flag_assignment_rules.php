<!-- ReLiS - Issue #103 - Admin toggle for the assignment rules feature flag -->

<div class="right_col" role="main">
    <?php top_msg(); ?>
    <div class="">

        <div class="page-title"></div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Assignment Rules feature</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <?= get_top_button('back') ?>
                        </ul>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">

                        <p class="text-muted" style="margin-bottom:20px;">
                            <i class="fa fa-info-circle"></i>
                            When enabled, project managers can define <b>reviewer tags</b>
                            and <b>assignment rules</b> that shape how papers are distributed
                            across reviewers. Turning it off only hides the menu entries —
                            existing tags and rules are preserved.
                        </p>

                        <form class="form-horizontal" method="POST"
                              action="<?= base_url('admin/save_feature_flag_assignment_rules') ?>">

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                    Feature enabled <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="hidden" name="enabled" value="0">
                                    <input type="checkbox" name="enabled" value="1"
                                           class="js-switch"
                                            <?= $is_enabled ? 'checked' : '' ?> />
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>