<html>
<body>
<?php

include_once("../core/class/class.API.php");
include_once("../core/config.php");

	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion){
		$API = new API();
		die($API->printerror(_MYSQLCONNECT));
	}
	mysql_select_db(_BD, $conexion);


	$query2	= mysql_QUERY("SELECT SUBSTRING(date,1,10) as f FROM `feed_ticket` GROUP BY `f` ORDER BY `id`")or die(mysql_error());
	while($data = mysql_fetch_array($query2)){
		$query = "SELECT date, SUM(subtotal) FROM `feed_ticket` WHERE date LIKE '".$data['f']."%'";
		$result = mysql_query($query)or die(mysql_error());
		while($row = mysql_fetch_array($result)){
			
			$space=explode(" ",$row['date']);
			//echo $space[0]."<br/>";
			$break=explode("-",$space[0]);
			$date=$break[2]."-".$break[1]."-".$break[0];
			$day=strftime("%A",strtotime($date));
			//echo $date." ".$day." ".$row['SUM(subtotal)']."<br/>";
			
			$week[$day]['total']+=$row['SUM(subtotal)'];
			$week[$day]['num']+=1;
			//"2011-05-19"
			if($day=="Sunday")
				echo $row['date']." ".$day." ".$row['SUM(subtotal)']."<br/>";
		}
		
	}
	
	
	echo $week['Sunday']['total']/$week['Sunday']['num'].'<br/>';
	
	echo $week['Sunday']['total']." ".$week['Sunday']['num'];
?>
</body>
</html>