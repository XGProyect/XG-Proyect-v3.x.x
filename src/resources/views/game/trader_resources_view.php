<table width="665px">
    <tr>
        <td class="c">{tr_resource_market}</td>
    </tr>
    <tr>
        <td>
            <form name="refill-resources" method="POST" action="" role="form">
                <table width="100%">
                    <tr>
                        <td class="c" colspan="2">{tr_merchant1_tab_title}</td>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <h2>{tr_merchant1_title}</h2>
                            <p>{tr_merchant1_explanation}</p>
                        </th>
                    </tr>
                    <tr>
                        <td class="c" colspan="2">
                            <h3>{tr_merchant1_info}</h3>
                        </td>
                    </tr>
                    {list_of_resources}
                    <tr>
                        <th>
                            <img border="0" src="{dpath}resources/{resource}.gif" width="42" height="22">
                            <br>
                            {resource_name}
                        </th>
                        <td>
                            <table width="100%">
                                <tr>
                                    <th colspan="3">
                                        {tr_storage_capacity}:
                                        {current_resource} / {max_resource}
                                    </th>
                                </tr>
                                <tr>
                                    {refill_options}
                                    <th>
                                        {label}:<br>
                                        <span style="font-size: 3em">{percentage}%</span><br>
                                        {tr_requires}:<br>
                                        {price}<br>
                                        {button}
                                    </th>
                                    {/refill_options}
                                </tr>
                            </table>
                        </td>
                    </tr>
                    {/list_of_resources}
                </table>
            </form>
            <form name="trade-resources" method="POST" action="" role="form">
                <table width="100%">
                    <tr>
                        <td class="c" colspan="2">{tr_merchant2_tab_title}</td>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <h2>{tr_merchant2_title}</h2>
                        </th>
                    </tr>
                    <tr>
                        <th style="text-align:left">
                            {tr_step1}
                        </th>
                        <th style="text-align:left">
                            {tr_step2}
                        </th>
                    </tr>
                    <tr>
                        <th width="50%">
                            <table width="100%">
                                <tr>
                                    <th>
                                        <img border="0" src="public/upload/skins/xgproyect/resources/metal.gif" width="42" height="22">
                                    </th>
                                    <th>
                                        <img border="0" src="public/upload/skins/xgproyect/resources/crystal.gif" width="42" height="22">
                                    </th>
                                    <th>
                                        <img border="0" src="public/upload/skins/xgproyect/resources/deuterium.gif" width="42" height="22">
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <a title="Sell your Metal and get Crystal or Deuterium. Costs: 3.500 Dark Matter">Metal</a>
                                        <input type="radio" name="sell" value="metal">
                                    </th>
                                    <th>
                                        <a title="Sell your Crystal and get Metal or Deuterium. Costs: 3.500 Dark Matter">Crystal</a>
                                        <input type="radio" name="sell" value="crystal">
                                    </th>
                                    <th>
                                        <a title="Sell your Deuterium and get Metal or Crystal. Costs: 3.500 Dark Matter">Deuterium</a>
                                        <input type="radio" name="sell" value="deuterium">
                                    </th>
                                </tr>
                            </table>
                        </th>
                        <th width="50%">
                            {tr_price}<br>
                            <input type="button" value="{tr_call_button}">
                        </th>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
