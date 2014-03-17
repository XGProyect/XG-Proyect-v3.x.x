<script type="text/javascript">$(function () {$('#checkall').click(function () {$(this).parents('table:eq(0)').find(':checkbox').attr('checked', this.checked);});});</script>
<h2>{al_alliance_members}</h2>
{alert_info}
<form name="save_ranks" method="post" action="">
<table width="100%" class="table table-bordered table-hover table-condensed">
	<tr>
		<th colspan="11">{al_configure_ranks}</th>
	</tr>
	<tr>
		<td class="align_center"><input type="checkbox" name="checkall" id="checkall"></td>
		<th>{al_alliance_username}</th>
		<th>{al_alliance_pending_request}</th>
		<th>{al_alliance_request_text}</th>
		<th>{al_inscription_date}</th>
		<th>{al_alliance_member_rank}</th>
	</tr>
	{members_table}
	<tr>
		<td colspan="11">
			<div align="center">
				<input type="submit" name="delete_ranks" value="{al_delete_members}" class="btn btn-primary">
			</div>
		</td>
	</tr>
</table>
</form>