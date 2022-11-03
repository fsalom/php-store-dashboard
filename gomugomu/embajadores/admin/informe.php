<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<link rel="stylesheet" type="text/css" href="informe.css" media="screen" />
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


$aw12=4249;
$gastos=12926;

	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion){
		$API = new API();
		die($API->printerror(_MYSQLCONNECT));
	}
	mysql_select_db(_BD, $conexion);
?>

<div id="main">
	<div id="main-title">Informe AW12</div>
	<div class="clear"></div>

		<?php
			
			$subtotal=mysql_query("SELECT SUM(items),SUM(subtotal),SUM(subtotalOriginal),SUM(priceBefore) FROM `feed_ticket` WHERE season='AW12'");
			while($dato=mysql_fetch_array($subtotal)){
				$prendas=number_format($dato['SUM(items)'],0,',','.');
				$precioReal_=$dato['SUM(subtotal)'];
				$precioReal=number_format($dato['SUM(subtotal)'],2,',','.');
				$precioCompleto=number_format($dato['SUM(subtotalOriginal)'],2,',','.');
				$coste_=$dato['SUM(subtotalOriginal)']/2.94;
				$coste=number_format($dato['SUM(subtotalOriginal)']/2.94,2,',','.');
				$iva_=$dato['SUM(subtotal)']-($dato['SUM(subtotal)']/1.21);
				$iva=number_format($dato['SUM(subtotal)']-($dato['SUM(subtotal)']/1.21),2,',','.');
				$porcentajeIVA=($dato['SUM(subtotal)']-($dato['SUM(subtotal)']/1.21))/$dato['SUM(subtotal)'];
				$porcentajeCoste=($dato['SUM(subtotalOriginal)']/2.94)/$dato['SUM(subtotal)'];
				$porcentajeBeneficio=($dato['SUM(subtotal)']-($dato['SUM(subtotalOriginal)']/2.94)-($dato['SUM(subtotal)']-($dato['SUM(subtotal)']/1.21)))/$dato['SUM(subtotal)'];
			}

			
			$porcentajeIVA=round($porcentajeIVA*100,2);
			$porcentajeBeneficio=round($porcentajeBeneficio*100,2);
			$porcentajeCoste=round($porcentajeCoste*100,2);
			
			$porcentajePrendas=($prendas/number_format($aw12,0,',','.'))*100;
			$porcentajePrendas=round($porcentajePrendas,2);
			$porcentajePrendasSobrantes=100-$porcentajePrendas;
		
			$beneficio=$precioReal_-$coste_-$iva_;
			
			
			 
		?>
		<h1>Ropa</h1>
		<div style="background-color:#cb5454; text-align:center;">
				<div style="width:<?php echo $porcentajePrendas?>%; background-color:#99cc33; float:left;padding:10px 0;">
				 	Prendas vendidas: <strong><?php echo $porcentajePrendas?>% - <?php echo $prendas?></strong> 
				</div>
			<div style="padding:10px 0; margin:0 auto;">Prendas sobrantes: <strong><?php echo $porcentajePrendasSobrantes?>% -<?php echo number_format($aw12,0,',','.')-$prendas?></strong></div>
			<div class="clear"></div>
		</div>
		
		<h1>Reparto ingresos</h1>
		<div style="background-color:#99cc33; text-align:center;">
			<div style="padding:10px 0; margin:0 auto;">Total ingresos: <strong><?php echo $precioReal ?> €</strong></div>
		</div>
<br/>
		<div style="background-color:#99cc33; text-align:center;">
			<div style="width:<?php echo $porcentajeIVA?>%; background-color:#cb5454; float:left;padding:10px 0;"> IVA: <strong><?php echo $iva?> €</strong> </div> 
			<div style="width:<?php echo $porcentajeCoste?>%; background-color:#e47b7b; float:left;padding:10px 0;  "> Coste: <strong><?php echo $coste?> €</strong></div>
			<div style="padding:10px 0; margin:0 auto;">Beneficio bruto: <strong><?php echo $precioReal-$coste-$iva?> €</strong></div>
			<div class="clear"></div>
		</div>
		
		
		<h1>Balance</h1>
		<div style="background-color:#cb5454; text-align:center;">
			<div style="padding:10px 0; margin:0 auto;">Total Gastos fijos 6 meses: <strong><?php echo number_format(($gastos*6),2,',','.') ?> €</strong></div>
		</div>
		<br/>
		<div style="background-color:#cb5454; text-align:center;">
			<div style="width:<?php echo ($beneficio/($gastos*6))*100?>%; background-color:#99cc33; float:left;padding:10px 0;"> Beneficio bruto: <strong><?php echo number_format($precioReal_-$coste_-$iva_,2,',','.')?> €</strong> </div> 
			<div style="padding:10px 0; margin:0 auto;">Diferencia: <strong><?php echo number_format(($gastos*6)-($precioReal_-$coste_-$iva_),2,',','.')?> €</strong></div>
			<div class="clear"></div>
		</div>
		
		<h1>Hombre vs Mujer</h1>
		<?php 
			$search_man=mysql_query("SELECT SUM(items) FROM `feed_ticket` WHERE  season='aw12'")
					or die(mysql_error());
					while($dato=mysql_fetch_array($search_man)){
						$num_prendas=$dato['SUM(items)'];
					}
			$search_man=mysql_query("SELECT SUM(items) FROM `feed_ticket` WHERE referencenr LIKE 'MS%' and season='aw12'")
					or die(mysql_error());
					while($dato=mysql_fetch_array($search_man)){
						$man=$dato['SUM(items)'];
					}
			$search_woman=mysql_query("SELECT SUM(items) FROM `feed_ticket` WHERE referencenr LIKE 'GS%' and season='aw12'")
					or die(mysql_error());
					while($dato=mysql_fetch_array($search_woman)){
						$woman=$dato['SUM(items)'];
					}
			$search_acc=mysql_query("SELECT SUM(items) FROM `feed_ticket` WHERE referencenr LIKE 'US%' and season='aw12'")
					or die(mysql_error());
					while($dato=mysql_fetch_array($search_acc)){
						$acc=$dato['SUM(items)'];
					}
		?>
		<div style="background-color:#CCC; text-align:center;">
			<div style="width:<?php echo (($man/$num_prendas)*100)?>%; background-color:#9ad1f4; float:left;padding:10px 0;"> Hombre: <strong><?php echo number_format($man,0,',','.')." - ".round((($man/$num_prendas)*100),2)."%"?> </strong> </div> 
			<div style="width:<?php echo (($woman/$num_prendas)*100)?>%; background-color:#f7d8f5; float:left;padding:10px 0;  "> Mujer: <strong><?php echo number_format($woman,0,',','.')." - ".round((($woman/$num_prendas)*100),2)."%"?></strong></div>
			<div style="padding:10px 0; margin:0 auto;"> <strong><?php echo round((($acc/$num_prendas)*100),2)."%" ?></strong></div>
			<div class="clear"></div>
		</div>

		
		<h1>Grupo de prendas más vendidas</h1>
		
		<?php
		$max=0;
		for($i=1;$i<4;$i++){
			if($i==1)$sex="MS";
			else if($i==2) $sex="GS";
			else $sex="US";
			
			for($x=0;$x<11;$x++){
				
				if($x!="10")$type=$x;
				else $type="K";
				$search=mysql_query("SELECT SUM(items) FROM `feed_ticket` WHERE referencenr LIKE '".$sex.$type."%' and season='aw12'")
						or die(mysql_error());
				
				while($dato=mysql_fetch_array($search)){
					if($dato['SUM(items)']!=0){
						if($max<$dato['SUM(items)'])
							$max=$dato['SUM(items)'];
						$total+=$dato['SUM(items)'];
					}
				}	
							
			}
			
		}
		
		?>
		<div style="text-align:left;">
		<?
		for($i=1;$i<4;$i++){
			if($i==1)$sex="MS";
			else if($i==2) $sex="GS";
			else $sex="US";
			
			for($x=0;$x<11;$x++){
				
				if($x!="10")$type=$x;
				else $type="K";
				$search=mysql_query("SELECT SUM(items) FROM `feed_ticket` WHERE referencenr LIKE '".$sex.$type."%' and season='aw12'")
						or die(mysql_error());
				
				while($dato=mysql_fetch_array($search)){
					if($dato['SUM(items)']!=0){
						
						echo '<div style="font-weight:bold; padding:10px 0; margin:2px 0; width:'.(($dato['SUM(items)']/$max)*100).'%; background-color:#EEE;">'.$sex.$type." - ".$dato['SUM(items)']."</div>";
					}
				}	
							
			}
			
		}
		$total=number_format($total,0,',','.');
		echo '<div style="font-weight:bold;padding:10px 0; margin:2px 0; width:'.((100/$max)*100).'%; background-color:#EEE;">Otros - 100</div>';
		?>
		</div>
			
</div>
		<br/>
</body>
</html>