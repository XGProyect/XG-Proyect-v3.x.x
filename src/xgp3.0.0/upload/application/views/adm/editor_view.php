

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{ce_edit}</h1>
			<br />
			<form action="" method="post">
				<table widtd="100%" class="table table-bordered table-hover table-condensed">
				<tr>
					<th><p class="text-info">{ce_notice}</p></th>
				</tr>
				<tr>
					<td class="align_center">
						<select name="file_edit" class="input-xlarge" onchange="submit()">
							<option value="">{ce_file}</option>
							{language_files}
						</select>
					</td>
				</tr>
				<tr>
					<td><textarea name="file_content" rows="20" class="field span12">{contents}</textarea></td>
				</tr>
				<tr>
					<td>
						<div align="center">
							<input type="submit" name="save_file" value="{ce_save_changes}" class="btn btn-primary" onClick="return confirm('{ce_warning} {ce_sure}')">
						</div>
					</td>
				</tr>
				<tr>
					<th><p class="text-error">{ce_warning}</p></th>
				</tr>
				</table>
			</form>
          </div>
        </div><!--/span-->