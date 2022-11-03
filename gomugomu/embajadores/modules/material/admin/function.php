<?php
/*********************************************************
	APP_materialS
		id
		name
		created
		id_user_created
		status 0 (ok) | 1(deleted)
**********************************************************/
function material_show(){
	$API= new API();
	$API->moduleName("material");
	$js=file_get_contents("../modules/material/admin/extra/show.txt");
	$API->setJS($js);
	
	$content['id']=$_GET['id_component'];
	//$content['id']=$_SESSION['login_id'];
	
	$content['item']=$_GET['id_item'];
	
	$content['component']="";
	$query=mysql_query("SELECT * FROM `dis_material` WHERE `status`='0'")or die(mysql_error());
	if(mysql_num_rows($query)>0){

	while($dato=mysql_fetch_array($query)){
		$component=mysql_query("SELECT * FROM `dis_component_material` WHERE `id_component`='".$_GET['id_component']."' AND `id_material`='".$dato['id']."'");
		
		$take=mysql_num_rows($component);
		
		if($take>0)
			$material['checked']='checked="checked"';
		else{
			$material['checked']='';
		}
		$material['id']=$dato['id'];
		$material['data1']=$dato['name'];
		$material['data2']=$dato['type'];
		$material['data3']=$dato['size'];
		$material['data4']=$dato['weight'];
		$material['data5']=$dato['price'];
		$material['data6']="<b>".getusername($dato['id_created_user'])."</b> | ".date(_TIMEFORMAT,$dato['created']);
		
		
		
		$material['url1']='?go=material&do=edit&id='.$dato['id'];
		$material['url3']='?go=material&do=delete&id='.$dato['id'].'&id_item='.$dato['id_item'];
		$url="materialrows";
		$content['rows'].=$API->templateAdmin($url,$material);
	}
	}else{
		$content['rows'].="";
	}
	if($_GET['id_component']!=""){
	$content['component']='<div class="line"><a href="#" onclick="document.getElementById'."('material').submit( );".' return false" class="button white" style="padding:10px 50px;"> + Añadir a componente</a></div>';
	}
	$url="materialtable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}

/*********************************************************
	ADD COMPONENT 
**********************************************************/

function material_add($id){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="materialedit";
	
	//echo print_r($_POST['select']);
		
	$id=$_GET['id'];

	$component=mysql_query("SELECT * FROM `dis_component_material` WHERE `id_component`='".$_GET['id']."'") or die(mysql_error());
	//echo mysql_num_rows($component);
	if(mysql_num_rows($component)>0){
		mysql_query("DELETE FROM `dis_component_material` WHERE `id_component` = '".$_GET['id']."';");
	}
	 
	
    $materials = $_POST['select'];
    for ($i=0; $i<count($materials); $i++) {
       //echo $materials[$i];
        mysql_query("INSERT INTO `dis_component_material`(`id_component`, `id_material`) VALUES ('".$id."', '".$materials[$i]."')") or die(mysql_error());
    }


		//mysql_query("UPDATE `dis_material` SET `name` = '".$material."',`type`='".$_POST['type']."',`size`='".$_POST['size']."',`weight`='".$_POST['weight']."',`price`='".$_POST['price']."' WHERE `id` ='".$id."'")or die(mysql_error());	
		
	$API->goto("?go=item");
		
	
}


/*********************************************************
	EDIT 
**********************************************************/

function material_edit($id){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="materialedit";
	
	$info['id']=$id;
	$info['name']="";
	$info['type']="";
	$info['price']="";
	$info['size']="";
	$info['weight']="";
	
	$info['board']="";
	$info['id']=$_GET['id'];
	
	$query=mysql_query("SELECT * FROM `dis_material` WHERE `id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['name']=$dato['name'];
		$info['price']=$dato['price'];
		$info['weight']=$dato['weight'];
		$info['size']=$dato['size'];
		$info['type']=$dato['type'];
		$info['id_item']=$dato['id_item'];
	}

	if($_GET['form']==1){		
		$material=$_POST['material'];
		$id=$_GET['id'];

		mysql_query("UPDATE `dis_material` SET `name` = '".$material."',`type`='".$_POST['type']."',`size`='".$_POST['size']."',`weight`='".$_POST['weight']."',`price`='".$_POST['price']."' WHERE `id` ='".$id."'")or die(mysql_error());	
		$API->goto("?go=material");
		
	}else{
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}
}

/*********************************************************
	DELETE change status 0 to 1
**********************************************************/
function material_delete($id){
	$API= new API();
	mysql_query("UPDATE `dis_material` SET `status` = '1', `id_modified_user`='".$_SESSION['login_id']."', `modified` = '".time()."' WHERE `id`='".$_GET['id']."'");
	$API->goto("?go=material&id_item=".$_GET['id_item']);
}

?>