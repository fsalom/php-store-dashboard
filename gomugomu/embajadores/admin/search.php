<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<link rel="stylesheet" type="text/css" href="buyers.css" media="screen" />
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
</head>
<body>

<div class="ball-arc">
	<div class="point"><img src="gomu.png" alt="Gomu Gomu"></div>
</div>

<?php
include_once("../core/class/class.API.php");
include_once("../core/config.php");

	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion){
		$API = new API();
		die($API->printerror(_MYSQLCONNECT));
	}
	mysql_select_db(_BD, $conexion);

	$reference = $_POST['reference'];
	
	
if(($_GET['form']==1)){

		$content="";
		$i=1;
		$result = mysql_query("SELECT * FROM `feed_ticket` WHERE `referencenr`='".$reference."'") or die(mysql_error());
		$num=mysql_num_rows($result);
			while($row = mysql_fetch_array($result)){
				$content.="[<b>".$i."</b>] Número de ticket: <b>".$row['id_ticket']."</b> - ".$row['date']."<br/>
							".$row['colour']." - ".$row['size']." - ".$row['items']."<br/>";
				$i++;
			}
			

		?>
			<div class="notification" style="color:#900; font-weight:bold;">Hay un total de <b><?php echo $num; ?></b> tickets con esa referencia</div>
	<?php
		}else{
		?>
			<div class="notification" style="color:#900; font-weight:bold;">Introduce la referencia que quieres buscar</div>
	<?php			
		}
		
	?>
		
		</div>
		<div id="content">
			<form method="post" name="myForm" action="?form=1">
				Buscar tickets por referencia:<br/><br/>
				<input type="text" name="reference" class="input" value="<?php echo $reference?>"><br/><br/>

				<input type="submit" class="button white" value="Aceptar">
			</form>
		</div>
		<?php
		
		
		
	


?>

<div id="content-time">
<b>Tickets con la referencia: (<?php echo $reference ?>)</b><br/><br/>
<?php
	echo $content;
?>
</div>
		
</body>
</html>