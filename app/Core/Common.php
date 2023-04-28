<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Enumerators\SwitchIntEnumerator as SwitchInt;
use App\Core\Enumerators\UserRanksEnumerator as UserRanks;
use App\Helpers\StringsHelper;
use App\Libraries\Functions;
use App\Libraries\SecurePageLib;
use App\Libraries\TimingLibrary as Timing;
use App\Libraries\UpdatesLibrary;
use App\Libraries\Users;
use Exception;

// Require some stuff
require_once XGP_ROOT . 'config' . DIRECTORY_SEPARATOR . 'constants.php';
require_once XGP_ROOT . VENDOR_PATH . 'autoload.php';

class Common
{
    private const APPLICATIONS = [
        'home' => ['setSystemTimezone', 'setSession', 'setUpdates', 'isServerOpen'],
        'admin' => ['setSystemTimezone', 'setSecure', 'setSession'],
        'game' => ['setSystemTimezone', 'setSecure', 'setSession', 'setUpdates', 'isServerOpen', 'checkBanStatus'],
        'install' => [],
    ];
    private bool $is_installed = false;
    private ?Sessions $sessions = null;

    /**
     * Start the system
     *
     * @param string $app
     * @return void
     */
    public function bootUp(string $app): void
    {
        // overall loads
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

    public function getSession(): Sessions
    {
        return $this->sessions;
    }

    private function setErrorHandler(): void
    {
        // XGP error handler
        new ErrorHandler();
    }

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

    private function initLanguage(): void
    {
        $set = false;

        if ($this->is_installed && !defined('IN_INSTALL')) {
            $set = true;
        }

        define('DEFAULT_LANG', Functions::getCurrentLanguage($set));
    }

    private function setSystemTimezone(): void
    {
        date_default_timezone_set(Functions::readConfig('date_time_zone'));
    }

    private function setSession(): void
    {
        $this->sessions = new Sessions();
    }

    private function setSecure(): void
    {
        $current_page = isset($_GET['page']) ? $_GET['page'] : '';

        $exclude = ['languages'];

        if (!in_array($current_page, $exclude)) {
            SecurePageLib::run();
        }
    }

    private function setUpdates(): void
    {
        define('SHIP_DEBRIS_FACTOR', Functions::readConfig('fleet_cdr') / 100);
        define('DEFENSE_DEBRIS_FACTOR', Functions::readConfig('defs_cdr') / 100);

        // Several updates
        new UpdatesLibrary();
    }

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
