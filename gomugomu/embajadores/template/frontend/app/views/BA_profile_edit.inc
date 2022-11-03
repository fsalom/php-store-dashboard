	<div class="profile-info">
		<a href="?" class="button small black" style="color:#FFF; float:right;">Volver</a>
		<a href="#" class="button small" id="send" style="color:#000; float:right;margin-right:2px;">Nuevo evento</a> 
		 
		 
		<h1>{nombre}</h1>
		Brand Ambassador<br/><br/><br/>
		
		<center>{errorPrize}</center>
		
		<div id="profile-prize">
			<form method="post" action="?go=profile&submit=prize&id={id}" id="premiar">
				<div id="profile-prize-select">
					<select name="type" id="seleccion">
						<option value="1">Propuesta</option>
						<option value="2">Premiar una acción</option>
						<option value="3">Tarjetas 20%</option>
					</select>
				</div>
				
				<div id="tarjetas">
					<b>Total (precio sin descuento):</b><br/>
					<input type="text" class="profile-prize-input" name="TvalueReal" value="">
					<b>Total pagado:</b><br/>
					<input type="text" class="profile-prize-input" name="TvalueDiscount" value="">
				</div>
				
				
				<div id="premio">
					<b>Premio:</b>
					<select name="premio">
						<option value="20">20€</option>
						<option value="30">30€</option>
						<option value="50">50€</option>
						<option value="80">80€</option>
						<option value="100">100€</option>
						<option value="120">120€</option>
						<option value="150">150€</option>
						<option value="200">200€</option>
					</select><br/>
					<b>Razón:</b>
						<textarea name="reason"  class="profile-prize-input" rows="6"></textarea>
						<br/><br/>
				</div>
				
				<div id="propuesta">
					<b>Propuesta:</b>:
					<textarea name="message"  class="profile-prize-input" rows="6"></textarea>
					<br/><br/>
				</div>
				
				<a href="#" id="sendPremiar" class="button black">Aceptar</a>
				
			</form>
		</div>
		<br/>
		<strong>Progresión del presupuesto</strong>
		
		<br/><br/>
		<div id="profile-budget">
			
			<div id="profile-budget-img" style="background-image: url('template/frontend/app/img/{id}.png');">
				
			</div>
			
			<div style="background-color:#e5e5e5; width:390px; text-align:center; border:1px solid #999; float:left; border-radius:0px 5px 5px 0px;">
				<div style="background-color:#FF9900; padding: 43px 0px 43px 0px; width: {budget_used_percentage}%; float:left; border-radius:0px 5px 5px 0px;">{budget_used}</div>
				<div style="background-color:#efebe2; padding: 43px 0px 43px 0px; width: {budget_ready_percentage}%; float:left; ">{budget_ready}</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			
		</div>
		<p>
		{error}
		<br/>
		<a href="#" id="item" class="button small black" style="color:#FFF; float:right;">Añadir artículo</a>
		<br/><br/>
		<div class="clear"></div>
		<div class="form">	
			<form method="post" action="?go=profile&submit=on&id={id}" id="formsubmit">
				Nombre
				<input type="text" name="name" value="{name}">
				Referencia
				<input type="text" name="reference" value="{reference}">
				Precio
				<input type="text" name="value" value="{value}">
				Talla
				<input type="text" name="size" value="{size}">
				<br/><br/>
				<a href="#" id="submit" class="button black">Aceptar</a>
			</form>
		</div>
		<br/><br/>
		<strong>Selección de prendas</strong>
		</p>
			<div id="profile-items">
				{items}
				<div class="clear"></div>
			</div>
		<p>
		<strong>Tarjetas de descuento</strong>
		</p>
			<div id="profile-items">
				{card}
				<div class="clear"></div>
			</div>
		<p>
		<strong>Premios</strong>
		</p>
			<div id="profile-items">
				{premios}
				<div class="clear"></div>
			</div>
		<p>
		<strong>Propuestas</strong>
		</p>
			<div id="profile-items">
				{propuestas}
				<div class="clear"></div>
			</div>
	</div>
	<div class="clear"></div>	
