<br/>
<form method="post" action="?go=changes&id={id}&id_ad={id_ad}" id="changes">
<div id="user-form">
{board}

<div id="myform">
<div id="user-col1">
	
  	<div class="info">
  		Review <b>#{review}/{num}</b> - Created: <b>{date}</b>
  	</div>
  	{image}
  	<div class="info">
  		{comment}
  	</div>



<div class="clear"></div>
</div>
<div id="user-col2">
		<div {css}>
			<a href="?go=approve&id={id}&id_ad={id_ad}" class="button green">Approve</a>
			<a onclick="document.getElementById('changes').submit( ); return false" class="button red"> Changes</a>
		</div>
		
	</div>
	<div class="clear"></div>
</div>
</form>

<div class="clear"></div>
</form>
