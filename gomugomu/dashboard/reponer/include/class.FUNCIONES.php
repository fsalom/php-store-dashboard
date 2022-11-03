<?php
class FUNCIONES {
	private $link_DB;
	
	private $support_season;
    
    public function connectMYSQL (){	    
    	$conexion = mysql_connect(_MYSQL_SERVER,_MYSQL_USERNAME,_MYSQL_PASSWORD);
		if (!$conexion)
			die('Something went wrong while connecting to MYSQL: '.mysql_error());
		mysql_select_db(_MYSQL_BD, $conexion); 
    }
    
    public function closeMYSQL (){
    	mysql_close($this->link_DB);
    }
    
    public function connectMSSQL (){
    	$link = mssql_connect(_MSSQL_SERVER, _MSSQL_USERNAME, _MSSQL_PASSWORD);
    	$this->link_DB = $link;
    	if (!$link)
    		die(_MSSQL_ERROR.mssql_get_last_message());
    	$selected = mssql_select_db( _MSSQL_DB, $link)
    	or die(_MSSQL_ERROR_DB." ".$myDB ." ".mssql_get_last_message() );
    }
    
    public function closeMSSQL (){
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
    		default:  $return	=	"Artículo devuelto";break;
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
}
?>
