<?php declare (strict_types = 1);

/**
 * Errors Controller
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

/**
 * Errors Class
 */
class Errors extends BaseController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/errors']);
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

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction(): void
    {
        $delete_all = filter_input(INPUT_GET, 'deleteall', FILTER_DEFAULT);

        if ($delete_all == 'yes') {
            $files = $this->getListOfLogFiles();

            if ($files != '') {
                foreach ($files as $file_name) {
                    unlink($file_name);
                }
            }
        }
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/errors_view',
                array_merge(
                    $this->langs->language,
                    $this->processErrorsLogs()
                )
            )
        );
    }

    /**
     * Parse the recovered log files
     *
     * @return array
     */
    private function processErrorsLogs(): array
    {
        // list of log files
        $files = $this->getListOfLogFiles();
        $list_of_errors = [];
        $error_count = 0;

        if ($files != '') {
            foreach ($files as $file_name) {
                $contents = file_get_contents($file_name);

                if ($contents) {
                    $error_count++;

                    $error_columns = explode('|', $contents);

                    $list_of_errors[] = [
                        'user_ip' => $error_columns[1],
                        'error_type' => $error_columns[2],
                        'error_code' => $error_columns[3],
                        'error_message' => $error_columns[4],
                        'error_trace' => $error_columns[5],
                        'error_datetime' => $error_columns[7],
                        'alert_type' => ($error_columns[3] == 'E_ERROR' ? 'danger' : 'warning'),
                    ];
                }
            }
        }

        return [
            'errors_list' => $list_of_errors,
            'errors_list_resume' => strtr($this->langs->line('er_errors'), ['%s' => $error_count]),
        ];
    }

    /**
     * Get a list of the log files
     *
     * @return array
     */
    private function getListOfLogFiles(): array
    {
        $logs_path = XGP_ROOT . LOGS_PATH;

        // list of log files
        return glob($logs_path . '*.txt');
    }
}
