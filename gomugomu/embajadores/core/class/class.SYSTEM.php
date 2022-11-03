<?php
class SYSTEM{
	function buffer(){
		$numero = count($_GET);
		$tags = array_keys($_GET);// obtiene los nombres de las varibles
		$valores = array_values($_GET);// obtiene los valores de las varibles
			for($i=0;$i<$numero;$i++){
				if(($tags[$i]!='go')&&($tags[$i]!='do')){
					$_SESSION[$tags[$i]]=$valores[$i];
				}
			}

		$numero2 = count($_POST);
		$tags2 = array_keys($_POST); // obtiene los nombres de las varibles
		$valores2 = array_values($_POST);// obtiene los valores de las varibles

			// crea las variables y les asigna el valor
			for($i=0;$i<$numero2;$i++){ 
				$_SESSION[$tags2[$i]]=$valores2[$i]; 
			}
	}
	function getData($name){
		if(!isset($_SESSION[$name])){
			return 0;
		}else{
			return $_SESSION[$name];
		}
	}
	function setData($name,$data){
		$_SESSION[$name]=$data;
	}
	function getDivs(){
		$url=$GLOBALS['_url_template'].$this->Data('template').'/';
		$filename="divs.txt";
		$urlFile=$url.$filename;
		$file=fopen($urlFile,"rt");
		$i=0;
		while(!feof($file)){
			$linea=fscanf($file,"%s");
			$div[$i]=$linea[0];
			$i++;
		}
		return $div;
		}
	}
?>
