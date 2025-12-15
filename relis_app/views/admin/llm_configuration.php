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

<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><i class="fa fa-robot"></i> Setup LLM Configuration</h2>
                    <p class="text-muted">Configure your LLM provider to start screening papers</p>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Provider Selection -->
                            <div class="form-group">
                                <label><strong>Choose Your LLM Provider</strong></label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="provider-card" data-provider="openai">
                                            <div class="text-center">
                                                <i class="fa fa-brain fa-2x text-primary"></i>
                                                <h5>OpenAI</h5>
                                                <small class="text-muted">GPT-3.5, GPT-4</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="provider-card" data-provider="anthropic">
                                            <div class="text-center">
                                                <i class="fa fa-shield fa-2x text-info"></i>
                                                <h5>Anthropic</h5>
                                                <small class="text-muted">Claude-3 Sonnet, Haiku</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="provider-card" data-provider="google">
                                            <div class="text-center">
                                                <i class="fa fa-google fa-2x text-danger"></i>
                                                <h5>Google</h5>
                                                <small class="text-muted">Gemini Pro</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="provider-card" data-provider="openrouter">
                                            <div class="text-center">
                                                <i class="fa fa-exchange fa-2x text-success"></i>
                                                <h5>OpenRouter</h5>
                                                <small class="text-muted">Multiple Models</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuration Form -->
                            <form id="llmSetupForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="model">Model</label>
                                            <select class="form-control" id="model" name="model" required>
                                                <option value="">Select a model...</option>
                                            </select>
                                            <small class="form-text text-muted">Choose the specific AI model for this provider. Different models have different capabilities and costs.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="maxTokens">Max Tokens</label>
                                            <input type="number" class="form-control" id="maxTokens" name="max_tokens" value="300" min="100" max="4000">
                                            <small class="form-text text-muted">Maximum number of tokens (words) the AI can use in its response. Recommended: 200-500 for screening.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="temperature">Temperature</label>
                                            <input type="number" class="form-control" id="temperature" name="temperature" value="0.2" min="0" max="1" step="0.1">
                                            <small class="form-text text-muted">Controls response consistency. 0.1-0.3 recommended for screening (consistent results).</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="costPer1k">Cost per 1K Tokens ($)</label>
                                            <input type="number" class="form-control" id="costPer1k" name="cost_per_1k_tokens" value="0.002" min="0" step="0.000001" readonly>
                                            <small class="form-text text-muted">Automatically calculated based on your selected model. This field cannot be edited.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="apiKey">API Key</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="apiKey" name="api_key" placeholder="Enter your API key" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleApiKey">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Your API key is stored in memory only and will not be saved to disk. Get your API key from your LLM provider's website.</small>
                                </div>

                                <!-- Security Notice -->
                                <div class="alert alert-warning">
                                    <i class="fa fa-shield"></i>
                                    <strong>Security Notice:</strong> Your API key is only stored in browser memory and will be cleared when you close the tab. We Never store your API KEY on the server.
                                </div>

                                <!-- Action Buttons -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-success btn-block" id="testApiKey">
                                            <i class="fa fa-check"></i> Test Connection
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-info btn-block" id="estimateCost">
                                            <i class="fa fa-calculator"></i> Estimate Cost
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fa fa-play"></i> Start Screening Setup
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-4">
                            <!-- Cost Estimate Display -->
                            <div id="costEstimate" class="x_panel" style="display: none;">
                                <div class="x_title">
                                    <h5><i class="fa fa-calculator"></i> Cost Estimate</h5>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div id="costDetails"></div>
                                </div>
                            </div>

                            <!-- Test Results -->
                            <div id="testResults"></div>



                            <!-- Provider Links -->
                            <div class="x_panel">
                                <div class="x_title">
                                    <h5><i class="fa fa-external-link"></i> Get API Keys</h5>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div class="list-group">
                                        <a href="https://platform.openai.com/api-keys" target="_blank" class="list-group-item list-group-item-action">
                                            <i class="fa fa-external-link"></i> OpenAI API Keys
                                        </a>
                                        <a href="https://console.anthropic.com/" target="_blank" class="list-group-item list-group-item-action">
                                            <i class="fa fa-external-link"></i> Anthropic API Keys
                                        </a>
                                        <a href="https://makersuite.google.com/app/apikey" target="_blank" class="list-group-item list-group-item-action">
                                            <i class="fa fa-external-link"></i> Google API Keys
                                        </a>
                                        <a href="https://openrouter.ai/keys" target="_blank" class="list-group-item list-group-item-action">
                                            <i class="fa fa-external-link"></i> OpenRouter API Keys
                                        </a>
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

<style>
.provider-card {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin: 10px 0;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.provider-card:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.provider-card.selected {
    border-color: #007bff;
    background-color: #e3f2fd;
    box-shadow: 0 4px 8px rgba(0,123,255,0.2);
}

.provider-card i {
    margin-bottom: 10px;
}

.provider-card h5 {
    margin: 5px 0;
    font-weight: 600;
}

.provider-card small {
    font-size: 11px;
}

.setup-steps {
    padding-left: 20px;
}

.setup-steps li {
    margin-bottom: 10px;
    line-height: 1.4;
}

/* Style for read-only cost field */
#costPer1k[readonly] {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}
</style>

<script>
$(document).ready(function() {
    let selectedProvider = 'openai';
    
    // Provider selection
    $('.provider-card').click(function() {
        $('.provider-card').removeClass('selected');
        $(this).addClass('selected');
        selectedProvider = $(this).data('provider');
        updateModels();
        updateDefaultCosts();
    });

    // Toggle API key visibility
    $('#toggleApiKey').click(function() {
        const input = $('#apiKey');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Update models based on provider
    function updateModels() {
        const models = {
            'openai': [
                {value: 'gpt-3.5-turbo', label: 'GPT-3.5 Turbo', cost: 0.002},
                {value: 'gpt-4', label: 'GPT-4', cost: 0.03},
                {value: 'gpt-4-turbo', label: 'GPT-4 Turbo', cost: 0.01}
            ],
            'anthropic': [
                {value: 'claude-3-sonnet', label: 'Claude-3 Sonnet', cost: 0.015},
                {value: 'claude-3-haiku', label: 'Claude-3 Haiku', cost: 0.00025}
            ],
            'google': [
                {value: 'gemini-pro', label: 'Gemini Pro', cost: 0.0005}
            ],
            'openrouter': [
                {value: 'openai/gpt-3.5-turbo', label: 'OpenAI GPT-3.5 Turbo', cost: 0.002},
                {value: 'openai/gpt-4', label: 'OpenAI GPT-4', cost: 0.03},
                {value: 'anthropic/claude-3-sonnet', label: 'Anthropic Claude-3 Sonnet', cost: 0.015},
                {value: 'anthropic/claude-3-haiku', label: 'Anthropic Claude-3 Haiku', cost: 0.00025},
                {value: 'google/gemini-pro', label: 'Google Gemini Pro', cost: 0.0005}
            ]
        };

        const modelSelect = $('#model');
        modelSelect.empty();
        modelSelect.append('<option value="">Select a model...</option>');
        
        models[selectedProvider].forEach(model => {
            modelSelect.append(`<option value="${model.value}" data-cost="${model.cost}">${model.label}</option>`);
        });
    }

    // Update default costs when model changes
    $('#model').change(function() {
        updateDefaultCosts();
    });

    function updateDefaultCosts() {
        const selectedOption = $('#model option:selected');
        const cost = selectedOption.data('cost');
        if (cost) {
            $('#costPer1k').val(cost);
        } else {
            // Set default cost if no model is selected
            $('#costPer1k').val('0.002');
        }
    }

    // Prevent manual editing of cost field
    $('#costPer1k').on('input', function() {
        // Revert to the correct value based on selected model
        updateDefaultCosts();
    });

    // Test API key
    $('#testApiKey').click(function() {
        const apiKey = $('#apiKey').val();
        const model = $('#model').val();
        
        if (!apiKey) {
            alert('Please enter an API key first');
            return;
        }
        
        if (!model) {
            alert('Please select a model first');
            return;
        }

        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');

        // Debug information
        console.log('Testing connection with:', {
            provider: selectedProvider,
            model: $('#model').val(),
            api_key_length: apiKey.length
        });
        
        $.ajax({
            url: '<?php echo base_url("admin/test_llm_config"); ?>',
            method: 'POST',
            data: {
                api_key: apiKey,
                provider_name: selectedProvider,
                model_name: $('#model').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#testResults').html(`
                        <div class="alert alert-success">
                            <i class="fa fa-check"></i> Connection successful! Your API key is working.
                        </div>
                    `);
                } else {
                    $('#testResults').html(`
                        <div class="alert alert-danger">
                            <i class="fa fa-times"></i> ${response.message}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error details:', xhr.responseText);
                $('#testResults').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-times"></i> Connection error: ${error}<br>
                        <small>Status: ${status}</small>
                    </div>
                `);
            },
            complete: function() {
                $('#testApiKey').prop('disabled', false).html('<i class="fa fa-check"></i> Test Connection');
            }
        });
    });

    // Estimate cost
    $('#estimateCost').click(function() {
        const maxTokens = $('#maxTokens').val();
        const selectedOption = $('#model option:selected');
        const costPer1k = selectedOption.data('cost') || 0.002;
        
        if (maxTokens) {
            // More realistic estimation based on typical screening scenarios
            const avgInputTokens = 500;  // Average paper title + abstract length
            const avgOutputTokens = Math.min(maxTokens, 200);  // Typical response length
            
            const inputCost = (avgInputTokens / 1000) * costPer1k;
            const outputCost = (avgOutputTokens / 1000) * costPer1k;
            const totalCostPerPaper = inputCost + outputCost;
            
            $('#costDetails').html(`
                <div class="row">
                    <div class="col-6"><strong>Input Tokens:</strong></div>
                    <div class="col-6">~${avgInputTokens} (paper content)</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Output Tokens:</strong></div>
                    <div class="col-6">~${avgOutputTokens} (AI response)</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Total per Paper:</strong></div>
                    <div class="col-6">$${totalCostPerPaper.toFixed(6)}</div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6"><strong>100 Papers:</strong></div>
                    <div class="col-6">$${(totalCostPerPaper * 100).toFixed(4)}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>500 Papers:</strong></div>
                    <div class="col-6">$${(totalCostPerPaper * 500).toFixed(4)}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>1000 Papers:</strong></div>
                    <div class="col-6">$${(totalCostPerPaper * 1000).toFixed(4)}</div>
                </div>
                <hr>
                <small class="text-muted">
                    <i class="fa fa-info-circle"></i> 
                    Estimates based on typical paper screening. Actual costs may vary based on paper length and response complexity.
                </small>
            `);
            $('#costEstimate').show();
        } else {
            alert('Please enter max tokens');
        }
    });

    // Form submission
    $('#llmSetupForm').submit(function(e) {
        e.preventDefault();
        
        // Ensure cost is calculated from selected model
        const selectedOption = $('#model option:selected');
        const calculatedCost = selectedOption.data('cost') || 0.002;
        
        const formData = {
            provider_name: selectedProvider,
            model_name: $('#model').val(),
            api_key: $('#apiKey').val(),
            max_tokens: $('#maxTokens').val(),
            temperature: $('#temperature').val(),
            cost_per_1k_tokens: calculatedCost
        };

        if (!formData.model_name || !formData.api_key) {
            alert('Please fill in all required fields');
            return;
        }

        // Store configuration in session for immediate use
        $.ajax({
            url: '<?php echo base_url("admin/save_llm_config"); ?>',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('LLM configuration saved! You can now proceed to set up your screening criteria.');
                    // Redirect to next step (screening setup)
                    window.location.href = '<?php echo base_url("element/edit_element/edit_config_llm_project/1"); ?>';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Connection error');
            }
        });
    });

    // Initialize
    updateModels();
    $('.provider-card[data-provider="openai"]').addClass('selected');
});
</script>
