

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{bku_title}</h1>
			<br />
			<form name="frm_backup" method="POST" action="?page=backup">
				<table width="100%" class="table table-bordered table-hover table-condensed">
				<tr>
					<td>
						<span class="{color}">{bku_auto}</span>
						<br/>
						<span class="small_font">{bku_auto_legend})</span>
					</td>
					<td><input type="checkbox" name="auto_backup" {checked} /></td>
				</tr>
				<tr>
					<td colspan="2">
						<div align="center">
							<input type="submit" class="btn btn-primary" name="save" value="{bku_save}" />
							<input type="submit" class="btn btn-primary" name="backup" value="{bku_now}" />
						</div>
					</td>
				</tr>
				</table>
			</form>
          </div>
        </div><!--/span-->