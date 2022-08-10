<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<br />
<div id="content" role="main">
    <form action="game.php?page=alliance&mode=circular&sendmail=1" method="POST" role="form">
        <table width="530">
            <tr>
                <td class="c" colspan="2">{al_circular_send_ciruclar}</td>
            </tr>
            <tr>
                <th>{al_receiver}</th>
                <th>
                    <select name="r">
                        <option value="0">{al_all_players}</option>
                        {ranks_list}
                        <option value="{value}">{name}</option>
                        {/ranks_list}
                    </select>
                </th>
            </tr>
            <tr>
                <th>{al_message} (<span id="cntChars">0</span> / 5000 {al_characters})</th>
                <th>
                    <textarea name="text" cols="60" rows="10" onkeyup="javascript:cntchar(5000)"></textarea>
                </th>
            </tr>
            <tr>
                <td class="c"><a href="game.php?page=alliance">{al_back}</a></td>
                <td align="center" class="c">
                    <input type="reset" value="{al_circular_reset}">
                    <input type="submit" value="{al_circular_send_submit}">
                </td>
            </tr>
        </table>
    </form>
</div>