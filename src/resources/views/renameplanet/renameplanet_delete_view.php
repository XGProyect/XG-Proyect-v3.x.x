<br />
<div id="content" role="main">
    <form action="game.php?page=renameplanet" method="POST" role="form">
        <table width="519">
            <tr>
                <td colspan="3" class="c">{rp_security_request}</td>
            </tr><tr>
                <th colspan="3">{rp_security_confirm} {galaxy_galaxy}:{galaxy_system}:{galaxy_planet} {rp_with_pass}</th>
            </tr><tr>
                <th>{rp_password}</th>
                <th><input type="password" name="pw"></th>
                <th><input type="submit" name="action" value="{rp_delete_planet}"></th>
            </tr>
        </table>
        <input type="hidden" name="kolonieloeschen" value="1">
        <input type="hidden" name="deleteid" value ="{planet_id}">
    </form>
</div>
