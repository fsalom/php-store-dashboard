<br/>
<div class="line">Acciones</div>
<div class="demo">
<div id="dialog-form" title="Crear una nueva acción">
	<div class="validateTips">Toda la información es requerida.</div>
	<form>
	<fieldset>
		<label for="name">Nombre</label>
		<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	<fieldset>
		<label for="price">Precio</label>
		<input type="text" name="price" id="price" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	<fieldset>
		<label for="type">Tipo</label>
		<select name="type" id="type">
			<option value="1">Precio por hora</option>
			<option value="2">Precio por unidad</option>
		</select>
	</fieldset>
	<fieldset>
		<input type="hidden" name="id" id="id" value="{id}"/>
	</fieldset>
	
	</form>
</div>



<button id="create-user">[+] Crear nueva acción</button>
<!--<a href="#" id="create-user" class="button white" style="padding:10px;">[+] create new zone</a>-->
<br/><br/>
<form method="post" action="?go=actions&do=add&id_item={id_item}" name="actions" id="actions">
{actions}
<table id="myTable" width="100%" class="tablesorter" cellpadding="0" cellspacing="0" class="ui-widget ui-widget-content">
<thead>
<tr>
	<th></th> 
    <th>Nombre</th>
    <th>Tipo</th>
    <th>Precio</th>
    <th>Opciones</th>
</tr>
</thead>
<tbody>
{rows}
</tbody>
</table>
</form>
