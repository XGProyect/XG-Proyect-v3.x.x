<script language="JavaScript">
    function f(target_url, win_name) {
        var new_win = window.open(target_url, win_name, 'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=800,height=600,top=0,left=0');
        new_win.focus();
    }
</script>
<br />
<div id="content" role="main">
    <form action="game.php?page=messages" method="post" role="form">
        <table width="519">
            <table>
                <tr>
                    <td>
                        <input name="messages" value="1" type="hidden">
                        <table width="519">
                            <tr>
                                <td class="c" colspan="4">{mg_title}</td></tr><tr>
                                <th>{mg_action}</th>
                                <th>{mg_date}</th>
                                <th>{mg_from}</th>
                                <th>{mg_subject}</th>
                            </tr>
                            {message_list}
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
                                <td class="b"></td>
                                <td colspan="3" class="b">{message_text}</td>
                            </tr>
                            {/message_list}
                            <tr>
                                <th colspan="4">
                                    &nbsp;
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4">
                                    <select id="deletemessages" name="deletemessages">
                                        <option value="deletemarked">{mg_delete_marked}</option>
                                        <option value="deleteunmarked">{mg_delete_unmarked}</option>
                                        <option value="deleteall">{mg_delete_all}</option>
                                    </select>
                                    <input value="{mg_confirm_action}" type="submit">
                                </th>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                        </table>
                        <table width="100%">
                            <tr>
                                <td class="c">{mg_operators}</td>
                            </tr>
                            {operators_list}
                            <tr>
                                <th colspan="4">{user_name} <a href="mailto:{user_email}"><img src="{dpath}/img/m.gif" alt=""/></a></th>
                            </tr>
                            {/operators_list}
                        </table>
                    </td>
                </tr>
            </table>
        </table>
    </form>
</div>