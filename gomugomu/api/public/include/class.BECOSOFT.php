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
    	//$item['count'] = $i;
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

    public function margen_bruto($precioCompleto,$precio){
    //echo $precioCompleto.' '.$precio.'<br/>';
        if($precio > 0){
            $margen['money']=round(($precio-($precio-($precio/1.21))-($precioCompleto/2.94)-($precio*0.03)),2);
            $margen['percentage']=round((($margen['money']/$precio))*100,2);
        }else{
            $margen['money']=0.0;
            $margen['percentage']=0.0;
        }
        
        return $margen;
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

/*
class ITEM {
    public $name;
    public $colour;
    public $size;
    public $reference;
    public $season;
    public $stock;
    public function __construct($name, $colour, $size, $reference, $season,$stock)
    {
        $this->name      = $name;
        $this->colour    = $colour;
        $this->size      = $size;
        $this->reference = $reference;
        $this->season    = $season;
        $this->stock     = $stock; // Added 20160113
    }
}
*/
    public function searchEAN($ean){
        $this->connect ();
        //m.Omschrijving, i.MatrixH, i.MatrixV, l.stock, i.Bestelnummer, m.Seizoen, i.EANBarcode, 
        $query = "SELECT m.Referentienummer, m.Verkoopprijs
                    FROM artikelmatrix m, artikelinfo i, artikellocal l 
                    WHERE m.artikelmatrixID = i.artikelmatrixID 
                    AND i.artikelnummer=l.artikelnummer 
                    AND l.stock>0 
                    AND i.EANBarcode like '%".$ean."%'";

        

        $result = mssql_query($query) or die(_MSSQL_ERROR.mssql_get_last_message());
        while($rowDetail =mssql_fetch_array($result)){
            $reference  = $rowDetail["Referentienummer"];
            $price      = $rowDetail["Verkoopprijs"];
        }

        $query = "SELECT m.Omschrijving, i.MatrixH, i.MatrixV, l.stock, i.Bestelnummer, m.Seizoen, m.Referentienummer
                    FROM artikelmatrix m, artikelinfo i, artikellocal l 
                    WHERE m.artikelmatrixID = i.artikelmatrixID 
                    AND i.artikelnummer=l.artikelnummer 
                    AND l.stock>0 
                    AND m.Referentienummer LIKE '%".$reference."%'
                    ORDER BY i.MatrixV";
        $stock = array();         
        $items = array();        
        $result = mssql_query($query) or die(_MSSQL_ERROR.mssql_get_last_message());
        while($rowDetail =mssql_fetch_array($result)){
            $item               = array();  
            $name               = $rowDetail["Omschrijving"];
            $item["name"]       = $rowDetail["Omschrijving"];
            $item["colour"]     = $rowDetail["MatrixV"];
            $item["size"]       = $rowDetail["MatrixH"];
            $item["reference"]  = $reference;
            $item["season"]     = $rowDetail["Seizoen"];
            $item["stock"]      = $rowDetail["stock"];
            $items[] = $item;
        } 
        $stock["name"]          = $name;
        $stock["reference"]     = $reference;
        $stock["price"]         = $price;
        $stock["items"]         = $items;

        $this->close();
        return $stock;
        
    }
    
    public function getTicketsDay($day) {    	
		$this->connect ();
		$sDate=$this->getDate($day);
        $daynum = explode("-",$day);
        $daytotal = $daynum[2]+$daynum[1]+$daynum[0];
        if($daytotal<=date("Ymd")){

    		$query = 'SELECT * FROM Verkoop WHERE  
    						  (DATEPART(yy, Datum) = "'.$sDate['Y'].'"
    					AND    DATEPART(mm, Datum) = "'.$sDate['M'].'"
    					AND    DATEPART(dd, Datum) = "'.$sDate['D'].'")';
    		$result = mssql_query($query) or die(_MSSQL_ERROR.mssql_get_last_message());		
    		$i=0;
    		$tickets            = array();
            $total_completo     = 0;
            $num_items          = 0;
            $num_tickets        = 0;
            $num_items_tickets  = 0;
            $precioCompleto     = 0;
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
                $info = array();
                $info['id_ticket']=$row["Factuurnummer"];
                $info['date'] = date("H:i", strtotime($row["Datum"]));
                $num_tickets += 1;

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
             
                
                $c=0;
                $pcs_this_tk=0;
                while($rowDetail =mssql_fetch_array($resultDetail)){
                    $num_items += 1;
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
                  
                  
                    $margen=$this->margen_bruto($rowDetail['origverkoopprijs'],$rowDetail["Verkoopprijs"]);
                  
                    $pcs+=$rowDetail["Aantal"];
                    $pcs_this_tk+=$rowDetail["Aantal"];
                  
                    $precioCompleto+=$rowDetail['origverkoopprijs']*$rowDetail["Aantal"];
                  
                    $precio=$rowDetail["Verkoopprijs"]*$rowDetail["Aantal"];
                    $precioDeVenta=$rowDetail["origverkoopprijs"];
                 
                    $replace = array("Superdry", "SUPERDRY");
                    $item=str_replace($replace,'',$rowDetail["Omschrijving"]);
                  
                    $data=$this->getReference($reference);

                    
                    $info['items'][$c]['percentage']=round((($rowDetail["origverkoopprijs"]-$rowDetail["Verkoopprijs"])/$rowDetail['origverkoopprijs'])*100,0);
                    $info['items'][$c]['margen']=$margen['money'];
                    $info['items'][$c]['margen_per']=$margen['percentage'];
                    $info['items'][$c]['returned']=$devuelto;
                    $info['items'][$c]['name']=$data->name;
                    $info['items'][$c]['colour']=$data->colour;
                    $info['items'][$c]['size']=$data->size;
                    $info['items'][$c]['reference']=$data->reference;
                    $info['items'][$c]['season']=$data->season;
                    //$info['items'][$i][$c]['reference'] = $item['reference'];
                    $info['items'][$c]['subtotalOriginal']=$precio;
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
        
            
                $total_completo += $total;
                $info['total']=$total;
                
                $i++;
                $tickets[] = $info;
            }
            $day = array();
            $day["total"] = $total_completo;
            $day["num_items"] = $num_items;
            $day["num_tickets"] = $num_tickets;
            if($num_tickets>0){
                $day["num_items_tickets"] = ($num_items/$num_tickets);
                $day["per_ticket"] = ($total_completo/$num_tickets);
            }
            if($precioCompleto>0){
                $day["percentage"] = (1 - ($total_completo/$precioCompleto))*100;
                $day["margin"] = round(($total_completo-($total_completo-($total_completo/1.21))-($precioCompleto/2.94)-($total_completo*0.03)),2);
            }
            $day["tickets"] = $tickets;
        }else{
            $day = array();
        }
          /*			
			$tickets['id_ticket'][$i]			= $row["Factuurnummer"];
			$tickets['date'][$i]				= date("H:i", strtotime($row["Datum"]));
            echo $row;			
			$i++;
            */
            
		
		$this->close();
        return $day;
    }

}
?>
