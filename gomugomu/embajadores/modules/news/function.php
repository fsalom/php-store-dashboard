<?php
function newsHtaccess($text){
	$API= new API();
	if(_HTACCESS){
		if($text[1]!="")
			return "/noticia/".$text[0]."/".$API->friendlyURL($text[1]);
		else
			return "/noticia/".$text[0]."/cita";
	}else{
		return "?go=news&do=view&id=".$text[0];
	}
}

function news(){
	$API= new API();
	$API->moduleName("news");
	
	$new="";
		
	$query=mysql_query("SELECT * FROM `news` WHERE status=0 AND trash=0 ORDER BY date DESC LIMIT "._MAXNEWS."")or die(mysql_error());

	while($dato=mysql_fetch_array($query)){
		
		$text[0]=$dato['id'];
		$text[1]=$dato['title'];
		
		if($dato['title']==""){
			$url="news-quote";
			$news['url']=newsHtaccess($text);
			$news['author']=$dato['author'];
			$news['id']=$dato['id'];
			$intro=explode("<!-- pagebreak -->",$dato['content']);
			$news['subtitle']=date(_TIMEFORMAT,$dato['date']);
			$news['content']=$intro[0];
			$comment=mysql_query("SELECT * FROM `comments` WHERE id_new=".$dato['id']." AND status=0")or die(mysql_error());
			$news['comment']='<a href="'.$news['url'].'#to-comments">'.mysql_num_rows($comment).' '._NEWS_COMMENTS.'</a>';
			$news['category']=_NEWS_CATEGORY.news_category($dato['category']);
			$news['day']=date("j",$dato['date']);
			$news['month']=date("M",$dato['date']);
			$news['year']=date("y",$dato['date']);

		}else{
			$url="news";
			$news['url']=newsHtaccess($text);
		
			$news['title']=$dato['title'];
			$news['id']=$dato['id'];
			$news['author']=$dato['author'];
			$intro=explode("<!-- pagebreak -->",$dato['content']);
			$news['subtitle']=date(_TIMEFORMAT,$dato['date']);
			$news['content1']=$intro[0];
			$comment=mysql_query("SELECT * FROM `comments` WHERE id_new=".$dato['id']." AND status=0")or die(mysql_error());
			$news['comment']='<a href="'.$news['url'].'#to-comments">'.mysql_num_rows($comment).' '._NEWS_COMMENTS.'</a>';
			if($intro[1]!="")
				$news['more']='<a href="'.$news['url'].'">LEER MÁS</a>';
			else
				$news['more']="";
			$news['category']=_NEWS_CATEGORY.news_category($dato['category']);
			$news['day']=date("j",$dato['date']);
			$news['month']=date("M",$dato['date']);
			$news['year']=date("y",$dato['date']);
		}
		$new.=$API->template($url,$news);
	}
	
	$API->printweb($new);
}
/*
Busca los nombres de las categorias relacionados con los ids proporcionados
*/
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
					$info.=','.$dato['name'];
					
			}
			$i++;
		}
		return $info;
}

/*
Busca los nombres de las categorias relacionados con los ids proporcionados
*/
function news_tags($array){
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
					$info.=','.$dato['name'];
					
			}
			$i++;
		}
		return $info;
}


function news_view($id){
	$API= new API();
	$API->moduleName("news");
	
	$config['id']=$id;
	$url2="modules/news/extra/checkcomment.txt";
	$js=$API->replacetags($url2,$config);
	$API->setJS($js);
	
		
	$new="";
	
		
	
	$query=mysql_query("SELECT * FROM `news` WHERE id=".$id)or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		
		if($dato['title']=="")
			$url="news-quote-more";
		else
			$url="news-more";
				
		$text[0]=$dato['id'];
		$text[1]=$dato['title'];
		$news['url']=newsHtaccess($text);
		$news['author']=$dato['author'];
		$API->setTITLE($dato['title']);
		$news['title']=$dato['title'];
		$news['date']=date(_TIMEFORMAT,$dato['date']);
		$cmt=mysql_query("SELECT * FROM `comments` WHERE id_new=".$id." AND status=0")or die(mysql_error());
		$news['comment']="<a href=".$news['url']."#to-comments>".mysql_num_rows($cmt)." "._NEWS_COMMENTS."</a>";
		$news['content']=$dato['content'];
		$news['category']=_NEWS_CATEGORY.news_category($dato['category']);
		
		$new.=$API->template($url,$news);
	}
	
	$url="news-comment";
	
	$query=mysql_query("SELECT * FROM `comments` WHERE id_new=".$id." AND status=0")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		if($dato['web']=="")
			$comment['author']=$dato['author'];
		else
			$comment['author']='<a href="'.$dato['web'].'">'.$dato['author'].'</a>';
		$comment['comment']=nl2br(strip_tags($dato['comment'],_NEWS_COMMENTS_AVAILABLE_TAGS));
		$comment['date']=date(_TIMEFORMAT,$dato['date']);
		
		  
	  $grvMail = $dato['email'];
	  $default = _webURL."/modules/news/img/spacer.png"; 
      $grvSize = 40;
      
      
      $img= "http://www.gravatar.com/avatar.php"; 	
      $img.= "?gravatar_id=".md5($grvMail);
      $img.="&default=".urlencode($default);  
      $img.= "&size=".$grvSize;

	  $comment['image']=$img;
		
		$new.=$API->template($url,$comment);
	}
	if(isset($_COOKIE["usName"]) && isset($_COOKIE["usMail"])){
		$answer['name']=$_COOKIE["usName"];
		$answer['mail']=$_COOKIE["usMail"];
		$answer['web']=$_COOKIE["usWeb"];
	}else{
		$answer['name']="";
		$answer['mail']="";
		$answer['web']="";
	}

	$url="news-answer";
	$comment['comment_action']="source/function/insert_comment.php";
	$new.=$API->template($url,$answer);
	

	$API->printweb($new);

}

function news_comment($username,$web,$email,$comment,$make){
  	$username=($_REQUEST['username']);
	$web=$_REQUEST['web'];
    $email=$_REQUEST['email'];
	$comment=$_REQUEST['comment'];
    
    // main submit logic

        global $taken_usernames;
        $resp = array();
        $username = trim($username);
        if (($username=="undefined")||($email=="undefined")||($comment=="undefined")) {
            $resp = array('ok' => false, 'msg' => '<b style="color:#990000">Por favor rellene todos los datos obligatorios</b>');
        }else if(!preg_match(
'/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',
$email)){
			$resp = array('ok' => false, 'msg' => '<b style="color:#990000">Por favor debe de introducir un email valido</b>');
        }else {
            $resp = array("ok" => true, "msg" => '<b style="color:#009900">Este USERNAME esta libre'.$username.$comment.$web.'</b>');
        }

	if (@$make == 'check' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        // means it was requested via Ajax
        echo json_encode($resp);
        exit; // only print out the json version of the response
    }  
 
}
?>