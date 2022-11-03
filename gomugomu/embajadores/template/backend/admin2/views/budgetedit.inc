<div class="clear"></div>
<div class="line">
	Books > edit book [ {username} ]
</div>
<div id="user-form">
{board}
<form name="user" id="user" action="?go=book&do=edit&form=1&id={id}" method="post">

<div id="myform">
<div id="user-col1">
	<div class="user-item1">
<div class="template-label">Company name :  </div>
		
		<input type="text" name="username" id="username" value="{username}" class="user-form"/><br/>

</div>

<div class="user-item2">

	<div class="template-label">Zone :</div>
	{zone}
</div>
<div class="clear"></div>
</div>
<div id="user-col2">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;">Apply</a>
		</div>	
	</div>
	<div class="clear"></div>
</div>
</form>

<div class="clear"></div>