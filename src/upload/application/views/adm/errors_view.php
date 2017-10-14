

        <div class="span9">
	      {alert}
          <div class="hero-unit">
			<h1>{er_error_list}</h1>
			<br />
			<table width="100%" class="table table-bordered table-hover table-condensed">
			    <tr>
			    	<td colspan="4">{er_error_list} [<a href="admin.php?page=errors&deleteall=yes">{er_dlte_all}</a>]</td>
			    </tr>
			    <tr>
			        <td width="25">{er_user_id}</td>
			        <td width="170">{er_type}</td>
			        <td width="230">{er_data}</td>
			        <td width="230">{er_track}</td>
			    </tr>
			    {errors_list}
			    <tr>
			    	<th colspan="5">{errors_list_resume}</th>
			    </tr>
			</table>
          </div>
        </div><!--/span-->