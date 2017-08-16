

        <div class="span9">
          <div class="hero-unit">
			<h1>{us_title}</h1>
			<br />
			<form class="form-search" action="" method="get">
			  <input type="hidden" name="page" value="users">
			  <div class="input-append">
			    <input type="text" name="user" class="search-query input-large" placeholder="{us_username_placeholder}" value="{user}">
			    <button type="submit" class="btn">{us_search}</button>
			  </div>
			  <div class="btn-group">
			    <a class="btn btn-primary{status_box}" href="#"><i class="icon-user icon-white"></i> {user_rank}</a>
			    <a class="btn btn-primary dropdown-toggle{status_box}" data-toggle="dropdown" href="#"><span class="caret"></span></a>
			    <ul class="dropdown-menu">
			      <li><a href="admin.php?page=users&type={type}&user={user}&mode=edit"><i class="icon-pencil"></i> {us_edit}</a></li>
			      <li><a href="admin.php?page=users&user={user}&mode=delete" onclick="return confirm('{us_delete_confirm}')"><i class="icon-trash"></i> {us_delete}</a></li>
			      <li><a href="admin.php?page=ban&mode=ban&ban_name={user}&regexp="><i class="icon-ban-circle"></i> {us_ban}</a></li>
			      <li class="divider"></li>
			      <li><a href="admin.php?page=moderation&moderation=2&user={user}"><i class="i"></i> {us_change_permissions}</a></li>
			    </ul>
			  </div>
			  {alert}
			</form>
			<{tag} class="btn btn-info{status}" href="admin.php?page=users&type=info&user={user}">
				{us_general_info}
			</{tag}>
			<{tag} class="btn btn-info{status}" href="admin.php?page=users&type=settings&user={user}">
				{us_settings}
			</{tag}>
			<{tag} class="btn btn-info{status}" href="admin.php?page=users&type=research&user={user}">
				{us_research}
			</{tag}>
			<{tag} class="btn btn-info{status}" href="admin.php?page=users&type=premium&user={user}">
				{us_premium}
			</{tag}>
			<{tag} class="btn btn-info{status}" href="admin.php?page=users&type=planets&user={user}">
				{us_planets}
			</{tag}>
			<{tag} class="btn btn-info{status}" href="admin.php?page=users&type=moons&user={user}">
				{us_moons}
			</{tag}>

			{content}

          </div>
        </div><!--/span-->