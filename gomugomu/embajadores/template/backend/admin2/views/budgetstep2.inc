<div class="clear"></div>
<div class="line">
	Presupuestos > Añadir artículos
</div>
<div id="user-form">
{board}


		

<div id="myform">
<form method="post" action="?go=budget&do=step2&option=add" name="budget" id="budget">
<input type="hidden" name="id_client" value="{id}">
<h3>2/3 - SELECCIONAR ARTÍCULOS</h3><br/>

	<div class="template-label">Datos del cliente :</div>
<br/>
<div class="profile">
	<div class="profile_col3">
	Número de cliente: <b>{p_id}</b><br/><br/>
	Nombre:	<b>{p_name}</b><br/><br/>
	Empresa:<b> {p_company}</b><br/><br/>
	Cargo: <b>{p_position}</b><br/><br/>
	</div>
	
	<div class="profile_line"></div>
	
	<div class="profile_col3">
	Teléfono: <b>{p_telephone}</b><br/><br/>
	Móvil:	<b>{p_mobile}</b><br/><br/>
	Email:<b> {p_email}</b><br/><br/>
	<br/>
	</div>
	
	<div class="profile_line"></div>
	
	<div class="profile_col3">
	Domicilio: <b>{p_street}</b><br/><br/>
	Población:	<b>{p_city}</b><br/><br/>
	Código postal:<b> {p_zip}</b><br/><br/>
	Provincia: <b>{p_province}</b><br/>
	
	</div>
	<div class="clear"></div>
</div> 
<br/>
<br/>
	<div class="template-label">Buscar artículo :</div>
		<input type="text" class="search" id="searchbox" />
		<div id="display">
		</div>
<br/><br/>				
<table  width="100%"  cellpadding="5" cellspacing="0">
<thead>
<tr>
    <th>Articulo</th>
    <th></th>
    <th>Cantidad</th>
    <th>Precio</th>
    <th>Total</th>
    <th>Opciones</th>
</tr>
</thead>
<tbody>
{rows}
</tbody>
</table>

<br/>
<br/>
<br/>
</form>



<div style="text-align:right;"><a href="#" onclick="document.getElementById('budget').submit( ); return false" class="button white" style="padding:10px 50px;">Siguiente</a></div>	
