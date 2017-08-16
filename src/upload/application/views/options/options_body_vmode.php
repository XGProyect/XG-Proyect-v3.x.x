<br />
<div id="content">
    <form action="game.php?page=options&mode=exit" method="post">
        <table width="519">
            <tr>
                <td class="c" colspan="2">{op_vacation_mode_title}</td>
            </tr>
            <tr>
                {op_finish_vac_mode}
            </tr>
            <tr>
                {op_vac_mode_msg}
            </tr>
            <tr>
                <th><a title="{op_dlte_account_descrip}">{op_dlte_account}</a></th>
                <th>
                    <input name="db_deaktjava"{db_deaktjava} type="checkbox" /> {db_deaktjava_until}
                    {verify}
                </th>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" value="{op_save_changes}" /></th>
            </tr>
        </table>
    </form>
</div>
