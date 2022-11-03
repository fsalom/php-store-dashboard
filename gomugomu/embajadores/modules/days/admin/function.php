<?php
/*********************************************************
	APP_daysS
		id
		name
		created
		id_user_created
		status 0 (ok) | 1(deleted)
**********************************************************/
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
}

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
	
	
	
	
	if(!$_GET['date']){	
		$extra="";
		$contar="";
	}else{
		$extra=" AND `date` LIKE '%".$_GET['date']."'";
		$contar=" WHERE `date` LIKE '%".$_GET['date']."'";
	}



	if($_GET['date']=="")
		$current='class="current"';
	else
		$current='';
	
	$content['menu']='<div class="btn_panel"></ul>';
	$content['menu'].='<li><a href="?go=days" '.$current.'>Acumulado</a> </li>';
	$query=mysql_query("SELECT SUBSTRING(date,4,7) as f FROM `feed_time` GROUP BY f ORDER BY id ")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		if($dato['f']==$_GET['date'])
			$current='class="current"';
		else
			$current='';
		$content['menu'].='<li><a href="?go=days&date='.$dato['f'].'" '.$current.'>'.$dato['f'].'</a></li> ';
	}
	$content['menu'].='</ul><div class="clear"></div></div>';

	$totalhoras=mysql_query("SELECT * FROM `feed_time`".$contar);
	
	$total=mysql_num_rows($totalhoras);

	for($i=10;$i<22;$i++){
		
		if($i>20){
		$query=mysql_query("SELECT * FROM `feed_time` WHERE `hour`>'20'".$extra)or die(mysql_error());
		$horas=mysql_num_rows($query);
		$percentage=(FLOAT)$horas/$total*100;
		$content['table'].= '<div class="hgraph" style="width:'.round((FLOAT)$horas/$total*100*5,2).'%; "><b>+21h</b> - '.round($percentage,2)."%</div>";
		}else{
			$query=mysql_query("SELECT * FROM `feed_time` WHERE hour='".$i."'".$extra )or die(mysql_error());
		$horas=mysql_num_rows($query);
		$percentage=(FLOAT)$horas/$total*100;
		$content['table'].= '<div class="hgraph" style="width:'.round((FLOAT)$horas/$total*100*5,2).'%;"><b>'.$i."h</b> - ".round($percentage,2)."%</div>";
			
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
	
	
	if($_GET['date']=="")
		$current='class="current"';
	else
		$current='';
	
	/*$content['nacional']='<div class="btn_panel"></ul>';
	$content['nacional'].='<li><a href="?go=days#nacional" '.$current.'>Acumulado</a> </li>';
	$query=mysql_query("SELECT date FROM `feed_people` GROUP BY `date`")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		if($dato['date']==$_GET['date'])
			$current='class="current"';
		else
			$current='';
		$content['nacional'].='<li><a href="?go=days&date='.$dato['date'].'#nacional" '.$current.'>'.$dato['date'].'</a> </li>';
	}
	$content['nacional'].='</ul><div class="clear"></div></div>';
	*/
	$query=mysql_query("SELECT SUM(quantity) FROM `feed_people`".$extra)or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$totalNacionalidades=$dato['SUM(quantity)'];
	}
	$query=mysql_query("SELECT country, SUM(quantity) FROM `feed_people` ".$extra." GROUP BY `country`")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){

		$num= $dato['SUM(quantity)'];
		$percentage=round((FLOAT)($num/$totalNacionalidades)*100,2);
		$content['nacional'].='<div class="hgraph" style="width:'.round($percentage,2).'%; "><b>'.$dato['country']."</b> ".round($percentage,2)."%</div>";

	}

	$query=mysql_query("SELECT gender, SUM(quantity) FROM `feed_people` ".$extra." GROUP BY `gender`")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){

		if($dato['gender']=="F")
			$gender="Mujer";
		else
			$gender="Hombre";
		$num= $dato['SUM(quantity)'];
		$percentage=round((FLOAT)($num/$totalNacionalidades)*100,2);
		$content['sexo'].='<div class="hgraph" style="width:'.round($percentage,2).'%; "><b>'.$gender."</b> ".round($percentage,2)."%</div>";

	}
	
	$query=mysql_query("SELECT age, SUM(quantity) FROM `feed_people` ".$extra." GROUP BY `age`")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){

		$num= $dato['SUM(quantity)'];
		$percentage=round((FLOAT)($num/$totalNacionalidades)*100,2);
		$content['edades'].='<div class="hgraph" style="width:'.round($percentage,2).'%; "><b>'.$dato['age']."</b> ".round($percentage,2)."%</div>";

	}

		
	
	$query=mysql_query("SELECT SUM(price),SUM(items),SUM(subtotalDiscounts),SUM(subtotalOriginal),SUM(subtotal),SUBSTRING(date,4,7) as f FROM `feed_ticket` GROUP BY f ORDER BY id ")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		
		$descuento_medio=0;
		$num_ticket=0;
		$descuento=mysql_query("SELECT * FROM `feed_ticket` WHERE `date` LIKE '%".$dato['f']."%' ")or die(mysql_error());
		while($row=mysql_fetch_array($descuento)){
			if($row['subtotalOriginal']!=0)
			$descuento_medio+=$row['subtotal']/$row['subtotalOriginal'];
			$num_ticket++;
		}
		
		$tickets=mysql_query("SELECT * FROM `feed_ticket` WHERE `date` LIKE '%".$dato['f']."%'  GROUP BY id_ticket")or die(mysql_error());
		
		$col['col_num_tickets']=mysql_num_rows($tickets);
		$col['col_items_per_ticket']=round($dato['SUM(items)']/mysql_num_rows($tickets),2);
		$col['col_ticket_medio']=number_format(($dato['SUM(subtotal)']/mysql_num_rows($tickets)),2,',','.').' €';
		
		$col['col_total_desc']=round((1-($descuento_medio/$num_ticket))*100,2).' %';
		$col['col_mes']=$dato['f'];
		$col['col_prendas']=$dato['SUM(items)'];
		$col['col_ventas']=number_format($dato['SUM(subtotalOriginal)'] , 2 , ',' , '.' ).' €';
		$col['col_ponderado_desc']=round(($dato['SUM(subtotalDiscounts)']/$dato['SUM(subtotalOriginal)'])*100,2).' %';
		
		$col['col_ventas_desc']=number_format($dato['SUM(subtotal)'] , 2 , ',' , '.' ).' €';
		$col['col_coste']=number_format($dato['SUM(subtotalOriginal)']/2.94,2,',','.').' €';
		$fecha=explode('-',$dato['f']);
		$num=$fecha[1].$fecha[0];
		
		$iva=1.18;
		if($num>201208)
			$iva=1.21;
		
		
		$col['col_iva']=number_format($dato['SUM(subtotal)']-($dato['SUM(subtotal)']/$iva),2,',','.').' €';
		$col['col_beneficio']=$col['col_ventas_desc']-($col['col_coste']+$col['col_iva']).' €';

		if($_GET['date']==$dato['f'])
			$col['color']='background-color:#FF9900;';
		else
			$col['color']='';
		$url="daystable_col";
		
		$content['col'].=$API->templateAdmin($url,$col);
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
	//$ticket=mysql_query("SELECT MAX(value) FROM `feed_control` WHERE `day` LIKE '".$year.'-'.$month."%'")or die(mysql_error());
	
		$value=0;
	$target=mysql_query("SELECT SUM(value), day FROM `feed_control` WHERE `day` LIKE '".$year.'-'.$month."%' GROUP BY `day` ORDER BY `day` ") or die(mysql_error());
	while($dato=mysql_fetch_array($target)){
		if($value<$dato['SUM(value)'])
			$value=$dato['SUM(value)'];
	}
	
	$content='<div id="dayafterday-panel"><div class="top">'.number_format($value , 2 , ',' , '.' ).'€ <img src="../template/backend/admin2/img/dayafterday-top.png" align="top"></div><div class="bottom">0€ <img src="../template/backend/admin2/img/dayafterday-bottom.png" align="top"></div></div>';	
	$techo=200;
	$target=mysql_query("SELECT SUM(value), day FROM `feed_control` WHERE `day` LIKE '".$year.'-'.$month."%' GROUP BY `day` ORDER BY `day` ")or die(mysql_error());
	if(mysql_num_rows($target)>0){
		while($dato=mysql_fetch_array($target)){
			$height=$techo*($dato['SUM(value)']/$value);
			$top=$techo-$height;
			$day=explode('-',$dato['day']);
			$content.='<a rel="tooltip" title="'.$day[2].'-'.$day[1].'-'.$day[0].'<br/>'.number_format($dato['SUM(value)'] , 2 , ',' , '.' ).'€"><div class="graph2" style="height:'.$height.'px; margin-top:'.$top.'px;"; background-color:#e5e5e5;"></div></a>';
		}
				
	}else{
		$content="nada ".$month;
	}	

	//$ticket=mysql_query("SELECT MAX(value) FROM `feed_control` WHERE `day` LIKE '".$year.'-'.$month."%'")or die(mysql_error());
	
	return $content;
	

}

function dayAfterDay_v2($year,$month){
		
	if(strlen($month)==1)
		$month='0'.$month;
	
	$max=0;
	
	$daysMonth=cal_days_in_month(CAL_GREGORIAN, $month, $year);
	
	for ($i=0;$i<$daysMonth+1;$i++){
		if($i<10)
			$d='0'.$i;
		else
			$d=$i;
	$target=mysql_query("SELECT SUM(quantity), date FROM `feed_buyers` WHERE `date` LIKE '".$d."-".$month."-".$year."%' ") or die(mysql_error());
		
		while($dato=mysql_fetch_array($target)){
			if($max<$dato['SUM(quantity)'])
				$max=$dato['SUM(quantity)'];
			}
			
	}
	
	$content='<div id="dayafterday-panel"><div class="top">'.number_format($max , 2 , ',' , '.' ).'€ <img src="../template/backend/admin2/img/dayafterday-top.png" align="top"></div><div class="bottom">0€ <img src="../template/backend/admin2/img/dayafterday-bottom.png" align="top"></div></div>';	
	$techo=200;
	
	
	
	for ($i=1;$i<$daysMonth+1;$i++){
		if($i<10)
			$d='0'.$i;
		else
			$d=$i;
		$target=mysql_query("SELECT SUM(quantity), date FROM `feed_buyers` WHERE `date` LIKE '".$d."-".$month."-".$year."%' ORDER BY `date` ")or die(mysql_error());
	
		if(mysql_num_rows($target)>0){
			while($dato=mysql_fetch_array($target)){
				if($dato['SUM(quantity)']!=""){
					$height=$techo*($dato['SUM(quantity)']/$max);
					$top=$techo-$height;
					$day=explode('-',$dato['date']);
					$content.='<a rel="tooltip" title="'.$dato['date'].'<br/>'.number_format($dato['SUM(quantity)'] , 2 , ',' , '.' ).'€"><div class="graph2" style="height:'.$height.'px; margin-top:'.$top.'px; background-color:#FF9900;"></div></a>';
				}else{
					//echo $dato['date'];
					$content.='<a rel="tooltip" title="'.$d.'-'.$month.'-'.$year.'<br/>0€"><div class="graph2" style="height:0px; margin-top:200px;	 background-color:#e5e5e5;"></div></a>';
				}
				
			}
				
		}
	}
	//$ticket=mysql_query("SELECT MAX(value) FROM `feed_control` WHERE `day` LIKE '".$year.'-'.$month."%'")or die(mysql_error());
	
	return $content;
	

}



function input_week($week){
	$return='<select name="weeks">';
	for($i=1;$i<53;$i++){
		$x=$i;
		if($i<10)
			$x='0'.$i;
		if($i==$week)
			$return.='<option value="'.$x.'" selected="selected">'.$i.'</option>';
		else
			$return.='<option value="'.$x.'">'.$i.'</option>';
	}
	$return.='</select>';
	return $return;
}

function input_year($year){
	$return='<select name="years">';
	for($i=2012;$i<2018;$i++){
		if($i==$year)
			$return.='<option value="'.$i.'" selected="selected">'.$i.'</option>';
		else
			$return.='<option value="'.$i.'">'.$i.'</option>';
	}
	$return.='</select>';
	return $return;
}



/******************************************
		ESTADISTICAS DE COMPRADORES
******************************************/
function days_stats(){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/days/admin/extra/js.txt");

	$API->setJS($js);

	$month[1]='Enero';
	$month[2]='Febrero';
	$month[3]='Marzo';
	$month[4]='Abril';
	$month[5]='Mayo';
	$month[6]='Junio';
	$month[7]='Julio';
	$month[8]='Agosto';
	$month[9]='Septiembre';
	$month[10]='Octubre';
	$month[11]='Noviembre';
	$month[12]='Diciembre';
	
	if($_GET['form']==1){
		$token = explode('-',$_POST['begin']);
		$begin= $token[0]."-".$token[1]."-".$token[2];
		$nbegin=  $token[0].$token[1].$token[2];

		
		$token= explode('-',$_POST['end']);
		$end= $token[0]."-".$token[1]."-".$token[2];
		$nend=  $token[0].$token[1].$token[2];
		
		
		if($nend<$nbegin){
			$content['alert']='<div class="rednotification">La fecha final es menor a la fecha de comienzo</div>';
			$search=mysql_query("SELECT * FROM `feed_buyers` ") or die(mysql_error());
			$extra="";
			$num=mysql_num_rows($search);
		}else{
		
		if($nbegin<20130121)
			$begin="2013-01-21";
		
		
		$search=mysql_query("SELECT * FROM `feed_buyers` 
		WHERE date2 >='".$begin."' AND date2 <='".$end."'") or die(mysql_error());
		$extra="AND date2 >='".$begin."' AND date2 <='".$end."'";
		$num=mysql_num_rows($search);
		$search_money=mysql_query("SELECT SUM(quantity) FROM `feed_buyers` WHERE date2 >='".$begin."' AND date2 <='".$end."'") or die(mysql_error());
		
		while($dato=mysql_fetch_array($search_money)){
			$cantidad=$dato['SUM(quantity)'];
		}
		
		$search_out=mysql_query("SELECT * FROM `feed_buyers` WHERE `from` ='1' AND date2 >='".$begin."' AND date2 <='".$end."'") or die(mysql_error());
		$search_in=mysql_query("SELECT * FROM `feed_buyers` WHERE `from` ='0' AND date2 >='".$begin."' AND date2 <='".$end."'") or die(mysql_error());
		
		$search_money_out=mysql_query("SELECT SUM(quantity) FROM `feed_buyers` WHERE `from` ='1' AND date2 >='".$begin."' AND date2 <='".$end."'") or die(mysql_error());

		while($dato=mysql_fetch_array($search_money_out)){
			$cantidad_out=$dato['SUM(quantity)'];
		}
		
		$search_money_in=mysql_query("SELECT SUM(quantity) FROM `feed_buyers` WHERE `from` ='0' AND date2 >='".$begin."' AND date2 <='".$end."'") or die(mysql_error());

		while($dato=mysql_fetch_array($search_money_in)){
			$cantidad_in=$dato['SUM(quantity)'];
		}
		
		$content['alert']='<div class="notification">Desde: '.$begin.' hasta: '.$end.'<br/>Ventas: <b>'.number_format($cantidad, 2 , ',' , '.' )."€</b> <br/>Compradores: <b>".$num."</b></div>";
				
		}
	}else{
		$content['alert']="";
		$search_money=mysql_query("SELECT SUM(quantity) FROM `feed_buyers` WHERE date2 >='2013-01-21'")or die(mysql_error());
		$search=mysql_query("SELECT * FROM `feed_buyers` WHERE date2 >='2013-01-21'")or die(mysql_error());
		$num=mysql_num_rows($search);
		
		while($dato=mysql_fetch_array($search_money)){
			$cantidad=$dato['SUM(quantity)'];
			
		}
		$search_out=mysql_query("SELECT * FROM `feed_buyers` WHERE `from` ='1' AND date2 >='2013-01-21'") or die(mysql_error());
		$search_in=mysql_query("SELECT * FROM `feed_buyers` WHERE `from` ='0' AND date2 >='2013-01-21'") or die(mysql_error());

		$search_money_out=mysql_query("SELECT SUM(quantity) FROM `feed_buyers` WHERE `from` ='1' AND date2 >='2013-01-21'") or die(mysql_error());

		while($dato=mysql_fetch_array($search_money_out)){
			$cantidad_out=$dato['SUM(quantity)'];
		}
		
		$search_money_in=mysql_query("SELECT SUM(quantity) FROM `feed_buyers` WHERE `from` ='0' AND date2 >='2013-01-21'") or die(mysql_error());

		while($dato=mysql_fetch_array($search_money_in)){
			$cantidad_in=$dato['SUM(quantity)'];
		}
		$extra="AND date2 >='2013-01-21'";
		
		$content['alert']='<div class="notification">Desde: 2013-01-21 hasta: '.date('Y-m-d',Time()).'<br/>Ventas: <b>'.number_format($cantidad, 2 , ',' , '.' )." €</b> <br/>Compradores: <b>".$num."</b></div>";
	}
	
	
	
	
	$how_reference[0]="Ya es cliente";
	$how_reference[1]="Pasaba por aquí";
	$how_reference[2]="Viajando fuera";
	$how_reference[3]="Flyer";
	$how_reference[4]="Evento en tienda";
	$how_reference[5]="Evento fuera de la tienda";
	$how_reference[6]="Cartel en Colón";
	$how_reference[7]="Otros";
	$how_reference[8]="Es turista";
	$how_reference[9]="Por un conocido";
	$how_reference[10]="Corte ingles";
	$how_reference[11]="Colegios";
	$how_reference[12]="Brand Ambassadors";
	
	$percentage=round((mysql_num_rows($search_in)/mysql_num_rows($search))*100,2);
	$content['bar2'].="Españoles: <b>".mysql_num_rows($search_in).'</b>';
	$content['bar2'].='<div class="hgraph" style="width:'.$percentage.'%;">'.$percentage."% </div>";	
	$percentage=round((mysql_num_rows($search_out)/mysql_num_rows($search))*100,2);
	$content['bar2'].='<div class="hgraph" style="width:'.$percentage.'%; background-color:#FF9900;">'.$percentage."% </div>";	
	$content['bar2'].="Extranjeros: <b>".mysql_num_rows($search_out).'</b><br/><br/>';
	
	$percentage=round(($cantidad_in/$cantidad)*100,2);
	$content['bar2'].="Españoles: <b>".number_format($cantidad_in, 2 , ',' , '.' ).' €</b>';
	$content['bar2'].='<div class="hgraph" style="width:'.$percentage.'%;">'.$percentage."% </div>";	
	$percentage=round(($cantidad_out/$cantidad)*100,2);
	$content['bar2'].='<div class="hgraph" style="width:'.$percentage.'%; background-color:#FF9900;">'.$percentage."% </div>";	
	$content['bar2'].="Extranjeros: <b>".number_format($cantidad_out, 2 , ',' , '.' ).' €</b>';
	
	
	if($num!=0){		
	for ($i=0;$i<11;$i++){
		$how=mysql_query("SELECT * FROM `feed_buyers` WHERE `how`='".$i."' ".$extra)or die(mysql_error());
		$how_money=mysql_query("SELECT SUM(quantity) FROM `feed_buyers` WHERE `how`='".$i."' ".$extra)or die(mysql_error());
		while($dato=mysql_fetch_array($how_money)){
			$quantity_how=$dato['SUM(quantity)'];
		}
		if(mysql_num_rows($how)!=0){
			
			 if($i==7){
			$percentage=(round(mysql_num_rows($how)/$num,2)*100);
			$percentage_money=(round($quantity_how/$cantidad,2)*100);
			$others.= "<b>".$how_reference[$i]."</b> - ".mysql_num_rows($how)."<br/>";
			$others.= "<b>".number_format($quantity_how, 2 , ',' , '.' )."€</b> - Ticket medio: <b>".number_format($quantity_how/mysql_num_rows($how), 2 , ',' , '.' )."€</b><br/>";
			$others.= '<div class="hgraph" style="width:'.$percentage.'%;">'.$percentage."% </div>";
			$others.= '<div class="hgraph" style="width:'.$percentage_money.'%; background-color:#FF9900;">'.$percentage_money."% </div>";

			
			$others.="<ul>";
			while($dato=mysql_fetch_array($how)){
				
				if($dato['others']!="")
					$others.="<li>".$dato['others']."</li>";
			}
			$others.="</ul>";
			
		}else{
			
		$percentage=(round(mysql_num_rows($how)/$num,2)*100);
		$percentage_money=(round($quantity_how/$cantidad,2)*100);
		$content['bar'].= "<b>".$how_reference[$i]."</b> - ".mysql_num_rows($how)."<br/>";
		$content['bar'].= '<div style="font-size:10px;"><b>'.number_format($quantity_how, 2 , ',' , '.' )."€</b> - Ticket medio: <b>".number_format($quantity_how/mysql_num_rows($how), 2 , ',' , '.' )."€</b></div>";
		$content['bar'].= '<div class="hgraph" style="width:'.$percentage.'%;">'.$percentage."% </div>";
		$content['bar'].= '<div class="hgraph" style="width:'.$percentage_money.'%; background-color:#FF9900;">'.$percentage_money."% </div>";
		
			
			}
		}
	}
	}
	$content['bar'].=$others;
	
	$url="daysstats";

	$table=$API->templateAdmin($url,$content);
	
	$_SESSION['content']=$table;
	$API->printadmin();
		
				
	
}


function real_vs_goal($week,$year){
  	
  	$fecha_inicio=date("Y-m-d",strtotime($year."W01"));
	list($inicio[0],$inicio[1],$inicio[2],$inicio[3],$inicio[4],$inicio[5],$inicio[6]) = x_week_range($fecha_inicio);		
	$fi=explode("-",$inicio[0]);
	$start_day=$fi[2];
	$start=$fi[1];
	  	
  	$fecha_fin=date("Y-m-d",strtotime($year."W".$week));
	list($fin[0],$fin[1],$fin[2],$fin[3],$fin[4],$fin[5],$fin[6]) = x_week_range($fecha_fin);		
	$ff=explode("-",$fin[6]);
	$end_day=$ff[2];
	$end=$ff[1];
	
	if($ff[0]!=2012){
		$acumulado_objetivo=0;

		for($i=0;$i<$week+1;$i++){	
			$search=mysql_query("SELECT value FROM `feed_control_target` WHERE `week`='".$i."' AND `year`='".$year."' AND `week`<>'0'")or die(mysql_error());
				while($dato=mysql_fetch_array($search)){
					$acumulado_objetivo+=$dato['value'];
				}
				
		}
		
		if($year==2013)
			$acumulado=1051.95;
		else	
			$acumulado=0;
		
		for($i=1;$i<$end+1;$i++){
			$m=$i;
			if($i<10)
				$m='0'.$i;
				
			$days=cal_days_in_month(CAL_GREGORIAN, $m, $year);
			//echo $days."<br/>";
			for($x=1;$x<$days+1;$x++){
				$d=$x;
				
				if($d<10)
					$d='0'.$x;
				
				
				$search=mysql_query("SELECT SUM(quantity) FROM `feed_buyers` WHERE `date` LIKE '".$d."-".$m."-".$year."%'")or die(mysql_error());
				while($dato=mysql_fetch_array($search)){
					$acumulado+=$dato['SUM(quantity)'];
					//$fecha=$d."-".$m."-".$year;
					//echo  $fecha." ".$dato['SUM(quantity)']."<br/>";
				}
				
				if(($m==$end)&&($d==$end_day))
					break;
			}
			
	
			}
			if(($acumulado-$acumulado_objetivo)<0)
				$diferencia='<b style="color:#990000;">'.number_format(($acumulado-$acumulado_objetivo), 2 , ',' , '.' )."</b>";
			else
				$diferencia='<b style="color:#99cc33;">'.number_format(($acumulado-$acumulado_objetivo), 2 , ',' , '.' )."</b>";
				
			return "<b>Objetivo acumulado:</b> ".number_format($acumulado_objetivo, 0 , ',' , '.' )."€<br/>
					<b>Acumulado real:</b> ".number_format($acumulado , 2 , ',' , '.' )."€<br/>
					<b>Diferencia:</b> ".$diferencia." €";
					
	}else{
		return "Característica no disponible para este año";
	}
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


	if($_POST['weeks']!=""){
		$week=$_POST['weeks'];
	}else if($_GET['week']!=""){
		$week=$_GET['week'];
	}else{
		$week=date("W",time());
	}	
	
	$year=$_GET['year'];
	
	if(($_GET['year']=="")){
		$year=date("Y",time());
	}else{
		$year=$_GET['year'];
	}	
	
	if($_POST['years']!=""){
		$year=$_POST['years'];
	}	
	
	
	$content['input-week']=input_week($week);
	$content['input-year']=input_year($year);


	$fechaValor=date("Ymd",strtotime($year."W".$week));
	
	if($week==""){
		$fecha=date("Y-m-d",time());
		$month=date("n",time());
    }else{
   		 $fecha=date("Y-m-d",strtotime($year."W".$week));
   		 $month=date("n",strtotime($year."W".$week));
   		
    }
		

    $content['weeks']= $week;
    
    $i=0;
		
		
		
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
		$content['day']= "Hoy es ".$dias[date('w')];
		
		$dia=date("d",time());
		
		
		list($day[0],$day[1],$day[2],$day[3],$day[4],$day[5],$day[6]) = x_week_range($fecha);
		
		$content['real_vs_goal']=real_vs_goal($week,$year);
		
		
		
		
		if($_GET['year']=="")
			$year=date("Y",time());
		else
			$year=$_GET['year'];
		
		if(($week-1)==0){
			$wanterior=53;
			$yanterior=$year-1;
		}else{
			
			$wanterior=$week-1;
			$yanterior=$year;
			if(strlen($wanterior)==1)
				$wanterior='0'.$wanterior;
		}
		
		if(($week+1)>53){
			$wsiguiente="01";
			$ysiguiente=$year+1;
		}else{
			$wsiguiente=$week+1;
			$ysiguiente=$year;
			if(strlen($wsiguiente)==1)
				$wsiguiente='0'.$wsiguiente;
		}
		
		
		$content['control']='<a href="?go=days&do=control&week='.$wanterior.'&year='.$yanterior.'"> < Anterior </a>';
		$content['control'].=" | Semana del <b>".corregir_fecha($day[0])."</b> hasta <b>".corregir_fecha($day[6])."</b> | ";
		$content['control'].='<a href="?go=days&do=control&week='.$wsiguiente.'&year='.$ysiguiente.'"> Siguiente > </a>';
		
		
		if($fechaValor<20130106){
		$max=1;
		
		for($i=0;$i<7;$i++){
			$ticket=mysql_query("SELECT * FROM `feed_control` WHERE `day`='".$day[$i]."' AND `store`='1' ")or die(mysql_error());
			$ticket2=mysql_query("SELECT * FROM `feed_control` WHERE `day`='".$day[$i]."' AND `store`='2' ")or die(mysql_error());
			//echo $day[$i];
			while($dato=mysql_fetch_array($ticket)){
				$x=$dato['value'];
			}
			while($dato2=mysql_fetch_array($ticket2)){
				$y=$dato2['value'];
			}
			if(($y+$x)>$max)
					$max=$y+$x;
		}
		
		
		for($i=0;$i<7;$i++){
			
			$techo=200;
			$tienda1=mysql_query("SELECT * FROM `feed_control` WHERE `day`='".$day[$i]."' AND `store`='1' ")or die(mysql_error());
			$tienda2=mysql_query("SELECT * FROM `feed_control` WHERE `day`='".$day[$i]."' AND `store`='2'")or die(mysql_error());
			
			
			
			if(mysql_num_rows($tienda1)==0){
				$x=0;
				$value1=0;
			}else{
				while($dato=mysql_fetch_array($tienda1)){
						$x=$dato['value'];
						$value1=number_format($dato['value'] , 2 , ',' , '.' );
				}
			}
			
			if(mysql_num_rows($tienda2)==0){
				$y=0;
				$value2=0;
			}else{
				while($dato=mysql_fetch_array($tienda2)){
						$y=$dato['value'];
						if($dato['value']!=0)
							$value2=number_format($dato['value'] , 2 , ',' , '.' );
						else
							$value2=0;
				}
			}
			
			$tmax=$x+$y;
			if($tmax>$max)
				$max=$tmax;
			
			
			
				
			$porcentaje=(FLOAT)(($x+$y)/$max);
			
			
				
			$porcentajex=(FLOAT)$x/$max;
			$porcentajey=(FLOAT)$y/$max;
			///////
			
			
			
			/////
			//echo $x." y:".$y." max:".$max." %:".$porcentaje." %x".$porcentajex." %y".$porcentajey."<br/>";
			$height0=$techo*$porcentaje;
			
			
			$heightx=$techo*$porcentajex;
			$heighty=$techo*$porcentajey;
			
			$content["week".$i.$i.""]=$height0;
			$content["week".$i."1"]=$heightx;
			$content["week".$i."2"]=$heighty;
			
			//echo "H:".$height." - ".$heightx." - ".$heighty."<br/>";
			
			$content["margin".$i.$i.""]=$techo-$height0;
			$content["value".$i."0"]=number_format(($x+$y) , 2 , ',' , '.' )."€";
			
			$content["value".$i."1"]=$value1."€";
			if($value2==0)
				$content["value".$i."2"]="";
			else
				$content["value".$i."2"]=$value2."€";
			
			$content['url0'.$i]="control.php?do=control&d=".$day[$i]."&w=".$week."&value=".$value1."&store=1";
			$content['url1'.$i]="control.php?do=control&d=".$day[$i]."&w=".$week."&value=".$value2."&store=2";
			
			$content["color02"]="background-color:#FFCC00;";
			$content["color01"]="background-color:#FF6600;";
			if($day[$i]==date('Y-m-d',time()))
				$content["color".$i.""]="background-color:#FF9900; font-weight:bold;";
			else
				$content["color".$i.""]="";
		}
		}else{
		
		// A PARTIR DEL 1 de ENERO de 2013
		
		$max=1;
		
		//MEDIA DIA
		$media_dia=media_dia();
		//---------------------------------
		for($i=0;$i<7;$i++){
			$rompe=explode('-',$day[$i]);
			$dia=$rompe[2]."-".$rompe[1]."-".$rompe[0];
			$ticket=mysql_query("SELECT sum(quantity) FROM `feed_buyers` WHERE `date` LIKE '".$dia."%' ")or die(mysql_error());
			while($dato=mysql_fetch_array($ticket)){
				$x=$dato['sum(quantity)'];
			}
			
			
			
			
			
			//OBTENER MEDIA EN ESE DIA
			
			$break=explode("-",$day[$i]);
			$mktime=mktime(0, 0, 0, $break[1], $break[2], $break[0]);
			$dia_de_la_semana=strftime("%A",$mktime);
			$media[$dia_de_la_semana]=$media_dia[$dia_de_la_semana]['total']/$media_dia[$dia_de_la_semana]['num'];
			
			/////////////////////////
			
			
			if(($x)>$max)
					$max=$x;
			if($media[$dia_de_la_semana]>$max)
					$max=$media[$dia_de_la_semana];
			
			
			
		}
		$acumulado_tienda1=0;
		$acumulado_tienda2=0;
		
		for($i=0;$i<7;$i++){
			$rompe=explode('-',$day[$i]);
			$dia=$rompe[2]."-".$rompe[1]."-".$rompe[0];
			$techo=200;
			$tienda1=mysql_query("
				SELECT SUM(quantity) FROM `feed_buyers` WHERE `date` LIKE '".$dia."%' AND `store`='0'
				")or die(mysql_error());
			$tienda2=mysql_query("
				SELECT SUM(quantity) FROM `feed_buyers` WHERE `date` LIKE '".$dia."%' AND `store`='1'
				")or die(mysql_error());
			
			if(mysql_num_rows($tienda1)==0){
				$x=0;
				$value1=0;
			}else{
				while($dato=mysql_fetch_array($tienda1)){
						$x=$dato['SUM(quantity)'];
						$value1=number_format($dato['SUM(quantity)'] , 2 , ',' , '.' );
						$acumulado_tienda1+=$dato['SUM(quantity)'];
				}
				
			}
			
			
			if(mysql_num_rows($tienda2)==0){
				$y=0;
				$value2=0;
			}else{
				while($dato=mysql_fetch_array($tienda2)){
						$y=$dato['SUM(quantity)'];
						$value2=number_format($dato['SUM(quantity)'] , 2 , ',' , '.' );
						$acumulado_tienda2+=$dato['SUM(quantity)'];
				}
				
			}
			
			//OBTENER MEDIA EN ESE DIA
			
			
			/////////////////////////
			$break=explode("-",$day[$i]);
			$mktime=mktime(0, 0, 0, $break[1], $break[2], $break[0]);
			$dia_de_la_semana=strftime("%A",$mktime);
			
			$tmax=$x+$y;
			if($tmax>$max)
				$max=$tmax;
			
			if($media[$dia_de_la_semana]>$max)
				$max=$media[$dia_de_la_semana];
			
			if($media[$dia_de_la_semana]>($x+$y))
				$porcentajemedia=(FLOAT)$media[$dia_de_la_semana]/$max;
			else
				$porcentajemedia=(FLOAT)($x+$y)/$max;
				
			
			if(($x+$y)<$media[$dia_de_la_semana]){
				$content["week".$i."3"]=(($media[$dia_de_la_semana]-($x+$y))/$max)*$techo;
				$content["value".$i."3"]=number_format($media[$dia_de_la_semana]-($x+$y),2,',','.');
			}else{
				$content["week".$i."3"]=0;
				$content["value".$i."3"]="";
				$content["display".$i."3"]="display:none;";
			}
			//echo $i."- ".(($media[$dia_de_la_semana]-($x+$y))/$max)."<br/>";
			
			$porcentaje=(FLOAT)(($x+$y)/$max);
			$porcentajex=(FLOAT)$x/$max;
			$porcentajey=(FLOAT)$y/$max;
			
			//echo $x." y:".$y." max:".$max." %:".$porcentaje." %x".$porcentajex." %y".$porcentajey."<br/>";
			$height0=$techo*$porcentaje;
			//$height0=$techo*$porcentajemedia;
			
			$heightx=$techo*$porcentajex;
			$heighty=$techo*$porcentajey;
			
			if($heighty==0)
				$content["display".$i]="display:none;";
			
			if($media[$dia_de_la_semana]<($x+$y))
				$content["week".$i.$i.""]=$height0;
			else
				$content["week".$i.$i.""]=$content["week".$i."3"]+$height0;
			
			$content["week".$i."1"]=$heightx;
			$content["week".$i."2"]=$heighty;
			
			//echo "H:".$height." - ".$heightx." - ".$heighty."<br/>";
			if($media[$dia_de_la_semana]<($x+$y))
				$content["margin".$i.$i.""]=$techo-$height0+20;
			else
				$content["margin".$i.$i.""]=$techo-$content["week".$i."3"]-$height0;
				
				
			$content["value".$i."0"]=number_format(($x+$y) , 2 , ',' , '.' )."€";
			
			$content["value".$i."1"]=$value1."€";
			
			if($value2==0)
				$content["value".$i."2"]="";
			else
				$content["value".$i."2"]=$value2."€";
			
			
			
			$content['url0'.$i]="control.php?do=control&d=".$day[$i]."&w=".$week."&value=".$value1."&store=1";
			$content['url1'.$i]="control.php?do=control&d=".$day[$i]."&w=".$week."&value=".$value2."&store=2";
			
			$content["color02"]="background-color:#FFCC00;";
			$content["color01"]="background-color:#FF6600;";
			$content["color03"]="background-color:#FFF;";
			
			if($day[$i]==date('Y-m-d',time()))
				$content["color".$i.""]="background-color:#FF9900; font-weight:bold;";
			else
				$content["color".$i.""]="";
		}		
		
		
		
		//-----------------------------------
		
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
			if($fechaValor<20130106){
			$ticket=mysql_query("SELECT SUM(value) FROM `feed_control` WHERE `day`='".$day[$i]."' AND `id_week`='".$week."' ")or die(mysql_error());
			if(mysql_num_rows($ticket)==0){
				$x=0;
				$value=0;
			}else{
				while($dato=mysql_fetch_array($ticket)){
						$x=$dato['SUM(value)'];
						$value=$dato['SUM(value)'];
						
				}
			}
			}else{
			$rompe=explode('-',$day[$i]);
			$dia=$rompe[2]."-".$rompe[1]."-".$rompe[0];
			$ticket=mysql_query("SELECT SUM(quantity) FROM `feed_buyers` WHERE `date` LIKE '".$dia."%' ")or die(mysql_error());
			if(mysql_num_rows($ticket)==0){
				$x=0;
				$value=0;
			}else{
				while($dato=mysql_fetch_array($ticket)){
						$x=$dato['SUM(quantity)'];
						$value=$dato['SUM(quantity)'];
						
				}
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
			
			
			$color[0]="background-color:#e5e5e5;";
			$color[1]="background-color:#DDDDDD;";
			$color[2]="background-color:#e5e5e5;";
			$color[3]="background-color:#DDDDDD;";
			$color[4]="background-color:#e5e5e5;";
			$color[5]="background-color:#DDDDDD;";
			$color[6]="background-color:#e5e5e5;";
			
			$dia=0;
			
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
		
		/********************************************/
		// Barra objetivo semanal
		/********************************************/
		
		
		$content['wbar1'].='<div class="wgraph" style="height:'.$height[0].'px; margin-top:'.$top[0].'px; background-color:#'.$color[0].'; color:#FFF;">Acumulado<br/>'.number_format($objetivo , 2 , ',' , '.' ).'€</div>';
		
		
		$content['wbar2'].='<div class="wgraph" style="height:'.$height[1].'px; margin-top:'.$top[1].'px; background-color:#'.$color[1].';">Objetivo<br/>'.number_format($max , 2 , ',' , '.' ).'€'.$semana[1].'</div>';
		
		/********************************************/
		//echo $acumulado_tienda1." ".$acumulado_tienda2;
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
			
			
			$acumuladoMes=0;
			
			
			$acumuladoMes=0;
			
			if($fechaValor>20130106){
				$nuevotarget=mysql_query("
					SELECT SUM(quantity) FROM `feed_buyers` WHERE `date` LIKE '%-".$year2[1]."-".$year2['0']."%'") or die(mysql_error());
			
					while($dato=mysql_fetch_array($nuevotarget)){
						$acumuladoMes=$dato['SUM(quantity)'];
						}
			
						//echo $acumuladoMes;
			}else{
				$target=mysql_query("
					SELECT * FROM `feed_control` WHERE `day` LIKE '".$year2['0'].'-'.$year2['1']."%' "
				)or die(mysql_error());
				while($dato=mysql_fetch_array($target)){
						$acumuladoMes+=$dato['value'];
				}
				//echo $acumuladoMes;
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
			
		}
		
		else{
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
			$content['maxmes']='<a class="fancybox fancybox.iframe" href="control.php?do=target&m='.$month.'&d='.$day[6].'">No se ha definido un objetivo</a>';
			$max=40000;
		}	
		
		
		
		$target=mysql_query("SELECT * FROM `feed_control` WHERE `day` LIKE '".$year['0'].'-'.$year['1']."%' ")or die(mysql_error());
		
		$acumuladoMes=0;
		
		if($fechaValor>20130106){
				$nuevotarget=mysql_query("
					SELECT SUM(quantity) FROM `feed_buyers` WHERE `date` LIKE '%-".$year[1]."-".$year['0']."%'") or die(mysql_error());
			
					while($dato=mysql_fetch_array($nuevotarget)){
						$acumuladoMes=$dato['SUM(quantity)'];
						}
			
						//echo "a".$acumuladoMes;
			}else{
				$target=mysql_query("
					SELECT * FROM `feed_control` WHERE `day` LIKE '".$year2['0'].'-'.$year2['1']."%' "
				)or die(mysql_error());
				while($dato=mysql_fetch_array($target)){
						$acumuladoMes+=$dato['value'];
				}
				
				//echo $acumuladoMes;
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
		
		$valor=date('Ymd',time());
		
		if($valor<20130101){
		$content['dayafterday']=dayAfterDay($year[0],$month);
		}else{
		$content['dayafterday']=dayAfterDay_v2($year[0],$month);
		}
			$url="dayscontrol";
			$table=$API->templateAdmin($url,$content);
			$_SESSION['content']=$table;
			$API->printadmin();
		
				
	
}

/*********************************************************
	ADD COMPONENT 
**********************************************************/
function week_range($year,$week,$format) {
			
    $ts = strtotime($year."W".$week);
    //$start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
    
    return array(date($format, $ts),
    			 date($format, strtotime('+1 day', $ts)),
    			 date($format, strtotime('+2 day', $ts)),
    			 date($format, strtotime('+3 day', $ts)),
    			 date($format, strtotime('+4 day', $ts)),
    			 date($format, strtotime('+5 day', $ts)),
    			 date($format, strtotime('+6 day', $ts)));
                 
}
function days_weeks(){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="materialedit";

	$id_ticket=$_GET['id'];
	$ticket=mysql_query("SELECT * FROM `feed_ticket`")or die(mysql_error());
	
	
	
	$total=0;
	$totalcoste=0;
	$totalbeneficio=0;
	
	$yactual=date("Y",time());
	
	$c=0;

	for ($y=2012;$y<$yactual+1;$y++){
		if($y==$yactual)
			$actual=date("W",time());
		else
			$actual=gmdate("W", strtotime("30 December ".$y));
		
	for($f=1;$f<$actual+1;$f++){
		if($f<10) $d="0".$f;
		else $d=$f;
		$day=week_range($y,$d,"Y-m-d");
		
		$sum=0;
		for($i=0;$i<7;$i++){
			
			$valor_day=explode('-',$day[$i]);
			$valor=$valor_day[0].$valor_day[1].$valor_day[2];
			
			$value=0;
			if($valor<20130105){
				$total_dia=mysql_query("
						SELECT value FROM `feed_control` 
						WHERE `day` = '".$day[$i]."'"
						) or die(mysql_error());
				while($dato=mysql_fetch_array($total_dia)){
					$value+=$dato['value'];
				}
			}else{
			
				$total_dia=mysql_query("
						SELECT SUM(quantity) FROM `feed_buyers` 
						WHERE `date2` = '".$day[$i]."'"
						) or die(mysql_error());
				
				while($dato=mysql_fetch_array($total_dia)){
					$value=$dato['SUM(quantity)'];
				}
				
			}
		
			$sum+=$value;
			
			
		}
		if($sum!=0){
			$cantidad_diaria[$c]=$sum;
			$cantidad_dia[$c]=$day[3];
			$c++;
		}
				
	}
	}
		$max=0;
		for($i=0;$i<count($cantidad_diaria);$i++){
			if ($max<$cantidad_diaria[$i])$max=$cantidad_diaria[$i];
		}
		
		$content['width']=0;
		$mesprevio=1;
		
		$color["01"]="#FFCC00";
		$color["02"]="#FFFF00";
		$color["03"]="#FFCC66";
		$color["04"]="#FFFF33";
		$color["05"]="#FFCC33";
		$color["06"]="#FFCC99";
		$color["07"]="#FFCC00";
		$color["08"]="#FFFF00";
		$color["09"]="#FFCC66";
		$color["10"]="#FFFF33";
		$color["11"]="#FFCC33";
		$color["12"]="#FFCC99";
		$nmes["01"]="Enero";
		$nmes["02"]="Febrero";
		$nmes["03"]="Marzo";
		$nmes["04"]="Abril";
		$nmes["05"]="Mayo";
		$nmes["06"]="Junio";
		$nmes["07"]="Julio";
		$nmes["08"]="Agosto";
		$nmes["09"]="Septiembre";
		$nmes["10"]="Octubre";
		$nmes["11"]="Noviembre";
		$nmes["12"]="Diciembre";
		for($i=0;$i<count($cantidad_diaria);$i++){
			$dia=explode('-',$cantidad_dia[$i]);
			
			$mes=$dia[1];
			$ano=$dia[0];
			if($mes!=$mesprevio){
				$mesprevio=$mes;
				
			}
			
			
			$content['content'].='<div style="height:200px; width:75px; float:left; margin:0px 1px; text-align:center;">
								 <div class="hgraph2" style="height:'.(($cantidad_diaria[$i]/$max)*200).'px; margin-top:
								 '.(200-(($cantidad_diaria[$i]/$max)*200)).'px; background-color:'.$color[$mes].';">
								 '.number_format($cantidad_diaria[$i],2,',','.').'€<br/>'.$nmes[$mes].'<br/>'.$ano.'
								 </div></div>';
			$content['width']+=77;
		}
		
		
		
	
	$url="daysweeks";
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

**********************************************************/
function days_delete($id){
	$API= new API();
	mysql_query("UPDATE `feed_day` SET `status` = '1', `id_modified_user`='".$_SESSION['login_id']."', `modified` = '".time()."'  WHERE  `id`='".$_GET['id']."'");
	$API->goto("?go=days&id_item=".$_GET['id_item']);
}

?>