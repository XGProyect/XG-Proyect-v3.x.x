<div id="merchant">
    <div style="margin:0px auto;">
        <form id="TraderForm" action="javascript:void(0);" onsubmit="trySubmit();" role="form">
            <table id="merchanttable" cellpadding="0" cellspacing="0" width="582px">
                <thead>
                    <td class="c" colspan="6">
                        There is a trader here buying crystal.
                    </td>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="freeStorage">Free storage capacity</td>
                        <td class="tradingRate">Exchange rate</td>
                    </tr>
                    <tr class="alt">
                        <td class="cresIcon noCenter">
                            <img border="0" src="{dpath}resources/metal.gif" width="42" height="22" alt=""/>
                        </td>
                        <td class="noCenter">
                            {metal}
                        </td>
                        <td>
                            <input type="text" pattern="[0-9,.]*" tabindex="1" class="textinput" size="11" name="1_value" id="1_value" value="0" onchange="checkValue(1)" onkeyup="checkValue(1)">
                        </td>
                        <td>
                            <a href="javascript:void(0);" onclick="setMaxValue(1); return false;" class="tooltip js_hideTipOnMobile max" title="Exchange maximum amount">
                                max
                            </a>
                        </td>
                        <td>
                            <span id="1_storage">0</span>
                        </td>
                        <td class="rate tooltipHTML tooltipRight" title="">
                            <span class="middlemark">2.46</span>
                        </td>
                    </tr>
                    <tr class=" toSell">
                        <td class="resIcon noCenter">
                            <img border="0" src="{dpath}resources/crystal.gif" width="42" height="22" alt=""/>
                        </td>
                        <td class="noCenter">
                            {crystal}
                        </td>
                        <td id="toSell">
                            <span id="2_value_label">0</span>
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td>
                            Being sold
                        </td>
                        <td class="rate">
                            <span class="tooltipHTML tooltipRight" title="">2</span>
                        </td>
                        <input type="hidden" name="2_value" id="2_value" value="0">
                    </tr>
                    <tr class="alt">
                        <td class="resIcon noCenter">
                            <img border="0" src="{dpath}resources/deuterium.gif" width="42" height="22" alt=""/>
                        </td>
                        <td class="noCenter">
                            {deuterium}
                        </td>
                        <td>
                            <input type="text" pattern="[0-9,.]*" tabindex="3" class="textinput" size="11" name="3_value" id="3_value" value="0" onchange="checkValue(3)" onkeyup="checkValue(3)">
                        </td>
                        <td>
                            <a href="javascript:void(0);" onclick="setMaxValue(3); return false;" class="tooltip js_hideTipOnMobile max" title="Exchange maximum amount">
                                max
                            </a>
                        </td>
                        <td>
                            <span id="3_storage">10.000</span>
                        </td>
                        <td class="rate tooltipHTML tooltipRight" title="">
                            <span class="undermark">1</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" style="padding:10px">
                            <span>A trader only delivers as much resources as there is free storage capacity.</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" rowspan="2" style="text-align:center">
                            <input type="button" tabindex="3" name="tradebutton" class="btn_blue" value="Trade resources!" onclick="trySubmit(); ">
                        </td>
                        <td colspan="3" rowspan="2" class="newRate" style="text-align:center">
                            <a href="javascript:void(0);" tabindex="4" name="tradebuttonRate" class="buttonTraderNewRate" data-offer-id="2" data-ask-overwrite="false">
                                New exchange rate
                            </a>
                            <br>
                            Costs: 3.500 Dark Matter
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div><!-- wrapper -->
</div>
