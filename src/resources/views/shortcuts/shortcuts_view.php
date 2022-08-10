<div id="content" role="main">
    <table border="0" cellpadding="0" cellspacing="1" width="519">
        <tr height="20">
            <td class="c" colspan="2">
                {fl_shortcuts} (<a href="game.php?page=shortcuts&mode=add">{fl_shortcut_add}</a>)
            </td>
        </tr>
        {shortcuts}
            {row_start}
            <th>
                <a href="game.php?page=shortcuts&mode=edit&a={shortcut_id}">
                    {shortcut_name} {shortcut_galaxy}:{shortcut_system}:{shortcut_planet} {shortcut_type}
                </a>
            </th>
            {row_end}
        {/shortcuts}
        {no_shortcuts}
        <tr>
            <td class="c" colspan="2">
                <a href="game.php?page=fleet1">{fl_back}</a>
            </td>
        </tr>
    </table>
</div>