<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( 'location:../../' ) );

class Attack extends Missions
{
	const SHIP_MIN_ID		= 202;
	const SHIP_MAX_ID		= 215;
	const DEFENSE_MIN_ID	= 401;
	const DEFENSE_MAX_ID	= 503;

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
		// null == use default handlers
		$errorHandler 		= null;
		$exceptionHandler 	= null;

		if ( $fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] <= time() )
		{
			$base 	= dirname ( dirname ( __dir__ ) ) . DIRECTORY_SEPARATOR;

			require ( $base . 'utils' . DIRECTORY_SEPARATOR . 'includer.php' );

			$target_planet = parent::$db->query_fetch ( "SELECT *
														FROM " . PLANETS . " AS p
														INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
														INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
														INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
														WHERE `planet_galaxy` = ". (int)$fleet_row['fleet_end_galaxy'] ." AND
																`planet_system` = ". (int)$fleet_row['fleet_end_system'] ." AND
																`planet_type` = ". (int)$fleet_row['fleet_end_type'] ." AND
																`planet_planet` = ". (int)$fleet_row['fleet_end_planet'] .";" );

			if ( $fleet_row['fleet_group'] > 0 )
			{

				parent::$db->query ( "DELETE FROM `" . ACS_FLEETS . "`
										WHERE `acs_fleet_id` = '" . (int)$fleet_row['fleet_group'] . "'" );

				parent::$db->query ( "UPDATE `" . FLEETS . "` SET
										`fleet_mess` = '1'
										WHERE `fleet_group` = '" . $fleet_row['fleet_group'] . "'" );
			}
			else
			{
				parent::return_fleet ( $fleet_row['fleet_id'] );
			}

			$target_user 	= doquery('SELECT * FROM {{table}} WHERE id=' . $target_planet['id_owner'], 'users', true);
			$target_userID 	= $target_user['id'];

			PlanetResourceUpdate ( $target_user , $target_planet , time() );

			// attackers fleet sum
			$attackers = new PlayerGroup();

			if ( $fleet_row['fleet_group'] != 0 )
			{
				$fleets 	= doquery('SELECT * FROM {{table}} WHERE fleet_group=' . $fleet_row['fleet_group'], 'fleets');
				$attackers 	= get_player_group_from_query($fleets);
			}
			else
			{
				$attackers 	= get_player_group ( $fleet_row );
			}
			//defenders fleet sum
			$def 			= doquery('SELECT * FROM {{table}} WHERE `fleet_end_galaxy` = ' . $fleet_row['fleet_end_galaxy'] . ' AND `fleet_end_system` = ' . $fleet_row['fleet_end_system'] . ' AND `fleet_end_type` = ' . $fleet_row['fleet_end_type'] . ' AND `fleet_end_planet` = ' . $fleet_row['fleet_end_planet'] . ' AND fleet_start_time<' . time() . ' AND fleet_end_stay>=' . time(), 'fleets');
			$defenders 		= get_player_group_from_query ( $def , true , $target_user );

			//defenses sum
			$homeFleet 		= new HomeFleet ( 0 );

			for ( $i = self::DEFENSE_MIN_ID ; $i < self::DEFENSE_MAX_ID ; $i++ )
			{
				if ( isset ( $this->_resource[$i] ) && isset ( $target_planet[$this->_resource[$i]] ) )
				{
					if ( $target_planet[$this->_resource[$i]] != 0 )
					{
						$homeFleet->add ( get_ship_type ( $i , $target_planet[$this->_resource[$i]] ) );
					}
				}
			}

			for ( $i = self::SHIP_MIN_ID ; $i < self::SHIP_MAX_ID ; $i++ )
			{
				if ( isset ( $this->_resource[$i] ) && isset ( $target_planet[$this->_resource[$i]] ) )
				{
					if ( $target_planet[$this->_resource[$i]] != 0 )
					{
						$homeFleet->add (get_ship_type ( $i , $target_planet[$this->_resource[$i]] ) );
					}
				}
			}

			if (!$defenders->existPlayer($target_userID))
			{
				$player	= new Player ( $target_userID , array ( $homeFleet ) );

				$player->setTech ( $target_user['military_tech'] , $target_user['shield_tech'] , $target_user['defence_tech'] );
				$defenders->addPlayer ( $player );
			}
			else
			{
				$defenders->getPlayer ( $target_userID )->addDefense ( $homeFleet );
			}

			//start of battle
			$battle 		= new Battle ( $attackers , $defenders );
			$startBattle 	= DebugManager::runDebugged ( array ( $battle , 'startBattle' ) , $errorHandler , $exceptionHandler );
			$startBattle();

			//end of battle
			$report 		= $battle->getReport();

			$steal 			= $this->update_attackers ( $report->getPresentationAttackersFleetOnRound ( 'START' ) , $report->getAfterBattleAttackers() , $target_planet );

			$this->update_defenders ( $report->getPresentationDefendersFleetOnRound ( 'START' ) , $report->getAfterBattleDefenders() , $target_planet , $steal );
			$this->updateDebris ( $fleet_row , $report );
			$this->update_moon ( $fleet_row , $report , '' , $target_userID , $target_planet );
			$this->send_message ( $fleet_row , $report );

		}
		elseif ( $fleet_row['fleet_end_time'] <= time() )
		{
			$message	= sprintf	( 	$this->_lang['sys_fleet_won'],
										$target_planet['planet_name'], Fleets_Lib::target_link ( $fleet_row , '' ),
										Format_Lib::pretty_number ( $fleet_row['fleet_resource_metal'] ) , $this->_lang['Metal'],
										Format_Lib::pretty_number ( $fleet_row['fleet_resource_crystal'] ) , $this->_lang['Crystal'],
										Format_Lib::pretty_number ( $fleet_row['fleet_resource_deuterium'] ) , $this->_lang['Deuterium']
									);

			Functions_Lib::send_message ( $fleet_row['fleet_owner'] , '' , $fleet_row['fleet_end_time'] , 1 , $this->_lang['sys_mess_tower'] , $this->_lang['sys_mess_fleetback'] , $message );

			parent::restore_fleet ( $fleet_row );
			parent::remove_fleet ( $fleet_row['fleet_id'] );
		}
	}

	/**
	 * method get_ship_type
	 * param $id
	 * param $count
	 * return the attack result
	 */
	private function get_ship_type ( $id , $count )
	{
		$rf 	= $this->_combat_caps[$id]['sd'];
		$shield	= $this->_combat_caps[$id]['shield'];
		$cost 	= array($this->_pricelist[$id]['metal'], $this->_pricelist[$id]['crystal']);
		$power 	= $this->_combat_caps[$id]['attack'];

		if ( $id >= self::SHIP_MIN_ID && $id <= self::SHIP_MAX_ID )
		{
			return new Ship($id, $count, $rf, $shield, $cost, $power);
		}

		return new Defense($id, $count, $rf, $shield, $cost, $power);
	}

	/**
	 * method update_debris
	 * param $fleet_row
	 * param $report
	 * return the attack result
	 */
	private function update_debris ( $fleet_row , $report )
	{
		list ( $metal , $crystal )	= $report->getDebris();

		parent::$db->query ( "UPDATE " . PLANETS . " SET
									`planet_invisible_start_time` = '".time()."',
									`planet_debris_metal` = `planet_debris_metal` + '" . $metal . "',
									`planet_debris_crystal` = `planet_debris_crystal` + '" . $crystal . "'
									WHERE `planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
											`planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
											`planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
											`planet_type` = 1
									LIMIT 1;" );
	}

	/**
	 * method get_player_group
	 * param $fleet_row
	 * return the attack result
	 */
	private function get_player_group ( $fleet_row )
	{
		$playerGroup 		= new PlayerGroup();
		$serializedTypes 	= explode ( ';' , $fleet_row['fleet_array'] );
		$idPlayer 			= $fleet_row['fleet_owner'];
		$fleet 				= new Fleet ( $fleet_row['fleet_id'] );

		foreach ( $serializedTypes as $serializedType )
		{
			list ( $id , $count )	= explode ( ',' , $serializedType );

			if ($id != 0 && $count != 0)
			{
				$fleet->add ( $this->get_ship_type ( $id , $count ) );
			}
		}

		$player_info 	= parent::$db->query_fetch ( "SELECT `research_weapons_technology`,
																`research_shielding_technology`,
																`research_armour_technology`
														FROM `" . RESEARCH . "`
														WHERE `research_user_id` = '" . $idPlayer . "';" );

		$player 		= new Player ( $idPlayer , array ( $fleet ) );
		$player->setTech ( $player_info['research_weapons_technology'] , $player_info['research_shielding_technology'] , $player_info['research_armour_technology'] );
		$playerGroup->addPlayer ( $player );

		return $playerGroup;
	}

	/**
	 * method get_player_group_from_query
	 * param $result
	 * param $target_user
	 * return the attack result
	 */
	private function get_player_group_from_query ( $result , $target_user = false )
	{
		$playerGroup	= new PlayerGroup();

		while ( $fleet_row = parent::$db->fetch_assoc ( $result ) )
		{
			//making the current fleet object
			$serializedTypes	= explode ( ';' , $fleet_row['fleet_array'] );
			$idPlayer 			= $fleet_row['fleet_owner'];
			$fleet 				= new Fleet ( $fleet_row['fleet_id'] );

			foreach ( $serializedTypes as $serializedType )
			{
				list ( $id , $count ) = explode ( ',' , $serializedType );

				if ( $id != 0 && $count != 0 )
				{
					$fleet->add ( get_ship_type ( $id , $count ) );
				}
			}

			//making the player object and add it to playerGroup object
			if ( ! $playerGroup->existPlayer ( $idPlayer ) )
			{
				if ( $target_user !== FALSE && $target_user['id'] == $idPlayer )
				{
					$player_info	= $target_user;
				}
				else
				{
					$player_info	= parent::$db->query_fetch ( "SELECT `research_weapons_technology`,
																			`research_shielding_technology`,
																			`research_armour_technology`
																	FROM `" . RESEARCH . "`
																	WHERE `research_user_id` = '" . $idPlayer . "';" );
				}

				$player 		= new Player ( $idPlayer , array ( $fleet ) );

				$player->setTech ( $player_info['military_tech'] , $player_info['shield_tech'] , $player_info['defence_tech'] );
				$playerGroup->addPlayer ( $player );
			}
			else
			{
				$playerGroup->getPlayer($idPlayer)->addFleet($fleet);
			}
		}

		return $playerGroup;
	}

	/**
	 * method update_moon
	 * param $fleet_row
	 * param $report
	 * param $moonName
	 * param $target_userId
	 * param $target_planet
	 * return the attack result
	 */
	private function update_moon ( $fleet_row , $report , $moonName , $target_userId , $target_planet )
	{
		$moon	= $report->tryMoon();

		if ( $moon === FALSE )
		{
			return;
		}

		$galaxy	= $fleet_row['fleet_end_galaxy'];
		$system = $fleet_row['fleet_end_system'];
		$planet = $fleet_row['fleet_end_planet'];

		$moon_exists	= parent::$db->query_fetch ( "SELECT `planet_id`
														FROM `" . PLANETS . "`
														WHERE `planet_galaxy` = '" . $galaxy . "'
															AND `planet_system` = '" . $system . "'
															AND `planet_planet` = '" . $planet . "'
															AND `planet_type` = '3';" );

		if ( $moon_exists['planet_id'] != 0 )
		{
			return;
		}

		extract ( $moon );	//$size and $fields

		$_creator	= Functions_Lib::load_library ( 'Creator_Lib' );
		$_creator->create_moon ( $galaxy , $system , $planet , $TargetUserID , '' , '' , $size );
	}

	/**
	 * method send_message
	 * param $fleet_row
	 * param $report
	 * return the attack result
	 */
	private function send_message ( $fleet_row , $report )
	{
		$idAtts	= $report->getAttackersId();
		$idDefs = $report->getDefendersId();
		$idAll 	= array_merge ( $idAtts , $idDefs );
		$owners = implode ( ',' , $idAll );
		$rid 	= md5 ( $report ) . time();

		parent::$db->query ( "INSERT INTO `" . REPORTS . "` SET
								`report_owners` = '" . ( $fleet_row['fleet_owner'] . ',' . $fleet_row['fleet_target_owner'] ) . "',
								`report_rid` = '" . $rid . "',
								`report_content` = '" . addslashes ( $report ) . "',
								`report_destroyed` = '0',
								`report_destroyed` = '" . time() . "'" );

		foreach ( $idAtts as $id )
		{
			if ( $report->attackerHasWin() )
			{
				$style = 'green';
			}
			elseif ( $report->isAdraw() )
			{
				$style = 'orange';
			}
			else
			{
				$style = 'red';
			}

			$raport = "<a href=\"#\" style=\"color:" . $style . ";\" OnClick=\'f(\"game.php?page=CombatReport&report=" . $rid . "\", \"\");\' >" . $this->_lang['sys_mess_attack_report'] . " [" . $fleet_row['fleet_end_galaxy'] . ":" . $fleet_row['fleet_end_system'] . ":" . $fleet_row['fleet_end_planet'] . "]</a>";
			Functions_Lib::send_message ( $fleet_row['fleet_owner'] , '' , $fleet_row['fleet_start_time'] , 1 , $this->_lang['sys_mess_tower'] , $raport , '' );
		}

		foreach ( $idDefs as $id )
		{
			if ( $report->attackerHasWin() )
			{
				$style = 'red';
			}
			elseif ( $report->isAdraw() )
			{
				$style = 'orange';
			}
			else
			{
				$style = 'green';
			}

			$raport = "<a href=\"#\" style=\"color:" . $style . ";\" OnClick=\'f(\"game.php?page=CombatReport&report=" . $rid . "\", \"\");\' >" . $this->_lang['sys_mess_attack_report'] . " [" . $fleet_row['fleet_end_galaxy'] . ":" . $fleet_row['fleet_end_system'] . ":" . $fleet_row['fleet_end_planet'] . "]</a>";
			Functions_Lib::send_message ( $id , '' , $fleet_row['fleet_start_time'] , 1 , $this->_lang['sys_mess_tower'] , $raport , '' );
		}
	}

	/**
	 * method getCapacity
	 * param $players
	 * return the attack result
	 */
	private function getCapacity ( PlayerGroup $players )
	{
		$capacity	= 0;

		foreach ( $players->getIterator() as $idPlayer => $player )
		{
			foreach ( $player->getIterator() as $idFleet => $fleet )
			{
				foreach ( $fleet->getIterator() as $idShipType => $shipType )
				{
					$capacity	+= $shipType->getCount() * $this->_pricelist[$idShipType]['capacity'];
				}
			}
		}

		return $capacity;
	}

	/**
	 * method update_attackers
	 * param $playerGroupBeforeBattle
	 * param $playerGroupAfterBattle
	 * param $target_planet
	 * return the attack result
	 */
	private function update_attackers ( $playerGroupBeforeBattle , $playerGroupAfterBattle , $target_planet )
	{
		$fleetArray 	= '';
		$emptyFleets 	= array();
		$capacity 		= $this->getCapacity ( $playerGroupAfterBattle );
		$steal 			= array	(
									'metal' 	=> 0,
									'crystal' 	=> 0,
									'deuterium' => 0
								);

		foreach ( $playerGroupBeforeBattle->getIterator() as $idPlayer => $player )
		{
			$existPlayer 			= $playerGroupAfterBattle->existPlayer ( $idPlayer );
			$Xplayer 				= null;

			if ( $existPlayer )
			{
				$Xplayer 			= $playerGroupAfterBattle->getPlayer ( $idPlayer );
			}

			foreach ( $player->getIterator() as $idFleet => $fleet )
			{
				$existFleet 		= $existPlayer && $Xplayer->existFleet ( $idFleet );
				$Xfleet 			= null;

				if ($existFleet)
				{
					$Xfleet 		= $Xplayer->getFleet ( $idFleet );
				}
				else
				{
					$emptyFleets[]	= $idFleet;
				}
				$fleetCapacity 		= 0;
				$totalCount 		= 0;
				$fleetArray 		= '';

				foreach ( $fleet as $idShipType => $fighters )
				{
					$existShipType 	= $existFleet && $Xfleet->existShipType ( $idShipType );
					$amount 			= 0;

					if ( $existShipType )
					{
						$XshipType			= $Xfleet->get_ship_type ( $idShipType );
						$amount 		 	= $XshipType->getCount();
						$fleetCapacity 	   += $amount * $this->_pricelist[$idShipType]['capacity'];
						$totalCount 	   += $amount;
						$fleetArray 	   .= "$idShipType,$amount;";
					}
				}

				if ( $existFleet )
				{
					$fleetSteal 			= array	(
														'metal' 	=> 0,
														'crystal' 	=> 0,
														'deuterium' => 0
													);

					if ( $playerGroupAfterBattle->battleResult == BATTLE_WIN )
					{
						$corrispectiveMetal 	= $target_planet['metal'] * $fleetCapacity / $capacity;
						$corrispectiveCrystal 	= $target_planet['crystal'] * $fleetCapacity / $capacity;
						$corrispectiveDeuterium = $target_planet['deuterium'] * $fleetCapacity / $capacity;
						$fleetSteal 			= $this->plunder ( $fleetCapacity , $corrispectiveMetal , $corrispectiveCrystal , $corrispectiveDeuterium );
						$steal['metal'] 	   += $fleetSteal['metal'];
						$steal['crystal']	   += $fleetSteal['crystal'];
						$steal['deuterium']    += $fleetSteal['deuterium'];
					}

					parent::$db->query ( "UPDATE `" . FLEETS . "` SET
											`fleet_array` = '" . substr ( $fleetArray , 0 , -1 ) . "',
											`fleet_amount` = '" . $totalCount . "',
											`fleet_mess` = '1',
											`fleet_resource_metal` = `fleet_resource_metal` + '" . $fleetSteal['metal'] . "' ,
											`fleet_resource_crystal` = `fleet_resource_crystal` + '" . $fleetSteal['crystal'] . "' ,
											`fleet_resource_deuterium` = `fleet_resource_deuterium` + '" . $fleetSteal['deuterium'] . "'
											WHERE `fleet_id` = '" . $idFleet . "';" );

				}
			}
		}

		//updating flying fleets
		$id_string	= implode ( ',' , $emptyFleets );

		if ( ! empty ( $id_string ) )
		{
			parent::$db->query ( "DELETE FROM `" . FLEETS . "`
									WHERE `fleet_id` IN (" . $id_string . ")");
		}

		return $steal;
	}

	/**
	 * method update_defenders
	 * param $playerGroupBeforeBattle
	 * param $playerGroupAfterBattle
	 * param $target_planet
	 * param $steal
	 * return the attack result
	 */
	private function update_defenders ( $playerGroupBeforeBattle , $playerGroupAfterBattle , $target_planet , $steal )
	{
		$Xplayer 		= $Xfleet = $XshipType = null;
		$fleetArray 	= '';
		$emptyFleets 	= array();

		foreach ( $playerGroupBeforeBattle->getIterator() as $idPlayer => $player )
		{
			$existPlayer	= $playerGroupAfterBattle->existPlayer ( $idPlayer );

			if ( $existPlayer)
			{
				$Xplayer = $playerGroupAfterBattle->getPlayer($idPlayer);
			}

			foreach ( $player->getIterator() as $idFleet => $fleet )
			{
				$existFleet			= $existPlayer && $Xplayer->existFleet ( $idFleet );

				if ($existFleet)
				{
					$Xfleet 		= $Xplayer->getFleet ( $idFleet );
				}
				else
				{
					$emptyFleets[]	= $idFleet;
				}

				foreach ( $fleet as $idShipType => $fighters )
				{
					$existShipType	= $existFleet && $Xfleet->existShipType ( $idShipType );
					$amount 		= 0;

					if ( $existShipType )
					{
						$XshipType	= $Xfleet->get_ship_type ( $idShipType );
						$amount 	= $XshipType->getCount();
					}

					$fleetArray .= '`' . $this->_resource[$SidShipType] . '`=' . $amount . ', ';
				}
			}
		}

		//updating defenses and ships on planet
		parent::$db->query ( "UPDATE `" . PLANETS . "` SET
								" . $fleetArray. "
								`planet_metal` = `planet_metal` -  " . $steal['metal'] . ",
								`planet_crystal` = `planet_crystal` -  " . $steal['crystal'] . ",
								`planet_deuterium` = `planet_deuterium` -  " . $steal['deuterium'] . "
								WHERE `planet_id` = '" . $target_planet['id'] . "'" );

		//updating flying fleets
		$id_string	= implode ( "," , $emptyFleets );

		if ( ! empty ( $id_string ) )
		{
			parent::$db->query ( "DELETE FROM `" . FLEETS . "`
									WHERE `fleed_id` IN (" . $id_string . ")" );
		}
	}

	/**
	 * method plunder
	 * param $capacity
	 * param $metal
	 * param $crystal
	 * param $deuterium
	 * return the attack result
	 */
	private function plunder ( $capacity , $metal , $crystal , $deuterium )
	{
		/**
		 * 1. Fill up to 1/3 of cargo capacity with metal
		 * 2. Fill up to half remaining capacity with crystal
		 * 3. The rest will be filled with deuterium
		 * 4. If there is still capacity available fill half of it with metal
		 * 5. Now fill the rest with crystal
		 */

		//stolen resources
		$steal	= array	(
							'metal' 	=> 0,
							'crystal' 	=> 0,
							'deuterium' => 0
						);

		//max resources that can be take
		$metal 		/= 2;
		$crystal 	/= 2;
		$deuterium 	/= 2;

		//Fill up to 1/3 of cargo capacity with metal
		$stolen 		 	 = min ( $capacity / 3 , $metal );
		$steal['metal'] 	+= $stolen;
		$metal 				-= $stolen;
		$capacity 			-= $stolen;

		//Fill up to half remaining capacity with crystal
		$stolen 			 = min ( $capacity / 2 , $crystal );
		$steal['crystal'] 	+= $stolen;
		$crystal 			-= $stolen;
		$capacity 			-= $stolen;

		//The rest will be filled with deuterium
		$stolen 			 = min ( $capacity , $deuterium );
		$steal['deuterium'] += $stolen;
		$deuterium 			-= $stolen;
		$capacity 			-= $stolen;

		//If there is still capacity available fill half of it with metal
		$stolen 			 = min ( $capacity / 2 , $metal );
		$steal['metal'] 	+= $stolen;
		$metal 				-= $stolen;
		$capacity 			-= $stolen;

		//Now fill the rest with crystal
		$stolen 			 = min($capacity, $crystal);
		$steal['crystal'] 	+= $stolen;
		$crystal 			-= $stolen;
		$capacity 			-= $stolen;

		return $steal;
	}
}
/* end of attack.php */