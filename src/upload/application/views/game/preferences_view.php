<br />
<div id="content">
    <form action="game.php?page=preferences" method="post">
        <table width="519px">
            <tbody>
                <tr>
                    <td class="c" colspan="2">{pr_user_data}</td>
                </tr>
                <tr>
                    <th colspan="2">{pr_players_name}</th>
                </tr>
                <tr>
                    <th>{pr_your_player_name}:</th>
                    <th>{user_name}</th>
                </tr>
                <tr>
                    <th>{pr_new_player_name}:</th>
                    <th><input name="new_user_name" size="20" type="text"></th>
                </tr>
                <tr>
                    <th>{pr_enter_password_confirmation}:</th>
                    <th><input name="user_password" size="20" type="password"></th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: justify; font-weight: normal;">
                        {pr_username_change_message}
                    </th>
                </tr>
                <tr>
                    <th colspan="2">{pr_players_password}</th>
                </tr>
                <tr>
                    <th>{pr_player_current_password}:</th>
                    <th><input name="user_password" size="20" type="password"></th>
                </tr>
                <tr>
                    <th>{pr_new_player_password}:</th>
                    <th>
                        <input name="new_user_password" size="20" type="password">
                    </th>
                </tr>
                <tr>
                    <th colspan="2">{pr_players_email}</th>
                </tr>
                <tr>
                    <th>{pr_your_player_email}:</th>
                    <th>{user_email}</th>
                </tr>
                <tr>
                    <th>{pr_new_player_email}:</th>
                    <th>
                        <input name="user_email" size="20" type="email">
                    </th>
                </tr>
                <tr>
                    <th colspan="2">
                        <input value="{pr_use_settings}" name="apply_settings" type="submit">
                    </th>
                </tr>
                <tr>
                    <td class="c" colspan="2">{pr_general}</td>
                </tr>
                <tr>
                    <th colspan="2">{pr_spy_probes}</th>
                </tr>
                <tr>
                    <th>{pr_number_espionage_probes}:</th>
                    <th>
                        <input name="preference_spy_probes" value="{preference_spy_probes}" size="3" type="number" min="1" max="99">
                    </th>
                </tr>
                <tr>
                    <th colspan="2">
                        <input value="{pr_use_settings}" name="apply_settings" type="submit">
                    </th>
                </tr>
                <tr>
                    <td class="c" colspan="2">{pr_display}</td>
                </tr>
                <tr>
                    <th colspan="2">{pr_your_planets}</th>
                </tr>
                <tr>
                    <th>{pr_sort_planets_by}:</th>
                    <th>
                        <select name="sort_planet">
                            {sort_planet}
                            <option value="{value}"{selected}>{text}</option>
                            {/sort_planet}
                        </select>
                    </th>
                </tr>
                <tr>
                    <th>{pr_sorting_sequence}:</th>
                    <th>
                        <select name="sort_sequence">
                            {sort_sequence}
                            <option value="{value}"{selected}>{text}</option>
                            {/sort_sequence}
                        </select>
                    </th>
                </tr>
                <tr>
                    <th colspan="2">
                        <input value="{pr_use_settings}" name="apply_settings" type="submit">
                    </th>
                </tr>
                <tr>
                    <td class="c" colspan="2">{pr_extended}</td>
                </tr>
                <tr>
                    <th colspan="2">{pr_vacation_mode}</th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: justify; font-weight: normal;">
                        {pr_vacation_mode_explanation}
                    </th>
                </tr>
                <tr>
                    <th colspan="2">
                        <input value="{pr_activate}" name="activate_vacation_mode" type="submit">
                    </th>
                </tr>
                <tr>
                    <th colspan="2">{pr_your_account}</th>
                </tr>
                <tr>
                    <th>{pr_delete_account}</th>
                    <th>
                        <input type="checkbox">
                    </th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: justify; font-weight: normal;">
                        {pr_delete_account_explanation}
                    </th>
                </tr>
                <tr>
                    <th colspan="2">
                        <input value="{pr_use_settings}" name="apply_settings" type="submit">
                    </th>
                </tr>
            </tbody>
        </table>
    </form>
</div>


<!--
<br />
<div id="content">
    <form action="game.php?page=options&mode=exit" method="post">
        <table width="519">
            <tr>
                <td class="c" colspan="2">{op_vacation_mode_title}</td>
            </tr>
            <tr>
                {op_finish_vac_mode}
            </tr>
            <tr>
                {op_vac_mode_msg}
            </tr>
            <tr>
                <th><a title="{op_dlte_account_descrip}">{op_dlte_account}</a></th>
                <th>
                    <input name="db_deaktjava"{db_deaktjava} type="checkbox" /> {db_deaktjava_until}
                    {verify}
                </th>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" value="{op_save_changes}" /></th>
            </tr>
        </table>
    </form>
</div>-->
