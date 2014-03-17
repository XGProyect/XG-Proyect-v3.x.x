<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Fleets_Lib extends XGPCore
{
	/**
	 * method ship_consumption
	 * param1 $ship
	 * param2 $user
	 * return the ship consumption
	 */
	public static function ship_consumption ( $ship , $user )
	{
		if ( $user['research_impulse_drive'] >= 5 )
		{
			return parent::$objects->get_price ( $ship , 'consumption2' );
		}
		else
		{
			return parent::$objects->get_price ( $ship , 'consumption' );
		}
	}

	/**
	 * method target_distance
	 * param1 $orig_galaxy
	 * param2 $dest_galaxy
	 * param3 $orig_system
	 * param4 $dest_system
	 * param5 $orig_planet
	 * param6 $dest_planet
	 * return the distance to the target
	 */
	public static function target_distance ( $orig_galaxy , $dest_galaxy , $orig_system , $dest_system , $orig_planet , $dest_planet )
	{
		$distance = 0;

		if ( ( $orig_galaxy - $dest_galaxy ) != 0 )
		{
			$distance = abs ( $orig_galaxy - $dest_galaxy ) * 20000;
		}
		elseif ( ( $orig_system - $dest_system ) != 0)
		{
			$distance = abs ( $orig_system - $dest_system ) * 5 * 19 + 2700;
		}
		elseif ( ( $orig_planet - $dest_planet ) != 0)
		{
			$distance = abs ( $orig_planet - $dest_planet ) * 5 + 1000;
		}
		else
		{
			$distance = 5;
		}

		return $distance;
	}

	/**
	 * method mission_duration
	 * param1 $game_speed
	 * param2 $max_fleet_speed
	 * param3 $distance
	 * param4 $speed_factor
	 * return the mission duration
	 */
	public static function mission_duration ( $game_speed , $max_fleet_speed , $distance , $speed_factor )
	{
		return round ( ( ( 35000 / $game_speed * sqrt ( $distance * 10 / $max_fleet_speed ) + 10 ) / $speed_factor ) );
	}

	/**
	 * method fleet_max_speed
	 * param1 $fleet_array
	 * param2 $fleet
	 * param3 $user
	 * return the fleet maximum speed
	 */
	public static function fleet_max_speed ( $fleet_array , $fleet , $user )
	{
		$pricelist	= parent::$objects->get_price();
		$reslist	= parent::$objects->get_objects_list();

		$speed_all	= array();

		if ( $fleet != 0 )
		{
			$fleet_array[$fleet] =  1;
		}

		foreach ( $fleet_array as $ship => $count )
		{
			if ( $ship == 202 )
			{
				if ( $user['research_impulse_drive'] >= 5 )
				{
					$speed_all[$ship]	= $pricelist[$ship]['speed2'] + ( ( $pricelist[$ship]['speed'] * $user['research_impulse_drive'] ) * 0.2 );
				}
				else
				{
					$speed_all[$ship]	= $pricelist[$ship]['speed']  + ( ( $pricelist[$ship]['speed'] * $user['research_combustion_drive'] ) * 0.1 );
				}

			}

			if ( $ship == 203 or $ship == 204 or $ship == 209 or $ship == 210 )
			{
				$speed_all[$ship] 		= $pricelist[$ship]['speed'] + ( ( $pricelist[$ship]['speed'] * $user['research_combustion_drive'] ) * 0.1 );
			}


			if ( $ship == 205 or $ship == 206 or $ship == 208 )
			{
				$speed_all[$ship] 		= $pricelist[$ship]['speed'] + ( ( $pricelist[$ship]['speed'] * $user['research_impulse_drive'] ) * 0.2 );
			}

			if ( $ship == 211 )
			{
				if ( $user['research_hyperspace_drive'] >= 8 )
				{
					$speed_all[$ship] 	= $pricelist[$ship]['speed2'] + ( ( $pricelist[$ship]['speed'] * $user['research_hyperspace_drive'] ) * 0.3 );

				}
				else
				{
					$speed_all[$ship] 	= $pricelist[$ship]['speed2'] + ( ( $pricelist[$ship]['speed'] * $user['research_hyperspace_drive'] ) * 0.3 );
				}
 			}

			if ( $ship == 207 or $ship == 213 or $ship == 214 or $ship == 215 )
			{
				$speed_all[$ship] 		= $pricelist[$ship]['speed'] + ( ( $pricelist[$ship]['speed'] * $user['research_hyperspace_drive'] ) * 0.3 );
			}
		}

		if ( $fleet != 0 )
		{
			$ship_speed	= isset ( $speed_all[$ship] ) ? $speed_all[$ship] : 0;
			$speed_all	= $ship_speed;
		}

		return $speed_all;
	}

	/**
	 * method fleet_consumption
	 * param1 $fleet_array
	 * param2 $speed_factor
	 * param3 $mission_duration
	 * param4 $mission_distance
	 * param5 $fleet_max_speed
	 * param6 $user
	 * return the final fleet consumption based on all the data obtained
	 */
	public static function fleet_consumption ( $fleet_array , $speed_factor , $mission_duration , $mission_distance , $fleet_max_speed , $user )
	{
		$consumption 		= 0;
		$basic_consumption 	= 0;

		foreach ( $fleet_array as $ship => $count )
		{
			if ( $ship > 0 )
			{
				$ship_speed         = self::fleet_max_speed ( "" , $ship , $user );
				$ship_consumption   = self::ship_consumption ( $ship , $user );
				$spd              	= 35000 / ( $mission_duration * $speed_factor - 10 ) * sqrt ( $mission_distance * 10 / $ship_speed );
				$basic_consumption	= $ship_consumption * $count;
				$consumption       += $basic_consumption * $mission_distance / 35000 * ( ( $spd / 10 ) + 1 ) * ( ( $spd / 10 ) + 1 );
			}
		}

		return ( round ( $consumption ) + 1 );
	}

	/**
	 * method get_max_fleets
	 * param1 $computer_tech
	 * param2 $amiral_level
	 * return the max quantity of fleets that an user can send
	 */
	public static function get_max_fleets ( $computer_tech , $amiral_level )
	{
		return Officiers_Lib::get_max_computer ( $computer_tech , $amiral_level );
	}

	/**
	 * method get_max_expeditions
	 * param $expedition_tech
	 * return the max quantity of expeditions that an user can send
	 */
	public static function get_max_expeditions ( $astrophysics_tech )
	{
		return floor ( sqrt ( $astrophysics_tech ) );
	}

	/**
	 * method get_max_expeditions
	 * param $expedition_tech
	 * return the max quantity of expeditions that an user can send
	 */
	public static function get_max_colonies ( $astrophysics_tech )
	{
		return ceil ( $astrophysics_tech / 2 );
	}

	/**
	 * method get_missions
	 * param $mission_number
	 * return all the missions or one specific mission
	 */
	public static function get_missions ( $mission_number = 0 )
	{
		$mission_type	=	array	(
										1 => parent::$lang['type_mission'][1],
										2 => parent::$lang['type_mission'][2],
										3 => parent::$lang['type_mission'][3],
										4 => parent::$lang['type_mission'][4],
										5 => parent::$lang['type_mission'][5],
										6 => parent::$lang['type_mission'][6],
										7 => parent::$lang['type_mission'][7],
										8 => parent::$lang['type_mission'][8],
										9 => parent::$lang['type_mission'][9],
										15 => parent::$lang['type_mission'][15]
									);

		if ( $mission_number === 0 )
		{
			return $mission_type;
		}
		else
		{
			return $mission_type[$mission_number];
		}
	}

	/**
	 * method start_link
	 * param1 $fleet_row
	 * param2 $fleet_type
	 * return creates the link with the coordinates of the start planet
	 */
	public static function start_link ( $fleet_row , $fleet_type )
	{
		return "<a href=\"game.php?page=galaxy&mode=3&galaxy=" . $fleet_row['fleet_start_galaxy'] . "&system=" . $fleet_row['fleet_start_system'] . "\" " . $fleet_type . " >[" . $fleet_row['fleet_start_galaxy'] . ":" . $fleet_row['fleet_start_system'] . ":" . $fleet_row['fleet_start_planet'] . "]</a>";
	}

	/**
	 * method target_link
	 * param1 $fleet_row
	 * param2 $fleet_type
	 * return creates the link with the coordinates of the target planet
	 */
	public static function target_link ( $fleet_row , $fleet_type )
	{
		return "<a href=\"game.php?page=galaxy&mode=3&galaxy=" . $fleet_row['fleet_end_galaxy'] . "&system=" . $fleet_row['fleet_end_system'] . "\" " . $fleet_type . " >[" . $fleet_row['fleet_end_galaxy'] . ":" . $fleet_row['fleet_end_system'] . ":" . $fleet_row['fleet_end_planet'] . "]</a>";
	}

	/**
	 * method fleet_resources_popup
	 * param1 $fleet_row
	 * param2 $text
	 * param3 $fleet_type
	 * return creates the link with the coordinates of the target planet
	 */
	public static function fleet_resources_popup ( $fleet_row , $text , $fleet_type )
	{
		$total_resources  = $fleet_row['fleet_resource_metal'] + $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'];

		if ( $total_resources <> 0 )
		{
			$resources_popup   = "<table width=200>";
			$resources_popup  .= "<tr><td width=50% align=left><font color=white>" . parent::$lang['Metal'] . "<font></td><td width=50% align=right><font color=white>" . Format_Lib::pretty_number ( $fleet_row['fleet_resource_metal'] ) . "<font></td></tr>";
			$resources_popup  .= "<tr><td width=50% align=left><font color=white>" . parent::$lang['Crystal'] . "<font></td><td width=50% align=right><font color=white>" . Format_Lib::pretty_number ( $fleet_row['fleet_resource_crystal'] ) . "<font></td></tr>";
			$resources_popup  .= "<tr><td width=50% align=left><font color=white>" . parent::$lang['Deuterium'] . "<font></td><td width=50% align=right><font color=white>" . Format_Lib::pretty_number ( $fleet_row['fleet_resource_deuterium'] ) . "<font></td></tr>";
			$resources_popup  .= "</table>";
		}
		else
		{
			$resources_popup   = "";
		}

		if ( $resources_popup <> "" )
		{
			$pop_up  = "<a href='#' onmouseover=\"return overlib('". $resources_popup ."');";
			$pop_up .= "\" onmouseout=\"return nd();\" class=\"". $fleet_type ."\">" . $text ."</a>";
		}
		else
		{
			$pop_up	= $text ."";
		}

		return $pop_up;
	}

	/**
	 * method fleet_ships_popup
	 * param $fleet_row
	 * param $text
	 * param $fleet_type
	 * param $current_user
	 * return creates the link with the coordinates of the target planet
	 */
	public static function fleet_ships_popup ( $fleet_row , $text , $fleet_type , $current_user = '' )
	{
		$ships		= explode ( ";" , $fleet_row['fleet_array'] );
		$pop_up   	= "<a href='#' onmouseover=\"return overlib('";
		$pop_up    .= "<table width=200>";

		$espionage_tech	= Officiers_Lib::get_max_espionage ( $current_user['research_espionage_technology'] , $current_user['premium_officier_technocrat'] );

		if ( $espionage_tech < 2 && $fleet_row['fleet_owner'] != $current_user['user_id'] )
		{
			$pop_up .= "<tr><td width=50% align=left><font color=white>" . parent::$lang['cff_no_fleet_data'] . "<font></td></tr>";
		}
		elseif ( $espionage_tech >= 2 && $espionage_tech < 4 && $fleet_row['fleet_owner'] != $current_user['user_id'] )
		{
			$pop_up .= "<tr><td width=50% align=left><font color=white>" . parent::$lang['cff_aproaching'] . $fleet_row['fleet_amount'] . parent::$lang['cff_ships'] . "<font></td></tr>";
		}
		else
		{
			if ( $fleet_row['fleet_owner'] != $current_user['user_id'] )
			{
				$pop_up .= "<tr><td width=100% align=left><font color=white>" . parent::$lang['cff_aproaching'] . $fleet_row['fleet_amount'] . parent::$lang['cff_ships'] . ":<font></td></tr>";
			}

			foreach ( $ships as $item => $group )
			{
				if ( $group != '' )
				{
					$ship	= explode ( ',' , $group );

					if ( $fleet_row['fleet_owner'] == $current_user['user_id'] )
					{
						$pop_up .= "<tr><td width=50% align=left><font color=white>" . parent::$lang['tech'][$ship[0]] . ":<font></td><td width=50% align=right><font color=white>" . Format_Lib::pretty_number ( $ship[1] ) . "<font></td></tr>";
					}
					elseif ( $fleet_row['fleet_owner'] != $current_user['user_id'] )
					{
						if ( $espionage_tech >= 4 && $espionage_tech < 8 )
						{
							$pop_up .= "<tr><td width=50% align=left><font color=white>" . parent::$lang['tech'][$ship[0]] . "<font></td></tr>";
						}
						elseif ( $espionage_tech >= 8 )
						{
							$pop_up .= "<tr><td width=50% align=left><font color=white>" . parent::$lang['tech'][$ship[0]] . ":<font></td><td width=50% align=right><font color=white>" . Format_Lib::pretty_number($ship[1]) . "<font></td></tr>";
						}
					}
				}
			}
		}

		$pop_up  .= "</table>";
		$pop_up  .= "');\" onmouseout=\"return nd();\" class=\"". $fleet_type ."\">". $text ."</a>";

		return $pop_up;
	}

	/**
	 * method enemy_link
	 * param1 $fleet_row
	 * return the enemy name and send a message to the enemy in a link format
	 */
	public static function enemy_link ( $fleet_row )
	{
		$enemy_name	= parent::$db->query_fetch ( "SELECT `user_name`
													FROM " . USERS . "
													WHERE `user_id` = '" . intval ( $fleet_row['fleet_owner'] ) . "';" );

		$link  		 = $enemy_name['user_name'] . " ";
		$link 		.= "<a href=\"game.php?page=messages&mode=write&id=".$fleet_row['fleet_owner']."\">";
		$link 		.= "<img src=\"" . DPATH . "/img/m.gif\" title=\"" . parent::$lang['write_message'] . "\" border=\"0\"></a>";

		return $link;
	}

	/**
	 * method flying_fleets_table
	 * param $fleet_row
	 * param $Status
	 * param $Owner
	 * param $Label
	 * param $Record
	 * param $current_user
	 * return the fleet table for the overview and phalanx
	 */
	public static function flying_fleets_table ( $fleet_row , $Status , $Owner , $Label , $Record , $current_user )
	{
		$FleetStyle  	= array	(	1 => 'attack' ,
									2 => 'federation' ,
									3 => 'transport' ,
									4 => 'deploy' ,
									5 => 'hold' ,
									6 => 'espionage' ,
									7 => 'colony' ,
									8 => 'harvest' ,
									9 => 'destroy' ,
									10 => 'missile' ,
									15 => 'transport'
								);

		$FleetStatus 	= array ( 0 => 'flight' , 1 => 'holding' , 2 => 'return' );
		$FleetPrefix	= ( $Owner ) ? 'own' : '';
		$RowsTPL        = parent::$page->get_template ( 'overview/overview_fleet_event' );
		$MissionType    = $fleet_row['fleet_mission'];
		$FleetContent   = self::fleet_ships_popup ( $fleet_row , parent::$lang['cff_flotte'] , $FleetPrefix . $FleetStyle[$MissionType] , $current_user );
		$FleetCapacity  = self::fleet_resources_popup ( $fleet_row , parent::$lang['type_mission'][$MissionType] , $FleetPrefix . $FleetStyle[$MissionType] );

		$planet_name	= parent::$db->query_fetch ( "SELECT	(SELECT `planet_name`
															FROM " . PLANETS . "
															WHERE `planet_galaxy` = '".intval($fleet_row['fleet_start_galaxy'])."' AND
																	`planet_system` = '".intval($fleet_row['fleet_start_system'])."' AND
																	`planet_planet` = '".intval($fleet_row['fleet_start_planet'])."' AND
																	`planet_type` = '".intval($fleet_row['fleet_start_type'])."') AS start_planet_name,
														(SELECT `planet_name`
															FROM " . PLANETS . "
															WHERE `planet_galaxy` = '".intval($fleet_row['fleet_end_galaxy'])."' AND
																	`planet_system` = '".intval($fleet_row['fleet_end_system'])."' AND
																	`planet_planet` = '".intval($fleet_row['fleet_end_planet'])."' AND
																	`planet_type` = '".intval($fleet_row['fleet_end_type'])."') AS target_planet_name" );

		$StartType      = $fleet_row['fleet_start_type'];
		$TargetType     = $fleet_row['fleet_end_type'];

		if ( $Status != 2 )
		{
			if ( $StartType == 1 )
			{
				$StartID  = parent::$lang['cff_from_the_planet'];
			}
			elseif ($StartType == 3)
			{
				$StartID  = parent::$lang['cff_from_the_moon'];
			}

			$StartID .= $planet_name['start_planet_name'] ." ";
			$StartID .= Fleets_Lib::start_link ( $fleet_row , $FleetPrefix . $FleetStyle[$MissionType] );

			if ( $MissionType != 15 )
			{
				switch ( $TargetType )
				{
					case 1:
						$TargetID  	= parent::$lang['cff_the_planet'];
					break;

					case 2:
						$TargetID  	= parent::$lang['cff_debris_field'];
					break;

					case 3:
						$TargetID  	= parent::$lang['cff_to_the_moon'];
					break;
				}
			}
			else
			{
				$TargetID  			= parent::$lang['cff_the_position'];
			}


			$TargetID .= $planet_name['target_planet_name'] ." ";
			$TargetID .= Fleets_Lib::target_link ( $fleet_row, $FleetPrefix . $FleetStyle[ $MissionType ] );
		}
		else
		{
			if ( $StartType == 1 )
			{
				$StartID  = parent::$lang['cff_to_the_planet'];
			}
			elseif ( $StartType == 3 )
			{
				$StartID  = parent::$lang['cff_the_moon'];
			}

			$StartID .= $planet_name['start_planet_name'] ." ";
			$StartID .= Fleets_Lib::start_link ( $fleet_row, $FleetPrefix . $FleetStyle[ $MissionType ] );

			if ( $MissionType != 15 )
			{
				switch ( $TargetType )
				{
					case 1:
						$TargetID  	= parent::$lang['cff_from_planet'];
					break;

					case 2:
						$TargetID  	= parent::$lang['cff_from_debris_field'];
					break;

					case 3:
						$TargetID  	= parent::$lang['cff_from_the_moon'];
					break;
				}
			}
			else
			{
				$TargetID  			= parent::$lang['cff_from_position'];
			}

			$TargetID .= $planet_name['target_planet_name'] ." ";
			$TargetID .= Fleets_Lib::target_link ( $fleet_row, $FleetPrefix . $FleetStyle[ $MissionType ] );
		}

		if ( $MissionType == 10 )
		{
			$EventString  = parent::$lang['cff_missile_attack'] . " ( " . preg_replace ( "(503,)i" , "" , $fleet_row['fleet_array'] ) . " ) ";
			$Time         = $fleet_row['fleet_start_time'];
			$Rest         = $Time - time();
			$EventString .= $StartID;
			$EventString .= parent::$lang['cff_to'];
			$EventString .= $TargetID;
			$EventString .= ".";
		}
		else
		{
			if ($Owner == TRUE)
			{
				$EventString  = parent::$lang['cff_one_of_your'];
				$EventString .= $FleetContent;
			}
			else
			{
				$EventString  = parent::$lang['cff_a'];
				$EventString .= $FleetContent;
				$EventString .= parent::$lang['cff_of'];
				$EventString .= self::enemy_link ( $fleet_row );
			}

			switch ( $Status )
			{
				case 0:
					$Time         = $fleet_row['fleet_start_time'];
					$Rest         = $Time - time();
					$EventString .= parent::$lang['cff_goes'];
					$EventString .= $StartID;
					$EventString .= parent::$lang['cff_toward'];
					$EventString .= $TargetID;
					$EventString .= parent::$lang['cff_with_the_mission_of'];
				break;

				case 1:
					$Time         = $fleet_row['fleet_end_stay'];
					$Rest         = $Time - time();
					$EventString .= parent::$lang['cff_goes'];
					$EventString .= $StartID;
					$EventString .= parent::$lang['cff_to_explore'];
					$EventString .= $TargetID;
					$EventString .= parent::$lang['cff_with_the_mission_of'];
				break;

				case 2:
					$Time         = $fleet_row['fleet_end_time'];
					$Rest         = $Time - time();
					$EventString .= parent::$lang['cff_comming_back'];
					$EventString .= $TargetID;
					$EventString .= $StartID;
					$EventString .= parent::$lang['cff_with_the_mission_of'];
				break;
			}

			$EventString .= $FleetCapacity;
		}

		$bloc['fleet_status'] = $FleetStatus[$Status];
		$bloc['fleet_prefix'] = $FleetPrefix;
		$bloc['fleet_style']  = $FleetStyle[$MissionType];
		$bloc['fleet_javai']  = Functions_Lib::chrono_applet ( $Label, $Record, $Rest, TRUE );
		$bloc['fleet_order']  = $Label . $Record;
		$bloc['fleet_descr']  = $EventString;
		$bloc['fleet_javas']  = Functions_Lib::chrono_applet ( $Label, $Record, $Rest, FALSE );

		return parent::$page->parse_template ( $RowsTPL , $bloc );
	}

	/**
	 * method is_fleet_returning
	 * param (array) $fleet_row
	 * return (bool) TRUE if the fleet is returning, FALSE if not
	 */
	public static function is_fleet_returning ( $fleet_row )
	{
		return ( $fleet_row['fleet_mess'] == 1 );
	}
}
/* end of Fleets_Lib.php */