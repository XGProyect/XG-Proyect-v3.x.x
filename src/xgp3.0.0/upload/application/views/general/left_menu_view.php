<div id='leftmenu'>
	<script language="JavaScript">
	function f(target_url,win_name) {
	var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');
	new_win.focus();
	}
	</script>
	<center>
		<div id='menu'>
			<p style="width:110px;">
				<NOBR>
					{servername} {changelog}
				</NOBR>
			</p>
			<table width="110" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<img src="{dpath}menu/ogame-produktion.jpg" width="110" height="40" />
					</td>
				</tr>
				{menu_block1}
				<tr>
					<td>
						<img src="{dpath}menu/info-help.jpg" width="110" height="19">
					</td>
				</tr>
				{menu_block2}
				<tr>
					<td>
						<img src="{dpath}menu/user-menu.jpg" width="110" height="35">
					</td>
				</tr>
				{menu_block3}
				{admin_link}
				<tr>
					<td>
						<div align="center">
							<font color="#FFFFFF">
								<a href="#" title="Powered by XG Proyect {version} &copy; 2008 - {year} GNU General Public License">&copy; 2008 - {year}</a>
							</font>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</center>
</div>
<!-- END LEFTMENU -->