<div id="profile">
	<div class="table-content grey r5">
	
	<div id="guardarImagen" style="display:none;">
		<form action="?go=profile&id=<?php echo $_GET['id']?>&do=upload" class="formulario_imagen" method="post" enctype="multipart/form-data"> 
			<input type="hidden" name="id" value="<?php echo $_GET['id']?>">
		 	<input type="file" name="myFile">	 	
			<input type="submit" class="btn btn-success" value="Upload">
		</form>
	</div>
	
	<?php 
	if($_GET["do"]=="upload"){
	?>
	<div id="guardarImagen">
		<?php $CLIENT->upload2();?>
	</div>
	<?php 
	}
	?>
	
	<div class="table-row">
		<div class="table-col s25">
			<div class="table-content">
				<div class="table-row">
					<div class="table-col s100">
					
					<?php 
					$name=$_GET['id'].".jpg";
					
					if(!file_exists('files/' . $name)){
						$name="no.png";
					}
							?>
						<div id="perfilImagen">
							<img src="files/<?php echo $name?>" id="imagen">
						</div>		  	
			  		</div>
			  	</div>
			  </div>
		  
		</div>
		<form action="" class="formulario_perfil" method="post" accept-charset="utf-8">
		<div class="table-col s75">
			<div class="mensaje-perfil"></div>
			<?php $data = $CLIENT->getClient($_GET['id']); 	?>
			<input type="hidden" name="id" value="<?php echo $_GET['id']?>">
			<input type="hidden" name="email_original" value="<?php echo $data->email?>">
			<div class="table-content">
				<div class="table-row">
					<div class="table-col s25">Nombre:</div>
					<div class="table-col s25"><input type="text" name="name" value="<?php echo $data->name?>" class="campoEditable"></div>
					<div class="table-col s25">Apellidos:</div>
					<div class="table-col s25"><input type="text" name="surname" value="<?php echo $data->surname; ?>" class="campoEditable"></div>
					<div class="clear"></div>
				</div>
				<div class="table-row">	
					<div class="table-col s25">Datos recogidos en: </div>
					<div class="table-col s75"><b><?php echo $data->from ?></b></div>
					<div class="clear"></div>
				</div>
				<div class="table-row">		
					<div class="table-col s25">Fecha de cumpleaños: </div>
					<div class="table-col s25"><input type="text" name="birthdate" value="<?php echo $data->birthdate ?>" class="campoEditable"></div>
					<div class="table-col s25">Edad:</div>
					<div class="table-col s25"><b><?php echo $data->age ?></b></div>
					<div class="clear"></div>
				</div>
				<div class="table-row">
					<div class="table-col s25">Correo:</div>
					<div class="table-col s75"><input type="text" name="email" value="<?php echo $data->email ?>" class="campoEditable"></div>				
					<div class="clear"></div>
				</div>
				<div class="table-row">
					<div class="table-col s25">Teléfono:</div>
					<div class="table-col s75"><input type="text" name="phone" value="<?php echo $data->phone ?>" class="campoEditable"></div>		
					<div class="clear"></div>
				</div>
				<div class="table-row">
					<div class="table-col s25">Dirección:</div>
					<div class="table-col s75"><input type="text" name="address" value="<?php echo $data->address ?>" class="campoEditable"></div>
					<div class="clear"></div>
				</div>
				<div class="table-row">
					<div class="table-col s25">Ciudad:</div>
					<div class="table-col s25"><input type="text" name="city" value="<?php echo $data->city ?>" class="campoEditable"></div>
					<div class="table-col s25">Provincia: </div>
					<div class="table-col s25"><input type="text" name="province" value="<?php echo $data->province ?>" readonly></div>
					<div class="clear"></div>
				</div>
				<div class="table-row">	
					<div class="table-col s25">Código Postal:</div>
					<div class="table-col s25"><input type="text" name="zipcode" value="<?php echo $data->zipcode ?>" class="campoEditable"></div>		
					<div class="clear"></div>
				</div>
				<div class="table-row">
					<div class="table-col s25">Detalles:</div>
					<div class="table-col s75"><textarea class="campoEditable" rows="4" name="details"><?php echo $data->details ?></textarea></div>		
					<div class="clear"></div>
				</div>
				<div class="table-row">
					<div class="table-col s100">
						<div id="guardar" style="text-align:right; display:none;">
							<input type="submit" name="submit" value="Guardar" class="btn btn-success" />
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		</form>
		<div class="clear"></div>
	</div>	
	
	<div class="table-content lightgrey">
		<div class="table-row">
			<div class="table-col s50">
				<h2>Compras</h2>
				<?php			
				$GOMUGOMU  = new GOMUGOMU();			
				$tickets = $GOMUGOMU->getTicketClient($_GET['id']);
										
				for($i=0;$i<$tickets['count'];$i++) {
					$item=$tickets[$i];									
					$color='#FFD076';
					
				?>
					<div class="item" style="background-color:<?php echo $color ?>">
						[ <b><?php echo $tickets['id_ticket'][$i] ?></b> ] <?php echo $item['date'] ?>						
						<div class="paid">
							<?php echo  $tickets['total'][$i]?>€
						</div>
							
						<?php 
						for($c=0;$c<$item['count'];$c++){
						?>
							<div class="item_detail">				
								<div class="item_price">
											
										<?php echo $item[$c]->price?> €<br/>
									<h3><?php echo $item[$c]->percentage?> %</h3>
									<h4><?php echo $item[$c]->margin->money?> €</h4>
									<h4><?php echo $item[$c]->margin->percentage?>%</h4>
								</div>					
							    <?php echo $item[$c]->info['name']?><br/>
								Color: 		 <b><?php echo $item[$c]->info['colour']?></b><br/>
								Talla: 		 <b><?php echo $item[$c]->info['size']?></b><br/>
								Referencia:  <b><?php echo $item[$c]->info['reference']?></b><br/>
								Temporada: 	 <b><?php echo $item[$c]->info['season']?></b><br/>
									 <i><?php echo $item[$c]->return?></i>
							</div>
						<?php 		
						}
						?>			
							</div>
					<?php 
						}
					?>	
			
		
			</div>
			<div class="table-col s50">
				<h2>Seguimiento</h2>
				Usuario creado : <?php echo $data->created ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>

<!-- Load jQuery and the necessary widget JS files to enable file upload -->  
  <script src="js/upload/jquery.ui.widget.js"></script>
  <script src="js/upload/jquery.iframe-transport.js"></script>
  <script src="js/upload/jquery.fileupload.js"></script>
<script>
    // When the server is ready...
    $(function () {
        'use strict';
        
        // Define the url to send the image data to
        var url = 'ajax_upload.php';
        
        // Call the fileupload widget and set some parameters
        $('#fileupload').fileupload({
            url: url,
            dataType: 'json',            
            done: function (e, data) {
                // Add each uploaded file name to the #files list
                $.each(data.result.files, function (index, file) {
                   $('#files').append('<img src="files/' + file.name + '">');
                });
                $('#progress').css('display','none');
            },
            progressall: function (e, data) {
                // Update the progress bar while files are being uploaded
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .bar').css(
                    'width',
                    progress + '%'
                );
            }
        });
    });
    
  </script>
 