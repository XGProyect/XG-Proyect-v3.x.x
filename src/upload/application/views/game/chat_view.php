<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<br />
<div id="content">
    {status_message}
    <table width="519px" style="border: 2px solid {error_color}; text-align:center;background:transparent;">
        <tr style="background:transparent;">
            <td style="background:transparent;"><font color="{error_color}"><strong>{error_text}</strong></font></td>
        </tr>
    </table>
    <br />
    {/status_message}
    <form action="game.php?page=chat&playerId={id}" method="post">
        <table width="519px">
            <tr>
                <td class="c" colspan="2">{mg_send_message}</td>
            </tr>
            <tr>
                <th>{mg_to}</th>
                <th>{to}</th>
            </tr>
            <tr>
                <th>{mg_subject}</th>
                <th><input type="text" name="subject" size="40" maxlength="40" value="{subject}" /></th>
            </tr>
            <tr>
                <th>{mg_message} (<span id="cntChars">0</span> / 5000 {mg_chars})</th>
                <th><textarea name="text" cols="40" rows="10" size="100" onkeyup="javascript:cntchar(5000)">{text}</textarea></th>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" value="{mg_send}" /></th>
            </tr>
        </table>
    </form>
</div>