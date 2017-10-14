<script type="text/javascript">
function t{type}{ref}() {
v = new Date();
var bxx{type}{ref} = document.getElementById('bxx{type}{ref}');
n = new Date();
ss{type}{ref} = pp{type}{ref};
ss{type}{ref} = ss{type}{ref} - Math.round((n.getTime() - v.getTime()) / 1000.);
m{type}{ref} = 0;
h{type}{ref} = 0;
if (ss{type}{ref} < 0) {
	bxx{type}{ref}.innerHTML = "-";
} else {
	if (ss{type}{ref} > 59) {
		m{type}{ref} = Math.floor(ss{type}{ref} / 60);
		ss{type}{ref} = ss{type}{ref} - m{type}{ref} * 60;
	}
	if (m{type}{ref} > 59) {
		h{type}{ref} = Math.floor(m{type}{ref} / 60);
		m{type}{ref} = m{type}{ref} - h{type}{ref} * 60;
	}
	if (ss{type}{ref} < 10) {
		ss{type}{ref} = "0" + ss{type}{ref};
	}
	if (m{type}{ref} < 10) {
		m{type}{ref} = "0" + m{type}{ref};
	}
	bxx{type}{ref}.innerHTML = h{type}{ref} + ":" + m{type}{ref} + ":" + ss{type}{ref};
}
pp{type}{ref} = pp{type}{ref} - 1;
window.setTimeout("t{type}{ref}();", 999);
}
</script>