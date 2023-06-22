<br />
<div id="content">
    <table style="width:654px">
        <tr>
            <td class="c" colspan="4">{al_alliance}</td>
        </tr>
        <tr>
            <th style="width:25%"><a href="game.php?page=alliance">{al_alliance_overview}</a></th>
            <th style="width:25%"><a href="game.php?page=alliance&mode=admin&edit=ally">{al_alliance_management}</a></th>
            <th style="width:25%"><a href="game.php?page=alliance&mode=circular">{al_alliace_communication}</a></th>
            <th style="width:25%"><a href="game.php?page=alliance&mode=apply">{al_alliance_application}</a></th>
        </tr>
        {image}
        <tr>
            <th colspan="4" style="text-align: center;">{al_your_ally}</th>
        </tr>
        <tr>
            <th colspan="4">
                <table style="width:100%;">
                    <tbody>
                        {details}
                        <tr>
                            <th style="text-align:left;width:50%">{detail_title}</th>
                            <th style="text-align:left;width:50%">{detail_content}</th>
                        </tr>
                        {/details}
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">
                                <span style="float: right;">
                                    <input type="button" onclick="location.href='{home_page}';" value="{al_alliance_web_open}" />
                                </span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">{al_user_list}</th>
        </tr>
        <tr>
            <th colspan="4">
                {members}
            </th>
        </tr>
        <tr>
            <th colspan="4">{al_inside_section}</th>
        </tr>
        <tr>
            <th colspan="4" height="100%">{text}</th>
        </tr>
        <tr>
            <th colspan="4">{al_outside_text}</th>
        </tr>
        {description}
    </table>
</div>
