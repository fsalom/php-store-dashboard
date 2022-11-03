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
$sql="INSERT INTO `dis_material`(`id`, `id_item`, `name`, `created`, `id_created_user`, `type`, `weight`,`size`,`price`) 
	  					VALUES ('', '".$id_item."', '".$_POST['name']."', '".time()."', '".$_POST['id']."', '".$_POST['type']."', '".$_POST['weight']."', '".$_POST['size']."', '".$_POST['price']."')";
	  					

mysql_query($sql);
$id = mysql_insert_id();

mysql_query("UPDATE `dis_item` SET `id_material` = '".$id."' WHERE `id`='".$id_item."'");

echo json_encode(array("user"=>"<b>".$username."</b> | ".$date, "url"=>'?go=material&do=delete&id='.$id.'&id_item='.$id_item, "name"=>$name, "type"=>$_POST['type'], "weight"=>$_POST['weight'], "size"=>$_POST['size'], "price"=>$_POST['price']));  
?>