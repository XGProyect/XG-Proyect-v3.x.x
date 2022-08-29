    <table width="519px">
        <tr>
            <td role="columnheader" class="c">{sh_col_tag}</td>
            <td role="columnheader" class="c"></td>
            <td role="columnheader" class="c">{sh_col_member}</td>
            <td role="columnheader" class="c">{sh_col_points}</td>
            <td role="columnheader" class="c">{sh_col_action}</td>
        </tr>
        {results}
        <tr>
            <th scope="row"><a href="game.php?page=alliance&mode=ainfo&allyid={alliance_id}">{alliance_tag}</a></th>
            <th role="cell"><a href="game.php?page=alliance&mode=ainfo&allyid={alliance_id}">{alliance_name}</a></th>
            <th role="cell">{alliance_members}</th>
            <th role="cell"><a href="game.php?page=statistics&range=1">{alliance_points}</a></th>
            <th role="cell">{alliance_actions}</th>
        </tr>
        {/results}
    </table>
</div>
