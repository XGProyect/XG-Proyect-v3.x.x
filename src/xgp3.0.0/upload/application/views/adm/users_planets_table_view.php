<tr>
	<td width="50%">
		<table width="100%" class="table table-condensed">
			<tr>
				<td width="50%">
					{planet_name} {planet_status}<br />
					<img src="{image_path}{planet_image}.jpg" alt="{planet_image}.jpg" title="{planet_image}.jpg" border="0">
				</td>
				<td width="50%">
					{moon_name} {moon_status}<br />
					{moon_image}
				</td>
			</tr>
		</table>
	</td>
	<td width="50%">
		<div class="btn-group">
			<button class="btn btn-info dropdown-toggle" data-toggle="dropdown">{us_user_planets_actions} <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="admin.php?page=users&type=planets&edit=planet&user={user}&planet={planet_id}">{us_user_planets_edit}</a></li>
				<li><a href="admin.php?page=users&type=planets&edit=buildings&user={user}&planet={planet_id}">{us_user_buildings_edit}</a></li>
				<li><a href="admin.php?page=users&type=planets&edit=ships&user={user}&planet={planet_id}">{us_user_ships_edit}</a></li>
				<li><a href="admin.php?page=users&type=planets&edit=defenses&user={user}&planet={planet_id}">{us_user_defenses_edit}</a></li>
				<li class="divider"></li>
				<li><a href="admin.php?page=users&type=planets&edit=delete&user={user}&planet={planet_id}">{us_user_delete_planet}</a></li>
				<li class="divider"></li>
				<li><a href="admin.php?page=maker&mode=moon&planet={planet_id}">{us_user_add_moon}</a></li>
				<li><a href="admin.php?page=users&type=moons&edit=delete&user={user}&moon={moon_id}">{us_user_delete_moon}</a></li>
			</ul>
		</div>
	</td>
</tr>