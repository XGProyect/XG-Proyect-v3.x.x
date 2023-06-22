<table width="100%" style="text-align: left;">
    <form action="game.php?page=alliance&mode=admin&edit=tag" method="POST">
        <tr>
            <td width="50%">{former}:</td>
            <td>{case}</td>
        </tr>
        <tr>
            <td>{title}:</td>
            <td>
                <input type="text" name="tag" maxlength="30">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span style="float: right;">
                    <input type="submit" value="{al_change_submit}">
                </span>
            </td>
        </tr>
    </form>
</table>