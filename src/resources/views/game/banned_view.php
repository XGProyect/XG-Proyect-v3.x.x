<br/>
<div id="content" role="main">
    <table width="600px">
        <tr>
            <td class="c" colspan="5">{bn_players_banned_list}</td>
        </tr>
        <tr>
            <th>{bn_player}</th>
            <th>{bn_reason}</th>
            <th>{bn_from}</th>
            <th>{bn_until}</th>
            <th>{bn_by}</th>
        </tr>
        {banned_players}
        <tr>
            <th scope="row" class="b">{player}</th>
            <th role="cell" class="b">{reason}</th>
            <th role="cell" class="b">{since}</th>
            <th role="cell" class="b">{until}</th>
            <th role="cell" class="b">{by}</th>
        </tr>
        {/banned_players}
        <tr>
            <th role="cell" class="5" colspan="5">{banned_msg}</th>
        </tr>
    </table>
</div>