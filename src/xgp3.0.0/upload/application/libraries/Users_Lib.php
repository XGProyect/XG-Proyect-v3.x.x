<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Users_Lib extends XGPCore
{
	private $_user_data;
	private $_planet_data;
	private $_lang;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		$this->_lang	= parent::$lang;

		if ( $this->is_session_set() )
		{
			// Get user data and check it
			$this->set_user_data();

			// Check game close
			Functions_Lib::check_server ( $this->_user_data );

			// Set the changed planet
			$this->set_planet();

			// Get planet data and check it
			$this->set_planet_data();

			// Update resources, ships, defenses & technologies
			UpdateResources_Lib::update_resource ( $this->_user_data , $this->_planet_data , time() );

			// Update buildings queue
			Update_Lib::update_buildings_queue ( $this->_planet_data , $this->_user_data );
		}
	}

	/**
	 * method user_login
	 * param $user_id
	 * param $user_name
	 * param $password
	 * return (bool) true on success, false if not
	 */
	public function user_login ( $user_id = 0 , $user_name = '' , $password = '' )
	{
		if ( $user_id != 0 && ! empty ( $user_name ) && ! empty ( $password ) )
		{
			$_SESSION['user_id']		= $user_id;
			$_SESSION['user_name']		= $user_name;
			$_SESSION['user_password']	= sha1 ( $password . '-' . SECRETWORD );

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * method get_user_data
	 * param
	 * return (array) all the user data
	 */
	public function get_user_data()
	{
		return $this->_user_data;
	}

	/**
	 * method get_planet_data
	 * param
	 * return (array) all the planet data
	 */
	public function get_planet_data()
	{
		return $this->_planet_data;
	}

	/**
	 * method check_session
	 * param
	 * return (void)
	 */
	public function check_session()
	{
		if ( ! $this->is_session_set() )
		{
			Functions_Lib::redirect ( XGP_ROOT );
		}
	}

	/**
	 * method delete_user
	 * param $user_id
	 * return delete all the selected user data
	 */
	public function delete_user ( $user_id )
	{
		$user_data = parent::$db->query_fetch ( "SELECT `user_ally_id` FROM " . USERS . " WHERE `user_id` = '" . $user_id . "';" );

		if ( $user_data['user_ally_id'] != 0 )
		{
			$alliance	= parent::$db->query_fetch ( "SELECT a.`alliance_id`, (SELECT COUNT(user_id) AS `ally_members` FROM `" . USERS . "` WHERE `user_ally_id` = '" . $user_data['user_ally_id'] . "') AS `ally_members`
														FROM " . ALLIANCE . " AS a
														WHERE a.`alliance_id` = '" . $user_data['user_ally_id'] . "';" );

			if ( $alliance['ally_members'] <= 0 )
			{
				parent::$db->query ( "DELETE ass,a FROM " . ALLIANCE . " AS a
										INNER JOIN " . ALLIANCE_STATISTICS . " AS ass ON ass.alliance_statistic_alliance_id = a.alliance_id
										WHERE a.`alliance_id` = '" . $alliance['alliance_id'] . "';" );
			}
		}

		parent::$db->query ( "DELETE p,b,d,s FROM " . PLANETS . " AS p
								INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
								INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
								INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
								WHERE `planet_user_id` = '" . $user_id . "';" );

		parent::$db->query ( "DELETE FROM " . MESSAGES . " WHERE `message_sender` = '" . $user_id . "' OR `message_receiver` = '" . $user_id . "';" );
		parent::$db->query ( "DELETE FROM " . BUDDY . " WHERE `buddy_sender` = '" . $user_id . "' OR `buddy_receiver` = '" . $user_id . "';" );

		parent::$db->query ( "DELETE r,f,n,p,se,s,u FROM " . USERS . " AS u
								INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
								LEFT JOIN " . FLEETS . " AS f ON f.fleet_owner = u.user_id
								LEFT JOIN " . NOTES . " AS n ON n.note_owner = u.user_id
								INNER JOIN " . PREMIUM . " AS p ON p.premium_user_id = u.user_id
								INNER JOIN " . SETTINGS . " AS se ON se.setting_user_id = u.user_id
								INNER JOIN " . USERS_STATISTICS . " AS s ON s.user_statistic_user_id = u.user_id
								WHERE u.`user_id` = '" . $user_id . "';" );
	}

	/**
	 * method is_on_vacations
	 * param $current_user
	 * return return TRUE if the user is on vacation mode, FALSE if not.
	 */
	public function is_on_vacations ( $user )
	{
		if ( $user['setting_vacations_status'] == 1 )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	###########################################################################
	#
	# Private Methods
	#
	###########################################################################

	/**
	 * method is_session_set
	 * param
	 * return (bool)
	 */
	private function is_session_set()
	{
		if ( ! isset ( $_SESSION['user_id'] ) or ! isset ( $_SESSION['user_name'] ) or ! isset ( $_SESSION['user_password'] ) )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * method set_user_data
	 * param
	 * return (void)
	 */
	private function set_user_data()
	{
		$user_row	= array();

		$this->_user_data = parent::$db->query ( "SELECT u.*,
	            											pre.*,
	            											se.*,
	            											usul.user_statistic_total_rank,
	            											usul.user_statistic_total_points,
	            											r.*,
	            											a.alliance_name,
	            											(SELECT COUNT(`message_id`) AS `new_message` FROM `" . MESSAGES . "` WHERE `message_receiver` = u.`user_id` AND `message_read` = 0) AS `new_message`
	            									FROM " . USERS . " AS u
	            									INNER JOIN " . SETTINGS . " AS se ON se.setting_user_id = u.user_id
	                                    			INNER JOIN " . USERS_STATISTICS . " AS usul ON usul.user_statistic_user_id = u.user_id
	                                    			INNER JOIN " . PREMIUM . " AS pre ON pre.premium_user_id = u.user_id
	                                    			INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
	                                    			LEFT JOIN " . ALLIANCE . " AS a ON a.alliance_id = u.user_ally_id
	            									WHERE (u.user_name = '" . parent::$db->escape_value ( $_SESSION['user_name'] ) . "')
	            									LIMIT 1;");

		if ( parent::$db->num_rows ( $this->_user_data ) != 1 )
		{
			Functions_Lib::message ( $this->_lang['ccs_multiple_users'] , XGP_ROOT , 3 , FALSE , FALSE );
		}

		$user_row    = parent::$db->fetch_array ( $this->_user_data );

		if ( $user_row['user_id'] != $_SESSION['user_id'] )
		{
			Functions_Lib::message ( $this->_lang['ccs_other_user'] , XGP_ROOT , 3 ,  FALSE , FALSE );
		}

		if ( sha1 ( $user_row['user_password'] . "-" . SECRETWORD ) != $_SESSION['user_password'] )
		{
			Functions_Lib::message ( $this->_lang['css_different_password'] , XGP_ROOT , 5 ,  FALSE , FALSE );
		}

		if ( $user_row['user_banned'] > 0 )
		{
			$parse					= $this->_lang;
			$parse['banned_until']	= date ( Functions_Lib::read_config ( 'date_format_extended' ) , $user_row['user_banned'] );

			die ( parent::$page->parse_template ( parent::$page->get_template ( 'home/banned_message' ) , $parse ) );
		}

		parent::$db->query ( "UPDATE " . USERS . " SET
            					`user_onlinetime` = '" . time() ."',
            					`user_current_page` = '". parent::$db->escape_value ( $_SERVER['REQUEST_URI'] ) ."',
            					`user_lastip` = '". parent::$db->escape_value ( $_SERVER['REMOTE_ADDR'] ) ."',
            					`user_agent` = '". parent::$db->escape_value ( $_SERVER['HTTP_USER_AGENT'] ) ."'
            					WHERE `user_id` = '". parent::$db->escape_value ( $_SESSION['user_id'] ) ."'
            					LIMIT 1;" );

		// pass the data
		$this->_user_data	= $user_row;

		// unset the old data
		unset ( $user_row );
	}

	/**
	 * method set_planet_data
	 * param
	 * return (void)
	 */
	private function set_planet_data()
	{
		$this->_planet_data	= parent::$db->query_fetch ( "SELECT p.*, b.*, d.*, s.*,
																m.planet_id AS moon_id,
																m.planet_name AS moon_name,
																m.planet_image AS moon_image,
																m.planet_destroyed AS moon_destruyed,
																m.planet_image AS moon_image,
																(SELECT COUNT(user_statistic_user_id) AS stats_users FROM `" . USERS_STATISTICS . "`) AS stats_users
															FROM " . PLANETS . " AS p
															INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
															INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
															INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
															LEFT JOIN " . PLANETS . " AS m ON m.planet_id = (SELECT mp.`planet_id`
																												FROM " . PLANETS . " AS mp
																												WHERE (mp.planet_galaxy=p.planet_galaxy AND
																														mp.planet_system=p.planet_system AND
																														mp.planet_planet=p.planet_planet AND
																														mp.planet_type=3))
															WHERE p.`planet_id` = '" . $this->_user_data['user_current_planet'] . "';" );
	}

	/**
	 * method set_planet
	 * param
	 * return (void)
	 */
	private function set_planet()
	{
		$select		= isset ( $_GET['cp'] ) ? (int)$_GET['cp'] : '';
		$restore 	= isset ( $_GET['re'] ) ? (int)$_GET['re'] : '';

		if ( isset ( $select ) && is_numeric ( $select ) && isset ( $restore ) && $restore == 0 && $select != 0 )
		{
			$owned   = parent::$db->query_fetch ( "SELECT `planet_id`
													FROM " . PLANETS . "
													WHERE `planet_id` = '". $select ."'
														AND `planet_user_id` = '" . $this->_user_data['user_id'] . "';");

			if ( $owned )
			{
				$this->_user_data['current_planet']	= $select;

				parent::$db->query ( "UPDATE " . USERS . " SET
										`user_current_planet` = '" . $select . "'
										WHERE `user_id` = '" . $this->_user_data['user_id'] . "';");
			}
		}
	}
}
/* end of Users_Lib.php */