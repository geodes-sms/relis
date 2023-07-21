	<!-- page content -->
        <div class="right_col" role="main">
          <div class="">
          
          <div class="page-title">
              
             
            </div>
            
            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" >
                  <div class="x_title">
                    <h2><?php echo isset($page_title) ? $page_title :"" ; ?></h2>
                    <?php 
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    
                    }                    
                    ?>
                    
                    
                    
                    <div class="clearfix"></div>
                  </div>
                  
                  
                  
                  <div class="x_content" style="min-height:400px ">
                  
                 
                  <?php
                            	
                            	
                if(validation_errors() OR !empty($err_msg))
				{
				 echo '<div class="alert alert-danger alert-dismissible fade in" role="alert">
					<button class="close" aria-label="Close" data-dismiss="alert" type="button">
					<span aria-hidden="true">×</span>
					</button>
					<strong>Error!</strong>';
							echo validation_errors();
							 if (isset($err_msg))echo $err_msg;
							echo "</div>";
				}       
                    $attributes = array('class' => 'form-horizontal');             
                    echo form_open_multipart('install/save_new_project_editor',$attributes);
                    if(!empty($project_result)){
                    	echo "<div class='form-group '><label id='selected_config' for='selected_config' class='control-label col-md-3 col-sm-3 col-xs-12'> Select the configuration file generated</label>
                  		<div class='col-md-6 col-sm-6 col-xs-12'>
                  		<select id='selected_config' name='selected_config' class=' select2_group form-control  '>
                  		
                  		";
                    $path_separator=path_separator();// used to diferenciate windows and linux server
               	
                    foreach ($project_result as $project => $project_detail) {
						$dir=$project_detail['dir'];
						
						if(!empty($project_detail['generated'])){
							echo "<optgroup label='".$project."'>";
							foreach ($project_detail['generated'] as $key => $value) {
								echo  "<option value='".$dir.$path_separator."src-gen".$path_separator.$value."'>$value</option>";
							}
							echo "</optgroup>";
						}
					}
                    	echo "</select>
                   			</div>
							</div>";
                    } 
                    ?>

<body>
  <div class="ln_solid"></div>
  <div class="form-group">
    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
    <div class="checkbox">
        <label>
          <input type="checkbox" id="privacy-policy-checkbox" required>
          By creating this project, you agree to the open access (OA) terms of ReLiS. 
          OA allows any user to read the configuration and data produced by this project. 
          ReLiS follows an OA policy where all data is shared and open. Using the data includes, but is not limited to, viewing the results of a systematic review project, accessing the data produced in the project, and analyzing the data for research purposes, such as tertiary reviews. <br>
          <br> <a href="#privacy-policy-popup" style="text-decoration: underline;">View Privacy Policy</a> 
        </label>
    </div>
      <br>
      <button type="submit" class="btn btn-success" id="submit-button" disabled>Submit</button>
    </div>
  </div>
  <!-- Popup for displaying the privacy policy -->
  
  <div id="privacy-policy-popup" class="popup-overlay">
    <div class="popup-content">
      <div class="popup-close">
        <button class="btn btn-danger" id="close-popup" >Close</button>
      </div>
      <h2>Privacy Policy</h2>
      <p>
        Data collection disclosure<br>
         To offer our services, we collect basic information for identification and to ensure the segregation of your data from other users. The fundamental information we collect consists of your name, email address, password, username, and your involvement in systematic review projects. You provide us with all of this information voluntarily, and we have a strict policy of not sharing it with any third parties under any circumstances.
      </p>
      <p>
        Ensuring security of your data <br>
        To ensure the basic functionality of ReLiS and maintain the separation of your data from other users, we collect and associate the information you provide within the ReLiS platform with your user account. To enhance data security, we recommend selecting a unique username specifically for ReLiS, as it is less frequently shared compared to email addresses. Additionally, choosing a strong and distinct password is crucial to safeguard your account information. We encourage the use of lengthy passwords, as their length is more significant than complexity. For your protection, your password is stored in our database in a hashed form, making it virtually impossible to retrieve the original plain text even if our database is compromised. However, it is vital to use different passwords for different services, so we kindly request that you create a password exclusive to your ReLiS account. While we implement extensive measures to secure your data and uphold your rights, it is important to note that no online service can guarantee absolute security. We strive to take every possible precaution to protect your data, and we kindly request that you do the same.
      </p>
      <p>
        Data retention period<br>
        We do not have a limit on how long we retain your account information and/or data.
      </p>
      <p>
        Right to access<br>
        You have the right to request access to the information we have stored about you in our records. If you would like to obtain a copy of all the information associated with your account, please contact us via the provided email address.
      </p>
      <p>
        Right to delete your information<br>
        If you wish to remove your personal information from our records, please contact us at the email below.
        Note that you will no longer have access to ReLiS after this operation.
      </p>
      <p>
        Disclosure of data sharing with 3rd parties<br>
        We do not share any information with third parties.
      </p>
      <p>
        Contact<br>
        If you have any questions about this data sharing policy, please feel free to contact us at <a href="mailto:info@relis.iro.umontreal.ca" style="text-decoration: underline;">info@relis.iro.umontreal.ca</a>.
      </p>
    </div>
  </div>
  <!-- Include jQuery library -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      // Show privacy policy popup when the link is clicked
      $('a[href="#privacy-policy-popup"]').click(function(e) {
        e.preventDefault();
        $('#privacy-policy-popup').show();
      });

      // Close the privacy policy popup when clicked outside or on close button
      $('#privacy-policy-popup, #close-popup').on('click', function() {
        $('#privacy-policy-popup').hide();
      });

      // Disable/enable submit button based on checkbox state
      $('#privacy-policy-checkbox').change(function() {
        if ($(this).is(':checked')) {
          $('#submit-button').prop('disabled', false);
        } else {
          $('#submit-button').prop('disabled', true);
        }
      });
    });
  </script>
  </body>
                    <?php
                    echo form_close();
					?>         
                  </div> 
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->