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

<style>
.criteria-modal-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    background: #fff;
}
.criteria-modal-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.question-display-modal {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 10px;
    margin-top: 10px;
    border-radius: 4px;
}
.cost-estimate-modal {
    background-color: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 4px;
    padding: 10px;
    margin-top: 10px;
}
.batch-controls-modal {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}
.section-header-modal {
    background-color: #007bff;
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    margin-bottom: 15px;
    font-size: 14px;
}
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}
</style>

<div class="modal-body" data-criteria-type="<?php echo $criteria_type; ?>">
    <!-- LLM Configuration Section -->
    <div class="batch-controls-modal">
        <h5><i class="fa fa-cog"></i> LLM Configuration</h5>
        <div class="row">
            <div class="col-md-6">
                <label for="modal_llm_config">Select LLM Provider:</label>
                <select class="form-control" id="modal_llm_config" onchange="updateModalCostEstimate()">
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
                <label for="modal_api_key">API Key:</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="modal_api_key" placeholder="Enter your API key">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" onclick="testModalApiKey()">
                            <i class="fa fa-check"></i> Test
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div id="modal_cost_estimate" class="cost-estimate-modal" style="display: none;">
            <strong>Estimated Cost:</strong> <span id="modal_cost_display">$0.00</span>
        </div>
    </div>

    <!-- Criteria Section -->
    <div class="section-header-modal">
        <h6><i class="fa fa-<?php echo $criteria_type === 'inclusion' ? 'plus-circle' : 'minus-circle'; ?>"></i> 
            <?php echo ucfirst($criteria_type); ?> Criteria
        </h6>
    </div>
    
    <!-- Batch Controls -->
    <div class="batch-controls-modal">
        <div class="row">
            <div class="col-md-8">
                <button class="btn btn-primary btn-sm" onclick="selectAllModalCriteria()">
                    <i class="fa fa-check-square"></i> Select All
                </button>
                <button class="btn btn-default btn-sm" onclick="deselectAllModalCriteria()">
                    <i class="fa fa-square"></i> Deselect All
                </button>
                <button class="btn btn-success btn-sm" onclick="convertModalCriteriaBatch()" id="modal_convert_btn">
                    <i class="fa fa-magic"></i> Convert Selected to Questions
                </button>
            </div>
            <div class="col-md-4 text-right">
                <span class="badge badge-info" id="modal_selected_count">0 selected</span>
            </div>
        </div>
    </div>

    <!-- Criteria Without Questions -->
    <h6>Criteria Without Questions:</h6>
    <div id="modal_criteria_list">
        <?php foreach ($criteria as $item): ?>
            <div class="criteria-modal-card">
                <div class="card-body p-3">
                    <div class="form-check">
                        <input class="form-check-input modal-criteria-checkbox" type="checkbox" 
                               value="<?php echo $item['ref_id']; ?>" 
                               id="modal_<?php echo $item['ref_id']; ?>"
                               onchange="updateModalSelection()">
                        <label class="form-check-label" for="modal_<?php echo $item['ref_id']; ?>">
                            <strong><?php echo htmlspecialchars($item['ref_value']); ?></strong>
                        </label>
                    </div>
                    <p class="text-muted mb-2 small"><?php echo htmlspecialchars($item['ref_desc']); ?></p>
                    <span class="badge badge-default"><?php echo ucfirst(str_replace('_', ' ', $item['criteria_category'])); ?></span>
                    <button class="btn btn-sm btn-primary pull-right" 
                            onclick="convertSingleModalCriteria(<?php echo $item['ref_id']; ?>)">
                        <i class="fa fa-magic"></i> Convert
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Criteria With Questions -->
    <?php if (!empty($criteria_with_questions)): ?>
        <h6 class="mt-4">Criteria With Generated Questions:</h6>
        <div id="modal_criteria_with_questions">
            <?php foreach ($criteria_with_questions as $item): ?>
                <div class="criteria-modal-card">
                    <div class="card-body p-3">
                        <h6><?php echo htmlspecialchars($item['ref_value']); ?></h6>
                        <p class="text-muted mb-2 small"><?php echo htmlspecialchars($item['ref_desc']); ?></p>
                        <div class="question-display-modal">
                            <strong>Generated Question:</strong><br>
                            <?php echo htmlspecialchars($item[($criteria_type === 'inclusion') ? 'inclusion_question' : 'exclusion_question']); ?>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                Generated: <?php echo date('M j, Y H:i', strtotime($item['question_generated_time'])); ?>
                            </small>
                            <button class="btn btn-sm btn-danger pull-right" 
                                    onclick="clearModalQuestion(<?php echo $item['ref_id']; ?>, '<?php echo $criteria_type; ?>')">
                                <i class="fa fa-trash"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Initialize modal selection count when content is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (typeof updateModalSelection === 'function') {
        updateModalSelection();
    }
});
</script>
