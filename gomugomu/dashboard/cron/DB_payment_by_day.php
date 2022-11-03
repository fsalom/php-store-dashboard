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

	
	$query = "SELECT Factuurnummer, Betalingswijze, Bedrag FROM VerkoopBetaling WHERE Factuurnummer> ".$begin;
	$result = mssql_query($query) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

//Buscamos los tickets de ese dia
$suma=0;
$i=0;
		while($row = mssql_fetch_array($result)){
			echo $row['Factuurnummer'].' '.$row['Betalingswijze'].' '.$row['Bedrag'].'<br/>';
			$suma+=$row['Bedrag'];
			$i++;
			mysql_query("INSERT INTO dash_payment (
										 id_ticket,
										 type,
										 quantity
										 ) 
								 VALUES ('".$row['Factuurnummer']."',
								 		 '".$row['Betalingswijze']."',
								 		 '".$row['Bedrag']."'
								 		 )") 
								 or die (mysql_error());

		}
		echo $i;
		  echo $suma;
	

mssql_close($link);
	
}


//INSERTA TODOS LOS PAGOS A PARTIR DEL TICKET QUE LE INDIQUEMOS
//$begin=12458;
//get_data($begin);
?>
