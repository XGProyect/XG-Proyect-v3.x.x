<script type="text/javascript">
<!--
function t() {
	v           = new Date();
	var blc     = document.getElementById('blc');
	var timeout = 1;
	n           = new Date();
	ss          = pp;
	aa          = Math.round( (n.getTime() - v.getTime() ) / 1000. );
	s           = ss - aa;
	m           = 0;
	h           = 0;
	rurl        = "game.php?page={call_program}&planet=" + pl;
	if ( (ss + 3) < aa ) {
		blc.innerHTML = "{bd_finished}<br>" + "<a href=" + rurl + ">{bd_continue}</a>";
		if ((ss + 6) >= aa) {
			window.setTimeout('document.location.href="' + rurl + '";', 3500);
		}
	} else {
		if ( s <= 0 ) {
			blc.innerHTML = "{bd_finished}<br>" + "<a href=" + rurl + ">{bd_continue}</a>";
			window.setTimeout('document.location.href="' + rurl + '";', 1100);
			return;
		} else {
			if ( s > 59) {
				m = Math.floor( s / 60);
				s = s - m * 60;
			}
			if ( m > 59) {
				h = Math.floor( m / 60);
				m = m - h * 60;
			}
			if ( s < 10 ) {
				s = "0" + s;
			}
			if ( m < 10 ) {
				m = "0" + m;
			}
			blc.innerHTML = h + ":" + m + ":" + s + "<br><a href=game.php?page={current_page}&r={call_program}&listid=" + pk + "&cmd=" + pm + "&planet=" + pl + ">{bd_cancel}</a>";
		}
		pp = pp - 1;
		if (timeout == 1) {
			window.setTimeout("t();", 999);
		}
	}
}
//-->
</script>
