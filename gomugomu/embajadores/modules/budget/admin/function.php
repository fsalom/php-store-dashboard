<?php
function budget_show(){
	$API= new API();
	$API->moduleName("budget");
	$API->setWHERE("Listado de usuarios");
	$js=file_get_contents("../modules/budget/admin/extra/show.txt");

	$API->setJS($js);
	
	$query=mysql_query("SELECT * FROM `dis_budget`")or die(mysql_error());
	$num=mysql_num_rows($query);
	if($num>0){

	$pages = new Paginator;
	
	$pages->url = "?go=budget";
	$pages->items_total = $num;
	$pages->mid_range =_PAGINATOR_MID_RANGE;
	$pages->items_per_page=_PAGINATOR_ITEMS_PER_PAGE; 
	$pages->paginate();
	
	$query=mysql_query("SELECT * FROM `dis_budget`")or die(mysql_error());


	
	$content['page']=$pages->display_pages();


	while($dato=mysql_fetch_array($query)){
		$budget['data3']=date(_TIMEFORMAT,$dato['date']);
		$budget['data1']=$dato['id'];
		
		
		$by=mysql_query("SELECT * FROM `clients` WHERE `id`='".$dato['id_user']."'");
		while($take=mysql_fetch_array($by)){
			$budget['data2']=$take['name']." ".$take['surname'];
		}
		
		
		//$budget['url1']='go=budget&do=step2&option=add&id_budget='.$dato['id'];
		$budget['url1']='#';
		$budget['url3']='#';
		
		//$budget['opt']='<a href="?go=budget&do=delete&id='.$dato['id'].'"  class="button red" onclick="return confirmar()">X</a>';
		$budget['opt']='<a href="#"  class="button red" onclick="return confirmar()">X</a>';		
		    		 
		if($dato['status']==1){
			$budget['data4']='Presupuesto sin finalizar';
		}else{
			$budget['data4']="Presupuesto cerrado";
		}
		
		$url="budgetrows";
		$content['rows'].=$API->templateAdmin($url,$budget);
	}
	}else{
		$content['rows'].="";
	}
	
	
	$url="budgettable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}


function budget_send($id,$id_budget){		
		$mail = new phpmailer();

		$API=new API();
		$query=mysql_query("SELECT * FROM `clients` WHERE `id`='".$id."' AND `status`='0'")or die(mysql_error());
		while($dato=mysql_fetch_array($query)){
			$email=$dato['email'];
			$info['name']=$dato['name'];
			$info['company']=$dato['company'];
			$query2=mysql_query("SELECT * FROM `dis_ads` WHERE `id`='".$_GET['id_ad']."' AND `status`<>'3'")or die(mysql_error());
			if(mysql_num_rows($query2)==0){
				echo "error".$_GET['id_ad'];
				exit();
			}else{
				while($dato2=mysql_fetch_array($query2)){
				$info['url']='<a href="http://www.socalcommunitypages.com/app/myad.php?id='.$dato2['unique_id'].'">Access to my ad</a>';
				}
			}
		}

		$url="budgetmail";
  		$content=$API->templateAdmin($url,$info);

		$mail->Mailer = "smtp";
		$mail->Host = "localhost";
		$mail->SMTPAuth = true;
		$mail->Username = "no-reply@socalcommunitypages.com"; 
		$mail->Password = "socal360";
		//$mail->SMTPDebug = true;
		$mail->Port = 25; 
		$mail->From = "no-reply@socalcommunitypages.com";
		$mail->FromName = "Socal Community Pages"; 
		$mail->Timeout=30;
		$mail->AddAddress($email);
		$mail->Subject = "Validation ad | Socal Community Pages";
		$mail->Body = $content;
		$mail->AltBody = htmlentities($content);
		$exito = $mail->Send();

   	if(!$mail->Send()){
   		echo "Message could not be sent. <p>";
   		echo "Mailer Error: " . $mail->ErrorInfo;
   		exit;
	}
	
	$API->goto("?go=budget&do=ads&send=1&id=".$id."&id_budget=".$id_budget);
	
}
function budget_list($id){
	$API= new API();
	$API->moduleName("budget");
	$API->setWHERE("Listado de usuarios");
	$js=file_get_contents("../modules/budget/admin/extra/show.txt");

	$API->setJS($js);
	
	$query=mysql_query("SELECT * FROM `dis_budget` WHERE `id`='".$id."' AND `status`='0'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$titulo=$dato['name'];
	}
	
	$num=mysql_num_rows($query);
	
	
	
	$pages = new Paginator;
	$pages->url = "?go=budget";
	$pages->items_total = $num;
	$pages->mid_range =_PAGINATOR_MID_RANGE;
	$pages->items_per_page=_PAGINATOR_ITEMS_PER_PAGE; 
	$pages->paginate();
	
	//$query=mysql_query("SELECT * FROM `dis_budget` WHERE `status`=0  ORDER BY `created` DESC".$pages->limit)or die(mysql_error());


	$clients=budget_clients($id);
	$num=count($clients);
	//echo $num." ".print_r($clients);
	
	$total=0;
	while($num>=0){
		
		//echo $num." - ".$clients[$num]."<br/>";
		$query=mysql_query("SELECT * FROM `clients` WHERE `id`='".$clients[$num]."' AND status='0'")or die(mysql_error());
		while($dato=mysql_fetch_array($query)){
			$budget['data1']=$dato['page'];
			$budget['data2']=$dato['company'];
				$budget['url2']="?go=client&do=edit&id=".$dato['id']."&email=".$dato['email'];
			$budget['data3']=$dato['rep'];
			$budget['data4']=$dato['size'];
			$total+=$dato['size'];
			$budget['data5']=$dato['email'];
			//COLORESSSS!!!
			
			$query2=mysql_query("SELECT * FROM `dis_ads` WHERE `id_client`='".$clients[$num]."' AND `id_budget`='".$id."' AND `id_ads`='0'")or die(mysql_error());
			if(mysql_num_rows($query2)>0){
				while($dato2=mysql_fetch_array($query2)){
					$status=$dato2['status'];
					
				}
				if($status==2)
					$budget['data6']='<div class="status ok"></div>';
				if($status==1)
					$budget['data6']='<div class="status change"></div>';
				if($status==0)	
					$budget['data6']='<div class="status nothing"></div>';
			}else{
				$budget['data6']='<div class="status no"></div>';
			}
			
			
			
			//status
				$budget['url1']="?go=budget&do=ads&id=".$dato['id']."&id_budget=".$id;
				
			$url="budgetlistrows";
			$content['rows'].=$API->templateAdmin($url,$budget);
		}
		$num--;
		
	}
	
	$content['total']=$total;
	$content['page']=$pages->display_pages();
	$table='<h2>'.$titulo.'</h2>';
	$url="budgetlisttable";
	$table.=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}
//ADVERTSMENT
function budget_ads($id,$id_budget){
	$API= new API();
	$API->moduleName("budget");
		
	$js=file_get_contents("../modules/budget/admin/extra/upload.txt");

	$API->setJS($js);
	$url="budgetadsnew";
	
	$query=mysql_query("SELECT * FROM `clients` WHERE `id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$content['name']=$dato['company'];
	}
	
	
	
	$query=mysql_query("SELECT * FROM `dis_ads` WHERE `id_client`='".$id."' AND `id_budget`='".$id_budget."' AND `id_ads`='0' AND `status`<>3")or die(mysql_error());
	
	if(mysql_num_rows($query)>0){
		//Entramos a añadir nuevos pasos
			$content['board']="";
			$content['review']=1;
			while($dato=mysql_fetch_array($query)){
				$content['id_ad']=$dato['id'];
				$content['image']='<img src="uploads/'.$dato['image'].'" class="ads_image">';
				$access=$dato['access'];
				if($dato['comment']=="")
					$content['comment']="No comments";
				else
					$content['comment']=$dato['comment'];
				
				if($access==0)	
					$content['access']="Never";
				else
					$content['access']=date("m-d-Y H:i",$access);
				
				$content['date']=date("m-d-Y H:i",$dato['created']);
				$unique_id=$dato['unique_id'];
				
			}
			if($_GET['send']=="1"){
			
				$content['board']='<div class="notification green">The message has been sent</div>';
			}
			$content['URL']='http://www.socalcommunitypages.com/app/myad.php?id='.$unique_id;
			$total=mysql_query("SELECT * FROM `dis_ads` WHERE `id_client`='".$id."' AND `id_budget`='".$id_budget."' AND `status`<>3")or die(mysql_error());
			$content['num']=mysql_num_rows($total);
			$content['id']=$id;
			$content['id_budget']=$id_budget;
			$url="budgetads";
			$data=$API->templateAdmin($url,$content);
			$_SESSION['content']=$data;
			
			
			$query=mysql_query("SELECT * FROM `dis_ads` WHERE `id_client`='".$id."' AND `id_budget`='".$id_budget."' AND `id_ads`='".$content['id_ad']."' AND `status`<>'3'")or die(mysql_error());
			if(mysql_num_rows($query)>0){
				while($dato=mysql_fetch_array($query)){
					$content['id_ad']=$dato['id'];
					$content['image']='<img src="uploads/'.$dato['image'].'" class="ads_image">';
					$access=$dato['access'];
					$content['review']++;
					if($dato['comment']=="")
						$content['comment']="No comments";
					else
						$content['comment']=$dato['comment'];
					
					if($access==0)	
						$content['access']="Never";
					else
						$content['access']=date("m-d-Y H:i",$access);
				
					$content['date']=date("m-d-Y H:i",$dato['created']);
					
					$content['id_ad']=$dato['id'];
					$content['id_budget']=$id_budget;
					$content['id']=$id;
					
					
					$url="budgetadsadd";
					$data=$API->templateAdmin($url,$content);
					$_SESSION['content'].=$data;
					
					
					
					
				}
			}
			
			
			
			$API->printadmin();
	}else{
		//Creamos
		if($_GET['form']==1){
		
			if (!isset($_POST)){die('You can"t access this file directly');}//avoid direct accessing to this file.
 
 				    if (!empty($_FILES['filefield'])) { //check for image submitted
        				if ($_FILES['filefield']['error'] > 0) { // check for error re file
            				echo "Error: " . $_FILES["filefield"]["error"] . "<br />";
        				} else {
            				$file=$_FILES['filefield'];  //every thing fine. file successfully uploaded to server
        				}

    				} else {
        				die('File not uploaded.'); // exit script
    				}
				
 
 				$upload_directory='uploads/';
 				$ext_str = "gif,jpg,JPG,JPEG,jpeg,png,PNG,tiff,bmp,pdf";
 				$allowed_extensions=explode(',',$ext_str);
 				$max_file_size = 10485760;//10 mb remember 1024bytes =1kbytes
 				$overwrite_file = false;
					 /* 
					 upload directory check 
					  */
				 $status = true;
				 if (!is_dir($upload_directory)) { //check if upload directory exists or not
            		if ($mkdir) {
                		if (!mkdir($upload_directory)) { //if directory doesn't exists try to create it,if fails warn user
                		    $status = false;
                		} else {
                    		if (!chmod($upload_directory, 0777)) $status = false; //change file permisson to write,read,execute
                		}
            		} else {
            	    	$status = false;
            		}
				} 
				if(!$status){  //if can't make a directory warn the user and exit
					die('There is no uploaded directory or i can" create the upload directory');
				}
 
				/* 
				check allowed extensions here
				 */ 	
				$ext = substr($file['name'], strrpos($file['name'], '.') + 1); //get file extension from last sub string from last . character
				if (!in_array($ext, $allowed_extensions) ) {
					die('only'.$ext_str.' files allowed to upload '.$file['name'].' - '.$ext); // exit the script by warning
 				}
				/* 
				check file size of the file if it exceeds the specified size warn user
				*/
				 if($file['size']>=$max_file_size){
					die('only the file less than '.$max_file_size.'mb  allowed to upload'); // exit the script by warning
				}
 
				/* 
				check if the file already exists or not in the upload directory
				 */
 
				if(!$overwrite_file and file_exists($upload_directory.$file['name']) ){
					die('the file  '.$file['name'].' already exists.'); // exit the script by warning
				}
 				$unique_id=uniqid();	
 				$name=$unique_id."_photo.".$ext;
				//if(!move_uploaded_file($file['tmp_name'],$upload_directory.$file['name'])){
				if(!move_uploaded_file($file['tmp_name'],$upload_directory.$name)){
					 die('The file can"t moved to target directory..'); 
					 //file can't moved with unknown reasons likr cleaning of server temperory files cleaning
				}
				
			mysql_query("INSERT INTO `dis_ads` ( `id_client` , `id_budget` ,`created`,`image`,`unique_id`)
			VALUES ('".$id."', '".$id_budget."', '".time()."','".$name."','".$unique_id."');")
			or die(mysql_error());
			
			$API->goto("?go=budget&do=ads&id=".$id."&id_budget=".$id_budget);
		}else if($_GET['sent']=="1"){
			$content['id']=$id;
			$content['id_budget']=$id_budget;
			$content['password']="";
			$content['budgetcheck']="false";
			$content['emailcheck']="false";
			$content['board']='<div class="notification green">The message has been sent</div>';
		
			$data=$API->templateAdmin($url,$content);
			$_SESSION['content']=$data;
	
			$API->printadmin();		
		}else{
			$content['id']=$id;
			$content['id_budget']=$id_budget;
			$content['password']="";
			$content['budgetcheck']="false";
			$content['emailcheck']="false";
			$content['board']="";
		
			
			$data=$API->templateAdmin($url,$content);
			$_SESSION['content']=$data;
	
			$API->printadmin();

		}

	}
	
		
}


function budget_ads_review($id,$id_budget,$id_ad){

	$API= new API();
	$API->moduleName("budget");
		
	$js=file_get_contents("../modules/budget/admin/extra/upload.txt");

	$API->setJS($js);
	$url="budgetadsreview";
	
	$query=mysql_query("SELECT * FROM `clients` WHERE `id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$content['name']=$dato['company'];
	}
	
	//Creamos
		if($_GET['form']==1){
		
			if (!isset($_POST)){die('You can"t access this file directly');}//avoid direct accessing to this file.
 
 				    if (!empty($_FILES['filefield'])) { //check for image submitted
        				if ($_FILES['filefield']['error'] > 0) { // check for error re file
            				echo "Error: " . $_FILES["filefield"]["error"] . "<br />";
        				} else {
            				$file=$_FILES['filefield'];  //every thing fine. file successfully uploaded to server
        				}

    				} else {
        				die('File not uploaded.'); // exit script
    				}
				
 
 				$upload_directory='uploads/';
 				$ext_str = "gif,jpg,JPG,JPEG,jpeg,png,PNG,tiff,bmp,pdf";
 				$allowed_extensions=explode(',',$ext_str);
 				$max_file_size = 10485760;//10 mb remember 1024bytes =1kbytes
 				$overwrite_file = false;
					 /* 
					 upload directory check 
					  */
				 $status = true;
				 if (!is_dir($upload_directory)) { //check if upload directory exists or not
            		if ($mkdir) {
                		if (!mkdir($upload_directory)) { //if directory doesn't exists try to create it,if fails warn user
                		    $status = false;
                		} else {
                    		if (!chmod($upload_directory, 0777)) $status = false; //change file permisson to write,read,execute
                		}
            		} else {
            	    	$status = false;
            		}
				} 
				if(!$status){  //if can't make a directory warn the user and exit
					die('There is no uploaded directory or i can" create the upload directory');
				}
 
				/* 
				check allowed extensions here
				 */ 	
				$ext = substr($file['name'], strrpos($file['name'], '.') + 1); //get file extension from last sub string from last . character
				if (!in_array($ext, $allowed_extensions) ) {
					die('only'.$ext_str.' files allowed to upload '.$file['name'].' - '.$ext); // exit the script by warning
 				}
				/* 
				check file size of the file if it exceeds the specified size warn user
				*/
				 if($file['size']>=$max_file_size){
					die('only the file less than '.$max_file_size.'mb  allowed to upload'); // exit the script by warning
				}
 
				/* 
				check if the file already exists or not in the upload directory
				 */
 
				if(!$overwrite_file and file_exists($upload_directory.$file['name']) ){
					die('the file  '.$file['name'].' already exists.'); // exit the script by warning
				}
 				$unique_id=uniqid();	
 				$name=$unique_id."_photo.".$ext;
				//if(!move_uploaded_file($file['tmp_name'],$upload_directory.$file['name'])){
				if(!move_uploaded_file($file['tmp_name'],$upload_directory.$name)){
					 die('The file can"t moved to target directory..'); 
					 //file can't moved with unknown reasons likr cleaning of server temperory files cleaning
				}
				
			mysql_query("INSERT INTO `dis_ads` ( `id_client` , `id_budget`, `id_ads` ,`created`,`image`,`unique_id`)
			VALUES ('".$id."', '".$id_budget."', '".$id_ad."', '".time()."','".$name."','".$unique_id."');")
			or die(mysql_error());
			
			$API->goto("?go=budget&do=ads&id=".$id."&id_budget=".$id_budget."&id_ad=".$id_ad);	
		}else{
			$content['id']=$id;
			$content['id_budget']=$id_budget;
			$content['id_ad']=$id_ad;
			$content['password']="";
			$content['budgetcheck']="false";
			$content['emailcheck']="false";
			$content['board']="";
			
			$data=$API->templateAdmin($url,$content);
			$_SESSION['content']=$data;
	
			$API->printadmin();

		}

	
	
		
}


function budget_show_client($id){
	$API= new API();
	$API->moduleName("budget");
	$js=file_get_contents("../modules/budget/admin/extra/show.txt");

	$API->setJS($js);
	
	$query=mysql_query("SELECT * FROM `dis_budget` WHERE `id`='".$id."' AND `status`='0'")or die(mysql_error());
	$num=mysql_num_rows($query);
	if($num>0){

	$pages = new Paginator;
	
	$pages->url = "?go=budget";
	$pages->items_total = $num;
	$pages->mid_range =_PAGINATOR_MID_RANGE;
	$pages->items_per_page=_PAGINATOR_ITEMS_PER_PAGE; 
	$pages->paginate();
	
	$query=mysql_query("SELECT * FROM `clients` WHERE `status`=0  ORDER BY `created` DESC".$pages->limit)or die(mysql_error());



	$content['page']=$pages->display_pages();


	while($dato=mysql_fetch_array($query)){
		$budget['data1']=$dato['name'];
		
		$budget['data4']=date(_TIMEFORMAT,$dato['created']);
		
		
		
		$by=mysql_query("SELECT * FROM `users` WHERE `id`='".$dato['id_user_created']."'");
		while($take=mysql_fetch_array($by)){
			$budget['data5']=$take['username'];
		}
		

		//NUMBER OF CLIENTS
		
		$clients=budget_clients($dato['id']);
		
		    		 
    	$budget['data3'] = count($clients);
    		 		
		//_________________
		$budget['data2']="";
		$zones=mysql_query("SELECT * FROM `dis_zones_relation` WHERE `id_budget`='".$dato['id']."'");
		while($take=mysql_fetch_array($zones)){
			$zones2=mysql_query("SELECT * FROM `dis_zones` WHERE `id`='".$take['id_zone']."'");
				while($take2=mysql_fetch_array($zones2)){
					$budget['data2'].=$take2['name']." ";
				}
		}

		
		$url="budgetrows";
		$content['rows'].=$API->templateAdmin($url,$budget);
	}
	}else{
		$content['rows'].="";
	}
	
	
	$url="budgettable";
	$table=$API->templateAdmin($url,$content);
	$_SESSION['content']=$table;
	$API->printadmin();
}

function budget_clients($id){

		$clients=array();
		$i=0;
		
		$zones=mysql_query("SELECT * FROM `dis_zones_relation` WHERE `id_budget`='".$id."'");
		while($take=mysql_fetch_array($zones)){
			//echo $take['id_zone']."<br/>";
			$zones2=mysql_query("SELECT * FROM `dis_zones_relation` WHERE `id_zone`='".$take['id_zone']."' AND `id_budget`='0' AND `status`='0'");
			
				while($take2=mysql_fetch_array($zones2)){
				//echo $take2['id_client'];
				$client=mysql_query("SELECT * FROM `clients` WHERE `id`='".$take2['id_client']."' AND `status`='0'");
					while($client_data=mysql_fetch_array($client)){
						$month=$client_data['month'];
						$year=$client_data['year'];
						$term=$client_data['term'];
						
						while($term!=0){
							$month++;
							$term--;
							if($month==13){
								$month=1;
								$year++;
								//echo "cambio<br/>";
							}
						}
						
						if(strlen($month)==1)
							$month="0".$month;
						$fecha=$year.$month;
						
						$month_origen=$client_data['month'];
						if(strlen($client_data['month'])==1)
							$month_origen="0".$client_data['month'];
						
						$fecha_origen=$client_data['year'].$month_origen;
					}
					//echo $fecha." - ".$fecha_origen."<br/>";
					if(($fecha_origen<=date("Ym",time()))&&($fecha>=date("Ym",time()))){	
						//echo ">>> ".$fecha." - ".$fecha_origen."<br/>";
					if(!in_array($take2['id_client'],$clients)){
						//echo $take2['id_client']." ".$take2['id']."<br/>";
						$clients[$i]=$take2['id_client'];
						$i++;
					}
					}
				}
				//echo print_r($clients);
		}
		return $clients;
}
function budget_new(){
	$API= new API();
	$API->moduleName("budget");
	$API->setWHERE("Nuevo usuario");
	
	
	$js=file_get_contents("../modules/budget/admin/extra/show.txt");
	

	$API->setJS($js);
	$url="budgetnew";
	
	$info['company']="";
	$info['name']="";
	$info['id']="";
	$info['email']="";
	$info['cif']="";
	
	if($_GET['form']==1){
		$info['company']=$_POST['company'];
		$info['name']=$_POST['name'];
		$info['id']=$_POST['id'];
		$info['email']=$_POST['email'];
		$info['cif']=$_POST['cif'];
	
		if(($_POST['name']=="")||($_POST['email']=="")){
					
			
			$info['board']=$API->adminWarning("Wrong fields");

			$_SESSION['content']=$API->templateAdmin($url,$info);
			
			$API->printadmin();
		}else{
			/*mysql_query("INSERT INTO `dis_budget` ( `name` , `created` ,`id_user_created`)
			VALUES ('".$info['company']."', '".time()."', '".$_SESSION['login_id']."');")
			or die(mysql_error());*/
			
			$API->goto("?go=budget&do=step2&id=".$_POST['id']);
		}
	}else if($_GET['id']!=""){
		$query=mysql_query("SELECT * FROM `clients` WHERE `id`='".$_GET['id']."'")or die(mysql_error());
		while($dato=mysql_fetch_array($query)){
			$info['name']=$dato['name']." ".$dato['surname'];
			$info['cif']=$dato['cif'];
			$info['company']=$dato['company'];
			$info['id']=$dato['id'];
			$info['email']=$dato['email'];
		}
			
		$info['board']='<div class="notification green">Se ha añadido el cliente</div>';
		
		
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();

	}else{
		$info['board']="";
			
		$content['validation']="";
		
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();

	}
	
}

function array_remove_keys($array, $keys = array()) { 
  
    // If array is empty or not an array at all, don't bother 
    // doing anything else. 
    if(empty($array) || (! is_array($array))) { 
        return $array; 
    } 
  
    // If $keys is a comma-separated list, convert to an array. 
    if(is_string($keys)) { 
        $keys = explode(',', $keys); 
    } 
  
    // At this point if $keys is not an array, we can't do anything with it. 
    if(! is_array($keys)) { 
        return $array; 
    } 
  
    // array_diff_key() expected an associative array. 
    $assocKeys = array(); 
    foreach($keys as $key) { 
        $assocKeys[$key] = true; 
    } 
  
    return array_diff_key($array, $assocKeys); 
}

function budget_step2(){
	$API= new API();
	$API->moduleName("budget");
	$API->setWHERE("Nuevo usuario");
	
	
	$js=file_get_contents("../modules/budget/admin/extra/show.txt");
	
	if($_POST['id_item']){
		$materials = $_POST['id_material'];
		$items = $_POST['id_item'];
		$actions = $_POST['id_actions'];
		$component = $_POST['id_component'];
		//print_r($items);
		//print_r($materials);
		//print_r($actions);
	
	if(!isset($_SESSION['budget'])){
    	mysql_query("INSERT INTO `dis_budget`(`id_user`, `date`,`status`) VALUES ('".$_SESSION['budget_client']."', '".time()."', '1')") or die(mysql_error());
    	$_SESSION['budget']=mysql_insert_id();
    }
    $id=$_SESSION['budget'];
    
    mysql_query("DELETE FROM `dis_budget_quantity` WHERE `id_budget`='".$id."'") or die(mysql_error());

//PARA ITEMS

	for ($i=0; $i<count($items); $i++){
		//echo "Inserto item ".$items[$i].'<br/>';
        mysql_query("INSERT INTO `dis_budget_quantity`(`id_budget`, `id_item`,`quantity`) VALUES ('".$id."', '".$items[$i]."', '".$_POST['item_quantity'][$i]."')") or die(mysql_error());
     }  
     
     
     
    for ($i=0; $i<count($items); $i++){
    	
       	for ($c=0; $c<count($materials[$i]); $c++) {
        	if($materials[$i][$c]!=""){
        	mysql_query("INSERT INTO `dis_budget_quantity`(`id_budget`,`id_item`, `id_component`, `id_material`,`quantity`) 
        	VALUES ('".$id."','".$items[$i]."','".$component[$i][$c]."', '".$materials[$i][$c]."', '".$_POST['material_quantity'][$i][$c]."')") or die(mysql_error());
    			//echo "Inserto materiales ".$materials[$i][$c].'<br/>';
    			//echo "id_item: ".$items[$i]."<br/>";
    			//echo "id_material: ".$materials[$i][$c]."<br/>";
    			//echo "quantity: ".$_POST['material_quantity'][$i][$c]."<br/>";
    		}
    		
    	}
    }
    for ($i=0; $i<count($items); $i++){
    	for ($b=0; $b<count($actions[$i]); $b++) {
    		if($actions[$i][$b]!="")
        	mysql_query("INSERT INTO `dis_budget_quantity`(`id_budget`, `id_item`,`id_actions`,`quantity`) VALUES ('".$id."', '".$items[$i]."', '".$actions[$i][$b]."', '".$_POST['actions_quantity'][$i][$b]."')") or die(mysql_error());
    		//echo "Inserto acciones ".$actions[$i][$b].'<br/>';
    	}
    
    }
    
    }

//PARA MATERIALES
    
    
//PARA ACCIONES 



	
	$API->setJS($js);
	$url="budgetstep2";
	if($_SESSION['budget_client']=="")
		$_SESSION['budget_client']=$_GET['id'];
			
		
	$query=mysql_query("SELECT * FROM `clients` WHERE `id`='".$_SESSION['budget_client']."'")or die(mysql_error());
		while($dato=mysql_fetch_array($query)){
			$info['p_name']=$dato['name']." ".$dato['surname'];
			$info['p_cif']=$dato['cif'];
			$info['p_company']=$dato['company'];
			$info['p_email']=$dato['email'];
			$info['p_position']=$dato['position'];
			$info['p_telephone']=$dato['telephone'];
			$info['p_mobile']=$dato['mobile'];
			$info['p_street']=$dato['ad_street'];
			$info['p_city']=$dato['ad_town'];
			$info['p_province']=$dato['ad_province']." ".$dato['ad_country'];
			$info['p_zip']=$dato['ad_zip'];
			$info['p_id']=$_SESSION['budget_client'];
		}
	
	if($_GET['form']==1){
		$info['company']=$_POST['company'];
		$info['name']=$_POST['name'];
		$info['id']=$_POST['id'];
		$info['email']=$_POST['email'];
		$info['cif']=$_POST['cif'];
	
		if(($_POST['name']=="")||($_POST['email']=="")){
					
			
			$info['board']=$API->adminWarning("Wrong fields");

			$_SESSION['content']=$API->templateAdmin($url,$info);
			
			$API->printadmin();
		}else{
			/*mysql_query("INSERT INTO `dis_budget` ( `name` , `created` ,`id_user_created`)
			VALUES ('".$info['company']."', '".time()."', '".$_SESSION['login_id']."');")
			or die(mysql_error());*/
			
			$API->goto("?go=budget&do=step2&id=".$_POST['id']);
		}
	}else if($_GET['option']=="add"){
		
		
		//echo "<br/><br/><br/>…".count($_SESSION['items'])."<br/><br/><br/>---".$_POST['add_id_item']."<br/><br/>xxx".print_r($_SESSION['items'])."<br/><br/>";
		//echo count($_SESSION['items']);
		if(isset($_SESSION['items']) 	){
			if(!in_array($_POST['add_this'],$_SESSION['items'])){
				//$i=count($_SESSION['items']);
				//$i++;
				//$_SESSION['items'][$i]=$_POST['add_id_item'];
				
				
				$stack = $_SESSION['items'];
				array_push($_SESSION['items'], $_POST['add_this']);
				
				//echo "<br/><br/><br/><br/><br/><br/>asd<br/><br/><br/><br/><br/><br/>";
			}
		}else{
			$_SESSION['items']=array($_POST['add_this']);
		}
	//echo $_SESSION['items'][$i]."<br/>";
	
		
	
	$x=0;
		for ($i=0;$i<=count($_SESSION['items']);$i++){
				//echo $_SESSION['items'][$i]."<br/>";
			$total=0;
		$query=mysql_query("SELECT * FROM `dis_item` WHERE `id`='".$_SESSION['items'][$i]."'")or die(mysql_error());
		$url="budgetstep2rows";	
		
		while($dato=mysql_fetch_array($query)){
			//$quantity=0;
			
			//echo "budget:".$_SESSION['budget']."<br/>item: ".$_SESSION['items'][$i]."<br/>";
			$query_quantity=mysql_query("SELECT * FROM `dis_budget_quantity` WHERE `id_budget`='".$_SESSION['budget']."' AND id_item='".$_SESSION['items'][$i]."' AND `id_material`='0' AND `id_actions`='0'")or die(mysql_error());
			while($row=mysql_fetch_array($query_quantity)){
				$quantity=$row['quantity'];
			}
			
			$my_item_quantity=$quantity;
			
			
			$budget['name']="<b>".$dato['name']."</b>".'<input type="hidden" name="id_item['.$x.']" value="'.$dato['id'].'" >';
			$budget['color']="#BBB";
			$budget['data2']="";
			$budget['data4']="";
			$budget['data5']="";
			$budget['option']='<a href="?go=budget&do=step2&option=remove&id_item='.$_SESSION['items'][$i].'"  class="button red" onclick="return confirmar()">X</a>';
			$budget['data3']='<input type="text" name="item_quantity[]" class="budget-form" value="'.$quantity.'">';
			
			$info['rows'].=$API->templateAdmin($url,$budget);
			
			$query_component=mysql_query("SELECT * FROM `dis_component` WHERE `id_item`='".$_SESSION['items'][$i]."'")or die(mysql_error());
			$budget['name']="<ul>Componentes</ul>";
			$budget['color']="#e5e5e5";
			$budget['data2']="";
			$budget['data4']="";
			$budget['option']='';
			$budget['data3']='';
			$budget['data5']="";
			$info['rows'].=$API->templateAdmin($url,$budget);
			
			while($dato2=mysql_fetch_array($query_component)){
				$query_material_rel=mysql_query("SELECT * FROM `dis_component_material` WHERE `id_component`='".$dato2['id']."'")or die(mysql_error());
				$budget['name']="<ul>[ + ] ".$dato2['name']."</ul>";
			
				while($dato3=mysql_fetch_array($query_material_rel)){
					$query_material=mysql_query("SELECT * FROM `dis_material` WHERE `id`='".$dato3['id_material']."'")or die(mysql_error());
				
					while($dato4=mysql_fetch_array($query_material)){
						$budget['color']="#FFF";
						$budget['data2']=$dato4['name'].'<input type="hidden" name="id_material['.$x.'][]" value="'.$dato4['id'].'">'.
						'<input type="hidden" name="id_component['.$x.'][]" value="'.$dato2['id'].'">';
						$budget['data4']=$dato4['price']._COIN;
						$budget['option']="";
						
						
						
						$query_quantity=mysql_query("SELECT * FROM `dis_budget_quantity` WHERE `id_budget`='".$_SESSION['budget']."' AND id_item='".$_SESSION['items'][$i]."' AND `id_component`='".$dato2['id']."' AND `id_material`='".$dato3['id_material']."' AND `id_actions`='0'")or die(mysql_error());
						while($row=mysql_fetch_array($query_quantity)){
							$quantity=$row['quantity'];
						}
						
						$budget['data5']=$dato4['price']*$quantity;
						$budget['data5'].=_COIN;
						$total+=$budget['data5'];
						
						$budget['data3']='<input type="text" name="material_quantity['.$x.'][]" class="budget-form" value="'.$quantity.'">';
						$info['rows'].=$API->templateAdmin($url,$budget);
						$budget['name']="";
					}
				
				}
				
			}
			
			$budget['name']="<ul>Acciones</ul>";
			$budget['color']="#e5e5e5";
			$budget['data2']="";
			$budget['data4']="";
			$budget['option']='';
			$budget['data3']='';
			$budget['data5']='';
			$info['rows'].=$API->templateAdmin($url,$budget);
			$query_actions_rel=mysql_query("SELECT * FROM `dis_item_actions` WHERE `id_item`='".$_SESSION['items'][$i]."'")or die(mysql_error());
			while($dato3=mysql_fetch_array($query_actions_rel)){
					$query_material=mysql_query("SELECT * FROM `dis_actions` WHERE `id`='".$dato3['id_actions']."'")or die(mysql_error());
				
					while($dato4=mysql_fetch_array($query_material)){
						$budget['name']="";
						$budget['color']="#FFF";
						$budget['name']="<ul>[ + ] ".$dato4['name']."</ul>".'<input type="hidden" name="id_actions['.$x.'][]" value="'.$dato4['id'].'">';
						
						$query_quantity=mysql_query("SELECT * FROM `dis_budget_quantity` WHERE `id_budget`='".$_SESSION['budget']."' AND id_item='".$_SESSION['items'][$i]."' AND `id_material`='0' AND `id_actions`='".$dato3['id_actions']."'")or die(mysql_error());
						while($row=mysql_fetch_array($query_quantity)){
							$quantity=$row['quantity'];
						}
						$budget['data5']=$dato4['price']*$quantity;
						$total+=$budget['data5'];
						$budget['data5'].=_COIN;
						if($dato4['type']==1)
							$budget['data4']=$dato4['price']._COIN." por hora";
						else if($dato4['type']==2)
							$budget['data4']=$dato4['price']._COIN." por unidad";
						else
							$budget['data4']=$dato4['price']._COIN;
						
						$budget['option']="";
						$budget['data3']='<input type="text" name="actions_quantity['.$x.'][]" class="budget-form" value="'.$quantity.'">';
						$info['rows'].=$API->templateAdmin($url,$budget);
						$budget['name']="";
					}
				
				}
				
		}
			$budget['name']="";
			$budget['color']="#DDD";
			$budget['data2']="";
			$budget['data4']="";
			$budget['option']='';
			$budget['data3']='';
			$budget['data5']="<b>".$total*$my_item_quantity._COIN."<b/>";
			$info['rows'].=$API->templateAdmin($url,$budget);
		
		$x++;
		}
		
			
		$info['board']='<div class="notification green">Se ha añadido el artículo</div>';
		
		$url="budgetstep2";
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();
	}else if($_GET['option']=="remove"){
		
		for ($i=0;$i<=count($_SESSION['items']);$i++){
			if($_SESSION['items'][$i]==$_GET['id_item'])
				unset($_SESSION['items'][$i]);
		}
		//print_r($_SESSION['items']);
		
		$_SESSION['items']= array_values($_SESSION['items']);
		mysql_query("DELETE FROM `dis_budget_quantity` WHERE `id_item`='".$_GET['id_item']."'") or die(mysql_error());

		//print_r($_SESSION['items']);
		$API->goto("?go=budget&do=step2&option=add");
	
	}else{
		$info['board']="";
			
		$content['validation']="";
		$info['rows']="";
		$data=$API->templateAdmin($url,$info);
		$_SESSION['content']=$data;
	
		$API->printadmin();

	}
	
}


function budget_delete($id){
	$API= new API();
	mysql_query("UPDATE `dis_budget` SET `status` = '1' WHERE `id` ='".$id."'")or die(mysql_error());
	$API->goto("?go=budget");
}

function budget_ads_delete($id,$id_ad,$id_budget){
	$API= new API();
	//status 3 eliminado
	mysql_query("UPDATE `dis_ads` SET `status` = '3' WHERE `id` ='".$id_ad."'")or die(mysql_error());
	$API->goto("?go=budget&do=ads&id=".$id."&id_budget=".$id_budget);
}

function budget_edit($email,$id){
	$API= new API();
	$API->moduleName("budget");
	$API->setWHERE("Editar privilegios");
	
	$js=file_get_contents("../modules/budget/admin/extra/checkbudget.txt");

	$API->setJS($js);
	$url="budgetedit";
	
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
	
	
	$query=mysql_query("SELECT * FROM `dis_budget` WHERE `id`='".$id."'")or die(mysql_error());
	while($dato=mysql_fetch_array($query)){
		$info['username']=$dato['name'];

		$info['page']=$dato['page'];
		$info['info']=$dato['info'];
	}
		//ZONE
		$info['zone']="";
		
		$azones=array();
		$i=0;
		$query=mysql_query("SELECT * FROM `dis_zones_relation` WHERE `id_budget`='".$id."' AND status='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
					$azones[$i]=$dato['id_zone'];
					$i++;
			}
			//print_r($azones);
			
		$query=mysql_query("SELECT * FROM `dis_zones` WHERE `status`='0'")or die(mysql_error());
			while($dato=mysql_fetch_array($query)){
				if(in_array($dato['id'],$azones))
					$info['zone'].='<input type="checkbox" name="zones[]" checked="checked" value="'.$dato['id'].'">'.$dato['name'].'<br>';
				else
					$info['zone'].='<input type="checkbox" name="zones[]" value="'.$dato['id'].'">'.$dato['name'].'<br>';
			}
		
		
		
		//POSITION
		$info['position']='<select id="position" name="position">';
		$info['position'].='<option value="0">None</option>';
		$query=mysql_query("SELECT * FROM `dis_position` WHERE `status`='0'")or die(mysql_error());
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
		$query=mysql_query("SELECT * FROM `dis_zones_relation` WHERE `id_budget`='".$id."' AND status='0'")or die(mysql_error());
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
   	 		 		$exist=mysql_query("SELECT * FROM `dis_zones_relation` WHERE `id_budget`='".$id."' AND id_zone='".$zones[$i]."'")or die(mysql_error());
      				if(mysql_num_rows($exist)>0){
      					mysql_query("UPDATE `dis_zones_relation` SET `status` = '0' WHERE `id_budget` ='".$id."' AND id_zone='".$zones[$i]."'")or die(mysql_error());
					}else{
						mysql_query("INSERT INTO `dis_zones_relation` ( `id_budget` , `id_zone`)
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
    		 		mysql_query("UPDATE `dis_zones_relation` SET `status` = '1' WHERE `id` ='".$azonesid[$i]."';") or die(mysql_error());
    		 		sleep(0.5);
    		 	}
    		 }

		
		mysql_query("UPDATE `dis_budget` SET `name` = '".$name."' WHERE `id` ='".$id."'")or die(mysql_error());
		
			
		$API->goto("?go=budget&do=edit&form=2&id=".$id);
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

function budget_reset($email,$id,$budgetname){

		$API= new API();
			
		$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$cad = "";
		for($i=0;$i<8;$i++) {
			$cad .= substr($str,rand(0,62),1);
		}
		
		$info['password']=$cad;
		$info['budgetname']=$budgetname;
		//echo $id." ".$email; 	
		$info['name']=_webNAME;
		
		$url="budgetmailremember";
		$busqueda="UPDATE `budgets` SET `password` = '".md5($info['password'])."' WHERE `id` ='".$id."'";
		//echo $busqueda;
		mysql_query($busqueda) or die(mysql_error());
		
		$content=$API->templateAdmin($url,$info);
		
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
			// Additional headers
			$headers .= 'To: '.$info['email'].'\r\n';
			$headers .= 'From: SoCal no-reply <no-reply@socalcommunitypages.com>' . "\r\n";
		
		mail($email,"Your password for SoCal APP has been changed",$content,$headers); 

  		//$url="../modules/budget/admin/face/mail2.html";
  		//$content=$API->template($url,$info);
  		//$API->sendMail($email,"Su password de acceso ha sido reiniciado",$content);
		$API->goto("?go=budget");

	
}

function budget_check(){

	$API= new API();
	$API->moduleName("budget");
	$API->setWHERE("Validar usuario");

	$js=file_get_contents("../modules/budget/admin/extra/show.txt");
	$API->setJS($js);

	$query=mysql_query("SELECT * FROM `budgets` WHERE `status`='no'")or die(mysql_error());
	$num=mysql_num_rows($query);
	
	if($num>0){
		while($dato=mysql_fetch_array($query)){
			$budget['data1']=$dato['budgetname'];
			$budget['data2']=$dato['email'];
			$budget['data3']=date(_TIMEFORMAT,$dato['register_date']);
			$budget['data4']=$dato['status'];
			$budget['data5']=$dato['name'];
			$budget['data6']=$dato['surname'];
		
			$budget['url2']="?go=budget&do=validate&id=".$dato['id'];
			$budget['url1']="?go=budget&do=edit&email=".$dato['email']."&id=".$dato['id']."";
			$budget['url3']='?go=budget&do=delete&id='.$dato['id'].'&email='.$dato['email'];
			$url="../modules/budget/admin/face/rows2.html";
			$content['rows'].=$API->template($url,$budget);
		}
		$url="../modules/budget/admin/face/table2.html";
		$table=$API->template($url,$content);
		$_SESSION['content']=$table;
	}else{	
		$table=$API->adminWarning("no hay usuarios para validar");
		
		$_SESSION['content']=$table;
	}
	$API->printadmin();

}

function budget_validate($id){
	$API= new API();
	$busqueda="UPDATE `budgets` SET `status` = 'si' WHERE `id` ='".$id."'";
	mysql_query($busqueda)or die(mysql_error());
	
	$query=mysql_query("SELECT * FROM `budgets` WHERE `id`='".$id."'")or die(mysql_error());

		while($dato=mysql_fetch_array($query)){
			$name=$dato['name'];
			$email=$dato['email'];
		}	
		$mail = new phpmailer();

	
		 $mail->PluginDir = "../source/class/";
  			 $mail->Mailer = "smtp";

			 $mail->Host = "mail.urbansecurity.es";
 			 $mail->SMTPAuth = true;
			 $mail->budgetname = "noresponder@urbansecurity.es"; 
  			 $mail->Password = "adecu13";
			 $mail->From = "noresponder@urbansecurity.es";
  			 $mail->FromName = "noresponder@urbansecurity.es";
             $mail->Timeout=20;
  			 $mail->ClearAddresses();
  			 $mail->AddAddress($email);
  			 $mail->Subject = "Alta Urbansecurity";
  			 
  			 $url="../modules/budget/admin/face/mail3.html";
  			 $info['info']="<b>".$name."</b> ha sido dado de alta en Urbanscurity";
  			 $mail_content=$API->template($url,$info);
  			 
    		 $mail->Body = $mail_content;
			 $mail->AltBody = "Ha sido dado de alta en Urbansecurity";

  			$exito = $mail->Send();
 			 $intentos=1; 
 			 while ((!$exito) && ($intentos < 2)) {
				sleep(2);
     			$exito = $mail->Send();
     			$intentos=$intentos+1;	
			}
			

	
	$API->goto("?go=budget");

}
?>