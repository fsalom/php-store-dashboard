<?php
include_once("../core/class/class.API.php");
define("_SERVER","db396502112.db.1and1.com");
define("_USERNAME","dbo396502112");
define("_PASSWORD","manologoaprint");
define("_BD","db396502112");
	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion){
		$API = new API();
		die($API->printerror(_MYSQLCONNECT));
	}
	mysql_select_db(_BD, $conexion);
    
    $username=$_REQUEST['username'];

	$query=mysql_query("SELECT * FROM `users` WHERE username='".$username."' ")or die(mysql_error());
	$num=mysql_num_rows($query);
	if($num>0){
		$valido= true;
	}else{
		$valido= false;
	}    
    
        $resp = array();
        $username = trim($username);
        if (!$username) {
            $resp = array('ok' => false, 'msg' => '<b style="color:#990000">Please write an USERNAME</b>');
        } else if (!preg_match('/^[a-zA-Z0-9ç\.\-_!]+$/', $username)) {
            $resp = array('ok' => false, "msg" => '<b style="color:#990000"> USERNAME not valid a-z0-9(.-_!)</b>');
        } else if ($valido) {
            $resp = array("ok" => false, "msg" => '<b style="color:#990000">USERNAME not available</b>');
        } else {
            $resp = array("ok" => true, "msg" => '<b style="color:#009900">USERNAME is available</b>');
        }

	if (@$_REQUEST['do'] == 'check' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        // means it was requested via Ajax
        echo json_encode($resp);
        exit; // only print out the json version of the response
    }  

  
          
?>