<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<br />
<div id="content" role="main">
    {status_message}
    <table width="519px" style="border: 2px solid {error_color}; text-align: center; background: transparent;">
        <tr style="background:transparent;">
            <td style="background:transparent;"><font color="{error_color}"><strong>{error_text}</strong></font></td>
        </tr>
    </table>
    <br />
    {/status_message}
    <form action="game.php?page=chat&playerId={id}" method="post" role="form">
        <table width="519px">
            <tr>
                <td class="c" colspan="2">{pm_send_message}</td>
            </tr>
            <tr>
                <th scope="row">{pm_to}</th>
                <th role="cell">{to}</th>
            </tr>
            <tr>
                <th scope="row">{pm_subject}</th>
                <th role="cell"><input type="text" name="subject" size="40" maxlength="40" value="{subject}" /></th>
            </tr>
            <tr>
                <th scope="row">{pm_message} (<span id="cntChars">0</span> / 5000 {pm_chars})</th>
                <th role="cell"><textarea name="text" cols="40" rows="10" size="100" onkeyup="javascript:cntchar(5000)">{text}</textarea></th>
            </tr>
            <tr>
                <th role="cell" colspan="2"><input type="submit" value="{pm_send}" /></th>
            </tr>
        </table>
    </form>
</div>
