<html>
<head>
	<style>
		body{
			font-family: Verdana;
			font-size: 12px;
		}
	</style>
</head>
<body>
<?php
error_reporting(E_ALL);

define("_SERVER","db449869918.db.1and1.com");
define("_USERNAME","dbo449869918");
define("_PASSWORD","osaka2011");
define("_BD","db449869918");

	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion){
		$API = new API();
		die($API->printerror(_MYSQLCONNECT));
	}
	mysql_select_db(_BD, $conexion);
	
	$query=mysql_query(
						"SELECT `id_ticket`,`date`,SUM(price) FROM `feed_ticket` GROUP BY `id_ticket` ORDER BY `id_ticket` "
						)
	or die(mysql_error());

	mysql_query("DELETE FROM `feed_time`");
	while($dato=mysql_fetch_array($query)){

			$token=explode(' ',$dato['date']);
			
			$time=explode(':',$token[1]);
			
			$hour=$time[0];
			$min=$time[1];
			
			$date=$token[0];
			$total=$dato['SUM(price)'];
			
			
			
			
			//INSERTADO HASTA EL 11 de JULIO
			//BORRAMOS Y REINSERTAMOS SI HAY MAS TICKETS
			
			
			
			mysql_query("INSERT INTO  `feed_time` 
			( `date` ,
			  `hour` ,
			  `min` ,
			  `total` 
			 ) 
VALUES 
			( '".$date."',  
			  '".$hour."', 
			  '".$min."',
			  '".$total."'
			 );") or die(mysql_error());
			 
			/*$query=mysql_query(
		"UPDATE `feed_ticket` SET `date` = '".$token[1]."-".$token[0]."-".$token[2]." ".$token[3]."' WHERE `id` ='".$dato['id']."'"
			)
			or die(mysql_error());
			 */
			echo $token[0]." ".$time[0].":".$time[1]."<br/>";
			//echo $date." - ".$hour.':'.$min." - ".$total." - insertado</br>";	
	}
?> 
</body>
</html>