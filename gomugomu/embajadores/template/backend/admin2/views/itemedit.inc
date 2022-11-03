<div class="clear"></div>
<div class="line">
	Acciones > editar artículos [ {name} ]
</div>
<div id="user-form">
{board}
<form name="user" id="user" action="?go=item&do=edit&form=1&id={id}" method="post">

	<div id="user-right">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;"> Aplicar cambios</a>
		</div>	
	</div>

	<div id="user-left">
		<div class="template-label">Nombre del artículo : </div>
		<input type="text" name="item" id="item" value="{name}" class="user-form"/><br/>

		<div class="template-label">Descripción : </div>
		<textarea name="description" id="description" class="user-form" rows="7">{description}</textarea><br/>
</form>
</div>