<?php
	error_reporting(0);

include("include/config.php");
include("include/class.APOYO.php");
include("include/class.BECOSOFT.php");
$BECOSOFT  = new BECOSOFT();	



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
	<link rel="stylesheet" type="text/css" href="css/button.css" media="screen" />
	
	
	<link href="css/jquery.circliful.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="js/Chart.js"></script>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.circliful.js"></script>

	
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>

	<script>


	$("document").ready(function(){		
		/*$(".item").focusout(function(){			
			if($(this).click()){
				$(this).children(".search").val('');
				$(this).children(".livesearch").empty();
				
			}else{
				if($(this).find(".selects ").val()==1){
					var sel = $(this).find(".default_answer").val();	
					//alert(sel);		
					$(this).find(".selects ").val(sel);
i
				}	
			}	
		});*/			

		$(".stock").click(function () { 
			var a = $(this).data('reference'); 
			$(".popup").show();
    		$("#frame").attr("src", "https://www.gomugomu.es/dashboard/stockxreferencia/?text-search=" + a);
		});
		$(".cerrar").click(function () {
			$(".popup").hide();
		} );
	 	$(".search").keyup(function() {	
		 	//alert($(this).attr("class") + " " + $(this).parent().attr("class")+ " "+$(this).parent().children(".livesearch").attr("class")  );	 			 	
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

	$('.reference').click(function(){    
		/*    
	    var reference = $(this).attr('name');
	            
        $(this).$('.popup-content').html("<b>Cargando...</b>");	        
		    $.post('subscribe.php', {  email: myemail}, function(data){	            
		        $('.popup-content').html("<b>"+data+"</b>");
		        $('.popup').delay(3000).fadeOut(300);
		        Cookies.remove('newsletter');
		        Cookies.set('newsletter', 'suscrito');
		    }).fail(function() {         	            
		     	$('.popup').delay(3000).fadeOut(300);
	    	});	 	       
	    */
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
?>

<div id="div1">
<a href="?">
	<div id="div2">
	</div>
</a>
</div>


<div id="content">
		
		
	
<?php echo target_month($data['total_day'])?>
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
	
	
	
	$num=count($data['id_ticket']);
	for ($i=0;$i<$num;$i++){
		$answer= get_answers($data['id_ticket'][$i]);
		$color='';

		if($answer['updated']==true)$color='#FFD076';
			
	
?>
	<div class="item" style="background-color:<?php echo $color ?>">
		[ <b><?php echo $data['id_ticket'][$i] ?></b> ] 
		<?php echo $data['date'][$i] ?> 
		<?php echo $answer['print'] ?>
		
		<div class="paid">
			<?php echo $data['total'][$i] ?> €
		</div>
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
		<div class="capsule">
			Total hoy <br/>
			<div class="data p1">
				<?php echo $data['total_day'] ?>€
			</div>
			<div class="data p2">
				<?php echo $data['total_margen']  ?>€
			</div>
			<!-- 
			<div class="data p3">
				<?php echo $data['total_margen_per']  ?>%
			</div>
			-->
			<div class="data p4">
				 <?php echo $data['desc'] ?>%
			</div>
			 
			<div class="data p5">
				 <?php echo $data['pcs'] ?>p
			</div>
			<div class="data p6">
				<?php echo $data['tks'] ?>t
			</div>
			<div class="data p7">
				<?php echo round($data['pcs']/$data['tks'],2) ?>pt
			</div>
			<div class="data p8">
				 <?php echo $data['total_day/tks'] ?>€
			</div>
			<div class="clear"></div>
		</div>
		
		
		<div class="capsule">
		<?php $sameday=same_day(); ?>
		Año pasado [ <?php
		echo $sameday['date'];
		?>]<br/>
		<div class="stats">	
			<?php
				if($sameday['qty']!=0){	
			?>
			<div class="data p1">
				<?php echo $sameday['qty']  ?>€
			</div>
			<div class="data p2">
				<?php echo $sameday['total_margen']  ?>€
			</div>
			<!-- 
			<div class="data p3">
				<?php echo $sameday['total_margen_per']  ?>%
			</div>
			 -->
			<div class="data p4">
				 <?php echo $sameday['desc'] ?>% 
			</div>
			
			<div class="data p5">
				<?php echo $sameday['pcs'] ?>p	 
			</div>
			<div class="data p6">
				<?php echo $sameday['rcp'] ?>t
			</div>
			<div class="data p7">
				<?php echo round($sameday['pcs']/$sameday['rcp'],2) ?>pt 
			</div>
			<div class="data p8">
				 <?php echo round($sameday['total_day']/$sameday['rcp'],0) ?>€
			</div>
			<div class="clear"></div>

			
			<?php
				}else{
			?>
				El año pasado no abrimos ese día
			<?php	
				}
			?>
				
		</div>
		</div>
	
	
		<!--
		Semanal [ <b><?php echo $data['objetivo']['untilnow'] ?> € </b>/<b> <?php echo $data['objetivo']['target'] ?> €</b> ]
		<?php echo $data['objetivo']['graph'] ?>
		-->
		
		<div class="capsule">
		Objetivo <br/>
		<?php if($data['objetivo']['t_total']['value']=="" ){
			echo "<b>No se ha definido un objetivo</b>";	
		}else{?>
		<div class="circlestat" 
			data-dimension="56"
			data-text="<?php echo $data['objetivo']['t_total']['value'] ?>€"
			data-width="0.1"
			data-fontsize="11"
			data-percent="<?php echo $data['objetivo']['t_total']['per'] ?>"
			data-fgcolor="<?php echo $data['objetivo']['t_total']['color'] ?>"
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
	
	<!--  
	<div class="stats">
			<div class="data p1">
				 <?php echo $data['objetivo']['target'] ?>€
			</div>
			<div class="data p2">
				<?php echo $data['objetivo']['total_margen']  ?>€
			</div>

			<div class="data p4">
				<div class="amargen"><?php echo $data['objetivo']['margin']  ?>%</div>
			</div>

			<div class="data p7">
				 <div class="aprendas"><?php  echo $data['objetivo']['items'] ?>pt</div>
			</div>
			<div class="data p8">
				<div class="atickets"><?php echo $data['objetivo']['tickets'] ?>€ </div>
			</div>
			<div class="clear"></div>
		-->
		<div class="hidden_field">
			<div id="margen">
			<form method="post" action="?do=insert&op=margen" name="margen">
				</b><input type="number" size="4" name="margin" min="0" max="99" required value="<?php echo $data['objetivo']['margin'] ?>" placeholder="Descuento">
				<input type="submit" value="aceptar">
			</form>
			</div>
			<div id="prendas">
			<form method="post"  action="?do=insert&op=prendas" name="prendas">
				
				<input type="number"  name="items" step="any"  value="<?php echo $data['objetivo']['items'] ?>" required placeholder="Número de prendas">
				
				<input type="submit" value="aceptar">
			</form>
			</div>
			<div id="tickets">
			<form method="post" action="?do=insert&op=tickets" name="tickets">
				<input type="number" size="4" name="tickets" min="0" max="999" value="<?php echo $data['objetivo']['tickets'] ?>" required placeholder="Ticket medio">
				<input type="submit" value="aceptar">
			</form>
			</div>
			<div id="estadisticas">
			<form method="post" action="?do=insert&op=estadisticas" name="estadisticas" id="estadistica">
					<div class="col_3">
					  <b>Hombre:</b>
					  <input type="text" id="hombre" name="hombre"  value="<?php echo $data['objetivo']['men'] ?>" style="border:0; color:#f6931f; font-weight:bold;"><br/>
					</div>
					<div class="col_3 .right">
					  <b>Mujer:</b>
					  <input type="text" id="mujer" name="mujer" value="<?php echo $data['objetivo']['women'] ?>" style="border:0; color:#f6931f; font-weight:bold;">
					</div>
					<div class="col_3 .right">
					  <b>Accesorios:</b>
					  <input type="text" id="acc" name="acc" value="<?php echo $data['objetivo']['acc'] ?>" style="border:0; color:#f6931f; font-weight:bold;">
					</div>
					<div class="clear"></div>
				
				<input type="submit" value="aceptar">
			</form>
			</div>
		</div>

	<!-- </div>-->	
				
	<div class="clear"></div>
	</div>
	
	<div class="capsule">
	Semana Actual
	<div class="stats">
			<div class="data p1">
				<?php  echo $data['week']['total'] ?>€
			</div>
			<div class="data p2">
				<?php echo $data['week']['total_margen']  ?>€
			</div>
			<!-- 
			<div class="data p3">
				<?php echo $data['week']['total_margen_per']  ?>%
			</div>
			 -->
			<div class="data p4">
				 <?php echo $data['week']['desc'] ?>%
			</div>
			
			<div class="data p5">
				 <?php echo $data['week']['pcs'] ?>p
			</div>
			<div class="data p6">
				<?php  echo $data['week']['tickets'] ?>t
			</div>
			<div class="data p7">
				<?php  echo round($data['week']['pcs']/$data['week']['tickets'],2) ?>pt
			</div>
			<div class="data p8">
				 <?php echo $data['week']['tks'] ?>€
			</div>
			<div class="clear"></div>
	</div>
	<div class="graph50 left">
		<?php echo $data['week']['stats']['graph'] ?>
	</div>
	<div class="graph50 right">
		<?php echo $data['week']['country'] ?>
		<!--<?php echo $data['week']['countryMoney'] ?>-->
	</div>
<div class="clear"></div>
	</div>
	
	<div class="capsule">
	Semana Año - 1
	<div class="stats">
			<div class="data p1">
				<?php  echo $data['week2']['total'] ?>€
			</div>
			<div class="data p2">
				<?php echo $data['week2']['total_margen']  ?>€
			</div>
			<!-- 
			<div class="data p3">
				<?php echo $data['week2']['total_margen_per']  ?>%
			</div>
			-->
			<div class="data p4">
				 <?php echo $data['week2']['desc'] ?>%
			</div>
			
			<div class="data p5">
				 <?php echo $data['week2']['pcs'] ?>p
			</div>
			<div class="data p6">
				<?php  echo $data['week2']['tickets'] ?>t
			</div>
			<div class="data p7">
				<?php  echo round($data['week2']['pcs']/$data['week2']['tickets'],2) ?>pt
			</div>
			<div class="data p8">
				 <?php echo $data['week2']['tks'] ?>€
			</div>
			<div class="clear"></div>
	</div>
	<div class="graph50 left">
		<?php echo $data['week2']['stats']['graph'] ?>
	</div>
	<div class="graph50 right">
		<?php echo $data['week2']['country'] ?>
		<!--<?php echo $data['week2']['countryMoney'] ?>-->
	</div>
	<div class="clear"></div>
	</div>
	
	<div class="capsule">
	Semana Año - 2
	<div class="stats">
			<div class="data p1">
				<?php  echo $data['week3']['total'] ?>€
			</div>
			<div class="data p2">
				<?php echo $data['week3']['total_margen']  ?>€
			</div>
			<!-- 
			<div class="data p3">
				<?php echo $data['week3']['total_margen_per']  ?>%
			</div>
			 -->
			<div class="data p4">
				 <?php echo $data['week3']['desc'] ?>%
			</div>
			
			<div class="data p5">
				 <?php echo $data['week3']['pcs'] ?>p
			</div>
			<div class="data p6">
				<?php  echo $data['week3']['tickets'] ?>t
			</div>
			<div class="data p7">
				<?php  echo round($data['week3']['pcs']/$data['week3']['tickets'],2) ?>pt
			</div>
			<div class="data p8">
				 <?php echo $data['week3']['tks'] ?>€
			</div>
			<div class="clear"></div>
	</div>
	<div class="graph50 left">
		<?php echo $data['week3']['stats']['graph'] ?>
	</div>
	<div class="graph50 right">
		<?php echo $data['week3']['country'] ?>
		<!--<?php echo $data['week3']['countryMoney'] ?>-->
	</div>
	<div class="clear"></div>
	</div>
	<div class="capsule">
	Semana Año - 3
	<div class="stats">
			<div class="data p1">
				<?php  echo $data['week4']['total'] ?>€
			</div>
			<div class="data p2">
				<?php echo $data['week4']['total_margen']  ?>€
			</div>
			<!-- 
			<div class="data p3">
				<?php echo $data['week4']['total_margen_per']  ?>%
			</div>
			 -->
			<div class="data p4">
				 <?php echo $data['week4']['desc'] ?>%
			</div>
			
			<div class="data p5">
				 <?php echo $data['week4']['pcs'] ?>p
			</div>
			<div class="data p6">
				<?php  echo $data['week4']['tickets'] ?>t
			</div>
			<div class="data p7">
				<?php  echo round($data['week4']['pcs']/$data['week4']['tickets'],2) ?>pt
			</div>
			<div class="data p8">
				 <?php echo $data['week4']['tks'] ?>€
			</div>
			<div class="clear"></div>
	</div>
	<div class="graph50 left">
		<?php echo $data['week4']['stats']['graph'] ?>
	</div>
	<div class="graph50 right">
		<?php echo $data['week4']['country'] ?>
		<!--<?php echo $data['week4']['countryMoney'] ?>-->
	</div>
	<div class="clear"></div>
	</div>
	<div class="capsule">
	Semana Año - 4
	<div class="stats">
			<div class="data p1">
				<?php  echo $data['week5']['total'] ?>€
			</div>
			<div class="data p2">
				<?php echo $data['week5']['total_margen']  ?>€
			</div>
			<!-- 
			<div class="data p3">
				<?php echo $data['week4']['total_margen_per']  ?>%
			</div>
			 -->
			<div class="data p4">
				 <?php echo $data['week5']['desc'] ?>%
			</div>
			
			<div class="data p5">
				 <?php echo $data['week5']['pcs'] ?>p
			</div>
			<div class="data p6">
				<?php  echo $data['week5']['tickets'] ?>t
			</div>
			<div class="data p7">
				<?php  echo round($data['week5']['pcs']/$data['week5']['tickets'],2) ?>pt
			</div>
			<div class="data p8">
				 <?php echo $data['week5']['tks'] ?>€
			</div>
			<div class="clear"></div>
	</div>
	<div class="graph50 left">
		<?php echo $data['week5']['stats']['graph'] ?>
	</div>
	<div class="graph50 right">
		<?php echo $data['week5']['country'] ?>
		<!--<?php echo $data['week4']['countryMoney'] ?>-->
	</div>
	<div class="clear"></div>
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