<br />
<div id="content">
    <form action="game.php?page=preferences&mode=change" method="post">
        <table width="519px">
            <tbody>
                <tr>
                    <td class="c" colspan="2">User data</td>
                </tr>
                <tr>
                    <th colspan="2">Players Name</th>
                </tr>
                <tr>
                    <th>Your player name:</th>
                    <th>lucky</th>
                </tr>
                <tr>
                    <th>New player name:</th>
                    <th><input name="new_user_name" size="20" type="text"></th>
                </tr>
                <tr>
                    <th>Enter password (as confirmation):</th>
                    <th><input name="user_password" size="20" type="password"></th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: left; font-weight: normal;">
                        You can change your username once per week. To do so, click on your name or the settings at the top of the screen.
                    </th>
                </tr>
                <tr>
                    <th colspan="2">Players Password</th>
                </tr>
                <tr>
                    <th>Current player password:</th>
                    <th><input name="user_password" size="20" type="password"></th>
                </tr>
                <tr>
                    <th>New player password:</th>
                    <th><input name="new_user_password" size="20" type="password"></th>
                </tr>
                <tr>
                    <th colspan="2">Players Email</th>
                </tr>
                <tr>
                    <th>Your player email:</th>
                    <th>lucky@lucky.com</th>
                </tr>
                <tr>
                    <th>New player email:</th>
                    <th><input name="user_email" size="20" type="email"></th>
                </tr>
                <tr>
                    <th colspan="2"><input value="Use settings" type="submit"></th>
                </tr>
                <tr>
                    <td class="c" colspan="2">General</td>
                </tr>
                <tr>
                    <th colspan="2">Spy probes</th>
                </tr>
                <tr>
                    <th>Number of espionage probes:</th>
                    <th><input name="preference_spy_probes" size="3" type="number" min="1" max="99"></th>
                </tr>
                <tr>
                    <th colspan="2"><input value="Use settings" type="submit"></th>
                </tr>
                <tr>
                    <td class="c" colspan="2">Display</td>
                </tr>
                <tr>
                    <th colspan="2">Your planets</th>
                </tr>
                <tr>
                    <th>Sort planets by:</th>
                    <th>
                        <select name="">
                            <option value="">Order of emergence</option>
                            <option value="">Coordinates</option>
                            <option value="">Alphabet</option>
                            <option value="">Size</option>
                            <option value="">Used fields</option>
                        </select>
                    </th>
                </tr>
                <tr>
                    <th>Sorting sequence:</th>
                    <th>
                        <select name="">
                            <option value="">up</option>
                            <option value="">down</option>
                        </select>
                    </th>
                </tr>
                <tr>
                    <th colspan="2"><input value="Use settings" type="submit"></th>
                </tr>
                <tr>
                    <td class="c" colspan="2">Extended</td>
                </tr>
                <tr>
                    <th colspan="2">Vacation Mode</th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: left; font-weight: normal;">
                        <p>Vacation mode is designed to protect you during long absences from the game. You can only activate it when none of your fleets are in transit. Building and research orders will be put on hold.</p>
                        <p>Once vacation mode is activated, it will protect you from new attacks. Attacks that have already started will, however, continue and your production will be set to zero.</p>
                        <p>Vacation mode lasts a minimum of 48 hours. Only after this time expires will you be able to deactivate it.</p>
                    </th>
                </tr>
                <tr>
                    <th colspan="2">
                        <input value="Activate" type="submit">
                    </th>
                </tr>
                <tr>
                    <th colspan="2">Your Account</th>
                </tr>
                <tr>
                    <th>Delete account</th>
                    <th>
                        <input type="checkbox">
                    </th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: left; font-weight: normal;">
                        <p>Check here to have your account marked for automatic deletion after 7 days.</p>
                    </th>
                </tr>
                <tr>
                    <th colspan="2"><input value="Use settings" type="submit"></th>
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
