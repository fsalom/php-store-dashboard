<?php


//ADVERTSMENT
function myad($id){
	$API= new API();
	$API->moduleName("book");
		
	$js=file_get_contents("../modules/book/admin/extra/upload.txt");

	$API->setJS($js);
	$url="bookadsnew";
	
	$query=mysql_query("SELECT * FROM `clients` WHERE `id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$content['name']=$dato['company'];
	}
	
	
	$query=mysql_query("SELECT * FROM `app_ads` WHERE `unique_id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$id_client=$dato['id_client'];
		$id_book=$dato['id_book'];
	}
	
	$query=mysql_query("SELECT * FROM `app_ads` WHERE `id_client`='".$id_client."' AND `id_book`='".$id_book."' AND `status`<>3  ORDER BY `created` DESC LIMIT 1")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$thisone=$dato['id'];
	}
	//echo $id_client."-".$id_book."-".$thisone;
	$query=mysql_query("SELECT * FROM `app_ads` WHERE `unique_id`='".$id."' AND `status`<>3")or die(mysql_error());
	$content['id']=$id;
	
	mysql_query("UPDATE `app_ads` SET `access` = '".time()."' WHERE `unique_id` ='".$id."'")or die(mysql_error());
	
	
	if(mysql_num_rows($query)>0){
		//Entramos a añadir nuevos pasos
			$content['board']="";
			$content['review']=1;
			while($dato=mysql_fetch_array($query)){
				$content['id_ad']=$dato['id'];
				$content['image']='<img src="admin/uploads/'.$dato['image'].'" class="ads_image">';
				$access=$dato['access'];
				$id_client=$dato['id_client'];
				$id_book=$dato['id_book'];
				
				
				if($access==0)	
					$content['access']="Never";
				else
					$content['access']=date("m-d-Y H:i",$access);
				
				if($thisone==$dato['id']){
					$content['css']="";
					$content['url']="?do=changes&id=".$id;
					if($dato['comment']=="")
						$content['comment']='<textarea name="comment" id="comment"></textarea>';
					else
						$content['comment']='<textarea name="comment" id="comment">'.$dato['comment'].'</textarea>';
					
				}else{
					$content['css']='class="hide"';
					if($dato['comment']=="")
						$content['comment']="No comments";
					else
						$content['comment']=$dato['comment'];
				}
				
				if($_GET['complete']=="0"){
						$content['board']='<div class="notification warning">You have to specify the changes</div>';
				}
				$content['board']="";
				$status=$dato['status'];
					if($status=="1"){
						$content['board']='<div class="notification changes">'.date("m-d-Y H:i",$dato['modified']).' | The ad has changes to be made. We will contact you soon</div>';
					}
					if($status=="2"){
						$content['css']='class="hide"';
						$content['board']='<div class="notification success">'.date("m-d-Y H:i",$dato['modified']).' | The ad has been approved</div>';
					}
				$content['date']=date("m-d-Y H:i",$dato['created']);
			}
			if($_GET['send']=="1"){
			
				$content['board']='<div class="notification green">The message has been sent</div>';
			}
			
			$total=mysql_query("SELECT * FROM `app_ads` WHERE `id_client`='".$id_client."' AND `id_book`='".$id_book."' AND `status`<>3")or die(mysql_error());
			$content['num']=mysql_num_rows($total);
			$content['id']=$id;
			$content['id_book']=$id_book;
			
			$url="bookads";
			$data = array();
			$data[0]=$API->template($url,$content);
			
			$query=mysql_query("SELECT * FROM `app_ads` WHERE `id_client`='".$id_client."' AND `id_book`='".$id_book."' AND `id_ads`='".$content['id_ad']."' AND `status`<>3")or die(mysql_error());
			if(mysql_num_rows($query)>0){
				while($dato=mysql_fetch_array($query)){
					$content['id_ad']=$dato['id'];
					$content['image']='<img src="admin/uploads/'.$dato['image'].'" class="ads_image">';
					$access=$dato['access'];
					$content['review']++;
					
					
					
					if($thisone==$dato['id']){
						$content['css']="";
						if($dato['comment']=="")
						$content['comment']='<textarea name="comment" id="comment"></textarea>';
						else
						$content['comment']='<textarea name="comment" id="comment">'.$dato['comment'].'</textarea>';
					
					}else{
						$content['css']='class="hide"';
						if($dato['comment']=="")
						$content['comment']="No comments";
						else
						$content['comment']=$dato['comment'];
					}
					$status=$dato['status'];
					$content['board']="";
					if($status=="1"){
						$content['board']='<div class="notification changes">'.date("m-d-Y H:i",$dato['modified']).' | The ad has changes to be made. We will contact you soon</div>';
					}
					if($status=="2"){
						$content['css']='class="hide"';
						$content['board']='<div class="notification success">'.date("m-d-Y H:i",$dato['modified']).' | The ad has been approved</div>';
					}
					if($access==0)	
						$content['access']="Never";
					else
						$content['access']=date("m-d-Y H:i",$access);
				
					$content['date']=date("m-d-Y H:i",$dato['created']);
					
					$content['id']=$id;
					$content['id_book']=$id_book;
					
					
					$data[$content['review']]=$API->template($url,$content);
					
					
					
				}
				
			}
			
		
			$print="";
			for($i=count($data); $i>-1;$i--){
				$print.=$data[$i];
			}
			$API->printweb($print);
		}else{
		
			$API->printweb("ERROR: not found");;
		}

	
	
		
}


function myad_approve($id,$id_ad){

	$API= new API();
	$API->moduleName("book");
		
	//$js=file_get_contents("../modules/book/admin/extra/upload.txt");

	$API->setJS($js);
	//$url="bookadsnew";
	
	//echo $_POST['comment']." ".$id." ".$id_ad;
	
	mysql_query("UPDATE `app_ads` SET `status` = '2', `modified` = '".time()."' WHERE `id` ='".$id_ad."'")or die(mysql_error());
	
	mysql_query("UPDATE `app_ads` SET `status` = '2', `modified` = '".time()."' WHERE `unique_id` ='".$id."'")or die(mysql_error());
	
	$API->goto("?id=".$id);


}
function myad_changes($id,$id_ad){
	$API= new API();
	$API->moduleName("book");
		
	//$js=file_get_contents("../modules/book/admin/extra/upload.txt");

	$API->setJS($js);
	//$url="bookadsnew";
	
	if($_POST['comment']==""){
		$API->goto("?id=".$id."&complete=0");
		break;	
	}
	
	mysql_query("UPDATE `app_ads` SET `status` = '1',`comment` = '".$_POST['comment']."', `modified` = '".time()."' WHERE `id` ='".$id_ad."'")or die(mysql_error());
	
	mysql_query("UPDATE `app_ads` SET `status` = '1', `modified` = '".time()."' WHERE `unique_id` ='".$id."'")or die(mysql_error());
	
	$API->goto("?id=".$id);


}

function myad_edit($email,$id){
	$API= new API();
	$API->moduleName("book");
	$API->setWHERE("Editar privilegios");
	
	$js=file_get_contents("../modules/book/admin/extra/checkbook.txt");

	$API->setJS($js);
	$url="bookedit";
	
	$info['id']=$id;
	$info['validation']="";
	$info['name']="";
	$info['surname']="";
	$info['board']="";
	$info['telephone']="";
	$info['email']="";
	$info['info']="";
	$info['rep']="";
	$info['page']="";
	
	$info['id']=$_GET['id'];
	$info['email']=$_GET['email'];
	
	
	$query=mysql_query("SELECT * FROM `app_book` WHERE `id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['username']=$dato['name'];

		$info['page']=$dato['page'];
		$info['info']=$dato['info'];
	}
		//ZONE
		$info['zone']="";
		
		$azones=array();
		$i=0;
		$query=mysql_query("SELECT * FROM `app_zones_relation` WHERE `id_book`='".$id."'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
					$azones[$i]=$dato['id_zone'];
					$i++;
			}
			//print_r($azones);
			
		$query=mysql_query("SELECT * FROM `app_zones` WHERE `status`<>'4'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				if(in_array($dato['id'],$azones))
					$info['zone'].='<input type="checkbox" name="zones[]" checked="checked" value="'.$dato['id'].'">'.$dato['name'].'<br>';
				else
					$info['zone'].='<input type="checkbox" name="zones[]" value="'.$dato['id'].'">'.$dato['name'].'<br>';
			}
		
		
		
		//POSITION
		$info['position']='<select id="position" name="position">';
		$info['position'].='<option value="0">None</option>';
		$query=mysql_query("SELECT * FROM `app_position` WHERE `status`='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				if($dato['id']==$position)
					$info['position'].='<option value="'.$dato['id'].'" selected="selected">'.$dato['name'].'</option>';
				else
					$info['position'].='<option value="'.$dato['id'].'">'.$dato['name'].'</option>';
			}
		$info['position'].="</select>";
	
		
	if($_GET['form']==1){
		
		$name=$_POST['username'];
		$id=$_GET['id'];
				$info['validation']="";

		//PREVIOUS ZONES
		$azones=array();
		$azonesid=array();
		$i=0;
		$query=mysql_query("SELECT * FROM `app_zones_relation` WHERE `id_book`='".$id."' AND status='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
					$azones[$i]=$dato['id_zone'];
					$azonesid[$i]=$dato['id'];
					$i++;
			}
		//azones con previous zones
		//nuevas zonas
		$zones = $_POST['zones'];

			 $N = count($zones);
   			//echo $N."<br/>";
   	 		 for($i=0; $i < $N; $i++){
   	 		 	if(!in_array($zones[$i],$azones)){
   	 		 		$exist=mysql_query("SELECT * FROM `app_zones_relation` WHERE `id_book`='".$id."' AND id_zone='".$zones[$i]."'")or die(mysql_error());
      				if(mysql_num_rows($exist)>0){
      					mysql_query("UPDATE `app_zones_relation` SET `status` = '0' WHERE `id_book` ='".$id."' AND id_zone='".$zones[$i]."'")or die(mysql_error());
					}else{
						mysql_query("INSERT INTO `app_zones_relation` ( `id_book` , `id_zone`)
					VALUES ('".$id."', '".$zones[$i]."');")
					or die(mysql_error());
					}
				}
    		 }
    		 //
    		 
    		 //print_r($azones);
    		 //print_r($azonesid);
    		 $X = count($azones);
    		 //echo $X;
    		 for($i=0; $i < $X; $i++){
    		 	if(!in_array($azones[$i],$zones)){
    		 		//echo "azones:".$azones[$i]."<br/>azonesid:".$azonesid[$i]."<br/>";
    		 		mysql_query("UPDATE `app_zones_relation` SET `status` = '1' WHERE `id` ='".$azonesid[$i]."';") or die(mysql_error());
    		 		sleep(0.5);
    		 	}
    		 }

		
		mysql_query("UPDATE `app_book` SET `name` = '".$name."' WHERE `id` ='".$id."'")or die(mysql_error());
		
			
		$API->goto("?go=book&do=edit&form=2&id=".$id);
	}else if($_GET['form']==2){
		$info['board']='
<div class="notification green">
	Changes have been saved
</div>';
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}else{
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}
}

?>