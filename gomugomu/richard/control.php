<?php
	define("_MYSQL_SERVER","localhost");
	define("_MYSQL_USERNAME","richard");
	define("_MYSQL_PASSWORD","osaka2011");
	define("_MYSQL_BD","admin_richard");
	define("_MYSQL_ERROR_DB","Couldn't open database");
	define("_MYSQL_ERROR","Something went wrong while connecting to MSSQL:");
	$conexion = mysql_connect(_MYSQL_SERVER,_MYSQL_USERNAME,_MYSQL_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	mysql_select_db(_MYSQL_BD, $conexion); 
	
	if($_GET['go']=='update'){
		$query 	= "UPDATE activo set pista='".$_GET['id']."' where id='1'";
		$result = mysql_query($query) or die(mysql_get_last_message()); 
	}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Geolocation</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px;
        font-family: 'Verdana';
        font-size: 10px;
        background-color: #FF9900;
        text-align: center;
      }
      #content{      	
      	width:280px;
      	padding:10px;
      	margin:0 auto;
      	margin-top:30px;
      	background-color:#FFF;
      	text-align:center;
      	
      }
      .activo{
	      display: block;
	      background-color: #FF9900;
	      padding: 10px;
	      margin: 5px 0;
	      color:#FFF;
	      text-decoration: none;
      }
      .boton{
	      display: block;
	      background-color: #e5e5e5;
	      padding: 10px;
	      margin: 5px 0;
	      text-decoration: none;
      }
      a:hover{
	      
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=geometry"></script>

  </head>
  <body>
	<div id="content">
  	<?php
	  	$query 	= "SELECT * FROM activo where id='1' ";
		$result = mysql_query($query) or die(mysql_get_last_message());    	
		$row 	= mysql_fetch_array($result);
		$pista 	= $row['pista'];		

		$query 	= "SELECT * FROM pistas ";
		$result = mysql_query($query) or die(mysql_get_last_message());    	
		$num 	= mysql_num_rows($result);
		while($row	= mysql_fetch_array($result)){
			if($pista==$row['id'])
				$class="activo";
			else
				$class="boton";
			echo '<a href="?go=update&id='.$row['id'].'" class="'.$class.'">'.utf8_encode($row['pista']).'</a>';
		}
		
	?>
	</div>
  </body>
</html>