<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( "location:../" ) ) ;

	###########################################################################
	#
	# Constants should not be changed!
	#
	###########################################################################

	// VERSION
	define('SYSTEM_VERSION' 			, '3.0');

	// HOOKS
	define('HOOKS_ENABLED'				, 'FALSE');

	// VISUAL DEFAULT PATHS
	define('SKIN_PATH'					, 'application/styles/skins/');
	define('TEMPLATE_DIR'     		 	, 'application/views/');
	define('DEFAULT_SKINPATH' 		 	, 'application/styles/skins/xgproyect/');
	define('JS_PATH'					, 'js/');
	define('CSS_PATH'					, 'application/styles/css/');
	define('IMG_PATH'					, 'application/styles/images/' );

	// CONTROLLERS PATHS
	define('CONTROLLERS_PATH'			, 'application/controllers/' );
	define('ADMIN_PATH'					, CONTROLLERS_PATH . 'adm/' );
	define('AJAX_PATH'					, CONTROLLERS_PATH . 'ajax/' );
	define('GAME_PATH'					, CONTROLLERS_PATH . 'game/' );
	define('HOME_PATH'					, CONTROLLERS_PATH . 'home/' );
	define('INSTALL_PATH'				, CONTROLLERS_PATH . 'install/' );

	// OTHER PATHS
	define('LOGS_PATH'					, 'logs/' );
	define('LANG_PATH'					, 'application/lang/' );
	define('BACKUP_PATH'				, 'backups/' );

	// GAME URL
	define ('GAMEURL'                  	, "http://" . $_SERVER['HTTP_HOST'] . "/");

	// BASE PATH
	define ('BASE_PATH'					, $_SERVER['HTTP_HOST'] . str_replace ( '/' . basename ( $_SERVER['SCRIPT_NAME'] ) , '' , $_SERVER['SCRIPT_NAME'] ) );

	// UNIVERSE DATA, GALAXY, SYSTEMS AND PLANETS || DEFAULT 9-499-15 RESPECTIVELY
	define ( 'MAX_GALAXY_IN_WORLD'      ,       9 );
	define ( 'MAX_SYSTEM_IN_GALAXY'     ,     499 );
	define ( 'MAX_PLANET_IN_SYSTEM'     ,      15 );

	// FIELDS FOR EACH LEVEL OF THE LUNAR BASE
	define ( 'FIELDS_BY_MOONBASIS_LEVEL',       3 );

	// FIELDS FOR EACH LEVEL OF THE TERRAFORMER
	define ( 'FIELDS_BY_TERRAFORMER'	,       5 );

	// NUMBER OF BUILDINGS THAT CAN GO IN THE CONSTRUCTION QUEUE
	define ( 'MAX_BUILDING_QUEUE_SIZE'  ,       5 );

	// NUMBER OF SHIPS THAT CAN BUILD FOR ONCE
	define ( 'MAX_FLEET_OR_DEFS_PER_ROW',    9999 );

	//PLANET SIZE MULTIPLER
	define ( 'PLANETSIZE_MULTIPLER'		,       1 );

	// INITIAL RESOURCE OF NEW PLANETS
	define ( 'BUILD_METAL'              ,     500 );
	define ( 'BUILD_CRISTAL'            ,     500 );
	define ( 'BUILD_DEUTERIUM'          , 	    0 );

	// OFFICIERS DEFAULT VALUES
	define ( 'AMIRAL'				  	,       2 );
	define ( 'ENGINEER_DEFENSE'			,       2 );
	define ( 'ENGINEER_ENERGY'			,     0.5 );
	define ( 'GEOLOGUE'				  	,     0.1 );
	define ( 'TECHNOCRATE_SPY'			,       2 );
	define ( 'TECHNOCRATE_SPEED'		,    0.25 );

	// INVISIBLES DEBRIS
	define ( 'DEBRIS_LIFE_TIME'      	,  604800 );
	define ( 'DEBRIS_MIN_VISIBLE_SIZE'	, 	  300 );

	// DESTROYED PLANETS LIFE TIME
	define ( 'PLANETS_LIFE_TIME'		, 	   24 ); // IN HOURS

	// VACATION TIME THAT AN USER HAS TO BE ON VACATION MODE BEFORE IT CAN REMOVE IT
	define ( 'VACATION_TIME_FORCED'		, 	    2 ); // IN DAYS

	// TO PREVENT ERRORS
	if ( ! defined ( 'DB_PREFIX' ) )
	{
		define ( 'DB_PREFIX' , '' );
	}

	// TABLES
	define ( 'ACS_FLEETS'				, DB_PREFIX . 'acs_fleets' );
	define ( 'ALLIANCE'					, DB_PREFIX . 'alliance' );
	define ( 'ALLIANCE_STATISTICS'		, DB_PREFIX . 'alliance_statistics' );
	define ( 'BANNED'					, DB_PREFIX . 'banned' );
	define ( 'BUDDY'					, DB_PREFIX . 'buddys' );
	define ( 'BUILDINGS'				, DB_PREFIX . 'buildings' );
	define ( 'DEFENSES'					, DB_PREFIX . 'defenses' );
	define ( 'FLEETS'					, DB_PREFIX . 'fleets' );
	define ( 'MESSAGES' 				, DB_PREFIX . 'messages' );
	define ( 'NOTES'					, DB_PREFIX . 'notes' );
	define ( 'PLANETS'					, DB_PREFIX . 'planets' );
	define ( 'PREMIUM'					, DB_PREFIX . 'premium' );
	define ( 'RESEARCH'					, DB_PREFIX . 'research' );
	define ( 'REPORTS'					, DB_PREFIX . 'reports' );
	define ( 'SESSIONS'					, DB_PREFIX . 'sessions' );
	define ( 'SETTINGS'					, DB_PREFIX . 'settings' );
	define ( 'SHIPS'					, DB_PREFIX . 'ships' );
	define ( 'USERS'					, DB_PREFIX . 'users' );
	define ( 'USERS_STATISTICS'			, DB_PREFIX . 'users_statistics' );
/* end of constants.php */