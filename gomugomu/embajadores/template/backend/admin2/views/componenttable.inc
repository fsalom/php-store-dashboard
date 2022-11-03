<br/>
<div class="line">Componentes</div>
<div class="demo">
<div id="dialog-form" title="Crear una nueva acción">
	<div class="validateTips">Toda la información es requerida.</div>
	<form>
	<fieldset>
		<label for="name">Nombre</label>
		<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	<fieldset>
		
		<input type="hidden" name="id" id="id" value="{id}"/>
		<input type="hidden" name="id_item" id="id_item" value="{id_item}"/>
	</fieldset>
	
	</form>
</div>



<button id="create-user">[+] Crear nuevo componente</button>
<!--<a href="#" id="create-user" class="button white" style="padding:10px;">[+] create new zone</a>-->
<br/><br/>
<table id="myTable" width="100%" class="tablesorter" cellpadding="0" cellspacing="0" class="ui-widget ui-widget-content">
<thead>
<tr>
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

