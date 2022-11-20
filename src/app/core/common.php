<?php

declare(strict_types=1);
/**
 * XG Proyect
 *
 * Open-source OGame Clon
 *
 * This content is released under the GPL-3.0 License
 *
 * Copyright (c) 2008-2020 XG Proyect
 *
 * @package    XG Proyect
 * @author     XG Proyect Team
 * @copyright  2008-2020 XG Proyect
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0 License
 * @link       https://github.com/XGProyect/
 * @since      3.0.0
 */

namespace App\core;

use App\core\enumerators\SwitchIntEnumerator as SwitchInt;
use App\core\enumerators\UserRanksEnumerator as UserRanks;
use App\core\ErrorHandler;
use App\core\Language;
use App\core\Sessions;
use App\helpers\StringsHelper;
use App\libraries\Functions;
use App\libraries\SecurePageLib;
use App\libraries\TimingLibrary as Timing;
use App\libraries\UpdatesLibrary;
use App\libraries\Users;
use AutoLoader;
use Exception;

// Require some stuff
require_once XGP_ROOT . 'config' . DIRECTORY_SEPARATOR . 'constants.php';
require_once XGP_ROOT . CORE_PATH . 'AutoLoader.php';

/**
 * Common class
 */
class Common
{
    private const APPLICATIONS = [
        'home' => ['setSystemTimezone', 'setSession', 'setUpdates', 'isServerOpen'],
        'admin' => ['setSystemTimezone', 'setSecure', 'setSession'],
        'game' => ['setSystemTimezone', 'setSecure', 'setSession', 'setUpdates', 'isServerOpen', 'checkBanStatus'],
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
     * Start the system
     *
     * @param string $app
     * @return void
     */
    public function bootUp(string $app): void
    {
        // overall loads
        $this->autoLoad();
        $this->setErrorHandler();
        $this->isServerInstalled();

        // specific pages load or executions
        if (isset(self::APPLICATIONS[$app])) {
            foreach (self::APPLICATIONS[$app] as $method) {
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
     * Auto load the core, libraries and helpers
     *
     * @return void
     */
    private function autoLoad(): void
    {
        AutoLoader::registerExcludes('BattleEngine');

        AutoLoader::registerNamespace('App_core', XGP_ROOT . CORE_PATH);
        AutoLoader::registerNamespace('App_helpers', XGP_ROOT . HELPERS_PATH);
        AutoLoader::registerNamespace('App_libraries', XGP_ROOT . LIB_PATH);
    }

    /**
     * Set a new error handler
     *
     * @return void
     */
    private function setErrorHandler(): void
    {
        // XGP error handler
        new ErrorHandler();
    }

    /**
     * Check if the server is installed
     *
     * @return void
     */
    private function isServerInstalled(): void
    {
        try {
            $config_file = XGP_ROOT . CONFIGS_PATH . 'config.php';

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
                Functions::redirect(SYSTEM_ROOT . 'install/');
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

        define('DEFAULT_LANG', Functions::getCurrentLanguage($set));
    }

    /**
     * Set the system timezone
     *
     * @return void
     */
    private function setSystemTimezone(): void
    {
        date_default_timezone_set(Functions::readConfig('date_time_zone'));
    }

    /**
     * Return a new session object
     *
     * @return Sessions
     */
    private function setSession(): void
    {
        $this->sessions = new Sessions();
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
        define('SHIP_DEBRIS_FACTOR', Functions::readConfig('fleet_cdr') / 100);
        define('DEFENSE_DEBRIS_FACTOR', Functions::readConfig('defs_cdr') / 100);

        // Several updates
        new UpdatesLibrary();
    }

    /**
     * Check if the server is open
     *
     * @return void
     */
    private function isServerOpen(): void
    {
        if (Functions::readConfig('game_enable') == SwitchInt::off) {
            $user = (new Users())->getUserData();
            $user_level = $user['user_authlevel'] ?? 0;

            if ($user_level < UserRanks::ADMIN) {
                die(Functions::message(Functions::readConfig('close_reason'), '', '', false, false));
            }
        }
    }

    /**
     * Check if the user is banned
     *
     * @return void
     */
    private function checkBanStatus(): void
    {
        Users::checkSession();
        $user = (new Users())->getUserData();

        if ($user['user_banned'] > 0) {
            die(Functions::message(StringsHelper::parseReplacements(
                (new Language())->loadLang('game/global', true)->language['bg_banned'],
                [Timing::formatShortDate($user['user_banned'])]
            ), '', '', false, false));
        }
    }
}
