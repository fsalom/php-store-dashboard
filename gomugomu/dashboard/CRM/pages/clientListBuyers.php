<table id="TClientesBuyers" class="tablaListado"> 
		<thead> 
		<tr> 
		    <th>Nombre</th> 
		    <th>Apellidos</th> 
		    <th>Email</th> 
		    <th>Telefono</th> 
		    <th>Cantidad gastada</th> 		    
		</tr> 
		</thead> 
		<tfoot>
		<tr>
			<th>Nombre</th> 
		    <th>Apellidos</th> 
		    <th>Email</th> 
		    <th>Telefono</th> 
		    <th>Cantidad gastada</th> 
		</tr>
	</tfoot>
		
		<tbody> 
			<?php echo $CLIENT->getAllBuyers();?>
		</tbody> 
		</table> 	