<?php
/*********************************************************
	APP_itemS
		id
		name
		created
		id_user_created
		status 0 (ok) | 1(deleted)
**********************************************************/
function material_data($id){
	$query=mysql_query("SELECT * FROM `dis_component_material` WHERE `id_component`='".$id."'")or die(mysql_error());
	$datos="";

		while($dato=mysql_fetch_array($query)){
			$materials=mysql_query("SELECT * FROM `dis_material` WHERE `id`='".$dato['id_material']."'")or die(mysql_error());
			while($data=mysql_fetch_array($materials)){
			$datos.="".$data['name']." | " .'<b><span class="template-links"><a href="?go=material&do=edit&id_item='.$id_item.'&id='.$data['id'].'">Editar</a></span></b><br/>';
			$datos.="<ul>";
			if($data['type']!="")
				$datos.="<li> Tipo: ".$data['type']."</li>";
			if($data['weight']!="")
				$datos.="<li> Gramaje: ".$data['weight']."</li>";
			if($data['size']!="")
				$datos.="<li> Tamaño: ".$data['size']."</li>";
			$datos.="<li> Precio: ".$data['price']._COIN."</li>";
			$datos.="</ul><br/>";
			}
		}
		return $datos;
	
	
}

function actions_data($id){
	$query=mysql_query("SELECT * FROM `dis_item_actions` WHERE `id_item`='".$id."' AND `status`='0'")or die(mysql_error());
	

		while($dato=mysql_fetch_array($query)){
			$actions=mysql_query("SELECT * FROM `dis_actions` WHERE `id`='".$dato['id_actions']."' AND `status`='0'")or die(mysql_error());
		
			while($data=mysql_fetch_array($actions)){
					$datos.='<div class="material">';
					$datos.='<div class="material_header">';
						$datos.='<a href="?go=actions&do=edit&id='.$data['id'].'">'.$data['name'].'</a>';
						$datos.='</div>';
						$datos.='<ul><li> Precio: '.$data['price']._COIN.'</li>';
						$datos.='<li> Tipo: ';
						if($data['type']==1)
							$datos.='Precio por hora</li>';
						else if($data['type']==2)
							$datos.='Precio por unidad</li>';
						else
							$datos.='Por definir</li>';
					$datos.='</ul>';
					$datos.='</div>';
			}
		}
		return $datos;
}


function component_data($id){
	$query=mysql_query("SELECT * FROM `dis_component` WHERE `id_item`='".$id."' AND `status`='0'")or die(mysql_error());
		
		while($dato=mysql_fetch_array($query)){
			$datos.='<div class="material">';
			$datos.='<div class="material_header">';
			$datos.='<a href="?go=component&do=edit&id='.$dato['id'].'">'.$dato['name'].'</a>';
			$datos.='<a href="?go=component&do=delete&id='.$dato['id'].'"  class="button red" onclick="return confirmar()" style="float:right; font-size:10px;">X</a>';
			$datos.='<div class="clear"></div>';
			$datos.='</div>';
			$material=material_data($dato['id']);
			
				$datos.='<b> + <a href="?go=material&id_component='.$dato['id'].'">Añadir material</a></b><br/><br/>';
			
				$datos.=$material;
			$datos.='</div>';
		}
		return $datos;
}


function item_show(){
	$API= new API();
	$API->moduleName("item");
	$js=file_get_contents("../modules/item/admin/extra/show.txt");
	$API->setJS($js);
	
	
	$content['id']=$_SESSION['login_id'];
	$content['item']=$_GET['id_item'];
	
	$query=mysql_query("SELECT * FROM `dis_item` WHERE `status`='0' ORDER BY created DESC")or die(mysql_error());
	if(mysql_num_rows($query)>0){

	while($dato=mysql_fetch_array($query)){
		$item['data1']=$dato['name'];
		$item['data2']="<b>".getusername($dato['id_created_user'])."</b> | ".date(_TIMEFORMAT,$dato['created']);
		
		
			$item['data3']='<b><span class="template-links"><a href="?go=component&id_item='.$dato['id'].'">Añadir componente</a></span></b><br/><br/>';
			$item['data3'].=component_data($dato['id']);
			
		$actions=mysql_query("SELECT * FROM `dis_actions` WHERE `id_item`='".$dato['id']."' AND `status`='0'");
		$num_actions=mysql_num_rows($actions);
		
		
					
			$item['data4']='<b><span class="template-links"><a href="?go=actions&id_item='.$dato['id'].'">Añadir acciones</a></span></b><br/><br/>';
			$item['data4'].=actions_data($dato['id']);

		
		$item['url1']='?go=item&do=edit&id='.$dato['id'];
		$item['url3']='?go=item&do=delete&id='.$dato['id'].'&id_item='.$_GET['id_item'];
		$url="itemrows";
		$content['rows'].=$API->templateAdmin($url,$item);
	}
	}else{
		$content['rows'].="";
	}
	
	
	$url="itemtable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}

/*********************************************************
	EDIT 
**********************************************************/
function item_edit($id){
	$API= new API();
	$API->moduleName("user");
	$js=file_get_contents("../modules/client/admin/extra/checkclient.txt");

	$API->setJS($js);
	$url="itemedit";
	
//	$info['id']=$id;
	$info['name']="";
	$info['board']="";
	$info['id']=$_GET['id'];
	
	$query=mysql_query("SELECT * FROM `dis_item` WHERE `id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['name']=$dato['name'];
		$info['description']=$dato['description'];
	}

	if($_GET['form']==1){		
		$item=$_POST['item'];
		$id=$_GET['id'];

		mysql_query("UPDATE `dis_item` SET `name` = '".$item."', `description` = '".$_POST['description']."' WHERE `id` ='".$id."'")or die(mysql_error());	
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
function item_delete($id){
	$API= new API();
	mysql_query("UPDATE `dis_item` SET `status` = '1', `id_modified_user`='".$_SESSION['login_id']."', `modified` = '".time()."'  WHERE  `id`='".$_GET['id']."'");
	$API->goto("?go=item&id_item=".$_GET['id_item']);
}

?>