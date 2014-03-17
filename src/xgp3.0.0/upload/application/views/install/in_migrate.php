

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{ins_migrate_title}</h1>
			<br />
			<form action="{dis_ins_btn}" method="post">
			<input type="hidden" name="page" value="step1" />
				<div align="left">
					<h2>{ins_migrate_admin_data}</h2>
				</div>
				<div align="left">
					<label>{ins_migrate_admin_email}</label>
					<input type="text" name="admin_email" placeholder="{ins_ex_tag} admin@xgproyect.net" value="">
					<label>{ins_migrate_admin_password}</label>
					<input type="password" name="admin_password">
					<div align="center">
						<input type="button" class="btn btn-primary" name="next" onclick="submit();" value="{ins_migrate_start}">
					</div>
				</div>
			</form>
          </div>
        </div><!--/span-->