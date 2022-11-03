<?php
define("_MYSQL_ERROR_DB","Couldn't open database");
define("_MYSQL_ERROR","Something went wrong while connecting to MSSQL:");

define("_MYSQL_SERVER","localhost");
define("_MYSQL_USERNAME","dashboard");
define("_MYSQL_PASSWORD","osaka2011");
define("_MYSQL_BD","admin_dashboard");


class ANSWER {
	public $updated;
	public $answer;
	public function __construct($updated, $answer)
	{
		$this->updated 	 = $updated;
		$this->answer 	 = $answer;
	}
}

class FORM {		
	private $link_DB;

    public function connect (){	    
    	$conexion = mysql_connect(_MYSQL_SERVER,_MYSQL_USERNAME,_MYSQL_PASSWORD);
		if (!$conexion)
			die('Something went wrong while connecting to MYSQL: '.mysql_error());
		mysql_select_db(_MYSQL_BD, $conexion); 
    }
    
    public function close (){
    	mysql_close($this->link_DB);
    }

	public function get_answers($id_ticket){
		$this->connect();
		
		$answer	=  '<input type="hidden" name="ticket[]" value="'.$id_ticket.'">';
		$answer	.= '<select name="answer[]" class="selects">';
		$answer	.= '<option value="0" selected="selected">No seleccionado</option>';
		$result	 = mysql_query("SELECT * FROM `dash_ticket_buyer` WHERE id_ticket='".$id_ticket."'") or die (mysql_error());
		$data	 = mysql_fetch_array($result);
		$result	 = mysql_query("SELECT * FROM `dash_ticket_buyer_answer` ORDER BY answer ") or die (mysql_error());
		$updated = false;
		while($row=mysql_fetch_array($result)){
			if($data['id_answer']==$row['id']){
				$answer	.= '<option value="'.$row['id'].'" selected>'.$row['answer'].'</option>';
				$updated = true;
			}else{
				$answer .= '<option value="'.$row['id'].'">'.$row['answer'].'</option>';			
			}
		}
		$answer.='</select>';
		$_ANSWER = new ANSWER($updated,$answer);
		$this->close();
		
		return $_ANSWER;
	}
}


?>
