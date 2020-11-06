<?php

declare (strict_types = 1);

/**
 * Common File
 *
 * @category Core
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @since    3.1.0
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

/**
 * Common class
 */
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
    private $sessions = null;

    /**
     * Contains the Hooks object
     *
     * @var Hooks
     */
    private $hooks = null;

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
        $this->isServerInstalled();

        // specific pages load or executions
        if (isset(self::APPLICATIONS[$application])) {
            foreach (self::APPLICATIONS[$application] as $method) {
                if (!empty($method)) {
                    $this->$method();
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
        return $this->sessions;
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
     * Check if the server is installed
     *
     * @return void
     */
    private function isServerInstalled(): void
    {
        try {
            $config_file = XGP_ROOT . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

            if (file_exists($config_file)) {
                require $config_file;

                // check if it is installed
                if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME') && defined('DB_PREFIX')) {
                    $this->is_installed = true;
                }
            } else {
                fopen($config_file, 'w+');
            }

            // set language
            $this->initLanguage();

            if (!$this->is_installed && !defined('IN_INSTALL')) {
                FunctionsLib::redirect(SYSTEM_ROOT . 'install/');
            }
        } catch (Exception $e) {
            die('Error #0001' . $e->getMessage());
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
        $this->sessions = new Sessions;
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
