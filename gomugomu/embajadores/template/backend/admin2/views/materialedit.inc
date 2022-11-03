<div class="clear"></div>
<div class="line">
	Acciones > editar acciones [ {name} ]
</div>
<div id="user-form">
{board}
<form name="user" id="user" action="?go=material&do=edit&form=1&id={id}" method="post">

	<div id="user-right">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;"> Aplicar cambios</a>
		</div>	
	</div>

	<div id="user-left">
		<div class="template-label">Nombre de la acción : </div>
		
		<input type="text" name="material" id="material" value="{name}" class="user-form"/><br/>
		
		<div class="template-label">Tipo : </div>
		
		<input type="text" name="type" id="type" value="{type}" class="user-form"/><br/>
		
		<div class="template-label">Gramaje : </div>
		
		<input type="text" name="weight" id="weight" value="{weight}" class="user-form"/><br/>
		
		<div class="template-label">Tamaño : </div>
		
		<input type="text" name="size" id="size" value="{size}" class="user-form"/><br/>
		
		<div class="template-label">Precio : </div>
		
		<input type="text" name="price" id="price" value="{price}" class="user-form"/><br/>

</form>
</div>