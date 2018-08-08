<form action="" method="POST">
    <table width="519">
        <tr>
            <td class="c" colspan="4">{nt_notes}</td>
        </tr>
        <tr>
            <th colspan="4">
                <a href="game.php?page=notes&a=1">{nt_create_new_note}</a>
            </th>
        </tr>
        <tr>
            <td class="c">&nbsp;</td>
            <td class="c">{nt_date_note}</td>
            <td class="c">{nt_subject_note}</td>
            <td class="c">{nt_size_note}</td>
        </tr>
        {list_of_notes}
        <tr>
            <th width="20">
                <input name="delmes{note_id}" value="y" type="checkbox">
            </th>
            <th width="150">{note_time}</th>
            <th>
                <a href="game.php?page=notes&a=2&amp;n={note_id}">
                    <font color="{note_color}">{note_title}</font>
                </a>
            </th>
            <th align="right" width="40">{note_text}</th>
        </tr>
        {/list_of_notes}
        {no_notes}
        <tr>
            <td colspan="4">
                <input value="{nt_dlte_note}" type="submit">
            </td>
        </tr>
    </table>
</form>