<?php
define("_MSSQL_SERVER","superdry.bscorp.be");
define("_MSSQL_USERNAME","Fernando");
define("_MSSQL_PASSWORD","S@l0m");
define("_MSSQL_DB","ES_Valencia");
define("_SERVER","localhost");
define("_USERNAME","dashboard");
define("_PASSWORD","osaka2011");
define("_BD","admin_dashboard");
define("_ALTO","200");

	function graph($a,$b,$op=''){
		
		$total=$b['value']+$a['value'];
		$per_a=($a['value']/$total)*100;
		$per_b=($b['value']/$total)*100;
		if($op==''){
			$value_a=$per_a;
			$value_b=$per_b;
			$symbol='%';
		}else{
			$value_a=$a['value'];
			$value_b=$b['value'];
			$symbol='€';
		}		
		
			$graph=	'<div class="stats">
						<div class="statsbar" style="width:'.$per_a.'%; background-color:#FF9900;">
						'.number_format($value_a,2,',','.').$symbol.'
						</div> 
						<div class="statsbar" style="width:'.$per_b.'%; background-color:#ffd076;">
						'.number_format($value_b,2,',','.').$symbol.'
						</div> 
						<div class="clear"></div>
						<div class="statsbar" style="width:'.$per_a.'%;">
						'.$a['name'].'
						</div> 
						<div class="statsbar" style="width:'.$per_b.'%;">
						'.$b['name'].'
						</div> 
						<div class="clear"></div>
					</div>';
			return $graph;
	}
	
	function get_ticket_month($month,$year){
		$mysqli = new mysqli(_SERVER, _USERNAME, _PASSWORD, _BD);
		if ($mysqli->connect_errno) {
		    printf("La conexión ha fallado: %s\n", $mysqli->connect_error);
		    exit();
		}
		$result = $mysqli->query("SELECT id_ticket FROM `dash_ticket` WHERE 
				date LIKE '".$year."-".$month."%' ORDER BY `id_ticket` DESC LIMIT 1"
				)or die ($mysqli->error());
		$row = $result->fetch_array();		
		$value['f']=$row['id_ticket'];
		
		$result = $mysqli->query("SELECT id_ticket FROM `dash_ticket` WHERE 
				date LIKE '".$year."-".$month."%' ORDER BY id_ticket ASC LIMIT 1"
				)or die ($mysqli->error());
		$row = $result->fetch_array();
		$value['i']=$row['id_ticket'];
		
		
		$result->close();
		return $value;
	}
	
	function stats($option,$n,$y){
		$mysqli = new mysqli(_SERVER, _USERNAME, _PASSWORD, _BD);
		if ($mysqli->connect_errno) {
		    printf("La conexión ha fallado: %s\n", $mysqli->connect_error);
		    exit();
		}
		if($option=='W'){
			$month=date('m', strtotime($y.'-W'.$n));;
		}else{
			if($n[0]!=0){
				if($n<10)
					$month='0'.$n;
				else
					$month=$n;
			}else{
				$month=$n;
			}
		}
		$id_ticket=get_ticket_month($month,$y);
		
		$result = $mysqli->query("SELECT count(country) FROM `dash_buyer` WHERE 
				id_ticket >= '".$id_ticket['i']."'
				AND id_ticket <='".$id_ticket['f']."'
				AND country= 'ES'"
				)or die ($mysqli->error());
			   
		$row = $result->fetch_array();
		$a['value']=$row['count(country)'];
		$a['name']="Españoles";
		
		$result = $mysqli->query("SELECT count(country) FROM `dash_buyer` WHERE 
				id_ticket >= '".$id_ticket['i']."'
				AND id_ticket <='".$id_ticket['f']."'
				AND country<> 'ES'"
				)or die ($mysqli->error());
			   
		$row = $result->fetch_array();
		$b['value']=$row['count(country)'];
		$b['name']="Resto";
		
		$graph['country']=graph($a,$b);
		
		$result = $mysqli->query("SELECT count(sex) FROM `dash_buyer` WHERE 
				id_ticket >= '".$id_ticket['i']."'
				AND id_ticket <='".$id_ticket['f']."'
				AND sex= 'M'"
				)or die ($mysqli->error());
			   
		$row = $result->fetch_array();
		$a['value']=$row['count(sex)'];
		$a['name']="Hombres";
		
		$result = $mysqli->query("SELECT count(sex) FROM `dash_buyer` WHERE 
				id_ticket >= '".$id_ticket['i']."'
				AND id_ticket <='".$id_ticket['f']."'
				AND sex= 'F'"
				)or die ($mysqli->error());
			   
		$row = $result->fetch_array();
		$b['value']=$row['count(sex)'];
		$b['name']="Mujeres";
		
		$graph['buyers']=graph($a,$b);
		
		
		$result = $mysqli->query("SELECT SUM(items) FROM `dash_ticket` WHERE 
				id_ticket >= '".$id_ticket['i']."'
				AND id_ticket <='".$id_ticket['f']."'
				AND referencenr LIKE 'M%'"
				)or die ($mysqli->error());
			   
		$row = $result->fetch_array();
		$a['value']=$row['SUM(items)'];
		$a['name']="Chico";
		
		$result = $mysqli->query("SELECT SUM(items) FROM `dash_ticket` WHERE 
				id_ticket >= '".$id_ticket['i']."'
				AND id_ticket <='".$id_ticket['f']."'
				AND referencenr LIKE 'G%'"
				)or die ($mysqli->error());
			   
		$row = $result->fetch_array();
		$b['value']=$row['SUM(items)'];
		$b['name']="Chica";
		
		$graph['sex']=graph($a,$b);
		
		
		$result->close();
		
		return $graph;
	}
	
	
	function get_info($option, $n, $year){
		$mysqli = new mysqli(_SERVER, _USERNAME, _PASSWORD, _BD);
		if ($mysqli->connect_errno) {
		    printf("La conexi�n ha fallado: %s\n", $mysqli->connect_error);
		    exit();
		}
		if($option=='W'){
			$month=date('m', strtotime($year.'-W'.$n));;
		}else{
			if($n[0]!=0){
				if($n<10)
					$month='0'.$n;
				else
					$month=$n;
			}else{
				$month=$n;
			}
		}
		$mn=get_month($month,$year);
		$id_ticket=get_ticket_month($month,$year);
		$value['tickets']=$id_ticket['f']-$id_ticket['i'];
		
		
		$totalOri=0;
		$result = $mysqli->query("SELECT subtotalOriginal, items FROM `dash_ticket` WHERE 
				date LIKE '".$year."-".$month."%'
				AND description<>'Waardebon'
				AND description<> 'KORTING'
				"
				)or die ($mysqli->error());
			   
		while($row1 = $result->fetch_array()){
			$totalOri+=$row1['subtotalOriginal']*$row1['items'];
		}
		$result->close();
		
		
		
		
		$result = $mysqli->query("SELECT SUM(subtotalOriginal), count(id_ticket) FROM `dash_ticket` WHERE 
				date LIKE '".$year."-".$month."%'
				AND description<>'Waardebon'
				AND description<> 'KORTING'
				AND items <> '-1'"
				)or die ($mysqli->error());
			   
		$row1 = $result->fetch_array();
		$result->close();
			   $decimales=0;
			   $value['discount']= number_format(
			   					(1-($mn[0]['total']/$totalOri))*100
			   					,1,',','.');
			   $value['real']=number_format($mn[0]['total'],$decimales,',','.');
			   $value['original']=number_format(
			   							round($totalOri,2)
			   							,$decimales,',','.');
			   $value['cost']=number_format($totalOri/2.94,$decimales,',','.');
			   $value['margin']=number_format(($mn[0]['total']
			   							-($mn[0]['total']-($mn[0]['total']/1.21))
			   							-($totalOri/2.94)
			   							-($mn[0]['total']*0.03)
			   							),$decimales,',','.');
			   $value['IVA']=number_format($mn[0]['total']-($mn[0]['total']/1.21),$decimales,',','.');
			   $value['items']=$row1['count(id_ticket)'];
			   $value['perticket']=number_format($value['items']/$value['tickets'],2,',','.');
			   $value['average']=number_format($totalOri/$value['tickets'],$decimales,',','.');
			   
			   $result = $mysqli->query("SELECT SUM(value) FROM `dash_target` WHERE
				year = '".$year."'
			   	AND month>=1
			   	AND month<='".$month."'
			   	AND week=''" 
			   )or die ($mysqli->error());
			  
			   
			   
			   $accTarget = $result->fetch_array();
			   $value['acc_target']=number_format($accTarget['SUM(value)'],2,',','.');
			   $result->close();
			   
			   $result = $mysqli->query("
			   	SELECT SUM(quantity)
				FROM dash_payment
				WHERE YEAR(date)=".$year." 
			   	AND MONTH(date) BETWEEN 1 
			   	AND ".$month."
			   	AND type!='Waardebon'"
			   )or die ($mysqli->error());
			   
			   $accReal = $result->fetch_array();
			   $result->close();
			   $value['acc_real']=number_format($accReal['SUM(quantity)'],2,',','.');	
			   
			   $acc_balance=$accReal['SUM(quantity)']-$accTarget['SUM(value)'];

			   if($acc_balance>0)
			   		$value['acc_balance']['color']="green";
			   else 	
			   		$value['acc_balance']['color']="red";
			   
			   $value['acc_balance']['value']=number_format($acc_balance,2,',','.');
			   
			   
			   return $value;
			   
			   
	}
/**************************************************/
// Devuelve el ob
/**************************************************/
	function get_target($option,$n,$year){
		$mysqli = new mysqli(_SERVER, _USERNAME, _PASSWORD, _BD);
		if ($mysqli->connect_errno) {
		    printf("La conexión ha fallado: %s\n", $mysqli->connect_error);
		    exit();
		}
		if($option=='W')
			$m=date('m', strtotime($year.'-W'.$n));
			if($m[0]=='0')
				$month= $m[1];
		else{
			$month=$n;
			if($m[0]=='0')
				$month= $m[1];
		}	
		
			
		if ($result = $mysqli->query("
				SELECT value
				FROM `dash_target`
				WHERE 
				week=".$n." AND
				year=".$year
				)) {
			   
			   $row = $result->fetch_array();
			   $result->close();
			   $value['w']=$row['value'];
			 }
		if ($result = $mysqli->query("
				SELECT value
				FROM `dash_target`
				WHERE 
				month=".$month." AND
				year=".$year
				)) {
			   
			   $row = $result->fetch_array();
			   $result->close();
			   $value['m']=$row['value'];
			 }
		return $value;
	}
/**************************************************/
// Devuelve la primera y última fecha de todos los tickets existentes
/**************************************************/
	function target_graph($option,$n,$year){
		$value=get_target($option,$n,$year);
		$week=get_week($n,$year);
		
		
		//echo $option.' '.$n.' '.$year;
		if($option=='W'){
			$mn=date('m', strtotime($year.'-W'.$n));
			if($mn[0]=='0')
				$mn= $mn[1];
		}else{
			$mn=$n;
		}
		$month=get_month($mn,$year);
		
		
		$totalm=$month[0]['total'];
		$totalw=$week[0]['total'];
		
		$color='FF9900';
		if($option=='W'){
			$h=_ALTO*($totalw/$value['w']);
			$th=_ALTO;
			$margin=_ALTO-$h;
			if($h>_ALTO){
				$h=_ALTO;
				$th=_ALTO*($value['w']/$totalw);
				
				$tmargin=_ALTO-$th;
				$margin=0;
				$color='99cc33';
			}
			
			
			
			$content['week']='<div id="target">
					  <div class="bar-content">
						  <div class="bar" style="background-color:#'.$color.';margin-top:'.$margin.'px; height:'.$h.'px; ">
						  '.number_format($totalw,2,',','.').'€
						  </div>
					  </div>
					  <div class="bar-content">
					  	<div class="bar" style="background-color:#e5e5e5; margin-top:'.$tmargin.'px; height:'.$th.'px; ">
					  	'.number_format($value['w'],2,',','.').'€
					  	</div>
					  </div>
					  </div>	';
					  $content['week_raw']=$value['w'];
			
		}
		
		$w=($totalm/$value['m'])*100;
		$color='FF9900';
		if($w>100){
			$w=100;
			$color='99cc33';
		}
			$content['value_target']=number_format($value['m'],2,',','.');
			$content['value_raw']=$value['m'];
			$content['month']='
			
			<div id="tmonth">
							   		<div class="hbar" style="width:'.$w.'%; background-color:#'.$color.';">
							   		'.number_format($totalm,2,',','.').'€
							   		</div> 
							   </div>';
		return $content;	
	}
/**************************************************/
// Devuelve la primera y última fecha de todos los tickets existentes
/**************************************************/
	function get_date(){
		$mysqli = new mysqli(_SERVER, _USERNAME, _PASSWORD, _BD);
		
		if ($mysqli->connect_errno) {
		    printf("La conexi—n ha fallado: %s\n", $mysqli->connect_error);
		    exit();
		}
		if ($result = $mysqli->query("
				SELECT date
				FROM `dash_ticket`
				ORDER BY id DESC LIMIT 1"
				)) {
			   
			   $row = $result->fetch_array();
			   $result->close();
			   $date['f']=$row['date'];
			  
			 }
		if ($result = $mysqli->query("
				SELECT date
				FROM `dash_ticket`
				ORDER BY id ASC LIMIT 1"
				)) {
			   
			   $row = $result->fetch_array();
			   $result->close();
			   $date['i']=$row['date'];
			  
			 }
		return $date;
	}
/**************************************************/
// Devuelve el desplegable con todos los meses y semanas en el programa
/**************************************************/
	function get_option($option, $n,$y){
		
		if($option=='W'){
			$search='W';
			$fin=53;
		}else{
			
			$search='m';
			$fin=13;
		}
		if($option=='')$option='m';
		$date=get_date();
		$fn=date($search,strtotime($date['f']));
		$fyear= date('Y',strtotime($date['f']));	
		$in= date($search,strtotime($date['i']));
		$iyear= date('Y',strtotime($date['i']));
		
		
		$select=
		'<form method="GET">
		<select name="option" onchange="this.form.submit()">';
		$inicio=$in;
		$extra='';
		for($i=$iyear;$i<$fyear+1;$i++){
			if($i==$fyear)
				$fin=$fn+1;
			for($c=$inicio;$c<=$fin;$c++){
				if($n!=''){
					if(($c==$n)&&($i==$y))
						$extra='selected="selected"';
					else
						$extra='';
				}else{
					if(($i==$fyear)&&($c==$fn))
						$extra='selected="selected"';
					else
						$extra='';
				}
				$select.='<option value="'.$option.'-'.$i.'-'.$c.'" '.$extra.'>'.$i.'-'.$c.'</option>';
			}
			$inicio=1;
		}
		
		$select.='</select></form>';
		
		echo $select;
	}
/**************************************************/
// Devuelve la cantidad de hoy
/**************************************************/
	function get_today(){
		$link = mssql_connect(_MSSQL_SERVER, _MSSQL_USERNAME, _MSSQL_PASSWORD);
		if (!$link)
		    die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
		
		$selected = mssql_select_db( _MSSQL_DB, $link) 
		or die("Couldn't open database $myDB ." .mssql_get_last_message() ); 
		
		$fecha=date('M j Y',time());

		
			$begin=time();
			$dy=date("Y",$begin);
			$dm=date("m",$begin);
			$dd=date("j",$begin);
	
		
		$queryDetail = '
		SELECT SUM(bedrag) FROM VerkoopBetaling WHERE  
	  (DATEPART(yy, Datum) = "'.$dy.'"
AND    DATEPART(mm, Datum) = "'.$dm.'"
AND    DATEPART(dd, Datum) = "'.$dd.'")
		AND Betalingswijze!="Waardebon"';
		
		$resultDetail = mssql_query($queryDetail) 
		 	or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
		 
		 
		$data =mssql_fetch_array($resultDetail);
		$hoy= $data['computed'];

		mssql_close($link);
		return $hoy;
	}
/**************************************************/
// Devuelve la cantidad de un dia dado
/**************************************************/
	function get_quantity($day){
		$mysqli = new mysqli(_SERVER, _USERNAME, _PASSWORD, _BD);
		
		if ($mysqli->connect_errno) {
		    printf("La conexi—n ha fallado: %s\n", $mysqli->connect_error);
		    exit();
		}
		
		if($day==date('Y-m-d',time())){
			$dato=round(get_today(),2);
		}else{
			if ($result = $mysqli->query("
				SELECT SUM(quantity)
				FROM `dash_payment`
				WHERE date LIKE '".$day."%'
				AND type!='Waardebon'")) {
			   
			   $row = $result->fetch_array();
			   $result->close();
			   $dato=round($row['SUM(quantity)'],2);
			 }
		}
		$mysqli->close();
		return  $dato;
		   
	}
/**************************************************/
// devuelve un array con las cantidades de cada día de la semana y el máximo
/**************************************************/
	function get_week($week_number,$year){
		$max=0;
		$total=0;
		if($week_number<10)
			$week_number='0'.$week_number;
		for($day=1; $day<=7; $day++){
	    	$date=date('Y-m-d', strtotime($year."W".$week_number.$day));
	    	$week[$day]['value']=get_quantity($date);
	    	$week[$day]['date']=$date;
	    	$total+=get_quantity($date);
	    	if($max<$week[$day]['value'])
	    		$max=$week[$day]['value'];
	    		
	    }
	    $week[0]['value']=$max;
	    $week[0]['total']=$total;
	    return $week;
	}
/**************************************************/
// devuelve un array con las cantidades de cada día del mes y el máximo
/**************************************************/
	function get_month($m, $year){
		$n = cal_days_in_month(CAL_GREGORIAN, $m, $year);
		if(strlen($m)<2)
			$m='0'.$m;
		$max=0;	
		for($day=1;$day<=$n;$day++){
			$d=$day;
			if($day<10)
				$d='0'.$day;
			$date=$year.'-'.$m.'-'.$d;
			$month[$day]['value']=get_quantity($date);
			$month[$day]['date']=$date;
			$total+=get_quantity($date);
			if($max<$month[$day]['value'])
	    		$max=$month[$day]['value'];
		}
		$month[0]['value']=$max;
		$month[0]['total']=$total;
		return $month;		
	}
/**************************************************/
// crea las graficas del mes y la semana
/**************************************************/
	function get_stats($option,$n,$year){
		
		$days = array("", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
		
		if($option=='m'){
			$stats=get_month($n,$year);
		}else{
			$stats=get_week($n,$year);
		}
		$max=$stats[0]['value'];
		$i=1;
		$n=count($stats);
		if($option=='m')
			$returned='	<div id="month"><div id="wrapper">	';
		else
			$returned='<div id="week"><div id="week-wrapper">';
		for ($i=1;$i<$n;$i++){
			$h=($stats[$i]['value']*_ALTO)/$max;
			$m=(_ALTO-$h);
			$color='#e5e5e5';
			if($stats[$i]['date']==date('Y-m-d',time()))
				$color='#FF9900';
			//echo $h.' '.$m.'<br/>';
			
			$returned.= '
			<div class="bar-content">
			<div class="bar" 
			style="height:'.$h.'px; margin-top:'.$m.'px; background-color:'.$color.'">
			'.number_format($stats[$i]['value'],2,',','.').'€
			</div>
			<br/>';
			if($option=='w')
				$returned.=$days[$i].'<br/> '.date('d-m-Y',strtotime($stats[$i]['date']));
			else
				$returned.=$days[date('N',strtotime($stats[$i]['date']))].'<br/>'.date('d-m-Y',strtotime($stats[$i]['date']));
			$returned.='</div>';
		}
		
		
			$returned.='</div></div>';	
		
		echo $returned;
	}

function get_diff($value,$value1,$symbol){
	$value=str_replace('.','',$value);
	$value=str_replace(',','.',$value);
	$value1=str_replace('.','',$value1);
	$value1=str_replace(',','.',$value1);
	$red="red";
	$green="green";
	$diff=$value-$value1;
	
	switch($symbol){
		case '%':
			$red="green";
			$green="red";
			$diff_format=number_format($diff,1,',','.');
		break;
		case '�':
			$diff_format=number_format($diff,2,',','.');
		break;
		case 'pt':
			$diff_format=number_format($diff,2,',','.');
		break;
		default:
			$diff_format=number_format($diff,0,',','.');
		break;
	}
	
	
	if($diff<0)
		return '<b class="'.$red.'">'.$diff_format.$symbol.'</b>';
	else
		return '<b class="'.$green.'">'.$diff_format.$symbol.'</b>';
	
}
function update_target($value, $date){
	$mysqli = new mysqli(_SERVER, _USERNAME, _PASSWORD, _BD);
		
		if ($mysqli->connect_errno) {
		    printf("La conexión ha fallado: %s\n", $mysqli->connect_error);
		    exit();
		}
		
		//echo 'value:'.$value.' fecha:'.$date.'<br/>';
			$break=explode('-',$date);
			$type=$break[0];
			$n=$break[2];
			$y=$break[1];
		if($type=='m'){
			if ($result = $mysqli->query("
				SELECT *
				FROM `dash_target`
				WHERE month = '".$n."'
				AND year='".$y."'")) {
			   	
					if($result->num_rows==0){
							
						$result = $mysqli->query("
							INSERT INTO `dash_target`
							(month,year,week,value)
							VALUES
							('".$n."','".$y."','0','".$value."')") or die($mysqli->error);
					}else{
						$result = $mysqli->query("
							UPDATE `dash_target`
							SET value='".$value."'
							WHERE month='".$n."' AND year='".$y."'");
						
					}
									
			}
		}else{
			if ($result = $mysqli->query("
				SELECT *
				FROM `dash_target`
				WHERE week = '".$n."'
				AND year='".$y."'")) {
			   
					if($result->num_rows==0){
						$result = $mysqli->query("
							INSERT INTO `dash_target`
							(week,year,month,value)
							VALUES
							('".$n."','".$y."','0','".$value."')");
					}else{
						$result = $mysqli->query("
							UPDATE `dash_target`
							SET value='".$value."'
							WHERE week='".$n."' AND year='".$y."'");
						
					}
			}
		}
		//header("Location: http://gomugomu.es/dashboard/admin/?option=".$date);
		//die();
		$mysqli->close();
}
?>