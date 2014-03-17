<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class NoobsProtection_Lib extends XGPCore
{
	private $_protection; // 1 OR 0
	private $_protectiontime;
	private $_protectionmulti;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		$this->_protection      	= Functions_Lib::read_config ( 'noobprotection' );
		$this->_protectiontime  	= Functions_Lib::read_config ( 'noobprotectiontime' );
		$this->_protectionmulti 	= Functions_Lib::read_config ( 'noobprotectionmulti' );
	}

	/**
	 * method is_weak
	 * param $current_points
	 * param $other_points
	 * return TRUE if player is weak
	 */
	public function is_weak ( $current_points , $other_points )
	{
		return  ( $current_points > $other_points * $this->_protectionmulti  or  $other_points < $this->_protectiontime )  &&  $this->_protection;
	}

	/**
	 * method is_weak
	 * param $current_points
	 * param $other_points
	 * return TRUE if player is strong
	 */
	public function is_strong ( $current_points , $other_points )
	{
		return (  $current_points * $this->_protectionmulti  < $other_points  or  $current_points < $this->_protectiontime  )  &&  $this->_protection;
	}

	/**
	 * method return_points
	 * param $current_user_id
	 * param $other_user_id
	 * return amount of points for each user
	 */
	public function return_points ( $current_user_id , $other_user_id )
	{
		$user_points	= parent::$db->query_fetch ( "SELECT
														(SELECT user_statistic_total_points
															FROM " . USERS_STATISTICS . "
																WHERE `user_statistic_user_id` = ". $current_user_id ."
																) AS user_points,
														(SELECT user_statistic_total_points
															FROM " . USERS_STATISTICS . "
																WHERE `user_statistic_user_id` = ". $other_user_id ."
																) AS target_points" );
		return $user_points;
	}
}
/* end of NoobsProtection_Lib.php */