<?php
class SHARE {
	public $conection;
    
    public function connectMYSQL (){	    
    	$this->conection = new mysqli(_MYSQL_SERVER,_MYSQL_USERNAME,_MYSQL_PASSWORD,_MYSQL_BD);
    	if ($this->conection->connect_errno) {
    		printf("Falló la conexión: %s\n", $this->conection->connect_error);
    		exit();
    	}    	
    }
    
    public function closeMYSQL (){
    	$this->conection->close();
    }
    
    public function query ($query){
    	return $this->conection->query($query);
    }
    
    public function num_rows ($result){
    	return mysqli_num_rows($result);
    }
    
    public function error (){
    	return mysqli_error($this->conection);
    	//return $this->conection->error;
    }
    
}
?>