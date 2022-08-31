<br />
<div id="content" role="main">
    <form action="game.php?page=alliance&mode=make" method="POST" role="form">
        <table width="519">
            <tr>
                <td class="c" colspan="2">{al_make_alliance}</td>
            </tr>
            <tr>
                <th scope="row">{al_make_ally_tag_required}</th>
                <th role="cell"><input type="text" name="atag" size="8" maxlength="8" value=""></th>
            </tr>
            <tr>
                <th scope="row">{al_make_alliance_name_required}</th>
                <th role="cell"><input type="text" name="aname" size="20" maxlength="30" value=""></th>
            </tr>
            <tr>
                <th role="cell" colspan="2"><input type="submit" value="{al_make_submit}"></th>
            </tr>
        </table>
    </form>
</div>