<h2>{mk_user_title}</h2>
<form name="frm_adduser" action="" method="POST">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<td>{mk_user_name}</th>
		<td><input type="text" name="name"></th>
	</tr>
	<tr>
		<td>{mk_user_pass}</th>
		<td>
			<input type="password" name="password">
			<input type="checkbox" checked="checked" name="password_check"> <span class="small_font">{mk_user_password_random}</span>
		</th>
	</tr>
	<tr>
		<td>{mk_user_email}</th>
		<td><input type="text" name="email"></td>
	</tr>
	<tr>
		<td>{mk_user_level}</td>
		<td>
			<select name="authlevel">
				{level_combo}
			</select>
		</td>
	</tr>
	<tr>
		<td>{mk_user_coords}</td>
		<td>
			<input name="galaxy" type="text" maxlength="1" class="input-mini"> <span style="font-size:30px">:</span>
			<input name="system" type="text" maxlength="3" class="input-mini"> <span style="font-size:30px">:</span>
			<input name="planet" type="text" maxlength="2" class="input-mini">
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div align="center">
				<input type="submit" value="{mk_user_add_user}" class="btn btn-primary" name="add_user">
			</div>
		</td>
	</tr>
</table>
</form>