<?php
define("_SERVER_OLD","localhost");
define("_USERNAME_OLD","gomugomu");
define("_PASSWORD_OLD","osaka2011");
define("_BD_OLD","admin_feedback");

define("_SERVER","localhost");
define("_USERNAME","dashboard");
define("_PASSWORD","osaka2011");
define("_BD","admin_dashboard");

define("_MSSQL_SERVER","superdry.bscorp.be");
define("_MSSQL_USERNAME","Fernando");
define("_MSSQL_PASSWORD","S@l0m");
define("_MSSQL_DB","ES_Valencia");


function stats(){
/*********************************************************************************
CAMBIAR CUANDO ESTE PASADO A DASHBOARD LOS DATOS DE OBJETIVOS SEMANALES/MENSUALES
*********************************************************************************/
	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	mysql_select_db(_BD, $conexion);
	
	$year=date('Y',time());
	$week=date('W',time());
	
	$year_ini = date("Y",strtotime($year."W".str_pad($week,2,0,STR_PAD_LEFT).'0'));
	$year_end = date("Y",strtotime($year."W".str_pad($week,2,0,STR_PAD_LEFT).'7'));
	//echo $week.' '.$year.' '.$year_ini.' '.$year_end;
	if($year_ini!=$year_end)
		$year=$year_ini;
		
	//	$year_day = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).$day_of_week));
	
	
	
	$query=mysql_query("SELECT value, margin, items, tickets,men, women,acc FROM `dash_target` WHERE `week`='".$week."' AND `year`='".$year."'")
	or die(mysql_error());
	
	$data=mysql_fetch_array($query);
	$objetivo=$data['value'];
	
	$margen_valor_completo=$data['value']*(($data['margin']/100)+1);
	$margen_coste_ropa=$margen_valor_completo/2.94;
	$margen_iva=$data['value']-($data['value']/1.21);
	$margen_royalti=$data['value']*0.03;
	

	
	$data_objetivo['total_margen']=round($data['value']-$margen_coste_ropa-$margen_iva-$margen_royalti,0);
	$data_objetivo['margin']=$data['margin'];
	$data_objetivo['items']=$data['items'];
	$data_objetivo['tickets']=$data['tickets'];
	
	if($data['acc']=='') $data['acc']=34;
	$data_objetivo['acc']=$data['acc'];
	if($data['men']=='') $data['men']=33;
	$data_objetivo['men']=$data['men'];
	if($data['women']=='') $data['women']=33;
	$data_objetivo['women']=$data['women'];
	
	$data_objetivo['this_week']=$data['value'];
	
	//echo '--------------------'.$data_objetivo['men'];
	//echo '--------------------'.$data_objetivo['women'];
	$date1 = date(
    'Y-m-d', 
    strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT))
    );
   
    /********************************************************
    ACCEDEMOS A DASHBOARD
    *********************************************************/
   

    $query=mysql_query("SELECT id_ticket FROM `dash_ticket` WHERE `date` LIKE '".$date1."%' ORDER BY `id` ASC LIMIT 1")
	or die(mysql_error());
	$no=1;
	$data=mysql_fetch_array($query);
	$id_ticket=$data['id_ticket'];
	if($id_ticket==0){
		$query=mysql_query("SELECT id_ticket FROM `dash_ticket` ORDER BY `id` DESC LIMIT 1")
	or die(mysql_error());
		$data=mysql_fetch_array($query);
		$id_ticket=$data['id_ticket'];
		$id_ticket++;
	}
	
	//calculamos el valor de los tickets que ya tenemos guardados
	
	$query=mysql_query("SELECT SUM(quantity) FROM `dash_payment` WHERE `id_ticket`>='".$id_ticket."' AND type!='Waardebon'")
	or die(mysql_error());
	$data=mysql_fetch_array($query);
	$hastahoy=round($data['SUM(quantity)'],2);
	/********************************************************
    ACCEDEMOS A BECOSOFT PARA CALCULAR LA CANTIDAD QUE LLEVAMOS
    *********************************************************/
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
		
	
	
	 //$queryDetail = "SELECT SUM(bedrag) FROM VerkoopBetaling WHERE Datum LIKE '".$fecha."%' AND Betalingswijze!='Waardebon'";
	 $resultDetail = mssql_query($queryDetail) 
	 	or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
	 
	 
	$data =mssql_fetch_array($resultDetail);
	print_r($DATA);
	$hoy= $data['computed'];
	/**********************************************************
	Calcular estadísticas
	*********************************************************/
	
	$hastahoy_per=round($hastahoy/$objetivo,2)*100;
	$hoy_per=round($hoy/$objetivo,2)*100;
	$total=($hastahoy+$hoy);
	$stats='<div class="stats">';
	if($total>$objetivo){
		$stats.='<div class="bar" style="width:100%; background-color:#99cc33;"></div>';
	}else{
		$stats.='<div class="bar" style="width:'.$hastahoy_per.'%; background-color:#ffd076;"></div>';
		$stats.='<div class="bar" style="width:'.$hoy_per.'%; background-color:#FF9900;"></div>';
	}
	$stats.='<div class="clear"></div>';
	$stats.='</div>';
	
	
	$data['graph']=$stats;
	$data['untilnow']=number_format(($hastahoy+$hoy),2,',','.');
	$data['target']=number_format($objetivo,0,',','.');
	$data['target']['per']=round(($hoy+$hastahoy)/$objetivo,2)*100;
	
	$data['margin']=$data_objetivo['margin'];
	$data['items']=$data_objetivo['items'];
	$data['tickets']=$data_objetivo['tickets'];
	$data['total_margen']=$data_objetivo['total_margen'];
	
	$data['acc']=$data_objetivo['acc'];
	$data['men']=$data_objetivo['men'];
	$data['women']=$data_objetivo['women'];
	$data['this_week']=$data_objetivo['this_week'];
	$data['week_target']=get_stats_gender($data_objetivo['men'],$data_objetivo['women'],0);
	
	
	return $data;
}

function get_target_data($objetivo,$valor,$simbolo=''){
	//Para comprobar los valores que entran
	//echo $objetivo.' '.$valor.' '.$simbolo.'<br/>';
	
	$v['value']=$objetivo;
	if($simbolo){
			//ESTO ES PARA EL CASO DE QUE EL SIMBOLO SEA % PORQUE FUNCIONA AL REVES QUE EL RESTO (DESCUENTO) CUANTO MENOS MEJOR
			$v['per']=round($objetivo/$valor,2)*100;
			if($v['per']>=100 || $v['per']==0)
				$v['per']=100;
			
			$v['color']="#99cc33";
			if($v['per']<100)
				$v['color']="#FF9900";
		
	}else{
		
			$v['per']=round($valor/$objetivo,2)*100;
			if($v['per']>=100)
				$v['per']=100;
		
			$v['color']="#99cc33";
			if($v['per']<100)
				$v['color']="#FF9900";
		
	}
	return $v;
}

function get_stats_country($ES,$REST){
	$objetivo=$ES+$REST;
	$E=round($ES/$objetivo,2)*100;
	$R=100-$E;
	$stats='<div class="stats">';

		$stats.='<div class="bar" style="width:'.$E.'%; background-color:#FF9900;">'.$E.'%</div>';
		$stats.='<div class="bar" style="width:'.$R.'%; background-color:#FFD076;">'.$R.'%</div>';
		
	$stats.='<div class="clear"></div>';
	$stats.='</div>';
	return $stats;	
}
function get_stats_gender($M,$G,$U){
	if($M<0)
		$M=0;
	if($G<0)
		$G=0;
	if($U<0)
		$U=0;
	$objetivo=$M+$G+$U;
	//echo "Objetivo: " . $objetivo."<br/>";
	//echo $M. " " . $G . " ".$U."<br/>";
	$Men=round($M/$objetivo,2)*100;
	$Wom=round($G/$objetivo,2)*100;
	$Acc=100-$Men-$Wom;
	//echo $Men. " " . $Wom . " ".$Acc."<br/>";
	if($Acc<0.5)	
		$ACC_data=0;
	else 
		$ACC_data=$Acc.'%';
	
	$stats['women']=$Wom;
	$stats['graph']='<div class="stats">';

		$stats['graph'].='<div class="bar" style="width:'.$Men.'%; background-color:#8cbdeb;">'.$Men.'%</div>';
		$stats['graph'].='<div class="bar" style="width:'.$Wom.'%; background-color:#eb8cb4;">'.$Wom.'%</div>';
		$stats['graph'].='<div class="bar" style="width:'.$ACC_data.'; background-color:#ff9900;">'.$ACC_data.'</div>';
	
	$stats['graph'].='<div class="clear"></div>';
	$stats['graph'].='</div>';
	return $stats;	
}

function get_desc_ponderado($idInicio,$idFin){
	//CALCULAMOS EL DESCUENTO PONDERADO DE UN RANGO DE ID
	$desc_C=0;
	$i=0;
	$query=mysql_query("SELECT subtotal, subtotalOriginal, items
						FROM `dash_ticket`
						WHERE `id_ticket` >= '".$idInicio."'
						AND `id_ticket`<'".$idFin."'
						AND `description`!='Waardebon'");
	while($row=mysql_fetch_array($query)){				
		
		$desc=(1-($row['subtotal']/$row['subtotalOriginal']))*100;
		$desc_C+=round($desc,0);
		$i++;
		
	}
	
	return ($desc_C/$i);
}

function get_data_week($today,$year){
	if($year=="")
		$year=date('Y',time());

	$week=date('W',time());
	
    if($week+1>53)
    	$week=1;
    //if($week==1)
    //	$year++;
    
     $check_day = date(
    		'm-d',
    		strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT))
    );
     $retrocedemos_inicio=0;
    $week_begin=$week;
    $holiday=array("01-06","12-25","05-01");
    if(in_array($check_day, $holiday)){    	
    	$week_begin-=1;
    	if($week_begin<10 && $week_begin[0]!="0")
    		$week_begin='0'.$week_begin;
    	$retrocedemos_inicio=1;	
    	$date = date('Y-m-d', strtotime($year."W".$week_begin."7"));
    }else{
    	$date = date(
    			'Y-m-d',
    			strtotime($year . 'W' . str_pad($week_begin, 2, '0', STR_PAD_LEFT))
    	);
    }
    
    /*
    $date = date(
    		'Y-m-d',
    		strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT))
    );
    */
    $check_day = date(
    		'm-d',
    		strtotime($year . 'W' . str_pad($week+1, 2, '0', STR_PAD_LEFT))
    );
    
    //Colocamos los dias que son fiesta seguro en caso de que caigan en lunes buscamos el martes
   
    if(in_array($check_day, $holiday)){    	
    	$week_next = $week + 1;
    	if($week_next<10)
    		$week_next='0'.$week_next;
    	$date_next = date('Y-m-d', strtotime($year."W".$week_next."2"));
    }else{
    	$date_next = date(
    			'Y-m-d',
    			strtotime($year . 'W' . str_pad($week+1, 2, '0', STR_PAD_LEFT))
    	);
    }
    
    //echo $check_day.' - '.$date.' - '.$date_next.'<br/>';
    //Conectar BBDD--------------------------------------
    $conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	mysql_select_db(_BD, $conexion);
    //---------------------------------------------------
	if($retrocedemos_inicio==0){
		$query=mysql_query("SELECT id_ticket 
							FROM `dash_ticket` 
							WHERE `date` LIKE '".$date."%' ORDER BY `id` ASC LIMIT 1")
							or die(mysql_error());
	}else{
		$query=mysql_query("SELECT id_ticket
							FROM `dash_ticket`
							WHERE `date` LIKE '".$date."%' ORDER BY `id` DESC LIMIT 1")
							or die(mysql_error());
	}
	$data=mysql_fetch_array($query);
	
	$id_ticket=$data['id_ticket'];
	//echo 'ID:'.$id_ticket.'<br/>';
	if($id_ticket==0){
		$id_ticket=100000000;
	}
	//echo $year.' '.$week.'<br/>';
	$mon=date('Y-m-d 00:00:01',strtotime($year.'-W'.$week.'-1'));
	$sun=date('Y-m-d 23:59:59',strtotime($year.'-W'.$week.'-7'));
	//echo 'Lunes:'.$mon.'  '.$sun.'<br/>'; 
	 

		$query=mysql_query("SELECT id_ticket 
							FROM `dash_ticket` 
							WHERE `date` LIKE '".$date_next."%' ORDER BY `id` ASC LIMIT 1")
		or die(mysql_error());
		$data=mysql_fetch_array($query);
	
		$id_ticket_next=$data['id_ticket'].'<br/>';
		if($id_ticket_next==0){
			$id_ticket_next=100000000;
		}
	
		//echo 'ID_NEXT:'.$id_ticket_next.'<br/>';
		$totalOri=0;
		$totalReal=0;
		
		
		//echo $test.' ---'.$date_next.' - '.$year . 'W' . str_pad($week+1, 2, '0', STR_PAD_LEFT).'<br/>';
	
	$exit=0;
	
			$query=mysql_query("SELECT subtotal, subtotalOriginal, items 
						FROM `dash_ticket` 
						WHERE `date` between '".$mon."%' AND '".$sun."'
						AND `description`!='Waardebon'")
						or die(mysql_error());
			
				while($data=mysql_fetch_array($query)){
					$totalOri+=$data['subtotalOriginal']*$data['items'];
					$totalReal+=$data['subtotal']*$data['items'];
				}
			
			
			

	
	$query=mysql_query("SELECT SUM(subtotal), SUM(subtotalOriginal), SUM(items), COUNT(DISTINCT id_ticket) 
						FROM `dash_ticket` 
						WHERE `id_ticket` >= '".$id_ticket."' 
						AND `id_ticket`<'".$id_ticket_next."'
						AND `description`!='Waardebon'")
	or die(mysql_error());
	$data=mysql_fetch_array($query);
	

	
	$value['pcs']= $data['SUM(items)']+$today['pcs'];	
	if($value['pcs']=='')$value['pcs']=0;
	
	$value['tickets']=$data['COUNT(DISTINCT id_ticket)']+$today['tks'];
	$value['tks']= round(($totalReal+$today['money'])/$value['tickets'],0);
		$coste=($totalOri+$today['money_completo'])/2.94;
	$value['total_margen']=($totalReal+$today['money'])-$coste;
	
	//---------------------------------------------------
	//Buscamos en payment los datos de pago para encontrar la cantidad real ingresada
	$query_payment=mysql_query("SELECT SUM(quantity) 
								FROM `dash_payment` 
								WHERE `id_ticket` >= '".$id_ticket."' 
								AND `id_ticket`<'".$id_ticket_next."' 
								AND type!='Waardebon'")
	or die(mysql_error());
	$data_payment=mysql_fetch_array($query_payment);
		$value['total']=round($data_payment['SUM(quantity)']+$today['money'],2);
	//---------------------------------------------------
	
	$value['IVA']=$value['total']-($value['total']/1.21);
	$value['total_margen']=round($value['total']-$value['IVA']-$coste-($value['total']*0.03),0);
	$value['total_margen_per']=round(($value['total_margen']/$value['total'])*100,0);
	$value['desc']=round((1-($value['total']/($totalOri+$today['money_completo'])))*100,0);
	//$value['desc']=round(get_desc_ponderado($id_ticket,$id_ticket_next),0);
	
	
	$query=mysql_query("SELECT SUM(items) 
						FROM `dash_ticket` 
						WHERE `referencenr` LIKE 'M%' 
						AND `id_ticket` >= '".$id_ticket."'
						AND `id_ticket`<'".$id_ticket_next."'")
	or die(mysql_error());
	$data=mysql_fetch_array($query);
	$value['M']=$data['SUM(items)']+$today['M'];
	$query=mysql_query("SELECT SUM(items) 
						FROM `dash_ticket` 
						WHERE `referencenr` LIKE 'G%' 
						AND `id_ticket` >= '".$id_ticket."'
						AND `id_ticket`<'".$id_ticket_next."'")

	or die(mysql_error());
	$data=mysql_fetch_array($query);
	$value['G']=$data['SUM(items)']+$today['G'];
	$value['U']=$value['pcs']-$value['M']-$value['G'];
	
	
	$query=mysql_query("SELECT COUNT(country) 
						FROM `dash_buyer` 
						WHERE `country` = 'ES' 
						AND `id_ticket` >= '".$id_ticket."'
						AND `id_ticket`<'".$id_ticket_next."'")

	or die(mysql_error());
	$data=mysql_fetch_array($query);
	$value['ES']=$data['COUNT(country)']+$today['ES'];	
	
	$query=mysql_query("SELECT COUNT(country) 
						FROM `dash_buyer` 
						WHERE `country` <> 'ES' 
						AND `id_ticket` >= '".$id_ticket."'
						AND `id_ticket`<'".$id_ticket_next."'")

	or die(mysql_error());
	$data=mysql_fetch_array($query);
	$value['REST']=$data['COUNT(country)']+$today['REST'];	
	
	$value['country']=get_stats_country($value['ES'],$value['REST']);
	$value['stats']=get_stats_gender($value['M'],$value['G'],$value['U']);
	
	return $value;
}

function get_category($men,$women,$acc){
	$total=$men+$women+$acc;
	
	$men_per=round(($men/$total)*100,0);
	$women_per=round(($women/$total)*100,0);
	$acc_per=round(($acc/$total)*100,0);
	$stats['nmen']=$men;
	$stats['nwomen']=$women;
	$stats['women_per']=$women_per;
	$stats['nacc']=$acc;
	$stats['men']='<div class="stats" style="width:'.$men_per.'%">'.$men_per.'%</div>';
	$stats['women']='<div class="stats" style="width:'.$women_per.'%">'.$women_per.'%</div>';
	$stats['acc']='<div class="stats" style="width:'.$acc_per.'%">'.$acc_per.'%</div>';
	
	return $stats;
}

function get_stats($ES,$ES_money,$REST,$REST_money){
	$total=$ES+$REST;
	$total_money=$ES_money+$REST_money;
	
	$ES_per=round(($ES/$total)*100,0);
	$REST_per=round(($REST/$total)*100,0);
	
	$ES_money_per=round(($ES_money/$total_money)*100,0);
	$REST_money_per=round(($REST_money/$total_money*100),0);
	
	$stats['ES']=$ES;
	$stats['REST']=$REST;
	$stats['country']='<div class="stats" style="width:'.$ES_per.'%">'.$ES_per.'%</div>';
	$stats['country'].='<div class="stats" style="width:'.$REST_per.'%">'.$REST_per.'%</div>';
	$stats['ES_money']=$ES_money;
	$stats['REST_money']=$REST_money;
	$stats['money']='<div class="stats" style="width:'.$ES_money_per.'%">'.$ES_money_per.'%</div>';
	$stats['money'].='<div class="stats" style="width:'.$REST_money_per.'%">'.$REST_money_per.'%</div>';
	return $stats;
}
function margen_bruto($precioCompleto,$precio){
	//echo $precioCompleto.' '.$precio.'<br/>';
	
		$margen['money']=round(($precio-($precio-($precio/1.21))-($precioCompleto/2.94)-($precio*0.03)),2);
		$margen['percentage']=round((($margen['money']/$precio))*100,2);
		return $margen;
}

function same_day(){
	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MSSQL: '.mysql_error());
	
	mysql_select_db(_BD, $conexion);		
		
		$week_number = date("W",time());
		$year = date("Y",time());
		
		$day_of_week = date("N",time());
		
		$year_ini = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).'0'));
		$year_end = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).'7'));
		$year_day = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).$day_of_week));
			
		
		
		if($week_number!=1 && $year_end==$year_ini)
			$year-=1;
		//echo $year;
		
		//echo $day_of_week.'<br/>';
		//echo date("d-m-Y",strtotime("2014W017"));
		$day2 = strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).$day_of_week);
		
		$fecha= date("Y-m-d",$day2);
		//echo $fecha;
		
	$query=mysql_query("SELECT SUM(quantity) FROM `dash_payment` WHERE `date` LIKE '".$fecha."%'") 
	or die(mysql_error());
	$tickets=mysql_query("SELECT * FROM `dash_ticket` WHERE `date` LIKE '".$fecha."%' GROUP BY `id_ticket`")
	or die(mysql_error());
	$prendas=mysql_query("SELECT SUM(items),SUM(subtotalOriginal) FROM `dash_ticket` WHERE `date` LIKE '".$fecha."%'") 
	or die(mysql_error());

	$data['date']=date("d-m-Y",$day2);


	$nprendas=mysql_fetch_array($prendas);
	$dato=mysql_fetch_array($query);
	
		if($dato['SUM(quantity)']!=0){
			$data['pcs']=$nprendas['SUM(items)'];
			$data['total_completo']=round($nprendas['SUM(subtotalOriginal)'],2);
			
			$margen_total=margen_bruto($data['total_completo'],$dato['SUM(quantity)']);
			$data['total_margen']=round($margen_total['money'],0);
			$data['total_margen_per']=round($margen_total['percentage'],0);
			
			
			$data['rcp']=mysql_num_rows($tickets);
			$data['total_day']=$dato['SUM(quantity)'];
			$data['qty']= number_format($dato['SUM(quantity)'],2,',','.');
			$data['desc']=round((1-($dato['SUM(quantity)']/$nprendas['SUM(subtotalOriginal)']))*100,0);
		}else{
			$data['pcs']=0;
			$data['rcp']=0;
			$data['qty']=0;
		}
	
	return $data;
}
function update_answers($id_ticket,$id_answer){
	$con=mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (mysqli_connect_errno($con))
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
  
	foreach( $id_ticket as $key => $n ) {
		
		$result=mysqli_query($con,"SELECT * FROM `dash_ticket_buyer` WHERE id_ticket='".$n."'");
		$inserted=mysqli_num_rows($result);
	  
		if($inserted>0){
			//update
			mysqli_query($con,"UPDATE dash_ticket_buyer SET id_answer='".$id_answer[$key]."' WHERE id_ticket='".$n."'");
	
		}else{
			//insert
			mysqli_query($con,"INSERT INTO dash_ticket_buyer (id_ticket,
											 id_answer
											 ) 
									 VALUES ('".$n."',
									 		 '".$id_answer[$key]."'
									 		 )") 
									 or die (mysql_error());
		}
	
	}
	mysqli_free_result($result);
	mysqli_close($con);
}

function get_answers($id_ticket){
	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MSSQL: '.mysql_error());
	
	mysql_select_db(_BD, $conexion);
	$answer['print']='<input type="hidden" name="ticket[]" value="'.$id_ticket.'">';
	$answer['print'].='<select name="answer[]" class="selects">';
	$answer['print'].='<option value="0" selected="selected">No seleccionado</option>';
	$result=mysql_query("SELECT * FROM `dash_ticket_buyer` WHERE id_ticket='".$id_ticket."'") or die (mysql_error());
	$data=mysql_fetch_array($result);
	
	$result=mysql_query("SELECT * FROM `dash_ticket_buyer_answer` ORDER BY answer ") or die (mysql_error());
		$answer['updated']=false;
		while($row=mysql_fetch_array($result)){
			if($data['id_answer']==$row['id']){
				$answer['print'].='<option value="'.$row['id'].'" selected>'.$row['answer'].'</option>';
				$answer['updated']=true;
			}else{
				$answer['print'].='<option value="'.$row['id'].'">'.$row['answer'].'</option>';
				
			}
		}
	$answer['print'].='</select>';
	return $answer;
}

function get_data_item($item){
	
	$colour= 'Color: <b>'.$item['colour'].'</b><br/>';
	$size= 'Talla: <b>'.$item['size'].'</b><br/>'; 
	$season= 'Temporada: <b>'.$item['season'].'</b><br/>';
	$reference= 'Referencia: <b>'.$item['reference'].'</b><br/>';
	
		$name=$item['name']."<br/>";
		$details=$name.' '.$colour.' '.$size.' '.$season.' '.$reference;
		
	return $details;
}

function get_reference($reference){
	
	$link = mssql_connect(_MSSQL_SERVER, _MSSQL_USERNAME, _MSSQL_PASSWORD);
	if (!$link)
	    die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
	
	$selected = mssql_select_db( _MSSQL_DB, $link) or die("Couldn't open database $myDB ." .mssql_get_last_message() ); 
		$queryDetail = "select * from artikelmatrix m inner join artikelinfo i 
						on i.artikelmatrixid = m.artikelmatrixid where i.artikelnummer = ".$reference;
	
	$resultDetail = mssql_query($queryDetail) 
					or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
	
	
		 while($rowDetail =mssql_fetch_array($resultDetail)){
		 	$data['name']=$rowDetail[2];
		 	$data['colour']=$rowDetail['MatrixV'];
		 	$data['size']=$rowDetail['MatrixH'];
		 	$data['reference']=$rowDetail['Referentienummer'];
		 	$data['season']=$rowDetail['Seizoen'];
		 }
	return $data;
}

function update_target($date,$margen,$prendas,$tickets,$men,$women,$acc,$insert){
	//echo $date.' - '.$margen.' - '.$prendas.'  - '.$tickets.' - '.$men.'- '.$women.'- '.$acc.' - '.$insert;
	
	if($date==""){
		$dy=date("Y",time());
		$dm=date("m",time());
		$dd=date("j",time());
		$dw=date("W",time());
	
	
		$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
		if (!$conexion)
			die('Something went wrong while connecting to MSSQL: '.mysql_error());
		mysql_select_db(_BD, $conexion);
		
		$check=mysql_query("SELECT id FROM `dash_target` 
				WHERE year='".$dy."' 
				AND week='".$dw."'") or die(mysql_error());
		
		//echo mysql_num_rows($check);
		
		if(mysql_num_rows($check)>0){
			$row=mysql_fetch_array($check);
			//echo $row['id'];
			
			switch($insert){
				case 'margen':
					//echo 'entra margen';
						$select="UPDATE dash_target SET margin='".$margen."' WHERE id='".$row['id']."'";
				break;
				case 'prendas':
					//echo 'entra prendas';
						$select="UPDATE dash_target SET items='".$prendas."' WHERE id='".$row['id']."'";
				break;
				case 'tickets':
					//echo 'entra tickets';
						$tickets=str_replace(',','.',$tickets);
						$select="UPDATE dash_target SET tickets='".$tickets."' WHERE id='".$row['id']."'";
				break;
				case 'estadisticas':
					//echo 'entra estadisticas';
						$select="UPDATE dash_target SET men='".$men."', women='".$women."', acc='".$acc."' WHERE id='".$row['id']."'";
				break;
			}
			$result=mysql_query($select) or die(mysql_error());
		}
	
	}else{
		//Si pasamos por GET ?date=24-2-2014 ira a ese dia
		$token=explode('-',$date);
		$dy=$token[2];
		$dm=$token[1];
		$dd=$token[0];
		//no se puede actualizar semanas ya pasadas
		//$dw=date("W", strtotime($date));
	}
}

function get_margin_v2($begin){
	if($begin==""){
		$year=date("Y",time());
	}else{
		//Si pasamos por GET ?date=24-2-2014 ira a ese dia
		$token=explode('-',$_GET['date']);
		$year=$token[2];
	}
	
	//Conectar BBDD--------------------------------------
	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	mysql_select_db(_BD, $conexion);
	//---------------------------------------------------
	
	if($year=="")
		$year=date('Y',time());
	
	$week=date('W',time());
	$date = date(
			'Y-m-d',
			strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT))
	);
	
	if($week+1>52)
		$week=1;
	
	$date_next = date(
			'Y-m-d',
			strtotime($year . 'W' . str_pad($week+1, 2, '0', STR_PAD_LEFT))
	);
	
	$date='2014-07-01';
	$date_next='2014-07-31';
	//Obtenemos los id_ticket final e inicial------------
	$query=mysql_query("SELECT id_ticket
						FROM `dash_ticket`
						WHERE `date` LIKE '".$date."%' ORDER BY `id` ASC LIMIT 1")
						or die(mysql_error());
	$data=mysql_fetch_array($query);
	$id_ticket_INI=$data['id_ticket'];
	
	$query=mysql_query("SELECT id_ticket
						FROM `dash_ticket`
						WHERE `date` LIKE '".$date_next."%' ORDER BY `id` ASC LIMIT 1")
						or die(mysql_error());
	$data=mysql_fetch_array($query);
	$id_ticket_FIN=$data['id_ticket'];
	if($id_ticket_FIN=='')
		$id_ticket_FIN=1000000000;
	//---------------------------------------------------
	
	echo 'Entramos en get_margin_v2<br/>';
	echo 'Fecha: '.$date.' '.$date_next.'<br/>';
	echo 'ID_ticket: '.$id_ticket_INI.' - '.$id_ticket_FIN.'<br/>';
	
	$query=mysql_query("SELECT id_ticket
						FROM `dash_ticket`
						WHERE `id_ticket` >= '".$id_ticket_INI."'
						AND `id_ticket` <='".$id_ticket_FIN."' 
						group by id_ticket 			
			")
						or die(mysql_error());
	
	$totalCoste=0;
	$totalSubtotal=0;
	
	while($data_main=mysql_fetch_array($query)){
		//echo $data_main['id_ticket'].'<br/>';
		$id_ticket=$data_main['id_ticket'];
		
		$query_ticket=mysql_query("SELECT subtotal, subtotalOriginal,items, description
						FROM `dash_ticket`
						WHERE `id_ticket` = '".$id_ticket."'")or die(mysql_error());
		$total=0;
		$coste=1;
		$contador_coste=0;
		while($data_ticket=mysql_fetch_array($query_ticket)){
			$total+=$data_ticket['subtotal']*$data_ticket['items'];
			$contador_coste+=$data_ticket['subtotalOriginal']/2.94;
			if($data_ticket['description']=='Waardebon')
				$coste=0;
		}
		if($coste!=0){
			$totalcoste+=$contador_coste;
			$totalSubtotal+=$total;
		}
		
		
		$query_payment=mysql_query("SELECT quantity, type
						FROM `dash_payment`
						WHERE `id_ticket` = '".$id_ticket."'")or die(mysql_error());
		
		$vale=0;
		$vale_cantidad=0;
		while($data_payment=mysql_fetch_array($query_payment)){
			if($data_payment['type']=='Waardebon'){
				$vale=1;
				$vale_cantidad+=$data_payment['quantity'];
			}else{
				$totalPayment+=$data_payment['quantity'];
			}
		}
		
		if(($vale==1)&&($vale_cantidad>=$total)){
			$totalCoste-=$contador_coste;
			$totalSubtotal-=$total;
		}else if(($vale==1)&&($vale_cantidad<$total)){
			//de esto no estoy seguro.
			//si el vale no cubre todo el ticket le resto a la cantidad del ticket y calculo el iva ahi.
			//$totalSubtotal-=$vale_cantidad;
			$totalCoste-=($vale_cantidad/2.94);
		}else{
			//dejamos los contadores tal y como estan ahora
		}
		
	}
	
	echo 'COSTE: '.$totalcoste.'<br/>';
	echo 'Subtotal: '.$totalSubtotal.'<br/>';
	echo 'Payment: '.$totalPayment.'<br/>';
	$IVA=($totalPayment-($totalPayment/1.21));
	echo 'IVA : '.($totalPayment-($totalPayment/1.21)).'<br/>';
	echo 'Margen: '.($totalPayment-$totalcoste-$IVA-($totalPayment*0.03)).'<br/>';
}

function get_data($begin){

/************************************************
	MSSQL
************************************************/
	$link = mssql_connect(_MSSQL_SERVER, _MSSQL_USERNAME, _MSSQL_PASSWORD);
	if (!$link)
	    die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
	
	$selected = mssql_select_db( _MSSQL_DB, $link) 
	or die("Couldn't open database $myDB ." .mssql_get_last_message() ); 
  
$total_day=0;



if($begin==""){
	$dy=date("Y",time());
	$dm=date("m",time());
	$dd=date("j",time());
}else{
	//Si pasamos por GET ?date=24-2-2014 ira a ese dia
	$token=explode('-',$_GET['date']);
	$dy=$token[2];
	$dm=$token[1];;
	$dd=$token[0];
}
	
	//$query = 'SELECT * FROM Verkoop WHERE Datum LIKE "'.$date.'%"';
	
	$query = 'SELECT * FROM Verkoop WHERE  
	  (DATEPART(yy, Datum) = "'.$dy.'"
AND    DATEPART(mm, Datum) = "'.$dm.'"
AND    DATEPART(dd, Datum) = "'.$dd.'")';
	$result = mssql_query($query) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());

	
	$total_completo=0;
	$ES=0;
	$ES_money=0;
	$REST=0;
	$REST_money=0;
	$i=0;
	$men=0;
	$women=0;
	$acc=0;
	$pcs=0;
	$tks=0;
	$descAcumulado=0;
	$descMedio=0;
	$descI=0;
	while($row = mssql_fetch_array($result)){
	  
	  $info['id_ticket'][$i]=$row["Factuurnummer"];
	  $info['date'][$i]=$mostrar = date("H:i", strtotime($row["Datum"]));
	  
	  
	   $queryDetail = "SELECT * FROM VerkoopBetaling WHERE Factuurnummer = ".$row["Factuurnummer"];
	   $resultDetail = mssql_query($queryDetail) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
$restar=0;
		 while($rowDetail =mssql_fetch_array($resultDetail)){
		 
		 	$type=$rowDetail['Betalingswijze'];
		 	$amount=$rowDetail['Bedrag'];
		 	
		 	if($type=='Waardebon')$restar+=$amount*-1;
		 }
	
		  $queryDetail = "SELECT * FROM VerkoopDetail WHERE Factuurnummer = ".$row["Factuurnummer"];
		  $resultDetail = mssql_query($queryDetail) or die('Something went wrong while connecting to MSSQL: '.mssql_get_last_message());
		 $total=0;
		 
		 $precioCompleto=0;
		  $c=0;
		  $pcs_this_tk=0;
		  while($rowDetail =mssql_fetch_array($resultDetail)){
		  	
		  	$reference=$rowDetail['Artikelnummer'];
		  	
		  	
		 	 if($rowDetail["RetourRedenID"]==4){
			  		$devuelto="<i>Art’culo devuelto - Insatisfecho</i><br/>";
			  }else if($rowDetail["RetourRedenID"]==3){
			  		$devuelto="<i>Art’culo devuelto - Color</i></br>";
			  }else if($rowDetail["RetourRedenID"]==2){
			  		$devuelto="<i>Art’culo devuelto - Talla</i></br>";
			  }else if($rowDetail["RetourRedenID"]==1){
			  		$devuelto="<i>Art’culo devuelto - Fallo</i></br>";
			  }else if($rowDetail["RetourRedenID"]==5){
			  		$devuelto="<i>Art’culo devuelto - Otro</i></br>";
			  }else{
				  	$devuelto="";
			  }
			  
			  
			  $margen=margen_bruto($rowDetail['origverkoopprijs'],$rowDetail["Verkoopprijs"]);
			  
			  $pcs+=$rowDetail["Aantal"];
			  $pcs_this_tk+=$rowDetail["Aantal"];
			  
			  $precioCompleto+=$rowDetail['origverkoopprijs']*$rowDetail["Aantal"];
			  
			  $precio=$rowDetail["Verkoopprijs"]*$rowDetail["Aantal"];
			  $precioDeVenta=$rowDetail["origverkoopprijs"];
			 
			 

			 
			  $replace = array("Superdry", "SUPERDRY");
			  $item=str_replace($replace,'',$rowDetail["Omschrijving"]);
			  
			  $data=get_reference($reference);
			  
			  if($rowDetail["Aantal"]!="-1"){
			  if($data['reference'][0]=='M')
			  	$men++;
			  else if($data['reference'][0]=='G')
			  	$women++;
			  else
			  	$acc++;
			  }
			  
			  $info['items'][$i][$c]['percentage']=round((($rowDetail["origverkoopprijs"]-$rowDetail["Verkoopprijs"])/$rowDetail['origverkoopprijs'])*100,0);
			  $info['items'][$i][$c]['margen']=$margen['money'];
			  $info['items'][$i][$c]['margen_per']=$margen['percentage'];
			  $info['items'][$i][$c]['returned']=$devuelto;
			  $info['items'][$i][$c]['aditional']=get_data_item($data);
			  $info['items'][$i][$c]['subtotalOriginal']=number_format($precio,2,',','.');
			  //echo $rowDetail["Verkoopprijs"].'/'.$rowDetail['origverkoopprijs'].'<br/>';
			  $descMedio=(1-($rowDetail["Verkoopprijs"]/$rowDetail['origverkoopprijs']))*100;
			  //echo $descPonderado.'<br/>';
			  $descAcumulado+= $descMedio;
			  $descI++;
			  $total=$total+$precio;
			  $c++;
		  }
		  if($total!=0)
		  	$tks++;
		// INFORMACION ---- Contamos el nœmero de espa–oles y extranjeros. Y el gasto de cada uno
		if($row["country"]=='ES'){ $ES++; $ES_money+=$total+$restar ;
	  	}else{ $REST++; $REST_money+=$total+$restar;}
		
	
	  	
		$total_day+=$total+$restar;
		$total_completo+=$precioCompleto;
		 
		
		
		 $info['total'][$i]=number_format($total+$restar,2,',','.');
		 $margen_total=margen_bruto($total_completo,$total_day); 
		 $info['margen'][$i]=$margen_total['money'];
		 $info['margen_per'][$i]=$margen_total['percentage'];
	  $i++;
	}
	
	$info['total_day']=number_format($total_day,2,',','.');
	$info['total_day/tks']=number_format(($total_day/$tks),0,',','.');
	$info['stats']=get_stats($ES,$ES_money,$REST,$REST_money);
	$info['stats_category']=get_category($men,$women,$acc);
	$margen_total=margen_bruto($total_completo,$total_day); 
	
	
	$info['total_margen']=round($margen_total['money'],0);
	$info['total_margen_per']=round($margen_total['percentage'],0);
	$info['desc']=round((1-($total_day/$total_completo))*100,0);
	//$info['desc']=round($descMedio/$descI,0);
	$info['objetivo']=stats();
	$info['pcs']=$pcs;
	$info['tks']=$tks;
	
	
	mssql_close($link);
	
	$today['ES']=$ES;
	$today['REST']=$REST;
	$today['M']=$men;
	$today['G']=$women;
	$today['U']=$acc;
	$today['pcs']=$pcs;
	$today['tks']=$tks;
	$today['money']=$total_day;
	$today['money_completo']=$total_completo;
	
	
	$info['week']=get_data_week($today,"");
	$info['week2']=get_data_week($empty,date("Y",time())-1);
	$info['week3']=get_data_week($empty,date("Y",time())-2);
	
	
	$info['objetivo']['t_total']=get_target_data($info['objetivo']['this_week'],$info['week']['total']);	
	$info['objetivo']['t_desc']=get_target_data($info['objetivo']['margin'],$info['week']['desc'],'%');	
	$info['objetivo']['t_tickets']=get_target_data($info['objetivo']['tickets'],$info['week']['total']/$info['week']['tickets']);	
	$info['objetivo']['t_items']=get_target_data($info['objetivo']['items'],round($info['week']['pcs']/$info['week']['tickets'],2));
	$info['objetivo']['t_women']=get_target_data($info['objetivo']['women'],$info['week']['stats']['women']);

	
	//$info['t_desc']['value']=$info['objetivo']['margin'];
	//$info['t_desc']['per']=round($week['desc']/$info['objetivo']['margin'],2)*100;
	//$info['t_tickets']['value']=$info['objetivo']['tickets'];
	//$info['t_tickets']['per']=round($info['objetivo']['tickets']/$week['tks'],2)*100;
	//$info['t_items']['value']=$info['objetivo']['items'];
	//$info['t_items']['per']=round($info['objetivo']['items']/(round($week['pcs']/$week['tickets'],2)),2)*100;
	
	//$info['objetivo']=get_target_graph();
	
	//$info['graph']['desc']=stats_2values($valor1,$valor2);
	//$info['graph']['pt']=stats_2values($info['week']['tks'],$info['objetivo']['tickets']);
	//$info['graph']['tm']=stats_2values(round($info['week']['pcs']/$info['week']['tickets'],2),$info['objetivo']['items']);
	
	//$info['week2']=get_data_week($today);
	echo $info;
	return $info;
}

?>