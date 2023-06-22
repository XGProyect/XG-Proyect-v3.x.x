<style>
    .tab-content {
        display: none;
    }

    .active {
        display: block;
    }
</style>
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
            <th style="width:25%"><a href="game.php?page=alliance&mode=admin&edit=requests">{al_alliance_application} {alliance_request}</a></th>
        </tr>

        <tr>
            <th colspan="4" style="text-align: center;">{al_configure_ranks}</th>
        </tr>

        <tr>
            <th colspan="4">
                {alliance_rank}
            </th>
        </tr>


        <tr>
            <th colspan="4" style="text-align: center;">{al_texts}</th>
        </tr>

        <tr>
            <th colspan="4">
                <form action="" method="POST">
                    <input type="hidden" name="t" value="{t}">
                    <table style="width: 654px">
                        <tr>
                            <th colspan="2">
                                <a href="game.php?page=alliance&mode=admin&edit=ally&t=2">{al_inside_text}</a>
                            </th>
                            <th colspan="2">
                                <a href="game.php?page=alliance&mode=admin&edit=ally&t=1">{al_outside_text}</a>
                            </th>
                            <th colspan="2">
                                <a href="game.php?page=alliance&mode=admin&edit=ally&t=3">{al_request_text}</a>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="6">
                                <textarea name="text" cols="70" rows="14" style="max-width: 100%;" class="alliancetexts">{text}</textarea>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="3">
                                <span style="float: left;">
                                    <span class="counter">50000</span> {al_characters}
                                </span>
                            </th>
                            <th colspan="3">Preview</th>
                        </tr>
                        <tr>
                            <th colspan="6">
                                <span style="float: right;">
                                    <input type="submit" value="{al_save}">
                                </span>
                            </th>
                        </tr>
                    </table>
                </form>
            </th>
        </tr>

        <tr>
            <th colspan="4" style="text-align: center;">{al_manage_options}</th>
        </tr>
        <tr>
            <th colspan="4">
                <form action="" method="POST">
                    <table width="100%">
                        <tr>
                            <th style="text-align: left;">{al_web_site}</th>
                            <th style="text-align: left;"><input type="text" name="homepage" value="{alliance_web}" size="70"></th>
                        </tr>
                        <tr>
                            <th style="text-align: left;">{al_manage_image}</th>
                            <th style="text-align: left;"><input type="text" name="image" value="{alliance_image}" size="70"></th>
                        </tr>
                        <tr>
                            <th style="text-align: left;">{al_manage_requests}</th>
                            <th style="text-align: left;">
                                <select name="request_notallow">
                                    <option value="0" {alliance_request_notallow_0}>{al_requests_not_allowed}</option>
                                    <option value="1" {alliance_request_notallow_1}>{al_requests_allowed}</option>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th style="text-align: left;">{al_manage_founder_rank}</th>
                            <th style="text-align: left;"><input type="text" name="owner_range" value="{alliance_owner_range}" size=30></th>
                        </tr>
                        <tr>
                            <th style="text-align: left;">{al_manage_newcomer_rank}</th>
                            <th style="text-align: left;"><input type="text" name="newcomer_range" value="{alliance_newcomer_range}" size=30></th>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <span style="float: right;">
                                    <input type="submit" name="options" value="{al_save}">
                                </span>
                            </th>
                        </tr>
                    </table>
                </form>
            </th>
        </tr>

        <tr>
            <th colspan="4" style="text-align: center;">{al_alliance_change_tag_name}</th>
        </tr>

        <tr>
            <th colspan="4" id="allyTagChange">
                <table width="50%">
                    <tr>
                        <th colspan="2">
                            <a href="#" class="tab-link active" data-tab="#tag">{al_manage_change_tag}</a>
                        </th>
                        <th colspan="2">
                            <a href="#" class="tab-link" data-tab="#name">{al_manage_change_name}</a>
                        </th>
                    </tr>
                </table>
                <table width="100%">
                    <tr>
                        <th colspan="11" id="tag" class="tab-content active">
                            {alliance_tag}
                        </th>
                        <th colspan="11" id="name" class="tab-content">
                            {alliance_name}
                        </th>
                    </tr>
                </table>
            </th>
        </tr>

        <tr>
            <th colspan="4" style="text-align: center;">{al_alliance_disolve_transfer}</th>
        </tr>

        <tr>
            <th colspan="4" id="quitAlly">
                <table width="50%">
                    <tr>
                        <th colspan="2">
                            <a href="#" class="tab-link active" data-tab="#delete">{al_disolve_alliance}</a>
                        </th>
                        <th colspan="2">
                            <a href="#" class="tab-link" data-tab="#transfer">{al_transfer_alliance}</a>
                        </th>
                    </tr>
                </table>
                <table width="100%">
                    <tr>
                        <th colspan="11" id="delete" class="tab-content active">
                            <table width="100%">
                                <tr>
                                    <td style="text-align: left;"> {al_disolve_alliance}</td>
                                </tr>
                                <tr>
                                    <td><input type="button" onclick="javascript:location.href = 'game.php?page=alliance&mode=admin&edit=exit';" value="{al_continue}" /></td>
                                </tr>
                            </table>
                        </th>
                        <th colspan="11" id="transfer" class="tab-content">
                            <table width="100%">
                                <tr>
                                    <td style="text-align: left;">{al_transfer_alliance}</td>
                                </tr>
                                <tr>
                                    <td><input type="button" onclick="javascript:location.href = 'game.php?page=alliance&mode=admin&edit=transfer';" value="{al_continue}" /></td>
                                </tr>
                            </table>
                        </th>
                    </tr>
                </table>
            </th>
        </tr>
    </table>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function setupTabs(tabId, tabLinkIdPrefix, tabContentIdPrefix) {
            var tabLinks = document.querySelectorAll('#' + tabId + ' .' + tabLinkIdPrefix);
            var tabContents = document.querySelectorAll('#' + tabId + ' .' + tabContentIdPrefix);

            tabLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    var targetTab = document.querySelector(link.getAttribute('data-tab'));

                    tabLinks.forEach(function(link) {
                        link.classList.remove('active');
                    });
                    tabContents.forEach(function(content) {
                        content.classList.remove('active');
                    });

                    link.classList.add('active');
                    targetTab.classList.add('active');
                });
            });
        }

        setupTabs('allyTagChange', 'tab-link', 'tab-content');
        setupTabs('quitAlly', 'tab-link', 'tab-content');

        var textarea = document.querySelector('.alliancetexts');
        var counter = document.querySelector('.counter');
        var maxLength = 50000;

        textarea.addEventListener('input', updateCounter);

        // Función para actualizar el contador de caracteres
        function updateCounter() {
            var remaining = maxLength - textarea.value.length;
            counter.textContent = remaining;
        }

        // Al cargar la página, mostrar el contador actualizado si hay contenido en el textarea
        updateCounter();
    });
</script>