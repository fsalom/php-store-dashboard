<br/>
<h1>Ticket {id}</h1>
<h3>{date}</h3>
<br/><br/>
<form method="post" action="?go=days&do=foreigner&id_ticket={id}" name="foreigner" id="foreigner">
	<div align="right">Extranjero: <input type="checkbox" name="foreigner" {checked} onchange="this.form.submit();"></div>
</form>
<table id="myTable" width="100%" class="tablesorter" cellpadding="0" cellspacing="0" class="ui-widget ui-widget-content">
<thead>
<tr>
    <th>Descripción</th>
    <th>Código</th>
    <th>Precio Original</th>
    <th>Descuento</th>
    <th>Precio</th>
    <th>IVA</th>
    <th>Coste</th>
    <th>Beneficio</th>
</tr>

</thead>
<tbody>
{rows}
</tbody>
</table>
<div align="right" style="padding-right:80px;"><h3>Total: {total} €</h3></div>
<div align="right" style="padding-right:80px;"><h3>Total Original: {totaloriginal} €</h3></div>
<div align="right" style="padding-right:80px;"><h3>Total IVA: {totaliva} €</h3></div>
<div align="right" style="padding-right:80px;"><h3>Total Coste: {totalcoste} €</h3></div>
<div align="right" style="padding-right:80px;"><h3>Total Beneficio: {totalbeneficio} €</h3></div>

