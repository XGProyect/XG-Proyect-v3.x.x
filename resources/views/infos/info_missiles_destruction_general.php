<br />
<div id="content">
    <table width="519">
        <tr>
            <td class="c">{name}</td>
        </tr>
        <tr>
            <th>
                <table>
                    <tbody>
                        <tr>
                            <td><img src="{dpath}elements/{image}.gif" align="top" border="0" height="120" width="120">
                            </td>
                            <td>{description}</td>
                        </tr>
                    </tbody>
                </table>
            </th>
        </tr>
        <tr>
            <th>
                <center>
                    <form action="game.php?page=infos&gid=44" method="post">
                        <input type="hidden" name="form" value="missiles" />
                        <table border="0" cellpadding="0" cellspacing="0" style="width:450px">
                            <tbody>
                                {table_head}
                                {table_data}
                            </tbody>
                        </table>
                        <br />
                        {table_footer}
                    </form>
                </center>
            </th>
        </tr>
    </table>