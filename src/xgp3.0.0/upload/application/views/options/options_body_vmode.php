<br />
<div id="content">
    <form action="game.php?page=options&mode=exit" method="post">
        <table width="519">
            <tr>
                <td class="c" colspan="2">{op_vacation_mode_active_message} {vacation_until}</td>
            </tr>
            <tr>
                <th>{op_end_vacation_mode}</th>
                <th><input type="checkbox" name="exit_modus" {opt_modev_exit}/></th>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" value="{op_save_changes}" /></th>
            </tr>
        </table>
    </form>
</div>
