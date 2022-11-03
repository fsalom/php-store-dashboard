<?php
session_start();
include_once("../../core/class/class.API.php");
include_once("../../core/config.php");
include_once("../../core/function/IConnect.php");

function replacetags($template , $config){
		$s=file_get_contents($template);
		foreach ($config as $i=>$v) {
			$s=str_replace("{".$i."}",$v,$s);
		}
		
		return $s;	
}
	
	$API = new API();

   	$username=($_REQUEST['username']);
	$web=$_REQUEST['web'];
    $email=$_REQUEST['email'];
	$comment=$_REQUEST['comment'];
	$id=$_REQUEST['id'];

        $resp = array();
        $username = trim($username);
        //comprobamos que no hay ningún campo obligatorio vacio
        if (($username=="undefined")||($email=="undefined")||($comment=="undefined")) {
            $resp = array('ok' => false, 'msg' => '<b style="color:#990000">Por favor rellene todos los datos obligatorios *</b>');
        //comprobamos que la dirección de correo es valida
        }else if(!preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$email)){
			$resp = array('ok' => false, 'msg' => '<b style="color:#990000">Por favor debe de introducir un email valido</b>');
        //cometario valido 
        }else {
        
        	$info['email']=$email;
        	
        	if($web=="undefined"){
        	
        		$web="";
        	
        	}else{
        	
        		if(substr($web, 0, 7)=="http://"){
        			$user='<a href="'.$web.'">'.$username.'</a>';
        		}else{
        			$user='<a href="http://'.$web.'">'.$username.'</a>';
        			$web='http://'.$web;
        		}
        	
        	}
        	
        	$info['author']=$username;
        	$info['web']=$web; 
     		
     		//imagen de gravatar
     		
      		$grvMail = $email; 
      		$default = _webURL."/modules/news/img/spacer.png"; 
      		$grvSize = 40;
      	
      		$img= "http://www.gravatar.com/avatar.php"; 
      		$img.= "?gravatar_id=".md5($grvMail);  
      		$img.= "&default=".urlencode($default);
      		$img.= "&size=".$grvSize;
			
			$info['image']=$img;     	
        	$info['date']=date("d-m-Y H:i",time());
        	
        	//Inserción del comentario con prevención para SQL_INJECTION
        	
        	if(get_magic_quotes_gpc()) {
            	$username     = stripslashes($username);
            	$web = stripslashes($web);
            	$comment = stripslashes($comment);
       		}
       		
            $comment=htmlentities($comment);
            $comment=html_entity_decode($comment);
            $info['comment']=nl2br($comment);
            
            //Obtenemos la ip
            
            if (!empty($_SERVER['HTTP_CLIENT_IP']))
            
    			$ip=$_SERVER['HTTP_CLIENT_IP'];
  			
  			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			
			    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		    
		    else
    		
    			$ip=$_SERVER['REMOTE_ADDR']; 			
            
            $consulta = sprintf("INSERT INTO `comments` ( `id_new` , `author` , `email` , `comment` , `web`,`date`,`ip`) VALUES ('%d', '%s', '%s', '%s','%s', '%d','%s')",
                    mysql_real_escape_string($id),
                    mysql_real_escape_string($username),
                    mysql_real_escape_string($email),
                    mysql_real_escape_string($comment),                   
                    mysql_real_escape_string($info['web']),
                    time(),
                    $ip);
        
			 mysql_query($consulta);		        	
        	
     		/*
        	$content = "Nuevo comentario enviado\n";
   			$content .="Nombre : " . $username . "\n";
    		$content .="Email : " . $email . "\n";
    		$content .="Comentario: " .$comment . "\n";
			$content .="Web : " . $web . "\n";
			$content .="URL : ".$GLOBALS['_websiteName']."noticia/".$id."/comentario";
        	$API->eMail(_mailADMIN,"Nuevo comentario",$content);
        	*/
        	
        	$url="../../"._FRONTENDDIR._FRONTENDNAME."views/news-comment.inc";
        	//$text=file_get_contents($url);
            $text=replacetags($url,$info);
        	
            $resp = array("ok" => true, "msg" => $text);
        }

	if (@$_REQUEST['do'] == 'check' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode($resp);
        exit;
    }  
?>