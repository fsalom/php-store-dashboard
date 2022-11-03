<?php
define("_SERVER","localhost");
define("_USERNAME","dashboard");
define("_PASSWORD","osaka2011");
define("_BD","admin_dashboard");


// Function to get all the dates in given range 
function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
      
    // Declare an empty array 
    $array = array(); 
      
    // Variable that store the date interval 
    // of period 1 day 
    $interval = new DateInterval('P1D'); 
  
    $realEnd = new DateTime($end); 
    $realEnd->add($interval); 
  
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
  
    // Use loop to store date into array 
    foreach($period as $date) {                  
        $array[] = $date->format($format);  
    } 
  
    // Return the array elements 
    return $array; 
} 
   

function getThis($day_start, $month_start, $day_end, $month_end, $year){
    $conexion =mysqli_connect(_SERVER,_USERNAME,_PASSWORD,_BD	);
	if (!$conexion)
		die('Something went wrong while connecting to MYSQL: '.mysql_error());
  $query=mysqli_query($conexion, "SELECT SUM(CASE WHEN `type` <> 'Waardebon' AND `type` <> 'Korting' THEN quantity ELSE 0 END), CAST(date AS DATE) FROM `dash_payment` WHERE `date` BETWEEN '".$year."-".$month_start."-".$day_start."' AND '".$year."-".$month_end."-".$day_end."' GROUP BY CAST(date AS DATE)")
  or die(mysql_error());

  // Function call with passing the start date and end date 
  $days = getDatesFromRange($year.'-'.$month_start.'-'.$day_start, $year.'-'.$month_end.'-'.$day_end); 
  
  
  while($row=mysqli_fetch_array($query)){      
    $daysOpen[$row['1']] = round($row["0"],2);
    // echo $row['1'];
    // echo $daysOpen[$row['1']];
  }

  $stats = "";
  foreach ($days as $key => $value) {
    $newValue = 0;
    if(array_key_exists($value, $daysOpen)){
      $newValue = $daysOpen[$value];
    }
  
    $stats.= $newValue.',';
  }


  return $stats;
}

$day_start = '01';
$month_start = '01';
$day_end = '31';
$month_end = '12';
if ($_GET['range']=='on'){
    $day_start=$_POST['day_start'];
    $month_start=$_POST['month_start'];
    $day_end=$_POST['day_end'];
    $month_end=$_POST['month_end'];
}

$months=['enero','febrero','marzo','abril','mayo', 'junio','julio','agosto','septiebre','octubre', 'noviembre','diciembre'];

?>

<!DOCTYPE html>
<html>
  <head>
    <style>

body, html{
    background-color: #FF9900;
    font-family: Verdana, sans-serif;
    font-size: 1.2em;
}

.range-selector{
    background-color: #FFFFFF;
    border-radius: 5px;
    padding: 10px;
    font-size: 12px;
}

.highcharts-figure, .highcharts-data-table table {
    min-width: 1000px; 
    max-width: 2000px;
    margin: 1em auto;
    background-color: #FF9900;
    border-radius: 5px;
}

.highcharts-data-table table {
  border-collapse: collapse;
  border: 1px solid #EBEBEB;
  margin: 10px auto;
  text-align: center;
  width: 100%;
  max-width: 2000px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    color: #555;
}
.highcharts-data-table th {
  font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
    </style>
  </head>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<div class="range-selector">
<form method="post" id="range" action="?range=on">
    <b>INICIO</b>
    <select id="day_start" name="day_start">
        <?php
            for ($x = 1; $x < 32; $x++){    
                $num_padded = sprintf("%02d", $x);
                $option = '<option value="'.$num_padded.'" ';
                if($day_start==$num_padded){
                    $option.='selected="selected"';
                }
                $option.= '>'.$x.'</option>';
                echo $option;
            }
        ?>
    </select>
    <select id="month_start" name="month_start">
        <?php
            for ($x = 1; $x < 13; $x++){    
                $num_padded = sprintf("%02d", $x);
                $option = '<option value="'.$num_padded.'" ';
                if($month_start==$num_padded){
                    $option.='selected="selected"';
                }
                $option.= '>'.$months[$x-1].'</option>';
                echo $option;
            }
        ?>
    </select>
    
    <b> FIN </b>

    <select id="day_end" name="day_end">
        <?php
            for ($x = 1; $x < 32; $x++){    
                $num_padded = sprintf("%02d", $x);
                $option = '<option value="'.$num_padded.'" ';
                if($day_end==$num_padded){
                    $option.='selected="selected"';
                }
                $option.= '>'.$x.'</option>';
                echo $option;
            }
        ?>
    </select>

    <select id="month_end" name="month_end">
        <?php
            for ($x = 1; $x < 13; $x++){    
                $num_padded = sprintf("%02d", $x);
                $option = '<option value="'.$num_padded.'" ';
                if($month_end==$num_padded){
                    $option.='selected="selected"';
                }
                $option.= '>'.$months[$x-1].'</option>';
                echo $option;
            }
        ?>
    </select>
    
    <button type="submit" form="range" value="submit">Buscar</button>

</form>
</div>

<figure class="highcharts-figure">
    <div id="container"></div>
    <p class="highcharts-description">

    </p>
</figure>


<script>


Highcharts.chart('container', {
    chart: {
        type: 'spline',
        scrollablePlotArea: {
            minWidth: 1000,
            scrollPositionX: 1
        },
        height: 800,
    },
    title: {
        text: 'Cantidad por día',
        align: 'left'
    },
    subtitle: {
        text: 'Desde 2017 a 2020',
        align: 'left'
    },
    xAxis: {
        type: 'datetime',
        labels: {
            overflow: 'justify'
        }
    },
    yAxis: {
        title: {
            text: 'Euros'
        },
        minorGridLineWidth: 0,
        gridLineWidth: 0,
        alternateGridColor: null,
    },
    tooltip: {
        valueSuffix: ' €'
    },
    plotOptions: {
        spline: {
            lineWidth: 2,
            states: {
                hover: {
                    lineWidth: 3
                }
            },
            marker: {
                enabled: false
            },
            pointInterval: 86400000, // one day
        }
    },
    series: [{
        name: '2017',
        data: [
            <?php echo getThis($day_start, $month_start, $day_end, $month_end, 2017) ?>
        ]
    }, {
        name: '2018',
        data: [
            <?php echo getThis($day_start, $month_start, $day_end, $month_end, 2018) ?>
        ]
    }, {
        name: '2019',
        data: [
            <?php echo getThis($day_start, $month_start, $day_end, $month_end, 2019) ?>
        ]
    }, {
        name: '2020',
        data: [
            <?php echo getThis($day_start, $month_start, $day_end, $month_end, 2020) ?>
        ]
    }],
    navigation: {
        menuItemStyle: {
            fontSize: '10px'
        }
    }
});

</script>
</html>