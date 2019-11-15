<br />
<div id="content">
    <table width=600>
        <tr>
            <td colspan="3" class="c">{dark_matter}</td>
        </tr>
        <tr>
            <td class="l">
                <img border='0' src="{dpath}premium/DMaterie.jpg" align='top' width='120' height='120'>
            </td>
            <td class="l">
                <strong>{dark_matter}</strong><br>
                {of_darkmatter_description}
                <div style="margin:4px 4px;">
                    <table>
                        <tr>
                            <td>
                                <img src="{dpath}premium/dm_klein_1.jpg" width="32" height="32" style="vertical-align:middle;"></td>
                            <td style='background-color:transparent;'>
                                <strong style="color:skyblue; vertical-align:middle;">{of_darkmatter_description_short}</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="l" style="width:90px;text-align:center; vertical-align:middle;">
                <a id='darkmatter2' href='{premium_pay_url}' style='cursor:pointer; text-align:center;width:100px;height:60px;'><br>
                    <div id='darkmatter2'><b>{of_get_darkmatter}</b></div>
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="c">{of_title}</td>
        </tr>
        {officier_list}
        <tr>
            <td class="l" rowspan="2">
                <img border='0' src="{dpath}premium/{img_big}.jpg" align='top' width='120' height='120'>
            </td>
            <td class="l" rowspan="2">
                <b>{name}</b>(<b>{status}</b>)<br>
                {description}
                <div style="margin:4px 4px;">
                    <table>
                        <tr>
                            <td>
                                <img src="{dpath}premium/{img_small}.gif" width="32" height="32" style="vertical-align:middle;" alt="{name}">
                            </td>
                            <td style='background-color:transparent;'>
                                <strong style="color:skyblue; vertical-align:middle;">{benefits}</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="l" style="width:90px;text-align:center; vertical-align:middle;">
                <a href='{link_month}'>
                    <b>{of_months}<br><font color=lime>{of_only} {month_price}</font>
                    <br>{dark_matter}</b>
                </a>
            </td>
        </tr>
        <tr>
            <td class="l" style="width:90px;text-align:center; vertical-align:middle;">
                <a href='{link_week}' >
                    <b>{of_week}<br><font color=lime>{of_only} {week_price}</font>
                    <br>{dark_matter}</b>
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="c" style='height:4px;'></td>
        </tr>
        {/officier_list}
    </table>
</div>
