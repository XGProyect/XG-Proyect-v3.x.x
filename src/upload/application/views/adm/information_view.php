

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{ia_title}</h1>
			<br />
			<table width="100%" class="table table-bordered table-hover table-condensed">
				<tr>
					<th colspan="2">{ia_upcoming_updates}</th>
				</tr>
				<tr>
					<th>{ia_name}</th>
					<th>{ia_value}</th>
				</tr>
				<tr>
					<td>{ia_next_points_update}</td>
					<td>{info_points}</td>
				</tr>
				<tr>
					<td>{ia_next_db_backup}</td>
					<td>{info_backup}</td>
				</tr>
				<tr>
					<td>{ia_next_cleanup}</td>
					<td>{info_cleanup}</td>
				</tr>
			</table>
			
			<br />
			
			<table width="100%" class="table table-bordered table-hover table-condensed">
				<tr>
					<th colspan="2">{ia_db_data}</th>
				</tr>
				<tr>
					<th>{ia_stats_name}</th>
					<th>{ia_stats_value}</th>
				</tr>
				<tr>
					<td>{ia_database_size}</td>
					<td>{info_database_size} <a href="admin.php?page=database"><i class="icon-wrench"></i></a></td>
				</tr>
				<tr>
					<td>{ia_database_server}</td>
					<td>{info_database_server}</td>
				</tr>
			</table>
			
			<br />
			
			<table width="100%" class="table table-bordered table-hover table-condensed">
				<tr>
					<th colspan="2">{ia_statistics}</th>
				</tr>
				<tr>
					<th>{ia_stats_name}</th>
					<th>{ia_stats_value}</th>
				</tr>
				<tr>
					<td>{ia_active_modules}</td>
					<td>{info_modules}</td>
				</tr>
				<tr>
					<td>{ia_total_users}</td>
					<td>{info_total_users}</td>
				</tr>
				<tr>
					<td>{ia_inactive_users}</td>
					<td>{info_inactive_users}</td>
				</tr>
				<tr>
					<td>{ia_vacation_users}</td>
					<td>{info_vacation_users}</td>
				</tr>
				<tr>
					<td>{ia_delete_mode_users}</td>
					<td>{info_delete_mode_users}</td>
				</tr>
				<tr>
					<td>{ia_banned_users}</td>
					<td>{info_banned_users}</td>
				</tr>
				<tr>
					<td>{ia_flying_fleets}</td>
					<td>{info_flying_fleets}</td>
				</tr>
			</table>
          </div>
        </div><!--/span-->