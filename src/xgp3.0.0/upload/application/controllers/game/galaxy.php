<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

include_once ( XGP_ROOT . 'application/libraries/Galaxy_Lib.php' );

class Galaxy extends XGPCore
{
	const MODULE_ID	= 11;

	private $_planet_count = 0;
	private $_current_user;
	private $_current_planet;
	private $_lang;
	private $_galaxy;
	private $_system;
	private $_formula;
	private $_noob;

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

		$this->_current_user	= parent::$users->get_user_data();
		$this->_current_planet	= parent::$users->get_planet_data();
		$this->_lang			= parent::$lang;
		$this->_resource		= parent::$objects->get_objects();
		$this->_pricelist		= parent::$objects->get_price();
		$this->_reslist			= parent::$objects->get_objects_list();
		$this->_formula			= Functions_Lib::load_library ( 'Formula_Lib' );
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
		if ( isset ( $_GET['fleet'] ) && $_GET['fleet'] == 'true' )
		{
			$this->send_fleet();
		}

		if ( isset ( $_GET['missiles'] ) &&  $_GET['missiles'] == 'true' )
		{
			$this->send_missiles();
		}

		$fleetmax      	= Fleets_Lib::get_max_fleets ( $this->_current_user['research_computer_technology'] , $this->_current_user['premium_officier_admiral'] );
		$CurrentPlID   	= $this->_current_planet['planet_id'];
		$CurrentSP     	= $this->_current_planet['ship_espionage_probe'];

		$maxfleet       = parent::$db->query ( "SELECT `fleet_id`
												FROM " . FLEETS . "
												WHERE `fleet_owner` = '". intval($this->_current_user['user_id']) ."';");

		$maxfleet_count = parent::$db->num_rows ( $maxfleet );

		if ( !isset ( $mode ) )
		{
			if ( isset ( $_GET['mode'] ) )
			{
				$mode	= intval ( $_GET['mode'] );
			}
			else
			{
				$mode	= 0;
			}
		}

		$setted_position	= $this->validate_position ( $mode );
		$this->_galaxy		= $setted_position['galaxy'];
		$this->_system		= $setted_position['system'];
		$planet				= $setted_position['planet'];
		$psystem			= $setted_position['psystem'];

		// START FIX BY alivan
		if ( $mode != 2 )
		{
			if ( ( $this->_current_planet['planet_system'] != ( $psystem - 1 ) ) && ( $this->_current_planet['planet_system'] != isset ( $_GET['system'] ) or $this->_current_planet['planet_galaxy'] != isset ( $_GET['galaxy'] ) ) && ( $mode != 0 ) && ( $this->_current_planet['planet_deuterium'] < 10 ) )
			{
				die ( Functions_Lib::message ( $this->_lang['gl_no_deuterium_to_view_galaxy'] , "game.php?page=galaxy&mode=0" , 2 ) );
			}
			elseif ( ( $this->_current_planet['planet_system'] != ( $psystem - 1 ) ) && ( $this->_current_planet['planet_system'] != isset ( $_GET['system'] ) or $this->_current_planet['planet_galaxy'] != isset ( $_GET['galaxy'] ) ) && ( $mode != 0 ) )
			{
				$this->reduce_deuterium ();
			}
		}
		elseif ( $mode == 2 && $this->_current_planet['defense_interplanetary_missile'] < 1 )
		{
			die ( Functions_Lib::message ( $this->_lang['ma_no_missiles'] , "game.php?page=galaxy&mode=0" , 2 ) );
		}
		// END FIX BY alivan

		$this->_galaxy_data = parent::$db->query ( "SELECT
														(SELECT CONCAT ( GROUP_CONCAT(buddy_receiver) , ',' , GROUP_CONCAT(buddy_sender) ) AS buddys FROM " . BUDDY . " AS b WHERE (b.buddy_receiver = u.user_id OR b.buddy_sender = u.user_id ) ) AS buddys,
														p.planet_debris_metal AS metal,
														p.planet_debris_crystal AS crystal,
														p.`planet_id` AS id_planet,
														p.planet_galaxy,
														p.planet_system,
														p.planet_planet,
														p.planet_type,
														p.planet_destroyed,
														p.planet_name,
														p.planet_image,
														p.planet_last_update,
														p.planet_user_id,
														u.user_id,
														u.user_ally_id,
														u.user_banned,
														se.setting_vacations_status,
														u.user_onlinetime,
														u.user_name,
														s.user_statistic_total_rank,
														s.user_statistic_total_points,
														m.planet_id AS id_luna,
														m.planet_diameter,
														m.planet_temp_min,
														m.planet_destroyed AS destruyed_moon,
														m.planet_name AS name_moon,
														a.alliance_name,
														a.alliance_tag,
														a.alliance_web,
														(SELECT COUNT(user_id) AS `ally_members` FROM `" . USERS . "` WHERE `user_ally_id` = a.`alliance_id`) AS `ally_members`
													FROM " . PLANETS . " AS p
														INNER JOIN " . USERS . " AS u ON p.planet_user_id = u.user_id
														INNER JOIN " . SETTINGS . " AS se ON se.setting_user_id = u.user_id
														INNER JOIN " . USERS_STATISTICS . " AS s ON s.user_statistic_user_id = u.user_id
														LEFT JOIN " . ALLIANCE . " AS a ON a.alliance_id = u.user_ally_id
														LEFT JOIN " . PLANETS . " AS m ON m.planet_id = (SELECT mp.`planet_id`
																									FROM " . PLANETS . " AS mp
																									WHERE (mp.planet_galaxy=p.planet_galaxy AND
																											mp.planet_system=p.planet_system AND
																											mp.planet_planet=p.planet_planet AND
																											mp.planet_type=3))
													WHERE (p.planet_galaxy='".$this->_galaxy."' AND
															p.planet_system='".$this->_system."' AND
															p.planet_type='1' AND
															(p.planet_planet>'0' AND
															p.planet_planet<='".MAX_PLANET_IN_SYSTEM."'))
													ORDER BY p.planet_planet;" );

		$parse						= $this->_lang;
		$parse['js_path']			= XGP_ROOT . JS_PATH;
		$parse['galaxy']			= $this->_galaxy;
		$parse['system']			= $this->_system;
		$parse['planet']			= $planet;
		$parse['currentmip']		= $this->_current_planet['defense_interplanetary_missile'];
		$parse['maxfleetcount']		= $maxfleet_count;
		$parse['fleetmax']			= $fleetmax;
		$parse['recyclers']   		= Format_Lib::pretty_number ( $this->_current_planet['ship_recycler'] );
		$parse['spyprobes']   		= Format_Lib::pretty_number ( $CurrentSP );
		$parse['missile_count']		= sprintf ( $this->_lang['gl_missil_to_launch'] , $this->_current_planet['defense_interplanetary_missile'] );
		$parse['current']			= isset ( $_GET['current'] ) ? $_GET['current'] : NULL;
		$parse['current_galaxy']	= $this->_current_planet['planet_galaxy'];
		$parse['current_system']	= $this->_current_planet['planet_system'];
		$parse['current_planet']	= $this->_current_planet['planet_planet'];
		$parse['planet_type'] 		= $this->_current_planet['planet_type'];
		$parse['mip'] 				= ( $mode == 2 ) ? parent::$page->parse_template ( parent::$page->get_template ( 'galaxy/galaxy_missile_selector' ) , $parse ) : " ";
		$parse['galaxyrows'] 		= $this->show_row ();
		$parse['planetcount'] 		= $this->_planet_count . " " . $this->_lang['gl_populed_planets'];

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'galaxy/galaxy_body' ) , $parse ) , FALSE );
	}

	/**
	 * method ShowGalaxyRows
	 * param
	 * return validates the position setted by the user
	 */
	private function show_row ()
	{
		$rows					= '';
		$start					= 1;
		$template				= parent::$page->get_template ( 'galaxy/galaxy_row' );
		$galaxy_row 			= new Galaxy_Lib ( $this->_current_user , $this->_current_planet , $this->_galaxy , $this->_system );
		$parse['planet'] 		= '';
		$parse['planetname'] 	= '';
		$parse['moon'] 			= '';
		$parse['debris'] 		= '';
		$parse['username'] 		= '';
		$parse['alliance'] 		= '';
		$parse['actions'] 		= '';

		while ( $row_data = parent::$db->fetch_array ( $this->_galaxy_data ) )
		{
			for ( $current_planet = $start ; $current_planet < 1 + ( MAX_PLANET_IN_SYSTEM ) ; $current_planet++ )
			{
				if ( $row_data['planet_galaxy'] == $this->_galaxy && $row_data['planet_system'] == $this->_system && $row_data['planet_planet'] == $current_planet )
				{
					if ( $row_data['id_planet'] != 0 )
					{
						if ( $row_data['planet_destroyed'] != 0 && $row_data['planet_user_id'] != '' && $row_data['id_planet'] != '' )
						{
							$this->check_planet_state ( $row_data );
						}
						else
						{
							$this->_planet_count++;
						}

						if ( $row_data['id_luna'] != 0 && $row_data['destruyed_moon'] != 0 )
						{
							$this->check_moon_state ( $row_data );
						}
					}

					// PARSE THE ROW INTO THE ROW TEMPLATE
					$rows  .= parent::$page->parse_template ( $template , $galaxy_row->build_row ( $row_data , $current_planet ) );

					$start++;
					break;
				}
				else
				{
					$parse['pos']	= $start;
					$rows 		   .= parent::$page->parse_template ( $template , $parse );
					$start++;
				}
			}
		}

		for ( $i = $start; $i <= MAX_PLANET_IN_SYSTEM; $i++ )
		{
			$parse['pos']	= $i;
			$rows 			.= parent::$page->parse_template ( $template , $parse );
		}

		// CLEAN SOME DATA
		unset ( $row_data );

		// RETURN THE ROWS
		return	$rows;
	}

	/**
	 * method validate_position
	 * param $mode
	 * return validates the position setted by the user
	 */
	private function validate_position ( $mode )
	{
		$return['galaxy']	= '';
		$return['system']	= '';
		$return['planet']	= '';
		$return['psystem']	= '';

		switch ( $mode )
		{
			case 0:

				$galaxy        		= $this->_current_planet['planet_galaxy'];
				$system        		= $this->_current_planet['planet_system'];
				$planet       	 	= $this->_current_planet['planet_planet'];

			break;

			case 1:

				// ONLY NUMBERS
				$_POST['galaxy']	= ( isset ( $_POST['galaxy'] ) && intval ( $_POST['galaxy'] ) ) ? preg_replace ( "[^0-9]" , "" , $_POST['galaxy'] ) : 1;
				$_POST['system']	= ( isset ( $_POST['system'] ) && intval ( $_POST['system'] ) ) ? preg_replace ( "[^0-9]" , "" , $_POST['system'] ) : 1;

				// DO NOT GO FAR FAR AWAY.. xD
				$_POST['galaxy']	= $_POST['galaxy'] > MAX_GALAXY_IN_WORLD ? MAX_GALAXY_IN_WORLD : $_POST['galaxy'];
				$_POST['system']	= $_POST['system'] > MAX_SYSTEM_IN_GALAXY ? MAX_SYSTEM_IN_GALAXY : $_POST['system'];

				if ( isset ( $_POST['galaxyLeft'] ) )
				{
					if ( $_POST['galaxy'] < 1 )
					{
						$_POST['galaxy'] = 1;
						$galaxy          = 1;
					}
					elseif ( $_POST['galaxy'] == 1 )
					{
						$_POST['galaxy'] = 1;
						$galaxy          = 1;
					}
					else
					{
						$galaxy = $_POST['galaxy'] - 1;
					}
				}
				elseif ( isset ( $_POST['galaxyRight'] ) )
				{
					if ( $_POST['galaxy'] > MAX_GALAXY_IN_WORLD or $_POST['galaxyRight'] > MAX_GALAXY_IN_WORLD )
					{
						$_POST['galaxy']      = MAX_GALAXY_IN_WORLD;
						$_POST['galaxyRight'] = MAX_GALAXY_IN_WORLD;
						$galaxy               = MAX_GALAXY_IN_WORLD;
					}
					elseif ( $_POST['galaxy'] == MAX_GALAXY_IN_WORLD )
					{
						$_POST['galaxy']      = MAX_GALAXY_IN_WORLD;
						$galaxy               = MAX_GALAXY_IN_WORLD;
					}
					else
					{
						$galaxy = $_POST['galaxy'] + 1;
					}
				}
				else
				{
					$galaxy = $_POST['galaxy'];
				}

				if ( isset ( $_POST['systemLeft'] ) )
				{
					if ( $_POST['system'] < 1 )
					{
						$_POST['system'] = 1;
						$system          = 1;
					}
					elseif ( $_POST['system'] == 1 )
					{
						$_POST['system'] = 1;
						$system          = 1;
					}
					else
					{
						$system = $_POST['system'] - 1;
					}
				}
				elseif ( isset ( $_POST['systemRight'] ) )
				{
					if ( $_POST['system'] > MAX_SYSTEM_IN_GALAXY or $_POST['systemRight'] > MAX_SYSTEM_IN_GALAXY )
					{
						$_POST['system']      = MAX_SYSTEM_IN_GALAXY;
						$system               = MAX_SYSTEM_IN_GALAXY;
					}
					elseif ( $_POST['system'] == MAX_SYSTEM_IN_GALAXY )
					{
						$_POST['system']      = MAX_SYSTEM_IN_GALAXY;
						$system               = MAX_SYSTEM_IN_GALAXY;
					}
					else
					{
						$system	= $_POST['system'] + 1;
					}
				}
				else
				{
					$system	= $_POST['system'];
				}

			break;

			case 2:

				$galaxy        		= intval ( $_GET['galaxy'] );
				$system       		= intval ( $_GET['system'] );
				$planet        		= intval ( $_GET['planet'] );

			break;

			case 3:

				$galaxy        		= intval ( $_GET['galaxy'] );
				$system        		= intval ( $_GET['system'] );

			break;

			default;

				$galaxy        		= 1;
				$system        		= 1;

			break;
		}

		$return['galaxy']	= $galaxy;
		$return['system']	= $system;
		$return['planet']	= isset ( $planet ) ? $planet : NULL;
		$return['psystem']	= isset ( $_POST['system'] ) ? $_POST['system'] : NULL;

		return $return;
	}

	/**
	 * method send_missiles
	 * param
	 * return send missiles routine
	 */
	private function send_missiles()
	{
		$g     			= intval ( $_GET['galaxy'] );
		$s     			= intval ( $_GET['system'] );
		$i     			= intval ( $_GET['planet'] );
		$anz   			= ( $_POST['SendMI'] < 0 ) ? 0 : intval ( $_POST['SendMI'] );
		$target			= $_POST['Target'];

		$missiles      = $this->_current_planet['defense_interplanetary_missile'];
		$tempvar1      	= abs ( $s - $this->_current_planet['planet_system'] );
		$tempvar2      	= $this->_formula->missile_range ( $this->_current_user['research_impulse_drive'] );

		$tempvar3      	= parent::$db->query_fetch ( "SELECT u.`user_id`,u.`user_onlinetime`,s.`setting_vacations_status`
														FROM " . USERS . " AS u
														INNER JOIN " . SETTINGS . " AS s ON s.setting_user_id = u.user_id
														WHERE u.user_id = (SELECT `planet_user_id`
																		FROM " . PLANETS . "
																			WHERE planet_galaxy = ".$g."  AND
																					planet_system = ".$s." AND
																					planet_planet = ".$i." AND
																					planet_type = 1 LIMIT 1) LIMIT 1");

		$user_points	= $this->_noob->return_points ( $this->_current_user['user_id'] , $tempvar3['id'] );
		$MyGameLevel	= $user_points['user_points'];
		$HeGameLevel	= $user_points['target_points'];
		$error	 		= '';

		if ( $this->_current_planet['building_missile_silo'] < 4 )
		{
			$error .= $this->_lang['ma_silo_level'].'<br>';
			$errors++;
		}

		if ( $this->_current_user['research_impulse_drive'] == 0 )
		{
			$error .= $this->_lang['ma_impulse_drive_required'].'<br>';
			$errors++;
		}

		if ($tempvar1 >= $tempvar2 || $g != $this->_current_planet['planet_galaxy'])
		{
			$error .= $this->_lang['ma_not_send_other_galaxy'].'<br>';
			$errors++;
		}

		if (!$tempvar3)
		{
			$error .= $this->_lang['ma_planet_doesnt_exists'].'<br>';
			$errors++;
		}

		if ($anz > $missiles)
		{
			$error .= $this->_lang['ma_cant_send'] . $anz . $this->_lang['ma_missile'] . $missiles.'<br>';
			$errors++;
		}

		if (((!is_numeric($target) && $target != "all") or ($target < 0 or $target > 8)))
		{
			$error .= $this->_lang['ma_wrong_target'].'<br>';
			$errors++;
		}

		if ($missiles==0)
		{
			$error .= $this->_lang['ma_no_missiles'].'<br>';
			$errors++;
		}

		if ($anz==0)
		{
			$error .= $this->_lang['ma_add_missile_number'].'<br>';
			$errors++;
		}

		if ($tempvar3['user_onlinetime'] >= (time()-60 * 60 * 24 * 7))
		{
			if ( $this->_noob->is_weak ( $MyGameLevel , $HeGameLevel ) )
			{
				$error .= $this->_lang['fl_week_player'].'<br>';
				$errors++;
			}
			elseif ( $this->_noob->is_strong ( $MyGameLevel , $HeGameLevel ) )
			{
				$error .= $this->_lang['fl_strong_player'].'<br>';
				$errors++;
			}
		}
		if ($tempvar3['setting_vacations_status']==1){
			$error .= $this->_lang['fl_in_vacation_player'].'<br>';
			$errors++;
		}

		if ($errors != 0)
		{
			Functions_Lib::message ($error, "game.php?page=galaxy&mode=0&galaxy=".$g."&system=".$s, 3);
		}

		$ziel_id = $tempvar3['id'];

		$flugzeit = round(((30 + (60 * $tempvar1)) * 2500) / Functions_Lib::read_config ( 'fleet_speed' ) );

		$DefenseLabel =	array	(
									0 => $this->_lang['tech'][401],
									1 => $this->_lang['tech'][402],
									2 => $this->_lang['tech'][403],
									3 => $this->_lang['tech'][404],
									4 => $this->_lang['tech'][405],
									5 => $this->_lang['tech'][406],
									6 => $this->_lang['tech'][407],
									7 => $this->_lang['tech'][408],
									'all' => $this->_lang['ma_all']
								);


		parent::$db->query ( "INSERT INTO " . FLEETS . " SET
								fleet_owner = ".$this->_current_user['user_id'].",
								fleet_mission = 10,
								fleet_amount = ".$anz.",
								fleet_array = '503,".$anz."',
								fleet_start_time = '".(time() + $flugzeit)."',
								fleet_start_galaxy = '".$this->_current_planet['planet_galaxy']."',
								fleet_start_system = '".$this->_current_planet['planet_system']."',
								fleet_start_planet ='".$this->_current_planet['planet_planet']."',
								fleet_start_type = 1,
								fleet_end_time = '".(time() + $flugzeit+1)."',
								fleet_end_stay = 0,
								fleet_end_galaxy = '".$g."',
								fleet_end_system = '".$s."',
								fleet_end_planet = '".$i."',
								fleet_end_type = 1,
								fleet_target_obj = '".$target."',
								fleet_resource_metal = 0,
								fleet_resource_crystal = 0,
								fleet_resource_deuterium = 0,
								fleet_target_owner = '".$ziel_id."',
								fleet_group = 0,
								fleet_mess = 0,
								fleet_creation = ".time().";");

		parent::$db->query ( "UPDATE " . DEFENSES . " SET
								defense_interplanetary_missile = defense_interplanetary_missile - ".$anz."
								WHERE defense_planet_id =  '".$this->_current_user['user_current_planet']."'");

		Functions_Lib::message ( "<b>" . $anz . "</b>" . $this->_lang['ma_missiles_sended'] . $DefenseLabel[$target] , "game.php?page=overview" , 3 );
	}

	/**
	 * method send_fleet
	 * param
	 * return send fleet routine
	 */
	private function send_fleet()
	{
		$max_spy_probes		= $this->_current_user['setting_probes_amount'];
		$UserSpyProbes  	= $this->_current_planet['ship_espionage_probe'];
		$UserRecycles   	= $this->_current_planet['ship_recycler'];
		$UserDeuterium  	= $this->_current_planet['planet_deuterium'];
		$UserMissiles   	= $this->_current_planet['defense_interplanetary_missile'];
		$fleet          	= array();
		$speedalls      	= array();
		$PartialFleet   	= FALSE;
		$PartialCount   	= 0;
		$order				= isset ( $_POST['order'] ) ? $_POST['order'] : NULL;
		$ResultMessage		= '';
		$fleet['fleetlist']	= '';
		$fleet['amount']	= '';

		switch ( $order )
		{
			case 6:
				$_POST['ship210']	= $_POST['shipcount'];
			break;

			case 7:
				$_POST['ship208']	= $_POST['shipcount'];
			break;

			case 8;
				$_POST['ship209']	= $_POST['shipcount'];
			break;
		}

		foreach ( $this->_reslist['fleet'] as $Node => $ShipID )
		{
			$TName 		= "ship" . $ShipID;

			if ( isset ( $_POST[$TName] ) && $ShipID > 200 && $ShipID < 300 && $_POST[$TName] > 0 )
			{
				if ( $_POST[$TName] > $this->_current_planet[$this->_resource[$ShipID]] )
				{
					$fleet['fleetarray'][$ShipID]   = $this->_current_planet[$this->_resource[$ShipID]];
					$fleet['fleetlist']            .= $ShipID .",". $this->_current_planet[$this->_resource[$ShipID]] .";";
					$fleet['amount']               += $this->_current_planet[$this->_resource[$ShipID]];
					$PartialCount                  += $this->_current_planet[$this->_resource[$ShipID]];
					$PartialFleet                   = TRUE;
				}
				else
				{
					$fleet['fleetarray'][$ShipID]   = $_POST[$TName];
					$fleet['fleetlist']            .= $ShipID .",". $_POST[$TName] .";";
					$fleet['amount']               += $_POST[$TName];
					$speedalls[$ShipID]             = $_POST[$TName];
				}
			}
		}

		$errors_types	= array (
									600 => $this->_lang['gl_success'],
									601 => $this->_lang['gl_error'],
									602 => $this->_lang['gl_no_moon'],
									603 => $this->_lang['gl_noob_protection'],
									604 => $this->_lang['gl_too_strong'],
									605 => $this->_lang['gl_vacation_mode'],
									610 => $this->_lang['gl_only_amount_ships'],
									611 => $this->_lang['gl_no_ships'],
									612 => $this->_lang['gl_no_slots'],
									613 => $this->_lang['gl_no_deuterium'],
									614 => $this->_lang['gl_no_planet'],
									615 => $this->_lang['gl_not_enough_storage'],
									616 => $this->_lang['gl_multi_alarm'],
								);

		if ( $PartialFleet == TRUE )
		{
			if ( $PartialCount < 1 )
			{
				die ( "611 " );
			}
		}

		$galaxy         = isset ( $_POST['galaxy'] ) ? (int)$_POST['galaxy'] : 0;
		$system 		= isset ( $_POST['system'] ) ? (int)$_POST['system'] : 0;
		$planet 		= isset ( $_POST['planet'] ) ? (int)$_POST['planet'] : 0;
		$FleetArray 	= isset ( $fleet['fleetarray'] ) ? $fleet['fleetarray'] : NULL;

		if ( ( $galaxy > MAX_GALAXY_IN_WORLD or $galaxy < 1 )  or ( $system > MAX_SYSTEM_IN_GALAXY or $system < 1 ) or ( $planet > MAX_PLANET_IN_SYSTEM or $planet < 1 ) or ( is_null ( $FleetArray ) ) )
		{
			die ( "614 " );
		}


		$CurrentFlyingFleets	= parent::$db->query_fetch	( "SELECT COUNT(fleet_id) AS `Nbre`
																FROM " . FLEETS . "
																WHERE `fleet_owner` = '".$this->_current_user['user_id']."';" );

		$CurrentFlyingFleets	= $CurrentFlyingFleets['Nbre'];

		$TargetRow				= parent::$db->query_fetch	( "SELECT *
																FROM " . PLANETS . "
																WHERE `planet_galaxy` = '". parent::$db->escape_value ( $_POST['galaxy'] ) ."' AND
																		`planet_system` = '". parent::$db->escape_value ( $_POST['system'] ) ."' AND
																		`planet_planet` = '". parent::$db->escape_value ( $_POST['planet'] ) ."' AND
																		`planet_type` = '". parent::$db->escape_value ( $_POST['planettype'] ) ."';");

		if ( $TargetRow['planet_user_id'] == '' )
		{
			$TargetUser = $this->_current_user;
		}
		elseif ( $TargetRow['planet_user_id'] != '' )
		{
			$TargetUser = parent::$db->query_fetch ( "SELECT u.`user_id`, u.`user_onlinetime`, u.`user_authlevel`, s.`setting_vacations_status`
														FROM " . USERS . " AS u
														INNER JOIN " . SETTINGS . " AS s ON s.setting_user_id = u.user_id
														WHERE `user_id` = '". $TargetRow['planet_user_id'] ."';" );
		}

		// invisible debris by jstar
		if ( $order == 8 )
		{
			$TargetGPlanet 	= parent::$db->query_fetch	( "SELECT planet_invisible_start_time, planet_debris_metal, planet_debris_crystal
																FROM " . PLANETS . "
																WHERE planet_galaxy = '" . parent::$db->escape_value ( $_POST['galaxy'] ) ."' AND
																		planet_system = '" . parent::$db->escape_value ( $_POST['system'] ) ."' AND
																		planet_planet = '" . parent::$db->escape_value ( $_POST['planet'] ) ."' AND
																		planet_type = 1;" );

			if ( $TargetGPlanet['planet_debris_metal'] == 0 && $TargetGPlanet['planet_debris_crystal'] == 0 && time() > ( $TargetGPlanet['planet_invisible_start_time'] + DEBRIS_LIFE_TIME ) )
			{
				die();
			}
		}

		$user_points	= $this->_noob->return_points ( $this->_current_user['user_id'] , $TargetUser['user_id'] );
		$CurrentPoints 	= $user_points['user_points'];
		$TargetPoints  	= $user_points['target_points'];
		$TargetVacat   	= $TargetUser['setting_vacations_status'];

		if ( ( Fleets_Lib::get_max_fleets ( $this->_current_user[$this->_resource[108]] , $this->_current_user['premium_officier_admiral'] ) ) <= $CurrentFlyingFleets )
		{
			die ( "612 " );
		}

		if ( ! is_array ( $FleetArray ) )
		{
			die ( "611 " );
		}

		if ( ! ( ( $order == 6 ) or ( $order == 8 ) ) )
		{
			die ( "601 " );
		}

		if ( ( $TargetVacat && $order != 8 ) or $this->_current_user['setting_vacations_status'] )
		{
			die ( "605 " );
		}

		if ( $TargetUser['user_onlinetime'] >= ( time() - 60 * 60 * 24 * 7 ) )
		{
			if ( $this->_noob->is_weak ( $CurrentPoints , $TargetPoints ) && $TargetRow['planet_user_id'] != '' && $order == 6 )
			{
				die ( "603 " );
			}

			if ( $this->_noob->is_strong ( $CurrentPoints , $TargetPoints ) && $TargetRow['planet_user_id'] != '' && $order == 6 )
			{
				die ( "604 " );
			}
		}

		if ( $TargetRow['planet_user_id'] == '' && $order != 8 )
		{
			die ( "601 " );
		}

		if ( ( $TargetRow['planet_user_id'] == $this->_current_planet['planet_user_id'] ) && ( $order == 6 ) )
		{
			die ( "601 " );
		}

		$Distance    		= Fleets_Lib::target_distance ( $this->_current_planet['planet_galaxy'] , $_POST['galaxy'] , $this->_current_planet['planet_system'] , $_POST['system'] , $this->_current_planet['planet_planet'] , $_POST['planet'] );
		$speedall    		= Fleets_Lib::fleet_max_speed ( $FleetArray , 0 , $this->_current_user );
		$SpeedAllMin 		= min ( $speedall );
		$Duration    		= Fleets_Lib::mission_duration ( 10, $SpeedAllMin, $Distance, Functions_Lib::fleet_speed_factor());

		$fleet['fly_time']   = $Duration;
		$fleet['start_time'] = $Duration + time();
		$fleet['end_time']   = ($Duration * 2) + time();

		$FleetShipCount      = 0;
		$FleetDBArray        = "";
		$FleetSubQRY         = "";
		$consumption         = 0;
		$SpeedFactor         = Functions_Lib::fleet_speed_factor ();

		foreach ( $FleetArray as $Ship => $Count )
		{
			if ( $Ship != '' )
			{
				$ShipSpeed        = $this->_pricelist[$Ship]['speed'];
				$spd              = 35000 / ( $Duration * $SpeedFactor - 10 ) * sqrt ( $Distance * 10 / $ShipSpeed );
				$basicConsumption = $this->_pricelist[$Ship]['consumption'] * $Count ;
				$consumption     += $basicConsumption * $Distance / 35000 *  ( ( $spd / 10 ) + 1 ) * ( ( $spd / 10 ) + 1 );
				$FleetShipCount  += $Count;
				$FleetDBArray    .= $Ship .",". $Count .";";
				$FleetSubQRY     .= "`".$this->_resource[$Ship] . "` = `" . $this->_resource[$Ship] . "` - " . $Count . " , ";
			}
		}

		$consumption 		= round ( $consumption ) + 1;

		if ( $UserDeuterium < $consumption )
		{
			die ( "613 " );
		}

		if ( Functions_Lib::read_config ( 'adm_attack' ) == 1 && $TargetUser['user_authlevel'] > 0 )
		{
			die ( "601 " );
		}

		parent::$db->query ( "INSERT INTO " . FLEETS . " SET
								`fleet_owner` = '". $this->_current_user['user_id'] ."',
								`fleet_mission` = '". intval ( $order ) ."',
								`fleet_amount` = '". $FleetShipCount ."',
								`fleet_array` = '". $FleetDBArray ."',
								`fleet_start_time` = '". $fleet['start_time']. "',
								`fleet_start_galaxy` = '". $this->_current_planet['planet_galaxy'] ."',
								`fleet_start_system` = '". $this->_current_planet['planet_system'] ."',
								`fleet_start_planet` = '". $this->_current_planet['planet_planet'] ."',
								`fleet_start_type` = '". $this->_current_planet['planet_type'] ."',
								`fleet_end_time` = '". $fleet['end_time'] ."',
								`fleet_end_galaxy` = '". intval($_POST['galaxy']) ."',
								`fleet_end_system` = '". intval($_POST['system']) ."',
								`fleet_end_planet` = '". intval($_POST['planet']) ."',
								`fleet_end_type` = '". intval($_POST['planettype']) ."',
								`fleet_target_owner` = '". $TargetRow['planet_user_id'] ."',
								`fleet_creation` = '" . time() . "';" );

		$UserDeuterium   -= $consumption;

		parent::$db->query( "UPDATE " . PLANETS . " AS p
								INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
								$FleetSubQRY
								p.`planet_deuterium` = '" . ( ( $UserDeuterium < 1 ) ? 0 : $UserDeuterium ) . "'
								WHERE p.`planet_id` = '". $this->_current_planet['planet_id'] ."';" );

		$CurrentFlyingFleets++;

		foreach ( $FleetArray as $Ships => $Count )
		{
			if ( $max_spy_probes > $this->_current_planet[$this->_resource[$Ships]] )
			{
				$ResultMessage = "610 " . $FleetShipCount;
			}
		}

		if ( $ResultMessage == '' )
		{
			$ResultMessage  = "600 " . $Ships;
		}

		die ( $ResultMessage );
	}

	/**
	 * method reduce_deuterium
	 * param
	 * return reduce deuterium exploring the galaxy
	 */
	private function reduce_deuterium ()
	{
		parent::$db->query ( "UPDATE " . PLANETS . " SET
								`planet_deuterium` = `planet_deuterium` -  10
								WHERE `planet_id` = '". $this->_current_planet['planet_id'] ."' LIMIT 1" );
	}

	/**
	 * method check_moon_state
	 * param $lunarow
	 * return
	 */
	private function check_moon_state ( $lunarow )
	{
		if ( ( $lunarow['planet_destroyed'] + 172800 ) <= time() && $lunarow['planet_destroyed'] != 0 && $lunarow['planet_type'] == 3 )
		{
			parent::$db->query ( "DELETE p,b,d,s FROM " . PLANETS . " AS p
									INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
									INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
									INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
									WHERE `planet_id` = '" . intval($lunarow['id']) . "';" );
		}
	}

	/**
	 * method check_planet_state
	 * param $planet
	 * return validates the position setted by the user
	 */
	private function check_planet_state ( $planet )
	{
		if ( $planet['planet_destroyed'] <= time() && $planet['planet_type'] == 1 )
		{
			parent::$db->query ( "DELETE p,b,d,s FROM " . PLANETS . " AS p
									INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
									INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
									INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
									WHERE `planet_id` = '" . $planet['id_planet'] . "';" );
		}
	}
}
/* end of galaxy.php */