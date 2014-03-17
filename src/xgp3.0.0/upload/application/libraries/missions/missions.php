<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( 'location:../../' ) );

class Missions extends XGPCore
{
	protected $_lang;
	protected $_resource;
	protected $_pricelist;
	protected $_combat_caps;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_lang		= parent::$lang;
		$this->_resource	= parent::$objects->get_objects();
		$this->_pricelist	= parent::$objects->get_price();
		$this->_combat_caps	= parent::$objects->get_combat_specs();
	}

	/**
	 * method remove_fleet
	 * param $fleet_id
	 * return removes the fleet from the fleets table
	*/
	protected function remove_fleet ( $fleet_id )
	{
		parent::$db->query ( "DELETE FROM " . FLEETS . "
								WHERE `fleet_id` = " . (int)$fleet_id );
	}

	/**
	 * method return_fleet
	 * param $fleet_id
	 * return set the fleet in return mode, mission completed
	*/
	protected function return_fleet ( $fleet_id )
	{
		parent::$db->query ( "UPDATE " . FLEETS . " SET
								`fleet_mess` = '1'
								WHERE `fleet_id` = " . (int)$fleet_id );
	}

	/**
	 * method restore_fleet
	 * param $fleet_row
	 * param $start
	 * return establish the fleet to its planet
	*/
	protected function restore_fleet ( $fleet_row , $start = TRUE )
	{
		if ( $start )
		{
			$galaxy	= $fleet_row['fleet_start_galaxy'];
			$system	= $fleet_row['fleet_start_system'];
			$planet	= $fleet_row['fleet_start_planet'];
			$type	= $fleet_row['fleet_start_type'];
		}
		else
		{
			$galaxy	= $fleet_row['fleet_end_galaxy'];
			$system	= $fleet_row['fleet_end_system'];
			$planet	= $fleet_row['fleet_end_planet'];
			$type	= $fleet_row['fleet_end_type'];
		}

		self::make_update ( $fleet_row , $galaxy , $system , $planet , $type );

		$ships         	= explode ( ";" , $fleet_row['fleet_array'] );
		$ships_fields	= "";

		foreach ( $ships as $item => $group )
		{
			if ( $group != '' )
			{
				$ship			= explode (",", $group);
				$ships_fields  .= "`" . $this->_resource[$ship[0]] . "` = `" . $this->_resource[$ship[0]] . "` + '" . $ship[1] . "', ";
			}
		}

		parent::$db->query ( "UPDATE " . PLANETS . " AS p
								INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id` SET
								{$ships_fields}
								`planet_metal` = `planet_metal` + '" . $fleet_row['fleet_resource_metal'] . "',
								`planet_crystal` = `planet_crystal` + '" . $fleet_row['fleet_resource_crystal'] . "',
								`planet_deuterium` = `planet_deuterium` + '" . $fleet_row['fleet_resource_deuterium'] . "'
								WHERE `planet_galaxy` = '" . $galaxy . "' AND
										`planet_system` = '" . $system . "' AND
										`planet_planet` = '" . $planet . "' AND
										`planet_type` = '" . $type . "'" );
	}

	/**
	 * method store_resources
	 * param $fleet_row
	 * param $start
	 * return store the resources in the destination planet
	*/
	protected function store_resources ( $fleet_row , $start = FALSE )
	{
		if ( $start )
		{
			$galaxy	= $fleet_row['fleet_start_galaxy'];
			$system	= $fleet_row['fleet_start_system'];
			$planet	= $fleet_row['fleet_start_planet'];
			$type	= $fleet_row['fleet_start_type'];
		}
		else
		{
			$galaxy	= $fleet_row['fleet_end_galaxy'];
			$system	= $fleet_row['fleet_end_system'];
			$planet	= $fleet_row['fleet_end_planet'];
			$type	= $fleet_row['fleet_end_type'];
		}

		self::make_update ( $fleet_row , $galaxy , $system , $planet , $type );

		parent::$db->query ( "UPDATE " . PLANETS . " SET
								`planet_metal` = `planet_metal` + '" . $fleet_row['fleet_resource_metal'] . "',
								`planet_crystal` = `planet_crystal` + '" . $fleet_row['fleet_resource_crystal'] . "',
								`planet_deuterium` = `planet_deuterium` + '" . $fleet_row['fleet_resource_deuterium'] . "'
								WHERE `planet_galaxy` = '" . $galaxy . "' AND
										`planet_system` = '" . $system . "' AND
										`planet_planet` = '" . $planet . "' AND
										`planet_type` = '" . $type . "'
										LIMIT 1;" );
	}

	/**
	 * method make_update
	 * param $fleet_row
	 * param $galaxy
	 * param $system
	 * param $planet
	 * param $type
	 * return make a planet update, required before do other stuff
	*/
	protected function make_update ( $fleet_row , $galaxy , $system , $planet , $type )
	{
		$target_planet	= parent::$db->query_fetch ( "SELECT *
														FROM `" . PLANETS . "` AS p
														LEFT JOIN `" . BUILDINGS . "` AS b ON b.building_planet_id = p.`planet_id`
														LEFT JOIN `" . DEFENSES . "` AS d ON d.defense_planet_id = p.`planet_id`
														LEFT JOIN `" . SHIPS . "` AS s ON s.ship_planet_id = p.`planet_id`
														WHERE `planet_galaxy` = " . $galaxy . " AND
																`planet_system` = " . $system . " AND
																`planet_planet` = " . $planet . " AND
																`planet_type` = " . $type . ";" );

		$target_user	= parent::$db->query_fetch ( "SELECT *
														FROM `" . USERS . "` AS u
														INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = u.user_id
														INNER JOIN " . PREMIUM . " AS pr ON pr.premium_user_id = u.user_id
														WHERE u.`user_id` = " . $target_planet['planet_user_id'] );

		UpdateResources_Lib::update_resource ( $target_user , $target_planet , time() );
	}
}
/* end of missions.php */