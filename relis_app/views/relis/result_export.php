	<!-- Select2 -->
    <script src="<?php echo site_url();?>cside/vendors/select2/dist/js/select2.full.min.js"></script>
	<!-- page content -->
        <div class="right_col" role="main">
           <?php top_msg();    ?> 
          <div class="">
          
          <div class="page-title">
              
             
            </div>
            
            <div class="clearfix"></div>

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" >
                  <div class="x_title">
                     <?php  //header_perspective('screen');?>
                    <h2><?php echo isset($page_title) ? $page_title :"" ; ?></h2>
                    <?php 
                    if(isset($top_buttons)){
                    	echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";
                    
                    }                    
                    ?>
                    
                    
                    
                    <div class="clearfix"></div>
                  </div>
                  
                  
                  
                  <div class="x_content" style="min-height:400px ">
                  
                  
                  
                  <div class="row">
                  
                  
             
            
           <?php 
           $paper_filename=FCPATH."cside/export_r/relis_paper_".project_db().".csv";
           $paper_bib_filename=FCPATH."cside/export_r/relis_paper_bibtex_".project_db().".bib";
           $paper_bib_filename_Included=FCPATH."cside/export_r/relis_paper_bibtex_Included_".project_db().".bib";
           $paper_bib_filename_Excluded=FCPATH."cside/export_r/relis_paper_bibtex_Excluded_".project_db().".bib";
           $class_filename=FCPATH."cside/export_r/relis_classification_".project_db().".csv";
           $screen_exluded_filename=FCPATH."cside/export_r/relis_paper_excluded_screen_".project_db().".csv";
           $class_exluded_filename=FCPATH."cside/export_r/relis_paper_excluded_class_".project_db().".csv";
           if(file_exists($paper_filename)){
           	$paper_size = (filesize($paper_filename)> 1000 ? round(filesize($paper_filename)/1000): round(filesize($paper_filename)/1000,1)).' Kb  Last update:';
           	 $paper_date = date("Y-m-d h:i:s", filemtime($paper_filename));
           
           $paper_dsc="<i class='fa fa-download'></i> Download CSV (".$paper_size.$paper_date.")";
           
           }else{
           	
           	$paper_dsc="";
           } 
           
           if(file_exists($paper_bib_filename)){
           	$paper_bib_size = (filesize($paper_bib_filename)> 1000 ? round(filesize($paper_bib_filename)/1000): round(filesize($paper_bib_filename)/1000,1)).' Kb  Last update:';
           	 $paper_bib_date = date("Y-m-d h:i:s", filemtime($paper_bib_filename));
           
           $paper_bib_dsc="<i class='fa fa-download'></i> Download BibTeX (".$paper_bib_size.$paper_bib_date.")";
           
           }else{
           	
           	$paper_bib_dsc="";
           } 
           
           if(file_exists($paper_bib_filename_Included)){
           	$paper_bib_size_Included = (filesize($paper_bib_filename_Included)> 1000 ? round(filesize($paper_bib_filename_Included)/1000): round(filesize($paper_bib_filename_Included)/1000,1)).' Kb  Last update:';
           	$paper_bib_date_Included = date("Y-m-d h:i:s", filemtime($paper_bib_filename_Included));
           	 
           	$paper_bib_dsc_Included="<i class='fa fa-download'></i> Download BibTeX (".$paper_bib_size_Included.$paper_bib_date_Included.")";
           	 
           }else{
           
           	$paper_bib_dsc_Included="";
           }
           
           
           if(file_exists($paper_bib_filename_Excluded)){
           	$paper_bib_size_Excluded = (filesize($paper_bib_filename_Excluded)> 1000 ? round(filesize($paper_bib_filename_Excluded)/1000): round(filesize($paper_bib_filename_Excluded)/1000,1)).' Kb  Last update:';
           	$paper_bib_date_Excluded = date("Y-m-d h:i:s", filemtime($paper_bib_filename_Excluded));
           	 
           	$paper_bib_dsc_Excluded="<i class='fa fa-download'></i> Download BibTeX (".$paper_bib_size_Excluded.$paper_bib_date_Excluded.")";
           	 
           }else{
           	 
           	$paper_bib_dsc_Excluded="";
           }
           
           
           if(file_exists($class_filename)){
           	$paper_size = (filesize($class_filename)>1000 ? round(filesize($class_filename)/1000): round(filesize($class_filename)/1000,1)).' Kb  Last update:';
           	$paper_date = date("Y-m-d h:i:s", filemtime($class_filename));
           	 
           	$class_dsc="<i class='fa fa-download'></i> Download CSV (".$paper_size.$paper_date.")";
           	 
           }else{
           
           	$class_dsc="";
           }
           
           if(file_exists($screen_exluded_filename)){
           	$paper_size = (filesize($screen_exluded_filename)>1000 ? round(filesize($screen_exluded_filename)/1000): round(filesize($screen_exluded_filename)/1000,1)).' Kb  Last update:';
           	$paper_date = date("Y-m-d h:i:s", filemtime($screen_exluded_filename));
           	 
           	$screen_excluded_dsc="<i class='fa fa-download'></i> Download CSV (".$paper_size.$paper_date.")";
           	 
           }else{
           	 
           	$screen_excluded_dsc="";
           }
           
           if(file_exists($class_exluded_filename)){
           	$paper_size = (filesize($class_exluded_filename)>1000 ? round(filesize($class_exluded_filename)/1000): round(filesize($class_exluded_filename)/1000,1)).' Kb  Last update:';
           	$paper_date = date("Y-m-d h:i:s", filemtime($class_exluded_filename));
           	 
           	$class_excluded_dsc="<i class='fa fa-download'></i> Download CSV (".$paper_size.$paper_date.")";
           	 
           }else{
           	 
           	$class_excluded_dsc="";
           }
			
           
           
           
           
           echo box_header("Result","",12,12,12);
           ?>
             <table class="table table-striped table-hover list_export_x" >
           
          
           <tr >
           <td>Classification</td><td><a href="<?php echo base_url();?>relis/manager/download/relis_classification_<?php echo project_db();?>.csv"><?php echo $class_dsc?></a></td><td><a href="<?php echo base_url();?>relis/manager/result_export_classification"><i class="fa fa-refresh"></i><?php echo lng('Update file')?></a></td><td></td>
           </tr>
           <tr >
           <td>Papers</td><td><a href="<?php echo base_url();?>relis/manager/download/relis_paper_<?php echo project_db();?>.csv"><?php echo  $paper_dsc?></a></td><td><a href="<?php echo base_url();?>relis/manager/result_export_papers"><i class="fa fa-refresh"></i><?php echo lng('Update file')?></a></td>
           </tr>
           
            
           <tr >
           <td>Papers (BibTeX)</td><td><a href="<?php echo base_url();?>relis/manager/download/relis_paper_bibtex_<?php echo project_db();?>.bib"><?php echo  $paper_bib_dsc?></a></td><td><a href="<?php echo base_url();?>relis/manager/result_export_papers_bib"><i class="fa fa-refresh"></i><?php echo lng('Update file')?></a></td>
           </tr>
           
           <tr >
           <td>Papers included (BibTeX)</td><td><a href="<?php echo base_url();?>relis/manager/download/relis_paper_bibtex_Included_<?php echo project_db();?>.bib"><?php echo  $paper_bib_dsc_Included?></a></td><td><a href="<?php echo base_url();?>relis/manager/result_export_included_papers_bib"><i class="fa fa-refresh"></i><?php echo lng('Update file')?></a></td>
           </tr>
           
           <tr >
           <td>Papers Excluded (BibTeX)</td><td><a href="<?php echo base_url();?>relis/manager/download/relis_paper_bibtex_Excluded_<?php echo project_db();?>.bib"><?php echo  $paper_bib_dsc_Excluded?></a></td><td><a href="<?php echo base_url();?>relis/manager/result_export_excluded_papers_bib"><i class="fa fa-refresh"></i><?php echo lng('Update file')?></a></td>
           </tr>
           
           <tr >
           <td>Papers Excluded Screening(CSV)</td><td><a href="<?php echo base_url();?>relis/manager/download/relis_paper_excluded_screen_<?php echo project_db();?>.csv"><?php echo  $screen_excluded_dsc?></a></td><td><a href="<?php echo base_url();?>relis/manager/result_export_excluded_screen"><i class="fa fa-refresh"></i><?php echo lng('Update file')?></a></td>
           </tr>
           
           <tr >
           <td>Papers Excluded Classification (CSV)</td><td><a href="<?php echo base_url();?>relis/manager/download/relis_paper_excluded_class_<?php echo project_db();?>.csv"><?php echo  $class_excluded_dsc?></a></td><td><a href="<?php echo base_url();?>relis/manager/result_export_excluded_class"><i class="fa fa-refresh"></i><?php echo lng('Update file')?></a></td>
           </tr>
           
          
           </table>
            <?php 
           echo  box_footer();
            
           
           
           
           
           ?>
                  
                  
                  
                  </div>
                
                   </div>
                  
                  
                  
                  
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->