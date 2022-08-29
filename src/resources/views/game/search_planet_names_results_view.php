    <table width="519px">
        <tr>
            <td role="columnheader" class="c">{sh_col_players_name}</td>
            <td role="columnheader" class="c">{sh_col_alliance}</td>
            <td role="columnheader" class="c">{sh_col_planet}</td>
            <td role="columnheader" class="c">{sh_col_position}</td>
            <td role="columnheader" class="c">{sh_col_highscore_ranking}</td>
            <td role="columnheader" class="c">{sh_col_action}</td>
        </tr>
        {results}
        <tr>
            <th scope="row">{user_name}</th>
            <th role="cell"><a href="game.php?page=alliance&mode=ainfo&allyid={alliance_id}">{alliance_name}</a></th>
            <th role="cell">{planet_name}</th>
            <th role="cell"><a href="game.php?page=galaxy&mode=3&galaxy={planet_galaxy}&system={planet_system}">{planet_position}</a></th>
            <th role="cell">{user_rank}</th>
            <th role="cell">{user_actions}</th>
        </tr>
        {/results}
    </table>
</div>
