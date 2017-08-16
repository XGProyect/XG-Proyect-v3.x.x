<script src="{js_path}cntchar-min.js" type="text/javascript"></script>

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{bn_title}</h1>
			<br />
			<form action="" method="POST" name="frm_ban">
				<table width="100%" class="table table-bordered table-hover table-condensed">
					<tr>
						<th>
							{bn_username}<br />
							<span class="small_font text-error">{banned_until}</span>
						</th>
						<td colspan="2">
							<input name="ban_name" type="text" value="{name}" readonly="true"/>
						</th>
					</tr>
					<tr>
						<th>
							{bn_reason} <br /><br />{bn_characters}<br /><input type="text" name="result" value="50" disabled class="input-mini"/>
						</th>
						<td colspan="2">
							<textarea name="why" class="field span12" rows="5" onKeyDown="contar('frm_ban','why',50)" onKeyUp="contar('frm_ban','why',50)">{reason}</textarea>
						</td>
					</tr>
					<tr>
						<th colspan="2">{changedate}</th>
					</tr>
					<tr>
						<th>{bn_time_days}</th>
						<td><input name="days" type="text" value="0"></td>
					</tr>
					<tr>
						<th>{bn_time_hours}</th>
						<td><input name="hour" type="text" value="0"></td>
					</tr>
					<tr>
						<th>{bn_vacation_mode}</th>
						<td>
							<input name="vacat" type="checkbox" {vacation}/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div align="center">
								<input type="submit" value="{bn_ban_user}" name="bannow" class="btn btn-primary">
							</div>
						</td>
					</tr>
				</table>
			</form>
          </div>
        </div><!--/span-->