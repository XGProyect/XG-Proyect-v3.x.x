<tr>
	<td>
		{message_id}
		<input type="checkbox" name="delete_message[{message_id}]">
	</td>
	<td>{sender}</td>
	<td>{receiver}</td>
	<td>{message_time}</td>
	<td>{message_type}</td>
	<td>{message_from}</td>
	<td>{message_subject}</td>
</tr>
<tr>
	<td colspan="7" bgcolor="#bce8f1">
		<div class="toggle{message_id}">
			<i class="icon-info-sign"></i> <span class="small_font">{mg_show_hide}</span>
		</div>
	</td>
</tr>
<tr>
	<td colspan="7">
		<div class="div{message_id}">
			{message_text}
		</div>
	</td>
</tr>
<script type="text/javascript">$('.toggle{message_id}').click(function(){$('.div{message_id}').slideToggle('slow');});</script>