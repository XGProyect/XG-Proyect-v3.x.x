<form action="" method="POST">
    <table width="519">
        <tr>
            <td class="c" colspan="4">{nt_my_notes}</td>
        </tr>
        <tr>
            <th colspan="4">
                <a href="game.php?page=notes&a=1">{nt_new_note}</a>
            </th>
        </tr>
        <tr>
            <td class="c">&nbsp;</td>
            <td class="c">{nt_col_subject}</td>
            <td class="c">{nt_col_date}</td>
        </tr>
        {list_of_notes}
        <tr>
            <th width="20">
                <input name="delnote[{note_id}]" value="y" type="checkbox">
            </th>
            <th>
                <a href="game.php?page=notes&a=2&amp;n={note_id}">
                    <font color="{note_color}">{note_title}</font>
                </a>
            </th>
            <th width="150">{note_time}</th>
        </tr>
        {/list_of_notes}
        {no_notes}
        <tr>
            <td colspan="4">
                <input value="{nt_delete_market_notes}" type="submit">
            </td>
        </tr>
    </table>
</form>