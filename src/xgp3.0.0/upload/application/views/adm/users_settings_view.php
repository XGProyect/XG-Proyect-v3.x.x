<h2>{settings}</h2>
{alert_info}
<form name="save_info" method="post" action="">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<th width="50%">{us_user_settings_field}</th>
		<th width="50%">{us_user_settings_value}</th>
	</tr>
	<tr>
		<td colspan="2">{us_user_settings_general_title}</td>
	</tr>
	<tr>
		<td>{us_user_setting_planet_sort}</td>
		<td>
			<select name="setting_planet_sort">
				{setting_planet_sort}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_setting_planet_order}</td>
		<td>
			<select name="setting_planet_order">
				{setting_planet_order}
			</select>
		</td>
	</tr>
	<tr>
		<td>{us_user_setting_no_ip_check}</td>
		<td><input type="checkbox" name="setting_no_ip_check"{setting_no_ip_check}></td>
	</tr>
	<tr>
		<td colspan="2">{us_user_settings_galaxy_title}</td>
	</tr>
	<tr>
		<td>{us_user_setting_probes_amount}</td>
		<td><input type="text" name="setting_probes_amount" value="{setting_probes_amount}"></td>
	</tr>
	<tr>
		<td>{us_user_setting_fleet_actions}</td>
		<td><input type="text" name="setting_fleet_actions" value="{setting_fleet_actions}"></td>
	</tr>
	<tr>
		<td colspan="2">{us_user_settings_shortcuts_title}</td>
	</tr>
	<tr>
		<td>{us_user_setting_galaxy_espionage}</td>
		<td><input type="checkbox" name="setting_galaxy_espionage"{setting_galaxy_espionage}></td>
	</tr>
	<tr>
		<td>{us_user_setting_galaxy_write}</td>
		<td><input type="checkbox" name="setting_galaxy_write"{setting_galaxy_write}></td>
	</tr>
	<tr>
		<td>{us_user_setting_galaxy_buddy}</td>
		<td><input type="checkbox" name="setting_galaxy_buddy"{setting_galaxy_buddy}></td>
	</tr>
	<tr>
		<td>{us_user_setting_galaxy_missile}</td>
		<td><input type="checkbox" name="setting_galaxy_missile"{setting_galaxy_missile}></td>
	</tr>
	<tr>
		<td colspan="2">{us_user_settings_other_title}</td>
	</tr>
	<tr>
		<td>{us_user_setting_vacations_status}</td>
		<td><input type="checkbox" name="setting_vacations_status"{setting_vacations_status}> <span class="small_font">{setting_vacations_until}</span></td>
	</tr>
	<tr>
		<td>{us_user_setting_delete_account}</td>
		<td><input type="checkbox" name="setting_delete_account"{setting_delete_account}></td>
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