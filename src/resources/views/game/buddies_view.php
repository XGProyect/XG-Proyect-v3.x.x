<br/>
<div id="content" role="main">
    <table width="520">
        <tr>
            <td class="c" colspan="5">{bu_buddy_list}</td>
        </tr>
        <tr>
            <td class="c">{bu_player}</td>
            <td class="c">{bu_alliance}</td>
            <td class="c">{bu_coords}</td>
            <td class="c">{bu_text}</td>
            <td class="c">{bu_action}</td>
        </tr>
        <tr>
            <th class="c" colspan="5">{bu_requests}</a></th>
        </tr>
        {list_of_requests_received}
        <tr>
            <th>
                <a href="game.php?page=chat&playerId={id}">{username}</a>
            </th>
            <th>
                <a href="game.php?page=alliance&mode=ainfo&allyid={ally_id}">{alliance_name}</a>
            </th>
            <th>
                <a href="game.php?page=galaxy&mode=3&galaxy={galaxy}&system={system}">{galaxy}:{system}:{planet}</a>
            </th>
            <th>
                {text}
            </th>
            <th>
                {action}
            </th>
        </tr>
        {/list_of_requests_received}
        <tr>
            <th class="c" colspan="5">{bu_my_requests}</th>
        </tr>
        {list_of_requests_sent}
        <tr>
            <th>
                <a href="game.php?page=chat&playerId={id}">{username}</a>
            </th>
            <th>
                <a href="game.php?page=alliance&mode=ainfo&allyid={ally_id}">{alliance_name}</a>
            </th>
            <th>
                <a href="game.php?page=galaxy&mode=3&galaxy={galaxy}&system={system}">{galaxy}:{system}:{planet}</a>
            </th>
            <th>
                {text}
            </th>
            <th>
                {action}
            </th>
        </tr>
        {/list_of_requests_sent}
        <tr>
            <th class="c" colspan="5">{bu_partners}</a></th>
        </tr>
        {list_of_buddies}
        <tr>
            <th>
                <a href="game.php?page=chat&playerId={id}">{username}</a>
            </th>
            <th>
                <a href="game.php?page=alliance&mode=ainfo&allyid={ally_id}">{alliance_name}</a>
            </th>
            <th>
                <a href="game.php?page=galaxy&mode=3&galaxy={galaxy}&system={system}">{galaxy}:{system}:{planet}</a>
            </th>
            <th>
                {text}
            </th>
            <th>
                {action}
            </th>
        </tr>
        {/list_of_buddies}
    </table>
</div>