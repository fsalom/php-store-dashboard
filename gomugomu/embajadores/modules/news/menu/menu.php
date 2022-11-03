case 'news':
if(!$_GET['do']){
	news();
}else{
	switch($_GET['do']){
		case 'view':
			news_view($_GET['id']);
		break;
		case 'comment':
			echo "hola";
			//news_comment($username,$web,$email,$comment,$make)
			news_comment($_REQUEST['username'],$_REQUEST['web'],$_REQUEST['email'],$_REQUEST['comment'],$_REQUEST['make']);
		break;
	}
}
break;