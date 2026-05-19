<?php
/* ReLiS - Issue #103 - Formulaire dynamique de contrainte */

$tags = $this->Screening_dataAccess->get_all_reviewer_tags();
$scopes = array(
    'screening' => 'Screening',
    'screening_validation' => 'Screening validation',
    'qa' => 'Quality assessment',
    'qa_validation' => 'QA validation',
    'classification' => 'Classification',
    'classification_validation' => 'Classification validation',
);
$types = array(
    'min_tag_per_paper' => 'Minimum N reviewers with tag',
    'max_tag_per_paper' => 'Maximum N reviewers with tag',
    'tag_combination' => 'Tag combination (X tag A OR Y tag B)',
    'same_user_from_previous_phase' => 'Same user from previous phase',
    'force_different_user_from_previous_phase' => 'Force different user from previous phase',
);
?>

<form action="<?= base_url('element/save_assignment_constraint'); ?>" method="POST" style="max-width:700px;margin:20px;">

    <h3>Create / Edit Assignment Constraint</h3>

    <div style="margin-bottom:12px;">
        <label><strong>Scope :</strong>
            <select name="constraint_scope" id="constraint_scope" required>
                <?php foreach ($scopes as $k => $v): ?>
                    <option value="<?= $k ?>"><?= $v ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <div style="margin-bottom:12px;">
        <label><strong>Phase</strong> (screening only, leave empty otherwise) :
            <select name="phase_id">
                <option value="">— None —</option>
                <?php foreach ($this->db_current->query("SELECT * FROM screen_phase")->result_array() as $ph): ?>
                    <option value="<?= $ph['screen_phase_id'] ?>"><?= $ph['phase_title'] ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <div style="margin-bottom:12px;">
        <label><strong>Constraint type :</strong>
            <select name="constraint_type" id="constraint_type" required>
                <?php foreach ($types as $k => $v): ?>
                    <option value="<?= $k ?>"><?= $v ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <hr>
    <h4>Parameters</h4>

    <div class="params_block" id="params_min_tag_per_paper">
        <label>Tag : <select name="p_min_tag_id">
                <?php foreach ($tags as $t): ?>
                    <option value="<?= $t['tag_id'] ?>"><?= htmlspecialchars($t['tag_name']) ?></option>
                <?php endforeach; ?>
            </select></label>
        <label>Min count : <input type="number" name="p_min_count" value="1" min="1"></label>
    </div>

    <div class="params_block" id="params_max_tag_per_paper" style="display:none">
        <label>Tag : <select name="p_max_tag_id">
                <?php foreach ($tags as $t): ?>
                    <option value="<?= $t['tag_id'] ?>"><?= htmlspecialchars($t['tag_name']) ?></option>
                <?php endforeach; ?>
            </select></label>
        <label>Max count : <input type="number" name="p_max_count" value="1" min="0"></label>
    </div>

    <div class="params_block" id="params_tag_combination" style="display:none">
        <p>Define at least 2 options. The engine will choose the most balanced one.</p>
        <div>
            <label>Option A — Tag : <select name="p_comb_tag_a">
                    <?php foreach ($tags as $t): ?>
                        <option value="<?= $t['tag_id'] ?>"><?= htmlspecialchars($t['tag_name']) ?></option>
                    <?php endforeach; ?>
                </select></label>
            <label>Count : <input type="number" name="p_comb_count_a" value="1" min="1"></label>
        </div>
        <div>
            <label>Option B — Tag : <select name="p_comb_tag_b">
                    <?php foreach ($tags as $t): ?>
                        <option value="<?= $t['tag_id'] ?>"><?= htmlspecialchars($t['tag_name']) ?></option>
                    <?php endforeach; ?>
                </select></label>
            <label>Count : <input type="number" name="p_comb_count_b" value="2" min="1"></label>
        </div>
    </div>

    <div class="params_block" id="params_same_user_from_previous_phase" style="display:none">
        <label>Previous scope :
            <select name="p_same_scope">
                <?php foreach ($scopes as $k => $v): ?>
                    <option value="<?= $k ?>"><?= $v ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Previous phase (screening only) :
            <select name="p_same_phase_id">
                <option value="">— None —</option>
                <?php foreach ($this->db_current->query("SELECT * FROM screen_phase")->result_array() as $ph): ?>
                    <option value="<?= $ph['screen_phase_id'] ?>"><?= $ph['phase_title'] ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Mode :
            <select name="p_same_mode">
                <option value="strict">Strict (block if user unavailable)</option>
                <option value="preferred">Preferred (try, fallback if unavailable)</option>
            </select>
        </label>
    </div>

    <div class="params_block" id="params_force_different_user_from_previous_phase" style="display:none">
        <label>Previous scope :
            <select name="p_diff_scope">
                <?php foreach ($scopes as $k => $v): ?>
                    <option value="<?= $k ?>"><?= $v ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Previous phase (screening only) :
            <select name="p_diff_phase_id">
                <option value="">— None —</option>
                <?php foreach ($this->db_current->query("SELECT * FROM screen_phase")->result_array() as $ph): ?>
                    <option value="<?= $ph['screen_phase_id'] ?>"><?= $ph['phase_title'] ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <hr>
    <label>Priority (lower = applied first) :
        <input type="number" name="constraint_priority" value="100">
    </label>

    <br><br>
    <button type="submit" class="btn btn-primary">Save constraint</button>
</form>

<script>
    document.getElementById('constraint_type').addEventListener('change', function() {
        document.querySelectorAll('.params_block').forEach(function(d) {
            d.style.display = 'none';
        });
        var target = document.getElementById('params_' + this.value);
        if (target) target.style.display = 'block';
    });
</script>