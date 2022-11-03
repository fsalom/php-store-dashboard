<?php 
error_reporting(E_ALL);
include("include/config.php");
include("include/class.FUNCIONES.php");
include("include/class.ADMIN.php");
include("include/class.APOYO.php");
include("include/class.BECOSOFT.php");
include("include/class.GOMUGOMU.php");
include("include/class.FORM.php");

$fecha=$_GET["fecha"];
//$fecha="21-02-2015";
if($fecha==""){
	$fecha = date("d-m-Y", time());
}

$GOMUGOMU  = new GOMUGOMU();
$BECOSOFT  = new BECOSOFT();
$FUNCIONES = new FUNCIONES();
$FORM	   = new FORM();
$ADMIN	   = new ADMIN(date("W",strtotime($fecha)), date("Y",strtotime($fecha)));
?>
<!DOCTYPE html>
<!--
Versión 2.0 con conexión a BBDD de Becosoft directamente y de esa forma coger los datos evitando errores.
-->

<html>

<head>
	<title></title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/general.css" />
	<link rel="stylesheet" media="screen and (max-width: 600px)" href="css/small.css" />
	<link rel="stylesheet" media="screen and (min-width: 600px)" href="css/large.css" />
	<link rel="stylesheet" type="text/css" href="css/button.css" media="screen" />
	
	
	<link href="css/jquery.circliful.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="js/Chart.js"></script>
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.circliful.js"></script>

	
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>

</head>
<body>

<div class="ball-arc">
	</a><div class="point"><a href="index.php"><img src="img/gomu.png" alt="Gomu Gomu"></a></div>
</div>



<div id="content">
	<?php 			
		
		$tickets = $BECOSOFT->getTicketsDay($fecha);
		$today	 = $BECOSOFT->getInfoDay($fecha,$today);
		$lastday = $GOMUGOMU->getSameDay($fecha);
		$week	 = $GOMUGOMU->get_data_week($fecha,$today);
		$week1	 = $GOMUGOMU->get_data_week($fecha, "", $date['Y']-1);
		$week2	 = $GOMUGOMU->get_data_week($fecha, "", $date['Y']-2);
		
		
		$count = count($tickets['id_ticket']);
		for($i=0;$i<$count;$i++) {
			$item	 = $BECOSOFT->getTicketDetails($tickets['id_ticket'][$i]);
			$payment = $BECOSOFT->getTicketPayment($tickets['id_ticket'][$i]);
			
			$answer  = $FORM->get_answers($tickets['id_ticket'][$i]);
			if($answer->updated==true)
				$color='#FFD076';
			else
				$color='';
			?>
			<div class="item" style="background-color:<?php echo $color ?>">
				[ <b><?php echo $tickets['id_ticket'][$i] ?></b> ] <?php echo $tickets['date'][$i] ?> <?php echo $answer->answer?>
			
				<div class="paid">
					<?php echo $BECOSOFT->getTicketTotal($tickets['id_ticket'][$i])?>€
				</div>
			
				<?php 				
				for($c=0;$c<$item['count'];$c++){
				?>
					<div class="item_detail">				
						<div class="item_price">
										<?php echo $item[$c]->price?> €<br/>
									<h3><?php echo $item[$c]->percentage?> %</h3>
									<h4><?php echo $item[$c]->margin->money?> €</h4>
									<h4><?php echo $item[$c]->margin->percentage?>%</h4>
						</div>					
									    <?php echo $item[$c]->info->name?><br/>
						Color: 		 <b><?php echo $item[$c]->info->colour?></b><br/>
						Talla: 		 <b><?php echo $item[$c]->info->size?></b><br/>
						Referencia:  <b><?php echo $item[$c]->info->reference?></b><br/>
						Temporada: 	 <b><?php echo $item[$c]->info->season?></b><br/>
									 <i><?php echo $item[$c]->return?></i>
					</div>
				<?php 		
				}
				?>			
			</div>
	<?php 
		}
	?>	
	<div>
		<div class="capsule">		
			Total hoy <br/>
			<div class="data p1">
				<?php echo $today->total?>€
			</div>
			<div class="data p2">
				<?php echo $today->margin?>€
			</div>		
			<div class="data p4">
				<?php echo $today->desc?>%
			</div>			 
			<div class="data p5">
				 <?php echo $today->nArticles?>p
			</div>
			<div class="data p6">
				<?php echo $today->nTickets?>t
			</div>
			<div class="data p7">
				<?php echo $today->perTicket?>pt
			</div>
			<div class="data p8">
				 <?php echo $today->averageTicket?>€
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="capsule">
		Objetivo <br/>
		<?php 
		if($ADMIN->target=="" ){
			echo "<b>No se ha definido un objetivo</b>";	
		}else{?>		
		<div class="circlestat" 
			data-dimension="56"
			data-text="<?php echo $ADMIN->target ?>€"
			data-width="0.1"
			data-fontsize="11"
			data-percent="<?php (($week->total/$ADMIN->target)*100) //necesito el total en RAW sin . ni ,?>"
			data-fgcolor="<?php echo '#333' ?>"
			data-bgcolor="#eee"
			data-fill="#ddd"></div>
		<div class="circlestat1" 
			data-dimension="56"
			data-text="<?php echo $data['objetivo']['t_desc']['value'] ?>%"
			data-width="0.1"
			data-fontsize="11"
			data-percent="<?php echo $data['objetivo']['t_desc']['per'] ?>"
			data-fgcolor="<?php echo $data['objetivo']['t_desc']['color'] ?>"
			data-bgcolor="#eee"
			data-fill="#ddd"></div>
		<div class="circlestat2" 
			data-dimension="56"
			data-text="<?php echo $data['objetivo']['t_items']['value'] ?>pt"
			data-width="0.1"
			data-fontsize="11"
			data-percent="<?php echo $data['objetivo']['t_items']['per'] ?>"
			data-fgcolor="<?php echo $data['objetivo']['t_items']['color'] ?>"
			data-bgcolor="#eee"
			data-fill="#ddd"></div>
		<div class="circlestat3" 
			data-dimension="56"
			data-text="<?php echo $data['objetivo']['t_tickets']['value'] ?>€"
			data-width="0.1"
			data-fontsize="11"
			data-percent="<?php echo $data['objetivo']['t_tickets']['per'] ?>"
			data-fgcolor="<?php echo $data['objetivo']['t_tickets']['color'] ?>"
			data-bgcolor="#eee"
			data-fill="#ddd"></div>
		<div class="circlestat4" 
			data-dimension="56"
			data-text="<?php echo $data['objetivo']['women']?>%"
			data-width="0.1"
			data-fontsize="11"
			data-percent="<?php echo $data['objetivo']['t_women']['per']?>"
			data-fgcolor="<?php echo $data['objetivo']['t_women']['color'] ?>"
			data-bgcolor="#eee"
			data-fill="#ddd"></div>
		<div id="canvas-holder">
			<!-- <canvas id="chart-area" width="500" height="500"/>-->
		</div>
	<?php } ?>
		<div class="clear"></div>
		
		<div class="capsule">
			<?php
			if($lastday->total==0){
			?>
				El año pasado no abrimos este dia
			<?php
			}else{
			?>
			
			Año pasado <br/>
			<div class="stats">	
			<div class="data p1">
				<?php echo $lastday->total?>€
			</div>
			<div class="data p2">
				<?php echo $lastday->margin?>€
			</div>		
			<div class="data p4">
				<?php echo $lastday->desc?>%
			</div>			 
			<div class="data p5">
				 <?php echo $lastday->nArticles?>p
			</div>
			<div class="data p6">
				<?php echo $lastday->nTickets?>t
			</div>
			<div class="data p7">
				<?php echo $lastday->perTicket?>pt
			</div>
			<div class="data p8">
				 <?php echo $lastday->averageTicket?>€
			</div>
			<div class="clear"></div>
			</div>
			<?php
			}
			?>
		</div>
		
		<div class="capsule">	
		Esta semana <br/>
			<div class="stats">					
			<div class="data p1">
				<?php echo $week->total?>€
			</div>
			<div class="data p2">
				<?php echo $week->margin?>€
			</div>		
			<div class="data p4">
				<?php echo $week->desc?>%
			</div>			 
			<div class="data p5">
				 <?php echo $week->nArticles?>p
			</div>
			<div class="data p6">
				<?php echo $week->nTickets?>t
			</div>
			<div class="data p7">
				<?php echo $week->perTicket?>pt
			</div>
			<div class="data p8">
				 <?php echo $week->averageTicket?>€
			</div>
			<div class="clear"></div>	
			</div>		
		</div>
			
		<div class="capsule">			
			Semaña de hace 1 año <br/>
			<div class="stats">	
			<div class="data p1">
				<?php echo $week1->total?>€
			</div>
			<div class="data p2">
				<?php echo $week1->margin?>€
			</div>		
			<div class="data p4">
				<?php echo $week1->desc?>%
			</div>			 
			<div class="data p5">
				 <?php echo $week1->nArticles?>p
			</div>
			<div class="data p6">
				<?php echo $week1->nTickets?>t
			</div>
			<div class="data p7">
				<?php echo $week1->perTicket?>pt
			</div>
			<div class="data p8">
				 <?php echo $week1->averageTicket?>€
			</div>
			<div class="clear"></div>	
			</div>		
		</div>
		
		<div class="capsule">			
			Semaña de hace 2 años <br/>
			<div class="stats">	
			<div class="data p1">
				<?php echo $week2->total?>€
			</div>
			<div class="data p2">
				<?php echo $week2->margin?>€
			</div>		
			<div class="data p4">
				<?php echo $week2->desc?>%
			</div>			 
			<div class="data p5">
				 <?php echo $week2->nArticles?>p
			</div>
			<div class="data p6">
				<?php echo $week2->nTickets?>t
			</div>
			<div class="data p7">
				<?php echo $week2->perTicket?>pt
			</div>
			<div class="data p8">
				 <?php echo $week2->averageTicket?>€
			</div>
			<div class="clear"></div>
			</div>			
		</div>
	</div>
</div>
	 
<script type="text/javascript">
	/*-----------------------------------------
					PARA LOS MENUS
	-----------------------------------------*/
	$(".item").click(function(){
		$(".item_detail").hide();
		$(this).find(".item_detail").show();
	});

	$(function() {
	    $('.selects').change(function() {
	         $('#sendSelect').submit();
	    });
	});
	/*-----------------------------------------
	CIRCLESTAT
	-----------------------------------------*/
	$(function(){
	$('.circlestat').circliful();
	$('.circlestat1').circliful();
	$('.circlestat2').circliful();
	$('.circlestat3').circliful();
	$('.circlestat4').circliful();
	});
	$(function() {
		$(".circlestat1").click(function(){
			$(".hidden_field").show();
			$("#prendas").hide();
			$("#tickets").hide();
			$("#estadisticas").hide();
			$("#margen").show();
		});
		$(".circlestat3").click(function(){
			$(".hidden_field").show();
			$("#prendas").hide();
			$("#margen").hide();
			$("#estadisticas").hide();
			$("#tickets").show();
		});
		$(".circlestat2").click(function(){
			$(".hidden_field").show();
			$("#tickets").hide();
			$("#margen").hide();
			$("#estadisticas").hide();
			$("#prendas").show();
		});
		$(".circlestat4").click(function(){
			$(".hidden_field").show();
			$("#tickets").hide();
			$("#margen").hide();
			$("#prendas").hide();
			$("#estadisticas").show();
		});
		$("#canvas-holder").click(function(){
			$(".hidden_field").show();
			$("#tickets").hide();
			$("#margen").hide();
			$("#prendas").hide();
			$("#estadisticas").show();
		});
		});
</script>
</body>
</html>