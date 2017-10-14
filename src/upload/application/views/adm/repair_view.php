

<div class="span9">
    {alert}
    <div class="hero-unit">
        <h1>{db_opt_db}</h1>
        <br />
        <form action="" method="post">
            <table widtd="100%" class="table table-bordered table-hover table-condensed">
                <tr>
                    {head}
                </tr>
                {tables}
                {results}
                <tr>
                    <td colspan="5">
                        <div align="center" style="display: {display}">
                            <input type="radio" name="Optimize" value="yes" checked="on"> {db_yes}
                            <input type="radio" name="Optimize" value="no"> {db_no} <strong>{db_optimize}</strong><br>
                            <input type="radio" name="Repair" value="yes" checked="on"> {db_yes}
                            <input type="radio" name="Repair" value="no"> {db_no} <strong>{db_repair}</strong><br><br>
                            <input type="submit" class="btn btn-primary">
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div><!--/span-->
<script type="text/javascript">
    $(document).ready(function () {
        $('input[name="checkall"]').click(function () {
            if ($('input[name="checkall"]').is(':checked')) {
                $('input[name="table[]"]').prop('checked', true);
            } else {
                $('input[name="table[]"]').prop('checked', false);
            }
        })
    })
</script>