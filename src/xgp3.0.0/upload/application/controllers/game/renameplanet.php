<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if(!defined('INSIDE')) {die(header('location:../../'));}

class RenamePlanet extends XGPCore
{
	const MODULE_ID	= 1;

	private $_lang;
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
		$parse 						= $this->_lang;
		$parse['planet_name'] 		= $this->_current_planet['planet_name'];
		$parse['planet_id'] 		= $this->_current_planet['planet_id'];
		$parse['galaxy_galaxy'] 	= $this->_current_planet['planet_galaxy'];
		$parse['galaxy_system'] 	= $this->_current_planet['planet_system'];
		$parse['galaxy_planet'] 	= $this->_current_planet['planet_planet'];

		// DEFAULT VIEW
		$current_view 			= parent::$page->get_template ( 'renameplanet/renameplanet_view' );

		// CHANGE THE ACTION
		switch ( ( isset ( $_POST['action'] ) ? $_POST['action'] : NULL ) )
		{
			case $this->_lang['ov_planet_rename_action']:

				$this->rename_planet ( $_POST['newname'] );

			break;

			case $this->_lang['ov_abandon_planet']:

				// DELETE VIEW
				$current_view	= parent::$page->get_template ( 'renameplanet/renameplanet_delete_view' );

			break;
		} // switch

		if ( isset ( $_POST['kolonieloeschen'] ) && (int)$_POST['kolonieloeschen'] == 1 && (int)$_POST['deleteid'] == $this->_current_user['user_current_planet'] )
		{
			$this->delete_planet();
		}

		// SET THE VIEW
		parent::$page->display ( parent::$page->parse_template ( $current_view , $parse ) );
	}

	/**
	 * method rename_planet
	 * param $new_name
	 * return main method, loads everything
	 */
	private function rename_planet ( $new_name )
	{
		$new_name = parent::$db->escape_value ( strip_tags ( trim ( $new_name ) ) );

		if ( preg_match ( "/[^A-z0-9_\- ]/" , $new_name ) == 1 )
		{
			Functions_Lib::message ( $this->_lang['ov_newname_error'] , "game.php?page=renameplanet" , 2 );
		}

		if ( $new_name != "" )
		{
			parent::$db->query ( "UPDATE " . PLANETS . "
									SET `planet_name` = '" . $new_name . "'
									WHERE `planet_id` = '" . intval ( $this->_current_user['user_current_planet'] ) . "'
									LIMIT 1;");
		}
	}

	/**
	 * method delete_planet
	 * param
	 * return deletes the planet
	 */
	private function delete_planet()
	{
		$own_fleet			= 0;
		$enemy_fleet		= 0;
		$fleets_incoming	= parent::$db->query ( "SELECT *
														FROM " . FLEETS . "
														WHERE ( fleet_owner = '" . intval ( $this->_current_user['user_id'] ) . "' AND
																fleet_start_galaxy='" . intval ( $this->_current_planet['planet_galaxy'] ) . "' AND
																fleet_start_system='" . intval ( $this->_current_planet['planet_system'] ) . "' AND
																fleet_start_planet='" . intval ( $this->_current_planet['planet_planet'] ) . "') OR
															  ( fleet_target_owner = '" . intval ( $this->_current_user['user_id'] ) . "' AND
																fleet_end_galaxy='" . intval ( $this->_current_planet['planet_galaxy'] ) . "' AND
																fleet_end_system='" . intval ( $this->_current_planet['planet_system'] ) . "' AND
																fleet_end_planet='" . intval ( $this->_current_planet['planet_planet'] ) . "')");

		while ( $fleet = parent::$db->fetch_array ( $fleets_incoming ) )
		{
			$own_fleet 		= $fleet['fleet_owner'];
			$enemy_fleet 	= $fleet['fleet_target_owner'];

			if ( $fleet['fleet_target_owner'] == $this->_current_user['user_id'] )
			{
				$end_type	= $fleet['fleet_end_type'];
			}

			$mess 			= $fleet['fleet_mess'];
		}

		if ( $own_fleet > 0 )
		{
			Functions_Lib::message ( $this->_lang['ov_abandon_planet_not_possible'] , 'game.php?page=renameplanet' );
		}
		elseif ( ( ( $enemy_fleet > 0 ) && ( $mess < 1 ) ) && $end_type != 2 )
		{
			Functions_Lib::message ( $this->_lang['ov_abandon_planet_not_possible'] , 'game.php?page=renameplanet' );
		}
		else
		{
			if ( sha1 ( $_POST['pw'] ) == $this->_current_user['password'] && $this->_current_user['user_home_planet_id'] != $this->_current_user['user_current_planet'])
			{
				if ( $this->_current_planet['moon_id'] != 0 )
				{
					parent::$db->query ( "UPDATE " . PLANETS . " AS p, " . PLANETS . " AS m, " . USERS . " AS u SET
													p.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
													m.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
													u.`user_current_planet` = u.`user_home_planet_id`
													WHERE p.`planet_id` = '" . $this->_current_user['user_current_planet'] . "' AND
															m.`planet_galaxy` = '" . $this->_current_planet['planet_galaxy'] . "' AND
															m.`planet_system` = '" . $this->_current_planet['planet_system'] . "' AND
															m.`planet_planet` = '" . $this->_current_planet['planet_planet'] . "' AND
															m.`planet_type` = '3' AND
															u.`user_id` = '" . $this->_current_user['user_id'] . "';");
				}
				else
				{
					parent::$db->query ( "UPDATE " . PLANETS . " AS p, " . USERS . " AS u SET
													p.`planet_destroyed` = '" . (time() + (PLANETS_LIFE_TIME * 3600)) . "',
													u.`user_current_planet` = u.`user_home_planet_id`
													WHERE p.`planet_id` = '" . $this->_current_user['user_current_planet'] . "' AND
															u.`user_id` = '" . $this->_current_user['user_id'] . "';");
				}


				Functions_Lib::message ( $this->_lang['ov_planet_abandoned']  , 'game.php?page=overview' );
			}
			elseif ( $this->_current_user['user_home_planet_id'] == $this->_current_user['user_current_planet'] )
			{
				Functions_Lib::message ( $this->_lang['ov_principal_planet_cant_abanone'] , 'game.php?page=renameplanet' );
			}
			else
			{
				Functions_Lib::message ( $this->_lang['ov_wrong_pass']  , 'game.php?page=renameplanet' );
			}
		}
	}
}
/* end of renameplanet.php */