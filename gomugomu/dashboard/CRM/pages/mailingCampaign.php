<table id="TMailing" class="tablaListado"> 
		<thead> 
		<tr> 
		    <th>Nombre de la Campaña</th> 
		    <th>Fecha</th> 
		    <th>Plantilla</th> 
		    <th>Enviados</th> 
		    <th>Opciones</th> 		    
		</tr> 
		</thead> 
		<tfoot>
		<tr>
			<th>Nombre de la Campaña</th> 
		    <th>Fecha</th> 
		    <th>Plantilla</th> 
		    <th>Enviados</th> 
		    <th>Opciones</th> 
		</tr>
	</tfoot>
		
		<tbody> 
			<?php echo $CLIENT->getAll()?>
		</tbody> 
		</table> 	