<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

define ( 'INSIDE'   , TRUE );
define ( 'IN_GAME' 	, TRUE );
define ( 'XGP_ROOT' , './' );

require ( XGP_ROOT . 'application/core/common.php' );

$hooks->call_hook ( 'before_page' );

switch ( ( isset ( $_GET['page'] ) ? $_GET['page'] : NULL ) )
{
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'changelog':
		include_once ( XGP_ROOT . GAME_PATH . 'changelog.php' );
		new Changelog();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'overview':
		include_once ( XGP_ROOT . GAME_PATH . 'overview.php' );
		new Overview();
	break;
	case 'renameplanet':
		include_once ( XGP_ROOT . GAME_PATH . 'renameplanet.php' );
		new RenamePlanet();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'imperium':
		include_once ( XGP_ROOT . GAME_PATH . 'imperium.php' );
		new Imperium();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'resources':
		include_once ( XGP_ROOT . GAME_PATH . 'buildings.php' );
		new Buildings();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'resourceSettings':
		include_once ( XGP_ROOT . GAME_PATH . 'resources.php' );
		new Resources();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'station':
		include_once ( XGP_ROOT . GAME_PATH . 'buildings.php' );
		new Buildings();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'trader':
		include_once ( XGP_ROOT . GAME_PATH . 'trader.php' );
		new Trader();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'research':
		include_once ( XGP_ROOT . GAME_PATH . 'research.php' );
		new Research();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'shipyard':
		include_once ( XGP_ROOT . GAME_PATH . 'shipyard.php' );
		new Shipyard();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'fleet1':
		include_once ( XGP_ROOT . GAME_PATH . 'fleet1.php' );
		new Fleet1();
	break;
	case 'fleet2':
		include_once ( XGP_ROOT . GAME_PATH . 'fleet2.php' );
		new Fleet2();
	break;
	case 'fleet3':
		include_once ( XGP_ROOT . GAME_PATH . 'fleet3.php' );
		new Fleet3();
	break;
	case 'fleet4':
		include_once ( XGP_ROOT . GAME_PATH . 'fleet4.php' );
		new Fleet4();
	break;
	case 'federationlayer':
		include_once ( XGP_ROOT . GAME_PATH . 'federation.php' );
		new Federation();
	break;
	case 'shortcuts':
		include_once ( XGP_ROOT . GAME_PATH . 'fleetshortcuts.php' );
		new Fleetshortcuts();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'movement':
		include_once ( XGP_ROOT . GAME_PATH . 'movement.php' );
		new Movement();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'techtree':
		include_once ( XGP_ROOT . GAME_PATH . 'techtree.php' );
		new Techtree();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'galaxy':
		include_once ( XGP_ROOT . GAME_PATH . 'galaxy.php' );
		new Galaxy();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'phalanx':
		include_once ( XGP_ROOT . GAME_PATH . 'phalanx.php' );
		new Phalanx();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'defense':
		include_once ( XGP_ROOT . GAME_PATH . 'defense.php' );
		new Defense();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'alliance':
		include_once ( XGP_ROOT . GAME_PATH . 'alliance.php' );
		new Alliance();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'forums':
		include_once ( XGP_ROOT . GAME_PATH . 'forum.php' );
		new Forum();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'officier':
		include_once ( XGP_ROOT . GAME_PATH . 'officier.php' );
		new Officier();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'statistics':
		include_once ( XGP_ROOT . GAME_PATH . 'statistics.php' );
		new Statistics();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'search':
		include_once ( XGP_ROOT . GAME_PATH . 'search.php' );
		new Search();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'messages':
		include_once ( XGP_ROOT . GAME_PATH . 'messages.php' );
		new Messages();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'notes':
		include_once ( XGP_ROOT . GAME_PATH . 'notes.php' );
		new Notes();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'buddy':
		include_once ( XGP_ROOT . GAME_PATH . 'buddy.php' );
		new Buddy();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'options':
		include_once ( XGP_ROOT . GAME_PATH . 'options.php' );
		new Options();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'banned':
		include_once ( XGP_ROOT . GAME_PATH . 'banned.php' );
		new Banned();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'CombatReport':
		include_once ( XGP_ROOT . GAME_PATH . 'combatreport.php' );
		new CombatReport();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'infos':
		include_once ( XGP_ROOT . GAME_PATH . 'infos.php' );
		new Infos();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'logout':
		$session->delete();
		Functions_Lib::redirect ( XGP_ROOT );
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	default:

		if ( ! isset ( $_GET['page'] ) )
		{
			Functions_Lib::redirect ( 'game.php?page=overview' );
		}

		if ( ! $hooks->call_hook ( 'new_page' ) )
		{
			Functions_Lib::redirect ( 'game.php?page=overview' );
		}

	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
}
/* end of game.php */