<?php
function gallery(){
	$API= new API();
	$API->moduleName("gallery");
	
	$info['action']="?go=login&do=auth";
	$url="modules/login/face/index.html";
	$login=$API->template($url,$info);
	$_SESSION['content']=$login;

	$API->printweb();
}
function gallery_new($user,$pass,$lastURL){
	$API= new API();
	$API->moduleName("login");
	if(!$_SESSION['auth_user_name']);
		$API->goto("index.php");
		
	$busqueda=mysql_query("SELECT * FROM `users` WHERE
						   username='".$user."' AND 
						   password='".$pass."' 
						   AND level>0")or die(mysql_error());
						   
	if(mysql_num_rows($busqueda)>0){
		$_SESSION['auth_user_name']=$user;
		if($_SESSION['login_last_url']=="")
			header("location: index.php");
		else	
			header("location: ".$_SESSION['login_last_url']); 
	}else{			
		header("location: index.php?go=login"); 
	}
}
?>