<br />
<div id="content">
    <a href="game.php?page=alliance&mode=admin&edit=ally">{al_back}</a>
    <table width="519"><tr><td class="c" colspan="11">{al_configura_ranks}</td></tr>
        <form action="game.php?page=alliance&mode=admin&edit=rights" method="POST">
            <tr>
                <th></th>
                <th>{al_rank_name}</th>
                <th><img src="{dpath}img/r1.png"></th>
                <th><img src="{dpath}img/r2.png"></th>
                <th><img src="{dpath}img/r3.png"></th>
                <th><img src="{dpath}img/r4.png"></th>
                <th><img src="{dpath}img/r5.png"></th>
                <th><img src="{dpath}img/r6.png"></th>
                <th><img src="{dpath}img/r7.png"></th>
                <th><img src="{dpath}img/r8.png"></th>
                <th><img src="{dpath}img/r9.png"></th>
            </tr>
            {list_of_ranks}
            <tr>
                <th><a href="game.php?page=alliance&mode=admin&edit=rights&d={rank_id}"><img src="{dpath}alliance/abort.gif" border="0"></a></th>
                <th>{rank_name}</th>
                <input type="hidden" name="id[]" value="{rank_id}">
                <th>{r1}</th>
                <th><input type="checkbox" name="u{rank_id}r2" {checked_r2}></th>
                <th><input type="checkbox" name="u{rank_id}r3" {checked_r3}></th>
                <th><input type="checkbox" name="u{rank_id}r4" {checked_r4}></th>
                <th><input type="checkbox" name="u{rank_id}r5" {checked_r5}></th>
                <th><input type="checkbox" name="u{rank_id}r6" {checked_r6}></th>
                <th><input type="checkbox" name="u{rank_id}r7" {checked_r7}></th>
                <th><input type="checkbox" name="u{rank_id}r8" {checked_r8}></th>
                <th><input type="checkbox" name="u{rank_id}r9" {checked_r9}></th>
            </tr>
            {/list_of_ranks}
            <tr>
                <th colspan="11"><input type="submit" value="{al_save}"></th>
            </tr>
        </form>
    </table>
    <br>
    <form action="game.php?page=alliance&mode=admin&edit=rights" method="POST">
        <table width="519">
            <tr>
                <td class="c" colspan="2">{al_create_new_rank}</td>
            </tr>
            <tr>
                <th>{al_rank_name}</th>
                <th><input type="text" name="newrangname" size="20" maxlength="30"></th>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" name="create" value="{al_create}"></th>
            </tr>
        </table>
    </form>
    <form action="game.php?page=alliance&mode=admin&edit=rights" method="POST">
        <table width="519">
            <tr>
                <td class=c colspan="2">{al_legend}</td>
            </tr>
            <tr>
                <th><img src="{dpath}img/r1.png"></th>
                <th>{al_legend_disolve_alliance}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r2.png"></th>
                <th>{al_legend_kick_users}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r3.png"></th>
                <th>{al_legend_see_requests}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r4.png"></th>
                <th>{al_legend_see_users_list}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r5.png"></th>
                <th>{al_legend_check_requests}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r6.png"></th>
                <th>{al_legend_admin_alliance}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r7.png"></th>
                <th>{al_legend_see_connected_users}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r8.png"></th>
                <th>{al_legend_create_circular}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r9.png"></th>
                <th>{al_legend_right_hand}</th>
            </tr>
        </table>
    </form>
</div>