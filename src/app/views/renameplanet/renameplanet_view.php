<br />
<div id="content">
    <form action="game.php?page=renameplanet" method="POST">
        <table width=519>
            <tr>
                <td class="c" colspan=3>{rp_your_planet}</td>
            </tr><tr>
                <th>{rp_coords}</th>
                <th>{rp_planet_name}</th>
                <th>{rp_actions}</th>
            </tr><tr>
                <th>{galaxy_galaxy}:{galaxy_system}:{galaxy_planet}</th>
                <th>{planet_name}</th>
                <th><input type="submit" name="action" value="{rp_abandon_planet}"></th>
            </tr><tr>
                <th>{rp_planet_rename}</th>
                <th><input type="text" name="newname" size=25 maxlength=20></th>
                <th><input type="submit" name="action" value="{rp_planet_rename_action}"></th>
            </tr>
        </table>
    </form>
</div>
