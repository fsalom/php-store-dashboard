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
    
    public function getReference($reference){     	
    	$query 			= "select * from artikelmatrix m inner join artikelinfo i on i.artikelmatrixid = m.artikelmatrixid where i.artikelnummer = '".$reference."'";    
    	$result 		= mssql_query($query)or die(mssql_get_last_message());
    	$row 			= mssql_fetch_array($result);   
    	$support_season =  $row['Seizoen'];
    	$data			= new ITEM($row[2],$row['MatrixV'],$row['MatrixH'], $row['Referentienummer'], $row['Seizoen']); 	    	   	
    	return $data;
    }
    

    public function top($number_of_items, $month){
    	if ($month == ''){
    		$month = date("Y-m-01", time());
    	}
    	$query = "SELECT TOP $number_of_items m.Referentienummer, COUNT(m.Referentienummer) as number, m.Omschrijving
					FROM VerkoopDetail t
					INNER JOIN artikelinfo a
					ON t.Artikelnummer = a.Artikelnummer
					INNER JOIN Artikelmatrix m
					ON a.ArtikelMatrixID = m.ArtikelMatrixID
					WHERE t.TimeStamp > '".$month."'
					Group BY m.Referentienummer, m.Omschrijving
					ORDER by number DESC";
    	$result = mssql_query($query) or die(mssql_get_last_message());
    	$i=0;	  
    	while($row = mssql_fetch_array($result)){    	
    		$item[$i] = new ITEM(    				
						    		$row["Omschrijving"],   		    		    							    		
						    		'',
									'',
									$row['Referentienummer'],
									'',
									$row["number"]									
			    					);	
    		$i++;
    	}    	
    	
    	return $item;
    }

    public function searchArticle($text){  
    	$$querytest = "SELECT 1";
    	$resulttest = mssql_query($query) or die(mssql_get_last_message());
    	$query = "SELECT m.Referentienummer, m.Omschrijving, i.MatrixH, i.MatrixV, l.stock, i.Bestelnummer, m.Seizoen
					FROM artikelmatrix m, artikelinfo i, artikellocal l 
					WHERE m.artikelmatrixID = i.artikelmatrixID 
					AND i.artikelnummer=l.artikelnummer 
					AND l.stock>0 
					--AND (m.Omschrijving LIKE '%".$text."%' 
					AND m.Referentienummer = '".$text."'
					ORDER BY m.Seizoen ASC";
    	$result = mssql_query($query) or die(mssql_get_last_message());
    	$i=0;	  
    	while($row = mssql_fetch_array($result)){ 	    		
    		$item[$i] = new ITEM(    				
						    		$row["Omschrijving"],   		    		    							    		
						    		$row["MatrixV"],
									$row["MatrixH"],
									$row['Bestelnummer'],
									$row["Seizoen"],
									$row["stock"]									
			    					);	
    		$i++;
    	}    	
    	
    	return $item;
    }       
     
 	public function getStock($text, $color){  
    	$$querytest = "SELECT 1";
    	$resulttest = mssql_query($query) or die(mssql_get_last_message());
    	$query = "SELECT m.Referentienummer, m.Omschrijving, i.MatrixH, i.MatrixV, l.stock, i.Bestelnummer, m.Seizoen
					FROM artikelmatrix m, artikelinfo i, artikellocal l 
					WHERE m.artikelmatrixID = i.artikelmatrixID AND i.MatrixV = '".$color."'
					AND i.artikelnummer=l.artikelnummer 
					AND l.stock>0 
					--AND (m.Omschrijving LIKE '%".$text."%' 
					AND m.Referentienummer = '".$text."'
					ORDER BY m.Seizoen ASC";
    	$result = mssql_query($query) or die(mssql_get_last_message());
    	$i=0;	  
    	while($row = mssql_fetch_array($result)){ 	    		
    		$item[$i] = new ITEM(    				
						    		$row["Omschrijving"],   		    		    							    		
						    		$row["MatrixV"],
									$row["MatrixH"],
									$row['Bestelnummer'],
									$row["Seizoen"],
									$row["stock"]									
			    					);	
    		$i++;
    	}    	
    	
    	return $item;
    }       


    public function searchReference($reference){    	
    	$this->connect ();
    	$query = "SELECT m.Omschrijving, i.MatrixH, i.MatrixV, l.stock, i.Bestelnummer, m.Seizoen, i.artikelnummer 
					FROM artikelmatrix m, artikelinfo i, artikellocal l 
					WHERE m.artikelmatrixID = i.artikelmatrixID 
					AND i.artikelnummer=l.artikelnummer 
					AND l.stock>0 
					AND i.Bestelnummer LIKE '%".$reference."%'
					ORDER BY m.Seizoen ASC";
    	$result = mssql_query($query) or die(mssql_get_last_message());
    	$i=0;	    	
    	while($row = mssql_fetch_array($result)){ 	    	
    		$item[$i] = new ITEM(    				
						    		$row["Omschrijving"],   		    		    							    		
						    		$row["MatrixV"],
									$row["MatrixH"],
									$row['Bestelnummer'],
									$row["Seizoen"],
									$row["stock"]									
			    					);	
    		$i++;
    	}    	
    	//print_r($item);
    	$this->close();
    	return $item;
    }    

}
?>
