<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<form action="game.php?page=alliance&mode=admin&edit=requests&show={id}&sort=0" method="POST" role="form">
    <tr>
        <th colspan="2">{request_from}</th>
    </tr>
    <tr>
        <th colspan="2">{request_text}</th>
    </tr>
    <tr>
        <td class="c" colspan="2">{al_reply_to_request}</td>
    </tr>
    <tr>
        <th>{al_reason} <span id="cntChars">0</span> / 500 {al_characters}</th>
        <th><textarea name="text" cols="40" rows="10" onkeyup="javascript:cntchar(500)"></textarea></th>
    </tr>
    <tr>
        <th colspan="2">
            <input type="submit" name="accept" value="{al_acept_request}"/> <input type="submit" name="cancel" value="{al_decline_request}"/>
        </th>
    </tr>
</form>