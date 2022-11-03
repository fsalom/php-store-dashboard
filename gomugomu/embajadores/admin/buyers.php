<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<link rel="stylesheet" type="text/css" href="buyers.css" media="screen" />
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
	<script>
		
function habilitar() {
	var objSelect = document.getElementById("how");
	var strUser = objSelect.options[objSelect.selectedIndex].value;

	
	if(strUser == 7){
		  document.getElementById('others').disabled=false;
    }
}

function setSelectedValue() {
	var objSelect = document.getElementById("from");
	var strUser = objSelect.options[objSelect.selectedIndex].value;
	if(strUser == 1){
		var setSelect = document.getElementById("how");
			setSelect.value=8;
    }
}
	</script>
	
</head>
<body>

<div class="ball-arc">
	<div class="point"><img src="gomu.png" alt="Gomu Gomu"></div>
</div>


<!-
Listado de opciones completo con las opciones que hay y las que no
			
0- Ya es cliente
1- Pasaba por aquí
2- Viajando fuera
3- Flyer
4- Evento en la tienda
5- Evento fuera de la tienda
6- Cartel en Colón
7- Otros
8- Es turista
9- Por un conocido
10- Corte ingles
11- Colegios
12- Brand Ambassadors
-->


<?php
include_once("../core/class/class.API.php");
include_once("../core/config.php");

function media_dia(){
		$query2	= mysql_QUERY("SELECT SUBSTRING(date,1,10) as f FROM `feed_ticket` GROUP BY `f` ORDER BY `id`")or die(mysql_error());
	while($data = mysql_fetch_array($query2)){
		$query = "SELECT date, SUM(subtotal) FROM `feed_ticket` WHERE date LIKE '".$data['f']."%'";
		$result = mysql_query($query)or die(mysql_error());
		while($row = mysql_fetch_array($result)){
			
			$space=explode(" ",$row['date']);
			//echo $space[0]."<br/>";
			$break=explode("-",$space[0]);
			$date=$break[2]."-".$break[1]."-".$break[0];
			$day=strftime("%A",strtotime($date));
			//echo $date." ".$day." ".$row['SUM(subtotal)']."<br/>";
			
			$week[$day]['total']+=$row['SUM(subtotal)'];
			$week[$day]['num']+=1;
			//"2011-05-19"
			
			
		}
		
	}
	
	return $week;
	//echo $week['Sunday']['total']/$week['Sunday']['num'].'<br/>';
	
	//echo $week['Sunday']['total']." ".$week['Sunday']['num'];
}


$error[0]="Se escribe la cantidad con un . no con una ,";
$error[1]="¿Estas de resaca?";
$error[2]="¿Que te pasa hoy? lo has escrito mal";
$error[3]="No esta bien escrito";
$error[4]="Esta mal. Hay que poner por ejemplo: 74.50";

$frases_sin_venta[0]="Como es posible que aun no hayas vendido nada!";
$frases_sin_venta[1]="Que desastre... no has vendido nada!";
$frases_sin_venta[2]="Ánimo que hay que vender algo!";
$frases_sin_venta[3]="0€ ... sniff sniff :_(";
$frases_sin_venta[4]="<b>¡DONDE ESTA LA GENTE!</b>";

$frases_con_venta_grande[0]="<b>YEAH!</b> eso si que es una venta";
$frases_con_venta_grande[1]="Otra venta más así y nos vamos a casa!";
$frases_con_venta_grande[2]="Si pudiera salir de aquí te daría un beso!";
$frases_con_venta_grande[3]="<b>GENIAL!</b> eres increible";
$frases_con_venta_grande[4]="Me encanta! que no pare la fiesta";

$frases_con_venta_media[0]="Paso a paso se consiguen los objetivos!";
$frases_con_venta_media[1]="Muy bien!";
$frases_con_venta_media[2]="Así me gusta!";
$frases_con_venta_media[3]="Venga a por otra más";
$frases_con_venta_media[4]="Que el ritmo no pare";

$frases_con_venta_baja[0]="Que rata que era ese cliente...";
$frases_con_venta_baja[1]="Jo... yo que creía que se llevaría más";
$frases_con_venta_baja[2]="Vaya!... bueno al menos se llevo algo";
$frases_con_venta_baja[3]="Gotita a gotita se llena el vaso";
$frases_con_venta_baja[4]="Arriba esos animos el siguiente comprará más";

$day_week[1]="lunes";
$day_week[2]="martes";
$day_week[3]="miercoles";
$day_week[4]="jueves";
$day_week[5]="viernes";
$day_week[6]="sábados";
$day_week[7]="domingos";


	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion){
		$API = new API();
		die($API->printerror(_MYSQLCONNECT));
	}
	mysql_select_db(_BD, $conexion);

	$value = $_POST['value'];
	$date = time();
	$from = $_POST['from'];
	$store = $_POST['store'];
	
	if($_POST['store']=="")
		$store=0;
	else
		$store=$_POST['store'];
	
	$ticket = $_POST['ticket'];
	$how=$_POST['how'];
	$others=$_POST['others'];
	?>


<?php	
	
if(($_GET['form']==2)||($_GET['form']==1)||($_GET['go']=='edit')){
	if((!is_numeric($value))){
		$frase="";
		$ticket=$_GET['ticket'];
		$quantity=$_GET['quantity'];
		$from=$_GET['from'];
		
		if($_GET['store']=="")
			$store=0;
		else
			$store=$_GET['store'];
		
		$how=$_GET['how'];
		$others=$_GET['others'];
		
		if($_POST['ticket']=="")
			$frase=$error[rand(0,4)]." Tienes que poner un número de ticket";
		if($_GET['go']=="edit")
			$frase="Listo para editar?";
			
		if($_GET['fecha']!="")
			$extra='&fecha='.$_GET['fecha'];
			
		if($_GET['go']=='edit')
			$action="?form=2&id=".$_GET['id'].$extra;
		else
			$action="?form=1".$extra;
		?>
		
		


		
			<div class="notification" style="color:#900; font-weight:bold;"><?php echo $frase; ?></div>
			<div id="content">
			<form method="post" name="myForm" action="<?php echo $action; ?>">
			Número de ticket :<br/><br/>
				<input type="text" name="ticket" class="input" value="<?php echo $ticket?>"><br/><br/>

				Cantidad :<br/><br/>
				<input type="text" name="value" class="input" value="<?php echo $quantity?>"><br/><br/>
				Nacionalidad:<br/><br/>
				<select name="from" id="from" class="input" onchange="setSelectedValue();">
					<option value="0" <?php if($from=="0") echo 'selected="selected"'?>>Español</option>
					<option value="1" <?php if($from=="1") echo 'selected="selected"'?>>Extranjero</option>
				</select><br/><br/>
				<!--Tienda:<br/><br/>
				<select name="store" class="input">
					<option value="0" <?php if($store=="0") echo 'selected="selected"'?>>Sorni</option>
					<option value="1" <?php if($store=="1") echo 'selected="selected"'?>>Arena</option>
				</select><br/><br/>-->
				Como nos ha conocido:<br/><br/>
				<select name="how" id="how" class="input2" onchange="habilitar();">
					<option value="0" <?php if($how=="0") echo 'selected="selected"'?>>Ya es cliente</option>
					<option value="1" <?php if($how=="1") echo 'selected="selected"'?>>Pasaba por aquí</option>
					<option value="2" <?php if($how=="2") echo 'selected="selected"'?>>Viajando fuera</option>
					<option value="9" <?php if($how=="9") echo 'selected="selected"'?>>Por un conocido</option>
					<option value="10" <?php if($how=="10") echo 'selected="selected"'?>>Corte ingles</option>
					<option value="11" <?php if($how=="11") echo 'selected="selected"'?>>Colegios</option>
					<option value="12" <?php if($how=="12") echo 'selected="selected"'?>>Brand Ambassadors</option>
					<option value="7" <?php if($how=="7") echo 'selected="selected"'?>>Otros</option>
					<option value="8" <?php if($how=="8") echo 'selected="selected"'?>>Es turista</option>
				</select><br/><br/>
				Otros:<br/><br/>
				<textarea name="others" id="others" class="input3" disabled="disabled"></textarea>
				<input type="submit" class="button white" value="Aceptar">
			</form>
			</div>
		<?php
		}else if($_GET['form']=="1"){
			
			$previo=mysql_query("SELECT * FROM `feed_buyers` WHERE `id_ticket` = '".$ticket."'")or die(mysql_error());
		
		if(mysql_num_rows($previo)>0){
			
			
		?>
		<div class="notification" style="color:#990000; font-weight:bold;">Ya existe el ticket!, editalo en la lista de abajo
		</div>
		
		<?php
		}else{
			
		if($_GET['fecha']!=""){
			$fecha=$_GET['fecha'];
			$fecha2=$_GET['fecha'].' 00:00:01';
		}else{
			$fecha=date('Y-m-d',$date);
			//echo $fecha;
			$fecha2=date('d-m-Y H:i:s',$date);
		}
		mysql_query("INSERT INTO `feed_buyers` ( `quantity` , `date`, `date2` ,`from`, `id_ticket`, `store`, `how`, `others` ) 
		VALUES ('".$value."', '".$fecha2."','".$fecha."', '".$from."','".$ticket."','".$store."','".$how."', '".$others."');") or die(mysql_error());
		if ($from==1) $buyer="Extranjero";
		else $buyer="Español";
		
		$lastId = mysql_insert_id();
		$result = mysql_query("SELECT * FROM `feed_buyers` WHERE `id` = $lastId");
		while($row = mysql_fetch_array($result)){
				$quantity = $row['quantity'];
			}
		
				if($quantity<50)
					$frase=$frases_con_venta_baja[rand(0,4)];
				else if(($quantity>50)&&($quantity<200))
					$frase=$frases_con_venta_media[rand(0,4)];
				else
					$frase=$frases_con_venta_grande[rand(0,4)];
					
		/*
		echo '
		
		<script type="text/javascript">

  function redirection(){  

  window.location ="http://www.gomugomu.es/feedback/admin/buyers.php";

  }  setTimeout ("redirection()", 10000);</script> 
		';*/
		?>
		
		<div class="notification" style="color:#608c21; font-weight:bold;">Añadido! <?php echo $frase;?>
		</div>
		
		<?php }
		?>
		<div id="content">
			<form method="post" name="myForm" action="?form=1<?php if($_GET['fecha']!="") echo '&fecha='.$_GET['fecha'];?>">
				Número de ticket :<br/><br/>
				<input type="text" name="ticket" class="input" value="<?php echo $ticket?>"><br/><br/>

				Cantidad :<br/><br/>
				<input type="text" name="value" class="input"><br/><br/>
				Nacionalidad :<br/><br/>
				<select name="from" id="from" class="input" onchange="setSelectedValue();">
					<option value="0" selected="selected"'>Español</option>
					<option value="1">Extranjero</option>
				</select><br/><br/>
				<!--Tienda:<br/><br/>
				<select name="store" class="input">
					<option value="0" <?php if($store=="0") echo 'selected="selected"'?>>Sorni</option>
					<option value="1" <?php if($store=="1") echo 'selected="selected"'?>>Arena</option>
				</select><br/><br/>-->
				Como nos ha conocido:<br/><br/>
				<select name="how" class="input2" id="how" onchange="habilitar();">
					<option value="0" <?php if($how=="0") echo 'selected="selected"'?>>Ya es cliente</option>
					<option value="1" <?php if($how=="1") echo 'selected="selected"'?>>Pasaba por aquí</option>
					<option value="2" <?php if($how=="2") echo 'selected="selected"'?>>Viajando fuera</option>
					<option value="9" <?php if($how=="9") echo 'selected="selected"'?>>Por un conocido</option>
					<option value="10" <?php if($how=="10") echo 'selected="selected"'?>>Corte ingles</option>				
					<option value="11" <?php if($how=="11") echo 'selected="selected"'?>>Colegios</option>
					<option value="12" <?php if($how=="12") echo 'selected="selected"'?>>Brand Ambassadors</option>
					<option value="7" <?php if($how=="7") echo 'selected="selected"'?>>Otros</option>
					<option value="8" <?php if($how=="8") echo 'selected="selected"'?>>Es turista</option>
				</select><br/><br/>
				Otros:<br/><br/>
				<textarea name="others" id="others" class="input3" disabled="disabled"></textarea>
				<input type="submit" class="button white" value="Aceptar">
			
			</form>
		</div>
<?php
			}else{
			mysql_query("UPDATE `feed_buyers`
SET 
`quantity` = '".$_POST['value']."',
`from` = '".$_POST['from']."',
`id_ticket` = '".$_POST['ticket']."',
`store` = '".$_POST['store']."',
`how` = '".$_POST['how']."',
`others` = '".$_POST['others']."'
WHERE id='".$_GET['id']."';") or die(mysql_error());
					echo '
		
		<script type="text/javascript">

  function redirection(){  

  window.location ="http://www.gomugomu.es/feedback/admin/buyers.php";

  }  setTimeout ("redirection()", 500);</script> 
		';
			?>
			<div class="notification" style="color:#608c21; font-weight:bold;">Editado! 
		</div>
		<div id="content">
			
		</div>

			<?php
			}
	}else{
			
?>
	<?php
		
		if($_GET['fecha']!="")
			$fecha=$_GET['fecha'];
		else
			$fecha=date('d-m-Y',time());
			
		$q = "SELECT MAX(id) FROM `feed_buyers`";
		$result = mysql_query($q);
		while($row = mysql_fetch_array($result)){
				$id= $row['MAX(id)'];
			}
			$quantity=0;
		$result = mysql_query("SELECT * FROM `feed_buyers` WHERE `id`='".$id."' AND `date`LIKE '".$fecha."%'");
		if(mysql_num_rows($result)>0){
			while($row = mysql_fetch_array($result)){
				$quantity = $row['quantity'];
			}
		}	
	?>
		<div class="notification">
		<?php
			
			if($quantity==0) {
				echo $frases_sin_venta[rand(0,4)];
			}else{
				if($quantity<50)
					$frase=$frases_con_venta_baja[rand(0,4)];
				else if(($quantity>50)&&($quantity<200))
					$frase=$frases_con_venta_media[rand(0,4)];
				else
					$frase=$frases_con_venta_grande[rand(0,4)];
				echo "La última compra fue de ".$quantity."€<br/>".$frase;
			}
		?>
		</div>
		<div id="content">
			<form method="post" name="myForm" action="?form=1<?php if($_GET['fecha']!="") echo '&fecha='.$_GET['fecha'];?>">
				Número de ticket :<br/>
				<input type="text" name="ticket" class="input" value="<?php echo $value?>"><br/><br/>

				Cantidad :<br/>
				<input type="text" name="value" class="input" value="<?php echo $value?>"><br/><br/>
				Nacionalidad :<br/>
				
				<select name="from" id="from" class="input2" onchange="setSelectedValue();">
					<option value="0" <?php if($from=="0") echo 'selected="selected"'?>>Español</option>
					<option value="1" <?php if($from=="1") echo 'selected="selected"'?>>Extranjero</option>
				</select><br/><br/>
				<!--Tienda:<br/><br/>
				<select name="store" class="input2">
					<option value="0" <?php if($store=="0") echo 'selected="selected"'?>>Sorni</option>
					<option value="1" <?php if($store=="1") echo 'selected="selected"'?>>Arena</option>
				</select><br/><br/>-->
				Como nos ha conocido:<br/>
				<select name="how" class="input2" id="how" onchange="habilitar();">
					<option value="0" <?php if($how=="0") echo 'selected="selected"'?>>Ya es cliente</option>
					<option value="1" <?php if($how=="1") echo 'selected="selected"'?>>Pasaba por aquí</option>
					<option value="2" <?php if($how=="2") echo 'selected="selected"'?>>Viajando fuera</option>
					<option value="9" <?php if($how=="9") echo 'selected="selected"'?>>Por un conocido</option>
					<option value="10" <?php if($how=="10") echo 'selected="selected"'?>>Corte ingles</option>
					<option value="11" <?php if($how=="11") echo 'selected="selected"'?>>Colegios</option>
					<option value="12" <?php if($how=="12") echo 'selected="selected"'?>>Brand Ambassadors</option>
					<option value="7" <?php if($how=="7") echo 'selected="selected"'?>>Otros</option>
					<option value="8" <?php if($how=="8") echo 'selected="selected"'?>>Es turista</option>
				</select><br/><br/>
				Otros:<br/>
				<textarea name="others" id="others" class="input3" disabled="disabled"></textarea>
				<input type="submit" class="button white" value="Aceptar">
			</form>
		</div>
		<?php
		
		
		
	
}
?>
<div id="content-time">
<b>Historial de ventas [<?php echo date('d-m-Y',time()); ?>]</b><br/><br/>
<?php
	if($_GET['fecha']!="")
		$fecha=$_GET['fecha'];
	else
		$fecha=date('d-m-Y',time());
		
	$query=mysql_query("SELECT * FROM `feed_buyers` WHERE `date` LIKE '".$fecha."%' ORDER BY `id_ticket`");
	$sumar=0;
		$i=1;
		$a=0;
		$b=0;
		$e=0;$ev=0;
		$f=0;$fv=0;
		$t=0;$tv=0;
	while($dato=mysql_fetch_array($query)){
		if($dato['from']==0){
			$from="Español";
			$e++;
			$ev+=$dato['quantity'];
		}else{
			$from="De fuera";
			$f++;
			$fv+=$dato['quantity'];
		}
		$t++;
		$tv+=$dato['quantity'];
		if($dato['store']==0){
			$store="Sorni";
			$a+=$dato['quantity'];
		}else{
			$store="Arena";
			$b+=$dato['quantity'];
		}	

		echo "[ <b>
		<a href=".'"?go=edit&
		id='.$dato['id'].'&
		ticket='.$dato['id_ticket'].'
		&quantity='.$dato['quantity'].'
		&from='.$dato['from'].'
		&store='.$dato['store'].'
		&how='.$dato['how'].'
		&others='.$dato['others'].'
		"'.">".
		
		$dato['id_ticket']."</a></b> ] ".substr($dato['date'], -8)." - ".$dato['quantity']."€ - ".$from."<br/>";
		
		$i++;	
	}
	$sumar=$a+$b;
	echo '<hr></hr>'.number_format($a,2,',','.').'€';
	if ($b>0)
		echo 'Arena: <b>'.$b.'</b><br/>';
	
	$week=media_dia();
	
	$break=explode("-",$fecha);
	$date=mktime(0, 0, 0, $break[1], $break[0], $break[2]);
	$day=strftime("%A",$date);
	
	$day_of_the_week=date('N', $date);
?>
</div>
<div id="stats-hoy">
	Español [ <b><?php  echo round(($e/$t)*100,2)?>%</b> ] 
	<div style="padding:5px 0px; background-color:#FF9900;width:<?php  echo ($e/$t)*100?>%; margin:6px 0px; border-radius: 3px;"></div>
		<div style="padding:5px 0px; background-color:#990000;width:<?php  echo ($f/$t)*100?>%; margin:6px 0px; border-radius:3px; ">
	
	</div>
	Fuera [ <b><?php  echo round(($f/$t)*100,2)?>%</b> ] <br/>
<hr/>
	Español [ <b><?php echo $ev; ?>€</b> ]
	<div style="padding:5px 0px; background-color:#FF9900;width:<?php  echo ($ev/$tv)*100?>%; margin:6px 0px; border-radius: 3px;">
	</div>
	<div style="padding:5px 0px; background-color:#990000;width:<?php  echo ($fv/$tv)*100?>%; margin:6px 0px; border-radius: 3px;">
	</div>	
	Fuera [ <b><?php echo $fv; ?>€</b> ]
</div>

<div id="total-hoy">

	Hemos abierto <b><?php echo $week[$day]['num'] ?></b> <?php echo $day_week[$day_of_the_week]?>
	con una media de <b><?php echo number_format( $week[$day]['total']/$week[$day]['num'],2,',','.') ?>€<br/><br/><br/>
	

	Total: <h1><?php echo number_format($a,2,',','.'); ?>€</h1>
</div>
		
</body>
</html>