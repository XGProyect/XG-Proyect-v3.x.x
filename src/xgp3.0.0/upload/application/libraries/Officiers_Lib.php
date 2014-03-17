<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( !defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ); }

class Officiers_Lib
{
	/**
	 * method max_resource
	 * param $storage_level
	 * return max storage capacity
	 */
	 public static function is_officier_active ( $expire_time )
	 {
		return ( $expire_time > time() && $expire_time != 0 );
	 }

	/**
	 * method get_max_espionage
	 * param $espionage_tech
	 * param $technocrate_level
	 * return the current espinage tech level
	 */
	public static function get_max_espionage ( $espionage_tech , $technocrate_level )
	{
		return $espionage_tech + ( 1 * ( self::is_officier_active ( $technocrate_level ) ? TECHNOCRATE_SPY : 0 ) );
	}

	/**
	 * method get_max_espionage
	 * param $computer_tech
	 * param $amiral_level
	 * return the current computer tech level
	 */
	public static function get_max_computer ( $computer_tech , $amiral_level )
	{
		return 1 + $computer_tech + ( 1 * ( self::is_officier_active ( $amiral_level ) ? AMIRAL : 0 ) );
	}
}
/* end of Officiers_Lib.php */