<br />
<div id="content">
    <form action="game.php?page=renameplanet" method="POST">
        <table width="519">
        <tr>
            <td colspan="3" class="c">{ov_security_request}</td>
        </tr><tr>
            <th colspan="3">{ov_security_confirm} {galaxy_galaxy}:{galaxy_system}:{galaxy_planet} {ov_with_pass}</th>
        </tr><tr>
            <th>{ov_password}</th>
            <th><input type="password" name="pw"></th>
            <th><input type="submit" name="action" value="{ov_delete_planet}"></th>
        </tr>
        </table>
    <input type="hidden" name="kolonieloeschen" value="1">
    <input type="hidden" name="deleteid" value ="{planet_id}">
    </form>
</div>