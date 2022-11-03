<?php
class CLIENT {
	public $name;
	public $surname;
	public $birthdate;
	public $age;
	public $gender;
	public $email;
	public $phone;
	public $details;
	public $address;
	public $province;
	public $zipcode;
	public $city;
	public $from;	

	public function __construct($name, $surname, $birthdate,$age, $gender, $email, $phone, $details, $address, $province, $zipcode, $city="", $from="")
	{				
		$this->name= $name;
		$this->surname = $surname;
		if($birthdate=="0000-00-00"){
			$this->birthdate = "No indicado";
			$this->age		 = "";
		}else{
			$this->birthdate = $birthdate;
			$this->age		 = $age;
		}
		
		$this->gender = $gender;
		$this->email = $email;
		$this->phone = $phone;
		$this->details = $details;
		$this->address = $address;
		$this->province = $province;
		if($zipcode=="0") $this->zipcode = "";
		else $this->zipcode = $zipcode;
		$this->city = $city;
		$this->from = $from;
	}
}


class CLIENTS extends SHARE  {	 
	private $msg;
	public function checkClientExist($email){
		$this->connectMYSQL();
		$query = "SELECT * FROM `crm_client` WHERE email = '".$email."' AND status=0";
		$result = $this->query($query);
				
		$registers =  $this->num_rows($result);		
		$this->closeMYSQL();
		if($registers>0)
			return true;
		else		
			return false;
	}
	public function checkFields($POST){
		if($POST['birthdate']){
			if (preg_match("/(\d{2})\/(\d{2})\/(\d{4})$/",$POST['birthdate'])){				
				return true;
			}else{
				$this->msg=_WRONG_BIRTHDATE."<br/> ("._FORMAT_DATE.")";
				return false;
			}
		}else{
			//vacio
			return true;
		}		
	}
	public function insertClient($POST){
		if($this->checkClientExist($POST["email"])){
			$return['mensaje']	="Este usuario ya existe";
			$return['ok'] 		= "1";
			return $return;
		}else{
			$this->connectMYSQL();
			if($this->checkFields($POST)){
				$newDate = "";
				if($POST['birthdate']){
					$newDate = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/","$3-$2-$1",$POST['birthdate']);}
				$query =  "INSERT INTO `crm_client` (
						  `name`,
						  `surname`,
						  `birthdate`,
						  `gender`,
						  `email`,
						  `phone`,
						  `details`,
						  `address`,
						  `province`,
						  `zipcode`,
						  `city`,
						  `from`
						  )VALUES(					  
						  '".$POST['name']."',
						  '".$POST['surname']."',
						  '".$newDate."',
						  '".$POST['gender']."',
						  '".$POST['email']."',
						  '".$POST['phone']."',
						  '".$POST['details']."',
						  '".$POST['address']."', 
						  '".$POST['province']."',
						  '".$POST['zipcode']."',
						  '".$POST['city']."',
						  '".$POST['from']."'
						  )" ;
				if ($result = $this->query($query)) {
					$return['mensaje']	="Cliente Guardado";
					$return['ok'] 		= "0";				
				}else{				
					$return['mensaje']	= "Se ha producido un error al guardar el usuario: <br/>". $this->error();
					$return['ok'] 		= "2";
				}
			}else{
				$return['mensaje']	= $this->msg;
				$return['ok'] 		= "2";
			}				
			$this->closeMYSQL();	
			
			return $return;
		}
	}
	public function getProvince(){
		$this->connectMYSQL();		
		$query = "SELECT * FROM `crm_province`" ;
		if($result =  $this->query($query)){	
			
			while($data=$result->fetch_assoc()){				
				$return.='<option value="'.$data['id'].'">'.utf8_encode($data['province']).'</option>';
			}		
		}
		$this->closeMYSQL();	
		return $return;		
	}
	
	public function getProvinceName($province){
		$this->connectMYSQL();
		$query = "SELECT * FROM `crm_province` where id=".$province ;
		if($result =  $this->query($query)){
			$data=$result->fetch_assoc();
			$return.=utf8_encode($data['province']);
			
		}
		$this->closeMYSQL();
		return $return;
	}
	
	public function selectFrom($from){
		$return = $from;
		switch($from){
			case '0': $return="Tienda";break;			
			case '1': $return="Colegios";break;
			case '2': $return="Red Bull";break;
			case '3': $return="Fiesta animas";break;
		}
		return $return;
	}
	public function searchClient($POST){
		$this->connectMYSQL();
		$query = "SELECT * FROM `crm_client` where (name like'%".$POST['search']."%' or surname like '%".$POST['search']."%') AND status=0 ORDER BY `from` ASC LIMIT 10"  ;
		if($result =  $this->query($query)){
			$return = "";
			while($data=$result->fetch_assoc()){												
				$replace_name 	 = str_ireplace($POST['search'], '<b>'.$POST['search'].'</b>', $data['name']);								
				$replace_surname = str_ireplace($POST['search'], '<b>'.$POST['search'].'</b>', $data['surname']);
				$return.='<a class="list_clients" href="?go=profile&id='.$data['id'].'">
							<div class="img"></div>
							'.ucwords(strtolower(strtoupper($replace_name))).' '.ucwords(strtolower(strtoupper($replace_surname))).'<br/><b>'.$this->selectFrom($data['from']).'</b>
						</a>';
			}
		}
		$this->closeMYSQL();
		return $return;
	}
	public function getClient($id){
		$this->connectMYSQL();
		$query = "SELECT * FROM `crm_client` WHERE id=" . $id  ;				
		if($result =  $this->query($query)){			
			$data=$result->fetch_assoc();		
				
			$return= NEW CLIENT(ucwords(strtolower($data['name'])),
								ucwords(strtolower($data['surname'])), 
								$data['birthdate'], 
								$this->getAge($data['birthdate']),
							 	$data['gender'], 
								$data['email'], 
								$data['phone'], 
								$data['details'], 
								$data['address'], 
								$this->getProvinceName($data['province']), 
								$data['zipcode'], 
								$data['city'], 
								$this->selectFrom($data['from']));			
		}
		$this->closeMYSQL();
		return $return;
	}
	
	public function getAll(){
		$this->connectMYSQL();
		$query = "SELECT * FROM `crm_client` WHERE status=0 ORDER BY `surname` "  ;
		if($result =  $this->query($query)){
			$return = "";
			while($data=$result->fetch_assoc()){
				$return.='<tr>
							<td><a href="?go=profile&id='.$data['id'].'">'.ucwords(strtolower($data['name'])).'</a></td>
							<td>'.ucwords(strtolower($data['surname'])).'</td>
							<td>'.$data['email'].'</td>
							<td>'.$data['phone'].'</td>
							<td><a href="?do=disable&id='.$data['id'].'"><span  class="btn-small btn-danger">X</span></a></td>
						  </tr>';
			}
		}
		$this->closeMYSQL();
		return $return;
	}
	
	public function getAge($birthdate){				
		$birthdate = explode("-", $birthdate);		
		$age = (date("md", date("U", mktime(0, 0, 0, $birthdate[2], $birthdate[1], $birthdate[0]))) > date("md")
				? ((date("Y") - $birthdate[0]) - 1)
				: (date("Y") - $birthdate[0]));
		return $age;	
	}
	
	public function disableClient($id){
		$this->connectMYSQL();
		//STATUS 0 -> allowed
		//STATUS 1 -> disabled
		$return=false;
		$query = "UPDATE `crm_client` set status=1 WHERE id=" . $id  ;
		if($result =  $this->query($query)){					
			$return=true;
		}
		$this->closeMYSQL();
		//return $return;
	}
}
?>