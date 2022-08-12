<br />
<div id="content" role="main">
    <a href="game.php?page=alliance&mode=admin&edit=ally">{al_back}</a>

    <table width="519"><tr><td class="c" colspan="11">{al_configure_ranks}</td></tr>
        <form action="game.php?page=alliance&mode=admin&edit=rights" method="POST" role="form">
            <tr>
                <th colspan="2"><span style="color: #6f9fc8;">{al_rank_name_title}</span></th>
                <th style="color: #6f9fc8;" colspan="2">{al_rank_applications_title}</th>
                <th style="color: #6f9fc8;" colspan="4">{al_rank_member_title}</th>
                <th style="color: #6f9fc8;" colspan="3">{al_rank_alliance_title}</th>
            </tr>
            <tr>
                <th colspan="2"></th>
                <th><img src="{dpath}img/r3.png" alt=""/></th>
                <th><img src="{dpath}img/r5.png" alt=""/></th>
                <th><img src="{dpath}img/r4.png" alt=""/></th>
                <th><img src="{dpath}img/r2.png" alt=""/></th>
                <th><img src="{dpath}img/r7.png" alt=""/></th>
                <th><img src="{dpath}img/r8.png" alt=""/></th>
                <th><img src="{dpath}img/r1.png" alt=""/></th>
                <th><img src="{dpath}img/r6.png" alt=""/></th>
                <th><img src="{dpath}img/r9.png" alt=""/></th>
            </tr>
            {list_of_ranks}
            <tr>
                <th>{rank_delete}</th>
                <th>{rank_name}</th>
                <input type="hidden" name="id[]" value="{rank_id}">
                <th><input type="checkbox" name="u{rank_id}r3" {checked_r3}{edit_check}></th>
                <th><input type="checkbox" name="u{rank_id}r5" {checked_r5}{edit_check}></th>
                <th><input type="checkbox" name="u{rank_id}r4" {checked_r4}{edit_check}></th>
                <th><input type="checkbox" name="u{rank_id}r2" {checked_r2}{edit_check}></th>
                <th><input type="checkbox" name="u{rank_id}r7" {checked_r7}{edit_check}></th>
                <th><input type="checkbox" name="u{rank_id}r8" {checked_r8}{edit_check}></th>
                <th>{r1}</th>
                <th><input type="checkbox" name="u{rank_id}r6" {checked_r6}{edit_check}></th>
                <th><input type="checkbox" name="u{rank_id}r9" {checked_r9}{edit_check}></th>
            </tr>
            {/list_of_ranks}
            <tr>
                <th colspan="11"><span style="float:rigth!important;"><input type="submit" value="{al_save}"></span></th>
            </tr>
            <tr>
                <th colspan="11" style="text-align:left;">
                    {al_rank_warning}
                </th>
            </tr>
        </form>
    </table>
    <br>
    <form action="game.php?page=alliance&mode=admin&edit=rights" method="POST" role="form">
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
    <form action="game.php?page=alliance&mode=admin&edit=rights" method="POST" role="form">
        <table width="519">
            <tr>
                <td class=c colspan="2">{al_legend}</td>
            </tr>
            <tr>
                <th><img src="{dpath}img/r1.png" alt=""/></th>
                <th>{al_legend_disolve_alliance}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r2.png" alt=""/></th>
                <th>{al_legend_kick_users}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r3.png" alt=""/></th>
                <th>{al_legend_see_requests}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r4.png" alt=""/></th>
                <th>{al_legend_see_users_list}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r5.png" alt=""/></th>
                <th>{al_legend_check_requests}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r6.png" alt=""/></th>
                <th>{al_legend_admin_alliance}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r7.png" alt=""/></th>
                <th>{al_legend_see_connected_users}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r8.png" alt=""/></th>
                <th>{al_legend_create_circular}</th>
            </tr>
            <tr>
                <th><img src="{dpath}img/r9.png" alt=""/></th>
                <th><a title="{al_legend_right_hand_detail}">{al_legend_right_hand}</a></th>
            </tr>
        </table>
    </form>
</div>
