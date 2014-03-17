<script type="text/javascript" src="{js_path}flotten-min.js"></script>
<script type="text/javascript">
function getStorageFaktor() {
	return 1
}
function returnValue(param,select) {
/* By lucky - required for the new select box */
var string = select.options[select.selectedIndex].value;
	if(string!=0){
		var array=string.split(";");
		return array[param];
	}else{
		return null;
	}
}
</script>
<form action="game.php?page=fleet3" method="post" onsubmit='this.submit.disabled = true;'>
    {fleetblock}
    <input type="hidden" name="speedallsmin"   value="{speedallsmin}" />
    <input type="hidden" name="usedfleet"      value="{fleetarray}" />
    <input type="hidden" name="thisgalaxy"     value="{galaxy}" />
    <input type="hidden" name="thissystem"     value="{system}" />
    <input type="hidden" name="thisplanet"     value="{planet}" />
    <input type="hidden" name="galaxyend"      value="{galaxy_post}" />
    <input type="hidden" name="systemend"      value="{system_post}" />
    <input type="hidden" name="planetend"      value="{planet_post}" />
    <input type="hidden" name="speedfactor"    value="{speedfactor}" />
    <input type="hidden" name="thisplanettype" value="{planet_type}" />
    <input type="hidden" name="thisresource1"  value="{metal}" />
    <input type="hidden" name="thisresource2"  value="{crystal}" />
    <input type="hidden" name="thisresource3"  value="{deuterium}" />
    <br />
    <div id="content">
    	<table width="519" border="0" cellpadding="0" cellspacing="1">
        	<tr height="20">
        		<td colspan="2" class="c">{fl_send_fleet}</td>
        	</tr>
            <tr height="20">
            	<th width="50%">{fl_destiny}</th>
            	<th>
                    <input name="galaxy" size="3" maxlength="2" onChange="shortInfo()" onKeyUp="shortInfo()" value="{g}" />
                    <input name="system" size="3" maxlength="3" onChange="shortInfo()" onKeyUp="shortInfo()" value="{s}" />
                    <input name="planet" size="3" maxlength="2" onChange="shortInfo()" onKeyUp="shortInfo()" value="{p}" />
                    <select name="planettype" onChange="shortInfo()" onKeyUp="shortInfo()">
                    {options_planettype}
                    </select>
                    <input name="fleet_group" type="hidden" onChange="shortInfo()" onKeyUp="shortInfo()" value="0" />
                    <input name="acs_target_mr" type="hidden" onChange="shortInfo()" onKeyUp="shortInfo()" value="0:0:0" />
            	</th>
            </tr>
            <tr height="20">
            	<th>{fl_fleet_speed}</th>
            	<th>
                <select name="speed" onChange="shortInfo()" onKeyUp="shortInfo()">
                    {options}
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
            {shortcut}
            <tr height="20">
            	<td colspan="2" class="c">{fl_my_planets}</td>
            </tr>
            {colonylist}
            </tr>
            <tr height="20">
                <td colspan="2" class="c">{fl_acs_title}</td>
            </tr>
            {asc}
            <tr height="20">
            	<th colspan="2"><input type="submit" name="submit" value="{fl_continue}" /></th>
            </tr>
        </table>
    </div>
    <input type="hidden" name="maxepedition" value="{maxepedition}" />
    <input type="hidden" name="curepedition" value="{curepedition}" />
    <input type="hidden" name="target_mission" value="{target_mission}" />
</form>
<script>javascript:shortInfo(); </script>