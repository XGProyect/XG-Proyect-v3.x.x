<script type="text/javascript" >
function calcul() {
	var Cristal = document.forms['trader'].elements['cristal'].value;
	var Deuterium = document.forms['trader'].elements['deut'].value;

	Cristal   = Cristal * {mod_ma_res_a};
	Deuterium = Deuterium * {mod_ma_res_b};

	var Metal = Cristal + Deuterium;
	document.getElementById("met").innerHTML = Metal;

	if (isNaN(document.forms['trader'].elements['cristal'].value)) {
		document.getElementById("met").innerHTML = "Sólo números";
	}
	if (isNaN(document.forms['trader'].elements['deut'].value)) {
		document.getElementById("met").innerHTML = "Sólo números";
	}
}
</script>
<br />
<div id="content">
    <form id="trader" action="" method="post">
    <input type="hidden" name="ress" value="metal">
    <table width="569">
    <tr>
        <td class="c" colspan="5"><b>{tr_sell_metal}</b></td>
    </tr><tr>
        <th>{tr_resource}</th>
        <th>{tr_amount}</th>
        <th>{tr_quota_exchange}</th>
    </tr><tr>
        <th>{Metal}</th>
        <th><span id='met'></span>&nbsp;</th>
        <th>{mod_ma_res}</th>
    </tr><tr>
        <th>{Crystal}</th>
        <th><input name="cristal" type="text" value="0" onkeyup="calcul()"/></th>
        <th>{mod_ma_res_a}</th>
    </tr><tr>
        <th>{Deuterium}</th>
        <th><input name="deut" type="text" value="0" onkeyup="calcul()"/></th>
        <th>{mod_ma_res_b}</th>
    </tr><tr>
        <th colspan="6"><input type="submit" value="{tr_exchange}" /></th>
    </tr>
    </table>
    </form>
</div>