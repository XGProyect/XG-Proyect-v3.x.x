<h2>{mk_alliance_title}</h2>
<form name="frm_addalliance" action="" method="POST">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<td>{mk_alliance_name}</th>
		<td><input type="text" name="name"></th>
	</tr>
	<tr>
		<td>{mk_alliance_tag}</th>
		<td><input type="text" name="tag"></th>
	</tr>
	<tr>
		<td>{mk_alliance_founder}</td>
		<td>
			<select name="founder">
				<option value="0">-</option>
				{founders_combo}
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div align="center">
				<input type="submit" value="{mk_alliance_add_alliance}" class="btn btn-primary" name="add_alliance">
			</div>
		</td>
	</tr>
</table>
</form>