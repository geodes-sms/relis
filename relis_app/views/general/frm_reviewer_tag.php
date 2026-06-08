<?php
/* ReLiS - Issue #103 - Formulaire custom pour reviewer_tag */

$is_edit = !empty($current_element);

// En mode édition, charger les données du tag depuis la DB
$tag_data = null;
if ($is_edit) {
    $db_project = $this->load->database(project_db(), TRUE);
    $tag_data = $db_project->query(
        "SELECT * FROM reviewer_tag WHERE tag_id = ?",
        array(intval($current_element))
    )->row_array();
}

$tag_id          = $is_edit && !empty($tag_data) ? $tag_data['tag_id']          : '';
$tag_name        = $is_edit && !empty($tag_data) ? $tag_data['tag_name']        : '';
$tag_description = $is_edit && !empty($tag_data) ? $tag_data['tag_description'] : '';
$tag_color       = $is_edit && !empty($tag_data) ? $tag_data['tag_color']       : '#888888';
?>

<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?= $is_edit ? 'Edit Reviewer Tag' : 'Add a new reviewer tag' ?></h2>
                    <?php if (isset($top_buttons)) {
                        echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    } ?>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <form class="form-horizontal" method="POST"
                          action="<?= base_url('element/save_reviewer_tag') ?>">

                        <?php if ($is_edit): ?>
                            <input type="hidden" name="tag_id" value="<?= htmlspecialchars($tag_id) ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="control-label col-md-3">Name <span style="color:red">*</span></label>
                            <div class="col-md-6">
                                <input type="text" name="tag_name" class="form-control"
                                       value="<?= htmlspecialchars($tag_name) ?>"
                                       maxlength="50" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Description</label>
                            <div class="col-md-6">
                <textarea name="tag_description" class="form-control" rows="3"
                          maxlength="250"><?= htmlspecialchars($tag_description) ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Color</label>
                            <div class="col-md-6">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <input type="color" name="tag_color" id="tag_color_picker"
                                           value="<?= htmlspecialchars($tag_color) ?>"
                                           style="width:60px; height:40px; padding:2px; cursor:pointer;">
                                    <span id="tag_color_preview"
                                          style="display:inline-block; padding:6px 14px; border-radius:12px;
                                                  color:white; font-size:13px;
                                                  background-color:<?= htmlspecialchars($tag_color) ?>;">
                    Preview
                  </span>
                                    <code id="tag_color_value" style="color:#666;"><?= htmlspecialchars($tag_color) ?></code>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Save
                                </button>
                                <a href="<?= base_url('element/entity_list/list_reviewer_tag') ?>"
                                   class="btn btn-default">Cancel</a>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('tag_color_picker').addEventListener('input', function() {
        document.getElementById('tag_color_preview').style.backgroundColor = this.value;
        document.getElementById('tag_color_value').textContent = this.value;
    });
</script>