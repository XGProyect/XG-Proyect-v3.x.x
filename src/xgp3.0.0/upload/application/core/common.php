<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

// For first installations, and weird errors
if ( @filesize ( XGP_ROOT . 'application/config/config.xml' ) == 0 )
{
	define ( 'XML_CONFIG_FILE' , 'config.xml.cfg' );
}
else
{
	define ( 'XML_CONFIG_FILE' , 'config.xml' );
}

// Require some stuff
require_once ( XGP_ROOT . 'application/core/Database.php' );
require_once ( XGP_ROOT . 'application/core/constants.php' );
require_once ( XGP_ROOT . 'application/core/XGPCore.php' );
require_once ( XGP_ROOT . 'application/core/Xml.php' );
require_once ( XGP_ROOT . 'application/libraries/Format_Lib.php' );
require_once ( XGP_ROOT . 'application/libraries/Officiers_Lib.php' );
require_once ( XGP_ROOT . 'application/libraries/Production_Lib.php' );
require_once ( XGP_ROOT . 'application/libraries/Fleets_Lib.php' );
require_once ( XGP_ROOT . 'application/libraries/Developments_Lib.php' );
require_once ( XGP_ROOT . 'application/libraries/Functions_Lib.php' );

// some values by default
$lang          	= array();

// set time zone
date_default_timezone_set ( Functions_Lib::read_config ( 'date_time_zone' ) );

// default skin path
define ( 'DPATH' , DEFAULT_SKINPATH );

// For debugging
if ( Functions_Lib::read_config ( 'debug' ) == 1 )
{
	// Show all errors
	ini_set ( 'display_errors' , 1 );
	error_reporting ( E_ALL );
}
else
{
	// Only for Betas, it's going to be changed
	ini_set ( 'display_errors' , 1 );
	error_reporting ( E_ALL );
}

$debug 				= Functions_Lib::load_library ( 'Debug_Lib' );
$db					= new Database();
$installed			= Functions_Lib::read_config ( 'game_installed' );
$game_version		= Functions_Lib::read_config ( 'version' );
$game_lang			= Functions_Lib::read_config  ( 'lang' );
$current_page		= isset ( $_GET['page'] ) ? $_GET['page'] : '';

// check if is installed
if ( $installed == 0 && ! defined ( 'IN_INSTALL' ) )
{
	Functions_Lib::redirect ( XGP_ROOT .  'install/' );
}

// define game version
if ( $installed != 0 )
{
	define ( 'VERSION' , ( $game_version == '' ) ? '' : 'v' . $game_version );
}

// define game language
define ( 'DEFAULT_LANG'	, (	$game_lang  == '' ) ? 'spanish' : $game_lang );

if ( ! defined ( 'IN_INSTALL' ) )
{
	require_once ( XGP_ROOT . 'application/core/Sessions.php' );
	require_once ( XGP_ROOT . 'application/core/Hooks.php' );
	require_once ( XGP_ROOT . 'application/libraries/Statistics_Lib.php' );
	require_once ( XGP_ROOT . 'application/libraries/UpdateResources_Lib.php' );
	require_once ( XGP_ROOT . 'application/libraries/Update_Lib.php' );

	// Sessions
	$session	= new Sessions();

	// Hooks
	$hooks		= new Hooks();

	// Before load stuff
	$hooks->call_hook ( 'before_loads' );

	if ( ! isset ( $InLogin ) or $InLogin != TRUE )
	{
		require_once ( XGP_ROOT . 'application/libraries/SecurePage_Lib.php' );
		$exclude	= array ( 'editor' );

		if ( !in_array ( $current_page , $exclude ) )
		{
			SecurePage_Lib::run();
		}
	}

	if ( ! defined ( 'IN_ADMIN' ) )
	{
		// SEVERAL UPDATES
		new Update_Lib();
	}
}
/* end of common.php */