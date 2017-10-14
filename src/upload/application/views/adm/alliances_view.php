

        <div class="span9">
          <div class="hero-unit">
			<h1>{al_title}</h1>
			<br />
			<form class="form-search" action="" method="get">
			  <input type="hidden" name="page" value="alliances">
			  <div class="input-append">
			    <input type="text" name="alliance" class="search-query input-large" placeholder="{al_alliance_placeholder}" value="{alliance}">
			    <button type="submit" class="btn">{al_search}</button>
			  </div>
			  <div class="btn-group">
			    <a class="btn btn-primary{status_box}" href="#"><i class="icon-wrench icon-white"></i> {al_actions}</a>
			    <a class="btn btn-primary dropdown-toggle{status_box}" data-toggle="dropdown" href="#"><span class="caret"></span></a>
			    <ul class="dropdown-menu">
			      <li><a href="admin.php?page=alliances&type={type}&alliance={alliance}&mode=edit"><i class="icon-pencil"></i> {al_edit}</a></li>
			      <li><a href="admin.php?page=alliances&alliance={alliance}&mode=delete"><i class="icon-trash"></i> {al_delete}</a></li>
			    </ul>
			  </div>
			  {alert}
			</form>
			<{tag} class="btn btn-info{status}" href="admin.php?page=alliances&type=info&alliance={alliance}">
				{al_general_info}
			</{tag}>
			<{tag} class="btn btn-info{status}" href="admin.php?page=alliances&type=ranks&alliance={alliance}">
				{al_ranks}
			</{tag}>
			<{tag} class="btn btn-info{status}" href="admin.php?page=alliances&type=members&alliance={alliance}">
				{al_members}
			</{tag}>

			{content}

          </div>
        </div><!--/span-->