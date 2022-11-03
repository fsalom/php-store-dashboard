<?php
	error_reporting(E_ALL);

include("include/config.php");
include("include/class.SHARE.php");
include("include/class.APOYO.php");
include("include/class.BECOSOFT.php");
include("include/class.STATS.php");
include("sellers/class.SELLER.php");
include("include/class.TARGET.php");
//include("../tcuento/index.php");
$BECOSOFT  = new BECOSOFT();
$SELLER  = new SELLERS();
$STATS   = new STATS();
$TARGET   = new TARGET();

$setDay  = 'd';
$setWeek = 'W';
$setMonth = 'm';
switch ($_GET['action']) {
	case 'set_target_day_quantity':
		$TARGET->setTarget($setDay,'value',$_POST["day_quantity"]);
		break;
	default:
		# code...
		break;
}
?>
<!DOCTYPE html>
<!--
Versión 2.0 con conexión a BBDD de Becosoft directamente y de esa forma coger los datos evitando errores.
-->

<html>

<head>
	<title>BUYERS</title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/general.css" />
	<link rel="stylesheet" media="screen and (max-width: 600px)" href="css/small.css" />
	<link rel="stylesheet" media="screen and (min-width: 600px)" href="css/large.css" />
	
	
	<link href="css/jquery.circliful.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="js/Chart.js"></script>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.circliful.js"></script>

	<style>
		.col{
			float:left;
		}
		.s5{
			width:3%;
			padding:1%;
		}
		.s10{
			width:8%;
			padding:1%;
		}
		.s15{
			width:13%;
			padding:1%;
		}
		.s20{
			width:18%;
			padding:1%;
		}
		.s25{
			width:23%;
			padding:1%;
		}
		.s30{
			width:28%;
			padding:1%;
		}
		.s35{
			width:23%;
			padding:1%;
		}
		.s40{
			width:38%;
			padding:1%;
		}
		.s60{
			width:58%;
			padding:1%;
		}
		.s70{
			width:68%;
			padding:1%;
		}
		.s80{
			width:78%;
			padding:1%;
		}
		.small{
			font-size: 10px;
		}
		#main-stock{
			width: 100%;
			margin: 0 auto;
		}
	</style>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>

	<script>
	$("document").ready(function(){
		$(".cerrar").click(function () {
			$(".popup").hide();
		} );
	 	
	 	$('input:text').focus(function(){
        	$(this).val('');
    	});

	$('.reference').click(function(){    
	    return false;
    });


		});
	</script>
</head>
<body>

<div class="popup">
	<div class="cerrar"><a href="#">CERRAR</a></div>
	 <iframe id="frame" src="" width="100%" height="100%" scrolling="yes">
     </iframe>
</div>

<div class="ball-arc">
	</a><div class="point"><a href="index.php"><img src="img/gomu.png" alt="Gomu Gomu"></a></div>
</div>


<?php
include_once("include/support.php");
date_default_timezone_set('Europe/Madrid');
$data=get_data($_GET['date']);
foreach ($data as $ticket) {
    echo $tickets;
}

$forDate = $_GET['date'];
$enable_fields = true;
if($forDate == ""){
	$forDate = date("Y-m-d",time());
}else{
	//Si pasamos por GET ?date=24-2-2014 ira a ese dia
	$token=explode('-',$_GET['date']);
	$forDate=$token[2].'-'.$token[1].'-'.$token[0];
	$enable_fields = false;
}

?>
<div id="main-stock">
		<div id="content">
			<form method="post" action="?send=on" id="sendSelect">
			<?php
				$num=count($data['id_ticket']);
				$BECOSOFT->connect();
				for ($i=0;$i<$num;$i++){
			?>

				<div class="item" style="background-color:<?php echo $color ?>">
					<div class="item-content">
					[ <b><?php echo $data['id_ticket'][$i] ?></b> ] 
					<?php echo $data['date'][$i] ?> 
					</div>
					<div class="paid">
						<?php echo $data['total'][$i] ?> €
					</div>
					
					<div class="clear"></div>
					<div class="SHOW">
					<?php 

						$items=count($data['items'][$i]);
						for ($c=0;$c<$items;$c++){
							echo $data['items'][$i][$c]['stock'];
							$items_data	 = $BECOSOFT->getStock($data['items'][$i][$c]['reference'], $data['items'][$i][$c]['color']);
							
		$count = count($items_data);		
		if ($count == 0){
			?>
			<div class="item-line" style="background-color: <?php echo $color_highlighted; ?>">		
				<div class="col s60"> <b>AGOTADO en color: </b><?php echo $data['items'][$i][$c]['color']; ?> </div>
				<div class="clear"></div>
			</div>
			<?php
		}
		for($z=0;$z<$count;$z++) {
	?>
	<?php
		$color_highlighted = "#f1f1f1";
		if ($data['items'][$i][$c]['size'] ==  $items_data[$z]->size){
			$color_highlighted = "#ccffcc";
		}
		?>
		<div class="item-line" style="background-color: <?php echo $color_highlighted; ?>">		
			<div class="col s10">
				<?php echo $items_data[$z]->season;?>
			</div>
			<div class="col s70">
				<?php echo $items_data[$z]->colour;?>
			</div>		
			<div class="col s10">
				<?php 
				if ($data['items'][$i][$c]['size'] ==  $items_data[$z]->size){
					echo '<b>'.$items_data[$z]->size.'</b>';
				}else{
					echo $items_data[$z]->size;
				}
				?>
			</div>		
			<div class="col s10">
				<?php echo $items_data[$z]->stock;?>
			</div>
			<div class="clear"></div>
		</div>
	<?php 							
		}
		?>
					
	<?php } ?>	
		</div>
		</div>
		<?php } 
			$BECOSOFT->close();	?>
				</form>		
	</div>
	<div class="clear"></div>
</div>
	<script type="text/javascript">
	/*-----------------------------------------
					PARA LOS MENUS
	-----------------------------------------*/
	$(".item").click(function(){
		$(".item_detail").hide();
		$(".SHOW").hide();
		$(".livesearch").empty();
			
		$(this).find(".item_detail").show();
		$(this).find(".SHOW").show();
		$(this).find(".search").val('');
		$(this).find(".livesearch").empty();
	});

	$(function() {
	    $('.selects').change(function() {	    			   	
			$('#sendSelect').submit();			
	    });
	    
	});
	    			
	</script>

</body>
</html>
