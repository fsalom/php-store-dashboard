<?php
class TARGET extends SHARE  {
	public function check($type, $field, $value){
		$this->connectMYSQL();
		switch ($type) {
			case 'd':
				$query = "SELECT * FROM `dash_target` WHERE day = '".date("d",time())."' AND month = '".date("m",time())."' AND year = '".date("Y",time())."'";
				break;
			case 'W':
				$query = "SELECT * FROM `dash_target` WHERE week = '".date("W",time())."' AND year = '".date("Y",time())."'";
				break;
			case 'm':
				$query = "SELECT * FROM `dash_target` WHERE month = '".date("n",time())."' AND year = '".date("Y",time())."'  AND day IS NULL AND week IS NULL";
				break;
			case 'Y':
				$query = "SELECT * FROM `dash_target` WHERE year = '".date("Y",time())."' AND day IS NULL AND week IS NULL AND month IS NULL";
				break;
			default:
				# code...
				break;
		}
		
		//echo $query;
		$result = $this->query($query);
		$items =  mysqli_num_rows($result);	
		$this->closeMYSQL();
		$this->debug("result: ".$items);
		if($items>0)
			return true;
		else		
			return false;
	}

	public function update($type, $field, $value){
		$this->connectMYSQL();
		$return=false;
		$this->debug("ACTUALIZAMOS");

		switch ($type) {
			case 'd':
				$query = "UPDATE `dash_target` set `$field` = $value  WHERE day = '".date("d",time())."' AND month = '".date("m",time())."' AND year = '".date("Y",time())."'" ;
				break;
			case 'W':
				$query = "UPDATE `dash_target` set `$field` = $value  WHERE week = '".date("W",time())."' AND year = '".date("Y",time())."'" ;
				break;
			case 'm':
				$this->debug("MES : ".date("n",time())."-".date("Y",time()));
				$query = "UPDATE `dash_target` set `$field` = $value  WHERE month = '".date("n",time())."' AND year = '".date("Y",time())."'  AND day IS NULL AND week IS NULL" ;
				break;
			case 'Y':
				$this->debug("AÑO : ".date("Y",time()));
				$query = "UPDATE `dash_target` set `$field` = $value  WHERE year = '".date("Y",time())."'  AND day IS NULL AND week IS NULL AND month IS NULL" ;
				break;
			default:
				# code...
				break;
		}
		//echo $query;

		if($result =  $this->query($query)){					
			$return=true;
		}
		$this->debug("RETURN : ".$return);
		$this->closeMYSQL();
		return $return;
	}

	public function insert($type ,$field, $value){
		$this->connectMYSQL();
		switch ($type) {
			case 'd':
				$query =  "INSERT INTO `dash_target` (
					  `day`,
					  `month`,
					  `year`,
					  `$field`
					  )VALUES(					  
					  '".date("d",time())."',
					  '".date("m",time())."',
					  '".date("Y",time())."',
					  '".$value."'
					  )" ;
				break;
			case 'W':
				$query =  "INSERT INTO `dash_target` (
					  `week`,
					  `year`,
					  `$field`
					  )VALUES(				
					  '".date("W",time())."',
					  '".date("Y",time())."',
					  '".$value."'
					  )" ;
				break;
			case 'm':
				$query =  "INSERT INTO `dash_target` (
					  `month`,
					  `year`,
					  `$field`
					  )VALUES(				
					  '".date("m",time())."',
					  '".date("Y",time())."',
					  '".$value."'
					  )" ;
				break;
			case 'Y':
				$query =  "INSERT INTO `dash_target` (
					  `year`,
					  `$field`
					  )VALUES(				
					  '".date("Y",time())."',
					  '".$value."'
					  )" ;
				break;
			default:
				# code...
				break;
		}
		$return = false;
		//echo $query;
		if ($result = $this->query($query)) {
			$return = true;
		}				
		$this->closeMYSQL();	
		return $return;
	}
	
	public function setTarget($type ,$field, $value){
		if(is_numeric($value) && ($value > 0)){
			if($this->check($type ,$field, $value)){
				$this->debug("actualizamos el campo ".$field." con valor ".$value." para el tipo: ".$type);
				$this->update($type ,$field, $value);
			}else{
				$this->debug("el valor no es numerico o no es mayor de 0");
				$this->insert($type ,$field, $value);
			}
		}else{
			$this->debug("el valor no es numerico o no es mayor de 0");
		}
	}

	public function debug($text){
		$is_debug = false;
		if($is_debug){
			echo $text.'<br/>';
		}
	}
}
?>