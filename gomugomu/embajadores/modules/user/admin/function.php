<?php
function user_show(){
	$API= new API();
	$API->moduleName("user");
	$API->setWHERE("Listado de usuarios");
	$js=file_get_contents("../modules/user/admin/extra/show.txt");

	$API->setJS($js);
	
	$query=mysql_query("SELECT * FROM `users` WHERE `status`='0'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		if($dato['level']=="10")
			$user['data1']="*** ".$dato['username'];
		else if($dato['level']=="5")
			$user['data1']="** ".$dato['username'];
		else
			$user['data1']="* ".$dato['username'];
		$user['data2']=$dato['email'];
		$user['data3']=date(_TIMEFORMAT,$dato['register_date']);
		if($dato['last_login']==0){
			$user['data4']="never logged in";
		}else{
			$user['data4']=date(_TIMEFORMAT,$dato['last_login']);
		}
		$user['data5']=$dato['level'];
		$user['url2']="?go=user&do=resetpass&email=".$dato['email']."&id=".$dato['id']."&username=".$dato['username']."";
		$user['url1']="?go=user&do=edit&email=".$dato['email']."&id=".$dato['id']."";
		$user['url3']='?go=user&do=delete&id='.$dato['id'].'&email='.$dato['email'];
		$url="userrows";
		$content['rows'].=$API->templateAdmin($url,$user);
	}
	
	$url="usertable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}
/*********************************************************
	USERS
		id
		name
		status 0 (ok) | 1(deleted)
**********************************************************/
function user_new(){
	$API= new API();
	$API->moduleName("user");
	$API->setWHERE("Nuevo usuario");
	
	
	$js=file_get_contents("../modules/user/admin/extra/checkuser.txt");

	$API->setJS($js);
	$url="usernew";
	
	if($_GET['form']==1){
		
		$info['username']=$_POST['username'];
		$info['password'] =$_POST['password'];
		$info['email']=$_POST['email'];
		$info['emailcheck']=$_POST['emailcheck'];
		$info['usercheck']=$_POST['usercheck'];
		$info['level']=$_POST['level'];
		$info['validation']="";
		
		$date =time();
		$category = $_POST['category'];
	
		if(($info['emailcheck']=="false")||($info['usercheck']=="false")||($info['password']=="")){
			
			$info['board']=$API->adminWarning("Wrong fields");

			$_SESSION['content']=$API->templateAdmin($url,$info);
			
			$API->printadmin();
		}else{
			mysql_query("INSERT INTO `users` ( `username` , `password` , `register_date` , `email` , `level` , `status`)
			VALUES ('".$info['username']."', '".md5($info['password'])."', '".$date."', '".$info['email']."','".$info['level']."','0');")
			or die(mysql_error());
			
			   			 
  			$url="usermail";
  			$info['name']=_webNAME;	
  			$content=$API->templateAdmin($url,$info);

						
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
			// Additional headers
			$headers .= 'To: '.$info['email'].'\r\n';
			$headers .= 'From: SoCal no-reply <no-reply@socalcommunitypages.com>' . "\r\n";

			
		/*
		$mail = new phpmailer();

		$mail->Mailer = "smtp";
		$mail->Host = "localhost";
		$mail->SMTPAuth = true;
		$mail->Username = "no-reply@socalcommunitypages.com"; 
		$mail->Password = "socal360";
		//$mail->SMTPDebug = true;
		$mail->Port = 25; 
		$mail->From = "no-reply@socalcommunitypages.com";
		$mail->FromName = "Socal Community Pages"; 
		$mail->Timeout=30;
		$mail->AddAddress($info['email']);
		$mail->Subject = "Your password for SoCal APP has been changed";
		$mail->Body = $content;
		$mail->AltBody = htmlentities($content);
		$exito = $mail->Send();

   if(!$mail->Send())
{
   echo "Message could not be sent. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}
	*/
   
			mail($info['email'],"Tus datos de acceso a "._webNAME,$content,$headers); 
  			
  			 
    		//$API->sendMail($info['email'],"usted es nuevo usuario de "._webNAME,$content);
			
			$API->goto("?go=user");
		}
	}else{
		$content['username']="";
		$content['password']="";
		$content['usercheck']="false";
		$content['emailcheck']="false";
		$content['board']="";
	
		$content['validation']="";
		
		
		$data=$API->templateAdmin($url,$content);
		$_SESSION['content']=$data;
	
		$API->printadmin();

	}
	
}

function user_delete($id){
	$API= new API();
	mysql_query("UPDATE `users` SET `status` = '1' WHERE `id`='".$_GET['id']."'");
	$API->goto("?go=user");
}

function user_edit($email,$id){
	$API= new API();
	$API->moduleName("user");
	$API->setWHERE("Editar privilegios");
	
	$js=file_get_contents("../modules/user/admin/extra/checkuser.txt");

	$API->setJS($js);
	$url="useredit";
	
	$info['id']=$id;
	$info['validation']="";
	$info['usuario']="";
	$info['invitado']="";
	$info['board']="";
	$info['se_admin']="";
	$info['se_user']="";
	
	$info['id']=$_GET['id'];
	$info['email']=$_GET['email'];
	
	
	$query=mysql_query("SELECT * FROM `users` WHERE `id`='".$id."' AND `email`='".$email."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['level']=$dato['level'];
		$info['username']=$dato['username'];
	}

		if($info['level']=="10"){
			$info['se_admin']='selected="selected"';
		}
		if($info['level']=="5"){
			$info['se_user']='selected="selected"';
		}
		if($info['level']=="1"){
			$info['se_art']='selected="selected"';
		}
	
	if($_GET['form']==1){
		
		$info['level']=$_POST['level'];
		$id=$_GET['id'];
		$email=$_POST['email'];
		
		$username=$_POST['username'];
		$info['validation']="";

		
		mysql_query("UPDATE `users` SET `level` = '".$info['level']."', `email`='".$email."',`username`='".$username."' WHERE `id` ='".$id."'")or die(mysql_error());
		
			
		$API->goto("?go=user");
		
	}else{
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}
}

function user_reset($email,$id,$username){

		$API= new API();
			
		$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$cad = "";
		for($i=0;$i<8;$i++) {
			$cad .= substr($str,rand(0,62),1);
		}
		
		$info['password']=$cad;
		$info['username']=$username;
		//echo $id." ".$email; 	
		$info['name']=_webNAME;
		
		$url="usermailremember";
		$busqueda="UPDATE `users` SET `password` = '".md5($info['password'])."' WHERE `id` ='".$id."'";
		//echo $busqueda;
		mysql_query($busqueda) or die(mysql_error());
		
		$content=$API->templateAdmin($url,$info);
		
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
			// Additional headers
			$headers .= 'To: '.$info['email'].'\r\n';
			$headers .= 'From: SoCal no-reply <no-reply@socalcommunitypages.com>' . "\r\n";
		
		$mail = new phpmailer();

		$mail->Mailer = "smtp";
		$mail->Host = "localhost";
		$mail->SMTPAuth = true;
		$mail->Username = "no-reply@socalcommunitypages.com"; 
		$mail->Password = "socal360";
		//$mail->SMTPDebug = true;
		$mail->Port = 25; 
		$mail->From = "no-reply@socalcommunitypages.com";
		$mail->FromName = "Socal Community Pages"; 
		$mail->Timeout=30;
		$mail->AddAddress($email);
		$mail->Subject = "Your password for SoCal APP has been changed";
		$mail->Body = $content;
		$mail->AltBody = htmlentities($content);
		$exito = $mail->Send();

/*  $intentos=1; 
  while ((!$exito) && ($intentos < 2)) {
	sleep(2);
     	$exito = $mail->Send();
  		$intentos=$intentos+1;	
   }*/
   if(!$mail->Send())
{
   echo "Message could not be sent. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}
		
		//mail($email,"Your password for SoCal APP has been changed",$content,$headers); 

  		//$url="../modules/user/admin/face/mail2.html";
  		//$content=$API->template($url,$info);
  		//$API->sendMail($email,"Su password de acceso ha sido reiniciado",$content);
		$API->goto("?go=user");

	
}

function user_check(){

	$API= new API();
	$API->moduleName("user");
	$API->setWHERE("Validar usuario");

	$js=file_get_contents("../modules/user/admin/extra/show.txt");
	$API->setJS($js);

	$query=mysql_query("SELECT * FROM `users` WHERE `status`='no'")or die(mysql_error());
	$num=mysql_num_rows($query);
	
	if($num>0){
		while($dato=mysql_fetch_array($query)){
			$user['data1']=$dato['username'];
			$user['data2']=$dato['email'];
			$user['data3']=date(_TIMEFORMAT,$dato['register_date']);
			$user['data4']=$dato['status'];
			$user['data5']=$dato['name'];
			$user['data6']=$dato['surname'];
		
			$user['url2']="?go=user&do=validate&id=".$dato['id'];
			$user['url1']="?go=user&do=edit&email=".$dato['email']."&id=".$dato['id']."";
			$user['url3']='?go=user&do=delete&id='.$dato['id'].'&email='.$dato['email'];
			$url="../modules/user/admin/face/rows2.html";
			$content['rows'].=$API->template($url,$user);
		}
		$url="../modules/user/admin/face/table2.html";
		$table=$API->template($url,$content);
		$_SESSION['content']=$table;
	}else{	
		$table=$API->adminWarning("no hay usuarios para validar");
		
		$_SESSION['content']=$table;
	}
	$API->printadmin();

}

function user_validate($id){
	$API= new API();
	$busqueda="UPDATE `users` SET `status` = 'si' WHERE `id` ='".$id."'";
	mysql_query($busqueda)or die(mysql_error());
	
	$query=mysql_query("SELECT * FROM `users` WHERE `id`='".$id."'")or die(mysql_error());

		while($dato=mysql_fetch_array($query)){
			$name=$dato['name'];
			$email=$dato['email'];
		}	
		$mail = new phpmailer();

	
		 $mail->PluginDir = "../source/class/";
  			 $mail->Mailer = "smtp";

			 $mail->Host = "mail.urbansecurity.es";
 			 $mail->SMTPAuth = true;
			 $mail->Username = "noresponder@urbansecurity.es"; 
  			 $mail->Password = "adecu13";
			 $mail->From = "noresponder@urbansecurity.es";
  			 $mail->FromName = "noresponder@urbansecurity.es";
             $mail->Timeout=20;
  			 $mail->ClearAddresses();
  			 $mail->AddAddress($email);
  			 $mail->Subject = "Alta Urbansecurity";
  			 
  			 $url="../modules/user/admin/face/mail3.html";
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
			

	
	$API->goto("?go=user");

}
?>