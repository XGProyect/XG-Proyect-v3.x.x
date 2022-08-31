<script type="text/javascript" src="{js_path}flotten-min.js"></script>
<script type="text/javascript">
    function getStorageFaktor() {
        return 1
    }
</script>
<form action="game.php?page=fleet4" method="post" onsubmit='this.submit.disabled = true;' role="form">
    <input type="hidden" name="thisresource1"  value="{this_metal}" />
    <input type="hidden" name="thisresource2"  value="{this_crystal}" />
    <input type="hidden" name="thisresource3"  value="{this_deuterium}" />
    <input type="hidden" name="thisgalaxy"     value="{this_galaxy}" />
    <input type="hidden" name="thissystem"     value="{this_system}" />
    <input type="hidden" name="thisplanet"     value="{this_planet}" />
    <input type="hidden" name="thisplanettype" value="{this_planet_type}" />
    <input type="hidden" name="galaxy"         value="{galaxy_end}" />
    <input type="hidden" name="system"         value="{system_end}" />
    <input type="hidden" name="planet"         value="{planet_end}" />
    <input type="hidden" name="planettype"     value="{planet_type_end}" />
    <input type="hidden" name="speed"          value="{speed}" />
    <input type="hidden" name="speedfactor"    value="{speedfactor}" />
    {fleet_block}
        <input type="hidden" name="consumption{ship_id}" value="{consumption}" />
        <input type="hidden" name="speed{ship_id}" value="{speed}" />
        <input type="hidden" name="capacity{ship_id}" value="{capacity}" />
        <input type="hidden" name="ship{ship_id}" value="{ship}" />
    {/fleet_block}
    <br />
    <div id="content" role="main">
        <table role="presentation" border="0" cellpadding="0" cellspacing="1" width="519">
            <tr align="left" height="20">
                <td class="c" colspan="2">{title}</td>
            </tr>
            <tr align="left" valign="top">
                <th width="50%">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="259">
                        <tr height="20">
                            <td class="c" colspan="2">{fl_mission}</td>
                        </tr>
                        {mission_selector}
                        <tr height="20">
                            <th>
                                <input id="{id}" type="radio" name="mission" value="{value}"{checked}/>
                                <label for="{id}">{mission}</label>
                                <br />
                                {expedition_message}
                            </th>
                        </tr>
                        {/mission_selector}
                    </table>
                </th>
                <th>
                    <table border="0" cellpadding="0" cellspacing="0" width="259">
                        <tr height="20">
                            <td colspan="3" class="c">{fl_resources}</td>
                        </tr>
                        <tr height="20">
                            <th scope="row">{metal}</th>
                            <th role="cell"><a href="javascript:maxResource('1');">{fl_max}</a></th>
                            <th role="cell"><input name="resource1" size="10" onchange="calculateTransportCapacity();" type="text"></th>
                        </tr>
                        <tr height="20">
                            <th scope="row">{crystal}</th>
                            <th role="cell"><a href="javascript:maxResource('2');">{fl_max}</a></th>
                            <th role="cell"><input name="resource2" size="10" onchange="calculateTransportCapacity();" type="text"></th>
                        </tr>
                        <tr height="20">
                            <th scope="row">{deuterium}</th>
                            <th role="cell"><a href="javascript:maxResource('3');">{fl_max}</a></th>
                            <th role="cell"><input name="resource3" size="10" onchange="calculateTransportCapacity();" type="text"></th>
                        </tr>
                        <tr height="20">
                            <th scope="row">{fl_resources_left}</th>
                            <th role="cell" colspan="2"><div id="remainingresources">-</div></th>
                        </tr>
                        <tr height="20">
                            <th role="cell" colspan="3"><a href="javascript:maxResources()">{fl_all_resources}</a></th>
                        </tr>
                        <tr height="20">
                            <th role="cell" colspan="3">&nbsp;</th>
                        </tr>
                        {stay_block}
                    </table>
                </th>
            </tr>
            <tr height="20">
                <th colspan="2"><input value="{fl_continue}" type="submit" name="submit"></th>
            </tr>
        </table>
    </div>
</form>
