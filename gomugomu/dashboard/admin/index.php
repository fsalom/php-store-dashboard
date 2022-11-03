<!DOCTYPE html>
<!--
Versión 2.0 con 
-->
<?php 
	error_reporting(0);
	include_once("include/support.php");
	// Recogemos los datos de los fomularios para objetivos
	//echo $_GET['target'].' '.$_POST['value'].' '.$_POST['date'];
	if($_GET['target']=='month'){
		if(($_POST['date']=='')||($_POST['date']=='m')) $date='m-'.date('Y',time()).'-'.date('n',time());
		else $date=$_POST['date'];
		update_target($_POST['value'],$date);
	}else if($_GET['target']=='week'){
		if(($_POST['date']=='')||($_POST['date']=='W')) $date='W-'.date('Y',time()).'-'.date('W',time());
		else $date=$_POST['date'];
		update_target($_POST['value'],$date);
	}
?>
<html>

<head>
	<title>Administrador</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
		
	<link rel="stylesheet" href="css/general.css" />
<link rel="stylesheet" media="screen and (max-width: 600px)" href="css/small.css" />
	<link rel="stylesheet" media="screen and (min-width: 600px)" href="css/large.css" />
	<link rel="stylesheet" media="print" href="print.css" />	
</head>
<body>

	<?php
		$data=explode('-',$_GET['option']);
		$option=$data[0];
		$n=$data[2];
		$y=$data[1];	
		$value=get_target($option,$n,$y);
	?>
	<div id="content">
		<div id="top">
			<img src="img/gomu.png">
		</div>
		
		<div id="menu">
			<ul>
				<li><a href="?option=m">Mes</a></li>
				<li><a href="?option=W">Semana</a></li>
			</ul>
		</div>
		
		<div class="clear"></div>
		
		<div id="select">
		<?php	
			get_option($option,$n,$y);					
		?>
		</div>
	
		<div class="clear"></div>
		<?php		
			
		?>
	
		<?php
			$graph = target_graph($option,$n,$y);
			
			if($n!=''){
				get_stats($option,$n,$y);
				if($option=='W')
					echo $graph['week'];
				$month=date('m',time());
				$week=date('W',time());
				$year=date('y',time());
			}else{
				if($option=='W'){
					$n=date('W',time());
					$y=date('Y',time());
					get_stats($option,$n,$y);
					$graph = target_graph('W',$n,$y);
					$week=$n;
					$year=date('y',time());
					echo $graph['week'];
				}else{
					$n=date('m',time());
					$y=date('Y',time());
					$month=$n;
					$year=date('y',time());
					get_stats('m',$n,$y);
					$graph = target_graph('m',$n,$y);
					$graph2 = target_graph2('m',$n,$y-1);
				}
			}
		?>
				
	
		<div class="clear"></div>
		<div class="targetw">
				<form method="post" action="?target=week">
					<input type="number" class="input-text" name="value" min="1000" maxlength="5" value="<?php echo $graph['week_raw']?>">
					<input type="hidden" name="date" value="<?php echo $_GET['option'] ?>">
					<input type="submit" value="Guardar">
				</form>
			</div> 
		<div class="clear"></div>
		<?php
			if($graph['value_target']!='0,00'){
				$target=$graph['value_target'].'€';
			}else{
				$target='No se ha definido un objetivo';
			}
		?>
			<?php
			$value=get_info($option,$n,$y);
			$value1=get_info($option,$n,$y-1);
			?>	
		<br/>
			El acumulado del año es de: [ <b><?php echo $value['acc_real']?> € / <?php echo $value['acc_target']?> €</b>]<br/>
			Diferencia: [ <b class="<?php echo $value['acc_balance']['color']?>"><?php echo $value['acc_balance']['value']?> €</b>]<br/>
			El objetivo para este mes es de: [ <b class="value"><?php echo $target?></b>]
			
			<div class="target">
				<form method="post" action="?target=month">
					<input type="number" class="input-text" name="value" min="10000" maxlength="5" value="<?php echo $graph['value_raw']?>">
					<input type="hidden" name="date" value="<?php echo $_GET['option'] ?>">
					<input type="submit" value="Guardar">
				</form>
			</div> 
	
		<br/>
			
		<?php
			echo $graph['month'];
		?>		
		<?php 	
			//echo $month.' = '.date('m',time()).' | '.$y.'='.date('Y',time());
			if((!isset($_GET['option']))&&($n==date('m',time()))&&($y==date('Y',time()))){
				echo $graph2['month'];
				?>
				<br/>El año pasado hasta el momento ibamos así: [ <b> <?php echo $graph2['total']?> / <?php echo $graph2['value_target'] ?> </b> ]
				<?php 				
			}
		?>
		<div id="content-info">
			<div class="block l">
				<div class="col4">
					Facturación completo:
				</div>
				<div class="col4"> 
					 <b><?php echo $value['original']?> €</b> 
				</div>
				<div class="col4">
					 <b><?php echo $value1['original']?> €</b> 
				</div>
				<div class="col4">
					 <b><?php echo get_diff($value['original'],$value1['original'],'€')?></b> 
				</div>
				<div class="clear"></div>
			
				<div class="col4">
					Facturación real:
				</div>
				<div class="col4"> 
					 <b><?php echo $value['real']?> €</b> 
				</div>
				<div class="col4"> 
					 <b><?php echo $value1['real']?> €</b> 
				</div>
				<div class="col4"> 
					 <b><?php echo get_diff($value['real'],$value1['real'],'€')?></b> 
				</div>
				<div class="clear"></div>
				<div class="col4">
					Descuento medio:
				</div>
				<div class="col4"> 
					  <b><?php echo $value['discount']?> %</b> 
				</div>
				<div class="col4"> 
					  <b><?php echo $value1['discount']?> %</b> 
				</div>
				<div class="col4"> 
					  <b><?php echo get_diff($value['discount'],$value1['discount'],'%')?></b> 
				</div>
				<div class="clear"></div>
				
				<div class="col4">
					Coste de la ropa:
				</div>
				<div class="col4">
				 <b><?php echo $value['cost']?> €</b> 
				</div>
				<div class="col4">
					 <b><?php echo $value1['cost']?> €</b> 
				</div>
				<div class="col4">
					 <b><?php echo get_diff($value['cost'],$value1['cost'],'€')?></b> 
				</div>
				<div class="clear"></div>
				
				<div class="col4">
					IVA:
				</div>
				<div class="col4">
					 <b><?php echo $value['IVA']?> € </b> 
				</div>
				<div class="col4">
					  <b><?php echo $value1['IVA']?> € </b> 
				</div>
				<div class="col4">
					  <b><?php echo get_diff($value['IVA'],$value1['IVA'],'€')?> </b> 
				</div>
				<div class="clear"></div>
			
				<div class="col4">
					Margen bruto: 
				</div>
				<div class="col4">
					  <b><?php echo $value['margin']?> €</b> 
				</div>
				<div class="col4">
					  <b><?php echo $value1['margin']?> €</b> 
				</div>
				<div class="col4">
					  <b><?php echo get_diff($value['margin'],$value1['margin'],'€')?></b> 
				</div>
				<div class="clear"></div>
				
				<div class="col4">
					No tickets: 
				</div>
				<div class="col4">
					  <b><?php echo $value['tickets']?> </b> 
				</div>
				<div class="col4">
					  <b><?php echo $value1['tickets']?> </b> 
				</div>
				<div class="col4">
					  <b><?php echo get_diff($value['tickets'],$value1['tickets'],'')?> </b> 
				</div>
				<div class="clear"></div>
				
				<div class="col4">
					No prendas:
				</div>
				<div class="col4">
					  <b><?php echo $value['items']?> </b> 
				</div>
				<div class="col4">
					  <b><?php echo $value1['items']?> </b> 
				</div>
				<div class="col4">
					 <b><?php echo get_diff($value['items'],$value1['items'],'')?> </b> 
				</div>
				<div class="clear"></div>
			 
			 	<div class="col4">
					Prendas por ticket:
				</div>
				<div class="col4">
					  <b><?php echo $value['perticket']?> </b> 
				</div>
				<div class="col4">
					   <b><?php echo $value1['perticket']?> </b> 
				</div>
				<div class="col4">
					   <b><?php echo get_diff($value['perticket'],$value1['perticket'],'pt')?> </b> 
				</div>
				<div class="clear"></div>
		
				<div class="col4">
					Ticket medio:
				</div>
				<div class="col4">
					   <b><?php echo $value['average']?> € </b> 
				</div>
				<div class="col4">
					 <b><?php echo $value1['average']?> € </b> 
				</div>
				<div class="col4">
					   <b><?php echo get_diff($value['average'],$value1['average'],'€')?> </b> 
				</div>
				<div class="clear"></div>
				
			<br/>
			</div>
			<div class="block r">
				<?php 
				$now= stats($option,$n,$y);
				$last= stats($option,$n,$y-1);
	
				echo $now['country'];
				//echo $last['country'];
				echo $now['buyers'];
				//echo $last['buyers'];
				echo $now['sex'];
				?>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<script>
		$(".value").click(function(){
			$(".target").toggle();
		});
		$(".bar").click(function(){
			$(".targetw").toggle();
		});
	</script>

	</body>
</html>