<form action="game.php?page=trader" method="post">
<input type="hidden" name="action" value="2">
<br />
<div id="content">
    <table width="600">
    <tr>
        <td class="c" colspan="10">{tr_call_trader}<td>
    </tr><tr>
        <th colspan="10">{tr_call_trader_who_buys}
          <select name="choix">
            <option value="metal">{Metal}</option>
            <option value="cristal">{Crystal}</option>
            <option value="deut">{Deuterium}</option>
        </select>
        <br>
        {tr_exchange_quota}<br /><br />
        <input type="submit" value="{tr_call_trader_submit}" /></th>
    </tr>
    </table>
    </form>
</div>