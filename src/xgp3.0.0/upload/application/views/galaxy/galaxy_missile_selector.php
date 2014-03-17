<form action="game.php?page=galaxy&missiles=true&c={current}&mode=2&galaxy={galaxy}&system={system}&planet={planet}" method="POST">
	<tr>
		<table border="0">
			<tr>
				<td class="c" colspan="2">{gl_missil_launch} [{galaxy}:{system}:{planet}]</td>
			</tr>
			<tr>

				<td class="c">{missile_count} <input type="text" name="SendMI" size="2" maxlength="3" /></td>
				<td class="c">{gl_objective}:
                	<select name="Target">
                        <option value="all" selected>{gl_all_defenses}</option>
                        <option value="0">{ma_misil_launcher}</option>
                        <option value="1">{ma_small_laser}</option>
                        <option value="2">{ma_big_laser}</option>
                        <option value="3">{ma_gauss_canyon}</option>
                        <option value="4">{ma_ionic_canyon}</option>
                        <option value="5">{ma_buster_canyon}</option>
                        <option value="6">{ma_small_protection_shield}</option>
                        <option value="7">{ma_big_protection_shield}</option>
                    </select>
                </td>
			</tr>
			<tr>
				<td class="c" colspan="2"><input type="submit" name="aktion" value="{gl_missil_launch_action}"></td>
			</tr>
		</table>
</form>