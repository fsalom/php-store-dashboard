case 'error':
if(!$_GET['do']){
	error();
}else{
	switch($_GET['do']){
		case '404':
			error_404();
		break;
	}
}
break;