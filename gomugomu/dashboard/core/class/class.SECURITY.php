<?php
/*Nombre de la clase : security
Objetivo de la clase :
Nos permite crear un menu dinamico con switch , case y break a partir de un array con la localizacion de las 
partes del menu


*/
class security{

	function auth(){
		//echo $_SESSION['login_username'].''.$_SESSION['login_password'];
			
		$query=mysql_query("SELECT * FROM `users` WHERE 
							username='". utf8_encode($_SESSION['login_username'])."' 
							AND password='".md5($_SESSION['login_password'])."' 
							AND level>0")or die(mysql_error());
		
		
		$num=mysql_num_rows($query);
		
		while ($data=mysql_fetch_array($query)){
   			 $_SESSION['login_id']=$data['id'];
   			 $_SESSION['login_level']=$data['level'];
		}	
		
		if($num>0){
			$busqueda="UPDATE `users` SET `last_login` = '".time()."' WHERE `username` ='".$_SESSION['login_username']."'";
			mysql_query($busqueda) or die(mysql_error());
			return true;
		}else{
			//echo "error".$_SESSION['login_username'].$_SESSION['login_password'];
			return false;
		}	
	}
	

}
?>
