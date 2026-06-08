<?php
/* ReLiS - Issue #103 - Formulaire dynamique de contrainte d'assignation */

// Récupération des tags depuis la DB projet
$db_project = $this->load->database(project_db(), TRUE);
$tags = $db_project->query("SELECT * FROM reviewer_tag WHERE tag_active = 1 ORDER BY tag_name ASC")->result_array();
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
                    <h2>Add an assignment rule</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <form class="form-horizontal" method="POST"
                          action="<?= base_url('element/save_assignment_constraint') ?>">

                        <!-- Scope -->
                        <div class="form-group">
                            <label class="control-label col-md-3">Scope *</label>
                            <div class="col-md-6">
                                <select name="constraint_scope" id="constraint_scope" class="form-control" required>
                                    <?php foreach ($scopes as $k => $v): ?>
                                        <option value="<?= $k ?>"><?= $v ?></option>
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
                                        <option value="<?= $ph['screen_phase_id'] ?>"><?= htmlspecialchars($ph['phase_title']) ?></option>
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
                                        <option value="<?= $k ?>"><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h4 style="margin-left:15px;">Rule parameters</h4>

                        <!-- Params: min_tag_per_paper -->
                        <div class="params_block" id="params_min_tag_per_paper">
                            <div class="form-group">
                                <label class="control-label col-md-3">Required tag</label>
                                <div class="col-md-6">
                                    <select name="p_min_tag_id" class="form-control">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?= $t['tag_id'] ?>"><?= htmlspecialchars($t['tag_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Minimum number per paper</label>
                                <div class="col-md-6">
                                    <input type="number" name="p_min_count" class="form-control" value="1" min="1">
                                    <small class="text-muted">Each paper must have at least this many reviewers with the selected tag.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Params: max_tag_per_paper -->
                        <div class="params_block" id="params_max_tag_per_paper" style="display:none">
                            <div class="form-group">
                                <label class="control-label col-md-3">Limited tag</label>
                                <div class="col-md-6">
                                    <select name="p_max_tag_id" class="form-control">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?= $t['tag_id'] ?>"><?= htmlspecialchars($t['tag_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Maximum number per paper</label>
                                <div class="col-md-6">
                                    <input type="number" name="p_max_count" class="form-control" value="1" min="0">
                                    <small class="text-muted">Each paper cannot have more than this many reviewers with the selected tag.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Params: tag_combination -->
                        <div class="params_block" id="params_tag_combination" style="display:none">
                            <p style="margin-left:15px;">
                                <small class="text-muted">Define alternative tag options (OR logic). For each paper, the engine picks the feasible option that forces the fewest mandatory placements.</small>                            </p>
                            <div class="form-group">
                                <label class="control-label col-md-3">Option A — Tag</label>
                                <div class="col-md-4">
                                    <select name="p_comb_tag_a" class="form-control">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?= $t['tag_id'] ?>"><?= htmlspecialchars($t['tag_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="p_comb_count_a" class="form-control" placeholder="Count" value="1" min="1">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">OR Option B — Tag</label>
                                <div class="col-md-4">
                                    <select name="p_comb_tag_b" class="form-control">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?= $t['tag_id'] ?>"><?= htmlspecialchars($t['tag_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="p_comb_count_b" class="form-control" placeholder="Count" value="2" min="1">
                                </div>
                            </div>
                        </div>

                        <!-- Params: same_user_from_previous_phase -->
                        <div class="params_block" id="params_same_user_from_previous_phase" style="display:none">
                            <div class="form-group">
                                <label class="control-label col-md-3">Previous scope</label>
                                <div class="col-md-6">
                                    <select name="p_same_scope" class="form-control">
                                        <?php foreach ($scopes as $k => $v): ?>
                                            <option value="<?= $k ?>"><?= $v ?></option>
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
                                            <option value="<?= $ph['screen_phase_id'] ?>"><?= htmlspecialchars($ph['phase_title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Mode</label>
                                <div class="col-md-6">
                                    <select name="p_same_mode" class="form-control">
                                        <option value="strict">Strict — block assignment if the previous reviewer is unavailable</option>
                                        <option value="preferred">Preferred — try to reuse, fall back to another reviewer if unavailable</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Params: force_different_user_from_previous_phase -->
                        <div class="params_block" id="params_force_different_user_from_previous_phase" style="display:none">
                            <div class="form-group">
                                <label class="control-label col-md-3">Previous scope</label>
                                <div class="col-md-6">
                                    <select name="p_diff_scope" class="form-control">
                                        <?php foreach ($scopes as $k => $v): ?>
                                            <option value="<?= $k ?>"><?= $v ?></option>
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
                                            <option value="<?= $ph['screen_phase_id'] ?>"><?= htmlspecialchars($ph['phase_title']) ?></option>
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
                                <input type="number" name="constraint_priority" class="form-control" value="100">
                                <small class="text-muted">Lower priority is applied first when several rules conflict. Default: 100.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-success">Save rule</button>
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

    // Affiche/cache le champ "phase" selon le scope
    document.getElementById('constraint_scope').addEventListener('change', function() {
        var isScreening = this.value === 'screening' || this.value === 'screening_validation';
        document.getElementById('phase_block').style.display = isScreening ? '' : 'none';
    });
</script>