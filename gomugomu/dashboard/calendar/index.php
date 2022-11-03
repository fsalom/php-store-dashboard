<?php error_reporting(0); ?>
<!DOCTYPE html>
<!--
Versión 2.0 con conexión a BBDD de Becosoft directamente y de esa forma coger los datos evitando errores.
-->

<html>

<head>
	<title></title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300|Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/general.css" />
	<link rel="stylesheet" media="screen and (max-width: 600px)" href="css/small.css" />
	<link rel="stylesheet" media="screen and (min-width: 600px)" href="css/large.css" />
	<link rel="stylesheet" type="text/css" href="css/button.css" media="screen" />
	
	
	<link href="css/jquery.circliful.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="js/Chart.js"></script>
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.circliful.js"></script>

	
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>	
	
</head>
<body>

<div class="ball-arc">
	</a><div class="point"><a href="index.php"><img src="img/gomu.png" alt="Gomu Gomu"></a></div>
</div>


<?php
include_once("include/support.php");
date_default_timezone_set('Europe/Madrid');
?>




<div id="content">
	<?php
define("_SERVER_OLD","localhost");
define("_USERNAME_OLD","gomugomu");
define("_PASSWORD_OLD","osaka2011");
define("_BD_OLD","admin_feedback");

define("_SERVER","localhost");
define("_USERNAME","dashboard");
define("_PASSWORD","osaka2011");
define("_BD","admin_dashboard");

define("_MSSQL_SERVER","superdry.bscorp.be");
define("_MSSQL_USERNAME","Fernando");
define("_MSSQL_PASSWORD","S@l0m");
define("_MSSQL_DB","ES_Valencia");
$days = array( 'LU', 'MA', 'MI', 'JU', 'VI', 'SA', 'DO');
function query_money($day){
	$conexion = mysql_connect(_SERVER,_USERNAME,_PASSWORD);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
	mysql_select_db(_BD, $conexion);
	
	$query=mysql_query("SELECT SUM(quantity) FROM `dash_payment` WHERE date like '".$day."%' AND type!='Waardebon'")
	or die(mysql_error());
	
	$data=mysql_fetch_array($query);
	return round($data['SUM(quantity)'],0).'€';
	
}

function disp_input($month,$year){
	$months = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	$content = '<select name="month" id="month">';
	$n=1;
	$my=12;
			for($y=2012; $y<=date("Y",time());$y++){
				if($y==2012)
					$n=6;
				if($y==date("Y",time()))
					$my=date("m",time());

				for($m=$n;$m<=$my;$m++){	
					$m_name=$months[$m];
					
					if($month==$m && $year==$y){				
						$content.= '<option value="?m='.$m.'&y='.$y.'" selected="selected">'.$m_name.'-'.$y.'</option>';
					}else{
						$content.= '<option value="?m='.$m.'&y='.$y.'">'.$m_name.'-'.$y.'</option>';
					}
				}
				$n=1;
			}
	$content.= '</select>';
	return $content;
}

function draw_calendar($month,$year){

	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array('L','M','X','J','V','S','D');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,0,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np"> </td>';
		$days_in_this_week++;
	endfor;


			if($month<10 && $month[0]!='0')
				$m_='0'.$month;
			else
				$m_=$month;

	

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		$calendar.= '<td class="calendar-day">';
			/* add in the day number */
			$calendar.= $list_day;
			if($list_day<10)
				$d='0'.$list_day;
			else
				$d=$list_day;			
			$calendar.= '<br/><a href="https://gomugomu.es/dashboard/buyers/?date='.$d.'-'.$m_.'-'.$year.'"><b>'.query_money($year."-".$m_."-".$d).'</b></a>';
			
		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';
	
	/* all done, return result */
	return $calendar;
}
?>
			<?php
				if(!isset($_GET['m'])){
					$m=date('m',time());
					$y=date('Y',time());
				}else{
					$m=$_GET['m'];
					$y=$_GET['y'];
				}
				$draw= draw_calendar($m,$y);
				$ya=$y;
				$yn=$y;
				$mn=$m+1;
				$ma=$m-1;
				if($mn>12){ $mn=1; $yn=$y+1;}
				if($ma<1){ $ma=12; $ya=$y-1;}
				
				
			?>
			<div id="menu">
				
				<a href="?m=<?php echo $ma?>&y=<?php echo $ya?>">Anterior</a> 
				| <?php echo disp_input($m,$y) ?> |	
				<a href="?m=<?php echo $mn?>&y=<?php echo $yn?>">Siguiente</a> 
			</div>
		<div class="month">
			<?php echo $draw; ?>
		</div>
	<div class="clear"></div>
		
</div>
<script>
    $('#month').bind('change', function () { // bind change event to select
        var url = $(this).val(); // get selected value
        if (url != '') { // require a URL
            window.location = url; // redirect
        }
        return false;
    });
</script>	
</body>
</html>