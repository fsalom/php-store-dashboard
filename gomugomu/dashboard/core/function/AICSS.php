<?php
$moduleName=$_GET['moduleName'];
header("Content-type: text/css");

$url="../../"; 
//Colocamos la url en la carpeta TEMPLATE


$url2="../../modules/";
//echo $url2.$moduleName."/admin/style/style.css";
//Colocamos la url en la carpeta MODULES
if(file_exists($url2.$moduleName."/admin/style/style.css")){
	echo file_get_contents($url2.$moduleName."/admin/style/style.css");		
}
?>