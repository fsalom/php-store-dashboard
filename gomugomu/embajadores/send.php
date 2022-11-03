<?php
	$email=$_POST['email'];
	$nombre = $_POST['nombre'];
	$comentario = $_POST['comentario'];

$to = "laura@disearte.net";
//Check whether the submission is made


if((!$email)||(!$nombre)){
	//echo $email." ".$nombre;
	?><script language="Javascript">

location.href='http://www.disearte.net/contacta.html';

</script><?php
}else{
	//Declarate the necessary variables
	$mail_to=$to;
	$mail_from=$email;
	$mail_sub="Mensaje de Disearte.net";
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
	
	if (mail($mail_to,$mail_sub,$mail_mesg,$email_header)) {
   	?>
	<script language="Javascript">

location.href='http://www.disearte.net/exito.html';

</script>
	<?php
  } else {
   echo("<p>Message delivery failed...</p>");
  }
	
	
	

}
?>