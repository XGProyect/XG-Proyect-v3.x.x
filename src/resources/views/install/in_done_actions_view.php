

<div class="span9">
    {alert}
    <div class="hero-unit">
        <h1>{ins_install_title}</h1>
        <br />
        <form action="" method="post">
            <input type="hidden" name="page" value="{step}" />
            <div align="center">
                <h2>
                    {done_config}
                    {done_connected}
                    {done_insert}
                </h2>
                <input type="button" class="btn btn-primary" name="next" onclick="submit();" value="{ins_continue}">
            </div>
        </form>
    </div>
</div><!--/span-->
