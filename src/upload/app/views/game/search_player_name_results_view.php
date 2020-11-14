    <table width="519px">
        <tr>
            <td class="c">{sh_col_players_name}</td>
            <td class="c">{sh_col_alliance}</td>
            <td class="c">{sh_col_homeworld}</td>
            <td class="c">{sh_col_position}</td>
            <td class="c">{sh_col_highscore_ranking}</td>
            <td class="c">{sh_col_action}</td>
        </tr>
        {results}
        <tr>
            <th>{user_name}</th>
            <th><a href="game.php?page=alliance&mode=ainfo&id={alliance_id}">{alliance_name}</a></th>
            <th>{planet_name}</th>
            <th><a href="game.php?page=galaxy&mode=3&galaxy={planet_galaxy}&system={planet_system}">{planet_position}</a></th>
            <th>{user_rank}</th>
            <th>{user_actions}</th>
        </tr>
        {/results}
    </table>
</div>