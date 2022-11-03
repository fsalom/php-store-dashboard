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

$months=['enero','febrero','marzo','abril','mayo', 'junio','julio','agosto','septiebre','octubre', 'noviembre','diciembre'];

$setDay  = 'd';
$setWeek = 'W';
$setMonth = 'm';
$setYear = 'Y';
switch ($_GET['action']) {
	case 'set_target_day_quantity':
		$TARGET->setTarget($setDay,'value',$_POST["day_quantity"]);
		break;
	case 'set_target_day_items':
		$TARGET->setTarget($setDay,'items',$_POST["day_items"]);
		break;
	case 'set_target_day_conversion':
		$TARGET->setTarget($setDay,'conversion',$_POST["day_conversion"]);
		break;
	case 'set_target_average_ticket':
		$TARGET->setTarget($setDay,'average_ticket',$_POST["average_ticket"]);
		break;
	//WEEK TARGETS
	case 'set_target_week_quantity':
		$TARGET->setTarget($setWeek,'value',$_POST["week_quantity"]);
		break;
	case 'set_target_week_items':
		$TARGET->setTarget($setWeek,'items',$_POST["week_items"]);
		break;
	case 'set_target_week_conversion':
		$TARGET->setTarget($setWeek,'conversion',$_POST["week_conversion"]);
		break;
	case 'set_target_week_average_ticket':
		$TARGET->setTarget($setWeek,'average_ticket',$_POST["week_average_ticket"]);
		break;
	case 'set_target_month_quantity':
		$TARGET->setTarget($setMonth,'value',$_POST["month_quantity"]);
		break;
	case 'set_target_month_items':
		$TARGET->setTarget($setMonth,'items',$_POST["month_items"]);
		break;
	case 'set_target_month_average_ticket':
		$TARGET->setTarget($setMonth,'average_ticket',$_POST["month_average_ticket"]);
		break;
	case 'set_target_month_conversion':
		$TARGET->setTarget($setMonth,'conversion',$_POST["month_conversion"]);
		break;
	case 'set_target_year_quantity':
		$TARGET->setTarget($setYear,'value',$_POST["year_quantity"]);
		break;
	case 'set_target_year_conversion':
		$TARGET->setTarget($setYear,'conversion',$_POST["year_conversion"]);
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
	<meta http-equiv=”Content-Type” content=”text/html; charset=ISO-8859-1″ />
	<link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/general.css" />
	<link rel="stylesheet" media="screen and (max-width: 600px)" href="css/small.css" />
	<link rel="stylesheet" media="screen and (min-width: 600px)" href="css/large.css" />
	
	
	<link href="css/jquery.circliful.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="js/Chart.js"></script>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.circliful.js"></script>

	
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>

	<script>
	function getURLParameter(url, sParam){
	    var sPageURL = url;
	    var sURLVariables = sPageURL.split('&');
	    for (var i = 0; i < sURLVariables.length; i++){
	        var sParameterName = sURLVariables[i].split('=');
	        if (sParameterName[0] == sParam){
	            return sParameterName[1];
	        }
	    }
	}
	$("document").ready(function(){
		$(".stock").click(function () { 
			var a = $(this).data('reference'); 
			$(".popup").show();
    		$("#frame").attr("src", "https://www.gomugomu.es/dashboard/stockxreferencia/?text-search=" + a);
		});
		$(".cerrar").click(function () {
			$(".popup").hide();
		} );
	 	$(".search").keyup(function() {
			$(this).parent().children(".livesearch").empty();			
		    var data = {"action": "test", "id_ticket": $(this).parent().children(".id_ticket").val()};
		    data = $(this).serialize() + "&" + $.param(data);
		    $.ajax({
		      type: "POST",
		      dataType: "json",
		      url: "ajax_search.php", //Relative or absolute path to response.php file
		      data: data,
		      success: function(data) {	 		     	    							  
		    	  $(data["search"]).appendTo(".livesearch");		    	     
		      }
		    });
		    return false;
		  });
	 	$("a").click(function() {	
	 		var $element = $(this);	
	 		var url = $(this).attr("href");
    		var idTicket = getURLParameter(url, 'idTicket');
    		var idSeller = getURLParameter(url, 'idSeller');
    		var date     = getURLParameter(url, 'date');
    		var color    = getURLParameter(url, 'color');
    		var clicked  = getURLParameter(url, 'clicked');
    		//alert(idTicket + " " + idSeller + " "+ date + " " + color);
		    var data = {"action": "click", "idTicket": idTicket, "idSeller": idSeller, "date": date, "color": color};
		    data = $(this).serialize() + "&" + $.param(data);
		    $.ajax({
		      type: "POST",
		      dataType: "json",
		      url: "ajax_seller.php", 
		      data: data,
		      success: function(data) {	 
		      		//alert(color);
		      		if(data["color"] == ""){
		      			$element.css({ 'background-color': ""});
		      		}else{
		      			$element.css({ 'background-color': ""});
		      			$element.css({ 'background-color': "#" + data["color"]  }); 
		      		}     	     
		      }
		    });
		    return false;
		  });
	 	$('input:text').focus(function(){
        	$(this).val('');
    	});

	$('.reference').click(function(){    

	    return false;
    });


		});

jQuery(window).resize(function () {
    var width = jQuery(document).width();
    var height = jQuery(document).height();
    console.log(width);
    console.log(height);
    $('#main-buyers').css('height',height+'px');
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
<?php
//20181005 - Añadidos campo Vendedores para asignar a cada compra
$sellers = $SELLER->getAllSellers();
?>
<div id="main">
	<div id="main-seller">
		<?php
		foreach ($sellers as $seller) {
		?>
		<form method="post" action = "?seller_id=<?php echo $seller->id ?>">
		<button class="seller" style="background-color: <?php echo "#".$seller->color ?>">
			<div class="seller-name">
				<?php echo $seller->name; ?>
			</div>
		</button>
		</form>
		<?php
		}
		?>
	</div>
	<div id="main-buyers">
		<div id="content">
			<!--
				DESCOMENTAR PARA VER EL GRAFICO DE CANTIDAD QUE LLLEVAMOS EN EL MES
			<?php echo target_month($data['total_day'])?>
			-->
			<form method="post" action="?send=on" id="sendSelect">
			<?php
				
				if($_GET['do']=="insert")
					update_target($_GET['date'],$_POST['margin'],$_POST['items'],$_POST['tickets'],$_POST['hombre'],$_POST['mujer'],$_POST['acc'],$_GET['op']);
					
				switch ($_GET['send']){
					case 'on':
						update_answers($_POST['ticket'],$_POST['answer']);
					break;
					case 'client':			
						update_client($_GET['id_ticket'],$_GET['id_client']);
					break;
				}
					
				
				
				
				
				
				//20140801 - No esta completo creo que no hace lo que tiene que hacer no me acaban de encajar los resultados que da
				//get_margin_v2($_GET['date']);

				
				$ticketsForSeller = $SELLER->getTicketsSellersFor($forDate);
				$people  = $STATS->getTcuentoBy($forDate);
				$num_tickets_more_than_0 = 0;
				$num=count($data['id_ticket']);
				for ($i=0;$i<$num;$i++){
					$answer= get_answers($data['id_ticket'][$i]);
					$color='';
					$num_tickets_more_than_0 += $data['total'][$i] > 0 ? 1 : 0;
					if($answer['updated']==true)$color='#FFD076';
						
				
			?>

				<div class="item" style="background-color:<?php echo $color ?>">
					<div class="item-content">
					[ <b><?php echo $data['id_ticket'][$i] ?></b> ] 
					<?php echo $data['date'][$i] ?> 
					<?php echo $answer['print'] ?>
					</div>
					<div class="paid">
						<?php echo $data['total'][$i] ?> €
					</div>
			<!-- SELLER -->
			        <div class = "sellers">

			            <?php 
			            	//print_r($sellers);
			            	foreach ($sellers as $s) {
			            		$url = "&color=".$s->color."&idTicket=".$data['id_ticket'][$i]."&idSeller=".$s->id."&date=".$forDate;
			            		if($SELLER->containsID($s->id, $data['id_ticket'][$i], $ticketsForSeller)) {
			            			$url .= "&clicked=true";
			            ?>
					    			<a href = "<?php echo $url ?>"  onclick="return false;" class = "seller disabled" style="background-color: #<?php echo $s->color ?>" >
					    				<?php 
					    					echo $s->name;
					    					
					    				?>
					    			</a>
			            <?php
			            		}else{
			            			$url .= "&clicked=false";
			            ?>
					    			<a href = "<?php echo $url ?>"  onclick="return false;" class = "seller disabled" style="background-color: #E5E5E5" >
					    				<?php 
					    					echo $s->name;
					    					
					    				?>
					    			</a>
						<?php
								} //CLOSE IF
							}//CLOSE FOREACH
			             ?>

			        </div>
			<!-- FIN SELLER -->
					
					<div class="clear"></div>

					<?php if($answer['value']=="1"){?>
					<div class="SHOW">
						<?php if($answer['client_id']>0){?>
							<div class="profileClient"><?php echo $answer['client_name'].' '.$answer['client_surname']?></div>
						<?php }?>
							<input type="hidden" name="default_answer" class="default_answer" value="<?php echo $answer['value']?>"> 
							<input type="hidden" name="id_ticket" class="id_ticket" value="<?php echo $data['id_ticket'][$i] ?>">
							<input type="text"   name="search" class="search"  autocomplete="off">	
							<div class="livesearch"></div>
										
					</div>
					<?php }?>
					<?php 
						$items=count($data['items'][$i]);
						for ($c=0;$c<$items;$c++){ 
							
					?>
						<div class="item_detail">
							
							<div class="item_price">
								<?php echo  $data['items'][$i][$c]['subtotalOriginal']?> €<br/>
								<h3><?php echo  $data['items'][$i][$c]['percentage']?> %</h3>
								<h4><?php echo  $data['items'][$i][$c]['margen']?> €</h4>
								<h4><?php echo  $data['items'][$i][$c]['margen_per']?> %</h4>
							</div>
							<?php
							if($c==1){
							//$items_data	 = $BECOSOFT->searchReference($data['items'][$i][$c]['reference']);
							//print_r($items_data);	
							}?>
							<?php echo $data['items'][$i][$c]['aditional']?>
							<?php echo $data['items'][$i][$c]['devuelto']?>
							
							
						</div>
						
					<?php } ?>
					
						<div class="item_total">		
							<?php  //echo $data['total_day'][$i]  ?>
							<?php  //echo $data['margen'][$i]  ?>
							<?php  //echo $data['margen_per'][$i]  ?>
						</div>
						
				</div>
				
				
			<?php } ?>
				</form>		
			</div>
	</div>
	<div id="main-stats">
		<div id="content">
		<?php if($_GET['seller_id']==''){ ?>
		<?php $sameday=same_day(); ?>
					<div class = "capsule">
						<div class = "graph_target bold">
							Dia
						</div>
						<div class = "graph_target bold center">
							Año pasado
						</div>
						<div class = "graph_target bold center">
							Este año
						</div>
						<div class = "graph_target bold center">
							Objetivo
						</div>
						<div class="clear"></div>
					</div>
					<div class="capsule">
						<div class = "graph_target ">
							Facturación
						</div>
						<div class = "graph_target data_target">
							<?php echo number_format($sameday['qty_raw'],0,",",".")  ?>€
						</div>
						<div class = "graph_target data_target bold">
							<?php echo number_format($data['total_day_raw'],0,",",".") ?>€
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['day_quantity']== ''){
							?>
									<form method="post" action="?action=set_target_day_quantity" autocomplete="off">
										<input type="text" name="day_quantity" <?php echo ($enable_fields) ? "" : "disabled" ?> value="">
									</form>
							<?php
								}else{
									 echo setBar($data['total_day_raw'], $data['objetivo']['day_quantity'],"€",0);
								}
							?>
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Ticket Medio
						</div>
						<div class = "graph_target data_target">
							<?php echo round($sameday['total_day']/$sameday['rcp']) ?>€
						</div>
						<div class = "graph_target data_target bold">
							<?php echo "<b>".round($data['total_day_raw']/$data['tks'])."€</b>" ?>
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['average_ticket']== ''){
							?>
								<form method="post" action="?action=set_target_average_ticket" autocomplete="off">
									<input type="text" name="average_ticket" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>
							<?php
								}else{
									 echo setBar(round($data['total_day_raw']/$data['tks']), $data['objetivo']['average_ticket'],"€",0);
								}
							?>
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Prendas por ticket
						</div>
						<div class = "graph_target data_target">
							<?php echo round($sameday['pcs']/$sameday['rcp'],2)."pt" ?>
						</div>
						<div class = "graph_target data_target bold">
							<?php echo round($data['pcs']/$data['tks'],2)."pt</b>" ?>
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['day_items']== ''){
							?>
								<form method="post" action="?action=set_target_day_items" autocomplete="off">
									<input type="text" name="day_items" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>
							<?php
								}else{
									 echo setBar(round($data['pcs']/$data['tks'],2), $data['objetivo']['day_items'],"pt",2);
								}
							?>
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Conversión
						</div>
						<div class = "graph_target data_target">
							<?php 
							echo round((($sameday['rcp']/ $sameday['people'])*100),1, PHP_ROUND_HALF_UP)."%"
							?><br/>
							<i><?php echo  $sameday['people'] ?> Personas</i>
						</div>
						<div class = "graph_target data_target bold">
							<?php 
							echo "<b>".round((($num_tickets_more_than_0 / $people)*100),1, PHP_ROUND_HALF_UP)."%</b>"
							?><br/>
							<i><?php echo $people ?> Personas</i>
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['day_conversion']== ''){
							?>
								<form method="post" action="?action=set_target_day_conversion" autocomplete="off">
									<input type="text" name="day_conversion" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{
									 echo setBar(round((($num_tickets_more_than_0 / $people)*100),1, PHP_ROUND_HALF_UP), $data['objetivo']['day_conversion'],"%",0);
								}
							?>			
						</div>
						<div class="clear"></div>
					</div>
					
					<div class = "capsule">
						<div class = "graph_target bold">
							Semana
							<?php
								$presentWeek = date("W", time());
								$presentYear = date("Y", time());

								$yearWeek = $presentYear . $presentWeek;
								
								class specialWeek{
									public  $total;
    								public  $tickets;
    								public  $itemsPerTicket;
    								public  $conversionRate;
    								public  $people;

    								public function __construct($total, $tickets, $itemsPerTicket, $conversionRate, $people){
    									$this->total = $total;
    									$this->tickets = $tickets;
    									$this->itemsPerTicket = $itemsPerTicket;
    									$this->conversionRate = $conversionRate;
    									$this->people = $people;
    								}
								}

								$specialWeeks = ["202151", "202152", "202201"];
								$weeksData["202151"] = new specialWeek("8.101", "85", "1,64", "11,8", "805");
								$weeksData["202152"] = new specialWeek("8.853", "105", "1,37", "8,9", "1.178");
								$weeksData["202201"] = new specialWeek("13.734", "72", "1,28", "18,1", "1.044");
							?>
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Facturación
						</div>
						<div class = "graph_target data_target">
							<?php  
								if(in_array($yearWeek, $specialWeeks)){
									echo $weeksData[$yearWeek]->total;
								}else{
									echo number_format($data['week2']['total'],0,",",".");
								}
							?>€
						</div>
						<div class = "graph_target data_target bold">
							<?php  echo number_format($data['week']['total'],0,",",".") ?>€
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['this_week']== ''){
							?>
								<form method="post" action="?action=set_target_week_quantity" autocomplete="off">
									<input type="text" name="week_quantity" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{
									 echo setBar($data['week']['total'], $data['objetivo']['this_week'],"€",0);
								}
							?>			
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Ticket medio
						</div>
						<div class = "graph_target data_target">
							
							<?php 
								if(in_array($yearWeek, $specialWeeks)){
									echo $weeksData[$yearWeek]->tickets;
								}else{
									echo round($data['week2']['total']/$data['week2']['tickets'],0);
								}
							
							?>€
						</div>
						<div class = "graph_target data_target bold">
							<?php echo round($data['week']['total']/$data['week']['tickets'],0) ?>€
						</div>
						<div class = "graph_target data_target">
							<?php 
							
								if($data['objetivo']['week_average_ticket']== ''){
							?>
								<form method="post" action="?action=set_target_week_average_ticket" autocomplete="off">
									<input type="text" name="week_average_ticket" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{
									 echo setBar($data['week']['total']/$data['week']['tickets'], $data['objetivo']['week_average_ticket'],"€",0);
								}
							?>	
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Prendas por ticket
						</div>
						<div class = "graph_target data_target">
							<?php  
								if(in_array($yearWeek, $specialWeeks)){
									echo $weeksData[$yearWeek]->itemsPerTicket;
								}else{
									echo round($data['week2']['pcs']/$data['week2']['tickets'],2);
								} ?>pt
						</div>
						<div class = "graph_target data_target bold">
							<?php  echo round($data['week']['pcs']/$data['week']['tickets'],2) ?>pt
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['items']== ''){
							?>
								<form method="post" action="?action=set_target_week_items" autocomplete="off">
									<input type="text" name="week_items" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{
									 echo setBar(round($data['week']['pcs']/$data['week']['tickets'],2), $data['objetivo']['items'],"pt",1);
								}
							?>
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Conversión
						</div>
						<div class = "graph_target data_target">
							<?php 
								if(in_array($yearWeek, $specialWeeks)){
									echo $weeksData[$yearWeek]->conversionRate.'%';
									echo '<br/><i>'.$weeksData[$yearWeek]->people.'Personas</i>';
								}else{
									echo round((($data['week2']['tickets']/$data['week2']['people'])*100),1, PHP_ROUND_HALF_UP)."%";
							?><br/>
							<i><?php echo $data['week2']['people'] ?> Personas</i> 
							<?php 
								} 
							?>
						</div>
						<div class = "graph_target data_target bold">
							<?php 
							echo "<b>".round((($data['week']['tickets']/($data['week']['people']))*100),1, PHP_ROUND_HALF_UP)."%</b>"
							?><br/>
							<i><?php echo $data['week']['people'] ?> Personas</i>
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['week_conversion']== ''){
							?>
								<form method="post" action="?action=set_target_week_conversion" autocomplete="off">
									<input type="text" name="week_conversion" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{
									 echo setBar(round((($data['week']['tickets']/$data['week']['people'])*100),1, PHP_ROUND_HALF_UP), $data['objetivo']['week_conversion'],"%",0);
								}
							?>
										
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target bold">
							Mes
							<?php 
							$last_year = getTicketsAndItemsMonth(1); 
							$this_year = getTicketsAndItemsMonth(0); 
							?>
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Facturación
						</div>
						<div class = "graph_target data_target">
							<?php echo number_format(getTotalMonth(1),0,",","."); ?>€
						</div>
						<div class = "graph_target data_target bold">
							<?php $actual = getTotalMonth(0) + $data['total_day_raw'];
							echo number_format($actual,0,",","."); ?>€
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['month_quantity']== ''){
							?>
								<form method="post" action="?action=set_target_month_quantity" autocomplete="off">
									<input type="text" name="month_quantity" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{
									 echo setBar((getTotalMonth(0) + $data['total_day_raw']), $data['objetivo']['month_quantity'],"€",0);
								}
							?>
							
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Ticket medio
						</div>
						<div class = "graph_target data_target">
							<?php 
							$actual = getTotalMonth(1) / $last_year['tickets'];
							echo number_format($actual,0,",","."); ?>€
							
						</div>
						<div class = "graph_target data_target bold">
							<?php $actual = (getTotalMonth(0) + $data['total_day_raw']) / ($this_year['tickets'] + $data['tks']);
							echo number_format($actual,0,",","."); ?>€
						</div>
						<div class = "graph_target data_target">
							<?php
								if($data['objetivo']['month_average_ticket']== ''){
							?>
								<form method="post" action="?action=set_target_month_average_ticket" autocomplete="off">
									<input type="text" name="month_average_ticket" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{
									 echo setBar((getTotalMonth(0) + $data['total_day_raw']) / ($this_year['tickets'] + $data['tks']), $data['objetivo']['month_average_ticket'],"€",0);
								}
							?>
							
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Prendas por ticket
						</div>
						<div class = "graph_target data_target">
							<?php 
							$actual = $last_year['items'] / $last_year['tickets'];
							echo number_format($actual,2,",","."); ?>pt
						</div>
						<div class = "graph_target data_target bold">
							<?php $actual = ($this_year['items'] +  $data['pcs'])  / ($this_year['tickets'] + $data['tks']);
							echo number_format($actual,2,",","."); ?>pt
						</div>
						<div class = "graph_target data_target">
							<?php
								if($data['objetivo']['month_items']== ''){
							?>
								<form method="post" action="?action=set_target_month_items" autocomplete="off">
									<input type="text" name="month_items" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{
									 echo setBar(($this_year['items'] +  $data['pcs'])  / ($this_year['tickets'] + $data['tks']), $data['objetivo']['month_items'],"pt",1);
								}
							?>
							
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Conversión
						</div>
						<div class = "graph_target data_target">
							<?php
							echo round((($last_year['tickets']/$data['last_year_month_people'])*100),1, PHP_ROUND_HALF_UP)."%"?><br/>
							<i><?php echo $data['last_year_month_people'] ?> Personas</i>
							
						</div>
						<div class = "graph_target data_target bold">
							<?php 
							$month_tickets=$data['month_tickets']+$data['tks'];
							echo "<b>".round((($month_tickets/$data['month_people'])*100),1, PHP_ROUND_HALF_UP)."%</b>"
							?><br/>
							<i><?php echo $data['month_people'] ?> Personas</i>
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['month_conversion']== ''){
							?>
								<form method="post" action="?action=set_target_month_conversion" autocomplete="off">
									<input type="text" name="month_conversion" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{

									 echo setBar(round((($month_tickets/$data['month_people'])*100),1, PHP_ROUND_HALF_UP), $data['objetivo']['month_conversion'],"%",0);
								}
							?>
										
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target bold">
							Año
						</div>
						<div class="clear"></div>
					</div>
					<div class = "capsule">
						<div class = "graph_target ">
							Facturación
						</div>
						<div class = "graph_target data_target">

							<?php 
							$year = date("Y",time());
							$last_year = $year - 1;
							echo number_format(getTotalYear($last_year),0,",","."); ?>€
							
						</div>
						<div class = "graph_target data_target bold">
							<?php $actual = getTotalYear($year) + $data['total_day_raw'];
							echo number_format($actual,0,",","."); ?>€
						</div>
						<div class = "graph_target data_target">
							<?php 
								if($data['objetivo']['year_quantity']== ''){
							?>
								<form method="post" action="?action=set_target_year_quantity" autocomplete="off">
									<input type="text" name="year_quantity" <?php echo ($enable_fields) ? "" : "disabled" ?> value="-">
								</form>	
							<?php
								}else{
									 echo setBar((getTotalYear($year) + $data['total_day_raw']), $data['objetivo']['year_quantity'],"€",0);
								}
							?>
							
						</div>
						<div class="clear"></div>
					</div>
		
		<?php 
			}else{ 
				$search_year  = $_POST['year'];
				$search_month = $_POST['month'];
				$seller_data = $SELLER->getStats($_GET['seller_id'], $search_year, $search_month);
		?>

			
			<div class="seller-search-option">
			<form method="post" action="?seller_id=<?php echo $_GET['seller_id']; ?>">
				<select id="month" name="month">
			        <?php
			            for ($x = 1; $x < 13; $x++){    
			                $num_padded = sprintf("%02d", $x);
			                $option = '<option value="'.$num_padded.'" ';
			                if($search_month==$num_padded){
			                    $option.='selected="selected"';
			                }
			                $option.= '>'.$months[$x-1].'</option>';
			                echo $option;
			            }
			        ?>
			    </select>

			    <select id="year" name="year">
			        <?php
			        	$recent_year = date('Y',time());
			            for ($x = 2017; $x <= $recent_year; $x++){    
			                $option = '<option value="'.$x.'" ';
			                if($search_year==$x){
			                    $option.='selected="selected"';
			                }
			                $option.= '>'.$x.'</option>';
			                echo $option;
			            }
			        ?>
			    </select>
			    <button>Filtrar</button>
			</form>
			</div>
			<form method="post" action="?">
				<button>Volver</button>
			</form>
			<div class = "capsule">
				<div class = "graph_target bold">
					Nº de prendas
				</div>
				<div class = "graph_target center">
					<?php echo $seller_data['items'] ?>
				</div>
				<div class = "graph_target bold">
					Nº de tickets
				</div>
				<div class = "graph_target center">
					<?php echo $seller_data['tickets'] ?>
				</div>
				<div class="clear"></div>
			</div>
			<div class = "capsule">
				<div class = "graph_target bold">
					Prendas x tickets
				</div>
				<div class = "graph_target center">
					<?php echo round($seller_data['items']/ $seller_data['tickets'],2) ?>
				</div>
				<div class = "graph_target bold center">
				</div>
				<div class = "graph_target bold center">
				</div>
				<div class="clear"></div>
			</div>
			<div class = "capsule">
				<div class = "graph_target bold">
					Ticket medio
				</div>
				<div class = "graph_target center">
					<?php echo round($seller_data['total']/$seller_data['tickets'],0)."€" ?>
				</div>
				<div class = "graph_target bold">
					Total
				</div>
				<div class = "graph_target center">
					<?php echo round($seller_data['total'],0)."€" ?>
				</div>
				<div class="clear"></div>
			</div>
		<?php }?>
		</div>
		
	<?php 
	$BECOSOFT->connect();
	$items = $BECOSOFT->top(20, '');
	$BECOSOFT->close();
	?>
		<div id="content">
			<div class = "capsule">
				<div class = "top_target name">
					<b>TOP VENTAS MES</b>
				</div>
				<div class= "clear"></div>
			</div>
			<?php
				foreach ($items as $item) {  	
			?>
			<div class = "capsule">
				<div class = "top_target name">
					<?php
						echo $item->name;
					?>
				</div>
				<div class = "top_target center">
					<?php
						echo $item->reference;
					?>
				</div>
				<div class = "top_target stock">
					<?php
						echo $item->stock;
					?>
				</div>
				<div class="clear"></div>
			</div>

			<?php
				}
			?>
			<div class="clear"></div>
		</div>
	</div>
	
	
</div>



	 
	 
	<script type="text/javascript">
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
	
	
	
	
	/*-----------------------------------------
					PARA LOS OBJETIVOS
	-----------------------------------------*/
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
	/*-----------------------------------------
			PARA ESTADISTICAS DE GENERO
	-------------------------------------------*/
	$( "#estadistica" ).submit(function( event ) {
		
		var suma= parseInt($( "#mujer" ).val())+ parseInt($( "#hombre" ).val())+ parseInt($( "#acc" ).val());
		  if (suma == 100 ) {
			  return;
		  }
		 
		 alert("la suma de los valores no da 100 , ["+ suma+"]");
		  event.preventDefault();
		});
		
	/*-----------------------------------------
			PARA LA REPRESENTACION
	-------------------------------------------*/
	var doughnutData = [
	    				{
	    					value: <?php echo $data['objetivo']['men']?>,
	    					color:"#8cbdeb",
	    					highlight: "#8cbdeb",
	    					label: "M"
	    				},
	    				{
	    					value: <?php echo $data['objetivo']['women']?>,
	    					color: "#eb8cb4",
	    					highlight: "#eb8cb4",
	    					label: "G"
	    				},
	    				{
	    					value: <?php echo $data['objetivo']['acc']?>,
	    					color: "#FF9900",
	    					highlight: "#FF9900",
	    					label: "A"
	    				}
	    			];

    window.onload = function(){
 		var ctx = document.getElementById("chart-area").getContext("2d");
	   	window.myDoughnut = new Chart(ctx).Doughnut(doughnutData, {responsive : true, animationEasing: "linear", animationSteps: 85});
	};
	    			
	</script>

</body>
</html>
