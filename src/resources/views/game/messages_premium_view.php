<div id="content" style="top: 118px; height: 656px;" role="main">
    <center>
        <table class="header">
            <tbody>
                <tr class="header">
                    <td>
                        <table width="519">
                            <tbody>
                                <form action="{form_submit}" method="POST" role="form">
                                    <tr>
                                        <td colspan="4" class="c">{mg_title}</td>
                                    </tr>
                                    <tr>
                                        <th>{mg_show_title}</th>
                                        <th colspan="2">{mg_type_title}</th>
                                        <th>{mg_amount_title} / {mg_unread_title}</th>
                                    </tr>
                                    {message_type_list}
                                    <tr>
                                        <th>
                                            <input type="checkbox" name="{message_type}" {checked}>
                                        </th>
                                        <th colspan="2">
                                            <a href="?page=messages&dsp=1&{message_type}={checked_status}">{message_type_name}</a>
                                        </th>
                                        <th>{message_amount} / {message_unread}</th>
                                    </tr>
                                    {/message_type_list}
                                    {messages}
                                    <tr>
                                        <td class="c">{mg_action}</td>
                                        <td class="c">{mg_date}</td>
                                        <td class="c">{mg_from}</td>
                                        <td class="c">{mg_subject}</td>
                                    </tr>
                                    {messages_list}
                                    <tr>
                                        <th>
                                            <input type="hidden" name="showmes{message_id}" />
                                            <input type="checkbox" name="delmes{message_id}" />
                                        </th>
                                        <th>{message_time}</th>
                                        <th>{message_from} {message_reply}</th>
                                        <th>{message_subject}</th>
                                    </tr>
                                    <tr>
                                        <td class="b"> </td>
                                        <td colspan="3" class="b">{message_text}</td>
                                    </tr>
                                    {/messages_list}
                                    {/messages}
                                    <tr>
                                        <th colspan="4">
                                            {delete_options}
                                            <select name="deletemessages">
                                                <option value="deletemarked">{mg_delete_marked}</option>
                                                <option value="deleteunmarked">{mg_delete_unmarked}</option>
                                                <option value="deleteallshown">{mg_delete_all_shown}</option>
                                                <option value="deleteall">{mg_delete_all}</option>
                                            </select>
                                            {/delete_options}
                                            <input type="submit" value="{mg_confirm_action}">
                                        </th>
                                    </tr>
                                    <input type="hidden" name="messages" value="1" />
                                </form>
                                <form action="game.php?page=messages" method="POST" role="form">
                                    <tr height="20"> </tr>
                                    <tr>
                                        <td colspan="4" class="c">{mg_address_book}</td>
                                    </tr>
                                    <tr>
                                        <th>{mg_show_title}</th>
                                        <th colspan="2">{mg_type_title}</th>
                                        <th>{mg_amount_title}</th>
                                    </tr>
                                    <tr>
                                        <th><input type="checkbox" name="owncontactsopen" {owncontactsopen}></th>
                                        <th colspan="2">{mg_friends_list} </th>
                                        <th>{buddys_count}</th>
                                    </tr>
                                    {buddy_list}
                                    <tr>
                                        <th colspan="4">
                                            {user_name} <a href="game.php?page=chat&playerId={user_id}"><img src="{dpath}/img/m.gif" /></a>
                                        </th>
                                    </tr>
                                    {/buddy_list}
                                    <tr>
                                        <th><input type="checkbox" name="ownallyopen" {ownallyopen}></th>
                                        <th colspan="2">{mg_alliance}</th>
                                        <th>{alliance_count}</th>
                                    </tr>
                                    {members_list}
                                    <tr>
                                        <th colspan="4">
                                            {user_name} <a href="game.php?page=chat&playerId={user_id}"><img src="{dpath}/img/m.gif" /></a>
                                        </th>
                                    </tr>
                                    {/members_list}
                                    <tr>
                                        <th><input type="checkbox" name="gameoperatorsopen" {gameoperatorsopen}></th>
                                        <th colspan="2">{mg_operators}</th>
                                        <th>{operators_count}</th>
                                    </tr>
                                    {operators_list}
                                    <tr>
                                        <th colspan="4">{user_name} <a href="mailto:{user_email}"><img
                                                    src="{dpath}/img/m.gif" /></a></th>
                                    </tr>
                                    {/operators_list}

                                    <tr>
                                        <th colspan="4">
                                            <input type="hidden" name="addressbook" value="1">
                                            <input type="submit" value="{mg_confirm_action}">
                                        </th>
                                    </tr>
                                </form>
                                <form action="game.php?page=messages" method="POST" role="form">
                                    <tr height="20">
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="c">{mg_notes}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">{mg_show_title}</th>
                                        <th colspan="2">{mg_amount_title}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2"><input type="checkbox" name="noticesopen" {noticesopen}></th>
                                        <th colspan="2">{notes_count}</th>
                                    </tr>
                                    {notes_list}
                                    <tr>
                                        <th colspan="4">
                                            <a href="#" onclick="f('game.php?page=notes&a=2&n={note_id}', 'Notes')">
                                                <font color="{note_color}">{note_title}</font>
                                            </a>
                                        </th>
                                    </tr>
                                    {/notes_list}
                                    <tr>
                                        <th colspan="4">
                                            <input type="hidden" name="notices" value="1">
                                            <input type="submit" value="{mg_confirm_action}">
                                        </th>
                                    </tr>
                                </form>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </center>
</div>