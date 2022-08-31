<br />
<div id="content" role="main">
    <form action="game.php?page=renameplanet" method="POST" role="form">
        <table width="519">
            <tr>
                <td class="c" colspan="3">{rp_your_planet}</td>
            </tr><tr>
                <th>{rp_coords}</th>
                <th>{rp_planet_name}</th>
                <th>{rp_actions}</th>
            </tr><tr>
                <th scope="row">{galaxy_galaxy}:{galaxy_system}:{galaxy_planet}</th>
                <th role="cell">{planet_name}</th>
                <th role="cell"><input type="submit" name="action" value="{rp_abandon_planet}"></th>
            </tr><tr>
                <th role="cell">{rp_planet_rename}</th>
                <th role="cell"><input type="text" name="newname" size=25 maxlength=20></th>
                <th role="cell"><input type="submit" name="action" value="{rp_planet_rename_action}"></th>
            </tr>
        </table>
    </form>
</div>
