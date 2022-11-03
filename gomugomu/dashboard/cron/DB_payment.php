<?php
/*********************************************************************************
		CRON PARA AÑADIR LOS TICKETS DIARIAMENTE A LA BASE DE DATOS
*********************************************************************************/

function get_data($begin){

/************************************************
	MYSQL
************************************************

Abrimos la conexión con la Base de datos MYSQL
*/
define("_SERVER","localhost");
define("_USERNAME","dashboard");
define("_PASSWORD","osaka2011");
define("_BD","admin_dashboard");

$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
if (!$conexion)
	die('Something went wrong while connecting to MSSQL: '.mysql_error());
mysql_select_db(_BD, $conexion);

$result=mysql_query("SELECT * FROM `dash_ticket` ORDER BY id DESC LIMIT 1 ") or die (mysql_error());
$row = mysql_fetch_array($result);


/************************************************
	MSSQL
************************************************/
$myServer = "superdry.bscorp.be";
$myUser = "Fernando";
$myPass = "S@l0m";
$myDB = "ES_Valencia";


$link = mssql_connect($myServer, $myUser, $myPass);
if (!$link)
    die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
$selected = mssql_select_db($myDB, $link) or die("Couldn't open database $myDB ." .mssql_get_last_message() ); 

	
	$query='SELECT * FROM VerkoopBetaling';
	$result = mssql_query($query) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

//Buscamos los tickets de ese dia
$i=0;
		while($row = mssql_fetch_array($result)){
			echo $row['Factuurnummer'].' '.$row['Betalingswijze'].' '.$row['Bedrag'].'<br/>';
			$i++;
			/*mysql_query("INSERT INTO dash_payment (
										 id_ticket,
										 type,
										 quantity
										 ) 
								 VALUES ('".$row['Factuurnummer']."',
								 		 '".$row['Betalingswijze']."',
								 		 '".$row['Bedrag']."'
								 		 )") 
								 or die (mysql_error());
*/
		}
		echo $i;
		  
	

mssql_close($link);
	
}

function is_updated(){
/************************************************
	MYSQL
************************************************/
define("_SERVER","localhost");
define("_USERNAME","dashboard");
define("_PASSWORD","osaka2011");
define("_BD","admin_dashboard");

$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
if (!$conexion)
	die('Something went wrong while connecting to MSSQL: '.mysql_error());

mysql_select_db(_BD, $conexion);

$result=mysql_query("SELECT * FROM `dash_payment` ORDER BY id_ticket DESC LIMIT 1 ") or die (mysql_error());
$row = mysql_fetch_array($result);
$mysql=$row['id_ticket'];
if($mysql=='')$mysql=0;
/************************************************
	MSSQL
************************************************/
$myServer = "superdry.bscorp.be";
$myUser = "Fernando";
$myPass = "S@l0m";
$myDB = "ES_Valencia";


$link = mssql_connect($myServer, $myUser, $myPass);
if (!$link)
    die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

$selected = mssql_select_db($myDB, $link) or die("Couldn't open database $myDB ." .mssql_get_last_message() ); 
  
$query = "SELECT top 1 * FROM VerkoopBetaling ORDER BY Factuurnummer DESC";

$result = mssql_query($query) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());;
$row = mssql_fetch_array($result);
$mssql = $row['Factuurnummer'];
mssql_close($link);
/************************************************
	MSSQL
************************************************/
//echo $mssql.' - '.$mysql.'<br/>';

if($mssql!=$mysql){
	echo 'Se van a actualizar '.$mssql-$mysql.' registros<br/> Desde el ticket: '.$mysql.' hasta el '.$mssql;
	return $mysql;
}else{
	echo 'la base de datos esta actualizada';
	echo $mssql.' '.$mysql;
	return false;
	}

}
echo is_updated();
//get_data(1);
?>
