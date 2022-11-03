<?php
function homeAmbassador(){
$API= new API();
		
	$usado=0;
	$query=mysql_query("SELECT * FROM `BA_items` WHERE id_BA='".$_SESSION['login_id']."'")or die(mysql_error());
	$url="BA_items";
	if(mysql_num_rows($query)>0){
		while($dato=mysql_fetch_array($query)){	
			$items['name']='<b>'.$dato['name']." - ".$dato['size'].'</b>';
			$items['reference']=$dato['reference'];
			$items['price']=$dato['value']." €";
			$data['items'].=$API->template($url,$items);
			$usado+=$dato['value'];
		}
	}else{
		$data['items']='<div style="padding:20px;">No se ha realizado ninguna compra</div>';
	}

	
	$query=mysql_query("SELECT * FROM `BA_user` WHERE id='".$_SESSION['login_id']."'")or die(mysql_error());
	$url="BA_profile";
	while($dato=mysql_fetch_array($query)){	
		$data['nombre']=utf8_encode($dato['nombre']." ".$dato['apellidos']);
		$query2=mysql_query("SELECT SUM(prize) FROM `BA_message` WHERE id_BA='".$dato['id']."' AND type='2'")or die(mysql_error());
			while($dato2=mysql_fetch_array($query2)){	
				$addbudget=$dato2['SUM(prize)'];
			}
		$data['budget']=$dato['budget']+$addbudget;
		$data['id']=$dato['id'];
		$data['budget_used_percentage']=($usado/($dato['budget']+$addbudget))*100;
		
		$data['budget_used']=$usado."€";
		$data['budget_ready']=($dato['budget']+$addbudget)-$usado."€";
		$data['budget_ready_percentage']=((($dato['budget']+$addbudget)-$usado)/($dato['budget']+$addbudget))*100;
		
		if($data['budget_ready']<0){
			$data['budget_ready']="";
			$data['budget_ready_percentage']=0;
			
			$data['budget_used_percentage']=100;
		}
		
		
		$data['tarjetas']=0;
		$card=mysql_query("SELECT * FROM `BA_message` WHERE id_BA='".$dato['id']."' AND type='3'")or die(mysql_error());
		$data['tarjetas']=mysql_num_rows($card);
		
		$data['message']="";
		$prize=mysql_query("SELECT * FROM `BA_message` WHERE id_BA='".$dato['id']."' AND type='2'")or die(mysql_error());
		while($message=mysql_fetch_array($prize)){	
			$data['message']='<div class="notification green">Felicidades!, Has recibido un premio por: '.$message['message']." de ".$message['prize'].'€</div>';
		}
		
		$data['propuestas']="";
		$propuesta=mysql_query("SELECT * FROM `BA_message` WHERE id_BA='".$dato['id']."' AND type='1'")or die(mysql_error());
		if(mysql_num_rows($propuesta)>0){
		$data['propuestas']='<p>
		<strong>Propuestas</strong>
		</p>
			<div id="profile-tarjetas">';
			
		
			while($dato3=mysql_fetch_array($propuesta)){	
				
				$data['propuestas'].='
				<b>FECHA: '.date("d-m-Y",$dato3['date']).'</b><p>'.
									nl2br($dato3['message']).'</p>';
			}
		$data['propuestas'].='</div>';
		}
	}
		
	$content=$API->template($url,$data);
	
	return $content;

}

function homeAdmin(){
	$API= new API();	
	$usado=0;
	
	$query=mysql_query("SELECT * FROM `BA_user` WHERE `admin`=0")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){	
			$query2=mysql_query("SELECT SUM(value) FROM `BA_items` WHERE id_BA='".$dato['id']."'")or die(mysql_error());
			$url="BA_profile_admin_player";
			while($dato2=mysql_fetch_array($query2)){	
				$usado=$dato2['SUM(value)'];
			}	
			
			
		$query3=mysql_query("SELECT * FROM `BA_message` WHERE id_BA='".$_dato['id']."' AND type='3'")or die(mysql_error());
		$data['tarjetas']=mysql_num_rows($query3);
			
			
			
		$data['url']="?go=profile&id=".$dato['id'];
		if($dato['id']>1)
			$data['id']=$dato['id'];
		else
			$data['id']="no-pic";
			
			
		$data['nombre']=$dato['nombre']." ".$dato['apellidos'];
		$data['budget']=$dato['budget'];
			$query2=mysql_query("SELECT SUM(prize) FROM `BA_message` WHERE id_BA='".$dato['id']."' AND type='2'")or die(mysql_error());
			while($dato2=mysql_fetch_array($query2)){	
				$addbudget=$dato2['SUM(prize)'];
			}
		$data['budget_used_percentage']=($usado/($dato['budget']+$addbudget))*100;
		$data['budget_used']=number_format($usado,2,',','.')."€";
		$data['budget_ready']=number_format(($dato['budget']+$addbudget)-$usado,2,',','.')."€";
		$data['budget_ready_percentage']=((($dato['budget']+$addbudget)-$usado)/($dato['budget']+$addbudget))*100;
		
		if($data['budget_ready']<0){
			$data['budget_ready']="";
			$data['budget_ready_percentage']=0;
			
			$data['budget_used_percentage']=100;
		}
		
		$tags['profiles'].=$API->template($url,$data);
	}
	
	$url="BA_profile_admin";
	$content=$API->template($url,$tags);
	
	return $content;

}

function home(){
	$API= new API();
	if($_SESSION['login_level']=="1"){
		$API->printweb(homeAdmin());
		
	}else{
		$API->printweb(homeAmbassador());
	}
}

function homeProfile($id){
$API= new API();

	$data['errorPrize']="";
	if($_GET['error']!="")
		$data['errorPrize']='<div class="error"><b>ERROR:</b> '.$_GET['error'].'</div>';
		
	
	$data['error']="";
	if($_GET['submit']=="on"){
		if(($_POST['name']=="")||($_POST['reference']=="")||($_POST['value']=="")){
			$data['error']='<div class="error">ERROR: Completa todos los campos</div>';
		}else{
			mysql_query("INSERT INTO `BA_items` (name, reference, value, id_BA, size) 
											VALUES ('".$_POST['name']."', '".$_POST['reference']."','".$_POST['value']."','".$_GET['id']."','".$_POST['size']."')") or die (mysql_error());
			$data['name']="";
			$data['reference']="";
			$data['value']="";
			$data['size']="";									
		}
	}else if($_GET['submit']=='edit'){
		if(($_POST['name']=="")||($_POST['reference']=="")||($_POST['value']=="")){
			echo '<script>window.location = "/embajadores/?go=profile&edit=item&id_item='.$_POST['id_item'].'&id='.$_GET['id'].'";</script>';
		}else{
			mysql_query("UPDATE `BA_items` SET name='".$_POST['name']."',reference='".$_POST['reference']."',value='".$_POST['value']."',size='".$_POST['size']."' WHERE id='".$_POST['id_item']."'") or mysql_error();
			$data['name']="";
			$data['reference']="";
			$data['value']="";
			$data['size']="";									
		}
	}else if($_GET['submit']=='delete'){
			mysql_query("DELETE FROM `BA_items` WHERE id='".$_GET['id_item']."'") or mysql_error();
			echo '<script>window.location = "/embajadores/?go=profile&id='.$_GET['id'].'";</script>';
	}else if($_GET['submit']=='deleteprize'){
			mysql_query("DELETE FROM `BA_message` WHERE id='".$_GET['id_item']."'") or mysql_error();
			echo '<script>window.location = "/embajadores/?go=profile&id='.$_GET['id'].'";</script>';
	
	}else if($_GET['submit']=='prize'){
			switch($_POST['type']){
			case '1':
				if($_POST['message']!=""){
					 mysql_query("INSERT INTO `BA_message` (id_BA, message, type, date) 
					VALUES ('".$_GET['id']."', '".$_POST['message']."','".$_POST['type']."','".time()."')") or die (mysql_error());
					echo '<script>window.location = "/embajadores/?go=profile&id='.$_GET['id'].'";</script>';
				}else{
					$error="Tienes que escribir una propuesta";
					echo '<script>window.location = "/embajadores/?go=profile&id='.$_GET['id'].'&error='.$error.'";</script>';
				}
				
			break;
			case '2':
				
			 	if($_POST['reason']!=""){
					 mysql_query("INSERT INTO `BA_message` (id_BA, message, type, date, prize) 
					VALUES ('".$_GET['id']."', '".$_POST['reason']."','".$_POST['type']."','".time()."','".$_POST['premio']."')") or die (mysql_error());
					echo '<script>window.location = "/embajadores/?go=profile&id='.$_GET['id'].'";</script>';
				}else{
					$error="Tienes que definir una razón del premio";
					echo '<script>window.location = "/embajadores/?go=profile&id='.$_GET['id'].'&error='.$error.'";</script>';
				}
			break;
			case '3':
				if(($_POST['TvalueReal']!="")&&($_POST['TvalueDiscount']!="")){
					 mysql_query("INSERT INTO `BA_message` (id_BA, TvalueReal, TvalueDiscount, type, date) 
					VALUES ('".$_GET['id']."', '".$_POST['TvalueReal']."', '".$_POST['TvalueDiscount']."','".$_POST['type']."','".time()."')") 
					or die (mysql_error());
					echo '<script>window.location = "/embajadores/?go=profile&id='.$_GET['id'].'";</script>';
				}else{
					$error="Tienes que rellenar tanto el <b>total gastado</b> como el <b>total sin descuento</b>";
					echo '<script>window.location = "/embajadores/?go=profile&id='.$_GET['id'].'&error='.$error.'";</script>';
				}
			break;
			}
	}else{
			$data['name']="";
			$data['reference']="";
			$data['value']="";
			$data['size']="";		
	}
	
	$usado=0;
	$query=mysql_query("SELECT * FROM `BA_items` WHERE id_BA='".$_GET['id']."'")or die(mysql_error());
	$url="BA_items";
	if(mysql_num_rows($query)>0){
		while($dato=mysql_fetch_array($query)){	
			$items['name']='<a href="?go=profile&edit=item&id_item='.$dato['id'].'&id='.$_GET['id'].'" >'.$dato['name']." - ".$dato['size'].'</a>';
			$items['reference']=$dato['reference'];
			$items['price']=$dato['value']." €".' <a href="?go=profile&submit=delete&id_item='.$dato['id'].'&id='.$_GET['id'].'" class="button red small" style="padding:5px; color:#FFF; float:right;">x</a>';
			$data['items'].=$API->template($url,$items);
			if(($_GET['edit']=='item')&&($_GET['id_item']==$dato['id'])){
				if(isset($_POST['name'])){
					$items['name']=$_POST['name'];
					$items['reference']=$_POST['reference'];
					$items['value']=$_POST['value'];
					$items['size']=$_POST['size'];
					$items['id']=$_POST['id_item'];
				}else{
					$items['id_BA']=$_GET['id'];
					$items['id']=$_GET['id_item'];
					$items['name']=$dato['name'];
					$items['value']=$dato['value'];
					$items['size']=$dato['size'];
				}
				$url2="BA_items_edit";
				$data['items'].=$API->template($url2,$items);
			}
			$usado+=$dato['value'];
		}
	}else{
		$data['items']='<div style="padding:20px;">No se ha realizado ninguna compra</div>';
	}


	$query=mysql_query("SELECT * FROM `BA_message` WHERE id_BA='".$_GET['id']."' AND type='3'")or die(mysql_error());
	$url="BA_items";
	$items['name']="<b>Fecha</b>";
	$items['reference']="<b>Total ticket</b>";
	$items['price']="<b>Total Pagado</b>";
	$data['card'].=$API->template($url,$items);
	$sumprice=0;
	$sumreference=0;
	if(mysql_num_rows($query)>0){
		while($dato=mysql_fetch_array($query)){	
			$items['name']=date("d-m-Y",$dato['date']);
			$items['reference']=$dato['TvalueReal']."€";
			$sumreference+=$dato['TvalueReal'];
			$sumprice+=$dato['TvalueDiscount'];
			$items['price']=$dato['TvalueDiscount']." €".' <a href="?go=profile&submit=deleteprize&id_item='.$dato['id'].'&id='.$_GET['id'].'" class="button red small" style="padding:5px; color:#FFF; float:right;">x</a>';
			$data['card'].=$API->template($url,$items);

		}
		$items['name']="<b>TOTAL</b>";
		$items['reference']='<b>'.$sumreference.'€</b>';
		$items['price']='<b>'.$sumprice.'€</b>';
		$data['card'].=$API->template($url,$items);
	}else{
		$data['card']='<div style="padding:20px;">No tiene tarjetas todavía</div>';
	}

/*********************************/
	$query=mysql_query("SELECT * FROM `BA_message` WHERE id_BA='".$_GET['id']."' AND type='2'")or die(mysql_error());
	$url="BA_items";
	$items['name']="<b>Fecha</b>";
	$items['reference']="<b>Total ticket</b>";
	$items['price']="<b>Total Pagado</b>";
	$data['premios'].=$API->template($url,$items);
	if(mysql_num_rows($query)>0){
		while($dato=mysql_fetch_array($query)){	
			$items['reference']=date("d-m-Y",$dato['date']);
			$items['name']=$dato['message'];
			$items['price']=$dato['prize'	]." €".' <a href="?go=profile&submit=deleteprize&id_item='.$dato['id'].'&id='.$_GET['id'].'" class="button red small" style="padding:5px; color:#FFF; float:right;">x</a>';
			$data['premios'].=$API->template($url,$items);

		}
	}else{
		$data['premios']='<div style="padding:20px;">No tiene premios todavía</div>';
	}
/*********************************/	
/*********************************/
	$query=mysql_query("SELECT * FROM `BA_message` WHERE id_BA='".$_GET['id']."' AND type='1'")or die(mysql_error());
	
	if(mysql_num_rows($query)>0){
		while($dato=mysql_fetch_array($query)){	
			$data['propuestas'].='<div class="profile-propuestas">
				<b>FECHA</b>: '.date('d-m-Y',$dato['date']).'<a href="#" class="button green small" style="padding:5px; color:#FFF; float:right; margin-left:5px;">&#10003;</a> 
				<a href="?go=profile&submit=deleteprize&id_item='.$dato['id'].'&id='.$_GET['id'].'" class="button red small" style="padding:6px; color:#FFF; float:right;">x</a><p>'.nl2br($dato['message'])."</p></div>";

		}
	}else{
		$data['propuestas']='<div style="padding:20px;">No tienes propuestas todavía</div>';
	}
/*********************************/	
	$query=mysql_query("SELECT * FROM `BA_user` WHERE id='".$_GET['id']."'")or die(mysql_error());
	$url="BA_profile_edit";
	while($dato=mysql_fetch_array($query)){	
		$data['nombre']=utf8_encode($dato['nombre']." ".$dato['apellidos']);
		$data['budget']=$dato['budget'];
			$query2=mysql_query("SELECT SUM(prize) FROM `BA_message` WHERE id_BA='".$_GET['id']."' AND type='2'")or die(mysql_error());
			while($dato2=mysql_fetch_array($query2)){	
				$addbudget=$dato2['SUM(prize)'];
			}
			
		$data['budget_used_percentage']=($usado/($dato['budget']+$addbudget))*100;
		$data['budget_used']=$usado;
		$data['id']=$dato['id'];
		$data['budget_ready']=($dato['budget']+$addbudget)-$usado;
		$data['budget_ready_percentage']=((($dato['budget']+$addbudget)-$usado)/($dato['budget']+$addbudget))*100;
		if($data['budget_ready']<0){
			$data['budget_ready']="";
			$data['budget_ready_percentage']=0;
			
			$data['budget_used_percentage']=100;
		}
	}
	
	
	$content=$API->template($url,$data);
	
	$API->printweb($content);

}


function ganaderia(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Ganadería";
	$col['col1']=file_get_contents("modules/home/face/info_ganaderia_a.inc");
	$col['col2']=file_get_contents("modules/home/face/info_ganaderia_b.inc");
	
	$url="modules/home/face/info_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}


function sustrato(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Sustrato fertilizado";
	$col['col1']=file_get_contents("modules/home/face/info_sustrato_a.inc");
	$col['col2']=file_get_contents("modules/home/face/info_sustrato_b.inc");
	
	$url="modules/home/face/info_sustrato_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}

function biocombustible(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Biogás";
	$col['col1']=file_get_contents("modules/home/face/info_biogas_a.inc");
	$col['col2']=file_get_contents("modules/home/face/info_biogas_b.inc");
	
	$url="modules/home/face/info_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}
function formacion(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Formación";
	$col['col1']=file_get_contents("modules/home/face/info_formacion_a.inc");
	$col['col2']=file_get_contents("modules/home/face/info_formacion_b.inc");
	
	$url="modules/home/face/info_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}
function produccion(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Producción";
	$col['col1']=file_get_contents("modules/home/face/info_formacion_a.inc");
	$col['col2']=file_get_contents("modules/home/face/info_formacion_b.inc");
	
	$url="modules/home/face/info_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}
function consultoria(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Consultoría";
	$col['col1']=file_get_contents("modules/home/face/info_consultoria_a.inc");
	$col['col2']=file_get_contents("modules/home/face/info_consultoria_b.inc");
	
	$url="modules/home/face/info_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}
function visitasguiadas(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Visitas guiadas";
	$col['col1']=file_get_contents("modules/home/face/about_visit_a.inc");
	$col['col2']=file_get_contents("modules/home/face/about_visit_b.inc");
	
	$url="modules/home/face/info_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}
function quienes(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Quíenes somos";
	$col['col1']=file_get_contents("modules/home/face/about_quienes_a.inc");
	$col['col2']=file_get_contents("modules/home/face/about_quienes_b.inc");
	
	$url="modules/home/face/about_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}
function where(){
	$API= new API();
	$API->moduleName("home");
	$col['content']=file_get_contents("modules/home/face/where.inc");

	
	$url="modules/home/face/contact_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}
function suscribe($mail){	
	$API= new API();
	$API->moduleName("home");
	if (preg_match(
'/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',
$mail)) {
		mysql_query("INSERT INTO `mail_list` ( `mail` , `date`)
				VALUES ('".$mail."', '".time()."');")or die(mysql_error());
	//Declarate the necessary variables
	$mail_to=_mailMAIL;
	$mail_from="notificacion@gruposanramon.com";
	$mail_sub="Nuevo correo - gruposanramon.com";
	$mail_mesg="<html><body><p>Notificación de nuevo correo</p><p>Nuevo correo añadido a la base de datos: <strong>".$mail."</strong></p></body></html>";

	//Check for success/failure of delivery
    $email_recipient = $email;
    $email_sender = $nombre;
    $email_content_type = "text/html; charset=us-ascii";
    $email_client = "PHP/" . phpversion();
    $email_header = "From: notificacion@gruposanramon.com\r\n";
    $email_header .= "Content-type: " . $email_content_type . "\r\n";
    $email_header .= "X-Mailer: " . $email_client . "\r\n";
	
	mail($mail_to,$mail_sub,$mail_mesg,$email_header);
	$API->goto("exito/");
	
	}else{
	$API->goto("fallo/");
	}
}
function suscribe_ok(){
	$API= new API();
	$API->moduleName("error");
	
	$content='<div class="error">
	<strong>Suscripción realizada con éxito</strong> : su correo ha sido registrado, a partir de este momento y hasta que usted desee formará parte de la lista de correo de grupo San Ramón. Muchas gracias</div>';
	$API->printweb($content);
}
function visit(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Visítanos";
	$col['col1']=file_get_contents("modules/home/face/about_visit_a.inc");
	$col['col2']=file_get_contents("modules/home/face/about_visit_b.inc");
	
	$url="modules/home/face/about_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}
function events(){
	$API= new API();
	$API->moduleName("home");
	
	$col['title']="Eventos";
	$col['col1']=file_get_contents("modules/home/face/info_events_a.inc");
	$col['col2']=file_get_contents("modules/home/face/info_events_b.inc");
	
	$url="modules/home/face/info_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}

function suscribe_success(){
	$API= new API();
	$API->moduleName("home");
	$content=file_get_contents("modules/home/face/suscribe_success.inc");
	$API->printweb($content);
}

function suscribe_fail(){
	$API= new API();
	$API->moduleName("home");
	$content=file_get_contents("modules/home/face/suscribe_fail.inc");
	$API->printweb($content);
}

function contact(){
	$API= new API();
	$API->moduleName("home");
	$col['content']=file_get_contents("modules/home/face/contact.inc");
	$url="modules/home/face/contact_col2.inc";
	$content=$API->replacetags($url,$col);
	$API->printweb($content);
}

function contact_send(){
	$API= new API();
	$API->moduleName("home");
	
	$email=$_POST['email'];
	$nombre = $_POST['nombre'];
	$comentario = $_POST['comentario'];

$to = _mailMAIL;
//Check whether the submission is made


if((!$email)||(!$nombre)){
	//echo $email." ".$nombre;
	$API->goto("?go=contact");
}else{
	//Declarate the necessary variables
	$mail_to=_mailMAIL;
	$mail_from=$email;
	$mail_sub="Contacto - gruposanramon.com";
	$mail_mesg="<html><body><p>Enviado por : $nombre ($email)</p>".nl2br($comentario)."</body></html>";

	//Check for success/failure of delivery
   $email_recipient = $email;
   $email_sender = $nombre;
   $email_return_to = $email;
   $email_content_type = "text/html; charset=us-ascii";
   $email_client = "PHP/" . phpversion();
   $email_header = "From: " . $email . "\r\n";
   $email_header .= "Reply-To: " . $email_return_to . "\r\n";
   $email_header .= "Return-Path: " . $email_return_to . "\r\n";
   $email_header .= "Content-type: " . $email_content_type . "\r\n";
   $email_header .= "X-Mailer: " . $email_client . "\r\n";
	
	mail($mail_to,$mail_sub,$mail_mesg,$email_header);
	
	$API->goto("?go=contact&do=success");
}



}

function contact_success(){
	$API= new API();
	$API->moduleName("home");
	
	$content=$API->getHTML("contact_success.inc");
	$API->printweb($content);
}
?>