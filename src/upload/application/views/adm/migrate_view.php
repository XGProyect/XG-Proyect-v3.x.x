

<div class="span9">
    {alert}
    <div class="hero-unit">
        <h1>{mi_title}</h1>
        <br />
        <form name="migrate_form" method="post" action="">
            <label>{mi_version_select}</label>
            <select name="version_select">
                <option value="0">{mi_option_init}</option>
                {versions_list}
            </select>
            <label>{mi_server_title}</label>
            <input type="text" name="host" placeholder="{mi_ex_tag} localhost" value="{v_host}"/>
            <label>{mi_user_title}</label>
            <input type="text" name="user" placeholder="{mi_ex_tag} root" value="{v_user}">
            <label>{mi_password_title}</label>
            <input type="text" name="password" value="" autocomplete="off">
            <label>{mi_db_title}</label>
            <input type="text" name="db" placeholder="{mi_ex_tag} xgproyect" value="{v_db}">
            <label>{mi_prefix_title}</label>
            <input type="text" name="prefix" placeholder="{mi_ex_tag} xgp_" value="{v_prefix}">
            <label>{mi_test_mode}</label>
            <input type="checkbox" name="demo_mode" checked>
            <p><em><small>{mi_test_mode_notice}</small></em></p>
            <p class="text-error bold_font">{mi_alert_title}</p>
            <ul>
                <li class="bold_font">{mi_alert_type_1_title}</li>
                <ul>
                    <li class="text-error">{mi_alert_type_1_content_1}</li>
                </ul>
                <li class="bold_font">{mi_alert_type_2_title}</li>
                <ul>
                    <li class="text-warning">{mi_alert_type_2_content_1}</li>
                </ul>
                <li class="bold_font">{mi_alert_type_3_title}</li>
                <ul>
                    <li class="text-info">{mi_alert_type_3_content_1}</li>
                    <li class="text-info">{mi_alert_type_3_content_2}</li>
                    <li class="text-info">{mi_alert_type_3_content_3}</li>
                </ul>
            </ul>
            <div align="center">
                <input type="button" class="btn btn-primary" name="next" onclick="submit();" value="{mi_go}">
            </div>
        </form>
    </div>
</div><!--/span-->