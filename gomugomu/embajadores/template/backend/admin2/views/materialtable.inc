<div class="demo">
<div id="dialog-form" title="Crear una nuevo material">
	<div class="validateTips">(*) Campos requeridos.</div>
	<form>
	<fieldset>
		<label for="name">Nombre (ej: papel) *</label>
		<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
		<label for="type">Tipo (ej: couche mate)</label>
		<input type="text" name="type" id="type" class="text ui-widget-content ui-corner-all" />
		<label for="weight">Gramaje (ej:150g)</label>
		<input type="text" name="weight" id="weight" class="text ui-widget-content ui-corner-all" />
		<label for="size">Tamaño (ej: 90x70)</label>
		<input type="text" name="size" id="size" class="text ui-widget-content ui-corner-all" />
		<label for="price">Precio (€) *</label>
		<input type="text" name="price" id="price" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	<fieldset>
		<input type="hidden" name="id" id="id" value="{id}"/>
		<input type="hidden" name="item" id="item" value="{item}"/>
	</fieldset>
	</form>
</div>



<button id="create-user">[+] Crear nuevo material</button>
<!--<a href="#" id="create-user" class="button white" style="padding:10px;">[+] create new zone</a>-->
<br/><br/>
<form method="post" action="?go=material&do=add&id={id}" name="material" id="material">
{component}
<table id="myTable" width="100%" class="tablesorter" cellpadding="0" cellspacing="0" class="ui-widget ui-widget-content">
<thead>
<tr>
    <th></th>
    <th>Nombre</th>
    <th>Tipo</th>
    <th>Gramaje</th>
    <th>Tamaño</th>
    <th>Precio</th>
    <th>Opciones</th>
</tr>
</thead>
<tbody>
{rows}
</tbody>
</table>
</form>
