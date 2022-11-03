<?php
	error_reporting(0);
	
?>
<?php
define("_SERVER","localhost");
define("_USERNAME","sabado");
define("_PASSWORD","sabado");
define("_BD","admin_sabados");

$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
if (!$conexion)
	die('Something went wrong while connecting to MYSQL: '.mysql_error());
mysql_select_db(_BD, $conexion);
function nombre($nombre){
	if($nombre==""){
		return "Invitado";
	}
	$token=explode(" ", $nombre);
	return utf8_encode($token[0].' '.$token[1][0].$token[1][1].'.');
}
function get_stats($id){
	$ids=array();
	$win=array();
	$query_players=mysql_query("select id from admin_sabados.players") or die(mysql_error());
	while($players=mysql_fetch_array($query_players)){
		//GANADOS
		$query_win=mysql_query("select COUNT(G.id_player), G.id_player, P.nombre from admin_sabados.games G, admin_sabados.players P where date in (SELECT date FROM admin_sabados.games where id_player='".$id."' and status = 1) and status = 1 and G.id_player = P.id and G.id_player='".$players['id']."'") or die(mysql_error());
		//PERDIDOS o EMPATADOS
		$query_games_lose  = mysql_query("select * from admin_sabados.games where date in (SELECT date FROM admin_sabados.games where id_player='".$id."' and GF<GC) and GF<GC and id_player='".$players['id']."'") or die(mysql_error());
		$query_games_draw  = mysql_query("select * from admin_sabados.games where date in (SELECT date FROM admin_sabados.games where id_player='".$id."' and GF=GC) and GF=GC and id_player='".$players['id']."'") or die(mysql_error());
		$query_games_win	= mysql_query("select * from admin_sabados.games where date in (SELECT date FROM admin_sabados.games where id_player='".$id."' and status = 1) and status = 1 and id_player='".$players['id']."'") or die(mysql_error());
		$ganados   = mysql_num_rows($query_games_win);
		$empatados = mysql_num_rows($query_games_draw);
		$perdidos  = mysql_num_rows($query_games_lose);
		$total 	   = $ganados + $empatados + $perdidos;
		//echo $ganados.' '.$empatados.' '.$perdidos.' '.$total.'<br/>';
		while($data=mysql_fetch_array($query_win)){
			$nombre=explode(' ',$data[2]);
			if($data[1]!=$id){
			echo '<div class="pl">
					'.$nombre[0][0].$nombre[0][1].'.'.$nombre[1][0].$nombre[1][1].' 
					<h1>'. round((($ganados*3+$empatados)/($total*3)*100),0).'%</h1>
					G:'.$ganados.'<br/>
					E:'.$empatados.'<br/>
					P:'.$perdidos.'<br/>
					</div>';
			}
			//echo $data[0].' '.$data[1].' '.$data[2].' % de VICTORIAS/JUGADOS: '.round(($ganados/($ganados+$noganados))*100,0).'<br/>';			
		}
	}
	
}
function list_players($team){
	$list='<select name="'.$team.'[]" class="listaJugadores">';
	$query=mysql_query("SELECT * FROM `players`")
	or die(mysql_error());
	$list.='<option value="0">Invitado</option>';
	while($data=mysql_fetch_array($query)){	
		$token=explode(' ',utf8_encode($data["nombre"]));
		$name= $token[0].' '.$token[1][0].$token[1][1].'.';
			$list.='<option value="'.$data["id"].'">'.$name.'</option>';
	}
	$list.='<select name="">';
	return $list;
}

if($_GET["submit"]=="on"){

	if($_POST["clave"]=="Gomu"){
		$a=$_POST["scoreA"];
		$b=$_POST["scoreB"];
		$match=$a-$b;
		echo $match;
		if($match<0){
			$statusA=0;
			$statusB=1;
		}elseif($match>0){
			$statusA=1;
			$statusB=0;
		}else{
			$statusA=0;
			$statusB=1;
		}
			
		for($i=0;$i<5;$i++){
			
			$query=mysql_query("INSERT INTO `games` (id_player, date, GF, GC, status) 
						VALUES ('".$_POST["A"][$i]."', '".$_POST["date"]."', '".$_POST["scoreA"]."', '".$_POST["scoreB"]."', '".$statusA."')")
						or die(mysql_error());
			
			$query=mysql_query("INSERT INTO `games` (id_player, date, GF, GC, status) 
						VALUES ('".$_POST["B"][$i]."', '".$_POST["date"]."', '".$_POST["scoreB"]."', '".$_POST["scoreA"]."', '".$statusB."')")
						or die(mysql_error());
		}
	}
}

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
	<link href="css/jquery.circliful.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="js/Chart.js"></script>
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.circliful.js"></script>

	
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="css/general.css" />
	
  	
  	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		
	<script src="http://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
	<script>
		$(document).ready(function(){
			$('#myTable').DataTable({
			"order": [[ 1 , "desc" ]],
			"searching": false,
			"pageLength": 30,
			"paging":false,
			"sDom": '<"top">rt<"bottom"flp><"clear">'
			});
			$('.match').hide();
			$( "#nuevo" ).click(function() {
				$(".match").fadeToggle()
			});

			$('.historial').click(function () {
				 $('.historial').hide();
			});
			
			$('.clave').click(function () {
				 $('.clave').val("");
			});
			
			$('.showSingle').click(function () {
			    $('.historial').hide();
			    $('#div' + $(this).attr('target')).show();
			    $("html, body").animate({ scrollTop: 0 }, "slow");
			});
		});
		$(function() {
		    $( document ).tooltip();
		  })

	</script>
</head>
<body>


<div id="content">
		
		<div class="match">
			<form method="post" name="match" action="?submit=on">
			<div class="team">
				<?php
					for($i=0;$i<5;$i++){
				?>
						<div class="players">
							<?php echo list_players("A"); ?>
						</div>
				<?php
					}
				?>
			</div>
			<div class="score">
				<input type="text" name="scoreA" value="0" class="scorematch" maxlength="2">
				<input type="text" name="scoreB" value="0" class="scorematch" maxlength="2">
				
				<div>VS</div>
				<input type="text" name="date" value="<?php echo date("Y-m-d",time())?>" class="date"> 
				<input type="text" name="clave" class="clave" value="Clave" onblur="if (this.value == 'Clave') {this.value = '';}">
			</div>
			<div class="team">
				<?php
					for($i=0;$i<5;$i++){
				?>
						<div class="players">
							<?php echo list_players("B"); ?>
						</div>
				<?php
					}
				?>
				
			</div>
			<div class="clear"></div>
			<button class="cupid-green" onclick="document.getElementByName('match').submit();">Guardar</button>
		<br/><br/>
			</form>
		</div>
		<div style="float:right;">
			<a href="?season=2015">2015</a> |  <a href="?season=2016">2016</a><br/><br/>
		</div>
		<button class="cupid-green" id="nuevo" onclick="document.getElementByName('match').submit();">Nuevo Partido</button>
		<br/><br/>
	
	
	<?php 
		$query_player=mysql_query("SELECT id from players")
		or die(mysql_error());
		$i=0;
		while($data_player=mysql_fetch_array($query_player)){
			
			?>
			<div class="historial" id="div<?php echo $i?>" style="display:none;">	
			
			<?php //get_stats($data_player['id']); ?>
			<div class="clear"></div>
			<?php 
			
			if(!$_GET["season"])
				$this_season = "2016-01-01 00:00:01";
			else 
				$this_season = $_GET["season"]."-01-01 00:00:01";
			
			$query_date=mysql_query("SELECT date, GF, GC from games WHERE id_player = ".$data_player['id']." and date> '".$this_season."' group by date order by date DESC") or die(mysql_error());
			while($data_date=mysql_fetch_array($query_date)){
				if($data_date['GF']<$data_date['GC'])
					$style=" background:#ffecec; border:1px solid #f5aca6;";
				else if($data_date['GF']==$data_date['GC'])
					$style=" background:#fff8c4; border:1px solid #f2c779;";
				else
					$style=" background:#e9ffd9; border:1px solid #a6ca8a;";				
				?>
				<div class="partido" style="<?php echo $style?>">					
				<div class="equipo">	
				<?php 
				$query_myteam=mysql_query("SELECT G.id_player,P.nombre, G.GF,G.GC FROM games G,players P WHERE G.date='".$data_date['date']."'  and date> '".$this_season."'  AND G.status='1' AND G.id_player=P.id") or die(mysql_error());
				
				while($data_myteam=mysql_fetch_array($query_myteam)){
						if($data_player['id']==$data_myteam['id_player'])
							echo '<b>'.nombre($data_myteam['nombre']).'</b><br/>';
						else if($data_player['id']==0)
							echo "Invitado";
						else 
							echo nombre($data_myteam['nombre']).'<br/>';
						$GF=$data_myteam['GF'];
						$GC=$data_myteam['GC'];
				}
				?>
				</div>
				<div class="resultado">
					<b><?php echo $GF?> - <?php echo $GC?></b>
					<br/>
					<div>VS</div>
					<br/>
					<?php
					$token_date=explode("-",$data_date['date']);
					echo $token_date[2].'-'.$token_date[1].'-'.$token_date[0]?>
				</div>
				<div class="equipo">	
				<?php 
				$query_other=mysql_query("SELECT G.id_player,P.nombre FROM games G,players P WHERE G.date='".$data_date['date']."'  AND G.status='0' AND G.id_player=P.id") or die(mysql_error());
				while($data=mysql_fetch_array($query_other)){
						if($data_player['id']==$data['id_player'])
							echo '<b>'.nombre($data['nombre']).'</b><br/>';
						else
							echo nombre($data['nombre']).'<br/>';
				
				}
				?>
				</div>
				<div class="clear"></div>
				</div>				
				<?php 
			}
			?>
			</div>
			
			<?php 
			$i++;
		}
				
	?>	
	
			
		
	
		
	
		
		<table cellpadding="7" cellspacing="0" id="myTable" width="100%">
		<thead>
			<tr>
				<td></td>
				<td>Puntos</td>
				<td>PJ</td>
				<td>PG</td>	
				<td>PE</td>				
				<td>PP</td>
				<td>GF</td>
				<td>GC</td>				
				<td>%</td>
			</tr>
		</thead>
		
		<tbody>
<?php	
	
	
	$query=mysql_query("SELECT * FROM `players`")
	or die(mysql_error());
	$color="#f9f9f9";
	$z=0;
	while($data=mysql_fetch_array($query)){
		$info=mysql_query("SELECT sum(GF), sum(GC), status  FROM `games` where id_player='".$data["id"]."' and date> '".$this_season."'")or die(mysql_error());
		
		$wins=mysql_query("SELECT count(status) FROM `games` where id_player='".$data["id"]."'  AND date> '".$this_season."' AND GF>GC")or die(mysql_error());
		$data_info=mysql_fetch_array($wins);
		$win=$data_info["count(status)"];
		
		$loses=mysql_query("SELECT count(status) FROM `games` where id_player='".$data["id"]."' AND date> '".$this_season."' AND GF<GC")or die(mysql_error());
		$data_info=mysql_fetch_array($loses);
		$lose=$data_info["count(status)"];
		
		$draws=mysql_query("SELECT count(status) FROM `games` where id_player='".$data["id"]."' AND date> '".$this_season."' AND GF=GC")or die(mysql_error());
		$data_info=mysql_fetch_array($draws);
		$draw=$data_info["count(status)"];
		
		$data2=mysql_fetch_array($info);
		
		if($data2["sum(GF)"]=="")
			$gf=0;
		else
			$gf=$data2["sum(GF)"];
			
		if($data2["sum(GC)"]=="")
			$gc=0;
		else
			$gc=$data2["sum(GC)"];
			
		$total=$win+$lose+$draw;
		
		if($total>0){
			$token=explode(' ',utf8_encode($data["nombre"]));
			$name= $token[0].' '.$token[1][0].$token[1][1].'.';
						
			if($color=="#f9f9f9")
				$color="#E5E5E5";
			else 
				$color="#f9f9f9";
	?>
	
		
			<tr>
				<td><div style="text-align:left"><a class="showSingle" target="<?php echo $z?>"><b><?php echo $name?></b></a></div></td>
				<td><?php echo $win*3+$draw?></td>
				<td><?php echo $total?></td>
				<td><?php echo $win?></td>	
				<td><?php echo $draw?></td>				
				<td><?php echo $lose?></td>
				<td><?php echo $gf?></td>
				<td><?php echo $gc?></td>
				<td><?php echo round(((($win*3+$draw)/($total*3))*100),0)?>%</td>
			</tr>				
	<?php
		}
		$z++;
	}


?>
</tbody>
<tfoot>

			<tr>				
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>				
			</tr>
</tfoot>
		</table>

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
		$(this).find(".item_detail").show();
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