<!-- page content -->
<style>
    .editing-mode {
        border: 2px solid #f0ad4e !important;
        background-color: #fef9e7 !important;
    }
    /* Make panels equal height and responsive */
    .equal-row {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
    }
    .equal-row > [class*='col-'] .panel {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .equal-row > [class*='col-'] .panel-body {
        flex: 1 1 auto;
    }

    /* Gentle spacing between stacked sections */
    .section-spaced {
        margin-top: 16px;
    }

    /* Subtle improvement to scrollable boxes */
    .scroll-box {
        max-height: 180px;
        overflow-y: auto;
        border: 1px solid #e5e5e5;
        background: #fafafa;
        padding: 10px;
        border-radius: 4px;
    }

    /* Brand accents */
    .brand-accent {
        color: #2a6df4;
    }
    .x_title h2, .page-title h3 { color: #2a6df4; }
    .panel-default > .panel-heading {
        background: linear-gradient(90deg, #f7faff 0%, #eef5ff 100%);
        border-bottom: 1px solid #e6eefc;
    }
    .panel-title i { margin-right: 6px; color: #2a6df4; }

    /* Category badge */
    .badge-category {
        display: inline-block;
        margin-top: 4px;
        background: #eef5ff;
        color: #285bcb;
        border: 1px solid #d7e4ff;
        border-radius: 10px;
        padding: 2px 8px;
        font-weight: 600;
        font-size: 11px;
        text-transform: capitalize;
    }

    /* Button spacing harmony */
    #prompt-display .btn { margin-right: 6px; }
</style>
</style>
<div class="right_col" role="main">
    <div class="">
        <?php top_msg(); ?>
        <div class="page-title">
            <div class="title_left">
                <h3><?php echo isset($page_title) ? $page_title : "Prompt Generation"; ?></h3>
            </div>
        </div>
        
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Generate Prompts from Database Values</h2>
                        <div class="clearfix"></div>
                    </div>
                    
                    <div class="x_content">
                        <div class="row equal-row">
                            <!-- Left Column: Configuration -->
                            <div class="col-md-6 col-sm-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Configuration</h4>
                                    </div>
                                    <div class="panel-body">
                                        <form id="prompt-generation-form">
                                            <!-- Template Selection (hidden, default to TrustSE) -->
                                            <input type="hidden" id="template-select" name="template_id" value="trustse">
                                            <small class="help-block" id="template-description" style="display:none;"></small>

                                            <!-- LLM Configuration (hidden) -->
                                            <input type="hidden" id="llm-config-select" name="llm_config_id" value="">

                                            <!-- Project Information -->
                                            <div class="form-group">
                                                <h5>Project Information</h5>
                                                <div class="well">
                                                    <strong>Title:</strong> <?php echo htmlspecialchars($project_info['project_title'] ?? 'Not set'); ?><br>
                                                    <strong>Description:</strong> <?php echo htmlspecialchars($project_info['project_description'] ?? 'Not set'); ?>
                                                </div>
                                            </div>

                                            <!-- Custom Variables (hidden) -->
                                            <div class="form-group" style="display:none;">
                                                <h5>Custom Variables</h5>
                                                <div id="custom-variables"></div>
                                                <button type="button" class="btn btn-sm btn-success" id="add-variable">Add Variable</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Research Questions and Key Concepts -->
                            <div class="col-md-6 col-sm-12">
                                <!-- Key Concepts Section -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-lightbulb-o"></i> Key Concepts & Definitions</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="scroll-box">
                                            <div class="form-group">
                                                <strong>SLR Topic</strong>
                                                <br><small><?php echo htmlspecialchars($slr_topic ?? 'Not set'); ?></small>
                                            </div>
                                            <?php if (!empty($key_concepts)): ?>
                                                <?php foreach ($key_concepts as $concept): ?>
                                                    <div class="form-group">
                                                        <strong><?php echo htmlspecialchars($concept['concept_name']); ?></strong>
                                                        <?php if (!empty($concept['concept_category'])): ?>
                                                            <span class="text-muted">(<?php echo ucfirst(str_replace('_', ' ', $concept['concept_category'])); ?>)</span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($concept['concept_definition'])): ?>
                                                            <br><small><?php echo htmlspecialchars($concept['concept_definition']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-muted">No key concepts defined. <a href="#" onclick="alert('Key concepts management not yet implemented')">Add key concepts</a></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Research Questions moved to its own row below for visibility -->
                        <div class="row section-spaced">
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-question-circle"></i> Research Questions</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="scroll-box">
                                            <?php if (!empty($research_questions)): ?>
                                                <?php foreach ($research_questions as $question): ?>
                                                    <div class="form-group" style="margin-bottom:8px;">
                                                        <strong><?php echo ucfirst($question['question_type']); ?> Question <?php echo $question['question_order']; ?>:</strong><br>
                                                        <span><?php echo htmlspecialchars($question['question_text']); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-muted">No research questions defined. <a href="#" onclick="alert('Research questions management not yet implemented')">Add research questions</a></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Second Row: Criteria Selection -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-check-square-o"></i> Selection Criteria</h4>
                                    </div>
                                    <div class="panel-body">
                                        <!-- Inclusion Criteria -->
                                        <div class="form-group">
                                            <h5 class="brand-accent">Inclusion Criteria</h5>
                                            <div class="scroll-box">
                                                <?php if (!empty($inclusion_criteria)): ?>
                                                    <?php foreach ($inclusion_criteria as $criterion): ?>
                                                        <div class="form-group" style="margin-bottom:8px;">
                                                            <strong><?php echo htmlspecialchars($criterion['ref_value']); ?></strong>
                                                            <?php if (!empty($criterion['ref_desc'])): ?>
                                                                <br><small><?php echo htmlspecialchars($criterion['ref_desc']); ?></small>
                                                            <?php endif; ?>
                                                            <?php if (!empty($criterion['criteria_category'])): ?>
                                                                <br><span class="badge-category"><?php echo str_replace('_', ' ', $criterion['criteria_category']); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-muted">No inclusion criteria found.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Exclusion Criteria -->
                                        <div class="form-group">
                                            <h5 class="brand-accent">Exclusion Criteria</h5>
                                            <div class="scroll-box">
                                                <?php if (!empty($exclusion_criteria)): ?>
                                                    <?php foreach ($exclusion_criteria as $criterion): ?>
                                                        <div class="form-group" style="margin-bottom:8px;">
                                                            <strong><?php echo htmlspecialchars($criterion['ref_value']); ?></strong>
                                                            <?php if (!empty($criterion['ref_desc'])): ?>
                                                                <br><small><?php echo htmlspecialchars($criterion['ref_desc']); ?></small>
                                                            <?php endif; ?>
                                                            <?php if (!empty($criterion['criteria_category'])): ?>
                                                                <br><small class="text-muted">Category: <?php echo ucfirst(str_replace('_', ' ', $criterion['criteria_category'])); ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-muted">No exclusion criteria found.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary" id="preview-prompt">
                                        <i class="fa fa-eye"></i> Preview Prompt
                                    </button>
                                    <button type="button" class="btn btn-success" id="generate-prompt">
                                        <i class="fa fa-magic"></i> Generate & Save Prompt
                                    </button>
                                    <button type="button" class="btn btn-info" id="load-saved-prompts">
                                        <i class="fa fa-history"></i> View Saved Prompts
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Generated Prompt Display -->
                        <div class="row" id="prompt-display" style="display: none;">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Generated Prompt</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label>Prompt Content:</label>
                                            <textarea class="form-control" id="generated-prompt-content" rows="20" readonly></textarea>
                                            <input type="hidden" id="current-prompt-id" value="">
                                        </div>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-warning" id="edit-prompt">
                                                <i class="fa fa-edit"></i> Edit Prompt
                                            </button>
                                            <button type="button" class="btn btn-success" id="save-edited-prompt" style="display: none;">
                                                <i class="fa fa-save"></i> Save Edited Prompt
                                            </button>
                                            <button type="button" class="btn btn-default" id="cancel-edit" style="display: none;">
                                                <i class="fa fa-times"></i> Cancel
                                            </button>
                                            <button type="button" class="btn btn-primary" id="copy-prompt">
                                                <i class="fa fa-copy"></i> Copy to Clipboard
                                            </button>
                                            <button type="button" class="btn btn-info" id="download-prompt">
                                                <i class="fa fa-download"></i> Download as Text
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Saved Prompts Display -->
                        <div class="row" id="saved-prompts-display" style="display: none;">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Saved Prompts</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div id="saved-prompts-list">
                                            <!-- Saved prompts will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Template description display
    $('#template-select').change(function() {
        var selectedOption = $(this).find('option:selected');
        var description = selectedOption.data('description');
        $('#template-description').text(description || '');
    });

    // Add custom variable
    $('#add-variable').click(function() {
        var newVariable = '<div class="input-group" style="margin-bottom: 5px;">' +
            '<input type="text" class="form-control" placeholder="Variable name" name="custom_var_name[]">' +
            '<input type="text" class="form-control" placeholder="Variable value" name="custom_var_value[]">' +
            '<span class="input-group-btn">' +
            '<button type="button" class="btn btn-danger remove-variable">×</button>' +
            '</span>' +
            '</div>';
        $('#custom-variables').append(newVariable);
    });

    // Remove custom variable
    $(document).on('click', '.remove-variable', function() {
        $(this).closest('.input-group').remove();
    });

    // Preview prompt
    $('#preview-prompt').click(function() {
        var formData = getFormData();
        if (!formData.template_id) {
            alert('Please select a template');
            return;
        }

        $.ajax({
            url: '<?php echo site_url("prompt_generation/preview_prompt"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#generated-prompt-content').val(response.prompt);
                    $('#prompt-display').show();
                    $('#saved-prompts-display').hide();
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('An error occurred while generating the prompt');
            }
        });
    });

    // Generate and save prompt
    $('#generate-prompt').click(function() {
        var formData = getFormData();
        if (!formData.template_id) {
            alert('Please select a template');
            return;
        }

        $.ajax({
            url: '<?php echo site_url("prompt_generation/generate_prompt"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#generated-prompt-content').val(response.prompt);
                    if (response.prompt_id) { $('#current-prompt-id').val(response.prompt_id); }
                    $('#prompt-display').show();
                    $('#saved-prompts-display').hide();
                    alert('Prompt generated and saved successfully!');
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('An error occurred while generating the prompt');
            }
        });
    });

    // Load saved prompts
    $('#load-saved-prompts').click(function() {
        $.ajax({
            url: '<?php echo site_url("prompt_generation/get_saved_prompts"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displaySavedPrompts(response.prompts);
                    $('#saved-prompts-display').show();
                    $('#prompt-display').hide();
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('An error occurred while loading saved prompts');
            }
        });
    });

    // Copy prompt to clipboard
    $('#copy-prompt').click(function() {
        var promptContent = $('#generated-prompt-content').val();
        navigator.clipboard.writeText(promptContent).then(function() {
            alert('Prompt copied to clipboard!');
        });
    });

    // Download prompt
    $('#download-prompt').click(function() {
        var promptContent = $('#generated-prompt-content').val();
        var blob = new Blob([promptContent], { type: 'text/plain' });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'generated_prompt_' + new Date().toISOString().slice(0, 10) + '.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    });

    // Edit prompt functionality
    var originalPromptContent = '';
    
    $('#edit-prompt').click(function() {
        originalPromptContent = $('#generated-prompt-content').val();
        $('#generated-prompt-content').prop('readonly', false);
        $('#generated-prompt-content').addClass('editing-mode');
        $('#edit-prompt').hide();
        $('#save-edited-prompt').show();
        $('#cancel-edit').show();
        $('#copy-prompt, #download-prompt').hide();
        
        // Add visual indicator (only for the prompt panel heading)
        $('#prompt-display .panel-title').html('Generated Prompt <span class="label label-warning">Editing Mode</span>');
    });

    $('#cancel-edit').click(function() {
        $('#generated-prompt-content').val(originalPromptContent);
        $('#generated-prompt-content').prop('readonly', true);
        $('#generated-prompt-content').removeClass('editing-mode');
        $('#edit-prompt').show();
        $('#save-edited-prompt').hide();
        $('#cancel-edit').hide();
        $('#copy-prompt, #download-prompt').show();
        
        // Remove visual indicator (only for the prompt panel heading)
        $('#prompt-display .panel-title').html('Generated Prompt');
    });

    $('#save-edited-prompt').click(function() {
        var editedPrompt = $('#generated-prompt-content').val();
        var promptId = $('#current-prompt-id').val();
        
        if (!editedPrompt.trim()) {
            alert('Please enter some content for the prompt');
            return;
        }

        // Save the edited prompt
        $.ajax({
            url: '<?php echo site_url("prompt_generation/save_edited_prompt"); ?>',
            type: 'POST',
            data: {
                edited_prompt: editedPrompt,
                prompt_id: promptId,
                template_id: $('#template-select').val(),
                llm_config_id: $('#llm-config-select').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Edited prompt saved successfully!');
                    $('#generated-prompt-content').prop('readonly', true);
                    $('#generated-prompt-content').removeClass('editing-mode');
                    $('#edit-prompt').show();
                    $('#save-edited-prompt').hide();
                    $('#cancel-edit').hide();
                    $('#copy-prompt, #download-prompt').show();
                    if (response.prompt_id) { $('#current-prompt-id').val(response.prompt_id); }
                    
                    // Remove visual indicator (only for the prompt panel heading)
                    $('#prompt-display .panel-title').html('Generated Prompt');
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('An error occurred while saving the edited prompt');
            }
        });
    });

    // Helper function to get form data
    function getFormData() {
        var formData = {
            template_id: $('#template-select').val(),
            llm_config_id: $('#llm-config-select').val(),
            include_criteria_ids: [],
            exclude_criteria_ids: [],
            custom_variables: {}
        };

        // Get selected inclusion criteria
        $('input[name="include_criteria_ids[]"]:checked').each(function() {
            formData.include_criteria_ids.push($(this).val());
        });

        // Get selected exclusion criteria
        $('input[name="exclude_criteria_ids[]"]:checked').each(function() {
            formData.exclude_criteria_ids.push($(this).val());
        });

        // Get custom variables
        $('input[name="custom_var_name[]"]').each(function(index) {
            var name = $(this).val();
            var value = $(this).siblings('input[name="custom_var_value[]"]').val();
            if (name && value) {
                formData.custom_variables[name] = value;
            }
        });

        return formData;
    }

    // Display saved prompts
    function displaySavedPrompts(prompts) {
        var html = '';
        if (prompts.length === 0) {
            html = '<p class="text-muted">No saved prompts found.</p>';
        } else {
            html = '<div class="table-responsive"><table class="table table-striped">';
            html += '<thead><tr><th>Template</th><th>Created By</th><th>Created At</th><th>Actions</th></tr></thead>';
            html += '<tbody>';
            
            prompts.forEach(function(prompt) {
                html += '<tr>';
                html += '<td>' + prompt.template_id + '</td>';
                html += '<td>' + (prompt.user_name || 'Unknown') + '</td>';
                html += '<td>' + prompt.created_at + '</td>';
                html += '<td>';
                html += '<button class="btn btn-sm btn-primary view-prompt" data-prompt-id="' + prompt.prompt_id + '">View</button> ';
                html += '<button class="btn btn-sm btn-danger delete-prompt" data-prompt-id="' + prompt.prompt_id + '">Delete</button>';
                html += '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
        }
        
        $('#saved-prompts-list').html(html);
    }

    // View saved prompt
    $(document).on('click', '.view-prompt', function() {
        var promptId = $(this).data('prompt-id');
        
        $.ajax({
            url: '<?php echo site_url("prompt_generation/get_prompt"); ?>',
            type: 'GET',
            data: { prompt_id: promptId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Display the prompt in the prompt display area
                    $('#generated-prompt-content').val(response.prompt.generated_prompt);
                    $('#current-prompt-id').val(response.prompt.prompt_id);
                    $('#generated-prompt-content').prop('readonly', true);
                    $('#generated-prompt-content').removeClass('editing-mode');
                    $('#edit-prompt').show();
                    $('#save-edited-prompt').hide();
                    $('#cancel-edit').hide();
                    $('#copy-prompt, #download-prompt').show();
                    
                    // Show the prompt display and hide saved prompts list
                    $('#prompt-display').show();
                    $('#saved-prompts-display').hide();
                    
                    // Update panel title
                    $('#prompt-display .panel-title').html('Saved Prompt - ' + response.prompt.template_id);
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('An error occurred while loading the prompt');
            }
        });
    });

    // Delete saved prompt
    $(document).on('click', '.delete-prompt', function() {
        if (confirm('Are you sure you want to delete this prompt?')) {
            var promptId = $(this).data('prompt-id');
            $.ajax({
                url: '<?php echo site_url("prompt_generation/delete_prompt"); ?>',
                type: 'POST',
                data: { prompt_id: promptId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Prompt deleted successfully');
                        $('#load-saved-prompts').click(); // Refresh the list
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the prompt');
                }
            });
        }
    });
});
</script>

