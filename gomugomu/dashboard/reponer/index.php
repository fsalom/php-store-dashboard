<?php 
error_reporting(0);
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
VersiÃ³n 2.0 con conexiÃ³n a BBDD de Becosoft directamente y de esa forma coger los datos evitando errores.
-->

<html>

<head>
	<title></title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/general.css" />
	<link rel="stylesheet" href="css/small.css" />	
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
		
		$referenceArticle["M10"] = "Camiseta";
		$referenceArticle["M11"] = "Polo";
		$referenceArticle["M20"] = "Sudadera";
		$referenceArticle["M30"] = "Bañador";
		$referenceArticle["M31"] = "Ropa interior";
		$referenceArticle["M40"] = "Camisa";
		$referenceArticle["M50"] = "Chaqueta";
		$referenceArticle["M60"] = "Camiseta tirante o Manga larga";
		$referenceArticle["M61"] = "Punto";
		$referenceArticle["M70"] = "Pantalon largo";
		$referenceArticle["M71"] = "Pantalon corto";
		$referenceArticle["M90"] = "Gorra";
		$referenceArticle["M91"] = "Mochila/Bolsa";
		$referenceArticle["M95"] = "Colonia";
		$referenceArticle["M97"] = "Gafas de sol";
		$referenceArticle["MF"] = "Zapato/chancla";
		
		$referenceArticle["G10"] = "Camiseta";
		$referenceArticle["G20"] = "Sudadera";
		$referenceArticle["G30"] = "Bañador";
		$referenceArticle["G31"] = "Ropa interior";
		$referenceArticle["G40"] = "Camisa";
		$referenceArticle["G50"] = "Chaqueta";
		$referenceArticle["G60"] = "Top / no manga corta";
		$referenceArticle["G61"] = "Punto";
		$referenceArticle["G70"] = "Pantalon largo / Alguno corto";
		$referenceArticle["G71"] = "Pantalon corto";
		$referenceArticle["G72"] = "Falda";
		$referenceArticle["G80"] = "Vestidos y monos";
		$referenceArticle["G91"] = "Mochila/Bolsa";
		$referenceArticle["G95"] = "Colonia";
		$referenceArticle["G97"] = "Gafas de sol";
		$referenceArticle["GF"] = "Zapato/chancla";
		
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
			<div class="item">
				[ <b><?php echo $tickets['id_ticket'][$i] ?></b> ] <?php echo $tickets['date'][$i] ?> 
			
				<div class="paid">
					<?php echo $BECOSOFT->getTicketTotal($tickets['id_ticket'][$i])?>â‚¬
				</div>
					
				<?php 				
				for($c=0;$c<$item['count'];$c++){				
					if($item[$c]->info->stock==''){
						$colorItem="#ffecec";
						$item[$c]->info->stock = 0;
						$colorBorder = "#f5aca6";
					}else{
						$colorItem="#e9ffd9";						
						$colorBorder= "#a6ca8a";
					}
					$sex =  substr($item[$c]->info->reference,0,1);
					$category =  substr($item[$c]->info->reference,1,2);
					
					if($sex =="M" || $sex=="G"){
						$extra = $referenceArticle[$sex.$category];											
					}else{
						$extra = "Accesorio";
					}
				?>
					<div class="item_detail" style="background-color:<?php echo $colorItem?>; border:1px solid <?php echo $colorBorder?>;">					
							<div class="col s40"><?php echo $item[$c]->info->name?><br/><b><?php echo $extra?></b></div>									
							<div class="col s30"><?php echo $item[$c]->info->colour?></div>
							<div class="col s10"><b><?php echo $item[$c]->info->stock?> x <?php echo $item[$c]->info->size?></b></div>
							<div class="col s10"><?php echo $item[$c]->info->reference?></div>
							<div class="col s5"><?php echo $item[$c]->info->season?></div>								
							<div class="clear"></div>
										
					</div>
				<?php				
				}
				?>			
				
			</div>
	<?php 
		}
	?>	
	<div>			
</div>
	 
<script type="text/javascript">
	/*-----------------------------------------
					PARA LOS MENUS
	-----------------------------------------*/
	$(".item").click(function(){
		$(".item_detail").hide();
		$(this).find(".item_detail").show();
	});

</script>
</body>
</html>
