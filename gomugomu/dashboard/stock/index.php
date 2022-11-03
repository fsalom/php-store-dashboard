<?php 
error_reporting(0);
include("include/config.php");
include("include/class.FUNCIONES.php");
include("include/class.ADMIN.php");
include("include/class.APOYO.php");
include("include/class.BECOSOFT.php");
include("include/class.GOMUGOMU.php");
include("include/class.FORM.php");


$BECOSOFT  = new BECOSOFT();

?>
<!DOCTYPE html>


<html>

<head>
	<title></title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/general.css" />
	<link rel="stylesheet" href="css/small.css" />	
	
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

<div class="ball-arc">
	</a><div class="point"><a href="index.php"><img src="img/gomu.png" alt="Gomu Gomu"></a></div>
</div>



<div id="content">	
	<div class="item-search">
		<form method="GET" action="?">
			<input type="text" name="text-search" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
			<input type="submit" class="button" value="buscar">
		</form>
	</div>
	<div class="item-header">	
		<div class="col s10">
			Temp.
		</div>
		<div class="col s60">
			Nombre
		</div>		
		<div class="col s15">
			Talla
		</div>		
		<div class="col s15">
			Stock
		</div>
		<div class="clear"></div>
	</div>
	<?php 	
	if($_GET["text-search"]!=""){
		$items	 = $BECOSOFT->searchArticle($_GET["text-search"]);	
		$count = count($items);		
		$lastReference ="";
		$color="#f1f1f1";
		$first=true;
		for($i=0;$i<$count;$i++) {
			if($lastReference!=$items[$i]->reference){					
				if($color=="#f1f1f1"){
					$color="#e5e5e5";
					if($first){
						$first=false;
					}else{
						?>
						</div></div>
						<?php 
					}
					?>
					
					<div class="item-main" style="background-color:#e5e5e5;">
						<div class="col s10">
							<b><?php echo $items[$i]->season;?></b>
						</div>
						<div class="col s60">
							<b><?php echo $items[$i]->name;?></b><br/>
							<?php echo $items[$i]->reference;?>
						</div>
						<div class="clear"></div>
					<div class="SHOW"><!-- ABRO -->
					<?php
				}else{
					$color="#f1f1f1";
					?>
					</div></div>
					<div class="item-main" style="background-color:#f1f1f1;">
						<div class="col s10">
							<b><?php echo $items[$i]->season;?></b>
						</div>
						<div class="col s60">
							<b><?php echo $items[$i]->name;?></b><br/>
							<?php echo $items[$i]->reference;?>
						</div>
						<div class="clear"></div>
					<div class="SHOW"><!-- ABRO -->
					<?php
				}
			}
	?>
		<div class="item-line" style="background-color:<?php echo $color?>;">		
			<div class="col s10">
				<?php echo $items[$i]->season;?>
			</div>
			<div class="col s60">
				<?php echo $items[$i]->name;?> | <?php echo $items[$i]->colour;?>
			</div>		
			<div class="col s15">
				<?php echo $items[$i]->size;?>
			</div>		
			<div class="col s15">
				<?php echo $items[$i]->stock;?>
			</div>
			<div class="clear"></div>
		</div>
	<?php 				
			$lastReference = $items[$i]->reference;			
		}
		
			?>
			</div>
			</div><!-- CIERRO -->
			<?php 
		
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