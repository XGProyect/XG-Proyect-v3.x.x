<?php

//##########################################################################
//
// Constants should not be changed, unless you know what you are doing!
//
//##########################################################################

/**
 *
 * SYSTEM CONFIGURATION
 *
 */
// GAME FILES VERSION
define('SYSTEM_VERSION', '3.4.0');

// DEBUG MODE
define('DEBUG_MODE', false);

// LOG DB AND PHP ERRORS
define('LOG_ERRORS', true);

// ERROR LOGS MAIL - If you share your errors with XG Proyect we will be able to improve the project faster.
define('ERROR_LOGS_MAIL', 'errors@xgproyect.org');

/**
 *
 * SYSTEM PATHS CONFIGURATION
 *
 */
if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
    (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')) {
    define('PROTOCOL', 'https://');
} else {
    define('PROTOCOL', 'http://');
}

// BASE PATH
define(
    'BASE_PATH',
    $_SERVER['HTTP_HOST'] . str_replace(
        '/' . basename($_SERVER['SCRIPT_NAME']),
        '',
        $_SERVER['SCRIPT_NAME']
    )
);

// SYSTEM ROOT, IGNORING PUBLIC
define('SYSTEM_ROOT', PROTOCOL . strtr(BASE_PATH, ['public' => '', 'public/' => '']));

// GAME URL
define('GAMEURL', PROTOCOL . $_SERVER['HTTP_HOST'] . '/');

// ADMIN PATHS
define('ADM_URL', PROTOCOL . strtr(BASE_PATH, ['public' => '', 'public/' => '']));

/**
 *
 * GLOBAL DIRECTORY STRUCTURE
 *
 */
define('APP_PATH', 'app' . DIRECTORY_SEPARATOR);
define('CONFIGS_PATH', 'config' . DIRECTORY_SEPARATOR);
define('DATABASE_PATH', 'database' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', 'public' . DIRECTORY_SEPARATOR);
define('RESOURCES_PATH', 'resources' . DIRECTORY_SEPARATOR);
define('DATA_PATH', 'storage' . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', 'vendor' . DIRECTORY_SEPARATOR);

/**
 *
 * APPLICATION DIRECTORY STRUCTURE
 *
 */
define('CONTROLLERS_PATH', APP_PATH . 'Http' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR);
define('CORE_PATH', APP_PATH . 'Core' . DIRECTORY_SEPARATOR);
define('HELPERS_PATH', APP_PATH . 'Helpers' . DIRECTORY_SEPARATOR);
define('LIB_PATH', APP_PATH . 'Libraries' . DIRECTORY_SEPARATOR);
define('MODELS_PATH', APP_PATH . 'Models' . DIRECTORY_SEPARATOR);

/**
 *
 * CONTROLLERS DIRECTORY STRUCTURE
 *
 */
define('ADMIN_PATH', CONTROLLERS_PATH . 'Adm' . DIRECTORY_SEPARATOR);
define('AJAX_PATH', CONTROLLERS_PATH . 'Ajax' . DIRECTORY_SEPARATOR);
define('GAME_PATH', CONTROLLERS_PATH . 'Game' . DIRECTORY_SEPARATOR);
define('HOME_PATH', CONTROLLERS_PATH . 'Home' . DIRECTORY_SEPARATOR);
define('INSTALL_PATH', CONTROLLERS_PATH . 'Install' . DIRECTORY_SEPARATOR);

/**
 *
 * DATABASE DIRECTORY STRUCTURE
 *
 */
define('MIGRATION_PATH', DATABASE_PATH . 'migration' . DIRECTORY_SEPARATOR);
define('UPDATE_PATH', DATABASE_PATH . 'update' . DIRECTORY_SEPARATOR);

/**
 *
 * RESOURCES DIRECTORY STRUCTURE
 *
 */
define('LANG_PATH', RESOURCES_PATH . 'lang' . DIRECTORY_SEPARATOR);
define('VIEWS_DIR', RESOURCES_PATH . 'views' . DIRECTORY_SEPARATOR);

/**
 *
 * STORAGE DIRECTORY STRUCTURE
 *
 */
define('BACKUP_PATH', DATA_PATH . 'backups' . DIRECTORY_SEPARATOR);
define('LOGS_PATH', DATA_PATH . 'logs' . DIRECTORY_SEPARATOR);

/**
 *
 * PUBLIC DIRECTORY STRUCTURE
 *
 */
define('CSS_PATH', PUBLIC_PATH . 'css' . DIRECTORY_SEPARATOR);
define('ADMIN_PUBLIC_PATH', PUBLIC_PATH . 'admin' . DIRECTORY_SEPARATOR);
define('IMG_PATH', PUBLIC_PATH . 'images' . DIRECTORY_SEPARATOR);
define('PUB_INS_PATH', PUBLIC_PATH . 'install' . DIRECTORY_SEPARATOR);
define('JS_PATH', PUBLIC_PATH . 'js' . DIRECTORY_SEPARATOR);
define('UPLOAD_PATH', PUBLIC_PATH . 'upload' . DIRECTORY_SEPARATOR);

/**
 *
 * SKIN DIRECTORY STRUCTURE
 *
 */
define('SKIN_PATH', UPLOAD_PATH . 'skins' . DIRECTORY_SEPARATOR);
define('DEFAULT_SKINPATH', SKIN_PATH . 'xgproyect' . DIRECTORY_SEPARATOR);
define('DPATH', DEFAULT_SKINPATH);

/**
 *
 * TIMING CONSTANTS
 *
 */
define('ONE_DAY', (60 * 60 * 24)); // 1 DAY
define('ONE_WEEK', (ONE_DAY * 7)); // 1 WEEK
define('ONE_MONTH', (ONE_DAY * 30)); // 1 MONTH

/**
 *
 * GAME MECHANICS RELATED
 * You can change almost anything below without breaking the game
 *
 */
// UNIVERSE DATA, GALAXY, SYSTEMS AND PLANETS || DEFAULT 9-499-15 RESPECTIVELY
define('MAX_GALAXY_IN_WORLD', 9);
define('MAX_SYSTEM_IN_GALAXY', 499);
define('MAX_PLANET_IN_SYSTEM', 15);

/**
 * New accounts planet position separation
 * By default new players are separated by just 1 galaxy and/or 1 system
 * Changing these factors can increase the initial separation
 */
define('GALAXY_SEPARATION_FACTOR', 1);
define('SYSTEM_SEPARATION_FACTOR', 1);
define('PLANET_SEPARATION_FACTOR', 2);

// FIELDS FOR EACH LEVEL OF THE LUNAR BASE
define('FIELDS_BY_MOONBASIS_LEVEL', 3);

// FIELDS FOR EACH LEVEL OF THE TERRAFORMER
define('FIELDS_BY_TERRAFORMER', 5);

// NUMBER OF BUILDINGS THAT CAN GO IN THE CONSTRUCTION QUEUE
define('MAX_BUILDING_QUEUE_SIZE', 5);

// NUMBER OF SHIPS THAT CAN BUILD FOR ONCE
define('MAX_FLEET_OR_DEFS_PER_ROW', 9999);

// MAX RESULTS TO SHOW IN SEARCH
define('MAX_SEARCH_RESULTS', 25);

//PLANET SIZE MULTIPLER
define('PLANETSIZE_MULTIPLER', 1);

// INITIAL RESOURCE OF NEW PLANETS
define('BUILD_METAL', 500);
define('BUILD_CRISTAL', 500);
define('BUILD_DEUTERIUM', 0);

// OFFICIERS DEFAULT VALUES
define('AMIRAL', 2);
define('ENGINEER_DEFENSE', 2);
define('ENGINEER_ENERGY', 0.1);
define('GEOLOGUE', 0.1);
define('TECHNOCRATE_SPY', 2);
define('TECHNOCRATE_SPEED', 0.25);

// INVISIBLES DEBRIS
define('DEBRIS_LIFE_TIME', ONE_WEEK);
define('DEBRIS_MIN_VISIBLE_SIZE', 300);

// DESTROYED PLANETS LIFE TIME
define('PLANETS_LIFE_TIME', 24); // IN HOURS

// VACATION TIME THAT AN USER HAS TO BE ON VACATION MODE BEFORE IT CAN REMOVE IT
define('VACATION_TIME_FORCED', 2); // IN DAYS

// RESOURCE MARKET
define('BASIC_RESOURCE_MARKET_DM', [
    'metal' => 4500,
    'crystal' => 9000,
    'deuterium' => 13500,
]);

// PHALANX COST
define('PHALANX_COST', 10000);

/**
 *
 * DATABASE RELATED
 *
 */
//##########################################################################
//
// Constants should not be changed, unless you know what you are doing!
//
//##########################################################################

// TABLES
define('ACS', '{xgp_prefix}acs');
define('ACS_MEMBERS', '{xgp_prefix}acs_members');
define('ALLIANCE', '{xgp_prefix}alliance');
define('ALLIANCE_STATISTICS', '{xgp_prefix}alliance_statistics');
define('BANNED', '{xgp_prefix}banned');
define('BUDDY', '{xgp_prefix}buddys');
define('BUILDINGS', '{xgp_prefix}buildings');
define('CHANGELOG', '{xgp_prefix}changelog');
define('DEFENSES', '{xgp_prefix}defenses');
define('FLEETS', '{xgp_prefix}fleets');
define('LANGUAGES', '{xgp_prefix}languages');
define('MESSAGES', '{xgp_prefix}messages');
define('NOTES', '{xgp_prefix}notes');
define('OPTIONS', '{xgp_prefix}options');
define('PLANETS', '{xgp_prefix}planets');
define('PREFERENCES', '{xgp_prefix}preferences');
define('PREMIUM', '{xgp_prefix}premium');
define('RESEARCH', '{xgp_prefix}research');
define('REPORTS', '{xgp_prefix}reports');
define('SESSIONS', '{xgp_prefix}sessions');
define('SHIPS', '{xgp_prefix}ships');
define('USERS', '{xgp_prefix}users');
define('USERS_STATISTICS', '{xgp_prefix}users_statistics');

define('MB_ENABLED', false);
define('ICONV_ENABLED', false);

ini_set('default_charset', 'UTF-8');
