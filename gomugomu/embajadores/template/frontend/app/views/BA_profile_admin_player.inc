		<div id="profile-budget">
			<a href="{url}">
				<div class="profile-budget-img" style="background-image: url('template/frontend/app/img/{id}.png');">
					<!--<div class="profile-budget-img-info">
						Tarjetas<br/>
						<h1>{tarjetas}</h1>
					</div>-->
				</div>
			</a>
			<div style="background-color:#e5e5e5; width:390px; text-align:center; border:1px solid #999; float:left; border-radius:0px 5px 5px 0px;">
				<div style="background-color:#FF9900; padding: 43px 0px 43px 0px; width: {budget_used_percentage}%; float:left; border-radius:0px 5px 5px 0px;">{budget_used}</div>
				<div style="background-color:#efebe2; padding: 43px 0px 43px 0px; width: {budget_ready_percentage}%; float:left; border-radius:0px 5px 5px 0px;">{budget_ready}</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			
		</div>