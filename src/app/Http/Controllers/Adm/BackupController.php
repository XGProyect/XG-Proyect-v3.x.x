<?php

declare(strict_types=1);

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Libraries\Adm\AdministrationLib as Administration;
use App\Libraries\FormatLib as Format;
use App\Libraries\Functions;
use App\Libraries\TimingLibrary as Timing;
use App\Models\Adm\Backup;

class BackupController extends BaseController
{
    public const BACKUP_SETTINGS = [
        'auto_backup' => FILTER_UNSAFE_RAW,
    ];
    private Backup $backupModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/backup']);

        $this->backupModel = new Backup();
    }

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
            'action' => FILTER_UNSAFE_RAW,
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
            $this->backupModel->performBackup();
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

    private function buildPage(): void
    {
        $this->page->displayAdmin(
            $this->template->set(
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
