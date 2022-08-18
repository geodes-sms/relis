<!-- page content -->
	<?php 
	$fr_checked="";
	$en_checked="checked";
	$label="en";
	$title="Switch to French ";
	
	if($this->session->userdata('active_language') AND $this->session->userdata('active_language')=='fr' ){
		
		$fr_checked="checked";
		$en_checked="";
		$label="fr";
		$title="Switch to English ";
	}
	
	?>
      <!--?  <span class="radio"  onclick="change_language()">
               <span  class="change_lang button_lang">EN <input type="radio"  class="flat button_lang" <?php// echo $en_checked; ?> name="iCheckz" value="en"></span>
               <span  class="change_lang button_lang"><input type="radio" class="flat button_lang" <?php// echo $fr_checked; ?> name="iCheckz" value="fr"> FR &nbsp&nbsp | </span> 
        </span>-->
        <span  class="change_lang button_lang"><br/>
        <a title="<?php echo $title ?>"  href="#" onclick="change_language()">
       
        <?php echo $label?>
         </a> &nbsp
         </span>
        <!-- /page content -->
         <script>
                  
                	
                 function change_language( ){
                    	
                		//alert(lang);
                	         $.ajax({
                	     type: "POST",
                	 url: "<?php echo base_url();?>home/change_lang/",
                        	 
                	 data: $('form.form_content').serialize(),
                	         success: function(msg){
                	               location.reload(); 
                	               
                	        
                	         },
                	 error: function(){
                	 alert("loading failure");
                	 }
                	       });
                	 }
                	</script>
                  