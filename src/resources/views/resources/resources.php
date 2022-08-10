<br />
<div id="content" role="main">
    <form action="" method="post" role="form">
        <table width="569">
            <tbody>
                <tr>
                    <td class="c" colspan="5">{Production_of_resources_in_the_planet}</td>
                </tr><tr>
                    <th height="22">&nbsp;</th>
                    <th width="60">{metal}</th>
                    <th width="60">{crystal}</th>
                    <th width="60">{deuterium}</th>
                    <th width="60">{energy}</th>
                    <th class="k"><input name="action" value="{rs_calculate}" type="submit"></th>
                </tr><tr>
                    <th height="22">{rs_basic_income}</th>
                    <td class="k">{metal_basic_income}</td>
                    <td class="k">{crystal_basic_income}</td>
                    <td class="k">{deuterium_basic_income}</td>
                    <td class="k">{energy_basic_income}</td>
                </tr>
                {resource_row}
                <tr>
                    <th height="22">{research_plasma_technology} ({level}: {plasma_level})</th>
                    <td class="k">{plasma_metal}</td>
                    <td class="k">{plasma_crystal}</td>
                    <td class="k">{plasma_deuterium}</td>
                    <td class="k">0</td>
                </tr><tr>
                    <th height="22">{rs_storage_capacity}</th>
                    <td class="k">{planet_metal_max}</td>
                    <td class="k">{planet_crystal_max}</td>
                    <td class="k">{planet_deuterium_max}</td>
                    <td class="k">0</td>
                </tr><tr>
                    <th height="22">{rs_sum}</th>
                    <td class="k">{metal_total}</td>
                    <td class="k">{crystal_total}</td>
                    <td class="k">{deuterium_total}</td>
                    <td class="k">{energy_total}</td>
                </tr>
                <tr>
                    <th>{rs_daily}</th>
                    <th>{daily_metal}</th>
                    <th>{daily_crystal}</th>
                    <th>{daily_deuterium}</th>
                    <th>{energy_total}</th>
                </tr>
                <tr>
                    <th>{rs_weekly}</th>
                    <th>{weekly_metal}</th>
                    <th>{weekly_crystal}</th>
                    <th>{weekly_deuterium}</th>
                    <th>{energy_total}</th>
                </tr>
            </tbody>
        </table>
    </form>
</div>
