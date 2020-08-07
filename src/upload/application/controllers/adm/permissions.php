<?php

declare (strict_types = 1);

/**
 * Permissions Controller
 *
 * PHP Version 7.1+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\controllers\adm;

use application\core\Controller;
use application\core\enumerators\UserRanksEnumerator as UserRanks;
use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\adm\Permissions as Per;
use application\libraries\FunctionsLib as Functions;

/**
 * Permissions Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Permissions extends Controller
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
        parent::loadLang(['adm/global', 'adm/permissions']);

        // set data
        $this->user = $this->getUserData();

        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // init a new permissions object
        $this->setUpPermissions();

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
        $this->permissions = new Per(
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
                'adm/permissions_view',
                array_merge(
                    $this->langs->language,
                    $this->buildListOfPermissions()
                )
            )
        );
    }

    /**
     * Build the list of permissions
     *
     * @return array
     */
    private function buildListOfPermissions(): array
    {
        $list_of_permissions = [];

        foreach ($this->permissions->getAllPermissionsAsArray() as $module => $roles) {
            $list_of_permissions[] = [
                'page_module' => $module,
                'go_role' => UserRanks::GO,
                'sgo_role' => UserRanks::SGO,
                'ga_role' => UserRanks::ADMIN,
                'go_checked' => ($roles[UserRanks::GO] == 1) ? 'checked' : '',
                'sgo_checked' => ($roles[UserRanks::SGO] == 1) ? 'checked' : '',
                'ga_checked' => ($roles[UserRanks::ADMIN] == 1) ? 'checked' : '',
            ];
        }

        return [
            'permissions_list' => $list_of_permissions,
        ];
    }
}

/* end of permissions.php */
