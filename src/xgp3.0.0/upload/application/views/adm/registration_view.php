

        <div class="span9">
	      {alert}
          <div class="hero-unit">
            <h1>{ur_settings}</h1>
            <br />
			<form action="" method="POST">
			<input type="hidden" name="opt_save" value="1">
				<label>
					<strong>{ur_open_close}</strong>
				</label>
				<input name="reg_closed"{reg_closed} type="checkbox" />

				<label>
					<strong>{ur_welcome_message}</strong>
				</label>
				<input name="reg_welcome_message"{reg_welcome_message} type="checkbox" />

				<label>
					<strong>{ur_welcome_email}</strong>
				</label>
				<input name="reg_welcome_email"{reg_welcome_email} type="checkbox" />

				<div align="center">
					<input value="{ur_save_parameters}" type="submit" class="btn btn-primary">
				</div>

			</form>
          </div>
        </div><!--/span-->