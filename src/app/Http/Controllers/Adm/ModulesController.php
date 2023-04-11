<?php

declare(strict_types=1);

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Libraries\Adm\AdministrationLib as Administration;
use App\Libraries\Functions;

class ModulesController extends BaseController
{
    private string $alert = '';

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/modules']);
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
        $modules = filter_input_array(INPUT_POST);

        if ($modules) {
            $modules_count = count(explode(';', Functions::readConfig('modules')));

            for ($i = 0; $i < $modules_count; $i++) {
                $modules_set[] = (isset($modules["status{$i}"]) ? 1 : 0);
            }

            Functions::updateConfig('modules', join(';', $modules_set));

            $this->alert = Administration::saveMessage('ok', $this->langs->line('mdl_all_ok_message'));
        }
    }

    private function buildPage(): void
    {
        $this->page->displayAdmin(
            $this->template->set(
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

        $modules = explode(';', Functions::readConfig('modules'));

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
