
<div class="clear"></div>
<div class="line">
	User > edit user [ {username} ]
</div>
<div id="user-form">
{board}
<form name="user" id="user" action="?go=user&do=edit&form=1&id={id}" method="post">

	<div id="user-right">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;"> Create new user</a>
		</div>	
	</div>

	<div id="user-left">
		<div class="template-label">Username : <span id="user-validateUsername">{validation}</span> </div>
		
		<input type="text" name="username" id="username" value="{username}" class="user-form"/><br/>
		<input type="hidden" name="usercheck" id="usercheck" value="{usercheck}">

	<br/>
	<div class="template-label">Email : <span id="user-validateEmail">{validation}</span> </div> 
		
		<input type="text" name="email" id="email" class="user-form" value="{email}"/>
		<input type="hidden" name="emailcheck" id="emailcheck" value="{emailcheck}">
	<br/>
<br/>
	<div class="template-label">Privileges :</div><br/>
		<select name="level" class="user-form-input">
			<option value="10" {se_admin}>Super Admin</option>
			<option value="5" {se_user}>Editor</option>
			<option value="1" {se_art}>Artist</option>
		</select>
	</div>

</form>
</div>