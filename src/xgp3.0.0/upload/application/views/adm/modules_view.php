

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{mdl_modules}</h1>
			<br />
			<form name="frm_modules" method="POST" action="?page=modules">
				{module_rows}
				<div align="center">
					<input type="submit" name="save" value="{mdl_save}" class="btn btn-primary">
				</div>
			</form>
          </div>
        </div><!--/span-->