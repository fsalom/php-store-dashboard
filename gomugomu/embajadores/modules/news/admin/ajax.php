<?php
session_start();
include_once("../../../core/class/class.API.php");
include_once("../../../core/config.php");

$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
mysql_select_db (_BD, $conexion);
	
$API = new API();

   	$upload=($_REQUEST['upload']);
	

        $resp = array();
        $username = trim($username);
        //comprobamos que no hay ningún campo obligatorio vacio
        if (($upload=="undefined")) {
            $resp = array('ok' => false, 'msg' => '<b style="color:#990000">Por favor rellene todos los datos obligatorios</b>');
        //comprobamos que la dirección de correo es valida
        }else if(!$upload){
			$resp = array('ok' => false, 'msg' => '<b style="color:#990000">Por favor debe de introducir un email valido</b>');
        //cometario valido 
        }else {
        		
	$dir = "../../../img/upload/";

	// Abrir un directorio conocido, y proceder a leer sus contenidos
	if (is_dir($dir)) {
    	if ($gd = opendir($dir)) {
    	    while (($archivo = readdir($gd)) !== false) {
    	    	$pre=substr($archivo,0,10);
    	    	$pre2=substr($archivo,0,7);
    	    	if(($archivo=="..")||($archivo==".")||($pre=="thumbnail_")||($pre2=="resize_")){
    	    		$x.= substr($arhivo,0,10);

    	    	}else{
    	    		$nom=substr($archivo,10,strlen($archivo));

    	    		$img='<img src="'.$dir.$archivo.'" >';
    	        	$x.='<a href="javascript:;"  onmousedown="tinyMCE.execCommand(\'mceInsertContent\',false,\'<img src=http://www.fernandosalom.es/img/upload/thumbnail_'.$nom.'>\');"><img src="'.$dir.$archivo.'" border="0" class="itemsimg"></a>';
    	    	}
    	    }
    	    closedir($gd);
    	}
	}else{
		$x='no se encontro '.$dir;
	}

            $resp = array("ok" => true, "msg" => $x);
        }

	if (@$_REQUEST['do'] == 'check' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode($resp);
        exit;
    }  
?>