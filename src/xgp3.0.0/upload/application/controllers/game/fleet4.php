<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Fleet4 extends XGPCore
{
	const MODULE_ID = 8;

	private $_lang;
	private $_noob;
	private $_current_user;
	private $_current_planet;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		// check if session is active
		parent::$users->check_session();

		// Check module access
		Functions_Lib::module_message ( Functions_Lib::is_module_accesible ( self::MODULE_ID ) );

		$this->_lang			= parent::$lang;
		$this->_current_user	= parent::$users->get_user_data();
		$this->_current_planet	= parent::$users->get_planet_data();
		$this->_noob			= Functions_Lib::load_library ( 'NoobsProtection_Lib' );

		$this->build_page();
	}

	/**
	 * method __destruct
	 * param
	 * return close db connection
	 */
	public function __destruct()
	{
		parent::$db->close_connection();
	}

	/**
	 * method build_page
	 * param
	 * return main method, loads everything
	 */
	private function build_page()
	{
		$resource	=	parent::$objects->get_objects();
		$pricelist	=	parent::$objects->get_price();
		$reslist	=	parent::$objects->get_objects_list();
		$parse		=	$this->_lang;

		if ( parent::$users->is_on_vacations ( $this->_current_user ) )
		{
			exit ( Functions_Lib::message ( $this->_lang['fl_vacation_mode_active'] , "game.php?page=overview" , 2 ) );
		}

		$fleet_group_mr = 0;

		if ( $_POST['fleet_group'] > 0 )
		{
			if ( $_POST['mission'] == 2 )
			{
				$target = 	'g' . (int)$_POST['galaxy'] .
							's' . (int)$_POST['system'] .
							'p' . (int)$_POST['planet'] .
							't' . (int)$_POST['planettype'];

				if ( $_POST['acs_target_mr'] == $target )
				{
					$aks_count_mr = parent::$db->query ( "SELECT COUNT(`acs_fleet_id`)
															FROM `" . ACS_FLEETS . "`
															WHERE `acs_fleet_id` = '" . (int)$_POST['fleet_group'] . "'" );

					if ($aks_count_mr > 0)
					{
						$fleet_group_mr = $_POST['fleet_group'];
					}
				}
			}
		}

		if(($_POST['fleet_group'] == 0) && ($_POST['mission'] == 2))
		{
			$_POST['mission'] = 1;
		}

		$TargetPlanet  		= parent::$db->query_fetch ( "SELECT `planet_user_id`,`planet_destroyed`
															FROM `" . PLANETS . "`
															 WHERE `planet_galaxy` = '". (int)$_POST['galaxy'] ."' AND
															 		`planet_system` = '". (int)$_POST['system'] ."' AND
															 		`planet_planet` = '". (int)$_POST['planet'] ."' AND
															 		`planet_type` = '". (int)$_POST['planettype'] ."';");

		$MyDBRec       		= parent::$db->query_fetch ( "SELECT u.`user_id`, u.`user_onlinetime`, u.`user_ally_id`, s.`setting_vacations_status`
															FROM " . USERS . " AS u, " . SETTINGS . " AS s
															WHERE u.`user_id` = '" . $this->_current_user['user_id']."'
																AND s.`setting_user_id` = '". $this->_current_user['user_id']."';");

		$fleetarray  = unserialize ( base64_decode ( str_rot13 ( $_POST['usedfleet'] ) ) );

		if ( $TargetPlanet['planet_destroyed'] != 0 )
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if ( !is_array ( $fleetarray ) )
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		foreach ( $fleetarray as $Ship => $Count )
		{
			$Count = intval ( $Count );

			if ($Count > $this->_current_planet[$resource[$Ship]])
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}
		}

		$error              = 0;
		$galaxy             = (int)$_POST['galaxy'];
		$system             = (int)$_POST['system'];
		$planet             = (int)$_POST['planet'];
		$planettype         = (int)$_POST['planettype'];
		$fleetmission       = (int)$_POST['mission'];

		//fix by jstar
		if ( $fleetmission == 7 && !isset ( $fleetarray[208] ) )
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if ($planettype != 1 && $planettype != 2 && $planettype != 3)
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		//fix invisible debris like ogame by jstar
		if ($fleetmission == 8)
		{
			$YourPlanet = FALSE;
			$UsedPlanet = FALSE;
			$select     = parent::$db->query_fetch ( "SELECT COUNT(*) AS count, p.*
														FROM `" . PLANETS . "` AS p
														WHERE `planet_galaxy` = '". $galaxy ."' AND
																`planet_system` = '". $system ."' AND
																`planet_planet` = '". $planet ."' AND
																`planet_type` = 1;" );

			if($select['planet_debris_metal'] == 0 && $select['planet_debris_crystal'] == 0 && time() > ($select['planet_invisible_start_time']+DEBRIS_LIFE_TIME))
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}
		}
		else
		{
			$YourPlanet = FALSE;
			$UsedPlanet = FALSE;
			$select     = parent::$db->query_fetch ( "SELECT COUNT(*) AS count, p.`planet_user_id`
														FROM `" . PLANETS . "` AS p
														WHERE `planet_galaxy` = '". $galaxy ."' AND
																`planet_system` = '". $system ."' AND
																`planet_planet` = '". $planet ."' AND
																`planet_type` = '". $planettype ."'" );
		}

		if ($this->_current_planet['planet_galaxy'] == $galaxy && $this->_current_planet['planet_system'] == $system &&
			$this->_current_planet['planet_planet'] == $planet && $this->_current_planet['planet_type'] == $planettype)
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if ($_POST['mission'] != 15)
		{
			if ($select['count'] < 1 && $fleetmission != 7)
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}
			elseif ($fleetmission == 9 && $select['count'] < 1)
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}
		}
		else
		{
			$MaxExpedition      = $this->_current_user[$resource[124]];

			if ($MaxExpedition >= 1)
			{
				$maxexpde  			= parent::$db->query_fetch ( "SELECT COUNT(fleet_owner) AS `expedi`
																	FROM " . FLEETS . "
																	WHERE `fleet_owner` = '" . $this->_current_user['user_id'] . "'
																		AND `fleet_mission` = '15';" );
				$ExpeditionEnCours  = $maxexpde['expedi'];
				$EnvoiMaxExpedition = Fleets_Lib::get_max_expeditions ( $MaxExpedition );
			}
			else
			{
				$ExpeditionEnCours 	= 0;
				$EnvoiMaxExpedition = 0;
			}

			if($EnvoiMaxExpedition == 0 )
			{
				Functions_Lib::message ("<font color=\"red\"><b>".$this->_lang['fl_expedition_tech_required']."</b></font>", "game.php?page=movement", 2);

			}
			elseif ($ExpeditionEnCours >= $EnvoiMaxExpedition )
			{
				Functions_Lib::message ("<font color=\"red\"><b>".$this->_lang['fl_expedition_fleets_limit']."</b></font>", "game.php?page=movement", 2);
			}
		}

		if ($select['planet_user_id'] == $this->_current_user['user_id'])
		{
			$YourPlanet = TRUE;
			$UsedPlanet = TRUE;
		}
		elseif (!empty($select['planet_user_id']))
		{
			$YourPlanet = FALSE;
			$UsedPlanet = TRUE;
		}
		else
		{
			$YourPlanet = FALSE;
			$UsedPlanet = FALSE;
		}

		//fix by jstar
		if($fleetmission == 9)
		{
			$countfleettype = count ( $fleetarray );

			if ( $YourPlanet or !$UsedPlanet or $planettype != 3 )
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}
			elseif ( $countfleettype == 1 && ! ( isset ( $fleetarray[214] ) ) )
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}
			elseif ( $countfleettype == 2 && ! ( isset ( $fleetarray[214] ) ) )
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}
			elseif ( $countfleettype > 2 )
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}
		}

		if ( empty ( $fleetmission ) )
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if ( $TargetPlanet['planet_user_id'] == '' )
		{
			$HeDBRec 	= $MyDBRec;
		}
		elseif ( $TargetPlanet['planet_user_id'] != '' )
		{
			$HeDBRec 	= parent::$db->query_fetch ( "SELECT u.`user_id`, u.`user_onlinetime`, u.`user_ally_id`, s.`setting_vacations_status`
														FROM " . USERS . " AS u, " . SETTINGS . " AS s
														WHERE u.`user_id` = '" . $TargetPlanet['planet_user_id'] ."'
															AND s.`setting_user_id` ='" . $TargetPlanet['planet_user_id'] ."';" );
		}

		$user_points	= $this->_noob->return_points ( $MyDBRec['user_id'] , $HeDBRec['user_id'] );
		$MyGameLevel  	= $user_points['user_points'];
		$HeGameLevel  	= $user_points['target_points'];

		if($HeDBRec['user_onlinetime'] >= (time()-60 * 60 * 24 * 7))
		{
			if ( $this->_noob->is_weak ( $MyGameLevel , $HeGameLevel ) &&
					$TargetPlanet['planet_user_id'] != '' &&
					($_POST['mission'] == 1 or $_POST['mission'] == 6 or $_POST['mission'] == 9))
			{
				Functions_Lib::message("<font color=\"lime\"><b>".$this->_lang['fl_week_player']."</b></font>", "game.php?page=movement", 2);
			}

			if ( $this->_noob->is_strong ( $MyGameLevel , $HeGameLevel ) &&
					$TargetPlanet['planet_user_id'] != '' &&
					($_POST['mission'] == 1 or $_POST['mission'] == 5 or $_POST['mission'] == 6 or $_POST['mission'] == 9))
			{
				Functions_Lib::message("<font color=\"red\"><b>".$this->_lang['fl_strong_player']."</b></font>", "game.php?page=movement", 2);
			}
		}

		if ( $HeDBRec['setting_vacations_status'] && $_POST['mission'] != 8 )
		{
			Functions_Lib::message("<font color=\"lime\"><b>".$this->_lang['fl_in_vacation_player']."</b></font>", "game.php?page=movement", 2);
		}

		$FlyingFleets = parent::$db->query_fetch ( "SELECT COUNT(fleet_id) as Number
													FROM " . FLEETS . "
													WHERE `fleet_owner`='" . $this->_current_user['user_id'] . "'" );
		$ActualFleets = $FlyingFleets['Number'];

		if ((Fleets_Lib::get_max_fleets ( $this->_current_user[$resource[108]] , $this->_current_user['premium_officier_admiral'] ) ) <= $ActualFleets)
		{
			Functions_Lib::message($this->_lang['fl_no_slots'], "game.php?page=movement", 1);
		}

		if ($_POST['resource1'] + $_POST['resource2'] + $_POST['resource3'] < 1 && $_POST['mission'] == 3)
		{
			Functions_Lib::message("<font color=\"lime\"><b>".$this->_lang['fl_empty_transport']."</b></font>", "game.php?page=movement", 1);
		}

		if ($_POST['mission'] != 15)
		{
			if ($TargetPlanet['planet_user_id'] == '' && $_POST['mission'] < 7)
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}

			if ($TargetPlanet['planet_user_id'] != '' && $_POST['mission'] == 7)
			{
				Functions_Lib::message ("<font color=\"red\"><b>".$this->_lang['fl_planet_populed']."</b></font>", "game.php?page=movement", 2);
			}

			if ($HeDBRec['user_ally_id'] != $MyDBRec['user_ally_id'] && $_POST['mission'] == 4)
			{
				Functions_Lib::message ("<font color=\"red\"><b>".$this->_lang['fl_stay_not_on_enemy']."</b></font>", "game.php?page=movement", 2);
			}

			if (($TargetPlanet['planet_user_id'] == $this->_current_planet['planet_user_id']) && (($_POST['mission'] == 1) or ($_POST['mission'] == 6)))
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}

			if (($TargetPlanet['planet_user_id'] != $this->_current_planet['planet_user_id']) && ($_POST['mission'] == 4))
			{
				Functions_Lib::message ("<font color=\"red\"><b>".$this->_lang['fl_deploy_only_your_planets']."</b></font>","game.php?page=movement", 2);
			}

			if($_POST['mission'] == 5)
			{
				$buddy = parent::$db->query_fetch ( "SELECT COUNT( * ) AS buddys
														FROM  `" . BUDDY . "`
															WHERE (
																(
																	buddy_sender ='" . intval($this->_current_planet['planet_user_id']) . "'
																	AND buddy_receiver ='" . intval($TargetPlanet['planet_user_id']) . "'
																)
																OR (
																	buddy_sender ='" . intval($TargetPlanet['planet_user_id']) . "'
																	AND buddy_receiver ='" . intval($this->_current_planet['planet_user_id']) . "'
																)
															)
															AND buddy_status =1" );

				if ( $HeDBRec['user_ally_id'] != $MyDBRec['user_ally_id'] && $buddy['buddys'] < 1 )
				{
					Functions_Lib::message ("<font color=\"red\"><b>".$this->_lang['fl_stay_not_on_enemy']."</b></font>", "game.php?page=movement", 2);
				}
			}
		}

		$missiontype	= Fleets_Lib::get_missions();
		$speed_possible	= array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
		$AllFleetSpeed	= Fleets_Lib::fleet_max_speed ($fleetarray, 0, $this->_current_user);
		$GenFleetSpeed  = $_POST['speed'];
		$SpeedFactor    = Functions_Lib::fleet_speed_factor();
		$MaxFleetSpeed  = min($AllFleetSpeed);

		if (!in_array($GenFleetSpeed, $speed_possible))
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if ($MaxFleetSpeed != $_POST['speedallsmin'])
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if (!$_POST['planettype'])
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if (!$_POST['galaxy'] || !is_numeric($_POST['galaxy']) || $_POST['galaxy'] > MAX_GALAXY_IN_WORLD || $_POST['galaxy'] < 1)
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if (!$_POST['system'] || !is_numeric($_POST['system']) || $_POST['system'] > MAX_SYSTEM_IN_GALAXY || $_POST['system'] < 1)
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if (!$_POST['planet'] || !is_numeric($_POST['planet']) || $_POST['planet'] > (MAX_PLANET_IN_SYSTEM + 1) || $_POST['planet'] < 1)
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if ($_POST['thisgalaxy'] != $this->_current_planet['planet_galaxy'] |
			$_POST['thissystem'] != $this->_current_planet['planet_system'] |
			$_POST['thisplanet'] != $this->_current_planet['planet_planet'] |
			$_POST['thisplanettype'] != $this->_current_planet['planet_type'])
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		if (!isset($fleetarray))
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		$distance      = Fleets_Lib::target_distance($_POST['thisgalaxy'], $_POST['galaxy'], $_POST['thissystem'], $_POST['system'], $_POST['thisplanet'], $_POST['planet']);
		$duration      = Fleets_Lib::mission_duration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
		$consumption   = Fleets_Lib::fleet_consumption($fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $this->_current_user);

		$fleet['start_time'] = $duration + time();

		// START CODE BY JSTAR
		if ($_POST['mission'] == 15)
		{
			$StayDuration	= floor($_POST['expeditiontime']);

			if ( $StayDuration > 0 )
			{
				$StayDuration    = $StayDuration  * 3600;
				$StayTime        = $fleet['start_time'] + $StayDuration;
			}
			else
			{
				Functions_Lib::redirect ( 'game.php?page=movement' );
			}
		} // END CODE BY JSTAR
		elseif ($_POST['mission'] == 5)
		{
			$StayDuration    = $_POST['holdingtime'] * 3600;
			$StayTime        = $fleet['start_time'] + $_POST['holdingtime'] * 3600;
		}
		else
		{
			$StayDuration    = 0;
			$StayTime        = 0;
		}

		$fleet['end_time']   = $StayDuration + (2 * $duration) + time();
		$FleetStorage        = 0;
		$FleetShipCount      = 0;
		$fleet_array         = "";
		$FleetSubQRY         = "";

		//fix by jstar
		$haveSpyProbos		= FALSE;

		foreach ($fleetarray as $Ship => $Count)
		{
			$Count = intval($Count);

			if($Ship == 210)
			{
				$haveSpyProbos = TRUE;
			}

			$FleetStorage    += $pricelist[$Ship]['capacity'] * $Count;
			$FleetShipCount  += $Count;
			$fleet_array     .= $Ship .",". $Count .";";
			$FleetSubQRY     .= "`".$resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . ", ";
		}

		if(!$haveSpyProbos && $_POST['mission'] == 6)
		{
			Functions_Lib::redirect ( 'game.php?page=movement' );
		}

		$FleetStorage        -= $consumption;
		$StorageNeeded        = 0;

		$_POST['resource1'] = max(0, (int)trim($_POST['resource1']));
		$_POST['resource2'] = max(0, (int)trim($_POST['resource2']));
		$_POST['resource3'] = max(0, (int)trim($_POST['resource3']));

		if ($_POST['resource1'] < 1)
		{
			$TransMetal      = 0;
		}
		else
		{
			$TransMetal      = $_POST['resource1'];
			$StorageNeeded  += $TransMetal;
		}

		if ($_POST['resource2'] < 1)
		{
			$TransCrystal    = 0;
		}
		else
		{
			$TransCrystal    = $_POST['resource2'];
			$StorageNeeded  += $TransCrystal;
		}
		if ($_POST['resource3'] < 1)
		{
			$TransDeuterium  = 0;
		}
		else
		{
			$TransDeuterium  = $_POST['resource3'];
			$StorageNeeded  += $TransDeuterium;
		}

		$StockMetal      = $this->_current_planet['planet_metal'];
		$StockCrystal    = $this->_current_planet['planet_crystal'];
		$StockDeuterium  = $this->_current_planet['planet_deuterium'];
		$StockDeuterium -= $consumption;

		$StockOk         = FALSE;

		if ($StockMetal >= $TransMetal)
		{
			if ($StockCrystal >= $TransCrystal)
			{
				if ($StockDeuterium >= $TransDeuterium)
				{
					$StockOk         = TRUE;
				}
			}
		}

		if (!$StockOk)
		{
			Functions_Lib::message ("<font color=\"red\"><b>". $this->_lang['fl_no_enought_deuterium'] . Format_Lib::pretty_number($consumption) ."</b></font>", "game.php?page=movement", 2);
		}

		if ( $StorageNeeded > $FleetStorage)
		{
			Functions_Lib::message ("<font color=\"red\"><b>". $this->_lang['fl_no_enought_cargo_capacity'] . Format_Lib::pretty_number($StorageNeeded - $FleetStorage) ."</b></font>", "game.php?page=movement", 2);
		}

		if ( Functions_Lib::read_config ( 'adm_attack' ) != 0 )
		{
			Functions_Lib::message($this->_lang['fl_admins_cannot_be_attacked'], "game.php?page=movement",2);
		}

		if ($fleet_group_mr != 0)
		{
			$AksStartTime = parent::$db->query_fetch ( "SELECT MAX(`fleet_start_time`) AS Start
														FROM " . FLEETS . "
														WHERE `fleet_group` = '". $fleet_group_mr . "';" );

			if ($AksStartTime['Start'] >= $fleet['start_time'])
			{
				$fleet['end_time']        += $AksStartTime['Start'] -  $fleet['start_time'];
				$fleet['start_time']     = $AksStartTime['Start'];
			}
			else
			{
				parent::$db->query ( "UPDATE " . FLEETS . " SET
										`fleet_start_time` = '". $fleet['start_time'] ."',
										`fleet_end_time` = fleet_end_time + '".($fleet['start_time'] - $AksStartTime['Start'])."'
										WHERE `fleet_group` = '". $fleet_group_mr ."';" );

				$fleet['end_time']         += $fleet['start_time'] -  $AksStartTime['Start'];
			}
		}

		parent::$db->query( "INSERT INTO " . FLEETS . " SET
							`fleet_owner` = '" . $this->_current_user['user_id']  . "',
							`fleet_mission` = '".(int)$_POST['mission']."',
							`fleet_amount` = '". (int)$FleetShipCount ."',
							`fleet_array` = '". $fleet_array ."',
							`fleet_start_time` = '". $fleet['start_time'] ."',
							`fleet_start_galaxy` = '". (int)$_POST['thisgalaxy'] ."',
							`fleet_start_system` = '". (int)$_POST['thissystem'] ."',
							`fleet_start_planet` = '". (int)$_POST['thisplanet'] ."',
							`fleet_start_type` = '". (int)$_POST['thisplanettype'] ."',
							`fleet_end_time` = '". (int)$fleet['end_time'] ."',
							`fleet_end_stay` = '". (int)$StayTime ."',
							`fleet_end_galaxy` = '". (int)$_POST['galaxy'] ."',
							`fleet_end_system` = '". (int)$_POST['system'] ."',
							`fleet_end_planet` = '". (int)$_POST['planet'] ."',
							`fleet_end_type` = '". (int)$_POST['planettype'] ."',
							`fleet_resource_metal` = '". $TransMetal ."',
							`fleet_resource_crystal` = '". $TransCrystal ."',
							`fleet_resource_deuterium` = '". $TransDeuterium ."',
							`fleet_target_owner` = '". (int)$TargetPlanet['planet_user_id'] ."',
							`fleet_group` = '".(int)$fleet_group_mr."',
							`fleet_creation` = '". time() ."';" );

		parent::$db->query ( "UPDATE `" . PLANETS . "` AS p
								INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
								$FleetSubQRY
								`planet_metal` = `planet_metal` - ". $TransMetal .",
								`planet_crystal` = `planet_crystal` - ". $TransCrystal .",
								`planet_deuterium` = `planet_deuterium` - ". ($TransDeuterium + $consumption) ."
								WHERE `planet_id` = ". $this->_current_planet['planet_id'] .";" );

		Functions_Lib::redirect ( 'game.php?page=movement' );
	}
}
/* end of fleet4.php */