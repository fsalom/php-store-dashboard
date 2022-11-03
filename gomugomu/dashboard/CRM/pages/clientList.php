<table id="TClientes" class="tablaListado"> 
		<thead> 
		<tr> 
		    <th>Nombre</th> 
		    <th>Apellidos</th> 
		    <th>Email</th> 
		    <th>Telefono</th> 
		    <th>Opciones</th> 		    
		</tr> 
		</thead> 
		<tfoot>
		<tr>
			<th>Nombre</th> 
		    <th>Apellidos</th> 
		    <th>Email</th> 
		    <th>Telefono</th> 
		    <th>Opciones</th> 
		</tr>
	</tfoot>
		
		<tbody> 
			<?php echo $CLIENT->getAll()?>
		</tbody> 
		</table> 	