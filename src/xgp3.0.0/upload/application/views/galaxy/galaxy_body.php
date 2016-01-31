<div id="content">
<center>

  <script  language="JavaScript">
  function galaxy_submit(value) {
      document.getElementById('auto').name = value;
      document.getElementById('galaxy_form').submit();
  }

  function fenster(target_url,win_name) {
  var new_win = window.open(target_url,win_name,'scrollbars=yes,menubar=no,top=0,left=0,toolbar=no,width=550,height=280,resizable=yes');
  new_win.focus();
  }


  var IE = document.all?true:false;

  function mouseX(e){
  	if (IE) { // grab the x-y pos.s if browser is IE
		return event.clientX + document.body.scrollLeft;
	} else {
		return e.pageX
	}
  }
  function mouseY(e) {
  	if (IE) { // grab the x-y pos.s if browser is IE
		return event.clientY + document.body.scrollTop;
	}else {
		return e.pageY;
	}
  }

  </script>
  <script language="JavaScript" src="{js_path}tw-sack-min.js"></script>
  <script type="text/javascript">
  var ajax = new sack();
  var strInfo = "";

  function whenLoading(){
      //var e = document.getElementById('fleetstatus');
      //e.innerHTML = "Sende Flotte...";
  }

  function whenLoaded(){
      //    var e = document.getElementById('fleetstatus');
      // e.innerHTML = "Flotte gesendet...";
  }

  function whenInteractive(){
      //var e = document.getElementById('fleetstatus');
      // e.innerHTML = "Erhalte Daten...";
  }

  /*
  We can overwrite functions of the sack object easily. :-)
  This function will replace the sack internal function runResponse(),
  which normally evaluates the xml return value via eval(this.response).
  */
  function whenResponse(){

      /*
      *
      *  600   OK
      *  601   no planet exists there
      *  602   no moon exists there
      *  603   player is in noob protection
      *  604   player is too strong
      *  605   player is in u-mode
      *  610   not enough espionage probes, sending x (parameter is the second return value)
      *  611   no espionage probes, nothing send
      *  612   no fleet slots free, nothing send
      *  613   not enough deuterium to send a probe
      *
      */
      // the first three digit long return value
      retVals = this.response.split(" ");
      // and the other content of the response
      // but since we only got it if we can send some but not all probes
      // theres no need to complicate things with better parsing

      // each case gets a different table entry, no language file used :P
      switch(parseInt(retVals[0])) {
          case 600:
          addToTable("{gl_success}", "success");
                    break;
          case 601:
          addToTable("{gl_error}", "error");
          break;
          case 602:
          addToTable("{gl_no_moon}", "error");
          break;
          case 603:
          addToTable("{gl_noob_protection}", "error");
          break;
          case 604:
          addToTable("{gl_too_strong}", "error");
          break;
          case 605:
          addToTable("{gl_vacation_mode}", "vacation");
          break;
          case 610:
          addToTable("{gl_only_amount_ships_1} "+retVals[1]+" {gl_only_amount_ships_2}", "notice");
          break;
          case 611:
          addToTable("{gl_no_ships}", "error");
          break;
          case 612:
          addToTable("{gl_no_slots}", "error");
          break;
          case 613:
          addToTable("{gl_no_deuterium}", "error");
          break;
          case 614:
          addToTable("{gl_no_planet}", "error");
          break;
          case 615:
          addToTable("{gl_not_enought_storage}", "error");
          break;
          case 616:
          addToTable("{gl_multi_alarm}", "error");
          break;
      }
  }

  function doit(order, galaxy, system, planet, planettype, shipcount){
      strInfo = "{gl_send} "+shipcount+" {gl_ship}"+(shipcount!=1?"{gl_ships}":"")+" {gl_to}  "+galaxy+":"+system+":"+planet+"...";
      ajax.requestFile = "game.php?page=galaxy&fleet=true&action=send";

      // no longer needed, since we don't want to write the cryptic
      // response somewhere into the output html
      //ajax.element = 'fleetstatus';
      //ajax.onLoading = whenLoading;
      //ajax.onLoaded = whenLoaded;
      //ajax.onInteractive = whenInteractive;

      // added, overwrite the function runResponse with our own and
      // turn on its execute flag
      ajax.runResponse = whenResponse;
      ajax.execute = true;

      ajax.setVar("session", "");
      ajax.setVar("order", order);
      ajax.setVar("galaxy", galaxy);
      ajax.setVar("system", system);
      ajax.setVar("planet", planet);
      ajax.setVar("planettype", planettype);
      ajax.setVar("shipcount", shipcount);
      ajax.setVar("speed", 10);
      ajax.setVar("reply", "short");
      ajax.runAJAX();
  }

  /*
  * This function will manage the table we use to output up to three lines of
  * actions the user did. If there is no action, the tr with id 'fleetstatusrow'
  * will be hidden (display: none;) - if we want to output a line, its display
  * value is cleaned and therefore its visible. If there are more than 2 lines
  * we want to remove the first row to restrict the history to not more than
  * 3 entries. After using the object function of the table we fill the newly
  * created row with text. Let the browser do the parsing work. :D
  */
  function addToTable(strDataResult, strClass) {
      var e = document.getElementById('fleetstatusrow');
      var e2 = document.getElementById('fleetstatustable');
      // make the table row visible
      e.style.display = '';
      if(e2.rows.length > 2) {
          e2.deleteRow(2);
      }
      var row = e2.insertRow('test');
      var td1 = document.createElement("td");
      var td1text = document.createTextNode(strInfo);
      td1.appendChild(td1text);
      var td2 = document.createElement("td");
      var span = document.createElement("span");
      var spantext = document.createTextNode(strDataResult);
      var spanclass = document.createAttribute("class");
      spanclass.nodeValue = strClass;
      span.setAttributeNode(spanclass);
      span.appendChild(spantext);
      td2.appendChild(span);
      row.appendChild(td1);
      row.appendChild(td2);

  }

  function changeSlots(slotsInUse) {
      var e = document.getElementById('slots');
      e.innerHTML = slotsInUse;
  }

  function setShips(ship, count) {
      var e = document.getElementById(ship);
      e.innerHTML = count;
  }

  function cursorevent(evt) {
  	  evt = (evt) ? evt : ((event) ? event : null);
  	  if(evt.keyCode == 37) {
          galaxy_submit('systemLeft');
      }

      if(evt.keyCode == 39) {
          galaxy_submit('systemRight');
      }

      if(evt.keyCode == 38) {
          galaxy_submit('galaxyRight');
      }

      if(evt.keyCode == 40) {
          galaxy_submit('galaxyLeft');
      }

  }
  document.onkeydown = cursorevent;
</script>
<body onUnload="">
	<form action="game.php?page=galaxy&mode=1" method="post" id="galaxy_form">
	<input type="hidden" id="auto" value="dr" >
	<table border="1" class="header">
	  <tr class="header">
	    <td class="header">
	      <table class="header">
	        <tr class="header">
	         <td class="c" colspan="3">{gl_galaxy}</td>
	        </tr>
	        <tr class="header">
	          <td class="l"><input type="button" name="galaxyLeft" value="&lt;-" onClick="galaxy_submit('galaxyLeft')"></td>
	          <td class="l"><input type="text" name="galaxy" value="{galaxy}" size="5" maxlength="3" tabindex="1"></td>
	          <td class="l"><input type="button" name="galaxyRight" value="-&gt;" onClick="galaxy_submit('galaxyRight')"></td>
	        </tr>
	       </table>
	      </td>
	      <td class="header">
	       <table class="header">
	        <tr class="header">
	         <td class="c" colspan="3">{gl_solar_system}</td>
	        </tr>
	         <tr class="header">
	          <td class="l"><input type="button" name="systemLeft" value="&lt;-" onClick="galaxy_submit('systemLeft')"></td>
	          <td class="l"><input type="text" name="system" value="{system}" size="5" maxlength="3" tabindex="2"></td>
	          <td class="l"><input type="button" name="systemRight" value="-&gt;" onClick="galaxy_submit('systemRight')"></td>
	         </tr>
	        </table>
	       </td>
	      </tr>
	      <tr class="header">
	        <td class="header" style="background-color:transparent;border:0px;" colspan="2" align="center">
	        	<input type="submit" value="{gl_show}">
	        </td>
	      </tr>
	</table>
	</form>
    {mip}
	<table width="569">
		<tr>
		    <td class="c" colspan="8">{gl_solar_system} {galaxy}:{system}</td>
		</tr>
		<tr>
		    <td class="c">{gl_pos}</td>
		    <td class="c">{gl_planet}</td>
		    <td class="c">{gl_name_activity}</td>
		    <td class="c">{gl_moon}</td>
		    <td class="c">{gl_debris}</td>
		    <td class="c">{gl_player_estate}</td>
		    <td class="c">{gl_alliance}</td>
		    <td class="c">{gl_actions}</td>
		</tr>
        {galaxyrows}
		<tr>
		    <th width="30">16</th>
		    <th colspan="7">
		    <a href="game.php?page=fleet1&galaxy={galaxy}&amp;system={system}&amp;planet=16&amp;planettype=1&amp;target_mission=15">{gl_out_space}</a>
		    </th>
		</tr>
		<tr>
		    <td class=c colspan="6">( {planetcount} )</td>
		    <td class=c colspan="2">
		        <a href="#" style="cursor: pointer;" onmouseover='return overlib("<table width=240><tr><td class=c colspan=2>{gl_legend}</td></tr><tr><td width=220>{gl_strong_player}</td><td><span class=strong>{gl_s}</span></td></tr><tr><td width=220>{gl_week_player}</td><td><span class=noob>{gl_w}</span></td></tr><tr><td width=220>{gl_vacation}</td><td><span class=vacation>{gl_v}</span></td></tr><tr><td width=220>{gl_banned}</td><td><span class=banned>{gl_b}</span></td></tr><tr><td width=220>{gl_inactive_seven}</td><td><span class=inactive>{gl_i}</span></td></tr><tr><td width=220>{gl_inactive_twentyeight}</td><td><span class=longinactive>{gl_I}</span></td></tr></table>", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -150, OFFSETY, -150 );' onmouseout='return nd();'>{gl_legend}</a>
		    </td>
		</tr>
		<tr>
		    <td class=c colspan="3"><span id="missiles">{currentmip}</span> {gl_avaible_missiles}</td>
		    <td class=c colspan="3"><span id="slots">{maxfleetcount}</span>/{fleetmax} {gl_fleets}</td>
		    <td class=c colspan="2">
				<span id="recyclers">{recyclers}</span> {gl_avaible_recyclers}<br>
				<span id="probes">{spyprobes}</span> {gl_avaible_spyprobes}</td>
		</tr>
		<tr style="display: none;" id="fleetstatusrow">
		    <th class=c colspan="8">
				<table style="font-weight: bold" width="100%" id="fleetstatustable">
					<!-- will be filled with content later on while processing ajax replys -->
				</table>
			</th>
		</tr>
	</table>
</body>