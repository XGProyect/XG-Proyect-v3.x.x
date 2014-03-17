        <form action="" method="POST">
        <input type="hidden" value="{federation_invited}" name="federation_invited">
        <table width="519" border="0" cellpadding="0" cellspacing="1">
        	<tr>
        		<td class="c" colspan="3">{fl_fleet_union}</td>
        	</tr>
        	<tr>
        		<th colspan="3">
        			<div style="text-align:left">{fl_fleet_union_name} <input name="name_acs" type="text" id="txt_name_acs" value="{acs_code}" maxlength="20"/> <a href="#" onclick="document.getElementById('search').style.visibility='visible';">{fl_search_user}</a></div>
        		</th>
        	</tr>
        	<tr>
        		<th width="150px">
        			{fl_friends_list}
        			<br />
					<select size="5" style="width:150px;" name="friends_list">
						{friends}
					</select>
        		</th>
        		<th>
	        		<a href="#"><input type="submit" value="Invitar >>" name="add"></a>
	        		<a href="#"><input type="submit" value="<< Expulsar" name="remove"></a>
        		</th>
        		<th width="150px">
        			{fl_union_members} ({invited_count}/5)
        			<br />
					<select size="5" style="width:150px;" name="members_list">
						{invited_members}
					</select>
        		</th>
        	</tr>
        	<tr>
        		<th colspan="3">
        			{add_user_message}
        			<div id="search" style="visibility:hidden;text-align:left;">
        				{fl_search_user} <input name="addtogroup" type="text" /> <input type="submit" value="{fl_search_user_btn}" name="search_user"/>
        			</div>
        		</th>
        	</tr>
        	<tr>
        		<th colspan="3">
        			<div style="text-align:right;"><input type="submit" value="{fl_save_all}" name="save_acs"/></div>
        		</th>
        	</tr>
        </table>
        </form>