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
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Las sorpresas de Richard</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px;
        font-family: 'Verdana';
        font-size: 10px;
        text-align: center;
      }
      #reload{      	
      	width:100%;
      	padding:10px;
      	background-color:#FFF;
      	position:absolute;
      	z-index:99999;
      	text-align:center;
      	border-bottom: 1px solid #CCC;	
	  }
      a{
	      display: block;
	      padding:10px;
	      background-color:#e5e5e5;
	      border:1px solid #DDD;
	      width:180px;
	      margin: 0 auto;
	      color: #333;
	      text-decoration: none;
      }
      #minion{
      	  position:absolute;
      	  width:80px;
      	  height:81px;
      	  top:50px;
      	  right:-10px;
      	  background-image: url('minion2.png');
      	  background-size:100%;
      	  z-index:999999;
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=geometry"></script>

    <script>
// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see a blank space instead of the map, this
// is probably because you have denied permission for location sharing.

 	//setInterval(function() {
 	//			setPos();
      //          }, 3000); 

var map;
var pistas;



//calculates distance between two points in km's
function calcDistance(p1, p2){
	  return (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1).toFixed(2);
}

function setPos(){
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
		var pos = new google.maps.LatLng(position.coords.latitude,
	            position.coords.longitude);
				map.setCenter(pos);
		});
	}
}
    
function initialize() {
  var mapOptions = {
    zoom: 15
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  // Try HTML5 geolocation
  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var pos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);
									
      
   
      <?php
	      $query 	= "SELECT * FROM activo where id='1'";
		  $result 	= mysql_query($query) or die(mysql_get_last_message());    	
		  $row 		= mysql_fetch_array($result);
		  
	      $query 	= "SELECT * FROM pistas where id='".$row['pista']."'";
		  $result 	= mysql_query($query) or die(mysql_get_last_message());    	
		  $row 		= mysql_fetch_array($result);
      ?>
      var destino   = new google.maps.LatLng(<?php echo $row['latitud'];?>, <?php echo $row['longitud'];?>);
      pistas	    = '<?php echo utf8_encode($row['pista']);?>';
      
      var distance= calcDistance(pos, destino);
      if(distance - <?php echo $row['margen'];?> <=0 && (5==<?php echo $row['id'];?> || 6==<?php echo $row['id'];?>))
	  	  pistas = '<img src="minion.png" width="150"><br/>Enhorabuena! llego el momento de llamar<br/><b> 622 024 006</b>';	  	
	  else
          pistas = '<b>'+pistas + '</b><br/>Estas a ' + distance + ' metros de tu proxima pista';
      
      var infowindow = new google.maps.InfoWindow({
        map: map,
        position: pos,
        content: pistas
      });

      map.setCenter(pos);
    }, function() {
      handleNoGeolocation(true);
    });
  } else {
    // Browser doesn't support Geolocation
    handleNoGeolocation(false);
  }
}

function handleNoGeolocation(errorFlag) {
  if (errorFlag) {
    var content = 'Error: The Geolocation service failed.';
  } else {
    var content = 'Error: Your browser doesn\'t support geolocation.';
  }

  var options = {
    map: map,
    position: new google.maps.LatLng(60, 105),
    content: content
  };

  var infowindow = new google.maps.InfoWindow(options);
  map.setCenter(options.position);
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
  	<div id="minion"></div>
  	<div id="reload"><a href="/richard">RECARGAR</a></div>
    <div id="map-canvas"></div>
  </body>
</html>