<br />
<div id="content" role="main">
    <table width="519px">
        <tr>
            <td class="c" colspan="2">{al_request_list}</td>
        </tr>
        {request_form}
        <tr>
            <th colspan="2">{pending_message}</th>
        </tr>
        <tr>
            <td class="c"><a href="game.php?page=alliance&mode=admin&edit=requests&show=0&sort=1">{al_candidate}</a></td>
            <td class="c"><a href="game.php?page=alliance&mode=admin&edit=requests&show=0&sort=0">{al_request_date}</a></th>
        </tr>
        {list_of_requests}
        <tr>
            <th><a href="game.php?page=alliance&mode=admin&edit=requests&show={id}&sort=0">{username}</a></th>
            <th>{time}</th>
        </tr>
        {/list_of_requests}
        {no_requests}
        <tr>
            <td class=c colspan=2><a href="game.php?page=alliance">{al_back}</a></td>
        </tr>
    </table>
</div>