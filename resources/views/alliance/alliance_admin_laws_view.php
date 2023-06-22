<table width="100%">
    <form action="game.php?page=alliance&mode=admin&edit=rights" method="POST">
        <tr>
            <td colspan="2"><span style="color: #6f9fc8;">{al_rank_name_title}</span></td>
            <td style="color: #6f9fc8;border-left: 1px solid #222A34;" colspan="2">{al_rank_applications_title}</td>
            <td style="color: #6f9fc8;border-left: 1px solid #222A34;" colspan="4">{al_rank_member_title}</td>
            <td style="color: #6f9fc8;border-left: 1px solid #222A34;" colspan="3">{al_rank_alliance_title}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td style="border-left: 1px solid #222A34;"><img src="{dpath}img/r3.png"></td>
            <td style="border-left: 1px solid #222A34;"><img src="{dpath}img/r5.png"></td>
            <td style="border-left: 1px solid #222A34;"><img src="{dpath}img/r4.png"></td>
            <td style="border-left: 1px solid #222A34;"><img src="{dpath}img/r2.png"></td>
            <td style="border-left: 1px solid #222A34;"><img src="{dpath}img/r7.png"></td>
            <td style="border-left: 1px solid #222A34;"><img src="{dpath}img/r8.png"></td>
            <td style="border-left: 1px solid #222A34;"><img src="{dpath}img/r1.png"></td>
            <td style="border-left: 1px solid #222A34;"><img src="{dpath}img/r6.png"></td>
            <td style="border-left: 1px solid #222A34;"><img src="{dpath}img/r9.png"></td>
        </tr>
        {list_of_ranks}
        <tr>
            <td>{rank_delete}</td>
            <td>{rank_name}</td>
            <input type="hidden" name="id[]" value="{rank_id}">
            <td style="border-left: 1px solid #222A34;"><input type="checkbox" name="u{rank_id}r3" {checked_r3}{edit_check}></td>
            <td style="border-left: 1px solid #222A34;"><input type="checkbox" name="u{rank_id}r5" {checked_r5}{edit_check}></td>
            <td style="border-left: 1px solid #222A34;"><input type="checkbox" name="u{rank_id}r4" {checked_r4}{edit_check}></td>
            <td style="border-left: 1px solid #222A34;"><input type="checkbox" name="u{rank_id}r2" {checked_r2}{edit_check}></td>
            <td style="border-left: 1px solid #222A34;"><input type="checkbox" name="u{rank_id}r7" {checked_r7}{edit_check}></td>
            <td style="border-left: 1px solid #222A34;"><input type="checkbox" name="u{rank_id}r8" {checked_r8}{edit_check}></td>
            <td style="border-left: 1px solid #222A34;">{r1}</td>
            <td style="border-left: 1px solid #222A34;"><input type="checkbox" name="u{rank_id}r6" {checked_r6}{edit_check}></td>
            <td style="border-left: 1px solid #222A34;"><input type="checkbox" name="u{rank_id}r9" {checked_r9}{edit_check}></td>
        </tr>
        {/list_of_ranks}
        <tr>
            <td colspan="11">
                <span style="float:right;"><input type="submit" value="{al_save}"></span>
            </td>
        </tr>
        <tr>
            <td colspan="11" style="text-align:left;">
                {al_rank_warning}
            </td>
        </tr>
    </form>
</table>

<form action="game.php?page=alliance&mode=admin&edit=rights" method="POST" style="border-top: 1px solid #222a34;">
    <!--  -->
    <table width="100%" style="text-align: left!important;">
        <tbody>
            <tr>
                <td colspan="6">
                    <h3>{al_legend}</h3>
                </td>
            </tr>
            <tr>
                <td width="25px">
                    <img src="{dpath}img/r3.png">
                </td>
                <td>{al_legend_see_requests}</td>
                <td width="25px">
                    <img src="{dpath}img/r5.png">
                </td>
                <td>{al_legend_check_requests}</td>
                <td width="25px">
                    <img src="{dpath}img/r4.png">
                </td>
                <td>{al_legend_see_users_list}</td>
            </tr>
            <tr>
                <td width="25px">
                    <img src="{dpath}img/r2.png">
                </td>
                <td>{al_legend_kick_users}</td>
                <td width="25px">
                    <img src="{dpath}img/r7.png">
                </td>
                <td>{al_legend_see_connected_users}</td>
                <td width="25px">
                    <img src="{dpath}img/r8.png">
                </td>
                <td>{al_legend_create_circular}</td>
            </tr>
            <tr>
                <td width="25px">
                    <img src="{dpath}img/r1.png">
                </td>
                <td>{al_legend_disolve_alliance}</td>
                <td width="25px">
                    <img src="{dpath}img/r6.png">
                </td>
                <td>{al_legend_admin_alliance}</td>
                <td width="25px">
                    <img src="{dpath}img/r9.png">
                </td>
                <td>{al_legend_right_hand}</td>
                <td><a title="{al_legend_right_hand_detail}">[?]</a></td>
            </tr>
            <!-- Alliance Class -->
        </tbody>
    </table>
</form>

<form action="game.php?page=alliance&mode=admin&edit=rights" method="POST" style="border-top: 1px solid #222a34;padding-top:5px">
    <table width="100%">
        <tr>
            <td colspan="3">
                <span style="float: right;">
                <input type="text" name="newrangname" size="20" maxlength="30"> <input type="submit" name="create" value="{al_create}">
                </span>
            </td>
        </tr>
    </table>
</form>