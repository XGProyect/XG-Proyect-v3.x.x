<tr>
	<td width="50%">
		{moon_name} {moon_status}<br />
		<img src="{image_path}{moon_image}.jpg" alt="{moon_image}.jpg" title="{moon_image}.jpg" border="0">
	</td>
	<td width="50%">
		<div class="btn-group">
			<button class="btn btn-info dropdown-toggle" data-toggle="dropdown">{us_user_planets_actions} <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="admin.php?page=users&type=moons&edit=moon&user={user}&moon={moon_id}">{us_user_moons_edit}</a></li>
				<li><a href="admin.php?page=users&type=moons&edit=buildings&user={user}&moon={moon_id}">{us_user_buildings_edit}</a></li>
				<li><a href="admin.php?page=users&type=moons&edit=ships&user={user}&moon={moon_id}">{us_user_ships_edit}</a></li>
				<li><a href="admin.php?page=users&type=moons&edit=defenses&user={user}&moon={moon_id}">{us_user_defenses_edit}</a></li>
				<li class="divider"></li>
				<li><a href="admin.php?page=users&type=moons&edit=delete&user={user}&moon={moon_id}">{us_user_delete_moon}</a></li>
			</ul>
		</div>
	</td>
</tr>