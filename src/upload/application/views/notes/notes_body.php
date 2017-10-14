<form action="" method="POST">
  <table width=519>
    <tr>
      <td class=c colspan=4>{nt_notes}</td>
    </tr>
    <tr>
      <th colspan=4><a href="game.php?page=notes&a=1">{nt_create_new_note}</a></th>
    </tr>
    <tr>
      <td class="c">&nbsp;</td>
      <td class="c">{nt_date_note}</td>
      <td class="c">{nt_subject_note}</td>
      <td class="c">{nt_size_note}</td>
    </tr>

    {BODY_LIST}

<tr>
      <td colspan=4><input value="{nt_dlte_note}" type="submit"></td>
    </tr>
  </table>
</form>