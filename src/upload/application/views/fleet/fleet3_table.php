<script type="text/javascript" src="{js_path}flotten-min.js"></script>
<script type="text/javascript">
function getStorageFaktor() {
	return 1
}
</script>
<form action="game.php?page=fleet4" method="post" onsubmit='this.submit.disabled = true;'>
<input type="hidden" name="thisresource1"  value="{metal}" />
<input type="hidden" name="thisresource2"  value="{crystal}" />
<input type="hidden" name="thisresource3"  value="{deuterium}" />
<input type="hidden" name="consumption"    value="{consumption}" />
<input type="hidden" name="dist"           value="{distance}" />
<input type="hidden" name="speedfactor"    value="{speedfactor}" />
<input type="hidden" name="thisgalaxy"     value="{thisgalaxy}" />
<input type="hidden" name="thissystem"     value="{thissystem}" />
<input type="hidden" name="thisplanet"     value="{thisplanet}" />
<input type="hidden" name="galaxy"         value="{galaxy}" />
<input type="hidden" name="system"         value="{system}" />
<input type="hidden" name="planet"         value="{planet}" />
<input type="hidden" name="thisplanettype" value="{thisplanettype}" />
<input type="hidden" name="planettype"     value="{planettype}" />
<input type="hidden" name="speedallsmin"   value="{speedallsmin}" />
<input type="hidden" name="speed"          value="{speed}" />
<input type="hidden" name="speedfactor"    value="{speedfactor}" />
<input type="hidden" name="usedfleet"      value="{usedfleet}" />
<input type="hidden" name="maxepedition"   value="{maxepedition}" />
<input type="hidden" name="curepedition"   value="{curepedition}" />
<input type="hidden" name="fleet_group"    value="{fleet_group}" />
<input type="hidden" name="acs_target_mr"  value="{acs_target_mr}" />
{input_extra}
<br />
<div id="content">
    <table border="0" cellpadding="0" cellspacing="1" width="519">
        <tr align="left" height="20">
        	<td class="c" colspan="2">{title}</td>
        </tr>
		<tr align="left" valign="top">
			<th width="50%">
        		<table border="0" cellpadding="0" cellspacing="0" width="259">
        			<tr height="20">
        				<td class="c" colspan="2">{fl_mission}</td>
        			</tr>
        				{missionselector}
        		</table>
        	</th>
        	<th>
				<table border="0" cellpadding="0" cellspacing="0" width="259">
        			<tr height="20">
        				<td colspan="3" class="c">{fl_resources}</td>
        			</tr>
                    <tr height="20">
        				<th>{Metal}</th>
        				<th><a href="javascript:maxResource('1');">{fl_max}</a></th>
        				<th><input name="resource1" size="10" onchange="calculateTransportCapacity();" type="text"></th>
        			</tr>
                    <tr height="20">
        				<th>{Crystal}</th>
        				<th><a href="javascript:maxResource('2');">{fl_max}</a></th>
        				<th><input name="resource2" size="10" onchange="calculateTransportCapacity();" type="text"></th>
        			</tr>
                    <tr height="20">
        				<th>{Deuterium}</th>
        				<th><a href="javascript:maxResource('3');">{fl_max}</a></th>
        				<th><input name="resource3" size="10" onchange="calculateTransportCapacity();" type="text"></th>
        			</tr>
                    <tr height="20">
        				<th>{fl_resources_left}</th>
        				<th colspan="2"><div id="remainingresources">-</div></th>
        			</tr>
                    <tr height="20">
        				<th colspan="3"><a href="javascript:maxResources()">{fl_all_resources}</a></th>
        			</tr>
                    <tr height="20">
        				<th colspan="3">&nbsp;</th>
        			</tr>
        				{stayblock}
				</table>
			</th>
		</tr>
        <tr height="20">
        	<th colspan="2"><input value="{fl_continue}" type="submit" name="submit"></th>
        </tr>
    </table>
</div>
</form>