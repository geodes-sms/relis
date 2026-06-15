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
                        <h2><?php echo isset($page_title) ? $page_title : ""; ?></h2>
                        <?php if (isset($top_buttons)) {
                            echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                        } ?>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content" style="min-height:400px">
                        <div class="tab-pane active" id="home">

                            <p>
                                <b>Number of papers to assign : <?php echo $number_papers ?></b><br/>
                                <i>Number of papers already assigned : <?php echo $number_papers_assigned ?></i><br/>
                            </p>

                            <?php
                            // ─── ISSUE #103 — Display constraints with toggle ────────────────
                            $this->load->model('Screening_dataAccess');
                            $scope    = 'screening';
                            $phase_id = active_screening_phase();

                            if (assignment_rules_enabled()) {
                                $db_project = $this->load->database(project_db(), TRUE);
                                $all_constraints = $db_project->query(
                                        "SELECT * FROM assignment_constraint
                                         WHERE constraint_scope = ?
                                           AND constraint_active = 1
                                           AND (phase_id = ? OR phase_id IS NULL)
                                         ORDER BY constraint_priority ASC",
                                        array($scope, $phase_id)
                                )->result_array();
                            }
                            ?>

                            <?php if (!empty($all_constraints)): ?>
                                <div class="alert alert-info" style="margin-bottom:15px;">
                                    <strong><i class="fa fa-info-circle"></i> Assignment rules:</strong>

                                    <ul style="margin-top:8px; margin-bottom:0; list-style:none; padding-left:0;">
                                        <?php foreach ($all_constraints as $c): ?>
                                            <li style="margin:6px 0;">
                                                <label style="cursor:pointer; display:flex; align-items:center; gap:10px;">
                                                    <input type="checkbox"
                                                           class="constraint-toggle"
                                                           data-constraint-id="<?= $c['constraint_id'] ?>"
                                                            <?= $c['constraint_enabled'] ? 'checked' : '' ?> />
                                                    <span class="constraint-label" style="<?= !$c['constraint_enabled'] ? 'opacity:0.5;' : '' ?>">
              <?= format_constraint_human($c) ?>
            </span>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <script>
                                document.querySelectorAll('.constraint-toggle').forEach(function(cb) {
                                    cb.addEventListener('change', function() {
                                        var fd = new FormData();
                                        fd.append('constraint_id', this.dataset.constraintId);
                                        fd.append('active', this.checked ? '1' : '0');

                                        fetch('<?= base_url('element/toggle_assignment_constraint'); ?>', {
                                            method: 'POST',
                                            body: fd
                                        }).then(function(r) { return r.json(); }).then(function(data) {
                                            var label = cb.parentElement.querySelector('.constraint-label');
                                            label.style.opacity = cb.checked ? '1' : '0.5';
                                        });
                                    });
                                });
                            </script>

                            <p class="lead">Select reviewers</p>

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
                                if (isset($err_msg)) echo $err_msg;
                                echo "</div>";
                            }

                            echo form_hidden(array('number_of_users'    => count($users)));
                            echo form_hidden(array('screening_phase'    => $screening_phase));
                            echo form_hidden(array('papers_sources'     => $papers_sources));
                            echo form_hidden(array('paper_source_status'=> $paper_source_status));

                            // ─── ISSUE #103 — Boucle users avec badges de tags ───────────
                            $i = 1;
                            foreach ($users as $user_id => $user_name) {

                                // Construction des badges HTML pour ce reviewer
                                $user_tags = $this->Screening_dataAccess->get_user_tags($user_id);
                                $badges_html = '';
                                foreach ($user_tags as $tag) {
                                    $badges_html .= ' <span style="display:inline-block; margin-left:6px;
                    padding:2px 8px; border-radius:10px; font-size:11px; color:white;
                    background-color:' . htmlspecialchars($tag['tag_color']) . ';">'
                                            . htmlspecialchars($tag['tag_name']) . '</span>';
                                }

                                // Le label inclut nom + badges
                                echo checkbox_form_bm($user_name . $badges_html, 'user_' . $i, 'user_' . $user_id, $user_id);
                                $i++;
                            }
                            // ── fin bloc 2 ──────────────────────────────────────────────

                            echo input_form_bm(lng('Reviews per paper'), 'reviews_per_paper', 'reviews_per_paper', $reviews_per_paper);
                            echo "<hr/>";

                            $label = "Assign all papers";
                            $name  = 'assign_all_paper_checkbox';
                            $id    = 'assign_all_paper_checkbox';
                            echo '<div class="form-group">';
                            echo form_label($label, $name, array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'));
                            echo '<div class="col-md-6 col-sm-6 col-xs-12">';
                            echo '<input type="hidden" name="' . $name . '" value="off">';
                            echo '<input type="checkbox" id="' . $id . '" name="' . $name . '" class="js-switch" onchange="toggleNumberPapersField()" value="on" checked />';
                            echo '</div></div>';

                            echo '<div id="number_of_papers_field" style="display: none;">';
                            echo '<div class="form-group">';
                            echo form_label('The number of papers to assign', 'number_of_papers_to_assign', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12'));
                            echo '<div class="col-md-6 col-sm-6 col-xs-12">';
                            echo '<input type="number" id="number_of_papers_to_assign" name="number_of_papers_to_assign" min="0" max="' . $number_papers . '" class="form-control" value="0" />';
                            echo '</div></div>';
                            echo '</div>';
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
                                        'table_open'  => '<table class="table table-striped table-hover">',
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

<script>
    function toggleNumberPapersField() {
        var checkbox     = document.getElementById('assign_all_paper_checkbox');
        var numberField  = document.getElementById('number_of_papers_field');
        numberField.style.display = checkbox.checked ? 'none' : 'block';
    }
</script>