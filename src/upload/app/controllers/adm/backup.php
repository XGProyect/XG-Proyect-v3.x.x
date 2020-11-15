<?php declare (strict_types = 1);

/**
 * Backup Controller
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
use App\libraries\TimingLibrary as Timing;

/**
 * Backup Class
 */
class Backup extends BaseController
{
    const BACKUP_SETTINGS = [
        'auto_backup' => FILTER_SANITIZE_STRING,
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/backup');

        // load Language
        parent::loadLang(['adm/global', 'adm/backup']);
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
        $save = filter_input(INPUT_POST, 'save');
        $backup = filter_input(INPUT_POST, 'backup');
        $file_actions = filter_input_array(INPUT_GET, [
            'action' => FILTER_SANITIZE_STRING,
            'file' => [
                'filter' => FILTER_CALLBACK,
                'options' => [$this, 'isValidFile'],
            ],
        ]);

        // save form
        if ($save) {
            $data = filter_input_array(INPUT_POST, self::BACKUP_SETTINGS, true);

            foreach ($data as $option => $value) {
                Functions::updateConfig($option, ($value == 'on' ? 1 : 0));
            }
        }

        // create a new backup
        if ($backup) {
            $this->Backup_Model->performBackup();
        }

        // download or delete a file
        if ($file_actions) {
            if (in_array($file_actions['action'], ['download', 'delete'])
                && $file_actions['file'] != null) {
                $this->{'do' . ucfirst($file_actions['action']) . 'Action'}($file_actions['file']);
            }
        }
    }

    /**
     * Download the provided file
     *
     * @param string $file_name
     * @return void
     */
    private function doDownloadAction(string $file_name): void
    {
        $to_download = XGP_ROOT . BACKUP_PATH . $file_name;

        if (file_exists($to_download)) {
            header('Content-type: text/plain');
            header('Content-disposition: attachment; filename=' . $file_name);
            readfile($to_download);
            exit();
        }
    }

    /**
     * Delete the provided file
     *
     * @param string $file_name
     * @return void
     */
    private function doDeleteAction(string $file_name): void
    {
        $to_delete = XGP_ROOT . BACKUP_PATH . $file_name;

        if (file_exists($to_delete)) {
            unlink($to_delete);
        }

        Functions::redirect('admin.php?page=backup');
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
                'adm/backup_view',
                array_merge(
                    $this->langs->language,
                    $this->getBackupSettings(),
                    $this->getBackupList()
                )
            )
        );
    }

    /**
     * Get new user registration settings
     *
     * @return void
     */
    private function getBackupSettings()
    {
        return $this->setChecked(
            array_filter(
                Functions::readConfig('', true),
                function ($key) {
                    return array_key_exists($key, self::BACKUP_SETTINGS);
                },
                ARRAY_FILTER_USE_KEY
            )
        );
    }

    /**
     * Coverts the setting value from an int to a "checked"
     *
     * @param array $settings
     * @return array
     */
    private function setChecked(array $settings): array
    {
        foreach ($settings as $key => $value) {
            $settings[$key] = $value == 1 ? 'checked="checked"' : '';
        }

        return $settings;
    }

    /**
     * Build the list of available backups
     *
     * @return void
     */
    private function getBackupList()
    {
        $backup_list = [];
        $backups_path = XGP_ROOT . BACKUP_PATH;

        // list of backup files
        chdir($backups_path);
        $files = glob('*.sql');

        if ($files != '') {
            foreach ($files as $file_name) {
                $backup_list[] = [
                    'file_name' => $this->formatFileName($file_name),
                    'file_size' => Format::prettyBytes(filesize($file_name)),
                    'full_file_name' => $file_name,
                ];
            }
        }

        krsort($backup_list);

        return [
            'backup_list' => $backup_list,
        ];
    }

    /**
     * Format the file name to get the current date as name
     *
     * @param string $file_name
     * @return string
     */
    private function formatFileName(string $file_name): string
    {
        $matches = [];
        preg_match('/db-backup-(?:[0-9]+)-([0-9]+)-(?:[a-zA-Z0-9]+)\.sql/', $file_name, $matches);

        return Timing::formatExtendedDate($matches[1]);
    }

    /**
     * Check whether if it's a valid file, returns an empty string if it's not
     *
     * @param string $file_name
     * @return boolean
     */
    private function isValidFile(string $file_name): string
    {
        if ((bool) preg_match('/db-backup-(?:[0-9]+)-([0-9]+)-(?:[a-zA-Z0-9]+)\.sql/', $file_name, $matches) !== false) {
            return $file_name;
        }

        return '';
    }
}
