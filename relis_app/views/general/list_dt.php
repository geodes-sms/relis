	<!-- page content -->
        <div class="right_col" role="main">
          <div class="">
           <?php top_msg(); ?>
          <div class="page-title">
              
              <?php 
                   if(isset($search_view)){
                   		$this->load->view($search_view);
                   	}
                ?>
              
            </div>
            
            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" >
                  <div class="x_title">
                    <h2><?php echo isset($page_title) ? lng($page_title) :"" ; ?></h2>
                    <?php 
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    }                    
                    ?>
                    <div class="clearfix"></div>
                  </div>

                  <div class="x_content" style="min-height:400px ">
                    <?php 
                    if(isset($nav_pre_link)) {//Navigation links
                    
                    	$nav_page_position=isset($nav_page_position)?$nav_page_position:3;
                    		
                    	?>
                    		<div id="nav1" class="col-md-5 col-xs-12 dataTables_paginate ">
                    	<?php $this->bm_lib->get_pagination($nav_pre_link,$nombre,$nav_page_position); ?>
                    		</div>
                    	<?php 
                    					}
                    
                    
                    $tmpl = array (
                    		'table_open'  => '<table class="hover">',
                    		'table_close'  => '</table>'
                    );
                    $this->table->set_template($tmpl);
                    if(isset($nombre) AND $nombre>0)
                    {
                    	//echo $this->table->generate($list);
                    	
                    	?>
                  <div class="table-responsive">	
                 <table id="datatable-responsive" class="table table-striped table-bordered  nowrap"  style="width:100%; border-spacing:0px;
    border-collapse: separate;">
                      <thead>
                        <tr>
                        <?php 
                        foreach ($list_header as $key => $value) {
                        	echo "<th>".$value."</th>";
                        }
                      
                        ?>
                        </tr>   	
     	
                    </thead>
                    <tbody>
                      <?php 
                    	
                    	foreach ($list as $key_row => $row) {
                    		echo"<tr>";
                        foreach ($row as $k_cel => $v_cell) {
                    			echo "<td>".$v_cell."</td>";
                        }
                    		
                    		echo"</tr>";
                    }
                    	?>
                   </tbody> 
                    </table>	
                    </div>
                    	
                    	
                    	<?php 
                    }else{
                    	echo "<p>".lng('No records found')." !</p>";
                    							} 
					
					?>
                   
                  </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

<!-- Generate Questions Modal -->
<div class="modal fade" id="generateQuestionsModal" tabindex="-1" role="dialog" aria-labelledby="generateQuestionsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="generateQuestionsModalLabel">
          <i class="fa fa-magic"></i> Generate Questions
        </h4>
      </div>
      <div id="generateQuestionsModalBody">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script>
function openGenerateQuestionsModal(criteriaType) {
    try {
        console.log('Opening modal for criteria type:', criteriaType);
        
        // Check if modal elements exist
        var modalBody = document.getElementById('generateQuestionsModalBody');
        var modal = document.getElementById('generateQuestionsModal');
        
        if (!modalBody) {
            console.error('Modal body element not found');
            alert('Modal not properly loaded. Please refresh the page.');
            return;
        }
        
        if (!modal) {
            console.error('Modal element not found');
            alert('Modal not properly loaded. Please refresh the page.');
            return;
        }
        
        // Show loading state
        modalBody.innerHTML = 
            '<div class="modal-body text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div><p class="mt-3">Loading question generator...</p></div>';
        
        // Show modal
        $('#generateQuestionsModal').modal('show');
        
        // Load modal content
        fetch('<?php echo base_url(); ?>criteria_question_converter/get_modal_content?type=' + criteriaType)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modalBody.innerHTML = data.content;
                } else {
                    modalBody.innerHTML = 
                        '<div class="modal-body"><div class="alert alert-danger">Error loading content: ' + data.message + '</div></div>';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                modalBody.innerHTML = 
                    '<div class="modal-body"><div class="alert alert-danger">Error loading content: ' + error.message + '</div></div>';
            });
    } catch (error) {
        console.error('JavaScript error in openGenerateQuestionsModal:', error);
        alert('An error occurred: ' + error.message);
    }
}

function loadModalContent(criteriaType) {
    // Reload modal content
    fetch('<?php echo base_url(); ?>criteria_question_converter/get_modal_content?type=' + criteriaType)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('generateQuestionsModalBody').innerHTML = data.content;
            }
        })
        .catch(error => {
            console.error('Error reloading modal content:', error);
        });
}

// Modal-specific functions
function updateModalSelection() {
    const checkboxes = document.querySelectorAll('.modal-criteria-checkbox:checked');
    document.getElementById('modal_selected_count').textContent = checkboxes.length + ' selected';
    updateModalCostEstimate();
}

function selectAllModalCriteria() {
    document.querySelectorAll('.modal-criteria-checkbox').forEach(cb => cb.checked = true);
    updateModalSelection();
}

function deselectAllModalCriteria() {
    document.querySelectorAll('.modal-criteria-checkbox').forEach(cb => cb.checked = false);
    updateModalSelection();
}

function updateModalCostEstimate() {
    const llmSelect = document.getElementById('modal_llm_config');
    const costEstimate = document.getElementById('modal_cost_estimate');
    const costDisplay = document.getElementById('modal_cost_display');
    
    if (!llmSelect.value) {
        costEstimate.style.display = 'none';
        return;
    }

    const selectedOption = llmSelect.options[llmSelect.selectedIndex];
    const costPer1k = parseFloat(selectedOption.dataset.cost);
    
    const selectedCount = document.querySelectorAll('.modal-criteria-checkbox:checked').length;
    
    if (selectedCount > 0) {
        const estimatedTokens = selectedCount * 150; // 150 tokens per criteria
        const estimatedCost = (estimatedTokens / 1000) * costPer1k;
        costDisplay.textContent = '$' + estimatedCost.toFixed(6);
        costEstimate.style.display = 'block';
    } else {
        costEstimate.style.display = 'none';
    }
}

function testModalApiKey() {
    const llmConfigId = document.getElementById('modal_llm_config').value;
    const apiKey = document.getElementById('modal_api_key').value;
    
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

function convertSingleModalCriteria(criteriaId) {
    const llmConfigId = document.getElementById('modal_llm_config').value;
    const apiKey = document.getElementById('modal_api_key').value;
    
    if (!llmConfigId || !apiKey) {
        alert('Please select an LLM provider and enter an API key');
        return;
    }

    const criteriaType = document.querySelector('[data-criteria-type]') ? 
        document.querySelector('[data-criteria-type]').getAttribute('data-criteria-type') : 
        'inclusion'; // fallback
    
    const endpoint = criteriaType === 'inclusion' ? 'convert_inclusion_criteria' : 'convert_exclusion_criteria';
    
    // Show loading state
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Converting...';
    btn.disabled = true;

    fetch('<?php echo base_url(); ?>criteria_question_converter/' + endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `criteria_id=${criteriaId}&llm_config_id=${llmConfigId}&api_key=${encodeURIComponent(apiKey)}`
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.success) {
            alert('Question generated successfully!');
            // Reload modal content
            loadModalContent(criteriaType);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Error: ' + error.message);
    });
}

function convertModalCriteriaBatch() {
    const selectedIds = Array.from(document.querySelectorAll('.modal-criteria-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one criteria');
        return;
    }

    const llmConfigId = document.getElementById('modal_llm_config').value;
    const apiKey = document.getElementById('modal_api_key').value;
    
    if (!llmConfigId || !apiKey) {
        alert('Please select an LLM provider and enter an API key');
        return;
    }

    if (!confirm(`Convert ${selectedIds.length} criteria to questions?`)) {
        return;
    }

    const criteriaType = document.querySelector('[data-criteria-type]') ? 
        document.querySelector('[data-criteria-type]').getAttribute('data-criteria-type') : 
        'inclusion'; // fallback
    
    const btn = document.getElementById('modal_convert_btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Converting...';
    btn.disabled = true;

    fetch('<?php echo base_url(); ?>criteria_question_converter/convert_criteria_batch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `criteria_ids=${JSON.stringify(selectedIds)}&criteria_type=${criteriaType}&llm_config_id=${llmConfigId}&api_key=${encodeURIComponent(apiKey)}`
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.success) {
            alert(`Successfully converted ${data.success_count} criteria to questions!`);
            // Reload modal content
            loadModalContent(criteriaType);
        } else {
            alert(`Batch conversion completed with ${data.error_count} errors. Check the results.`);
            // Reload modal content
            loadModalContent(criteriaType);
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Error: ' + error.message);
    });
}

function clearModalQuestion(criteriaId, criteriaType) {
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
            // Reload modal content
            loadModalContent(criteriaType);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}
</script>