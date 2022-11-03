<?php 
error_reporting(E_ALL);
include("config.php");
include("class.SHARE.php");
include("class.CLIENT.php");
$CLIENT  = new CLIENTS();
?>
<!DOCTYPE html>
<!--
Versión 2.0 con conexión a BBDD de Becosoft directamente y de esa forma coger los datos evitando errores.
-->

<html>

<head>
	<title></title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<style>
		html, body{
		color:#000;
		font-family:Tahoma,Geneva,Arial,sans-serif;
		font-size:12px;
		margin:0 auto;
		background-color:#FF9900;
		}
		#content{
		margin:  0 auto;
		margin-top: 10px;
		background-color: #FFF;
		width: 300px;
		padding: 10px;
		padding-top: 10px;
		border-radius: 5px;
		-moz-border-radius: 5px;
		-webkit-border-radius: 5px;
		}
		#content input, textarea, select{
		padding:10px;
		width:278px;
		border: 1px solid #E5E5E5;
		margin: 5px 0;
		background-color:#FAFAFA;
		font-family: Verdana;
		font-size:12px;
		color:#000;
		outline:none;
		}
		.the-return{
			width:278px;			
			height:70px;
			margin:0 auto;
			padding-top:20px;
		}
		.alert-box {
		width:213px;
		color:#555;
		border-radius:5px;		
		padding:10px 36px;		
		float:left;		
		}
		
		.alert-box span {
			font-weight:bold;
			text-transform:uppercase;
		}
		.error {
			background:#ffecec url('img/notification/error.png') no-repeat 10px 50%;
			
		}
		.success {
			background:#e9ffd9 url('img/notification/success.png') no-repeat 10px 50%;
			
		}
		.warning {
			background:#fff8c4 url('img/notification/warning.png') no-repeat 10px 50%;
			
		}
		.notice {
			background:#e3f7fc url('img/notification/notice.png') no-repeat 10px 50%;
			
		}
		
		.button_app
    {        
        display: inline-block;
        white-space: nowrap;
        background-color: #ddd;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#eee), to(#ccc));
        background-image: -webkit-linear-gradient(top, #eee, #ccc);
        background-image: -moz-linear-gradient(top, #eee, #ccc);
        background-image: -ms-linear-gradient(top, #eee, #ccc);
        background-image: -o-linear-gradient(top, #eee, #ccc);
        background-image: linear-gradient(top, #eee, #ccc);
        border: 1px solid #777;
        padding: 0.5em 1.5em;
        margin: 0.5em;        
        text-decoration: none;        
        cursor:pointer;
        color: #333;
        text-shadow: 0 1px 0 rgba(255,255,255,.8);
        -moz-border-radius: .2em;
        -webkit-border-radius: .2em;
        border-radius: .2em;
        -moz-box-shadow: 0 0 1px 1px rgba(255,255,255,.8) inset, 0 1px 0 rgba(0,0,0,.3);
        -webkit-box-shadow: 0 0 1px 1px rgba(255,255,255,.8) inset, 0 1px 0 rgba(0,0,0,.3);
        box-shadow: 0 0 1px 1px rgba(255,255,255,.8) inset, 0 1px 0 rgba(0,0,0,.3);
    }
    
    .button_app:hover
    {
        background-color: #eee;        
        background-image: -webkit-gradient(linear, left top, left bottom, from(#fafafa), to(#ddd));
        background-image: -webkit-linear-gradient(top, #fafafa, #ddd);
        background-image: -moz-linear-gradient(top, #fafafa, #ddd);
        background-image: -ms-linear-gradient(top, #fafafa, #ddd);
        background-image: -o-linear-gradient(top, #fafafa, #ddd);
        background-image: linear-gradient(top, #fafafa, #ddd);
    }
    
    .button_app:active
    {
        -moz-box-shadow: 0 0 4px 2px rgba(0,0,0,.3) inset;
        -webkit-box-shadow: 0 0 4px 2px rgba(0,0,0,.3) inset;
        box-shadow: 0 0 4px 2px rgba(0,0,0,.3) inset;
        position: relative;
        top: 1px;
    }
    
    .button_app:focus
    {
        outline: 0;
        background: #fafafa;
    }    
    
    .button_app:before
    {
        background: #ccc;
        background: rgba(0,0,0,.1);
        float: left;        
        width: 1em;
        text-align: center;
        font-size: 1.5em;
        margin: 0 1em 0 -1em;
        padding: 0 .2em;
        -moz-box-shadow: 1px 0 0 rgba(0,0,0,.5), 2px 0 0 rgba(255,255,255,.5);
        -webkit-box-shadow: 1px 0 0 rgba(0,0,0,.5), 2px 0 0 rgba(255,255,255,.5);
        box-shadow: 1px 0 0 rgba(0,0,0,.5), 2px 0 0 rgba(255,255,255,.5);
        -moz-border-radius: .15em 0 0 .15em;
        -webkit-border-radius: .15em 0 0 .15em;
        border-radius: .15em 0 0 .15em;     
        pointer-events: none;		
    }    
    </style>
  	
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">	
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	
	<script type="text/javascript">

	var notification="error";
	
	$("document").ready(function(){
	  $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
	  $(".formulario_cliente").submit(function(){
	    var data = {
	      "action": "test"
	    };
	    data = $(this).serialize() + "&" + $.param(data);
	    $.ajax({
	      type: "POST",
	      dataType: "json",
	      url: "ajax.php", //Relative or absolute path to response.php file
	      data: data,
	      success: function(data) {
	    	if(data['ok']==0){
	    		notification="success";	    		
			}
	    	if(data['ok']==1){
	    		notification="warning";
			}
	    	if(data['ok']==2){
	    		notification="error";
			}
	 
	        $('<div class="alert-box '+notification+'">' +data["mensaje"] + '</div>').hide().appendTo(".the-return").fadeIn("slow").delay(5000).fadeOut('slow');
			if(notification!="error"){
		        $("form [name=name]").val("");
		        $("form [name=surname]").val("");
		        $("form [name=birthdate]").val("");
		        $("form [name=gender]").val("");
		        $("form [name=email]").val("");
		        $("form [name=phone]").val("");
		        $("form [name=address]").val("");
		        $("form [name=city]").val("");
		        $("form [name=province]").val("");
		        $("form [name=zipcode]").val("");
		        $("form [name=details]").val("");
			}
	      }
	    });
	    return false;
	  });
	});
	</script>
</head>
<body>
<div class="the-return"></div>
<div id="content">
	<form action="return.php" class="formulario_cliente" method="post" accept-charset="utf-8">
		<select name="from" selected="selected">
			<option value="0">Tienda</option>
			<option value="1">Colegios</option>
			<option value="2">Red Bull</option>
			<option value="3">Fiesta animas</option>
	 	</select>
	  <b>Datos personales:</b>	  
	  <input type="text" name="name" value="" placeholder="Nombre" required/>
	  <input type="text" name="surname" value="" placeholder="Apellidos" required/>
	  <input type="text" name="birthdate" value="" placeholder="Fecha de nacimiento (dd/mm/aaaa)" />
	  <select name="gender">
	    <option value="M">Hombre</option>
	    <option value="F">Mujer</option>
	  </select>
	  <textarea rows="" cols="" name="details"  placeholder="Detalles del cliente"></textarea>
	  
	  <b>Datos de contacto:</b>
	  <input type="email" name="email" value="" placeholder="Email"/>
	  <input type="number" size="9" min="599999999" max="999999999" name="phone" value="" placeholder="Teléfono" />
	  
	  <b>Dirección:</b>	  
	  <input type="text" name="address" value="" placeholder="Dirección"/>
	  <input type="text" name="city" value="" placeholder="Ciudad"/>	
	  <select name="province">
		<?php echo $CLIENT->getProvince() ?>
	  </select>  
	  <input type="text" name="zipcode" value="" placeholder="Código Postal"/>	  
	  
	  
	  
	  <input type="submit" name="submit" value="Guardar" class="button_app" />
	</form>
	 
</div>
</body>
</html>