

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{re_reset_all}</h1>
			<br />
				<form name="frm_reset" action="" method="post">
				<table width="100%" class="table table-bordered table-hover table-condensed">
					<tr>
						<td colspan="2"><span class="bold_font">{re_defenses_and_ships}</span></td>
					</tr>
					<tr>
						<td>{re_defenses}</td>
						<td><input type="checkbox" name="defenses" /></td>
					</tr>
					<tr>
						<td>{re_ships}</td>
						<td><input type="checkbox" name="ships" /></td>
					</tr>
					<tr>
						<td>{re_reset_hangar}</td>
						<td><input type="checkbox" name="h_d" /></td>
					</tr>
					<tr>
						<td colspan="2"><span class="bold_font">{re_buldings}</span></td></tr>
					<tr>
						<td>{re_buildings_pl}</td>
						<td><input type="checkbox" name="edif_p" /></td>
					</tr>
					<tr>
						<td>{re_buildings_lu}</td>
						<td><input type="checkbox" name="edif_l" /></td>
					</tr>
					<tr>
						<td>{re_reset_buldings}</td>
						<td><input type="checkbox" name="edif" /></td>
					</tr>
					<tr>
						<td colspan="2"><span class="bold_font">{re_inve_ofis}</span></td>
					</tr>
						<tr><td>{re_ofici}</td>
						<td><input type="checkbox" name="ofis" /></td>
					</tr>
						<tr><td>{re_investigations}</td>
						<td><input type="checkbox" name="inves" /></td>
					</tr>
						<tr><td>{re_reset_invest}</td>
						<td><input type="checkbox" name="inves_c" /></td>
					</tr>
					<tr>
						<td colspan="2"><span class="bold_font">{re_resources}</span></td>
					</tr>
						<tr><td>{re_resources_dark}</td>
						<td><input type="checkbox" name="dark" /></td>
					</tr>
					<tr>
						<td>{re_resources_met_cry}</td>
						<td><input type="checkbox" name="resources" /></td>
					</tr>
					<tr>
						<td colspan="2"><span class="bold_font">{re_general}</span></td>
					</tr>
					<tr>
						<td>{re_reset_moons}</td>
						<td><input type="checkbox" name="moons" /></td>
					</tr>
					<tr>
						<td>{re_reset_notes}</td>
						<td><input type="checkbox" name="notes" /></td>
					</tr>
					<tr>
						<td>{re_reset_rw}</td>
						<td><input type="checkbox" name="rw" /></td>
					</tr>
					<tr>
						<td>{re_reset_buddies}</td>
						<td><input type="checkbox" name="friends" /></td>
					</tr>
					<tr>
						<td>{re_reset_allys}</td>
						<td><input type="checkbox" name="alliances" /></td>
					</tr>
					<tr>
						<td>{re_reset_fleets}</td>
						<td><input type="checkbox" name="fleets" /></td>
					</tr>
					<tr>
						<td>{re_reset_banned}</td>
						<td><input type="checkbox" name="banneds" /></td>
					</tr>
					<tr>
						<td>{re_reset_messages}</td>
						<td><input type="checkbox" name="messages" /></td>
					</tr>
					<tr>
						<td>{re_reset_statpoints}</td>
						<td><input type="checkbox" name="statpoints" /></td>
					</tr>
					<tr>
						<td><span class="text-error bold_font">{re_reset_all}</span></td>
						<td><input type="checkbox" name="resetall" /></td>
					</tr>
					<tr>
						<td colspan="2">
							<div align="center">
								<input type="submit" value="{re_reset_go}" onClick="return confirm('{re_reset_universe_confirmation}');" class="btn btn-primary">
							</div>
						</td>
					</tr>
				</table>
				</form>
          </div>
        </div><!--/span-->