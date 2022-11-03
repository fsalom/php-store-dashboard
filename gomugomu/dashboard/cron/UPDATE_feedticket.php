<?php
/*********************************************************************************
		CRON PARA AÑADIR LOS TICKETS DIARIAMENTE A LA BASE DE DATOS
*********************************************************************************/

function get_data($begin){



$link = mysqli_connect("localhost", "gomugomu", "osaka2011", "admin_feedback");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


$link2 = mysqli_connect("localhost", "dashboard", "osaka2011", "admin_dashboard");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


echo "conectado";

if($begin!=false){
	echo $begin;
	$query = mysqli_query($link2, "SELECT * FROM `dash_ticket` WHERE id_ticket > ".$begin,MYSQLI_USE_RESULT);	
	
	while($row = mysqli_fetch_assoc($query)){
		$date = date( "d-m-Y H:i:s", strtotime( $row['date'] ) );
		 mysqli_query($link,"INSERT INTO feed_ticket (
										 id_ticket,
										 date,
										 articlenr,
										 description,
										 season,
										 size,
										 colour,
										 referencenr,
										 VAT,
										 items,
										 price,
										 priceList,
										 discount,
										 subtotal,
										 subtotalOriginal,
										 subtotalDiscounts,
										 created_date
										 ) 
								 VALUES ('".$row['id_ticket']."',
								 		 '".$date."',
								 		 '".$row['articleNr']."',
								 		 '".$row['description']."',
								 		 '".$row['season']."',
								 		 '".$row['size']."',
								 		 '".$row['colour']."',
								 		 '".$row['referencenr']."',
								 		 '".$row['vat']."',
								 		 '".$row['items']."',
								 		 '".$row['subtotal']."',
								 		 '".$row['subtotalOriginal']."',
								 		 '".$row['subtotalDiscounts']."',
								 		 '".$row['subtotal']."',
								 		 '".$row['subtotalOriginal']."',
								 		 '".$row['subtotalDiscounts']."',
								 		 now()
								 		 )") 
								 or die (mysql_error());

	 }	
}
echo "Ya esta";
	mysqli_free_result($query);
	mysqli_close($link);
	mysqli_close($link2);
}



function is_updated(){
/************************************************
	MYSQL
************************************************/

$link = mysqli_connect("localhost", "gomugomu", "osaka2011", "admin_feedback");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


$link2 = mysqli_connect("localhost", "dashboard", "osaka2011", "admin_dashboard");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


$result=mysqli_query($link,"SELECT * FROM `feed_ticket` ORDER BY id DESC LIMIT 1 ") or die (mysql_error());
$row = mysqli_fetch_array($result);
$mysql=$row['id_ticket'];
if($mysql=='')$mysql=0;
/************************************************
	MSSQL
************************************************/
$result=mysqli_query($link2,"SELECT * FROM `dash_ticket` ORDER BY id DESC LIMIT 1 ") or die (mysql_error());
$row = mysqli_fetch_array($result);
$mysql_updated=$row['id_ticket'];
if($mysql_updated=='')$mysql_updated=0;


mysqli_close($link);
mysqli_close($link2);

if($mysql_updated!=$mysql){
	echo $mysql_updated-$mysql.' registros<br/> Desde el ticket: '.$mysql.' hasta el '.$mysql_updated;
	return $mysql;
}else{
	echo 'la base de datos esta actualizada';
	return false;
	}

}

get_data(is_updated());
?>
