<div id="content" style="top: 118px; height: 656px;">
<center>
<table class="header">
<tbody><tr class="header">

 <td>
  <table width="519">
   <form action="{form_submit}" method="POST">
    <tbody><tr>
    <td colspan="4" class="c">{mg_title}</td>
    </tr>

    <tr>
    <th>{mg_show_title}</th>
    <th colspan="2">{mg_type_title}</th>
    <th>{mg_amount_title} / {mg_unread_title}</th>
    </tr>
	{message_type_rows}
	{message_list}





        <tr>
     <th colspan="4">
      {delete_options}
      <input type="submit" value="{mg_confirm_action}">
     </th>
    </tr>

      <input type="hidden" name="messages" value="1" />
   </form>


      <form action="game.php?page=messages" method="POST">
    <tr height="20"> </tr>
    <tr>
      <td colspan="4" class="c">{mg_address_book}</td>
    </tr>
    <tr>
      <th>{mg_show_title}</th>
     <th colspan="2">{mg_type_title}</th>
     <th>{mg_amount_title}</th>
    </tr>
    <tr>
     <th><input type="checkbox" name="owncontactsopen"{owncontactsopen}></th>
     <th colspan="2">{mg_friends_list} </th>
     <th>{buddys_count}</th>
    </tr>
    {mg_ab_friends}
    <tr>
     <th><input type="checkbox" name="ownallyopen"{ownallyopen}></th>
     <th colspan="2">{mg_alliance}</th>
     <th>{alliance_count}</th>
    </tr>
    {mg_ab_members}
    <tr>
     <th><input type="checkbox" name="gameoperatorsopen"{gameoperatorsopen}></th>
     <th colspan="2">{mg_operators}</th>
     <th>{operators_count}</th>
    </tr>
    {mg_ab_operators}

        <tr>
     <th colspan="4">
      <input type="hidden" name="addressbook" value="1">
      <input type="submit" value="{mg_confirm_action}">
     </th>
    </tr>
      </form>


   <form action="game.php?page=messages" method="POST">
    <tr height="20"> </tr>
    <tr>
    <td colspan="4" class="c">{mg_notes}</td>
    </tr>
    <tr>
    <th colspan="2">{mg_show_title}</th>
    <th colspan="2">{mg_amount_title}</th>
    </tr>
    <tr>
     <th colspan="2"><input type="checkbox" name="noticesopen"{noticesopen}></th>
     <th colspan="2">{notes_count}</th>
    </tr>
    {mg_notes_rows}

    <tr>
     <th colspan="4">
      <input type="hidden" name="notices" value="1">
      <input type="submit" value="{mg_confirm_action}">
     </th>
    </tr>
</form>

      </tbody></table>
  </td>
 </tr>
 </tbody></table>
<br><br><br><br>
</center>
</div>