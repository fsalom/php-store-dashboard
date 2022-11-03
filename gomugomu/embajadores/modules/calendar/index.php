<?php

$API = new API();
$tipo_semana = 1;
$tipo_mes = 0;

$MESCOMPLETO[1] = 'Enero';
$MESCOMPLETO[2] = 'Febrero';
$MESCOMPLETO[3] = 'Marzo';
$MESCOMPLETO[4] = 'Abril';
$MESCOMPLETO[5] = 'Mayo';
$MESCOMPLETO[6] = 'Junio';
$MESCOMPLETO[7] = 'Julio';
$MESCOMPLETO[8] = 'Agosto';
$MESCOMPLETO[9] = 'Septiembre';
$MESCOMPLETO[10] = 'Octubre';
$MESCOMPLETO[11] = 'Noviembre';
$MESCOMPLETO[12] = 'Diciembre';

$MESABREVIADO[1] = 'Ene';
$MESABREVIADO[2] = 'Feb';
$MESABREVIADO[3] = 'Mar';
$MESABREVIADO[4] = 'Abr';
$MESABREVIADO[5] = 'May';
$MESABREVIADO[6] = 'Jun';
$MESABREVIADO[7] = 'Jul';
$MESABREVIADO[8] = 'Ago';
$MESABREVIADO[9] = 'Sep';
$MESABREVIADO[10] = 'Oct';
$MESABREVIADO[11] = 'Nov';
$MESABREVIADO[12] = 'Dic';

$SEMANACOMPLETA[0] = 'Domingo';
$SEMANACOMPLETA[1] = 'Lunes';
$SEMANACOMPLETA[2] = 'Martes';
$SEMANACOMPLETA[3] = 'MiŽrcoles';
$SEMANACOMPLETA[4] = 'Jueves';
$SEMANACOMPLETA[5] = 'Viernes';
$SEMANACOMPLETA[6] = 'S‡bado';

$SEMANAABREVIADA[0] = 'D';
$SEMANAABREVIADA[1] = 'L';
$SEMANAABREVIADA[2] = 'M';
$SEMANAABREVIADA[3] = 'Mi';
$SEMANAABREVIADA[4] = 'J';
$SEMANAABREVIADA[5] = 'V';
$SEMANAABREVIADA[6] = 'S';

////////////////////////////////////
if($tipo_semana == 0){
$ARRDIASSEMANA = $SEMANACOMPLETA;
}elseif($tipo_semana == 1){
$ARRDIASSEMANA = $SEMANAABREVIADA;
}
if($tipo_mes == 0){
$ARRMES = $MESCOMPLETO;
}elseif($tipo_mes == 1){
$ARRMES = $MESABREVIADO;
}

if(!isset($dia)) $dia = date("d",time());
if(!isset($mes)) $mes = date("n",time());
if(!isset($ano)) $ano = date("Y",time());

$TotalDiasMes = date("t",mktime(0,0,0,$mes,$dia,$ano));
$DiaSemanaEmpiezaMes = date("w",mktime(0,0,0,$mes,1,$ano));
$DiaSemanaTerminaMes = date("w",mktime(0,0,0,$mes,$TotalDiasMes,$ano));
$EmpiezaMesCalOffset = $DiaSemanaEmpiezaMes;
$TerminaMesCalOffset = 6 - $DiaSemanaTerminaMes;
$TotalDeCeldas = $TotalDiasMes + $DiaSemanaEmpiezaMes + $TerminaMesCalOffset;


if($mes == 1){
$MesAnterior = 12;
$MesSiguiente = $mes + 1;
$AnoAnterior = $ano - 1;
$AnoSiguiente = $ano;
}elseif($mes == 12){
$MesAnterior = $mes - 1;
$MesSiguiente = 1;
$AnoAnterior = $ano;
$AnoSiguiente = $ano + 1;
}else{
$MesAnterior = $mes - 1;
$MesSiguiente = $mes + 1;
$AnoAnterior = $ano;
$AnoSiguiente = $ano;
$AnoAnteriorAno = $ano - 1;
$AnoSiguienteAno = $ano + 1;
}
$name=" <br/><h1>CALENDARIO</h1>"; 
$name.= "<center><table width=\"100%\" style=\"font-family:arial;font-size:11px\" bordercolor=navy align=center border=0 cellpadding=1 cellspacing=1>";
$name.= " <tr>";
$name.= " <td colspan=10>";
$name.= " <table border=0 align=center width=\"1%\" style=\"font-family:arial;font-size:12px\" cellspacing=5>";
$name.= " <tr>";
//$name.= " <td width=\"1%\"><a href=\"$PHP_SELF?mes=$mes&ano=$AnoAnteriorAno\"><<</a></td>";
//$name.= " <td width=\"1%\"><a href=\"$PHP_SELF?mes=$MesAnterior&ano=$AnoAnterior \"><</a></td>";
$name.= " <td width=\"1%\" colspan=\"1\" align=\"center\" nowrap><b>".$ARRMES[$mes]." - ".$ano."</b></td>";
//$name.= " <td width=\"1%\"><a href=\"$PHP_SELF?mes=$MesSiguiente&ano=$AnoSiguien te\">></a></td>";
//$name.= " <td width=\"1%\"><a href=\"$PHP_SELF?mes=$mes&ano=$AnoSiguienteAno\">>></a></td>";
$name.= " </tr>";
$name.= " </table>";
$name.= " </td>";
$name.= "</tr>";
$name.= "<tr>";
foreach($ARRDIASSEMANA AS $key){
$name.= "<td height=\"25\"><b><center>$key</b></center></td>";
}
$name.= "</tr>";

for($a=1;$a <= $TotalDeCeldas;$a++){
if(!isset($b)) $b = 0;
if($b == 7) $b = 0;
if($b == 0) $name.= '<tr>';
if(!isset($c)) $c = 1;
if($a > $EmpiezaMesCalOffset AND $c <= $TotalDiasMes){
if($c == date("d",time()) && $mes == date("m",time()) && $ano == date("Y",time())){
$name.= "<td bgcolor=\"#444\" height=\"25\" style=\"color:#FFF;\"><center>$c</center></td>";
}elseif($b == 0 OR $b == 6){
$name.= "<td height=\"25\"><center>$c</center></td>";
}else{
$name.= "<td height=\"25\"><center>$c</center></td>";
}
$c++;
}else{
$name.= "<td> </td>";
}
$b++;
}
$name.= "<tr><td align=center colspan=10></a></td></tr>";
$name.= "</table></center>";
 


$_SESSION['left'] .= $name;
?>
