<?php
    /**
     * CLASS API
     *
     * <code>
     * require_once 'Accumulator.php';
     *
     * $acc = new Accumulator( 10 );
     *
     * $acc->addNum( 20 );
     *
     * echo $acc->getTotal();
     * </code>
     *
     * @author Glen Scott <glen_scott@yahoo.co.uk>
     */
class API{
	     /**
         * Create an instance, optionally setting a starting point
         *
         * @param int $initial an integer that represents the number
         *                     to start counting from
         * @access public
         */
	function security(){
		if(!isset($_SESSION['auth_user_name'])){
			//$_SESSION['login_last_url']=$referer;
			echo "<script>location.href='http://"._webURL."?go=login'</script>";			
		}
	}
	//obtener un dato de un $_POST
	function getData($name){
		return $_SESSION['system']->Data($name);
	}
	//fijamos un dato para ser utilizado en otra pagina
	function setData($name,$data){
		return $_SESSION['system']->setData($name,$data);
	}
	function setLogin($login){
		$_SESSION['templateLogin']=$login;
	}
	//funcion para imprimir en la pagina
	function printweb($content){
		return $_SESSION['print']->web($content);
	}
	//funcion para imprimir el administrador
	function printadmin(){
		return $_SESSION['print']->admin();
	}	
	function printlogin(){
		return $_SESSION['print']->login();
	}
	function printerror($error){
		return $_SESSION['print']->showerror($error);
	}		
	function mailADMIN(){
		return _mailADMIN;
	}
	//funcion template a partir de una url y un array de datos transforma el fichero enrutado
	function template($url,$dato){
		$url2=_FRONTENDDIR._FRONTENDNAME.'views/'.$url.'.inc';
		return $_SESSION['print']->template($url2,$dato);
	}

	function templateAdmin($url,$dato){
		$url2="../"._BACKENDDIR._BACKENDNAME.'views/'.$url.'.inc';
		return $_SESSION['print']->template($url2,$dato);
	}
	
	function replacetags($template , $config){
		$s=file_get_contents($template);
		foreach ($config as $i=>$v) {
			$s=str_replace("{".$i."}",$v,$s);
		}
		
		return $s;	
	
	}
	function cutString($texto,$num){
		return $_SESSION['class_format']->cutString($texto,$num);
	}
	//para transformar un titulo a url amigable
	function friendlyURL($url){
		return $_SESSION['class_format']->friendlyURL($url);
	}
	//fija un javascript a la pagina
	function setJS($js){
		$_SESSION['templateJS']=$js;
	}
	//fijamos el nombre del modulo
	function setWHERE($where){
		$_SESSION['template_whereIam']=$where;
	}
	//fijamos el titulo de la pagina
	function setTITLE($title){
		$_SESSION['template_title']=" | ".$title;
	}
	
	function error($info){
		$error='<div id="error"><div id="error_info">'.$info.'</div></div>';
		echo $error;
	}
	//imprimir un error
	function adminWarning($text){
		$dato['text']=$text;
		if(file_exists("../template/admin/warning.html")){
			$s=file_get_contents("../template/admin/warning.html");
			foreach ($dato as $i=>$v) {
				$s=str_replace("{".$i."}",$v,$s);
			}
		}
		return $s;
	}
	//enviar un mail
	function sendMail($email,$subject,$content){
		$mail = new phpmailer();
		//$mail->PluginDir = "../source/class/";
  		$mail->Mailer = "smtp";
		$mail->Host = _mailHOST;
 		$mail->SMTPAuth = true;
		$mail->Username = _mailUSER; 
  		$mail->Password = _mailPASS;
		$mail->From = _mailNAME;
  		$mail->FromName = _mailName;
        $mail->Timeout=20;
  		$mail->ClearAddresses();
  		$mail->AddAddress($email);
  		$mail->Subject = $subject;
  		$mail->Body = $content;
		
		$exito = $mail->Send();
 		$intentos=1; 
 		
 			 while ((!$exito) && ($intentos < 2)) {
				sleep(2);
     			$exito = $mail->Send();
     			$intentos++;	
			}

	}
	
	function eMail($email,$subject,$content){
		
    	mail($email,$subject,$content); 
	}

	//devuelve la url al template
	function geturlpath(){
		$url=$GLOBALS['_templateDir'].$GLOBALS['_templateName'];
		return $url; 
	}		
	function CSS($css){
		$_SESSION['systemCSS']=$css;
	}
	//fijamos el nombre del modulo
	function moduleName($name){
		$_SESSION['moduleName']=$name;
	}
	//te devuelve a la pagina de la que procedes
	function goback(){
		$go=$_SERVER['HTTP_REFERER'];
		echo "<script>location.href='".$go."'</script>";
	}
	
	/*
	function goto($go){
		echo "<script>location.href='".$go."'</script>";
	}*/
	
	//obtenemos el HTML de la ruta
	function getHTML($dir){
		$content="";
		if(file_exists($dir)){
			$content=file_get_contents($dir);
		}
		$url="modules/".$_SESSION['moduleName']."/face/";
		//echo $url.$dir;
		if(file_exists($url.$dir)){
			$content=file_get_contents($url.$dir);
		}
		$url="../modules/".$_SESSION['moduleName']."/admin/face/";
		if(file_exists($url.$dir)){
			$content=file_get_contents($url.$dir);
		}
		return $content;
	}

	//name    : nombre del modulo 
	//content : contenido del modulo
	function addmodule($name,$content){
		
		
		if($_SESSION['nmodule'][$name]!=1){
		
				if(!isset($_SESSION['num_module'])){
					$_SESSION['num_module']=0;
				}else{
					$_SESSION['num_module']++;
				}
				
				$x=$_SESSION['num_module'];
				
				$_SESSION['nmodule'][$name]=1;
				$_SESSION['mname'][$x]=$name;
				$_SESSION['mcont'][$name]=$content;
		}
				
		
		//echo $_SESSION['num_module'];
		
		//for($i=0;$i<$_SESSION['num_module'];$i++){
			
			//echo $_SESSION['module'][$_SESSION['num_module']]['name'].' '.$_SESSION['module'][$_SESSION['num_module']]['content'];
		//}
	}
}
?>