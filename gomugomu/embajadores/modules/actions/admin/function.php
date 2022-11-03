<?php
/*********************************************************
	APP_actionsS
		id
		name
		created
		id_user_created
		status 0 (ok) | 1(deleted)
**********************************************************/
function actions_show(){
	$API= new API();
	$API->moduleName("actions");
	$js=file_get_contents("../modules/actions/admin/extra/show.txt");
	$API->setJS($js);
	
	$content['id']=$_SESSION['login_id'];
	/*$query=mysql_query("SELECT * FROM `dis_item` WHERE `status`='0' AND id='".$_GET['id_item']."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$content['item_name']=$dato['name'];
	}*/
	

		
	
	$content['actions']="";
	$content['id_item']=$_GET['id_item'];
	$query=mysql_query("SELECT * FROM `dis_actions` WHERE `status`='0' ORDER BY name")or die(mysql_error());
	if(mysql_num_rows($query)>0){

	while($dato=mysql_fetch_array($query)){
	
		$component=mysql_query("SELECT * FROM `dis_item_actions` WHERE `id_item`='".$_GET['id_item']."' AND `id_actions`='".$dato['id']."'") or die(mysql_error());
		
		$take=mysql_num_rows($component);
		
		if($take>0)
			$actions['checked']='checked="checked"';
		else{
			$actions['checked']='';
		}
		$actions['action_id']=$dato['id'];
		$actions['data1']=$dato['name'];
		if($dato['type']=="1"){
			$actions['data2']="Precio por hora";
		}else if($dato['type']=="2"){
			$actions['data2']="Precio por unidad";
		}else{
			$actions['data2']="Por definir";
		}
		
		$actions['data3']=$dato['price']." "._COIN;
		
		$actions['url1']='?go=actions&do=edit&id='.$dato['id'];
		$actions['url3']='?go=actions&do=delete&id='.$dato['id'].'&id_item='.$_GET['id_item'];
		$url="actionsrows";
		$content['rows'].=$API->templateAdmin($url,$actions);
	}
	}else{
		$content['rows'].="";
	}
	if($_GET['id_item']!=""){
	$content['actions']='<div class="line"><a href="#" onclick="document.getElementById'."('actions').submit( );".' return false" class="button white" style="padding:10px 50px;"> + Añadir a artículo</a></div>';
	}

	
	$url="actionstable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}


/*********************************************************
	ADD COMPONENT 
**********************************************************/

function actions_add($id){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="materialedit";
	
	//echo print_r($_POST['select']);
		
	$id_item=$_GET['id_item'];

	$component=mysql_query("SELECT * FROM `dis_item_actions` WHERE `id_item`='".$_GET['id_item']."'") or die(mysql_error());
	//echo mysql_num_rows($component);
	if(mysql_num_rows($component)>0){
		mysql_query("DELETE FROM `dis_item_actions` WHERE `id_item` = '".$_GET['id_item']."';");
	}
	 
	
    $materials = $_POST['select'];
    for ($i=0; $i<count($materials); $i++) {
       //echo $materials[$i];
        mysql_query("INSERT INTO `dis_item_actions`(`id_item`, `id_actions`) VALUES ('".$id_item."', '".$materials[$i]."')") or die(mysql_error());
    }


		//mysql_query("UPDATE `dis_material` SET `name` = '".$material."',`type`='".$_POST['type']."',`size`='".$_POST['size']."',`weight`='".$_POST['weight']."',`price`='".$_POST['price']."' WHERE `id` ='".$id."'")or die(mysql_error());	
		
	$API->goto("?go=item");
		
	
}

/*********************************************************
	EDIT 
**********************************************************/
function actions_edit($id){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="actionsedit";
	
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
	
	
	$query=mysql_query("SELECT * FROM `dis_actions` WHERE `id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['name']=$dato['name'];
		$info['price']=$dato['price'];

		$info['type']='<select name="type">';
		if($dato['type']==1)
			$info['type'].='<option value="1" selected="selected">Precio por hora</option>';
		else
			$info['type'].='<option value="1">Precio por hora</option>';
		
		
		if($dato['type']==2)
			$info['type'].='<option value="2" selected="selected">Precio por unidad</option>';
		else
			$info['type'].='<option value="2">Precio por unidad</option>';
		
		
		if($dato['type']==0)
			$info['type'].='<option value="0" selected="selected">Por definir</option>';
		else
			$info['type'].='<option value="0">Por definir</option>';
			
		$info['type'].='</select>';
		
	}

	if($_GET['form']==1){		
		$actions=$_POST['actions'];
		$id=$_GET['id'];

		mysql_query("UPDATE `dis_actions` SET `name` = '".$actions."',`price` = '".$_POST['price']."',`type` = '".$_POST['type']."'  WHERE `id` ='".$id."'")or die(mysql_error());	
		$API->goto("?go=actions");
		
	}else{
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}
}

/*********************************************************
	DELETE change status 0 to 1
**********************************************************/
function actions_delete($id){
	$API= new API();
	mysql_query("UPDATE `dis_actions` SET `status` = '1', `id_modified_user`='".$_SESSION['login_id']."', `modified` = '".time()."'  WHERE  `id`='".$_GET['id']."'");
	$API->goto("?go=actions&id_item=".$_GET['id_item']);
}

?>