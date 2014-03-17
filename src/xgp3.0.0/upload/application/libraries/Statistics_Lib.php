<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Statistics_Lib extends XGPCore
{
	private static $time;

	/**
	 * method calculate_points
	 * param $element
	 * param $level
	 * return the points for the current element and level
	 */
	public static function calculate_points ( $element , $level , $type = '' )
	{
		switch ( $type )
		{
			case 'tech':

				$current_level	= $level;

			break;

			case '':
			default:

				$current_level	= ( $level - 1 < 0 ) ? 0 : $level - 1;

			break;
		}

		$element			= parent::$objects->get_price ( $element );
		$resources_total	= $element['metal'] + $element['crystal'] + $element['deuterium'];
		$level_mult     	= pow ( $element['factor'] , $current_level );
		$points 			= ( $resources_total * $level_mult ) / Functions_Lib::read_config ( 'stat_settings' );

		return $points;
	}

	/**
	 * method make_stats
	 * param
	 * return builds the statistics
	 */
	public static function make_stats ()
	{
		// INITIAL TIME
		$mtime        				= microtime();
		$mtime        				= explode ( " " , $mtime );
		$mtime        				= $mtime[1] + $mtime[0];
		$starttime    				= $mtime;
		self::$time					= time();

		// INITIAL MEMORY
		$result['initial_memory']	= array ( round ( memory_get_usage() / 1024 , 1 ) ,round ( memory_get_usage ( 1 ) / 1024 , 1 ) );

		// MAKE STATISTICS FOR USERS
		self::make_user_rank();

		// MAKE STATISTICS FOR ALLIANCE
		self::make_ally_rank();

		// END STATISTICS BUILD
		$mtime						= microtime();
		$mtime       		 		= explode ( " " , $mtime );
		$mtime        				= $mtime[1] + $mtime[0];
		$endtime      				= $mtime;
		$result['stats_time']		= self::$time;
		$result['totaltime']    	= ( $endtime - $starttime );
		$result['memory_peak']		= array ( round ( memory_get_peak_usage() / 1024 , 1 ) , round ( memory_get_peak_usage ( 1 ) / 1024 , 1 ) );
		$result['end_memory']		= array ( round ( memory_get_usage() / 1024 , 1 ) , round ( memory_get_usage ( 1 ) / 1024 , 1 ) );

		return $result;
	}

	/**
	 * method make_user_rank
	 * param
	 * return build the user statistics
	 */
	private static function make_user_rank ()
	{
		// GET ALL DATA FROM THE USERS TO UPDATE
		$all_stats_data		= parent::$db->query ( "SELECT `user_statistic_user_id`,
															`user_statistic_technology_rank`,
															`user_statistic_technology_points`,
															`user_statistic_buildings_rank`,
															`user_statistic_buildings_points`,
															`user_statistic_defenses_rank`,
															`user_statistic_defenses_points`,
															`user_statistic_ships_rank`,
															`user_statistic_ships_points`,
															`user_statistic_total_rank`,
															(user_statistic_buildings_points + user_statistic_defenses_points + user_statistic_ships_points + user_statistic_technology_points) AS total_points
														FROM " . USERS_STATISTICS . "
														ORDER BY `user_statistic_user_id` ASC;" );

		// BUILD ALL THE ARRAYS
		while ( $CurUser = parent::$db->fetch_assoc ( $all_stats_data ) )
		{
			$tech['old_rank'][$CurUser['user_statistic_user_id']]	= $CurUser['user_statistic_technology_rank'];
			$tech['points'][$CurUser['user_statistic_user_id']]		= $CurUser['user_statistic_technology_points'];

			$build['old_rank'][$CurUser['user_statistic_user_id']]	= $CurUser['user_statistic_buildings_rank'];
			$build['points'][$CurUser['user_statistic_user_id']]	= $CurUser['user_statistic_buildings_points'];

			$defs['old_rank'][$CurUser['user_statistic_user_id']]	= $CurUser['user_statistic_defenses_rank'];
			$defs['points'][$CurUser['user_statistic_user_id']]		= $CurUser['user_statistic_defenses_points'];

			$ships['old_rank'][$CurUser['user_statistic_user_id']]	= $CurUser['user_statistic_ships_rank'];
			$ships['points'][$CurUser['user_statistic_user_id']]	= $CurUser['user_statistic_ships_points'];

			$total['old_rank'][$CurUser['user_statistic_user_id']]	= $CurUser['user_statistic_total_rank'];
			$total['points'][$CurUser['user_statistic_user_id']]	= $CurUser['total_points'];
		}

		// ORDER THEM FROM HIGHEST TO LOWEST
		arsort ( $tech['points'] );
		arsort ( $build['points'] );
		arsort ( $defs['points'] );
		arsort ( $ships['points'] );
		arsort ( $total['points'] );

		// ALL RANKS SHOULD START ON 1
		$rank['tech']		= 1;
		$rank['buil']		= 1;
		$rank['defe']		= 1;
		$rank['ship']		= 1;
		$rank['tota']		= 1;

		// TECH
		foreach ( $tech as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $user_id => $data )
				{
					$tech['rank'][$user_id] = $rank['tech']++;
				}
			}
		}

		// BUILDINGS
		foreach ( $build as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $user_id => $data )
				{
					$build['rank'][$user_id] = $rank['buil']++;
				}
			}
		}

		// DEFENSES
		foreach ( $defs as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $user_id => $data )
				{
					$defs['rank'][$user_id] = $rank['defe']++;
				}
			}
		}

		// SHIPS
		foreach ( $ships as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $user_id => $data )
				{
					$ships['rank'][$user_id] = $rank['ship']++;
				}
			}
		}

		// UPDATE QUERY
		$update_query	= "INSERT INTO " . USERS_STATISTICS . "
							(user_statistic_user_id,
								user_statistic_buildings_old_rank,
								user_statistic_buildings_rank,
								user_statistic_defenses_old_rank,
								user_statistic_defenses_rank,
								user_statistic_ships_old_rank,
								user_statistic_ships_rank,
								user_statistic_technology_old_rank,
								user_statistic_technology_rank,
								user_statistic_total_points,
								user_statistic_total_old_rank,
								user_statistic_total_rank,
								user_statistic_update_time) VALUES ";

		// SET VARIABLES
		$values				= '';
		$update				= '';

		// TOTAL POINTS
		// UPDATE QUERY DYNAMIC BLOCK
		foreach ( $total as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $user_id => $data )
				{
					$values			.= '(' . $user_id . ',
										' . $build['old_rank'][$user_id] . ',
										' . $build['rank'][$user_id] . ',
										' . $defs['old_rank'][$user_id] . ',
										' . $defs['rank'][$user_id] . ',
										' . $ships['old_rank'][$user_id] . ',
										' . $ships['rank'][$user_id] . ',
										' . $tech['old_rank'][$user_id] . ',
										' . $tech['rank'][$user_id] . ',
										' . $total['points'][$user_id] . ',
										' . $total['old_rank'][$user_id] . ',
										' . $rank['tota']++ . ',
										' . self::$time . '),';
				}
			}
		}

		// REMOVE LAST COMMA
		$values	= substr_replace ( $values , '' , -1 );

		// FINISH UPDATE QUERY
		$update_query	.= $values;
		$update_query 	.= ' ON DUPLICATE KEY UPDATE
								user_statistic_buildings_old_rank = VALUES(user_statistic_buildings_old_rank),
								user_statistic_buildings_rank = VALUES(user_statistic_buildings_rank),
								user_statistic_defenses_old_rank = VALUES(user_statistic_defenses_old_rank),
								user_statistic_defenses_rank = VALUES(user_statistic_defenses_rank),
								user_statistic_ships_old_rank = VALUES(user_statistic_ships_old_rank),
								user_statistic_ships_rank = VALUES(user_statistic_ships_rank),
								user_statistic_technology_old_rank = VALUES(user_statistic_technology_old_rank),
								user_statistic_technology_rank = VALUES(user_statistic_technology_rank),
								user_statistic_total_points = VALUES(user_statistic_total_points),
								user_statistic_total_old_rank = VALUES(user_statistic_total_old_rank),
								user_statistic_total_rank = VALUES(user_statistic_total_rank),
								user_statistic_update_time = VALUES(user_statistic_update_time);';

		// RUN QUERY
		parent::$db->query ( $update_query );

		// MEMORY CLEAN UP
		unset ( $all_stats_data , $build , $defs , $ships , $tech , $rank , $update_query , $values );
	}

	/**
	 * method make_ally_rank
	 * param
	 * return build the alliance statistics
	 */
	private static function make_ally_rank ()
	{
		// GET ALL DATA FROM THE USERS TO UPDATE
		$all_stats_data = parent::$db->query ( "SELECT a.`alliance_id`,
													ass.alliance_statistic_technology_rank,
													ass.alliance_statistic_buildings_rank,
													ass.alliance_statistic_defenses_rank,
													ass.alliance_statistic_ships_rank,
													ass.alliance_statistic_total_rank,
													SUM(us.user_statistic_buildings_points) AS buildings_points,
													SUM(us.user_statistic_defenses_points) AS defenses_points,
													SUM(us.user_statistic_ships_points) AS ships_points,
													SUM(us.user_statistic_technology_points) AS technology_points,
													SUM(us.user_statistic_total_points) AS total_points
													FROM " . ALLIANCE . " AS a
													LEFT JOIN " . USERS . " AS u ON a.`alliance_id` = u.`user_ally_id`
													LEFT JOIN " . USERS_STATISTICS . " AS us ON us.`user_statistic_user_id` = u.`user_id`
													LEFT JOIN " . ALLIANCE_STATISTICS . " AS ass ON ass.`alliance_statistic_alliance_id` = a.`alliance_id`
													GROUP BY alliance_id" );

		// ANY ALLIANCE ?
		if ( empty ( $all_stats_data ) or parent::$db->num_rows ( $all_stats_data ) == 0 )
		{
			return;
		}

		// BUILD ALL THE ARRAYS
		while ( $CurAlliance = parent::$db->fetch_assoc ( $all_stats_data ) )
		{
			$tech['old_rank'][$CurAlliance['alliance_id']]	= $CurAlliance['alliance_statistic_technology_rank'];
			$tech['points'][$CurAlliance['alliance_id']]	= $CurAlliance['technology_points'];

			$build['old_rank'][$CurAlliance['alliance_id']]	= $CurAlliance['alliance_statistic_buildings_rank'];
			$build['points'][$CurAlliance['alliance_id']]	= $CurAlliance['buildings_points'];

			$defs['old_rank'][$CurAlliance['alliance_id']]	= $CurAlliance['alliance_statistic_defenses_rank'];
			$defs['points'][$CurAlliance['alliance_id']]	= $CurAlliance['defenses_points'];

			$ships['old_rank'][$CurAlliance['alliance_id']]	= $CurAlliance['alliance_statistic_ships_rank'];
			$ships['points'][$CurAlliance['alliance_id']]	= $CurAlliance['ships_points'];

			$total['old_rank'][$CurAlliance['alliance_id']]	= $CurAlliance['alliance_statistic_total_rank'];
			$total['points'][$CurAlliance['alliance_id']]	= $CurAlliance['total_points'];
		}

		// ORDER THEM FROM HIGHEST TO LOWEST
		arsort ( $tech['points'] );
		arsort ( $build['points'] );
		arsort ( $defs['points'] );
		arsort ( $ships['points'] );
		arsort ( $total['points'] );

		// ALL RANKS SHOULD START ON 1
		$rank['tech']		= 1;
		$rank['buil']		= 1;
		$rank['defe']		= 1;
		$rank['ship']		= 1;
		$rank['tota']		= 1;

		// TECH
		foreach ( $tech as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $alliance_id => $data )
				{
					$tech['rank'][$alliance_id] = $rank['tech']++;
				}
			}
		}

		// BUILDINGS
		foreach ( $build as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $alliance_id => $data )
				{
					$build['rank'][$alliance_id] = $rank['buil']++;
				}
			}
		}

		// DEFENSES
		foreach ( $defs as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $alliance_id => $data )
				{
					$defs['rank'][$alliance_id] = $rank['defe']++;
				}
			}
		}

		// SHIPS
		foreach ( $ships as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $alliance_id => $data )
				{
					$ships['rank'][$alliance_id] = $rank['ship']++;
				}
			}
		}

		// UPDATE QUERY
		$update_query	= "INSERT INTO " . ALLIANCE_STATISTICS . "
							(alliance_statistic_alliance_id,
								alliance_statistic_buildings_points,
								alliance_statistic_buildings_old_rank,
								alliance_statistic_buildings_rank,
								alliance_statistic_defenses_points,
								alliance_statistic_defenses_old_rank,
								alliance_statistic_defenses_rank,
								alliance_statistic_ships_points,
								alliance_statistic_ships_old_rank,
								alliance_statistic_ships_rank,
								alliance_statistic_technology_points,
								alliance_statistic_technology_old_rank,
								alliance_statistic_technology_rank,
								alliance_statistic_total_points,
								alliance_statistic_total_old_rank,
								alliance_statistic_total_rank,
								alliance_statistic_update_time) VALUES ";

		// SET VARIABLES
		$values				= '';
		$update				= '';

		// TOTAL POINTS
		// UPDATE QUERY DYNAMIC BLOCK
		foreach ( $total as $key => $value )
		{
			if ( $key == 'points' )
			{
				foreach ( $value as $alliance_id => $data )
				{
					$values			.= '(' . $alliance_id . ',
										' . $build['points'][$alliance_id] . ',
										' . $build['old_rank'][$alliance_id] . ',
										' . $build['rank'][$alliance_id] . ',
										' . $defs['points'][$alliance_id] . ',
										' . $defs['old_rank'][$alliance_id] . ',
										' . $defs['rank'][$alliance_id] . ',
										' . $ships['points'][$alliance_id] . ',
										' . $ships['old_rank'][$alliance_id] . ',
										' . $ships['rank'][$alliance_id] . ',
										' . $tech['points'][$alliance_id] . ',
										' . $tech['old_rank'][$alliance_id] . ',
										' . $tech['rank'][$alliance_id] . ',
										' . $total['points'][$alliance_id] . ',
										' . $total['old_rank'][$alliance_id] . ',
										' . $rank['tota']++ . ',
										' . self::$time . '),';
				}
			}
		}

		// REMOVE LAST COMMA
		$values	= substr_replace ( $values , '' , -1 );

		// FINISH UPDATE QUERY
		$update_query	.= $values;
		$update_query 	.= ' ON DUPLICATE KEY UPDATE
								alliance_statistic_buildings_points = VALUES(alliance_statistic_buildings_points),
								alliance_statistic_buildings_old_rank = VALUES(alliance_statistic_buildings_old_rank),
								alliance_statistic_buildings_rank = VALUES(alliance_statistic_buildings_rank),
								alliance_statistic_defenses_points = VALUES(alliance_statistic_defenses_points),
								alliance_statistic_defenses_old_rank = VALUES(alliance_statistic_defenses_old_rank),
								alliance_statistic_defenses_rank = VALUES(alliance_statistic_defenses_rank),
								alliance_statistic_ships_points = VALUES(alliance_statistic_ships_points),
								alliance_statistic_ships_old_rank = VALUES(alliance_statistic_ships_old_rank),
								alliance_statistic_ships_rank = VALUES(alliance_statistic_ships_rank),
								alliance_statistic_technology_points = VALUES(alliance_statistic_technology_points),
								alliance_statistic_technology_old_rank = VALUES(alliance_statistic_technology_old_rank),
								alliance_statistic_technology_rank = VALUES(alliance_statistic_technology_rank),
								alliance_statistic_total_points = VALUES(alliance_statistic_total_points),
								alliance_statistic_total_old_rank = VALUES(alliance_statistic_total_old_rank),
								alliance_statistic_total_rank = VALUES(alliance_statistic_total_rank),
								alliance_statistic_update_time = VALUES(alliance_statistic_update_time);';

		// RUN QUERY
		parent::$db->query ( $update_query );

		// MEMORY CLEAN UP
		unset ( $all_stats_data , $build , $defs , $ships , $tech , $rank , $update_query , $values );
	}
}
/* end of Statistics_Lib.php */