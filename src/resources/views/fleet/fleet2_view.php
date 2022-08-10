<script type="text/javascript" src="{js_path}flotten-min.js"></script>
<script type="text/javascript">
    function getStorageFaktor() {
        return 1
    }
    function returnValue(param, select) {
        /* By lucky - required for the new select box */
        var string = select.options[select.selectedIndex].value;
        if (string != 0) {
            var array = string.split(";");
            return array[param];
        } else {
            return null;
        }
    }
</script>
<form action="game.php?page=fleet3" method="post" onsubmit='this.submit.disabled = true;' role="form">
    {fleet_block}
        <input type="hidden" name="consumption{ship_id}" value="{consumption}" />
        <input type="hidden" name="speed{ship_id}" value="{speed}" />
        <input type="hidden" name="capacity{ship_id}" value="{capacity}" />
        <input type="hidden" name="ship{ship_id}" value="{ship}" />
    {/fleet_block}
    <input type="hidden" name="speedfactor" value="{speedfactor}" />
    <input type="hidden" name="thisgalaxy" value="{galaxy}" />
    <input type="hidden" name="thissystem" value="{system}" />
    <input type="hidden" name="thisplanet" value="{planet}" />
    <input type="hidden" name="thisplanettype" value="{planet_type}" />
    <input type="hidden" name="target_mission" value="{target_mission}" />
    <br />
    <div id="content" role="main">
        <table width="519" border="0" cellpadding="0" cellspacing="1">
            <tr height="20">
                <td colspan="2" class="c">{fl_send_fleet}</td>
            </tr>
            <tr height="20">
                <th width="50%">{fl_destiny}</th>
                <th>
                    <input name="galaxy" type="number" style="width: 37px" min="1" maxlength="2" onChange="shortInfo()" onKeyUp="shortInfo()" value="{galaxy_end}" />
                    <input name="system" type="number" style="width: 40px" min="1" maxlength="3" onChange="shortInfo()" onKeyUp="shortInfo()" value="{system_end}" />
                    <input name="planet" type="number" style="width: 37px" min="1" maxlength="2" onChange="shortInfo()" onKeyUp="shortInfo()" value="{planet_end}" />
                    <select name="planettype" onChange="shortInfo()" onKeyUp="shortInfo()">
                        {planet_types}
                        <option value="{value}"{selected}>{title}</option>
                        {/planet_types}
                    </select>
                    <input name="fleet_group" type="hidden" onChange="shortInfo()" onKeyUp="shortInfo()" value="0" />
                    <input name="acs_target" type="hidden" onChange="shortInfo()" onKeyUp="shortInfo()" value="0:0:0" />
                </th>
            </tr>
            <tr height="20">
                <th>{fl_fleet_speed}</th>
                <th>
                    <select name="speed" onChange="shortInfo()" onKeyUp="shortInfo()">
                        <option value="10">100</option>
                        <option value="9">90</option>
                        <option value="8">80</option>
                        <option value="7">70</option>
                        <option value="6">60</option>
                        <option value="5">50</option>
                        <option value="4">40</option>
                        <option value="3">30</option>
                        <option value="2">20</option>
                        <option value="1">10</option>
                    </select> %
                </th>
            </tr>
            <tr height="20">
                <th>{fl_distance}</th>
                <th><div id="distance">-</div></th>
            </tr>
            <tr height="20">
                <th>{fl_flying_time}</th>
                <th><div id="duration">-</div></th>
            </tr>
            <tr height="20">
                <th>{fl_fuel_consumption}</th>
                <th><div id="consumption">-</div></th>
            </tr>
            <tr height="20">
                <th>{fl_max_speed}</th>
                <th><div id="maxspeed">-</div></th>
            </tr>
            <tr height="20">
                <th>{fl_cargo_capacity}</th>
                <th><div id="storage">-</div></th>
            </tr>
            {shortcuts}
            <tr height="20">
                <td colspan="2" class="c">{fl_my_planets}</td>
            </tr>
            {colonies}
            </tr>
            <tr height="20">
                <td colspan="2" class="c">{fl_acs_title}</td>
            </tr>
            {acs}
                <tr height="20">
                    <th colspan="2">
                        <a href="javascript:setTarget({galaxy},{system},{planet},{planet_type}); shortInfo(); setACS({id}); setACS_target('g{galaxy}s{system}p{planet}t{planet_type}');">
                            ({name})
                        </a>
                    </th>
                </tr>
            {/acs}
            <tr height="20">
                <th colspan="2"><input type="submit" name="submit" value="{fl_continue}" /></th>
            </tr>
        </table>
    </div>
</form>
<script>javascript:shortInfo();</script>
