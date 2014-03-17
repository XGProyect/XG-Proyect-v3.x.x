<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( 'location:../../' ) );

class Colonize extends Missions
{
	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * method colonize_mission
	 * param $fleet_row
	 * return the colonization result
	*/
	public function colonize_mission ( $fleet_row )
	{
		if ( $fleet_row['fleet_mess'] == 0 )
		{
			$colonization_check	= parent::$db->query_fetch	( "SELECT
																	(SELECT COUNT(*)
																		FROM " . PLANETS . " AS pc1
																		WHERE pc1.`planet_user_id` = '". $fleet_row['fleet_owner'] ."' AND
																				pc1.`planet_type` = '1' AND
																				pc1.`planet_destroyed` = '0') AS planet_count,
																	(SELECT COUNT(*)
																		FROM " . PLANETS . " AS pc2
																		WHERE pc2.`planet_galaxy` = '". $fleet_row['fleet_end_galaxy']."' AND
																				pc2.`planet_system` = '". $fleet_row['fleet_end_system']."' AND
																				pc2.`planet_planet` = '". $fleet_row['fleet_end_planet']." AND
																				pc2.`planet_type` = 1') AS galaxy_count,
																	(SELECT `research_astrophysics`
																		FROM " . RESEARCH . "
																		WHERE `research_user_id` = '". $fleet_row['fleet_owner']."') AS astro_level"

															);

			// SOME REQUIRED VALUES
			$target_coords 	= sprintf ( $this->_lang['sys_adress_planet'] , $fleet_row['fleet_end_galaxy'] , $fleet_row['fleet_end_system'] , $fleet_row['fleet_end_planet'] );
			$max_colonies	= Fleets_Lib::get_max_colonies ( $colonization_check['astro_level'] );
			$planet_count	= $colonization_check['planet_count'] - 1; // THE TOTAL AMOUNT OF PLANETS MINUS 1 (BECAUSE THE MAIN PLANET IT'S NOT CONSIDERED)

			// DIFFERENT TYPES OF MESSAGES
			$message[1]		= $this->_lang['sys_colo_arrival'] . $target_coords . $this->_lang['sys_colo_maxcolo'] . ( $max_colonies + 1 ) . $this->_lang['sys_colo_planet'];
			$message[2]		= $this->_lang['sys_colo_arrival'] . $target_coords . $this->_lang['sys_colo_allisok'];
			$message[3]		= $this->_lang['sys_colo_arrival'] . $target_coords . $this->_lang['sys_colo_notfree'];
			$message[4]		= $this->_lang['sys_colo_arrival'] . $target_coords . $this->_lang['sys_colo_astro_level'];

			if ( $colonization_check['galaxy_count'] == 0 )
			{
				if ( $planet_count >= $max_colonies )
				{
					$this->colonize_message ( $fleet_row['fleet_owner'] , $message[1] , $fleet_row['fleet_start_time'] );

					parent::return_fleet ( $fleet_row['fleet_id'] );
				}
				elseif ( ! $this->position_allowed ( $fleet_row['fleet_end_planet'] , $colonization_check['astro_level'] ) )
				{
					$this->colonize_message ( $fleet_row['fleet_owner'] , $message[4] , $fleet_row['fleet_start_time'] );

					parent::return_fleet ( $fleet_row['fleet_id'] );
				}
				else
				{
					if ( $this->start_creation ( $fleet_row ) )
					{
						$this->colonize_message ( $fleet_row['fleet_owner'] , $message[2] , $fleet_row['fleet_start_time'] );

						if ( $fleet_row['fleet_amount'] == 1 )
						{
							parent::$db->query ( "UPDATE " . USERS_STATISTICS . " AS us SET
													us.`user_statistic_ships_points` = us.`user_statistic_ships_points` - " . Statistics_Lib::calculate_points ( 208 , 1 ) . "
													WHERE us.`user_statistic_user_id` = (SELECT p.planet_user_id FROM " . PLANETS . " AS p
																							WHERE p.planet_galaxy = '". $fleet_row['fleet_start_galaxy'] ."' AND
																									p.planet_system = '". $fleet_row['fleet_start_system'] ."' AND
																									p.planet_planet = '". $fleet_row['fleet_start_planet'] ."' AND
																									p.planet_type = '". $fleet_row['fleet_start_type'] ."');" );

							parent::store_resources ( $fleet_row );
							parent::remove_fleet ( $fleet_row['fleet_id'] );
						}
						else
						{
							parent::store_resources ( $fleet_row );

							parent::$db->query ( "UPDATE " . FLEETS . ", " . USERS_STATISTICS . " SET
													`fleet_array` = '" . $this->build_new_fleet ( $fleet_row['fleet_array'] ) . "',
													`fleet_amount` = `fleet_amount` - 1,
													`fleet_resource_metal` = '0',
													`fleet_resource_crystal` = '0',
													`fleet_resource_deuterium` = '0',
													`fleet_mess` = '1',
													`user_statistic_ships_points` = `user_statistic_ships_points` - " . Statistics_Lib::calculate_points ( 208 , 1 ) . "
													WHERE `fleet_id` = '". $fleet_row['fleet_id'] ."' AND
															`user_statistic_user_id` = (SELECT planet_user_id FROM " . PLANETS . "
																							WHERE planet_galaxy = '". $fleet_row['fleet_start_galaxy'] ."' AND
																									planet_system = '". $fleet_row['fleet_start_system'] ."' AND
																									planet_planet = '". $fleet_row['fleet_start_planet'] ."' AND
																									planet_type = '". $fleet_row['fleet_start_type'] ."');" );
						}
					}
					else
					{
						$this->colonize_message ( $fleet_row['fleet_owner'] , $message[3] , $fleet_row['fleet_end_time'] );

						parent::return_fleet ( $fleet_row['fleet_id'] );
					}
				}
			}
			else
			{
				$this->colonize_message ( $fleet_row['fleet_owner'] , $message[3] , $fleet_row['fleet_end_time'] );

				parent::return_fleet ( $fleet_row['fleet_id'] );
			}
		}

		if ( $fleet_row['fleet_end_time'] < time() )
		{
			parent::restore_fleet ( $fleet_row , TRUE );
			parent::remove_fleet ( $fleet_row['fleet_id'] );
		}
	}

	/**
	 * method start_creation
	 * param $fleet_row
	 * return process the planet creation and returns a result
	*/
	private function start_creation ( $fleet_row )
	{
		include_once ( XGP_ROOT . 'application/libraries/Creator_Lib.php' );

		$creator	= new Creator_Lib();

		return $creator->create_planet ( $fleet_row['fleet_end_galaxy'] , $fleet_row['fleet_end_system'] , $fleet_row['fleet_end_planet'] , $fleet_row['fleet_owner'] , $this->_lang['sys_colo_defaultname'] , FALSE );
	}

	/**
	 * method build_new_fleet
	 * param $fleet_array
	 * return the new fleet, with a less colony ship
	*/
	private function build_new_fleet ( $fleet_array )
	{
		$current_fleet	= explode ( ';' , $fleet_array );
		$new_fleet     	= '';

		foreach ( $current_fleet as $item => $group )
		{
			if ( $group != '' )
			{
				$ship	= explode ( ',' , $group );

				if ( $ship[0] == 208 )
				{
					if ( $ship[1] > 1 )
					{
						$new_fleet	.= $ship[0] . ',' . ( $ship[1] - 1 ) . ',';
					}
				}
				else
				{
					if ( $ship[1] <> 0 )
					{
						$new_fleet  .= $ship[0] . ',' . $ship[1] . ',';
					}
				}
			}
		}

		return $new_fleet;
	}

	/**
	 * method colonize_message
	 * param $owner
	 * param $message
	 * param $time
	 * return send a message with the colonization details
	*/
	private function colonize_message ( $owner , $message , $time )
	{
		Functions_Lib::send_message ( $owner , '' , $time , 5 , $this->_lang['sys_colo_mess_from'] , $this->_lang['sys_colo_mess_report'] , $message );
	}

	/**
	 * method position_allowed
	 * param $position
	 * param $level
	 * return send a message with the colonization details
	 */
	private function position_allowed ( $position , $level )
	{
		// CHECK IF THE POSITION IS NEAR THE SPACE LIMITS
		if ( $position <= 3 or $position >= 13 )
		{
			// POSITIONS 3 AND 13 CAN BE POPULATED FROM LEVEL 4 ONWARDS.
			if ( $level >= 4  && ( $position == 3 or $position == 13 ) )
			{
				return TRUE;
			}

			// POSITIONS 2 AND 14 CAN BE POPULATED FROM LEVEL 6 ONWARDS.
			if ( $level >= 6  && ( $position == 2 or $position == 14 ) )
			{
				return TRUE;
			}

			// POSITIONS 1 AND 15 CAN BE POPULATED FROM LEVEL 8 ONWARDS.
			if ( $level >= 8 )
			{
				return TRUE;
			}

			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}
/* end of colonize.php */