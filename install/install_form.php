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
function install_form($values=array(),$error=array()){?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ReLis | Setup </title>

    <!-- Bootstrap -->
    <link href="../cside/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../cside/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../cside/css/custom.css" rel="stylesheet">
    <link href="../cside/css/install.css" rel="stylesheet">
  </head>

  <body style="background:#F7F7F7;">
    <div class="">
     
      <div id="wrapper">
        <div id="login" class=" form">
          <section class="login_content">
            <form action="index.php" method="POST">
              <h1>ReLiS installer</h1><br/>
			  <?php
                    	if(isset($err_msg))
						{
							echo '<div class="alert alert-danger" style="text-align:center">';
							//echo validation_errors();
							
							 if (isset($err_msg))echo $err_msg;
							
							echo "</div>";
						}
						
						foreach ($error as $key => $err_value) {
							echo '<div class="alert alert-danger" style="text-align:center">';
								echo $err_value;	
							echo "</div>";
							
						}
			$db_host=(!empty($values['db_host']))?$values['db_host']:'localhost' ;
			$db_user=(!empty($values['db_user']))?$values['db_user']:'root' ;
		//	$db_pass=(!empty($values['db_pass']))?$values['db_pass']:'' ;
			$db_name=(!empty($values['db_name']))?$values['db_name']:'' ;
			
			$full_name=(!empty($values['full_name']))?$values['full_name']:'Admin' ;
			$user_mail=(!empty($values['user_mail']))?$values['user_mail']:'' ;
			$user_name=(!empty($values['user_name']))?$values['user_name']:'' ;
			
			$dsl_url=(!empty($values['dsl_url']))?$values['dsl_url']:'' ;
			$dsl_workspace=(!empty($values['dsl_workspace']))?$values['dsl_workspace']:'' ;
			
			$array_fields_db=array();
			$array_fields_admin=array();
			$array_fields_dsl=array();
			
			$field=array(
					'type'=>'text',
					'id'=>'db_host',
					'value'=>$db_host,
					'label'=>'Host Name',
					'extra_note'=>'This is usually "localhost"',
					'is_required'=>True
			);
			array_push($array_fields_db,$field);
			$field=array(
					'type'=>'text',
					'id'=>'db_user',
					'value'=>$db_user,
					'label'=>'Username',
					'extra_note'=>'Either something as "root" or a username given by the host',
					'is_required'=>True
			);
			array_push($array_fields_db,$field);
			$field=array(
					'type'=>'password',
					'id'=>'db_pass',
					'value'=>'',
					'label'=>'Password ',
					'extra_note'=>'The database password',
					'is_required'=>False
			);
			array_push($array_fields_db,$field);
			$field=array(
					'type'=>'text',
					'id'=>'db_name',
					'value'=>$db_name,
					'label'=>'Database Name ',
					'extra_note'=>'The name of your database: the database will be created',
					'is_required'=>True
			);
			
			array_push($array_fields_db,$field);
			
			
			$field=array(
					'type'=>'text',
					'id'=>'dsl_url',
					'value'=>$dsl_url,
					'label'=>'Editor location',
					'extra_note'=>'The url of the Editor',
					'is_required'=>False
			);
			
			array_push($array_fields_dsl,$field);
			
			
			$field=array(
					'type'=>'text',
					'id'=>'dsl_workspace',
					'value'=>$dsl_workspace,
					'label'=>'Editor work space',
					'extra_note'=>'The path to the Editor workspace',
					'is_required'=>False
			);
			
			array_push($array_fields_dsl,$field);
			
			
			
			
			$field=array(
					'type'=>'text',
					'id'=>'full_name',
					'value'=>$full_name,
					'label'=>'Administrator name',
					'extra_note'=>'Name of your Super User',
					'is_required'=>True
			);
			array_push($array_fields_admin,$field);
			$field=array(
					'type'=>'text',
					'id'=>'user_mail',
					'value'=>$user_mail,
					'label'=>'Administrator Email ',
					'extra_note'=>'',
					'is_required'=>False
			);
			array_push($array_fields_admin,$field);
			$field=array(
					'type'=>'text',
					'id'=>'user_name',
					'value'=>$user_name,
					'label'=>'Administrator Username',
					'extra_note'=>'Set the username for your Super User account.',
					'is_required'=>True
			);
			array_push($array_fields_admin,$field);
			
			$field=array(
					'type'=>'password',
					'id'=>'user_password',
					'value'=>'',
					'label'=>'Administrator Password',
					'extra_note'=>'Set the password for your Super User account and confirm it in the field below',
					'is_required'=>True
			);
			array_push($array_fields_admin,$field);
			
			$field=array(
					'type'=>'password',
					'id'=>'user_password_v',
					'value'=>'',
					'label'=>'Confirm Administrator Password',
					'extra_note'=>'',
					'is_required'=>True
			);
			array_push($array_fields_admin,$field);
			
			
                    	?>
            <div class="col-md-6 col-sm-6 col-xs-12 ">
				
			<div>	
			 <h2 class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">User information</h2>
			 </div> 
                    	<br/>
				<?php
				foreach ($array_fields_admin as $key => $value) {
					if($value['is_required']){
						$required_p='<span class="required">*</span>';
						$required_s='required="" ';
					}else{
						$required_p="";
						$required_s="";
					}
					
					if(!empty($value['extra_note'])){
						$extra_note='<p>'.$value['extra_note'].'</p>';
					}else{
						$extra_note="<p> . </p>";
					}
					?>
					
					<div class="form-group">
						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">
						 <?php echo $value['label'].' '. $required_p ?>
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
						<?php echo '<input type="'.$value['type'].'" name="'.$value['id'].'" value="'. $value['value'].'" id="'.$value['id'].'" class="form-control col-md-7 col-xs-12"  '.$required_s .'style="margin-bottom:1px">'.$extra_note ?>
											
						</div>		
					</div>
					
					<?php
				}
				
				?>
            </div>  
            
            <div class="col-md-6 col-sm-6 col-xs-12 ">
			<div class>
			 <h2 class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">Database configuration</h2>
			 </div> 
                    	<br/>
				<?php
				foreach ($array_fields_db as $key => $value) {
					if($value['is_required']){
						$required_p='<span class="required">*</span>';
						$required_s='required="" ';
					}else{
						$required_p="";
						$required_s="";
					}
					
					if(!empty($value['extra_note'])){
						$extra_note='<p>'.$value['extra_note'].'</p>';
					}else{
						$extra_note="<p> . </p>";
					}
					?>
					
					<div class="form-group">
						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">
						 <?php echo $value['label'].' '. $required_p ?>
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
						<?php echo '<input type="'.$value['type'].'" name="'.$value['id'].'" value="'. $value['value'].'" id="'.$value['id'].'" class="form-control col-md-7 col-xs-12"  '.$required_s .'style="margin-bottom:1px">'.$extra_note ?>
											
						</div>		
					</div>
					
					<?php
				}
				
				?>
				
				<div class>
			 <h2 class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">RelisEditor configuration</h2>
			 </div> 
                    	<br/>
				<?php
				foreach ($array_fields_dsl as $key => $value) {
					if($value['is_required']){
						$required_p='<span class="required">*</span>';
						$required_s='required="" ';
					}else{
						$required_p="";
						$required_s="";
					}
					
					if(!empty($value['extra_note'])){
						$extra_note='<p>'.$value['extra_note'].'</p>';
					}else{
						$extra_note="<p> . </p>";
					}
					?>
					
					<div class="form-group">
						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">
						 <?php echo $value['label'].' '. $required_p ?>
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
						<?php echo '<input type="'.$value['type'].'" name="'.$value['id'].'" value="'. $value['value'].'" id="'.$value['id'].'" class="form-control col-md-7 col-xs-12"  '.$required_s .'style="margin-bottom:1px">'.$extra_note ?>
											
						</div>		
					</div>
					
					<?php
				}
				
				?>
				</div>
				
			  
			<div class="form-group">
			<div class="col-md-12 col-sm-12 col-xs-12 " style="text-align: center">
			<button class="btn btn-info" type="submit" name='submit_form' style="padding-left: 20px; padding-right: 20px"> Install </button>
			</div>
			</div>
		
              <div class="clearfix"></div>
              <div class="separator">

                
                <div class="clearfix"></div>
                
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
<?php }?>