<?php

declare (strict_types = 1);

/**
 * Modules Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\adm;

use application\core\Controller;
use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\FunctionsLib;

/**
 * Modules Class
 */
class Modules extends Controller
{
    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Contains the alert string
     *
     * @var string
     */
    private $alert = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/modules']);

        // set data
        $this->user = $this->getUserData();

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
        $modules = filter_input_array(INPUT_POST);

        if ($modules) {
            $modules_count = count(explode(';', FunctionsLib::readConfig('modules')));

            for ($i = 0; $i < $modules_count; $i++) {
                $modules_set[] = (isset($modules["status{$i}"]) ? 1 : 0);
            }

            FunctionsLib::updateConfig('modules', join(';', $modules_set));

            $this->alert = Administration::saveMessage('ok', $this->langs->line('mdl_all_ok_message'));
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
                'adm/modules_view',
                array_merge(
                    $this->langs->language,
                    [
                        'alert' => $this->alert ?? '',
                        'modules' => $this->buildModulesList(),
                    ]
                )
            )
        );
    }

    /**
     * Build the list of modules
     *
     * @return array
     */
    private function buildModulesList(): array
    {
        $modules_list = [];

        $modules = explode(';', FunctionsLib::readConfig('modules'));

        if ($modules) {
            foreach ($modules as $module => $status) {
                if ($status != null) {
                    $modules_list[] = [
                        'module' => $module,
                        'module_name' => $this->langs->language['mdl_modules'][$module],
                        'module_value' => ($status == 1) ? 'checked' : '',
                        'color' => ($status == 1) ? 'success' : 'danger',
                    ];
                }
            }
        }

        return $modules_list;
    }
}
