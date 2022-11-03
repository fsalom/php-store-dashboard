<?php
function descargas(){
	$API= new API();
	$API->moduleName("login");
	$API->security();
	$_SESSION['content']="descargas";
	$API->printweb();
}
?>