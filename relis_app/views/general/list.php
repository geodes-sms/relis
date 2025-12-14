	<!-- page content -->
        <div class="right_col brice" role="main">
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
                    <h2><?php echo isset($page_title) ? $page_title:"" ; ?></h2>
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
                    	<?php
                    		$this->bm_lib->get_pagination($nav_pre_link,$nombre,$nav_page_position);
                    											?>											
                    		</div>
                    	<?php 
                    					}
                    
                    
                    $tmpl = array (
                    		'table_open'  => '<table class="table table-striped table-hover">',
                    		'table_close'  => '</table>'
                    );
                    $this->table->set_template($tmpl);
                    if(isset($nombre) AND $nombre>0)
                    {
                    	echo $this->table->generate($list);
                    }else{
                    	echo "<p>No records found !</p>";
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
    // Show loading state
    document.getElementById('generateQuestionsModalBody').innerHTML = 
        '<div class="text-center modal-body"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-3">Loading question generator...</p></div>';
    
    // Show modal
    $('#generateQuestionsModal').modal('show');
    
    // Load modal content
    fetch('<?php echo base_url(); ?>criteria_question_converter/get_modal_content?type=' + criteriaType)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('generateQuestionsModalBody').innerHTML = data.content;
            } else {
                document.getElementById('generateQuestionsModalBody').innerHTML = 
                    '<div class="modal-body"><div class="alert alert-danger">Error loading content: ' + data.message + '</div></div>';
            }
        })
        .catch(error => {
            document.getElementById('generateQuestionsModalBody').innerHTML = 
                '<div class="modal-body"><div class="alert alert-danger">Error loading content: ' + error.message + '</div></div>';
        });
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
</script>