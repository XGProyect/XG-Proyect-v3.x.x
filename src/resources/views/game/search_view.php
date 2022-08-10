<br />
<div id="content" role="main">
    <form action="" method="POST" role="form">
        <table width="519px">
            <tr>
                <td class="c">{sh_search_universe}</td>
            </tr>
            <tr>
                <th>
                    {sh_put_in_leyend}
                    <br><br>
                    <select name="search_type">
                        <option value="player_name"{player_name}>{sh_option_player_name}</option>
                        <option value="alliance_tag"{alliance_tag}>{sh_option_alliance_tag}</option>
                        <option value="planet_names"{planet_names}>{sh_option_planet_names}</option>
                    </select>
                    &nbsp;&nbsp;
                    <input type="text" name="search_text" value="{search_text}">
                    &nbsp;&nbsp;

                    <input type="submit" value="{sh_search_button}">
                </th>
            </tr>
        </table>
    </form>
    {error_block}
    {search_results}
