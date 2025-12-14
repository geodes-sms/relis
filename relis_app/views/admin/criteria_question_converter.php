<?php
/* ReLiS - A Tool for conducting systematic literature reviews and mapping studies.
 * Copyright (C) 2018  Eugene Syriani
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 * 
 * --------------------------------------------------------------------------
 * 
 *  :Author: LLM Integration Team
 */

defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criteria to Question Converter - ReLiS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .criteria-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .criteria-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .question-display {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .loading {
            display: none;
        }
        .cost-estimate {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
        }
        .batch-controls {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .criteria-type-section {
            margin-bottom: 30px;
        }
        .section-header {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="mt-4 mb-4">
                    <i class="fas fa-robot"></i> Criteria to Question Converter
                </h1>
                
                <!-- LLM Configuration Section -->
                <div class="batch-controls">
                    <h3><i class="fas fa-cog"></i> LLM Configuration</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="llm_config" class="form-label">Select LLM Provider:</label>
                            <select class="form-select" id="llm_config" onchange="updateCostEstimate()">
                                <option value="">Choose LLM Provider...</option>
                                <?php foreach ($llm_configs as $config): ?>
                                    <option value="<?php echo $config['llm_config_id']; ?>" 
                                            data-cost="<?php echo $config['cost_per_1k_tokens']; ?>"
                                            data-provider="<?php echo $config['provider_name']; ?>"
                                            data-model="<?php echo $config['model_name']; ?>">
                                        <?php echo ucfirst($config['provider_name']); ?> - <?php echo $config['model_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="api_key" class="form-label">API Key:</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="api_key" placeholder="Enter your API key">
                                <button class="btn btn-outline-secondary" type="button" onclick="testApiKey()">
                                    <i class="fas fa-check"></i> Test
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="cost_estimate" class="cost-estimate" style="display: none;">
                        <strong>Estimated Cost:</strong> <span id="cost_display">$0.00</span>
                    </div>
                </div>

                <!-- Inclusion Criteria Section -->
                <div class="criteria-type-section">
                    <div class="section-header">
                        <h3><i class="fas fa-plus-circle"></i> Inclusion Criteria</h3>
                    </div>
                    
                    <!-- Batch Controls for Inclusion -->
                    <div class="batch-controls">
                        <div class="row">
                            <div class="col-md-8">
                                <button class="btn btn-primary" onclick="selectAllInclusion()">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <button class="btn btn-secondary" onclick="deselectAllInclusion()">
                                    <i class="fas fa-square"></i> Deselect All
                                </button>
                                <button class="btn btn-success" onclick="convertInclusionBatch()" id="convert_inclusion_btn">
                                    <i class="fas fa-magic"></i> Convert Selected to Questions
                                </button>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-info" id="inclusion_selected_count">0 selected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Inclusion Criteria Without Questions -->
                    <h5>Criteria Without Questions:</h5>
                    <div id="inclusion_criteria_list">
                        <?php foreach ($inclusion_criteria as $criteria): ?>
                            <div class="criteria-card">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input inclusion-checkbox" type="checkbox" 
                                               value="<?php echo $criteria['ref_id']; ?>" 
                                               id="incl_<?php echo $criteria['ref_id']; ?>"
                                               onchange="updateInclusionSelection()">
                                        <label class="form-check-label" for="incl_<?php echo $criteria['ref_id']; ?>">
                                            <strong><?php echo htmlspecialchars($criteria['ref_value']); ?></strong>
                                        </label>
                                    </div>
                                    <p class="text-muted mb-2"><?php echo htmlspecialchars($criteria['ref_desc']); ?></p>
                                    <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $criteria['criteria_category'])); ?></span>
                                    <button class="btn btn-sm btn-outline-primary float-end" 
                                            onclick="convertSingleInclusion(<?php echo $criteria['ref_id']; ?>)">
                                        <i class="fas fa-magic"></i> Convert
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Inclusion Criteria With Questions -->
                    <?php if (!empty($inclusion_with_questions)): ?>
                        <h5 class="mt-4">Criteria With Generated Questions:</h5>
                        <div id="inclusion_with_questions">
                            <?php foreach ($inclusion_with_questions as $criteria): ?>
                                <div class="criteria-card">
                                    <div class="card-body">
                                        <h6><?php echo htmlspecialchars($criteria['ref_value']); ?></h6>
                                        <p class="text-muted mb-2"><?php echo htmlspecialchars($criteria['ref_desc']); ?></p>
                                        <div class="question-display">
                                            <strong>Generated Question:</strong><br>
                                            <?php echo htmlspecialchars($criteria['inclusion_question']); ?>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                Generated: <?php echo date('M j, Y H:i', strtotime($criteria['question_generated_time'])); ?>
                                            </small>
                                            <button class="btn btn-sm btn-outline-danger float-end" 
                                                    onclick="clearQuestion(<?php echo $criteria['ref_id']; ?>, 'inclusion')">
                                                <i class="fas fa-trash"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Exclusion Criteria Section -->
                <div class="criteria-type-section">
                    <div class="section-header">
                        <h3><i class="fas fa-minus-circle"></i> Exclusion Criteria</h3>
                    </div>
                    
                    <!-- Batch Controls for Exclusion -->
                    <div class="batch-controls">
                        <div class="row">
                            <div class="col-md-8">
                                <button class="btn btn-primary" onclick="selectAllExclusion()">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <button class="btn btn-secondary" onclick="deselectAllExclusion()">
                                    <i class="fas fa-square"></i> Deselect All
                                </button>
                                <button class="btn btn-success" onclick="convertExclusionBatch()" id="convert_exclusion_btn">
                                    <i class="fas fa-magic"></i> Convert Selected to Questions
                                </button>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-info" id="exclusion_selected_count">0 selected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Exclusion Criteria Without Questions -->
                    <h5>Criteria Without Questions:</h5>
                    <div id="exclusion_criteria_list">
                        <?php foreach ($exclusion_criteria as $criteria): ?>
                            <div class="criteria-card">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input exclusion-checkbox" type="checkbox" 
                                               value="<?php echo $criteria['ref_id']; ?>" 
                                               id="excl_<?php echo $criteria['ref_id']; ?>"
                                               onchange="updateExclusionSelection()">
                                        <label class="form-check-label" for="excl_<?php echo $criteria['ref_id']; ?>">
                                            <strong><?php echo htmlspecialchars($criteria['ref_value']); ?></strong>
                                        </label>
                                    </div>
                                    <p class="text-muted mb-2"><?php echo htmlspecialchars($criteria['ref_desc']); ?></p>
                                    <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $criteria['criteria_category'])); ?></span>
                                    <button class="btn btn-sm btn-outline-primary float-end" 
                                            onclick="convertSingleExclusion(<?php echo $criteria['ref_id']; ?>)">
                                        <i class="fas fa-magic"></i> Convert
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Exclusion Criteria With Questions -->
                    <?php if (!empty($exclusion_with_questions)): ?>
                        <h5 class="mt-4">Criteria With Generated Questions:</h5>
                        <div id="exclusion_with_questions">
                            <?php foreach ($exclusion_with_questions as $criteria): ?>
                                <div class="criteria-card">
                                    <div class="card-body">
                                        <h6><?php echo htmlspecialchars($criteria['ref_value']); ?></h6>
                                        <p class="text-muted mb-2"><?php echo htmlspecialchars($criteria['ref_desc']); ?></p>
                                        <div class="question-display">
                                            <strong>Generated Question:</strong><br>
                                            <?php echo htmlspecialchars($criteria['exclusion_question']); ?>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                Generated: <?php echo date('M j, Y H:i', strtotime($criteria['question_generated_time'])); ?>
                                            </small>
                                            <button class="btn btn-sm btn-outline-danger float-end" 
                                                    onclick="clearQuestion(<?php echo $criteria['ref_id']; ?>, 'exclusion')">
                                                <i class="fas fa-trash"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Converting criteria to questions...</p>
                    <small class="text-muted">This may take a few moments</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let loadingModal;

        document.addEventListener('DOMContentLoaded', function() {
            loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            updateInclusionSelection();
            updateExclusionSelection();
        });

        function updateInclusionSelection() {
            const checkboxes = document.querySelectorAll('.inclusion-checkbox:checked');
            document.getElementById('inclusion_selected_count').textContent = checkboxes.length + ' selected';
            updateCostEstimate();
        }

        function updateExclusionSelection() {
            const checkboxes = document.querySelectorAll('.exclusion-checkbox:checked');
            document.getElementById('exclusion_selected_count').textContent = checkboxes.length + ' selected';
            updateCostEstimate();
        }

        function selectAllInclusion() {
            document.querySelectorAll('.inclusion-checkbox').forEach(cb => cb.checked = true);
            updateInclusionSelection();
        }

        function deselectAllInclusion() {
            document.querySelectorAll('.inclusion-checkbox').forEach(cb => cb.checked = false);
            updateInclusionSelection();
        }

        function selectAllExclusion() {
            document.querySelectorAll('.exclusion-checkbox').forEach(cb => cb.checked = true);
            updateExclusionSelection();
        }

        function deselectAllExclusion() {
            document.querySelectorAll('.exclusion-checkbox').forEach(cb => cb.checked = false);
            updateExclusionSelection();
        }

        function updateCostEstimate() {
            const llmSelect = document.getElementById('llm_config');
            const costEstimate = document.getElementById('cost_estimate');
            const costDisplay = document.getElementById('cost_display');
            
            if (!llmSelect.value) {
                costEstimate.style.display = 'none';
                return;
            }

            const selectedOption = llmSelect.options[llmSelect.selectedIndex];
            const costPer1k = parseFloat(selectedOption.dataset.cost);
            
            const inclusionCount = document.querySelectorAll('.inclusion-checkbox:checked').length;
            const exclusionCount = document.querySelectorAll('.exclusion-checkbox:checked').length;
            const totalCount = inclusionCount + exclusionCount;
            
            if (totalCount > 0) {
                const estimatedTokens = totalCount * 150; // 150 tokens per criteria
                const estimatedCost = (estimatedTokens / 1000) * costPer1k;
                costDisplay.textContent = '$' + estimatedCost.toFixed(6);
                costEstimate.style.display = 'block';
            } else {
                costEstimate.style.display = 'none';
            }
        }

        function testApiKey() {
            const llmConfigId = document.getElementById('llm_config').value;
            const apiKey = document.getElementById('api_key').value;
            
            if (!llmConfigId || !apiKey) {
                alert('Please select an LLM provider and enter an API key');
                return;
            }

            fetch('<?php echo base_url(); ?>criteria_question_converter/test_api_key', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `llm_config_id=${llmConfigId}&api_key=${encodeURIComponent(apiKey)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('API key is valid!');
                } else {
                    alert('API key test failed: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error testing API key: ' + error.message);
            });
        }

        function convertSingleInclusion(criteriaId) {
            const llmConfigId = document.getElementById('llm_config').value;
            const apiKey = document.getElementById('api_key').value;
            
            if (!llmConfigId || !apiKey) {
                alert('Please select an LLM provider and enter an API key');
                return;
            }

            loadingModal.show();

            fetch('<?php echo base_url(); ?>criteria_question_converter/convert_inclusion_criteria', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `criteria_id=${criteriaId}&llm_config_id=${llmConfigId}&api_key=${encodeURIComponent(apiKey)}`
            })
            .then(response => response.json())
            .then(data => {
                loadingModal.hide();
                if (data.success) {
                    alert('Question generated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                loadingModal.hide();
                alert('Error: ' + error.message);
            });
        }

        function convertSingleExclusion(criteriaId) {
            const llmConfigId = document.getElementById('llm_config').value;
            const apiKey = document.getElementById('api_key').value;
            
            if (!llmConfigId || !apiKey) {
                alert('Please select an LLM provider and enter an API key');
                return;
            }

            loadingModal.show();

            fetch('<?php echo base_url(); ?>criteria_question_converter/convert_exclusion_criteria', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `criteria_id=${criteriaId}&llm_config_id=${llmConfigId}&api_key=${encodeURIComponent(apiKey)}`
            })
            .then(response => response.json())
            .then(data => {
                loadingModal.hide();
                if (data.success) {
                    alert('Question generated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                loadingModal.hide();
                alert('Error: ' + error.message);
            });
        }

        function convertInclusionBatch() {
            const selectedIds = Array.from(document.querySelectorAll('.inclusion-checkbox:checked'))
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) {
                alert('Please select at least one inclusion criteria');
                return;
            }

            const llmConfigId = document.getElementById('llm_config').value;
            const apiKey = document.getElementById('api_key').value;
            
            if (!llmConfigId || !apiKey) {
                alert('Please select an LLM provider and enter an API key');
                return;
            }

            if (!confirm(`Convert ${selectedIds.length} inclusion criteria to questions?`)) {
                return;
            }

            loadingModal.show();

            fetch('<?php echo base_url(); ?>criteria_question_converter/convert_criteria_batch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `criteria_ids=${JSON.stringify(selectedIds)}&criteria_type=inclusion&llm_config_id=${llmConfigId}&api_key=${encodeURIComponent(apiKey)}`
            })
            .then(response => response.json())
            .then(data => {
                loadingModal.hide();
                if (data.success) {
                    alert(`Successfully converted ${data.success_count} criteria to questions!`);
                    location.reload();
                } else {
                    alert(`Batch conversion completed with ${data.error_count} errors. Check the results.`);
                    location.reload();
                }
            })
            .catch(error => {
                loadingModal.hide();
                alert('Error: ' + error.message);
            });
        }

        function convertExclusionBatch() {
            const selectedIds = Array.from(document.querySelectorAll('.exclusion-checkbox:checked'))
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) {
                alert('Please select at least one exclusion criteria');
                return;
            }

            const llmConfigId = document.getElementById('llm_config').value;
            const apiKey = document.getElementById('api_key').value;
            
            if (!llmConfigId || !apiKey) {
                alert('Please select an LLM provider and enter an API key');
                return;
            }

            if (!confirm(`Convert ${selectedIds.length} exclusion criteria to questions?`)) {
                return;
            }

            loadingModal.show();

            fetch('<?php echo base_url(); ?>criteria_question_converter/convert_criteria_batch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `criteria_ids=${JSON.stringify(selectedIds)}&criteria_type=exclusion&llm_config_id=${llmConfigId}&api_key=${encodeURIComponent(apiKey)}`
            })
            .then(response => response.json())
            .then(data => {
                loadingModal.hide();
                if (data.success) {
                    alert(`Successfully converted ${data.success_count} criteria to questions!`);
                    location.reload();
                } else {
                    alert(`Batch conversion completed with ${data.error_count} errors. Check the results.`);
                    location.reload();
                }
            })
            .catch(error => {
                loadingModal.hide();
                alert('Error: ' + error.message);
            });
        }

        function clearQuestion(criteriaId, criteriaType) {
            if (!confirm('Are you sure you want to clear this generated question?')) {
                return;
            }

            fetch('<?php echo base_url(); ?>criteria_question_converter/clear_question', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `criteria_id=${criteriaId}&criteria_type=${criteriaType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Question cleared successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
    </script>
</body>
</html>



