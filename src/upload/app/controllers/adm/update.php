<?php
/**
 * Update Controller
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
use App\libraries\Functions;

/**
 * Update Class
 */
class Update extends BaseController
{
    /**
     * @var mixed
     */
    private $system_version;
    /**
     * @var mixed
     */
    private $db_version;
    /**
     * @var mixed
     */
    private $demo;
    /**
     * @var array
     */
    private $output = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/update');

        // load Language
        parent::loadLang(['adm/global', 'adm/update']);
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

                    parent::$page->displayAdmin(
                        $this->getTemplate()->set(
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

        parent::$page->displayAdmin(
            $this->getTemplate()->set(
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
                    $this->output[] = $this->Update_Model->runQuery($query);
                } else {
                    $this->output[] = $query;
                }
            }
        }
    }
}
