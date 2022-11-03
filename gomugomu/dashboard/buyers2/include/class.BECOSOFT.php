<?php
class BECOSOFT {
	private $link_DB;
	
	private $support_season;
    
    public function connect (){	    
    	$link = mssql_connect(_MSSQL_SERVER, _MSSQL_USERNAME, _MSSQL_PASSWORD);
    	$this->link_DB = $link; 
		if (!$link)
		    die(_MSSQL_ERROR.mssql_get_last_message());		
		$selected = mssql_select_db( _MSSQL_DB, $link) 
			or die(_MSSQL_ERROR_DB." ".$myDB ." ".mssql_get_last_message() ); 
    }
    
    public function close (){
    	mssql_close($this->link_DB);
    }

    public function getDate($myDate){
	    if($myDate==""){
			$date['Y']	=	date("Y",time());
			$date['M']	=	date("m",time());
			$date['D']	=	date("j",time());
		}else{
			$token=explode('-',$myDate);
			$date['Y']	=	$token[2];
			$date['M']	=	$token[1];
			$date['D']	=	$token[0];
		}
		return $date;
    }
        
    public function getNameReturn($id){
    	switch ($id){
    		case '1': $return	=	"Artículo devuelto - Fallo";break;
    		case '2': $return	=	"Artículo devuelto - Talla";break;
    		case '3': $return	=	"Artículo devuelto - Color";break;
    		case '4': $return	=	"Artículo devuelto - Insatisfecho";break;
    		case '5': $return	=	"Artículo devuelto - Otro";break;
    		default:  $return	=	"";break;
    	}
    	return $return;    	
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
    
    public function getReference($reference){     	
    	$query 			= "select * from artikelmatrix m inner join artikelinfo i on i.artikelmatrixid = m.artikelmatrixid where i.artikelnummer = '".$reference."'";    
    	$result 		= mssql_query($query)or die(mssql_get_last_message());
    	$row 			= mssql_fetch_array($result);   
    	$support_season =  $row['Seizoen'];
    	$data			= new ITEM($row[2],$row['MatrixV'],$row['MatrixH'], $row['Referentienummer'], $row['Seizoen']); 	    	   	
    	return $data;
    }
    
    public function getTicketDetails($id){    	
    	$this->connect ();
    	$query = "SELECT * FROM VerkoopDetail WHERE Factuurnummer = ".$id;
    	$result = mssql_query($query) or die(mssql_get_last_message());
    	$i=0;	    	
    	while($row = mssql_fetch_array($result)){ 	
    		
    		$item[$i] = new ARTICLE(
						    		$this->getReference($row['Artikelnummer']), //name, colour, size, reference, season   		    		    	
						    		$this->getNameReturn($row["RetourRedenID"]),   		
						    		str_replace(array("Superdry", "SUPERDRY"),'',$row["Omschrijving"]),
									$row["Verkoopprijs"]*$row["Aantal"],
									$row['origverkoopprijs']*$row["Aantal"],
									$row["Aantal"],
									$this->getMargenBruto($row['origverkoopprijs'],$row["Verkoopprijs"],$this->support_season),  //money, percentage 			   
									round((($row["origverkoopprijs"]-$row["Verkoopprijs"])/$row['origverkoopprijs'])*100,0)
			    					);		    		    		    		
    		$i++;    		
    	}
    	$item['count'] = $i;
    	$this->close();
    	return $item;
    }    
    
    public function getTicketTotal($id) {
    	$this->connect ();
    	$query 		= "SELECT SUM(Bedrag) FROM VerkoopBetaling WHERE Factuurnummer = '".$id."' AND Betalingswijze!='Waardebon'";
    	$result 	= mssql_query($query) or die(mssql_get_last_message());    	
    	$row = mssql_fetch_array($result);  	
    	$this->close();
    	return number_format($row[0],2,',','.');
    }
    
    public function getInfoDay($day){
    	$this->connect ();
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
    	$this->close();    	
    	//$money=round(($precio-($precio-($precio/_IVA))-($precioCompleto/$mymargin)-($precio*_ROYALTY)),2);    	    	
    	return $today;
    }    
    
    public function getTotalDay($day) {
    	$this->connect ();
    	$sDate=$this->getDate($day);
		$query = 'SELECT SUM(Bedrag) FROM VerkoopBetaling WHERE  
						  (DATEPART(yy, Datum) = "'.$sDate['Y'].'"
					AND    DATEPART(mm, Datum) = "'.$sDate['M'].'"
					AND    DATEPART(dd, Datum) = "'.$sDate['D'].'"
    				AND     Betalingswijze!="Waardebon")';
		$result = mssql_query($query) or die(_MSSQL_ERROR.mssql_get_last());				    
    	$row = mssql_fetch_array($result);    	
    	
    	$this->close();
    	return $row[0];
    }
    
    public function getTicketPayment($id) {
    	$this->connect ();
    	$query 		= "SELECT * FROM VerkoopBetaling WHERE Factuurnummer = '".$id."'";
    	$result 	= mssql_query($query) or die(mssql_get_last_message());
    	$i=0;
    	while($row =mssql_fetch_array($result)){
    		$payment[$i]['type']	=	$row['Betalingswijze'];
    		$payment[$i]['amount']	=	$row['Bedrag'];
    		$i++;
    	}
    	$payment['count'] = $i;
    	$this->close();
    	return $payment;
    }    
    
    public function getTicketsDay($day) {    	
		$this->connect ();
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
		$this->close();
        return $tickets;
    }

}
?>
