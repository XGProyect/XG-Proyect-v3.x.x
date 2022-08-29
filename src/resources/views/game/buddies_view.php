<br/>
<div id="content" role="main">
    <table width="520">
        <tr>
            <td class="c" colspan="5">{bu_buddy_list}</td>
        </tr>
        <tr>
            <td role="columnheader" class="c">{bu_player}</td>
            <td role="columnheader" class="c">{bu_alliance}</td>
            <td role="columnheader" class="c">{bu_coords}</td>
            <td role="columnheader" class="c">{bu_text}</td>
            <td role="columnheader" class="c">{bu_action}</td>
        </tr>
        <tr>
            <th role="cell" class="c" colspan="5">{bu_requests}</a></th>
        </tr>
        {list_of_requests_received}
        <tr>
            <th scope="row">
                <a href="game.php?page=chat&playerId={id}">{username}</a>
            </th>
            <th role="cell">
                <a href="game.php?page=alliance&mode=ainfo&allyid={ally_id}">{alliance_name}</a>
            </th>
            <th role="cell">
                <a href="game.php?page=galaxy&mode=3&galaxy={galaxy}&system={system}">{galaxy}:{system}:{planet}</a>
            </th>
            <th role="cell">
                {text}
            </th>
            <th role="cell">
                {action}
            </th>
        </tr>
        {/list_of_requests_received}
        <tr>
            <th role="cell" class="c" colspan="5">{bu_my_requests}</th>
        </tr>
        {list_of_requests_sent}
        <tr>
            <th scope="row">
                <a href="game.php?page=chat&playerId={id}">{username}</a>
            </th>
            <th role="cell">
                <a href="game.php?page=alliance&mode=ainfo&allyid={ally_id}">{alliance_name}</a>
            </th>
            <th role="cell">
                <a href="game.php?page=galaxy&mode=3&galaxy={galaxy}&system={system}">{galaxy}:{system}:{planet}</a>
            </th>
            <th role="cell">
                {text}
            </th>
            <th role="cell">
                {action}
            </th>
        </tr>
        {/list_of_requests_sent}
        <tr>
            <th role="cell" class="c" colspan="5">{bu_partners}</a></th>
        </tr>
        {list_of_buddies}
        <tr>
            <th scope="row">
                <a href="game.php?page=chat&playerId={id}">{username}</a>
            </th>
            <th role="cell">
                <a href="game.php?page=alliance&mode=ainfo&allyid={ally_id}">{alliance_name}</a>
            </th>
            <th role="cell">
                <a href="game.php?page=galaxy&mode=3&galaxy={galaxy}&system={system}">{galaxy}:{system}:{planet}</a>
            </th>
            <th role="cell">
                {text}
            </th>
            <th role="cell">
                {action}
            </th>
        </tr>
        {/list_of_buddies}
    </table>
</div>