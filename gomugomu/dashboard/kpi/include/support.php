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
	$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	
	
	$year 	=date('Y',time());
	$week 	=date('W',time());
	$month 	=date('m',time());
	$day 	=date('d',time());

	$year_ini = date("Y",strtotime($year."W".str_pad($week,2,0,STR_PAD_LEFT).'0'));
	$year_end = date("Y",strtotime($year."W".str_pad($week,2,0,STR_PAD_LEFT).'7'));
	//echo $week.' '.$year.' '.$year_ini.' '.$year_end;
	if($year_ini!=$year_end)
		$year=$year_ini;
		
	//	$year_day = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).$day_of_week));
	
	
	
	$query=mysqli_query($conexion, "SELECT value, conversion, margin, items, tickets,men, women,acc, average_ticket FROM `dash_target` WHERE `week`='".$week."' AND `year`='".$year."'")
	or die(mysql_error());
	
	$data=mysqli_fetch_array($query);
	$objetivo=$data['value'];
	
	$margen_valor_completo=$data['value']*(($data['margin']/100)+1);
	$margen_coste_ropa=$margen_valor_completo/2.94;
	$margen_iva=$data['value']-($data['value']/1.21);
	$margen_royalti=$data['value']*0.03;
	

	
	$data_objetivo['total_margen']=round($data['value']-$margen_coste_ropa-$margen_iva-$margen_royalti,0);
	$data_objetivo['margin']=$data['margin'];
	$data_objetivo['items']=$data['items'];
	$data_objetivo['tickets']=$data['tickets'];
	$data_objetivo['week_conversion']=$data['conversion'];
	$data_objetivo['week_average_ticket']=$data['average_ticket'];
	
	
	
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
   

	$query=mysqli_query($conexion, "SELECT value, conversion, average_ticket, items FROM `dash_target` WHERE `month`='".$month."' AND `year`='".$year."' AND `day`='".$day."'")
	or die(mysql_error());
	
	$data=mysqli_fetch_array($query);
	$data_objetivo['day_quantity'] 	  = $data['value'];
	$data_objetivo['day_conversion']  = $data['conversion'];
	$data_objetivo['average_ticket']  = $data['average_ticket'];
	$data_objetivo['day_items'] = $data['items'];
    
    $query=mysqli_query($conexion, "SELECT value, conversion, average_ticket, items FROM `dash_target` WHERE `month`='".$month."' AND `year`='".$year."' AND `day` IS NULL")
	or die(mysql_error());
	
	$data=mysqli_fetch_array($query);

	$data_objetivo['month_quantity'] 	   = $data['value'];
	$data_objetivo['month_conversion'] 	   = $data['conversion'];
	$data_objetivo['month_average_ticket'] = $data['average_ticket'];
	$data_objetivo['month_items'] 	  	   = $data['items'];


	$query=mysqli_query($conexion, "SELECT value FROM `dash_target` WHERE `year`='".$year."' AND `day` IS NULL AND `month` IS NULL AND `week` IS NULL")
	or die(mysql_error());
	
	$data=mysqli_fetch_array($query);
	$data_objetivo['year_quantity'] 	  = $data['value'];
    /********************************************************
    ACCEDEMOS A DASHBOARD
    *********************************************************/
   

    $query=mysqli_query($conexion, "SELECT id_ticket FROM `dash_ticket` WHERE `date` LIKE '".$date1."%' ORDER BY `id` ASC LIMIT 1")
	or die(mysql_error());
	$no=1;
	$data=mysqli_fetch_array($query);
	$id_ticket=$data['id_ticket'];
	if($id_ticket==0){
		$query=mysqli_query($conexion, "SELECT id_ticket FROM `dash_ticket` ORDER BY `id` DESC LIMIT 1")
	or die(mysql_error());
		$data=mysqli_fetch_array($query);
		$id_ticket=$data['id_ticket'];
		$id_ticket++;
	}
	
	//calculamos el valor de los tickets que ya tenemos guardados
	
	$query=mysqli_query($conexion, "SELECT SUM(quantity) FROM `dash_payment` WHERE `id_ticket`>='".$id_ticket."' AND type!='Waardebon'")
	or die(mysql_error());
	$data=mysqli_fetch_array($query);
	$hastahoy=round($data['SUM(quantity)'],2);
	/********************************************************
    ACCEDEMOS A BECOSOFT PARA CALCULAR LA CANTIDAD QUE LLEVAMOS
    *********************************************************/
	$link = mssql_connect(_MSSQL_SERVER, _MSSQL_USERNAME, _MSSQL_PASSWORD);
	if (!$link)
	    die('get_data: Error al conectar al servidor para obtener la cantidad que llevamos MSSQL: '.mssql_get_last_message());
	
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
	 	or die('get_data: Error al hacer la query para saber la cantidad que llevamos MSSQL '.mssql_get_last_message());
	 
	 
	$data =mssql_fetch_array($resultDetail);
	
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
	$data['day_quantity'] = $data_objetivo['day_quantity'];
	$data['day_conversion'] = $data_objetivo['day_conversion'];
	$data['week_conversion'] = $data_objetivo['week_conversion'];
	$data['week_average_ticket'] = $data_objetivo['week_average_ticket'];
	$data['day_items'] = $data_objetivo['day_items'];
	$data['month_quantity'] = $data_objetivo['month_quantity'];
	$data['month_average_ticket'] = $data_objetivo['month_average_ticket'];
	$data['month_items'] = $data_objetivo['month_items'];
	$data['month_conversion'] = $data_objetivo['month_conversion'];
	$data['year_quantity'] = $data_objetivo['year_quantity'];

	$data['acc']=$data_objetivo['acc'];
	$data['men']=$data_objetivo['men'];
	$data['women']=$data_objetivo['women'];
	$data['this_week']=$data_objetivo['this_week'];
	$data['average_ticket']=$data_objetivo['average_ticket'];
	$data['week_target']=get_stats_gender($data_objetivo['men'],$data_objetivo['women'],0);
	
	
	return $data;
}

function get_target_data($objetivo,$valor,$simbolo=''){
	//Para comprobar los valores que entran
	
	
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
	//echo $objetivo.' '.$valor.' '.$simbolo.' '.$v['per'].'<br/>';
	return $v;
}


function get_stats_country($ES,$REST){
	$objetivo=$ES+$REST;
	$E=round($ES/$objetivo,2)*100;
	$R=100-$E;
	//echo $ES.' '.$REST.'<br/>';
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
	$query=mysqli_query($conexion,"SELECT subtotal, subtotalOriginal, items
						FROM `dash_ticket`
						WHERE `id_ticket` >= '".$idInicio."'
						AND `id_ticket`<'".$idFin."'
						AND `description`!='Waardebon'");
	while($row=mysqli_fetch_array($query)){				
		
		$desc=(1-($row['subtotal']/$row['subtotalOriginal']))*100;
		$desc_C+=round($desc,0);
		$i++;
		
	}
	
	return ($desc_C/$i);
}

function get_data_week($today,$year){
	debug("--------------------------------");
	debug("get_data_week() - year: ".$year);
	debug("--------------------------------");
	$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if($year=="")
		$year=date('Y',time());
	
	$year_original = $year;
	$week=date('W',time());
	debug("semana inicial: ".$week);
    if($week>53)
    	$week=1;
    if($week==1 && $year<2016)
    	$week++;
    if($year==2020){
    	$week++;
    }
    //La semana 2 de 2016 equivale a la semana 1 de los años anteriores por eso se pone esto
    debug("semana tras calculos: ".$week);
    if($year<2016){
    	$week++;
    	debug("semana year<2016: ".$week);
    }

    
    if ($week == "01" && $year!="2022"){
    	$year++;
    	debug("semana == 01: ".$week);
    }

   	if(strlen($week)==1){
    	$week='0'.$week;
    	debug("strlen(week)==1: ".$week);
    }
    
    debug("fecha inicial: ".$year.''.$week);
    
    $check_day = date(
    		'm-d',
    		strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT))
    );
    $check_year = date(
    		'm-d-Y',
    		strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT))
    );

	$mon=date('Ymd',strtotime($year.'-W'.$week.'-1'));
	$sun=date('Ymd',strtotime($year.'-W'.$week.'-7'));
	debug("lunes: ".$mon.' - domingo: '.$sun);
	$query=mysqli_query($conexion, "SELECT SUM(people) FROM `dash_tcuento` WHERE day >= '".$mon."' AND day <='".$sun."'") or die(mysql_error());
	$result=mysqli_fetch_array($query);

	$value['people'] = $result["SUM(people)"];
	
    $retrocedemos_inicio=0;
    $week_begin=$week;
    $holiday=array("01-01","01-06","12-06","12-08","12-25","05-01","08-15","10-09","10-12","11-01","03-19");
    //Casos especiales. Ejemplo 20-11-2017 La tienda estuvo cerrada por obras en la reforma
    $cases = array("11-20-2017", "04-02-2018", "04-22-2019", "04-29-2019", "04-05-2021", "04-12-2021", "11-01-2021"); 
    debug("comprobamos inicial : ".$check_day);
    debug("comprobamos inicial completo: ".$check_year);
    if(in_array($check_day, $holiday) || in_array($check_year, $cases)){   
    	debug("en esas fechas estaba cerrado");
    	debug("semana: ".$week_begin); 
    	$year_begin = $year;
    	if($week_begin == 1){
    		$year_begin = $year - 1;
    		$date = strtotime("31 December ".($year_begin));
        	$week_begin = gmdate("W", $date);
    	}else{
    		$week_begin-=1;
    	}
    	debug("retrocedemos a la semana: ".$week_begin);
    	if($week_begin<10 && $week_begin[0]!="0")
    		$week_begin='0'.$week_begin;
    	$retrocedemos_inicio=1;	
    	$date = date('Y-m-d', strtotime($year_begin."W".$week_begin."7"));
    	//echo "date: ".$date;
    	//COMPROBAMOS que el dia que estamos cogiendo tiene ticket sino cogemos el dia anterior a este (sabado)
    	$query=mysqli_query($conexion, "SELECT id_ticket 
							FROM `dash_ticket` 
							WHERE `date` LIKE '".$date."%' ORDER BY `id` ASC LIMIT 1")
							or die(mysql_error());
		$data=mysqli_fetch_array($query);
		debug("buscamos el ultimo ticket del dia ".$date);
		$id_ticket=$data['id_ticket'];
		debug("el ticket es: ".$id_ticket);
    	if($id_ticket == ""){
    		debug("como el ticket es vacio buscamos el ultimo dia de la semana : ".$week_begin." - ".$year_begin);
    		$date = date('Y-m-d', strtotime($year_begin."W".$week_begin."6"));
    		debug("la fecha es : ".$date);
    	}
    	//echo "date: ".$date;
    	if ($week_begin >= 52){
    		$week_begin = $week;
    	}
    }else{
    	$date = date(
    			'Y-m-d',
    			strtotime($year . 'W' . str_pad($week_begin, 2, '0', STR_PAD_LEFT))
    	);
    }

    $week_next = $week+1;

    //FER20190401
    //ESTO ESTABA PUESTO ANTES PERO PARA EL CASO DE LA SEMANA 14 del año 2018 FALLA. No se si es en general. Creo que tiene más sentido comentado porque
    //week_begin tiene el mismo valor que week siempre salvo que haya que retroceder de semana
    //$week_next = $week_begin+1;
    //echo "WEEK NEXT<br/>";
    //echo $week_next.'<br/>';
    
    debug("semana siguiente: ".$week_next.' '.$year_original);
    if($week_next>53){
    	$week_next=1;
    	$year_next=$year_original+1;
    	if($year_next == 2016 || $year_next == 2020){
    		$week_next = 1;
    	}
    }else{
    	if ($week_next == 53){
    		$week_next = 1;
    		$year_next=$year_original+1;
    	}else{
    		$year_next=$year_original;
    	}
    }
    debug("comprobamos final: ".$week_next.' '.$year_next);
    $check_day = date(
    		'm-d',
    		strtotime($year_next . 'W' . str_pad($week_next, 2, '0', STR_PAD_LEFT))
    );
    $check_year = date(
    		'm-d-Y',
    		strtotime($year . 'W' . str_pad($week_next, 2, '0', STR_PAD_LEFT))
    );
    //Colocamos los dias que son fiesta seguro en caso de que caigan en lunes buscamos el martes
    
    debug('comprobamos final : '.$check_day);
    debug('comprobamos final completo : '.$check_day);
    if(in_array($check_day, $holiday)|| in_array($check_year, $cases)){  	  
    	debug("en esas fechas estaba cerrado");   	
    	if($week_next<10)
    		$week_next='0'.$week_next;
    	$date_next = date('Y-m-d', strtotime($year_next."W".$week_next."2"));
    }else{
    	debug("en esas fechas estaba abierto");
    	$date_next = date(
    			'Y-m-d',
    			strtotime($year_next . 'W' . str_pad($week_next, 2, '0', STR_PAD_LEFT))
    	);
    }
    debug('fecha final : '.$date.' - '.$date_next);
    //Conectar BBDD--------------------------------------
    
	if (!$conexion)
		die('get_data_week: Error al acceder al servidor MYSQL: '.mysql_error());
	
    //---------------------------------------------------
	if($retrocedemos_inicio==0){
		$query=mysqli_query($conexion,"SELECT id_ticket 
							FROM `dash_ticket` 
							WHERE `date` LIKE '".$date."%' ORDER BY `id` ASC LIMIT 1")
							or die(mysql_error());
		$data=mysqli_fetch_array($query);
		$id_ticket=$data['id_ticket'];
	
	}else{
		$query=mysqli_query($conexion,"SELECT id_ticket
							FROM `dash_ticket`
							WHERE `date` LIKE '".$date."%' ORDER BY `id` DESC LIMIT 1")
							or die(mysql_error());
		$data=mysqli_fetch_array($query);
		$id_ticket=$data['id_ticket']+1;
	}
	
	debug('ticket inicial : '.$id_ticket);
	if($id_ticket==0){
		$id_ticket=100000000;
	}	
	
	$mon=date('Y-m-d 00:00:01',strtotime($year.'-W'.$week.'-1'));
	$sun=date('Y-m-d 23:59:59',strtotime($year.'-W'.$week.'-7'));
	
		$query=mysqli_query($conexion,"SELECT id_ticket 
							FROM `dash_ticket` 
							WHERE `date` LIKE '".$date_next."%' ORDER BY `id` ASC LIMIT 1")
		or die(mysql_error());
		$data=mysqli_fetch_array($query);
	
		$id_ticket_next=$data['id_ticket'];
		if($id_ticket_next==0){
			$id_ticket_next=100000000;
		}
	
		debug('ticket final :'.$id_ticket_next);
		$totalOri=0;
		$totalReal=0;
		
		
		//echo $test.' ---'.$date_next.' - '.$year . 'W' . str_pad($week+1, 2, '0', STR_PAD_LEFT).'<br/>';
	
	$exit=0;
	
			$query=mysqli_query($conexion,"SELECT subtotal, subtotalOriginal, items 
						FROM `dash_ticket` 
						WHERE `date` between '".$mon."%' AND '".$sun."'
						AND `description`!='Waardebon'")
						or die(mysql_error());
			
				while($data=mysqli_fetch_array($query)){
					$totalOri+=$data['subtotalOriginal']*$data['items'];
					$totalReal+=$data['subtotal']*$data['items'];
				}
			
			
			//echo 'total:'.$totalOri.' - '.$totalReal.'<br/>';

			debug('tickets:'.$id_ticket.' - '.$id_ticket_next);
	$query=mysqli_query($conexion,"SELECT SUM(subtotal), SUM(subtotalOriginal), SUM(items), COUNT(DISTINCT id_ticket) 
						FROM `dash_ticket` 
						WHERE `id_ticket` >= '".$id_ticket."' 
						AND `id_ticket`<'".$id_ticket_next."'
						AND `description`!='Waardebon'")
	or die(mysql_error());
	$data=mysqli_fetch_array($query);
	

	
	$value['pcs']= $data['SUM(items)']+$today['pcs'];	
	if($value['pcs']=='')$value['pcs']=0;
	
	$value['tickets']=$data['COUNT(DISTINCT id_ticket)']+$today['tks'];
	$value['tks']= round(($totalReal+$today['money'])/$value['tickets'],0);
		$coste=($totalOri+$today['money_completo'])/2.94;
	$value['total_margen']=($totalReal+$today['money'])-$coste;
	
	//---------------------------------------------------
	//Buscamos en payment los datos de pago para encontrar la cantidad real ingresada
	$query_payment=mysqli_query($conexion,"SELECT SUM(quantity) 
								FROM `dash_payment` 
								WHERE `id_ticket` >= '".$id_ticket."' 
								AND `id_ticket`<'".$id_ticket_next."' 
								AND type!='Waardebon'")
	or die(mysql_error());
	$data_payment=mysqli_fetch_array($query_payment);
		$value['total']=round($data_payment['SUM(quantity)']+$today['money'],2);
	//---------------------------------------------------
	
	$value['IVA']=$value['total']-($value['total']/1.21);
	$value['total_margen']=round($value['total']-$value['IVA']-$coste-($value['total']*0.03),0);
	$value['total_margen_per']=round(($value['total_margen']/$value['total'])*100,0);
	$value['desc']=round((1-($value['total']/($totalOri+$today['money_completo'])))*100,0);
	//$value['desc']=round(get_desc_ponderado($id_ticket,$id_ticket_next),0);
	
	
	$query=mysqli_query($conexion,"SELECT SUM(items) 
						FROM `dash_ticket` 
						WHERE `referencenr` LIKE 'M%' 
						AND `id_ticket` >= '".$id_ticket."'
						AND `id_ticket`<'".$id_ticket_next."'")
	or die(mysql_error());
	$data=mysqli_fetch_array($query);
	$value['M']=$data['SUM(items)']+$today['M'];
	$query=mysqli_query($conexion,"SELECT SUM(items) 
						FROM `dash_ticket` 
						WHERE `referencenr` LIKE 'G%' 
						AND `id_ticket` >= '".$id_ticket."'
						AND `id_ticket`<'".$id_ticket_next."'")

	or die(mysql_error());
	$data=mysqli_fetch_array($query);
	$value['G']=$data['SUM(items)']+$today['G'];
	$value['U']=$value['pcs']-$value['M']-$value['G'];
	
	
	$query=mysqli_query($conexion,"SELECT COUNT(country) 
						FROM `dash_buyer` 
						WHERE `country` = 'ES' 
						AND `id_ticket` >= '".$id_ticket."'
						AND `id_ticket`<'".$id_ticket_next."'")or die(mysql_error());
	$data=mysqli_fetch_array($query);
	$value['ES']=$data['COUNT(country)']+$today['ES'];
	
	$query=mysqli_query($conexion,"SELECT COUNT(country)
						FROM `dash_buyer`
						WHERE `country` <> 'ES'
						AND `id_ticket` >= '".$id_ticket."'
						AND `id_ticket`<'".$id_ticket_next."'")
							or die(mysql_error());
	$data=mysqli_fetch_array($query);
	$value['REST']=$data['COUNT(country)']+$today['REST'];
	
	$query_money_ES=mysqli_query($conexion,"SELECT SUM(quantity)
						FROM admin_dashboard.dash_payment P, admin_dashboard.dash_buyer B
						WHERE (B.country = 'ES' or B.country = 'ESP')
						AND P.type<>'Waardebon'
						AND B.id_ticket = P.id_ticket
						AND B.id_ticket >= '".$id_ticket."'
						AND B.id_ticket <'".$id_ticket_next."'")or die(mysql_error());
	$data=mysqli_fetch_array($query_money_ES);
	$value['ES_money']=$data['SUM(quantity)']+$today['ES_money'];
	
	$query_money_noES=mysqli_query($conexion,"SELECT SUM(quantity)
						FROM admin_dashboard.dash_payment P, admin_dashboard.dash_buyer B
						WHERE B.country <> 'ES'
						AND P.type<>'Waardebon'
						AND B.id_ticket = P.id_ticket
						AND B.id_ticket >= '".$id_ticket."'
						AND B.id_ticket <'".$id_ticket_next."'")or die(mysql_error());
	$data=mysqli_fetch_array($query_money_noES);
	$value['REST_money']=$data['SUM(quantity)']+$today['REST_money'];						
	//echo $value['ES_money']."----".$value['REST_money'];
	//echo '<br/>'.$id_ticket.' '.$id_ticket_next;
	$value['country']=get_stats_country($value['ES_money'],$value['REST_money']);
	$value['countryMoney']=get_stats_country($value['ES'],$value['REST']);
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
	$conexion=mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (!$conexion)
		die('same_day: Error al acceder al servidor MSSQL: '.mysql_error());
		
		debug("--------------------------------");
		debug("-- CALCULO DIA               ---");
		debug("--------------------------------");
		
		
		$week_number = date("W",time());
		$year_ori = date("Y",time());
		debug("WEEK:".$week_number );
		debug("YEAR:".$year_ori );
		 
		if($year_ori==2016 || $year_ori==2020) $week_number ++;
		
		debug("WEEK:".$week_number );
		debug("YEAR:".$year_ori );
		//echo $week_number;
		//Comprobación si estamos en la última semana del año
		if($week_number==53){			
			$year = $year_ori - 1;			
		}else{
			$year = $year_ori;
		}
		
		debug("WEEK:".$week_number );
		debug("YEAR:".$year_ori );
		
		$day_of_week = date("N",time());
		
		$year_ini = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).'0'));
		$year_end = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).'7'));
		$year_day = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).$day_of_week));
		// echo $year_ini.' '.$year_end.' '.$year_day;	
		// //Comprobación si estamos en la primera semana del año				
		if($week_number==1 && $year_day ==2016){
			$week_number+=1;
		}
		
		if($year_end==$year_ini && $year_ori==$year){			
			$year-=1;
		}else{
			$year-=1;
		}

		if($year==2020){
			$week_number+=1;
		}

		debug("WEEK:".$week_number );
		debug("YEAR:".$year );
		
		// echo 'AÑO: '.$year.'<br/>';		
		// echo 'DIA DE LA SEMANA: '.$day_of_week.'<br/>';
		// echo 'SEMANA: '.$week_number.'<br/>';
		
		$day2 = strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).$day_of_week);
		
		$fecha= date("Y-m-d",$day2);
		$date_people= date("Ymd",$day2);

		// echo $fecha;
	
	$query=mysqli_query($conexion,"SELECT SUM(quantity) FROM `dash_payment` WHERE `date` LIKE '".$fecha."%' and type<>'Waardebon'") 
	or die(mysql_error());
	$tickets=mysqli_query($conexion,"SELECT id FROM `dash_ticket` WHERE `date` LIKE '".$fecha."%' GROUP BY `id_ticket`")
	or die(mysql_error());
	$prendas=mysqli_query($conexion,"SELECT SUM(items),SUM(subtotalOriginal) FROM `dash_ticket` WHERE `date` LIKE '".$fecha."%'") 
	or die(mysql_error());

	$data['date']=date("d-m-Y",$day2);


	$nprendas=mysqli_fetch_array($prendas);
	$dato=mysqli_fetch_array($query);
	
		if($dato['SUM(quantity)']!=0){
			$data['pcs']=$nprendas['SUM(items)'];
			$data['total_completo']=round($nprendas['SUM(subtotalOriginal)'],2);
			
			$margen_total=margen_bruto($data['total_completo'],$dato['SUM(quantity)']);
			$data['total_margen']=round($margen_total['money'],0);
			$data['total_margen_per']=round($margen_total['percentage'],0);
			
			
			$data['rcp']=mysqli_num_rows($tickets);
			$data['total_day']=$dato['SUM(quantity)'];			
			$data['qty']= number_format($dato['SUM(quantity)'],2,',','.');
			$data['qty_raw']= $dato['SUM(quantity)'];
			$data['desc']=round((1-($dato['SUM(quantity)']/$nprendas['SUM(subtotalOriginal)']))*100,0);
		}else{
			$data['pcs']=0;
			$data['rcp']=0;
			$data['qty']=0;
			$data['qty_raw']=0;
		}

	$query 			= mysqli_query($conexion,"SELECT SUM(people) FROM `dash_tcuento` WHERE day = '$date_people'");
	$people_data	= mysqli_fetch_array($query);
	$data['people'] = $people_data['SUM(people)'];

	return $data;
}
function update_client($id_ticket,$id_client){
	$con=mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (mysqli_connect_errno($con))
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	
	$result=mysqli_query($con,"SELECT id FROM `dash_ticket_buyer` WHERE id_ticket='".$id_ticket."'");
	$inserted=mysqli_num_rows($result);
	 
	if($inserted>0){
		//update es cliente por lo que siempre va a ser 1
		mysqli_query($con,"UPDATE dash_ticket_buyer SET id_answer='1', id_client='".$id_client."' WHERE id_ticket='".$id_ticket."'");
	
	}else{
		//insert es cliente por lo que siempre va a ser 1
		mysqli_query($con,"INSERT INTO dash_ticket_buyer (id_ticket,
											 id_answer,
											 id_client
											 )
									 VALUES ('".$id_ticket."',
									 		 '1',
									 		 '".$id_client."'
									 		 )")
										 		 or die (mysql_error());
	}
	
	mysqli_free_result($result);
	mysqli_close($con);
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
	$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (!$conexion)
		die('get_answers: Error al acceder al servidor MSSQL: '.mysql_error());
	
	$answer['print']='<input type="hidden" name="ticket[]" value="'.$id_ticket.'">';
	$answer['print'].='<select name="answer[]" class="selects">';
	$answer['print'].='<option value="0" selected="selected">No seleccionado</option>';
	$result=mysqli_query($conexion,"SELECT * FROM `dash_ticket_buyer` WHERE id_ticket='".$id_ticket."'") or die (mysql_error());
	$data=mysqli_fetch_array($result);
	
	$result=mysqli_query($conexion,"SELECT * FROM `dash_ticket_buyer_answer` WHERE kpi = true ORDER BY answer") or die (mysql_error());
		$answer['updated']=false;
		while($row=mysqli_fetch_array($result)){
			if($data['id_answer']==$row['id']){
				$answer['print'].='<option value="'.$row['id'].'" selected>'.$row['answer'].'</option>';
				$result2=mysqli_query($conexion,"SELECT * FROM `crm_client` WHERE id='".$data['id_client']."'") or die (mysql_error());
						
				if(mysqli_num_rows($result2)>0){
					$row2=mysqli_fetch_array($result2);
					$answer['client_name']=$row2['name'];
					$answer['client_surname']=$row2['surname'];
					$answer['client_id']=$row2['id'];					
				}else{
					$answer['client_id']=0;
				}
				$answer['updated']=true;
				$answer['value']=$row['id'];
			}else{
				$answer['print'].='<option value="'.$row['id'].'">'.$row['answer'].'</option>';
				
			}
		}
	$answer['print'].='</select>';
	return $answer;
	mysqli_free_result($result);
	mysqli_close($conexion);
}

function get_data_item($item){
	
	$colour= 'Color: <b>'.$item['colour'].'</b><br/>';
	$size= 'Talla: <b>'.$item['size'].'</b><br/>'; 
	$season= 'Temporada: <b>'.$item['season'].'</b><br/>';
	$reference= 'Referencia: <b>'.$item['reference'].'</b><br/>';
	
		$name=$item['name']."<br/>";
		$details='<a href="#" class="stock" name="'.$item['reference'].'" data-reference="'.$item['reference'].'">'.$name.'</a> '.$colour.' '.$size.' '.$season.' '.$reference;
		//$details=$name.' '.$colour.' '.$size.' '.$season.' '.$reference;
		
	return $details;
}

function get_data_item_inline($item){
	
	$colour= 'Color: <b>'.$item['colour'].'</b><br/>';
	$size= 'Talla: <b>'.$item['size'].'</b><br/>'; 
	$season= 'Temporada: <b>'.$item['season'].'</b><br/>';
	$reference= 'Referencia: <b>'.$item['reference'].'</b><br/>';
	

	$details= '<div class="item-line">		
			<div class="col s80">
				<b>'.$item['name'].'</b> ('.$item['reference'].')
			</div>		
			<div class="col s20">
				<b>'.$item['size'].'</b>
			</div>		
			<div class="clear"></div>
		</div>';
		
	return $details;
}

function get_reference($reference){
	
	$link = mssql_connect(_MSSQL_SERVER, _MSSQL_USERNAME, _MSSQL_PASSWORD);
	if (!$link)
	    die('GET_REFERENCE: Error al conectar a MSSQL: '.mssql_get_last_message());
	
	$selected = mssql_select_db( _MSSQL_DB, $link) or die("Couldn't open database $myDB ." .mssql_get_last_message() ); 
		$queryDetail = "select * from artikelmatrix m inner join artikelinfo i 
						on i.artikelmatrixid = m.artikelmatrixid where i.artikelnummer = ".$reference;
	
	$resultDetail = mssql_query($queryDetail) 
					or die('GET_REFERENCE: Error al hacer la query a MSSQL: '.mssql_get_last_message());
	
	
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
	
	
		$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
		if (!$conexion)
			die('update_target: Error al conectar a MSSQL: '.mysql_error());
	
		
		$check=mysqli_query($conexion,"SELECT id FROM `dash_target` 
				WHERE year='".$dy."' 
				AND week='".$dw."'") or die(mysql_error());
		
		//echo mysql_num_rows($check);
		
		if(mysql_num_rows($check)>0){
			$row=mysqli_fetch_array($check);
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
			$result=mysqli_query($conexion,$select) or die(mysql_error());
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
	$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (!$conexion)
		die('get_margin_v2: Error al conectar a MYSQL: '.mysql_error());
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
	$query=mysqli_query($conexion,"SELECT id_ticket
						FROM `dash_ticket`
						WHERE `date` LIKE '".$date."%' ORDER BY `id` ASC LIMIT 1")
						or die(mysql_error());
	$data=mysqli_fetch_array($query);
	$id_ticket_INI=$data['id_ticket'];
	
	$query=mysqli_query($conexion,"SELECT id_ticket
						FROM `dash_ticket`
						WHERE `date` LIKE '".$date_next."%' ORDER BY `id` ASC LIMIT 1")
						or die(mysql_error());
	$data=mysqli_fetch_array($query);
	$id_ticket_FIN=$data['id_ticket'];
	if($id_ticket_FIN=='')
		$id_ticket_FIN=1000000000;
	//---------------------------------------------------
	
	echo 'Entramos en get_margin_v2<br/>';
	echo 'Fecha: '.$date.' '.$date_next.'<br/>';
	echo 'ID_ticket: '.$id_ticket_INI.' - '.$id_ticket_FIN.'<br/>';
	
	$query=mysqli_query($conexion,"SELECT id_ticket
						FROM `dash_ticket`
						WHERE `id_ticket` >= '".$id_ticket_INI."'
						AND `id_ticket` <='".$id_ticket_FIN."' 
						group by id_ticket 			
			")
						or die(mysql_error());
	
	$totalCoste=0;
	$totalSubtotal=0;
	
	while($data_main=mysqli_fetch_array($query)){
		//echo $data_main['id_ticket'].'<br/>';
		$id_ticket=$data_main['id_ticket'];
		
		$query_ticket=mysqli_query($conexion,"SELECT subtotal, subtotalOriginal,items, description
						FROM `dash_ticket`
						WHERE `id_ticket` = '".$id_ticket."'")or die(mysql_error());
		$total=0;
		$coste=1;
		$contador_coste=0;
		while($data_ticket=mysqli_fetch_array($query_ticket)){
			$total+=$data_ticket['subtotal']*$data_ticket['items'];
			$contador_coste+=$data_ticket['subtotalOriginal']/2.94;
			if($data_ticket['description']=='Waardebon')
				$coste=0;
		}
		if($coste!=0){
			$totalcoste+=$contador_coste;
			$totalSubtotal+=$total;
		}
		
		
		$query_payment=mysqli_query($conexion,"SELECT quantity, type
						FROM `dash_payment`
						WHERE `id_ticket` = '".$id_ticket."'")or die(mysql_error());
		
		$vale=0;
		$vale_cantidad=0;
		while($data_payment=mysqli_fetch_array($query_payment)){
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
	    die('get_data: Error al conectar a MSSQL: '.mssql_get_last_message());
	
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
	$dm=$token[1];
	$dd=$token[0];
}

	//$query = 'SELECT * FROM Verkoop WHERE Datum LIKE "'.$date.'%"';
	
	$query = 'SELECT Factuurnummer, Datum FROM Verkoop WHERE  
	  (DATEPART(yy, Datum) = "'.$dy.'"
AND    DATEPART(mm, Datum) = "'.$dm.'"
AND    DATEPART(dd, Datum) = "'.$dd.'")';
	$result = mssql_query($query) or die('get_data: Error al hacer la query a MSSQL: '.mssql_get_last_message());

	
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
	   $resultDetail = mssql_query($queryDetail) or die('get_data: Error al hacer la query de detalle de ticket MSSQL: '.mssql_get_last_message());
$restar=0;
		 while($rowDetail =mssql_fetch_array($resultDetail)){
		 
		 	$type=$rowDetail['Betalingswijze'];
		 	$amount=$rowDetail['Bedrag'];
		 	
		 	if($type=='Waardebon')$restar+=$amount*-1;
		 }
	
		  $queryDetail = "SELECT * FROM VerkoopDetail WHERE Factuurnummer = ".$row["Factuurnummer"];
		  $resultDetail = mssql_query($queryDetail) or die('get_data: Error al hacer la query de detalle de ticket 2 MSSQL: '.mssql_get_last_message());
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
			  $info['items'][$i][$c]['reference'] = $data['reference'];
			  $info['items'][$i][$c]['color'] = $data['colour'];
			  $info['items'][$i][$c]['size'] = $data['size'];
			  $info['items'][$i][$c]['aditional']=get_data_item($data);
			  $info['items'][$i][$c]['stock']= get_data_item_inline($data);
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
		if($row["country"]=='ES' || $row["country"]=='ESP'){ $ES++; $ES_money+=$total+$restar ;
	  	}else{ $REST++; $REST_money+=$total+$restar;}
		
	
	  	
		$total_day+=$total+$restar;
		$total_completo+=$precioCompleto;
		 
		
		
		 $info['total'][$i]=number_format($total+$restar,2,',','.');
		 $margen_total=margen_bruto($total_completo,$total_day); 
		 $info['margen'][$i]=$margen_total['money'];
		 $info['margen_per'][$i]=$margen_total['percentage'];
	  $i++;
	}

	$currentyear 	  =date('Y', time());
	$currentmonth 	  =date('m', time());
	$currentyearmonth =$currentyear.$currentmonth;
	$lastyearmonth	  =($currentyear - 1).$currentmonth;
	
	//Conectar BBDD--------------------------------------
	$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD);
	if (!$conexion)
		die('get_margin_v2: Error al conectar a MYSQL: '.mysql_error());
	//---------------------------------------------------
	
	//debug($currentyearmonth)
	$query_people_month=mysqli_query($conexion, "SELECT SUM(people) FROM `dash_tcuento` WHERE day like '".$currentyearmonth."%'") or die(mysql_error());
	$result_people_month=mysqli_fetch_array($query_people_month);
	$info['month_people'] = $result_people_month["SUM(people)"];
	
	$query_people_month=mysqli_query($conexion, "SELECT SUM(people) FROM `dash_tcuento` WHERE day like '".$lastyearmonth."%'") or die(mysql_error());
	$result_people_month=mysqli_fetch_array($query_people_month);
	$info['last_year_month_people'] = $result_people_month["SUM(people)"];



	$currentmonth=date('m',time());
	$currentyear=date('Y',time());
	$query_month_tickets =mysqli_query($conexion, "SELECT count(distinct id_ticket) FROM `dash_ticket` WHERE date BETWEEN '".$currentyear."-".$currentmonth."-01' AND '".$currentyear."-".$currentmonth."-31'")or die(mysql_error());
	$num_month_tickets=mysqli_fetch_array($query_month_tickets);
	$info['month_tickets'] = $num_month_tickets['count(distinct id_ticket)'];
	$info['total_day_raw']=$total_day;
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
	$today['ES_money']=$ES_money;
	$today['REST']=$REST;
	$today['REST_money']=$REST_money;
	$today['M']=$men;
	$today['G']=$women;
	$today['U']=$acc;
	$today['pcs']=$pcs;
	$today['tks']=$tks;
	$today['money']=$total_day;
	$today['money_completo']=$total_completo;
	
	
	
	$info['week']=get_data_week($today,"");
	$info['week2']=get_data_week($empty,date("Y",time())-1);
	//$info['week3']=get_data_week($empty,date("Y",time())-2);
	//$info['week4']=get_data_week($empty,date("Y",time())-3);
	//$info['week5']=get_data_week($empty,date("Y",time())-4);
	
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
	return $info;
}



function getTotalMonth($count){
	$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	
	
	
	$year= date("Y",time());
	$month = date("m",time());
	$year=$year-$count;
	//echo $month.'-'.$year;
	
	$query=mysqli_query($conexion, "SELECT SUM(quantity) FROM `dash_payment` WHERE
		 		date LIKE '".$year."-".$month."%' 
		 		AND `type`!='Waardebon'
		 		")
	or die(mysql_error());
	$total= 0;
	$data=mysqli_fetch_array($query);
		$total = round($data['SUM(quantity)'],2);
	return $total;
	mysqli_free_result($result);
	mysqli_close($con);
}

function getTotalTicketsMonth($count){
	$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	
	$year= date("Y",time());
	$month = date("m",time());
	$year=$year-$count;
	//echo $month.'-'.$year;
	$query=mysqli_query($conexion, "SELECT count(id_ticket) FROM `dash_ticket` WHERE
		 		date LIKE '".$year."-".$month."%'
		 		AND description<>'Waardebon'
		 		AND description<> 'KORTING'
		 		AND items <> '-1'")
	or die(mysql_error());
	$data=mysqli_fetch_array($query);
	return $data['count(id_ticket)'];;
	mysqli_free_result($result);
	mysqli_close($con);
}

function getTicketsAndItemsMonth($count){
	$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	
	$year= date("Y",time());
	$month = date("m",time());
	$year=$year-$count;
	//echo $month.'-'.$year;
	$query=mysqli_query($conexion, "SELECT SUM(items) FROM `dash_ticket` WHERE
		 		date LIKE '".$year."-".$month."%'
		 		AND description<>'Waardebon'
		 		AND description<> 'KORTING'
		 		AND items <> '-1'")
	or die(mysql_error());
	$data=mysqli_fetch_array($query);
	$info['items']  = $data['SUM(items)'];
	$query=mysqli_query($conexion, "SELECT DISTINCT id_ticket FROM `dash_ticket` WHERE
		 		date LIKE '".$year."-".$month."%'")
	or die(mysql_error());
	$info['tickets']	  = mysqli_num_rows($query);
	return $info;
	mysqli_free_result($result);
	mysqli_close($con);
}

function get(){
	$result = $mysqli->query("SELECT  SUM(items) FROM `dash_ticket` WHERE
		 		date LIKE '".$year."-".$month."%'
		 		AND description<>'Waardebon'
		 		"
		 )or die ($mysqli->error());
		 $row2 = $result->fetch_array();
		 $result->close();
		 $value['items']=$row2['SUM(items)'];
}

function getTotalYear($year){
	$conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	
	
	$query=mysqli_query($conexion, "SELECT SUM(quantity) FROM `dash_payment` WHERE
		 		date LIKE '".$year."%' 
		 		AND `type`!='Waardebon'
		 		")
	or die(mysql_error());
	$total= 0;
	$data=mysqli_fetch_array($query);
		$total = round($data['SUM(quantity)'],2);
	return $total;
	mysqli_free_result($result);
	mysqli_close($con);
}

function target_month($day){
	
	$day=str_replace('.', '', $day);
	$day=str_replace(',', '.', $day);
	
	$actual = getTotalMonth(0) + $day;
	$last = getTotalMonth(1);
	
	$per_g=round(($actual*100)/$last,2);
	$per=$per_g;
	if($last>$actual){
		$color="#FF9900";
		
	}else{
		$color="#8cbdeb";
		$per_g = 100;
	}
	$graph= 'El año pasado hicimos <b>'.number_format($last, 0, ',', '.').'&euro;</b> ahora llevamos <b>'.number_format($actual, 0, ',', '.').'&euro;</b>';
	$graph.='<div class="stats mt">';

		$graph.='<div class="bar" style="width:'.$per_g.'%; background-color:'.$color.';">'.$per.' %</div>';
	
	$graph.='<div class="clear"></div>';
	$graph.='</div>';
	
	return utf8_encode($graph);
}

function getNumberOfItems($year, $month){
	$result = $mysqli->query("SELECT count(id_ticket) FROM `dash_ticket` WHERE
		 		date LIKE '".$year."-".$month."%'
		 		AND description<>'Waardebon'
		 		AND description<> 'KORTING'
		 		AND items <> '-1'"
		)or die ($mysqli->error());
	$data=mysqli_fetch_array($result);

	return $data['count(id_ticket)'];
}


function setBar($value, $target,$symbol,$decimal){
	if(is_numeric($value) && is_numeric($target)){
		$percentage = 0;

		$color  = "#FF9900";
		$bar 	= '<div class = "bar_main">';
		$in 	= "";
		$out    = "";

		if($target >= $value){
			$percentage = ($value/$target);
		}else{
			$color="#99cc33";
			$percentage = 1;
		}
		$percentage = $percentage * 100;
		if($percentage > 50){
			$in = number_format($target,$decimal,",",".").$symbol;
		}else{
			$out = number_format($target,$decimal,",",".").$symbol;
		}
		$bar .= '	<div class = "bar"  style = "background-color: '.$color.'; width: '.$percentage.'%;">'.$in.'</div>'.$out.'';
		$bar .= '<div class="clear"></div></div>';

		return $bar ;
	}else{
		return "-";
	}
}

function debug($text){
	$is_debug = false;
	if($is_debug){
		echo $text.'<br/>';
	}
}
?>