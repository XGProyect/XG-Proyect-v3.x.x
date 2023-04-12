<?php

declare(strict_types=1);

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Core\Enumerators\UserRanksEnumerator as UserRanks;
use App\Libraries\Adm\AdministrationLib as Administration;
use App\Libraries\Adm\Permissions;
use App\Libraries\Functions;

class PermissionsController extends BaseController
{
    private string $alert = '';
    private Permissions $permissions;

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/permissions', 'adm/menu']);

        // init a new permissions object
        $this->setUpPermissions();
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
     * Creates a new preferences object that will handle all the preferences
     * creation methods and actions
     *
     * @return void
     */
    private function setUpPermissions(): void
    {
        $this->permissions = new Permissions(
            Functions::readConfig('admin_permissions')
        );
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction(): void
    {
        $permissions = filter_input_array(INPUT_POST);

        if ($permissions) {
            $modules = $this->permissions->getAdminModules();
            $roles = $this->permissions->getRoles(true);

            foreach ($modules as $module) {
                foreach ($module as $module_name) {
                    foreach ($roles as $role) {
                        if (isset($permissions[$module_name][$role]) && $permissions[$module_name][$role] == 'on') {
                            $this->permissions->grantAccess($module_name, $role);
                        } else {
                            $this->permissions->removeAccess($module_name, $role);
                        }
                    }
                }
            }

            $this->permissions->savePermissions();

            $this->alert = Administration::saveMessage('ok', $this->langs->line('pr_all_ok_message'));
        }
    }

    private function buildPage(): void
    {
        $this->page->displayAdmin(
            $this->template->set(
                'adm/permissions_view',
                array_merge(
                    $this->langs->language,
                    ['alert' => $this->alert ?? ''],
                    $this->buildListOfPermissions()
                )
            )
        );
    }

    /**
     * Build list of roles
     *
     * @return array
     */
    private function buildRolesList(): array
    {
        $roles_list = [];

        foreach ($this->permissions->getRoles() as $role) {
            $roles_list[$role] = [
                'role_name' => $this->langs->language['user_level'][$role],
            ];
        }

        return $roles_list;
    }

    /**
     * Build the list of permissions
     *
     * @return array
     */
    private function buildListOfPermissions(): array
    {
        $sections_list = [];
        $modules_list = [];
        $permissions_list = [];

        // get necessary data
        $sections = $this->permissions->getAdminSections();
        $modules = $this->permissions->getAdminModules();
        $roles = $this->buildRolesList();

        // build sections array
        foreach ($sections as $section_id => $section) {
            // build modules array
            foreach ($modules[$section_id] as $module) {
                // build permissions array
                foreach ($roles as $role => $name) {
                    $permissions_list[] = [
                        'module' => $module,
                        'role' => $role,
                        'permission_checked' => ($this->permissions->isAccessAllowed($module, $role) ? 'checked' : ''),
                        'permission_disabled' => ($role == UserRanks::ADMIN ? 'disabled' : ''),
                    ];
                }

                // put all inside
                $modules_list[] = [
                    'page_module' => $module,
                    'page_module_title' => $this->langs->language[$module],
                    'permissions_list' => $permissions_list,
                ];

                unset($permissions_list); // reset
            }

            // put all inside
            $sections_list[$section_id] = [
                'section_name' => ucfirst($section),
                'section_title' => $this->langs->language[$section],
                'roles_list' => $roles,
                'modules_list' => $modules_list,
            ];

            unset($modules_list); // reset
        }

        return [
            'sections_list' => $sections_list,
        ];
    }
}
