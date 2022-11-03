<?php
/*********************************************************************************
		CRON PARA AÑADIR LOS DATOS DE TCUENTO A LA BASE DE DATOS MYSQL
*********************************************************************************/

/************************************************
	MYSQL
************************************************/
define("MYSQL_SERVER","localhost");
define("MYSQL_USERNAME","dashboard");
define("MYSQL_PASSWORD","osaka2011");
define("MYSQL_BD","admin_dashboard");

/************************************************
	FTP
************************************************/
define("FTP_SERVER","82.223.242.192"); // integration.t-cuento.com
define("FTP_USERNAME","adm_superdrySorni");
define("FTP_PASSWORD","14802f!");
define("FTP_FOLDER","/exportsuperdrySorni");


function get_last_day(){
	$conexion = mysql_connect(MYSQL_SERVER,MYSQL_USERNAME,MYSQL_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	mysql_select_db(MYSQL_BD, $conexion);
	$result=mysql_query("SELECT * FROM `dash_tcuento` ORDER BY id DESC LIMIT 1 ") or die (mysql_error());
	$row = mysql_fetch_array($result);

	$hour    = str_replace(":","",$row["time"]);
	$lastday = $row["day"]."-".$hour;
	mysql_close();
	return $lastday;
}

function insertLine($day, $time, $people){
	$conexion = mysql_connect(MYSQL_SERVER,MYSQL_USERNAME,MYSQL_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	mysql_select_db(MYSQL_BD, $conexion);
	mysql_query("INSERT INTO dash_tcuento (day, time, people ) VALUES ('".$day."', '".$time."', '".$people."')") or die (mysql_error());
	mysql_close();
}

function parseCSVwriteDB($content, $hour_ori){
	$lines = explode(PHP_EOL, $content);
	$array = array();
	foreach ($lines as $line) {
	    $data 	= explode(";", $line);
	    if($data[1]!= ""){
		    $date 		= $data[1];
		    $hour 		= $data[2];
		    $hour_now 	= str_replace(":", "", $hour);
		    $people 	= $data[3];
		    $day 		= str_replace("-","",$data[1]);
		    //echo $day."<br/>";
		    //echo $hour_ori."<".$hour_now."<br/>";
		    if($hour_ori < $hour_now){
		    	//echo "insertar<br/>";
		    	//echo $day." - ".$hour." - ".$people."<br/>";
				insertLine($day, $hour, $people);
		    }
	    }
	}
}

function readFTPwriteDB(){
	$token 		= explode("-", get_last_day());
	$lastday 	= $token[0];
	$hour 	    = $token[1];
	if($lastday == ""){
		$lastday = 20180901;
		$hour    = 0;
	}
	echo $lastday."---".$hour."<br/>";
	
	$conn_id = @ftp_connect(FTP_SERVER);
	print_r(error_get_last());
	$login_result = ftp_login($conn_id, FTP_USERNAME, FTP_PASSWORD);
	echo "login_OK";
	if (!$login_result){
		echo "login_result: KO<br/>";
		echo $login_result;
	    exit();
	} 
	$contents = ftp_nlist($conn_id, FTP_FOLDER);
	foreach ($contents as $file){
	    // Full path to a remote file
	    $remote_path = "/$file";
	    echo $file."<br/>";
	    // Path to a temporary local copy of the remote file
	    $temp_path = tempnam(sys_get_temp_dir(), "ftp");
	    //echo $file;
	    $fileWithoutFolder 	= str_replace("/exportsuperdrySorni/", "", $file);
	    $fileWithoutPre 	= str_replace("ExportLiveDataInterval_","",$fileWithoutFolder);
	    $fileWithoutSub 	= str_replace(".csv","",$fileWithoutPre);

	    echo $fileWithoutSub.">". $lastday.'<br/>';
	    if($fileWithoutSub >= $lastday){
	    	echo "open<br/>";
	    	ftp_get($conn_id, $temp_path, $remote_path, FTP_BINARY);
	    	$contents = file_get_contents($temp_path);
	    	parseCSVwriteDB($contents, $hour);
	    	$hour = 0;
	    	unlink($temp_path);
	    }
	    // Discard the temporary copy
	    
	}
	
}
readFTPwriteDB();

?>