<script language="JavaScript" src="{js_path}flotten-min.js"></script>
<script language="JavaScript" src="{js_path}ocnt-min.js"></script>
<br />
<div id="content" role="main">
    <table width="519" border="0" cellpadding="0" cellspacing="1">
        <tr height="20">
            <td colspan="9" class="c">
                <table border="0" width="100%">
                    <tr>
                        <td style="background-color: transparent;" align="center">{fl_fleets} {fleets} / {max_fleets} &nbsp; &nbsp; {fl_expeditions} {expeditions} / {max_expeditions}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr height="20">
            <th>{fl_number}</th>
            <th>{fl_mission}</th>
            <th>{fl_ammount}</th>
            <th>{fl_beginning}</th>
            <th>{fl_departure}</th>
            <th>{fl_destiny}</th>
            <th>{fl_objective}</th>
            <th>{fl_arrival}</th>
            <th>{fl_order}</th>
        </tr>
        {list_of_movements}
        <tr height="20px">
            <th scope="row">{num}</th>
            <th role="cell">
                <a>{fleet_mission}</a>
                <a title="{tooltip}">{title}</a>
            </th>
            <th role="cell">
                <a title="{fleet}">{fleet_amount}</a>
            </th>
            <th role="cell">
                {fleet_start}
            </th>
            <th role="cell">
                {fleet_start_time}
            </th>
            <th role="cell">
                {fleet_end}
            </th>
            <th role="cell">
                {fleet_end_time}
            </th>
            <th role="cell">
                {fleet_arrival}
            </th>
            <th role="cell" style="vertical-align: middle">
                {fleet_actions}
            </th>
        </tr>
        {/list_of_movements}
    </table>
</div>