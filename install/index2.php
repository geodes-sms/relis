<?php
include ('install_form.php');
include ('install_result.php');

if(isset($_POST['submit_form'])){
	$error=array();
	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";
	
	if(empty($_POST['db_host'])){
		array_push($error, "The 'Host Name' field is required");
	}
	
	if(empty($_POST['db_user'])){
		array_push($error, "The 'Username' field is required");
	}
	
	if(empty($_POST['db_name'])){
		array_push($error, "The 'Database Name' field is required");
	}
	
	if(empty($_POST['full_name'])){
		array_push($error, "The 'Administrator name' field is required");
	}
	
	if(empty($_POST['user_name'])){
		array_push($error, "The 'Administrator Username' field is required");
	}
	
	if(empty($_POST['user_password'])){
		array_push($error, "The 'Administrator Password' field is required");
	}else{
		if(trim($_POST['user_password']) != trim($_POST['user_password_v'])  ){
			array_push($error, "Wrong Password confirmation");
		}
	}
	
	
	
	if(!empty($error)){
		
		install_form($_POST,$error);
		
	}else{
		
		$db_host=trim($_POST['db_host']);
		$db_name=trim($_POST['db_name']);
		$db_user=trim($_POST['db_user']);
		$db_pass=trim($_POST['db_pass']);
		
		$result_array=array();
		
		$link = mysqli_connect($db_host, $db_user, $db_pass);
		if (!$link) {
			//die('Database hosts connection error : ' . mysql_error());
			install_form($_POST,array('Database hosts connection error : '. mysqli_connect_error()));
		}else{
			//echo "<h2>Relis installation</h2>";
			$sql = 'CREATE DATABASE IF NOT EXISTS '.$db_name;
			if (mysqli_query($link,$sql)) {
		
				
				//echo "<h2>database created</h2>";
				array_push($result_array, 'Database created');
				//select_database
		
				$db_selected = mysqli_select_db($link, $db_name);
				if (!$db_selected) {
					//die ('Database connection error  : ' . mysql_error());
					install_form($_POST,array('Database connection error : '. mysqli_connect_error()));
				}else{
		
					//initialisation des données
					//echo "<h2>database initialisation</h2>";
					$db_sql=file_get_contents("sql_init/initial_values.sql");
		
		
					$T_db_sql=explode ( '$$' , $db_sql);
		
		
					foreach ($T_db_sql as $key => $v_sql) {
						$sql=trim($v_sql);
						//echo $sql."<br/><br/><br/>";
						if( !empty($sql ) ){
							$result = mysqli_query($$link, $sql);
							if (!$result) {
								die('Invalid query : ' . mysqli_connect_error());
							}
		
						}
					}
					$full_name=trim($_POST['full_name']);
					$user_mail=trim($_POST['user_mail']);
					$user_name=trim($_POST['user_name']);
					$user_password=md5(trim($_POST['user_password']));
					
					
					// Add admin user
					$sql="INSERT INTO users (user_name,user_username,user_password,user_mail,user_usergroup) VALUES('".$full_name."','".$user_name."','".$user_password."','".$user_mail."',1)";
					$result = mysqli_query($$link, $sql);
					if (!$result) {
						die('Invalid query : ' . mysqli_connect_error());
					}
					
					// Add to CodeIgniter the database configuration
					add_database_config($db_host,$db_name,$db_user,$db_pass);
		
					update_ci_configuration();
					
					//create new index file
					copy( "temp/model_ci_index.php", "../index.php" );
		
		
					
					install_result();
				}
		
			} else {
				
				install_form($_POST,array('Database not created : '. mysqli_connect_error()));
			}
		
		}
		
		
	}
	
}else{
	install_form();

}


function add_database_config($host,$database_name,$username,$pass_word){
		
	$database_config = '$db'."['default'] = array(
		'dsn'	=> '',
		'hostname' => '".$host."',
		'username' => '".$username."',
		'password' => '".$pass_word."',
		'database' => '".$database_name."',
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => TRUE
);";
	// adding value in the CodeIgniter database configuration file
	$f_config = fopen("../relis_app/config/database.php", 'a+');


	fputs($f_config, "\n".$database_config. "\n");

	fclose($f_config);
	
	
	// adding value in the CodeIgniter configuration file
	$f_config1 = fopen("../relis_app/config/config.php", 'a+');
	
	$database_config1 = '$config[\'project_db_host\'] = "'.$host.'";
 	$config[\'project_db_user\'] = "'.$username.'";
 	$config[\'project_db_pass\'] = "'.$pass_word.'";
 ';
	fputs($f_config1, "\n".$database_config1. "\n");
	
	fclose($f_config1);


}

function update_ci_configuration(){

	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$actual_folder = "$_SERVER[DOCUMENT_ROOT]$_SERVER[REQUEST_URI]";
	
	
	$base_url=str_replace('/install/run_install.php','',$actual_link);
	$base_url=str_replace('/install/index.php','',$base_url);
	$base_url=str_replace('/install/','',$base_url);
	
	$database_config = '$config[\'base_url\'] = "'.$base_url.'";';
	
	$f_config = fopen("../relis_app/config/config.php", 'a+');
	
	
	fputs($f_config, "\n".$database_config. "\n");
	
	fclose($f_config);

}




