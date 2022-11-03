<?php
function home(){
	$API= new API();
	$_SESSION['content']=$API->getHTML("../modules/home/face/dashboard.html");
	$API->printadmin();
}
?>