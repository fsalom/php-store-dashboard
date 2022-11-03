case 'files':
if(!$_GET['do']){
	files();
}else{
	switch($_GET['do']){
		case 'category':
			files_category($_GET['cat']);
		break;		
	}
}
break;