<?php
/* ReLiS - Issue #103 - Dynamic assignment constraint form (create + edit) */

$is_edit = !empty($current_element);

// Load existing constraint data in edit mode
$constraint = null;
if ($is_edit) {
    $db_lookup = $this->load->database(project_db(), TRUE);
    $constraint = $db_lookup->query(
            "SELECT * FROM assignment_constraint WHERE constraint_id = ?",
            array(intval($current_element))
    )->row_array();
}

$constraint_id       = $is_edit && $constraint ? $constraint['constraint_id']       : '';
$cur_scope           = $is_edit && $constraint ? $constraint['constraint_scope']    : '';
$cur_phase_id        = $is_edit && $constraint ? $constraint['phase_id']            : '';
$cur_type            = $is_edit && $constraint ? $constraint['constraint_type']     : 'min_tag_per_paper';
$cur_priority        = $is_edit && $constraint ? $constraint['constraint_priority'] : 100;
$cur_params          = ($is_edit && $constraint && !empty($constraint['constraint_params']))
        ? json_decode($constraint['constraint_params'], true)
        : array();
if (!is_array($cur_params)) { $cur_params = array(); }

// Pre-fill helpers (return value if matches, empty otherwise)
function _sel($current, $candidate) {
    return ((string)$current === (string)$candidate) ? 'selected' : '';
}

// Load tags + phases from project DB
$db_project = $this->load->database(project_db(), TRUE);
$tags   = $db_project->query("SELECT * FROM reviewer_tag WHERE tag_active = 1 ORDER BY tag_name ASC")->result_array();
$phases = $db_project->query("SELECT * FROM screen_phase WHERE screen_phase_active = 1 ORDER BY screen_phase_order")->result_array();

$scopes = array(
        'screening'                 => 'Screening',
        'screening_validation'      => 'Screening validation',
        'qa'                        => 'Quality assessment',
        'qa_validation'             => 'QA validation',
        'classification'            => 'Classification',
        'classification_validation' => 'Classification validation',
);

$types = array(
        'min_tag_per_paper'                        => 'Minimum N reviewers with a specific tag',
        'max_tag_per_paper'                        => 'Maximum N reviewers with a specific tag',
        'tag_combination'                          => 'Tag combination (option A OR option B)',
        'same_user_from_previous_phase'            => 'Reuse same reviewers as a previous phase',
        'force_different_user_from_previous_phase' => 'Forbid reviewers who were already assigned to the paper',
);
?>

<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?= $is_edit ? 'Edit assignment rule' : 'Add an assignment rule' ?></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <form class="form-horizontal" method="POST"
                          action="<?= base_url('element/save_assignment_constraint') ?>">

                        <?php if ($is_edit): ?>
                            <input type="hidden" name="constraint_id" value="<?= $constraint_id ?>">
                        <?php endif; ?>

                        <!-- Scope -->
                        <div class="form-group">
                            <label class="control-label col-md-3">Scope *</label>
                            <div class="col-md-6">
                                <select name="constraint_scope" id="constraint_scope" class="form-control" required>
                                    <?php foreach ($scopes as $k => $v): ?>
                                        <option value="<?= $k ?>" <?= _sel($cur_scope, $k) ?>><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">The phase of assignment where this rule applies.</small>
                            </div>
                        </div>

                        <!-- Phase (only for screening scopes) -->
                        <div class="form-group" id="phase_block">
                            <label class="control-label col-md-3">Screening phase</label>
                            <div class="col-md-6">
                                <select name="phase_id" class="form-control">
                                    <option value="">— Apply to all phases —</option>
                                    <?php foreach ($phases as $ph): ?>
                                        <option value="<?= $ph['screen_phase_id'] ?>" <?= _sel($cur_phase_id, $ph['screen_phase_id']) ?>>
                                            <?= htmlspecialchars($ph['phase_title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Leave empty to apply to all screening phases. Ignored for QA and Classification.</small>
                            </div>
                        </div>

                        <!-- Type -->
                        <div class="form-group">
                            <label class="control-label col-md-3">Rule type *</label>
                            <div class="col-md-6">
                                <select name="constraint_type" id="constraint_type" class="form-control" required>
                                    <?php foreach ($types as $k => $v): ?>
                                        <option value="<?= $k ?>" <?= _sel($cur_type, $k) ?>><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h4 style="margin-left:15px;">Rule parameters</h4>

                        <!-- Params: min_tag_per_paper -->
                        <div class="params_block" id="params_min_tag_per_paper" style="<?= $cur_type === 'min_tag_per_paper' ? '' : 'display:none' ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3">Required tag</label>
                                <div class="col-md-6">
                                    <select name="p_min_tag_id" class="form-control">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?= $t['tag_id'] ?>" <?= _sel(isset($cur_params['tag_id']) ? $cur_params['tag_id'] : '', $t['tag_id']) ?>>
                                                <?= htmlspecialchars($t['tag_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Minimum number per paper</label>
                                <div class="col-md-6">
                                    <input type="number" name="p_min_count" class="form-control"
                                           value="<?= isset($cur_params['min_count']) ? intval($cur_params['min_count']) : 1 ?>" min="1">
                                    <small class="text-muted">Each paper must have at least this many reviewers with the selected tag.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Params: max_tag_per_paper -->
                        <div class="params_block" id="params_max_tag_per_paper" style="<?= $cur_type === 'max_tag_per_paper' ? '' : 'display:none' ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3">Limited tag</label>
                                <div class="col-md-6">
                                    <select name="p_max_tag_id" class="form-control">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?= $t['tag_id'] ?>" <?= _sel(isset($cur_params['tag_id']) ? $cur_params['tag_id'] : '', $t['tag_id']) ?>>
                                                <?= htmlspecialchars($t['tag_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Maximum number per paper</label>
                                <div class="col-md-6">
                                    <input type="number" name="p_max_count" class="form-control"
                                           value="<?= isset($cur_params['max_count']) ? intval($cur_params['max_count']) : 1 ?>" min="0">
                                    <small class="text-muted">Each paper cannot have more than this many reviewers with the selected tag.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Params: tag_combination -->
                        <?php
                        $opt_a = (isset($cur_params['options'][0]) && is_array($cur_params['options'][0])) ? $cur_params['options'][0] : array();
                        $opt_b = (isset($cur_params['options'][1]) && is_array($cur_params['options'][1])) ? $cur_params['options'][1] : array();
                        ?>
                        <div class="params_block" id="params_tag_combination" style="<?= $cur_type === 'tag_combination' ? '' : 'display:none' ?>">
                            <p style="margin-left:15px;">
                                <small class="text-muted">Define alternative tag options (OR logic). For each paper, the engine picks the feasible option that forces the fewest mandatory placements.</small>
                            </p>
                            <div class="form-group">
                                <label class="control-label col-md-3">Option A — Tag</label>
                                <div class="col-md-4">
                                    <select name="p_comb_tag_a" class="form-control">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?= $t['tag_id'] ?>" <?= _sel(isset($opt_a['tag_id']) ? $opt_a['tag_id'] : '', $t['tag_id']) ?>>
                                                <?= htmlspecialchars($t['tag_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="p_comb_count_a" class="form-control" placeholder="Count"
                                           value="<?= isset($opt_a['count']) ? intval($opt_a['count']) : 1 ?>" min="1">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">OR Option B — Tag</label>
                                <div class="col-md-4">
                                    <select name="p_comb_tag_b" class="form-control">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?= $t['tag_id'] ?>" <?= _sel(isset($opt_b['tag_id']) ? $opt_b['tag_id'] : '', $t['tag_id']) ?>>
                                                <?= htmlspecialchars($t['tag_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="p_comb_count_b" class="form-control" placeholder="Count"
                                           value="<?= isset($opt_b['count']) ? intval($opt_b['count']) : 2 ?>" min="1">
                                </div>
                            </div>
                        </div>

                        <!-- Params: same_user_from_previous_phase -->
                        <div class="params_block" id="params_same_user_from_previous_phase" style="<?= $cur_type === 'same_user_from_previous_phase' ? '' : 'display:none' ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3">Previous scope</label>
                                <div class="col-md-6">
                                    <select name="p_same_scope" class="form-control">
                                        <?php foreach ($scopes as $k => $v): ?>
                                            <option value="<?= $k ?>" <?= _sel(isset($cur_params['previous_scope']) ? $cur_params['previous_scope'] : '', $k) ?>>
                                                <?= $v ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Previous screening phase</label>
                                <div class="col-md-6">
                                    <select name="p_same_phase_id" class="form-control">
                                        <option value="">— None —</option>
                                        <?php foreach ($phases as $ph): ?>
                                            <option value="<?= $ph['screen_phase_id'] ?>" <?= _sel(isset($cur_params['previous_phase_id']) ? $cur_params['previous_phase_id'] : '', $ph['screen_phase_id']) ?>>
                                                <?= htmlspecialchars($ph['phase_title']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Mode</label>
                                <div class="col-md-6">
                                    <select name="p_same_mode" class="form-control">
                                        <option value="strict" <?= _sel(isset($cur_params['mode']) ? $cur_params['mode'] : '', 'strict') ?>>Strict — block assignment if the previous reviewer is unavailable</option>
                                        <option value="preferred" <?= _sel(isset($cur_params['mode']) ? $cur_params['mode'] : '', 'preferred') ?>>Preferred — try to reuse, fall back to another reviewer if unavailable</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Params: force_different_user_from_previous_phase -->
                        <div class="params_block" id="params_force_different_user_from_previous_phase" style="<?= $cur_type === 'force_different_user_from_previous_phase' ? '' : 'display:none' ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3">Previous scope</label>
                                <div class="col-md-6">
                                    <select name="p_diff_scope" class="form-control">
                                        <?php foreach ($scopes as $k => $v): ?>
                                            <option value="<?= $k ?>" <?= _sel(isset($cur_params['previous_scope']) ? $cur_params['previous_scope'] : '', $k) ?>>
                                                <?= $v ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Previous screening phase</label>
                                <div class="col-md-6">
                                    <select name="p_diff_phase_id" class="form-control">
                                        <option value="">— None —</option>
                                        <?php foreach ($phases as $ph): ?>
                                            <option value="<?= $ph['screen_phase_id'] ?>" <?= _sel(isset($cur_params['previous_phase_id']) ? $cur_params['previous_phase_id'] : '', $ph['screen_phase_id']) ?>>
                                                <?= htmlspecialchars($ph['phase_title']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Priority -->
                        <div class="form-group">
                            <label class="control-label col-md-3">Priority</label>
                            <div class="col-md-6">
                                <input type="number" name="constraint_priority" class="form-control" value="<?= intval($cur_priority) ?>">
                                <small class="text-muted">Lower priority is applied first when several rules conflict. Default: 100.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-success"><?= $is_edit ? 'Update rule' : 'Save rule' ?></button>
                                <a href="<?= base_url('element/entity_list/list_assignment_constraint') ?>"
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
    document.getElementById('constraint_type').addEventListener('change', function() {
        document.querySelectorAll('.params_block').forEach(function(d) { d.style.display = 'none'; });
        var target = document.getElementById('params_' + this.value);
        if (target) target.style.display = 'block';
    });

    // Show/hide the phase field based on the selected scope
    document.getElementById('constraint_scope').addEventListener('change', function() {
        var isScreening = this.value === 'screening' || this.value === 'screening_validation';
        document.getElementById('phase_block').style.display = isScreening ? '' : 'none';
    });
</script>