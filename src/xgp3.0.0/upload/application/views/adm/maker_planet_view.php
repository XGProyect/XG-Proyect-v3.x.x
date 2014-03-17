<h2>{mk_planet_title}</h2>
<form name="frm_addplanet" action="" method="POST">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<td>{mk_planet_user}</td>
		<td>
			<select name="user">
				<option value="0">-</option>
				{users_combo}
			</select>
		</td>
	</tr>
	<tr>
		<td>{mk_planet_name}</td>
		<td><input name="name" type="text" maxlength="25" value="{mk_planet_default_name}"></td>
	</tr>
	<tr>
		<td>{mk_planet_available_fields}</td>
		<td><input name="planet_field_max" type="text" maxlength="3" value="163"></td>
	</tr>
	<tr>
		<td>{mk_planet_coords}</td>
		<td>
			<input name="galaxy" type="text" maxlength="1" class="input-mini"> <span style="font-size:30px">:</span>
			<input name="system" type="text" maxlength="3" class="input-mini"> <span style="font-size:30px">:</span>
			<input name="planet" type="text" maxlength="2" class="input-mini">
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div align="center">
				<input type="submit" value="{mk_planet_add_planet}" class="btn btn-primary" name="add_planet">
			</div>
		</td>
	</tr>
</table>
</form>