<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<br />
<div id="content" role="main">
    <form action="game.php?page=alliance&mode=apply&allyid={allyid}" method="POST" id="apply" role="form">
        <table width="519">
            <tr>
                <td class="c" colspan="2">{write_to_alliance}</td>
            </tr>
            <tr>
                <th scope="row">{al_message} (<span id="cntChars">0</span> / 6000 {al_characters})</th>
                <th role="cell"><textarea name="text" id="text" cols="40" rows="10" onkeyup="javascript:cntchar(6000)">{text_apply}</textarea></th>
            </tr>
            <tr>
                <th role="cell" colspan="2">
                    <input type="submit" name="send" value="{al_applyform_send}"/> <input type="reset" name="reload" value="{al_applyform_reload}" onclick="cntInitChars();"/>
                </th>
            </tr>
        </table>
    </form>
</div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    cntInitChars();
});

function cntInitChars() {
    
    this.event.preventDefault();
    document.getElementById('apply').reset();
    document.getElementById('cntChars').innerHTML = document.getElementById('text').value.length;
}
</script>