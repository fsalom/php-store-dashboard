<?php

function clean($string) {
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
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


if($begin!=false){

	// Seleccionamos fecha
	$dy=date("Y",$begin);
	$dm=date("m",$begin);
	$dd=date("j",$begin);
	
	/*$query = 'SELECT * FROM Verkoop WHERE  
	  (DATEPART(yy, Datum) = "'.$dy.'"
AND    DATEPART(mm, Datum) = "'.$dm.'"
AND    DATEPART(dd, Datum) = "'.$dd.'")';
	*/
	
	//echo $begin;
	
	$query='SELECT Factuurnummer, agerange, geslacht, country, Datum FROM Verkoop WHERE Factuurnummer > '.$begin;
	$result = mssql_query($query) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

//Buscamos los tickets de ese dia
while($row = mssql_fetch_array($result)){
	$queryDetail = "SELECT * FROM VerkoopBetaling WHERE Factuurnummer = ".$row["Factuurnummer"];
	$resultDetail = mssql_query($queryDetail) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
	$restar=0;
	
	
	mysql_query("INSERT INTO dash_buyer (id_ticket,
										 agerange,
										 sex,
										 country
										 ) 
								 VALUES ('".$row['Factuurnummer']."',
								 		 '".$row['agerange']."',
								 		 '".$row['geslacht']."',
								 		 '".$row['country']."'
								 		 )") 
								 or die (mysql_error());
								 
	$queryDetail = "SELECT Factuurnummer, Artikelnummer, Omschrijving, origverkoopprijs, Verkoopprijs, Aantal  FROM VerkoopDetail WHERE Factuurnummer = ".$row["Factuurnummer"];
	$resultDetail = mssql_query($queryDetail) 
			or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

	//Obtenemos todos los datos de los articulos de ese ticket
	$queryPayment = "SELECT Factuurnummer, Betalingswijze, Bedrag, Datum FROM VerkoopBetaling WHERE Factuurnummer = ".$row["Factuurnummer"];
	$resultPayment = mssql_query($queryPayment)
			or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

	print($rowPayment);
	while($rowPayment=mssql_fetch_array($resultPayment)){
			//print_r($rowPayment);
			mysql_query("INSERT INTO dash_payment (
											 id_ticket,
											 type,
											 quantity,
											 date
											 ) 
									 VALUES ('".$rowPayment['Factuurnummer']."',
									 		 '".$rowPayment['Betalingswijze']."',
									 		 '".$rowPayment['Bedrag']."',
									 		 '".date('Y-m-d h:i:s',strtotime($rowPayment['Datum']))."'
									 		 )") 
									 or die (mysql_error());
			}
			
			
	while($rowDetail =mssql_fetch_array($resultDetail)){
			//print_r($rowDetail);
			$id_ticket=$rowDetail['Factuurnummer'];
		  	$date=$row['Datum'];
		  	$articleNr=$rowDetail['Artikelnummer'];
		  	$description=str_replace("'"," ",$rowDetail['Omschrijving']);
		  	
		  	$data=get_reference($rowDetail['Artikelnummer']);
			$season=$data['season'];
			$referencenr=$data['reference'];
			$size=$data['size'];
			$colour=str_replace("'"," ",$data['colour']);
			
			$subtotalOriginal=$rowDetail['origverkoopprijs'];
			$subtotal=$rowDetail['Verkoopprijs'];
			$subtotalDiscounts=$rowDetail['origverkoopprijs']-$rowDetail['Verkoopprijs'];
			$items=$rowDetail['Aantal'];
			
			$date = date( "Y-m-d H:i:s", strtotime( $date ) );
			$ndate=date("Ymd",strtotime( $date ));
			$vat=18;
			//echo $ndate;
			if($ndate>"20120901")
				$vat=21;
			
			
			/*
			echo 'Ticket: '.$id_ticket.' <br/>'.
				 'Fecha: '.$date.' <br/>'.
				 'Articulo : '.$articleNr.' <br/>'.
				 'Descripción: '.$description.'<br/> '.
				 'Season: '.$season.' <br/>'.
				 'Size: '.$size.' <br/>'.
				 'Colour: '.$colour.'<br/> '.
				 'Precio original: '.$subtotalOriginal.'<br/> '.
				 'Precio de venta: '.$subtotal.' <br/>'.
				 'Descuento: '.$subtotalDiscounts.' <br/>-----------<br/>';
			*/
				
			//print('------TICKET-------</br>');
			//print($id_ticket);
			//print($id_ticket." ".$date." ".$articleNr." ".$description." ".$season." ".$size." ".$colour." ".$referencenr." ".$vat." ".$items);

			mysql_query("INSERT INTO dash_ticket (
										 id_ticket,
										 date,
										 articlenr,
										 description,
										 season,
										 size,
										 colour,
										 referencenr,
										 VAT,
										 items,
										 subtotal,
										 subtotalOriginal,
										 subtotalDiscounts,
										 created_date
										 ) 
								 VALUES ('".$id_ticket."',
								 		 '".$date."',
								 		 '".$articleNr."',
								 		 '".$description."',
								 		 '".$season."',
								 		 '".$size."',
								 		 '".$colour."',
								 		 '".$referencenr."',
								 		 '".$vat."',
								 		 '".$items."',
								 		 '".$subtotal."',
								 		 '".$subtotalOriginal."',
								 		 '".$subtotalDiscounts."',
								 		 now()
								 		 )") 
								 or die (mysql_error());
/*----------------------------
  BASE DE DATOS dash_ticket
------------------------------
1 	id 
2 	id_ticket
3 	id_day
4 	date 	datetime
5 	articlenr
6 	description
7 	season
8 	size
9 	colour
10 	referencenr
11 	VAT
12 	items
13 	subtotal
14 	subtotalOriginal
15 	subtotalDiscounts
16 	info
17 	created_id
18 	created_date
19 	status
*/		
			  
		  }
		  
		
	 }
	
}
mssql_close($link);
	
}

function get_reference($reference){
$myServer = "superdry.bscorp.be";
$myUser = "Fernando";
$myPass = "S@l0m";
$myDB = "ES_Valencia";


$link = mssql_connect($myServer, $myUser, $myPass);
if (!$link)
    die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

$selected = mssql_select_db($myDB, $link) or die("Couldn't open database $myDB ." .mssql_get_last_message() ); 
	$queryDetail = "select * from artikelmatrix m inner join artikelinfo i on i.artikelmatrixid = m.artikelmatrixid where i.artikelnummer = ".$reference;

$resultDetail = mssql_query($queryDetail) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
$restar=0;

	 while($rowDetail =mssql_fetch_array($resultDetail)){
	 	//print_r($rowDetail);
	 	$data['colour']=$rowDetail['MatrixV'];
	 	$data['size']=$rowDetail['MatrixH'];
	 	$data['reference']=$rowDetail['Referentienummer'];
	 	$data['season']=$rowDetail['Seizoen'];
	 }
	 return $data;
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

$result=mysql_query("SELECT * FROM `dash_ticket` ORDER BY id DESC LIMIT 1 ") or die (mysql_error());
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
  
$query = "SELECT TOP(1) Factuurnummer FROM Verkoop ORDER BY Factuurnummer DESC";

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
	return false;
	}

}


get_data(is_updated());
?>
