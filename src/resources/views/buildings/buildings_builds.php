<br />
<div id="content" role="main">
    {BuildListScript}
    <table width=530>
        {BuildList}
        {list_of_buildings}
        <tr>
            <td class="l" width="120" height="120">
                <a href="game.php?page=infos&gid={i}">
                    <img alt="{n}" border="0" src="{dpath}elements/{i}.gif" align="top" width="120" height="120">
                </a>
            </td>
            <td class="l">
                <a href="game.php?page=infos&gid={i}">{n}</a>{nivel}<br>
                {descriptions}<br>
                {price}
                {time}
            </td>
            <td class="k">{click}</td>
        </tr>
        {/list_of_buildings}
    </table>
</div>