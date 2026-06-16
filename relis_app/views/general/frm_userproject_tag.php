<?php
/* ReLiS - Issue #103 - Formulaire d'attribution de tag à un user du projet */

// Récupère les users du projet courant uniquement
$db_project = $this->load->database(project_db(), TRUE);
$db_system = $this->db;

$project_id = $this->session->userdata('project_id');

$users = $db_system->query("
    SELECT u.user_id, u.user_name
    FROM userproject up
    JOIN users u ON u.user_id = up.user_id
    WHERE up.project_id = ?
      AND up.userproject_active = 1
    ORDER BY u.user_name
", array($project_id))->result_array();

$tags = $db_project->query("
    SELECT * FROM reviewer_tag
    WHERE tag_active = 1
    ORDER BY tag_name
")->result_array();

// Données existantes en mode édition
$is_edit = !empty($current_element);
$current_user_id = $is_edit && isset($content_item['user_id']) ? $content_item['user_id'] : '';
$current_tag_id  = $is_edit && isset($content_item['tag_id'])  ? $content_item['tag_id']  : '';
$record_id       = $is_edit && isset($content_item['userproject_tag_id']) ? $content_item['userproject_tag_id'] : '';
?>

<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?= $is_edit ? 'Edit User Tag Assignment' : 'Assign tag to user' ?></h2>
                    <?php if (isset($top_buttons)) {
                        echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    } ?>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <form class="form-horizontal" method="POST"
                          action="<?= base_url('element/save_userproject_tag') ?>"

                        <input type="hidden" name="page_action" value="<?= $is_edit ? 'edit' : 'add' ?>">
                        <input type="hidden" name="page_action_target" value="<?= $is_edit ? 'edit_userproject_tag' : 'add_userproject_tag' ?>">
                        <?php if ($is_edit): ?>
                            <input type="hidden" name="userproject_tag_id" value="<?= htmlspecialchars($record_id) ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="control-label col-md-3">User <span style="color:red">*</span></label>
                            <div class="col-md-6">
                                <select name="user_id" class="form-control" required <?= $is_edit ? 'disabled' : '' ?>>
                                    <option value="">— Select a user —</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['user_id'] ?>" <?= $current_user_id == $u['user_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($u['user_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($is_edit): ?>
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($current_user_id) ?>">
                                <?php endif; ?>
                                <small class="text-muted">Only users of the current project are listed.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Tag <span style="color:red">*</span></label>
                            <div class="col-md-6">
                                <select name="tag_id" class="form-control" required>
                                    <option value="">— Select a tag —</option>
                                    <?php foreach ($tags as $t): ?>
                                        <option value="<?= $t['tag_id'] ?>" <?= $current_tag_id == $t['tag_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($t['tag_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Save
                                </button>
                                <a href="<?= base_url('element/entity_list/list_userproject_tag') ?>"
                                   class="btn btn-default">Cancel</a>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>