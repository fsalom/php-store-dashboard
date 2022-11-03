<div class="clear"></div>
<div class="line">
	Clientes > añadir nuevo cliente
</div>
<div id="user-form">
{board}
<form name="user" id="user" action="?go=client&do=new&form=1" method="post">

<div id="myform">
<div id="user-col1">
	
	<div class="client-item1">
	
	<div class="client-group">
<h3>Datos de contacto</h3>
<div class="template-label">Empresa : <span id="user-validateUsername">{validation}</span> </div>
		
		<input type="text" name="username" id="username" value="{username}" class="user-form"/>
		<input type="hidden" name="usercheck" id="usercheck" value="{usercheck}">

		
	<br/>
		<div class="template-label">CIF :</div> 
		<input type="text" name="cif" id="cif" class="user-form" />

		<div class="template-label">Nombre :</div> 
			
		<input type="text" name="name" id="name" class="user-form" />
		<div class="template-label">Apellidos :</div> 
			
		<input type="text" name="surname" id="surname" class="user-form" />
		
		<div class="template-label">Cargo :</div> 
			
		<input type="text" name="position" id="position" class="user-form" />
		
		<div class="template-label">Teléfono :</div> 
			
		<input type="text" name="telephone" id="telephone" class="user-form" />
		
		<div class="template-label">Móvil :</div> 
			
		<input type="text" name="mobile" id="mobile" class="user-form" />
		
<div class="template-label">Email : <span id="user-validateEmail">{validation}</span> </div> 
		
		<input type="text" name="email" id="email" class="user-form"/>
		<input type="hidden" name="emailcheck" id="emailcheck" value="{emailcheck}">
		</div>
		<br/>
		<div class="client-group">
			<h3>Información adicional</h3>
			<div class="template-label"></div> 
			<textarea name="info" class="user-form" rows="10"></textarea>
		</div>
	<br/>
	<br/>
	
</div>
<div class="client-item2">

	<div class="client-group">
<h3>Dirección de envio</h3>

		<div class="template-label">Domicilio :</div> 
			<input type="text" name="street" id="street" class="user-form" />
		<div class="template-label">Población :</div> 
			<input type="text" name="town" id="town" class="user-form" />
		<div class="template-label">Código postal :</div> 
			<input type="text" name="zip" id="zip" class="user-form" />
		<div class="template-label">Provincia :</div> 
			<input type="text" name="province" id="province" class="user-form"/>
		<div class="template-label">País :</div> 
			<input type="text" name="country" id="country" class="user-form"/>
		
		
		</div>
		<br/>
		<div class="client-group">
			<h3>Comercial asignado</h3>
			<div class="template-label"></div> 
			<input type="text" name="comercial" id="comercial" class="user-form" />
		</div>
</div>
<div class="clear"></div>
</div>
<div id="user-col2">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;">Crear</a>
		</div>	
	</div>
	<div class="clear"></div>
</div>
</form>

<div class="clear"></div>