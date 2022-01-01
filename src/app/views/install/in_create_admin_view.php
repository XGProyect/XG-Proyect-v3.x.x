

<div class="span9">
    {alert}
    <div class="hero-unit">
        <h1>{ins_install_title}</h1>
        <br />
        <form action="" method="post">
            <input type="hidden" name="page" value="{step}" />
            <div align="left">
                <h2>{ins_admin_create_title}</h2>
            </div>
            <div align="left">
                <label>{ins_admin_create_user}</label>
                <input name="adm_user" size="20" maxlength="20" type="text">
                <label>{ins_admin_create_pass}</label>
                <input name="adm_pass" size="20" maxlength="20" type="text" value="" autocomplete="off">
                <label>{ins_admin_create_email}</label>
                <input name="adm_email" size="20" maxlength="40" type="text">
                <div align="center">
                    <input type="button" class="btn btn-primary" name="next" onclick="submit();" value="{ins_admin_create_create}">
                </div>
            </div>
        </form>
    </div>
</div><!--/span-->
