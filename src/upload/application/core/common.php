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
require_once XGP_ROOT . 'application/config/constants.php';
require_once XGP_ROOT . CORE_PATH . 'AutoLoader.php';

// Auto load a few things
AutoLoader::registerDirectory(XGP_ROOT . CORE_PATH);
AutoLoader::registerDirectory(XGP_ROOT . LIB_PATH);

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

    // set time zone
    date_default_timezone_set(FunctionsLib::readConfig('date_time_zone'));

    // For debugging
    if (DEBUG_MODE or ($_SERVER['HTTP_HOST'] == 'localhost')) {

        // Show all errors
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    } else {

        // Hide all errors
        ini_set('display_errors', 0);
        error_reporting(0);   
    }

    $current_page   = isset($_GET['page']) ? $_GET['page'] : '';
    
    // Sessions
    $session    = new Sessions();

    // Hooks
    $hooks      = new Hooks();

    // Before load stuff
    $hooks->call_hook('before_loads');

    if (!defined('IN_LOGIN') or 'IN_LOGIN' != true) {

        $exclude    = array('editor');

        if (!in_array($current_page, $exclude)) {
            SecurePageLib::run();
        }
    }

    if (!defined('IN_ADMIN')) {
        
        define('SHIP_DEBRIS_FACTOR', FunctionsLib::readConfig('fleet_cdr') / 100);
        define('DEFENSE_DEBRIS_FACTOR', FunctionsLib::readConfig('defs_cdr') / 100);
        
        // Several updates
        new UpdateLib();
    }
}

/* end of common.php */
