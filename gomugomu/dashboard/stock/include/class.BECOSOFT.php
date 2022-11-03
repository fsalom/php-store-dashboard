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
    
    public function searchArticle($text){    	
    	$this->connect ();
    	$query = "SELECT m.Omschrijving, i.MatrixH, i.MatrixV, l.stock, m.Referentienummer, m.Seizoen
					FROM artikelmatrix m, artikelinfo i, artikellocal l 
					WHERE m.artikelmatrixID = i.artikelmatrixID 
					AND i.artikelnummer=l.artikelnummer 
					AND l.stock>0 
					AND m.Omschrijving LIKE '%".$text."%' 
					ORDER BY m.Seizoen, m.Referentienummer, i.MatrixV,
                    CASE i.MatrixH
                        WHEN '2XS' THEN 1
                        WHEN 'XS' THEN 2
                        WHEN 'S' THEN 3
                        WHEN 'M' THEN 4
                        WHEN 'L' THEN 5
                        WHEN 'XL' THEN 6
                        WHEN '2XL' THEN 7
                        WHEN '3XL' THEN 8
                        WHEN 'OS' THEN 0
                        WHEN ISNUMERIC(i.MatrixH) THEN CONVERT(INT, i.MatrixH)
                        ELSE 9
                    END ASC
					
					--AND i.Bestelnummer = '".$text."'
					--ORDER BY m.Seizoen ASC";
    	$result = mssql_query($query) or die(mssql_get_last_message());
    	$i=0;	    	
    	while($row = mssql_fetch_array($result)){ 	    		
    		$item[$i] = new ITEM(    				
						    		$row["Omschrijving"],   		    		    							    		
						    		$row["MatrixV"],
									$row["MatrixH"],
									$row['Referentienummer'],
									$row["Seizoen"],
									$row["stock"]									
			    					);	
    		$i++;
    	}    	
    	$this->close();
    	return $item;
    }    
      

}
?>