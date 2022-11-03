<?php 
error_reporting(0);
include("include/config.php");
include("include/class.FUNCIONES.php");
include("include/class.ADMIN.php");
include("include/class.APOYO.php");
include("include/class.BECOSOFT.php");
include("include/class.GOMUGOMU.php");
include("include/class.FORM.php");


$GOMUGOMU  = new GOMUGOMU();

?>
<!DOCTYPE html>


<html>

<head>
	<title></title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/general.css" />
	
	<link rel="stylesheet" type="text/css" href="css/button.css" media="screen" />
	
	
	<link href="css/jquery.circliful.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="js/Chart.js"></script>
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.circliful.js"></script>

	
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>

</head>
<body>





<div id="content">	
	<div class="item-search">
		<form method="GET" action="?">
			<input type="text" name="text-search" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" value="<?php echo $_GET["text-search"] ?>">
			<input type="submit" class="button" value="buscar">
		</form>
	</div>
	<div class="item-header">	
		<div class="row">
			Temp.
		</div>
	</div>
	<?php 	
	if($_GET["text-search"]!=""){

		$items = $GOMUGOMU->getTicketByReference($_GET["text-search"]);	
		echo($items);
		}	
	?>	
</div>	 
<script type="text/javascript">

	/*-----------------------------------------
					PARA LOS MENUS
	-----------------------------------------*/
	$(".item-main").click(function(){		
		$(".SHOW").hide();							
		$(this).find(".SHOW").show();
	});
</script>
</body>
</html>