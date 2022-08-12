<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<div id="header_top" role="banner">
    <table class="header" style="margin:0 auto">
        <tr class="header">
            <td class="header" style="width:5;">
                <table class="header">
                    <tr class="header">
                        <td class="header">
                            <img src="{dpath}planets/small/s_{image}.jpg" height="50" width="50" alt=""/>
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
                <table class="header" id="resources" cellspacing="0" cellpadding="0" padding-right="30">
                    <tr class="header" style="text-align:center">
                        <td width="85" class="header">
                            <img src="{dpath}resources/metal.gif" width="42" height="22" alt="{metal}"/>
                        </td>
                        <td width="85" class="header">
                            <img src="{dpath}resources/crystal.gif" width="42" height="22" alt="{cristal}"/>
                        </td>
                        <td width="85" class="header">
                            <img src="{dpath}resources/deuterium.gif" width="42" height="22" alt="{deuterium}"/>
                        </td>
                        <td width="85" class="header">
                            <img src="{dpath}resources/darkmatter.jpg" width="42" height="22" title="{dark_matter}"/>
                        </td>
                        <td width="85" class="header">
                            <img src="{dpath}resources/energy.gif" width="42" height="22" alt="{energy}"/>
                        </td>
                    </tr>
                    <tr class="header" style="text-align:center">
                        <td class="header" width="85">
                            <span style="font-weight:700;font-style: italic;">{metal}</span>
                        </td>
                        <td class="header" width="85">
                            <span style="font-weight:700;font-style: italic;">{crystal}</span>
                        </td>
                        <td class="header" width="85">
                            <span style="font-weight:700;font-style: italic;">{deuterium}</span>
                        </td>
                        <td class="header" width="85">
                            <span style="font-weight:700;font-style: italic;">{dark_matter}</span>
                        </td>
                        <td class="header" width="85">
                            <span style="font-weight:700;font-style: italic;">{energy}</span>
                        </td>
                    </tr>
                    <tr class="header" style="text-align:center">
                        <td class="header" width="90">{re_metal}</td>
                        <td class="header" width="90">{re_crystal}</td>
                        <td class="header" width="90">{re_deuterium}</td>
                        <td class="header" width="90">{re_darkmatter}</td>
                        <td class="header" width="90">{re_energy}</td>
                    </tr>
                </table>
            </td>
            <td class="header">
                <table class="header">
                    <tr class="header">
                        <td style="margin: 0 auto;" width="35px" class='header'>
                            <a href="game.php?page=officier" accesskey="o">
                                <img style="border:0;" src="{dpath}premium/commander_ikon{img_premium_officier_commander}.gif" width="32" height="32" alt="{tn_commander}" onmouseover="return overlib('<table width=390px><tr><td class=c>{of_hire_commander}</td></tr><tr><th style=text-align:left>{add_premium_officier_commander}</th></tr></table>');" onmouseout="return nd();">
                            </a>
                        </td>
                        <td style="margin: 0 auto;" width="35" class="header">
                            <a href="game.php?page=officier" accesskey="o">
                                <img style="border:0;" src="{dpath}premium/admiral_ikon{img_premium_officier_admiral}.gif" width="32" height="32" alt="{tn_admiral}" onmouseover="return overlib('<table width=390px><tr><td class=c>{of_hire_admiral}</td></tr><tr><th style=text-align:left>{add_premium_officier_admiral}</th></tr></table>');" onmouseout="return nd();">
                            </a>
                        </td>
                        <td style="margin: 0 auto;" width="35" class="header">
                            <a href="game.php?page=officier" accesskey="o">
                                <img style="border:0;" src="{dpath}premium/ingenieur_ikon{img_premium_officier_engineer}.gif" width="32" height="32" alt="{tn_engineer}" onmouseover="return overlib('<table width=310px><tr><td class=c>{of_hire_engineer}</td></tr><tr><th style=text-align:left>{add_premium_officier_engineer}</th></tr></table>');" onmouseout="return nd();">
                            </a>
                        </td>
                        <td style="margin: 0 auto;" width="35" class="header">
                            <a href="game.php?page=officier" accesskey="o">
                                <img style="border:0;" src="{dpath}premium/geologe_ikon{img_premium_officier_geologist}.gif" width="32" height="32" alt="{tn_geologist}" onmouseover="return overlib('<table width=200px><tr><td class=c>{of_hire_geologist}</td></tr><tr><th style=text-align:left>{add_premium_officier_geologist}</th></tr></table>');" onmouseout="return nd();">
                            </a>
                        </td>
                        <td style="margin: 0 auto;" width="35" class="header">
                            <a href="game.php?page=officier" accesskey="o">
                                <img style="border:0;" src="{dpath}premium/technokrat_ikon{img_premium_officier_technocrat}.gif" width="32" height="32" alt="{tn_technocrat}" onmouseover="return overlib('<table width=275px><tr><td class=c>{of_hire_technocrat}</td></tr><tr><th style=text-align:left>{add_premium_officier_technocrat}</th></tr></table>');" onmouseout="return nd();">
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {show_umod_notice}
</div>
