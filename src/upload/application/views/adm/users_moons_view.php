<h2>{moons}</h2>
{alert_info}
<form name="save_info" method="post" action="">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<th width="50%">{us_user_moons_header}</th>
		<th width="50%">{us_user_moons_actions}</th>
	</tr>
	{moons_table}
	<tr>
		<td colspan="2">
			<div align="center">
				<input type="submit" class="btn btn-primary" name="send_data" value="{us_send_data}">
			</div>
		</td>
	</tr>
</table>
</form>