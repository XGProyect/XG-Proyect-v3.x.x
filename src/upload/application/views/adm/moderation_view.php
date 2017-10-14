

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{mod_title}</h1>
			<br />
				<form action="" method="post">
				<table widtd="100%" class="table table-bordered table-hover table-condensed">
				<tr>
					<th colspan="6">{mod_title}</th>
				</tr>
				<tr>
					<td>{mod_range}</td>
					<td width="30px"><i class="icon-adjust" title="{mod_power_config}"></i></td>
					<td width="30px"><i class="icon-eye-open" title="{mod_power_info}"></i></td>
					<td width="30px"><i class="icon-pencil" title="{mod_power_edition}"></i></td>
					<td width="30px"><i class="icon-wrench" title="{mod_power_tools}"></i></td>
					<td width="30px"><i class="icon-tint" title="{mod_power_maintenance}"></i></td>
					<td width="30px"><i class="icon-lock" title="{mod_power_log}"></i></td>
				</tr>				
				<tr>
					<td>{ge_go}</td>
					<td><input type="checkbox" {config_m} name="config_m" value="on"/></td>
					<td><input type="checkbox" {view_m} name="view_m" value="on" /></td>
					<td><input type="checkbox" {edit_m} name="edit_m" value="on"/></td>
					<td><input type="checkbox" {tools_m} name="tools_m" value="on"/></td>
					<td><input type="checkbox" {maintenance_m} name="maintenance_m" value="on"/></td>
					<td><input type="checkbox" {log_m} name="log_m" value="on"/></td>
				</tr>
				
				<tr>
					<td>{ge_sgo}</td>
					<td><input type="checkbox" {config_o} name="config_o" value="on"/></td>
					<td><input type="checkbox" {view_o} name="view_o" value="on"/></td>
					<td><input type="checkbox" {edit_o} name="edit_o" value="on"/></td>
					<td><input type="checkbox" {tools_o} name="tools_o" value="on"/></td>
					<td><input type="checkbox" {maintenance_o} name="maintenance_o" value="on"/></td>
					<td><input type="checkbox" {log_o} name="log_o" value="on"/></td>
				</tr>
				
				<tr>
					<td>{ge_ga}</td>
					<td><input type="checkbox" checked="checked" disabled="disabled"/></td>
					<td><input type="checkbox" checked="checked" disabled="disabled"/></td>
					<td><input type="checkbox" checked="checked" disabled="disabled"/></td>
					<td><input type="checkbox" checked="checked" disabled="disabled"/></td>
					<td><input type="checkbox" checked="checked" disabled="disabled"/></td>
					<td><input type="checkbox" {log_a} name="log_a" value="on"/></td>
				</tr>
				<tr>
					<th colspan="6">
						<div align="center">
							<input type="submit" value="{mod_save_all}" name="mode" class="btn btn-primary"/>
						</div>
					</td>
				</tr>
				</table>
				
				<br />
				<table widtd="100%" class="table table-bordered table-hover table-condensed">
					<tr><th colspan="2">{mod_reference}</th></tr>
					<tr><td width="30px"><i class="icon-adjust" title="{mod_power_config}"></i></td><td>{mod_power_config}</td></tr>
					<tr><td><i class="icon-eye-open" title="{mod_power_info}"></i></td><td>{mod_power_info}</td></tr>
					<tr><td><i class="icon-pencil" title="{mod_power_edition}"></i></td><td>{mod_power_edition}</td></tr>
					<tr><td><i class="icon-wrench" title="{mod_power_tools}"></i></td><td>{mod_power_tools}</td></tr>
					<tr><td><i class="icon-tint" title="{mod_power_maintenance}"></i></td><td>{mod_power_maintenance}</td></tr>
					<tr><td><i class="icon-lock" title="{mod_power_log}"></i></td><td>{mod_power_log}</td></tr>
				</table>
			</form>
          </div>
        </div><!--/span-->