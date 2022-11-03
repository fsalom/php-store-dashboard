<html>
<head>
	<style>
		body{
			font-family: Verdana;
			font-size: 12px;
		}
	</style>
</head>
<body>
<?php
error_reporting(E_ALL);

define("_SERVER","db449869918.db.1and1.com");
define("_USERNAME","dbo449869918");
define("_PASSWORD","osaka2011");
define("_BD","db449869918");

	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion){
		$API = new API();
		die($API->printerror(_MYSQLCONNECT));
	}
	mysql_select_db(_BD, $conexion);

//mysql_query("DELETE FROM `feed_people` WHERE id>671");

require_once 'excel/Classes/PHPExcel/IOFactory.php';
//$objPHPExcel = PHPExcel_IOFactory::load("compradores_marzo.xls");
$objWorksheet = $objPHPExcel->getActiveSheet();
$x=0;
$suma=0;
$sumaSUBTOTAL=0;



/*********************************************

ACTUALIZADO CON TODOS LOS DATOS DE JUNIO JULIO Y AGOSTO

*********************************************/
/*
	foreach ($objWorksheet->getRowIterator() as $row) {
		//echo " - ROW:".$x."</br>";
		$x++;
		//$row->getRowIndex(); Obtienen el numero de la fila
		
		 //Nos saltamos la primera linea
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);
			
			$numRow=$row->getRowIndex();
			$country=     $objWorksheet->getCell("A".$numRow)->getValue(); //SHOP
			$age=         $objWorksheet->getCell("C".$numRow)->getValue(); //DATE
			$gender=      $objWorksheet->getCell("D".$numRow)->getValue(); //TICKET
			$quantity=    $objWorksheet->getCell("E".$numRow)->getValue(); //ARTICLE NUMBER
			$date=        "03-2013";//ARTICLE NUMBER
			$created=     time();
			
			
			
			mysql_query("INSERT INTO  `feed_people` 
			( `country` ,
			  `age` ,
			  `gender` ,
			  `quantity` ,
			  `date`,
			  `created`
			 ) 
VALUES 
			( '".$country."',  
			  '".$age."', 
			  '".$gender."',
			  '".$quantity."', 
			  '".$date."',
			  '".$created."' 
			  );") or die(mysql_error());
			 
			echo $country." ".$age." ".$gender." ".$quantity." - INSERTADO<br/>";
				
			//echo getCell($numRow."8")->getValue();
			//$row->getCell('B8')->getValue();
			//foreach ($cellIterator as $cell) {
			//	echo "- ".$cell->getCoordinate();
			//	echo "- ".$cell->getValue()."</br>";
			//}
		
		
	}
*/
?> 
</body>
</html>