<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<br />
<div id="content">
    <form action="game.php?page=alliance&mode=apply&allyid={allyid}" method=POST>
    <table width=519>
        <tr>
          <td class=c colspan=2>{Write_to_alliance}</td>
        </tr>
        <tr>
          <th>{al_message} (<span id="cntChars">{chars_count}</span> / 6000 {al_characters})</th>
          <th><textarea name="text" cols="40" rows="10" onkeyup="javascript:cntchar(6000)">{text_apply}</textarea></th>
        </tr>
        <tr>
          <th colspan="2"><input type="submit" name="enviar" value="{al_applyform_send}"/> <input type="submit" name="enviar" value="{al_applyform_reload}"/></th>
        </tr>
    </table>
    </form>
</div>