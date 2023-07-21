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
 *  :Author: Brice Michel Bigendako
 */

//function is used to generate an HTML <i> element with a Font Awesome icon class.
function icon($icon,$action='display') {
	if($action=='diplay')
		echo '<i class="fa fa-'.$icon.'"></i>';
		else 
		return '<i class="fa fa-'.$icon.'"></i>';
}

// used to generate an HTML anchor tag (<a>) with a truncated label
function string_anchor($link,$text,$trim_size=0,$title=True){
	if(empty($trim_size)){
		$label=$text;
	}else{
		$label=mb_substr($text,0,$trim_size);
	}
	
	if(strlen($label) < strlen($text)){
		$label .= '...';
	}
	//$label .= '...';
	if($title)
			$attibute=array('title'=>$text);
	else
		$attibute=array();
	
		return anchor($link,"<u>".$label."</u>",$attibute);
	
}

//used to generate the HTML markup for a box header section
function box_header($title="",$content="",$w1=6,$w2=6,$w6=12,$button=""){
	echo  '<div class="col-md-'.$w1.' col-sm-'.$w2.' col-xs-'.$w1.'">
              <div class="x_panel tile  overflow_hidden">
                <div class="x_title">
                  <h2>'.$title.'</h2>'.$button.'
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">'.$content;
}

//used in conjunction with the box_header() function to wrap the content within a box section
function box_footer(){
	echo "</div>
              </div>
            </div>";
}

//function is used to generate a completion gauge with associated statistics and counts
function add_completion_gauge($data=array(),$div_id='foo_test',$box_size=6){
	$all_papers=37;
	$processed_papers=30;
	$pending_papers=7;
	//print_test($data);
	
	if(!empty($data['conflict_papers'])){//we have to reduce the size of each column
		$count_class='col-md-3 col-sm-3 col-xs-12';
	}else{
		$count_class='col-md-4 col-sm-4 col-xs-12';
	}
	
	box_header($data['title']);
	
	
	?>
	
	
	             
                <!-- top tiles -->
          <div class="row tile_count centre">
            <div class="<?php echo $count_class;?> tile_stats_count " style="color: black;">
              <span class="count_top"><i class="fa fa-list "></i> <?php echo $data['all_papers']['title']?></span>
            
               <?php
               if(!empty($data['all_papers']['url'])){
               		echo anchor($data['all_papers']['url'],' <div class="count " style="color: black;">'.nombre($data['all_papers']['value']).'</div>');
               }else{
               		echo '<div class="count " style="color: black;">'.nombre($data['all_papers']['value']).'</div>';
               }
               ?>
             
            </div>
            <div class="<?php echo $count_class;?> tile_stats_count green">
              <span class="count_top"><i class="fa fa-check-circle-o"></i> <?php echo  $data['done_papers']['title']?></span>
              
              <?php 
              if(!empty($data['done_papers']['url'])){
              		echo anchor($data['done_papers']['url'],' <div class="count green">'.nombre($data['done_papers']['value']).'</div>');
              }else{
              		echo ' <div class="count green">'.nombre($data['done_papers']['value']).'</div>';
              }
              ?>
             
             
              
            </div>
            <div class="<?php echo $count_class;?> tile_stats_count">
              <span class="count_top"><i class="fa fa-clock-o"></i> <?php echo  $data['pending_papers']['title']?></span>
              <?php 
              if(!empty($data['pending_papers']['url'])){
              	echo anchor($data['pending_papers']['url'],' <div class="count ">'.nombre($data['pending_papers']['value']).'</div>');
              }else{
              	echo ' <div class="count ">'.nombre($data['pending_papers']['value']).'</div>';
              }
              ?>
             
              
            </div>
            <?php if(!empty($data['conflict_papers'])){
            ?>
            <div class="<?php echo $count_class;?> tile_stats_count red">
              <span class="count_top"><i class="red fa fa-warning"></i> <?php echo $data['conflict_papers']['title']?></span>
              <?php 
              if(!empty($data['conflict_papers']['url'])){
            		echo anchor($data['conflict_papers']['url'],' <div class="count red">'.nombre($data['conflict_papers']['value']).'</div>');
              }else{
              		echo ' <div class="count red">'.nombre($data['conflict_papers']['value']).'</div>';
              }
              ?>
             
              
            </div>
            <?php 
            }
            ?>
            <div class="col-md-12 col-sm-12 col-xs-12">
           
			<div class="sidebar-widget">
                       <?php
			          //  if($data['gauge_done'] > 0){
			            ?>
                      <canvas  id="<?php echo $div_id;?>" class="" ></canvas>
                      <div class="goal-wrapper">
                        <span class="gauge-value pull-left"></span>
                        <span id="gauge-text" class="gauge-value pull-left"><?php echo $data['gauge_done']?></span>
                        <span id="goal-text" class="goal-value pull-right"><?php echo nombre($data['gauge_all'])?></span>
                      </div>
                      <?php //}?>
             </div>
             </div>
           
          </div>
        
          <!-- /top tiles -->
            
	
	<?php 
	box_footer();
	
	//TO DISPLAY NEEDDEL
	$gauge_done=!empty($data['gauge_done'])?$data['gauge_done']:0.000000000001;
	?>
	<!-- gauge.js -->
    <script>
      var opts = {
          lines: 12,
          angle: 0,
          lineWidth: 0.4,
          pointer: {
              length: 0.75,
              strokeWidth: 0.042,
              color: '#1D212A'
          },
          limitMax: 'false',
          colorStart: '#1ABC9C',
          colorStop: '#1ABC9C',
          strokeColor: '#F0F3F3',
          generateGradient: true
      };
      var target = document.getElementById('<?php echo $div_id ?>'),
          gauge = new Gauge(target).setOptions(opts);

      gauge.maxValue = <?php echo $data['gauge_all'] ?>;
      gauge.animationSpeed = 32;
      gauge.set(<?php echo $gauge_done ?>);
    //  gauge.setTextField(document.getElementById("gauge-text"));
    </script>
    <!-- /gauge.js -->
	
	<?php
	
}