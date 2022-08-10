<br />
<div id="content" role="main">
    <table width="520px">
        <tr>
            <td class="c" colspan="3">{al_transfer_alliance}</td>
        </tr>
        <form action="game.php?page=alliance&mode=admin&edit=transfer" method="POST" role="form">
            <tr>
                <th>{al_transfer_to}:</th>
                <th>
                    <select name="newleader">
                        {list_of_members}
                        <option value="{user_id}">{user_name} [{user_rank}]</option>
                        {/list_of_members}
                    </select>
                </th>
                <th><input type="submit" value="{al_transfer_submit}"></th>
            </tr>
        </form>        
        <tr>
            <td class="c" colspan="3"><a href="game.php?page=alliance&mode=admin&edit=ally">{al_back}</a></td>
        </tr>
    </table>
</div>