<br />
<div id="content" role="main">
    {message}
    <form action="" method="post" role="form">
        <table align="top" width="530">
            {list_of_items}
            <tr>
                <th scope="row" class="l">
                    <a href="game.php?page=infos&gid={element}">
                        <img border="0" src="{dpath}elements/{element}.gif" align="top" width="120" height="120" alt="{element_name}"/>
                    </a>
                </th>
                <td class="l">
                    <a href="game.php?page=infos&gid={element}">{element_name}</a> {element_nbre}<br>
                    {element_description}<br/>
                    {element_price}
                    {building_time}
                </td>
                <th role="cell" class="k">
                    {add_element}
                </th>
            </tr>
            {/list_of_items}
            {build_button}
        </table>
    </form>
    {building_list}
</div>