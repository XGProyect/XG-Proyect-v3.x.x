<h2>{planets}</h2>
{alert_info}
<form name="save_info" method="post" action="">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<th width="50%">{us_user_main_field}</th>
		<th width="50%">{us_user_main_value}</th>
	</tr>
	<tr>
		<td>{us_user_main_name}</td>
		<td><input type="text" name="planet_name" value="{planet_name}"></td>
	</tr>
	<tr>
		<td>{us_user_main_id_owner}</td>
		<td>
			<select name="planet_user_id">
				{planet_user_id}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_coords}</td>
		<td><input type="text" name="galaxy" value="{galaxy}" class="input-mini">:<input type="text" name="system" value="{system}" class="input-mini">:<input type="text" name="planet" value="{planet}" class="input-mini"></td>
	</tr>
	<tr>
		<td>{us_user_main_last_update}</td>
		<td>{planet_last_update}</td>
	</tr>
	<tr>
		<td>{us_user_main_planet_type}</td>
		<td>
			<select name="planet_type">
				<option value="1" {type1}>{us_user_main_planet}</option>
				<option value="3" {type2}>{us_user_main_moon}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_destruyed}</td>
		<td>
			<select name="planet_destroyed">
				<option value="1"{dest1}>{us_user_main_planet_destroyed_yes}</option>
				<option value="2"{dest2}>{us_user_main_planet_destroyed_no}</option>
			</select>
			{planet_destroyed}
		</td>
	</tr>
	<tr>
		<td>{us_user_main_b_building}</td>
		<td>{planet_b_building}</td>
	</tr>
	<tr>
		<td>{us_user_main_b_building_id}</td>
		<td>
			<select name="planet_b_building_id">
				{planet_b_building_id}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_b_tech}</td>
		<td>{planet_b_tech}</td>
	</tr>
	<tr>
		<td>{us_user_main_b_tech_id}</td>
		<td>
			<select name="planet_b_tech_id">
				{planet_b_tech_id}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_b_hangar}</td>
		<td>{planet_b_hangar}</td>
	</tr>
	<tr>
		<td>{us_user_main_b_hangar_id}</td>
		<td>
			<select name="planet_b_hangar_id">
				{planet_b_hangar_id}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_image}</td>
		<td>
			<select name="planet_image">
				{planet_image}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_diameter}</td>
		<td><input type="text" name="planet_diameter" value="{planet_diameter}"></td>
	</tr>
	<tr>
		<td>{us_user_main_field_current}</td>
		<td><input type="text" name="planet_field_current" value="{planet_field_current}"></td>
	</tr>
	<tr>
		<td>{us_user_main_field_max}</td>
		<td><input type="text" name="planet_field_max" value="{planet_field_max}"></td>
	</tr>
	<tr>
		<td>{us_user_main_temp_min}</td>
		<td><input type="text" name="planet_temp_min" value="{planet_temp_min}"></td>
	</tr>
	<tr>
		<td>{us_user_main_temp_max}</td>
		<td><input type="text" name="planet_temp_max" value="{planet_temp_max}"></td>
	</tr>
	<tr>
		<td>{us_user_main_metal}</td>
		<td><input type="text" name="planet_metal" value="{planet_metal}"></td>
	</tr>
	<tr>
		<td>{us_user_main_metal_perhour}</td>
		<td><input type="text" name="planet_metal_perhour" value="{planet_metal_perhour}"></td>
	</tr>
	<tr>
		<td>{us_user_main_metal_max}</td>
		<td><input type="text" name="planet_metal_max" value="{planet_metal_max}"></td>
	</tr>
	<tr>
		<td>{us_user_main_crystal}</td>
		<td><input type="text" name="planet_crystal" value="{planet_crystal}"></td>
	</tr>
	<tr>
		<td>{us_user_main_crystal_perhour}</td>
		<td><input type="text" name="planet_crystal_perhour" value="{planet_crystal_perhour}"></td>
	</tr>
	<tr>
		<td>{us_user_main_crystal_max}</td>
		<td><input type="text" name="planet_crystal_max" value="{planet_crystal_max}"></td>
	</tr>
	<tr>
		<td>{us_user_main_deuterium}</td>
		<td><input type="text" name="planet_deuterium" value="{planet_deuterium}"></td>
	</tr>
	<tr>
		<td>{us_user_main_deuterium_perhour}</td>
		<td><input type="text" name="planet_deuterium_perhour" value="{planet_deuterium_perhour}"></td>
	</tr>
	<tr>
		<td>{us_user_main_deuterium_max}</td>
		<td><input type="text" name="planet_deuterium_max" value="{planet_deuterium_max}"></td>
	</tr>
	<tr>
		<td>{us_user_main_energy_used} / {us_user_main_energy_max}</td>
		<td><input type="text" name="planet_energy_used" value="{planet_energy_used}" class="input-mini"> <span style="font-size:30px">/</span> <input type="text" name="planet_energy_max" value="{planet_energy_max}" class="input-mini"></td>
	</tr>
	<tr>
		<td>{us_user_main_building_metal_mine_porcent}</td>
		<td>
			<select name="planet_building_metal_mine_porcent">
				{planet_building_metal_mine_porcent}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_building_crystal_mine_porcent}</td>
		<td>
			<select name="planet_building_crystal_mine_porcent">
				{planet_building_crystal_mine_porcent}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_building_deuterium_sintetizer_porcent}</td>
		<td>
			<select name="planet_building_deuterium_sintetizer_porcent">
				{planet_building_deuterium_sintetizer_porcent}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_building_solar_plant_porcent}</td>
		<td>
			<select name="planet_building_solar_plant_porcent">
				{planet_building_solar_plant_porcent}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_building_fusion_reactor_porcent}</td>
		<td>
			<select name="planet_building_fusion_reactor_porcent">
				{planet_building_fusion_reactor_porcent}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_ship_solar_satellite_porcent}</td>
		<td>
			<select name="planet_ship_solar_satellite_porcent">
				{planet_ship_solar_satellite_porcent}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_main_planet_debris_metal}</td>
		<td><input type="text" name="planet_debris_metal" value="{planet_debris_metal}"></td>
	</tr>
	<tr>
		<td>{us_user_main_planet_debris_crystal}</td>
		<td><input type="text" name="planet_debris_crystal" value="{planet_debris_crystal}"></td>
	</tr>
	<tr>
		<td>{us_user_main_invisible_start_time}</td>
		<td>
			{planet_invisible_start_time}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div align="center">
				<input type="submit" class="btn btn-primary" name="send_data" value="{us_send_data}">
			</div>
		</td>
	</tr>
</table>
</form>