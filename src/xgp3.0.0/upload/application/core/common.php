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

use application\core\Database;
use application\core\Hooks;
use application\core\Sessions;
use application\libraries\FunctionsLib;
use application\libraries\SecurePageLib;
use application\libraries\UpdateLib;

$config_file    = XGP_ROOT . 'application/config/config.php';
$installed      = false;

if (file_exists($config_file)) {

    require $config_file;
    $installed  = true;
}

// Require some stuff
require_once XGP_ROOT . 'application/core/constants.php';
require_once XGP_ROOT . CORE_PATH . 'Database.php';
require_once XGP_ROOT . CORE_PATH . 'XGPCore.php';
require_once XGP_ROOT . CORE_PATH . 'Options.php';
require_once XGP_ROOT . CORE_PATH . 'Xml.php';
require_once XGP_ROOT . LIB_PATH . 'FormatLib.php';
require_once XGP_ROOT . LIB_PATH . 'OfficiersLib.php';
require_once XGP_ROOT . LIB_PATH . 'ProductionLib.php';
require_once XGP_ROOT . LIB_PATH . 'FleetsLib.php';
require_once XGP_ROOT . LIB_PATH . 'DevelopmentsLib.php';
require_once XGP_ROOT . LIB_PATH . 'FunctionsLib.php';

// some values by default
$lang   = array();

// DEFAULT LANGUAGE
if ($installed) {
    if (defined('IN_INSTALL')) {
        $set    = false;
    } else {
        $set    = true;
    }
} else {
    $set    = false;
}

define('DEFAULT_LANG', FunctionsLib::getCurrentLanguage($set));   

// check if is installed
if ($installed == false && !defined('IN_INSTALL')) {
    
    FunctionsLib::redirect(XGP_ROOT .  'install/');
}

// when we are not in the install section
if (!defined('IN_INSTALL')) {
    
    require_once XGP_ROOT . CORE_PATH . 'Sessions.php';
    require_once XGP_ROOT . CORE_PATH . 'Hooks.php';
    require_once XGP_ROOT . LIB_PATH . 'StatisticsLib.php';
    require_once XGP_ROOT . LIB_PATH . 'UpdateResourcesLib.php';
    require_once XGP_ROOT . LIB_PATH . 'UpdateLib.php';

    // set time zone
    date_default_timezone_set(FunctionsLib::readConfig('date_time_zone'));

    // For debugging
    if (DEBUG_MODE == 1) {

        // Show all errors
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    } else {

        // Only for Betas, it's going to be changed
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    }

    $current_page   = isset($_GET['page']) ? $_GET['page'] : '';
    
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
