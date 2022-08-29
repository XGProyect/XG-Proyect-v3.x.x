<script language="JavaScript" src="{js_path}flotten-min.js"></script>
<script language="JavaScript" src="{js_path}ocnt-min.js"></script>
<br />
<div id="content" role="main">
    <table width="519" border="0" cellpadding="0" cellspacing="1">
        <tr height="20">
            <td colspan="9" class="c">
                <table border="0" width="100%">
                    <tr>
                        <td style="background-color: transparent;">{fl_fleets} {fleets} / {max_fleets} &nbsp; &nbsp; {fl_expeditions} {expeditions} / {max_expeditions}</td>
                        <td style="background-color: transparent;" align="right"><a href="game.php?page=movement">{fl_fleets_movements}</a></td>
                    </tr>
                </table>
            </td>
        </tr>
        {no_slot}
    </table>
    <form action="game.php?page=fleet2" method="POST" role="form">
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
            {list_of_ships}
            <tr height="20px">
                <th scope="row">
                    {ship_name}
                </th>
                <th role="cell">
                    {ship_amount}
                </th>
                <th role="cell">
                    {max_ships_link}
                </th>
                <th role="cell">
                    {ships_input}
                    <input type="hidden" name="maxship{ship_id}" value="{max_ships}" />
                    <input type="hidden" name="consumption{ship_id}" value="{consumption}" />
                    <input type="hidden" name="speed{ship_id}" value="{speed}" />
                    <input type="hidden" name="capacity{ship_id}" value="{capacity}" />
                </th>
            </tr>
            {/list_of_ships}
            </tr>
            {none_max_selector}
            {no_ships}
            {continue_button}
        </table>
        <input type="hidden" name="galaxy" value="{galaxy}" />
        <input type="hidden" name="system" value="{system}" />
        <input type="hidden" name="planet" value="{planet}" />
        <input type="hidden" name="planet_type" value="{planettype}" />
        <input type="hidden" name="target_mission" value="{target_mission}" />
    </form>
</div>