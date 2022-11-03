<?php
function get_data($begin){
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

$result=mysql_query("SELECT * FROM `feed_ticket` ORDER BY id DESC LIMIT 1 ") or die (mysql_error());
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
  
if($begin!=0){
	$query = "SELECT * FROM Verkoop WHERE Factuurnummer>".$begin;
	
	$result = mssql_query($query) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
	
	while($row = mssql_fetch_array($result)){
	  echo "<li>" . $row["Factuurnummer"] ." ". $mostrar = date("d-m-Y H:i:s", strtotime($row["Datum"]))  ." ".  $row["geslacht"] ." ".$row["agerange"]. " ".$row["country"]. "</li>";
		  $queryDetail = "SELECT * FROM VerkoopDetail WHERE Factuurnummer = ".$row["Factuurnummer"];
		  $resultDetail = mssql_query($queryDetail) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
		 
		  while($rowDetail =mssql_fetch_array($resultDetail)){
			  echo "<br/>- Serial:" . $rowDetail["Artikelnummer"] .
			  "<br/> Descripción: ". $rowDetail["Omschrijving"] .
			  "<br/> Precio: ". $rowDetail["Verkoopprijs"] .
			  "<br/> Precio de venta: ". $rowDetail["origverkoopprijs"].
			  "<br/> IVA: ". $row["Lock"].
			  "<br/> Articulo: ". $rowDetail["RetourRedenID"]."<br/>";
		  }
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

$result=mysql_query("SELECT * FROM `feed_ticket` ORDER BY id DESC LIMIT 1 ") or die (mysql_error());
$row = mysql_fetch_array($result);
$mysql=$row['id_ticket'];
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
echo $mssql.' - '.$mysql;

if($mssql!=$mysql)
	return $mysql;
else
	return false;

}

get_data(is_updated());
?>