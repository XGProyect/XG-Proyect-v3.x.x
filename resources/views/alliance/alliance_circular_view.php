<br />
<div id="content">
    <table style="width:654px">
        <tr>
            <td class="c" colspan="4">{al_alliance}</td>
        </tr>
        <tr>
            <th style="width:25%"><a href="game.php?page=alliance">{al_alliance_overview}</a></th>
            <th style="width:25%"><a href="game.php?page=alliance&mode=admin&edit=ally">{al_alliance_management}</a></th>
            <th style="width:25%"><a href="game.php?page=alliance&mode=circular">{al_alliace_communication}</a></th>
            <th style="width:25%"><a href="game.php?page=alliance&mode=apply">{al_alliance_application}</a></th>
        </tr>
        <tr>
            <th colspan="4">
                <form action="game.php?page=alliance&mode=circular&sendmail=1" method="POST">
                    <table width="100%">
                        <tbody>
                            <tr>
                                <th style="width: 25%;">
                                    <span style="float: left;">{al_receiver}</span>
                                </th>
                                <th colspan="1">
                                    <span style="float: left;">
                                        <select name="r">
                                            <option value="0">{al_all_players}</option>
                                            {ranks_list}
                                            <option value="{value}">{name}</option>
                                            {/ranks_list}
                                        </select>
                                    </span>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="textLeft">
                                    <textarea name="text" cols="60" rows="10" class="circulartexts"></textarea>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="1">
                                    <span style="float: left;">
                                        <span class="counter">2000</span> {al_characters}
                                    </span>
                                </th>
                                <th colspan="1"></th>
                            </tr>
                            <tr>
                                <th colspan="2">
                                    <input type="submit" value="{al_circular_send_submit}">
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </th>
        </tr>
    </table>
</div>
<script>
    var textarea = document.querySelector('.circulartexts');
    var counter = document.querySelector('.counter');
    var maxLength = 2000;

    textarea.addEventListener('input', updateCounter);

    // Función para actualizar el contador de caracteres
    function updateCounter() {
        var remaining = maxLength - textarea.value.length;
        counter.textContent = remaining;
    }

    // Al cargar la página, mostrar el contador actualizado si hay contenido en el textarea
    updateCounter();
</script>