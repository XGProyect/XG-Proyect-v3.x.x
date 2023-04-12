<?php

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Libraries\Adm\AdministrationLib as Administration;
use App\Libraries\Functions;
use App\Models\Adm\Update;

class UpdateController extends BaseController
{
    private $system_version;
    private $db_version;
    private $demo;
    private $output = [];
    private Update $updateModel;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/update']);

        $this->updateModel = new Update();
    }

    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // build the page
        $this->buildPage();
    }

    private function buildPage(): void
    {
        $parse = $this->langs->language;
        $continue = true;

        $this->system_version = SYSTEM_VERSION;
        $this->db_version = Functions::readConfig('version');

        if ($this->system_version == $this->db_version) {
            die(Administration::noAccessMessage($this->langs->line('up_no_update_required')));
        }

        $parse['alert'] = '';
        $parse['up_sub_title'] = sprintf($this->langs->line('up_sub_title'), $this->db_version, $this->system_version);

        if ($_POST && isset($_POST['send'])) {
            $this->demo = (isset($_POST['demo_mode']) && $_POST['demo_mode'] == 'on') ? true : false;

            if (!$this->checkVersion()) {
                $alerts = $this->langs->line('up_no_version_file');
                $continue = false;
            }

            if ($continue) {
                $this->startUpdate();

                $parse['alert'] = Administration::saveMessage('ok', $this->langs->line('up_success'));

                if ($this->demo) {
                    $parse['result'] = print_r($this->output, true);

                    $this->page->displayAdmin(
                        $this->template->set(
                            'adm/update_result_view',
                            $parse
                        )
                    );
                } else {
                    die(Administration::noAccessMessage($this->langs->line('up_success')));
                }
            } else {
                $parse['alert'] = Administration::saveMessage('warning', $alerts);
            }
        }

        $this->page->displayAdmin(
            $this->template->set(
                'adm/update_view',
                $parse
            )
        );
    }

    /**
     * checkVersion
     *
     * @return boolean
     */
    private function checkVersion()
    {
        return file_exists(
            XGP_ROOT . UPDATE_PATH . 'update_common.php'
        );
    }

    /**
     * startUpdate
     *
     * @return void
     */
    private function startUpdate()
    {
        $updates_dir = opendir(XGP_ROOT . UPDATE_PATH);
        $exceptions = ['.', '..', '.htaccess', 'index.html', '.DS_Store', 'update_common.php'];
        $files_to_read = [];
        $db_version = strtr($this->db_version, ['v' => '', '.' => '']);

        while (($update_dir = readdir($updates_dir)) !== false) {
            if (!in_array($update_dir, $exceptions)) {
                $file_version = strtr(
                    $update_dir,
                    ['update_' => '', '.php' => '']
                );

                // ignore previous versions, we only want the newer ones
                if ($db_version >= $file_version) {
                    continue;
                }

                array_push($files_to_read, $file_version);
            }
        }

        // sort very important to keep versions order
        asort($files_to_read);

        // add common
        array_push($files_to_read, 'common');

        // Do we have something? Go...
        if (count($files_to_read) > 0) {
            foreach ($files_to_read as $version) {
                $this->executeFile($version);
            }
        }
    }

    /**
     * executeFile
     *
     * @param string $version Version number
     *
     * @return void
     */
    private function executeFile($version)
    {
        // Define some stuff
        $update_path = XGP_ROOT . UPDATE_PATH . 'update_' . $version . '.php';
        $queries = [];

        require_once $update_path;

        // Check if there was something
        if (isset($queries) && count($queries) > 0) {
            foreach ($queries as $query) {
                if (!$this->demo) {
                    $this->output[] = $this->updateModel->runQuery($query);
                } else {
                    $this->output[] = $query;
                }
            }
        }
    }
}
