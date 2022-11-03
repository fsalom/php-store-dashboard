<?php
/*
* CLASE CLOCK
*
* Uso : se utiliza para ficheros que se han de modificar cada X segundos
* Variables que utiliza :
*  - _UPDATE_TIME_IN_SECONDS : definido en el fichero config.php indica la cantidad en segundos cada cuanto se actualizará el fichero
*  - $name : nombre del fichero , usado para la bbdd
* Devuelve :
*  - 0 : si no hay que actualizar
*  - 1 : hay que actualizar
*  - 2 : $name no existe en la BBDD 
*
*/
class CLOCK{
	var $name;	
	function CLOCK($name){
		$this->name=$name;
	}
	function getTimeMod(){
		$query=mysql_query("SELECT * FROM `files_info` WHERE name='".$this->name."'") or die(mysql_error());
		$num=mysql_num_rows($query);
		if($num==0){
			return 2;
		}else{
			$row = mysql_fetch_array($query);
			$date_mod=$row['date_mod'];
		
			if((time()-$date_mod)>_UPDATE_TIME_FILES_IN_SECONDS){
				//Actualizamos la bbdd con la fecha de ahora que se ha actualizado para que no tenga que hacerlo el desarrollador desde otro modulo;
				mysql_query("UPDATE `files_info` SET `date_mod` = '".time()."' WHERE `name` ='".$this->name."'");
				return 1;
			}else{
				return 0;
			}

		}
	}
	function NewClock(){
		//Lo utilizamos si no se ha definido un reloj previamente y queremos crear uno
		$query = sprintf("INSERT INTO `files_info` ( `name` , `date_create`) VALUES ('%s', '%d')",
                    mysql_real_escape_string("HTACCESS"),time());
        mysql_query($query);
        return true;
	}		
}
?>
