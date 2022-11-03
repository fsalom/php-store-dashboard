<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<link rel="stylesheet" type="text/css" href="time.css" media="screen" />
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
</head>
<body>

<h1>Evolución desde Septiembre a Diciembre</h1>
<div class="logo"><img src="gomu.png"></div>
<div class="content">
<?php
include_once("../core/class/class.API.php");
include_once("../core/config.php");


	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion){
		$API = new API();
		die($API->printerror(_MYSQLCONNECT));
	}
	mysql_select_db(_BD, $conexion);

	$total_9=mysql_query("SELECT * FROM `feed_time` WHERE `date` LIKE '%-09-2012'");
	$total_10=mysql_query("SELECT * FROM `feed_time` WHERE `date` LIKE '%-10-2012'");
	$total_11=mysql_query("SELECT * FROM `feed_time` WHERE `date` LIKE '%-11-2012'");
	$total_12=mysql_query("SELECT * FROM `feed_time` WHERE `date` LIKE '%-12-2012'");
	
	$totalhoras=mysql_num_rows($total_9)+
	mysql_num_rows($total_10)+
	mysql_num_rows($total_11)+
	mysql_num_rows($total_12);
	
	$extra="AND (`date` LIKE '%-09-2012' OR `date` LIKE '%-10-2012' OR `date` LIKE '%-11-2012' OR `date` LIKE '%-12-2012')";
	
	for($i=10;$i<22;$i++){
		if($i>20){
		
			$query=mysql_query("SELECT * FROM `feed_time` WHERE `hour`>'20' ".$extra)or die(mysql_error());
			
			$horas=mysql_num_rows($query);
			$percentage=(FLOAT)$horas/$totalhoras*100;
			$content.= '<div class="hgraph" style="width:'.round((FLOAT)($horas/$totalhoras)*100*20,2).'%; "><b>+21h</b> - '.round($percentage,2)."%</div>";
		
		}else{
			for ($x=0; $x<60;$x+=15){
				if($x==0)
					$query=mysql_query("SELECT * FROM `feed_time` WHERE hour='".$i."'  AND `min`>'".$x."' AND `min`<'15' ".$extra )or die(mysql_error());
				else if($x==45)
					$query=mysql_query("SELECT * FROM `feed_time` WHERE hour='".$i."'  AND `min`>'".$x."' AND `min`<'60' ".$extra )or die(mysql_error());
				else
						$query=mysql_query("SELECT * FROM `feed_time` WHERE hour='".$i."'  AND `min`>'".$x."' AND `min`<'".($x+15)."' ".$extra )or die(mysql_error());
		
				$horas=mysql_num_rows($query);
				$percentage=(FLOAT)$horas/$totalhoras*100;
				
				if($horas!=0)
					$content.= '<div class="hgraph" style="width:'.round((FLOAT)($horas/$totalhoras)*100*20,2).'%;"><b>'.$i."h:".$x."m</b> - ".round($percentage,2)."%</div>";
			
			}
			$x=0;
		}
			
	}
	echo $content;

?>
</div>
</body>
</html>