<?php

declare (strict_types = 1);

/**
 * Changelog Controller
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
use application\libraries\adm\AdministrationLib;

/**
 * Changelog Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Changelog extends Controller
{
    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        // load Model
        parent::loadModel('adm/changelog');

        // load Language
        parent::loadLang(['adm/global', 'adm/changelog']);

        // set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::authorization($this->user['user_authlevel'], 'edit_users') != 1) {
            AdministrationLib::noAccessMessage($this->langs->line('no_permissions'));
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
        $sub_page = filter_input(INPUT_GET, 'sub_page');

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
                'adm/changelog_view',
                array_merge(
                    $this->langs->language,
                    [
                        'changelog' => $this->buildListOfEntries(),
                    ]
                )
            )
        );
    }

    /**
     * Build the list of changelog entries
     *
     * @return array
     */
    private function buildListOfEntries(): array
    {
        $entries = $this->Changelog_Model->getAllItems();
        $entries_list = [];

        foreach ($entries as $entry) {
            $entries_list[] = array_merge(
                $this->langs->language,
                $entry
            );
        }

        return $entries_list;
    }
}

/* end of changelog.php */
