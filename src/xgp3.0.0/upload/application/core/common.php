<?php
/**
 * Common File
 *
 * PHP Version 5.5+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

// For first installations, and weird errors
if (@filesize(XGP_ROOT . 'application/config/config.xml') == 0) {
    
    define('XML_CONFIG_FILE', 'config.xml.cfg');
} else {
    
    define('XML_CONFIG_FILE', 'config.xml');
}

use application\core\Database;
use application\core\Hooks;
use application\core\Sessions;
use application\libraries\FunctionsLib;
use application\libraries\SecurePageLib;
use application\libraries\UpdateLib;

$config_file    = XGP_ROOT . 'application/config/config.php';

if (file_exists($config_file)) {
    require $config_file;
}

// Require some stuff
require_once XGP_ROOT . 'application/core/constants.php';
require_once XGP_ROOT . CORE_PATH . 'Database.php';
require_once XGP_ROOT . CORE_PATH . 'XGPCore.php';
require_once XGP_ROOT . CORE_PATH . 'Xml.php';
require_once XGP_ROOT . LIB_PATH . 'FormatLib.php';
require_once XGP_ROOT . LIB_PATH . 'OfficiersLib.php';
require_once XGP_ROOT . LIB_PATH . 'ProductionLib.php';
require_once XGP_ROOT . LIB_PATH . 'FleetsLib.php';
require_once XGP_ROOT . LIB_PATH . 'DevelopmentsLib.php';
require_once XGP_ROOT . LIB_PATH . 'FunctionsLib.php';

// some values by default
$lang   = array();

// set time zone
date_default_timezone_set(FunctionsLib::readConfig('date_time_zone'));

// default skin path
define('DPATH', DEFAULT_SKINPATH);

// For debugging
if (FunctionsLib::readConfig('debug') == 1) {

    // Show all errors
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {

    // Only for Betas, it's going to be changed
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$debug          = FunctionsLib::loadLibrary('DebugLib');
$db             = new Database();
$installed      = FunctionsLib::readConfig('game_installed');
$game_version   = FunctionsLib::readConfig('version');
$game_lang      = FunctionsLib::readConfig('lang');
$current_page   = isset($_GET['page']) ? $_GET['page'] : '';

// check if is installed
if ($installed == 0 && !defined('IN_INSTALL')) {
    
    FunctionsLib::redirect(XGP_ROOT .  'install/');
}

// define game version
if ($installed != 0) {
    
    define('VERSION', ($game_version == '') ? '' : 'v' . $game_version);
}

// define game language
define('DEFAULT_LANG', ($game_lang == '') ? 'spanish' : $game_lang);

if (!defined('IN_INSTALL')) {
    
    require_once XGP_ROOT . CORE_PATH . 'Sessions.php';
    require_once XGP_ROOT . CORE_PATH . 'Hooks.php';
    require_once XGP_ROOT . LIB_PATH . 'StatisticsLib.php';
    require_once XGP_ROOT . LIB_PATH . 'UpdateResourcesLib.php';
    require_once XGP_ROOT . LIB_PATH . 'UpdateLib.php';

    // Sessions
    $session    = new Sessions();

    // Hooks
    $hooks      = new Hooks();

    // Before load stuff
    $hooks->call_hook('before_loads');

    if (!defined('IN_LOGIN') or 'IN_LOGIN' != true) {

        require_once XGP_ROOT . LIB_PATH . 'SecurePageLib.php';
        $exclude    = array('editor');

        if (!in_array($current_page, $exclude)) {
            SecurePageLib::run();
        }
    }

    if (!defined('IN_ADMIN')) {
        // Several updates
        new UpdateLib();
    }
}

/* end of common.php */
