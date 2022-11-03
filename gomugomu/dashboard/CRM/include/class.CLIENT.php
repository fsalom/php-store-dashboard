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
	public $created;

	public function __construct($name, $surname, $birthdate,$age, $gender, $email, $phone, $details, $address, $province, $zipcode, $city="", $from="", $created)
	{				
		$this->name= $name;
		$this->surname = $surname;
		if($birthdate=="0000-00-00"){
			$this->birthdate = "No indicado";
			$this->age		 = "";
		}else{
			$token=explode("-",$birthdate);
			$this->birthdate = $token[2]."/".$token[1]."/".$token[0];
			$this->age		 = $age;
		}
		
		$this->gender = $gender;
		$this->email = $email;
		if($this->email=="")
			$this->email="No definido";
		$this->phone = $phone;
		if($this->phone=="")
			$this->phone="No definido";
		$this->details = $details;
		$this->address = $address;
		$this->province = $province;
		if($zipcode=="0") $this->zipcode = "";
		else $this->zipcode = $zipcode;
		$this->city = $city;
		$this->from = $from;
		$this->created = $created;
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
		if($registers>0 && $email!="")
			return true;
		else		
			return false;
	}
	public function checkFields($POST){
		if($POST['birthdate']=="No indicado"){
			return true;
		}else{	
			if($POST['birthdate']){
				if (preg_match("/(\d{2})\/(\d{2})\/(\d{4})$/",$POST['birthdate'])){				
					return true;
				}else{
					$this->msg=_WRONG_BIRTHDATE."<br/> ("._FORMAT_DATE_USER.")";
					return false;
				}
			}else{
				//vacio
				return true;
			}	
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
					$return['mensaje']	= $result.'  -----  '.$query;
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

public function updateClient($POST){
		
		if($POST["email"]!=$POST['email_original']){
			if($this->checkClientExist($POST["email"])){
				$return['mensaje']	="Este correo ya existe";
				$return['ok'] 		= "1";
				return $return;
			}
		}
		$result=true;
		$this->connectMYSQL();
		if($this->checkFields($POST)){
				$newDate = "";
				if($POST['birthdate']){
					$newDate = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/","$3-$2-$1",$POST['birthdate']);
				}
				$query =  "UPDATE `crm_client` set
							  `name`='".$POST['name']."',
							  `surname`='".$POST['surname']."',
							  `birthdate`='".$newDate."',
							  `email`='".$POST['email']."',
							  `phone`='".$POST['phone']."',
							  `details`='".$POST['details']."',
							  `address`='".$POST['address']."',
							  `zipcode`='".$POST['zipcode']."',
							  `city`='".$POST['city']."'
							  WHERE id='".$POST['id']."'";
				
				if ($result = $this->query($query)) {
					$return['mensaje']	="Cliente Actualizado";
					$return['ok'] 		= "0";				
				}else{				
					$return['mensaje']	.= "Se ha producido un error al guardar el usuario: <br/>". $this->error() . " ". $result;
					$return['ok'] 		= "2";
				}
		}else{
				$return['mensaje']	= $this->msg;
				$return['ok'] 		= "2";
		}				
		$this->closeMYSQL();	
			
		return $return;
		
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
		$search = strtolower($POST['search']);
		$query = "SELECT * FROM `crm_client` where (lower(name) like'%".$search."%' or lower(surname) like '%".$search."%') AND status=0 ORDER BY `surname` ASC LIMIT 20"  ;
		if($result =  $this->query($query)){
			$return = "";
			while($data=$result->fetch_assoc()){												
				$replace_name 	 = str_ireplace($POST['search'], '<b>'.$POST['search'].'</b>', $data['name']);								
				$replace_surname = str_ireplace($POST['search'], '<b>'.$POST['search'].'</b>', $data['surname']);
				$name=$data['id'].".jpg";				
				if(!file_exists('files/' . $name)){
					$name="no.png";
				}
				$return.='<a class="list_clients" href="?go=profile&id='.$data['id'].'">
							<div class="img"><img src="files/'.$name.'"></div>
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
								$this->selectFrom($data['from']),
								$data['date_created']);			
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
	
	public function getAllBuyers(){
		$this->connectMYSQL();
		$query = "SELECT sum(B.`quantity`) as totalquantity, A.`id_client` FROM `dash_ticket_buyer` A, `dash_payment` B WHERE A.`id_ticket` = B.`id_ticket` AND A.`id_client` <> 0 group by A.`id_client` ORDER BY totalquantity DESC"  ;
		if($result =  $this->query($query)){
			$return = "";
			while($data=$result->fetch_assoc()){
				//print_r($data);
				$sum = 0;
				$query2 = "SELECT * FROM `crm_client`  WHERE `id` = ".$data['id_client']  ;
				if($result2 =  $this->query($query2)){
					$data2=$result2->fetch_assoc();
				}
				if($data['totalquantity']!=0){
				$return.='<tr>
							<td><a href="?go=profile&id='.$data2['id'].'">'.ucwords(strtolower($data2['name'])).'</a></td>
							<td>'.ucwords(strtolower($data2['surname'])).'</td>
							<td>'.$data2['email'].'</td>
							<td>'.$data2['phone'].'</td>
							<td>'.number_format($data['totalquantity'], 2, '.', '').' euros</td>
						  </tr>';
				}
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
	
	public function upload($POST){
		
				
		if (!empty($_FILES["myFile"])) {
			//SUBIR GUARDAR
				$myFile = $_FILES["myFile"];
			
				if ($myFile["error"] !== UPLOAD_ERR_OK) {
					echo "Se ha producido un error al añadir la imagen";
					exit;
				}
			
				
				$temp = explode(".", $_FILES["myFile"]["name"]);
				$newfilename = $_GET['id'] . '.' . $temp[1];
				/*
				// ensure a safe filename
				$name = preg_replace("/[^A-Z0-9._-]/i", "_", $myFile["name"]);
				
				// don't overwrite an existing file
				$i = 0;
				$parts = pathinfo($name);
				while (file_exists(UPLOAD_DIR . $name)) {
					$i++;
					$name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
				}*/
			
				// preserve file from temporary directory
				$upload_dir = $_SERVER['DOCUMENT_ROOT'] . UPLOAD_DIR;
				
				if (is_dir($upload_dir) && is_writable($upload_dir)) {
					// do upload logic here
				} else {
					echo 'El directorio no tiene los permisos de escritura necesario o no existe.' . $upload_dir;
					exit;
				}
				$success = move_uploaded_file($myFile["tmp_name"],
						$upload_dir . $newfilename);
				if (!$success) {
					echo "No se ha podido guardar la imagen";	
					exit;
				}
			
				// set proper permissions on the new file
				chmod(UPLOAD_DIR . $name, 0644);
				echo "Imagen guardada correctamente";						
			
		}else{
			echo "No se ha adjuntado imagen para guardar";			
		}		
	}
	
	public function getExtension($str) {

         $i = strrpos($str,".");
         if (!$i) { return ""; } 
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
	}
	
	public function upload2(){
		//SUBIR REDIMENSIONAR Y GUARDAR
		define ("MAX_SIZE","400");
		
		$errors=0;
		
		$image =$_FILES["myFile"]["name"];
		$uploadedfile = $_FILES['myFile']['tmp_name'];
		
		if ($image){
				$filename = stripslashes($_FILES['myFile']['name']);
				$extension = $this->getExtension($filename);
				$extension = strtolower($extension);
				if (($extension != "jpg") && ($extension != "jpeg")
						&& ($extension != "png") && ($extension != "gif"))
				{
					echo 'tipo de imagen no permitida';
					$errors=1;
				}else{
					$size=filesize($_FILES['myfile']['tmp_name']);
		
					if ($size > MAX_SIZE*1024)
					{
						echo "La imagen es demasiado grande";
						$errors=1;
					}
		
					if($extension=="jpg" || $extension=="jpeg" )
					{
						$uploadedfile = $_FILES['myFile']['tmp_name'];
						$src = imagecreatefromjpeg($uploadedfile);																
					}
					else if($extension=="png")
					{
						$uploadedfile = $_FILES['myFile']['tmp_name'];
						$src = imagecreatefrompng($uploadedfile);
					}
					else
					{
						$src = imagecreatefromgif($uploadedfile);
					}
		
					
					//$filename = 'files/cropped_whatever.jpg';
					
					$thumb_width = 300;
					$thumb_height = 300;
					
					$width = imagesx($src);
					$height = imagesy($src);
					
					$original_aspect = $width / $height;
					$thumb_aspect = $thumb_width / $thumb_height;
					
					if ( $original_aspect >= $thumb_aspect )
					{
						// If image is wider than thumbnail (in aspect ratio sense)
						$new_height = $thumb_height;
						$new_width = $width / ($height / $thumb_height);
					}
					else
					{
						// If the thumbnail is wider than the image
						$new_width = $thumb_width;
						$new_height = $height / ($width / $thumb_width);
					}
					
					$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
					
					// Resize and crop
					imagecopyresampled($thumb,
							$src,
							0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
							0 - ($new_height - $thumb_height) / 2, // Center the image vertically
							0, 0,
							$new_width, $new_height,
							$width, $height);					
					
					
					
					/*
					list($width,$height)=getimagesize($uploadedfile);
					echo $height." -- ".$width;
					$newwidth=300;
					$newheight=300;($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
		
					$newwidth1=25;
					$newheight1=25;//($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
		
					imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,
							$width,$height);
		
					imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,
							$width,$height);*/
		
					$filename = "files/".$_GET['id'].".".$extension;
					//$filename1 = "files/small".$_GET['id'].".".$extension;
		
					imagejpeg($thumb,$filename,100);
					//imagejpeg($tmp1,$filename1,100);
		
					imagedestroy($src);
					imagedestroy($thumb);
					//imagedestroy($tmp1);
				}
			}
		
		//If no errors registred, print the success message	
		if(!$errors)
		{
			// mysql_query("update SQL statement ");
			echo "La imagen se ha guardado correctamente";
		
		}		
	}
}
?>