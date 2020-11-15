<?php declare (strict_types = 1);

/**
 * Home Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\controllers\adm;

use App\core\BaseController;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\FormatLib as Format;
use App\libraries\Functions;
use JsonException;

/**
 * Home Class
 */
class Home extends BaseController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/home');

        // load Language
        parent::loadLang(['adm/global', 'adm/home']);
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        $server_stats = $this->Home_Model->getUsersStats();

        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/home_view',
                array_merge(
                    $this->langs->language,
                    $server_stats,
                    [
                        'alert' => [$this->buildAlertsBlock()],
                        'average_user_points' => Format::shortlyNumber($server_stats['average_user_points']),
                        'average_alliance_points' => Format::shortlyNumber($server_stats['average_alliance_points']),
                        'database_size' => Format::prettyBytes($this->Home_Model->getDbSize()['db_size']),
                        'database_server' => $this->Home_Model->getDbVersion(),
                        'php_version' => PHP_VERSION,
                        'server_version' => SYSTEM_VERSION,
                    ]
                )
            )
        );
    }

    /**
     * Build the alerts block based on our current server status
     *
     * @return array
     */
    private function buildAlertsBlock(): array
    {
        $alert = [];

        if ($this->user['user_authlevel'] >= 3) {
            if ((bool) (@fileperms(XGP_ROOT . CONFIGS_PATH . 'config.php') & 0x0002)) {
                $alert[] = $this->langs->line('hm_config_file_writable');
            }

            if ($this->getServerErrors()) {
                $alert[] = $this->langs->line('hm_errors');
            }

            if ($this->checkUpdates()) {
                $alert[] = $this->langs->line('hm_old_version');
            }

            if (Administration::installDirExists()) {
                $alert[] = $this->langs->line('hm_install_file_detected');
            }

            if (Functions::readConfig('version') != SYSTEM_VERSION) {
                $alert[] = $this->langs->line('hm_update_required');
            }
        }

        $alerts_count = count($alert);
        $messages = $second_style = $error_type = null;

        if ($alerts_count > 1) {
            $messages = join('<br>', $alert);
            $second_style = 'alert-danger';
            $error_type = $this->langs->line('hm_error');
        }

        if ($alerts_count == 1) {
            $messages = join('<br>', $alert);
            $second_style = 'alert-warning';
            $error_type = $this->langs->line('hm_warning');
        }

        return [
            'error_message' => $messages ?? $this->langs->line('hm_all_ok'),
            'second_style' => $second_style ?? 'alert-success',
            'error_type' => $error_type ?? $this->langs->line('hm_ok'),
        ];
    }

    /**
     * Check if there's any new version available
     *
     * @return boolean
     */
    private function checkUpdates(): bool
    {
        try {
            if (function_exists('file_get_contents')) {
                $file_data = @file_get_contents(
                    'https://updates.xgproyect.org/latest.php',
                    false,
                    stream_context_create(
                        ['https' =>
                            [
                                'timeout' => 1, // one second
                            ],
                        ]
                    )
                );

                if ($file_data) {
                    $system_v = Functions::readConfig('version');
                    $last_v = @json_decode(
                        $file_data,
                        false,
                        512,
                        JSON_THROW_ON_ERROR
                    )->version;

                    return version_compare($system_v, $last_v, '<');
                }
            }

            return false;
        } catch (JsonException $e) {
            return false;
        }
    }

    /**
     * Check if there are any errors logged
     *
     * @return boolean
     */
    private function getServerErrors(): bool
    {
        $logs_path = XGP_ROOT . LOGS_PATH;

        return (count(glob($logs_path . '*.txt')) > 0);
    }
}
