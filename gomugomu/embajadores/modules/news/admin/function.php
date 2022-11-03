<?php
function news(){
	$API= new API();
	$API->moduleName("news");
	$API->setWHERE("Listado de noticias");
	$js=file_get_contents("../modules/news/admin/extra/show.txt");

	$API->setJS($js);
	
	$table=$API->getHTML("../modules/news/admin/face/table_option.html");
	
	$query=mysql_query("SELECT * FROM `news` WHERE trash=0 ORDER BY date DESC LIMIT "._MAXNEWS."")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$news['id']=$dato['id'];
		if($dato['title']!=""){
			$news['data1']=$dato['title'];
		}else{
			$news['data1']="cita";
		}
		
		
		
		$news['data2']=mysql_num_rows(mysql_query("SELECT * FROM `comments` WHERE id_new = '".$dato['id']."'"));
		
		$news['data3']=htmlentities($dato3);
		$news['data4']=$dato['author'];
		$news['data5']=date(_TIMEFORMAT,$dato['date']);
		$news['cat']=news_category($dato['category']);
		if($dato['status']==0){
			$news['data7']="publicado";
		}else if($dato['status']==1){
			$news['data7']="borrador";
		}
		
		$news['url3']='?go=news&do=delete&id='.$dato['id'];
		$news['url2']='?go=news&do=edit&id='.$dato['id'];
		$url="../modules/news/admin/face/rows.html";
		$content['rows'].=$API->replacetags($url,$news);
	}
	
	$url="../modules/news/admin/face/table.html";
	$table.=$API->replacetags($url,$content);
	
	$_SESSION['content']=$table;
	$API->printadmin();
}

function news_category($array){
		$category=explode(";",$array);
		$i=0;
		$n = count($category);
		if($array==""){
			$info=_NEWS_CATEGORY_NONE;
		}
		while($n-1>$i){
			$query=mysql_query("SELECT * FROM `category` WHERE id=".$category[$i]."")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				if($i==0)
					$info.=$dato['name'];
				else
					$info.=' , '.$dato['name'];
					
			}
			$i++;
		}
		return $info;
}

function news_new(){
	
	$API= new API();
	$API->moduleName("news");
	$API->setWHERE("Escribir nueva entrada");

	$js=file_get_contents("../modules/news/admin/extra/form.txt");

	$API->setJS($js);
	
	$url="../modules/news/admin/face/new.html";
	echo $_GET['form'];
	echo $_POST['title'];
	//$_SESSION['panel']=file_get_contents("../modules/news/admin/face/panel.html");
	if($_GET['form']==1){
		$info['board']="";
		$info['title']=$_POST['title'];
		$info['more'] =$_POST['more'];
		$info['intro']=$_POST['intro'];
		$status=$_GET['status'];
		
		$date =time();
		echo $_POST['more'];
		if(($_POST['more']!="")){
			/*$info['board']=$API->adminWarning("Debe de completar al menos los campos titulo e introducción");
			$_SESSION['content']=$API->replacetags($url,$info);
		}else{
			if (isset($_POST['category'])){
				$category = $_POST['category'];
				$n      = count($category);
	    		$i      = 0;
		
				while ($i < $n){
        			$cat.=$category[$i].";";
      				$i++;
   				}
			}*/
			
			mysql_query("INSERT INTO `news` ( `title` , `intro` , `content` , `date` , `author`,`category` , `status`,`datePost`)
						 VALUES ('".$info['title']."', '".$info['intro']."', '".$info['more']."', '".$date."','".$_SESSION['login_username']."', '".$cat."', '".$status."' , '".$info['datePost']."');");
			$API->goto("?go=news");
		}
	}else{
		$info['board']="";
		$info['title']="";
		$info['more']="";
		$info['intro']="";
		
		/*$info['cat']='<select name="main" id="main">
   	 	<option value="0">Categoria principal</option>';
   	 	
   	 	$query=mysql_query("SELECT * FROM `category` WHERE id_main = 0 ORDER BY id DESC")or die(mysql_error());
		while($dato=mysql_fetch_array($query)){
			$info['cat'].='<option value="'.$dato['id'].'">'.$dato['name'].'</option>';
			$info['category'].='<div class="tag"><input type="checkbox" name="category[]" value="'.$dato['id'].'" style="float:left; padding-right=5px;"> - '.$dato['name'].'</div>';
			$query2=mysql_query("SELECT * FROM `category` WHERE id_main = ".$dato['id']."" )or die(mysql_error());
			while($dato=mysql_fetch_array($query2)){
				$info['cat'].='<option value="'.$dato['id'].'">'.$dato['name'].'</option>';
				$info['category'].='<div class="tag_child"><input type="checkbox" name="category[]" value="'.$dato['id'].'" style="float:left; padding-right=5px;"> - '.$dato['name'].'</div>';
			}	
		}
  		$info['cat'].='</select>';*/
  		$info['cat']="";
		$url="../modules/news/admin/face/new.html";
		$_SESSION['content']=$API->replacetags($url,$info);
	}
	
	$API->printadmin();
}

function news_delete(){
	/*if (isset($_POST['delete'])){
		$delete = $_POST['delete'];
		$n      = count($delete);
	    $i      = 0;
		
		while ($i < $n){
        	//echo $delete[$i].'<br/>';
        	mysql_query("DELETE FROM `news` WHERE `id` = '".$delete[$i]."'");
        	mysql_query("DELETE FROM `comments` WHERE `id_new` = '".$delete[$i]."'");
      		$i++;
   		}
	}
*/	mysql_query("UPDATE `news` SET `trash` = '1' WHERE `id` ='".$_GET['id']."'");
	mysql_query("UPDATE `comments` SET `trash` = '1' WHERE `id_new` ='".$_GET['id']."'");
    //mysql_query("DELETE FROM `comments` WHERE `id_new` = '".$_GET['id']."'");
	$API= new API();
	$API->goto("?go=news");
}

function news_edit($id){
	$API= new API();
	$API->moduleName("news");
	$API->setWHERE("Editar entrada");
	
	$js=file_get_contents("../modules/news/admin/extra/form.txt");

	$API->setJS($js);
	$_SESSION['panel']=file_get_contents("../modules/news/admin/face/panel.html");
	$url="../modules/news/admin/face/edit.html";		
	
	
	
	if($_GET['form']==1){
		$info['board']="";
		$info['title']=$_POST['title'];
		$info['more'] =$_POST['more'];
		$info['intro']=$_POST['intro'];
		$info['id']=$_POST['id'];
		
		if($_POST['status']==$_GET['status']){
			$status=$_POST['status'];
		}else{
			$status=$_GET['status'];
		}
			
		if($info['more']==""){
			$info['board']='<div id="news-board">Debe de completar al menos los campos titulo e introducci&oacute;n</div>';
			$_SESSION['content']=$API->replacetags($url,$info);
		}else{
			$busqueda="UPDATE `news` SET `title` = '".$info['title']."' , `content`='".$info['more']."' , `status`='".$_POST['type']."' WHERE `id` ='".$info['id']."'";
			mysql_query($busqueda) or die(mysql_error());
			echo "<script>location.href='?go=news'</script>";
		}
	}else{

	$query=mysql_query("SELECT * FROM `news` WHERE id='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$news['title']=$dato['title'];
		$news['status']=$dato['status'];
		$news['intro']=$dato['intro'];
		$news['more']=$dato['content'];
	}
	if($news['status']=="0"){
		$news['select1']='selected="selected"';
		$news['select2']='';
	}else{
		$news['select2']='selected="selected"';
		$news['select1']='';
	}	
	$news['id']=$id;
	$news['board']='';
	$_SESSION['content']=$API->replacetags($url,$news);
	
	$API->printadmin();
	}
}

function news_comments($id){
$API= new API();
	$API->moduleName("news");
	
	$js=file_get_contents("../modules/news/admin/extra/show.txt");

	$API->setJS($js);
	$query=mysql_query("SELECT * FROM `news` WHERE id='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$news['id']=$dato['id'];
		$news['data1']=$dato['title'];
		$API->setWHERE("Comentarios en ".$dato['title']);
		
		$news['data2']=mysql_num_rows(mysql_query("SELECT * FROM `comments` WHERE id_new = '".$dato['id']."'"));
		
		$news['data3']=htmlentities($dato3);
		$news['data4']=$dato['author'];
		$news['data5']=date(_TIMEFORMAT,$dato['date']);
		$news['cat']=news_category($dato['category']);	
		if($dato['status']==0){
			$news['data7']="publicado";
		}else if($dato['status']==1){
			$news['data7']="borrador";
		}
		
		$news['url3']='?go=news&do=delete&id='.$dato['id'];
		$news['url2']='?go=news&do=edit&id='.$dato['id'];
		$url="../modules/news/admin/face/rows.html";
		$content['rows'].=$API->replacetags($url,$news);
	}
	$url="../modules/news/admin/face/table.html";
	$main=$API->replacetags($url,$content);
	
	
	$query=mysql_query("SELECT * FROM `comments` WHERE id_new='".$id."' AND (status=1 OR status=0)")or die(mysql_error());
	
	$i=1;
	while($dato=mysql_fetch_array($query)){
		$id=$dato['id'];
		$status='<span class="replacetags-links"><a href="?go=news&do=comment&id='.$id;
		if($dato['status']==0){
			$comment['options']=$status.'&op=1">Rechazar</a></span> | '.$status.'&op=2">Spam</a></span> | '.$status.'&op=3">Borrar</a></span>';
		}else{
			$comment['options']=$status.'&op=0">Aceptar</a></span> | '.$status.'&op=2">Spam</a></span> | '.$status.'&op=3">Borrar</a></span>';
		}
		
		if($dato['status']=="0"){
			$comment['color']="#eaeaea";
		}else{
			$comment['color']="#fefed0";
		}
		$comment['id']=$dato['id'];
		$comment['author']="#".$i." ".$dato['author'];
		$comment['email']=$dato['email'];
		$comment['web']=$dato['web'];
		$comment['comment']=strip_tags($dato['comment'],_NEWS_COMMENTS_AVAILABLE_TAGS);
		$comment['date']=date(_TIMEFORMAT,$dato['date']);
		$comment['ip']=$dato['ip'];
	
		$grvMail = $dato['email']; 
      	$grvInit = "spacer.gif";
      	$grvSize = 40;
      
      	$img= "http://www.gravatar.com/avatar.php"; 
      	$img.= "?gravatar_id=".md5($grvMail);  
      	//$img.= "&default=".urlencode($grvInit);
      	$img.= "&size=".$grvSize;

	  	$comment['img']=$img;
		
		$url="../modules/news/admin/face/comment.html";
		$main.=$API->replacetags($url,$comment);
		$i++;
	}
	
	
	$_SESSION['content']=$main;
	$API->printadmin();

}
function news_comment($id,$op){
	$API= new API();
	$API->moduleName("news");	
	if($op<2){
		$query="UPDATE `comments` SET `status` = '".$op."' WHERE `id` ='".$id."'";
	}else if($op==3){
		$aux_query=mysql_query("SELECT * FROM `comments` WHERE `id`='".$id."'");
		while($dato=mysql_fetch_array($aux_query)){
			$author=$dato['author'];
			$email=$dato['email'];
			$id=$dato['ip'];
		}
		$query="INSERT INTO `spam` ( `author` , `email` ,`ip`) VALUES ('".$author."', '".$email."', '".$ip."')";
		mysql_query($query) or die(mysql_error());
		$query="DELETE FROM `comments` WHERE `id` = '".$id."'";
	}else{
		$query="DELETE FROM `comments` WHERE `id` = '".$id."'";
	}	
	mysql_query($query) or die(mysql_error());
			
	$API->goback();
}
?>