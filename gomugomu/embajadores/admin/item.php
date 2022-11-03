<?php
include_once("../core/class/class.API.php");
include_once("../core/config.php");
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

function getusername($id){
	$by=mysql_query("SELECT * FROM `users` WHERE `id`='".$id."'");
		while($data=mysql_fetch_array($by)){
			$username=$data['username'];
		}
	return $username;
}

$username=getusername($_POST['id']);
$id_item=$_POST['item'];
$date=date(_TIMEFORMAT,time()); 
$name=$_POST['name'];
$sql="INSERT INTO `dis_item`(`name`, `description`, `created`, `id_created_user`) 
	  					VALUES ('".$_POST['name']."', '".$_POST['description']."',  '".time()."', '".$_POST['id']."')";
mysql_query($sql);
$id = mysql_insert_id();
echo json_encode(array("user"=>"<b>".$username."</b> | ".$date, "url"=>'?go=item&do=delete&id='.$id, "name"=>$name, "id"=>$id));  
?>