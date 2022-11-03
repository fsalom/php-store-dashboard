<div class="clear"></div>
<div class="line">
	Clients > Edit client: [ {username} ]
</div>
<div id="user-form">
{board}
<form name="user" id="user" action="?go=client&do=edit&form=1&id={id}" method="post">

	<div id="user-right">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;"> Apply changes</a>
		</div>	
	</div>

	<div id="user-left">
		<div class="template-label">Company name : <span id="user-validateUsername">{validation}</span> </div>
		
		<input type="text" name="username" id="username" value="{username}" class="user-form"/><br/>
		<input type="hidden" name="usercheck" id="usercheck" value="{usercheck}">

		<div class="template-label">Name :</div> 
			
		<input type="text" name="name" id="name" class="user-form" value="{name}" />
		<div class="template-label">Surname :</div> 
			
		<input type="text" name="surname" id="surname" class="user-form"  value="{surname}"/>
		<div class="template-label">Telephone :</div> 
			
		<input type="text" name="telephone" id="telephone" class="user-form" value="{telephone}" />

	<br/>
	<div class="template-label">Email : <span id="user-validateEmail">{validation}</span> </div> 
		
		<input type="text" name="email" id="email" class="user-form"  value="{email}"/>
		<input type="hidden" name="emailcheck" id="emailcheck" value="{emailcheck}">
	<br/>

</form>
</div>
<div class="clear"></div>