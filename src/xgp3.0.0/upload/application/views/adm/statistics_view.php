

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{cs_title}</h1>
			<br />
			<form method="post" action="">

				<label>
					<strong>{cs_point_per_resources_used} ({cs_resources})</strong>
				</label>
				<input type="text" name="stat_points" id="stat_points" value="{stat_points}" />

				<label>
					<strong>{cs_time_between_updates} ({cs_minutes})</strong>
				</label>
				<input type="text" name="stat_update_time" id="stat_update_time" value="{stat_update_time}" />

			  	<label>
			  		<strong>{cs_access_lvl}</strong>
				</label>
				<select name="stat_admin_level" id="stat_admin_level">
                                  {admin_levels}
			  	</select>
		      	<div align="center">
			      	<input type="submit" name="save" value="{cs_save_changes}" class="btn btn-primary">
		      	</div>
			</form>
          </div>
        </div><!--/span-->