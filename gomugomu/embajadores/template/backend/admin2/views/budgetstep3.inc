<div class="clear"></div>
<div class="line">
	Book > Add new ads
</div>
<div id="user-form">
{board}

<div id="myform">
<div id="user-col1">
	<form id="uploadform" action="?go=book&do=review&id={id}&id_book={id_book}&id_ad={id_ad}&form=1" enctype="multipart/form-data" method="post">
		<div id="user-content">
		<div id="user-col1">
			<div class="user-item1">
				

<table id="myTable" width="100%" class="tablesorter" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th>Articulo</th>
    <th>Cantidad</th>
    <th>Precio</th>
    <th>Opciones</th>
</tr>
</thead>
<tbody>
<tr>     
    <td>
    <span class="template-links">
    <a href="{url3}"><strong>{data1}</strong></a></span></td>  
    <td>{data2}</td> 
    <td>{data3}</td> 
    <td>	
    	<a href="{url1}"  class="button white">Access book</a></a>
    	{opt}

    </td> 
</tr>
<tr>     
    <td>
    <span class="template-links">
    <a href="{url3}"><strong>{data1}</strong></a></span></td>  
    <td>{data2}</td> 
    <td>{data3}</td> 
    <td>	
    	<a href="{url1}"  class="button white">Access book</a></a>
    	{opt}

    </td> 
</tr>
</tbody>
</table>




</div>

<div class="user-item2">
<br/>
	<div class="template-label">Buscar artículo :</div>
		<input type="text" class="search" id="searchbox" />
		<div id="display">
		</div>
</div>
<div class="clear"></div>
</div>
<div id="user-col2">
		
	</div>
	<div class="clear"></div>
	</div>
</div>
</form>


<div class="clear"></div>
</div>
<div id="user-col2">
		<div style="text-align:right;">
			<a href="#" onclick="document.getElementById('uploadform').submit( ); return false" class="button white" style="padding:10px 50px;">Upload</a>
		</div>	
	</div>
	<div class="clear"></div>
</div>
</form>

<div class="clear"></div>