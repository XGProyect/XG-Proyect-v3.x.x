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

use application\core\ErrorHandler;
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
    private const APPLICATIONS = [
        'home' => ['setSystemTimezone', 'setSession', 'setHooks', 'setUpdates'],
        'admin' => ['setSystemTimezone', 'setSecure', 'setSession', 'setHooks'],
        'game' => ['setSystemTimezone', 'setSecure', 'setSession', 'setHooks', 'setUpdates'],
        'install' => [],
    ];

    /**
     * Contains the value that indicated if the game is installed or not
     *
     * @var boolean
     */
    private $is_installed = false;

    /**
     * Contains the Session object
     *
     * @var Sessions
     */
    private $session = null;

    /**
     * Contains the Hooks object
     *
     * @var Hooks
     */
    private $hooks = null;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Start the system
     *
     * @param string $application
     * @return void
     */
    public function bootUp(string $application): void
    {
        // overall loads
        $this->autoLoad();
        $this->setErrorHandler();
        $this->isGameInstalled();

        // specific pages load or executions
        if (isset(self::APPLICATIONS[$application])) {
            foreach (self::APPLICATIONS[$application] as $methods) {
                if (!empty($methods)) {
                    $this->$methods();
                }
            }
        }
    }

    /**
     * Get session
     *
     * @return Sessions
     */
    public function getSession(): Sessions
    {
        return $this->session;
    }

    /**
     * Get hooks
     *
     * @return Hooks
     */
    public function getHooks(): Hooks
    {
        return $this->hooks;
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
        date_default_timezone_set(FunctionsLib::readConfig('date_time_zone'));
    }

    /**
     * Return a new session object
     *
     * @return Sessions
     */
    private function setSession(): void
    {
        $this->session = new Sessions;
    }

    /**
     * Return a new Hooks objects
     *
     * @return Hooks
     */
    private function setHooks(): void
    {
        $this->hooks = new Hooks;

        // Before load stuff
        $this->hooks->call_hook('before_loads');
    }

    /**
     * Set secure page to escape requests
     *
     * @return void
     */
    private function setSecure(): void
    {
        $current_page = isset($_GET['page']) ? $_GET['page'] : '';

        $exclude = ['languages'];

        if (!in_array($current_page, $exclude)) {
            SecurePageLib::run();
        }
    }

    /**
     * Set updates
     *
     * @return void
     */
    private function setUpdates(): void
    {
        define('SHIP_DEBRIS_FACTOR', FunctionsLib::readConfig('fleet_cdr') / 100);
        define('DEFENSE_DEBRIS_FACTOR', FunctionsLib::readConfig('defs_cdr') / 100);

        // Several updates
        new UpdatesLibrary;
    }
}
/* end of common.php */
