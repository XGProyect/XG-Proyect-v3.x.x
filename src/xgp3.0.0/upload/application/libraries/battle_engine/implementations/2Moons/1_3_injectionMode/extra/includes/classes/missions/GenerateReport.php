<?php

/**
 *  2Moons
 *  Copyright (C) 2011  Slaver
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package 2Moons
 * @author Slaver <slaver7@gmail.com>
 * @copyright 2009 Lucky <douglas@crockford.com> (XGProyecto)
 * @copyright 2011 Slaver <slaver7@gmail.com> (Fork/2Moons)
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.3 (2011-01-21)
 * @link http://code.google.com/p/2moons/
 */

function GenerateReport($RESULT, $INFO)
{
	$html 		= '"<div style=\"width:100%;text-align:center\">';
	$html	   .= $INFO['moon']['des'] == 1 ? '".$LNG["sys_destruc_title"]." '.date(TDFORMAT, $INFO['fleet_start_time']).'. <br><br>' : '".$LNG["sys_attack_title"]." '.date(TDFORMAT, $INFO['fleet_start_time']).'. <br><br>';
		
	$round_no 	= 1;
	$des		= array('att' => array(), 'def' => array());
	
	foreach($RESULT['rw'] as $round => $data1)
	{
		//if($round_no > 6)
		//	break;

		$html 		.= '".$LNG["sys_attack_round"]." '.$round_no.' :<br><br>';
		$attackers1 = $data1['attackers'];
		$attackers2 = $data1['infoA'];
		$attackers3 = $data1['attackA'];
		$defenders1 = $data1['defenders'];
		$defenders2 = $data1['infoD'];
		$defenders3 = $data1['defenseA'];
		$coord4 	= 0;
		$coord5 	= 0;
		$coord6 	= 0;
		$html		.= '<table><tr>';
		foreach($attackers1 as $fleet_id1 => $data2)
		{
			$name 	= $data2['user']['username'];
			$coord1 = $data2['fleet']['fleet_start_galaxy'];
			$coord2 = $data2['fleet']['fleet_start_system'];
			$coord3 = $data2['fleet']['fleet_start_planet'];
			$coord4 = $data2['fleet']['fleet_end_galaxy'];
			$coord5 = $data2['fleet']['fleet_end_system'];
			$coord6 = $data2['fleet']['fleet_end_planet'];
			$weap 	= $data2['techs'][0] * 100;
			$shie 	= $data2['techs'][1] * 100;
			$armr 	= $data2['techs'][2] * 100;

			$fl_info1  	= '<td class=\"transparent\"><table><tr><td>".$LNG["sys_attack_attacker_pos"]." '.$name.' (['.$coord1.':'.$coord2.':'.$coord3.'])<br>".$LNG["sys_ship_weapon"]." '.$weap.'% - ".$LNG["sys_ship_shield"]." '.$shie.'% - ".$LNG["sys_ship_armour"]." '.$armr.'%';
			$table1  	= '<table width=\'100%\'>';

			if (array_sum($data2['detail']) != 0)
			{
				$ships1  = '<tr><td class=\"transparent\">".$LNG["sys_ship_type"]."</td>';
				$count1  = '<tr><td class=\"transparent\">".$LNG["sys_ship_count"]."</td>';

				foreach($data2['detail'] as $ship_id1 => $ship_count1)
				{
					if ($ship_count1 <= 0)
						continue;

					$ships1 .= '<td class=\"transparent\">".$LNG["tech"]['.$ship_id1.']."</td>';
					$count1 .= '<td class=\"transparent\">'.pretty_number($ship_count1).'</td>';
				}

				$ships1 .= '</tr>';
				$count1 .= '</tr>';
			}
			else
			{
				$des['att'][] = 1;
				$ships1 = '<tr><td class=\"transparent\"><br><br>". $LNG["sys_destroyed"]."<br></td></tr>';
				$count1 = '';
			}

			$info_part1[$fleet_id1] = $fl_info1.$table1.$ships1.$count1;
		}

		foreach($attackers2 as $fleet_id2 => $data3)
		{
			$weap1		= '<tr><td class=\"transparent\">".$LNG["sys_ship_weapon"]."</td>';
			$shields1	= '<tr><td class=\"transparent\">".$LNG["sys_ship_shield"]."</td>';
			$armour1	= '<tr><td class=\"transparent\">".$LNG["sys_ship_armour"]."</td>';
			foreach($data3 as $ship_id2 => $ship_points1)
			{
				if($ship_points1['shield'] <= 0)
					continue;

				$weap1 		.= '<td class=\"transparent\">'.pretty_number($ship_points1['att']).'</td>';
				$shields1 	.= '<td class=\"transparent\">'.pretty_number($ship_points1['def']).'</td>';
				$armour1 	.= '<td class=\"transparent\">'.pretty_number($ship_points1['shield']).'</td>';
			}

			$weap1 		.= '</tr>';
			$shields1 	.= '</tr>';
			$armour1 	.= '</tr>';
			$endtable1 	= '</table></td></tr></table></td>';

			$info_part2[$fleet_id2] = $weap1.$shields1.$armour1.$endtable1;

			if (array_sum($attackers1[$fleet_id2]['detail']) != 0)
			{
				$html .= $info_part1[$fleet_id2].$info_part2[$fleet_id2];
			}
			else
			{
				$html .= $info_part1[$fleet_id2];
				$html .= '</table></td></tr></table><br><br>';
			}
		}
		
		$html .= '</tr></table>';
		$html .= '<br><br>';
		$html .= '<table><tr>';
		foreach($defenders1 as $fleet_id1 => $data2)
		{
			$name = $data2['user']['username'];
			$weap 	= $data2['techs'][0] * 100;
			$shie 	= $data2['techs'][1] * 100;
			$armr 	= $data2['techs'][2] * 100;

			$fl_info1	= '<td class=\"transparent\"><table><tr><td>".$LNG["sys_attack_defender_pos"]." '.$name.' (['.$coord4.':'.$coord5.':'.$coord6.'])<br>".$LNG["sys_ship_weapon"]." '.$weap.'% - ".$LNG["sys_ship_shield"]." '.$shie.'% - ".$LNG["sys_ship_armour"]." '.$armr.'%';
			$table1  	= '<table border=\'1\' align=\'center\' width=\'100%\'>';

			if (array_sum($data2['def']) != 0)
			{
				$ships1  = '<tr><td class=\"transparent\">".$LNG["sys_ship_type"]."</td>';
				$count1  = '<tr><td class=\"transparent\">".$LNG["sys_ship_count"]."</td>';

				foreach($data2['def'] as $ship_id1 => $ship_count1)
				{
					if ($ship_count1 == 0)
						continue;

					$ships1 .= '<td class=\"transparent\">".$LNG["tech"]['.$ship_id1.']."</td>';
					$count1 .= '<td class=\"transparent\">'.pretty_number($ship_count1).'</td>';
				}

				$ships1 .= '</tr>';
				$count1 .= '</tr>';
			}
			else
			{
				$des['def'][] = 1;
				$ships1 = '<tr><td class=\"transparent\"><br><br>".$LNG["sys_destroyed"]."<br></td></tr>';
				$count1 = '';
			}

			$info_part1[$fleet_id1] = $fl_info1.$table1.$ships1.$count1;
		}

		foreach($defenders2 as $fleet_id2 => $data3)
		{
			$weap1  	= '<tr><td class=\"transparent\">".$LNG["sys_ship_weapon"]."</td>';
			$shields1  	= '<tr><td class=\"transparent\">".$LNG["sys_ship_shield"]."</td>';
			$armour1  	= '<tr><td class=\"transparent\">".$LNG["sys_ship_armour"]."</td>';

			foreach( $data3 as $ship_id2 => $ship_points1)
			{
				if($ship_points1['shield'] <= 0)
					continue;

				$weap1 		.= '<td class=\"transparent\">'.pretty_number($ship_points1['att']).'</td>';
				$shields1 	.= '<td class=\"transparent\">'.pretty_number($ship_points1['def']).'</td>';
				$armour1 	.= '<td class=\"transparent\">'.pretty_number($ship_points1['shield']).'</td>';
			}

			$weap1 		.= '</tr>';
			$shields1 	.= '</tr>';
			$armour1 	.= '</tr>';
			$endtable1 	= '</table></td></tr></table>';

			$info_part2[$fleet_id2] = $weap1.$shields1.$armour1.$endtable1;

			if (array_sum($defenders1[$fleet_id2]['def']) != 0)
			{
				$html .= $info_part1[$fleet_id2].$info_part2[$fleet_id2];
				$html .= '<br><br>';
			}
			else
			{
				$html .= $info_part1[$fleet_id2];
				$html .= '</table></td></tr></table><br><br>';
			}
		}
				
		$html .= '</tr></table>';
		//if (array_sum($des['att']) == count($attackers2) || array_sum($des['def']) == count($defenders2)) break;
        if (!isset($RESULT['rw'][$round +1])) break; // fix: show remain ships without shotting enemy info
		
        $attackAmount = number_format($data1['attackAmount'],0,',',"'");
        $defendAmount = number_format($data1['defendAmount'],0,',',"'");
        $attack = number_format($data1['attack'],0,',',"'");
        $defense = number_format($data1['defense'],0,',',"'");
        $defShield = number_format($data1['defShield'],0,',',"'");
        $attackShield = number_format($data1['attackShield'],0,',',"'");
        
        $a1 = 'str_replace(array("{count}","{damage}"),array("'.$attackAmount.'","'.$attack.'"),$LNG["fleet_attack_1"])';
        $a2 = 'str_replace(array("{damage}"),array("'.$defShield.'"),$LNG["fleet_attack_2"])';
        
        $d1 = 'str_replace(array("{count}","{damage}"),array("'.$defendAmount.'","'.$defense.'"),$LNG["fleet_defs_1"])';
        $d2 = 'str_replace(array("{damage}"),array("'.$attackShield.'"),$LNG["fleet_defs_2"])';
        		
		$html .= "\".$a1.\"<br>";
        $html .= "\".$a2.\"<br>";
        $html .= '<br>';
        $html .= "\".$d1.\"<br>";
        $html .= "\".$d2.\"<br>";
		$html .= '<br>';
        $html .= '<br>';
        $html .= '<br>';
		$round_no++;			
	}

	switch($RESULT['won'])
	{
		case 'r':
			$result1  = '".$LNG["sys_defender_won"]."<br>';
		break;
		case 'a':
			$result1  = '".$LNG["sys_attacker_won"]."<br>".$LNG["sys_stealed_ressources"]." '.pretty_number($INFO['steal']['metal']).' ".$LNG["Metal"].", '.pretty_number($INFO['steal']['crystal']).' ".$LNG["Crystal"]." ".$LNG["and"]." '.pretty_number($INFO['steal']['deuterium']).' ".$LNG["Deuterium"]."<br>';
		break;
		default:
			$result1  = '".$LNG["sys_both_won"].".<br>';
		break;
	}

	$html .= '<br><br>'.$result1.'<br>';
	$html .= '".$LNG["sys_attacker_lostunits"]." '.pretty_number($RESULT['lost']['att']).' ".$LNG["sys_units"]."<br>';
	$html .= '".$LNG["sys_defender_lostunits"]." '.pretty_number($RESULT['lost']['def']).' ".$LNG["sys_units"]."<br>';
	$html .= '".$LNG["debree_field_1"]." '.pretty_number($RESULT['debree']['att'][0] + $RESULT['debree']['def'][0]).' ".$LNG["Metal"]." ".$LNG["sys_and"]." '.pretty_number($RESULT['debree']['att'][1] + $RESULT['debree']['def'][1]).' ".$LNG["Crystal"]." ".$LNG["debree_field_2"]."<br><br>';
		
	if($INFO['moon']['des'] == 1) {
		$html .= '".sprintf($LNG["sys_destruc_mess"], "'.$INFO['start_galaxy'].'", "'.$INFO['start_system'].'", "'.$INFO['start_planet'].'", "'.$INFO['end_galaxy'].'", "'.$INFO['end_system'].'", "'.$INFO['end_planet'].'")."<br>';	
		if($INFO['moon']['desfail'] == 1) {
			$html .= '".$LNG["sys_destruc_stop"]."<br>';
		} else {
			$html .= '".sprintf($LNG["sys_destruc_lune"], "'.$INFO['moon']['chance'].'")."<br>".$LNG["sys_destruc_mess1"]."';
			$html .= $INFO['moon']['desfail'] == 0 ? '".$LNG["sys_destruc_reussi"]."' : '".$LNG["sys_destruc_null"]."';			
			$html .= '<br>".sprintf($LNG["sys_destruc_rip"], "'.$INFO['moon']['chance2'].'")."';
			if($INFO['moon']['fleetfail'] == 1) {
				$html .= '<br>".$LNG["sys_destruc_echec"]."';
			}			
		}
	} else {
		$html .= '".$LNG["sys_moonproba"]." '.$INFO['moon']['chance'].' %<br>';
		if(!empty($INFO['moon']['name']))
		{
			$html .= '".sprintf($LNG["sys_moonbuilt"], "'.$INFO['moon']['name'].'", "'.$INFO['end_galaxy'].'", "'.$INFO['end_system'].'", "'.$INFO['end_planet'].'")."';
		}
	}
	
	if(isset($INFO['moon']['battlesim'])) {
		$html .= $INFO['moon']['battlesim'];
	}
	$html .= '</div><script type=\"text/javascript\">RaportInfo = '.addcslashes(json_encode(array($RESULT['won'], ($RESULT['lost']['att'] + $RESULT['lost']['def']), $RESULT['debree']['att'][0], $RESULT['debree']['def'][0], $INFO['attvsdef'])), '"').';</script>"';
	return $html;
}
	
?>