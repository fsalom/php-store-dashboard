case 'user':
if(!$_GET['do']){
	user_show();
}else{
	switch($_GET['do']){
		case'new':
			user_new();
		break;
		case'update':
			user_update();
		break;
		case'check':
			user_check();
		break;
		case'delete':
			user_delete($_GET['email'],$_GET['id']);
		break;
		case'edit':
			user_edit($_GET['email'],$_GET['id']);
		break;
		case'resetpass':
			user_reset($_GET['email'],$_GET['id'],$_GET['username']);
		break;
		case'validate':
			user_validate($_GET['id']);
		break;
	}
}
break;