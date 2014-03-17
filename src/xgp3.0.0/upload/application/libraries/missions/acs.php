<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( 'location:../../' ) );

class Acs extends Missions
{
	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * method acs_mission
	 * param $fleet_row
	 * return the acs result
	*/
	public function acs_mission ( $fleet_row )
	{
		if ( $fleet_row['fleet_mess'] == 0 && $fleet_row['fleet_start_time'] > time() )
		{
			parent::return_fleet ( $fleet_row['fleet_id'] );
		}

		if ( $fleet_row['fleet_end_time'] <= time() )
		{
			parent::restore_fleet ( $fleet_row );
			parent::remove_fleet ( $fleet_row['fleet_id'] );
		}
	}
}
/* end of acs.php */