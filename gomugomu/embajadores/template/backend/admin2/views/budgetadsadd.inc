<br/>
<div id="user-form">

<div id="myform">
<div id="user-col1">
	<form id="uploadform" action="?go=book&do=ads&id={id}&id_book={id_book}&form=1" enctype="multipart/form-data" method="post">
  	<div id="ads_comment">
  		Review <b>#{review}/{num}</b> - Created: <b>{date}</b>
  	</div>
  	{image}
  	<div id="ads_comment">
  		{comment}
  	</div>
</form>


<div class="clear"></div>
</div>
<div id="user-col2">
		<div style="text-align:right;">
			
			<a href="?go=book&do=deleteads&id={id}&id_ad={id_ad}&id_book={id_book}" class="button red" style="width:140px; padding:10px 10px;margin-left:30px;margin-bottom:4px;">Delete</a>
		<br/>
		Last access: {access}<br/>
		</div>
		
	</div>
	<div class="clear"></div>
</div>
</form>

<div class="clear"></div>