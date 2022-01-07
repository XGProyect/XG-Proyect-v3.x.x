<br />
<div id="content">
    <form action="" method="post">
        <table style="margin-top:0px;width: 600px;" class="listOfResourceSettingsPerPlanet">
            <tr>
                <td class="c" colspan="7">{Production_of_resources_in_the_planet}</td>
            </tr>
            <tr>
                <th colspan="7" class="l" style="font-weight: 700;text-align: center;line-height: 27px;vertical-align: middle;color: #d3d3d3;">
                    <div style="display: block;float: none;margin: 0;">
                        <div style="width:376px; margin: 0px auto;">
                            <span style="color: #9c0;float: left;display: inline;">{rs_producto_factor}: 100%</span>
                            <span style="float: right;margin-left: 13px;display: inline;">
                                <input name="action" value="{rs_calculate}" type="submit" style="margin-top:5px">
                            </span>
                            <br style="clear: both;height: 0;font-size: 1px;line-height: 0;">
                        </div>
                    </div>
                </th>
            </tr>
            <tr>
                <td colspan="2" class="k"></td>
                <td class="k" style="text-align: right;">{metal}</td>
                <td class="k" style="text-align: right;width:55px">{crystal}</td>
                <td class="k" style="text-align: right;width:60px">{deuterium}</td>
                <td class="k" style="text-align: right;">{energy}</td>
                <td class="k" rowspan="2"></td>
            </tr>
            <tr>
                <th colspan="2" class="l" style="text-align: left;font-weight:400">{rs_basic_income}</th>
                <th style="text-align:right;color:#99CC00 !important;font-weight:400">
                    <span title="{metal_basic_income}">{metal_basic_income}</span>
                </th>
                <th style="text-align:right;color:#99CC00 !important;font-weight:400">
                    <span title="{crystal_basic_income}">{crystal_basic_income}</span>
                </th>
                <th style="text-align:right;font-weight:400">
                    <span title="{deuterium_basic_income}">{deuterium_basic_income}</span>
                </th>
                <th style="text-align:right;font-weight:400">
                    <span title="0">{energy_basic_income}</span>
                </th>
                <!-- <th></th> -->
            </tr>
            {resource_row}
            <!-- Resbuggy or Taladrador -->
            <!-- Plasma technology-->
            <tr>
                <th colspan="2" height="22" style="text-align: left;font-weight:400"> {research_plasma_technology} ({level} {plasma_level})</th>
                <th style="text-align: right;font-weight:400">{plasma_metal}</th>
                <th style="text-align: right;font-weight:400">{plasma_crystal}</th>
                <th style="text-align: right;font-weight:400">{plasma_deuterium}</th>
                <th style="text-align: right;font-weight:400">0</th>
                <th rowspan="7">
                </th>
            </tr>
            <!-- Objects -->

            <!-- Officies -->
            <tr>
                <th height="22" style="text-align: left;font-weight:400">{rs_geologist}</th>
                <th style="width: 35px;">
                    <a href="game.php?page=officier" title="{rs_geologist}" onmouseover="return overlib('<table width=150px><tr><td class=l>{premium_geologist_tip}</td></tr></table>', CENTER, ABOVE);" onmouseout="return nd();">
                        <img src="{dpath}premium/geologe_ikon{premium_geologist}.gif" width="25" height="25">
                    </a>
                </th>
                <th class="l" style="text-align:right;font-weight:400">{geologist_metal}</th>
                <th class="l" style="text-align:right;font-weight:400">{geologist_crystal}</th>
                <th class="l" style="text-align:right;font-weight:400">{geologist_deuterium}</th>
                <th class="l" style="text-align:right;font-weight:400;">{geologist_energy}</th>
                <!-- <th></th> -->
            </tr>
            <tr>
                <th height="22" style="text-align: left;font-weight:400">{rs_engineer}</th>
                <th style="width: 35px;">
                    <a href="game.php?page=officier" title="{rs_engineer}" onmouseover="return overlib('<table width=150px><tr><td class=l>{premium_engineer_tip}</td></tr></table>', CENTER, ABOVE);" onmouseout="return nd();">
                        <img src="{dpath}premium/ingenieur_ikon{premium_engineer}.gif" width="25" height="25">
                    </a>
                </th>
                <th class="l" style="text-align:right;font-weight:400">{officers_blocked}</th>
                <th class="l" style="text-align:right;font-weight:400">{officers_blocked}</th>
                <th class="l" style="text-align:right;font-weight:400">{officers_blocked}</th>
                <th class="l" style="text-align:right;font-weight:400">{engineer_energy}</th>
                <!-- <th class="l" style="text-align:right;"></th> -->
            </tr>
            <!-- Command team o Equipo Comando -->
            <tr>
                <th colspan="2" class="l" style="text-align: left;font-weight:400">{rs_storage_capacity}</th>
                <th class="l" style="text-align:right;font-weight:400;">{planet_metal_max}</span></th>
                <th class="l" style="text-align:right;font-weight:400">{planet_crystal_max}</th>
                <th class="l" style="text-align:right;font-weight:400">{planet_deuterium_max}</th>
                <th class="l" style="text-align:right;font-weight:400">-</th>
                <!-- <th class="l" style="text-align:right;"></th> -->
            </tr>
            <tr class="summary">
                <th colspan="2" class="l" style="text-align: left;"><em>{rs_sum}</em></th>
                <th class="l" style="text-align:right;font-weight:400;">{metal_total}</span></th>
                <th class="l" style="text-align:right;font-weight:400">{crystal_total}</th>
                <th class="l" style="text-align:right;font-weight:400">{deuterium_total}</th>
                <th class="l" style="text-align:right;font-weight:400">{energy_total}</th>
                <!-- <th class="l" style="text-align:right;font-weight:400"></th> -->
            </tr>
            <tr>
                <th colspan="2" class="l" style="text-align: left;"><em>{rs_daily}</em></th>
                <th class="l" style="text-align:right;font-weight:400">{daily_metal}</span></th>
                <th class="l" style="text-align:right;font-weight:400">{daily_crystal}</th>
                <th class="l" style="text-align:right;font-weight:400">{daily_deuterium}</th>
                <th class="l" style="text-align:right;font-weight:400">{energy_total}</th>
                <!-- <th class="l" style="text-align:right;font-weight:400"></th> -->
            </tr>
            <tr>
                <th colspan="2" class="l" style="text-align: left;"><em>{rs_weekly}</em></th>
                <th class="l" style="text-align:right;font-weight:400">{weekly_metal}</span></th>
                <th class="l" style="text-align:right;font-weight:400">{weekly_crystal}</th>
                <th class="l" style="text-align:right;font-weight:400">{weekly_deuterium}</th>
                <th class="l" style="text-align:right;font-weight:400">{energy_total}</th>
                <!-- <th class="l" style="text-align:right;font-weight:400"></th> -->
            </tr>
        </table>
    </form>


</div>
