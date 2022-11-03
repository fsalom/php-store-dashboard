<?php
include_once("core/class/class.RENDER.php");
include_once("core/class/class.FORMAT.php");
session_start();

function getmicrotime(){
$micro = microtime();
$micro = explode(" ",$micro);
$micro = $micro[1] + $micro[0];
return ($micro);
}
$inicio=getmicrotime();

include_once("core/config.php");
include_once("core/lang/"._LANGUAGE.".php");
//activamos todos los errores menos los notice
error_reporting(E_ALL & ~E_NOTICE); 
ini_set("display_errors", _DISPLAYERROR); 

if(!isset($_SESSION['class_format']))
	$_SESSION['class_format']=new format();

if(!isset($_SESSION['print']))
	$_SESSION['print']=new render();
	
//cambiar la conexion de iconnect a class.db.php
include_once("core/class/class.INCLUDE.php");
include_once("core/class/class.MENU.php");
include_once("core/class/class.CLOCK.php");
//mejorar el sistema de creacion de htaccess profundizar en ello y ver la posibilidad de acticar o desactivar el htaccess
include_once("core/class/class.URL.php");
include_once("core/class/class.SECURITY.php");
include_once("core/class/class.ERROR.php");
include_once("core/class/class.API.php");
//include_once("core/class/class.FILE.php");
include_once("core/function/IConnect.php");
include_once("core/function/IForms.php");
include_once(_URLMODULES);

$inc=new includes($_modules);
$menu=new menu($_modules);
//$API= new API();
if(!$_GET['go']){
	myad($_GET['id']);
}else{
	switch($_GET['go']){
		case 'approve':
			myad_approve($_GET['id'],$_GET['id_ad']);
		break;
		case 'changes':
			myad_changes($_GET['id'],$_GET['id_ad']);
		break;
	}
}

//codigo del script
$final=getmicrotime();

//echo "tiempo de ejecucion = ".($final-$inicio);
?>

