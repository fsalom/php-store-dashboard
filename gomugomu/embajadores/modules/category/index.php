<?php
$API = new API();


$API->moduleName("category");

$query=mysql_query("SELECT * FROM `category` WHERE id_main = 0 ORDER BY id DESC")or die(mysql_error());
$cnt="<ul>";
while($dato=mysql_fetch_array($query)){
	$cnt.='<li><a href="#">'.$dato['name'].'</a></li>';
	$query2=mysql_query("SELECT * FROM `category` WHERE id_main = ".$dato['id']."" )or die(mysql_error());
	$cnt2="<ul>";
	while($dato2=mysql_fetch_array($query2)){
		$cnt2.='<li><a href="#">'.$dato2['name'].'</a></li>';
	}
	$cnt2.="</ul>";
	$cnt.=$cnt2;
}
$cnt.="</ul>";


//$API->addmodule($name,$content);

$_SESSION['left']= $cnt;
?>