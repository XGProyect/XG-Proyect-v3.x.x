<br />
<div id="content">
    <table width="520">
        <tr>
            <td class="c" colspan="8">{al_transfer_alliance}</td>
        </tr>
        {list_of_members}
        <form action="game.php?page=alliance&mode=admin&edit=transfer&id={id}" method="POST">
            <tr>
                <th colspan="3">{al_transfer_to}:</th>
                <th><select name="newleader">{righthand}</select></th>
                <th colspan="3"><input type="submit" value="{al_transfer_submit}"></th>
            </tr>
        </form>        
        {/list_of_members}
        <tr>
            <td class="c" colspan="8"><a href="game.php?page=alliance&mode=admin&edit=ally">{al_back}</a></td>
        </tr>
    </table>
</div>