<br />
<div id="content">
    <form action="" method="post">
     <table width="519">
      <tr>
       <td class="c">{sh_searcg_in_the_universe}</td>
      </tr>
      <tr>
       <th>
        <select name="type">
         <option value="playername"{type_playername}>{sh_player_name}</option>
         <option value="planetname"{type_planetname}>{sh_planet_name}</option>
         <option value="allytag"{type_allytag}>{sh_alliance_tag}</option>
         <option value="allyname"{type_allyname}>{sh_alliance_name}</option>
        </select>
        &nbsp;&nbsp;
        <input type="text" name="searchtext" value="{searchtext}"/>
        &nbsp;&nbsp;
    
        <input type="submit" value="{sh_search}" />
       </th>
      </tr>
    </table>
    </form>
    {search_results}