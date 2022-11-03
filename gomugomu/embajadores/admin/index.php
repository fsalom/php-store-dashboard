<?php
date_default_timezone_set('America/Los_Angeles');
include_once("../core/class/class.RENDER.php");
include_once("../core/class/class.FORMAT.php");
session_start();
error_reporting(E_ALL & ~E_NOTICE); 
if($_GET['go']=="logout"){
	session_destroy();
	header("location: index.php");
}
include_once("../core/config.php");
include_once("../core/class/class.smtp.php");
include_once("../core/class/class.phpmailer.php");
include_once("../core/function/IConnect.php");
include_once("../core/function/IForms.php");
include_once("../core/class/class.AINCLUDE.php");
include_once("../core/class/class.PAGINATOR.php");
include_once("../core/class/class.AMENU.php");
include_once("../core/class/class.NAV.php");
include_once("../core/class/class.SYSTEM.php");
include_once("../core/class/class.SECURITY.php");
//include_once("../core/class/class.FILE.php");
include_once("../"._URLMODULES);
include_once("../core/class/class.API.php");

$nav =new nav();
$nav->admin();

$security=new security();

$system=new SYSTEM();

$system->buffer();

$inc=new includes($_modules);
$menu=new menu($_modules);


if(!isset($_SESSION['class_format']))
	$_SESSION['class_format']=new format();

if(!isset($_SESSION['print']))
	$_SESSION['print']=new render();
	
	//echo $_SESSION['login_level'];
if($security->auth()){
	if(!$_GET['go']){
		echo days_control();		
	}
	$menu->imprimir();
}else{
	$_SESSION['print']->alogin();
}





	
?>
