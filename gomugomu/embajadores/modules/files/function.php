<?php

function files(){
	$API= new API();
	$API->moduleName("files");
	
	$url="modules/news/face/index.html";
	
	$query=mysql_query("SELECT * FROM `files`")or die(mysql_error());
	
	while($dato=mysql_fetch_array($query)){
	
		$files['url']="/files/".$dato['id']."/".$API->friendlyURL($dato['title']);		
		$files['title']=$dato['title'];
		
		$new.=$API->template($url,$news);

	}
	
	$_SESSION['content']=$new;
	$API->printweb();
}
?>