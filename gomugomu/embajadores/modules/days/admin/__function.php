<?php
/*********************************************************
	APP_daysS
		id
		name
		created
		id_user_created
		status 0 (ok) | 1(deleted)
**********************************************************/
function days_tickets(){
	$API= new API();
	$API->moduleName("days");
	//$js=file_get_contents("../modules/days/admin/extra/show.txt");
	//$API->setJS($js);
	
	$content['id']=$_SESSION['login_id'];		
	$content['days']="";
	$content['id_item']=$_GET['id_item'];
	
	
	$query=mysql_query("SELECT * FROM `feed_ticket` GROUP BY `id_ticket` ORDER BY `id_ticket`")or die(mysql_error());
	$num=mysql_num_rows($query);
	//echo $num;
	$pages = new Paginator;
	
	$pages->url = "?go=days&do=tickets";
	$pages->items_total = $num;
	$pages->mid_range =_PAGINATOR_MID_RANGE;
	$pages->items_per_page=_PAGINATOR_ITEMS_PER_PAGE; 
	$pages->paginate();
	
	$query=mysql_query("SELECT `id_ticket`,`date`,SUM(price),COUNT(id_ticket) FROM `feed_ticket` GROUP BY `id_ticket` ORDER BY `id_ticket` ".$pages->limit)or die(mysql_error());

	$content['page']=$pages->display_pages();

	
	
	if(mysql_num_rows($query)>0){
		$lastTicket=0;
		
		
		$articles=0;
		while($dato=mysql_fetch_array($query)){
				
				$items=mysql_query("SELECT * FROM `feed_ticket` WHERE `id_ticket`='".$dato['id_ticket']."'")or die(mysql_error());
				$itemsperticket=mysql_num_rows($items);
				$lastTicket=$dato['id_ticket'];

				$num=mysql_num_rows($query);
				
					$days['id_ticket']=$dato['id_ticket'];
					$days['date']=$dato['date'];
					$days['pieces']=$dato['COUNT(id_ticket)'];
					$days['price']=round($dato["SUM(price)"],2)." "._COIN."<br/>";
					$articles++;
					$articles=0;
					
					$days['url1']="?go=days&do=ticket&id=".$days['id_ticket'];
					$url="daysrows";	
					$content['rows'].=$API->templateAdmin($url,$days);

		}
	}else{
		$content['rows'].="";
	}
	
	$url="daysticketstable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}

function days_show(){
	$API= new API();
	$API->moduleName("days");
	//$js=file_get_contents("../modules/days/admin/extra/show.txt");
	//$API->setJS($js);
	
	$content['id']=$_SESSION['login_id'];		
	$content['days']="";
	$content['id_item']=$_GET['id_item'];
	
	
	$totalhoras=mysql_query("SELECT * FROM `feed_time`");
	$total=mysql_num_rows($totalhoras);
	

	for($i=10;$i<23;$i++){
		
		if($i==22){
		$query=mysql_query("SELECT * FROM `feed_time` WHERE `hour`='22' OR `hour`='23' OR `hour`='00'")or die(mysql_error());
		$horas=mysql_num_rows($query);
		$percentage=(FLOAT)$horas/$total*100;
		$content['table'].= '<div style="width:'.round((FLOAT)$horas/$total*100*5,2).'%; padding:10px; background-color:#DDD; margin:5px 0;"><b>+21h</b> - '.round($percentage,2)."%</div>";
		}else{
			$query=mysql_query("SELECT * FROM `feed_time` WHERE hour='".$i."'" )or die(mysql_error());
		$horas=mysql_num_rows($query);
		$percentage=(FLOAT)$horas/$total*100;
		$content['table'].= '<div style="width:'.round((FLOAT)$horas/$total*100*5,2).'%; padding:10px; background-color:#DDD; margin:5px 0;"><b>'.$i."h</b> - ".round($percentage,2)."%</div>";
			
		}
			
		}
	
	/*
	TABLA MESES
	*/
	
	if(!$_GET['date']){	
		$extra="";
	}else{
		$extra=" WHERE `date`='".$_GET['date']."'";
	}
	
	$query=mysql_query("SELECT date FROM `feed_people` GROUP BY `date`")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$content['nacional'].='<a href="?go=days&date='.$dato['date'].'#nacional">'.$dato['date'].'</a> ';
	}
	
	
	$query=mysql_query("SELECT SUM(quantity) FROM `feed_people`".$extra)or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$totalNacionalidades=$dato['SUM(quantity)'];
	}
	$query=mysql_query("SELECT country, SUM(quantity) FROM `feed_people` ".$extra." GROUP BY `country`")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){

		$num= $dato['SUM(quantity)'];
		$percentage=round((FLOAT)($num/$totalNacionalidades)*100,2);
		$content['nacional'].='<div style="width:'.round($percentage,2).'%; padding:10px; background-color:#DDD; margin:5px 0;"><b>'.$dato['country']."</b> ".round($percentage,2)."%</div>";

	}
	
	$query=mysql_query("SELECT SUM(quantity) FROM `feed_people` WHERE `country`='ES' AND `date`='06-2012'");
	while($dato=mysql_fetch_array($query)){
		$content['junioEspanoles']=$dato['SUM(quantity)'];
	}
	
	$query=mysql_query("SELECT SUM(quantity) FROM `feed_people` WHERE `country`='ES' AND `date`='07-2012'");
	while($dato=mysql_fetch_array($query)){
		$content['julioEspanoles']=$dato['SUM(quantity)'];
	}
	

	$query=mysql_query("SELECT SUM(quantity) FROM `feed_people` WHERE `country`='ES'");
	while($dato=mysql_fetch_array($query)){
		$content['TotalEspanoles']=$dato['SUM(quantity)'];
	}

	$query=mysql_query("SELECT SUM(quantity) FROM `feed_people` WHERE `country`!='ES' AND `date`='06-2012'");
	while($dato=mysql_fetch_array($query)){
		$content['junioExtranjeros']=$dato['SUM(quantity)'];
	}

	$query=mysql_query("SELECT SUM(quantity) FROM `feed_people` WHERE `country`!='ES' AND `date`='07-2012'");
	while($dato=mysql_fetch_array($query)){
		$content['julioExtranjeros']=$dato['SUM(quantity)'];
	}	
	
	$query=mysql_query("SELECT SUM(quantity) FROM `feed_people` WHERE `country`!='ES' ");
	while($dato=mysql_fetch_array($query)){
		$content['TotalExtranjeros']=$dato['SUM(quantity)'];
	}	

		
	/*
	CALCULO DE DESCUENTOS 
	*/
	$descAcumulado=0.0;
	$prendasConDesc=0;
	$sumarDesc=0;
	$sumarTotal=0;
	$ticketsJunio=0;
	$ticketsJulio=0;
	$ticketsAgosto=0;
	$ticketsSeptiembre=0;
	$ticketsAgostoVale=0;
	$items=0;
	$valesjulio=0;	
	$valesagosto=0;
	$query=mysql_query("SELECT * FROM `feed_ticket`")or die(mysql_error());
	$content['junioPrendas']=0;
	$content['julioPrendas']=0;
	$content['agostoPrendas']=0;
	$content['septiembrePrendas']=0;
	$totalTickets=mysql_num_rows($query);
	while($dato=mysql_fetch_array	($query)){
		//echo $dato['subtotalOriginal']."/".$dato['subtotalDiscounts']." descuento </br>";
		$sumarDesc+=$dato['subtotalDiscounts'];
		$sumarTotal+=$dato['subtotalOriginal'];
		
		$fecha=$dato['date'];
		
		if($fecha[4]==6){
			$content['junioTotalVentas']+=$dato['subtotalOriginal'];
			$content['junioVentasDesc']+=$dato['subtotal'];
			$content['juniocoste']+=round($dato['subtotalOriginal']/2.94,2);
			$content['junioiva']+=round($dato['subtotal']-($dato['subtotal']/1.18),2);
			$ticketsJunio++;
			if($dato['items']!="-1")
				$content['junioPrendas']+=$dato['items'];
			else
				$content['junioPrendas']-=1;	
			
			
		}	
		//echo $dato['id']."-".$fecha[4]." ".$fecha."<br/>";
		if($fecha[4]==7){
			$content['julioTotalVentas']+=$dato['subtotalOriginal'];
			$content['julioVentasDesc']+=$dato['subtotal'];
			$content['juliocoste']+=round($dato['subtotalOriginal']/2.94,2);
			$content['julioiva']+=round($dato['subtotal']-($dato['subtotal']/1.18),2);
			$ticketsJulio++;		
			if($dato['items']!="-1")
				$content['julioPrendas']+=$dato['items'];
			else
				$content['julioPrendas']-=1;
			
			if($dato['description']=="Vale"){
				$valesjulio+=$dato['subtotal'];
					
			}		
		}
		if($fecha[4]==8){
			$content['agostoTotalVentas']+=$dato['subtotalOriginal'];
			//$content['agostoVentasDesc']+=$dato['subtotal'];
			$content['agostocoste']+=round($dato['subtotalOriginal']/2.94,2);
			$content['agostoiva']+=round($dato['subtotal']-($dato['subtotal']/1.18),2);
			$ticketsAgosto++;	
			$content['agostoVentasDesc']+=$dato['subtotal'];	
			if($dato['items']!="-1"){
				$content['agostoPrendas']+=$dato['items'];
				
			}else{
				$content['agostoPrendas']-=1;
				
			}
			if(($dato['description']=="Vale")&&($dato['subtotal']>0))
				$valesagosto+=$dato['subtotal'];	
					
		}
		if($fecha[4]==9){
			$content['septiembreTotalVentas']+=$dato['subtotalOriginal'];
			//$content['agostoVentasDesc']+=$dato['subtotal'];
			$content['septiembrecoste']+=round($dato['subtotalOriginal']/2.94,2);
			$content['septiembreiva']+=round($dato['subtotal']-($dato['subtotal']/1.21),2);
			$ticketsSeptiembre++;	
			$content['septiembreVentasDesc']+=$dato['subtotal'];	
			if($dato['items']!="-1"){
				$content['septiembrePrendas']+=$dato['items'];
				
			}else{
				$content['septiembrePrendas']-=1;
				
			}
			if(($dato['description']=="Vale")&&($dato['subtotal']>0))
				$valesagosto+=$dato['subtotal'];	
					
		}
		if($dato['subtotalDiscounts']!=0){
			if($fecha[4]==6){
						$content['junioTotalDesc']+=$dato['subtotalDiscounts']/$dato['subtotalOriginal'];			
			}
			if($fecha[4]==7){
						$content['julioTotalDesc']+=$dato['subtotalDiscounts']/$dato['subtotalOriginal'];			
			}
			if($fecha[4]==8){
						$content['agostoTotalDesc']+=$dato['subtotalDiscounts']/$dato['subtotalOriginal'];			
			}
			if($fecha[4]==9){
						$content['septiembreTotalDesc']+=$dato['subtotalDiscounts']/$dato['subtotalOriginal'];			
			}
			$descAcumulado+=(FLOAT)$dato['subtotalDiscounts']/$dato['subtotalOriginal'];
			$prendasConDesc+=$dato['items'];
		}
		
			//echo $descAcumulado;
	}
	
	//echo $ticketsAgostoVale." ".$items."<br/>";
	//echo $valesjulio." ".$valesagosto;
	$content['TotalPrendas']=$content['junioPrendas']+$content['julioPrendas']+$content['agostoPrendas']+$content['septiembrePrendas'];
	
	$totalValor=$sumarDesc/$sumarTotal;
	
	$content['TotalVentas']=$content['junioTotalVentas']+$content['julioTotalVentas']+$content['agostoTotalVentas']+$content['septiembreVentas'];
	$content['TotalDesc']=round($totalValor,2)*100;
	$content['VentasDesc']=$content['junioVentasDesc']+$content['julioVentasDesc']+$content['agostoVentasDesc']+$content['septiembreDesc'];
	$content['coste']=$content['juniocoste']+$content['juliocoste']+$content['agostocoste']+$content['septiembrecoste'];
	$content['iva']=$content['junioiva']+$content['julioiva']+$content['agostoiva']+$content['septiembreiva'];
	
	$content['juniobeneficio']=$content['junioVentasDesc']-($content['juniocoste']+$content['junioiva']);
	$content['juliobeneficio']=$content['julioVentasDesc']-($content['juliocoste']+$content['julioiva']);
	$content['agostobeneficio']=$content['agostoVentasDesc']-($content['agostocoste']+$content['agostoiva']);
	
	$content['septiembrebeneficio']=$content['septiembreVentasDesc']-($content['septiembrecoste']+$content['septiembreiva']);
	
	
	$content['beneficio']=$content['juniobeneficio']+$content['juliobeneficio']+$content['agostobeneficio']+$content['septiembreobeneficio'];
	
	/************************************/
	$content['juniobeneficio']=number_format($content['juniobeneficio'] , 2 , ',' , '.' );
	$content['juliobeneficio']=number_format($content['juliobeneficio'] , 2 , ',' , '.' );
	$content['agostobeneficio']=number_format($content['agostobeneficio'] , 2 , ',' , '.' );
	$content['septiembrebeneficio']=number_format($content['septiembrebeneficio'] , 2 , ',' , '.' );
	
	$content['beneficio']=number_format($content['beneficio'] , 2 , ',' , '.' );
	$content['TotalVentas']=number_format($content['TotalVentas'] , 2 , ',' , '.' );
	$content['VentasDesc']=number_format($content['VentasDesc'] , 2 , ',' , '.' );
	$content['coste']=number_format($content['coste'] , 2 , ',' , '.' );
	$content['iva']=number_format($content['iva'] , 2 , ',' , '.' );
	
	$content['junioTotalVentas']=number_format($content['junioTotalVentas'] , 2 , ',' , '.' );
	$content['julioTotalVentas']=number_format($content['julioTotalVentas'] , 2 , ',' , '.' );
	$content['agostoTotalVentas']=number_format($content['agostoTotalVentas'] , 2 , ',' , '.' );
	$content['septiembreTotalVentas']=number_format($content['septiembreTotalVentas'] , 2 , ',' , '.' );
	
	$content['junioVentasDesc']=number_format($content['junioVentasDesc'] , 2 , ',' , '.' );
	$content['julioVentasDesc']=number_format($content['julioVentasDesc'] , 2 , ',' , '.' );
	$content['agostoVentasDesc']=number_format($content['agostoVentasDesc'] , 2 , ',' , '.' );
	$content['septiembreVentasDesc']=number_format($content['septiembreVentasDesc'] , 2 , ',' , '.' );
	
	
	$content['juniocoste']=number_format($content['juniocoste'] , 2 , ',' , '.' );
	$content['juliocoste']=number_format($content['juliocoste'] , 2 , ',' , '.' );
	$content['agostocoste']=number_format($content['agostocoste'] , 2 , ',' , '.' );
	$content['septiembrecoste']=number_format($content['septiembrecoste'] , 2 , ',' , '.' );
	
	$content['junioiva']=number_format($content['junioiva'] , 2 , ',' , '.' );
	$content['julioiva']=number_format($content['julioiva'] , 2 , ',' , '.' );
	$content['agostoiva']=number_format($content['agostoiva'] , 2 , ',' , '.' );
	$content['septiembreiva']=number_format($content['septiembreiva'] , 2 , ',' , '.' );
	
	/************************************/
		
	
	$content['junioTotalDesc']=round($content['junioTotalDesc']/$ticketsJunio,2)*100;
	$content['julioTotalDesc']=round($content['julioTotalDesc']/$ticketsJulio,2)*100;
	$content['agostoTotalDesc']=round($content['agostoTotalDesc']/$ticketsAgosto,2)*100;
	$content['septiembreTotalDesc']=round($content['septiembreTotalDesc']/$ticketsSeptiembre,2)*100;
	
	$content['junioDescPonderado']=round(1-($content['junioVentasDesc']/$content['junioTotalVentas']),2)*100;
	$content['julioDescPonderado']=round(1-($content['julioVentasDesc']/$content['julioTotalVentas']),2)*100;
	$content['agostoDescPonderado']=round(1-($content['agostoVentasDesc']/$content['agostoTotalVentas']),2)*100;
	$content['septiembreDescPonderado']=round(1-($content['septiembreVentasDesc']/$content['septiembreTotalVentas']),2)*100;
	
	
	$content['TotalDescPonderado']=round(1-($content['VentasDesc']/$content['TotalVentas']),2)*100	;
	/*
	$content['table'].= "Valor del descuento (".$sumarDesc.") / Valor de las prendas: (".$sumarTotal.") = ".$totalValor."<br/>";
	
	$totalDesc= $descAcumulado/$totalTickets;
	$totalPrendasDesc= $descAcumulado/$prendasConDesc;
	$content['table'].= $totalPrendasDesc."</br>";
	$content['table'].= $totalDesc."</br>";
	*/
	
	/*
	
	*/
	$query=mysql_query("SELECT * FROM `feed_ticket` GROUP BY `id_ticket` ORDER BY `id_ticket`")or die(mysql_error());
	$num=mysql_num_rows($query);
	//echo $num;
	$pages = new Paginator;
	
	$pages->url = "?go=days";
	$pages->items_total = $num;
	$pages->mid_range =_PAGINATOR_MID_RANGE;
	$pages->items_per_page=_PAGINATOR_ITEMS_PER_PAGE; 
	$pages->paginate();
	
	$query=mysql_query("SELECT `id_ticket`,`date`,SUM(price),COUNT(id_ticket) FROM `feed_ticket` GROUP BY `id_ticket` ORDER BY `id_ticket` ".$pages->limit)or die(mysql_error());

	$content['page']=$pages->display_pages();

	
	
	if(mysql_num_rows($query)>0){
		$lastTicket=0;
		
		
		$articles=0;
		while($dato=mysql_fetch_array($query)){
				
				$items=mysql_query("SELECT * FROM `feed_ticket` WHERE `id_ticket`='".$dato['id_ticket']."'")or die(mysql_error());
				$itemsperticket=mysql_num_rows($items);
				$lastTicket=$dato['id_ticket'];

				$num=mysql_num_rows($query);
				
					$days['id_ticket']=$dato['id_ticket'];
					$days['date']=$dato['date'];
					$days['pieces']=$dato['COUNT(id_ticket)'];
					$days['price']=round($dato["SUM(price)"],2)." "._COIN."<br/>";
					$articles++;
					$articles=0;
					
					$days['url1']="?go=days&do=ticket&id=".$days['id_ticket'];
					$url="daysrows";	
					$content['rows'].=$API->templateAdmin($url,$days);

		}
	}else{
		$content['rows'].="";
	}
	
	$url="daystable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}


/*********************************************************
	CONTROL
**********************************************************/
function x_week_range($date) {
    $ts = strtotime($date);
    $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
    return array(date('Y-m-d', strtotime('+1 day', $start)),
    			 date('Y-m-d', strtotime('+2 day', $start)),
    			 date('Y-m-d', strtotime('+3 day', $start)),
    			 date('Y-m-d', strtotime('+4 day', $start)),
    			 date('Y-m-d', strtotime('+5 day', $start)),
    			 date('Y-m-d', strtotime('+6 day', $start)),
                 date('Y-m-d', strtotime('next sunday', $start)));
}

function corregir_fecha($fecha){
	$datos=explode('-',$fecha);
	return $datos[2].'-'.$datos[1].'-'.$datos[0];
}



function dayAfterDay($year,$month){
	
	if(strlen($month)==1)
		$month='0'.$month;
	$ticket=mysql_query("SELECT MAX(value) FROM `feed_control` WHERE `day` LIKE '".$year.'-'.$month."%'")or die(mysql_error());
	
	while($dato=mysql_fetch_array($ticket)){
		$value=$dato['MAX(value)'];
	}
	
	$content='<div id="dayafterday-panel"><div class="top">'.number_format($value , 2 , ',' , '.' ).'€ <img src="../template/backend/admin2/img/dayafterday-top.png" align="top"></div><div class="bottom">0€ <img src="../template/backend/admin2/img/dayafterday-bottom.png" align="top"></div></div>';	
	$techo=200;
	$target=mysql_query("SELECT * FROM `feed_control` WHERE `day` LIKE '".$year.'-'.$month."%' ORDER BY `day` ")or die(mysql_error());
	if(mysql_num_rows($target)>0){
		while($dato=mysql_fetch_array($target)){
			$height=$techo*($dato['value']/$value);
			$top=$techo-$height;
			$day=explode('-',$dato['day']);
			$content.='<a rel="tooltip" title="'.$day[2].'-'.$day[1].'-'.$day[0].'<br/>'.number_format($dato['value'] , 2 , ',' , '.' ).'€"><div class="graph2" style="height:'.$height.'px; margin-top:'.$top.'px;"; background-color:#e5e5e5;"></div></a>';
		}
				
	}else{
		$content="nada ".$month;
	}	
	return $content;
	

}



function days_control(){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/days/admin/extra/js.txt");

	$API->setJS($js);
	$url="materialedit";

	$id_ticket=$_GET['id'];
	$ticket=mysql_query("SELECT * FROM `feed_ticket` WHERE `id_ticket`='".$id_ticket."'")or die(mysql_error());
	
	$total=0;
	$totalcoste=0;
	$totalbeneficio=0;

	if(!$_GET['week']){
		$fecha=date("Y-m-d",time());
		$month=date("n",time());
    }else{
   		 $fecha=date("Y-m-d",strtotime($_GET['year']."W".$_GET['week']));
   		 $month=date("n",strtotime($_GET['year']."W".$_GET['week']));
    }
		


	if($_GET['week']=="")
		$week=date("W",time());
	else
		$week=$_GET['week'];
		
		$i=0;
		
		
		$content['weeks']= $week;
		
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
		$content['day']= "Hoy es ".$dias[date('w')];
		
		
		$dia=date("d",time());
		$year=date("Y",time());
		
		list($day[0],$day[1],$day[2],$day[3],$day[4],$day[5],$day[6]) = x_week_range($fecha);
		
		
		
		if(($week-1)==0){
			$wanterior=52;
			$yanterior=$year-1;
		}else{
			
			$wanterior=$week-1;
			$yanterior=$year;
			if(strlen($wanterior)==1)
				$wanterior='0'.$wanterior;
		}
		
		if(($week+1)>52){
			$wsiguiente=01;
			$ysiguiente=$year+1;
		}else{
			$wsiguiente=$week+1;
			$ysiguiente=$year;
		}
		
		
		$content['control']='<a href="?go=days&do=control&week='.$wanterior.'&year='.$yanterior.'"> < Anterior </a>';
		$content['control'].=" | Semana del <b>".corregir_fecha($day[0])."</b> hasta <b>".corregir_fecha($day[6])."</b> | ";
		$content['control'].='<a href="?go=days&do=control&week='.$wsiguiente.'&year='.$ysiguiente.'"> Siguiente > </a>';
		
		$max=0;
		
		for($i=0;$i<7;$i++){
			$ticket=mysql_query("SELECT * FROM `feed_control` WHERE `day`='".$day[$i]."' ")or die(mysql_error());
			//echo $day[$i];
			while($dato=mysql_fetch_array($ticket)){
				if($dato['value']>$max)
					$max=$dato['value'];
			}
		}
		
		
		for($i=0;$i<7;$i++){
			//$semana=mysql_query("SELECT * FROM `feed_control` WHERE `date`='".$day[$i]."' AND ")or die(mysql_error());
			
			$techo=200;
			$ticket=mysql_query("SELECT * FROM `feed_control` WHERE `day`='".$day[$i]."' ")or die(mysql_error());
			if($dato['value']>$max)
				$max=$dato['value'];
			if(mysql_num_rows($ticket)==0){
				$x=0;
				$value=0;
			}else{
				while($dato=mysql_fetch_array($ticket)){
						$x=$dato['value'];
						$value=number_format($dato['value'] , 2 , ',' , '.' );
				}
			}
			
			if($x==""){
				$porcentaje=0;
				$x=0;
			}else{
				$porcentaje=($x/$max);
				$x=$techo*$porcentaje;
			}
			//$x=rand(0,$techo);
			
			$content["week".$i.""]=$x;
			$content["margin".$i.""]=$techo-$x;
			$content["value".$i.""]=$value."€";
		
			
			
			$content['url'.$i]="control.php?do=control&d=".$day[$i]."&w=".$week."&value=$value";
			
			
			
			if($day[$i]==date('Y-m-d',time()))
				$content["color".$i.""]="background-color:#FF9900; font-weight:bold;";
			else
				$content["color".$i.""]="";
		}
		
		$year=explode('-',$day[1]);
		
		//echo $year[0]." ".$week;
		$target=mysql_query("SELECT * FROM `feed_control_target` WHERE `year`='".$year[0]."' AND `week`='".$week."' ")or die(mysql_error());
		//echo mysql_num_rows($target);
		if(mysql_num_rows($target)>0){
			while($dato=mysql_fetch_array($target)){
				$valueweek=$dato['value'];
				$content['max']='<a class="fancybox fancybox.iframe" href="control.php?do=target&w='.$week.'&d='.$day[0].'&value='.$valueweek.'">'.number_format($valueweek , 2 , ',' , '.' ).' €	</a>';	
				$max=$dato['value'];
			}
			
		}else{
			$content['max']='<a class="fancybox fancybox.iframe" href="control.php?do=target&w='.$week.'&d='.$day[0].'">No se ha definido un objetivo</a>';
			$max=10000;
		}
		
		$acumulado=0;
		$acumuladoPorcentaje=0;
		
		for($i=0;$i<7;$i++){
			//$semana=mysql_query("SELECT * FROM `feed_control` WHERE `date`='".$day[$i]."' AND ")or die(mysql_error());
			
			$techo=200;
			$ticket=mysql_query("SELECT * FROM `feed_control` WHERE `day`='".$day[$i]."' AND `id_week`='".$week."' ")or die(mysql_error());
			
			if(mysql_num_rows($ticket)==0){
				$x=0;
				$value=0;
			}else{
				while($dato=mysql_fetch_array($ticket)){
						$x=$dato['value'];
						$value=$dato['value'];
						
				}
			}
			$acumulado+=$value;
			$porcentaje=($x/$max)*100;
			
			if(($acumuladoPorcentaje+$porcentaje)>100){
				$sobre+=($acumuladoPorcentaje+$porcentaje)-100;
				$porcentaje=100-$acumuladoPorcentaje;
				$acumuladoPorcentaje=100;
			}else{
				$acumuladoPorcentaje+=$porcentaje;
			}
			
			$x=$techo*$porcentaje;
			
			//$x=rand(0,$techo);
			
			
			
			$color[0]="background-color:#e5e5e5;";
			$color[1]="background-color:#DDDDDD;";
			$color[2]="background-color:#e5e5e5;";
			$color[3]="background-color:#DDDDDD;";
			$color[4]="background-color:#e5e5e5;";
			$color[5]="background-color:#DDDDDD;";
			$color[6]="background-color:#e5e5e5;";
			
			$dia=0;
			
			//echo date('N',time());
			$mycolor="";
			$dia=$i+1;
			if($value!=0){
				if($day[$i]==date('Y-m-d',time()))
					$mycolor="background-color:#FF9900; font-weight:bold;";
				else
					$mycolor=$color[$i];
					
				if($sobre==0){	
				$content['bar'].='<div class="barra" style="width:'.$porcentaje.'%; '.$mycolor.'">
								 '.corregir_fecha($day[$i]).'<br/>'.number_format($value , 2 , ',' , '.' ).'€</div>';
				}else{
				$content['bar'].='<div class="barra" style="width:'.$porcentaje.'%; '.$mycolor.'"></div>';
				}
			}
		}

		
		$porcentaje=100-$acumuladoPorcentaje;
		$restante=$max-$acumulado;
		
		if($restante<0){
			$restante*=-1;
			$objetivo=$max+$restante;
		}
		$techo=175;
		if($objetivo>$max){
			
			$wporcentaje=($max/$objetivo);
			$x=$techo*$wporcentaje;
			
			$margen=$techo-$x;
			
			$height[0]=$techo;
			$top[0]=0;
			$color[0]='99cc33';
			
			$height[1]=$x;
			$top[1]=$margen;
			$color[1]='e5e5e5';
			$semana[1]='';
		}else{
			$objetivo=$max-$restante;
			$wporcentaje=($objetivo/$max);
			$x=$techo*$wporcentaje;
			
			$margen=$techo-$x;
			
			$height[0]=$x;
			$top[0]=$margen;
			$color[0]='FF9900';
			
			$height[1]=$techo;
			$top[1]=0;
			$color[1]='e5e5e5';
			$semana[1]='<br/><br/>Restante<br/>'.number_format($restante , 2 , ',' , '.' ).'€';
			
		}
		
		$content['wbar1'].='<div class="wgraph" style="height:'.$height[0].'px; margin-top:'.$top[0].'px; background-color:#'.$color[0].'; color:#FFF;">Acumulado<br/>'.number_format($objetivo , 2 , ',' , '.' ).'€</div>';
		$content['wbar2'].='<div class="wgraph" style="height:'.$height[1].'px; margin-top:'.$top[1].'px; background-color:#'.$color[1].';">Objetivo<br/>'.number_format($max , 2 , ',' , '.' ).'€'.$semana[1].'</div>';
		
		
		$content['acumulado']=number_format($acumulado , 2 , ',' , '.' );
		//echo $porcentaje;
		if(($porcentaje<10)&&($porcentaje>1)){
			$content['bar'].='<div class="barra" style="width:'.$porcentaje.'%; background-color:#CC0000;  color:#FFF;"></div>';
			$content['downbar']='Hasta el objetivo: '.number_format($restante , 2 , ',' , '.' ).'€ | '.number_format($porcentaje , 2 , ',' , '.' ).'%';
		}elseif($porcentaje==0){
			$content['bar'].="";
			$content['downbar']='Por encima del objetivo: '.number_format($restante , 2 , ',' , '.' ).'€ | '.number_format(100+$sobre , 2 , ',' , '.' ).'%';
		}else{
			$content['bar'].='<div class="barra" style="width:'.$porcentaje.'%; background-color:#CC0000;  color:#FFF;">Hasta el objetivo:<br/>'.number_format($restante , 2 , ',' , '.' ).'€ | '.number_format($porcentaje , 2 , ',' , '.' ).'%</div>';
			$content['downbar']="";
		}
				
		/*------------------------------------*/
		/*			CONTROL DEL MES			  */	
		/*------------------------------------*/
		$year=explode('-',$day[0]);
		$wsiguiente=$day[6];
		$year2=explode('-',$day[6]);
		$ysiguiente=$year2[0];
		$msiguiente=$year2[1];
		
		$mes[1]="Enero";
		$mes[2]="Febrero";
		$mes[3]="Marzo";
		$mes[4]="Abril";
		$mes[5]="Mayo";
		$mes[6]="Junio";
		$mes[7]="Julio";
		$mes[8]="Agosto";
		$mes[9]="Septiembre";
		$mes[10]="Octubre";
		$mes[11]="Noviembre";
		$mes[12]="Diciembre";
		
		if($msiguiente[0]==0)
			$msiguiente=$msiguiente[1];
		
		if($msiguiente!=$month){
			$target=mysql_query("SELECT * FROM `feed_control_target` WHERE `year`='".$year2[0]."' AND `month`='".$msiguiente."' ")or die(mysql_error());
			if(mysql_num_rows($target)>0){
				while($dato=mysql_fetch_array($target)){
					$valuemonth=$dato['value'];
					$maxmes='<a class="fancybox fancybox.iframe" href="control.php?do=target&m='.$msiguiente.'&d='.$day[6].'&value='.$valuemonth.'">'.number_format($valuemonth , 2 , ',' , '.' ).' €	</a>';	
					$max=$dato['value'];
				}
				
			}else{
				$maxmes='<a class="fancybox fancybox.iframe" href="control.php?do=target&m='.$msiguiente.'&d='.$day[6].'">No se ha definido un objetivo</a>';
				$max=40000;
			}	
			
			$target=mysql_query("SELECT * FROM `feed_control` WHERE `day` LIKE '".$year2['0'].'-'.$year2['1']."%' ")or die(mysql_error());
			$acumuladoMes=0;
			while($dato=mysql_fetch_array($target)){
					$acumuladoMes+=$dato['value'];
			}
			
			$porcentajeA=($acumuladoMes/$max)*100;
			if($porcentajeA==0)
			$content['mbar2']='<div class="barra" style="width:100%; background-color:#e5e5e5;"><b>Sin datos<br/>para el mes</b></div>';
			elseif($porcentajeA>100)
			$content['mbar2']='<div class="barra" style="width:100%; background-color:#99cc33;"><b>Acumulado en el mes</b><br/>'.number_format($acumuladoMes , 2 , ',' , '.' ).'€</div>';
			else
			$content['mbar2']='<div class="barra" style="width:'.$porcentajeA.'%; background-color:#FF9900;"><b>Acumulado en el mes</b><br/>'.number_format($acumuladoMes , 2 , ',' , '.' ).'€</div>';
			$content['class2']='class="target"';
			
			
		
			$content['m2']='<br/><h3>Objetivo de '.$mes[$msiguiente].'</h3>
							<p>Este mes el objetivo es: <b>'.$maxmes.'</b></p>';
			
		}else{
			$content['mbar2']="";
			$content['class2']="";
			$content['m2']="";
		}
		
		
		$target=mysql_query("SELECT * FROM `feed_control_target` WHERE `year`='".$year[0]."' AND `month`='".$month."' ")or die(mysql_error());
		if(mysql_num_rows($target)>0){
			while($dato=mysql_fetch_array($target)){
				$valuemonth=$dato['value'];
				$content['maxmes']='<a class="fancybox fancybox.iframe" href="control.php?do=target&m='.$month.'&d='.$day[0].'&value='.$valuemonth.'">'.number_format($valuemonth , 2 , ',' , '.' ).' €	</a>';	
				$max=$dato['value'];
			}
			
		}else{
			$content['maxmes']='<a class="fancybox fancybox.iframe" href="control.php?do=target&m='.$month.'&d='.$day[0].'">No se ha definido un objetivo</a>';
			$max=40000;
		}	
		
		$target=mysql_query("SELECT * FROM `feed_control` WHERE `day` LIKE '".$year['0'].'-'.$year['1']."%' ")or die(mysql_error());
		$acumuladoMes=0;
		while($dato=mysql_fetch_array($target)){
				$acumuladoMes+=$dato['value'];
		}
		
		if($acumuladoMes>$max)
			$content['acumuladomes']=', por encima del objetivo: '.number_format($acumuladoMes-$max , 2 , ',' , '.' ).'€';
		else
			$content['acumuladomes']=', para el objetivo: <b>'.number_format($max-$acumuladoMes , 2 , ',' , '.' ).' €</b>';
			
		
		$porcentajeA=($acumuladoMes/$max)*100;
		if($porcentajeA==0)
		$content['mbar']='<div class="barra" style="width:100%; background-color:#e5e5e5;"><b>Sin datos<br/>para el mes</b></div>';
		elseif($porcentajeA>100)
		$content['mbar']='<div class="barra" style="width:100%; background-color:#99cc33;"><b>Acumulado en el mes</b><br/>'.number_format($acumuladoMes , 2 , ',' , '.' ).'€</div>';
		else
		$content['mbar']='<div class="barra" style="width:'.$porcentajeA.'%; background-color:#FF9900;"><b>Acumulado en el mes</b><br/>'.number_format($acumuladoMes , 2 , ',' , '.' ).'€</div>';
		
		
		
		$content['dayafterday']=dayAfterDay($year[0],$month);
		
			$url="dayscontrol";
			$table=$API->templateAdmin($url,$content);
			$_SESSION['content']=$table;
			$API->printadmin();
		
				
	
}

/*********************************************************
	ADD COMPONENT 
**********************************************************/

function days_ticket($id){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="materialedit";

	$id_ticket=$_GET['id'];
	$ticket=mysql_query("SELECT * FROM `feed_ticket` WHERE `id_ticket`='".$id_ticket."'")or die(mysql_error());
	
	$total=0;
	$totalcoste=0;
	$totalbeneficio=0;
	if(mysql_num_rows($ticket)>0){
		while($dato=mysql_fetch_array($ticket)){

				$content['id']=$dato['id_ticket'];
				
				if($dato['foreigner']=="1")
					$content['checked']='checked="on"';
				
				$content['date']=$dato['date'];
				$days['coste']=round($dato['subtotalOriginal']/2.94,2);
				
				$days['beneficio']=round($dato['subtotal']-$days['coste'],2);
				
				$days['iva']=round($dato['subtotal']-($dato['subtotal']/1.18),2);
				
				$days['price']=round($dato['subtotal'],2)._COIN."<br/>";
				$days['descuento']=round($dato['subtotalDiscounts']/$dato['subtotalOriginal'],2)*100;
				
				$content['totalcoste']+=$days['coste'];
				$days['beneficio']-=$days['iva'];
				
				$days['original']=$dato['subtotalOriginal'];
				$content['totaloriginal']+=$days['original'];
				
				$content['total']+=(FLOAT)$dato['subtotal'];
				$content['totaliva']+=round($days['iva'],2);
				
				$content['totalbeneficio']=$content['total']-($content['totalcoste']+$content['totaliva']);
				
				$days['description']=$dato['description'];
				$days['referenceNr']=$dato['referencenr'];					
				$days['url1']="?go=days&do=ticket&id=".$days['id_ticket'];
				$url="daysticketrows";	
				$content['rows'].=$API->templateAdmin($url,$days);
		}
	}else{
		$content['rows'].="";
	}
	
	$url="daysticket";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();

		
	
}

/*********************************************************
	EDIT 
**********************************************************/
function days_foreigner(){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

		
		$id=$_GET['id_ticket'];
		$foreigner=$_POST['foreigner'];
		
		if($foreigner=="on")
			$foreigner=1;
		else
			$foreigner=0;
		
		//echo $foreigner;

		mysql_query("UPDATE `feed_ticket` SET `foreigner` = '".$foreigner."' WHERE `id_ticket` ='".$id."'")or die(mysql_error());	
		$API->goto("?go=days&do=ticket&id=".$id);
	
	
}

/*********************************************************
	DELETE change status 0 to 1
**********************************************************/
function days_delete($id){
	$API= new API();
	mysql_query("UPDATE `feed_day` SET `status` = '1', `id_modified_user`='".$_SESSION['login_id']."', `modified` = '".time()."'  WHERE  `id`='".$_GET['id']."'");
	$API->goto("?go=days&id_item=".$_GET['id_item']);
}

?>