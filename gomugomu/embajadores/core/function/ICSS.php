<?php
/*include_once("../var/configuration.php");
header("Content-type: text/css");

$url="../../"; 
//Colocamos la url en la carpeta TEMPLATE
if(file_exists($url.$GLOBALS['_templateDir'].$GLOBALS['_templateName']."style/style.css")){
	echo file_get_contents($url.$GLOBALS['_templateDir'].$GLOBALS['_templateName']."style/style.css");		
}

$url2="../modules/";
//Colocamos la url en la carpeta MODULES
if(file_exists($url2.$GLOBALS['_templateDir'].$GLOBALS['_templateName']."style/style.css")){
	echo file_get_contents($url2.$module_name."style/style.css");		
}
*/
$moduleName=$_GET['moduleName'];
header("Content-type: text/css");

$url="../"; 
//Colocamos la url en la carpeta TEMPLATE


$url2="../../modules/";
//echo $url2.$moduleName."/admin/style/style.css";
//Colocamos la url en la carpeta MODULES
//echo $url2.$moduleName."/style/style.css";
if(file_exists($url2.$moduleName."/style/style.css")){
	echo file_get_contents($url2.$moduleName."/style/style.css");		
}?>