    {gate_time_script}
    <form action="" method="post">
        <table border="0">
            <tr>
                <th>{in_jump_gate_start_moon}</th>
              <th>{gate_start_link}</th>
            </tr>
            <tr>
                <th>{in_jump_gate_finish_moon}</th>
                <th>
                <select name="jmpto">
                {gate_dest_moons}
                </select>
                </th>
            </tr>
		</table>
        <table width="519">
                <tr>
                	<td class="c" colspan="2">{in_jump_gate_select_ships}</td>
                </tr>
                <tr>
                	<th class="l" colspan="2" align="right">
                        <table width="100%">
                        	<tr>
                        		<td style="background-color: transparent;" align="right">{gate_wait_time}</td>
                        	</tr>
                        </table>
                	</th>
                </tr>
                	{gate_fleet_rows}
                <tr>
                	<th colspan="2"><input value="{in_jump_gate_jump}" type="submit"></th>
                </tr>
        {gate_script_go}
        </table>
    </form>