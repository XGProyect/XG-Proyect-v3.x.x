<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<br/>
<div id="content" role="main">
    <form action="game.php?page=buddies&mode=1&sm=3" method="post" role="form">
        <input type="hidden" name="user" value="{user_id}">
        <table width="520">
            <tr>
                <td class="c" colspan="2">{bu_request_message}</td>
            </tr>
            <tr>
                <th>{bu_player}</th>
                <th>{user_name}</th>
            </tr>
            <tr>
                <th scope="row">
                    {bu_request_text} (<span id="cntChars">0</span> / 5000 {bu_characters})
                </th>
                <th role="cell">
                    <textarea name="text" cols="60" rows="10" onKeyUp="javascript:cntchar(5000)"></textarea>
                </th>
            </tr>
            <tr>
                <td class="c">
                    <a href="javascript:window.history.back();">{bu_back}</a>
                </td>
                <td class="c">
                    <input type="submit" value="{bu_send}">
                </td>
            </tr>
        </table>
    </form>
</div>
