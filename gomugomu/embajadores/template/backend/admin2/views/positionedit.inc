<div class="clear"></div>
<div class="line">
	Zones > edit zone [ {name} ]
</div>
<div id="user-form">
{board}
<form name="user" id="user" action="?go=position&do=edit&form=1&id={id}" method="post">

	<div id="user-right">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('user').submit( ); return false" class="button white" style="padding:10px 50px;"> Apply changes</a>
		</div>	
	</div>

	<div id="user-left">
		<div class="template-label">Position name : </div>
		
		<input type="text" name="position" id="position" value="{name}" class="user-form"/><br/>

</form>
</div>