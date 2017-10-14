

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{et_encrypter}</h1>
			<br />
			<form name="frm_encrypter" method="POST" action="?page=encrypter">
				<label>
					<strong>{et_pass}</strong>
				</label>
				<input type="text" name="uncrypted" value="{uncrypted}">

				<label>
					<strong>{et_result}</strong>
				</label>
				<input type="text" name="encrypted" value="{encrypted}">

				<div align="center">
					<input type="submit" class="btn btn-primary" name="ok" value="{et_encript}">
				</div>
			</form>
          </div>
        </div><!--/span-->