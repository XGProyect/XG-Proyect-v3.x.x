<br />
<div id="content">
    <table border="0" cellpadding="0" cellspacing="1" width="750">
        <tbody>
            <tr height="20" valign="left">
                <td class="c" colspan="{mount}">{iv_imperium_title}</td>
            </tr>
            <tr height="75">
                <th width="75">{iv_planet}</th>
                {file_images}
            </tr>
            <tr height="20">
            <th width="75">{iv_name}</th>
            {file_names}
            </tr>
            <tr height="20">
            <th width="75">{iv_coords}</th>
            {file_coordinates}
            </tr>
            <tr height="20">
            <th width="75">{iv_fields}</th>
            {file_fields}
            </tr>
            <tr>
            <td class="c" colspan="{mount}" align="left">{iv_resources}</td>
            </tr>
            <tr>
            <th width="75">{Metal}</th>
            {file_metal}
            </tr>
            <tr>
            <th width="75">{Crystal}</th>
            {file_crystal}
            </tr>
            <tr>
            <th width="75">{Deuterium}</th>
            {file_deuterium}
            </tr>
            <tr>
            <th width="75">{Energy}</th>
            {file_energy}
            </tr>
            <tr>
                <td class="c" colspan="{mount}" align="left">{iv_buildings}</td>
            </tr>
        <!-- Buildings list -->
        {building_row}
            <tr height="20">
                <td class="c" colspan="{mount}" align="left">{iv_technology}</td>
            </tr>
        <!-- Technology list -->
        {technology_row}
            <tr height="20">
                <td class="c" colspan="{mount}" align="left">{iv_ships}</td>
            </tr>
        <!-- Ships list -->
        {fleet_row}
            <tr height="20">
                <td class="c" colspan="{mount}" align="left">{iv_defenses}</td>
            </tr>
        <!-- Defenses list -->
        {defense_row}
        </tbody>
    </table>
</div>