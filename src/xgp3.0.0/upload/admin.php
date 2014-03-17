<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

define ( 'INSIDE'   , TRUE );
define ( 'IN_ADMIN' , TRUE );
define ( 'XGP_ROOT' , './' );

require ( XGP_ROOT . 'application/core/common.php' );

include_once ( XGP_ROOT . 'application/libraries/adm/Administration_Lib.php' );

// check if SSL is setted
Administration_Lib::secure_connection();

switch ( ( isset ( $_GET['page'] ) ? $_GET['page'] : '' ) )
{
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case '':
	default:
		include_once ( XGP_ROOT . ADMIN_PATH . 'home.php');
		new Home();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'moderation':
		include_once ( XGP_ROOT . ADMIN_PATH . 'moderation.php');
		new Moderation();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'reset':
		include_once ( XGP_ROOT . ADMIN_PATH . 'reset.php');
		new Reset();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'queries':
		include_once ( XGP_ROOT . ADMIN_PATH . 'queries.php');
		new Queries();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'logout':
		Functions_Lib::redirect ( XGP_ROOT . 'game.php?page=overview' );
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'server':
		include_once ( XGP_ROOT . ADMIN_PATH . 'server.php');
		new Server();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'modules':
		include_once ( XGP_ROOT . ADMIN_PATH . 'modules.php');
		new Modules();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'registration':
		include_once ( XGP_ROOT . ADMIN_PATH . 'registration.php');
		new Registration();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'planets':
		include_once ( XGP_ROOT . ADMIN_PATH . 'planets.php');
		new Planets();
break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'statistics':
		include_once ( XGP_ROOT . ADMIN_PATH . 'statistics.php');
		new Statistics();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'premium':
		include_once ( XGP_ROOT . ADMIN_PATH . 'premium.php');
		new Premium();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'editor':
		include_once ( XGP_ROOT . ADMIN_PATH . 'editor.php');
		new Editor();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'information':
		include_once ( XGP_ROOT . ADMIN_PATH . 'information.php');
		new Information();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'errors':
		include_once ( XGP_ROOT . ADMIN_PATH . 'errors.php');
		new Errors();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'fleetmovements':
		include_once ( XGP_ROOT . ADMIN_PATH . 'fleetmovements.php');
		new FleetMovements();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'messages':
		include_once ( XGP_ROOT . ADMIN_PATH . 'messages.php');
		new Messages();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'maker':
		include_once ( XGP_ROOT . ADMIN_PATH . 'maker.php');
		new Maker();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'users':
		include_once ( XGP_ROOT . ADMIN_PATH . 'users.php');
		new Users();
	break;
		// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'alliances':
		include_once ( XGP_ROOT . ADMIN_PATH . 'alliances.php');
		new Alliances();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'backup':
		include_once ( XGP_ROOT . ADMIN_PATH . 'backup.php');
		new Backup();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'encrypter':
		include_once ( XGP_ROOT . ADMIN_PATH . 'encrypter.php');
		new Encrypter();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'globalmessage':
		include_once ( XGP_ROOT . ADMIN_PATH . 'globalmessage.php');
		new Globalmessage();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'ban':
		include_once ( XGP_ROOT . ADMIN_PATH . 'ban.php');
		new Ban();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'buildstats':
		include_once ( XGP_ROOT . ADMIN_PATH . 'buildstats.php');
		new ShowBuildStatsPage();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case 'database':
		include_once ( XGP_ROOT . ADMIN_PATH . 'data.php');
		new Data();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
}
/* end of admin.php */