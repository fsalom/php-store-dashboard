<?php
include_once("../core/class/class.API.php");
include_once("../core/config.php");
define("_SERVER","205.178.146.110");
define("_USERNAME","app_socal");
define("_PASSWORD","Clippers6");
define("_BD","app_management");

$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
if (!$conexion){
	$API = new API();
	die($API->printerror(_MYSQLCONNECT));
}
mysql_select_db(_BD, $conexion);

function getusername($id){
	$by=mysql_query("SELECT * FROM `users` WHERE `id`='".$id."'");
		while($data=mysql_fetch_array($by)){
			$username=$data['username'];
		}
	return $username;
}

$username=getusername($_POST['id']);
$date=date(_TIMEFORMAT,time()); 
$name=$_POST['name'];
$sql="INSERT INTO `app_position`(`name`, `created`, `id_user_created`) VALUES ('".$_POST['name']."', '".time()."', '".$_POST['id']."')";
mysql_query($sql);
$id = mysql_insert_id();
echo json_encode(array("user"=>"<b>".$username."</b> | ".$date, "url"=>'?go=zone&do=delete&id='.$id, "name"=>$name));  
?>