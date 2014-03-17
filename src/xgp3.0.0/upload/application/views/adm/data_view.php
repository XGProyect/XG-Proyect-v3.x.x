

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{db_opt_db}</h1>
			<br />
			<form action="" method="post">
			<table widtd="100%" class="table table-bordered table-hover table-condensed">
			<tr>
			    <th colspan="2">{db_opt_db}</th>
			</tr>
			 {tabla} 
			<tr>
			    <td colspan="2">
			    	<div align="center">
						<input value="{db_optimize}" type="submit" name="Optimize" class="btn btn-primary">
						<input value="{db_repair}" type="submit" name="Repair" class="btn btn-primary">
						<input value="{db_check}" type="submit" name="Check" class="btn btn-primary">
			    	</div>
				</td>
			</tr>
			</table>
			</form>
          </div>
        </div><!--/span-->