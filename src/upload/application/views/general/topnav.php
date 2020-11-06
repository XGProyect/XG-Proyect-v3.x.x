<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<div id="header_top">
    <center>
        <table class="header">
            <tr class="header">
                <td class="header" style="width:5;">
                    <table class="header">
                        <tr class="header">
                            <td class="header"><img src="{dpath}planets/small/s_{image}.jpg" height="50" width="50">
                            </td>
                            <td class="header">
                                <table class="header">
                                    <select size="1" onChange="eval('location=\'' + this.options[this.selectedIndex].value + '\'');">
                                        {planetlist}
                                    </select>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="header">
                    <table class="header" id="resources" border="0" cellspacing="0" cellpadding="0" padding-right="30">
                        <tr class="header">
                            <td align="center" width="85" class="header">
                                <img border="0" src="{dpath}resources/metal.gif" width="42" height="22">
                            </td>

                            <td align="center" width="85" class="header">
                                <img border="0" src="{dpath}resources/crystal.gif" width="42" height="22">
                            </td>

                            <td align="center" width="85" class="header">
                                <img border="0" src="{dpath}resources/deuterium.gif" width="42" height="22">
                            </td>

                            <td align="center" width="85" class="header">
                                <img border="0" src="{dpath}resources/darkmatter.jpg" width="42" height="22"
                                    title="Dark Matter">
                            </td>

                            <td align="center" width="85" class="header">
                                <img border="0" src="{dpath}resources/energy.gif" width="42" height="22">
                            </td>
                        </tr>
                        <tr class="header">
                            <td align="center" class="header" width="85">
                                <i>
                                    <b>
                                        <font color="#ffffff">{metal}</font>
                                    </b>
                                </i>
                            </td>
                            <td align="center" class="header" width="85">
                                <i>
                                    <b>
                                        <font color="#ffffff">{crystal}</font>
                                    </b>
                                </i>
                            </td>
                            <td align="center" class="header" width="85">
                                <i>
                                    <b>
                                        <font color="#ffffff">{deuterium}</font>
                                    </b>
                                </i>
                            </td>
                            <td align="center" class="header" width="85">
                                <i>
                                    <b>
                                        <font color="#ffffff">{dark_matter}</font>
                                    </b>
                                </i>
                            </td>
                            <td align="center" class="header" width="85">
                                <i>
                                    <b>
                                        <font color="#ffffff">{energy}</font>
                                    </b>
                                </i>
                            </td>
                        </tr>
                        <tr class="header">
                            <td align="center" class="header" width="90">
                                <font>{re_metal}</font>
                            </td>
                            <td align="center" class="header" width="90">
                                <font>{re_crystal}</font>
                            </td>
                            <td align="center" class="header" width="90">
                                <font>{re_deuterium}</font>
                            </td>
                            <td align="center" class="header" width="90">
                                <font color="#FFFFFF">{re_darkmatter}</font>
                            </td>
                            <td align="center" class="header" width="90">{re_energy}</td>
                        </tr>
                        </table>
                    </td>
                <td class="header">
                    <table class="header" align=left>
                        <tr class="header">
                            <td style="margin: 0 auto;" width="35px" class='header'>
                                <a href="game.php?page=officier" accesskey="o">
                                    <img style="border:0;" src="{dpath}premium/commander_ikon{img_premium_officier_commander}.gif" width="32"
                                        height="32" alt="{tn_commander}"
                                        onmouseover="return overlib('<table width=390px><tr><td class=c>{tn_hire_commander}</td></tr><tr><th style=text-align:left>{add_premium_officier_commander}</th></tr></table>', CENTER, WIDTH, 390, OFFSETY, 20);"
                                        onmouseout="return nd();">
                                </a>
                            </td>
                            <td style="margin: 0 auto;" width="35" class="header">
                                <a href="game.php?page=officier" accesskey="o">
                                    <img style="border:0;" src="{dpath}premium/admiral_ikon{img_premium_officier_admiral}.gif" width="32"
                                        height="32" alt="{tn_admiral}"
                                        onmouseover="return overlib('<table width=400px><tr><td class=c>{tn_hire_admiral}</td></tr><tr><th style=text-align:left>{add_premium_officier_admiral}</th></tr></table>', CENTER, WIDTH, 400, OFFSETY, 20);"
                                        onmouseout="return nd();">
                                </a>
                            </td>
                            <td style="margin: 0 auto;" width="35" class="header">
                                <a href="game.php?page=officier" accesskey="o">
                                    <img style="border:0;" src="{dpath}premium/ingenieur_ikon{img_premium_officier_engineer}.gif" width="32"
                                        height="32" alt="{tn_engineer}"
                                        onmouseover="return overlib('<table width=310px><tr><td class=c>{tn_hire_admiral}</td></tr><tr><th style=text-align:left>{add_premium_officier_engineer}</th></tr></table>', CENTER, WIDTH, 310, OFFSETY, 20);"
                                        onmouseout="return nd();">
                                </a>
                            </td>
                            <td style="margin: 0 auto;" width="35" class="header">
                                <a href="game.php?page=officier" accesskey="o">
                                    <img style="border:0;" src="{dpath}premium/geologe_ikon{img_premium_officier_geologist}.gif" width="32"
                                        height="32" alt="{tn_geologist}"
                                        onmouseover="return overlib('<table width=150px><tr><td class=c>{tn_hire_geologist}</td></tr><tr><th style=text-align:left>{add_premium_officier_geologist}</th></tr></table>', CENTER, WIDTH, 150, OFFSETY, 20);"
                                        onmouseout="return nd();">
                                </a>
                            </td>
                            <td style="margin: 0 auto;" width="35" class="header">
                                <a href="game.php?page=officier" accesskey="o">
                                    <img style="border:0;" src="{dpath}premium/technokrat_ikon{img_premium_officier_technocrat}.gif" width="32"
                                        height="32" alt="{tn_technocrat}"
                                        onmouseover="return overlib('<table width=275px><tr><td class=c>{tn_hire_technocrat}</td></tr><tr><th style=text-align:left>{add_premium_officier_technocrat}</th></tr></table>', CENTER, WIDTH, 275, OFFSETY, 20);"
                                        onmouseout="return nd();">
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        {show_umod_notice}
    </center>
</div>
