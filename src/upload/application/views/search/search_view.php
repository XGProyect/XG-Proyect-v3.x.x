<br />
<div id="content">
    <form action="" method="POST">
        <table width="519px">
            <tr>
                <td class="c">{sh_search_universe}</td>
            </tr>
            <tr>
                <th>
                    {sh_put_in_leyend}
                    <br><br>
                    <select name="search_type">
                        <option value="option_player_name"{option_player_name}>{sh_option_player_name}</option>
                        <option value="option_alliance_tag"{option_alliance_tag}>{sh_option_alliance_tag}</option>
                        <option value="option_planet_names"{option_planet_names}>{sh_option_planet_names}</option>
                    </select>
                    &nbsp;&nbsp;
                    <input type="text" name="search_text" value="{searchtext}">
                    &nbsp;&nbsp;

                    <input type="submit" value="{sh_search_button}">
                </th>
            </tr>
        </table>
    </form>
    {error_block}
    {search_results}
    {/search_results}