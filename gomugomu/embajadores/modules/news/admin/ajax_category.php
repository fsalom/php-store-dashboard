<?php
session_start();
include_once("../../../source/class/class.API.php");
define("_SERVER","db125.1and1.es");
define("_USERNAME","dbo265379109");
define("_PASSWORD","2vrsbukY");
define("_BD","db265379109");
define("_mailADMIN" , "fdosalom@gmail.com");

$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
mysql_select_db (_BD, $conexion);
	
$API = new API();

   	$name=($_REQUEST['name']);
	$main=($_REQUEST['main']);

        $resp = array();
        $username = trim($username);
        //comprobamos que no hay ningún campo obligatorio vacio
        if (($name=="")||($main=="")) {
            $resp = array('ok' => false, 'msg' => '<b style="color:#990000">Por favor rellene todos los datos obligatorios</b>');
        //comprobamos que la dirección de correo es valida
        }else {
        	$consulta = sprintf("INSERT INTO `category` (`name` , `id_main`) VALUES ('%s', '%d')",
                    mysql_real_escape_string($name),
                    mysql_real_escape_string($main));
        
			 mysql_query($consulta);
			 $query=mysql_query("SELECT * FROM `category`  ORDER BY id DESC LIMIT 1")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				$id=$dato['id'];
			}
			 $x='<div class="tag">
			 <input type="checkbox" name="category[]" value="'.$id.'" style="float:left; padding-bottom=5px;">'
			 .$name.'</div>';
            $resp = array("ok" => true, "msg" => $x);
        }

	if (@$_REQUEST['do'] == 'check' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode($resp);
        exit;
    }  
?>