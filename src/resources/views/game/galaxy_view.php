<br>
<div id="content" role="main">
    <script  language="JavaScript">
        function galaxy_submit(value) {
            document.getElementById('auto').name = value;
            document.getElementById('galaxy_form').submit();
        }

        function fenster(target_url, win_name) {
            var new_win = window.open(target_url, win_name, 'scrollbars=yes,menubar=no,top=0,left=0,toolbar=no,width=550,height=280,resizable=yes');
            new_win.focus();
        }


        var IE = document.all ? true : false;

        function mouseX(e) {
            if (IE) { // grab the x-y pos.s if browser is IE
                return event.clientX + document.body.scrollLeft;
            } else {
                return e.pageX
            }
        }
        function mouseY(e) {
            if (IE) { // grab the x-y pos.s if browser is IE
                return event.clientY + document.body.scrollTop;
            } else {
                return e.pageY;
            }
        }

    </script>
    <script language="JavaScript" src="{js_path}tw-sack-min.js"></script>
    <script type="text/javascript">
        var ajax = new sack();
        var strInfo = "";

        function whenLoading() {
            //var e = document.getElementById('fleetstatus');
            //e.innerHTML = "Sende Flotte...";
        }

        function whenLoaded() {
            //    var e = document.getElementById('fleetstatus');
            // e.innerHTML = "Flotte gesendet...";
        }

        function whenInteractive() {
            //var e = document.getElementById('fleetstatus');
            // e.innerHTML = "Erhalte Daten...";
        }

        /*
            We can overwrite functions of the sack object easily. :-)
            This function will replace the sack internal function runResponse(),
            which normally evaluates the xml return value via eval(this.response).
            */
        function whenResponse() {

            /*
                *
                *  600   OK
                *  601   no planet exists there
                *  602   no moon exists there
                *  603   player is in noob protection
                *  604   player is too strong
                *  605   player is in u-mode
                *  610   not enough espionage probes, sending x (parameter is the second return value)
                *  611   no espionage probes, nothing send
                *  612   no fleet slots free, nothing send
                *  613   not enough deuterium to send a probe
                *
                */
            // the first three digit long return value
            retVals = this.response.split(" ");
            // and the other content of the response
            // but since we only got it if we can send some but not all probes
            // theres no need to complicate things with better parsing

            // each case gets a different table entry, no language file used :P
            switch (parseInt(retVals[0])) {
                case 600:
                    addToTable("{gl_success}", "success");
                    break;
                case 601:
                    addToTable("{gl_error}", "error");
                    break;
                case 602:
                    addToTable("{gl_no_moon}", "error");
                    break;
                case 603:
                    addToTable("{gl_noob_protection}", "error");
                    break;
                case 604:
                    addToTable("{gl_too_strong}", "error");
                    break;
                case 605:
                    addToTable("{gl_vacation_mode}", "vacation");
                    break;
                case 610:
                    addToTable("{gl_only_amount_ships_1} " + retVals[1] + " {gl_only_amount_ships_2}", "notice");
                    break;
                case 611:
                    addToTable("{gl_no_ships}", "error");
                    break;
                case 612:
                    addToTable("{gl_no_slots}", "error");
                    break;
                case 613:
                    addToTable("{gl_no_deuterium}", "error");
                    break;
                case 614:
                    addToTable("{gl_no_planet}", "error");
                    break;
                case 615:
                    addToTable("{gl_not_enought_storage}", "error");
                    break;
                case 616:
                    addToTable("{gl_multi_alarm}", "error");
                    break;
            }
        }

        function doit(order, galaxy, system, planet, planettype, shipcount) {
            strInfo = "{gl_send} " + shipcount + " {gl_ship}" + (shipcount != 1 ? "{gl_ships}" : "") + " {gl_to}  " + galaxy + ":" + system + ":" + planet + "...";
            ajax.requestFile = "game.php?page=galaxy&fleet=true&action=send";

            // no longer needed, since we don't want to write the cryptic
            // response somewhere into the output html
            //ajax.element = 'fleetstatus';
            //ajax.onLoading = whenLoading;
            //ajax.onLoaded = whenLoaded;
            //ajax.onInteractive = whenInteractive;

            // added, overwrite the function runResponse with our own and
            // turn on its execute flag
            ajax.runResponse = whenResponse;
            ajax.execute = true;

            ajax.setVar("session", "");
            ajax.setVar("order", order);
            ajax.setVar("galaxy", galaxy);
            ajax.setVar("system", system);
            ajax.setVar("planet", planet);
            ajax.setVar("planettype", planettype);
            ajax.setVar("shipcount", shipcount);
            ajax.setVar("speed", 10);
            ajax.setVar("reply", "short");
            ajax.runAJAX();
        }

        /*
            * This function will manage the table we use to output up to three lines of
            * actions the user did. If there is no action, the tr with id 'fleetstatusrow'
            * will be hidden (display: none;) - if we want to output a line, its display
            * value is cleaned and therefore its visible. If there are more than 2 lines
            * we want to remove the first row to restrict the history to not more than
            * 3 entries. After using the object function of the table we fill the newly
            * created row with text. Let the browser do the parsing work. :D
            */
        function addToTable(strDataResult, strClass) {
            var e = document.getElementById('fleetstatusrow');
            var e2 = document.getElementById('fleetstatustable');
            // make the table row visible
            e.style.display = '';
            if (e2.rows.length > 0) {
                e2.deleteRow(0);
            }
            var row = e2.insertRow('test');
            var td1 = document.createElement("td");
            var td1text = document.createTextNode(strInfo);
            td1.appendChild(td1text);
            var td2 = document.createElement("td");
            var span = document.createElement("span");
            var spantext = document.createTextNode(strDataResult);
            var spanclass = document.createAttribute("class");
            spanclass.nodeValue = strClass;
            span.setAttributeNode(spanclass);
            span.appendChild(spantext);
            td2.appendChild(span);
            row.appendChild(td1);
            row.appendChild(td2);

        }

        function changeSlots(slotsInUse) {
            var e = document.getElementById('slots');
            e.innerHTML = slotsInUse;
        }

        function setShips(ship, count) {
            var e = document.getElementById(ship);
            e.innerHTML = count;
        }

        function cursorevent(evt) {
            evt = (evt) ? evt : ((event) ? event : null);
            if (evt.keyCode == 37) {
                galaxy_submit('systemLeft');
            }

            if (evt.keyCode == 39) {
                galaxy_submit('systemRight');
            }

            if (evt.keyCode == 38) {
                galaxy_submit('galaxyRight');
            }

            if (evt.keyCode == 40) {
                galaxy_submit('galaxyLeft');
            }

        }
        document.onkeydown = cursorevent;
    </script>
    {mip}
    <table width="656px">
        <tr>
            <td class="c" colspan="8">
                <form action="game.php?page=galaxy&mode=1" method="post" id="galaxy_form" style="margin:0" role="form">
                    <input type="hidden" id="auto" value="dr" >
                    <table width="100%">
                        <tr>
                            <td style="background-color: transparent">
                                {gl_galaxy}
                            </td>
                            <td style="background-color: transparent">
                                <input type="button" name="galaxyLeft" value="&lt;-" onClick="galaxy_submit('galaxyLeft')">
                            </td>
                            <td style="background-color: transparent">
                                <input type="number" name="galaxy" value="{selected_galaxy}" style="width:50px;" min="1" max="{max_galaxy}" tabindex="1">
                            </td>
                            <td style="background-color: transparent">
                                <input type="button" name="galaxyRight" value="-&gt;" onClick="galaxy_submit('galaxyRight')">
                            </td>
                            <td style="background-color: transparent">
                                {gl_solar_system}
                            </td>
                            <td style="background-color: transparent">
                                <input type="button" name="systemLeft" value="&lt;-" onClick="galaxy_submit('systemLeft')">
                            </td>
                            <td style="background-color: transparent">
                                <input type="number" name="system" value="{selected_system}" style="width:50px;" min="1" max="{max_system}" tabindex="2">
                            </td>
                            <td style="background-color: transparent">
                                <input type="button" name="systemRight" value="-&gt;" onClick="galaxy_submit('systemRight')">
                            </td>
                            <td style="background-color: transparent">
                                <input type="submit" value="{gl_go}">
                            </td>
                            <td style="background-color: transparent; width: 50%; text-align: right;">
                                <a href="game.php?page=fleet1&amp;galaxy={selected_galaxy}&amp;system={selected_system}&amp;planet=16&amp;planettype=1&amp;target_mission=15">
                                    <input type="button" value="{gl_expedition}">
                                </a>
                            </td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
        <tr>
            <th role="cell" colspan="8">
                <span id="probes">
                    {gl_esp_probe}:
                    <span id="probeValue">{spyprobes}</span>
                </span>
                <span id="recycler">
                    {gl_recyclers}:
                    <span id="recyclerValue">{recyclers}</span>
                </span>
                <span id="rockets">
                    {gl_ipm}:
                    <span id="missileValue">{currentmip}</span>
                </span>
                <span id="slots">
                    {gl_used_slots}:
                    <span id="slotValue">
                        <span id="slotUsed">{maxfleetcount}</span>/{fleetmax}
                    </span>
                </span>
            </th>
        </tr>
        <tr>
            <td role="columnheader" class="c" colspan="2">{gl_planet}</td>
            <td role="columnheader" class="c">{gl_name_activity}</td>
            <td role="columnheader" class="c">{gl_moon}</td>
            <td role="columnheader" class="c">{gl_debris}</td>
            <td role="columnheader" class="c">{gl_player_estate}</td>
            <td role="columnheader" class="c">{gl_alliance}</td>
            <td role="columnheader"class="c">{gl_actions}</td>
        </tr>
        {list_of_positions}
        <tr>
            <th role="cell" width="30px">{pos}</th>
            <th role="cell" width="30px">{planet}</th>
            <th role="cell" width="130px" style="white-space: nowrap;">{planetname}</th>
            <th role="cell" width="30px" style="white-space: nowrap;">{moon}</th>
            <th role="cell" width="30px" style="white-space: nowrap;">{debris}</th>
            <th role="cell" width="150px">{username}</th>
            <th role="cell" width="80px">{alliance}</th>
            <th role="cell" width="125px" style="white-space: nowrap;">{actions}</th>
        </tr>
        {/list_of_positions}
        <tr id="fleetstatusrow">
            <th role="cell" class="c" colspan="8">
                <table style="font-weight: bold" width="100%" id="fleetstatustable">
                    <!-- will be filled with content later on while processing ajax replies -->
                </table>
            </th>
        </tr>
        <tr>
            <td class="c" colspan="7">
                {planet_count} {gl_colonized_planets}
            </td>
            <td class="c">
                <a href="#" style="cursor: pointer;" onmouseover='return overlib("<table width=150><tr><td class=c colspan=2>{gl_legend}</td></tr><tr><td style=width: 20px;><span class=status_abbr_admin>{gl_a}</span></td><td width=220>{gl_administrator}</td></tr><tr><td style=width: 20px;><span class=status_abbr_strong>{gl_s}</span></td><td width=220>{gl_strong_player}</td></tr><tr><td style=width: 20px;><span class=status_abbr_noob>{gl_w}</span></td><td width=220>{gl_week_player}</td></tr><tr><td style=width: 20px;><span class=status_abbr_outlaw>{gl_o}</span></td><td width=220>{gl_outlaw}</td> </tr><tr><td style=width: 20px;><span class=status_abbr_vacation>{gl_v}</span></td><td width=220>{gl_vacation}</td></tr><tr><td style=width: 20px;><span class=status_abbr_banned><s>{gl_b}</s></span></td><td width=220>{gl_banned}</td> </tr> <tr> <td style=width: 20px;><span class=status_abbr_inactive>{gl_i}</span></td> <td width=220>{gl_inactive_seven}</td></tr><tr><td style=width: 20px;><span class=status_abbr_longinactive>{gl_I}</span></td><td width=220>{gl_inactive_twentyeight}</td></tr><tr> <td style=width: 20px;><span class=status_abbr_honorableTarget>{gl_hp}</span></td><td width=220>{gl_honourable_target}</td></tr></table>", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETY, -150);' onmouseout='return nd();'>
                    {gl_legend}
                </a>
            </td>
        </tr>
    </table>
</div>
