<script language="JavaScript">
function f(target_url, win_name) {
var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=800,height=600,top=0,left=0');
new_win.focus();
}
</script>
<br />
<div id="content">
	<form action="game.php?page=messages" method="post">
		<table width="519">
			<table>
				<tr>
					<td>
						<input name="messages" value="1" type="hidden">
						<table width="519">
							<tr>
								<td class="c" colspan="4">{mg_title}</td></tr><tr>
								<th>{mg_action}</th>
								<th>{mg_date}</th>
								<th>{mg_from}</th>
								<th>{mg_subject}</th>
							</tr>
								{message_list}
							<tr>
								<th colspan="4">
									&nbsp;
								</th>
							</tr>
							<tr>
								<th colspan="4">
									<select id="deletemessages" name="deletemessages">
										<option value="deletemarked">{mg_delete_marked}</option>
										<option value="deleteunmarked">{mg_delete_unmarked}</option>
										<option value="deleteall">{mg_delete_all}</option>
									</select>
									<input value="{mg_confirm_action}" type="submit">
								</th>
							</tr>
							<tr>
								<td colspan="4"></td>
							</tr>
						</table>
						<table width="100%">
							<tr>
								<td class="c">{mg_operators}</td>
							</tr>
								{show_operators}
						</table>
					</td>
				</tr>
			</table>
		</table>
	</form>
</div>