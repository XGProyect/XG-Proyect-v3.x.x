<h2>{al_alliance_information}</h2>
{alert_info}
<form name="save_info" method="post" action="">
<input type="hidden" name="alliance_name_orig" value="{alliance_name}">
<input type="hidden" name="alliance_tag_orig" value="{ally_tag}">
<input type="hidden" name="alliance_owner_orig" value="{ally_founder}">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<th width="50%">{al_alliance_information_field}</th>
		<th width="50%">{al_alliance_information_value}</th>
	</tr>
	<tr>
		<td>{al_alliance_information_register_time}</td>
		<td>{alliance_register_time}</td>
	</tr>
	<tr>
		<td>{al_alliance_information_name}</td>
		<td><input type="text" name="alliance_name" value="{alliance_name}"></td>
	</tr>
	<tr>
		<td>{al_alliance_information_tag}</td>
		<td><input type="text" name="ally_tag" value="{ally_tag}"></td>
	</tr>
	<tr>
		<td>{al_alliance_information_owner}</td>
		<td>
			<select name="alliance_owner">
				{alliance_owner}
			</select>
		</td>
	</tr>
	<tr>
		<td>{al_alliance_information_owner_range}</td>
		<td><input type="text" name="alliance_owner_range" value="{alliance_owner_range}"></td>
	</tr>
	<tr>
		<td>{al_alliance_information_web}</td>
		<td><input type="text" name="alliance_web" value="{alliance_web}"></td>
	</tr>
	<tr>
		<td>{al_alliance_information_image}</td>
		<td><input type="text" name="alliance_image" value="{alliance_image}"></td>
	</tr>
	<tr>
		<td>{al_alliance_information_description}</td>
		<td>
			<textarea name="alliance_description" class="field span12" rows="10">{alliance_description}</textarea>
		</td>
	</tr>
	<tr>
		<td>{al_alliance_information_text}</td>
		<td>
			<textarea name="alliance_text" class="field span12" rows="10">{alliance_text}</textarea>
		</td>
	</tr>
	<tr>
		<td>{al_alliance_information_request}</td>
		<td>
			<textarea name="alliance_request" class="field span12" rows="10">{alliance_request}</textarea>
		</td>
	</tr>
	<tr>
		<td>{al_alliance_information_request_notallow}</td>
		<td>
			<select name="alliance_request_notallow">
				<option value="1" {sel1}>{al_allow_yes}</option>
				<option value="0" {sel0}>{al_allow_no}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div align="center">
				<input type="submit" class="btn btn-primary" name="send_data" value="{al_send_data}">
			</div>
		</td>
	</tr>
</table>
</form>