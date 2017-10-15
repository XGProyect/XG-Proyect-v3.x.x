

<div class="span9">
    {alert}
    <div class="hero-unit">
        <h1>{er_error_list}</h1>
        <br />
        <div class="center">
            <a href="admin.php?page=errors&deleteall=yes">
                <button class="btn btn-primary">{er_dlte_all}</button>
            </a>
        </div>
        <br />
        <table width="100%" class="table table-bordered table-hover table-condensed table-striped">
            <thead>
                <tr>
                    <th>{er_user_id}</th>
                    <th>{er_type}</th>
                    <th>{er_code}</th>
                    <th>{er_data}</th>
                    <th>{er_track}</th>
                </tr>
            </thead>
            <tbody>
                {errors_list}
                <tr>
                    <td>{user_ip}</td>
                    <td>{error_type}</td>
                    <td>{error_code}</td>
                    <td>{error_datetime}</td>
                    <td>{error_trace}</td>
                </tr>
                <tr>
                    <td colspan="5" style="border:2px solid red;height: 1px" height="1px">{error_message}</td>
                </tr>
                {/errors_list}
            </tbody>
            <tr>
                <th colspan="5">{errors_list_resume}</th>
            </tr>
        </table>
        <div class="center">
            <a href="admin.php?page=errors&deleteall=yes">
                <button class="btn btn-primary">{er_dlte_all}</button>
            </a>
        </div>
    </div>
</div><!--/span-->