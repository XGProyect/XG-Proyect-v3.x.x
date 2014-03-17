<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( 'location:../../' ) );

class Attack extends Missions
{
	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * method attack_mission
	 * param $fleet_row
	 * return the attack result
	*/
	public function attack_mission ( $fleet_row )
	{
		$targetPlanet = parent::$db->query_fetch ( "SELECT *
														FROM " . PLANETS . " AS p
														INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
														INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
														INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
														WHERE `planet_galaxy` = ". (int)$fleet_row['fleet_end_galaxy'] ." AND
																`planet_system` = ". (int)$fleet_row['fleet_end_system'] ." AND
																`planet_type` = ". (int)$fleet_row['fleet_end_type'] ." AND
																`planet_planet` = ". (int)$fleet_row['fleet_end_planet'] .";" );

		if ($fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] <= time())
		{
			if ($fleet_row['fleet_group'] > 0)
			{
				parent::$db->query ( "DELETE FROM " . ACS_FLEETS . "
										WHERE acs_fleet_id =".intval($fleet_row['fleet_group']) );

				parent::$db->query ( "UPDATE `" . FLEETS . "` SET
										`fleet_mess` = '1'
										WHERE `fleet_group` = " . $fleet_row['fleet_group'] );
			}
			else
			{
				parent::return_fleet ( $fleet_row['fleet_id'] );
			}

			$targetUser   = parent::$db->query_fetch ( 'SELECT u.*,
																r.research_energy_technology,
																pr.premium_officier_geologist,
																pr.premium_officier_engineer
															FROM ' . USERS . ' AS u
															INNER JOIN ' . RESEARCH . ' AS r ON r.research_user_id = u.user_id
															INNER JOIN ' . PREMIUM . ' AS pr ON pr.premium_user_id = u.user_id
															WHERE u.user_id = '.intval($targetPlanet['planet_user_id']) );

			UpdateResources_Lib::update_resource ( $targetUser, $targetPlanet, time() );

			$targetGalaxy = parent::$db->query_fetch ( 'SELECT `planet_id`
														FROM ' . PLANETS . '
														WHERE `planet_galaxy` = '. intval($fleet_row['fleet_end_galaxy']) .' AND
																`planet_system` = '. intval($fleet_row['fleet_end_system']) .' AND
																`planet_planet` = '. intval($fleet_row['fleet_end_planet']) .' AND
																`planet_type` = 3;' );

			$targetUser   = parent::$db->query_fetch ( 'SELECT 	u.*,
																r.research_weapons_technology,
																r.research_shielding_technology,
																r.research_armour_technology,
																pr.premium_officier_technocrat
														FROM ' . USERS . ' AS u
														INNER JOIN ' . RESEARCH . ' AS r ON r.research_user_id = u.user_id
														INNER JOIN ' . PREMIUM . ' AS pr ON pr.premium_user_id = u.user_id
														WHERE u.user_id = '.intval($targetPlanet['planet_user_id']) );

			$TargetUserID = $targetUser['user_id'];
			$attackFleets = array();

			if ($fleet_row['fleet_group'] != 0)
			{
				$fleets = parent::$db->query ( 'SELECT *
												FROM ' . FLEETS . '
												WHERE fleet_group='.$fleet_row['fleet_group'] );

				if ( $fleets != NULL )
				{
					while ($fleet = parent::$db->fetch_assoc($fleets))
					{
						$attackFleets[$fleet['fleet_id']]['fleet'] = $fleet;
						$attackFleets[$fleet['fleet_id']]['user'] = parent::$db->query_fetch ( 'SELECT 	u.*,
																									r.research_weapons_technology,
																									r.research_shielding_technology,
																									r.research_armour_technology
																							FROM ' . USERS . ' AS u
																							INNER JOIN ' . RESEARCH . ' AS r ON r.research_user_id = u.user_id
																							WHERE user_id='.intval($fleet_row['fleet_owner']) );
						$attackFleets[$fleet['fleet_id']]['detail'] = array();
						$temp = explode(';', $fleet['fleet_array']);
						foreach ($temp as $temp2)
						{
							$temp2 = explode(',', $temp2);

							if ($temp2[0] < 100) continue;

							if (!isset($attackFleets[$fleet['fleet_id']]['detail'][$temp2[0]]))
								$attackFleets[$fleet['fleet_id']]['detail'][$temp2[0]] = 0;

							$attackFleets[$fleet['fleet_id']]['detail'][$temp2[0]] += $temp2[1];
						}
					}
				}
			}
			else
			{
				$attackFleets[$fleet_row['fleet_id']]['fleet'] = $fleet_row;
				$attackFleets[$fleet_row['fleet_id']]['user'] = parent::$db->query_fetch ( 'SELECT 	u.*,
																									r.research_weapons_technology,
																									r.research_shielding_technology,
																									r.research_armour_technology
																							FROM ' . USERS . ' AS u
																							INNER JOIN ' . RESEARCH . ' AS r ON r.research_user_id = u.user_id
																							WHERE user_id='.intval($fleet_row['fleet_owner']) );

				$attackFleets[$fleet_row['fleet_id']]['detail'] = array();
				$temp = explode(';', $fleet_row['fleet_array']);
				foreach ($temp as $temp2)
				{
					$temp2 = explode(',', $temp2);

					if ($temp2[0] < 100) continue;

					if (!isset($attackFleets[$fleet_row['fleet_id']]['detail'][$temp2[0]]))
						$attackFleets[$fleet_row['fleet_id']]['detail'][$temp2[0]] = 0;

					$attackFleets[$fleet_row['fleet_id']]['detail'][$temp2[0]] += $temp2[1];
				}
			}
			$defense = array();

			$def = parent::$db->query_fetch ( 'SELECT *
												FROM ' . FLEETS . '
												WHERE `fleet_end_galaxy` = '. intval($fleet_row['fleet_end_galaxy']) .' AND
														`fleet_end_system` = '. intval($fleet_row['fleet_end_system']) .' AND
														`fleet_end_type` = '. intval($fleet_row['fleet_end_type']) .' AND
														`fleet_end_planet` = '. intval($fleet_row['fleet_end_planet']) .' AND
														fleet_start_time<'.time().' AND
														fleet_end_stay>='.time() );

			if ( $def != NULL )
			{
				while ($defRow = parent::$db->fetch_assoc($def))
				{
					$defRowDef = explode(';', $defRow['fleet_array']);
					foreach ($defRowDef as $Element)
					{
						$Element = explode(',', $Element);

						if ($Element[0] < 100) continue;

						if (!isset($defense[$defRow['fleet_id']]['def'][$Element[0]]))
							$defense[$defRow['fleet_id']][$Element[0]] = 0;

						$defense[$defRow['fleet_id']]['def'][$Element[0]] += $Element[1];
						$defense[$defRow['fleet_id']]['user'] = parent::$db->query_fetch ( 'SELECT *
																								FROM ' . USERS . '
																								WHERE user_id = '.intval($defRow['fleet_owner']) );
					}
				}
			}

			$defense[0]['def'] = array();
			$defense[0]['user'] = $targetUser;
			for ($i = 200; $i < 500; $i++)
			{
				if (isset($this->_resource[$i]) && isset($targetPlanet[$this->_resource[$i]]))
				{
					$defense[0]['def'][$i] = $targetPlanet[$this->_resource[$i]];
				}
			}
			$start 		= microtime(TRUE);
			$result 	= $this->acs_attack($attackFleets, $defense);
			$totaltime 	= microtime(TRUE) - $start;

			parent::$db->query ( "UPDATE " . PLANETS . " SET
									`planet_invisible_start_time` = '".time()."',
									`planet_debris_metal` = `planet_debris_metal` +'".($result['debree']['att'][0]+$result['debree']['def'][0]) . "',
									`planet_debris_crystal` = `planet_debris_crystal` + '" .($result['debree']['att'][1]+$result['debree']['def'][1]). "'
									WHERE `planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
											`planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
											`planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
											`planet_type` = 1
									LIMIT 1;" );

			$totalDebree = $result['debree']['def'][0] + $result['debree']['def'][1] + $result['debree']['att'][0] + $result['debree']['att'][1];

			$steal = array('metal' => 0, 'crystal' => 0, 'deuterium' => 0);

			if ($result['won'] == "a")
			{
				$steal = $this->acs_steal($attackFleets, $targetPlanet);
			}

			foreach ($attackFleets as $fleetID => $attacker)
			{
				$fleetArray = '';
				$totalCount = 0;
				foreach ($attacker['detail'] as $element => $amount)
				{
					if ($amount)
						$fleetArray .= $element.','.$amount.';';

					$totalCount += $amount;
				}

				if ($totalCount <= 0)
				{
					parent::remove_fleet ( $fleetID );
				}
				else
				{
					parent::$db->query ('UPDATE ' . FLEETS . ' SET
											fleet_array="'.substr($fleetArray, 0, -1).'",
											fleet_amount='.$totalCount.',
											fleet_mess=1
											WHERE fleet_id='.intval($fleetID) );
				}
			}

			foreach ($defense as $fleetID => $defender)
			{
				if ($fleetID != 0)
				{
					$fleetArray = '';
					$totalCount = 0;

					foreach ($defender['def'] as $element => $amount)
					{
						if ($amount) $fleetArray .= $element.','.$amount.';';
						$totalCount += $amount;
					}

					if ($totalCount <= 0)
					{
						parent::remove_fleet ( $fleetID );
					}
					else
					{
						parent::$db->query ( "UPDATE " . FLEETS . " SET
												fleet_array='$fleetArray',
												fleet_amount='$totalCount'
												WHERE fleet_id='$fleetID'" );
					}

				}
				else
				{
					$fleetArray = '';
					$totalCount = 0;

					foreach ($defender['def'] as $element => $amount)
					{
						$fleetArray .= '`'.$this->_resource[$element].'`='.$amount.', ';
					}


					parent::$db->query ( "UPDATE " . PLANETS . " AS p
											INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
											INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id` SET
											$fleetArray
											`planet_metal` = `planet_metal` - '". $steal['metal'] ."',
											`planet_crystal` = `planet_crystal` - '". $steal['crystal'] ."',
											`planet_deuterium` = `planet_deuterium` - '". $steal['deuterium'] ."'
											WHERE `planet_galaxy` = '". $fleet_row['fleet_end_galaxy'] ."' AND
													`planet_system` = '". $fleet_row['fleet_end_system'] ."' AND
													`planet_planet` = '". $fleet_row['fleet_end_planet'] ."' AND
													`planet_type` = '". $fleet_row['fleet_end_type'] ."';" );
				}
			}

			$FleetDebris      = $result['debree']['att'][0] + $result['debree']['def'][0] + $result['debree']['att'][1] + $result['debree']['def'][1];
			$StrAttackerUnits = sprintf ($this->_lang['sys_attacker_lostunits'], $result['lost']['att']);
			$StrDefenderUnits = sprintf ($this->_lang['sys_defender_lostunits'], $result['lost']['def']);
			$StrRuins         = sprintf ($this->_lang['sys_gcdrunits'], $result['debree']['def'][0] + $result['debree']['att'][0], $this->_lang['Metal'], $result['debree']['def'][1] + $result['debree']['att'][1], $this->_lang['Crystal']);
			$DebrisField      = $StrAttackerUnits ."<br />". $StrDefenderUnits ."<br />". $StrRuins;
			$MoonChance       = $FleetDebris / 100000;

			if($FleetDebris > 2000000)
			{
				$MoonChance = 20;
				$UserChance = mt_rand(1, 100);
				$ChanceMoon = sprintf ($this->_lang['sys_moonproba'], $MoonChance);
			}
			elseif($FleetDebris < 100000)
			{
				$UserChance = 0;
				$ChanceMoon = sprintf ($this->_lang['sys_moonproba'], $MoonChance);
			}
			elseif($FleetDebris >= 100000)
			{
				$UserChance = mt_rand(1, 100);
				$ChanceMoon = sprintf ($this->_lang['sys_moonproba'], $MoonChance);
			}

			if (($UserChance > 0) && ($UserChance <= $MoonChance) && ($targetGalaxy['id'] == 0))
			{
				include_once ( XGP_ROOT . 'application/libraries/Creator_Lib.php' );
				$creator	= new Creator_Lib();

				$TargetPlanetName = $creator->create_moon ( $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet'], $TargetUserID, $fleet_row['fleet_start_time'], '', $MoonChance );
				$GottenMoon       = sprintf ($this->_lang['sys_moonbuilt'], $TargetPlanetName, $fleet_row['fleet_end_galaxy'], $fleet_row['fleet_end_system'], $fleet_row['fleet_end_planet']);
				$GottenMoon .= "<br />";
			}
			elseif ($UserChance = 0 or $UserChance > $MoonChance)
			{
				$GottenMoon = "";
			}

			$formatted_cr 	= $this->build_report($result,$steal,$MoonChance,$GottenMoon,$totaltime);
			$raport 		= $formatted_cr['html'];


			$rid   = md5($raport);
			$QryInsertRapport  = 'INSERT INTO ' . REPORTS . ' SET ';
			$QryInsertRapport .= '`report_time` = UNIX_TIMESTAMP(), ';

			foreach ($attackFleets as $fleetID => $attacker)
			{
				$users2[$attacker['user']['user_id']] = $attacker['user']['user_id'];
			}

			foreach ($defense as $fleetID => $defender)
			{
				$users2[$defender['user']['user_id']] = $defender['user']['user_id'];
			}

			$QryInsertRapport .= '`report_owners` = "'.implode(',', $users2).'", ';
			$QryInsertRapport .= '`report_rid` = "'. $rid .'", ';
			$QryInsertRapport .= '`report_destroyed` = "'.$formatted_cr['destroyed'].'", ';
			$QryInsertRapport .= '`report_content` = "'. parent::$db->escape_value( $raport ) .'"';
			parent::$db->query ($QryInsertRapport );

			if($result['won'] == "a")
			{
				$style = "green";
			}
			elseif ($result['won'] == "w")
			{
				$style = "orange";
			}
			elseif ($result['won'] == "r")
			{
				$style = "red";
			}

			$raport  = "<a href=\"#\" style=\"color:".$style.";\" OnClick=\'f(\"game.php?page=CombatReport&report=". $rid ."\", \"\");\' >" . $this->_lang['sys_mess_attack_report'] ." [". $fleet_row['fleet_end_galaxy'] .":". $fleet_row['fleet_end_system'] .":". $fleet_row['fleet_end_planet'] ."]</a>";

			Functions_Lib::send_message ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_start_time'], 1, $this->_lang['sys_mess_tower'], $raport, '' );

			if($result['won'] == "a")
			{
				$style = "red";
			}
			elseif ($result['won'] == "w")
			{
				$style = "orange";
			}
			elseif ($result['won'] == "r")
			{
				$style = "lime";
			}

			$raport2  = "<a href=\"#\" style=\"color:".$style.";\" OnClick=\'f(\"game.php?page=CombatReport&report=". $rid ."\", \"\");\' >" . $this->_lang['sys_mess_attack_report'] ." [". $fleet_row['fleet_end_galaxy'] .":". $fleet_row['fleet_end_system'] .":". $fleet_row['fleet_end_planet'] ."]</a>";

			foreach ( $users2 as $id )
			{
				if ( $id != $fleet_row['fleet_owner'] && $id != 0 )
				{
					Functions_Lib::send_message ( $id , '' , $fleet_row['fleet_start_time'] , 1 , $this->_lang['sys_mess_tower'] , $raport2 , '' );
				}
			}
		}
		elseif ($fleet_row['fleet_end_time'] <= time())
		{
			$Message	= sprintf( $this->_lang['sys_fleet_won'],
									$targetPlanet['planet_name'], Fleets_Lib::target_link($fleet_row, ''),
									Format_Lib::pretty_number($fleet_row['fleet_resource_metal']), $this->_lang['Metal'],
									Format_Lib::pretty_number($fleet_row['fleet_resource_crystal']), $this->_lang['Crystal'],
									Format_Lib::pretty_number($fleet_row['fleet_resource_deuterium']), $this->_lang['Deuterium'] );

			Functions_Lib::send_message ( $fleet_row['fleet_owner'], '', $fleet_row['fleet_end_time'], 1, $this->_lang['sys_mess_tower'], $this->_lang['sys_mess_fleetback'], $Message);

			parent::restore_fleet ( $fleet_row );
			parent::remove_fleet ( $fleet_row['fleet_id'] );
		}
	}

	/**
	 * method build_report
	 * param $result_array
	 * param $steal_array
	 * param $moon_int
	 * param $moon_string
	 * param $time_float
	 * return the builded report
	*/
	private function build_report ( &$result_array , &$steal_array , &$moon_int , &$moon_string , &$time_float )
	{
		$html 		= '';
		$bbc 		= '';
		$html 	   .= $this->_lang['sys_attack_title']." ".date(Functions_Lib::read_config ( 'date_format_extended' ), time()).".<br /><br />";
		$round_no	= 1;
		$destroyed	= 0;
		$endtable1	= '';

		foreach( $result_array['rw'] as $round => $data1)
		{
			if($round_no <= 6)
			{
				$html 		.= $this->_lang['sys_attack_round']." ".$round_no." :<br /><br />";
				$attackers1 = $data1['attackers'];
				$attackers2 = $data1['infoA'];
				$attackers3 = $data1['attackA'];
				$defenders1 = $data1['defenders'];
				$defenders2 = $data1['infoD'];
				$defenders3 = $data1['defenseA'];
				$coord4 	= 0;
				$coord5 	= 0;
				$coord6 	= 0;

				foreach( $attackers1 as $fleet_id1 => $data2)
				{
					$name 	= $data2['user']['user_name'];
					$coord1 = $data2['fleet']['fleet_start_galaxy'];
					$coord2 = $data2['fleet']['fleet_start_system'];
					$coord3 = $data2['fleet']['fleet_start_planet'];
					$weap 	= ($data2['user']['research_weapons_technology'] * 10);
					$shie 	= ($data2['user']['research_shielding_technology'] * 10);
					$armr 	= ($data2['user']['research_armour_technology'] * 10);

					if($coord4 == 0){$coord4 += $data2['fleet']['fleet_end_galaxy'];}
					if($coord5 == 0){$coord5 += $data2['fleet']['fleet_end_system'];}
					if($coord6 == 0){$coord6 += $data2['fleet']['fleet_end_planet'];}

					$fl_info1  	= "<table><tr><th>";
					$fl_info1 	.= $this->_lang['sys_attack_attacker_pos']." ".$name." ([".$coord1.":".$coord2.":".$coord3."])<br />";
					$fl_info1 	.= $this->_lang['sys_ship_weapon']." ".$weap."% - ".$this->_lang['sys_ship_shield']." ".$shie."% - ".$this->_lang['sys_ship_armour']." ".$armr."%";
					$table1  	= "<table border=1 align=\"center\">";

					if (number_format($data1['attack']['total']) >= 0 && $round_no == 1)
					{
						if(number_format($data1['attack']['total']) == 0)
						{
							$ships1 = "<tr><br /><br />". $this->_lang['sys_destroyed']."<br /></tr>";
							$count1 = "";
							$destroyed = 1;
						}
						else
						{
							$destroyed = 0;
						}

						$ships1  = "<tr><th>".$this->_lang['sys_ship_type']."</th>";
						$count1  = "<tr><th>".$this->_lang['sys_ship_count']."</th>";

						foreach( $data2['detail'] as $ship_id1 => $ship_count1)
						{
						   if ($ship_count1 > 0)
						   {
						       $ships1 .= "<th>[ship[".$ship_id1."]]</th>";
						       $count1 .= "<th>".number_format($ship_count1)."</th>";
						   }
						}

						$ships1 .= "</tr>";
						$count1 .= "</tr>";
					}
					elseif(number_format($data1['attack']['total']) > 0)
					{
						$ships1  = "<tr><th>".$this->_lang['sys_ship_type']."</th>";
						$count1  = "<tr><th>".$this->_lang['sys_ship_count']."</th>";

						foreach( $data2['detail'] as $ship_id1 => $ship_count1)
						{
							if ($ship_count1 > 0)
							{
								$ships1 .= "<th>[ship[".$ship_id1."]]</th>";
								$count1 .= "<th>".number_format($ship_count1)."</th>";
							}
						}

						$ships1 .= "</tr>";
						$count1 .= "</tr>";
					}
					else
					{
						$ships1 = "<tr><br /><br />". $this->_lang['sys_destroyed']."<br /></tr>";
						$count1 = "";
					}

					$info_part1[$fleet_id1] = $fl_info1.$table1.$ships1.$count1;
				}

				foreach( $attackers2 as $fleet_id2 => $data3)
				{
					$weap1  	= "<tr><th>".$this->_lang['sys_ship_weapon']."</th>";
					$shields1  	= "<tr><th>".$this->_lang['sys_ship_shield']."</th>";
					$armour1  	= "<tr><th>".$this->_lang['sys_ship_armour']."</th>";

					foreach( $data3 as $ship_id2 => $ship_points1)
					{
						if ($ship_points1['shield'] > 0)
						{
						   $weap1 		.= "<th>".number_format($ship_points1['att'])."</th>";
						   $shields1 	.= "<th>".number_format($ship_points1['def'])."</th>";
						   $armour1 	.= "<th>".number_format($ship_points1['shield'])."</th>";
						}
					}

					$weap1 		.= "</tr>";
					$shields1 	.= "</tr>";
					$armour1 	.= "</tr>";
					$endtable1 	.= "</table></th></tr></table>";

					$info_part2[$fleet_id2] = $weap1.$shields1.$armour1.$endtable1;

					if (number_format($data1['attackA']['total']) > 0)
					{
						$html .= $info_part1[$fleet_id2].$info_part2[$fleet_id2];
						$html .= "<br /><br />";
					}
					else
					{
						$html .= $info_part1[$fleet_id2];
						$html .= "</table></th></tr></table><br /><br />";
					}
				}

				foreach( $defenders1 as $fleet_id1 => $data2)
				{
					$name = $data2['user']['user_name'];
					$weap = ($data2['user']['research_weapons_technology'] * 10);
					$shie = ($data2['user']['research_shielding_technology'] * 10);
					$armr = ($data2['user']['research_armour_technology'] * 10);

					$fl_info1  = "<table><tr><th>";
					$fl_info1 .= $this->_lang['sys_attack_defender_pos']." ".$name." ([".$coord4.":".$coord5.":".$coord6."])<br />";
					$fl_info1 .= $this->_lang['sys_ship_weapon']." ".$weap."% - ".$this->_lang['sys_ship_shield']." ".$shie."% - ".$this->_lang['sys_ship_armour']." ".$armr."%";

					$table1  = "<table border=1 align=\"center\">";

					if (number_format($data1['defenseA']['total']) > 0)
					{
						$ships1  = "<tr><th>".$this->_lang['sys_ship_type']."</th>";
						$count1  = "<tr><th>".$this->_lang['sys_ship_count']."</th>";

						foreach( $data2['def'] as $ship_id1 => $ship_count1)
						{
							if ($ship_count1 > 0)
							{
								$ships1 .= "<th>[ship[".$ship_id1."]]</th>";
								$count1 .= "<th>".number_format($ship_count1)."</th>";
							}
						}

						$ships1 .= "</tr>";
						$count1 .= "</tr>";
					}
					else
					{
						$ships1 = "<tr><br /><br />". $this->_lang['sys_destroyed']."<br /></tr>";
						$count1 = "";
					}

					$info_part1[$fleet_id1] = $fl_info1.$table1.$ships1.$count1;
				}

				foreach( $defenders2 as $fleet_id2 => $data3)
				{
					$weap1  	= "<tr><th>".$this->_lang['sys_ship_weapon']."</th>";
					$shields1  	= "<tr><th>".$this->_lang['sys_ship_shield']."</th>";
					$armour1  	= "<tr><th>".$this->_lang['sys_ship_armour']."</th>";

					foreach( $data3 as $ship_id2 => $ship_points1)
					{
						if ($ship_points1['shield'] > 0)
						{
							$weap1 .= "<th>".number_format($ship_points1['att'])."</th>";
							$shields1 .= "<th>".number_format($ship_points1['def'])."</th>";
							$armour1 .= "<th>".number_format($ship_points1['shield'])."</th>";
						}
					}

					$weap1 		.= "</tr>";
					$shields1 	.= "</tr>";
					$armour1 	.= "</tr>";
					$endtable1 	.= "</table></th></tr></table>";

					$info_part2[$fleet_id2] = $weap1.$shields1.$armour1.$endtable1;

					if (number_format($data1['defenseA']['total']) > 0)
					{
						$html .= $info_part1[$fleet_id2].$info_part2[$fleet_id2];
						$html .= "<br /><br />";
					}
					else
					{
						$html .= $info_part1[$fleet_id2];
						$html .= "</table></th></tr></table><br /><br />";
					}
				}

				$html .=  $this->_lang['fleet_attack_1']." ".number_format($data1['attack']['total'])." ".$this->_lang['fleet_attack_2']." ".number_format($data1['defShield'], 0, ' ', ' ')." ".$this->_lang['damage']."<br />";
				$html .= $this->_lang['fleet_defs_1']." ".number_format($data1['defense']['total'])." ".$this->_lang['fleet_defs_2']." ".number_format($data1['attackShield'], 0, ' ', ' ')." ".$this->_lang['damage']."<br /><br />";

				$round_no++;
			}
		}

		if ($result_array['won'] == "r")
		{
			$result1  = $this->_lang['sys_defender_won']."<br />";
		}
		elseif ($result_array['won'] == "a")
		{
			$result1  = $this->_lang['sys_attacker_won']."<br />";
			$result1 .= $this->_lang['sys_stealed_ressources']." ".$steal_array['metal']." ".$this->_lang['Metal'].", ".$steal_array['crystal']." ".$this->_lang['Crystal']." ".$this->_lang['sys_and']." ".$steal_array['deuterium']." ".$this->_lang['Deuterium']."<br />";
		}
		else
		{
			$result1  = $this->_lang['sys_both_won'].".<br />";
		}

		$html .= "<br /><br />";
		$html .= $result1;
		$html .= "<br />";

		$debirs_meta = ($result_array['debree']['att'][0] + $result_array['debree']['def'][0]);
		$debirs_crys = ($result_array['debree']['att'][1] + $result_array['debree']['def'][1]);

		$html .= $this->_lang['sys_attacker_lostunits']." ".$result_array['lost']['att']." ".$this->_lang['sys_units']."<br />";
		$html .= $this->_lang['sys_defender_lostunits']." ".$result_array['lost']['def']." ".$this->_lang['sys_units']."<br />";
		$html .= $this->_lang['debree_field_1']." ".$debirs_meta." ".$this->_lang['Metal']." ".$this->_lang['sys_and']." ".$debirs_crys." ".$this->_lang['Crystal']." ".$this->_lang['debree_field_2']."<br /><br />";
		$html .= $this->_lang['sys_moonproba']." ".floor($moon_int)." %<br />";
		$html .= $moon_string."<br /><br />";

		return array('html' => $html, 'bbc' => $bbc, 'destroyed' => $destroyed);
	}

	/**
	 * method acs_attack
	 * param $attackers
	 * param $defenders
	 * return the attack result
	*/
	private function acs_attack ( &$attackers , &$defenders )
    {
        $totalResourcePoints = array('attacker' => 0, 'defender' => 0);
        $resourcePointsAttacker = array('metal' => 0, 'crystal' => 0);

        foreach ($attackers as $fleetID => $attacker) {
            foreach ($attacker['detail'] as $element => $amount) {
                $resourcePointsAttacker['metal'] += $this->_pricelist[$element]['metal'] * $amount;
                $resourcePointsAttacker['crystal'] += $this->_pricelist[$element]['crystal'] * $amount ;

                $totalResourcePoints['attacker'] += $this->_pricelist[$element]['metal'] * $amount ;
                $totalResourcePoints['attacker'] += $this->_pricelist[$element]['crystal'] * $amount ;
            }
        }

        $resourcePointsDefender = array('metal' => 0, 'crystal' => 0);
        foreach ($defenders as $fleetID => $defender) {
            foreach ($defender['def'] as $element => $amount) {                                //Line20
                if ($element < 300) {
                    $resourcePointsDefender['metal'] += $this->_pricelist[$element]['metal'] * $amount ;
                    $resourcePointsDefender['crystal'] += $this->_pricelist[$element]['crystal'] * $amount ;

                    $totalResourcePoints['defender'] += $this->_pricelist[$element]['metal'] * $amount ;
                    $totalResourcePoints['defender'] += $this->_pricelist[$element]['crystal'] * $amount ;
                } else {
                    if (!isset($originalDef[$element])) $originalDef[$element] = 0;
                    $originalDef[$element] += $amount;

                    $totalResourcePoints['defender'] += $this->_pricelist[$element]['metal'] * $amount ;
                    $totalResourcePoints['defender'] += $this->_pricelist[$element]['crystal'] * $amount ;
                }
            }
        }

		$max_rounds = 6;

        for ($round = 0, $rounds = array(); $round < $max_rounds; $round++) {
            $attackDamage  = array('total' => 0);
            $attackShield  = array('total' => 0);
            $attackAmount  = array('total' => 0);
            $defenseDamage = array('total' => 0);
            $defenseShield = array('total' => 0);
            $defenseAmount = array('total' => 0);
            $attArray = array();
            $defArray = array();

            foreach ($attackers as $fleetID => $attacker) {
                $attackDamage[$fleetID] = 0;
                $attackShield[$fleetID] = 0;
                $attackAmount[$fleetID] = 0;

                foreach ($attacker['detail'] as $element => $amount) {
                    $attTech    = (1 + (0.1 * ($attacker['user']['research_weapons_technology']))); // WEAPONS
                    $defTech    = (1 + (0.1 * ($attacker['user']['research_shielding_technology']))); // SHIELD
                    $shieldTech = (1 + (0.1 * ($attacker['user']['research_armour_technology']))); // ARMOUR

                    $attackers[$fleetID]['techs'] = array($shieldTech, $defTech, $attTech);

                    $thisAtt    = $amount * ($this->_combat_caps[$element]['attack']) * $attTech * (mt_rand(80, 120) / 100); //attaque
                    $thisDef    = $amount * ($this->_combat_caps[$element]['shield']) * $defTech ; //bouclier
                    $thisShield    = $amount * ($this->_pricelist[$element]['metal'] + $this->_pricelist[$element]['crystal']) / 10 * $shieldTech; //coque

                    $attArray[$fleetID][$element] = array('def' => $thisDef, 'shield' => $thisShield, 'att' => $thisAtt);

                    $attackDamage[$fleetID] += $thisAtt;
                    $attackDamage['total'] += $thisAtt;
                    $attackShield[$fleetID] += $thisDef;
                    $attackShield['total'] += $thisDef;
                    $attackAmount[$fleetID] += $amount;
                    $attackAmount['total'] += $amount;
                }
            }

            foreach ($defenders as $fleetID => $defender) {
                $defenseDamage[$fleetID] = 0;
                $defenseShield[$fleetID] = 0;
                $defenseAmount[$fleetID] = 0;

                foreach ($defender['def'] as $element => $amount) {
                    $attTech    = (1 + (0.1 * ($defender['user']['research_weapons_technology']))); //attaquue
                    $defTech    = (1 + (0.1 * ($defender['user']['research_shielding_technology']))); //bouclier
                    $shieldTech = (1 + (0.1 * ($defender['user']['research_armour_technology']))); //coque

                    $defenders[$fleetID]['techs'] = array($shieldTech, $defTech, $attTech);

                    $thisAtt    = $amount * ($this->_combat_caps[$element]['attack']) * $attTech * (mt_rand(80, 120) / 100); //attaque
                    $thisDef    = $amount * ($this->_combat_caps[$element]['shield']) * $defTech ; //bouclier
                    $thisShield    = $amount * ($this->_pricelist[$element]['metal'] + $this->_pricelist[$element]['crystal']) / 10 * $shieldTech; //coque

                    if ($element == 407 || $element == 408 ) $thisAtt = 0;

                    $defArray[$fleetID][$element] = array('def' => $thisDef, 'shield' => $thisShield, 'att' => $thisAtt);

                    $defenseDamage[$fleetID] += $thisAtt;
                    $defenseDamage['total'] += $thisAtt;
                    $defenseShield[$fleetID] += $thisDef;
                    $defenseShield['total'] += $thisDef;
                    $defenseAmount[$fleetID] += $amount;
                    $defenseAmount['total'] += $amount;
                }
            }

            $rounds[$round] = array('attackers' => $attackers, 'defenders' => $defenders, 'attack' => $attackDamage, 'defense' => $defenseDamage, 'attackA' => $attackAmount, 'defenseA' => $defenseAmount, 'infoA' => $attArray, 'infoD' => $defArray);

            if ($defenseAmount['total'] <= 0 || $attackAmount['total'] <= 0) {
                break;
            }

            // Calculate hit percentages (ACS only but ok)
            $attackPct = array();
            foreach ($attackAmount as $fleetID => $amount) {
                if (!is_numeric($fleetID)) continue;
                $attackPct[$fleetID] = $amount / $attackAmount['total'];
            }

            $defensePct = array();
            foreach ($defenseAmount as $fleetID => $amount) {
                if (!is_numeric($fleetID)) continue;
                $defensePct[$fleetID] = $amount / $defenseAmount['total'];
            }

            // CALCUL DES PERTES !!!
            $attacker_n = array();
            $attacker_shield = 0;
            foreach ($attackers as $fleetID => $attacker) {
                $attacker_n[$fleetID] = array();

                foreach($attacker['detail'] as $element => $amount) {
                    $defender_moc = $amount * ($defenseDamage['total'] * $attackPct[$fleetID]) / $attackAmount[$fleetID];

                    if ($amount > 0) {
                        if ($attArray[$fleetID][$element]['def']/$amount < $defender_moc) {
                            $max_removePoints = floor($amount * $defenseAmount['total'] / $attackAmount[$fleetID] * $attackPct[$fleetID]);

                            $defender_moc -= $attArray[$fleetID][$element]['def'];
                            $attacker_shield += $attArray[$fleetID][$element]['def'];
                            $ile_removePoints = floor($defender_moc / (($this->_pricelist[$element]['metal'] + $this->_pricelist[$element]['crystal'])  / 10));

                            if ($max_removePoints < 0) $max_removePoints = 0;
                            if ($ile_removePoints < 0) $ile_removePoints = 0;

                            if ($ile_removePoints > $max_removePoints) {
                                $ile_removePoints = $max_removePoints;
                            }

                            $attacker_n[$fleetID][$element] = ceil($amount - $ile_removePoints);
                            if ($attacker_n[$fleetID][$element] <= 0) {
                                $attacker_n[$fleetID][$element] = 0;
                            }
                        } else {
                            $attacker_n[$fleetID][$element] = round($amount);
                            $attacker_shield += $defender_moc;
                        }
                    } else {
                        $attacker_n[$fleetID][$element] = round($amount);
                        $attacker_shield += $defender_moc;
                    }
                }
            }

            $defender_n = array();
            $defender_shield = 0;

            foreach ($defenders as $fleetID => $defender) {
                $defender_n[$fleetID] = array();

                foreach($defender['def'] as $element => $amount) {
                    $attacker_moc = $amount * ($attackDamage['total'] * $defensePct[$fleetID]) / $defenseAmount[$fleetID];

                    if ($amount > 0) {
                        if ($defArray[$fleetID][$element]['def']/$amount < $attacker_moc) {
                            $max_removePoints = floor($amount * $attackAmount['total'] / $defenseAmount[$fleetID] * $defensePct[$fleetID]);
                            $attacker_moc -= $defArray[$fleetID][$element]['def'];
                            $defender_shield += $defArray[$fleetID][$element]['def'];
                            $ile_removePoints = floor($attacker_moc / (($this->_pricelist[$element]['metal'] + $this->_pricelist[$element]['crystal']) / 10));

                            if ($max_removePoints < 0) $max_removePoints = 0;
                            if ($ile_removePoints < 0) $ile_removePoints = 0;

                            if ($ile_removePoints > $max_removePoints) {
                                $ile_removePoints = $max_removePoints;
                            }

                            $defender_n[$fleetID][$element] = ceil($amount - $ile_removePoints);
                            if ($defender_n[$fleetID][$element] <= 0) {
                                $defender_n[$fleetID][$element] = 0;
                            }

                        } else {
                            $defender_n[$fleetID][$element] = round($amount);
                            $defender_shield += $attacker_moc;
                        }
                    } else {
                        $defender_n[$fleetID][$element] = round($amount);
                        $defender_shield += $attacker_moc;
                    }
                }
            }

            // "Rapidfire"
            foreach ($attackers as $fleetID => $attacker) {
                foreach ($defenders as $fleetID2 => $defender) {
                    foreach($attacker['detail'] as $element => $amount) {
                        if ($amount > 0) {
                            foreach ($this->_combat_caps[$element]['sd'] as $c => $d) {
                                if (isset($defender['def'][$c])) {
                                    if ($d > 0) {
                                        $e = ($d / $defender['techs'][0]) / ($defender['techs'][1] * $attacker['techs'][2]);
                                        $defender_n[$fleetID2][$c] -= ceil(($amount * $e * (mt_rand(50,120)/ 100)/ 2) * $defensePct[$fleetID2] * ($amount / $attackAmount[$fleetID]));
                                        if ($defender_n[$fleetID2][$c] <= 0) {
                                            $defender_n[$fleetID2][$c] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    foreach($defender['def'] as $element => $amount) {
                        if ($amount > 0) {
                            foreach ($this->_combat_caps[$element]['sd'] as $c => $d) {
                                if (isset($attacker['detail'][$c])) {
                                    if ($d > 0) {
                                        $e = ($d / $defender['techs'][0]) / ($defender['techs'][1] * $attacker['techs'][2]);
                                        $attacker_n[$fleetID][$c] -= ceil(($amount * $e * (mt_rand(50,120)/ 100)/ 2) * $attackPct[$fleetID] * ($amount / $defenseAmount[$fleetID2]));
                                        if ($attacker_n[$fleetID][$c] <= 0) {
                                            $attacker_n[$fleetID][$c] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $rounds[$round]['attackShield'] = $attacker_shield;
            $rounds[$round]['defShield'] = $defender_shield;

            foreach ($attackers as $fleetID => $attacker) {
                $attackers[$fleetID]['detail'] = array_map('round', $attacker_n[$fleetID]);
            }

            foreach ($defenders as $fleetID => $defender) {
                $defenders[$fleetID]['def'] = array_map('round', $defender_n[$fleetID]);
            }
        }

        if ($attackAmount['total'] <= 0) {
            $won = "r"; // defender

        } elseif ($defenseAmount['total'] <= 0) {
            $won = "a"; // attacker

        } else {
            $won = "w"; // draw
            $rounds[count($rounds)] = array('attackers' => $attackers, 'defenders' => $defenders, 'attack' => $attackDamage, 'defense' => $defenseDamage, 'attackA' => $attackAmount, 'defenseA' => $defenseAmount);
        }

        // CDR
        foreach ($attackers as $fleetID => $attacker) {                                       // flotte attaquant en CDR
            foreach ($attacker['detail'] as $element => $amount) {
                $totalResourcePoints['attacker'] -= $this->_pricelist[$element]['metal'] * $amount ;
                $totalResourcePoints['attacker'] -= $this->_pricelist[$element]['crystal'] * $amount ;

                $resourcePointsAttacker['metal'] -= $this->_pricelist[$element]['metal'] * $amount ;
                $resourcePointsAttacker['crystal'] -= $this->_pricelist[$element]['crystal'] * $amount ;
            }
        }

        $resourcePointsDefenderDefs = array('metal' => 0, 'crystal' => 0);
        foreach ($defenders as $fleetID => $defender) {
            foreach ($defender['def'] as $element => $amount) {
                if ($element < 300) {                                                        // flotte defenseur en CDR
                    $resourcePointsDefender['metal'] -= $this->_pricelist[$element]['metal'] * $amount;
                    $resourcePointsDefender['crystal'] -= $this->_pricelist[$element]['crystal'] * $amount;

                    $totalResourcePoints['defender'] -= $this->_pricelist[$element]['metal'] * $amount;
                    $totalResourcePoints['defender'] -= $this->_pricelist[$element]['crystal'] * $amount;
                } else {                                                                    // defs defenseur en CDR + reconstruction
                    $totalResourcePoints['defender'] -= $this->_pricelist[$element]['metal'] * $amount;
                    $totalResourcePoints['defender'] -= $this->_pricelist[$element]['crystal'] * $amount;

                	if ( Officiers_Lib::is_officier_active ( $defender['user']['premium_officier_technocrat'] ) )
                	{
						$lost = floor ( ( $originalDef[$element] - $amount ) / ENGINEER_DEFENSE );
                	}
                	else
                	{
                		$lost = $originalDef[$element] - $amount;
                	}

                	$giveback = round ( $lost * ( mt_rand ( 70 * 0.8 , 70 * 1.2 ) / 100 ) );
                    $defenders[$fleetID]['def'][$element] += $giveback;
                    $resourcePointsDefenderDefs['metal'] += $this->_pricelist[$element]['metal'] * ($lost - $giveback) ;
                    $resourcePointsDefenderDefs['crystal'] += $this->_pricelist[$element]['crystal'] * ($lost - $giveback) ;

                }
            }
        }

		$game_fleet_cdr	= Functions_Lib::read_config ( 'fleet_cdr' );
		$game_defs_cdr	= Functions_Lib::read_config ( 'defs_cdr' );
        $totalLost 		= array('att' => $totalResourcePoints['attacker'], 'def' => $totalResourcePoints['defender']);
        $debAttMet 		= ($resourcePointsAttacker['metal'] * ($game_fleet_cdr / 100));
        $debAttCry 		= ($resourcePointsAttacker['crystal'] * ($game_fleet_cdr / 100));
        $debDefMet 		= ($resourcePointsDefender['metal'] * ($game_fleet_cdr / 100)) + ($resourcePointsDefenderDefs['metal'] * ($game_defs_cdr / 100));
        $debDefCry 		= ($resourcePointsDefender['crystal'] * ($game_fleet_cdr / 100)) + ($resourcePointsDefenderDefs['crystal'] * ($game_defs_cdr / 100));

        return array('won' => $won, 'debree' => array('att' => array($debAttMet, $debAttCry), 'def' => array($debDefMet, $debDefCry)), 'rw' => $rounds, 'lost' => $totalLost);
    }

	/**
	 * method acs_steal
	 * param $attackFleets
	 * param $defenderPlanet
	 * param $ForSim
	 * return the steal result
	*/
	private function acs_steal ( $attackFleets , $defenderPlanet , $ForSim = FALSE )
	{
		$SortFleets = array();

		foreach ($attackFleets as $FleetID => $Attacker)
		{
			foreach($Attacker['detail'] as $Element => $amount)
			{
				if ($Element != 210) //fix probos capacity in attack by jstar
					$SortFleets[$FleetID]        += $this->_pricelist[$Element]['capacity'] * $amount;
			}

			$SortFleets[$FleetID]            -= $Attacker['fleet']['fleet_resource_metal'] - $Attacker['fleet']['fleet_resource_crystal'] - $Attacker['fleet']['fleet_resource_deuterium'];
		}

		$Sumcapacity              = array_sum($SortFleets);
		//FIX JTSAMPER
		$booty['deuterium']       = min($Sumcapacity / 3,  ($defenderPlanet['planet_deuterium'] / 2));
		$Sumcapacity             -= $booty['deuterium'];

		$booty['crystal']         = min(($Sumcapacity / 2),  ($defenderPlanet['planet_crystal'] / 2));
		$Sumcapacity             -= $booty['crystal'];

		$booty['metal']           = min(($Sumcapacity ),  ($defenderPlanet['planet_metal'] / 2));
		$Sumcapacity             -= $booty['metal'];


		$oldMetalBooty            = $booty['crystal'] ;
		$booty['crystal']         += min(($Sumcapacity /2 ),  max((($defenderPlanet['planet_crystal']) / 2) - $booty['crystal'], 0));

		$Sumcapacity             += $oldMetalBooty - $booty['crystal'] ;

		$booty['metal']          += min(($Sumcapacity ),  max(($defenderPlanet['planet_metal'] / 2) - $booty['metal'], 0));


		$booty['metal']             = max($booty['metal'] ,0);
		$booty['crystal']           = max($booty['crystal'] ,0);
		$booty['deuterium']         = max($booty['deuterium'] ,0);
		//END FIX

		$steal                 = array_map('floor', $booty);
		if($ForSim)
		{
			return $steal;
		}


		$AllCapacity    	= array_sum($SortFleets);
		$QryUpdateFleet    	= '';

		if ( $AllCapacity != 0 )
		{
			foreach ( $SortFleets as $FleetID => $Capacity )
			{
				parent::$db->query ( 'UPDATE ' . FLEETS . ' SET
										`fleet_resource_metal` = `fleet_resource_metal` + '.Format_Lib::float_to_string($steal['metal'] * ($Capacity / $AllCapacity)).',
										`fleet_resource_crystal` = `fleet_resource_crystal` +'.Format_Lib::float_to_string($steal['crystal'] * ($Capacity / $AllCapacity)).',
										`fleet_resource_deuterium` = `fleet_resource_deuterium` +'.Format_Lib::float_to_string($steal['deuterium'] * ($Capacity / $AllCapacity)).'
										WHERE fleet_id = '.$FleetID.'
										LIMIT 1;' );
			}
		}
		else
		{
			$steal	= 0;
		}

		return $steal;
	}
}
/* end of attack.php */