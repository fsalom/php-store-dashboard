<?php
function client_show(){
	$API= new API();
	$API->moduleName("client");
	$API->setWHERE("Listado de usuarios");
	$js=file_get_contents("../modules/client/admin/extra/show.txt");

	$API->setJS($js);
	
	$query=mysql_query("SELECT * FROM `clients` WHERE `status`='0'")or die(mysql_error());
	$num=mysql_num_rows($query);
	if($num>0){

	$pages = new Paginator;
	
	$pages->url = "?go=client";
	$pages->items_total = $num;
	$pages->mid_range =_PAGINATOR_MID_RANGE;
	$pages->items_per_page=_PAGINATOR_ITEMS_PER_PAGE; 
	$pages->paginate();
	
	$query=mysql_query("SELECT * FROM `clients` WHERE `status`=0  ORDER BY `company` ASC".$pages->limit)or die(mysql_error());

	$content['page']=$pages->display_pages();


	while($dato=mysql_fetch_array($query)){
		$client['data1']=$dato['name']." ".$dato['surname'];
		$client['data2']=$dato['company'];
		$client['data3']=$dato['email'];
		$client['data4']=date(_TIMEFORMAT,$dato['registered_date']);
		
			$client['data6']=$dato['telephone'];
		
		$by=mysql_query("SELECT * FROM `users` WHERE `id`='".$dato['registered_by']."'");
		while($take=mysql_fetch_array($by)){
			$client['data5']=$take['username'];
		}
		$client['url2']="mailto:".$dato['email'];
		$client['url1']="?go=client&do=edit&email=".$dato['email']."&id=".$dato['id']."";
		$client['url3']='?go=client&do=delete&id='.$dato['id'].'&email='.$dato['email'];
		$url="clientrows";
		$content['rows'].=$API->templateAdmin($url,$client);
	}
	}else{
		$content['rows'].="";
	}
	
	
	$url="clienttable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}



function client_new(){
	$API= new API();
	$API->moduleName("client");
	$API->setWHERE("Nuevo usuario");
	
	
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="clientnew";
	
	if($_GET['form']==1){
		
		$info['company']=$_POST['username'];
		$info['name'] =$_POST['name'];
		$info['surname'] =$_POST['surname'];
		$info['telephone'] =$_POST['telephone'];
		$info['email']=$_POST['email'];
		$info['emailcheck']=$_POST['emailcheck'];
		$info['clientcheck']=$_POST['clientcheck'];
		$info['position']=$_POST['position'];
		$info['mobile']=$_POST['mobile'];
		$info['ad_street']=$_POST['street'];
		$info['ad_town']=$_POST['town'];
		$info['ad_zip']=$_POST['zip'];
		$info['ad_province']=$_POST['province'];
		$info['ad_country']=$_POST['country'];
		$info['comercial']=$_POST['comercial'];
		$info['cif']=$_POST['cif'];
		$info['validation']="";
		
	
		$date =time();
	
		if(($info['emailcheck']=="false")||($info['name']=="")){
					
			$info['username']=$_POST['username'];
			$info['zone']="";
			$query=mysql_query("SELECT * FROM `app_zones` WHERE `status`='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				$info['zone'].='<input type="checkbox" name="zones[]" value="'.$dato['id'].'">'.$dato['name'].'<br>';
			}
			
			$info['position']='<select id="position" name="position">';
			$info['position'].='<option value="0">None</option>';
			$query=mysql_query("SELECT * FROM `app_position` WHERE `status`='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				$content['position'].='<option value="'.$dato['id'].'">'.$dato['name'].'</option>';
			}
			$info['position'].="</select>";
			
			$info['validation']="";

			
			
			
			$info['board']=$API->adminWarning("Wrong fields");

			$_SESSION['content']=$API->templateAdmin($url,$info);
			
			$API->printadmin();
		}else{
			echo $_POST['month'];
			mysql_query("INSERT INTO `clients` ( `company` , `name` ,`surname` , `registered_date` , `registered_by` , `email`, `telephone`, `position`, `mobile`, `ad_street`, `ad_town`, `ad_zip`, `ad_province`, `ad_country`, `comercial`, `info`, `CIF`)
			VALUES ('".$info['company']."', '".$info['name']."', '".$info['surname']."', '".time()."', '".$_SESSION['login_id']."', '".$info['email']."', '".$info['telephone']."', '".$info['position']."', '".$info['mobile']."', '".$_POST['street']."','".$info['ad_town']."','".$info['ad_zip']."','".$info['ad_province']."','".$info['ad_country']."','".$info['comercial']."','".$_POST['info']."','".$_POST['cif']."');")
			or die(mysql_error());
			//ZONES

			
			$API->goto("?go=client");
		}
	}else{
		$content['clientname']="";
		$content['username']="";
		$content['password']="";
		$content['clientcheck']="false";
		$content['emailcheck']="false";
		$content['board']="";
		
		
		$content['zone']="";
		$query=mysql_query("SELECT * FROM `app_zones` WHERE `status`='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				$content['zone'].='<input type="checkbox" name="zones[]" value="'.$dato['id'].'">'.$dato['name'].'<br>';
			}
			
		$content['position']='<select id="position" name="position">';
		$content['position'].='<option value="0">None</option>';
		$query=mysql_query("SELECT * FROM `app_position` WHERE `status`='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				$content['position'].='<option value="'.$dato['id'].'">'.$dato['name'].'</option>';
			}
		$content['position'].="</select>";
		
		$content['validation']="";
		
		
		$data=$API->templateAdmin($url,$content);
		$_SESSION['content']=$data;
	
		$API->printadmin();

	}
	
}

function client_delete($email,$id){
	$API= new API();
	mysql_query("DELETE FROM `clients` WHERE `id` = '".$id."' AND `email` = '".$email."'");
	$API->goto("?go=client");
}

function client_edit($email,$id){
	$API= new API();
	$API->moduleName("client");
	$API->setWHERE("Editar privilegios");
	
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="clientedit";
	
	$info['id']=$id;
	$info['validation']="";
	$info['name']="";
	$info['surname']="";
	$info['board']="";
	$info['telephone']="";
	$info['email']="";
	$info['info']="";
	$info['rep']="";
	$info['page']="";
			$info['position']="";
		$info['mobile']="";
		$info['ad_street']="";
		$info['ad_town']="";
		$info['ad_zip']="";
		$info['ad_province']="";
		$info['ad_country']="";
		$info['comercial']="";
		$info['cif']="";
		$info['validation']="";
		
	$info['id']=$_GET['id'];
	$info['email']=$_GET['email'];
	
	
	$query=mysql_query("SELECT * FROM `clients` WHERE `id`='".$id."' AND `email`='".$email."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['name']=$dato['name'];
		$info['surname']=$dato['surname'];
		$info['email']=$dato['email'];
		$info['telephone']=$dato['telephone'];
		$info['username']=$dato['company'];
		$info['position']=$dato['position'];
		$info['mobile']=$dato['mobile'];
		$info['street']=$dato['ad_street'];
		$info['town']=$dato['ad_town'];
		$info['zip']=$dato['ad_zip'];
		$info['cif']=$dato['CIF'];
		$info['province']=$dato['ad_province'];
		$info['country']=$dato['ad_country'];
		$info['comercial']=$dato['comercial'];
		$info['info']=$dato['info'];
	}
		//ZONE
		$info['zone']="";
		
		$azones=array();
		$i=0;
		$query=mysql_query("SELECT * FROM `app_zones_relation` WHERE `id_client`='".$id."' AND status='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
					$azones[$i]=$dato['id_zone'];
					$i++;
			}
			//print_r($azones);
			
		$query=mysql_query("SELECT * FROM `app_zones` WHERE `status`='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				if(in_array($dato['id'],$azones))
					$info['zone'].='<input type="checkbox" name="zones[]" checked="checked" value="'.$dato['id'].'">'.$dato['name'].'<br>';
				else
					$info['zone'].='<input type="checkbox" name="zones[]" value="'.$dato['id'].'">'.$dato['name'].'<br>';
			}
		

				
	if($_GET['form']==1){
		
		$name=$_POST['name'];
		$id=$_GET['id'];
		$email=$_POST['email'];
		$surname=$_POST['surname'];
		$company=$_POST['username'];
		$telephone=$_POST['telephone'];
		$term=$_POST['term'];
		$position=$_POST['position'];
		$rep=$_POST['rep'];
		$information=$_POST['info'];
		$page=$_POST['page'];
		$size=$_POST['size'];
		$month=$_POST['month'];
		$year=$_POST['year'];
		$info['validation']="";
		$info['cif']=$_POST['cif'];
		$info['position']=$_POST['position'];
		$info['mobile']=$_POST['mobile'];
		$info['ad_street']=$_POST['street'];
		$info['ad_town']=$_POST['town'];
		$info['ad_zip']=$_POST['zip'];
		$info['ad_province']=$_POST['province'];
		$info['ad_country']=$_POST['country'];
		$info['comercial']=$_POST['comercial'];
		$info['validation']="";
 

		
		mysql_query("UPDATE `clients` SET `name` = '".$name."', `email`='".$email."',`company`='".$company."',`telephone`='".$telephone."',`surname`='".$surname."',`mobile`='".$info['mobile']."',`position`='".$position."',`ad_street`='".$info['ad_street']."',`ad_town`='".$info['ad_town']."',`ad_zip`='".$info['ad_zip']."',`info`='".$information."',`ad_province`='".$info['ad_province']."',`ad_country`='".$info['ad_country']."', `comercial`='".$info['comercial']."', `CIF`='".$info['cif']."' WHERE `id` ='".$id."'")or die(mysql_error());
		
			
		$API->goto("?go=client&do=edit&form=2&email=".$email."&id=".$id);
	}else if($_GET['form']==2){
		$info['board']='
<div class="notification green">
	Los cambios han sido guardados
</div>';
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}else{
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}
}

function client_reset($email,$id,$clientname){

		$API= new API();
			
		$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$cad = "";
		for($i=0;$i<8;$i++) {
			$cad .= substr($str,rand(0,62),1);
		}
		
		$info['password']=$cad;
		$info['clientname']=$clientname;
		//echo $id." ".$email; 	
		$info['name']=_webNAME;
		
		$url="clientmailremember";
		$busqueda="UPDATE `clients` SET `password` = '".md5($info['password'])."' WHERE `id` ='".$id."'";
		//echo $busqueda;
		mysql_query($busqueda) or die(mysql_error());
		
		$content=$API->templateAdmin($url,$info);
		
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
			// Additional headers
			$headers .= 'To: '.$info['email'].'\r\n';
			$headers .= 'From: SoCal no-reply <no-reply@socalcommunitypages.com>' . "\r\n";
		
		mail($email,"Your password for SoCal APP has been changed",$content,$headers); 

  		//$url="../modules/client/admin/face/mail2.html";
  		//$content=$API->template($url,$info);
  		//$API->sendMail($email,"Su password de acceso ha sido reiniciado",$content);
		$API->goto("?go=client");

	
}

function client_check(){

	$API= new API();
	$API->moduleName("client");
	$API->setWHERE("Validar usuario");

	$js=file_get_contents("../modules/client/admin/extra/show.txt");
	$API->setJS($js);

	$query=mysql_query("SELECT * FROM `clients` WHERE `status`='no'")or die(mysql_error());
	$num=mysql_num_rows($query);
	
	if($num>0){
		while($dato=mysql_fetch_array($query)){
			$client['data1']=$dato['clientname'];
			$client['data2']=$dato['email'];
			$client['data3']=date(_TIMEFORMAT,$dato['register_date']);
			$client['data4']=$dato['status'];
			$client['data5']=$dato['name'];
			$client['data6']=$dato['surname'];
		
			$client['url2']="?go=client&do=validate&id=".$dato['id'];
			$client['url1']="?go=client&do=edit&email=".$dato['email']."&id=".$dato['id']."";
			$client['url3']='?go=client&do=delete&id='.$dato['id'].'&email='.$dato['email'];
			$url="../modules/client/admin/face/rows2.html";
			$content['rows'].=$API->template($url,$client);
		}
		$url="../modules/client/admin/face/table2.html";
		$table=$API->template($url,$content);
		$_SESSION['content']=$table;
	}else{	
		$table=$API->adminWarning("no hay usuarios para validar");
		
		$_SESSION['content']=$table;
	}
	$API->printadmin();

}

function client_validate($id){
	$API= new API();
	$busqueda="UPDATE `clients` SET `status` = 'si' WHERE `id` ='".$id."'";
	mysql_query($busqueda)or die(mysql_error());
	
	$query=mysql_query("SELECT * FROM `clients` WHERE `id`='".$id."'")or die(mysql_error());

		while($dato=mysql_fetch_array($query)){
			$name=$dato['name'];
			$email=$dato['email'];
		}	
		$mail = new phpmailer();

	
		 $mail->PluginDir = "../source/class/";
  			 $mail->Mailer = "smtp";

			 $mail->Host = "mail.urbansecurity.es";
 			 $mail->SMTPAuth = true;
			 $mail->clientname = "noresponder@urbansecurity.es"; 
  			 $mail->Password = "adecu13";
			 $mail->From = "noresponder@urbansecurity.es";
  			 $mail->FromName = "noresponder@urbansecurity.es";
             $mail->Timeout=20;
  			 $mail->ClearAddresses();
  			 $mail->AddAddress($email);
  			 $mail->Subject = "Alta Urbansecurity";
  			 
  			 $url="../modules/client/admin/face/mail3.html";
  			 $info['info']="<b>".$name."</b> ha sido dado de alta en Urbanscurity";
  			 $mail_content=$API->template($url,$info);
  			 
    		 $mail->Body = $mail_content;
			 $mail->AltBody = "Ha sido dado de alta en Urbansecurity";

  			$exito = $mail->Send();
 			 $intentos=1; 
 			 while ((!$exito) && ($intentos < 2)) {
				sleep(2);
     			$exito = $mail->Send();
     			$intentos=$intentos+1;	
			}
			

	
	$API->goto("?go=client");

}
?>