case 'home':
if(!$_GET['do']){
	home();
}else{
	switch($_GET['do']){
		case 'home-equipo':
			home_equipo();
		break;
		case 'home-mision':
			home_mision();
		break;	
		case 'home-comision':
			home_comision();
		break;		
	}
}
break;