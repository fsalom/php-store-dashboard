<?php
class DB{

	var $conection;
	var $server;
	var $username;
	var $password;
	var $database;
	
	function DB($server,$username,$password,$database){		
		$this->server=$server;
		$this->username=$username;
		$this->password=$password;
		$this->database=$database;		
	}
	
	function open(){
		$this->conection = mysql_connect($this->server,
										 $this->username,
										 $this->password
										 );
		mysql_select_db ($this->database, $this->conection);
	}
	
	function close(){
		mysql_close($this->conection);
	}
	
	
	function select ($db,$arg){
			$DB->open();
			$query=mysql_query("SELECT * FROM `".$db."`".$arg[0])or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
			
			}
			$DB->close();
	
	}
	
}
?>
