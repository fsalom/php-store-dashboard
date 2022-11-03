<div class="clear"></div>
<div class="line">
	Web > Add edit book
</div>
<div id="user-form">
{board}
<form name="user" id="user" action="?go=web&do=edit&form=1&id={id}" method="post">

<div id="myform">
<div id="user-col1">
<div class="user-item1">
<div class="template-label">Name : (example November 2011)<span id="user-validateUsername">{validation}</span> </div>
		
		<input type="text" name="username" id="username" value="{username}" class="user-form"/>
		<input type="hidden" name="webcheck" id="usercheck" value="{usercheck}">
<br/>
</div>
<div class="user-item1">
<div class="template-label"> URL - Newport Beach - Costa Mesa : </div>
		
		<input type="text" name="newport"  value="{newport}" class="user-form"/>
</div>
<div class="user-item1">
<div class="template-label"> URL - Anaheim Hills - Yorba Linda : </div>
		
		<input type="text" name="anaheim" value="{anaheim}" class="user-form"/>
</div>
<div class="user-item1">
<div class="template-label"> URL - Tustin - Orange : </div>
		
		<input type="text" name="tustin" value="{tustin}" class="user-form"/>
</div>


<div class="clear"></div>
</div>
<div id="user-col2">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;">Create</a>
		</div>	
	</div>
	<div class="clear"></div>
</div>
</form>

<div class="clear"></div>