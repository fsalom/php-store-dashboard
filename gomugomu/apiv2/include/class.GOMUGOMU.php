<?php
class GOMUGOMU extends FUNCIONES  {
	public function getTicketClient($id_client){
		$this->connectMYSQL();
		$query = "SELECT id_ticket FROM dash_ticket_buyer WHERE id_client = ".$id_client;
		$result = mysql_query($query) or die(mysql_get_last_message());
		$i=0;
		while($row = mysql_fetch_array($result)){
			$item[$i] 				=  $this->getTicketDetails($row['id_ticket'],1);
			$item['id_ticket'][$i]  =  $row['id_ticket'];
			$item['total'][$i] 		=  $this->getTotalTicket($row['id_ticket']);			
			$i++;
		}
		$item['count']=$i;
		$this->closeMYSQL();
		return $item;
	}
	
	public function getTotalTicket($id_ticket){
		$this->connectMYSQL();		
						
		$query = "SELECT SUM(quantity)
								FROM `dash_payment`
								WHERE `id_ticket` = '".$id_ticket."'								
								AND type!='Waardebon'";
		$result = mysql_query($query) or die(mysql_get_last_message());
		
		$row = mysql_fetch_array($result);
						
		$this->closeMYSQL();
		return number_format($row['SUM(quantity)'],2,',','.');
	}
	
    public function getTicketDetails($id,$MYSQL=null){
    	if($MYSQL==null)    	
    		$this->connectMYSQL();
    	$query = "SELECT * FROM dash_ticket WHERE id_ticket = ".$id;
    	$result = mysql_query($query) or die(mysql_get_last_message());
    	$i=0;	
    	while($row = mysql_fetch_array($result)){
    		
    		
    		
    		$item[$i] = new ARTICLE(
						    		$array = array(						    										    			
									    "name" => $row["description"],
						    			"colour" => $row['colour'],
						    			"size" => $row['size'],									    
						    			"reference" => $row['referencenr'],
						    			"season" => $row['season']
									), //name, colour, size, reference, season   		    		    	
						    		'',   		
						    		str_replace(array("Superdry", "SUPERDRY"),'',$row["description"]),
									$row["subtotal"]*$row["items"],
									$row['subtotalOriginal']*$row["items"],
									$row["items"],
									$this->getMargenBruto($row['subtotalOriginal'],$row["subtotal"],$row['season']),  //money, percentage 			   
									round((($row["subtotalOriginal"]-$row["subtotal"])/$row['subtotalOriginal'])*100,0)
			    					);		 
    		    		
    		$i++;	    		    		    
    		$item['date']=$row['date'];
    	}
    	$item['count']=$i;
    	
    	if($MYSQL==null)
    	$this->closeMYSQL();
    	return $item;
    }    
    
    
    public function getMargenBruto($precioCompleto,$precio,$season){
    	$seasons = array("SS15", "AW15");
    	if (!in_array($season, $seasons))$mymargin=_MARGEN;
    	else $mymargin=_MARGEN_ESPECIAL;
    	$money=round(($precio-($precio-($precio/_IVA))-($precioCompleto/$mymargin)-($precio*_ROYALTY)),2);
    	$percentage=round((($money/$precio))*100,2);
    	$margen = new MARGIN($money,$percentage);
    	return $margen;
    }
    
    
    public function getTicketTotal($id) {
    	$this->connectMYSQL ();
    	$query 		= "SELECT SUM(Bedrag) FROM VerkoopBetaling WHERE Factuurnummer = '".$id."'";
    	$result 	= mssql_query($query) or die(mssql_get_last_message());    	
    	$row = mssql_fetch_array($result);  	
    	$this->closeMYSQL();
    	return number_format($row[0],2,',','.');
    }
    
    public function getInfoDay($day){
    	$this->connectMYSQL ();
    	$sDate=$this->getDate($day);
    	$query = 'SELECT * FROM VerkoopDetail D,Verkoop T  WHERE
    				D.Factuurnummer = T.Factuurnummer
					AND	  (DATEPART(yy, T.Datum) = "'.$sDate['Y'].'"
					AND    DATEPART(mm, T.Datum) = "'.$sDate['M'].'"
					AND    DATEPART(dd, T.Datum) = "'.$sDate['D'].'")';
    	$result = mssql_query($query) or die(_MSSQL_ERROR.mssql_get_last());
    	while($row = mssql_fetch_array($result)){
    		$originalPrice += ($row['origverkoopprijs']*$row["Aantal"]);
    		$items		   += $row['Aantal'];
    	}
    	
    	$query = 'SELECT COUNT(Factuurnummer) FROM Verkoop  WHERE    				
						  (DATEPART(yy, Datum) = "'.$sDate['Y'].'"
					AND    DATEPART(mm, Datum) = "'.$sDate['M'].'"
					AND    DATEPART(dd, Datum) = "'.$sDate['D'].'")';
		$result	 = mssql_query($query) or die(_MSSQL_ERROR.mssql_get_last());
    	$row	 = mssql_fetch_array($result);
    	$ntickets=$row[0];
    	$total	 =$this->getTotalDay($day);
    	$today = new DAY(
    					$sDate['D'].'-'.$sDate['M'].'-'.$sDate['Y'],
						$total,
    					$originalPrice,
    					$ntickets,
    					$items,
    					$items/$ntickets,
    					($total/$ntickets),
    					round(($total-($total-($total/_IVA))-($originalPrice/_MARGEN)-($total*_ROYALTY)),2),
    					round((($originalPrice-$total)/$originalPrice)*100,0)
    				   );
    	$this->closeMYSQL();    	    	    	
    	return $today;
    }    
    
    public function getTotalDay($day) {
    	$this->connectMYSQL();
    	$sDate=$this->getDate($day);
		$query = 'SELECT SUM(Bedrag) FROM VerkoopBetaling WHERE  
						  (DATEPART(yy, Datum) = "'.$sDate['Y'].'"
					AND    DATEPART(mm, Datum) = "'.$sDate['M'].'"
					AND    DATEPART(dd, Datum) = "'.$sDate['D'].'")';
		$result = mssql_query($query) or die(_MSSQL_ERROR.mssql_get_last());				    
    	$row = mssql_fetch_array($result);    	    	
    	$this->closeMYSQL();
    	return $row[0];
    }
    
    public function getTicketPayment($id) {
    	$this->connectMYSQL();
    	$query 		= "SELECT * FROM VerkoopBetaling WHERE Factuurnummer = '".$id."'";
    	$result 	= mssql_query($query) or die(mssql_get_last_message());
    	$i=0;
    	while($row =mssql_fetch_array($result)){
    		$payment[$i]['type']	=	$row['Betalingswijze'];
    		$payment[$i]['amount']	=	$row['Bedrag'];
    		$i++;
    	}
    	$payment['count'] = $i;
    	$this->closeMYSQL();
    	return $payment;
    }    
    
    public function getTicketsDay($day) {    	
		$this->connectMYSQL();
		$sDate=$this->getDate($day);
		$query = 'SELECT * FROM Verkoop WHERE  
						  (DATEPART(yy, Datum) = "'.$sDate['Y'].'"
					AND    DATEPART(mm, Datum) = "'.$sDate['M'].'"
					AND    DATEPART(dd, Datum) = "'.$sDate['D'].'")';
		$result = mssql_query($query) or die(_MSSQL_ERROR.mssql_get_last_message());		
		$i=0;
		$tickets = array();
		while($row = mssql_fetch_array($result)){				
			$tickets['id_ticket'][$i]			= $row["Factuurnummer"];
			$tickets['date'][$i]				= date("H:i", strtotime($row["Datum"]));			
			$i++;
		}
		$this->closeMYSQL();
        return $tickets;
    }

	
	public function getSameDay($day){		
		$this->connectMYSQL ();
		if($day=="")$day=date("d-m-Y",time());
		$week_number = date("W",strtotime($day));
		$year 		 = date("Y",strtotime($day));	
		$day_of_week = date("N",strtotime($day));
		$year_ini 	 = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).'0'));
		$year_end 	 = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).'7'));
		$year_day 	 = date("Y",strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).$day_of_week));
			
		if($week_number!=1 && $year_end==$year_ini) $year-=1;

		$day2 = strtotime($year."W".str_pad($week_number,2,0,STR_PAD_LEFT).$day_of_week);
		$fecha= date("Y-m-d",$day2);
		$query=mysql_query("SELECT SUM(quantity) FROM `dash_payment` WHERE `date` LIKE '".$fecha."%' and type<>'Waardebon'") 
			or die(mysql_error());
		$tickets=mysql_query("SELECT * FROM `dash_ticket` WHERE `date` LIKE '".$fecha."%' GROUP BY `id_ticket`")
			or die(mysql_error());
		$prendas=mysql_query("SELECT SUM(items),SUM(subtotalOriginal) FROM `dash_ticket` WHERE `date` LIKE '".$fecha."%'") 
			or die(mysql_error());
		
		$nprendas=mysql_fetch_array($prendas);
		$dato=mysql_fetch_array($query);
		$today = new DAY(
    				$day,
					$dato['SUM(quantity)'],
    				$nprendas['SUM(subtotalOriginal)'],
    				mysql_num_rows($tickets),
    				$nprendas['SUM(items)'],
    				$nprendas['SUM(items)']/mysql_num_rows($tickets),
					($dato['SUM(quantity)']/mysql_num_rows($tickets)),
    				round(($dato['SUM(quantity)']-($dato['SUM(quantity)']-($dato['SUM(quantity)']/_IVA))-($nprendas['SUM(subtotalOriginal)']/_MARGEN)-($dato['SUM(quantity)']*_ROYALTY)),2),
    				round((($nprendas['SUM(subtotalOriginal)']-$dato['SUM(quantity)'])/$nprendas['SUM(subtotalOriginal)'])*100,0)
    			   );
		$this->closeMYSQL();
		return $today;
	}

	
	function get_data_week($fecha, $today ,$year = ""){
		if($fecha=="") $fecha = date("d-m-Y",time());
		if($year=="")  $year  = date('Y',strtotime($fecha));		
		$week = date('W',strtotime($fecha));
		if($week+1>53) $week = 1;
	
		$check_day 			 = date('m-d',strtotime($year . 'W' . str_pad($week, 2, '0', STR_PAD_LEFT)));
		$retrocedemos_inicio = 0;
		$week_begin			 = $week;
		$holiday=array("01-06","12-25","05-01");
		if(in_array($check_day, $holiday)){
			$week_begin-=1;
			if($week_begin<10 && $week_begin[0]!="0")$week_begin='0'.$week_begin;
			$retrocedemos_inicio=1;
			$date = date('Y-m-d', strtotime($year."W".$week_begin."7"));
		}else{
			$date = date('Y-m-d',strtotime($year . 'W' . str_pad($week_begin, 2, '0', STR_PAD_LEFT)));
		}

		$check_day = date('m-d',strtotime($year . 'W' . str_pad($week+1, 2, '0', STR_PAD_LEFT)));
	
		//Colocamos los dias que son fiesta seguro en caso de que caigan en lunes buscamos el martes		 
		if(in_array($check_day, $holiday)){
			$week_next = $week + 1;
			if($week_next<10) $week_next='0'.$week_next;
			$date_next = date('Y-m-d', strtotime($year."W".$week_next."2"));
		}else{
			$date_next = date('Y-m-d', strtotime($year . 'W' . str_pad($week+1, 2, '0', STR_PAD_LEFT)));
		}
	
		$this->connectMYSQL ();
		$queryORDER = "DESC"; if($retrocedemos_inicio==0) $queryORDER = "ASC";
		
		$query = mysql_query("SELECT id_ticket FROM `dash_ticket` WHERE `date` LIKE '".$date."%' ORDER BY `id` ".$queryORDER." LIMIT 1") or die(mysql_error());		
		$data  = mysql_fetch_array($query);
	
		$id_ticket = $data['id_ticket'];
		if($id_ticket==0)$id_ticket=100000000;

		$mon=date('Y-m-d 00:00:01',strtotime($year.'-W'.$week.'-1'));
		$sun=date('Y-m-d 23:59:59',strtotime($year.'-W'.$week.'-7'));
	
		$query = mysql_query("SELECT id_ticket FROM `dash_ticket` WHERE `date` LIKE '".$date_next."%' ORDER BY `id` ASC LIMIT 1") or die(mysql_error());
		$data  = mysql_fetch_array($query);
	
		$id_ticket_next=$data['id_ticket'].'<br/>';
		if($id_ticket_next==0)$id_ticket_next=100000000;
			
		$week_original=0;		
			
		$query = mysql_query("SELECT subtotal, subtotalOriginal, items FROM `dash_ticket` WHERE `date` between '".$mon."%' AND '".$sun."' AND `description`!='Waardebon'") or die(mysql_error());			
		while($data=mysql_fetch_array($query)){
			$week_original+=$data['subtotalOriginal']*$data['items'];			
		}
						
		$query = mysql_query("SELECT SUM(subtotal), SUM(subtotalOriginal), SUM(items), COUNT(DISTINCT id_ticket) FROM `dash_ticket` WHERE `id_ticket` >= '".$id_ticket."' AND `id_ticket`<'".$id_ticket_next."' AND `description`!='Waardebon'") or die(mysql_error());
		$data  = mysql_fetch_array($query);
		$week_pcs	  = $data['SUM(items)'];		
		$week_tickets = $data['COUNT(DISTINCT id_ticket)'];

		$query_payment  = mysql_query("SELECT SUM(quantity) FROM `dash_payment` WHERE `id_ticket` >= '".$id_ticket."' AND `id_ticket`<'".$id_ticket_next."' AND type!='Waardebon'") or die(mysql_error());
		$data_payment   = mysql_fetch_array($query_payment);
		$week_total	 	= $data_payment['SUM(quantity)'];					
							
		$week = new DAY(
				$day,
				$week_total + $today->total,
				$week_original,
				$week_tickets + $today->nTickets,
				$week_pcs + $today->nArticles,
				($week_pcs/$week_tickets) + $today->perTicket,
				($week_total/$week_tickets) + $today->averageTickets,
				round(($week_total-($week_total-($week_total/_IVA))-($week_original/_MARGEN)-($week_total*_ROYALTY)),2) +  $today->margin,
				round((($week_original-$week_total)/$week_original)*100,0)
		);
		
		return $week;
	}
	
	public function getGenderStats(){
		$query = mysql_query("SELECT SUM(items) FROM `dash_ticket` WHERE `referencenr` LIKE 'M%' AND `id_ticket` >= '".$id_ticket."' AND `id_ticket`<'".$id_ticket_next."'") or die(mysql_error());
		$data  = mysql_fetch_array($query);
		$value['M']=$data['SUM(items)']+$today['M'];
		
		$query = mysql_query("SELECT SUM(items) FROM `dash_ticket` WHERE `referencenr` LIKE 'G%' AND `id_ticket` >= '".$id_ticket."' AND `id_ticket`<'".$id_ticket_next."'") or die(mysql_error());
		$data  = mysql_fetch_array($query);
		$value['G']=$data['SUM(items)']+$today['G'];
		$value['U']=$value['pcs']-$value['M']-$value['G'];
		
		$query=mysql_query("SELECT COUNT(country) FROM `dash_buyer` WHERE `country` = 'ES' AND `id_ticket` >= '".$id_ticket."' AND `id_ticket`<'".$id_ticket_next."'") or die(mysql_error());
		$data=mysql_fetch_array($query);
		$value['ES']=$data['COUNT(country)']+$today['ES'];
		
		$query=mysql_query("SELECT COUNT(country) FROM `dash_buyer` WHERE `country` <> 'ES' AND `id_ticket` >= '".$id_ticket."' AND `id_ticket`<'".$id_ticket_next."'") or die(mysql_error());
		$data=mysql_fetch_array($query);
		$value['REST']=$data['COUNT(country)']+$today['REST'];
		
		$value['country']=get_stats_country($value['ES'],$value['REST']);
		$value['stats']=get_stats_gender($value['M'],$value['G'],$value['U']);
	}
	
}
?>
