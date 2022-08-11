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
 * --------------------------------------------------------------------------------
 * 
 *  :Author: Brice Michel Bigendako
 */
function install_result($success=array(),$error=array()){
?>
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
            
              <h1>ReLiS installer</h1><br/>
			  
			
                    
            <div class="row" >
				
			<div class=" col-md-8 col-sm-8 col-xs-8 col-md-offset-2 alert alert-success alert-dismissible fade in" role="alert">
			
			<h3 style="text-align:center">Installation success</h3>
			
			</div>
			
		
			
			</div>
			<br/>
		<br/>
			<h1><a href='../index.php'><button class="btn btn-info btn-lg" type="button">Start ReLiS</button></a></h1>
				
			  
			
		
              <div class="clearfix"></div>
              <div class="separator">

                
                <div class="clearfix"></div>
                
              </div>
            
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
<?php }?>