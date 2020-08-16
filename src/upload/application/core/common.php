<?php

/**
 * Common File
 *
 * PHP Version 7.1+
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */

namespace application\core;

use application\core\Hooks;
use application\core\Sessions;
use application\libraries\FunctionsLib;
use application\libraries\SecurePageLib;
use application\libraries\UpdatesLibrary;
use AutoLoader;
use Exception;

// Require some stuff
require_once XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'constants.php';
require_once XGP_ROOT . CORE_PATH . 'AutoLoader.php';

class Common
{
    /**
     * Contains the value that indicated if the game is installed or not
     *
     * @var boolean
     */
    private $is_installed = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bootUp();
    }

    /**
     * Return a new session object
     *
     * @return Sessions|null
     */
    public function setSession(): ?Sessions
    {
        if (!defined('IN_INSTALL')) {
            return new Sessions;
        }

        return null;
    }

    /**
     * Return a new Hooks objects
     *
     * @return Hooks|null
     */
    public function setHooks(): ?Hooks
    {
        if (!defined('IN_INSTALL')) {
            $hooks = new Hooks;

            // Before load stuff
            $hooks->call_hook('before_loads');

            return $hooks;
        }

        return null;
    }

    /**
     * Set secure page to escape requests
     *
     * @return void
     */
    public function setSecure(): void
    {
        if (!defined('IN_INSTALL') && (!defined('IN_LOGIN') or 'IN_LOGIN' != true)) {
            $current_page = isset($_GET['page']) ? $_GET['page'] : '';

            $exclude = ['languages'];

            if (!in_array($current_page, $exclude)) {
                SecurePageLib::run();
            }
        }
    }

    /**
     * Set updates
     *
     * @return void
     */
    public function setUpdates(): void
    {
        if (!defined('IN_INSTALL') && !defined('IN_ADMIN')) {
            define('SHIP_DEBRIS_FACTOR', FunctionsLib::readConfig('fleet_cdr') / 100);
            define('DEFENSE_DEBRIS_FACTOR', FunctionsLib::readConfig('defs_cdr') / 100);

            // Several updates
            new UpdatesLibrary;
        }
    }

    /**
     * Start the system
     *
     * @return void
     */
    private function bootUp(): void
    {
        $this->autoLoad();
        $this->setErrorHandler();
        $this->isGameInstalled();
        $this->setSystemTimezone();
    }

    /**
     * Auto load the core, libraries and helpers
     *
     * @return void
     */
    private function autoLoad(): void
    {
        AutoLoader::registerDirectory(XGP_ROOT . CORE_PATH);
        AutoLoader::registerDirectory(XGP_ROOT . LIB_PATH);
        AutoLoader::registerDirectory(XGP_ROOT . HELPERS_PATH);
    }

    /**
     * Set a new error handler
     *
     * @return void
     */
    private function setErrorHandler(): void
    {
        // XGP error handler
        new ErrorHandler;
    }

    /**
     * Check if the game is installed
     *
     * @return void
     */
    private function isGameInstalled(): void
    {
        try {
            $config_file = XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

            if (file_exists($config_file)) {
                require $config_file;

                // check if it is installed
                if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME') && defined('DB_PREFIX')) {
                    $this->is_installed = true;
                }

                // set language
                $this->initLanguage();

                if (!$this->is_installed && !defined('IN_INSTALL')) {
                    FunctionsLib::redirect(SYSTEM_ROOT . 'install/');
                }
            } else {
                throw new Exception('Error #001 - config.php file doesn\'t exists!');
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Init the default language
     *
     * @return void
     */
    private function initLanguage(): void
    {
        $set = false;

        if ($this->is_installed && !defined('IN_INSTALL')) {
            $set = true;
        }

        define('DEFAULT_LANG', FunctionsLib::getCurrentLanguage($set));
    }

    /**
     * Set the system timezone
     *
     * @return void
     */
    private function setSystemTimezone(): void
    {
        if (!defined('IN_INSTALL')) {
            date_default_timezone_set(FunctionsLib::readConfig('date_time_zone'));
        }
    }
}

$bootUp = new Common;

$session = $bootUp->setSession();
$hooks = $bootUp->setHooks();

/* end of common.php */
