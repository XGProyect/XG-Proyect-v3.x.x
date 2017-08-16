<h2>{premium}</h2>
{alert_info}
<form name="save_info" method="post" action="">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<th width="50%">{us_user_premium_field}</th>
		<th width="50%">{us_user_premium_value}</th>
	</tr>
	<tr>
		<td>{us_user_premium_dark_matter}</td>
		<td><input type="text" name="premium_dark_matter" value="{premium_dark_matter}"></td>
	</tr>
	{premium_table}
	<tr>
		<td colspan="2">
			<div align="center">
				<input type="submit" class="btn btn-primary" name="send_data" value="{us_send_data}">
			</div>
		</td>
	</tr>
</table>
</form>