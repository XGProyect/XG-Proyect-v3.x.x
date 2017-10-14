<h2>{mk_moon_title}</h2>
<form name="frm_addmoon" action="" method="POST">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<td>{mk_moon_planet}</td>
		<td>
			<select name="planet">
				<option value="0">-</option>
				{planets_combo}
			</select>
		</td>
	</tr>
	<tr>
		<td>{mk_moon_name}</td>
		<td>
			<input type="text" value="{mk_moon_default_name}" name="name">
		</td>
	</tr>
	<tr>
		<td>{mk_moon_diameter}</td>
		<td>
			<input type="text" name="planet_diameter" maxlength="5">
			<input type="checkbox" checked="checked" name="diameter_check"> <span class="small_font">{mk_moon_random}</span>
		</td>
	</tr>
	<tr>
		<td>{mk_moon_temperature}</td>
		<td>
			<input type="text" name="planet_temp_min" maxlength="3" class="input-mini"> <span style="font-size:30px">/</span>
			<input type="text" name="planet_temp_max" maxlength="3" class="input-mini">
			<input type="checkbox" checked="checked" name="temp_check"> <span class="small_font">{mk_moon_random}</span>
		</td>
	</tr>
	<tr>
		<td>{mk_moon_available_fields}</td>
		<td>
			<input type="text" name="planet_field_max" maxlength="5" value="1" class="input-xsmall">
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div align="center">
				<input type="submit" value="{mk_moon_add_moon}" class="btn btn-primary" name="add_moon">
			</div>
		</td>
	</tr>
</table>
</form>