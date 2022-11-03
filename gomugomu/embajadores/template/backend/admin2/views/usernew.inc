<div class="clear"></div>
<div class="line">
	Usuario > añadir nuevo usuario
</div>
<div id="user-form">
{board}

<div id="myform">
<form name="user" id="user" action="?go=user&do=new&form=1" method="post">

	<div id="user-right">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;"> Crear usuario</a>
		</div>	
	</div>

	<div id="user-left">
		<div class="template-label">Nombre de usuario : <span id="user-validateUsername">{validation}</span> </div>
		
		<input type="text" name="username" id="username" value="{username}" class="user-form"/><br/>
		<input type="hidden" name="usercheck" id="usercheck" value="{usercheck}">

		<div class="template-label">Password :</div> 
			
		<!--<div style="text-align:right; margin-right:3px;"><a href="#" onclick="azar()" class="button white" style="padding:10px;"> Generate password</a></div>-->
		<input type="text" name="password" id="password" class="user-form" />
		<input type="text" name="password2" id="password2" class="user-form"/>

	<br/>
	<div class="template-label">Email : <span id="user-validateEmail">{validation}</span> </div> 
		
		<input type="text" name="email" id="email" class="user-form"/>
		<input type="hidden" name="emailcheck" id="emailcheck" value="{emailcheck}">
	<br/>

	<div class="template-label">Privilegios :</div>
		<select name="level" class="user-form-input">
			<option value="10">Super Admin</option>
			<option value="5">Admin</option>
			<option value="1">Editor</option>
		</select>
	</div>

</form>
</div>
</div>