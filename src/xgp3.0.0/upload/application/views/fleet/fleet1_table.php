<script language="JavaScript" src="{js_path}flotten-min.js"></script>
<script language="JavaScript" src="{js_path}ocnt-min.js"></script>
<br />
<div id="content">
    <table width="519" border="0" cellpadding="0" cellspacing="1">
        <tr height="20">
        	<td colspan="9" class="c">
                <table border="0" width="100%">
                    <tr>
                        <td style="background-color: transparent;">{fl_fleets} {flyingfleets} / {maxfleets} &nbsp; &nbsp; {fl_expeditions} {currentexpeditions} / {maxexpeditions}</td>
                        <td style="background-color: transparent;" align="right"><a href="game.php?page=movement">{fl_fleets_movements}</a></td>
                    </tr>
                </table>
        	</td>
        </tr>
            {message_nofreeslot}
        </table>
        {acs_members}
        <form action="game.php?page=fleet2" method="POST">
        <table width="519" border="0" cellpadding="0" cellspacing="1">
            <tr height="20">
            	<td colspan="4" class="c">{fl_new_mission_title}</td>
            </tr>
            <tr height="20">
                <th>{fl_ship_type}</th>
                <th>{fl_ship_available}</th>
                <th>-</th>
                <th>-</th>
            </tr>
            	{body}
            </tr>
            {none_max_selector}
			{noships_row}
			{continue_button}
    	</table>
        	{shipdata}
            <input type="hidden" name="galaxy" value="{galaxy}" />
            <input type="hidden" name="system" value="{system}" />
            <input type="hidden" name="planet" value="{planet}" />
            <input type="hidden" name="planet_type" value="{planettype}" />
            <input type="hidden" name="mission" value="{target_mission}" />
            <input type="hidden" name="maxepedition" value="{envoimaxexpedition}" />
            <input type="hidden" name="curepedition" value="{expeditionencours}" />
            <input type="hidden" name="target_mission" value="{target_mission}" />
        </form>
</div>