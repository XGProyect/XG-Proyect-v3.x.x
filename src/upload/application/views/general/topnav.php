<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<div id='header_top'><center>
        <table class='header'>
            <tr class='header' >
                <td class='header' style='width:5;' >
                    <table class='header'>
                        <tr class='header'>
                            <td class='header'><img src="{dpath}planets/small/s_{image}.jpg" height="50" width="50"></td>
                            <td class='header'>
                                <table class='header'>

                                    <select size="1" onChange="eval('location=\'' + this.options[this.selectedIndex].value + '\'');">
                                        {planetlist}
                                    </select>
                                </table>
                            </td>
                        </tr>
                    </table></td>
                <td class='header'>   <table class='header' id='resources' border="0" cellspacing="0" cellpadding="0" padding-right='30' >

                        <tr class='header'>

                            <td align="center" width="85" class='header'>
                                <img border="0" src="{dpath}resources/metal.gif" width="42" height="22">
                            </td>

                            <td align="center" width="85" class='header'>
                                <img border="0" src="{dpath}resources/crystal.gif" width="42" height="22">
                            </td>

                            <td align="center" width="85" class='header'>
                                <img border="0" src="{dpath}resources/deuterium.gif" width="42" height="22">
                            </td>

                            <td align="center" width="85" class='header'>
                                <img border="0" src="{dpath}resources/darkmatter.jpg" width="42" height="22" title="Dark Matter">
                            </td>

                            <td align="center" width="85" class='header'>
                                <img border="0" src="{dpath}resources/energy.gif" width="42" height="22">
                            </td>
                        </tr>

                        <tr class='header'>
                            <td align="center" class='header' width="85"><i><b><font color="#ffffff">{metal}</font></b></i></td>
                            <td align="center" class='header' width="85"><i><b><font color="#ffffff">{crystal}</font></b></i></td>
                            <td align="center" class='header' width="85"><i><b><font color="#ffffff">{deuterium}</font></b></i></td>
                            <td align="center" class='header' width="85"><i><b><font color="#ffffff">{dark_matter}</font></b></i></td>

                            <td align="center" class='header' width="85"><i><b><font color="#ffffff">{energy}</font></b></i></td>

                        </tr>
                        <tr class='header'>
                            <td id="value_metal" align="center" class='header' width="90"><font >{re_metal}</font></td>
                            <td id="value_crystal" align="center" class='header' width="90"><font >{re_crystal}</font></td>
                            <td id="value_deuterium" align="center" class='header' width="90"><font >{re_deuterium}</font></td>
                            <td align="center" class='header' width="90"><font color="#FFFFFF">{re_darkmatter}</font></DIV></td>

                            <td align="center" class='header' width="90">{re_energy}</td>

                        </tr>
		    </table>
		<script>
		var planet_resources = {'metal': [{value_metal}, {value_metal_max}, {value_metal_perhour}], 'crystal': [{value_crystal}, {value_crystal_max}, {value_crystal_perhour}], 'deuterium': [{value_deuterium}, {value_deuterium_max}, {value_deuterium_perhour}]};
		var dateRes = Date.now();
		function updateResources(){
			Object.entries(planet_resources).forEach(([res, values]) => {
				var element = document.getElementById('value_'+res);
				var amount = Math.floor(values[0] + ((Date.now() - dateRes) / 1000) * (values[2] / 3600));
				if (values[1] > 0 && amount >= values[1]) {
					if (values[0] >= values[1]) {
						amount = values[0];
					} else {
						amount = values[1];
					}
					element.innerHTML = '<font color="#ff0000">' + amount.toLocaleString() + '</font>';
				} else {
					element.innerHTML = amount.toLocaleString();
				}
			});
			setTimeout(updateResources, 1000);
		}
		setTimeout(updateResources, 1000);
		</script>
		</td>
                <td class='header'>
                    <table class='header' align=left>
                        <tr class='header'>


                            <td align="center" width="35" class='header'>
                                <a href='game.php?page=officier' accesskey="o">
                                    <img border="0" src="{dpath}premium/commander_ikon{img_commander}.gif" width="32" height="32" alt="{tn_commander}"
                                         onmouseover="return overlib('<center><font size=1 color=white><b><br>{tn_commander}</font><br><br><a href=game.php?page=officier><font size=1 color=lime>{tn_get_now}</b></font></a></center>', LEFT, WIDTH, 150);" onmouseout="return nd();">
                                </a>
                            </td>
                            <td align="center" width="35" class='header'>
                                <a href='game.php?page=officier' accesskey="o">
                                    <img border="0" src="{dpath}premium/admiral_ikon{img_admiral}.gif" width="32" height="32" alt="{tn_admiral}"
                                         onmouseover="return overlib('<center><font size=1 color=white><b><br>{tn_admiral}</font><br><font size=1 color=skyblue>&amp;nbsp;{tn_add_admiral}</font><br><br><a href=game.php?page=officier><font size=1 color=lime>{tn_get_now}</b></font></a></center>', LEFT, WIDTH, 150);" onmouseout="return nd();">

                                </a>
                            </td>
                            <td align="center" width="35" class='header'>
                                <a href='game.php?page=officier' accesskey="o">
                                    <img border="0" src="{dpath}premium/ingenieur_ikon{img_engineer}.gif" width="32" height="32" alt="{tn_engineer}"
                                         onmouseover="return overlib('<center><font size=1 color=white><b><br>{tn_engineer}</font><br><font size=1 color=skyblue>{tn_add_engineer}</font><br><br><a href=game.php?page=officier><font size=1 color=lime>{tn_get_now}</b></font></a></center>', LEFT, WIDTH, 150);" onmouseout="return nd();">
                                </a>
                            </td>
                            <td align="center" width="35" class='header'>
                                <a href='game.php?page=officier' accesskey="o">
                                    <img border="0" src="{dpath}premium/geologe_ikon{img_geologist}.gif" width="32" height="32" alt="{tn_geologist}"
                                         onmouseover="return overlib('<center><font size=1 color=white><b><br>{tn_geologist}</font><br><font size=1 color=skyblue>{tn_add_geologist}</font><br><br><a href=game.php?page=officier><font size=1 color=lime>{tn_get_now}</b></font></a></center>', LEFT, WIDTH, 150);" onmouseout="return nd();">
                                </a>
                            </td>
                            <td align="center" width="35" class='header'>
                                <a href='game.php?page=officier' accesskey="o">
                                    <img border="0" src="{dpath}premium/technokrat_ikon{img_technocrat}.gif" width="32" height="32" alt="{tn_technocrat}"
                                         onmouseover="return overlib('<center><font size=1 color=white><b><br>{tn_technocrat}</font><br><font size=1 color=skyblue>{tn_add_technocrat}</font><br><br><a href=game.php?page=officier><font size=1 color=lime>{tn_get_now}</b></font></a></center>', LEFT, WIDTH, 150);" onmouseout="return nd();">
                                </a>
                            </td>
                            <td align="center" class='header'></td>
                        </tr>
                    </table></td>
            </tr>
            </tr>
        </table>
        {show_umod_notice}
</div>
