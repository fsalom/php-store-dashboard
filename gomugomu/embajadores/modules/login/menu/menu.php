case 'login':
if(!$_GET['do']){
	login();
}else{
	switch($_GET['do']){
		case 'auth':
			login_auth($_POST['user'],$_POST['pass'],$_SERVER['HTTP_REFERER']);
		break;
		case'logout':
			session_destroy();
			header("location: index.php");
		break;
	}
}
break;