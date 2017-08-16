<script type="text/javascript">$(function () {$('#checkall').click(function () {$(this).parents('table:eq(0)').find(':checkbox').attr('checked', this.checked);});});</script>


        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{mg_title}</h1>
			<br />
			<form name="frm_message_filter" method="POST" action="">
				<table width="100%" class="table table-bordered table-hover table-condensed">
					<tr>
						<td colspan="3">{mg_filter_by}</td>
					</tr>
					<tr>
						<td width="250px">
							<label>{mg_filter_message_id}</label>
							<input type="text" name="message_id" class="input-small">
						</td>
						<td>
							<label>{mg_filter_user}</label>
							<input type="text" name="message_user">
						</td>
						<td>
							<label>{mg_filter_planet}</label>
							<input type="text" name="message_planet">
						</td>
					</tr>
					<tr>
						<td>
							<label>{mg_filter_date}</label>
							<select name="message_day" class="input-mini">
								<option value="0">-</option>
								{days_options}
							</select>
							<span style="font-size:30px">/</span>
							<select name="message_month" class="input-mini">
								<option value="0">-</option>
								{months_options}
							</select>
							<span style="font-size:30px">/</span>
							<select name="message_year" class="input-small">
								<option value="0">-</option>
								{years_options}
							</select>
						</td>
						<td>
							<label>{mg_filter_type}</label>
							<select name="message_type" class="input-mini">
								<option value="0">-</option>
								{type_options}
							</select>
						</td>
						<td>
							<label>{mg_filter_content}</label>
							<input type="text" name="message_text">
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div align="center">
								<input type="submit" name="search" value="{mg_filter_start_search}" class="btn btn-primary">
							</div>
						</td>
					</tr>
				</table>
			</form>
			<form name="frm_message_results" method="POST" action="">
				<table width="100%" class="table table-bordered table-hover table-condensed">
					<tr>
						<td colspan="7">{mg_search_results}</td>
					</tr>
					<tr>
						<td colspan="7">
							<div align="center">
								<input type="submit" name="delete" value="{mg_delete_selected}" class="btn btn-primary">
							</div>
						</td>
					</tr>
					<tr>
						<td>
							{mg_delete_id} <input type="checkbox" name="checkall" id="checkall">
						</td>
						<td>{mg_delete_sender}</td>
						<td>{mg_delete_receiver}</td>
						<td>{mg_delete_time}</td>
						<td>{mg_delete_type}</td>
						<td>{mg_delete_from}</td>
						<td>{mg_delete_subject}</td>
					</tr>
					{results}
					<tr>
						<td colspan="7">
							<div align="center">
								<input type="submit" name="delete" value="{mg_delete_selected}" class="btn btn-primary">
							</div>
						</td>
					</tr>
				</table>
			</form>
          </div>
        </div><!--/span-->