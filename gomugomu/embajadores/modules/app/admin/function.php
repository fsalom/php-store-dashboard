<?php
/*********************************************************
	APP_CATALOG
		id 	 	 	 	 	 	
		id_user_created 	 	 	 	 	 	
		created
		id_user_modified
		modified
		name
		status 0 (ok) | 1(deleted)
	
	APP_ZONES
		id
		name
		created
		id_user_created
		status 0 (ok) | 1(deleted)
**********************************************************/
function app_show(){
	$API= new API();
	$API->moduleName("app");
	$js=file_get_contents("../modules/app/admin/extra/show.txt");
	$API->setJS($js);
	
	$query=mysql_query("SELECT * FROM `app_catalog` WHERE `status`='0'")or die(mysql_error());
	if(mysql_num_rows($query)>0){


	while($dato=mysql_fetch_array($query)){
		$app['data1']=$dato['name'];
		$app['data2']=getusername($dato['id_user_created'])." | ".date(_TIMEFORMAT,$dato['created']);
		$app['data3']="COMPLETE";
		$app['data4']=date(_TIMEFORMAT,$dato['last_update']);
		
		$app['url1']="?go=app&do=edit&email=".$dato['email']."&id=".$dato['id']."";
		$app['url3']='?go=app&do=delete&id='.$dato['id'].'&email='.$dato['email'];
		$url="approws";
		$content['rows'].=$API->templateAdmin($url,$app);
	}
	}else{
		$content['rows'].="";
	}
	
	$url="apptable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}

function getusername($id){
	$by=mysql_query("SELECT * FROM `users` WHERE `id`='".$id."'");
		while($data=mysql_fetch_array($by)){
			$username=$data['username'];
		}
	return $username;
}

function app_new(){
	$API= new API();
	$API->moduleName("app");
	$API->setWHERE("Nuevo usuario");
	
	
	$js=file_get_contents("../modules/app/admin/extra/checkapp.txt");

	$API->setJS($js);
	$url="appnew";
	
	if($_GET['form']==1){
		
		$info['company']=$_POST['username'];
		$info['name'] =$_POST['name'];
		$info['surname'] =$_POST['surname'];
		$info['telephone'] =$_POST['telephone'];
		$info['email']=$_POST['email'];
		$info['emailcheck']=$_POST['emailcheck'];
		$info['appcheck']=$_POST['appcheck'];
		$info['validation']="";
		
		$date =time();
	
		if(($info['emailcheck']=="false")||($info['appcheck']=="false")||($info['name']=="")){
			
			$info['board']=$API->adminWarning("Wrong fields");

			$_SESSION['content']=$API->templateAdmin($url,$info);
			
			$API->printadmin();
		}else{
			mysql_query("INSERT INTO `apps` ( `company` , `name` ,`surname` , `registered_date` , `registered_by` , `email`, `telephone`)
			VALUES ('".$info['company']."', '".$info['name']."', '".$info['surname']."', '".time()."', '".$_SESSION['id']."', '".$info['email']."', '".$info['telephone']."');")
			or die(mysql_error());
			
			   			 
  			/*$url="appmail";
  			$info['name']=_webNAME;	
  			$content=$API->templateAdmin($url,$info);

						
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
			// Additional headers
			$headers .= 'To: '.$info['email'].'\r\n';
			$headers .= 'From: SoCal no-reply <no-reply@socalcommunitypages.com>' . "\r\n";

			mail($info['email'],"Your data to access - "._webNAME,$content,$headers); 
  			
  			 
    		//$API->sendMail($info['email'],"usted es nuevo usuario de "._webNAME,$content);*/
			
			$API->goto("?go=app");
		}
	}else{
		$content['appname']="";
		$content['username']="";
		$content['password']="";
		$content['appcheck']="false";
		$content['emailcheck']="false";
		$content['board']="";
	
		$content['validation']="";
		
		
		$data=$API->templateAdmin($url,$content);
		$_SESSION['content']=$data;
	
		$API->printadmin();

	}
	
}

function app_delete($email,$id){
	$API= new API();
	mysql_query("DELETE FROM `apps` WHERE `id` = '".$id."' AND `email` = '".$email."'");
	$API->goto("?go=app");
}

function app_edit($email,$id){
	$API= new API();
	$API->moduleName("app");
	$API->setWHERE("Editar privilegios");
	
	$js=file_get_contents("../modules/app/admin/extra/checkapp.txt");

	$API->setJS($js);
	$url="appedit";
	
	$info['id']=$id;
	$info['validation']="";
	$info['name']="";
	$info['surname']="";
	$info['board']="";
	$info['telephone']="";
	$info['email']="";
	
	$info['id']=$_GET['id'];
	$info['email']=$_GET['email'];
	
	
	$query=mysql_query("SELECT * FROM `apps` WHERE `id`='".$id."' AND `email`='".$email."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['name']=$dato['name'];
		$info['surname']=$dato['surname'];
		$info['email']=$dato['email'];
		$info['telephone']=$dato['telephone'];
		$info['username']=$dato['company'];
	}

		
	
	if($_GET['form']==1){
		
		$name=$_POST['name'];
		$id=$_GET['id'];
		$email=$_POST['email'];
		$surname=$_POST['surname'];
		$company=$_POST['username'];
		$telephone=$_POST['telephone'];
		$info['validation']="";

		
		mysql_query("UPDATE `apps` SET `name` = '".$name."', `email`='".$email."',`company`='".$company."',`telephone`='".$telephone."',`surname`='".$surname."' WHERE `id` ='".$id."'")or die(mysql_error());
		
			
		$API->goto("?go=app");
		
	}else{
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}
}

function app_reset($email,$id,$appname){

		$API= new API();
			
		$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$cad = "";
		for($i=0;$i<8;$i++) {
			$cad .= substr($str,rand(0,62),1);
		}
		
		$info['password']=$cad;
		$info['appname']=$appname;
		//echo $id." ".$email; 	
		$info['name']=_webNAME;
		
		$url="appmailremember";
		$busqueda="UPDATE `apps` SET `password` = '".md5($info['password'])."' WHERE `id` ='".$id."'";
		//echo $busqueda;
		mysql_query($busqueda) or die(mysql_error());
		
		$content=$API->templateAdmin($url,$info);
		
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
			// Additional headers
			$headers .= 'To: '.$info['email'].'\r\n';
			$headers .= 'From: SoCal no-reply <no-reply@socalcommunitypages.com>' . "\r\n";
		
		mail($email,"Your password for SoCal APP has been changed",$content,$headers); 

  		//$url="../modules/app/admin/face/mail2.html";
  		//$content=$API->template($url,$info);
  		//$API->sendMail($email,"Su password de acceso ha sido reiniciado",$content);
		$API->goto("?go=app");

	
}

function app_check(){

	$API= new API();
	$API->moduleName("app");
	$API->setWHERE("Validar usuario");

	$js=file_get_contents("../modules/app/admin/extra/show.txt");
	$API->setJS($js);

	$query=mysql_query("SELECT * FROM `apps` WHERE `status`='no'")or die(mysql_error());
	$num=mysql_num_rows($query);
	
	if($num>0){
		while($dato=mysql_fetch_array($query)){
			$app['data1']=$dato['appname'];
			$app['data2']=$dato['email'];
			$app['data3']=date(_TIMEFORMAT,$dato['register_date']);
			$app['data4']=$dato['status'];
			$app['data5']=$dato['name'];
			$app['data6']=$dato['surname'];
		
			$app['url2']="?go=app&do=validate&id=".$dato['id'];
			$app['url1']="?go=app&do=edit&email=".$dato['email']."&id=".$dato['id']."";
			$app['url3']='?go=app&do=delete&id='.$dato['id'].'&email='.$dato['email'];
			$url="../modules/app/admin/face/rows2.html";
			$content['rows'].=$API->template($url,$app);
		}
		$url="../modules/app/admin/face/table2.html";
		$table=$API->template($url,$content);
		$_SESSION['content']=$table;
	}else{	
		$table=$API->adminWarning("no hay usuarios para validar");
		
		$_SESSION['content']=$table;
	}
	$API->printadmin();

}

function app_validate($id){
	$API= new API();
	$busqueda="UPDATE `apps` SET `status` = 'si' WHERE `id` ='".$id."'";
	mysql_query($busqueda)or die(mysql_error());
	
	$query=mysql_query("SELECT * FROM `apps` WHERE `id`='".$id."'")or die(mysql_error());

		while($dato=mysql_fetch_array($query)){
			$name=$dato['name'];
			$email=$dato['email'];
		}	
		$mail = new phpmailer();

	
		 $mail->PluginDir = "../source/class/";
  			 $mail->Mailer = "smtp";

			 $mail->Host = "mail.urbansecurity.es";
 			 $mail->SMTPAuth = true;
			 $mail->appname = "noresponder@urbansecurity.es"; 
  			 $mail->Password = "adecu13";
			 $mail->From = "noresponder@urbansecurity.es";
  			 $mail->FromName = "noresponder@urbansecurity.es";
             $mail->Timeout=20;
  			 $mail->ClearAddresses();
  			 $mail->AddAddress($email);
  			 $mail->Subject = "Alta Urbansecurity";
  			 
  			 $url="../modules/app/admin/face/mail3.html";
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
			

	
	$API->goto("?go=app");

}
?>