<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( 'location:../../' ) );

class Recycle extends Missions
{
	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * method recycle_mission
	 * param $fleet_row
	 * return the recycle result
	*/
	public function recycle_mission ( $fleet_row )
	{
		if ( $fleet_row['fleet_mess'] == '0' )
		{
			if ( $fleet_row['fleet_start_time'] <= time() )
			{
				$recycled_resources	= $this->calculate_capacity ( $fleet_row );

				parent::$db->query ( "UPDATE " . PLANETS . ", " . FLEETS . " SET
										`planet_debris_metal` = `planet_debris_metal` - '" . $recycled_resources['metal'] . "',
										`planet_debris_crystal` = `planet_debris_crystal` - '" . $recycled_resources['crystal'] . "',
										`fleet_resource_metal` = `fleet_resource_metal` + '" . $recycled_resources['metal'] . "',
										`fleet_resource_crystal` = `fleet_resource_crystal` + '" . $recycled_resources['crystal'] . "',
										`fleet_mess` = '1'
										WHERE `planet_galaxy` = '".$fleet_row['fleet_end_galaxy']."' AND
												`planet_system` = '".$fleet_row['fleet_end_system']."' AND
												`planet_planet` = '".$fleet_row['fleet_end_planet']."' AND
												`planet_type` = 1 AND
												`fleet_id` = '".(int)$fleet_row['fleet_id']."'" );

				$message	= sprintf ( $this->_lang['sys_recy_gotten'] , Format_Lib::pretty_number ( $recycled_resources['metal'] ) , $this->_lang['Metal'] , Format_Lib::pretty_number ( $recycled_resources['crystal'] ) , $this->_lang['Crystal'] );
				$this->recycle_message ( $fleet_row['fleet_owner'] , $message , $fleet_row['fleet_start_time'] , $this->_lang['sys_recy_report'] );
			}
		}
		elseif ( $fleet_row['fleet_end_time'] <= time() )
		{
			$message	= sprintf ( $this->_lang['sys_tran_mess_owner'] , $TargetName , Fleets_Lib::target_link ( $fleet_row , '' ) , Format_Lib::pretty_number ( $fleet_row['fleet_resource_metal'] ) , $this->_lang['Metal'] , Format_Lib::pretty_number ( $fleet_row['fleet_resource_crystal'] ) , $this->_lang['Crystal'] , Format_Lib::pretty_number ( $fleet_row['fleet_resource_deuterium'] ) , $this->_lang['Deuterium'] );
			$this->recycle_message ( $fleet_row['fleet_owner'] , $message , $fleet_row['fleet_end_time'] , $this->_lang['sys_mess_fleetback'] );

			parent::restore_fleet ( $fleet_row , TRUE );
			parent::remove_fleet ( $fleet_row['fleet_id'] );
		}
	}

	/**
	 * method calculate_capacity
	 * param $fleet_row
	 * return the amount of resources that can be recycled
	*/
	private function calculate_capacity ( $fleet_row )
	{
		$target_planet     	= parent::$db->query_fetch ( "SELECT `planet_debris_metal`, `planet_debris_crystal`
															FROM " . PLANETS . "
															WHERE `planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
																	`planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
																	`planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
																	`planet_type` = 1
															LIMIT 1;" );
		// SOME REQUIRED VALUES
		$ships				= explode ( ';' , $fleet_row['fleet_array'] );
		$recycle_capacity   = 0;
		$other_capacity		= 0;
		$current_resources	= $fleet_row['fleet_resource_metal'] + $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'];

		// CALCULATE STORAGE FOR EACH KIND OF SHIP
		foreach ( $ships as $item => $group )
		{
			if ( $group != '' )
			{
				$ship        = explode ( "," , $group );

				if ( $ship[0] == 209 )
				{
					$recycle_capacity	+= $this->_pricelist[$ship[0]]['capacity'] * $ship[1];
				}
				else
				{
					$other_capacity		+= $this->_pricelist[$ship[0]]['capacity'] * $ship[1];
				}
			}
		}

		if ( $current_resources > $other_capacity )
		{
			$recycle_capacity -= ( $current_resources - $other_capacity );
		}

		if ( ( $target_planet['planet_debris_metal'] + $target_planet['planet_debris_crystal'] ) <= $recycle_capacity )
		{
			$recycled_resources['metal']	= $target_planet['planet_debris_metal'];
			$recycled_resources['crystal']	= $target_planet['planet_debris_crystal'];
		}
		else
		{
			if ( ( $target_planet['planet_debris_metal'] > $recycle_capacity / 2 ) && ( $target_planet['planet_debris_crystal'] > $recycle_capacity / 2 ) )
			{
				$recycled_resources['metal']	= $recycle_capacity / 2;
				$recycled_resources['crystal']	= $recycle_capacity / 2;
			}
			else
			{
				if ( $target_planet['planet_debris_metal'] > $target_planet['planet_debris_crystal'] )
				{
					$recycled_resources['crystal']			= $target_planet['planet_debris_crystal'];

					if ( $target_planet['planet_debris_metal'] > ( $recycle_capacity - $recycled_resources['crystal'] ) )
					{
						$recycled_resources['metal']		= $recycle_capacity - $recycled_resources['crystal'];
					}
					else
					{
						$recycled_resources['metal']		= $target_planet['planet_debris_metal'];
					}
				}
				else
				{
					$recycled_resources['metal']			= $target_planet['planet_debris_metal'];

					if ( $target_planet['planet_debris_crystal'] > ( $recycle_capacity - $recycled_resources['metal'] ) )
					{
						$recycled_resources['crystal']		= $recycle_capacity - $recycled_resources['metal'];
					}
					else
					{
						$recycled_resources['crystal']		= $target_planet['planet_debris_crystal'];
					}
				}
			}
		}

		return $recycled_resources;
	}

	/**
	 * method recycle_message
	 * param $owner
	 * param $message
	 * param $time
	 * param $status_message
	 * return send a message with the recycle details
	*/
	private function recycle_message ( $owner , $message , $time , $status_message )
	{
		Functions_Lib::send_message ( $owner , '' , $time , 5 , $this->_lang['sys_mess_spy_control'] , $status_message , $message );
	}
}
/* end of recycle.php */