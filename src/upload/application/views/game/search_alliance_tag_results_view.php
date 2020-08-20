    <table width="519px">
        <tr>
            <td class="c">{sh_col_tag}</td>
            <td class="c"></td>
            <td class="c">{sh_col_member}</td>
            <td class="c">{sh_col_points}</td>
            <td class="c">{sh_col_action}</td>
        </tr>
        {results}
        <tr>
            <th><a href="game.php?page=alliance&mode=ainfo&allyid={alliance_id}">{alliance_tag}</a></th>
            <th><a href="game.php?page=alliance&mode=ainfo&allyid={alliance_id}">{alliance_name}</a></th>
            <th>{alliance_members}</th>
            <th><a href="game.php?page=statistics&range=1">{alliance_points}</a></th>
            <th>{alliance_actions}</th>
        </tr>
        {/results}
    </table>
</div>
