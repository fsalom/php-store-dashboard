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
define("_USERNAME","gomugomu");
define("_PASSWORD","osaka2011");
define("_BD","admin_feedback");

$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
if (!$conexion)
	die('Something went wrong while connecting to MSSQL: '.mysql_error());
mysql_select_db(_BD, $conexion);

$result=mysql_query("SELECT * FROM `feed_buyers` ORDER BY id DESC LIMIT 1 ") or die (mysql_error());
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


if($begin!=false){

	// Seleccionamos fecha
	$dy=date("Y",$begin);
	$dm=date("m",$begin);
	$dd=date("j",$begin);
	
	
	$query='SELECT * FROM Verkoop WHERE Factuurnummer > '.$begin;
	$result = mssql_query($query) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

//Buscamos los tickets de ese dia
while($row = mssql_fetch_array($result)){
	$from='1';
	if($row['country']=="ES") $from='0';
	

				
	$queryDetail = "SELECT * FROM VerkoopBetaling WHERE Factuurnummer = ".$row["Factuurnummer"];
	$resultDetail = mssql_query($queryDetail) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
$restar=0;
		 while($rowDetail =mssql_fetch_array($resultDetail)){
		 
		 	$type=$rowDetail['Betalingswijze'];
		 	$amount=$rowDetail['Bedrag'];
		 	
		 	if($type=='Waardebon')$restar+=$amount*-1;
		 }
		
									 
	$queryDetail = "SELECT * FROM VerkoopDetail WHERE Factuurnummer = ".$row["Factuurnummer"];
	$resultDetail = mssql_query($queryDetail) 
			or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

	//Obtenemos todos los datos de los articulos de ese ticket
	$subtotal=0;
	$subtotalDiscounts=0;
		while($rowDetail =mssql_fetch_array($resultDetail)){
			//print_r($rowDetail);
			$id_ticket=$rowDetail['Factuurnummer'];
		  	$date=$row['Datum'];
		  				
			$subtotalOriginal+=$rowDetail['origverkoopprijs'];
			$subtotal+=$rowDetail['Verkoopprijs']*$rowDetail["Aantal"];
			$subtotalDiscounts+=$rowDetail['origverkoopprijs']-($rowDetail['Verkoopprijs']*$rowDetail["Aantal"]);
			
			$date = date( "d-m-Y H:i:s", strtotime( $date ) );
			$date2=date("Y-m-d",strtotime( $date ));
		
				
			  
		  }
		  $subtotal+=$restar;
		  mysql_query("INSERT INTO `feed_buyers` (
										 `id_ticket`,
										 `date`,
										 `date2`,
										 `quantity`,
										 `from`,
										 `discount`
										 ) 
								 VALUES ('".$id_ticket."',
								 		 '".$date."',
								 		 '".$date2."',
								 		 '".$subtotal."',
								 		 '".$from."',
								 		 '".$subtotalDiscounts."'
								 		 )") 
								 or die (mysql_error());

		
	 }
	
}
mssql_close($link);
	
}




function is_updated(){
/************************************************
	MYSQL
************************************************/
define("_SERVER","localhost");
define("_USERNAME","gomugomu");
define("_PASSWORD","osaka2011");
define("_BD","admin_feedback");

$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
if (!$conexion)
	die('Something went wrong while connecting to MSSQL: '.mysql_error());

mysql_select_db(_BD, $conexion);

$result=mysql_query("SELECT * FROM `feed_buyers` ORDER BY id DESC LIMIT 1 ") or die (mysql_error());
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
  
$query = "SELECT top 1 * FROM Verkoop ORDER BY Factuurnummer DESC";

$result = mssql_query($query) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());;
$row = mssql_fetch_array($result);
$mssql = $row['Factuurnummer'];
mssql_close($link);
/************************************************
	MSSQL
************************************************/
//echo $mssql.' - '.$mysql.'<br/>';

if($mssql!=$mysql){
	echo $mssql-$mysql.' registros<br/> Desde el ticket: '.$mysql.' hasta el '.$mssql;
	return $mysql;
}else{
	echo 'la base de datos esta actualizada';
	return false;
	}

}

get_data(is_updated());
?>
