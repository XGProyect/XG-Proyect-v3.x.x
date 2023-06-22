<br />
<div id="content">
    <table style="width:654px">
        <tr>
            <td class="c" colspan="2">{al_alliance}</td>
        </tr>
        <tr>
            <th style="width:25%"><a href="#">{al_alliance_make}</a></th>
            <th style="width:25%"><a href="game.php?page=search">{al_alliance_search}</a></th>
        </tr>
        <tr>
            <th colspan="2">
                <form action="game.php?page=alliance&mode=make" method="POST">
                    <table style="width:654px">
                        <tr>
                            <th style="text-align: left;">{al_make_ally_tag_required}</th>
                            <th style="text-align: left;"><input type="text" name="atag" size="8" maxlength="8" value=""></th>
                        </tr>
                        <tr>
                            <th style="text-align: left;">{al_make_alliance_name_required}</th>
                            <th style="text-align: left;"><input type="text" name="aname" size="20" maxlength="30" value=""></th>
                        </tr>
                        <tr>
                            <th colspan="2"><input type="submit" value="{al_make_submit}"></th>
                        </tr>
                    </table>
                </form>
            </th>
        </tr>
    </table>
</div>
