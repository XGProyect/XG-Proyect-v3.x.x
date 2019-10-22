<h2>{settings}</h2>
{alert_info}
<form name="save_info" method="post" action="">
    <table width="100%" class="table table-bordered table-hover table-condensed">
        <tr>
            <th width="50%">{us_user_settings_field}</th>
            <th width="50%">{us_user_settings_value}</th>
        </tr>
        <tr>
            <td colspan="2">{us_user_settings_general_title}</td>
        </tr>
        <tr>
            <td>{us_user_preference_planet_sort}</td>
            <td>
                <select name="preference_planet_sort">
                    {preference_planet_sort}
                </select>
            </td>
        </tr>
        <tr>
            <td>{us_user_preference_planet_sort_sequence}</td>
            <td>
                <select name="preference_planet_sort_sequence">
                    {preference_planet_sort_sequence}
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">{us_user_settings_galaxy_title}</td>
        </tr>
        <tr>
            <td>{us_user_preference_spy_probes}</td>
            <td><input type="text" name="preference_spy_probes" value="{preference_spy_probes}"></td>
        </tr>
        <tr>
            <td colspan="2">{us_user_settings_other_title}</td>
        </tr>
        <tr>
            <td>{us_user_preference_vacations_status}</td>
            <td><input type="checkbox" name="preference_vacations_status"{preference_vacations_status}> <span class="small_font">{preference_vacation_mode}</span></td>
        </tr>
        <tr>
            <td>{us_user_preference_delete_mode}</td>
            <td><input type="checkbox" name="preference_delete_mode"{preference_delete_mode}></td>
        </tr>
        <tr>
            <td colspan="2">
                <div align="center">
                    <input type="submit" class="btn btn-primary" name="send_data" value="{us_send_data}">
                </div>
            </td>
        </tr>
    </table>
</form>