<?php
/**
 * Constants
 *
 * PHP Version 5.5+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
###########################################################################
#
# Constants should not be changed, unless you know what you are doing!
#
###########################################################################

/**
 * 
 * SYSTEM CONFIGURATION
 * 
 */
// GAME FILES VERSION
define('SYSTEM_VERSION', '3.1.0');

// HOOKS
define('HOOKS_ENABLED', false);

// DEBUG MODE
define('DEBUG_MODE', false);

// LOG DB AND PHP ERRORS
define('LOG_ERRORS', true);

// ERROR LOGS MAIL
define('ERROR_LOGS_MAIL', '');

// RUN QUERIES FLAG
define('ADMIN_ACCESS_QUERY', true);

/**
 * 
 * SYSTEM PATHS CONFIGURATION
 * 
 */
// BASE PATH
define(
    'BASE_PATH', $_SERVER['HTTP_HOST'] . str_replace(
        DIRECTORY_SEPARATOR . basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']
    )
);

// SYSTEM ROOT, IGNORING PUBLIC
define('SYSTEM_ROOT', 'http://' . strtr(BASE_PATH, ['public' => '']));

// GAME URL
define('GAMEURL', "http://" . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR);

// ADMIN PATHS
define('ADM_URL', 'http://' . strtr(BASE_PATH, ['public' => '']));
define('ADM_SECURE_URL', 'https://' . strtr(BASE_PATH, ['public' => '']));

/**
 * 
 * GLOBAL DIRECTORY STRUCTURE
 * 
 */
define('APP_PATH', 'application' . DIRECTORY_SEPARATOR);
define('DATA_PATH', 'data' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', 'public' . DIRECTORY_SEPARATOR);
define('SYSTEM_PATH', 'system' . DIRECTORY_SEPARATOR);

/**
 * 
 * APPLICATION DIRECTORY STRUCTURE
 * 
 */
define('CONFIGS_PATH', APP_PATH . 'config' . DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', APP_PATH . 'controllers' . DIRECTORY_SEPARATOR);
define('CORE_PATH', APP_PATH . 'core' . DIRECTORY_SEPARATOR);
define('HOOKS_PATH', APP_PATH . 'hooks' . DIRECTORY_SEPARATOR);
define('LANG_PATH', APP_PATH . 'language' . DIRECTORY_SEPARATOR);
define('LIB_PATH', APP_PATH . 'libraries' . DIRECTORY_SEPARATOR);
define('LOGS_PATH', APP_PATH . 'logs' . DIRECTORY_SEPARATOR);
define('MODELS_PATH', APP_PATH . 'models' . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', APP_PATH . 'third_party' . DIRECTORY_SEPARATOR);
define('TEMPLATE_DIR', APP_PATH . 'views' . DIRECTORY_SEPARATOR);

/**
 * 
 * CONTROLLERS DIRECTORY STRUCTURE
 * 
 */
define('ADMIN_PATH', CONTROLLERS_PATH . 'adm' . DIRECTORY_SEPARATOR);
define('AJAX_PATH', CONTROLLERS_PATH . 'ajax' . DIRECTORY_SEPARATOR);
define('GAME_PATH', CONTROLLERS_PATH . 'game' . DIRECTORY_SEPARATOR);
define('HOME_PATH', CONTROLLERS_PATH . 'home' . DIRECTORY_SEPARATOR);
define('INSTALL_PATH', CONTROLLERS_PATH . 'install' . DIRECTORY_SEPARATOR);

/**
 * 
 * DATA DIRECTORY STRUCTURE
 * 
 */
define('BACKUP_PATH', DATA_PATH . 'backups' . DIRECTORY_SEPARATOR);

/**
 * 
 * PUBLIC DIRECTORY STRUCTURE
 * 
 */
define('CSS_PATH', PUBLIC_PATH . 'css' . DIRECTORY_SEPARATOR);
define('IMG_PATH', PUBLIC_PATH . 'images' . DIRECTORY_SEPARATOR);
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
 * GAME MECHANICS RELATED
 * You can change almost anything below without breaking the game
 * 
 */
// UNIVERSE DATA, GALAXY, SYSTEMS AND PLANETS || DEFAULT 9-499-15 RESPECTIVELY
define('MAX_GALAXY_IN_WORLD', 9);
define('MAX_SYSTEM_IN_GALAXY', 499);
define('MAX_PLANET_IN_SYSTEM', 15);

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
define('ENGINEER_ENERGY', 0.5);
define('GEOLOGUE', 0.1);
define('TECHNOCRATE_SPY', 2);
define('TECHNOCRATE_SPEED', 0.25);

// INVISIBLES DEBRIS
define('DEBRIS_LIFE_TIME', 604800);
define('DEBRIS_MIN_VISIBLE_SIZE', 300);

// DESTROYED PLANETS LIFE TIME
define('PLANETS_LIFE_TIME', 24); // IN HOURS
// VACATION TIME THAT AN USER HAS TO BE ON VACATION MODE BEFORE IT CAN REMOVE IT
define('VACATION_TIME_FORCED', 2); // IN DAYS

/**
 * 
 * DATABASE RELATED 
 * 
 */
###########################################################################
#
# Constants should not be changed, unless you know what you are doing!
#
###########################################################################
// TO PREVENT ERRORS
if (!defined('DB_PREFIX')) {

    define('DB_PREFIX', '');
}

// TABLES
define('ACS_FLEETS', DB_PREFIX . 'acs_fleets');
define('ALLIANCE', DB_PREFIX . 'alliance');
define('ALLIANCE_STATISTICS', DB_PREFIX . 'alliance_statistics');
define('BANNED', DB_PREFIX . 'banned');
define('BUDDY', DB_PREFIX . 'buddys');
define('BUILDINGS', DB_PREFIX . 'buildings');
define('DEFENSES', DB_PREFIX . 'defenses');
define('FLEETS', DB_PREFIX . 'fleets');
define('MESSAGES', DB_PREFIX . 'messages');
define('NOTES', DB_PREFIX . 'notes');
define('OPTIONS', DB_PREFIX . 'options');
define('PLANETS', DB_PREFIX . 'planets');
define('PREMIUM', DB_PREFIX . 'premium');
define('RESEARCH', DB_PREFIX . 'research');
define('REPORTS', DB_PREFIX . 'reports');
define('SESSIONS', DB_PREFIX . 'sessions');
define('SETTINGS', DB_PREFIX . 'settings');
define('SHIPS', DB_PREFIX . 'ships');
define('USERS', DB_PREFIX . 'users');
define('USERS_STATISTICS', DB_PREFIX . 'users_statistics');

// FOR MAILING
$charset = 'UTF-8';
ini_set('default_charset', $charset);

if (extension_loaded('mbstring')) {
    define('MB_ENABLED', TRUE);
    // mbstring.internal_encoding is deprecated starting with PHP 5.6
    // and it's usage triggers E_DEPRECATED messages.
    @ini_set('mbstring.internal_encoding', $charset);
    // This is required for mb_convert_encoding() to strip invalid characters.
    // That's utilized by CI_Utf8, but it's also done for consistency with iconv.
    mb_substitute_character('none');
} else {
    define('MB_ENABLED', FALSE);
}

// There's an ICONV_IMPL constant, but the PHP manual says that using
// iconv's predefined constants is "strongly discouraged".
if (extension_loaded('iconv')) {
    define('ICONV_ENABLED', TRUE);
    // iconv.internal_encoding is deprecated starting with PHP 5.6
    // and it's usage triggers E_DEPRECATED messages.
    @ini_set('iconv.internal_encoding', $charset);
} else {
    define('ICONV_ENABLED', FALSE);
}

/* end of constants.php */
