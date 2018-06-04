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
use application\libraries\Updates_library;

// report all errors
error_reporting(E_ALL);
ini_set('display_errors', 0);

$config_file = XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
$installed = false;

if (file_exists($config_file) && filesize($config_file) > 0) {

    require $config_file;
    $installed = true;
}

// Require some stuff
require_once XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'constants.php';
require_once XGP_ROOT . CORE_PATH . 'AutoLoader.php';

// Auto load a few things
AutoLoader::registerDirectory(XGP_ROOT . CORE_PATH);
AutoLoader::registerDirectory(XGP_ROOT . LIB_PATH);

// For debugging

if (DEBUG_MODE or (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false)) {

    // Show all errors
    ini_set('display_errors', 1);
}

// We should use our custom function to handle errors.
set_error_handler(function($code, $description, $file = null, $line = null, $context = null) {

    $displayErrors = strtolower(ini_get("display_errors"));

    if (error_reporting() === 0 || $displayErrors === "on") {

        return false;
    }

    $debug = new application\libraries\DebugLib();
    $debug->error($description, $code, 'php');
});

// some values by default
$lang = [];

// DEFAULT LANGUAGE
if ($installed) {

    if (defined('IN_INSTALL')) {

        $set = false;
    } else {

        $set = true;
    }
} else {

    $set = false;
}

define('DEFAULT_LANG', FunctionsLib::getCurrentLanguage($set));

// check if is installed
if ($installed == false && !defined('IN_INSTALL')) {

    FunctionsLib::redirect(SYSTEM_ROOT . 'install/');
}

// when we are not in the install section
if (!defined('IN_INSTALL')) {

    // set time zone
    date_default_timezone_set(FunctionsLib::readConfig('date_time_zone'));

    $current_page = isset($_GET['page']) ? $_GET['page'] : '';

    // Sessions
    $session = new Sessions();

    // Hooks
    $hooks = new Hooks();

    // Before load stuff
    $hooks->call_hook('before_loads');

    if (!defined('IN_LOGIN') or 'IN_LOGIN' != true) {

        $exclude = ['editor'];

        if (!in_array($current_page, $exclude)) {

            SecurePageLib::run();
        }
    }

    if (!defined('IN_ADMIN')) {

        define('SHIP_DEBRIS_FACTOR', FunctionsLib::readConfig('fleet_cdr') / 100);
        define('DEFENSE_DEBRIS_FACTOR', FunctionsLib::readConfig('defs_cdr') / 100);

        // Several updates
        new Updates_library();
    }
}

/* end of common.php */
