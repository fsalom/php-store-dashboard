case 'gallery':
if(!$_GET['do']){
	gallery();
}else{
	switch($_GET['do']){
		case 'new':
			gallery_new($_POST['user'],$_POST['pass'],$_SERVER['HTTP_REFERER']);
		break;
	}
}
break;