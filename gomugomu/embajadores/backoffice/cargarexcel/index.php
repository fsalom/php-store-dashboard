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
	
require_once 'excel/Classes/PHPExcel/IOFactory.php';

/*INSERTADO HASTA EL 11 de AGOSTO*/
/*
*
*
* CAMBIAR ESTO*/
//$objPHPExcel = PHPExcel_IOFactory::load("marzo_fer.xls");
/*
*
*
*/
//mysql_query("DELETE FROM `feed_ticket` WHERE id>3768");


$objWorksheet = $objPHPExcel->getActiveSheet();
$x=0;
$suma=0;
$sumaSUBTOTAL=0;
	foreach ($objWorksheet->getRowIterator() as $row) {
		$x++;
		
		if ($row->getRowIndex()!=1){ //Nos saltamos la primera linea
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);
			
			
			
			$numRow=$row->getRowIndex();
			$shop= $objWorksheet->getCell("A".$numRow)->getValue(); //SHOP
			$date= $objWorksheet->getCell("B".$numRow)->getValue(); //DATE
			
						
			$ticket= $objWorksheet->getCell("C".$numRow)->getValue(); //TICKET
			$articleNr= $objWorksheet->getCell("D".$numRow)->getValue(); //ARTICLE NUMBER
			$description= str_replace("'","",$objWorksheet->getCell("E".$numRow)->getValue()); //ARTICLE NUMBER
			$season= $objWorksheet->getCell("F".$numRow)->getValue(); //SEASON
			$size= $objWorksheet->getCell("G".$numRow)->getValue(); //SIZE
			//$bodytag = str_replace("%body%", "black", "<body text='%body%'>");
			$colour= str_replace("'","",$objWorksheet->getCell("H".$numRow)->getValue()); //COLOUR
			$referenceNr= str_replace("'","",$objWorksheet->getCell("I".$numRow)->getValue()); //REFERENCE NUMBER
			$vat18= $objWorksheet->getCell("J".$numRow)->getValue(); //VAT 18%
			$priceBefore= $objWorksheet->getCell("K".$numRow)->getValue(); //PRICE VAT EXCLUDED
			$priceList= $objWorksheet->getCell("L".$numRow)->getValue(); //LIST PRICE
			$price= $objWorksheet->getCell("M".$numRow)->getValue(); //PRICE
			$discount= $objWorksheet->getCell("N".$numRow)->getValue(); //DISCOUNT
			$items= $objWorksheet->getCell("O".$numRow)->getValue(); //ITEMS
			$VATeuros= $objWorksheet->getCell("P".$numRow)->getValue(); //VAT(Euros)
			$subtotal= $objWorksheet->getCell("Q".$numRow)->getValue(); //SUBTOTAL
			$subtotalOriginal= $objWorksheet->getCell("R".$numRow)->getValue(); //SUBTOTAL ORIGINAL PRICE
			$subtotalDiscounts= $objWorksheet->getCell("S".$numRow)->getValue(); //SUBTOTAL DISCOUNTS
			
			$created_date=time();
			
			mysql_query("INSERT INTO  `feed_ticket` 
			( `id_ticket` ,
			  `date` ,
			  `articlenr` ,
			  `description` ,
			  `season` ,
			  `size` , 
			  `colour` ,
			  `referencenr` ,
			  `VAT` ,
			  `priceBefore` ,
			  `priceList` ,
			  `price` ,
			  `discount` ,
			  `items` ,
			  `VATeuros` ,
			  `subtotal` ,  
			  `subtotalOriginal` ,  
			  `subtotalDiscounts` , 
			  `created_date` ) 
VALUES 
			( '".$ticket."',  
			  '".$date."', 
			  '".$articleNr."',
			  '".$description."', 
			  '".$season."', 
			  '".$size."', 
			  '".$colour."', 
			  '".$referenceNr."', 
			  '".$vat18."', 
			  '".$priceBefore."', 
			  '".$priceList."', 
			  '".$price."',  
			  '".$discount."',  
			  '".$items."', 
			  '".$VATeuros."', 
			  '".$subtotal."', 
			  '".$subtotalOriginal."', 
			  '".$subtotalDiscounts."', 
			  '".$created_date."' 
			  );") or die(mysql_error());
				
	
		
			echo "Insertado el ticket: ".$ticket." - Referencia: ".$referenceNr." - Descripcion: ".$description." - Color: ".$colour." - Size: ".$size." - Fecha: ".$date." - ".$price."<br/>";
		}
	}

?> 
</body>
</html>