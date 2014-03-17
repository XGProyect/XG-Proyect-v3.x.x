<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Administration_Lib extends XGPCore
{
	/**
	 * __construct()
	 */
	public function __construct ()
	{
		parent::__construct();
	}

	/**
	 * method have_access
	 * param $user_level
	 * return TRUE = access allowed | FALSE = access disallowed
	 */
	public static function have_access ( $user_level )
	{
		if ( $user_level >= 1 )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * method install_dir_exists
	 * param
	 * return TRUE = install dir exists | FALSE = install dir was not found
	 */
	public static function install_dir_exists()
	{
		if ( file_exists ( XGP_ROOT . 'install/' ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * method authorization
	 * param1 $user_level
	 * param2 $permission
	 * return the page permission
	 */
	public static function authorization ( $user_level , $permission )
	{
		$QueryModeration	=	Functions_Lib::read_config ( 'moderation' );
		$QueryModerationEx  =   explode ( ";" , $QueryModeration );
		$Moderator			=	explode ( "," , $QueryModerationEx[0] );
		$Operator			=	explode ( "," , $QueryModerationEx[1] );
		$Administrator		=	explode ( "," , $QueryModerationEx[2] );

		if ( $user_level == 1 )
		{
			$permissions['observation']		=	$Moderator[0];
			$permissions['edit_users']		=	$Moderator[1];
			$permissions['config_game']		=	$Moderator[2];
			$permissions['use_tools']		=	$Moderator[3];
			$permissions['track_activity']	=	$Moderator[4];
		}

		if ( $user_level == 2 )
		{
			$permissions['observation']		=	$Operator[0];
			$permissions['edit_users']		=	$Operator[1];
			$permissions['config_game']		=	$Operator[2];
			$permissions['use_tools']		=	$Operator[3];
			$permissions['track_activity']	=	$Operator[4];
		}

		if ( $user_level == 3 )
		{
			$permissions['observation']		=	1;
			$permissions['edit_users']		=	1;
			$permissions['config_game']		=	1;
			$permissions['use_tools']		=	1;
			$permissions['track_activity']	=	$Administrator[0];
		}

		return $permissions[$permission];
	}

	/**
	 * method save_message
	 * param $result
	 * return show the save message
	 */
	public static function save_message ( $result = 'ok' , $message )
	{
		switch ( $result )
		{
			case 'ok':
				$parse['color']		= 'alert-success';
				$parse['status']	= parent::$lang['gn_ok_title'];
			break;

			case 'error':
				$parse['color']		= 'alert-error';
				$parse['status']	= parent::$lang['gn_error_title'];
			break;

			case 'warning':
				$parse['color']		= 'alert-block';
				$parse['status']	= parent::$lang['gn_warning_title'];
			break;
		}

		$parse['message']			= $message;

		return parent::$page->parse_template ( parent::$page->get_template ( 'adm/save_message_view' ) , $parse );
	}

	/**
	 * method return_rank
	 * param $authlevel
	 * return show the save message
	 */
	public static function return_rank ( $authlevel )
	{
		switch ( $authlevel )
		{
			default:
			case 0:

				return parent::$lang['ge_user'];

			break;

			case 1:

				return parent::$lang['ge_go'];

			break;

			case 2:

				return parent::$lang['ge_sgo'];

			break;

			case 3:

				return parent::$lang['ge_ga'];

			break;
		}
	}

	/**
	 * method show_pop_up
	 * param $message
	 * return show the pop up
	 */
	public static function show_pop_up ( $message )
	{
		$parse['message']	= $message;

		return parent::$page->parse_template ( parent::$page->get_template ( 'adm/popup_view' ) , $parse );
	}

	/**
	 * method secure_connection
	 * param
	 * return stablish a secure connection and force it
	 */
	public static function secure_connection()
	{
		if ( ( Functions_Lib::read_config ( 'ssl_enabled' ) == 1 ) && ( $_SERVER['SERVER_PORT'] !== 443 ) && ( empty ( $_SERVER['HTTPS'] ) or $_SERVER['HTTPS'] === 'off' ) )
		{
			Functions_Lib::redirect ( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		}
	}
}
/* end of Administration_Lib.php */