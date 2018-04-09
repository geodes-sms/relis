<!-- footer content -->
      <!--  <footer>
          <div class="pull-right">
            ReLiS - Revue Littéraire Systématique 
          </div>
          <div class="clearfix"></div>
        </footer>
         /footer content -->
      </div>
    </div>
	 <link href="<?php echo site_url();?>cside/vendors/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" media="screen">
	 <link href="<?php echo site_url();?>cside/vendors/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
    
    <!-- FastClick -->
    <script src="<?php echo site_url();?>cside/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="<?php echo site_url();?>cside/vendors/nprogress/nprogress.js"></script>
    <!-- Switchery -->
  	<script src="<?php echo site_url();?>cside/vendors/switchery/dist/switchery.min.js"></script>
    <!-- Select2 -->
    <script src="<?php echo site_url();?>cside/vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="<?php echo site_url();?>cside/js/custom.js"></script>
    
    <?php if (isset($has_graph))
			{
			
			?>
	
	<script src="<?php echo site_url(); ?>cside/js/highcharts.js"></script>
	<script src="<?php echo site_url(); ?>cside/js/exporting.js"></script>
	
	<?php }?>
	<!-- bootstrap-datepicker -->
     <script src="<?php echo site_url();?>cside/vendors/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
     <!-- bootstrap-datepicker -->
     
     
	<!-- bootstrap-colorpicker -->
     <script src="<?php echo site_url();?>cside/vendors/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
     <!-- bootstrap-colorpicker -->
     
     <!-- PNotify -->
    <script src="<?php echo site_url();?>cside/vendors/pnotify/dist/pnotify.js"></script>
    <script src="<?php echo site_url();?>cside/vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="<?php echo site_url();?>cside/vendors/pnotify/dist/pnotify.nonblock.js"></script>
     
     
    <!-- /PNotify -->
    <script>
      $(document).ready(function() {
    	  $('.datepicker').datepicker({
			  format: 'yyyy-mm-dd',
			  clearBtn: true,
			  autoclose: true,
			  orientation: "auto right",
				todayBtn: true,
				todayHighlight: true
	});

    	 
        	   $('#color_p').colorpicker();

        	 
    	  
      });
    </script>
    
    
     <!-- Select2 -->
    <script>
      $(document).ready(function() {
        $(".select2_single").select2({
          placeholder: "Select ...",
          allowClear: true
        });
        $(".select2_group").select2({});
        $(".select2_multiple").select2({
          
          placeholder: "Select multi ...",
          allowClear: true
        });
      });
    </script>
    <!-- /Select2 -->
    
    
    <!-- /bootstrap-daterangepicker -->
    <script language="javascript">

    function clear_function( parametter ){
		var obj = document.getElementById(parametter);
			obj.value=""
			//alert('maintenant il contient : "'+obj.value+'"')
		//alert(parametter);
	}
	
	function confirm_delete(){
		
		return result=confirm("Confirm to delete this record !");
	
		
	}
	
</script>
  </body>
</html>