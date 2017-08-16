

        <div class="span9">
	      {alert}
          <div class="hero-unit">
            <h1>{np_parameters}</h1>
            <br />
			<form action="" method="POST">
			<input type="hidden" name="opt_save" value="1">
				<label>
					<strong>{np_initial_fields}</strong>
				</label>
				<input name="initial_fields" maxlength="10" size="10" value="{initial_fields}" type="text">
				<label>
					<strong>{np_metal_production}</strong>
				</label>
				<input name="metal_basic_income" maxlength="10" size="10" value="{metal_basic_income}" type="text">
				<label>
					<strong>{np_crystal_production}</strong>
				</label>
				<input name="crystal_basic_income" maxlength="10" size="10" value="{crystal_basic_income}" type="text">
				<label>
					<strong>{np_deuterium_production}</strong>
				</label>
				<input name="deuterium_basic_income" maxlength="10" size="10" value="{deuterium_basic_income}" type="text">
				<label>
					<strong>{np_energy_production}</strong>
				</label>
				<input name="energy_basic_income" maxlength="10" size="10" value="{energy_basic_income}" type="text">

				<div align="center">
					<input value="{np_save_parameters}" type="submit" class="btn btn-primary">
				</div>

			</form>
          </div>
        </div><!--/span-->