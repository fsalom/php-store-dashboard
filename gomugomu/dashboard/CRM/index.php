<?php 
error_reporting(0);
include("include/config.php");
include("include/class.SHARE.php");
include("include/class.CLIENT.php");
include("include/class.APOYO.php");
include("include/class.FUNCIONES.php");
include("include/class.GOMUGOMU.php");
$CLIENT  = new CLIENTS();
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>	
	<link rel="stylesheet" href="css/bootstrap.min.css">	
	
	
	<link rel="stylesheet" media="screen and (max-width: 600px)" href="css/small.css" />
	<link rel="stylesheet" media="screen and (min-width: 600px)" href="css/general.css" />
	
	
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>	
	<script type="text/javascript" src="js/datatable/js/jquery.dataTables.min.js"></script> 	
	
	<script type="text/javascript">
	var notification="error";	
	
	$("document").ready(function(){
		$('#TClientes').DataTable( {
	        "language":{
	            "sProcessing":     "Procesando...",
	            "sLengthMenu":     "Mostrar _MENU_ registros",
	            "sZeroRecords":    "No se encontraron resultados",
	            "sEmptyTable":     "Ningún dato disponible en esta tabla",
	            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
	            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
	            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
	            "sInfoPostFix":    "",
	            "sSearch":         "Buscar:",
	            "sUrl":            "",
	            "sInfoThousands":  ",",
	            "sLoadingRecords": "Cargando...",
	            "oPaginate": {
	                "sFirst":    "Primero",
	                "sLast":     "Último",
	                "sNext":     "Siguiente",
	                "sPrevious": "Anterior"
	            },
	            "oAria": {
	                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
	                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
	            }
	        },	        
	        bFilter: false, 
	        bInfo: false, 
	        bLengthChange : false
	    });	

		$("table").delegate('td','mouseover mouseleave', function(e) {
		    if (e.type == 'mouseover') {
		      $(this).parent().addClass("hover");
		      $("colgroup").eq($(this).index()).addClass("hover");
		    }
		    else {
		      $(this).parent().removeClass("hover");
		      $("colgroup").eq($(this).index()).removeClass("hover");
		    }
		})	


	  	$( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });

	  	$( ".CLOSE" ).click(function() {$( "#NEW" ).hide( "slow", function() {location.reload();});});
		$( ".NEW-CLIENT" ).click(function() {$( "#NEW" ).show( "slow", function() {});});	 	

	 	$(".formulario_cliente").submit(function(){
		 	var data = {"action": "test"};
		    data = $(this).serialize() + "&" + $.param(data);
		    $.ajax({
		      type: "POST",
		      dataType: "json",
		      url: "ajax_form.php", //Relative or absolute path to response.php file
		      data: data,
		      success: function(data) {
		    	if(data['ok']==0){notification="success";}
		    	if(data['ok']==1){notification="warning";}
		    	if(data['ok']==2){notification="error";}
		 
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

	  
	  $("#search").keyup(function() {	
		$("#livesearch").empty();	  
	    var data = {"action": "test"};
	    data = $(this).serialize() + "&" + $.param(data);
	    $.ajax({
	      type: "POST",
	      dataType: "json",
	      url: "ajax_search.php", //Relative or absolute path to response.php file
	      data: data,
	      success: function(data) {	 		     	    			
	        $(data["search"]).appendTo("#livesearch");			
	      }
	    });
	    return false;
	  });

	  $(".formulario_perfil").submit(function(){
		 	var data = {"action": "test"};
		    data = $(this).serialize() + "&" + $.param(data);
		    $.ajax({
		      type: "POST",
		      dataType: "json",
		      url: "ajax_profile.php", //Relative or absolute path to response.php file
		      data: data,
		      success: function(data) {
		    	if(data['ok']==0){notification="success";}
		    	if(data['ok']==1){notification="warning";}
		    	if(data['ok']==2){notification="error";}
		 
		        $('<div class="alert-box '+notification+'">' +data["mensaje"] + '</div>').hide().appendTo(".mensaje-perfil").fadeIn("slow").delay(5000).fadeOut('slow');
		      }
		    });
		    return false;
		  });




	  $( "#livesearch" ).blur(function() {
		  $( "#search" ).val("");
		  $("#livesearch").empty();
		});

	  $(".item").click(function(){
			//$(".item_detail").hide();
			$(this).find(".item_detail").toggle();
		});
	
		$( ".campoEditable" ).click(function(){
			$(this).css( "background-color", "#FFF" );
			$(this).css( "color", "red" );
			$("#guardar").show();
		});
		
		$( ".campoEditable" ).focusout(function(){
			$(this).css( "background-color", "#FFF" );
			$(this).css( "color", "red" );
		});

		$( "#imagen" ).click(function(){		
			$("#guardarImagen").show();
		});
	});
	
	</script>
</head>
<body>
<div id="NEW" >

	<div id="content-NEW">
		<a href="#" class="CLOSE" id="CERRAR">X</a>
		
		<div class="table-content">		
		<form action="" class="formulario_cliente" method="post" accept-charset="utf-8">
			<div class="table-row">
				<div class="table-col s100">
					<select name="from" selected="selected">
						<option value="0">Tienda</option>
						<option value="1">Colegios</option>
						<option value="2">Red Bull</option>
						<option value="3">Fiesta animas</option>
				 	</select>
			 	</div>
			 	<div class="clear"></div>
		 	</div>
		 	<div class="table-row">
				<div class="table-col s100">
		 			<b>Datos personales:</b>
		 		</div>
		 		<div class="clear"></div>
		 	</div>	  
		 	<div class="table-row">
		 	<div class="table-col s20">
		 			 <select name="gender">
					    <option value="M">Hombre</option>
					    <option value="F">Mujer</option>
					  </select>	
					  	 			
		 		</div>
		 		<div class="clear"></div>
		 	</div>
		 	<div class="table-row">
		 		<div class="table-col s50">
		 			<input type="text" name="name" value="" placeholder="Nombre" required/>
		 		</div>
		 		<div class="table-col s50">
		 			<input type="text" name="surname" value="" placeholder="Apellidos" required/>
		 		</div>
		 		<div class="clear"></div>
		 	</div>
		 	<div class="table-row">
		 		<div class="table-col s100">
					<input type="text" name="address" value="" placeholder="Dirección"/>
		  		</div>
		  		<div class="clear">
		  		</div>
		 	</div>
		 	<div class="table-row">
		 		<div class="table-col s50">
		  			<input type="text" name="zipcode" value="" placeholder="Código Postal"/>
		  		</div>
		  		<div class="table-col s50">
		  			<input type="text" name="city" value="" placeholder="Ciudad"/>
		  		</div>
		  		<div class="clear">
		  		</div>
		 	</div>
		 	<div class="table-row">
		 		<div class="table-col s100">
		  			<select name="province">
						<?php echo $CLIENT->getProvince() ?>
					</select>
		  		</div>
		  		<div class="clear"></div>
		 	</div>
		 	<div class="table-row">
				<div class="table-col s100">
					<input type="email" name="email" value="" placeholder="Email"/>
		  		</div>
		  		<div class="clear">
		  		</div>
		  	</div>
		  	<div class="table-row">
				<div class="table-col s100">
					<input type="number" size="9" min="599999999" max="999999999" name="phone" value="" placeholder="Teléfono" />
		  		</div>
		  		<div class="clear">
		  		</div>
		  	</div>
		 	<div class="table-row">
		 		<div class="table-col s100">
		 			<input type="text" name="birthdate" value="" placeholder="Fecha de nacimiento (dd/mm/aaaa)" />
		 		</div>
		 		
		 		<div class="clear"></div>
		 	</div>
		 	
		  	<div class="table-row">
				<div class="table-col s100">
		 			<textarea rows="2" cols="" name="details"  placeholder="Detalles del cliente"></textarea>
		 		</div>
		 		<div class="clear"></div>
		 	</div>	 
		 	
		  	<div class="table-row">
				<div class="table-col s100">
					<div class="the-return"></div>
		 			<input type="submit" name="submit" value="Guardar" class="btn btn-success" />
		 		</div>
		 		<div class="clear"></div>
		 	</div>	  	
		    
		  
		  
		</form>	
		</div>
	</div>
</div>

<div id="main">	
	
	<div id="main-menu">
		<div class="col-md-6">
		<ul>
			<li><a href="#" class="NEW-CLIENT">Nuevo cliente</a></li>
			<li><a href="?">Listado de clientes</a></li>
			<li><a href="?go=listByBuyers">Listado de clientes más compras</a></li>			
			<li><a href="?go=mailing">Mailing</a></li>	
		</ul>
		</div>
		<div class="col-md-1">
			<div id="main-search">
				<form action="" class="formulario_busqueda" method="post" accept-charset="utf-8">
					<input type="text" size="30" id="search" name="search" autocomplete="off">				
				</form>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div id="livesearch"></div>
	<div id="main-content">
		
		<?php 
			switch ($_GET['go']){
				case 'profile':
					include_once("pages/clientProfile.php");
				break;
				case 'listByBuyers':
					include_once("pages/clientListBuyers.php");
				break;
				case 'mailing':
					include_once("pages/mailingCampaign.php");
				break;
				default:		
					if($_GET['do']=='disable')
						$CLIENT->disableClient($_GET['id']);
					include_once("pages/clientList.php");
				break;					
			}
		?>				
	</div>
</div>
</body>
</html>