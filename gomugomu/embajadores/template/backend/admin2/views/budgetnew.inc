<div class="clear"></div>
<div class="line">
	Presupuesto > Crear nuevo presupuesto
</div>
<div id="user-form">
	{board}

<form name="user" id="user" action="?go=budget&do=step2&form=1" method="post">

	<div id="myform">
		
		<div id="user-content">
		<h3>1/3 - SELECCIONAR CLIENTE</h3>
		<div id="user-col1">
			<div class="user-item1">
			<br/>Datos del cliente:<br/><br/>

<div class="profile">
Número de cliente: <div class="profile_id"><input type="text" name="id" id="id" value="{id}" class="user-form"/><br/></div><br/>
Nombre:	<div class="profile_name"><input type="text" name="name" id="name" value="{name}" class="user-form"/></div><br/>
Empresa: <div class="profile_company"><input type="text" name="company" id="company" value="{company}" class="user-form"/></div><br/>
Email: <div class="profile_email"><input type="text" name="email" id="email" value="{email}" class="user-form"/></div><br/>
</div>  

			</div>

			<div class="user-item2">
			<br/>
			<div class="template-label">Buscar cliente:</div>
			<input type="text" class="searchclient" id="searchbox" />
			<div id="displayclient">
			</div>
			</div>
		<div class="clear"></div>
		</div>
		
	<div id="user-col2"><br/><br/>
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 40px;">Siguente</a>
		</div>	
	</div>
	<div class="clear"></div>
	</div>		

</form>
<br/>
<div class="clear"></div>
