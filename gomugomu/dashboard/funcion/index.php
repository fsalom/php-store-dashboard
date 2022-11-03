<?php
define("_SERVER","localhost");
define("_USERNAME","dashboard");
define("_PASSWORD","osaka2011");
define("_BD","admin_dashboard");

$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MSSQL: '.mysql_error());
	
mysql_select_db(_BD, $conexion);

$month=$_GET['month'];

$query=mysql_query("SELECT 
		referencenr,
		subtotal,
		subtotalOriginal,
		items
		FROM `dash_ticket` 
		WHERE `date` LIKE '%".$month."%' ") or die(mysql_error());
		
$total=0;
$coste=0;
$div=0;
$totalOriginal=0;
while($row=mysql_fetch_array($query)){
	echo $row['referencenr'].' '.$row['items'].' '.$row['subtotal'].' '.$row['subtotalOriginal'].'<br/>'; 
	if($row['referencenr']!="DIV"){
		$total+=($row['subtotal']*$row['items']);
		$totalOriginal+=($row['subtotalOriginal']*$row['items']);
		$coste+=($row['subtotalOriginal']*$row['items'])/2.94;
	}else{
		$div+=$row['subtotal'];
	}
}

echo 'Total: '.number_format($total,2,',','.').'<br/>';
echo 'Total precio completo: '.number_format($totalOriginal,2,',','.').'<br/>';

echo 'Total sin IVA: '.number_format($total/1.21,2,',','.').'<br/>';

echo 'Coste ropa: '.number_format($coste,2,',','.').'<br/>';

echo 'Margen bruto (Sin IVA, sin coste ropa, sin royalties): '.number_format((($total/1.21)-$totalOriginal-($total*0.03)),2,',','.').'<br/>';

echo 'Margen bruto (Sin IVA, sin coste ropa): '.number_format((($total/1.21)-$totalOriginal),2,',','.').'<br/>';

echo 'Total Vales: '.$div;

?>