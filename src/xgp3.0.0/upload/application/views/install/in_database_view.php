

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{ins_install_title}</h1>
			<br />
			<form action="{dis_ins_btn}" method="post">
			<input type="hidden" name="page" value="step1" />
				<div align="left">
					<h2>{ins_connection_data_title}</h2>
				</div>
				<div align="left">
					<label>{ins_server_title}</label>
					<input type="text" name="host" placeholder="{ins_ex_tag} localhost" value=""/>
					<label>{ins_db_title}</label>
					<input type="text" name="db" placeholder="{ins_ex_tag} xgproyect" value="">
					<label>{ins_user_title}</label>
					<input type="text" name="user" placeholder="{ins_ex_tag} root" value="">
					<label>{ins_password_title}</label>
					<input type="password" name="password">
					<label>{ins_prefix_title}</label>
					<input type="text" name="prefix" placeholder="{ins_ex_tag} xgp_" value="">
					<div align="center">
						<input type="button" class="btn btn-primary" name="next" onclick="submit();" value="{ins_install_go}">
					</div>
					<div align="center">
						<br/>
						<span class="text-error">{ins_chmod_notice}</span>
					</div>
				</div>
			</form>
          </div>
        </div><!--/span-->