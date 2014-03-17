<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( 'location:../../' ) );

class Stay extends Missions
{
	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * method stay_mission
	 * param $fleet_row
	 * return the stay result
	*/
	public function stay_mission ( $fleet_row )
	{
		if ( $fleet_row['fleet_mess'] == 0 )
		{
			if ( $fleet_row['fleet_start_time'] <= time() )
			{
				$transport_check	= parent::$db->query_fetch ( "SELECT pc1.`planet_user_id` AS start_id,
																			pc1.`planet_name` AS start_name,
																			pc2.`planet_user_id` AS target_id,
																			pc2.`planet_name` AS target_name
																	FROM " . PLANETS . " AS pc1, " . PLANETS . " AS pc2
																	WHERE pc1.planet_ = '" . $fleet_row['fleet_start_galaxy'] . "' AND
																			pc1.`planet_system` = '" . $fleet_row['fleet_start_system'] . "' AND
																			pc1.`planet_planet` = '" . $fleet_row['fleet_start_planet'] . "' AND
																			pc1.`planet_type` = '" . $fleet_row['fleet_start_type'] . "' AND
																			pc2.`planet_galaxy` = '" . $fleet_row['fleet_end_galaxy'] . "' AND
																			pc2.`planet_system` = '" . $fleet_row['fleet_end_system'] . "' AND
																			pc2.`planet_planet` = '" . $fleet_row['fleet_end_planet'] . "' AND
																			pc2.`planet_type` = '" . $fleet_row['fleet_end_type'] . "'" );

				// SOME REQUIRED VALUES
				$start_name			= $transport_check['start_id'];
				$start_owner_id		= $transport_check['start_name'];
				$target_name		= $transport_check['target_id'];
				$target_owner_id	= $transport_check['target_name'];

				// DIFFERENT TYPES OF MESSAGES
				$message[1]			= sprintf ( $this->_lang['sys_tran_mess_owner'] , $target_name , Fleets_Lib::target_link ( $fleet_row , '' ) , $fleet_row['fleet_resource_metal'] , $this->_lang['Metal'] , $fleet_row['fleet_resource_crystal'] , $this->_lang['Crystal'] , $fleet_row['fleet_resource_deuterium'] , $this->_lang['Deuterium'] );
				$message[2]			= sprintf ( $this->_lang['sys_tran_mess_user'] , $start_name , Fleets_Lib::start_link ( $fleet_row , '' ) , $target_name , Fleets_Lib::target_link ( $fleet_row , '' ) , $fleet_row['fleet_resource_metal'] , $this->_lang['Metal'] , $fleet_row['fleet_resource_crystal'] , $this->_lang['Crystal'] , $fleet_row['fleet_resource_deuterium'] , $this->_lang['Deuterium'] );
				$message[3]			= sprintf ( $this->_lang['sys_tran_mess_back'] , $start_name , Fleets_Lib::start_link ( $fleet_row , '' ) );

				$this->stay_message ( $start_owner_id , $message[1] , $fleet_row['fleet_start_time'] , $this->_lang['sys_mess_transport'] );
				$this->stay_message ( $target_owner_id , $message[2] , $fleet_row['fleet_start_time'] , $this->_lang['sys_mess_transport'] );

				$this->start_stay ( $fleet_row['fleet_id'] );
			}

			if ( $fleet_row['fleet_end_stay'] <= time() )
			{
				parent::return_fleet ( $fleet_row['fleet_id'] );
			}
		}

		if ( $fleet_row['fleet_end_time'] < time() )
		{
			$this->stay_message ( $start_owner_id , $message , $fleet_row['fleet_end_time'] , $this->_lang['sys_mess_fleetback'] );

			parent::restore_fleet ( $fleet_row, TRUE );
			parent::remove_fleet ( $fleet_row['fleet_id'] );
		}
	}

	/**
	 * method start_stay
	 * param $fleet_id
	 * return the fleet on stay mode, just reached the position to start the stay
	*/
	private function start_stay ( $fleet_id )
	{
		parent::$db->query ( "UPDATE " . FLEETS . " SET
								`fleet_mess` = 2
								WHERE `fleet_id` = '" . $fleet_id . "' LIMIT 1 ;" );
	}

	/**
	 * method stay_message
	 * param $owner
	 * param $message
	 * param $time
	 * param $status_message
	 * return send a message with the stay details
	*/
	private function stay_message ( $owner , $message , $time , $status_message )
	{
		Functions_Lib::send_message ( $owner , '' , $time , 5 , $this->_lang['sys_mess_tower'] , $status_message , $message );
	}
}
/* end of stay.php */