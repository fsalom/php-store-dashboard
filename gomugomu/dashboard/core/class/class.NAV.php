<?php

class nav{

	function admin(){
		$nombre="";
		if(file_exists("../modules/".$_SESSION['moduleName'].'/admin/nav/nav.php')){
				$nombre.=file_get_contents("../modules/".$_SESSION['moduleName']."/admin/nav/nav.php");
		}
		
		$_SESSION['template_submenu']=$nombre;
	}

}
?>