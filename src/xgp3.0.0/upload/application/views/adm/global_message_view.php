<script src="{js_path}cntchar-min.js" type="text/javascript"></script>

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{ma_send_global_message}</h1>
			<br />
			<form action="admin.php?page=globalmessage&mode=change" method="POST" name="frm_global_message">
		    <table width="100%" class="table table-bordered table-hover table-condensed">
	            <tr>
	                <td>{ma_subject}</td>
	                <td><input name="temat" maxlength="100" value="{ma_none}" type="text"></td>
	            </tr>
				<tr>
	                <td>{ma_characters}</td>
	                <td><input name="result" value="5000" readonly="true" class="character"></td>
	            </tr>
	            <tr>
	                <td colspan="2">
	                	<textarea name="tresc" class="field span12" rows="10" onKeyDown="contar('frm_global_message','tresc',5000)" onKeyUp="contar('frm_global_message','tresc',5000)"></textarea>
	                	{ma_send_as} <input type="checkbox" name="message" checked> <span class="small_font">{ma_send_as_message}</span> <input type="checkbox" name="mail"> <span class="small_font">{ma_send_as_email}</span>
					</td>
	            </tr>
	            <tr>
	                <th colspan="2">
	                	<div align="center">
	                		<input value="{ma_send_message}" type="submit" class="btn btn-primary">
	                	</div>
	                </td>
	            </tr>
		    </table>
			</form>
          </div>
        </div><!--/span-->