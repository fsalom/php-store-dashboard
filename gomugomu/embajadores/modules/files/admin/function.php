<?php
function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
   
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
   
    $bytes /= pow(1024, $pow); 
   
    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

function files_show(){
	$API= new API();
	$API->moduleName("files");
	$API->setWHERE("Listado de Documentos");
	
	$js=file_get_contents("../modules/files/admin/extra/show.js");

	$API->setJS($js);

	
	$query=mysql_query("SELECT * FROM `files` order by idgroup")or die(mysql_error());
	if(mysql_num_rows($query)>0){
		while($dato=mysql_fetch_array($query)){
			$file['id']=$dato['id'];
			$file['data1']=$dato['filename'];
			$file['data5']=formatBytes(filesize($dato['path']));
			$file['data3']=$dato['user'];
			$file['data4']=date(_TIMEFORMAT,$dato['date_upload']);
			$file['data2']=end(explode(".", basename( $dato['path'])));
			$query2=mysql_query("SELECT * FROM `files_group` where id='".$dato['idgroup']."'")or die(mysql_error());
			while($dato2=mysql_fetch_array($query2)){
				$file['data6']=$dato2['groupname'];
			}
			
			$url="../modules/files/admin/face/rows.html";
			$content['rows'].=$API->template($url,$file);
		}
		$url="../modules/files/admin/face/table.html";
		$table=$API->template($url,$content);
	}else{
		$table="No hay ficheros todavía";
	}

	$_SESSION['content']=$table;
	$API->printadmin();
}

function files_list($selected){
	$query=mysql_query("SELECT * FROM `files_group`")or die(mysql_error());
	
	if(mysql_num_rows($query)>0){
	$content='<select name="list">';
	
		while($dato=mysql_fetch_array($query)){
			if($dato['id']==$selected){
				$content.='<option value="'.$dato['id'].'" selected="selected">"'.$dato['groupname'].'"</option>';
			}else{
				$content.='<option value="'.$dato['id'].'">"'.$dato['groupname'].'"</option>';
			}
		}	
		$content.="</select>";
	}else{
		$content='No se han creado grupos nuevos , añada uno nuevo <span class="template-links"><a href="?go=files&do=newg">AQUI</a></span>';
	}
	return $content;
	
}

function files_new_group(){
	$API= new API();
	$API->moduleName("files");
	$API->setWHERE("Nuevo Grupo de Ficheros");

	$status=$_GET['status'];
	if($status==1){
		mysql_query("INSERT INTO `files_group` ( `groupname` , `user` , `date`)
		VALUES ('".$_POST['nombre']."','".$_SESSION['login_username']."','".time()."');")or die(mysql_error());
		$API->goto("?go=files&do=showg");
	}else if($status==2){
		$API->setWHERE("Editar Grupo de Ficheros");
		$query=mysql_query("SELECT * FROM `files_group` WHERE id='".$_GET['id']."'")or die(mysql_error());
		while($dato=mysql_fetch_array($query)){
			$file['groupname']=$dato['groupname'];
		}
		$file['action']="index.php?go=files&do=newg&status=4&id=".$_GET['id']."";
		$url="../modules/files/admin/face/newg.html";
		$content=$API->template($url,$file);
	}else if($status==3){
		mysql_query("DELETE FROM `files_group` WHERE `id` = '".$_GET['id']."'");
		$API->goto("?go=files&do=showg");		
	}else if($status==4){
		$busqueda="UPDATE `files_group` SET `groupname` = '".$_POST['nombre']."' WHERE id='".$_GET['id']."'";
			mysql_query($busqueda) or die(mysql_error());
		$API->goto("?go=files&do=showg");
	}else{
		$file['groupname']="";
		$file['action']="index.php?go=files&do=newg&status=1";
		$url="../modules/files/admin/face/newg.html";
		$content=$API->template($url,$file);
	}
	
	$_SESSION['content']=$content;
	$API->printadmin();
}

function files_show_group(){
	$API= new API();
	$API->moduleName("files");
	$API->setWHERE("Listado de Grupos de Ficheros");
	
		$js=file_get_contents("../modules/files/admin/extra/show.js");

	$API->setJS($js);
	
	$query=mysql_query("SELECT * FROM `files_group`")or die(mysql_error());
	if(mysql_num_rows($query)>0){
		while($dato=mysql_fetch_array($query)){
			$file['data1']=$dato['groupname'];
			$file['id']=$dato['id'];
			$file['data2']=$dato['user'];
			$file['data3']=date(_TIMEFORMAT,$dato['date']);
			
			$url="../modules/files/admin/face/rowsg.html";
			$content['rows'].=$API->template($url,$file);
		}
		$url="../modules/files/admin/face/tableg.html";
		$table=$API->template($url,$content);
	}else{
		$table="No hay grupos todavía";
	}

	$_SESSION['content']=$table;
	$API->printadmin();

}

function files_new(){
	$API= new API();
	$API->moduleName("files");
	$API->setWHERE("Nuevo Documento");
	
	$API->setJS($js);
	$status=$_GET['status'];	
	if($status==1){

	}else if($status==2){
		$API->setWHERE("Editar Documento");
		$query=mysql_query("SELECT * FROM `files` WHERE id='".$_GET['id']."'")or die(mysql_error());
		while($dato=mysql_fetch_array($query)){
			$file['filename']=$dato['filename'];
			$file['description']=$dato['description'];
			$file['file']="fichero ya subido , no se puede editar este campo";
			$file['list']=files_list($dato['idgroup']);
			$id=$dato['id'];
		}
		$file['action']="index.php?go=files&do=new&status=4&id=".$id."";
		$url="../modules/files/admin/face/new.html";
		$table=$API->template($url,$file);
	}else if($status==3){
		mysql_query("DELETE FROM `files` WHERE `id` = '".$_GET['id']."'");
		$API->goto("?go=files");		
	}else if($status==4){
		$busqueda="UPDATE `files` SET `filename` = '".$_POST['nombre']."' , `description` = '".$_POST['description']."' , `idgroup` = '".$_POST['list']."' WHERE id='".$_GET['id']."'";
			mysql_query($busqueda) or die(mysql_error());
		$API->goto("?go=files");
	}else{
		$content['list']=files_list(0);
		$content['filename']="";
		$content['description']="";
		$content['action']="index.php?go=files&do=upload";
		$content['file']='<input type="file" name="file" id="file" />  ';
		$url="../modules/files/admin/face/new.html";
		$table=$API->template($url,$content);
	}

	
	
	
	
	
	
	//$content=$API->getHTML("new.html");
	

	$_SESSION['content']=$table;
	$API->printadmin();
}

function isAllowedExtension($fileName) {
  $allowedExtensions = array("gif", "jpg", "doc" , "pdf" , "png");
  return in_array(end(explode(".", $fileName)), $allowedExtensions);
}

function renameFile($url) {
	$url = strtolower($url);
	//Rememplazamos caracteres especiales latinos
	$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
	$repl = array('a', 'e', 'i', 'o', 'u', 'n');
	$url = str_replace ($find, $repl, $url);
	// Añaadimos los guiones
	$find = array(' ', '&', '\r\n', '\n', '+');
	$url = str_replace ($find, '', $url);
	// Eliminamos y Reemplazamos demás caracteres especiales
	$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
	$repl = array('', '-', '');
	$url = preg_replace ($find, $repl, $url);
return $url; 
}


function files_upload(){
	$API= new API();
	$API->moduleName("files");
	$API->setWHERE("Nuevo Documento");
	
	
	$upload_dir = "uploads/";


	if(!is_dir($upload_dir)){
		mkdir($upload_dir, 0777);
		chmod($upload_dir, 0777);
	}

	$file = $_FILES['file'];
	$fichero=explode(".", $_FILES['file']['name']);
	$nombre=$fichero[0];
	$extension=$fichero[1];
	
	
	//$target_path = $upload_dir . basename( $_FILES['file']['name']); 
	
	$target_path = $upload_dir . md5($nombre) .".".$extension; 
	
	
	if($file['error'] == UPLOAD_ERR_OK) {
	  if(isAllowedExtension($file['name'])){
	  			//$file['tmp_name']e
	  			
	  		
			if(move_uploaded_file($file['tmp_name'], $target_path)) {
				mysql_query("INSERT INTO `files` ( `filename` , `user`, `description` , `date_upload` , `path` , `idgroup`)
			VALUES ('".$_POST['nombre']."', '".$_SESSION['login_username']."', '".$_POST['description']."', '".time()."' ,'".$target_path."' , '".$_POST['list']."');")or die(mysql_error());
			$API->goto("?go=files");
			}else{
	    		$content= "Ha habido un error al subir el fichero , intentelo más tarde";
			}	
  		}else{
  			$content= "El fichero no es valido";
  		}
	}else{
		$content= "error subiendo el fichero";
	}
	

	$_SESSION['content']=$content;
	$API->printadmin();
}
?>