<?php
/*********************************************************
	APP_componentS
		id
		name
		created
		id_user_created
		status 0 (ok) | 1(deleted)
**********************************************************/
function component_show(){
	$API= new API();
	$API->moduleName("component");
	$js=file_get_contents("../modules/component/admin/extra/show.txt");
	$API->setJS($js);
	
	$content['id']=$_SESSION['login_id'];
	/*$query=mysql_query("SELECT * FROM `dis_item` WHERE `status`='0' AND id='".$_GET['id_item']."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$content['item_name']=$dato['name'];
	}*/
	
	$content['id_item']=$_GET['id_item'];
	$query=mysql_query("SELECT * FROM `dis_component` WHERE `status`='0' AND `id_item`='".$_GET['id_item']."' ORDER BY name")or die(mysql_error());
	if(mysql_num_rows($query)>0){

	while($dato=mysql_fetch_array($query)){
		$component['data1']=$dato['name'];
		
		if($dato['type']=="1"){
			$component['data2']="Precio por hora";
		}else if($dato['type']=="2"){
			$component['data2']="Precio por unidad";
		}else{
			$component['data2']="Por definir";
		}
		
		$component['data3']=$dato['price']." "._COIN;
		
		$component['url1']='?go=component&do=edit&id='.$dato['id'];
		$component['url3']='?go=component&do=delete&id='.$dato['id'].'&id_item='.$_GET['id_item'];
		$url="componentrows";
		$content['rows'].=$API->templateAdmin($url,$component);
	}
	}else{
		$content['rows'].="";
	}
	
	
	$url="componenttable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}

/*********************************************************
	EDIT 
**********************************************************/
function component_edit($id){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="componentedit";
	
	$info['id']=$id;
	$info['name']="";
	$info['price']="";
	$info['type']="";
	$info['board']="";
	$info['id']=$_GET['id'];
	
	/*$query=mysql_query("SELECT * FROM `dis_item` WHERE `id`='".$_GET['id_item']."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['item']=$dato['name'];
	}*/
	
	
	$query=mysql_query("SELECT * FROM `dis_component` WHERE `id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['name']=$dato['name'];
	}

	if($_GET['form']==1){		
		$component=$_POST['component'];
		$id=$_GET['id'];

		mysql_query("UPDATE `dis_component` SET `name` = '".$component."'  WHERE `id` ='".$id."'")or die(mysql_error());	
		$API->goto("?go=item");
		
	}else{
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}
}

/*********************************************************
	DELETE change status 0 to 1
**********************************************************/
function component_delete($id){
	$API= new API();
	mysql_query("UPDATE `dis_component` SET `status` = '1'  WHERE  `id`='".$_GET['id']."'");
	$API->goto("?go=item");
}

?>