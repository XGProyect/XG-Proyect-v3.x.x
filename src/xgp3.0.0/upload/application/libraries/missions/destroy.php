<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( 'location:../../' ) );

class Destroy extends Missions
{
	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * method destroy_mission
	 * param $fleet_row
	 * return the transport result
	*/
	public function destroy_mission ( $fleet_row )
	{
		if ( $fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] <= time() )
		{
			$current_data	= parent::$db->query_fetch ( "SELECT p.planet_name, r.research_weapons_technology, r.research_shielding_technology, r.research_armour_technology, u.user_name, u.user_id
															FROM " . PLANETS . " AS p
															INNER JOIN " . USERS . " AS u ON u.user_id = p.planet_user_id
															INNER JOIN " . PREMIUM . " AS pr ON pr.premium_user_id = p.planet_user_id
															INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = p.planet_user_id
															WHERE p.`planet_galaxy` = " . $fleet_row['fleet_start_galaxy'] . " AND
																	p.`planet_system` = " . $fleet_row['fleet_start_system'] . " AND
																	p.`planet_planet` = " . $fleet_row['fleet_start_planet'] . " AND
																	p.`planet_type` = " . $fleet_row['fleet_start_type'] . ";" );

			$target_data	= parent::$db->query_fetch ( "SELECT s.*, d.*, p.`planet_id`, p.planet_diameter, p.planet_user_id, u.user_name, u.user_current_planet, r.research_weapons_technology, r.research_shielding_technology, r.research_armour_technology
															FROM " . PLANETS . " AS p
															INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
															INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
															INNER JOIN " . USERS . " AS u ON u.user_id = p.planet_user_id
															INNER JOIN " . PREMIUM . " AS pr ON pr.premium_user_id = p.planet_user_id
															INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = p.planet_user_id
															WHERE p.`planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
																	p.`planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
																	p.`planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
																	p.`planet_type` = '" . $fleet_row['fleet_end_type'] . "';" );

			for ( $SetItem = 200 ; $SetItem < 500 ; $SetItem++ )
			{
				if ( $target_data[$this->_resource[$SetItem]] > 0 )
				{
					$target_ships[$SetItem]['count'] = $target_data[$this->_resource[$SetItem]];
				}
			}

			$TheFleet = explode ( ";" , $fleet_row['fleet_array'] );

			foreach ( $TheFleet as $a => $b )
			{
				if ( $b != '' )
				{
					$a								= explode ( "," , $b );
					$current_ships[$a[0]]['count'] 	= $a[1];
				}
			}


			$attack			= $this->attack ( $current_ships , $target_ships , $current_data , $target_data );
			$current_ships  = $attack['attacker'];
			$target_ships	= $attack['enemy'];
			$FleetResult  	= $attack['win'];
			$dane_do_rw   	= $attack['data_for_rw'];
			$zlom         	= $attack['debris'];
			$FleetArray   	= '';
			$FleetAmount  	= 0;
			$FleetStorage 	= 0;

			foreach ( $current_ships as $Ship => $Count )
			{
				$FleetStorage += $this->_pricelist[$Ship]['capacity'] * $Count['count'];
				$FleetArray   .= $Ship . "," .$Count['count'] . ";";
				$FleetAmount  += $Count['count'];
			}

			$TargetPlanetUpd	= "";

			if ( ! is_null ( $target_ships ) )
			{
				foreach ( $target_ships as $Ship => $Count )
				{
					$TargetPlanetUpd	.= "`" . $this->_resource[$Ship] . "` = '" . $Count['count'] . "', ";
				}
			}

			if ( $FleetResult == "a" )
			{
				$destructionl1	= 100 - sqrt ( $target_data['planet_diameter'] );
				$destructionl21	= $destructionl1 * sqrt ( $current_ships['214']['count'] );
				$destructionl2	= $destructionl21 / 1;

				if ( $destructionl2 > 100 )
				{
					$chance = '100';
				}
				else
				{
					$chance	= round ( $destructionl2 );
				}

				$tirage		= mt_rand ( 0 , 100 );
				$probalune	= sprintf ( $this->_lang['sys_destruc_lune'] , $chance );

				if ( $tirage <= $chance )
				{
					$resultat 	= '1';
					$finmess 	= $this->_lang['sys_destruc_reussi'];

					parent::$db->query ( "UPDATE " . FLEETS . " AS f1 SET
											f1.`fleet_start_type` = '1'
											WHERE f1.`fleet_start_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
													f1.`fleet_start_system` = '" . $fleet_row['fleet_end_system'] . "' AND
													f1.`fleet_start_planet` = '" . $fleet_row['fleet_end_planet'] . "';" );

					parent::$db->query ( "UPDATE " . FLEETS . " AS f2 SET
											f2.`fleet_end_type` = '1'
											WHERE f2.`fleet_end_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
													f2.`fleet_end_system` = '" . $fleet_row['fleet_end_system'] . "' AND
													f2.`fleet_end_planet` = '" . $fleet_row['fleet_end_planet'] . "';" );

					parent::$db->query ( "UPDATE " . PLANETS . " AS p SET
											`planet_destroyed` = '" . time() . "'
											WHERE p.`planet_id` = '" . $target_data['id'] . "';" );

					if ( $target_data['user_current_planet'] == $target_data['id'] )
					{
						parent::$db->query ( "UPDATE " . USERS . " SET
												`user_current_planet` = (SELECT `planet_id`
																	FROM " . PLANETS . "
																	WHERE `planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
																			`planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
																			`planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
																			`planet_type` = '1')
												WHERE `user_id` = '" . $target_data['planet_user_id'] . "';" );
					}
				}
				else
				{
					$resultat	= '0';
				}

				$destructionrip	= sqrt ( $target_data['planet_diameter'] ) / 2;
				$chance2		= round ( $destructionrip );

				if ( $resultat == 0 )
				{
					$tirage2	= mt_rand ( 0 , 100 );
					$probarip	= sprintf ( $this->_lang['sys_destruc_rip'] , $chance2 );

					if ( $tirage2 <= $chance2 )
					{
						$resultat2	= ' detruite 1';
						$finmess	= $this->_lang['sys_destruc_echec'];

						parent::remove_fleet ( $fleet_row['fleet_id'] );
					}
					else
					{
						$resultat2	= 'sauvees 0';
						$finmess 	= $this->_lang['sys_destruc_null'];
					}
				}
			}

			$introdestruc			= sprintf ( $this->_lang['sys_destruc_mess'] , $current_data['planet_name'] , $fleet_row['fleet_start_galaxy'] , $fleet_row['fleet_start_system'] , $fleet_row['fleet_start_planet'] , $fleet_row['fleet_end_galaxy'] , $fleet_row['fleet_end_system'] , $fleet_row['fleet_end_planet'] );

			parent::$db->query ( "UPDATE " . PLANETS . " AS p
									INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
									INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id` SET
									$TargetPlanetUpd
									`planet_metal` = `planet_metal` - '". $Mining['metal'] ."',
									`planet_crystal` = `planet_crystal` - '". $Mining['crystal'] ."',
									`planet_deuterium` = `planet_deuterium` - '". $Mining['deuter'] ."',
									`planet_invisible_start_time` = '".time()."',
									`planet_debris_metal` = `planet_debris_metal` + '". $zlom['metal'] ."',
									`planet_debris_crystal` = `planet_debris_crystal` + '". $zlom['crystal'] ."'
									WHERE `planet_galaxy` = '". $fleet_row['fleet_end_galaxy'] ."' AND
											`planet_system` = '". $fleet_row['fleet_end_system'] ."' AND
											`planet_planet` = '". $fleet_row['fleet_end_planet'] ."' AND
											`planet_type` = '". $fleet_row['fleet_end_type'] ."';" );

			$StrAttackerUnits 	= sprintf ( $this->_lang['sys_attacker_lostunits'] , $zlom['attacker'] );
			$StrDefenderUnits 	= sprintf ( $this->_lang['sys_defender_lostunits'] , $zlom['enemy'] );
			$StrRuins         	= sprintf ( $this->_lang['sys_gcdrunits'] , $zlom['metal'] , $this->_lang['Metal'] , $zlom['crystal'] , $this->_lang['Crystal'] );
			$DebrisField      	= $StrAttackerUnits . "<br />" . $StrDefenderUnits . "<br />" . $StrRuins;

			$AttackDate        	= date ( "r" , $fleet_row['fleet_start_time'] );
			$title             	= sprintf ( $this->_lang['sys_destruc_title'] , $AttackDate );
			$raport            	= "<center><table><tr><td>" . $title . "<br />";
			$zniszczony        	= FALSE;
			$a_zestrzelona     	= 0;
			$AttackTechon['A'] 	= $current_data['research_weapons_technology'] * 10;
			$AttackTechon['B'] 	= $current_data['research_shielding_technology'] * 10;
			$AttackTechon['C'] 	= $current_data['research_armour_technology'] * 10;
			$AttackerData      	= sprintf ( $this->_lang['sys_attack_attacker_pos'] , $current_data['user_name'] , $fleet_row['fleet_start_galaxy'] , $fleet_row['fleet_start_system'] , $fleet_row['fleet_start_planet'] );
			$AttackerTech      	= sprintf ( $this->_lang['sys_attack_techologies'] , $AttackTechon['A'] , $AttackTechon['B'] , $AttackTechon['C'] );
			$DefendTechon['A'] 	= $target_data['research_weapons_technology'] * 10;
			$DefendTechon['B'] 	= $target_data['research_shielding_technology'] * 10;
			$DefendTechon['C'] 	= $target_data['research_armour_technology'] * 10;
			$DefenderData      	= sprintf ( $this->_lang['sys_attack_defender_pos'] , $target_data['user_name'] , $fleet_row['fleet_end_galaxy'] , $fleet_row['fleet_end_system'] , $fleet_row['fleet_end_planet'] );
			$DefenderTech      	= sprintf ( $this->_lang['sys_attack_techologies'] , $DefendTechon['A'] , $DefendTechon['B'] , $DefendTechon['C']);

			foreach ( $dane_do_rw as $a => $b )
			{
				$raport .= "<table border=1 width=100%><tr><th><br /><center>" . $AttackerData . "<br />" . $AttackerTech . "<table border=1>";

				if ($b['attacker']['count'] > 0)
				{
					$raport1 = "<tr><th>" . $this->_lang['sys_ship_type'] . "</th>";
					$raport2 = "<tr><th>" . $this->_lang['sys_ship_count'] . "</th>";
					$raport3 = "<tr><th>" . $this->_lang['sys_ship_weapon'] . "</th>";
					$raport4 = "<tr><th>" . $this->_lang['sys_ship_shield'] . "</th>";
					$raport5 = "<tr><th>" . $this->_lang['sys_ship_armour'] . "</th>";

					foreach ( $b['attacker'] as $Ship => $Data )
					{
						if ( is_numeric ( $Ship ) )
						{
							if ( $Data['count'] > 0 )
							{
								$raport1 .= "<th>" . $this->_lang['tech_rc'][$Ship] . "</th>";
								$raport2 .= "<th>" . $Data['count'] . "</th>";
								$raport3 .= "<th>" . round ( $Data['attack']   / $Data['count'] ) . "</th>";
								$raport4 .= "<th>" . round ( $Data['shield'] / $Data['count'] ) . "</th>";
								$raport5 .= "<th>" . round ( $Data['defense'] / $Data['count'] ) . "</th>";
							}
						}
					}

					$raport1 .= "</tr>";
					$raport2 .= "</tr>";
					$raport3 .= "</tr>";
					$raport4 .= "</tr>";
					$raport5 .= "</tr>";
					$raport  .= $raport1 . $raport2 . $raport3 . $raport4 . $raport5;

				}
				else
				{
					if ( $a == 2 )
					{
						$a_zestrzelona = 1;
					}

					$zniszczony	 = TRUE;
					$raport 	.= "<br />" . $this->_lang['sys_destroyed'];
				}

				$raport .= "</table></center></th></tr></table>";
				$raport .= "<table border=1 width=100%><tr><th><br /><center>".$DefenderData."<br />".$DefenderTech."<table border=1>";

				if ( $b['enemy']['count'] > 0 )
				{
					$raport1	= "<tr><th>" . $this->_lang['sys_ship_type'] . "</th>";
					$raport2	= "<tr><th>" . $this->_lang['sys_ship_count'] . "</th>";
					$raport3	= "<tr><th>" . $this->_lang['sys_ship_weapon'] . "</th>";
					$raport4 	= "<tr><th>" . $this->_lang['sys_ship_shield'] . "</th>";
					$raport5 	= "<tr><th>" . $this->_lang['sys_ship_armour'] . "</th>";

					foreach ( $b['enemy'] as $Ship => $Data )
					{
						if ( is_numeric ( $Ship ) )
						{
							if ( $Data['count'] > 0 )
							{
								$raport1 .= "<th>" . $this->_lang['tech_rc'][$Ship] . "</th>";
								$raport2 .= "<th>" . $Data['count'] . "</th>";
								$raport3 .= "<th>" . round ( $Data['attack']   / $Data['count'] ) . "</th>";
								$raport4 .= "<th>" . round ( $Data['shield'] / $Data['count'] ) . "</th>";
								$raport5 .= "<th>" . round ( $Data['defense'] / $Data['count'] ) . "</th>";
							}
						}
					}

					$raport1 .= "</tr>";
					$raport2 .= "</tr>";
					$raport3 .= "</tr>";
					$raport4 .= "</tr>";
					$raport5 .= "</tr>";
					$raport  .= $raport1 . $raport2 . $raport3 . $raport4 . $raport5;

				}
				else
				{
					$zniszczony	= TRUE;
					$raport    .= "<br />" . $this->_lang['sys_destroyed'];
				}

				$raport .= "</table></center></th></tr></table>";

				if ( ( $zniszczony == FALSE ) && ! ( $a == 8 ) )
				{
					$AttackWaveStat    = sprintf ( $this->_lang['sys_attack_attack_wave'] , floor ( $b['attacker']['attack'] ) , floor ( $b['enemy']['shield'] ) );
					$DefendWavaStat    = sprintf ( $this->_lang['sys_attack_defend_wave'] , floor ( $b['enemy']['attack'] ) , floor ( $b['attacker']['shield'] ) );
					$raport           .= "<br /><center>".$AttackWaveStat."<br />".$DefendWavaStat."</center>";
				}
			}

			switch ( $FleetResult )
			{
				case "a":

					$raport           .= $this->_lang['sys_attacker_won'] ."<br />";
					$raport           .= $DebrisField ."<br />";
					$raport           .= $introdestruc ."<br />";
					$raport           .= $this->_lang['sys_destruc_mess1'];
					$raport           .= $finmess ."<br />";
					$raport           .= $probalune ."<br />";
					$raport           .= $probarip ."<br />";

				break;

				case "r":

					$raport           .= $this->_lang['sys_both_won'] ."<br />";
					$raport           .= $DebrisField ."<br />";
					$raport           .= $introdestruc ."<br />";
					$raport           .= $this->_lang['sys_destruc_stop'] ."<br />";

				break;

				case "w":

					$raport           .= $this->_lang['sys_defender_won'] ."<br />";
					$raport           .= $DebrisField ."<br />";
					$raport           .= $introdestruc ."<br />";
					$raport           .= $this->_lang['sys_destruc_stop'] ."<br />";

					parent::remove_fleet ( $fleet_row['fleet_id'] );

				break;
			}

			$raport            .= "</table>";
			$rid   			   	= md5 ( $raport );

			$owners				= $fleet_row['fleet_owner'].",".$target_data['planet_user_id'];

			parent::$db->query ( "INSERT INTO " . REPORTS . " SET
									`report_time` = UNIX_TIMESTAMP(),
									`report_owners` = '" . $owners . "',
									`report_rid` = '" . $rid . "',
									`report_destroyed` = '" . $a_zestrzelona . "',
									`report_content` = '" . addslashes ( $raport ) . "';" );

			$raport  = "<a href=\"#\" OnClick=\'f(\"game.php?page=CombatReport&report=". $rid ."\", \"\");\' >";
			$raport .= "<center>";
			$raport	.= $this->set_report_color ( $FleetResult );
			$raport .= $this->_lang['sys_mess_destruc_report'] . " [" . $fleet_row['fleet_end_galaxy'] . ":" . $fleet_row['fleet_end_system'] . ":" . $fleet_row['fleet_end_planet'] . "] </font></a><br /><br />";
			$raport .= "<font color=\"red\">" . $this->_lang['sys_perte_attaquant'] . ": " . $zlom['attacker'] . "</font>";
			$raport .= "<font color=\"green\">   " . $this->_lang['sys_perte_defenseur'] . ":" . $zlom['enemy'] . "</font><br />" ;
			$raport .= $this->_lang['sys_debris'] . " " . $this->_lang['Metal'] . ":<font color=\"#adaead\">" . $zlom['metal'] . "</font>   " . $this->_lang['Crystal'] . ":<font color=\"#ef51ef\">" . $zlom['crystal'] . "</font><br /></center>";

			parent::$db->query ( "UPDATE " . FLEETS . " SET
									`fleet_amount` = '" . $FleetAmount . "',
									`fleet_array` = '" . $FleetArray . "',
									`fleet_mess` = '1'
									WHERE fleet_id = '" . (int)$fleet_row['fleet_id'] . "';" );

			$this->destroy_message ( $current_data['id'] , $raport , $fleet_row['fleet_start_time'] );

			$raport2  = "<a href=\"#\" OnClick=\'f(\"game.php?page=CombatReport&report=". $rid ."\", \"\");\' >";
			$raport2 .= "<center>";
			$raport2 .= $this->set_report_color ( $FleetResult , FALSE );
			$raport2 .= $this->_lang['sys_mess_destruc_report'] ." [". $fleet_row['fleet_end_galaxy'] .":". $fleet_row['fleet_end_system'] .":". $fleet_row['fleet_end_planet'] ."] </font></a><br /><br />";

			$this->destroy_message ( $target_data['planet_user_id'] , $raport2 , $fleet_row['fleet_start_time'] );
		}
		elseif ( $fleet_row['fleet_mess'] == 1 && $fleet_row['fleet_end_time'] <= time() )
		{
			parent::restore_fleet ( $fleet_row , TRUE );
			parent::remove_fleet ( $fleet_row['fleet_id'] );
		}
	}

	/**
	 * method attack
	 * param $current_ships
	 * param $target_ships
	 * param $current_tech
	 * param $target_tech
	 * return process the attack
	*/
	private function attack ( $current_ships , $target_ships , $current_tech , $target_tech )
	{
		$round			= array();
		$attacker_n 	= array();
		$enemy_n      	= array();

		if ( ! is_null ( $current_ships ) )
		{
			$current_debris_start['metal']   = 0;
			$current_debris_start['crystal'] = 0;

			foreach ( $current_ships as $a => $b )
			{
				$current_debris_start['metal']   = $current_debris_start['metal']   + $current_ships[$a]['count'] * $this->_pricelist[$a]['metal'];
				$current_debris_start['crystal'] = $current_debris_start['crystal'] + $current_ships[$a]['count'] * $this->_pricelist[$a]['crystal'];
			}
		}

		$target_debris_start['metal']   = 0;
		$target_debris_start['crystal']	= 0;
		$target_start 					= $target_ships;

		if ( !is_null ( $target_ships ) )
		{
			foreach ( $target_ships as $a => $b )
			{
				if ( $a < 300 )
				{
					$target_debris_start['metal']   		= $target_debris_start['metal']   + $target_ships[$a]['count'] * $this->_pricelist[$a]['metal'];
					$target_debris_start['crystal'] 		= $target_debris_start['crystal'] + $target_ships[$a]['count'] * $this->_pricelist[$a]['crystal'];
				}
				else
				{
					$target_debris_start_defense['metal']   = $target_debris_start_defense['metal']   + $target_ships[$a]['count'] * $this->_pricelist[$a]['metal'];
					$target_debris_start_defense['crystal'] = $target_debris_start_defense['crystal'] + $target_ships[$a]['count'] * $this->_pricelist[$a]['crystal'];
				}
			}
		}

		for ( $i = 1 ; $i <= 7 ; $i++ )
		{
			$attacker_attack	= 0;
			$enemy_attack		= 0;
			$attacker_defense 	= 0;
			$enemy_defense      = 0;
			$attacker_amount  	= 0;
			$enemy_amount       = 0;
			$attacker_shield 	= 0;
			$enemy_shield      	= 0;

			if ( ! is_null ( $current_ships ) )
			{
				foreach ( $current_ships as $a => $b )
				{
					$current_ships[$a]['defense']	= $current_ships[$a]['count'] * ( $this->_pricelist[$a]['metal'] + $this->_pricelist[$a]['crystal'] ) / 10 * ( 1 + ( 0.1 * ( $current_tech['research_shielding_technology'] ) ) );
					$rand 							= mt_rand ( 80 , 120 ) / 100;
					$current_ships[$a]['shield'] 	= $current_ships[$a]['count'] * $this->_combat_caps[$a]['shield'] * ( 1 + ( 0.1 * $current_tech['research_armour_technology'] ) ) * $rand;
					$atak_statku 					= $this->_combat_caps[$a]['attack'];
					$technologie 					= ( 1 + ( 0.1 * $current_tech['research_weapons_technology'] ) );
					$rand 							= mt_rand ( 80 , 120 ) / 100;
					$ilosc 							= $current_ships[$a]['count'];
					$current_ships[$a]['attack'] 	= $number * $atak_statku * $technologie * $rand;
					$attacker_attack				= $attacker_attack + $current_ships[$a]['attack'];
					$attacker_defense 				= $attacker_defense + $current_ships[$a]['defense'];
					$attacker_amount 				= $attacker_amount + $current_ships[$a]['count'];
				}
			}
			else
			{
				$attacker_amount	= 0;
				break;
			}

			if ( ! is_null ( $target_ships ) )
			{
				foreach ( $target_ships as $a => $b )
				{
					$target_ships[$a]['defense']	= $target_ships[$a]['count'] * ( $this->_pricelist[$a]['metal'] + $this->_pricelist[$a]['crystal'] ) / 10 * ( 1 + ( 0.1 * ( $target_tech['research_shielding_technology'] ) ) );
					$rand 							= mt_rand ( 80 , 120 ) / 100;
					$target_ships[$a]['shield'] 	= $target_ships[$a]['count'] * $this->_combat_caps[$a]['shield'] * ( 1 + ( 0.1 * $target_tech['research_armour_technology'] ) ) * $rand;
					$atak_statku 					= $this->_combat_caps[$a]['attack'];
					$technologie 					= ( 1 + ( 0.1 * $target_tech['research_weapons_technology'] ) );
					$rand 							= mt_rand ( 80 , 120 ) / 100;
					$number 						= $target_ships[$a]['count'];
					$target_ships[$a]['attack'] 	= $number * $atak_statku * $technologie * $rand;
					$enemy_attack 					= $enemy_attack + $target_ships[$a]['attack'];
					$enemy_defense 					= $enemy_defense + $target_ships[$a]['defense'];
					$enemy_amount 					= $enemy_amount + $target_ships[$a]['count'];
				}
			}
			else
			{
				$enemy_amount						= 0;
				$round[$i]['attacker'] 				= $current_ships;
				$round[$i]['enemy'] 				= $target_ships;
				$round[$i]['attacker']['attack']	= $attacker_attack;
				$round[$i]['enemy']['attack'] 		= $enemy_attack;
				$round[$i]['attacker']['count']		= $attacker_amount;
				$round[$i]['enemy']['count'] 		= $enemy_amount;
				break;
			}

			$round[$i]['attacker'] 					= $current_ships;
			$round[$i]['enemy'] 					= $target_ships;
			$round[$i]['attacker']['attack'] 		= $attacker_attack;
			$round[$i]['enemy']['attack'] 			= $enemy_attack;
			$round[$i]['attacker']['count']			= $attacker_amount;
			$round[$i]['enemy']['count'] 			= $enemy_amount;

			if ( ( $attacker_amount == 0 ) OR ( $enemy_amount == 0 ) )
			{
				break;
			}

			foreach ( $current_ships as $a => $b )
			{
				if ( $attacker_amount > 0 )
				{
					$wrog_moc	= $current_ships[$a]['count'] * $enemy_attack / $attacker_amount;

					if ( $current_ships[$a]['shield'] < $wrog_moc )
					{
						$max_zdjac			= floor ( $current_ships[$a]['count'] * $enemy_amount / $attacker_amount );
						$wrog_moc			= $wrog_moc - $current_ships[$a]['shield'];
						$attacker_shield 	= $attacker_shield + $current_ships[$a]['shield'];
						$ile_zdjac 			= floor ( ( $wrog_moc / ( ( $this->_pricelist[$a]['metal'] + $this->_pricelist[$a]['crystal'] ) / 10 ) ) );

						if ( $ile_zdjac > $max_zdjac )
						{
							$ile_zdjac = $max_zdjac;
						}

						$attacker_n[$a]['count']		= ceil ( $current_ships[$a]['count'] - $ile_zdjac );

						if ( $attacker_n[$a]['count'] <= 0 )
						{
							$attacker_n[$a]['count']	= 0;
						}
					}
					else
					{
						$attacker_n[$a]['count'] 	= $current_ships[$a]['count'];
						$attacker_shield 			= $attacker_shield + $wrog_moc;
					}
				}
				else
				{
					$attacker_n[$a]['count'] 		= $current_ships[$a]['count'];
					$attacker_shield 				= $attacker_shield + $wrog_moc;
				}
			}

			foreach ( $target_ships as $a => $b )
			{
				if ( $enemy_amount > 0 )
				{
					$atakujacy_moc		= $target_ships[$a]['count'] * $attacker_attack / $enemy_amount;

					if ( $target_ships[$a]['shield'] < $atakujacy_moc )
					{
						$max_zdjac 		= floor ( $target_ships[$a]['count'] * $attacker_amount / $enemy_amount );
						$atakujacy_moc 	= $atakujacy_moc - $target_ships[$a]['shield'];
						$enemy_shield 	= $enemy_shield + $target_ships[$a]['shield'];
						$ile_zdjac 		= floor ( ( $atakujacy_moc / ( ( $this->_pricelist[$a]['metal'] + $this->_pricelist[$a]['crystal'] ) / 10 ) ) );

						if ( $ile_zdjac > $max_zdjac )
						{
							$ile_zdjac = $max_zdjac;
						}

						$enemy_n[$a]['count']		= ceil ( $target_ships[$a]['count'] - $ile_zdjac );

						if ($enemy_n[$a]['count'] <= 0)
						{
							$enemy_n[$a]['count']	= 0;
						}
					}
					else
					{
						$enemy_n[$a]['count']	= $target_ships[$a]['count'];
						$enemy_shield 			= $enemy_shield + $atakujacy_moc;
					}
				}
				else
				{
					$enemy_n[$a]['count'] 		= $target_ships[$a]['count'];
					$enemy_shield 				= $enemy_shield + $atakujacy_moc;
				}
			}

			foreach ( $current_ships as $a => $b )
			{
				foreach ( $this->_combat_caps[$a]['sd'] as $c => $d )
				{
					if ( isset ( $target_ships[$c] ) )
					{
						$enemy_n[$c]['count']		= $enemy_n[$c]['count'] - floor ( $d * mt_rand ( 50 , 100 ) / 100 );

						if ($enemy_n[$c]['count'] <= 0)
						{
							$enemy_n[$c]['count']	= 0;
						}
					}
				}
			}

			foreach ( $target_ships as $a => $b )
			{
				foreach ( $this->_combat_caps[$a]['sd'] as $c => $d )
				{
					if ( isset ( $current_ships[$c] ) )
					{
						$attacker_n[$c]['count']		= $attacker_n[$c]['count'] - floor ( $d * mt_rand ( 50 , 100 ) / 100 );

						if ($attacker_n[$c]['count'] <= 0)
						{
							$attacker_n[$c]['count']	= 0;
						}
					}
				}
			}

			$round[$i]['attacker']['shield']	= $attacker_shield;
			$round[$i]['enemy']['shield'] 		= $enemy_shield;
			$target_ships 						= $enemy_n;
			$current_ships 						= $attacker_n;
		}

		if ( ( $attacker_amount == 0 ) OR ( $enemy_amount == 0 ) )
		{
			if ( ( $attacker_amount == 0 ) && ( $enemy_amount == 0 ) )
			{
				$wygrana	= "r";
			}
			else
			{
				if ( $attacker_amount == 0 )
				{
					$wygrana = "w";
				}
				else
				{
					$wygrana = "a";
				}
			}
		}
		else
		{
			$i 									= sizeof ( $round );
			$round[$i]['attacker'] 				= $current_ships;
			$round[$i]['enemy'] 				= $target_ships;
			$round[$i]['attacker']['attack'] 	= $attacker_attack;
			$round[$i]['enemy']['attack'] 		= $enemy_attack;
			$round[$i]['attacker']['count']		= $attacker_amount;
			$round[$i]['enemy']['count']	 	= $enemy_amount;
			$wygrana 							= "r";
		}

		$current_debris_end['metal'] 			= 0;
		$current_debris_end['crystal'] 			= 0;

		if ( ! is_null ( $current_ships ) )
		{
			foreach ( $current_ships as $a => $b )
			{
				$current_debris_end['metal']	= $current_debris_end['metal'] + $current_ships[$a]['count'] * $this->_pricelist[$a]['metal'];
				$current_debris_end['crystal'] 	= $current_debris_end['crystal'] + $current_ships[$a]['count'] * $this->_pricelist[$a]['crystal'];
			}
		}

		$target_debris_end['metal'] 			= 0;
		$target_debris_end['crystal'] 			= 0;

		if ( ! is_null ( $target_ships ) )
		{
			foreach ( $target_ships as $a => $b )
			{
				if ( $a < 300 )
				{
					$target_debris_end['metal'] 			= $target_debris_end['metal'] + $target_ships[$a]['count'] * $this->_pricelist[$a]['metal'];
					$target_debris_end['crystal'] 			= $target_debris_end['crystal'] + $target_ships[$a]['count'] * $this->_pricelist[$a]['crystal'];
				}
				else
				{
					$target_debris_end_obrona['metal'] 		= $target_debris_end_obrona['metal'] + $target_ships[$a]['count'] * $this->_pricelist[$a]['metal'];
					$target_debris_end_obrona['crystal'] 	= $target_debris_end_obrona['crystal'] + $target_ships[$a]['count'] * $this->_pricelist[$a]['crystal'];
				}
			}
		}

		$ilosc_wrog 		= 0;
		$straty_obrona_wrog = 0;

		if ( ! is_null ( $target_ships ) )
		{
			foreach ( $target_ships as $a => $b )
			{
				if ( $a > 300 )
				{
					$straty_obrona_wrog			= $straty_obrona_wrog + ( ( $target_start[$a]['count'] - $target_ships[$a]['count'] ) * ( $this->_pricelist[$a]['metal'] + $this->_pricelist[$a]['crystal'] ) );
					$target_ships[$a]['count'] 	= $target_ships[$a]['count'] + ( ( $target_start[$a]['count'] - $target_ships[$a]['count'] ) * mt_rand ( 60 , 80 ) / 100 );
					$ilosc_wrog 				= $ilosc_wrog + $target_ships[$a]['count'];
				}
			}
		}

		if ( ( $ilosc_wrog > 0 ) && ( $attacker_amount == 0 ) )
		{
			$wygrana = "w";
		}

		$game_fleet_cdr		= Functions_Lib::read_config ( 'fleet_cdr' );
		$game_def_cdr		= Functions_Lib::read_config ( 'defs_cdr' );

		$debris['metal']    = ( ( ( $current_debris_start['metal']   - $current_debris_end['metal'] )   + ($target_debris_start['metal']   - $target_debris_end['metal'] ) )   * ( $game_fleet_cdr / 100 ) );
		$debris['crystal']  = ( ( ( $current_debris_start['crystal'] - $current_debris_end['crystal'] ) + ($target_debris_start['crystal'] - $target_debris_end['crystal'] ) ) * ( $game_fleet_cdr / 100 ) );

		$debris['metal']   += ( ( ( $current_debris_start['metal']   - $current_debris_end['metal'])   + ($target_debris_start['metal']   - $target_debris_end['metal']))   * ($game_def_cdr / 100));
		$debris['crystal'] += ( ( ( $current_debris_start['crystal'] - $current_debris_end['crystal']) + ($target_debris_start['crystal'] - $target_debris_end['crystal'])) * ($game_def_cdr / 100));

		$debris['attacker']	= ( ( $current_debris_start['metal'] - $current_debris_end['metal'] ) + ($current_debris_start['crystal'] - $current_debris_end['crystal'] ) );
		$debris['enemy']    = ( ( $target_debris_start['metal'] - $target_debris_end['metal'] ) + ($target_debris_start['crystal'] - $target_debris_end['crystal'] ) + $straty_obrona_wrog );

		return array ( "attacker" => $current_ships , "enemy" => $target_ships , "win" => $wygrana , "data_for_rw" => $round , "debris" => $debris );
	}

	/**
	 * method set_report_color
	 * param $result
	 * parem $current
	 * return the color for the current attack result
	*/
	private function set_report_color ( $result , $current = TRUE )
	{
		if ( $current )
		{
			switch ( $result )
			{
				case 'a':
					return "<font color=\"green\">";
				break;

				case 'r':
					return "<font color=\"orange\">";
				break;

				case 'w':
					return "<font color=\"red\">";
				break;
			}
		}
		else
		{
			switch ( $result )
			{
				case 'a':
					return "<font color=\"red\">";
				break;

				case 'r':
					return "<font color=\"orange\">";
				break;

				case 'w':
					return "<font color=\"green\">";
				break;
			}
		}
	}

	/**
	 * method destroy_message
	 * param $owner
	 * param $message
	 * param $time
	 * return send a message with the destroy details
	*/
	private function destroy_message ( $owner , $message , $time )
	{
		Functions_Lib::send_message ( $owner , '' , $time , 1 , $this->_lang['sys_mess_tower'] , $this->_lang['sys_mess_destruc_report'] , $message );
	}
}
/* end of destroy.php */