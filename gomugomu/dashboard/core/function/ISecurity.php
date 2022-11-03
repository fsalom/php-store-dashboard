<?php

function secauth(){
	$busqueda=mysql_query("SELECT * FROM `users` WHERE username='".$_SESSION['username']."' AND password='".$_SESSION['password']."'");
	$existe=mysql_num_rows($busqueda);
	return $existe;
}

function seclogin($username,$password){

	$API=new API();
	$pass=md5($password);
	$busqueda=mysql_query("SELECT * FROM `users` WHERE username='$username' AND password='$pass'");

	$existe=mysql_num_rows($busqueda);
	
	if($existe==1){
		$_SESSION['username']=$username;
		$_SESSION['password']=$pass;
		echo "<script>location.href='index.php'</script>";
	}else{
		$dato['url']=$GLOBALS['_LTemplateDir'].$GLOBALS['_LTemplateName'];
		$dato['info']=file_get_contents("template/login/default/add/remember.tpl");		
		$content=$API->template("template/login/default/index.html",$dato);
		echo $content;
	}
}

?>