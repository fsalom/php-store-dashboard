	<div class="profile-info">
		<a href="?logout=on" class="button small black" style="color:#FFF; float:right;">Salir</a>
		
		<!--<a href="?logout=on" class="button small black" style="color:#FFF; float:right;margin-right:2px;">No tienes mensajes</a>--> 
		 
		 
		<h1>{nombre}</h1>
		Brand Ambassador<br/><br/><br/>
		
		
		{message}
		
		<strong>Progresión del presupuesto</strong>
		
		
		
		<br/><br/>
		<div id="profile-budget">
			<div id="profile-budget-img" style="background-image: url('template/frontend/app/img/{id}.png');">
				
			</div>
			<div style="background-color:#e5e5e5; width:390px; text-align:center; border:1px solid #999; float:left;  border-radius:0px 5px 5px 0px;">
				<div style="background-color:#FF9900; padding: 43px 0px 43px 0px; width: {budget_used_percentage}%; float:left; border-radius:0px 5px 5px 0px;">{budget_used}</div>
				<div style="background-color:#efebe2; padding: 43px 0px 43px 0px; width: {budget_ready_percentage}%; float:left; border-radius:0px 5px 5px 0px;">{budget_ready}</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			
		</div>
		<p>
		<strong>Selección de prendas</strong>
		</p>
			<div id="profile-items">
				{items}
				<div class="clear"></div>
			</div>
		<p>
		<strong>Tarjetas del 20%</strong>
		</p>
			<div id="profile-tarjetas">
				<h1>{tarjetas}</h1>
				<img src="template/frontend/app/img/20.jpg">
				<img src="template/frontend/app/img/20b.jpg">
			</div>
		
				{propuestas}
			
	</div>
	<div class="clear"></div>	
