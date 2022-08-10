<form action="game.php?page=galaxy&missiles=true&c={current}&mode=2&galaxy={selected_galaxy}&system={selected_system}&planet={selected_planet}" method="POST" role="form">
    <tr>
    <table border="0">
        <tr>
            <td class="c" colspan="2">{gl_missil_launch} {coords}</td>
        </tr>
        <tr>

            <td class="c">{missile_count} <input type="text" name="SendMI" size="2" maxlength="3" /></td>
            <td class="c">{gl_objective}:
                <select name="Target">
                    <option value="0" selected>{gl_all_defenses}</option>
                    <option value="1">{defense_rocket_launcher}</option>
                    <option value="2">{defense_light_laser}</option>
                    <option value="3">{defense_heavy_laser}</option>
                    <option value="4">{defense_gauss_cannon}</option>
                    <option value="5">{defense_ion_cannon}</option>
                    <option value="6">{defense_plasma_turret}</option>
                    <option value="7">{defense_small_shield_dome}</option>
                    <option value="8">{defense_large_shield_dome}</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="c" colspan="2"><input type="submit" name="aktion" value="{gl_missil_launch_action}"></td>
        </tr>
    </table>
</form>
