<?php
class format{
	
	function format(){		
	
	}
	
	function friendlyURL($url){
		// Tranformamos todo a minusculas
		$url = strtolower($url);
		//Rememplazamos caracteres especiales latinos
		$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
		$repl = array('a', 'e', 'i', 'o', 'u', 'n');
		
		$url = str_replace ($find, $repl, $url);	
		// A–aadimos los guiones
		$find = array(' ', '&', '\r\n', '\n', '+');
		$url = str_replace ($find, '-', $url);
		// Eliminamos y Reemplazamos dem‡s caracteres especiales
		$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
		$repl = array('', '-', '');
		$url = preg_replace ($find, $repl, $url);
		return $url; 
	}
	
	function cutString($texto,$num){
		if(strlen($texto)>$num){
			$text=substr($texto, 0, $num)."..."; 
		}else{
			$text=substr($texto, 0, $num); 
		}
		return $text;
	}
	
}