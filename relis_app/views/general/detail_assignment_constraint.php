<?php
/* ReLiS - Issue #103 - Vue détail d'une contrainte d'assignation */

// Récupérer l'ID depuis l'URL — ReLiS passe $ref_id au controller mais pas à la vue.
// On le récupère depuis l'URI directement.
$CI =& get_instance();
$uri_segments = $CI->uri->segment_array();
// L'URL est : element/display_element/detail_assignment_constraint/{id}
// Le dernier segment est l'ID
$constraint_id = intval(end($uri_segments));

$db_project = $CI->load->database(project_db(), TRUE);
$constraint = $db_project->query(
        "SELECT * FROM assignment_constraint WHERE constraint_id = ? AND constraint_active = 1",
        array($constraint_id)
)->row_array();

// Si pas trouvé avec le filtre actif, on essaie sans (pour afficher les inactives aussi)
if (empty($constraint)) {
    $constraint = $db_project->query(
            "SELECT * FROM assignment_constraint WHERE constraint_id = ?",
            array($constraint_id)
    )->row_array();
}

$scope_labels = array(
        'screening'                 => 'Screening',
        'screening_validation'      => 'Screening validation',
        'qa'                        => 'Quality assessment',
        'qa_validation'             => 'QA validation',
        'classification'            => 'Classification',
        'classification_validation' => 'Classification validation',
);
?>

<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Assignment rule details</h2>
                    <?php if (isset($top_buttons)) {
                        echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    } ?>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">

                    <?php if (empty($constraint)): ?>
                        <div class="alert alert-warning">Rule not found (ID: <?= $constraint_id ?>).</div>

                    <?php else: ?>

                        <div class="alert alert-info" style="margin-bottom:20px;">
                            <strong><i class="fa fa-info-circle"></i> Active rules for this assignment:</strong>
                            <ul style="margin-top:8px; margin-bottom:0;">
                                <li><?= format_constraint_human($constraint) ?></li>
                            </ul>
                        </div>

                        <table class="table table-bordered table-striped" style="max-width:700px;">
                            <tr>
                                <th style="width:35%">Scope</th>
                                <td>
                                    <?= isset($scope_labels[$constraint['constraint_scope']])
                                            ? $scope_labels[$constraint['constraint_scope']]
                                            : htmlspecialchars($constraint['constraint_scope']) ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Screening phase</th>
                                <td>
                                    <?php
                                    if (empty($constraint['phase_id'])) {
                                        echo '<em style="color:#888">All phases (or not applicable)</em>';
                                    } else {
                                        $ph = $db_project->query(
                                                "SELECT phase_title FROM screen_phase WHERE screen_phase_id = ?",
                                                array($constraint['phase_id'])
                                        )->row_array();
                                        echo $ph ? htmlspecialchars($ph['phase_title']) : '(phase #' . $constraint['phase_id'] . ')';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Rule type</th>
                                <td><code><?= htmlspecialchars($constraint['constraint_type']) ?></code></td>
                            </tr>
                            <tr>
                                <th>Priority</th>
                                <td>
                                    <?= intval($constraint['constraint_priority']) ?>
                                    <small class="text-muted"> — lower is applied first</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php if (!empty($constraint['constraint_active'])): ?>
                                        <span class="label label-success">Active</span>
                                    <?php else: ?>
                                        <span class="label label-default">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Created on</th>
                                <td><?= htmlspecialchars($constraint['creation_time'] ?? '—') ?></td>
                            </tr>
                        </table>

                        <details style="margin-top:15px;">
                            <summary style="cursor:pointer; color:#888; font-size:13px;">
                                View raw JSON parameters
                            </summary>
                            <pre style="background:#f5f5f5; padding:10px; margin-top:8px; font-size:12px;"><?=
                                htmlspecialchars(json_encode(
                                        json_decode($constraint['constraint_params'], true),
                                        JSON_PRETTY_PRINT
                                ))
                                ?></pre>
                        </details>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>