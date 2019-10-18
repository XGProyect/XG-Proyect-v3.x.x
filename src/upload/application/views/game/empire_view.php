<br />
<div id="content">
    <table border="0" cellpadding="0" cellspacing="1" width="750px">
        <tbody>
            <tr height="20px" valign="left">
                <td class="c" colspan="{amount_of_planets}">{iv_imperium_title}</td>
            </tr>
            <tr height="75px">
                <th width="75px">{iv_planet}</th>
                {image}
                <th width="75px">
                    <a href="game.php?page=overview&cp={planet_id}&re=0">
                        <img src="{dpath}planets/small/s_{planet_image}.jpg" border="0" width="80px" height="80px">
                    </a>
                </th>
                {/image}
            </tr>
            <tr height="20px">
                <th width="75px">{iv_name}</th>
                {name}
                <th width="75px">
                    {planet_name}
                </th>
                {/name}
            </tr>
            <tr height="20px">
                <th width="75px">{iv_coords}</th>
                {coords}
                <th width="75px">
                    <a href="game.php?page=galaxy&mode=3&galaxy={planet_galaxy}&system={planet_system}">{planet_coords}</a>
                </th>
                {/coords}
            </tr>
            <tr height="20px">
                <th width="75px">{iv_fields}</th>
                {fields}
                <th width="75px">
                    {planet_field_current} / {planet_field_max}
                </th>
                {/fields}
            </tr>
            <tr>
                <td class="c" colspan="{amount_of_planets}" align="left">{iv_resources}</td>
            </tr>
            <tr>
                <th width="75px">{Metal}</th>
                {metal}
                <th width="75px">
                    <a href="game.php?page=resources&cp={planet_id}&re=0&planettype={planet_type}">{planet_current_amount}</a> / {planet_production}
                </th>
                {/metal}
            </tr>
            <tr>
                <th width="75px">{Crystal}</th>
                {crystal}
                <th width="75px">
                    <a href="game.php?page=resources&cp={planet_id}&re=0&planettype={planet_type}">{planet_current_amount}</a> / {planet_production}
                </th>
                {/crystal}
            </tr>
            <tr>
                <th width="75px">{Deuterium}</th>
                {deuterium}
                <th width="75px">
                    <a href="game.php?page=resources&cp={planet_id}&re=0&planettype={planet_type}">{planet_current_amount}</a> / {planet_production}
                </th>
                {/deuterium}
            </tr>
            <tr>
                <th width="75px">{Energy}</th>
                {energy}
                <th width="75px">
                    {used_energy} / {max_energy}
                </th>
                {/energy}
            </tr>
            <tr>
                <td class="c" colspan="{amount_of_planets}" align="left">{iv_resources}</td>
            </tr>
            {resources}
            <tr>
                {value}
            </tr>
            {/resources}
            <tr>
                <td class="c" colspan="{amount_of_planets}" align="left">{iv_buildings}</td>
            </tr>
            {facilities}
            <tr>
                {value}
            </tr>
            {/facilities}
            <tr height="20px">
                <td class="c" colspan="{amount_of_planets}" align="left">{iv_defenses}</td>
            </tr>
            {defenses}
            <tr>
                {value}
            </tr>
            {/defenses}
            {missiles}
            <tr>
                {value}
            </tr>
            {/missiles}
            <tr height="20px">
                <td class="c" colspan="{amount_of_planets}" align="left">{iv_technology}</td>
            </tr>
            {tech}
            <tr>
                {value}
            </tr>
            {/tech}
            <tr height="20px">
                <td class="c" colspan="{amount_of_planets}" align="left">{iv_ships}</td>
            </tr>
            {fleet}
            <tr>
                {value}
            </tr>
            {/fleet}
        </tbody>
    </table>
</div>