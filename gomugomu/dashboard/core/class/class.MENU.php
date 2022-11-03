<?php
//Nombre de la clase : menu
//
//Objetivo de la clase :
//Nos permite crear un menu dinamico con switch , case y break a partir de un array con la localizacion de las 
//partes del menu
//
//Variables de la clase :
//$_array -> almacenara el array con el que se construya la clase y que contiene la localizacion de las partes del menu
//
//Funciones de la clase :
//menu($array) -> constructor de la clase con el array de las partes del menu
//       $array-> array con las partes del menu
//construir_menu() -> como su propio nombre indica se encarga de juntar las partes y obtener el menu completo 
//        devuelve ->el codigo completo del menu
//imprimir() -> se encarga de interpretar el menu con los datos de construir_menu()
//

class menu{
	var $_array;	
	function menu($array){
		$this->_array=$array;
	}
	function construir_menu(){
	
		$nombre= "switch(\$_GET['"._VARMENU."']){";
		$num=count($this->_array);
		for ($i=0; $i < $num; $i++){
			if(file_exists($this->_array[$i].'/menu/menu.php')){
				$nombre.=file_get_contents($this->_array[$i].'/menu/menu.php');
			}
		}
		$nombre.="}";
		
		return $nombre;
	}
	function imprimir(){
		eval($this->construir_menu());
	}
}
?>