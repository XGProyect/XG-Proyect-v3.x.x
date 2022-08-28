<br>
<div id="content" role="main">
    {status_message}
    <table role="presentation" width="519px" style="border: 2px solid {error_color}; text-align: center; background: transparent;">
        <tr style="background: transparent;">
            <td style="background: transparent;">
                <span style="color: {error_color}; font-weight: bold">{error_text}</span>
            </td>
        </tr>
    </table>
    <br />
    {/status_message}
    <form action="game.php?page=preferences" method="post" role="form">
        <table role="presentation" width="519px">
            <tbody>
                <tr>
                    <td role="heading" aria-level="2" class="c" colspan="2">{pr_user_data}</td>
                </tr>
                <tr>
                    <th colspan="2">{pr_players_name}</th>
                </tr>
                <tr>
                    <th>{pr_your_player_name}:</th>
                    <th>{user_name}</th>
                </tr>
                <tr {hide_nickname_change}>
                    <th>{pr_new_player_name}:</th>
                    <th>
                        <input type="text" name="new_user_name" size="20" minlength="3" maxlength="20">
                    </th>
                </tr>
                <tr {hide_nickname_change}>
                    <th>{pr_enter_password_confirmation}:</th>
                    <th>
                        <input type="password" name="confirmation_user_password" size="20" minlength="8" autocomplete="off">
                    </th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: justify; font-weight: normal;">
                        {pr_username_change_message}
                    </th>
                </tr>
                <tr>
                    <th role="heading" aria-level="2" colspan="2">{pr_players_password}</th>
                </tr>
                <tr>
                    <th>{pr_player_current_password}:</th>
                    <th>
                        <input type="password" name="current_user_password" size="20" minlength="8" autocomplete="off">
                    </th>
                </tr>
                <tr>
                    <th>{pr_new_player_password}:</th>
                    <th>
                        <input type="password" name="new_user_password" size="20" minlength="8" autocomplete="off">
                    </th>
                </tr>
                <tr>
                    <th role="heading" aria-level="2" colspan="2">{pr_players_email}</th>
                </tr>
                <tr>
                    <th>{pr_your_player_email}:</th>
                    <th>{user_email}</th>
                </tr>
                <tr>
                    <th>{pr_new_player_email}:</th>
                    <th>
                        <input type="email" name="new_user_email" size="20" minlength="4" maxlength="64">
                    </th>
                </tr>
                <tr>
                    <th>{pr_enter_password_confirmation}:</th>
                    <th>
                        <input type="password" name="confirmation_email_password" size="20" minlength="8" autocomplete="off">
                    </th>
                </tr>
                <tr>
                    <th colspan="2">
                        <input type="submit" name="apply_settings" value="{pr_use_settings}">
                    </th>
                </tr>
                <tr {hide_vacation_invalid}>
                    <td role="heading" aria-level="2" class="c" colspan="2">{pr_general}</td>
                </tr>
                <tr {hide_vacation_invalid}>
                    <th colspan="2">{pr_spy_probes}</th>
                </tr>
                <tr {hide_vacation_invalid}>
                    <th>{pr_number_espionage_probes}:</th>
                    <th>
                        <input type="number" name="preference_spy_probes" value="{preference_spy_probes}" size="3" minlength="1" min="1" max="99" pattern="[0-9]*">
                    </th>
                </tr>
                <tr {hide_vacation_invalid}>
                    <th colspan="2">
                        <input type="submit" name="apply_settings" value="{pr_use_settings}">
                    </th>
                </tr>
                <tr {hide_vacation_invalid}>
                    <td role="heading" aria-level="2" class="c" colspan="2">{pr_display}</td>
                </tr>
                <tr {hide_vacation_invalid}>
                    <th role="heading" aria-level="3" colspan="2">{pr_your_planets}</th>
                </tr>
                <tr {hide_vacation_invalid}>
                    <th>{pr_sort_planets_by}:</th>
                    <th>
                        <select name="preference_planet_sort">
                            {sort_planet}
                            <option value="{value}"{selected}>{text}</option>
                            {/sort_planet}
                        </select>
                    </th>
                </tr>
                <tr {hide_vacation_invalid}>
                    <th>{pr_sorting_sequence}:</th>
                    <th>
                        <select name="preference_planet_sort_sequence">
                            {sort_sequence}
                            <option value="{value}"{selected}>{text}</option>
                            {/sort_sequence}
                        </select>
                    </th>
                </tr>
                <tr {hide_vacation_invalid}>
                    <th colspan="2">
                        <input type="submit" name="apply_settings" value="{pr_use_settings}">
                    </th>
                </tr>
                <tr>
                    <td role="heading" aria-level="2" class="c" colspan="2">{pr_extended}</td>
                </tr>
                <tr>
                    <th role="heading" aria-level="3" colspan="2">{pr_vacation_mode}</th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: justify; font-weight: normal;">
                        {pr_vacation_mode_active}
                        {pr_vacation_mode_explanation}
                    </th>
                </tr>
                <tr {disabled}>
                    <th colspan="2">
                        <input type="submit" onclick="javascript:return confirm('{pr_activate_alert}');" name="preference_vacation_mode" value="{pr_activate}" {hide_vacation_invalid}>
                        <input type="submit" name="preference_vacation_mode" value="{pr_deactivate}" {hide_no_vacation}>
                    </th>
                </tr>
                <tr>
                    <th role="heading" aria-level="3" colspan="2">{pr_your_account}</th>
                </tr>
                <tr>
                    <th>{pr_delete_account}</th>
                    <th>
                        <input type="checkbox" name="preference_delete_mode"{preference_delete_mode}>
                    </th>
                </tr>
                <tr {hide_delete}>
                    <th colspan="2" style="text-align: justify; font-weight: normal;">
                        {pr_delete_account_explanation}
                    </th>
                </tr>
                <tr>
                    <th colspan="2">
                        <input type="submit" name="apply_settings" value="{pr_use_settings}">
                    </th>
                </tr>
            </tbody>
        </table>
    </form>
</div>