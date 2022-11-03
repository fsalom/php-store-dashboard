<div class="clear"></div>
<div class="line">
	Acciones > editar acciones [ {name} ]
</div>
<div id="user-form">
{board}
<form name="user" id="user" action="?go=actions&do=edit&form=1&id={id}" method="post">

	<div id="user-right">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;"> Aplicar cambios</a>
		</div>	
	</div>

	<div id="user-left">
		<div class="template-label">Nombre de la acción : </div>
		<input type="text" name="actions" id="actions" value="{name}" class="user-form"/><br/>
		
		<div class="template-label">Precio : </div>
		<input type="text" name="price" id="price" value="{price}" class="user-form"/><br/>

		<div class="template-label">Tipo : </div>
		{type}
		<br/>

</form>
</div>