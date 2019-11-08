<br>
<div id="content">
    {status_message}
    <table width="519px" style="border: 2px solid {error_color}; text-align: center; background: transparent;">
        <tr style="background: transparent;">
            <td style="background: transparent;">
                <span style="color: {error_color}; font-weight: bold">{error_text}</span>
            </td>
        </tr>
    </table>
    <br />
    {/status_message}
    <table width="665px">
        <tr>
            <th width="50%">
                <a href="game.php?page=traderOverview&mode=traderResources" title="{tr_resource_market_title}">{tr_resource_market}<a>
            </th>
            <!--<th width="50%">
                <a href="game.php?page=traderOverview&mode=traderAuctioneer" title="{tr_auctioneer_title}">{tr_auctioneer}<a>
            </th>-->
        </tr>
        <!--<tr>
            <th width="50%">
                <a href="game.php?page=traderOverview&mode=traderScrap" title="{tr_scrap_merchant_title}">{tr_scrap_merchant}<a>
            </th>
            <th width="50%">
                <a href="game.php?page=traderOverview&mode=traderImportExport" title="{tr_import_export_title}">{tr_import_export}<a>
            </th>
        </tr>-->
    </table>

    {current_mode}
</div>
