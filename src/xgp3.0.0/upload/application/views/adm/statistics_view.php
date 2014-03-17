

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{cs_title}</h1>
			<br />
			<form method="post" action="">

				<label>
					<strong>{cs_point_per_resources_used} ({cs_resources})</strong>
				</label>
				<input type="text" name="stat_settings" id="stat_settings" value="{stat_settings}" />

				<label>
					<strong>{cs_time_between_updates} ({cs_minutes})</strong>
				</label>
				<input type="text" name="stat_update_time" id="stat_update_time" value="{stat_update_time}" />

				<label>
					<strong>{cs_points_to_zero}</strong>
				</label>
				<select name="stat" id="stat">
			          <option value="1" {sel_sta1}>{cs_yes}</option>
			          <option value="0" {sel_sta0}>{cs_no}</option>
			  	</select>

			  	<label>
			  		<strong>{cs_access_lvl}</strong>
				</label>
				<input type="text" name="stat_level" id="stat_level" value="{stat_level}" />

		      	<div align="center">
			      	<input type="submit" name="save" value="{cs_save_changes}" class="btn btn-primary">
		      	</div>
			</form>
          </div>
        </div><!--/span-->