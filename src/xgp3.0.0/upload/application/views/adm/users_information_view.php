<h2>{information}</h2>
{alert_info}
<form name="save_info" method="post" action="">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<th width="50%">{us_user_information_field}</th>
		<th width="50%">{us_user_information_value}</th>
	</tr>
	<tr>
		<td>{us_user_information_username}</td>
		<td><input type="text" name="username" value="{user_name}"></td>
	</tr>
	<tr>
		<td>{us_user_information_password}</td>
		<td><input type="text" name="password" value=""></td>
	</tr>
	<tr>
		<td>{us_user_information_email}</td>
		<td><input type="text" name="email" value="{user_email}"></td>
	</tr>
	<tr>
		<td>{us_user_information_pemail}</td>
		<td><input type="text" name="user_email_permanent" value="{user_email_permanent}"></td>
	</tr>
	<tr>
		<td>{us_user_information_level}</td>
		<td>
			<select name="authlevel">
				<option value="0" {sel0}>{ge_user}</option>
				<option value="1" {sel1}>{ge_go}</option>
				<option value="2" {sel2}>{ge_sgo}</option>
				<option value="3" {sel3}>{ge_ga}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_information_pp}</td>
		<td>
			<select name="id_planet">
				{main_planet}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_information_ap}</td>
		<td>
			<select name="current_planet">
				{current_planet}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_information_last_ip}</td>
		<td>{user_lastip}</td>
	</tr>
	<tr>
		<td>{us_user_information_reg_ip}</td>
		<td>{user_ip_at_reg}</td>
	</tr>
	<tr>
		<td>{us_user_information_browser}</td>
		<td>{user_agent}</td>
	</tr>
	<tr>
		<td>{us_user_information_actual_page}</td>
		<td>{user_current_page}</td>
	</tr>
	<tr>
		<td>{us_user_information_date_reg}</td>
		<td>{user_register_time}</td>
	</tr>
	<tr>
		<td>{us_user_information_conection}</td>
		<td>{user_onlinetime}</td>
	</tr>
	<tr>
		<td>{us_user_information_shortcuts}</td>
		<td>
			<select name="user_fleet_shortcuts">
				{user_fleet_shortcuts}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_information_alliance}</td>
		<td>
			<select name="ally_id">
				<option value="0">-</option>
				{alliances}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_information_banned}</td>
		<td>{user_banned}</td>
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