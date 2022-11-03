<?php
function error_404(){
	$API= new API();
	$API->moduleName("error");
	
	$content=file_get_contents("modules/error/face/404.html");
	$API->printweb($content);
}
?>