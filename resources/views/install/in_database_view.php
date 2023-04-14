

<div class="span9">
    {alert}
    <div class="hero-unit">
        <h1>{ins_install_title}</h1>
        <br />
        <form action="" method="post">
            <input type="hidden" name="page" value="step1" />
            <div align="left">
                <h2>{ins_connection_data_title}</h2>
            </div>
            <div align="left">
                <label>{ins_server_title}</label>
                <input type="text" name="host" placeholder="{ins_ex_tag} localhost" value="{v_host}"/>
                <label>{ins_user_title}</label>
                <input type="text" name="user" placeholder="{ins_ex_tag} root" value="{v_user}">
                <label>{ins_password_title}</label>
                <input type="text" name="password" value="" autocomplete="off">
                <label>{ins_db_title}</label>
                <input type="text" name="db" placeholder="{ins_ex_tag} xgproyect" value="{v_db}">
                <label>{ins_prefix_title}</label>
                <input type="text" name="prefix" placeholder="{ins_ex_tag} xgp_" value="{v_prefix}">
                <div align="center">
                    <input type="button" class="btn btn-primary" name="next" onclick="submit();" value="{ins_install_go}">
                </div>
            </div>
        </form>
    </div>
</div><!--/span-->
