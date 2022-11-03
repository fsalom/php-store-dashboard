<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<link rel="stylesheet" type="text/css" href="control.css" media="screen" />
</head>
<body>
<?php
include_once("../core/class/class.API.php");
include_once("../core/config.php");

$month[1]="Enero";
$month[2]="Febrero";
$month[3]="Marzo";
$month[4]="Abril";
$month[5]="Mayo";
$month[6]="Junio";
$month[7]="Julio";
$month[8]="Agosto";
$month[9]="Septiembre";
$month[10]="Octubre";
$month[11]="Noviembre";
$month[12]="Diciembre";



	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion){
		$API = new API();
		die($API->printerror(_MYSQLCONNECT));
	}
	mysql_select_db(_BD, $conexion);


$m =$_GET['m'];
		$w =$_GET['w'];
		$d =$_GET['d'];

if($_GET['do']=='target'){
	 $w =$_GET['w'];
	 $value =str_replace('.','',$_GET['value']);
	 $value =str_replace(',','.',$value);
	 if($_GET['form']==1){
		
		$value=$_POST['value'];
		$m =$_GET['m'];
		$w =$_GET['w'];
		$d =$_GET['d'];
		
		
		
		if((!is_numeric($value))){
			if($m==""){
		
		?>
			<div class="notification">Revisa los datos</div>
			<div id="content">
			<form method="post" name="myForm" action="?do=target&w=<?php echo $w?>&d=<?php echo $d?>&form=1">
				Introduce el objetivo para la semana <?php echo $w?>:<br/><br/>
				<input type="text" name="value" class="input" value="<?php echo $value?>"><br/><br/>
				<input type="submit" class="button white" value="Aceptar">
			</form>
			</div>
		<?php
			}else{
		?>	
			<div class="notification">Revisa los datos</div>
			<div id="content">
			<form method="post" name="myForm" action="?do=target&m=<?php echo $m?>&d=<?php echo $d?>&form=1">
				Introduce el objetivo para el mes de <b><?php echo $month[$m] ?></b>:<br/><br/>
				<input type="text" name="value" class="input" value="<?php echo $value?>"><br/><br/>
				<input type="submit" class="button white" value="Aceptar">
			</form>
			</div>
		<?php		
			}
		}else{
			$year=explode('-',$d);
			
			if($_GET['m']!=""){
					$ticket=mysql_query("SELECT * FROM `feed_control_target` WHERE `year`='".$year[0]."' AND `month`='".$m."' ")or die(mysql_error());
					
					if(mysql_num_rows($ticket)>0){
							mysql_query("UPDATE `feed_control_target` SET `value` = '".$value."' WHERE `year` ='".$year[0]."' AND `month`='".$m."'")or die(mysql_error());	
				?>
						<div class="green">Los datos han sido insertados, puede cerrar la ventana</div>
				<?php
					}else{
							mysql_query("INSERT INTO `feed_control_target` ( `month` , `year` ,`value` )
										VALUES ('".$m."', '".$year[0]."', '".$value."');") or die(mysql_error());
				?>
						<div class="green">Los datos han sido insertados, puede cerrar la ventana</div>
				<?php
		
					}

				
			}else{
				//echo $w." ".$year[0];
					if($value==0){
						mysql_query("DELETE FROM `feed_control_target` WHERE `year`='".$year[0]."' AND `week`='".$w."' ")or die(mysql_error());

					
						?>
						<div class="green">Los datos han sido insertados, puede cerrar la ventana</div>
				<?php
					}else{
					$this_year=date("Y",time());
					$ticket=mysql_query("SELECT * FROM `feed_control_target` WHERE `year`='".$this_year."' AND `week`='".$w."' ")or die(mysql_error());
					if(mysql_num_rows($ticket)>0){
							mysql_query("UPDATE `feed_control_target` SET `value` = '".$value."' WHERE `year` ='".$this_year."' AND `week`='".$w."'")or die(mysql_error());	
				?>
						<div class="green">Los datos han sido insertados, puede cerrar la ventana</div>
				<?php
					}else{
							mysql_query("INSERT INTO `feed_control_target` ( `week` , `year` ,`value` )
										VALUES ('".$w."', '".$this_year."', '".$value."');") or die(mysql_error());
				?>
						<div class="green">Los datos han sido insertados, puede cerrar la ventana</div>
				<?php
		
					}
					}
			}
		}
	}else{
		
		if($_GET['m']==""){
		?>
		<div id="content">
			<form method="post" name="myForm" action="?do=target&w=<?php echo $w?>&d=<?php echo $d?>&store=<?php echo $store?>&form=1">
				Introduce el objetivo para la semana <?php echo $w?>:<br/><br/>
				<input type="text" name="value" class="input" value="<?php echo $value?>"><br/><br/>
				<input type="submit" class="button white" value="Aceptar">
			</form>
		</div>
		<?php
		}else{
		?>
		<div id="content">
			<form method="post" name="myForm" action="?do=target&m=<?php echo $m?>&d=<?php echo $d?>&store=<?php echo $store?>&form=1">
				Introduce el objetivo para el mes de <b><?php echo $month[$m] ?></b>:<br/><br/>
				<input type="text" name="value" class="input" value="<?php echo $value?>"><br/><br/>
				<input type="submit" class="button white" value="Aceptar">
			</form>
		</div>
		<?php
		}
	}
		
}else{
    $w =$_GET['w'];
	$d =$_GET['d'];
	$store=$_GET['store'];
	$value =str_replace('.','',$_GET['value']);
	$value =str_replace(',','.',$value);
	if($_GET['form']==1){
		
		$value=$_POST['value'];
		$w =$_GET['w'];
		$d =$_GET['d'];
		
		$date =time();
	
		if((!is_numeric($value))){
			
			
			echo '<div class="notification">Revisa los datos</div>';
			?>
			<div id="content">
			<form method="post" name="myForm" action="?do=control&w=<?php echo $w?>&d=<?php echo $d?>&store=<?php echo $store?>&form=1">
				Introduce la cantidad para el <?php echo $d?>:<br/><br/>
				<input type="text" name="value" class="input" value="<?php echo $value?>"><br/><br/>
				<input type="submit" class="button white" value="Aceptar">
			</form>
			</div>
			<?php

		}else{
		//echo $w." ".$year[0];
					if($value==0){
						mysql_query("DELETE FROM `feed_control` WHERE `day`='".$d."' AND `id_week`='".$w."' AND `store`='".$store."' ")or die(mysql_error());

					
						?>
						<div class="green">Los datos han sido insertados, puede cerrar la ventana</div>
				<?php
					}else{
			$year=explode('-',$d);
			$ticket=mysql_query("SELECT * FROM `feed_control` WHERE `day`='".$d."' AND `id_week`='".$w."' AND `store`='".$store."' ")or die(mysql_error());
			
			if(mysql_num_rows($ticket)>0){
					mysql_query("UPDATE `feed_control` SET `value` = '".$value."' WHERE `day` ='".$d."' AND `id_week`='".$w."' AND `store`='".$store."'")or die(mysql_error());	
					?>
				<div class="green">Los datos han sido insertados, puede cerrar la ventana</div>
			<?php
			}else{
					mysql_query("INSERT INTO `feed_control` ( `id_week` , `day` ,`value` , `year` , `store` )
			VALUES ('".$w."', '".$d."', '".$value."', '".$year[0]."', '".$store."' );")
			or die(mysql_error());
			//ZONES
			?>
				<div class="green">Los datos han sido insertados, puede cerrar la ventana</div>
			<?php
		}
		}
			
		}
	}else{
		
		?>
		<div id="content">
			<form method="post" name="myForm" action="?do=control&w=<?php echo $w?>&d=<?php echo $d?>&store=<?php echo $store?>&form=1">
				Introduce la cantidad para el <?php echo $d?>:<br/><br/>
				<input type="text" name="value" class="input" value="<?php echo $value?>"><br/><br/>
				<input type="submit" class="button white" value="Aceptar">
			</form>
		</div>
		<?php
		
		
		
	}
}
?>

		
</body>
</html>